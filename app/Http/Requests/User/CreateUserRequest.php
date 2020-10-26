<?php
namespace App\Http\Requests\User;

use App\Base\BaseRequest;
use App\Helpers\Str;


class CreateUserRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'first_name'	=> 'string|max:255',
			'last_name'		=> 'string|max:255',
			'email'			=> 'required|email|unique:users,email|max:255',
			'cellphone'		=> 'required|string|unique:users,cellphone|min:6|max:255',
			'password'		=> 'required|string|min:6|max:255|confirmed',
			'job_title'		=> 'nullable|string|max:255',
			'organization'	=> 'nullable|string|max:255',
			'gender'		=> 'nullable|in:'. Str::implode(',', config('app-config.user-genders')),
			'language'		=> 'in:'. Str::implode(',', config('app-config.languages')),
			'calendar_type'	=> 'in:'. Str::implode(',', config('app-config.calendars')),
			'timezone'		=> 'timezone',
			'how_to_find'	=> 'nullable|string|max:2048',
		];
	}
}
