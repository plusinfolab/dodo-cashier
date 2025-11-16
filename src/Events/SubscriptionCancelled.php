<?php

namespace Plusinfolab\DodoPayments\Events;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(public Model $billable, public Subscription $subscription, public array $payload) {}
}
