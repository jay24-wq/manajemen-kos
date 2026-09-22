<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Tenantcontroller;
use Illuminate\Support\Facades\Route;
use App\Livewire\KontrakPage; 
use App\Livewire\PembayaranPage; 

Route::get('/', function () {
    return view('welcome');
});

Route::resource('branches', BranchController::class);
Route::resource('rooms', RoomController::class);
Route::get('/branches/{branch}/rooms', [RoomController::class, 'indexByBranch'])->name('branches.rooms');
Route::resource('tenants', Tenantcontroller::class)->only(['index', 'store', 'update', 'destroy']);
Route::get('/kontrak', KontrakPage::class)->name('kontrak.index');
Route::get('/pembayaran', PembayaranPage::class)->name('pembayaran.index');