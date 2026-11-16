<?php

use App\Http\Controllers\HRDController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\OwnerController;
use Illuminate\Support\Facades\Route;

/*
|----------------------- RBAC Protected Routes -----------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Karyawan routes
    Route::middleware('role:karyawan')->prefix('karyawan')->group(function () {
        Route::get('dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
        Route::post('absensi', [KaryawanController::class, 'absensi'])->name('karyawan.absensi');
        Route::get('slip-gaji', [KaryawanController::class, 'slipGaji'])->name('karyawan.slip-gaji');
    });

    // HRD routes
    Route::middleware('role:hrd')->prefix('hrd')->group(function () {
        Route::get('dashboard', [HRDController::class, 'dashboard'])->name('hrd.dashboard');
        Route::post('leave-requests', [HRDController::class, 'storeLeaveRequest'])->name('hrd.leave-requests.store');
        Route::get('leave-requests', [HRDController::class, 'index'])->name('hrd.leave-requests.index');
    });

    // Owner routes
    Route::middleware('role:owner')->prefix('owner')->group(function () {
        Route::get('dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    });
});

require __DIR__.'/auth.php';