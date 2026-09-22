<?php

namespace Database\Factories;

use App\Models\Rental;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = (clone $startDate)->modify('+6 months');
        return [
            'room_id' => Room::factory(),
            'tenant_id' => Tenant::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'monthly_price' => $this->faker->randomElement([2500000, 3000000]),
            'status' => $this->faker->randomElement(['aktif', 'selesai', 'dibatalkan']),
        ];
    }
}
