<?php
namespace App\Http\Requests\User;

use App\Base\BaseRequest;

class VerifyUserRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'verification_code'	=> 'required|string|regex:/[0-9]+/',
		];
	}
}
