<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rota pública - envio anônimo de mensagens
Route::get('/', [MessageController::class, 'create'])->name('home');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas autenticadas
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Alteração de senha (primeiro acesso)
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('update-password');

    // Rotas administrativas
    Route::middleware(['App\Http\Middleware\AdminMiddleware', 'App\Http\Middleware\CheckFirstLogin'])->prefix('admin')->name('admin.')->group(function () {
        // Mensagens
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::post('/messages/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('messages.toggle-read');
        Route::get('/messages/{message}/download', [MessageController::class, 'downloadAttachment'])->name('messages.download');

        // Tipos de Mensagem
        Route::resource('message-types', MessageTypeController::class);

        // Usuários
        Route::resource('users', UserController::class);

        // Configurações
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
