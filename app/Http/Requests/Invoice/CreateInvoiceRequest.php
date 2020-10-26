<?php
namespace App\Http\Requests\Invoice;

use App\Base\BaseRequest;

class CreateInvoiceRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'amount'		=> 'required|numeric|min:0',
			'description'	=> 'nullable|string',
		];
	}
}
