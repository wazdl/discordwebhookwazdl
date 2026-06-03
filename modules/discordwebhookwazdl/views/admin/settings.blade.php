@extends('admin.layouts.admin')

@section('title', 'Discord Webhook — WazdL')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    .dw-wrap {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        --clr-bg:            #f8fafc;
        --clr-surface:       #ffffff;
        --clr-surface-2:     #f1f5f9;
        --clr-border:        rgba(0, 0, 0, 0.08);
        --clr-border-hover:  rgba(0, 0, 0, 0.16);
        --clr-text:          #0f172a;
        --clr-text-muted:    #64748b;
        --clr-text-dim:      #475569;
        --clr-hover:         rgba(0, 0, 0, 0.03);
        --clr-discord:       #5865F2;
        --clr-discord-light: #7983f5;
        --clr-emerald:       #10b981;
        --clr-rose:          #f43f5e;
        --clr-amber:         #f59e0b;
        --radius-lg:         16px;
        --radius-md:         12px;
        --radius-sm:         8px;
        --transition:        all 0.25s cubic-bezier(0.4,0,0.2,1);
        color: var(--clr-text);
    }

    /* Dark Theme Adaptive Override */
    :root[data-theme="dark"] .dw-wrap,
    html[data-theme="dark"] .dw-wrap,
    body[data-theme="dark"] .dw-wrap,
    :root[data-bs-theme="dark"] .dw-wrap,
    html[data-bs-theme="dark"] .dw-wrap,
    body[data-bs-theme="dark"] .dw-wrap,
    .theme-dark .dw-wrap,
    .dark .dw-wrap {
        --clr-bg:            #0f1117;
        --clr-surface:       #161b27;
        --clr-surface-2:     #1c2333;
        --clr-border:        rgba(255,255,255,0.07);
        --clr-border-hover:  rgba(255,255,255,0.14);
        --clr-text:          #e2e8f0;
        --clr-text-muted:    #64748b;
        --clr-text-dim:      #94a3b8;
        --clr-hover:         rgba(255,255,255,0.02);
    }

    .dw-wrap * { box-sizing: border-box; }

    /* ── BREADCRUMB ── */
    .dw-breadcrumb {
        display: flex; align-items: center; gap: 0.4rem;
        font-size: 0.78rem; color: var(--clr-text-muted);
        margin-bottom: 1.25rem; font-weight: 500; letter-spacing: 0.02em;
    }
    .dw-breadcrumb i { font-size: 0.6rem; opacity: 0.5; }
    .dw-breadcrumb a { color: var(--clr-text-muted); text-decoration: none; transition: color 0.2s; }
    .dw-breadcrumb a:hover { color: var(--clr-discord-light); }
    .dw-breadcrumb span:last-child { color: var(--clr-text-dim); }

    /* ── HEADER ── */
    .dw-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
    }
    .dw-brand { display: flex; align-items: center; gap: 1rem; }
    .dw-brand-logo {
        width: 52px; height: 52px; border-radius: var(--radius-md);
        background: linear-gradient(135deg, #5865F2, #7289da);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: white;
        box-shadow: 0 4px 20px rgba(88,101,242,0.4);
        flex-shrink: 0; position: relative; overflow: hidden;
    }
    .dw-brand-logo::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
    }
    .dw-brand-text h1 {
        font-size: 1.5rem; font-weight: 800; margin: 0;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #0f172a, #334155);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    :root[data-theme="dark"] .dw-brand-text h1,
    html[data-theme="dark"] .dw-brand-text h1,
    body[data-theme="dark"] .dw-brand-text h1,
    :root[data-bs-theme="dark"] .dw-brand-text h1,
    html[data-bs-theme="dark"] .dw-brand-text h1,
    body[data-bs-theme="dark"] .dw-brand-text h1,
    .theme-dark .dw-brand-text h1,
    .dark .dw-brand-text h1 {
        background: linear-gradient(135deg, #e2e8f0, #94a3b8);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .dw-brand-text p { margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--clr-text-muted); }

    /* ── ALERT ── */
    .dw-alert {
        display: flex; align-items: flex-start; gap: 0.75rem;
        border-radius: var(--radius-md); padding: 0.9rem 1.1rem;
        margin-bottom: 1.5rem; font-size: 0.85rem;
    }
    .dw-alert--success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #34d399; }
    .dw-alert--error   { background: rgba(244,63,94,0.1);  border: 1px solid rgba(244,63,94,0.2);  color: #fb7185; }
    .dw-alert i { flex-shrink: 0; font-size: 1rem; margin-top: 0.1rem; }

    /* ── GRID 2 COLS ── */
    .dw-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; align-items: start; }
    @media (max-width: 1100px) { .dw-grid { grid-template-columns: 1fr; } }

    /* ── CARD ── */
    .dw-card {
        background: var(--clr-surface); border: 1px solid var(--clr-border);
        border-radius: var(--radius-lg); overflow: hidden;
        margin-bottom: 1.25rem; transition: var(--transition);
    }
    .dw-card:last-child { margin-bottom: 0; }
    .dw-card:hover { border-color: var(--clr-border-hover); }

    .dw-card-head {
        padding: 1.1rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid var(--clr-border); gap: 1rem;
    }
    .dw-card-title {
        display: flex; align-items: center; gap: 0.65rem;
        font-size: 0.95rem; font-weight: 700; color: var(--clr-text); margin: 0;
    }
    .dw-card-title-icon {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
    }
    .dw-icon--discord  { background: rgba(88,101,242,0.15); color: #7983f5; }
    .dw-icon--emerald  { background: rgba(16,185,129,0.15);  color: #34d399; }
    .dw-icon--amber    { background: rgba(245,158,11,0.15);   color: #fbbf24; }
    .dw-icon--violet   { background: rgba(139,92,246,0.15);   color: #a78bfa; }
    .dw-icon--gray     { background: rgba(100,116,139,0.15);  color: #94a3b8; }

    .dw-card-body { padding: 1.5rem; }

    /* ── FORM ── */
    .dw-form-group { margin-bottom: 1.25rem; }
    .dw-form-group:last-child { margin-bottom: 0; }

    .dw-label {
        display: block; font-size: 0.8rem; font-weight: 600;
        color: var(--clr-text-dim); margin-bottom: 0.45rem; letter-spacing: 0.01em;
    }
    .dw-label-hint {
        display: block; font-size: 0.72rem; font-weight: 400;
        color: var(--clr-text-muted); margin-top: 0.15rem;
    }

    .dw-input {
        width: 100%; background: var(--clr-surface-2);
        border: 1px solid var(--clr-border); border-radius: var(--radius-sm);
        color: var(--clr-text); padding: 0.6rem 0.9rem;
        font-size: 0.875rem; font-family: inherit;
        transition: var(--transition); outline: none;
    }
    .dw-input:focus { border-color: var(--clr-discord); box-shadow: 0 0 0 3px rgba(88,101,242,0.15); }
    .dw-input::placeholder { color: var(--clr-text-muted); }

    .dw-input-group { display: flex; gap: 0.5rem; }
    .dw-input-group .dw-input { flex: 1; }

    /* ── TOGGLE EVENTS TABLE ── */
    .dw-events-grid { display: flex; flex-direction: column; gap: 0; }

    .dw-event-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid var(--clr-border);
        gap: 1rem; transition: background 0.2s;
    }
    .dw-event-row:last-child { border-bottom: none; }
    .dw-event-row:hover { background: var(--clr-hover); }

    .dw-event-left { display: flex; align-items: center; gap: 0.85rem; flex: 1; min-width: 0; }
    .dw-event-emoji {
        width: 36px; height: 36px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
        background: var(--clr-surface-2); border: 1px solid var(--clr-border);
    }
    .dw-event-info h4 { margin: 0 0 0.15rem; font-size: 0.85rem; font-weight: 600; color: var(--clr-text); }
    .dw-event-info p { margin: 0; font-size: 0.72rem; color: var(--clr-text-muted); }

    .dw-event-right { display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0; }

    /* Color picker inline */
    .dw-color-input {
        width: 36px; height: 36px;
        border-radius: 8px; border: 1px solid var(--clr-border);
        cursor: pointer; padding: 2px;
        background: var(--clr-surface-2);
        transition: var(--transition);
    }
    .dw-color-input:hover { border-color: var(--clr-discord); transform: scale(1.05); }

    /* Toggle switch */
    .dw-toggle { position: relative; display: inline-flex; align-items: center; cursor: pointer; }
    .dw-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
    .dw-toggle-track {
        width: 44px; height: 24px; background: var(--clr-surface-2);
        border: 1px solid var(--clr-border); border-radius: 100px;
        transition: var(--transition); position: relative;
    }
    .dw-toggle-thumb {
        position: absolute; top: 3px; left: 3px;
        width: 16px; height: 16px; border-radius: 50%;
        background: var(--clr-text-muted); transition: var(--transition);
    }
    .dw-toggle input:checked ~ .dw-toggle-track { background: var(--clr-discord); border-color: var(--clr-discord); }
    .dw-toggle input:checked ~ .dw-toggle-track .dw-toggle-thumb { transform: translateX(20px); background: white; }

    /* ── BUTTONS ── */
    .dw-btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1.1rem; border-radius: var(--radius-sm);
        font-weight: 600; font-size: 0.82rem; text-decoration: none;
        cursor: pointer; transition: var(--transition);
        border: 1px solid transparent; white-space: nowrap;
        font-family: inherit;
    }
    .dw-btn--primary {
        background: linear-gradient(135deg, #5865F2, #7289da);
        color: white; box-shadow: 0 4px 15px rgba(88,101,242,0.35);
    }
    .dw-btn--primary:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(88,101,242,0.5); }
    .dw-btn--ghost {
        background: var(--clr-surface-2); color: var(--clr-text-dim);
        border-color: var(--clr-border);
    }
    .dw-btn--ghost:hover { background: rgba(255,255,255,0.06); border-color: var(--clr-border-hover); color: var(--clr-text); transform: translateY(-1px); }
    .dw-btn--emerald { background: rgba(16,185,129,0.15); color: #34d399; border-color: rgba(16,185,129,0.25); }
    .dw-btn--emerald:hover { background: rgba(16,185,129,0.25); transform: translateY(-1px); }
    .dw-btn--danger { background: rgba(244,63,94,0.12); color: #fb7185; border-color: rgba(244,63,94,0.2); }
    .dw-btn--danger:hover { background: rgba(244,63,94,0.2); transform: translateY(-1px); }
    .dw-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

    /* ── STATUS PREVIEW ── */
    .dw-status-box {
        background: var(--clr-surface-2); border: 1px solid var(--clr-border);
        border-radius: var(--radius-md); padding: 1rem 1.1rem;
        margin-bottom: 1rem;
    }
    .dw-status-row { display: flex; align-items: center; gap: 0.6rem; font-size: 0.82rem; }
    .dw-status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .dw-status-dot--green  { background: #34d399; box-shadow: 0 0 6px rgba(52,211,153,0.5); }
    .dw-status-dot--red    { background: #f87171; }
    .dw-status-dot--yellow { background: #fbbf24; }
    .dw-status-label { font-weight: 600; color: var(--clr-text); }
    .dw-status-sub { font-size: 0.72rem; color: var(--clr-text-muted); margin-top: 0.25rem; margin-left: 1.4rem; }

    /* ── DISCORD EMBED PREVIEW ── */
    .dw-embed-preview {
        background: #2b2d31; border-radius: 8px;
        padding: 0.75rem 1rem 0.75rem 0.75rem;
        margin-top: 1rem; position: relative; overflow: hidden;
        font-family: 'Inter', sans-serif;
    }
    .dw-embed-stripe {
        position: absolute; top: 0; left: 0; bottom: 0;
        width: 4px; background: #5865F2; border-radius: 4px 0 0 4px;
    }
    .dw-embed-content { margin-left: 0.5rem; }
    .dw-embed-author { font-size: 0.78rem; font-weight: 600; color: #e3e5e8; margin-bottom: 0.3rem; }
    .dw-embed-title { font-size: 0.9rem; font-weight: 700; color: #ffffff; margin-bottom: 0.4rem; }
    .dw-embed-desc { font-size: 0.78rem; color: #b5bac1; margin-bottom: 0.6rem; line-height: 1.5; }
    .dw-embed-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem; }
    .dw-embed-field-name { font-size: 0.7rem; font-weight: 700; color: #e3e5e8; margin-bottom: 0.1rem; }
    .dw-embed-field-value { font-size: 0.72rem; color: #b5bac1; }
    .dw-embed-footer { font-size: 0.68rem; color: #87898c; margin-top: 0.6rem; display: flex; align-items: center; gap: 0.3rem; }
    .dw-embed-footer::before { content: ''; width: 3px; height: 3px; border-radius: 50%; background: #87898c; }

    /* ── TOAST ── */
    #dw-toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
        display: flex; align-items: center; gap: 0.65rem;
        padding: 0.75rem 1.15rem; border-radius: var(--radius-md);
        font-size: 0.85rem; font-weight: 600; font-family: 'Inter', sans-serif;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        transform: translateY(120%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
        pointer-events: none; max-width: 380px;
    }
    #dw-toast.show { transform: translateY(0); }
    #dw-toast.success { background: #1e3a2f; color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
    #dw-toast.error   { background: #3b1f22; color: #fb7185; border: 1px solid rgba(251,113,133,0.3); }
</style>

<div class="dw-wrap">
    {{-- BREADCRUMB --}}
    <nav class="dw-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i> Tableau de bord</a>
        <i class="bi bi-chevron-right"></i>
        <span>Discord Webhook</span>
        <i class="bi bi-chevron-right"></i>
        <span>Configuration</span>
    </nav>

    {{-- HEADER --}}
    <div class="dw-header">
        <div class="dw-brand">
            <div class="dw-brand-logo"><i class="bi bi-discord"></i></div>
            <div class="dw-brand-text">
                <h1>Discord Webhook</h1>
                <p>Notifications en temps réel • WazdL Team</p>
            </div>
        </div>
        <div style="display:flex;gap:0.65rem;flex-wrap:wrap;">
            {{-- Hiding diagnostic console for clean production free module release --}}
            {{-- <a href="{{ route('admin.discordwebhookwazdl.diagnostic') }}" class="dw-btn dw-btn--ghost">
                <i class="bi bi-bug"></i> Diagnostic
            </a> --}}
            <button id="btn-test" class="dw-btn dw-btn--emerald" type="button">
                <i class="bi bi-send-check"></i> Tester le Webhook
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div class="dw-alert dw-alert--success">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error') || $errors->any())
    <div class="dw-alert dw-alert--error">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('error') ?? $errors->first() }}</span>
    </div>
    @endif

    <form action="{{ route('admin.discordwebhookwazdl.update') }}" method="POST" id="dw-form">
        @csrf

        <div class="dw-grid">
            {{-- COLONNE PRINCIPALE --}}
            <div>
                {{-- CARD: Webhook & Identité --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--discord"><i class="bi bi-link-45deg"></i></span>
                            Webhook & Identité du Bot
                        </h2>
                    </div>
                    <div class="dw-card-body">
                        <div class="dw-form-group">
                            <label class="dw-label" for="webhook_url">
                                URL du Webhook Discord <span style="color:var(--clr-rose)">*</span>
                                <span class="dw-label-hint">Discord → Paramètres du serveur → Intégrations → Webhooks → Copier l'URL</span>
                            </label>
                            <div class="dw-input-group">
                                <input id="webhook_url" name="webhook_url" type="url" class="dw-input"
                                    value="{{ old('webhook_url', $settings->webhook_url) }}"
                                    placeholder="https://discord.com/api/webhooks/...">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div class="dw-form-group" style="margin-bottom:0;">
                                <label class="dw-label" for="webhook_username">
                                    Nom du bot (Username)
                                    <span class="dw-label-hint">Affiché comme auteur du message</span>
                                </label>
                                <input id="webhook_username" name="webhook_username" type="text" class="dw-input"
                                    value="{{ old('webhook_username', $settings->webhook_username ?? 'WazdL Panel') }}"
                                    placeholder="WazdL Panel" maxlength="80">
                            </div>
                            <div class="dw-form-group" style="margin-bottom:0;">
                                <label class="dw-label" for="webhook_avatar_url">
                                    Avatar du bot (URL image)
                                    <span class="dw-label-hint">URL PNG/JPG de l'icône</span>
                                </label>
                                <input id="webhook_avatar_url" name="webhook_avatar_url" type="url" class="dw-input"
                                    value="{{ old('webhook_avatar_url', $settings->webhook_avatar_url) }}"
                                    placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD: Footer & Mention --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--violet"><i class="bi bi-layout-text-sidebar"></i></span>
                            Footer & Mention
                        </h2>
                    </div>
                    <div class="dw-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div class="dw-form-group" style="margin-bottom:0;">
                                <label class="dw-label" for="embed_footer_text">
                                    Texte du footer
                                    <span class="dw-label-hint">Ex : WazdL Panel • Pelican</span>
                                </label>
                                <input id="embed_footer_text" name="embed_footer_text" type="text" class="dw-input"
                                    value="{{ old('embed_footer_text', $settings->embed_footer_text ?? 'WazdL Panel') }}"
                                    placeholder="WazdL Panel" maxlength="120">
                            </div>
                            <div class="dw-form-group" style="margin-bottom:0;">
                                <label class="dw-label" for="embed_footer_icon">
                                    Icône du footer (URL image)
                                    <span class="dw-label-hint">Petite icône à gauche du footer</span>
                                </label>
                                <input id="embed_footer_icon" name="embed_footer_icon" type="url" class="dw-input"
                                    value="{{ old('embed_footer_icon', $settings->embed_footer_icon) }}"
                                    placeholder="https://...">
                            </div>
                        </div>
                        <div class="dw-form-group" style="margin-top:1rem;margin-bottom:0;">
                            <label class="dw-label" for="mention_role">
                                Mention (optionnel)
                                <span class="dw-label-hint">ID de rôle numérique, <code style="background:var(--clr-surface-2);padding:0.1rem 0.3rem;border-radius:4px;font-size:0.7rem;">@everyone</code> ou <code style="background:var(--clr-surface-2);padding:0.1rem 0.3rem;border-radius:4px;font-size:0.7rem;">@here</code></span>
                            </label>
                            <input id="mention_role" name="mention_role" type="text" class="dw-input"
                                value="{{ old('mention_role', $settings->mention_role) }}"
                                placeholder="1234567890 ou @everyone" maxlength="100">
                        </div>
                    </div>
                </div>

                {{-- CARD: Événements & Couleurs --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--amber"><i class="bi bi-bell-fill"></i></span>
                            Événements & Couleurs
                        </h2>
                        <span style="font-size:0.75rem;color:var(--clr-text-muted);">Toggle = activer • Couleur = personnaliser</span>
                    </div>
                    <div class="dw-events-grid">
                        @php
                        $events = [
                            ['key' => 'customer_created',    'emoji' => '🆕', 'label' => 'Nouveau client inscrit',      'desc' => 'Déclenchée lors de la création d\'un compte client',   'default_color' => '5865F2'],
                            ['key' => 'invoice_created',     'emoji' => '🧾', 'label' => 'Facture créée',               'desc' => 'Déclenchée lors de la génération d\'une nouvelle facture', 'default_color' => '5865F2'],
                            ['key' => 'invoice_paid',        'emoji' => '💰', 'label' => 'Paiement reçu',               'desc' => 'Déclenchée lorsqu\'une facture est réglée',            'default_color' => '57F287'],
                            ['key' => 'service_created',     'emoji' => '🚀', 'label' => 'Service créé',                'desc' => 'Déclenchée lors du provisioning d\'un nouveau service', 'default_color' => '3BA55C'],
                            ['key' => 'service_suspended',   'emoji' => '⚠️', 'label' => 'Service suspendu',            'desc' => 'Déclenchée lors de la suspension d\'un service',       'default_color' => 'FEE75C'],
                            ['key' => 'service_unsuspended', 'emoji' => '✅', 'label' => 'Service réactivé',            'desc' => 'Déclenchée lors de la réactivation d\'un service',     'default_color' => '57F287'],
                            ['key' => 'service_expired',     'emoji' => '❌', 'label' => 'Service expiré / supprimé',   'desc' => 'Déclenchée à l\'expiration ou à la résiliation',       'default_color' => 'ED4245'],
                            ['key' => 'service_upgraded',    'emoji' => '⬆️', 'label' => 'Service mis à niveau',        'desc' => 'Déclenchée lors d\'un upgrade ou downgrade',           'default_color' => 'EB459E'],
                        ];
                        @endphp

                        @foreach($events as $ev)
                        @php
                            $notifyField = 'notify_' . $ev['key'];
                            $colorField  = 'color_'  . $ev['key'];
                            $isEnabled   = old($notifyField, $settings->{$notifyField} ?? true);
                            $colorVal    = old($colorField,  $settings->{$colorField}  ?? $ev['default_color']);
                        @endphp
                        <div class="dw-event-row">
                            <div class="dw-event-left">
                                <div class="dw-event-emoji">{{ $ev['emoji'] }}</div>
                                <div class="dw-event-info">
                                    <h4>{{ $ev['label'] }}</h4>
                                    <p>{{ $ev['desc'] }}</p>
                                </div>
                            </div>
                            <div class="dw-event-right">
                                {{-- Color picker --}}
                                <div style="display:flex;flex-direction:column;align-items:center;gap:0.2rem;">
                                    <input type="color" class="dw-color-input" name="{{ $colorField }}" id="{{ $colorField }}"
                                        value="#{{ $colorVal }}" title="Couleur de l'embed">
                                    <span style="font-size:0.6rem;color:var(--clr-text-muted);">Couleur</span>
                                </div>
                                {{-- Toggle --}}
                                <label class="dw-toggle" title="{{ $isEnabled ? 'Désactiver' : 'Activer' }}">
                                    <input type="checkbox" name="{{ $notifyField }}" value="1" {{ $isEnabled ? 'checked' : '' }}>
                                    <div class="dw-toggle-track">
                                        <div class="dw-toggle-thumb"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- BOUTON SAVE --}}
                <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:0.5rem;">
                    <button type="reset" class="dw-btn dw-btn--ghost"><i class="bi bi-arrow-counterclockwise"></i> Réinitialiser</button>
                    <button type="submit" class="dw-btn dw-btn--primary"><i class="bi bi-floppy2-fill"></i> Sauvegarder la configuration</button>
                </div>
            </div>

            {{-- COLONNE DROITE --}}
            <div>
                {{-- STATUS --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--emerald"><i class="bi bi-activity"></i></span>
                            Statut du module
                        </h2>
                    </div>
                    <div class="dw-card-body">
                        @php $webhookSet = !empty($settings->webhook_url); @endphp
                        <div class="dw-status-box">
                            <div class="dw-status-row">
                                <div class="dw-status-dot {{ $webhookSet ? 'dw-status-dot--green' : 'dw-status-dot--red' }}"></div>
                                <span class="dw-status-label">{{ $webhookSet ? 'Webhook configuré' : 'Webhook non configuré' }}</span>
                            </div>
                            <div class="dw-status-sub">
                                @if($webhookSet)
                                    {{ Str::limit($settings->webhook_url, 45) }}
                                @else
                                    Aucune URL de webhook renseignée
                                @endif
                            </div>
                        </div>
                        @php
                            $activeCount = collect([
                                $settings->notify_customer_created,
                                $settings->notify_invoice_created,
                                $settings->notify_invoice_paid,
                                $settings->notify_service_created,
                                $settings->notify_service_suspended,
                                $settings->notify_service_unsuspended,
                                $settings->notify_service_expired,
                                $settings->notify_service_upgraded,
                            ])->filter()->count();
                        @endphp
                        <div class="dw-status-box" style="margin-bottom:0;">
                            <div class="dw-status-row">
                                <div class="dw-status-dot {{ $activeCount > 0 ? 'dw-status-dot--green' : 'dw-status-dot--yellow' }}"></div>
                                <span class="dw-status-label">{{ $activeCount }} / 8 événements actifs</span>
                            </div>
                            <div class="dw-status-sub">Bot : <strong>{{ $settings->webhook_username ?? 'WazdL Panel' }}</strong></div>
                        </div>
                    </div>
                </div>

                {{-- APERÇU EMBED --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--discord"><i class="bi bi-eye"></i></span>
                            Aperçu embed Discord
                        </h2>
                    </div>
                    <div class="dw-card-body">
                        <div class="dw-embed-preview" id="embed-preview">
                            <div class="dw-embed-stripe" id="embed-stripe" style="background:#5865F2;"></div>
                            <div class="dw-embed-content">
                                <div class="dw-embed-author" id="preview-username">🤖 WazdL Panel</div>
                                <div class="dw-embed-title">🆕 Nouveau client inscrit</div>
                                <div class="dw-embed-desc">Un nouveau compte client vient d'être créé sur le panel.</div>
                                <div class="dw-embed-fields">
                                    <div>
                                        <div class="dw-embed-field-name">👤 Nom</div>
                                        <div class="dw-embed-field-value">Jean Dupont</div>
                                    </div>
                                    <div>
                                        <div class="dw-embed-field-name">📧 Email</div>
                                        <div class="dw-embed-field-value">jean@example.com</div>
                                    </div>
                                    <div>
                                        <div class="dw-embed-field-name">🆔 ID Client</div>
                                        <div class="dw-embed-field-value">42</div>
                                    </div>
                                    <div>
                                        <div class="dw-embed-field-name">📅 Inscription</div>
                                        <div class="dw-embed-field-value">{{ now()->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <div class="dw-embed-footer" id="preview-footer">
                                    <span id="preview-footer-text">{{ $settings->embed_footer_text ?? 'WazdL Panel' }}</span>
                                    <span style="opacity:0.5"> • {{ now()->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        <p style="font-size:0.72rem;color:var(--clr-text-muted);margin:0.75rem 0 0;text-align:center;">
                            Aperçu mis à jour en temps réel selon votre configuration
                        </p>
                    </div>
                </div>

                {{-- GUIDE --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--gray"><i class="bi bi-question-circle"></i></span>
                            Guide rapide
                        </h2>
                    </div>
                    <div class="dw-card-body" style="padding:1.25rem 1.5rem;">
                        <ol style="margin:0;padding:0 0 0 1.2rem;color:var(--clr-text-dim);font-size:0.8rem;line-height:1.9;">
                            <li>Sur Discord, accédez aux <strong>Paramètres du serveur</strong></li>
                            <li>Cliquez sur <strong>Intégrations → Webhooks</strong></li>
                            <li>Créez un nouveau webhook et copiez son URL</li>
                            <li>Collez-la dans le champ <em>URL du Webhook</em> ci-contre</li>
                            <li>Personnalisez les couleurs et activez les événements souhaités</li>
                            <li>Cliquez sur <strong>Tester le Webhook</strong> pour valider</li>
                        </ol>
                        <div style="margin-top:1rem;padding:0.75rem;background:rgba(88,101,242,0.08);border:1px solid rgba(88,101,242,0.15);border-radius:var(--radius-sm);">
                            <p style="margin:0;font-size:0.75rem;color:var(--clr-discord-light);">
                                <i class="bi bi-info-circle"></i>
                                <strong> Info :</strong> Le module fonctionne sur les événements internes de ClientXCMS. Aucune configuration supplémentaire n'est requise.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- INFO MODULE --}}
                <div class="dw-card">
                    <div class="dw-card-head">
                        <h2 class="dw-card-title">
                            <span class="dw-card-title-icon dw-icon--discord"><i class="bi bi-info-circle"></i></span>
                            À propos du module
                        </h2>
                    </div>
                    <div class="dw-card-body" style="padding:1.1rem 1.5rem;">
                        <div style="display:flex;flex-direction:column;gap:0.5rem;">
                            <div style="display:flex;justify-content:space-between;font-size:0.8rem;">
                                <span style="color:var(--clr-text-muted);">Version</span>
                                <span style="font-weight:600;color:var(--clr-text);">1.0</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:0.8rem;">
                                <span style="color:var(--clr-text-muted);">Auteur</span>
                                <span style="font-weight:600;color:var(--clr-discord-light);">WazdL Team</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:0.8rem;">
                                <span style="color:var(--clr-text-muted);">Événements</span>
                                <span style="font-weight:600;color:var(--clr-text);">8 types</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:0.8rem;">
                                <span style="color:var(--clr-text-muted);">Compatibilité</span>
                                <span style="font-weight:600;color:var(--clr-emerald);">ClientXCMS</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- TOAST --}}
<div id="dw-toast" role="alert" aria-live="polite">
    <i id="dw-toast-icon" class="bi bi-check-circle-fill"></i>
    <span id="dw-toast-msg"></span>
</div>

<script>
(function () {
    'use strict';

    // ── Live preview ────────────────────────────────────────────────
    const usernameInput    = document.getElementById('webhook_username');
    const footerTextInput  = document.getElementById('embed_footer_text');
    const colorInput       = document.getElementById('color_customer_created');
    const previewUsername  = document.getElementById('preview-username');
    const previewFooter    = document.getElementById('preview-footer-text');
    const embedStripe      = document.getElementById('embed-stripe');

    function updatePreview() {
        if (usernameInput && previewUsername) {
            previewUsername.textContent = '🤖 ' + (usernameInput.value || 'WazdL Panel');
        }
        if (footerTextInput && previewFooter) {
            previewFooter.textContent = footerTextInput.value || 'WazdL Panel';
        }
        if (colorInput && embedStripe) {
            embedStripe.style.background = colorInput.value;
        }
    }

    [usernameInput, footerTextInput, colorInput].forEach(el => {
        if (el) el.addEventListener('input', updatePreview);
    });

    // ── Toast helper ─────────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('dw-toast');
        const icon  = document.getElementById('dw-toast-icon');
        const text  = document.getElementById('dw-toast-msg');
        toast.className = 'show ' + type;
        icon.className  = 'bi ' + (type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill');
        text.textContent = msg;
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    // ── Bouton Test Webhook ───────────────────────────────────────────
    document.getElementById('btn-test')?.addEventListener('click', async function () {
        const url = document.getElementById('webhook_url')?.value?.trim();
        if (!url) {
            showToast('⚠️ Renseignez d\'abord l\'URL du webhook !', 'error');
            return;
        }
        this.disabled = true;
        const orig = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:14px;height:14px;border-width:2px;display:inline-block;border-radius:50%;border-color:#34d399 transparent transparent;animation:spin .7s linear infinite;"></span> Envoi...';

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                       || document.querySelector('input[name="_token"]')?.value;

            const res = await fetch('{{ route('admin.discordwebhookwazdl.test') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                },
                body: JSON.stringify({ webhook_url: url })
            });

            const data = await res.json();
            showToast(data.message, data.success ? 'success' : 'error');
        } catch (e) {
            showToast('❌ Erreur réseau lors du test.', 'error');
        } finally {
            this.disabled = false;
            this.innerHTML = orig;
        }
    });

    // ── Spinner CSS ────────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);

})();
</script>
@endsection
