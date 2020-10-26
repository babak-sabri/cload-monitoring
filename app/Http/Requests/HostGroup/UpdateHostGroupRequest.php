<?php
namespace App\Http\Requests\HostGroup;

use App\Base\BaseRequest;

class UpdateHostGroupRequest extends BaseRequest
{
	public function authorize()
	{
		return request()->group->user_id == auth()->user()->id;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules	= [
			'group_name'	=> 'required|string|max:255',
			'decription'	=> 'nullable|string|max:1024',
		];
		return $rules;
	}
}
