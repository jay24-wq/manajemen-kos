<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Rental;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class KontrakPage extends Component
{
    // ==== Search & Filter ====
    public string $search = '';
    public string $statusFilter = 'semua'; // semua | aktif | selesai | dibatalkan
    public ?int $branchFilter = null;

    // ==== Detail panel ====
    public ?int $selectedId = null;

    // ==== Modal: Buat Kontrak Baru ====
    public bool $showContractModal = false;
    public ?int $formTenantId = null;
    public ?int $formBranchId = null;
    public ?int $formRoomId = null;
    public string $formTipeKontrak = 'bulanan'; // bulanan | kontrak
    public string $formTanggalMulai = '';
    public ?int $formDurasi = null;
    public ?float $formHargaBulanan = null;
    public ?float $formTotalHarga = null;

    // ==== Tambah Penyewa Baru (inline, dari dalam modal Buat Kontrak) ====
    public bool $showAddTenant = false;
    public string $newTenantNama = '';
    public string $newTenantTelepon = '';
    public string $newTenantEmail = '';
    public string $newTenantKtp = '';

    // ==== Modal: Perpanjang ====
    public ?int $perpanjangRentalId = null;
    public int $perpanjangBulan = 1;
    public ?float $perpanjangBiaya = null;
    public string $perpanjangCatatan = '';

    // ==== Modal: Akhiri (bulanan) ====
    public ?int $akhiriRentalId = null;
    public string $akhiriTanggal = '';

    // ==== Modal: DP Hangus ====
    public ?int $dpHangusRentalId = null;
    public string $dpHangusCatatan = '';

    // ==== Modal: Batalkan ====
    public ?int $batalkanRentalId = null;
    public string $batalkanAlasan = '';

    public function updatedFormRoomId($value): void
    {
        if ($value) {
            $room = \App\Models\Room::find($value);
            $this->formHargaBulanan = $room?->price_monthly;
        } else {
            $this->formHargaBulanan = null;
        }
    }

    public function render()
    {
        $query = Rental::with(['tenant', 'room', 'branch'])
            ->when($this->statusFilter !== 'semua', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->branchFilter, fn ($q) => $q->where('branch_id', $this->branchFilter))
            ->when($this->search !== '', function ($q) {
                $s = '%' . $this->search . '%';
                $q->where(function ($qq) use ($s) {
                    $qq->whereHas('tenant', fn ($t) => $t->where('name', 'like', $s))
                        ->orWhereHas('branch', fn ($b) => $b->where('name', 'like', $s))
                        ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', $s));
                });
            })
            ->latest();

        $contracts = $query->get();

        $aktif = Rental::aktif()->get();
        $bulananAktif = $aktif->where('tipe_kontrak', 'bulanan')->count();
        $kontrakAktif = $aktif->where('tipe_kontrak', 'kontrak')->count();
        $expiringSoon = $aktif->filter(fn ($r) => $r->is_expiring_soon)->count();
        $dpHangusCount = Rental::where('dp_hangus', true)->count();

        $belumLunas = $aktif->filter(fn ($r) => $r->tipe_kontrak === 'kontrak' && $r->sisa_tagihan > 0);
        $totalSisa = $belumLunas->sum('sisa_tagihan');

        return view('livewire.kontrak-page', [
            'contracts'      => $contracts,
            'branches'       => Branch::all(),
            'tenants'        => \App\Models\Tenant::orderBy('name')->get(),
            'availableRooms' => $this->formBranchId
                ? \App\Models\Room::where('branch_id', $this->formBranchId)->where('status', 'kosong')->get()
                : collect(),
            'detailContract' => $this->selectedId ? Rental::with(['tenant', 'room', 'branch', 'payments', 'renewals'])->find($this->selectedId) : null,
            'stats' => [
                'aktif_total'   => $aktif->count(),
                'bulanan_aktif' => $bulananAktif,
                'kontrak_aktif' => $kontrakAktif,
                'expiring_soon' => $expiringSoon,
                'belum_lunas_count' => $belumLunas->count(),
                'belum_lunas_total' => $totalSisa,
                'dp_hangus'     => $dpHangusCount,
            ],
        ]);
    }

    public function selectContract(int $id): void
    {
        $this->selectedId = $this->selectedId === $id ? null : $id;
    }

    // ================= Buat Kontrak Baru =================

    public function openContractModal(): void
    {
        $this->reset(['formTenantId', 'formBranchId', 'formRoomId', 'formDurasi', 'formTotalHarga']);
        $this->reset(['showAddTenant', 'newTenantNama', 'newTenantTelepon', 'newTenantEmail', 'newTenantKtp']);
        $this->formTipeKontrak = 'bulanan';
        $this->formTanggalMulai = now()->toDateString();
        $this->formHargaBulanan = null;
        $this->showContractModal = true;
    }

    /**
     * Simpan penyewa baru langsung dari dalam modal Buat Kontrak,
     * tanpa perlu pindah ke halaman Penyewa dulu. Begitu tersimpan,
     * langsung terpilih otomatis di dropdown "Penyewa".
     */
    public function saveNewTenant(): void
    {
        $this->validate([
            'newTenantNama'    => 'required|string|max:255',
            'newTenantTelepon' => 'nullable|string|max:30',
            'newTenantEmail'   => 'nullable|email|max:255',
            'newTenantKtp'     => 'nullable|string|max:30',
        ]);

        $tenant = \App\Models\Tenant::create([
            'name'     => $this->newTenantNama,
            'phone'  => $this->newTenantTelepon ?: null,
            'email'    => $this->newTenantEmail ?: null,
            'ktp_number'   => $this->newTenantKtp ?: null,
        ]);

        $this->formTenantId = $tenant->id;
        $this->showAddTenant = false;
        $this->reset(['newTenantNama', 'newTenantTelepon', 'newTenantEmail', 'newTenantKtp']);
    }

    public function submitContract(): void
    {
        $rules = [
            'formTenantId'     => 'required|exists:tenants,id',
            'formBranchId'     => 'required|exists:branches,id',
            'formRoomId'       => 'required|exists:rooms,id',
            'formTipeKontrak'  => 'required|in:bulanan,kontrak',
            'formTanggalMulai' => 'required|date',
            'formHargaBulanan' => 'required|numeric|min:0',
        ];

        if ($this->formTipeKontrak === 'kontrak') {
            $rules['formDurasi'] = 'required|integer|min:1';
            $rules['formTotalHarga'] = 'required|numeric|min:0';
        }

        $this->validate($rules);

        Rental::create([
            'tenant_id'     => $this->formTenantId,
            'branch_id'     => $this->formBranchId,
            'room_id'       => $this->formRoomId,
            'tipe_kontrak'  => $this->formTipeKontrak,
            'tanggal_mulai' => $this->formTanggalMulai,
            'durasi'        => $this->formTipeKontrak === 'kontrak' ? $this->formDurasi : null,
            'harga_bulanan' => $this->formHargaBulanan,
            'total_harga'   => $this->formTipeKontrak === 'kontrak' ? $this->formTotalHarga : null,
            'status'        => 'aktif',
        ]);

        $this->showContractModal = false;
        session()->flash('success', 'Kontrak baru berhasil dibuat.');
    }

    // ================= Perpanjang =================

    public function openPerpanjang(int $id): void
    {
        $this->perpanjangRentalId = $id;
        $this->perpanjangBulan = 1;
        $this->perpanjangBiaya = null;
        $this->perpanjangCatatan = '';
    }

    public function submitPerpanjang(): void
    {
        $this->validate([
            'perpanjangBulan' => 'required|integer|min:1',
            'perpanjangBiaya' => 'nullable|numeric|min:0',
        ]);

        $rental = Rental::findOrFail($this->perpanjangRentalId);
        $rental->perpanjang($this->perpanjangBulan, $this->perpanjangBiaya, $this->perpanjangCatatan ?: null);

        $this->perpanjangRentalId = null;
        session()->flash('success', 'Kontrak berhasil diperpanjang.');
    }

    // ================= Akhiri (bulanan) =================

    public function openAkhiri(int $id): void
    {
        $this->akhiriRentalId = $id;
        $this->akhiriTanggal = now()->toDateString();
    }

    public function submitAkhiri(): void
    {
        $rental = Rental::findOrFail($this->akhiriRentalId);
        $rental->akhiri($this->akhiriTanggal);

        $this->akhiriRentalId = null;
        session()->flash('success', 'Kontrak bulanan diakhiri, kamar kembali kosong.');
    }

    // ================= DP Hangus =================

    public function openDpHangus(int $id): void
    {
        $this->dpHangusRentalId = $id;
        $this->dpHangusCatatan = '';
    }

    public function submitDpHangus(): void
    {
        $rental = Rental::findOrFail($this->dpHangusRentalId);
        $rental->tandaiDpHangus($this->dpHangusCatatan ?: null);

        $this->dpHangusRentalId = null;
        session()->flash('success', 'DP kontrak ini ditandai hangus.');
    }

    // ================= Batalkan =================

    public function openBatalkan(int $id): void
    {
        $this->batalkanRentalId = $id;
        $this->batalkanAlasan = '';
    }

    public function submitBatalkan(): void
    {
        $this->validate(['batalkanAlasan' => 'required|string|min:5']);

        $rental = Rental::findOrFail($this->batalkanRentalId);
        $rental->batalkan($this->batalkanAlasan);

        $this->batalkanRentalId = null;
        session()->flash('success', 'Kontrak dibatalkan.');
    }

    // ================= Sewa Lagi =================

    public function sewaLagi(int $id): void
    {
        $rental = Rental::findOrFail($id);
        $baru = $rental->sewaLagi();

        // Sesuai kesepakatan: setelah sewa lagi, langsung diarahkan
        // ke halaman Pembayaran dengan kontrak baru ini terpilih.
        $this->redirect(route('pembayaran.index', ['rental' => $baru->id]), navigate: true);
    }
}