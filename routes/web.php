<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

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

Route::get('/', function () {
    return view('game');
});

Route::post('start', [GameController::class, 'startGame'])
    ->name('game.startGame');

Route::post('move', [GameController::class, 'Move'])
    ->name('game.move');

Route::get('game-results', [GameController::class, 'gameResults'])
    ->name('game.results');
