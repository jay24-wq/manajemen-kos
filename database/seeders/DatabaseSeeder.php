<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $branches = Branch::factory()->count(3)->create();

        $branches -> each(function ($branch) {
            Room::factory()->count(10)->create([
            'branch_id' => $branch->id,
            ]);
        });

        $tenants = Tenant::factory()->count(15)->create();

        $rooms = Room::all();

        $rentals = Rental::factory()->count(10)->create([
        'room_id' => $rooms->random()->id,
        'tenant_id' => $tenants->random()->id,
        ]);

        $rentals -> each(function ($rental){
            Payment::factory()->count(2)->create([
            'rental_id' => $rental->id,
            'amount' => $rental->monthly_price,
            ]);
        });
    }
}
