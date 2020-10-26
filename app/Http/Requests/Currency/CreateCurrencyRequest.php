<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CreateCurrencyRequest extends FormRequest
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
			'currency_title'		=> 'required|string|unique:currencies',
			'currency_price'		=> 'required|numeric|min:0'
		];
	}
}
