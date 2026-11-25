<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LoanPostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────
Route::get('/', [FeedController::class, 'index'])->name('feed');

// ─── Guest Only ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ─── Authenticated ───────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Loan Posts CRUD
    Route::get('/posts/create', [LoanPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [LoanPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [LoanPostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [LoanPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [LoanPostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [LoanPostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/posts/{post}/close', [LoanPostController::class, 'close'])->name('posts.close');
    Route::get('/posts/{post}/pdf', [LoanPostController::class, 'downloadPdf'])->name('posts.pdf');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // My Posts
    Route::get('/my-posts', [LoanPostController::class, 'myPosts'])->name('my-posts');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ─── Admin ───────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/posts', [PostManagementController::class, 'index'])->name('posts');
    Route::delete('/posts/{post}', [PostManagementController::class, 'destroy'])->name('posts.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/xlsx', [ReportController::class, 'exportXlsx'])->name('reports.xlsx');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users');
    Route::patch('/users/{user}/toggle-role', [UserManagementController::class, 'toggleRole'])->name('users.toggle-role');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});