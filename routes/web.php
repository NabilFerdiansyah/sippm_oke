<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Manager\AkunController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\HistoriController;
use App\Http\Controllers\Manager\PenugasanController;
use App\Http\Controllers\Manager\ValidasiAkhirController;
use App\Http\Controllers\Manager\ValidasiController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Operator\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Teknisi\DashboardController as TeknisiDashboardController;
use App\Http\Controllers\Teknisi\HasilController;
use App\Http\Controllers\Teknisi\RiwayatController;
use App\Http\Controllers\Teknisi\TugasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIPPM PG Rendeng - Web Routes
|--------------------------------------------------------------------------
| Setiap route diberi nama sesuai screen id pada mockup supaya alur
| navigasi (go('...')) identik dengan mockup.
*/

Route::redirect('/', '/login');

// ---- Guest (Splash + Login) ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:login');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Semua peran (setelah login) ----
Route::middleware(['auth', 'force.password.change'])->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');

    // ---- Operator ----
    Route::prefix('operator')->name('operator.')->middleware('role:operator')->group(function () {
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/laporan/buat', [LaporanController::class, 'create'])->name('laporan.create');
        Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
        Route::get('/laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');
    });

    // ---- Manager ----
    Route::prefix('manager')->name('manager.')->middleware('role:manager')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/laporan/{laporan}/validasi', [ValidasiController::class, 'show'])->name('laporan.validasi');
        Route::post('/laporan/{laporan}/validasi/setujui', [ValidasiController::class, 'approve'])->name('laporan.validasi.approve');
        Route::post('/laporan/{laporan}/validasi/tolak', [ValidasiController::class, 'reject'])->name('laporan.validasi.reject');

        Route::get('/laporan/{laporan}/penugasan', [PenugasanController::class, 'show'])->name('laporan.penugasan');
        Route::post('/laporan/{laporan}/penugasan', [PenugasanController::class, 'store'])->name('laporan.penugasan.store');

        Route::get('/laporan/{laporan}/validasi-akhir', [ValidasiAkhirController::class, 'show'])->name('laporan.validasiAkhir');
        Route::post('/laporan/{laporan}/validasi-akhir/setujui', [ValidasiAkhirController::class, 'approve'])->name('laporan.validasiAkhir.approve');
        Route::post('/laporan/{laporan}/validasi-akhir/kembalikan', [ValidasiAkhirController::class, 'kembalikan'])->name('laporan.validasiAkhir.kembalikan');

        Route::get('/histori', [HistoriController::class, 'index'])->name('histori');

        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::get('/akun/tambah', [AkunController::class, 'create'])->name('akun.create');
        Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
        Route::get('/akun/{user}/berhasil', [AkunController::class, 'berhasil'])->name('akun.berhasil');
        Route::post('/akun/{user}/reset-password', [AkunController::class, 'resetPassword'])->name('akun.resetPassword');
        Route::post('/akun/{user}/toggle-status', [AkunController::class, 'toggleStatus'])->name('akun.toggleStatus');
    });

    // ---- Teknisi ----
    Route::prefix('teknisi')->name('teknisi.')->middleware('role:teknisi')->group(function () {
        Route::get('/dashboard', [TeknisiDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tugas/{laporan}', [TugasController::class, 'show'])->name('tugas.show');
        Route::post('/tugas/{laporan}/mulai', [TugasController::class, 'mulai'])->name('tugas.mulai');
        Route::get('/hasil', [HasilController::class, 'index'])->name('hasil.index');
        Route::get('/tugas/{laporan}/hasil', [HasilController::class, 'edit'])->name('tugas.hasil.edit');
        Route::put('/tugas/{laporan}/hasil', [HasilController::class, 'update'])->name('tugas.hasil.update');
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    });
});
