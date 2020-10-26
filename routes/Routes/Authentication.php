<?php
use App\Http\Controllers\Authentication\AuthController;
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('auth')->group(function () {
	Route::post('login', [AuthController::class, 'login'])
		->name('auth.login')
		;
	Route::post('logout', [AuthController::class, 'logout'])
		->name('auth.logout')
		->middleware('auth:api')
		;
});