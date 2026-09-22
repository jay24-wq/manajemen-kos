<?php

namespace App\Observers;

use App\Models\Rental;

class RentalObserver
{
    /**
     * Handle the Rental "created" event.
     */
    public function created(Rental $rental): void
    {
        $rental->room()->update(['status' => 'terisi']);
    }

    /**
     * Handle the Rental "updated" event.
     */
    public function updated(Rental $rental): void
    {
        if ($rental->wasChanged('status') && in_array($rental->status, ['selesai', 'dibatalkan'], true)) {
            $rental->room()->update(['status' => 'kosong']);
        }
    }

    /**
     * Handle the Rental "deleted" event.
     */
    public function deleted(Rental $rental): void
    {
        //
    }

    /**
     * Handle the Rental "restored" event.
     */
    public function restored(Rental $rental): void
    {
        //
    }

    /**
     * Handle the Rental "force deleted" event.
     */
    public function forceDeleted(Rental $rental): void
    {
        //
    }
}
