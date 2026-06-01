@extends('admin.layouts.admin')

@section('title', 'Diagnostic — Discord Webhook WazdL')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

    .dw-diag {
        font-family: 'Inter', system-ui, sans-serif;
        --clr-bg:           #0f1117;
        --clr-surface:      #161b27;
        --clr-surface-2:    #1c2333;
        --clr-border:       rgba(255,255,255,0.07);
        --clr-text:         #e2e8f0;
        --clr-text-muted:   #64748b;
        --clr-text-dim:     #94a3b8;
        --clr-discord:      #5865F2;
        --clr-emerald:      #10b981;
        --clr-amber:        #f59e0b;
        --clr-rose:         #f43f5e;
        --radius-lg:        16px;
        --radius-md:        12px;
        --radius-sm:        8px;
        color: var(--clr-text);
    }
    .dw-diag * { box-sizing: border-box; }

    .dw-breadcrumb {
        display: flex; align-items: center; gap: 0.4rem;
        font-size: 0.78rem; color: var(--clr-text-muted);
        margin-bottom: 1.25rem; font-weight: 500;
    }
    .dw-breadcrumb i { font-size: 0.6rem; opacity: 0.5; }
    .dw-breadcrumb a { color: var(--clr-text-muted); text-decoration: none; transition: color 0.2s; }
    .dw-breadcrumb a:hover { color: #7983f5; }

    .dw-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
    }
    .dw-brand { display: flex; align-items: center; gap: 1rem; }
    .dw-brand-logo {
        width: 48px; height: 48px; border-radius: var(--radius-md);
        background: linear-gradient(135deg, #f59e0b, #ef4444);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; color: white;
        box-shadow: 0 4px 20px rgba(245,158,11,0.35); flex-shrink: 0;
    }
    .dw-brand-text h1 {
        font-size: 1.4rem; font-weight: 800; margin: 0;
        background: linear-gradient(135deg, #e2e8f0, #94a3b8);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .dw-brand-text p { margin: 0.15rem 0 0; font-size: 0.78rem; color: var(--clr-text-muted); }

    .dw-card {
        background: var(--clr-surface); border: 1px solid var(--clr-border);
        border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 1.25rem;
    }
    .dw-card-head {
        padding: 1rem 1.5rem; display: flex; align-items: center;
        justify-content: space-between; border-bottom: 1px solid var(--clr-border); gap: 1rem;
    }
    .dw-card-title {
        display: flex; align-items: center; gap: 0.65rem;
        font-size: 0.95rem; font-weight: 700; color: var(--clr-text); margin: 0;
    }
    .dw-icon {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
    }
    .dw-icon--amber   { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .dw-icon--emerald { background: rgba(16,185,129,0.15); color: #34d399; }
    .dw-icon--discord { background: rgba(88,101,242,0.15); color: #7983f5; }

    /* Log terminal */
    .dw-log-wrap {
        background: #0d1117; border-radius: var(--radius-md);
        border: 1px solid var(--clr-border);
        max-height: 600px; overflow-y: auto; font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem; line-height: 1.6;
    }
    .dw-log-line {
        padding: 0.2rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.03);
        color: #94a3b8; word-break: break-all;
    }
    .dw-log-line:hover { background: rgba(255,255,255,0.03); }
    .dw-log-line .ts { color: #475569; margin-right: 0.5rem; }
    .dw-log-line .event-name { color: #7983f5; font-weight: 600; }
    .dw-log-line .label { color: #fbbf24; }
    .dw-log-line .observer-label { color: #34d399; }
    .dw-log-empty {
        padding: 3rem 1.5rem; text-align: center; color: var(--clr-text-muted);
    }
    .dw-log-empty i { font-size: 2rem; display: block; margin-bottom: 0.75rem; opacity: 0.4; }

    /* Hints */
    .dw-hint {
        border-radius: var(--radius-md); padding: 0.85rem 1.1rem;
        display: flex; gap: 0.75rem; align-items: flex-start;
        font-size: 0.82rem; margin-bottom: 1rem;
    }
    .dw-hint--amber  { background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); color: #fbbf24; }
    .dw-hint--emerald { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #34d399; }
    .dw-hint--blue   { background: rgba(88,101,242,0.08); border: 1px solid rgba(88,101,242,0.2); color: #7983f5; }
    .dw-hint i { flex-shrink: 0; margin-top: 0.1rem; }

    /* Buttons */
    .dw-btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.55rem 1rem; border-radius: var(--radius-sm);
        font-weight: 600; font-size: 0.82rem; cursor: pointer;
        transition: all 0.2s; border: 1px solid transparent; font-family: inherit;
        text-decoration: none;
    }
    .dw-btn--ghost { background: var(--clr-surface-2); color: var(--clr-text-dim); border-color: var(--clr-border); }
    .dw-btn--ghost:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.14); color: var(--clr-text); }
    .dw-btn--amber { background: rgba(245,158,11,0.15); color: #fbbf24; border-color: rgba(245,158,11,0.25); }
    .dw-btn--amber:hover { background: rgba(245,158,11,0.25); }

    /* Model grid */
    .dw-model-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; padding: 1.25rem 1.5rem; }
    @media (max-width: 768px) { .dw-model-grid { grid-template-columns: 1fr; } }
    .dw-model-card {
        background: var(--clr-surface-2); border: 1px solid var(--clr-border);
        border-radius: var(--radius-md); padding: 0.9rem 1rem;
        display: flex; align-items: center; gap: 0.75rem;
    }
    .dw-model-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .dw-model-dot--found   { background: #34d399; box-shadow: 0 0 6px rgba(52,211,153,0.5); }
    .dw-model-dot--missing { background: #475569; }
    .dw-model-info h4 { margin: 0 0 0.1rem; font-size: 0.78rem; font-weight: 600; color: var(--clr-text); }
    .dw-model-info p  { margin: 0; font-size: 0.68rem; color: var(--clr-text-muted); font-family: 'JetBrains Mono', monospace; }
</style>

<div class="dw-diag">
    <nav class="dw-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i> Tableau de bord</a>
        <i class="bi bi-chevron-right"></i>
        <a href="{{ route('admin.discordwebhookwazdl.settings') }}">Discord Webhook</a>
        <i class="bi bi-chevron-right"></i>
        <span>Diagnostic</span>
    </nav>

    <div class="dw-header">
        <div class="dw-brand">
            <div class="dw-brand-logo"><i class="bi bi-bug"></i></div>
            <div class="dw-brand-text">
                <h1>Diagnostic Événements</h1>
                <p>Détection des événements ClientXCMS en temps réel</p>
            </div>
        </div>
        <div style="display:flex;gap:0.65rem;">
            <a href="{{ route('admin.discordwebhookwazdl.settings') }}" class="dw-btn dw-btn--ghost">
                <i class="bi bi-arrow-left"></i> Retour config
            </a>
            <button id="btn-refresh" class="dw-btn dw-btn--amber" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Rafraîchir
            </button>
        </div>
    </div>

    {{-- HINTS --}}
    <div class="dw-hint dw-hint--amber">
        <i class="bi bi-lightbulb-fill"></i>
        <div>
            <strong>Comment utiliser ce diagnostic :</strong> Effectuez une action sur votre panel (créer un compte, passer une commande…) puis rafraîchissez cette page. Les événements interceptés apparaîtront dans le log ci-dessous.
        </div>
    </div>

    @php
    $observedModels = [
        ['label' => 'Customer', 'classes' => [
            \App\Models\Account\Customer::class,
            \App\Models\Core\Customer::class,
            \App\Models\Customer::class,
            \App\Models\Auth\Customer::class,
            \App\Models\User::class,
        ]],
        ['label' => 'Invoice', 'classes' => [
            \App\Models\Billing\Invoice::class,
            \App\Models\Invoice::class,
        ]],
        ['label' => 'Service', 'classes' => [
            \App\Models\Provisioning\Service::class,
            \App\Models\Service::class,
        ]],
    ];
    @endphp

    {{-- MODÈLES OBSERVÉS --}}
    <div class="dw-card">
        <div class="dw-card-head">
            <h2 class="dw-card-title">
                <span class="dw-icon dw-icon--emerald"><i class="bi bi-diagram-3"></i></span>
                Modèles observés (fallback direct)
            </h2>
        </div>
        <div class="dw-model-grid">
            @foreach($observedModels as $group)
                @php
                    $found = null;
                    foreach ($group['classes'] as $cls) {
                        if (class_exists($cls)) { $found = $cls; break; }
                    }
                @endphp
                <div class="dw-model-card">
                    <div class="dw-model-dot {{ $found ? 'dw-model-dot--found' : 'dw-model-dot--missing' }}"></div>
                    <div class="dw-model-info">
                        <h4>{{ $group['label'] }} {{ $found ? '✅ trouvé' : '❌ non trouvé' }}</h4>
                        <p>{{ $found ?? implode(' | ', array_map(fn($c) => class_basename($c), $group['classes'])) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @if(collect($observedModels)->every(fn($g) => !collect($g['classes'])->contains(fn($c) => class_exists($c))))
        <div style="padding:0 1.5rem 1.25rem;">
            <div class="dw-hint dw-hint--amber" style="margin:0;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Aucun modèle trouvé. Le module n'est probablement pas encore installé dans ClientXCMS. Copiez-le dans <code style="background:rgba(255,255,255,0.08);padding:0.1rem 0.3rem;border-radius:4px;">modules/discordwebhookwazdl/</code> et lancez <code style="background:rgba(255,255,255,0.08);padding:0.1rem 0.3rem;border-radius:4px;">php artisan migrate</code>.</span>
            </div>
        </div>
        @endif
    </div>

    {{-- LOG DES ÉVÉNEMENTS --}}
    <div class="dw-card">
        <div class="dw-card-head">
            <h2 class="dw-card-title">
                <span class="dw-icon dw-icon--amber"><i class="bi bi-terminal"></i></span>
                Événements interceptés (100 derniers)
            </h2>
            <span style="font-size:0.75rem;color:var(--clr-text-muted);">
                <i class="bi bi-info-circle"></i>
                Source : <code style="background:var(--clr-surface-2);padding:0.1rem 0.35rem;border-radius:4px;font-size:0.68rem;">storage/logs/laravel.log</code> ou <code style="background:var(--clr-surface-2);padding:0.1rem 0.35rem;border-radius:4px;font-size:0.68rem;">laravel-*.log</code> (daily) — tag <code style="background:var(--clr-surface-2);padding:0.1rem 0.35rem;border-radius:4px;font-size:0.68rem;">[DWWL]</code>
            </span>
        </div>
        <div style="padding:1.25rem 1.5rem;">
            @if(empty($lines))
                <div class="dw-log-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Aucun événement intercepté pour le moment.</p>
                    <p style="font-size:0.78rem;margin-top:0.5rem;">Effectuez une action sur votre panel puis rafraîchissez.</p>
                </div>
            @else
                <div style="margin-bottom:0.75rem;font-size:0.78rem;color:var(--clr-text-muted);">
                    <i class="bi bi-check-circle" style="color:#34d399;"></i>
                    <strong style="color:#34d399;">{{ count($lines) }} événement(s)</strong> intercepté(s) — les plus récents en premier
                </div>
                <div class="dw-log-wrap">
                    @foreach($lines as $line)
                        @php
                            // Extraire le nom de l'événement si possible
                            preg_match('/Événement détecté : ([^\s"]+)/', $line, $m);
                            $evName = $m[1] ?? null;
                            preg_match('/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2})/', $line, $tm);
                            $ts = $tm[1] ?? '';
                            $isObserver = str_contains($line, 'Observer');
                        @endphp
                        <div class="dw-log-line">
                            @if($ts)<span class="ts">{{ $ts }}</span>@endif
                            @if($isObserver)
                                <span class="observer-label">[OBSERVER]</span>
                            @else
                                <span class="label">[EVENT]</span>
                            @endif
                            @if($evName)
                                <span class="event-name">{{ $evName }}</span>
                                &nbsp;—&nbsp;
                            @endif
                            {{ strip_tags($line) }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- GUIDE CORRECTION --}}
    <div class="dw-card">
        <div class="dw-card-head">
            <h2 class="dw-card-title">
                <span class="dw-icon dw-icon--discord"><i class="bi bi-wrench"></i></span>
                Que faire avec ces infos ?
            </h2>
        </div>
        <div style="padding:1.25rem 1.5rem;font-size:0.83rem;color:var(--clr-text-dim);line-height:1.9;">
            <ol style="margin:0;padding:0 0 0 1.2rem;">
                <li>Effectuez une <strong>création de compte</strong> sur le panel</li>
                <li>Rafraîchissez cette page et repérez la ligne <code style="background:var(--clr-surface-2);padding:0.1rem 0.35rem;border-radius:4px;">[EVENT]</code> ou <code style="background:var(--clr-surface-2);padding:0.1rem 0.35rem;border-radius:4px;">[OBSERVER]</code> correspondante</li>
                <li>Si <strong>[OBSERVER]</strong> apparaît → le webhook se déclenche automatiquement, vérifiez l'URL Discord</li>
                <li>Si <strong>[EVENT]</strong> apparaît avec un nom → communiquez ce nom pour qu'on l'ajoute dans le ServiceProvider</li>
                <li>Si <strong>rien n'apparaît</strong> → le module n'est pas chargé, vérifiez l'installation</li>
            </ol>
        </div>
    </div>
</div>
@endsection
