<?php

namespace Database\Factories;


use Plusinfolab\DodoCashier\DodoPayments;
use Plusinfolab\DodoCashier\Subscription;
use Plusinfolab\DodoCashier\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->word,
            'product_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['active', 'cancelled', 'expired']),
            'ends_at' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}
