<?php

namespace App\Modules\DiscordWebhookWazdL\Listeners;

use App\Modules\DiscordWebhookWazdL\Services\DiscordWebhookService;

class ServiceUpgradedListener
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
            ];

            if ($customer) {
                $fields[] = ['name' => '👤 Client', 'value' => trim(($customer->firstname ?? '') . ' ' . ($customer->lastname ?? '')) ?: ($customer->email ?? 'N/A'), 'inline' => true];
                $fields[] = ['name' => '📧 Email', 'value' => $customer->email ?? 'N/A', 'inline' => true];
            }

            $fields[] = ['name' => '⏱️ Date', 'value' => now()->format('d/m/Y H:i'), 'inline' => false];

            DiscordWebhookService::send(
                'service_upgraded',
                '⬆️ Service mis à niveau',
                $fields,
                'Un service a été mis à niveau (upgrade/downgrade).'
            );
        } catch (\Throwable $e) {
            // Fail silently
        }
    }
}
