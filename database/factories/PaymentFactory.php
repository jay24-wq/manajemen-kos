<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Payment::factory(),
            'period_month' => $this->faker->dateTimeThisYear()->format('F Y'),
            'amount' => $this->faker->randomElement([2500000, 3000000]),
            'payment_date' => $this->faker->dateTimeThisMonth()->format('Y-m-d'),
            'status' => $this->faker->randomElement(['lunas', 'kurang', 'dibatalkan']),
            'method' => $this->faker->randomElement(['cash', 'bank']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
