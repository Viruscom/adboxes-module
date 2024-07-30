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
    Route::group(['prefix' => 'ad-boxes'], static function () {
        Route::get('/', [AdBoxesController::class, 'index'])->name('admin.ad-boxes.index');
        Route::get('/create', [AdBoxesController::class, 'create'])->name('admin.ad-boxes.create');
        Route::post('/store', [AdBoxesController::class, 'store'])->name('admin.ad-boxes.store');

        Route::group(['prefix' => 'multiple'], static function () {
            Route::get('active/{active}', [AdBoxesController::class, 'activeMultiple'])->name('admin.ad-boxes.active-multiple');
            Route::get('delete', [AdBoxesController::class, 'deleteMultiple'])->name('admin.ad-boxes.delete-multiple');
        });

        Route::group(['prefix' => '{adBoxType}'], static function () {
            Route::get('editButton', [AdBoxesController::class, 'editButton'])->name('admin.ad-boxes.edit-button');
            Route::post('updateButton', [AdBoxesController::class, 'updateButton'])->name('admin.ad-boxes.update-button');
        });

        Route::group(['prefix' => '{id}'], static function () {
            Route::get('edit', [AdBoxesController::class, 'edit'])->name('admin.ad-boxes.edit');
            Route::post('update', [AdBoxesController::class, 'update'])->name('admin.ad-boxes.update');
            Route::get('delete', [AdBoxesController::class, 'delete'])->name('admin.ad-boxes.delete');
            Route::get('show', [AdBoxesController::class, 'show'])->name('admin.ad-boxes.show');
            Route::get('/active/{active}', [AdBoxesController::class, 'active'])->name('admin.ad-boxes.changeStatus');
            Route::get('position/up', [AdBoxesController::class, 'positionUp'])->name('admin.ad-boxes.position-up');
            Route::get('position/down', [AdBoxesController::class, 'positionDown'])->name('admin.ad-boxes.position-down');
            Route::get('image/delete', [AdBoxesController::class, 'deleteImage'])->name('admin.ad-boxes.delete-image');
            Route::get('return_to_waiting', [AdBoxesController::class, 'returnToWaiting'])->name('admin.ad-boxes.return-to-waiting');
        });
    });
});




