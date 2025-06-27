<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'distributor_id' => User::factory(),
            'distributor_level' => 1,
            'amount' => $this->faker->numberBetween(1000000, 10000000),
            'sale_time' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'bill_code' => strtoupper(Str::random(10)),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
} 