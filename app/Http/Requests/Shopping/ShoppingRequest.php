<?php
namespace App\Http\Requests\Shopping;

use App\Base\BaseRequest;
use App\Models\Product\Product;
use App\Models\Product\UserInventory;

class ShoppingRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$existPermanentProducts	= UserInventory::
			where('user_id', request()->user()->id)
			->whereIn('product_id',  function($query) {
				$query
					->select('product_id')
					->from('products')
					->where('product_cat', PERMANENT)
					;
			})
			->get()
			->implode('product_id', ',')
			;
		$rules	= [
			'products'					=> 'required|array',
			'products.*.product_id'		=> 'required|integer|in:'.Product::all()->implode('product_id', ','),
			'products.*.product_count'	=> 'required|integer|min:1'
		];
		if(!empty($existPermanentProducts)) {
			$rules['products.*.product_id']	.= '|not_in:'.$existPermanentProducts;
		}

		return $rules;
	}
}
