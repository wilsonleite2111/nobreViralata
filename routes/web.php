<?php

use App\Http\Controllers\BandController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\PastShowController;
use App\Http\Controllers\PhotoAlbumController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/shows', [ShowController::class, 'index'])->name('shows.index');
Route::get('/shows/anteriores', [PastShowController::class, 'index'])->name('shows.past');
Route::get('/shows/{show:slug}', [ShowController::class, 'show'])->name('shows.show');
Route::get('/noticias', [PostController::class, 'index'])->name('news.index');
Route::get('/noticias/{post:slug}', [PostController::class, 'show'])->name('news.show');
Route::get('/fotos', [PhotoAlbumController::class, 'index'])->name('photos.index');
Route::get('/fotos/{photoAlbum:slug}', [PhotoAlbumController::class, 'show'])->name('photos.show');
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/banda', BandController::class)->name('band');
Route::get('/loja', [ProductController::class, 'index'])->name('store.index');
Route::get('/loja/{product:slug}', [ProductController::class, 'show'])->name('store.show');
Route::get('/contato', [ContactMessageController::class, 'create'])->name('contact');
Route::post('/contato', [ContactMessageController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::view('/privacidade', 'privacy')->name('privacy');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::post('/newsletter', [NewsletterSubscriptionController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');
Route::get('/newsletter/confirmar/{subscriber:token}', [NewsletterSubscriptionController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/sair/{subscriber:token}', [NewsletterSubscriptionController::class, 'edit'])->name('newsletter.unsubscribe');
Route::delete('/newsletter/sair/{subscriber:token}', [NewsletterSubscriptionController::class, 'destroy'])->name('newsletter.destroy');
