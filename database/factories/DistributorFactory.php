<?php

namespace Database\Factories;

use App\Models\Distributor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DistributorFactory extends Factory
{
    protected $model = Distributor::class;

    public function definition(): array
    {
        return [
            'distributor_code' => 'NPP' . $this->faker->unique()->numerify('#####'),
            'distributor_name' => $this->faker->name(),
            'distributor_email' => $this->faker->unique()->safeEmail(),
            'distributor_phone' => $this->faker->phoneNumber(),
            'distributor_address' => $this->faker->address(),
            'parent_id' => null,
            'level' => 1,
            'join_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'status' => 'active',
        ];
    }
} 