<?php

use App\Http\Controllers\BandController;
use App\Http\Controllers\PastShowController;
use App\Http\Controllers\PhotoAlbumController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/shows', [ShowController::class, 'index'])->name('shows.index');
Route::get('/shows/anteriores', [PastShowController::class, 'index'])->name('shows.past');
Route::get('/shows/{show:slug}', [ShowController::class, 'show'])->name('shows.show');
Route::get('/noticias', [PostController::class, 'index'])->name('news.index');
Route::get('/noticias/{post:slug}', [PostController::class, 'show'])->name('news.show');
Route::get('/fotos', [PhotoAlbumController::class, 'index'])->name('photos.index');
Route::get('/fotos/{photoAlbum:slug}', [PhotoAlbumController::class, 'show'])->name('photos.show');
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/banda', BandController::class)->name('band');
Route::view('/loja', 'coming-soon', ['title' => 'Loja'])->name('store.index');
Route::view('/contato', 'coming-soon', ['title' => 'Contato'])->name('contact');
