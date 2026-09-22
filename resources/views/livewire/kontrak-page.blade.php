<div class="flex gap-5 min-h-0">

    @if (session('success'))
        <div class="fixed top-4 right-4 z-50 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-2 rounded-lg shadow">
            {{ session('success') }}
        </div>
    @endif

    {{-- ============ Kolom Utama ============ --}}
    <div class="flex flex-col gap-4 min-w-0 {{ $detailContract ? 'w-[520px] flex-shrink-0' : 'flex-1' }}">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-foreground">Kontrak Sewa</h1>
                <p class="text-sm text-muted-foreground mt-0.5">
                    {{ $contracts->count() }} kontrak &middot; {{ $stats['aktif_total'] }} aktif
                </p>
            </div>
            <button wire:click="openContractModal"
                class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                + Buat Kontrak Baru
            </button>
        </div>

        {{-- Stats cards --}}
        @unless($detailContract)
        <div class="grid grid-cols-4 gap-3">

            {{-- 1. Kontrak Aktif --}}
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <span class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wide">Kontrak Aktif</span>
                <div class="text-2xl font-bold text-emerald-800 mt-2">{{ $stats['aktif_total'] }}</div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="text-[10px] bg-blue-100 text-blue-700 font-semibold px-1.5 py-0.5 rounded">{{ $stats['bulanan_aktif'] }} Bulanan</span>
                    <span class="text-[10px] bg-violet-100 text-violet-700 font-semibold px-1.5 py-0.5 rounded">{{ $stats['kontrak_aktif'] }} Kontrak</span>
                </div>
            </div>

            {{-- 2. Segera Berakhir --}}
            <div class="rounded-xl border p-4 {{ $stats['expiring_soon'] > 0 ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-slate-50' }}">
                <span class="text-[10px] font-semibold uppercase tracking-wide {{ $stats['expiring_soon'] > 0 ? 'text-amber-600' : 'text-slate-500' }}">Segera Berakhir</span>
                <div class="text-2xl font-bold mt-2 {{ $stats['expiring_soon'] > 0 ? 'text-amber-800' : 'text-slate-500' }}">{{ $stats['expiring_soon'] }}</div>
                <div class="text-[10px] mt-2 {{ $stats['expiring_soon'] > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                    {{ $stats['expiring_soon'] > 0 ? 'Perlu perpanjangan segera' : 'Tidak ada yang berakhir' }}
                </div>
            </div>

            {{-- 3. Belum Pelunasan --}}
            @php $adaBelumLunas = $stats['belum_lunas_count'] > 0; @endphp
            <div class="rounded-xl border p-4 {{ $adaBelumLunas ? 'border-orange-200 bg-orange-50' : 'border-slate-200 bg-slate-50' }}">
                <span class="text-[10px] font-semibold uppercase tracking-wide {{ $adaBelumLunas ? 'text-orange-600' : 'text-slate-500' }}">Belum Pelunasan</span>
                <div class="text-2xl font-bold mt-2 {{ $adaBelumLunas ? 'text-orange-800' : 'text-slate-500' }}">{{ $stats['belum_lunas_count'] }}</div>
                <div class="text-[10px] font-semibold mt-1 {{ $adaBelumLunas ? 'text-orange-700' : 'text-slate-400' }}">
                    @if ($adaBelumLunas)
                        Sisa Rp{{ number_format($stats['belum_lunas_total'], 0, ',', '.') }}
                    @else
                        Semua sudah lunas
                    @endif
                </div>
            </div>

            {{-- 4. DP Hangus --}}
            <div class="rounded-xl border p-4 {{ $stats['dp_hangus'] > 0 ? 'border-red-200 bg-red-50' : 'border-slate-200 bg-slate-50' }}">
                <span class="text-[10px] font-semibold uppercase tracking-wide {{ $stats['dp_hangus'] > 0 ? 'text-red-500' : 'text-slate-500' }}">DP Hangus</span>
                <div class="text-2xl font-bold mt-2 {{ $stats['dp_hangus'] > 0 ? 'text-red-700' : 'text-slate-500' }}">{{ $stats['dp_hangus'] }}</div>
                <div class="text-[10px] mt-2 {{ $stats['dp_hangus'] > 0 ? 'text-red-400' : 'text-slate-400' }}">
                    {{ $stats['dp_hangus'] > 0 ? 'DP dinyatakan void' : 'Tidak ada DP hangus' }}
                </div>
            </div>
        </div>
        @endunless

        {{-- Search --}}
        <div class="relative">
            <input wire:model.live.debounce.300ms="search"
                class="w-full pl-4 pr-9 py-2.5 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30"
                placeholder="Cari nama penyewa, cabang, nomor kamar...">
        </div>

        {{-- Filter tabs --}}
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-1 bg-muted/40 rounded-lg p-1">
                @foreach (['semua' => 'Semua', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $key => $label)
                    <button wire:click="$set('statusFilter', '{{ $key }}')"
                        class="px-3 py-1 rounded-md text-xs font-medium transition-all {{ $statusFilter === $key ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="h-4 w-px bg-border"></div>

            <button wire:click="$set('branchFilter', null)"
                class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ is_null($branchFilter) ? 'bg-sidebar text-white border-sidebar' : 'bg-card border-border text-muted-foreground' }}">
                Semua Cabang
            </button>
            @foreach ($branches as $b)
                <button wire:click="$set('branchFilter', {{ $b->id }})"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $branchFilter === $b->id ? 'bg-primary text-white border-transparent' : 'bg-card border-border text-muted-foreground' }}">
                    {{ $b->name }}
                </button>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/40">
                        @if ($detailContract)
                            @foreach (['Penyewa', 'Kamar', 'Berakhir', 'Status', ''] as $h)
                                <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase text-left">{{ $h }}</th>
                            @endforeach
                        @else
                            @foreach (['Penyewa', 'Cabang / Kamar', 'Mulai', 'Durasi', 'Berakhir', 'Harga', 'Status', ''] as $h)
                                <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase text-left">{{ $h }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($contracts as $c)
                        @php $isSelected = $selectedId === $c->id; @endphp
                        <tr wire:click="selectContract({{ $c->id }})"
                            class="cursor-pointer {{ $isSelected ? 'bg-primary/5 border-l-[3px] border-l-primary' : 'hover:bg-muted/20 border-l-[3px] border-l-transparent' }}">

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold {{ $isSelected ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }}">
                                        {{ strtoupper(substr($c->tenant->name, 0, 1)) }}
                                    </div>
                                    <div class="font-medium text-xs">{{ $c->tenant->name }}</div>
                                </div>
                            </td>

                            @unless ($detailContract)
                            <td class="px-4 py-3 text-xs">
                                <div class="font-medium">Kamar {{ $c->room->room_number }}</div>
                                <div class="text-muted-foreground">{{ $c->branch->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-muted-foreground whitespace-nowrap">
                                {{ $c->tanggal_mulai->translatedFormat('d M y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $c->tipe_kontrak === 'bulanan' ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700' }}">
                                        {{ $c->tipe_kontrak === 'bulanan' ? 'Bulanan' : 'Kontrak' }}
                                    </span>
                                    @if ($c->tipe_kontrak === 'kontrak')
                                        <span class="text-[10px] text-muted-foreground">{{ $c->durasi }} bln</span>
                                    @endif
                                    @if ($c->renewals->count() > 0)
                                        <span class="text-[9px] font-bold bg-blue-100 text-blue-600 px-1 py-0.5 rounded">+{{ $c->renewals->count() }}&times;</span>
                                    @endif
                                    @if ($c->dp_hangus)
                                        <span class="text-[9px] font-bold bg-amber-100 text-amber-700 px-1 py-0.5 rounded">DP Hangus</span>
                                    @endif
                                </div>
                            </td>
                            @else
                            <td class="px-4 py-3 text-xs font-medium">Kamar {{ $c->room->room_number }}</td>
                            @endunless

                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                <span class="{{ $c->is_expiring_soon ? 'font-semibold text-amber-600' : 'text-muted-foreground' }}">
                                    {{ $c->end_date ? $c->end_date->translatedFormat('d M Y') : 'Tidak terbatas' }}
                                </span>
                                @if ($c->is_expiring_soon)
                                    <span class="ml-1 text-[9px] font-bold bg-amber-100 text-amber-600 px-1 py-0.5 rounded">{{ $c->days_left }}h</span>
                                @endif
                            </td>

                            @unless ($detailContract)
                            <td class="px-4 py-3 font-medium text-xs whitespace-nowrap">
                                @if ($c->tipe_kontrak === 'bulanan')
                                    Rp{{ number_format($c->harga_bulanan, 0, ',', '.') }}
                                @else
                                    Rp{{ number_format($c->total_harga, 0, ',', '.') }}
                                @endif
                            </td>
                            @endunless

                            <td class="px-4 py-3"><x-status-badge :status="$c->status" /></td>

                            <td class="px-4 py-3 text-right" wire:click.stop>
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    @if ($c->can_perpanjang)
                                        <button wire:click="openPerpanjang({{ $c->id }})"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg">Perpanjang</button>
                                    @endif
                                    @if ($c->can_akhiri)
                                        <button wire:click="openAkhiri({{ $c->id }})"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-300 rounded-lg">Akhiri</button>
                                    @endif
                                    @if ($c->can_dp_hangus)
                                        <button wire:click="openDpHangus({{ $c->id }})"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-300 rounded-lg">DP Hangus</button>
                                    @endif
                                    @if ($c->can_batalkan)
                                        <button wire:click="openBatalkan({{ $c->id }})"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-red-500 bg-red-50 border border-red-200 rounded-lg">Batalkan</button>
                                    @endif
                                    @if ($c->can_sewa_lagi)
                                        <button wire:click="sewaLagi({{ $c->id }})"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg">Sewa Lagi</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-12 text-center text-sm text-muted-foreground">Belum ada kontrak yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============ Panel Detail ============ --}}
    @if ($detailContract)
        @php $c = $detailContract; @endphp
        <div class="flex-1 min-w-0 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold">Detail Kontrak</h2>
                <button wire:click="selectContract({{ $c->id }})" class="p-1.5 hover:bg-muted rounded-lg">&times;</button>
            </div>

            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-muted/30 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold">{{ $c->tenant->name }}</div>
                        <div class="font-mono text-[10px] text-muted-foreground uppercase">RENTAL-{{ $c->id }}</div>
                    </div>
                    <x-status-badge :status="$c->status" />
                </div>

                <div class="divide-y divide-border text-xs">
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <span class="text-muted-foreground">Tipe</span>
                        <span>{{ $c->tipe_kontrak === 'bulanan' ? 'Bulanan (tanpa batas)' : 'Kontrak (durasi tetap)' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <span class="text-muted-foreground">Cabang</span><span>{{ $c->branch->name }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <span class="text-muted-foreground">Kamar</span><span>{{ $c->room->room_number }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <span class="text-muted-foreground">Mulai Sewa</span><span>{{ $c->tanggal_mulai->translatedFormat('d F Y') }}</span>
                    </div>
                    @if ($c->tipe_kontrak === 'kontrak')
                        <div class="flex items-center justify-between px-5 py-2.5">
                            <span class="text-muted-foreground">Durasi</span><span>{{ $c->durasi }} bulan</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-2.5">
                            <span class="text-muted-foreground">Berakhir</span>
                            <span>{{ $c->end_date->translatedFormat('d F Y') }}@if($c->is_expiring_soon) &mdash; {{ $c->days_left }}h lagi @endif</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between px-5 py-2.5">
                            <span class="text-muted-foreground">Berakhir</span><span>Tidak terbatas</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <span class="text-muted-foreground">Harga</span>
                        @if ($c->tipe_kontrak === 'bulanan')
                            <span class="font-semibold">Rp{{ number_format($c->harga_bulanan, 0, ',', '.') }}</span>
                        @else
                            <span class="font-semibold">Rp{{ number_format($c->total_harga, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    @if ($c->tipe_kontrak === 'kontrak')
                        @php $persen = $c->total_harga > 0 ? min(100, ($c->total_dibayar / $c->total_harga) * 100) : 0; @endphp
                        <div class="px-5 py-3 space-y-2">
                            <div class="flex items-center justify-between text-[10px] text-muted-foreground">
                                <span>Progress Pembayaran</span><span>{{ round($persen) }}%</span>
                            </div>
                            <div class="h-2 bg-muted rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $c->dp_hangus ? 'bg-amber-400' : ($c->status === 'selesai' ? 'bg-teal-500' : 'bg-emerald-500') }}"
                                        style="width: {{ $persen }}%"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-[10px] text-center">
                                <div><div class="font-bold">Rp{{ number_format($c->total_harga, 0, ',', '.') }}</div><div class="text-muted-foreground">Total Kontrak</div></div>
                                <div><div class="font-bold text-emerald-600">Rp{{ number_format($c->total_dibayar, 0, ',', '.') }}</div><div class="text-muted-foreground">Dibayar</div></div>
                                <div><div class="font-bold {{ $c->sisa_tagihan > 0 ? 'text-red-600' : 'text-emerald-600' }}">Rp{{ number_format($c->sisa_tagihan, 0, ',', '.') }}</div><div class="text-muted-foreground">Sisa</div></div>
                            </div>
                        </div>
                    @endif

                    @if ($c->alasan_batal)
                        <div class="px-5 py-3 bg-red-50">
                            <p class="text-[10px] font-semibold text-red-500 uppercase mb-1">Alasan Pembatalan</p>
                            <p class="text-xs text-red-700">{{ $c->alasan_batal }}</p>
                        </div>
                    @endif

                    @if ($c->dp_hangus)
                        <div class="px-5 py-3 bg-amber-50 border-t border-amber-100">
                            <p class="text-[10px] font-semibold text-amber-700 uppercase mb-1">DP Dihanguskan</p>
                            <p class="text-xs text-amber-700">Semua pembayaran sebelumnya tidak dihitung sebagai pengurang tagihan.</p>
                            @if ($c->catatan_dp_hangus)
                                <p class="text-xs text-amber-600 mt-1 italic">{{ $c->catatan_dp_hangus }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Tombol aksi di panel detail --}}
                <div class="px-5 py-3 border-t border-border flex flex-wrap gap-2">
                    @if ($c->can_perpanjang)
                        <button wire:click="openPerpanjang({{ $c->id }})" class="text-xs font-semibold text-blue-600 border border-blue-200 rounded-lg px-3 py-1.5">Perpanjang</button>
                    @endif
                    @if ($c->can_akhiri)
                        <button wire:click="openAkhiri({{ $c->id }})" class="text-xs font-semibold text-slate-600 border border-slate-300 rounded-lg px-3 py-1.5">Akhiri Kontrak</button>
                    @endif
                    @if ($c->can_dp_hangus)
                        <button wire:click="openDpHangus({{ $c->id }})" class="text-xs font-semibold text-amber-700 border border-amber-300 rounded-lg px-3 py-1.5">Tandai DP Hangus</button>
                    @endif
                    @if ($c->can_batalkan)
                        <button wire:click="openBatalkan({{ $c->id }})" class="text-xs font-semibold text-red-500 border border-red-200 rounded-lg px-3 py-1.5">Batalkan</button>
                    @endif
                    @if ($c->can_sewa_lagi)
                        <button wire:click="sewaLagi({{ $c->id }})" class="text-xs font-semibold text-emerald-600 border border-emerald-200 rounded-lg px-3 py-1.5">Sewa Lagi (Kontrak Baru)</button>
                    @endif
                </div>
            </div>

            {{-- Riwayat Perpanjangan --}}
            @if ($c->renewals->count() > 0)
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-border"><p class="text-xs font-semibold">Riwayat Perpanjangan</p></div>
                <div class="divide-y divide-border">
                    @foreach ($c->renewals as $rp)
                        <div class="px-5 py-3 text-xs flex items-start justify-between gap-4">
                            <div>
                                <div class="font-medium">+{{ $rp->durasi_after - $rp->durasi_before }} bulan
                                    <span class="text-muted-foreground font-normal">({{ $rp->durasi_before }} &rarr; {{ $rp->durasi_after }} bln)</span>
                                </div>
                                @if ($rp->catatan)<div class="text-muted-foreground mt-0.5">{{ $rp->catatan }}</div>@endif
                            </div>
                            <span class="text-muted-foreground whitespace-nowrap">{{ $rp->tanggal->translatedFormat('d M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Riwayat Pembayaran --}}
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-border flex items-center justify-between">
                    <p class="text-xs font-semibold">Riwayat Pembayaran</p>
                    <span class="text-xs text-muted-foreground">{{ $c->payments->count() }} transaksi &middot; Rp{{ number_format($c->total_dibayar, 0, ',', '.') }}</span>
                </div>
                @forelse ($c->payments->sortByDesc('periode') as $p)
                    <div class="flex items-center gap-3 px-5 py-3 border-t border-border first:border-t-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium">{{ $p->periode }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $p->tanggal_bayar->translatedFormat('d M Y') }} &middot; {{ ucfirst($p->metode) }}</div>
                        </div>
                        <div class="text-xs font-semibold text-emerald-600">Rp{{ number_format($p->jumlah_dibayar, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-xs text-muted-foreground">Belum ada pembayaran tercatat untuk kontrak ini.</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ============ Modal: Buat Kontrak Baru ============ --}}
    @if ($showContractModal)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" wire:click.self="$set('showContractModal', false)">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                <h2 class="font-semibold text-sm">Buat Kontrak Baru</h2>
                <button type="button" wire:click="$set('showContractModal', false)" class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Cabang <span class="text-red-500">*</span></label>
                    <select wire:model.live="formBranchId" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('formBranchId') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Kamar Tersedia <span class="text-red-500">*</span></label>
                    <select wire:model.live="formRoomId" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">-- Pilih Kamar --</option>
                        @foreach ($availableRooms as $r)
                            <option value="{{ $r->id }}">{{ $r->room_number }}</option>
                        @endforeach
                    </select>
                    @if ($formBranchId && $availableRooms->isEmpty())
                        <p class="text-[11px] text-amber-600 mt-1">Tidak ada kamar kosong di cabang ini.</p>
                    @endif
                    @error('formRoomId') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Penyewa <span class="text-red-500">*</span></label>
                @if (!$showAddTenant)
                    <div class="flex gap-2 mt-1">
                        <select wire:model="formTenantId" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="">-- Pilih Penyewa --</option>
                            @foreach ($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} · {{ $t->phone }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="$set('showAddTenant', true)"
                            class="flex-shrink-0 flex items-center gap-1.5 px-3 h-10 text-xs font-medium border border-dashed border-primary text-primary rounded-lg hover:bg-primary/5 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">user-plus</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6m3-3h-6"/></g></svg>
                            Baru
                        </button>
                    </div>
                    @error('formTenantId') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                @else
                    <div class="border border-dashed border-primary/40 rounded-xl p-4 space-y-3 bg-primary/5 mt-1">
                        <p class="text-xs font-semibold text-primary">Tambah Penyewa Baru</p>

                        <input wire:model="newTenantNama" placeholder="Nama Lengkap *"
                            class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        @error('newTenantNama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                        <div class="grid grid-cols-2 gap-3">
                            <input wire:model="newTenantTelepon" placeholder="Nomor Telepon *"
                                class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <input wire:model="newTenantEmail" placeholder="Email"
                                class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        </div>
                        @error('newTenantEmail') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                        <input wire:model="newTenantKtp" placeholder="No. KTP"
                            class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <div class="flex gap-2">
                            <button type="button" wire:click="$set('showAddTenant', false)"
                                class="flex-1 text-xs py-2 border border-border rounded-lg hover:bg-muted transition-colors">
                                Batal
                            </button>
                            <button type="button" wire:click="saveNewTenant"
                                class="flex-1 text-xs py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                Simpan Penyewa
                            </button>
                        </div>
                    </div>
                @endif
            </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-2 uppercase tracking-wide">Tipe Kontrak</label>
                    <div class="flex gap-2 mt-1">
                        <button type="button" wire:click="$set('formTipeKontrak', 'bulanan')"
                            class="flex-1 text-xs font-semibold py-2 rounded-lg border {{ $formTipeKontrak === 'bulanan' ? 'bg-primary text-white border-primary' : 'border-border text-muted-foreground' }}">
                            Bulanan
                        </button>
                        <button type="button" wire:click="$set('formTipeKontrak', 'kontrak')"
                            class="flex-1 text-xs font-semibold py-2 rounded-lg border {{ $formTipeKontrak === 'kontrak' ? 'bg-primary text-white border-primary' : 'border-border text-muted-foreground' }}">
                            Kontrak (Durasi Tetap)
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-2 uppercase tracking-wide">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="formTanggalMulai" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @error('formTanggalMulai') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-2 uppercase tracking-wide">Harga Sewa/Bulan (RP) <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="formHargaBulanan" readonly placeholder="Pilih kamar dulu" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <p class="text-xs text-muted-foreground mt-1">Otomatis mengikuti harga kamar yang dipilih.</p>
                    @error('formHargaBulanan') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                </div>

                @if ($formTipeKontrak === 'kontrak')
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground mb-2 uppercase tracking-wide">Durasi (Bulan)</label>
                        <input type="number" wire:model="formDurasi" placeholder="12" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        @error('formDurasi') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="relative" x-data="{
                        open: false,
                        display: '0',
                        expr: '',
                        num(n){ this.display = (this.display === '0') ? String(n) : this.display + n; },
                        dot(){ if(!this.display.includes('.')) this.display += '.'; },
                        op(o){
                            if(this.expr && /[+\-*\/]$/.test(this.expr)){ this.expr = this.expr.slice(0,-1)+o; }
                            else { this.expr = this.display + o; }
                            this.display = '0';
                        },
                        clear(){ this.display='0'; this.expr=''; },
                        back(){ this.display = this.display.length>1 ? this.display.slice(0,-1) : '0'; },
                        calc(){
                            let full = this.expr + this.display;
                            if(/^[0-9+\-*\/.]+$/.test(full) && full !== ''){
                                try { this.display = String(Function('return ('+full+')')()); } catch(e){}
                            }
                            this.expr='';
                        },
                        rupiah(v){ let n = parseFloat(v)||0; return new Intl.NumberFormat('id-ID').format(n); },
                        apply(){
                            this.calc();
                            $wire.set('formTotalHarga', parseFloat(this.display) || 0);
                            this.open = false;
                            this.display = '0'; this.expr = '';
                        }
                    }">
                        <label class="text-[11px] font-semibold text-muted-foreground uppercase">Total Harga Kontrak (Rp)</label>
                        <div class="flex gap-2 mt-1">
                            <input type="number" wire:model="formTotalHarga" placeholder="10000000"
                                class="flex-1 h-10 border rounded-lg px-3 text-sm">
                            <button type="button" @click="open = !open"
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center border border-border rounded-lg hover:bg-muted transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">calculator</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M8 6h8m0 8v4m0-8h.01M12 10h.01M8 10h.01M12 14h.01M8 14h.01M12 18h.01M8 18h.01"/></g></svg>
                            </button>
                        </div>
                        @error('formTotalHarga') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
    
                        {{-- Popup Kalkulator (di tengah layar) --}}
                        <div x-show="open" x-cloak
                            class="fixed inset-0 bg-black/40 z-[60] flex items-center justify-center p-6"
                            @click.self="open = false">
    
                            <div class="w-72 bg-white rounded-xl shadow-xl border border-border p-4" @click.stop>
    
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-semibold text-sm">Kalkulator</span>
                                    <button type="button" @click="open = false" class="text-muted-foreground hover:text-foreground">&times;</button>
                                </div>
    
                                <div class="bg-muted/40 rounded-lg p-3 mb-3 text-right">
                                    <div class="text-2xl font-bold truncate" x-text="display"></div>
                                    <div class="text-xs text-muted-foreground" x-text="'&asymp; Rp ' + rupiah(display)"></div>
                                </div>
    
                                <div class="grid grid-cols-4 gap-2 text-sm font-semibold">
                                    <button type="button" @click="clear()" class="py-2 rounded-lg bg-muted/60 hover:bg-muted">C</button>
                                    <button type="button" @click="back()" class="py-2 rounded-lg bg-muted/60 hover:bg-muted">&larr;</button>
                                    <button type="button" @click="op('/')" class="py-2 rounded-lg bg-muted/60 hover:bg-muted">&divide;</button>
                                    <button type="button" @click="op('*')" class="py-2 rounded-lg bg-muted/60 hover:bg-muted">&times;</button>
    
                                    <button type="button" @click="num(7)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">7</button>
                                    <button type="button" @click="num(8)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">8</button>
                                    <button type="button" @click="num(9)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">9</button>
                                    <button type="button" @click="op('-')" class="py-2 rounded-lg bg-muted/60 hover:bg-muted">&minus;</button>
    
                                    <button type="button" @click="num(4)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">4</button>
                                    <button type="button" @click="num(5)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">5</button>
                                    <button type="button" @click="num(6)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">6</button>
                                    <button type="button" @click="op('+')" class="py-2 rounded-lg bg-muted/60 hover:bg-muted">+</button>
    
                                    <button type="button" @click="num(1)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">1</button>
                                    <button type="button" @click="num(2)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">2</button>
                                    <button type="button" @click="num(3)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">3</button>
                                    <button type="button" @click="calc()" class="py-2 rounded-lg bg-muted/30 hover:bg-primary/90 row-span-2">=</button>
    
                                    <button type="button" @click="num(0)" class="py-2 rounded-lg bg-muted/30 hover:bg-muted col-span-2">0</button>
                                    <button type="button" @click="dot()" class="py-2 rounded-lg bg-muted/30 hover:bg-muted">.</button>
                                </div>
    
                                <button type="button" @click="apply()"
                                    class="w-full mt-3 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary transition-colors">
                                    Pakai ke Total
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex gap-3 pt-2 py-4">
                    <button wire:click="$set('showContractModal', false)" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                    <button wire:click="submitContract" class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">Simpan Kontrak</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============ Modal: Perpanjang ============ --}}
    @if ($perpanjangRentalId)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" wire:click.self="$set('perpanjangRentalId', null)">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h2 class="text-base font-semibold text-foreground">Perpanjang Kontrak</h2>
                <button wire:click="$set('perpanjangRentalId', null)"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Tambah Berapa Bulan</label>
                    <input type="number" min="1" wire:model="perpanjangBulan" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Biaya Tambahan (opsional, khusus tipe Kontrak)</label>
                    <input type="number" wire:model="perpanjangBiaya" placeholder="0" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Catatan</label>
                    <textarea wire:model="perpanjangCatatan" rows="2" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"></textarea>
                </div>
                <div class="flex gap-3 pt-2 py-4">
                    <button wire:click="$set('perpanjangRentalId', null)" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                    <button wire:click="submitPerpanjang" class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============ Modal: Akhiri ============ --}}
    @if ($akhiriRentalId)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" wire:click.self="$set('akhiriRentalId', null)">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h2 class="font-semibold text-sm">Akhiri Kontrak Bulanan</h2>
                <button wire:click="$set('akhiriRentalId', null)"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Tanggal Keluar</label>
                    <input type="date" wire:model="akhiriTanggal" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                </div>
                <div class="flex gap-3 pt-2 py-4">
                    <button wire:click="$set('akhiriRentalId', null)" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                    <button wire:click="submitAkhiri" class="flex-1 px-4 py-2 text-sm font-medium bg-slate-700 text-white rounded-lg hover:bg-slate-800 transition-colors flex items-center justify-center gap-2">Akhiri Kontrak</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============ Modal: DP Hangus ============ --}}
    @if ($dpHangusRentalId)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" wire:click.self="$set('dpHangusRentalId', null)">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h2 class="font-semibold text-sm text-amber-700">Tandai DP Hangus?</h2>
                <button wire:click="$set('dpHangusRentalId', null)"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 space-y-4">
                <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl">
                    <p class="text-xs text-orange-700">Tindakan ini akan membuat semua pembayaran sebelumnya tidak dihitung sebagai pengurang tagihan. Tidak bisa dibatalkan.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Catatan (opsional)</label>
                    <textarea wire:model="dpHangusCatatan" rows="2" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Jelaskan alasan DP dihanguskan..."></textarea>
                </div>
                <div class="flex gap-3 pt-2 py-4">
                    <button wire:click="$set('dpHangusRentalId', null)" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                    <button wire:click="submitDpHangus" class="flex-1 px-4 py-2 text-sm font-medium bg-amber-700 text-white rounded-lg hover:bg-amber-800 transition-colors flex items-center justify-center gap-2">Ya, Hanguskan</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============ Modal: Batalkan ============ --}}
    @if ($batalkanRentalId)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-6" wire:click.self="$set('batalkanRentalId', null)">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h2 class="font-semibold text-sm text-red-600">Batalkan Kontrak</h2>
                <button wire:click="$set('batalkanRentalId', null)"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Alasan Pembatalan <span class="text-red-500">*</span></label>
                    <textarea wire:model="batalkanAlasan" rows="3" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"></textarea>
                    @error('batalkanAlasan') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3 pt-2 py-4">
                    <button wire:click="$set('batalkanRentalId', null)" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                    <button wire:click="submitBatalkan" class="flex-1 px-4 py-2 text-sm font-medium bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">Batalkan Kontrak</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>