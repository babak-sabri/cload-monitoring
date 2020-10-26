<?php
namespace App\Http\Requests\Authentication;

use App\Base\BaseRequest;

class LoginRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'login_type'	=> 'required|string|in:email,cellphone,user_name|max:255',
			'email'			=> 'string|email|required_if:login_type,email|max:255',
			'cellphone'		=> 'string|required_if:login_type,cellphone|max:255',
			'user_name'		=> 'string|required_if:login_type,user_name|max:255',
            'password'		=> 'required|string|max:255',
            'remember_me'	=> 'boolean|in:0,1'
		];
	}
}
