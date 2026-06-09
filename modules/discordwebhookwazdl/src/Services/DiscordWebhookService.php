<?php

namespace App\Modules\DiscordWebhookWazdL\Services;

use App\Modules\DiscordWebhookWazdL\Models\DiscordWebhookWazdLSettings;
use Illuminate\Support\Facades\Http;

class DiscordWebhookService
{
    /**
     * Correspondance événement → champ notify_ dans la table
     */
    private const NOTIFY_MAP = [
        'customer_created'    => 'notify_customer_created',
        'invoice_created'     => 'notify_invoice_created',
        'invoice_paid'        => 'notify_invoice_paid',
        'service_created'     => 'notify_service_created',
        'service_suspended'   => 'notify_service_suspended',
        'service_unsuspended' => 'notify_service_unsuspended',
        'service_expired'     => 'notify_service_expired',
        'service_upgraded'    => 'notify_service_upgraded',
    ];

    /**
     * Correspondance événement → champ color_ dans la table
     */
    private const COLOR_MAP = [
        'customer_created'    => 'color_customer_created',
        'invoice_created'     => 'color_invoice_created',
        'invoice_paid'        => 'color_invoice_paid',
        'service_created'     => 'color_service_created',
        'service_suspended'   => 'color_service_suspended',
        'service_unsuspended' => 'color_service_unsuspended',
        'service_expired'     => 'color_service_expired',
        'service_upgraded'    => 'color_service_upgraded',
    ];

    /**
     * Envoie un embed Discord pour l'événement donné.
     *
     * @param  string  $event   Clé de l'événement (ex: 'service_created')
     * @param  string  $title   Titre de l'embed (emoji + texte)
     * @param  array   $fields  Champs { name, value, inline } à afficher
     * @param  string|null $description  Description optionnelle sous le titre
     */
    public static function send(
        string $event,
        string $title,
        array $fields = [],
        ?string $description = null
    ): void {
        try {
            $settings = DiscordWebhookWazdLSettings::getInstance();

            // 1. Vérification URL webhook
            if (empty($settings->webhook_url)) {
                return;
            }

            // 2. Vérification activation de l'événement
            $notifyField = self::NOTIFY_MAP[$event] ?? null;
            if ($notifyField && !$settings->{$notifyField}) {
                return;
            }

            // 3. Couleur de l'embed (hex → int)
            $colorField   = self::COLOR_MAP[$event] ?? null;
            $colorHex     = $colorField ? ($settings->{$colorField} ?? '5865F2') : '5865F2';
            $colorInt     = hexdec(ltrim($colorHex, '#'));

            // 4. Construire l'embed
            $embed = [
                'title'       => $title,
                'color'       => $colorInt,
                'fields'      => $fields,
                'timestamp'   => now()->toIso8601String(),
                'footer'      => [
                    'text'     => $settings->embed_footer_text ?: 'WazdL Panel',
                    'icon_url' => $settings->embed_footer_icon ?: null,
                ],
            ];

            if ($description) {
                $embed['description'] = $description;
            }

            // Nettoyage des nulls du footer
            $embed['footer'] = array_filter($embed['footer'], fn($v) => !is_null($v));

            // 5. Construire le payload
            $content = '';
            if (!empty($settings->mention_role)) {
                $mention = $settings->mention_role;
                // Si c'est un ID numérique, on l'encadre en mention de rôle
                if (is_numeric($mention)) {
                    $content = "<@&{$mention}>";
                } elseif ($mention === '@everyone' || $mention === 'everyone') {
                    $content = '@everyone';
                } elseif ($mention === '@here' || $mention === 'here') {
                    $content = '@here';
                } else {
                    $content = $mention;
                }
            }

            $payload = [
                'username'   => $settings->webhook_username ?: 'WazdL Panel',
                'embeds'     => [$embed],
            ];

            if (!empty($content)) {
                $payload['content'] = $content;
            }

            if (!empty($settings->webhook_avatar_url)) {
                $payload['avatar_url'] = $settings->webhook_avatar_url;
            }

            // 6. Envoyer
            Http::timeout(5)->post($settings->webhook_url, $payload);
        } catch (\Throwable $e) {
            // Silence
        }
    }

    /**
     * Envoie un embed de test directement avec une URL donnée (pour le bouton Test).
     */
    public static function sendTest(string $webhookUrl, string $username = 'WazdL Panel', ?string $avatarUrl = null, string $footerText = 'WazdL Panel'): bool
    {
        try {
            $payload = [
                'username' => $username,
                'embeds'   => [
                    [
                        'title'       => '🧪 Test du Webhook — WazdL Panel',
                        'description' => 'Si vous voyez ce message, votre webhook Discord est **correctement configuré** ! 🎉',
                        'color'       => hexdec('5865F2'),
                        'fields'      => [
                            ['name' => '📦 Module', 'value' => 'discordwebhookwazdl', 'inline' => true],
                            ['name' => '👤 Auteur', 'value' => 'WazdL Team', 'inline' => true],
                            ['name' => '⏱️ Heure', 'value' => now()->format('d/m/Y H:i:s'), 'inline' => false],
                        ],
                        'timestamp' => now()->toIso8601String(),
                        'footer'    => [
                            'text' => $footerText ?: 'WazdL Panel',
                        ],
                    ],
                ],
            ];

            if ($avatarUrl) {
                $payload['avatar_url'] = $avatarUrl;
            }

            $response = Http::timeout(5)->post($webhookUrl, $payload);
            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
