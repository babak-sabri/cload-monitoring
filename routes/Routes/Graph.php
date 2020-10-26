<?php
use App\Http\Controllers\Graph\GraphController;
/*
|--------------------------------------------------------------------------
| Graphs Routes
|--------------------------------------------------------------------------
|
|
*/

Route::prefix('graph')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::post('/sync', [GraphController::class, 'sync'])
		->name('graph.sync')
	;
	Route::get('/', [GraphController::class, 'index'])
		->name('graph.index')
	;
	Route::post('/', [GraphController::class, 'store'])
		->name('graph.store')
	;
});