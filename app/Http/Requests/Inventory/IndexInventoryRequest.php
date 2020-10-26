<?php
namespace App\Http\Requests\Inventory;

use App\Base\BaseRequest;
use App\Helpers\PaginateHelper;

class IndexInventoryRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return PaginateHelper::getRules([
			'inventory_id'		=> 'array',
			'inventory_id.*'	=> 'integer',
			'product_id'		=> 'array',
			'product_id.*'		=> 'integer',
			'productType.*'		=> 'integer',
		],
		[
			'inventory_id',
			'product_id',
		],
		[
			'inventory_id',
			'product_id',
			'created_at'
		]);
	}
}
