<?php

namespace Plusinfolab\DodoCashier\Events;

use Plusinfolab\DodoCashier\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(public Model $billable, public Subscription $subscription, public array $payload) {}
}
