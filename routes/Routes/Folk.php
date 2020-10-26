<?php
use App\Http\Controllers\User\FolkController;
/*
|--------------------------------------------------------------------------
| Folk routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('folk')->middleware(['auth:api', 'ACL'])->group(function () {
	Route::post('/', [FolkController::class, 'store'])
		->name('folk.store')
		;
});