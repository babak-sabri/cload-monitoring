<?php
use App\Http\Controllers\Monitoring\MonitoringController;
/*
|--------------------------------------------------------------------------
| Hosts group Routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('monitoring')->middleware(['auth:api'])->group(function () {
	Route::get('/templates', [MonitoringController::class, 'templates'])
		->name('monitoring.template.list')
		;
});