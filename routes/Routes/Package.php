<?php
use App\Http\Controllers\Package\PackageController;
/*
|--------------------------------------------------------------------------
| Package routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('package')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/', [PackageController::class, 'index'])
		->name('package.index')
		;
	Route::get('/{package}', [PackageController::class, 'show'])
		->name('package.show')
		;
	Route::post('/', [PackageController::class, 'store'])
		->name('package.create')
		;
	Route::put('/{package}', [PackageController::class, 'update'])
		->where('package', '[0-9]+')
		->name('package.update')
		;
	Route::delete('/{package}', [PackageController::class, 'destroy'])
		->where('package', '[0-9]+')
		->name('package.destroy')
		;
});