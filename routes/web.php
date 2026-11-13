<?php

use App\Livewire\Core\Shop\Storefront;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Storefront::class)->name('storefront');
