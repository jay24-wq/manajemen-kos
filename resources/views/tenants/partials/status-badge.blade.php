@if($status === 'aktif')
    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-100 text-emerald-700">Aktif</span>
@elseif($status === 'selesai')
    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600">Selesai</span>
@elseif($status === 'dibatalkan')
    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-red-100 text-red-700">Dibatalkan</span>
@endif