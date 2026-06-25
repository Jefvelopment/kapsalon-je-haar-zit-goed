<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');

//Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.send');

//Producten
Route::get('/producten', [ProductController::class, 'index'])->name('products');
Route::get('/producten/{id}', [ProductController::class, 'view'])->name('products.view');
Route::middleware(['auth', 'verified', 'owner'])->group(function () {
    Route::get('/producten/aanmaken', [ProductController::class, 'create'])->name('products.create');
    Route::post('/producten', [ProductController::class, 'store'])->name('products.store');
    Route::get('/producten/{id}/bewerken', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/producten/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/producten/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});

//Winkelmandje
Route::get('/mandje', [CartController::class, 'index'])->name('cart.index');
Route::post('/mandje/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/mandje/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/mandje/{product}', [CartController::class, 'remove'])->name('cart.remove');

//Bestellingen
Route::get('/bestellen', [OrderController::class, 'create'])->name('orders.create');
Route::post('/bestellen', [OrderController::class, 'store'])->name('orders.store');
Route::get('/mijn-bestellingen', [OrderController::class, 'index'])->middleware(['auth', 'verified'])->name('orders.index');
Route::middleware(['auth', 'verified', 'owner'])->group(function () {
    Route::get('/bestellingen-beheer', [OrderController::class, 'manage'])->name('orders.manage');
    Route::patch('/bestellingen-beheer/{id}', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});

//Behandelingen
Route::get('/behandelingen', [TreatmentController::class, 'index'])->name('treatments');
Route::get('/behandelingen/aanmaken', [TreatmentController::class, 'create'])->middleware(['auth', 'verified'])->name('treatments.create');
Route::get('/behandelingen/{id}', [TreatmentController::class, 'view'])->name('treatments.view');
Route::get('/behandelingen/{id}/bewerken', [TreatmentController::class, 'edit'])->name('treatments.edit');
Route::patch('/behandelingen/{id}', [TreatmentController::class, 'update'])->middleware(['auth', 'verified'])->name('treatments.update');
Route::delete('/behandelingen/{id}', [TreatmentController::class, 'destroy'])->middleware(['auth', 'verified'])->name('treatments.destroy');
Route::post('/behandelingen', [TreatmentController::class, 'store'])->middleware(['auth', 'verified'])->name('treatments.store');

//Afspraken
// Publieke kalenderweergave: vr iedereen bereikbaar, geen login vereist.
Route::get('/afspraken', [AppointmentController::class, 'index'])->name('appointments');
Route::get('/afspraken/nieuw', [AppointmentController::class, 'create'])->middleware(['auth', 'verified'])->name('appointments.create');
Route::get('/afspraken/{id}', [AppointmentController::class, 'view'])->name('appointments.view');
Route::post('/afspraken', [AppointmentController::class, 'store'])->middleware(['auth', 'verified'])->name('appointments.store');
Route::get('/afspraken/{id}/bewerken', [AppointmentController::class, 'edit'])->name('appointments.edit');
Route::patch('/afspraken/{id}', [AppointmentController::class, 'update'])->middleware(['auth', 'verified'])->name('appointments.update');
Route::delete('/afspraken/{id}', [DashboardController::class, 'destroy'])->middleware(['auth', 'verified'])->name('appointments.destroy');

//Kalender
Route::middleware(['auth', 'verified', 'owner'])->group(function () {
    Route::get('/schema/bewerken', [ScheduleController::class, 'edit'])->name('schedule.edit');
    Route::patch('/schema/openingstijden', [ScheduleController::class, 'updateBusinessHours'])->name('schedule.business-hours.update');
    Route::post('/schema/blokkades', [ScheduleController::class, 'storeBlock'])->name('schedule.blocks.store');
    Route::delete('/schema/blokkades/{id}', [ScheduleController::class, 'destroyBlock'])->name('schedule.blocks.destroy');
});

//Medewerkers
Route::get('/medewerkers/{id}/bewerken', [UserController::class, 'edit'])->middleware(['auth', 'verified'])->name('users.edit');
Route::patch('/medewerkers/{id}', [UserController::class, 'update'])->middleware(['auth', 'verified'])->name('users.update');
Route::delete('/medewerkers/{id}', [UserController::class, 'destroy'])->middleware(['auth', 'verified'])->name('users.destroy');
Route::get('/medewerkers/aanmaken', [UserController::class, 'create'])->middleware(['auth', 'verified'])->name('users.create');
Route::post('/medewerkers', [UserController::class, 'store'])->middleware(['auth', 'verified'])->name('users.store');

require __DIR__.'/auth.php';