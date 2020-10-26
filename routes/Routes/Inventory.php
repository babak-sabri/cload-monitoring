<?php
use App\Http\Controllers\Inventory\InventoryController;

/*
|--------------------------------------------------------------------------
| Inventory routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('inventory')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/{all?}', [InventoryController::class, 'index'])
		->name('inventory.index')
		->where([
			'all'	=> '(all)'
		])
		;
});
