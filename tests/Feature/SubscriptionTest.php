<?php

use Plusinfolab\DodoPayments\Checkout;
use Plusinfolab\DodoPayments\DodoPayments;
use Plusinfolab\DodoPayments\Subscription;
use Plusinfolab\DodoPayments\Tests\Fixtures\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Plusinfolab\DodoPayments\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('customers can start a subscription', function () {
    Http::fake([
        '*/subscriptions' => Http::response([
            'subscription_id' => 'sub_123',
            'payment_link' => 'https://example.com/checkout',
        ]),
    ]);

    $user = User::factory()->create();

    $checkout = $user->newSubscription('default', 'prod_123')->create();

    expect($checkout)->toBeInstanceOf(Checkout::class);
    expect($checkout->subscription)->toBeInstanceOf(Subscription::class);
    expect($checkout->checkoutUrl)->toBe('https://example.com/checkout');
});

test('customers can cancel a subscription', function () {
    Http::fake([
        '*/subscriptions/*' => Http::response([
            'subscription_id' => 'sub_123',
            'status' => 'cancelled',
            'ends_at' => now()->addDays(10)->toIso8601String(),
        ]),
    ]);

    $user = User::factory()->create();
    $subscription = $user->subscriptions()->create([
        'type' => 'default',
        'subscription_id' => 'sub_123',
        'product_id' => 'prod_123',
        'status' => 'active',
    ]);

    $subscription->cancel();

    expect($subscription->status)->toBe('cancelled');
    expect($subscription->ends_at)->not->toBeNull();
});

test('customers can resume a cancelled subscription', function () {
    Http::fake([
        '*/subscriptions/*' => Http::response([
            'subscription_id' => 'sub_123',
            'status' => 'active',
        ]),
    ]);

    $user = User::factory()->create();
    $subscription = $user->subscriptions()->create([
        'type' => 'default',
        'subscription_id' => 'sub_123',
        'product_id' => 'prod_123',
        'status' => 'cancelled',
        'ends_at' => now()->addDays(10),
    ]);

    $subscription->resume();

    expect($subscription->status)->toBe('active');
});

test('customers can swap a subscription', function () {
    Http::fake([
        '*/subscriptions/*/change-plan' => Http::response([
            'subscription_id' => 'sub_123',
            'status' => 'active',
            'product_id' => 'prod_456',
        ]),
    ]);

    $user = User::factory()->create();
    $subscription = $user->subscriptions()->create([
        'type' => 'default',
        'subscription_id' => 'sub_123',
        'product_id' => 'prod_123',
        'status' => 'active',
    ]);

    $subscription->swap('prod_456', 'default');

    expect($subscription->product_id)->toBe('prod_456');
});
