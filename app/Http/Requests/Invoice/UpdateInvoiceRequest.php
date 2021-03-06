<?php
namespace App\Http\Requests\Invoice;

use App\Base\BaseRequest;

class UpdateInvoiceRequest extends BaseRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return request()->invoice->user_id == request()->user()->id;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'amount'		=> 'numeric|min:0',
			'description'	=> 'nullable|string',
		];
	}
}
