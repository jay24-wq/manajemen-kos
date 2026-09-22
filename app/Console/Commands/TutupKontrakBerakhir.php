<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;

class TutupKontrakBerakhir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kontrak:tutup-otomatis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menutup otomatis kontrak durasi tetap yang sudah lewat end_date dan tidak diperpanjang';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $kandidat = Rental::aktif()->where('tipe_kontrak', 'kontrak')->get();

        $ditutup = 0;

        foreach ($kandidat as $rental) {
            $daysLeft = $rental->days_left;

            if (!is_null($daysLeft) && $daysLeft < 0) {
                $rental->status = 'selesai';
                $rental->tanggal_selesai = $rental->end_date->toDateString();
                $rental->save(); // RentalObserver otomatis kembalikan kamar jadi kosong

                $ditutup++;
            }
        }

        $this->info("Selesai. {$ditutup} kontrak otomatis ditutup karena sudah lewat end_date.");

        return self::SUCCESS;
    }
}
