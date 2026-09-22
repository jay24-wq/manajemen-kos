<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Pembayaran</h1>
            <p class="text-sm text-muted-foreground mt-0.5">{{ $activeContracts->sum(fn($c) => $c->payments->count() ?? 0) }} kontrak aktif dipantau</p>
        </div>
        <button wire:click="openPaymentModal" class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
            + Catat Pembayaran
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-card rounded-xl border border-border p-4 shadow-sm">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide mb-2">Total Diterima</p>
            <p class="text-xl font-bold text-foreground">Rp{{ number_format($totalDiterima, 0, ',', '.') }}</p>
            <p class="text-xs text-muted-foreground mt-1">Seluruh periode</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs font-medium text-emerald-700 uppercase tracking-wide mb-2">Bulan Ini</p>
            <p class="text-xl font-bold text-emerald-700">Rp{{ number_format($totalBulanIni, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-600 mt-1">{{ count($sudahBayarBulanIni) }} dari {{ $activeContracts->count() }} kontrak</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs font-medium text-red-600 uppercase tracking-wide mb-2">Belum Bayar Bulan Ini</p>
            <p class="text-xl font-bold text-red-600">{{ $belumBayarCount }}</p>
            <p class="text-xs text-red-500 mt-1">
                {{ $belumBayarCount > 0 ? 'Rp'.number_format($belumBayarTotal, 0, ',', '.') : 'Semua lunas' }}
            </p>
        </div>
        <div class="bg-card border border-border rounded-xl p-4 shadow-sm">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide mb-2">Transaksi Bulan Ini</p>
            <p class="text-xl font-bold text-foreground">{{ $transaksiBulanIni }}</p>
            <p class="text-xs text-muted-foreground mt-1">Pembayaran tercatat</p>
        </div>
    </div>

    {{-- Filter cabang --}}
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs font-medium text-muted-foreground">Cabang:</span>
        <button wire:click="$set('branchFilter', null)"
            class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ is_null($branchFilter) ? 'bg-sidebar text-white border-sidebar' : 'bg-card border-border text-muted-foreground' }}">
            Semua Cabang
        </button>
        @foreach ($branches as $b)
            <button wire:click="$set('branchFilter', {{ $b->id }})"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border {{ $branchFilter === $b->id ? 'bg-primary text-white border-transparent' : 'bg-card border-border text-muted-foreground' }}">
                {{ $b->name }}
                <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $branchFilter === $b->id ? 'bg-white/20 text-white' : 'bg-muted text-muted-foreground' }}">
                    {{ $activeContracts->where('branch_id', $b->id)->count() }}
                </span>
            </button>
        @endforeach
    </div>

    {{-- Riwayat grouped cards --}}
    <div class="space-y-4">
        @if ($rows->isEmpty())
            <div class="bg-card border border-dashed border-border rounded-xl flex flex-col items-center justify-center py-12 gap-2">
                <p class="text-sm text-muted-foreground">Tidak ada data pembayaran.</p>
            </div>
        @endif

        @foreach ($rows as $row)
            @php
                $c = $row['rental'];
                $paidThisMonth = $row['payments']->contains(fn($p) => $p->periode === $bulanIni);
            @endphp
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">

                {{-- Group header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-border bg-muted/30">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary flex-shrink-0">
                            {{ strtoupper(substr($row['label'], 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-foreground">{{ $row['label'] }}</div>
                            <div class="text-xs text-muted-foreground">{{ $row['sub'] }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            @if ($c->tipe_kontrak === 'bulanan')
                                <div class="text-xs text-muted-foreground">Harga/bln</div>
                                <div class="text-sm font-semibold text-foreground">Rp{{ number_format($c->harga_bulanan, 0, ',', '.') }}</div>
                            @else
                                <div class="text-xs text-muted-foreground">Total Kontrak</div>
                                <div class="text-sm font-semibold text-foreground">Rp{{ number_format($c->total_harga, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        @if ($c->tipe_kontrak === 'bulanan')
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $paidThisMonth ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                {{ $paidThisMonth ? 'Lunas bulan ini' : 'Belum bulan ini' }}
                            </div>
                        @else
                            @if ($c->sisa_tagihan <= 0)
                                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    Lunas
                                </div>
                            @elseif ($c->total_dibayar <= 0)
                                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-600">
                                    Belum DP
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                                    Belum Lunas
                                </div>
                            @endif
                        @endif
                        <button wire:click="openPaymentModal({{ $c->id }})"
                            class="flex items-center gap-1.5 text-xs font-medium text-primary border border-primary/20 rounded-lg px-2.5 py-1.5 hover:bg-primary/5 transition-colors">
                            + Catat
                        </button>
                    </div>
                </div>

                {{-- Payment rows --}}
                @if ($row['payments']->isEmpty())
                    <div class="px-5 py-6 text-center text-xs text-muted-foreground">Belum ada pembayaran tercatat.</div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/20">
                                <th class="px-5 py-2.5 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Periode</th>
                                <th class="px-5 py-2.5 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Tgl Bayar</th>
                                <th class="px-5 py-2.5 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Jumlah</th>
                                <th class="px-5 py-2.5 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Metode</th>
                                <th class="px-5 py-2.5 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($row['payments'] as $p)
                                @php
                                    $isThisMonth = $p->periode === $bulanIni;
                                    $kurang = $c->tipe_kontrak === 'bulanan' && $p->jumlah_dibayar < $c->harga_bulanan;
                                @endphp
                                <tr class="hover:bg-muted/20 transition-colors {{ $isThisMonth ? 'bg-emerald-50/40' : '' }}">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-foreground">{{ $p->periode }}</span>
                                            @if ($isThisMonth)
                                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">Bulan ini</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-muted-foreground">{{ $p->tanggal_bayar->translatedFormat('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-semibold {{ $kurang ? 'text-amber-600' : 'text-foreground' }}">Rp{{ number_format($p->jumlah_dibayar, 0, ',', '.') }}</span>
                                            @if ($kurang)
                                                <span class="text-[10px] bg-amber-100 text-amber-700 font-medium px-1.5 py-0.5 rounded">
                                                    Kurang Rp{{ number_format($c->harga_bulanan - $p->jumlah_dibayar, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $p->metode === 'transfer' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($p->metode) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-muted-foreground">{{ $p->catatan ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Footer summary --}}
                <div class="px-5 py-2.5 border-t border-border bg-muted/10 flex items-center justify-between">
                    <span class="text-xs text-muted-foreground">{{ $row['payments']->count() }} pembayaran tercatat</span>
                    <span class="text-xs font-semibold text-foreground">
                        Total: Rp{{ number_format($row['payments']->sum('jumlah_dibayar'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============ Modal: Catat Pembayaran ============ --}}
    @if ($showPaymentModal)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" wire:click.self="$set('showPaymentModal', false)">
        <div class="bg-card w-full max-w-md rounded-2xl shadow-2xl border border-border flex flex-col">
            <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-foreground">Catat Pembayaran</h2>
                <button wire:click="$set('showPaymentModal', false)"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Kontrak</label>
                    <select wire:model.live="formRentalId" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">-- Pilih Kontrak --</option>
                        @foreach ($activeContracts as $c)
                            <option value="{{ $c->id }}">{{ $c->tenant->name ?? '—' }} · {{ $c->room->room_number ?? '—' }}</option>
                        @endforeach
                    </select>
                    @error('formRentalId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                @php $selectedRental = $activeContracts->firstWhere('id', $formRentalId); @endphp

                @if ($selectedRental && $selectedRental->tipe_kontrak === 'kontrak')
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Jenis Pembayaran</label>
                        <div class="grid grid-cols-1 gap-2 mt-1">
                            <button type="button" wire:click="$set('formJenisPembayaran', 'dp_awal')"
                                class="px-3 py-2 rounded-lg text-xs font-medium border transition-all text-left {{ $formJenisPembayaran === 'dp_awal' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-muted-foreground hover:border-primary/40' }}">
                                DP Awal <span class="block text-xs font-normal opacity-70">Uang muka di awal kontrak</span>
                            </button>
                            <button type="button" wire:click="$set('formJenisPembayaran', 'bayar_lunas')"
                                class="px-3 py-2 rounded-lg text-xs font-medium border transition-all text-left {{ $formJenisPembayaran === 'bayar_lunas' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-muted-foreground hover:border-primary/40' }}">
                                Bayar Lunas <span class="block text-xs font-normal opacity-70">Pelunasan sisa setelah DP</span>
                            </button>
                            <button type="button" wire:click="$set('formJenisPembayaran', 'bayar_penuh')"
                                class="px-3 py-2 rounded-lg text-xs font-medium border transition-all text-left {{ $formJenisPembayaran === 'bayar_penuh' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-muted-foreground hover:border-primary/40' }}">
                                Bayar Penuh <span class="block text-xs font-normal opacity-70">Tanpa DP, lewat masa tenggat pelunasan</span>
                            </button>
                        </div>
                        @error('formJenisPembayaran') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div>
                        <label class="text-[11px] font-semibold text-muted-foreground uppercase">Periode Bulan</label>
                        <input type="month" wire:model="formPeriode" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        @error('formPeriode') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div x-data="{
                        raw: @entangle('formJumlah'),
                        display: '',
                        format(v) {
                            if (v === null || v === '' || v === undefined) return '';
                            return new Intl.NumberFormat('id-ID').format(v);
                        },
                        onInput(e) {
                            let digits = e.target.value.replace(/\D/g, '');
                            this.raw = digits ? parseInt(digits) : null;
                            this.display = this.format(this.raw);
                        }
                    }" x-init="display = format(raw); $watch('raw', v => display = format(v))">
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Jumlah Dibayar</label>
                    <div class="relative mt-1">
                        <input type="text" inputmode="numeric" x-model="display" @input="onInput($event)"
                            placeholder="0"
                            class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                    @error('formJumlah') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Tanggal Bayar</label>
                    <input type="date" wire:model="formTanggalBayar" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @error('formTanggalBayar') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Metode</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="$set('formMetode', 'transfer')"
                            class="px-3 py-2 rounded-lg text-xs font-medium border transition-all text-left {{ $formMetode === 'transfer' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-muted-foreground hover:border-primary/40' }}">
                            &#127974; Transfer Bank
                        </button>
                        <button type="button" wire:click="$set('formMetode', 'cash')"
                            class="px-3 py-2 rounded-lg text-xs font-medium border transition-all text-left {{ $formMetode === 'cash' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-muted-foreground hover:border-primary/40' }}">
                            &#128181; Cash
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Catatan</label>
                    <textarea wire:model="formCatatan" rows="2" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"></textarea>
                </div>

                <div class="flex gap-3 pt-2 py-4">
                    <button wire:click="$set('showPaymentModal', false)" class="flex-1 text-sm border rounded-lg py-2">Batal</button>
                    <button wire:click="submitPayment" class="flex-1 text-sm bg-primary text-white rounded-lg py-2">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
