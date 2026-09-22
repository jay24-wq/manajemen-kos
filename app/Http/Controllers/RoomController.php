<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->query('branch');
        $branch = $branchId ? Branch::find($branchId) : null;

        $rooms = Room::with('branch')->get();
        $branches = Branch::all();
        return view('rooms.index', compact('rooms', 'branches', 'branch'));
    }

    public function create(Request $request)
    {
        $branches = Branch::all();
        $selectedBranchId = $request->query('branch');
        return view('rooms.create', compact('branches', 'selectedBranchId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:20',
            'floor' => 'required|numeric|min:0',
            'room_type' => 'required|string|max:50',
            'price_monthly' => 'required|numeric|min:0',
            'status' => 'required|in:terisi,kosong,maintenance',
        ]);

        Room::create($validated);

        return back()->with('success', 'Kamar baru berhasil ditambahkan');
    }

    public function edit(Room $room)
    {
        $branch = $room->branch;
        return view('rooms.edit', compact('branch', 'room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:20',
            'floor' => 'required|numeric|min:0',
            'room_type' => 'required|string|max:50',
            'price_monthly' => 'required|numeric|min:0',
            'status' => 'required|in:terisi,kosong,maintenance',
        ]);

        $room->update($validated);

        return back()->with('success', 'Data kamar berhasil diperbarui');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Kamar berhasil dihapus');
    }

    public function indexByBranch($branchId)
    {
        $branch = Branch::with('rooms.tenant')->findOrFail($branchId);
        return view('branches.room', compact('branch'));
    }
}
