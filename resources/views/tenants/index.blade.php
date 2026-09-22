@extends('layouts.app')

@section('content')
<div class="flex gap-5 min-h-0">
    <div class="flex flex-col gap-4 min-w-0 {{ $detailTenant ? 'w-[380px] flex-shrink-0' : 'flex-1' }}">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-foreground">Manajemen Penyewa</h1>
                <p class="text-sm text-muted-foreground mt-0.5">{{ $totalTenants }} penyewa terdaftar</p>
            </div>
            <button onclick="openTenantModal()" class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">plus</title><path fill="#ffffff" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
                Tambah Penyewa
            </button>
        </div>

        <form method="GET" action="{{ route('tenants.index') }}" class="relative">
            @if($selectedId)
                <input type="hidden" name="selected_id" value="{{ $selectedId }}">
            @endif
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"><title xmlns="">search</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m17 17l4 4M3 11a8 8 0 1 0 16 0a8 8 0 0 0-16 0"/></svg>
            <input type="text" name="search" class="w-full pl-9 pr-8 py-2.5 text-sm bg-card border border-border rounded-xl text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Cari nama, telepon, email, atau No KTP..." value="{{ $search }}" onchange="this.form.submit()"/>
            @if($search)
                <a href="{{ route('tenants.index', array_filter(['selected_id' => $selectedId])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </a>
            @endif
        </form>

        @if(!$detailTenant)
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-muted/40">
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Nama</th>
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">No HP</th>
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">No KTP</th>
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Kontak Darurat</th>
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-left">Kamar Aktif</th>
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($tenants as $t)
                            @php
                                $activeRental = $t->rentals->firstWhere('status', 'aktif');
                                $room = $activeRental ? $activeRental->room : null;
                            @endphp
                            <tr onclick="window.location.href='{{ route('tenants.index', array_merge(request()->query(), ['selected_id' => $t->id])) }}'" class="cursor-pointer hover:bg-muted/20 transition-colors group">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary flex-shrink-0">
                                            {{ strtoupper(substr($t->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-foreground">{{ $t->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground whitespace-nowrap">{{ $t->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-muted-foreground max-w-[160px] truncate">{{ $t->email ?? '—' }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-muted-foreground whitespace-nowrap">{{ $t->ktp_number ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if(!empty($t->emergency_contact['name']))
                                        <div>
                                            <div class="text-xs font-medium text-foreground">{{ $t->emergency_contact['name'] }}</div>
                                            <div class="text-[10px] text-muted-foreground">{{ $t->emergency_contact['relation'] ?? '' }} · {{ $t->emergency_contact['phone'] ?? '' }}</div>
                                        </div>
                                    @else
                                        <span class="text-xs text-muted-foreground">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($room)
                                        <div>
                                            <div class="text-xs font-medium text-foreground">Kamar {{ $room->room_number }}</div>
                                            <div class="text-[10px] text-muted-foreground">{{ explode(' - ', $room->branch->name)[0] }}</div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick="editTenant({{ $t->id }})" class="p-1.5 hover:bg-muted rounded-lg transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4"/></svg>
                                        </button>
                                        <button type="button" onclick="openDeleteTenantModal('{{ $t->id }}')" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-red-400"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="flex flex-col items-center justify-center py-14 gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground/40"><title xmlns="">search</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m17 17l4 4M3 11a8 8 0 1 0 16 0a8 8 0 0 0-16 0"/></svg>
                                        <p class="text-sm text-muted-foreground">
                                            {{ $search ? 'Tidak ditemukan hasil untuk "' . $search . '"' : 'Belum ada penyewa. Tambahkan penyewa pertama.' }}
                                        </p>
                                        @if($search)
                                            <a href="{{ route('tenants.index') }}" class="text-xs text-primary hover:underline">Hapus pencarian</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($tenants->hasPages())
                    <div class="flex items-center justify-between px-4 py-3 border-t border-border bg-muted/20">
                        <p class="text-xs text-muted-foreground">
                            Menampilkan {{ $tenants->firstItem() }}-{{ $tenants->lastItem() }} dari {{ $tenants->total() }} tenants
                        </p>
                        <div class="flex items-center gap-1">
                            <a href="{{ $tenants->url(1) }}" class="p-1.5 rounded-lg hover:bg-muted transition-colors {{ $tenants->onFirstPage() ? 'opacity-30 pointer-events-none' : '' }}" title="Halaman pertama">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><title xmlns="">arrow-ios-back-outline</title><path fill="currentColor" d="M13.83 19a1 1 0 0 1-.78-.37l-4.83-6a1 1 0 0 1 0-1.27l5-6a1 1 0 0 1 1.54 1.28L10.29 12l4.32 5.36a1 1 0 0 1-.78 1.64"/></svg>
                            </a>
                            <a href="{{ $tenants->previousPageUrl() }}" class="p-1.5 rounded-lg hover:bg-muted transition-colors {{ $tenants->onFirstPage() ? 'opacity-30 pointer-events-none' : '' }}" title="Sebelumnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><title xmlns="">arrow-ios-back-outline</title><path fill="currentColor" d="M13.83 19a1 1 0 0 1-.78-.37l-4.83-6a1 1 0 0 1 0-1.27l5-6a1 1 0 0 1 1.54 1.28L10.29 12l4.32 5.36a1 1 0 0 1-.78 1.64"/></svg>
                            </a>

                            @foreach ($tenants->getUrlRange(max(1, $tenants->currentPage() - 1), min($tenants->lastPage(), $tenants->currentPage() + 1)) as $page => $url)
                                <a href="{{ $url }}" class="min-w-[28px] h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-colors {{ $page == $tenants->currentPage() ? 'bg-primary text-white' : 'hover:bg-muted text-muted-foreground' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            <a href="{{ $tenants->nextPageUrl() }}" class="p-1.5 rounded-lg hover:bg-muted transition-colors {{ !$tenants->hasMorePages() ? 'opacity-30 pointer-events-none' : '' }}" title="Selanjutnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><title xmlns="">arrow-ios-forward-outline</title><path fill="currentColor" d="M10 19a1 1 0 0 1-.64-.23a1 1 0 0 1-.13-1.41L13.71 12L9.39 6.63a1 1 0 0 1 .15-1.41a1 1 0 0 1 1.46.15l4.83 6a1 1 0 0 1 0 1.27l-5 6A1 1 0 0 1 10 19"/></svg>
                            </a>
                            <a href="{{ $tenants->url($tenants->lastPage()) }}" class="p-1.5 rounded-lg hover:bg-muted transition-colors {{ !$tenants->hasMorePages() ? 'opacity-30 pointer-events-none' : '' }}" title="Halaman terakhir">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><title xmlns="">arrow-ios-forward-outline</title><path fill="currentColor" d="M10 19a1 1 0 0 1-.64-.23a1 1 0 0 1-.13-1.41L13.71 12L9.39 6.63a1 1 0 0 1 .15-1.41a1 1 0 0 1 1.46.15l4.83 6a1 1 0 0 1 0 1.27l-5 6A1 1 0 0 1 10 19"/></svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if($detailTenant)
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <div class="divide-y divide-border">
                    @foreach($tenants as $t)
                        @php
                            $activeRental = $t->rentals->firstWhere('status', 'aktif');
                            $room = $activeRental ? $activeRental->room : null;
                            $isSelected = $selectedId == $t->id;
                        @endphp
                        <div onclick="window.location.href='{{ route('tenants.index', array_merge(request()->query(), ['selected_id' => $isSelected ? null : $t->id])) }}'" class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors group {{ $isSelected ? 'bg-primary/5 border-l-[3px] border-l-primary' : 'hover:bg-muted/20 border-l-[3px] border-l-transparent' }}">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 {{ $isSelected ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }}">
                                {{ strtoupper(substr($t->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-foreground truncate">{{ $t->name }}</div>
                                <div class="text-xs text-muted-foreground truncate">
                                    {{ $room ? "Kamar {$room->room_number} · " . explode(' - ', $room->branch->name)[0] : "Tidak ada kamar aktif" }}
                                </div>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                                <button type="button" onclick="editTenant({{ $t->id }})" class="p-1.5 hover:bg-muted rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4"/></svg>
                                </button>
                                <button type="button" onclick="openDeleteTenantModal('{{ $t->id }}')" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-red-400"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if($detailTenant)
        @php
            $activeRental = $tenantRentals->firstWhere('status', 'aktif');
            $activeRoom = $activeRental ? $activeRental->room : null;
        @endphp
        <div class="flex-1 space-y-4 min-w-0">
            <div class="bg-card rounded-xl border border-border shadow-sm p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-lg font-bold text-white flex-shrink-0">
                            {{ strtoupper(substr($detailTenant->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-foreground">{{ $detailTenant->name }}</h2>
                            @if($activeRoom)
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                                    <span class="text-xs text-muted-foreground">Kamar {{ $activeRoom->room_number }} · {{ explode(' - ', $activeRoom->branch->name)[0] }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-500 mt-0.5">Tidak aktif</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button type="button" onclick="editTenant({{ $detailTenant->id }})" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-border rounded-lg hover:bg-muted transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4"/></svg>Edit
                        </button>
                        <a href="{{ route('tenants.index', request()->except('selected_id')) }}" class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-muted-foreground mb-0.5">No HP</p>
                        <p class="text-sm text-foreground">{{ $detailTenant->phone }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-muted-foreground mb-0.5">Email</p>
                        <p class="text-sm text-foreground">{{ $detailTenant->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-muted-foreground mb-0.5">Nomor KTP</p>
                        <p class="text-sm text-foreground font-mono">{{ $detailTenant->ktp_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-muted-foreground mb-0.5">Kontrak Ke</p>
                        <p class="text-sm text-foreground">{{ $tenantRentals->count() }}x kontrak</p>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-700 mb-2">Kontak Darurat</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ $detailTenant->emergency_contact['name'] ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $detailTenant->emergency_contact['relation'] ?? '' }}
                                {{ !empty($detailTenant->emergency_contact['relation']) && !empty($detailTenant->emergency_contact['phone']) ? ' · ' : '' }}
                                {{ $detailTenant->emergency_contact['phone'] ?? '' }}
                            </p>
                        </div>
                        @if(!empty($detailTenant->emergency_contact['phone']))
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-amber-700"><title xmlns="">phone-outline</title><path fill="currentColor" fill-rule="evenodd" d="M5.733 2.043c1.217-1.21 3.221-.995 4.24.367l1.262 1.684c.83 1.108.756 2.656-.229 3.635l-.238.238a.65.65 0 0 0-.008.306c.063.408.404 1.272 1.832 2.692s2.298 1.76 2.712 1.824a.7.7 0 0 0 .315-.009l.408-.406c.876-.87 2.22-1.033 3.304-.444l1.91 1.04c1.637.888 2.05 3.112.71 4.445l-1.421 1.412c-.448.445-1.05.816-1.784.885c-1.81.169-6.027-.047-10.46-4.454c-4.137-4.114-4.931-7.702-5.032-9.47l.749-.042l-.749.042c-.05-.894.372-1.65.91-2.184zm3.04 1.266c-.507-.677-1.451-.731-1.983-.202l-1.57 1.56c-.33.328-.488.69-.468 1.036c.08 1.405.72 4.642 4.592 8.492c4.062 4.038 7.813 4.159 9.263 4.023c.296-.027.59-.181.865-.454l1.42-1.413c.578-.574.451-1.62-.367-2.064l-1.91-1.039c-.528-.286-1.146-.192-1.53.19l-.455.453l-.53-.532c.53.532.529.533.528.533l-.001.002l-.003.003l-.007.006l-.015.014a1 1 0 0 1-.136.106c-.08.053-.186.112-.319.161c-.27.101-.628.155-1.07.087c-.867-.133-2.016-.724-3.543-2.242c-1.526-1.518-2.122-2.66-2.256-3.526c-.069-.442-.014-.8.088-1.07a1.5 1.5 0 0 1 .238-.42l.032-.035l.014-.015l.006-.006l.003-.003l.002-.002l.53.53l-.53-.531l.288-.285c.428-.427.488-1.134.085-1.673z" clip-rule="evenodd"/></svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-foreground">Riwayat Kamar</h3>
                        <p class="text-xs text-muted-foreground mt-0.5">{{ $tenantRentals->count() }} kontrak tercatat</p>
                    </div>
                </div>
                @if($tenantRentals->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground/40"><title xmlns="">file-text</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.478 3H7.25A2.25 2.25 0 0 0 5 5.25v13.5A2.25 2.25 0 0 0 7.25 21h9a2.25 2.25 0 0 0 2.25-2.25V12M9.478 3c1.243 0 2.272 1.007 2.272 2.25V7.5A2.25 2.25 0 0 0 14 9.75h2.25A2.25 2.25 0 0 1 18.5 12M9.478 3c3.69 0 9.022 5.36 9.022 9M9 16.5h6m-6-3h4"/></svg>
                        <p class="text-sm text-muted-foreground">Belum ada riwayat kontrak.</p>
                    </div>
                @else
                    <div class="relative">
                        <div class="absolute left-[28px] top-0 bottom-0 w-px bg-border"></div>
                        <div class="divide-y divide-border">
                            @foreach($tenantRentals as $idx => $c)
                                @php
                                    $room = $c->room;
                                    $branch = $c->branch;
                                    $startDate = \Carbon\Carbon::parse($c->tanggal_mulai);
                                    $endDate = (clone $startDate)->addMonths($c->durasi);
                                    $isFirst = $idx === 0;
                                @endphp
                                <div class="flex gap-4 px-5 py-4 hover:bg-muted/20 transition-colors relative">
                                    <div class="w-6 h-6 rounded-full border-2 flex-shrink-0 flex items-center justify-center z-10 mt-0.5 {{ $c->status === 'aktif' ? 'bg-emerald-500 border-emerald-500' : ($c->status === 'dibatalkan' ? 'bg-red-400 border-red-400' : 'bg-card border-border') }}">
                                        @if ($c->status === 'aktif')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-white"><title xmlns="">check-circle</title><path fill="currentColor" d="M10.5 15.25A.74.74 0 0 1 10 15l-3-3a.75.75 0 0 1 1-1l2.47 2.47L19 5a.75.75 0 0 1 1 1l-9 9a.74.74 0 0 1-.5.25"/><path fill="currentColor" d="M12 21a9 9 0 0 1-7.87-4.66a8.7 8.7 0 0 1-1.07-3.41a9 9 0 0 1 4.6-8.81a8.7 8.7 0 0 1 3.41-1.07a8.9 8.9 0 0 1 3.55.34a.75.75 0 1 1-.43 1.43a7.6 7.6 0 0 0-3-.28a7.4 7.4 0 0 0-2.84.89a7.5 7.5 0 0 0-2.2 1.84a7.42 7.42 0 0 0-1.64 5.51a7.4 7.4 0 0 0 .89 2.84a7.5 7.5 0 0 0 1.84 2.2a7.42 7.42 0 0 0 5.51 1.64a7.4 7.4 0 0 0 2.84-.89a7.5 7.5 0 0 0 2.2-1.84a7.42 7.42 0 0 0 1.64-5.51a.75.75 0 1 1 1.57-.15a9 9 0 0 1-4.61 8.81A8.7 8.7 0 0 1 12.93 21z"/></svg>
                                        @elseif($c->status === 'selesai')
                                            <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                        @elseif($c->status === 'dibatalkan')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-white"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-sm font-semibold text-foreground">
                                                        Kamar {{ $room->room_number ?? '—' }} · {{ $room->room_type ?? '—' }}
                                                    </span>
                                                    @if($isFirst)
                                                        @include('tenants.partials.status-badge', ['status' => $c->status])
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1 mt-0.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground flex-shrink-0"><title xmlns="">map-pin</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/><path d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0"/></g></svg>
                                                    <span class="text-xs text-muted-foreground truncate">{{ $branch->name ?? '—' }}</span>
                                                </div>
                                            </div>
                                            @if(!$isFirst)
                                                @include('tenants.partials.status-badge', ['status' => $c->status])
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-2 text-xs text-muted-foreground flex-wrap">
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">calendar-days</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M8 2v4m8-4v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></g></svg>
                                                {{ $startDate->isoFormat('D MMM Y') }} &rarr; {{ $endDate->isoFormat('D MMM Y') }}
                                            </span>
                                            <span class="font-medium text-foreground">{{ $c->durasi }} bulan</span>
                                            <span class="font-medium text-primary">Rp {{ number_format($c->monthly_price, 0, ',', '.') }}/bln</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<div id="tenantModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
        <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
            <h2 id="modalTitle" class="text-base font-semibold text-foreground">Tambah Penyewa Baru</h2>
            <button type="button" onclick="closeTenantModal()" class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="tenantForm" method="POST" action="{{ route('tenants.store') }}" class="px-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="space-y-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-primary mb-3">Data Diri</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="field_nama" required class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Nama sesuai KTP">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    No HP <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" id="field_telepon" required class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="08xxxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Email 
                                </label>
                                <input type="email" name="email" id="field_email" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="email@contoh.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                No KTP
                            </label>
                            <input type="text" name="ktp_number" id="field_ktp_no" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="16 digit NIK">
                        </div>
                    </div>
                </div>

                <div class="border-t border-border pt-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-primary mb-3">Kontak Darurat</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Nama Kontak
                            </label>
                            <input type="text" name="emergency_contact_name" id="field_kd_nama" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"  placeholder="Nama kerabat / wali">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">Hubungan</label>
                                <input type="text" name="emergency_contact_relation" id="field_kd_hubungan" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Hubungan (mis. Orang tua)">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">No HP Kontak</label>
                                <input type="text" name="emergency_contact_phone" id="field_kd_telepon" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 py-4">
                    <button type="button" onclick="closeTenantModal()" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">save</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></g></svg>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal-hapus-tenant" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
        <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
            <h2 class="text-base font-semibold text-foreground">Konfirmasi Hapus</h2>
            <button type="button" onclick="closeDeleteTenantModal('{{ $t->id }}')" 
            class="p-1.5 hover:bg-muted rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        </div>
        <form id="deleteTenantForm" action="" method="POST" class="px-6 space-y-4">
            @csrf
            @method('DELETE')
            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="-2 -2 24 24" class="text-red-500 shrink-0 mt-0.5"><path fill="currentColor" d="M10 20C4.477 20 0 15.523 0 10S4.477 0 10 0s10 4.477 10 10s-4.477 10-10 10m0-2a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0-13a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V6a1 1 0 0 1 1-1m0 10a1 1 0 1 1 0-2a1 1 0 0 1 0 2"/></svg>
                <p class="text-sm text-red-700" id="deleteTenantText">Hapus penyewa ini? Seluruh data dan riwayat kontraknya akan terhapus.</p>
            </div>
            <div class="flex gap-3 pt-2 py-4">
                <button type="button" onclick="closeDeleteTenantModal()" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-white"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
const tenantsData = @json($tenants->keyBy('id'));

function openTenantModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Penyewa Baru';
    document.getElementById('tenantForm').action = "{{ route('tenants.store') }}";
    document.getElementById('formMethod').value = 'POST';
    
    // Reset Form
    document.getElementById('tenantForm').reset();
    
    document.getElementById('tenantModal').classList.remove('hidden');
    document.getElementById('tenantModal').classList.add('flex');
}

function editTenant(id) {
    const tenant = tenantsData[id];
    if (!tenant) return;

    document.getElementById('modalTitle').innerText = 'Edit Data Penyewa';
    document.getElementById('tenantForm').action = "/tenants/" + id;
    document.getElementById('formMethod').value = 'PUT';

    // Populate Fields
    document.getElementById('field_nama').value = tenant.name || '';
    document.getElementById('field_telepon').value = tenant.phone || '';
    document.getElementById('field_email').value = tenant.email || '';
    document.getElementById('field_ktp_no').value = tenant.ktp_number || '';

    const kd = tenant.kontak_darurat || {};
    document.getElementById('field_kd_nama').value = kd.name || '';
    document.getElementById('field_kd_hubungan').value = kd.relation || '';
    document.getElementById('field_kd_telepon').value = kd.phone || '';

    document.getElementById('tenantModal').classList.remove('hidden');
    document.getElementById('tenantModal').classList.add('flex');
}

function closeTenantModal() {
    document.getElementById('tenantModal').classList.add('hidden');
    document.getElementById('tenantModal').classList.remove('flex');
}

function openDeleteTenantModal(id) {
    const tenant = tenantsData[id];
    if (!tenant) return;

    document.getElementById('deleteTenantForm').action = "/tenants/" + id;
    document.getElementById('deleteTenantText').innerText =
        "Hapus penyewa " + tenant.name + "? Seluruh data dan riwayat kontraknya akan terhapus.";

    document.getElementById('modal-hapus-tenant').classList.remove('hidden');
    document.getElementById('modal-hapus-tenant').classList.add('flex');
}
function closeDeleteTenantModal() {
    document.getElementById('modal-hapus-tenant').classList.add('hidden');
    document.getElementById('modal-hapus-tenant').classList.remove('flex');
}
</script>