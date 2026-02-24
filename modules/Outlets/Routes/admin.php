<?php

use Illuminate\Support\Facades\Route;

/**
 * 'admin' middleware and 'outlets' prefix applied to all routes
 *
 * @see \App\Providers\Route::register
 */

Route::admin('outlets', function () {
    Route::get('/', 'Outlets@index')->name('index');
    Route::get('create', 'Outlets@create')->name('create');
    Route::post('/', 'Outlets@store')->name('store');
    Route::get('{outlet}', 'Outlets@show')->name('show');
    Route::get('{outlet}/edit', 'Outlets@edit')->name('edit');
    Route::patch('{outlet}', 'Outlets@update')->name('update');
    Route::delete('{outlet}', 'Outlets@destroy')->name('destroy');
});
