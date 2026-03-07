<?php

use App\Livewire\HomePage;
use App\Livewire\SubmitEvent;
use App\Livewire\AboutPage;
use App\Livewire\ContactPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/submit-event', SubmitEvent::class)->name('submit-event');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');
