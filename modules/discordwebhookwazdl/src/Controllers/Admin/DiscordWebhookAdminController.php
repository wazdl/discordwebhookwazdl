<?php

namespace App\Modules\DiscordWebhookWazdL\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\DiscordWebhookWazdL\Models\DiscordWebhookWazdLSettings;
use App\Modules\DiscordWebhookWazdL\Services\DiscordWebhookService;
use Illuminate\Http\Request;

class DiscordWebhookAdminController extends Controller
{
    public function index()
    {
        $settings = DiscordWebhookWazdLSettings::getInstance();
        return view('discordwebhookwazdl::admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'webhook_url'               => 'nullable|url|max:512',
            'webhook_username'          => 'nullable|string|max:80',
            'webhook_avatar_url'        => 'nullable|url|max:512',
            'embed_footer_text'         => 'nullable|string|max:120',
            'embed_footer_icon'         => 'nullable|url|max:512',
            'mention_role'              => 'nullable|string|max:100',
            'color_customer_created'    => 'nullable|string|max:7',
            'color_invoice_created'     => 'nullable|string|max:7',
            'color_invoice_paid'        => 'nullable|string|max:7',
            'color_service_created'     => 'nullable|string|max:7',
            'color_service_suspended'   => 'nullable|string|max:7',
            'color_service_unsuspended' => 'nullable|string|max:7',
            'color_service_expired'     => 'nullable|string|max:7',
            'color_service_upgraded'    => 'nullable|string|max:7',
        ]);

        // Récupérer les toggles (les checkboxes non cochées ne sont pas envoyées)
        $notifyFields = [
            'notify_customer_created',
            'notify_invoice_created',
            'notify_invoice_paid',
            'notify_service_created',
            'notify_service_suspended',
            'notify_service_unsuspended',
            'notify_service_expired',
            'notify_service_upgraded',
        ];
        foreach ($notifyFields as $field) {
            $validated[$field] = $request->boolean($field);
        }

        // Nettoyer les couleurs (supprimer # si présent)
        $colorFields = [
            'color_customer_created', 'color_invoice_created', 'color_invoice_paid',
            'color_service_created', 'color_service_suspended', 'color_service_unsuspended',
            'color_service_expired', 'color_service_upgraded',
        ];
        foreach ($colorFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = ltrim($validated[$field], '#');
            }
        }

        $settings = DiscordWebhookWazdLSettings::getInstance();
        $settings->update($validated);

        return redirect()->route('admin.discordwebhookwazdl.settings')
            ->with('success', '✅ Configuration Discord Webhook sauvegardée avec succès !');
    }

    public function testWebhook(Request $request)
    {
        $request->validate([
            'webhook_url' => 'required|url',
        ]);

        $settings = DiscordWebhookWazdLSettings::getInstance();

        $success = DiscordWebhookService::sendTest(
            $request->input('webhook_url'),
            $settings->webhook_username ?: 'WazdL Panel',
            $settings->webhook_avatar_url,
            $settings->embed_footer_text ?: 'WazdL Panel'
        );

        if ($success) {
            return response()->json(['success' => true, 'message' => '✅ Embed de test envoyé avec succès sur Discord !']);
        }

        return response()->json(['success' => false, 'message' => '❌ Échec de l\'envoi. Vérifiez l\'URL du webhook.'], 400);
    }
}
