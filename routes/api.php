<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\IsbnController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/search/books', [SearchController::class, 'books'])->name('api.search.books');
Route::get('/search/members', [SearchController::class, 'members'])->name('api.search.members');
Route::get('/search/borrow-records', [SearchController::class, 'borrowRecords'])->name('api.search.borrow-records');

Route::get('/isbn/{isbn}', [IsbnController::class, 'show'])->name('api.isbn.show');
Route::get('/barcode/{data}', [BarcodeController::class, 'generate'])->name('api.barcode.generate');
