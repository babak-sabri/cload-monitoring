<?php
use App\Http\Controllers\Currency\CurrencyController;
/*
|--------------------------------------------------------------------------
| Currency routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('currency')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/', [CurrencyController::class, 'index'])
		->name('currency.index')
		;
	Route::get('/{currency}', [CurrencyController::class, 'show'])
		->name('currency.show')
		;
	Route::post('/', [CurrencyController::class, 'store'])
		->name('currency.create')
		;
	Route::put('/{currency}', [CurrencyController::class, 'update'])
		->where('currency', '[0-9]+')
		->name('currency.update')
		;
	Route::delete('/{currency}', [CurrencyController::class, 'destroy'])
		->where('currency', '[0-9]+')
		->name('currency.destroy')
		;
});