<?php
namespace App\Http\Requests\HostGroup;

use App\Base\BaseRequest;
use App\Models\HostGroup\HostGroup;

class CreateHostGroupRequest extends BaseRequest
{
	public function authorize()
	{
		if(request()->parent_id>0) {
			$hostGroup	= HostGroup::where('group_id', (int)request()->parent_id)->first();
			return $hostGroup->user_id == auth()->user()->id;
		}
		
		return true;
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
			'parent_id'		=> 'required|integer',
		];
		return $rules;
	}
}
