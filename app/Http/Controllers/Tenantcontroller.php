<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Rental;
use Illuminate\Http\Request;

class Tenantcontroller extends Controller
{
    public function index(Request $request)
    {
        $search = strtolower(trim($request->input('search', '')));
        $selectedId = $request->input('selected_id');

        // Query Pencarian
        $query = Tenant::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                ->orWhere('ktp_number', 'LIKE', "%{$search}%")
                ->orWhereRaw('LOWER(emergency_contact->>\'name\') LIKE ?', ["%{$search}%"]);
            });
        }

        $totalTenants = Tenant::count();
        $tenants = $query->paginate(10)->appends($request->all());

        // Detail Penyewa jika dipilih
        $detailTenant = null;
        $tenantRentals = collect();
        if ($selectedId) {
            $detailTenant = Tenant::find($selectedId);
            if ($detailTenant) {
                $tenantRentals = Rental::with(['rooms.branch'])
                    ->where('tenant_id', $selectedId)
                    ->orderBy('start_date', 'desc')
                    ->get();
            }
        }

        return view('tenants.index', compact('tenants','totalTenants','search','detailTenant','tenantRentals','selectedId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => 'required|string|max:100',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:100',
        'ktp_number' => 'nullable|string|max:30',
        'emergency_contact_name' => 'nullable|string|max:100',
        'emergency_contact_relation' => 'nullable|string|max:50',
        'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        Tenant::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'ktp_number' => $validated['ktp_number'] ?? null,
        'emergency_contact' => [
            'name' => $validated['emergency_contact_name'] ?? null,
            'relation' => $validated['emergency_contact_relation'] ?? null,
            'phone' => $validated['emergency_contact_phone'] ?? null,
        ],
        ]);
        return redirect()->route('tenants.index')->with('success', 'Penyewa baru berhasil ditambahkan');
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
        'name' => 'required|string|max:100',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:100',
        'ktp_number' => 'nullable|string|max:30',
        'emergency_contact_name' => 'nullable|string|max:100',
        'emergency_contact_relation' => 'nullable|string|max:50',
        'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $tenant->update([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'ktp_number' => $validated['ktp_number'] ?? null,
        'emergency_contact' => [
            'name' => $validated['emergency_contact_name'] ?? null,
            'relation' => $validated['emergency_contact_relation'] ?? null,
            'phone' => $validated['emergency_contact_phone'] ?? null,
        ],
        ]);
        return redirect()->route('tenants.index', array_filter(['selected_id' => $id]))->with('success', 'Data penyewa berhasil diperbarui');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant = Tenant::findOrFail($id);

        if ($tenant->rentals()->where('status', 'aktif')->exists()) {
            return redirect()->back()->with('error', 'Penyewa tidak bisa dihapus karena masih memiliki riwayat kontrak sewa.');
        }

        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Penyewa berhasil dihapus');
    }
}
