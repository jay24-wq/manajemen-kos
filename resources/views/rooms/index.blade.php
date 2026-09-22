@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Manajemen Kamar</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                {{ $rooms->count() }} kamar &middot; {{ $branches->count() }} cabang
            </p>
        </div>
        <button
            type="button"
            onclick="openTambahModal()"
            {{ $branches->isEmpty() ? 'disabled' : '' }}
            class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path fill="currentColor" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
            Tambah Kamar
        </button>
    </div>

    @if($branches->isEmpty())
        <div class="bg-card border border-dashed border-border rounded-xl flex flex-col items-center justify-center py-16 gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="0 0 24 24" class="text-muted-foreground/40"><title xmlns="">door-open-outline-rounded</title><path fill="currentColor" d="M11.713 12.713Q12 12.425 12 12t-.288-.712T11 11t-.712.288T10 12t.288.713T11 13t.713-.288M7 21v-2l6-1V6.875q0-.375-.225-.675t-.575-.35L7 5V3l5.5.9q1.1.2 1.8 1.025T15 6.85v11.1q0 .725-.475 1.288t-1.2.687zm0-2h10V5H7zm-3 2q-.425 0-.712-.288T3 20t.288-.712T4 19h1V5q0-.825.588-1.412T7 3h10q.825 0 1.413.588T19 5v14h1q.425 0 .713.288T21 20t-.288.713T20 21z"/></svg>
            <p class="text-sm text-muted-foreground">Belum ada cabang. Tambahkan cabang terlebih dahulu.</p>
        </div>
    @else
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('rooms.index') }}"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all border {{ !$branch ? 'bg-sidebar text-white border-sidebar' : 'bg-card border-border text-muted-foreground hover:text-foreground' }}">
                    Semua Cabang
                    <span class="ml-1 rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ !$branch ? 'bg-white/20 text-white' : 'bg-muted text-muted-foreground' }}">{{ $rooms->count() }}</span>
                </a>
                @foreach($branches as $b)
                    <a href="{{ route('rooms.index', ['branch' => $b->id]) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all border {{ $branch && $branch->id === $b->id ? 'bg-sidebar text-white border-sidebar' : 'bg-card border-border text-muted-foreground hover:text-foreground' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">apartment</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.5 2.5h-3c-1.886 0-2.828 0-3.414.586S2.5 4.614 2.5 6.5v11c0 1.886 0 2.828.586 3.414s1.528.586 3.414.586h7v-15c0-1.886 0-2.828-.586-3.414S11.386 2.5 9.5 2.5m-4 3h1m3 0h1m-5 3h1m3 0h1m-5 3h1m3 0h1m-5 3h1m3 0h1m-2.5 7v-3m9.5-11h-4v14h4c1.886 0 2.828 0 3.414-.586s.586-1.528.586-3.414v-6c0-1.886 0-2.828-.586-3.414S19.386 7.5 17.5 7.5m-.5 3h1m-1 3h1m-1 3h1"/></svg>
                        {{ explode(' - ', $b->name)[0] }}
                        <span class="ml-1 rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $branch && $branch->id === $b->id ? 'bg-white/20 text-white' : 'bg-muted text-muted-foreground' }}">{{ $rooms->where('branch_id', $b->id)->count() }}</span>
                    </a>
                @endforeach
            </div>

            <div class="sm:ml-auto flex gap-2" id="status-tabs">
                @foreach(['semua' => 'Semua', 'terisi' => 'Terisi', 'kosong' => 'Kosong', 'maintenance' => 'Maintenance'] as $key => $label)
                    <button
                        type="button"
                        data-status="{{ $key }}"
                        onclick="filterStatus('{{ $key }}')"
                        class="status-tab px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ $key === 'semua' ? 'bg-primary text-white' : 'bg-card border border-border text-muted-foreground hover:text-foreground' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @php
            $displayedRooms = $branch ? $rooms->where('branch_id', $branch->id) : $rooms;
        @endphp

        <div class="grid grid-cols-4 gap-3">
            @php
                $stats = [
                    ['label' => 'Total Kamar', 'value' => $displayedRooms->count(), 'color' => 'bg-slate-50 border-slate-200 text-slate-700'],
                    ['label' => 'Terisi', 'value' => $displayedRooms->where('status', 'terisi')->count(), 'color' => 'bg-emerald-50 border-emerald-200 text-emerald-700'],
                    ['label' => 'Kosong', 'value' => $displayedRooms->where('status', 'kosong')->count(), 'color' => 'bg-sky-50 border-sky-200 text-sky-700'],
                    ['label' => 'Maintenance', 'value' => $displayedRooms->where('status', 'maintenance')->count(), 'color' => 'bg-amber-50 border-amber-200 text-amber-700'],
                ];
            @endphp
            @foreach($stats as $s)
                <div class="rounded-xl border p-3 text-center {{ $s['color'] }}">
                    <div class="text-xl font-bold">{{ $s['value'] }}</div>
                    <div class="text-xs mt-0.5 opacity-80">{{ $s['label'] }}</div>
                </div>
            @endforeach
        </div>

        @if($displayedRooms->isEmpty())
            <div class="bg-card border border-dashed border-border rounded-xl flex flex-col items-center justify-center py-16 gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" class="text-muted-foreground/40"><path fill="currentColor" d="M11.713 12.713Q12 12.425 12 12t-.288-.712T11 11t-.712.288T10 12t.288.713T11 13t.713-.288M7 21v-2l6-1V6.875q0-.375-.225-.675t-.575-.35L7 5V3l5.5.9q1.1.2 1.8 1.025T15 6.85v11.1q0 .725-.475 1.288t-1.2.687zm0-2h10V5H7zm-3 2q-.425 0-.712-.288T3 20t.288-.712T4 19h1V5q0-.825.588-1.412T7 3h10q.825 0 1.413.588T19 5v14h1q.425 0 .713.288T21 20t-.288.713T20 21z"/></svg>
                <p class="text-sm text-muted-foreground">
                    {{ $branch ? 'Belum ada kamar di cabang ini.' : 'Belum ada kamar sama sekali.' }}
                </p>
                <button type="button" onclick="openTambahModal()"
                    class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="currentColor" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
                    Tambah Kamar
                </button>
            </div>
        @else
            <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-muted/40">
                            @foreach(['No. Kamar', 'Cabang', 'Jumlah Kamar', 'Tipe Kamar', 'Harga Sewa / Bulan', 'Status Kamar', 'Aksi'] as $h)
                                <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide {{ $h === 'Aksi' ? 'text-right' : 'text-left' }}">
                                    {{ $h }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border" id="room-tbody">
                        @foreach($displayedRooms->sortBy(fn($r) => [$r->branch->name ?? '', $r->floor, $r->room_number]) as $room)
                            <tr class="room-row hover:bg-muted/20 transition-colors group" data-status="{{ $room->status }}">
                                <td class="px-4 py-3 font-semibold text-foreground">{{ $room->room_number }}</td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $room->branch->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $room->floor }} kamar</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                        @if($room->room_type === 'Suite') bg-violet-100 text-violet-700
                                        @elseif($room->room_type === 'Deluxe') bg-blue-100 text-blue-700
                                        @else bg-slate-100 text-slate-600
                                        @endif">{{ $room->room_type }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-foreground">Rp {{ number_format($room->price_monthly, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                        @if($room->status === 'terisi') bg-emerald-100 text-emerald-700
                                        @elseif($room->status === 'kosong') bg-sky-100 text-sky-700
                                        @else bg-amber-100 text-amber-700
                                        @endif">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick="openEditModal('{{ $room->id }}')" title="Edit"
                                            class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" class="text-muted-foreground"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4"/></svg>
                                        </button>
                                        <button type="button" onclick="openConfirmModal('{{ $room->id }}')" title="Hapus"
                                            class="p-1.5 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" class="text-red-400"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="hidden flex-col items-center justify-center py-16 gap-3" id="status-tab-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" class="text-muted-foreground/40"><path fill="currentColor" d="M11.713 12.713Q12 12.425 12 12t-.288-.712T11 11t-.712.288T10 12t.288.713T11 13t.713-.288M7 21v-2l6-1V6.875q0-.375-.225-.675t-.575-.35L7 5V3l5.5.9q1.1.2 1.8 1.025T15 6.85v11.1q0 .725-.475 1.288t-1.2.687zm0-2h10V5H7zm-3 2q-.425 0-.712-.288T3 20t.288-.712T4 19h1V5q0-.825.588-1.412T7 3h10q.825 0 1.413.588T19 5v14h1q.425 0 .713.288T21 20t-.288.713T20 21z"/></svg>
                    <p class="text-sm text-muted-foreground" id="status-tab-empty-text">Tidak ada kamar dengan status ini.</p>
                </div>

                <div class="px-4 py-3 border-t border-border bg-muted/20 flex items-center justify-between">
                    <span class="text-xs text-muted-foreground">
                        Menampilkan <span id="room-visible-count">{{ $displayedRooms->count() }}</span> dari {{ $displayedRooms->count() }} kamar
                    </span>
                    <span class="text-xs text-muted-foreground">{{ $branch->name ?? 'Semua Cabang' }}</span>
                </div>
            </div>
        @endif

        <div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                    <h2 class="text-base font-semibold text-foreground">Tambah Kamar Baru</h2>
                    <button type="button" onclick="closeTambahModal()" 
                    class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('rooms.store') }}" method="POST" class="px-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                            Cabang <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branch && $branch->id === $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Nomor Kamar <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="room_number" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required placeholder="mis. A01">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Jumlah Kamar <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="floor" min="1" max="20" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                            Tipe Kamar <span class="text-red-500">*</span>
                        </label>
                        <select name="room_type" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="Standar">Standar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                            Harga Sewa per Bulan (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price_monthly" min="0" step="10000" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Masukkan harga kosan" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                            Status Kamar <span class="text-red-500">*</span>
                        </label>
                        <select name="status" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="kosong">Kosong</option>
                            <option value="terisi">Terisi</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-2 py-4">
                        <button type="button" onclick="closeTambahModal()" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">save</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></g></svg>
                            Tambah Kamar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @foreach($displayedRooms as $room)
            <div id="modal-edit-{{ $room->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                    <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                        <h2 class="text-base font-semibold text-foreground">Edit Kamar {{ $room->room_number }}</h2>
                        <button type="button" onclick="closeEditModal('{{ $room->id }}')" 
                        class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="px-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Nomor Kamar <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="room_number" value="{{ $room->room_number }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Jumlah Kamar <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="floor" min="1" max="20" value="{{ $room->floor }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Tipe Kamar <span class="text-red-500">*</span>
                            </label>
                            <select name="room_type" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="Standar" {{ $room->room_type === 'Standar' ? 'selected' : '' }}>Standar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Harga Sewa per Bulan (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price_monthly" min="0" step="10000" value="{{ $room->price_monthly }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Status Kamar <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="kosong" {{ $room->status === 'kosong' ? 'selected' : '' }}>Kosong</option>
                                <option value="terisi" {{ $room->status === 'terisi' ? 'selected' : '' }}>Terisi</option>
                                <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-2 py-4">
                            <button type="button" onclick="closeEditModal('{{ $room->id }}')" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                            <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">save</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></g></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modal-confirm-{{ $room->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                    <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                        <h2 class="text-base font-semibold text-foreground">Konfirmasi Hapus</h2>
                        <button type="button" onclick="closeConfirmModal('{{ $room->id }}')" 
                        class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="px-6 space-y-4">
                        @csrf
                        @method('DELETE')
                        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="-2 -2 24 24" class="text-red-500 shrink-0 mt-0.5"><path fill="currentColor" d="M10 20C4.477 20 0 15.523 0 10S4.477 0 10 0s10 4.477 10 10s-4.477 10-10 10m0-2a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0-13a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V6a1 1 0 0 1 1-1m0 10a1 1 0 1 1 0-2a1 1 0 0 1 0 2"/></svg>
                            <p class="text-sm text-red-700">Apakah Anda yakin ingin menghapus kamar {{ $room->room_number }}? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <div class="flex gap-3 pt-2 py-4">
                            <button type="button" onclick="closeConfirmModal('{{ $room->id }}')" class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">Batal</button>
                            <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-white"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                Ya, Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
    function filterStatus(status) {
        document.querySelectorAll('.status-tab').forEach(tab => {
            const isActive = tab.dataset.status === status;
            tab.classList.toggle('bg-primary', isActive);
            tab.classList.toggle('text-white', isActive);
            tab.classList.toggle('bg-card', !isActive);
            tab.classList.toggle('border', !isActive);
            tab.classList.toggle('border-border', !isActive);
            tab.classList.toggle('text-muted-foreground', !isActive);
        });

        const rows = document.querySelectorAll('.room-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const matches = status === 'semua' || row.dataset.status === status;
            row.classList.toggle('hidden', !matches);
            if (matches) visibleCount++;
        });

        const tbody = document.getElementById('room-tbody');
        const tabEmpty = document.getElementById('status-tab-empty');
        if (tbody && tabEmpty) {
            const noResult = rows.length > 0 && visibleCount === 0;
            tbody.closest('table').classList.toggle('hidden', noResult);
            tabEmpty.classList.toggle('hidden', !noResult);
            tabEmpty.classList.toggle('flex', noResult);
            if (noResult) {
                document.getElementById('status-tab-empty-text').textContent =
                    `Tidak ada kamar berstatus "${status}".`;
            }
        }

        const visibleCountEl = document.getElementById('room-visible-count');
        if (visibleCountEl) visibleCountEl.textContent = visibleCount;
    }

    function openTambahModal() {
        document.getElementById('modal-tambah').classList.remove('hidden');
    }
    function closeTambahModal() {
        document.getElementById('modal-tambah').classList.add('hidden');
    }

    function openEditModal(id) {
        document.getElementById('modal-edit-' + id).classList.remove('hidden');
    }
    function closeEditModal(id) {
        document.getElementById('modal-edit-' + id).classList.add('hidden');
    }

    function openConfirmModal(id) {
        document.getElementById('modal-confirm-' + id).classList.remove('hidden');
    }
    function closeConfirmModal(id) {
        document.getElementById('modal-confirm-' + id).classList.add('hidden');
    }
</script>
@endsection
