<?php

use Illuminate\Support\Facades\Route;
use Plusinfolab\DodoPayments\Http\Controllers\WebhookController;

Route::post('webhook', [WebhookController::class, '__invoke'])->name('webhook');
