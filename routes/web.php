<?php

use Illuminate\Support\Facades\Route;
use Spatie\GoogleCalendar\Event;
use App\Http\Controllers\GoogleCalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/', function () {

//    $eventId = Event::get()->first()->id;
//
//    $e = Event::get();
//
//    dd($eventId, $e);

        return view('layouts.app');

});

Route::get('/addEvent', function () {
    return view('addEvent');
});


Route::post('/booking', [GoogleCalendarController::class, 'store'])->name('booking.store');

Route::get('/display', [GoogleCalendarController::class, 'fetchEvents']);

Route::get('/allEvents', [GoogleCalendarController::class, 'displayAllEvents']);

Route::get('/events/{id}/update', [GoogleCalendarController::class, 'showUpdateForm'])->name('events.update');

Route::put('/events/{id}/update', [GoogleCalendarController::class, 'update'])->name('events.update');

Route::delete('/events/{id}/delete', [GoogleCalendarController::class, 'delete'])->name('events.delete');
