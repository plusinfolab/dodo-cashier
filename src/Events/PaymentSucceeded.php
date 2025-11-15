<?php

namespace Plusinfolab\DodoCashier\Events;

use Plusinfolab\DodoCashier\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSucceeded
{
    use Dispatchable, SerializesModels;


    public function __construct(public Model $billable, public Transaction $transaction, public array $payload) {}
}
