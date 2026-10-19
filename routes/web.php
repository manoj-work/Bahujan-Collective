<?php

use App\Http\Controllers\PlaceSearchController;
use App\Livewire\HomePage;
use App\Livewire\SubmitEvent;
use App\Livewire\AboutPage;
use App\Livewire\ContactPage;
use App\Livewire\ImportData;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

Route::get('/', HomePage::class)->name('home');
Route::get('/places', PlaceSearchController::class)->middleware('throttle:30,1')->name('places.search');
Route::get('/submit-event', SubmitEvent::class)->name('submit-event');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/import', ImportData::class)->name('import');
Route::get('/import/templates/{type}', function (string $type): BinaryFileResponse {
    $templates = [
        'events' => database_path('import-templates/events.csv'),
        'organizations' => database_path('import-templates/organizations.csv'),
    ];

    abort_unless(isset($templates[$type]), 404);

    return response()->download($templates[$type], "{$type}-import-sample.csv", [
        'Content-Type' => 'text/csv',
    ]);
})->name('import.template');
