<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListingClickController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ListingController::class, 'index'])->name('listings.index');
Route::get('/create', [ListingController::class, 'create'])
    ->name('listings.create');
Route::post('/create', [ListingController::class, 'store'])
    ->name('listings.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Catch-all route
// Not to get priority in place of Dashboard, Login, etc.
Route::get('/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/{listing}/apply', [ListingClickController::class, 'store'])->name('listings.apply');
