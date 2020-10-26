<?php
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\ProfileController;
/*
|--------------------------------------------------------------------------
| Customer User Routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('user')->group(function () {
	Route::post('/', [UserController::class, 'store'])
		->name('user.store')
	;
	Route::post('/verify/{user}', [UserController::class, 'verify'])
		->where('user', '[0-9]+')
		->name('user.verify.cellphone')
	;
	Route::post('/resend-verify-code/{user}', [UserController::class, 'resendVerifyCode'])
		->where('user', '[0-9]+')
		->name('user.resend.verifycode')
	;
	Route::middleware(['auth:api', 'ACL'])->group(function () {
		Route::get('/', [ProfileController::class, 'show'])
			->name('profile.show')
			;
		Route::put('/', [ProfileController::class, 'update'])
			->name('profile.update')
		;
		Route::patch('/changePass', [ProfileController::class, 'changePassword'])
			->name('profile.changePassword')
		;
	});
});
