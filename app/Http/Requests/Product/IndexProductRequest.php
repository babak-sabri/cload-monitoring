<?php
namespace App\Http\Requests\Product;

use App\Base\BaseRequest;
use App\Helpers\Arr;
use App\Helpers\Str;
use App\Helpers\PaginateHelper;

class IndexProductRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		[$productType]	= Arr::divide(config('products.products'));
		return PaginateHelper::getRules([
			'product_id'		=> 'array',
			'product_id.*'		=> 'integer',
			'title'				=> 'nullable|string|max:255',
			'description'		=> 'nullable|string',
			'product_type'		=> 'array',
			'product_type.*'	=> 'in:'.Str::implode(',', $productType),
			'product_cat'		=> 'array',
			'product_cat.*'		=> 'in:'.Str::implode(',', config('products.product_cats')),
			'entity_id'			=> 'array'
		],
		[
			'product_id',
			'title',
			'description',
			'product_type',
			'product_cat'
		],
		[
			'product_id',
			'title'
		]);
	}
}