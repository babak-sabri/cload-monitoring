<?php
namespace App\Http\Requests\Shopping;

use App\Base\BaseRequest;
use App\Helpers\PaginateHelper;
use App\Helpers\Str;

class ShoppingIndexRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return PaginateHelper::getRules([
			'payment_log_id'	=> 'array',
			'payment_log_id.*'	=> 'integer',
			'entity_id'			=> 'array',
			'entity_id.*'		=> 'integer',
			'pay_for'			=> 'array',
			'pay_for.*'			=> 'integer|in:'.Str::implode(',', config('payment.pay-for')),
		],
		[
			'payment_log_id',
			'entity_id',
			'pay_for',
			'created_at',
		],
		[
			'payment_log_id',
			'entity_id',
			'pay_for',
			'created_at',
		]);
	}
}
