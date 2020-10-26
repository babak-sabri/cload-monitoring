<?php
namespace App\Http\Requests\User;

use App\Base\BaseRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'old_password'	=> ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
				if (!Hash::check($value, Auth::user()->password)) {
					$fail('Old Password didn\'t match');
				}
			}],
			'new_password'	=> 'required|string|min:6|max:255|confirmed|different:old_password',
		];
	}
}
