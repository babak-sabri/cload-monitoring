<?php
namespace App\Base;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest implements BaseRequestInterface
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
}