<?php
namespace App\Http\Requests\User;

use App\Base\BaseRequest;

class CreateFolkRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		// TODO : acl validattion for parent department id
		$languages	= config('app-config.languages');
		$calendars	= config('app-config.calendars');
		
		return [
			'first_name'		=> 'string|max:255',
			'last_name'			=> 'string|max:255',
			'user_name'			=> 'string|unique:users,user_name|min:6|max:255',
			'email'				=> 'required|email|unique:users,email|max:255',
			'cellphone'			=> 'required|string|unique:users,cellphone|min:6|max:255',
			'password'			=> 'required|string|min:6|max:255|confirmed',
			'birth_date'		=> 'integer',
			'language'			=> 'in:'. implode(',', array_keys($languages)),
			'calendar_type'		=> 'in:'. implode(',', array_keys($calendars)),
		];
	}
}
