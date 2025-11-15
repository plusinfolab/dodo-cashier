<?php

namespace Database\Factories;

use Plusinfolab\DodoCashier\DodoPayments;
use Plusinfolab\DodoCashier\Tests\Models\User;
use Plusinfolab\DodoCashier\Transaction;
use Plusinfolab\DodoCashier\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'payment_id' => $this->faker->unique()->uuid,
            'subscription_id' => null, // Default to null; set dynamically in state
            'status' => $this->faker->randomElement(['paid', 'pending', 'failed']),
            'total' => $this->faker->randomFloat(2, 10, 500),
            'tax' => $this->faker->randomFloat(2, 0, 50),
            'currency' => $this->faker->currencyCode,
            'billed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function forSubscription(Subscription $subscription)
    {
        return $this->state(function () use ($subscription) {
            return [
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
            ];
        });
    }
}
