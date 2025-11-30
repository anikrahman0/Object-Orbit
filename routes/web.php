<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3Controller;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// routes/web.php
Route::controller(S3Controller::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('storage-list', 'storageList')->name('storage.list');
    Route::get('storage/create', 'storageCreate')->name('storage.create');
    Route::post('storage/store', 'storageStore')->name('storage.store');
    Route::get('/storage/edit/{id}', 'storageEdit')->name('storage.edit');
    Route::post('/storage/update/{id}', 'storageUpdate')->name('storage.update');
    Route::get('/storage/delete/{id}', 'storageDelete')->name('storage.delete');
    Route::get('/storage/connect/{id}', 'storageConnect')->name('storage.connect');
    Route::post('/create-folder/{connectionId}', 'createFolder') ->name('storage.create.folder');
    Route::post('/storage/{id}/folders/delete', 'deleteMultipleFolders') ->name('storage.folder.deleteMultiple');
});



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
