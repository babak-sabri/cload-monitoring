<?php
use App\Http\Controllers\Item\ItemController;
/*
|--------------------------------------------------------------------------
| Graphs Routes
|--------------------------------------------------------------------------
|
|
*/

Route::prefix('item')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::get('/', [ItemController::class, 'index'])
		->name('item.index')
	;
});