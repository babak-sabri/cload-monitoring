<?php
namespace App\Http\Requests\Invoice;

use App\Base\BaseRequest;
use App\Helpers\PaginateHelper;
use App\Helpers\Str;

class IndexInvoiceRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return PaginateHelper::getRules([
			'invoice_id'	=> 'array',
			'invoice_id.*'	=> 'integer',
			'description'	=> 'string|max:255',
			'tracking_code'	=> 'string|max:255',
			'pay_type'		=> 'array',
			'pay_type.*'	=> 'in:'.Str::implode(',', config('payment.pay_types')),
		],
		[
			'tracking_code',
			'pay_type',
		],
		[
			'payed_at',
			'pay_type',
			'created_at'
		]);
	}
}