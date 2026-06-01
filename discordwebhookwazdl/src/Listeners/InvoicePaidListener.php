<?php

namespace App\Modules\DiscordWebhookWazdL\Listeners;

use App\Modules\DiscordWebhookWazdL\Services\DiscordWebhookService;

class InvoicePaidListener
{
    public function handle(object $event): void
    {
        try {
            $invoice = $event->invoice ?? null;
            if (!$invoice) return;

            $customer = $invoice->customer ?? null;

            $fields = [
                ['name' => '🧾 N° Facture', 'value' => '#' . ($invoice->id ?? 'N/A'), 'inline' => true],
                ['name' => '💶 Montant', 'value' => number_format($invoice->total ?? 0, 2) . ' ' . ($invoice->currency ?? '€'), 'inline' => true],
                ['name' => '💳 Mode de paiement', 'value' => $invoice->payment_method ?? 'N/A', 'inline' => true],
            ];

            if ($customer) {
                $fields[] = ['name' => '👤 Client', 'value' => trim(($customer->firstname ?? '') . ' ' . ($customer->lastname ?? '')) ?: ($customer->email ?? 'N/A'), 'inline' => true];
                $fields[] = ['name' => '📧 Email', 'value' => $customer->email ?? 'N/A', 'inline' => true];
            }

            $fields[] = ['name' => '📅 Payé le', 'value' => isset($invoice->paid_at) ? $invoice->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'), 'inline' => false];

            DiscordWebhookService::send(
                'invoice_paid',
                '💰 Paiement reçu !',
                $fields,
                'Une facture vient d\'être réglée avec succès.'
            );
        } catch (\Throwable $e) {
            // Fail silently
        }
    }
}
