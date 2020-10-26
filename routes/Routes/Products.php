<?php
use App\Http\Controllers\Product\ProductController;
/*
|--------------------------------------------------------------------------
| Products routes
|--------------------------------------------------------------------------
|
|
*/

Route::prefix('product')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/{all?}', [ProductController::class, 'index'])
		->name('product.index')
		->where([
			'all'	=> '(all)'
		])
		;

	Route::get('/{product}', [ProductController::class, 'show'])
		->name('product.show')
		->where('product', '[0-9]+')
		;

	Route::post('/', [ProductController::class, 'store'])
		->name('product.store')
		;

	Route::put('/{product}', [ProductController::class, 'update'])
		->name('product.update')
		->where('product', '[0-9]+')
		;

	Route::delete('/{product}', [ProductController::class, 'destroy'])
		->name('product.delete')
		->where('product', '[0-9]+')
		;
});
