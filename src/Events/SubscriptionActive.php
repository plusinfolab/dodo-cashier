<?php

namespace Plusinfolab\DodoPayments\Events;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Plusinfolab\DodoPayments\Subscription as VendorSubscription;

class SubscriptionActive
{
    use Dispatchable, SerializesModels;

public function __construct(
    public Model $billable,
    public Subscription|VendorSubscription $subscription,
    public array $payload
) {}}
