<?php

use App\Domains\Files\Controllers\FileController;
use App\Domains\Files\Controllers\ReportController;
use App\Domains\Records\Controllers\ConfigController;
use App\Domains\Records\Controllers\RecordController;
use App\Domains\Users\Controllers\UserController;
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

Route::middleware('auth')->controller(ConfigController::class)->group(function () {
    Route::post('/config/addArea', 'addArea')->name('area.add');
    Route::post('/config/addType', 'addType')->name('type.add');
    Route::post('/config/addCounterparty', 'addCounterparty')->name('counterparty.add');
    Route::post('/config/addState', 'addState')->name('state.add');
});

Route::middleware('auth')->controller(RecordController::class)->group(function () {
    Route::get('/records', 'index')->name('records.index');
    Route::get('/record/add', 'showAddRecord')->name('record.add');
    Route::get('/records/{id}', 'showRecord')->name('record.show');
    Route::post('/record/add', 'addRecord')->name('add.record');
    Route::post('/record/edit', 'updateRecord')->name('record.update');
    Route::post('/record/delete', 'deleteRecord')->name('record.delete');
    Route::post('/record/addmovement', 'addMovement')->name('movement.add');
});

Route::middleware('auth')->controller(FileController::class)->group(function () {
    Route::get('/file/upload', 'showUploadFile')->name('file.add');
    Route::post('/file/upload', 'uploadFile')->name('add.file');
});

Route::middleware('auth')->controller(ReportController::class)->group(function () {
    Route::get('/report/generate', 'index')->name('report.generate');
    Route::get('/report/download', 'download')->name('report.generate.excel');
});

Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/homepage', function () {
        return Inertia::render('Home', [
            'title' => 'Inicio',
        ]);
    })->name('home');
    Route::get('/', function () {
        return Inertia::render('Home', [
            'title' => 'Inicio',
        ]);
    })->name('home');
});
