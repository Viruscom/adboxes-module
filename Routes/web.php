<?php

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

use Illuminate\Support\Facades\Route;
use Modules\AdBoxes\Http\Controllers\AdBoxesController;

/* Admin */
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], static function () {

    /* AdBoxes */
    Route::group(['prefix' => 'ad_boxes', 'middleware' => 'auth'], static function () {
        Route::get('/', [AdBoxesController::class, 'index'])->name('ad-boxes');
        Route::get('/create', [AdBoxesController::class, 'create'])->name('ad-boxes.create');
        Route::post('/store', [AdBoxesController::class, 'store'])->name('ad-boxes.store');
        Route::get('/{id}/edit', [AdBoxesController::class, 'edit'])->name('ad-boxes.edit');
        Route::post('/{id}/update', [AdBoxesController::class, 'update'])->name('ad-boxes.update');
        Route::delete('/{id}/delete', [AdBoxesController::class, 'delete'])->name('ad-boxes.delete');
        Route::get('/{id}/image/delete', [AdBoxesController::class, 'imgDelete'])->name('ad-boxes.delete-img');
        Route::delete('/delete/multiple/', [AdBoxesController::class, 'deleteMultiple'])->name('ad-boxes.delete-multiple');
        Route::get('/{id}/show', [AdBoxesController::class, 'show'])->name('ad-boxes.show');
        Route::post('/active/{id}/{active}', [AdBoxesController::class, 'active'])->name('ad-boxes.active');
        Route::post('/active/multiple/{active}', [AdBoxesController::class, 'activeMultiple'])->name('ad-boxes.active-multiple');
        Route::get('/move/up/{id}', [AdBoxesController::class, 'positionUp'])->name('ad-boxes.position-up');
        Route::get('/move/down/{id}', [AdBoxesController::class, 'positionDown'])->name('ad-boxes.position-down');
        Route::get('/{adBoxType}/editButton', [AdBoxesController::class, 'editButton'])->name('ad-boxes.edit-button');
        Route::post('/{adBoxType}/updateButton', [AdBoxesController::class, 'updateButton'])->name('ad-boxes.update-button');
        Route::get('{id}/return_to_waiting', [AdBoxesController::class, 'returnToWaiting'])->name('ad-boxes.return-to-waiting');
    });
});




