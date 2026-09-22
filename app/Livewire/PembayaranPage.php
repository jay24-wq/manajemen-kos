<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Payment;
use App\Models\Rental;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PembayaranPage extends Component
{
    public ?int $branchFilter = null;

    // ==== Modal: Catat Pembayaran ====
    public bool $showPaymentModal = false;
    public ?int $formRentalId = null;
    public string $formPeriode = '';
    public string $formJenisPembayaran = 'dp_awal'; // dp_awal | bayar_lunas | bayar_penuh (khusus tipe kontrak)
    public ?float $formJumlah = null;
    public string $formTanggalBayar = '';
    public string $formMetode = 'transfer';
    public string $formCatatan = '';

    /**
     * Dukung redirect dari halaman Kontrak (aksi "Sewa Lagi"):
     * /pembayaran?rental=123 -> modal langsung kebuka dengan kontrak itu terpilih.
     */
    public function mount(): void
    {
        $rentalId = request()->integer('rental');
        if ($rentalId) {
            $this->openPaymentModal($rentalId);
        }
    }

    public function render()
    {
        $bulanIni = now()->format('Y-m');

        $activeContracts = Rental::with(['tenant', 'room', 'branch'])
            ->aktif()
            ->when($this->branchFilter, fn ($q) => $q->where('branch_id', $this->branchFilter))
            ->get();

        // Kontrak tipe "bulanan" yang belum ada pembayaran untuk periode bulan berjalan.
        // Tipe "kontrak" sengaja tidak dihitung di sini, karena progres pembayarannya
        // dipantau lewat sisa_tagihan di halaman Kontrak, bukan per-bulan seperti ini.
        $sudahBayarBulanIni = Payment::where('periode', $bulanIni)->pluck('rental_id')->all();
        $belumBayar = $activeContracts
            ->where('tipe_kontrak', 'bulanan')
            ->reject(fn (Rental $r) => in_array($r->id, $sudahBayarBulanIni));

        $totalDiterima = Payment::sum('jumlah_dibayar');
        $totalBulanIni = Payment::where('periode', $bulanIni)->sum('jumlah_dibayar');
        $transaksiBulanIni = Payment::where('periode', $bulanIni)->count();

        // Grouped rows per kontrak, buat ditampilkan sebagai card + tabel riwayat.
        $rows = $activeContracts->map(function (Rental $c) {
            return [
                'rental'    => $c,
                'label'     => $c->tenant->name ?? '—',
                'sub'       => 'Kamar '.($c->room->room_number ?? '—').' · '.($c->branch->name ?? '—'),
                'payments'  => $c->payments()->orderByDesc('periode')->get(),
            ];
        });

        return view('livewire.pembayaran-page', [
            'branches'          => Branch::all(),
            'activeContracts'   => $activeContracts,
            'belumBayarCount'   => $belumBayar->count(),
            'belumBayarTotal'   => $belumBayar->sum('harga_bulanan'),
            'totalDiterima'     => $totalDiterima,
            'totalBulanIni'     => $totalBulanIni,
            'transaksiBulanIni' => $transaksiBulanIni,
            'sudahBayarBulanIni'=> $sudahBayarBulanIni,
            'rows'              => $rows,
            'bulanIni'          => $bulanIni,
        ]);
    }

    public function openPaymentModal(?int $rentalId = null): void
    {
        $this->reset(['formRentalId', 'formJumlah', 'formCatatan']);
        $this->formRentalId = $rentalId;
        $this->formPeriode = now()->format('Y-m');
        $this->formTanggalBayar = now()->toDateString();
        $this->formMetode = 'transfer';
        $this->formJenisPembayaran = 'dp_awal';

        if ($rentalId) {
            $rental = Rental::find($rentalId);
            $this->formJumlah = $rental?->harga_bulanan;

            // Kalau kontrak tipe "kontrak" dan sudah pernah ada pembayaran (DP sudah masuk),
            // default jenis pembayaran diarahkan ke "Bayar Lunas", bukan "DP Awal" lagi.
            if ($rental && $rental->tipe_kontrak === 'kontrak' && $rental->total_dibayar > 0) {
                $this->formJenisPembayaran = 'bayar_lunas';
            }
        }

        $this->showPaymentModal = true;
    }

    public function updatedFormRentalId($value): void
    {
        if (!$value) return;

        $rental = Rental::find($value);
        $this->formJumlah = $rental?->harga_bulanan;

        if ($rental && $rental->tipe_kontrak === 'kontrak') {
            $this->formJenisPembayaran = $rental->total_dibayar > 0 ? 'bayar_lunas' : 'dp_awal';
        }
    }

    public function submitPayment(): void
    {
        $rental = Rental::find($this->formRentalId);
        $isKontrak = $rental && $rental->tipe_kontrak === 'kontrak';

        $rules = [
            'formRentalId'     => 'required|exists:rentals,id',
            'formJumlah'       => 'required|numeric|min:0',
            'formTanggalBayar' => 'required|date',
            'formMetode'       => 'required|in:transfer,cash',
        ];

        if ($isKontrak) {
            $rules['formJenisPembayaran'] = 'required|in:dp_awal,bayar_lunas,bayar_penuh';
        } else {
            $rules['formPeriode'] = 'required|date_format:Y-m';
        }

        $this->validate($rules);

        Payment::create([
            'rental_id'         => $this->formRentalId,
            // Periode tetap diisi otomatis (bulan berjalan) walau tipe kontrak,
            // sekadar buat pengurutan riwayat, bukan penanda periode tagihan.
            'periode'           => $isKontrak ? now()->format('Y-m') : $this->formPeriode,
            'jumlah_dibayar'    => $this->formJumlah,
            'tanggal_bayar'     => $this->formTanggalBayar,
            'metode'            => $this->formMetode,
            'jenis_pembayaran'  => $isKontrak ? $this->formJenisPembayaran : null,
            'catatan'           => $this->formCatatan ?: null,
            'recorded_by'       => auth()->id(),
        ]);

        $this->showPaymentModal = false;
        session()->flash('success', 'Pembayaran berhasil dicatat.');
    }
}