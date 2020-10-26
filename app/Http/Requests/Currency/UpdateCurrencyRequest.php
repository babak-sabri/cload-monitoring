<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
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
		return [
			'currency_title'		=> 'sometimes|required|string|unique:currencies,currency_title,'. request()->currency->currency_id.',currency_id',
			'currency_price'		=> 'sometimes|required|numeric|min:0'
		];
	}
}
