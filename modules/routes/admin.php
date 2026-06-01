<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DiscordWebhookWazdL\Controllers\Admin\DiscordWebhookAdminController;

Route::group([
    'prefix'     => 'admin/discordwebhookwazdl',
    'as'         => 'admin.discordwebhookwazdl.',
    'middleware' => ['web', 'admin'],
], function () {
    Route::get('/', [DiscordWebhookAdminController::class, 'index'])->name('settings');
    Route::post('/update', [DiscordWebhookAdminController::class, 'update'])->name('update');
    Route::post('/test', [DiscordWebhookAdminController::class, 'testWebhook'])->name('test');
    Route::get('/diagnostic', [DiscordWebhookAdminController::class, 'diagnostic'])->name('diagnostic');
    Route::post('/clear-log', [DiscordWebhookAdminController::class, 'clearLog'])->name('clear-log');
});

