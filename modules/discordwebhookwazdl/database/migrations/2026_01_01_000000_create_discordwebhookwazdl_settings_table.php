<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discordwebhookwazdl_settings', function (Blueprint $table) {
            $table->id();

            // ── Webhook Discord ──────────────────────────────────────
            $table->string('webhook_url')->nullable()->comment('URL du webhook Discord');
            $table->string('webhook_username')->default('WazdL Panel')->comment('Nom affiché comme auteur du message');
            $table->string('webhook_avatar_url')->nullable()->comment('URL de l\'avatar du bot webhook');

            // ── Embed Discord ─────────────────────────────────────────
            $table->string('embed_footer_text')->default('WazdL Panel')->comment('Texte du footer des embeds');
            $table->string('embed_footer_icon')->nullable()->comment('URL de l\'icône du footer');

            // ── Mention ──────────────────────────────────────────────
            $table->string('mention_role')->nullable()->comment('ID de rôle à mentionner (ex: 1234567890 ou @everyone)');

            // ── Couleurs par événement (hex sans #) ──────────────────
            $table->string('color_customer_created')->default('5865F2')->comment('Couleur embed : nouveau client');
            $table->string('color_invoice_paid')->default('57F287')->comment('Couleur embed : facture payée');
            $table->string('color_service_created')->default('3BA55C')->comment('Couleur embed : service créé');
            $table->string('color_service_suspended')->default('FEE75C')->comment('Couleur embed : service suspendu');
            $table->string('color_service_unsuspended')->default('57F287')->comment('Couleur embed : service réactivé');
            $table->string('color_service_expired')->default('ED4245')->comment('Couleur embed : service expiré');
            $table->string('color_service_upgraded')->default('EB459E')->comment('Couleur embed : service mis à niveau');
            $table->string('color_invoice_created')->default('5865F2')->comment('Couleur embed : facture créée');

            // ── Activation par événement ─────────────────────────────
            $table->boolean('notify_customer_created')->default(true);
            $table->boolean('notify_invoice_created')->default(true);
            $table->boolean('notify_invoice_paid')->default(true);
            $table->boolean('notify_service_created')->default(true);
            $table->boolean('notify_service_suspended')->default(true);
            $table->boolean('notify_service_unsuspended')->default(true);
            $table->boolean('notify_service_expired')->default(true);
            $table->boolean('notify_service_upgraded')->default(true);

            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        \Illuminate\Support\Facades\DB::table('discordwebhookwazdl_settings')->insert([
            'webhook_url'           => null,
            'webhook_username'      => 'WazdL Panel',
            'webhook_avatar_url'    => null,
            'embed_footer_text'     => 'WazdL Panel',
            'embed_footer_icon'     => null,
            'mention_role'          => null,
            'color_customer_created'    => '5865F2',
            'color_invoice_paid'        => '57F287',
            'color_service_created'     => '3BA55C',
            'color_service_suspended'   => 'FEE75C',
            'color_service_unsuspended' => '57F287',
            'color_service_expired'     => 'ED4245',
            'color_service_upgraded'    => 'EB459E',
            'color_invoice_created'     => '5865F2',
            'notify_customer_created'    => true,
            'notify_invoice_created'     => true,
            'notify_invoice_paid'        => true,
            'notify_service_created'     => true,
            'notify_service_suspended'   => true,
            'notify_service_unsuspended' => true,
            'notify_service_expired'     => true,
            'notify_service_upgraded'    => true,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('discordwebhookwazdl_settings');
    }
};
