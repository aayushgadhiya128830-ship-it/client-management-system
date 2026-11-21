<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/clients');

// index, create, store, show, edit, update, destroy
Route::resource('clients', ClientController::class);
