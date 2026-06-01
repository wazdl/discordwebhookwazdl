<?php

namespace App\Modules\DiscordWebhookWazdL\Listeners;

use App\Modules\DiscordWebhookWazdL\Services\DiscordWebhookService;

class ServiceCreatedListener
{
    public function handle(object $event): void
    {
        try {
            $service = $event->service ?? null;
            if (!$service) return;

            $customer = $service->customer ?? null;
            $product  = $service->product ?? null;

            $fields = [
                ['name' => '🆔 ID Service', 'value' => (string)($service->id ?? 'N/A'), 'inline' => true],
                ['name' => '📦 Produit', 'value' => $product->name ?? 'N/A', 'inline' => true],
                ['name' => '📋 Statut', 'value' => ucfirst($service->status ?? 'active'), 'inline' => true],
            ];

            if ($customer) {
                $fields[] = ['name' => '👤 Client', 'value' => trim(($customer->firstname ?? '') . ' ' . ($customer->lastname ?? '')) ?: ($customer->email ?? 'N/A'), 'inline' => true];
                $fields[] = ['name' => '📧 Email', 'value' => $customer->email ?? 'N/A', 'inline' => true];
            }

            if (isset($service->expires_at) && $service->expires_at) {
                $fields[] = ['name' => '📅 Expiration', 'value' => $service->expires_at->format('d/m/Y'), 'inline' => true];
            }

            DiscordWebhookService::send(
                'service_created',
                '🚀 Nouveau service créé',
                $fields,
                'Un nouveau service vient d\'être provisionné.'
            );
        } catch (\Throwable $e) {
            // Fail silently
        }
    }
}
