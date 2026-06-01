<?php

namespace App\Modules\DiscordWebhookWazdL\Listeners;

use App\Modules\DiscordWebhookWazdL\Services\DiscordWebhookService;

class CustomerCreatedListener
{
    public function handle(object $event): void
    {
        try {
            $customer = $event->customer ?? $event->user ?? null;
            if (!$customer) return;

            $fields = [
                ['name' => '👤 Nom', 'value' => trim(($customer->firstname ?? '') . ' ' . ($customer->lastname ?? '')) ?: 'N/A', 'inline' => true],
                ['name' => '📧 Email', 'value' => $customer->email ?? 'N/A', 'inline' => true],
                ['name' => '🆔 ID Client', 'value' => (string)($customer->id ?? 'N/A'), 'inline' => true],
                ['name' => '📅 Inscription', 'value' => isset($customer->created_at) ? $customer->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'), 'inline' => false],
            ];

            DiscordWebhookService::send(
                'customer_created',
                '🆕 Nouveau client inscrit',
                $fields,
                'Un nouveau compte client vient d\'être créé sur le panel.'
            );
        } catch (\Throwable $e) {
            // Fail silently pour ne pas bloquer l'inscription
        }
    }
}
