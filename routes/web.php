<?php

use App\Http\Controllers\PastShowController;
use App\Http\Controllers\ShowController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/shows', [ShowController::class, 'index'])->name('shows.index');
Route::get('/shows/anteriores', [PastShowController::class, 'index'])->name('shows.past');
Route::get('/shows/{show:slug}', [ShowController::class, 'show'])->name('shows.show');
Route::view('/noticias', 'coming-soon', ['title' => 'Notícias'])->name('news.index');
Route::view('/fotos', 'coming-soon', ['title' => 'Fotos'])->name('photos.index');
Route::view('/videos', 'coming-soon', ['title' => 'Vídeos'])->name('videos.index');
Route::view('/banda', 'coming-soon', ['title' => 'A Banda'])->name('band');
Route::view('/loja', 'coming-soon', ['title' => 'Loja'])->name('store.index');
Route::view('/contato', 'coming-soon', ['title' => 'Contato'])->name('contact');
