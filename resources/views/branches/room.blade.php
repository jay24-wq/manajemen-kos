@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-start gap-3">
        <a href="{{ route('branches.index') }}" class="mt-1 p-1.5 hover:bg-muted rounded-lg transition-colors shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><title xmlns="">arrow-ios-back-outline</title><path fill="currentColor" d="M13.83 19a1 1 0 0 1-.78-.37l-4.83-6a1 1 0 0 1 0-1.27l5-6a1 1 0 0 1 1.54 1.28L10.29 12l4.32 5.36a1 1 0 0 1-.78 1.64"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-semibold text-foreground">{{ $branch->name }}</h1>
            <div class="flex items-center gap-3 mt-1 text-xs text-muted-foreground">
                <span class="flex items-center gap-3 mt-1 text-xs text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">map-pin</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/><path d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0"/></g></svg>{{ $branch->address }}</span>
            </div>
        </div>
        <button type="button" onclick="openTambahModal()"
            class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">plus</title><path fill="#ffffff" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
                Tambah Kamar
        </button>
    </div>

    <div id="modal-tambah"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                <h2 class="text-base font-semibold text-foreground">
                Tambah Kamar Baru
                </h2>
                <button type="button" onclick="closeTambahModal()"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('rooms.store') }}" method="POST" class="px-6 space-y-4">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
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
                    <label for="tipe-tambah" class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                        Tipe Kamar <span class="text-red-500">*</span>
                    </label>
                    <select id="tipe-tambah" name="room_type" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
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
                    <label for="status-tambah" class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                        Status Kamar <span class="text-red-500">*</span>
                    </label>
                    <select id="status-tambah" name="status" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="kosong">Kosong</option>
                        <option value="terisi">Terisi</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2 py-4">
                    <button type="button" onclick="closeTambahModal()"
                    class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                    class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">save</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></g></svg>
                        Tambah Kamar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-3">
        @php
            $stats = [
                [
                'label' => 'Total Kamar',
                'value' => $branch->rooms->count(),
                'color' => 'bg-slate-50 border-slate-200 text-slate-700'
                ],
                [
                'label' => 'Terisi',
                'value' => $branch->rooms->where('status', 'terisi')->count(),
                'color' => 'bg-emerald-50 border-emerald-200 text-emerald-700'
                ],
                [
                'label' => 'Kosong',
                'value' => $branch->rooms->where('status', 'kosong')->count(),
                'color' => 'bg-sky-50 border-sky-200 text-sky-700'
                ],
                [
                'label' => 'Maintenance',
                'value' => $branch->rooms->where('status', 'maintenance')->count(),
                'color' => 'bg-amber-50 border-amber-200 text-amber-700'
                ],
            ]
        @endphp

        @foreach($stats as $s)
        <div class="rounded-xl border p-3 text-center {{ $s['color'] }}">
            <div class="text-xl font-bold">{{ $s['value'] }}</div>
            <div class="text-xs mt-0.5 opacity-80">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>

    @if($branch->rooms->isEmpty())
        <div class="bg-card border border-dashed border-border rounded-xl flex flex-col items-center justify-center py-16 gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="0 0 24 24" class="text-muted-foreground/40"><title xmlns="">door-open-outline-rounded</title><path fill="currentColor" d="M11.713 12.713Q12 12.425 12 12t-.288-.712T11 11t-.712.288T10 12t.288.713T11 13t.713-.288M7 21v-2l6-1V6.875q0-.375-.225-.675t-.575-.35L7 5V3l5.5.9q1.1.2 1.8 1.025T15 6.85v11.1q0 .725-.475 1.288t-1.2.687zm0-2h10V5H7zm-3 2q-.425 0-.712-.288T3 20t.288-.712T4 19h1V5q0-.825.588-1.412T7 3h10q.825 0 1.413.588T19 5v14h1q.425 0 .713.288T21 20t-.288.713T20 21z"/></svg>
            <p class="text-sm text-muted-foreground">Belum ada kamar. Tambahkan kamar pertama.</p>
            <button onclick="openTambahModal()"
                class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">plus</title><path fill="#ffffff" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
                    Tambah Kamar
            </button>
        </div>
    @else
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/40">
                        @php
                            $headers = ["No. Kamar", "Jumlah Kamar", "Tipe Kamar", "Harga Sewa/Bln", "Status", "Penyewa", "Aksi"];
                        @endphp

                        @foreach ($headers as $h)
                            <th class="px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide {{ $h === 'Aksi' ? 'text-right' : 'text-left' }}">
                                {{ $h }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($branch->rooms->sortBy(fn($r) => [$r->floor, $r->room_number]) as $room)
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="px-4 py-3 font-semibold text-foreground">{{ $room->room_number }}</td>
                            <td class="px-4 py-3 text-muted-foreground">{{ $room->floor }} kamar</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600">{{ $room->room_type }}</span>
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
                            <td class="px-4 py-3">
                            @if($room->tenant)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">
                                        {{ strtoupper(substr($room->tenant->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-foreground">{{ $room->tenant->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-muted-foreground">-</span>
                            @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="openEditModal('{{ $room->id }}')" title="Edit"
                                    class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4"/></svg>
                                    </button>
                                    <button onclick="openConfirmModal('{{ $room->id }}')" title="Hapus"
                                    class="p-1.5 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-red-400"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach($branch->rooms as $room)
            <div id="modal-edit-{{ $room->id }}"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                    <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                        <h2 class="text-base font-semibold text-foreground">
                        Edit Kamar {{ $room->room_number }}
                        </h2>
                        <button type="button" onclick="closeEditModal('{{ $room->id }}')"
                        class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
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
                                <input type="text" name="room_number" value="{{ $room->room_number }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required placeholder="mis. A01">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Jumlah Kamar <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="floor" min="1" max="20" value="{{ $room->floor }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required>
                            </div>
                        </div>
                        <div>
                            <label for="tipe-{{ $room->id }}" class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Tipe Kamar <span class="text-red-500">*</span>
                            </label>
                            <select id="tipe-{{ $room->id }}" name="room_type" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="Standar" {{ $room->room_type === 'Standar' ? 'selected' : '' }}>Standar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Harga Sewa per Bulan (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price_monthly" min="0" step="10000" value="{{ $room->price_monthly }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Masukkan harga kosan" required>
                        </div>
                        <div>
                            <label for="status-{{ $room->id }}" class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                Status Kamar <span class="text-red-500">*</span>
                            </label>
                            <select id="status-{{ $room->id }}" name="status" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="kosong" {{ $room->status === 'kosong' ? 'selected' : '' }}>Kosong</option>
                                <option value="terisi" {{ $room->status === 'terisi' ? 'selected' : '' }}>Terisi</option>
                                <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-2 py-4">
                            <button type="button" onclick="closeEditModal('{{ $room->id }}')"
                            class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                            class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">save</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></g></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modal-confirm-{{ $room->id }}"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                    <div class="flex items-center justify-between px-8 py-4 border-b border-border shrink-0">
                        <h2 class="text-base font-semibold text-foreground">
                        Konfirmasi Hapus
                        </h2>
                        <button type="button" onclick="closeConfirmModal('{{ $room->id }}')"
                        class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="px-6 space-y-4">
                        @csrf
                        @method('DELETE')
                        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="-2 -2 24 24" class="text-red-500 shrink-0 mt-0.5"><title xmlns="">alert</title><path fill="currentColor" d="M10 20C4.477 20 0 15.523 0 10S4.477 0 10 0s10 4.477 10 10s-4.477 10-10 10m0-2a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0-13a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V6a1 1 0 0 1 1-1m0 10a1 1 0 1 1 0-2a1 1 0 0 1 0 2"/></svg>
                            <p class="text-sm text-red-700">Apakah Anda yakin ingin menghapus kamar {{ $room->room_number }}? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <div class="flex gap-3 pt-2 py-4">
                            <button type="button" onclick="closeConfirmModal('{{ $room->id }}')"
                            class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                            class="flex-1 px-4 py-2 text-sm font-medium bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-white"><title xmlns="">trash</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                Ya, Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

<script>
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
