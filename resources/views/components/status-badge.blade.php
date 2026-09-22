@props(['status'])

@php
$map = [
    'aktif'      => ['bg-emerald-100', 'text-emerald-700', 'Aktif'],
    'selesai'    => ['bg-slate-100',   'text-slate-600',   'Selesai'],
    'dibatalkan' => ['bg-red-100',     'text-red-600',     'Dibatalkan'],
];
[$bg, $text, $label] = $map[$status] ?? ['bg-slate-100', 'text-slate-600', $status];
@endphp

<span class="{{ $bg }} {{ $text }} text-[10px] font-bold px-2 py-1 rounded-full whitespace-nowrap">
    {{ $label }}
</span>
