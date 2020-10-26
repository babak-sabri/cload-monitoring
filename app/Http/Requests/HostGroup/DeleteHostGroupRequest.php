<?php
namespace App\Http\Requests\HostGroup;

use App\Base\BaseRequest;

class DeleteHostGroupRequest extends BaseRequest
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
		return [];
	}
}
