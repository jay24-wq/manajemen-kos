<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'room_number' => strtoupper($this->faker->unique()->bothify('?##')),
            'floor' => $this->faker->randomElement(['1']),
            'room_type' => $this->faker->randomElement(['Standar']),
            'price_monthly' => $this->faker->randomElement([2500000, 3000000]),
            'status' => $this->faker->randomElement(['kosong', 'terisi', 'maintenance']),
        ];
    }
}
