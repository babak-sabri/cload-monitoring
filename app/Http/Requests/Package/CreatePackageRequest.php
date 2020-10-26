<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Str;
use App\Models\Product\Product;

class CreatePackageRequest extends FormRequest
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
		$product	= Product::select('product_id','product_type','product_cat')->get();
		$countable	= [];
		$permanent	= [];
		$productId	= [];
		foreach ($product as $value){
			$productId[] = $value['product_id'];
			if($value['product_cat'] == COUNTABLE){
				$countable[] = $value['product_id'];
			}
		}
		return [
			'title'							=> 'required|string|unique:packages',
			'description'					=> 'string|nullable|max:1024',
			'price'							=> 'required|numeric|min:0',
			'status'						=> 'required|in:'.Str::implode(',', config('package.product-status')).'|min:0',
			'product_items'					=> 'array|required',
			'product_items.*.product_id'	=> 'required|distinct|integer|min:1|in:'.Str::implode(',',$productId),
			'product_items.*.count'			=> 'required_if:product_items.*.product_id,in:['.Str::implode(',',$countable).']|integer|min:0',
		];
	}
}
