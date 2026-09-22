<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'room_id', 'branch_id',
        'tipe_kontrak', 'tanggal_mulai', 'durasi',
        'harga_bulanan', 'total_harga',
        'status', 'alasan_batal',
        'dp_hangus', 'catatan_dp_hangus',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'dp_hangus'       => 'boolean',
        'harga_bulanan'   => 'decimal:2',
        'total_harga'     => 'decimal:2',
    ];

    protected $appends = [
        'end_date', 'days_left', 'is_expiring_soon',
        'total_dibayar', 'sisa_tagihan',
        'can_perpanjang', 'can_akhiri', 'can_dp_hangus', 'can_batalkan', 'can_sewa_lagi',
    ];

    // ================= Relasi =================

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function room() { return $this->belongsTo(Room::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function renewals() { return $this->hasMany(RentalRenewal::class); }

    // ================= Accessors =================

    /** Tanggal berakhir hasil hitungan. NULL untuk tipe bulanan (tanpa batas). */
    public function getEndDateAttribute(): ?Carbon
    {
        if ($this->tipe_kontrak !== 'kontrak' || !$this->durasi) {
            return null;
        }
        return $this->tanggal_mulai->copy()->addMonths($this->durasi);
    }

    /** Sisa hari sampai end_date. NULL kalau tidak berlaku (tipe bulanan). */
    public function getDaysLeftAttribute(): ?int
    {
        $end = $this->end_date;
        if (!$end) return null;
        return (int) now()->startOfDay()->diffInDays($end->copy()->startOfDay(), false);
    }

    /** Flag buat highlight amber "Segera Berakhir" di UI. */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->status === 'aktif'
            && $this->tipe_kontrak === 'kontrak'
            && !is_null($this->days_left)
            && $this->days_left >= 0
            && $this->days_left <= 30;
    }

    public function getTotalDibayarAttribute(): float
    {
        return (float) $this->payments()->sum('jumlah_dibayar');
    }

    /**
     * Sisa tagihan cuma relevan untuk tipe "kontrak".
     * Kalau dp_hangus true, pembayaran sebelumnya diabaikan
     * (dianggap tidak mengurangi tagihan), sisa = total_harga penuh.
     */
    public function getSisaTagihanAttribute(): float
    {
        if ($this->tipe_kontrak !== 'kontrak') return 0;

        if ($this->dp_hangus) {
            return (float) $this->total_harga;
        }

        return max(0, (float) $this->total_harga - $this->total_dibayar);
    }

    // ---- flag boleh/tidaknya tiap tombol aksi, dipakai langsung di Blade ----

    public function getCanPerpanjangAttribute(): bool
    {
        return $this->status === 'aktif'
            && $this->tipe_kontrak === 'kontrak'
            && $this->is_expiring_soon;
    }

    public function getCanAkhiriAttribute(): bool
    {
        return $this->status === 'aktif' && $this->tipe_kontrak === 'bulanan';
    }

    public function getCanDpHangusAttribute(): bool
    {
        return $this->status === 'aktif'
            && $this->tipe_kontrak === 'kontrak'
            && !$this->dp_hangus
            && $this->sisa_tagihan > 0;
    }

    public function getCanBatalkanAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    public function getCanSewaLagiAttribute(): bool
    {
        return $this->status === 'selesai'
            && $this->room
            && $this->room->status === 'kosong';
    }

    // ================= Scopes =================

    public function scopeAktif(Builder $q): Builder { return $q->where('status', 'aktif'); }
    public function scopeSelesai(Builder $q): Builder { return $q->where('status', 'selesai'); }
    public function scopeDibatalkan(Builder $q): Builder { return $q->where('status', 'dibatalkan'); }

    /** Kontrak tetap yang aktif dan end_date-nya <= $days hari lagi. */
    public function scopeExpiringSoon(Builder $q, int $days = 30): Builder
    {
        return $q->aktif()
            ->where('tipe_kontrak', 'kontrak')
            ->whereNotNull('durasi')
            ->get() // dihitung di PHP karena end_date bukan kolom asli (derived dari tanggal_mulai + durasi)
            ->filter(fn (self $r) => $r->is_expiring_soon)
            ->pluck('id')
            ->pipe(fn ($ids) => Rental::whereIn('id', $ids));
    }

    // ================= Aksi Bisnis =================

    /**
     * Perpanjang kontrak Aktif (tipe "kontrak" saja).
     * Hanya update end_date (via durasi) dan opsional menambah total_harga.
     * TIDAK mencatat pembayaran apa pun, itu tetap lewat halaman Pembayaran.
     */
    public function perpanjang(int $tambahBulan, ?float $biayaTambahan = null, ?string $catatan = null): void
    {
        if ($this->tipe_kontrak !== 'kontrak') {
            throw new RuntimeException('Hanya kontrak durasi tetap yang bisa diperpanjang.');
        }
        if ($this->status !== 'aktif') {
            throw new RuntimeException('Hanya kontrak berstatus Aktif yang bisa diperpanjang.');
        }

        $durasiSebelum = (int) $this->durasi;
        $this->durasi = $durasiSebelum + $tambahBulan;

        if ($biayaTambahan) {
            $this->total_harga = (float) $this->total_harga + $biayaTambahan;
        }

        $this->save();

        $this->renewals()->create([
            'durasi_before' => $durasiSebelum,
            'durasi_after'  => $this->durasi,
            'catatan'       => $catatan,
            'tanggal'       => now(),
        ]);
    }

    /**
     * Akhiri kontrak bulanan (penyewa keluar baik-baik, sesuai prosedur).
     * Kamar otomatis kembali kosong lewat RentalObserver.
     */
    public function akhiri(?string $tanggalSelesai = null): void
    {
        if ($this->tipe_kontrak !== 'bulanan') {
            throw new RuntimeException('Aksi "Akhiri Kontrak" hanya berlaku untuk tipe Bulanan.');
        }
        if ($this->status !== 'aktif') {
            throw new RuntimeException('Kontrak ini sudah tidak aktif.');
        }

        $this->status = 'selesai';
        $this->tanggal_selesai = $tanggalSelesai ?? now()->toDateString();
        $this->save();
    }

    /**
     * Tandai DP hangus. Murni aksi manual admin, tidak berbasis waktu otomatis.
     * Pembayaran sebelumnya tetap tersimpan di riwayat, tapi tidak lagi
     * mengurangi sisa_tagihan (lihat getSisaTagihanAttribute).
     */
    public function tandaiDpHangus(?string $catatan = null): void
    {
        if (!$this->can_dp_hangus) {
            throw new RuntimeException('Kontrak ini tidak memenuhi syarat untuk ditandai DP Hangus.');
        }

        $this->dp_hangus = true;
        $this->catatan_dp_hangus = $catatan;
        $this->save();
    }

    /**
     * Batalkan kontrak (penyewa keluar mendadak / DP hangus tanpa pelunasan).
     * Wajib isi alasan. Kamar otomatis kembali kosong lewat RentalObserver.
     */
    public function batalkan(string $alasan): void
    {
        if (trim($alasan) === '') {
            throw new RuntimeException('Alasan pembatalan wajib diisi.');
        }
        if ($this->status !== 'aktif') {
            throw new RuntimeException('Hanya kontrak Aktif yang bisa dibatalkan.');
        }

        $this->status = 'dibatalkan';
        $this->alasan_batal = $alasan;
        $this->tanggal_selesai = now()->toDateString();
        $this->save();
    }

    /**
     * "Sewa Lagi" dari kontrak yang sudah Selesai.
     * Membuat rental_id BARU (bukan menyambung kontrak lama),
     * supaya riwayat tiap masa sewa tetap utuh terpisah.
     */
    public function sewaLagi(): self
    {
        if (!$this->can_sewa_lagi) {
            throw new RuntimeException('Kontrak ini tidak bisa disewa lagi (belum Selesai atau kamar sudah terisi).');
        }

        return self::create([
            'tenant_id'     => $this->tenant_id,
            'room_id'       => $this->room_id,
            'branch_id'     => $this->branch_id,
            'tipe_kontrak'  => $this->tipe_kontrak,
            'tanggal_mulai' => now()->toDateString(),
            'durasi'        => $this->tipe_kontrak === 'kontrak' ? $this->durasi : null,
            'harga_bulanan' => $this->harga_bulanan,
            'total_harga'   => $this->tipe_kontrak === 'kontrak' ? $this->total_harga : null,
            'status'        => 'aktif',
        ]);
    }
}
