<?php

namespace App\Modules\DiscordWebhookWazdL;

use App\Extensions\BaseModuleServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Modules\DiscordWebhookWazdL\Listeners\CustomerCreatedListener;
use App\Modules\DiscordWebhookWazdL\Listeners\InvoiceCreatedListener;
use App\Modules\DiscordWebhookWazdL\Listeners\InvoicePaidListener;
use App\Modules\DiscordWebhookWazdL\Listeners\ServiceCreatedListener;
use App\Modules\DiscordWebhookWazdL\Listeners\ServiceSuspendedListener;
use App\Modules\DiscordWebhookWazdL\Listeners\ServiceUnsuspendedListener;
use App\Modules\DiscordWebhookWazdL\Listeners\ServiceExpiredListener;
use App\Modules\DiscordWebhookWazdL\Listeners\ServiceUpgradedListener;

class DiscordWebhookWazdLServiceProvider extends BaseModuleServiceProvider
{
    protected string $uuid = 'discordwebhookwazdl';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrations();
        $this->loadViewsFrom(__DIR__ . '/../views', 'discordwebhookwazdl');

        if (file_exists(__DIR__ . '/../routes/admin.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/admin.php');
        }

        // ── 1. Écouteurs d'événements Laravel ─────────────────────────────
        $this->registerEventListeners();

        // ── 2. Écouteur wildcard pour découvrir les vrais noms d'événements ─
        // Cherche "[DWWL]" dans storage/logs/laravel.log pour les voir
        /* $this->registerDiscoveryListener(); */

        // ── 3. Observers sur les modèles (fallback fiable) ─────────────────
        $this->registerModelObservers();

