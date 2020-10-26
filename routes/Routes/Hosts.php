<?php
use App\Http\Controllers\Host\HostController;
/*
|--------------------------------------------------------------------------
| Hosts Routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('host')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/', [HostController::class, 'index'])
		->name('host.index')
	;

	Route::post('/', [HostController::class, 'store'])
		->name('host.store')
	;

	Route::put('/{hostObject}', [HostController::class, 'update'])
		->where('hostObject', '[0-9]+')
		->name('host.update')
		;

	Route::delete('/{hostObject}', [HostController::class, 'delete'])
		->where('hostObject', '[0-9]+')
		->name('host.delete')
		;
});