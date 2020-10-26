<?php
namespace App\Http\Requests\Product;

use App\Base\BaseRequest;
use App\Helpers\Arr;
use App\Helpers\Str;

class UpdateProductRequest extends BaseRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		//@todo check with perissions
		return (request()->user()->user_type == ADMIN_USER);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		[$productType]	= Arr::divide(config('products.products'));
		return [
			'title'			=> 'string|max:255',
			'description'	=> 'nullable|string',
			'price'			=> 'numeric|min:0',
			'product_type'	=> 'in:'.Str::implode(',', $productType),
			'product_cat'	=> 'in:'.Str::implode(',', [COUNTABLE, PERMANENT]),
			'entity_id'		=> 'nullable|string'
		];
	}
}