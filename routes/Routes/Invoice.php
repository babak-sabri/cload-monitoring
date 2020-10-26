<?php
use App\Helpers\Str;
use App\Http\Controllers\Invoice\InvoiceController;

/*
|--------------------------------------------------------------------------
| Invoice routes
|--------------------------------------------------------------------------
|
|
*/
Route::prefix('invoice')->middleware(['auth:api', 'ACL'])->group(function () {
		Route::post('/pay/{payType}/{invoice}', [InvoiceController::class, 'pay'])
			->name('invoice.pay')
			->where([
				'invoice'	=> '[0-9]+',
				'payType'	=> '('. Str::implode('|', config('payment.pay_types')).')'
			])
			;
		Route::get('/', [InvoiceController::class, 'index'])
			->name('invoice.index')
			;
		Route::get('/{invoice}', [InvoiceController::class, 'show'])
			->where('invoice', '[0-9]+')
			->name('invoice.show')
			;
		Route::post('/', [InvoiceController::class, 'store'])
			->name('invoice.store')
			;
		Route::put('/{invoice}', [InvoiceController::class, 'update'])
			->where('invoice', '[0-9]+')
			->name('invoice.update')
			;
		Route::delete('/{invoice}', [InvoiceController::class, 'delete'])
			->where('invoice', '[0-9]+')
			->name('invoice.delete')
			;
});
