<?php
use App\Http\Controllers\Shopping\ShoppingController;
/*
|--------------------------------------------------------------------------
| Shopping routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('shopping')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/', [ShoppingController::class, 'index'])
		->name('shopping.index')
		;
	Route::post('/', [ShoppingController::class, 'store'])
		->name('shopping.store')
		;
	Route::post('/{package}', [ShoppingController::class, 'buypackage'])
		->where('package', '[0-9]+')
		->name('shopping.buypackage')
		;
});