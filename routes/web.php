<?php

use App\Domains\Records\Controllers\ConfigController;
use App\Domains\Users\Controllers\UserController;
use App\Domains\Records\Controllers\RecordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->controller(UserController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::get('/signup', 'showSignup')->name('signup');

    Route::post('/login', 'login')->name('login.store');
    Route::post('/signup', 'signup')->name('signup.store');
});

Route::middleware('auth')->controller(UserController::class)->group(function () {
    Route::get('/profile', 'showProfile')->name('profile');
    Route::get('/users', 'showUsers')->name('users.index');
    Route::get('/users/{id}', 'showUser')->name('users.show');
    Route::get('/settings', 'showSettings')->name('user.settings');
    Route::post('/profile', 'updateProfile')->name('profile.update');
    Route::post('/profile/updatePassword', 'updatePassword')->name('profile.updatePassword');
});

Route::middleware('auth')->controller(ConfigController::class)->group(function (){
    Route::post('/config/addArea', 'addArea')->name('area.add');
    Route::post('/config/addType', 'addType')->name('type.add');
    Route::post('/config/addCounterparty', 'addCounterparty')->name('counterparty.add');
    Route::post('/config/addState', 'addState')->name('state.add');
});

Route::middleware('auth')->controller(RecordController::class)->group(function () {
    Route::get('/records', 'index')->name('records.index');
    Route::get('/records/add', 'showAddRecords')->name('records.add');
    Route::get('/record/add', 'showAddRecord')->name('record.add');
    Route::get('/record/{id}', 'showRecord')->name('record.show');
    Route::get('/report/generate', 'showGenerateReport')->name('report.generate');

    Route::post('/records/add', 'addRecords')->name('add.records');
    Route::post('/record/add', 'addRecord')->name('add.record');
    Route::post('/record/edit', 'updateRecord')->name('record.update');
    Route::post('/record/delete', 'deleteRecord')->name('record.delete');
    Route::post('/record/addmovement', 'addMovement')->name('movement.add');
    Route::post('/report/download', 'downloadGeneratedReport')->name('report.download');
});

Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

    Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home', [
            'title' => 'Inicio',
        ]);
    })->name('home');
});




