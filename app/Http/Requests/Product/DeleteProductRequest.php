<?php
namespace App\Http\Requests\Product;

use App\Base\BaseRequest;

class DeleteProductRequest extends BaseRequest
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
	
	public function rules()
	{
		return [];
	}
}