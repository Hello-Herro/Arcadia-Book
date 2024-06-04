<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('', function() {
//     return view((''))
// }) ->middleware('auth');

// Route::get('/', function(){
// return view('Home');
// })->middleware('auth');

// Group for only_guest middleware
Route::middleware('only_guest')->group(function(){
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/', [AuthController::class, 'authenticating']);
    Route::get('/SignUp', [AuthController::class, 'register'])->name('register');
});

// Group for auth middleware
Route::middleware('auth')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::get('Dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'only_admin']);
    Route::get('/Dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('only_admin');

    // Route::get('home', [HomeController::class, 'Home'])->middleware('auth', 'only_users');
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('only_users');
    Route::get('/books', [BookController::class, 'books'])->name('books');
});
