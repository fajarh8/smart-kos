<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\SessionController;
use App\Livewire\UserDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function(){
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

Route::get('/home', function(){
    return redirect('/admin');
});

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [AdminController::class, 'index']);
    // Route::get('/dashboard/admin', [AdminController::class, 'admin'])->middleware('role:admin')->name('admin.dashboard');
    Route::get('/dashboard/admin/kos', [KosController::class, 'index'])->middleware('role:admin')->name('admin.kos');
    Route::get('/dashboard/admin/tagihan', [KosController::class, 'electricBill'])->middleware('role:admin')->name('admin.listrik');
    Route::get('/dashboard/admin/profile', [KosController::class, 'profile'])->middleware('role:admin')->name('admin.profil');
    Route::get('/dashboard/admin/request', [KosController::class, 'userRequest'])->middleware('role:admin')->name('admin.request');
    Route::post('/dashboard/admin/kos', [KosController::class, 'store'])->middleware('role:admin')->name('admin.kos.store');
    Route::put('/dashboard/admin/kos/{id}', [KosController::class, 'update'])->middleware('role:admin')->name('admin.kos.update');
    Route::delete('/dashboard/admin/kos/{id}', [KosController::class, 'destroy'])->middleware('role:admin')->name('admin.kos.destroy');
    // Route::get('/', UserDashboard::class)->middleware('role:user');

    Route::get('/dashboard/user', [SensorDataController::class, 'index'])->middleware('role:user');
    Route::get('/dashboard/user/automation', [SensorDataController::class, 'automation'])->middleware('role:user');
    Route::get('/dashboard/user/history', [SensorDataController::class, 'history'])->middleware('role:user');
    Route::get('/dashboard/user/userprofile', [SensorDataController::class, 'userProfile'])->middleware('role:user');
    Route::get('/dashboard/user/kossearch', [SensorDataController::class, 'kosSearch'])->middleware('role:user');
    //Route::get('/dashboard/user', [AdminController::class, 'user'])->middleware('role:user');
    Route::get('/logout', [SessionController::class, 'logout']);
});
