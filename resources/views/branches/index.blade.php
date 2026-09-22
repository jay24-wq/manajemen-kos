@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Manajemen Cabang</h1>
            <p class="text-sm text-muted-foreground mt-0.5">{{ $branches->count() }} cabang terdaftar</p>
        </div>
        <button type="button" onclick="openTambahModal()"
            class="flex items-center gap-2 bg-primary text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">plus</title><path fill="#ffffff" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
            Tambah Cabang
        </button>
    </div>

    <div id="modal-tambah"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h2 class="text-base font-semibold text-foreground">
                Tambah Cabang Baru
                </h2>
                <button type="button" onclick="closeTambahModal()"
                class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('branches.store') }}" method="POST" class="px-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                        Nama Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required placeholder="mis. Kos Margonda">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required placeholder="Jl. Contoh No. 1, Kota"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                        Notes
                    </label>
                    <input type="text" name="notes" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                </div>
                <div class="flex gap-3 pt-2 py-4">
                    <button type="button" onclick="closeTambahModal()"
                    class="flex-1 px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-muted transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                    class="flex-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">save</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></g></svg>
                        Tambah Cabang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($branches as $b)
            @php
                $terisi = $b->rooms->where('status', 'terisi')->count();
                $kosong = $b->rooms->where('status', 'kosong')->count();
                $maint = $b->rooms->where('status', 'maintenance')->count();
                $pct = $b->rooms->count() > 0 ? round(($terisi / $b->rooms->count()) * 100 ) : 0;
            @endphp

            <div class="bg-card border border-border rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow group">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-primary"><title xmlns="">apartment</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.5 2.5h-3c-1.886 0-2.828 0-3.414.586S2.5 4.614 2.5 6.5v11c0 1.886 0 2.828.586 3.414s1.528.586 3.414.586h7v-15c0-1.886 0-2.828-.586-3.414S11.386 2.5 9.5 2.5m-4 3h1m3 0h1m-5 3h1m3 0h1m-5 3h1m3 0h1m-5 3h1m3 0h1m-2.5 7v-3m9.5-11h-4v14h4c1.886 0 2.828 0 3.414-.586s.586-1.528.586-3.414v-6c0-1.886 0-2.828-.586-3.414S19.386 7.5 17.5 7.5m-.5 3h1m-1 3h1m-1 3h1"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-foreground leading-snug">{{ $b->name }}</h3>
                            <div class="flex items-center gap-1 mt-1 text-xs text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">map-pin</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/><path d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0"/></g></svg>
                                <span class="line-clamp-1">{{ $b->address }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" onclick="openEditModal ('{{ $b->id }}')"
                        class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4"/></svg>
                        </button>
                        <button type="button" onclick="openConfirmModal ('{{ $b->id }}')"
                        class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-red-400"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                        </button>
                    </div>
                </div>

                <div id="modal-edit-{{ $b->id }}"
                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                    <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-border shrink-0">
                            <h2 class="text-base font-semibold text-foreground">
                            Edit Cabang
                            </h2>
                            <button type="button" onclick="closeEditModal('{{ $b->id }}')"
                            class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form action="{{ route('branches.update', $b->id) }}" method="POST" class="px-6 space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Nama Cabang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ $b->name }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required placeholder="mis. Kos Margonda">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" required placeholder="Jl. Contoh No. 1, Kota">{{ $b->address }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground mb-1.5 uppercase tracking-wide">
                                    Notes
                                </label>
                                <input type="text" name="notes" value="{{ $b->notes }}" class="w-full px-3 py-2 text-sm bg-input-background border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            </div>
                            <div class="flex gap-3 pt-2 py-4">
                                <button type="button" onclick="closeEditModal('{{ $b->id }}')"
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

                <div id="modal-confirm-{{ $b->id }}"
                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                    <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col border border-border space-y-4">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-border shrink-0">
                            <h2 class="text-base font-semibold text-foreground">
                            Konfirmasi Hapus
                            </h2>
                            <button type="button" onclick="closeConfirmModal('{{ $b->id }}')"
                            class="p-1.5 hover:bg-muted rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">x</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form action="{{ route('branches.destroy', $b->id) }}" method="POST" class="px-6 space-y-4">
                            @csrf
                            @method('DELETE')
                            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="-2 -2 24 24" class="text-red-500 shrink-0 mt-0.5"><title xmlns="">alert</title><path fill="currentColor" d="M10 20C4.477 20 0 15.523 0 10S4.477 0 10 0s10 4.477 10 10s-4.477 10-10 10m0-2a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0-13a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V6a1 1 0 0 1 1-1m0 10a1 1 0 1 1 0-2a1 1 0 0 1 0 2"/></svg>
                                <p class="text-sm text-red-700">Apakah Anda yakin ingin menghapus {{ $b->name }}? Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                            <div class="flex gap-3 pt-2 py-4">
                                <button type="button" onclick="closeConfirmModal('{{ $b->id }}')"
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

                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-muted-foreground">Hunian</span>
                        <span class="text-xs font-semibold text-foreground">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 bg-muted rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                <div class="flex gap-2 mb-4">
                    <div class="flex-1 bg-emerald-50 rounded-lg px-2 py-1.5 text-center">
                        <div class="text-sm font-bold text-emerald-700">{{ $terisi }}</div>
                        <div class="text-[10px] text-emerald-600">Terisi</div>
                    </div>
                    <div class="flex-1 bg-emerald-50 rounded-lg px-2 py-1.5 text-center">
                        <div class="text-sm font-bold text-sky-600">{{ $kosong }}</div>
                        <div class="text-[10px] text-sky-500">Kosong</div>
                    </div>
                    @if ($maint > 0)
                        <div class="flex-1 bg-emerald-50 rounded-lg px-2 py-1.5 text-center">
                            <div class="text-sm font-bold text-amber-600">{{ $maint }}</div>
                            <div class="text-[10px] text-amber-500">Maintenance</div>
                        </div>
                    @endif
                </div>

                <a href="{{ route('branches.rooms', $b->id) }}"
                class="appearance-none no-underline w-full py-2 text-xs font-medium text-primary border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors inline-block text-center">
                    Kelola Kamar →
                </a>
            </div>
        @endforeach

        <button onclick="openTambahModal()"
        class="border-2 border-dashed border-border rounded-xl p-5 flex flex-col items-center justify-center gap-3 hover:border-primary/40 hover:bg-primary/5 transition-all group min-h-[200px]">
            <div class="w-10 h-10 rounded-xl bg-muted flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-muted-foreground group-hover:text-primary"><title xmlns="">plus</title><path fill="currentColor" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2"/></svg>
            </div>
            <span class="text-sm text-muted-foreground group-hover:text-primary font-medium transition-colors">Tambah Cabang Baru</span>
        </button>
    </div>
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
