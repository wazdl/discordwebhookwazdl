<?php

namespace App\Modules\DiscordWebhookWazdL\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordWebhookWazdLSettings extends Model
{
    protected $table = 'discordwebhookwazdl_settings';

    protected $fillable = [
        'webhook_url',
        'webhook_username',
        'webhook_avatar_url',
        'embed_footer_text',
        'embed_footer_icon',
        'mention_role',
        'color_customer_created',
        'color_invoice_paid',
        'color_invoice_created',
        'color_service_created',
        'color_service_suspended',
        'color_service_unsuspended',
        'color_service_expired',
        'color_service_upgraded',
        'notify_customer_created',
        'notify_invoice_created',
        'notify_invoice_paid',
        'notify_service_created',
        'notify_service_suspended',
        'notify_service_unsuspended',
        'notify_service_expired',
        'notify_service_upgraded',
    ];

    protected $casts = [
        'notify_customer_created'    => 'boolean',
        'notify_invoice_created'     => 'boolean',
        'notify_invoice_paid'        => 'boolean',
        'notify_service_created'     => 'boolean',
        'notify_service_suspended'   => 'boolean',
        'notify_service_unsuspended' => 'boolean',
        'notify_service_expired'     => 'boolean',
        'notify_service_upgraded'    => 'boolean',
    ];

    /**
     * Récupère (ou crée si absent) le seul enregistrement de configuration.
     */
    public static function getInstance(): self
    {
        $instance = static::first();
        if (!$instance) {
            $instance = static::create([
                'webhook_url'               => null,
                'webhook_username'          => 'WazdL Panel',
                'embed_footer_text'         => 'WazdL Panel',
                'color_customer_created'    => '5865F2',
                'color_invoice_paid'        => '57F287',
                'color_invoice_created'     => '5865F2',
                'color_service_created'     => '3BA55C',
                'color_service_suspended'   => 'FEE75C',
                'color_service_unsuspended' => '57F287',
                'color_service_expired'     => 'ED4245',
                'color_service_upgraded'    => 'EB459E',
                'notify_customer_created'    => true,
                'notify_invoice_created'     => true,
                'notify_invoice_paid'        => true,
                'notify_service_created'     => true,
                'notify_service_suspended'   => true,
                'notify_service_unsuspended' => true,
                'notify_service_expired'     => true,
                'notify_service_upgraded'    => true,
            ]);
        }
        return $instance;
    }
}
