<?php
use App\Http\Controllers\HostGroup\HostGroupController;
/*
|--------------------------------------------------------------------------
| Hosts group Routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('host-groups')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/', [HostGroupController::class, 'index'])
		->name('hostgroup.index')
		;

	Route::get('/tree', [HostGroupController::class, 'tree'])
		->name('hostgroup.tree')
		;

	Route::get('/{group}', [HostGroupController::class, 'show'])
		->where('group', '[0-9]+')
		->name('hostgroup.show')
		;

	Route::post('/', [HostGroupController::class, 'store'])
		->name('hostgroup.store')
		;

	Route::put('/{group}', [HostGroupController::class, 'update'])
		->where('group', '[0-9]+')
		->name('hostgroup.update')
		;

	Route::delete('/{group}', [HostGroupController::class, 'delete'])
		->where('group', '[0-9]+')
		->name('hostgroup.delete')
		;
});