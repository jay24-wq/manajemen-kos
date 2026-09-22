<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(9);
        return view('branches.index', compact('branches'));
    }

    public function show($id)
    {
        $selectedBranch = Branch::with('rooms')->findOrFail($id);
        return view('branches.show', compact('selectedBranch'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
        'name' => 'required|string|max:100',
        'address' => 'required|string|max:255',
        'notes' => 'nullable|string',
        ]);

        Branch::create($validate);

        return redirect()->route('branches.index')->with('success', 'Cabang Berhasil Ditambahkan');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validate = $request->validate([
        'name' => 'required|string|max:100',
        'address' => 'required|string|max:255',
        'notes' => 'nullable|string',
        ]);

        $branch->update($validate);

        return redirect()->route('branches.index')->with('success', 'Cabang Berhasil Diperbaharui');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Cabang Berhasil Dihapus');
    }
}