        // ── Menu admin ────────────────────────────────────────────────────
        if (!$this->app->runningInConsole()) {
            if (class_exists(\App\Core\Menu\AdminMenuItem::class)) {
                $this->app['extension']->addAdminMenuItem(
                    new \App\Core\Menu\AdminMenuItem(
                        'discordwebhookwazdl',
                        'admin.discordwebhookwazdl.settings',
                        'bi bi-discord',
                        'Discord Webhook',
                        90,
                        'admin.settings.manage'
                    )
                );
            }

            if (isset($this->app['settings'])) {
                $this->app['settings']->addCard(
                    'discordwebhookwazdl',
                    'Discord Webhook',
                    'Notifications Discord en temps réel pour vos événements clients et services.',
                    20,
                    null,
                    true
                );

                $this->app['settings']->addCardItem(
                    'discordwebhookwazdl',
                    'discordwebhookwazdl_settings',
                    'Configuration Webhook',
                    'URL, couleurs, mentions et activation par événement.',
                    'bi bi-discord',
                    [Controllers\Admin\DiscordWebhookAdminController::class, 'index'],
                    'admin.settings.manage'
                );
            }
        }
    }

    /**
     * Écouteur wildcard : log TOUS les événements pour découvrir ceux de ClientXCMS.
     * Cherche [DWWL] dans storage/logs/laravel.log
     */
    /* private function registerDiscoveryListener(): void
    {
        Event::listen('*', function (string $eventName, array $data) {
            // Filtrer les événements Illuminate/Laravel internes pour ne pas polluer les logs
            if (str_starts_with($eventName, 'Illuminate\\')
                || str_starts_with($eventName, 'Laravel\\')
                || str_starts_with($eventName, 'bootstrapping')
                || str_starts_with($eventName, 'bootstrapped')
                || str_contains($eventName, 'eloquent.retrieved')
                || str_contains($eventName, 'cache.')
                || str_contains($eventName, 'log.')
            ) {
                return;
            }

            Log::info('[DWWL] Événement détecté : ' . $eventName, [
                'data_keys' => array_keys($data),
                'data_class' => isset($data[0]) ? get_class($data[0]) : 'N/A',
            ]);
        });
    } */

    /**
     * Observer les modèles directement — fallback si les événements ne correspondent pas.
     * Tente plusieurs noms de modèles possibles selon la version de ClientXCMS.
     */
    private function registerModelObservers(): void
    {
        // ── Customer / User ──────────────────────────────────────────────
        // App\Models\Account\Customer est le namespace confirmé de ClientXCMS NextGen
        $customerModels = [
            \App\Models\Account\Customer::class,   // ✅ ClientXCMS NextGen (confirmé)
            \App\Models\Core\Customer::class,
            \App\Models\Customer::class,
            \App\Models\Auth\Customer::class,
            \App\Models\User::class,
        ];

        foreach ($customerModels as $model) {
            if (class_exists($model)) {
                try {
                    $model::created(function ($customer) {
                        (new CustomerCreatedListener())->handle((object) ['customer' => $customer]);
                    });
                    Log::info('[DWWL] Observer Customer enregistré sur : ' . $model);
                    break; // On s'arrête au premier modèle trouvé
                } catch (\Throwable $e) {
                    // Continuer avec le prochain modèle
                }
            }
        }

        // ── Invoice ──────────────────────────────────────────────────────
        $invoiceModels = [
            \App\Models\Billing\Invoice::class,
            \App\Models\Invoice::class,
        ];

        foreach ($invoiceModels as $model) {
            if (class_exists($model)) {
                try {
                    $model::created(function ($invoice) {
                        (new InvoiceCreatedListener())->handle((object) ['invoice' => $invoice]);
                    });
                    $model::updated(function ($invoice) {
                        // Détecter le passage au statut "paid"/"completed"
                        $status = $invoice->status ?? '';
                        $wasStatus = $invoice->getOriginal('status') ?? '';
                        if (in_array($status, ['paid', 'completed']) && !in_array($wasStatus, ['paid', 'completed'])) {
                            (new InvoicePaidListener())->handle((object) ['invoice' => $invoice]);
                        }
                    });
                    Log::info('[DWWL] Observer Invoice enregistré sur : ' . $model);
                    break;
                } catch (\Throwable $e) {
                    // Continuer
                }
            }
        }

        // ── Service ──────────────────────────────────────────────────────
        $serviceModels = [
            \App\Models\Provisioning\Service::class,
            \App\Models\Service::class,
        ];

        foreach ($serviceModels as $model) {
            if (class_exists($model)) {
                try {
                    $model::created(function ($service) {
                        (new ServiceCreatedListener())->handle((object) ['service' => $service]);
                    });
                    $model::updated(function ($service) {
                        $status    = $service->status ?? '';
                        $wasStatus = $service->getOriginal('status') ?? '';

                        // Détecter la mise à niveau du service (changement de produit)
                        $productId = $service->product_id ?? null;
                        $wasProductId = $service->getOriginal('product_id') ?? null;
                        if ($productId !== null && $wasProductId !== null && $productId != $wasProductId) {
                            (new ServiceUpgradedListener())->handle((object) ['service' => $service]);
                        }

                        if ($status === $wasStatus) return;

                        if ($status === 'suspended') {
                            (new ServiceSuspendedListener())->handle((object) ['service' => $service]);
                        } elseif ($status === 'active' && $wasStatus === 'suspended') {
                            (new ServiceUnsuspendedListener())->handle((object) ['service' => $service]);
                        } elseif (in_array($status, ['expired', 'terminated', 'cancelled'])) {
                            (new ServiceExpiredListener())->handle((object) ['service' => $service]);
                        }
                    });
                    Log::info('[DWWL] Observer Service enregistré sur : ' . $model);
                    break;
                } catch (\Throwable $e) {
                    // Continuer
                }
            }
        }
    }

    /**
     * Enregistre les listeners sur les noms d'événements possibles de ClientXCMS.
     */
    private function registerEventListeners(): void
    {
        // ── Customer / Utilisateur ───────────────────────────────────────
        $customerEvents = [
            'App\Events\Customer\CustomerCreated',
            'App\Events\Auth\Registered',
            'App\Events\Core\Customer\CustomerCreated',
            'customer.created',
        ];
        foreach ($customerEvents as $eventClass) {
            Event::listen($eventClass, CustomerCreatedListener::class);
        }

        // ── Facture créée ────────────────────────────────────────────────
        $invoiceCreatedEvents = [
            'App\Events\Invoice\InvoiceCreated',
            'App\Events\Billing\InvoiceCreated',
            'invoice.created',
        ];
        foreach ($invoiceCreatedEvents as $eventClass) {
            Event::listen($eventClass, InvoiceCreatedListener::class);
        }

        // ── Facture payée ────────────────────────────────────────────────
        $invoicePaidEvents = [
            'App\Events\Invoice\InvoiceCompleted',
            'App\Events\Invoice\InvoicePaid',
            'App\Events\Billing\InvoicePaid',
            'App\Events\Billing\InvoiceCompleted',
            'invoice.paid',
            'invoice.completed',
        ];
        foreach ($invoicePaidEvents as $eventClass) {
            Event::listen($eventClass, InvoicePaidListener::class);
        }

        // ── Service créé ─────────────────────────────────────────────────
        $serviceCreatedEvents = [
            'App\Events\Provisioning\ServiceCreated',
            'App\Events\Service\ServiceCreated',
            'service.created',
        ];
        foreach ($serviceCreatedEvents as $eventClass) {
            Event::listen($eventClass, ServiceCreatedListener::class);
        }

        // ── Service suspendu ─────────────────────────────────────────────
        $serviceSuspendedEvents = [
            'App\Events\Provisioning\ServiceSuspended',
            'App\Events\Service\ServiceSuspended',
            'service.suspended',
        ];
        foreach ($serviceSuspendedEvents as $eventClass) {
            Event::listen($eventClass, ServiceSuspendedListener::class);
        }

        // ── Service réactivé ─────────────────────────────────────────────
        $serviceUnsuspendedEvents = [
            'App\Events\Provisioning\ServiceUnsuspended',
            'App\Events\Service\ServiceUnsuspended',
            'service.unsuspended',
        ];
        foreach ($serviceUnsuspendedEvents as $eventClass) {
            Event::listen($eventClass, ServiceUnsuspendedListener::class);
        }

        // ── Service expiré / terminé ─────────────────────────────────────
        $serviceExpiredEvents = [
            'App\Events\Provisioning\ServiceTerminated',
            'App\Events\Provisioning\ServiceExpired',
            'App\Events\Service\ServiceTerminated',
            'App\Events\Service\ServiceExpired',
            'service.expired',
            'service.terminated',
        ];
        foreach ($serviceExpiredEvents as $eventClass) {
            Event::listen($eventClass, ServiceExpiredListener::class);
        }

        // ── Service mis à niveau ─────────────────────────────────────────
        $serviceUpgradedEvents = [
            'App\Events\Provisioning\ServiceUpgraded',
            'App\Events\Service\ServiceUpgraded',
            'service.upgraded',
        ];
        foreach ($serviceUpgradedEvents as $eventClass) {
            Event::listen($eventClass, ServiceUpgradedListener::class);
        }
    }
}
