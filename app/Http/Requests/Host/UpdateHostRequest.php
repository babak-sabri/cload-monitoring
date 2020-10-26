<?php
namespace App\Http\Requests\Host;

use App\Base\BaseRequest;
use App\Helpers\HostHelper;

class UpdateHostRequest extends BaseRequest
{
	
	public function authorize()
	{
		return request()->hostObject->user_id == request()->user()->id;
	}
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return HostHelper::getRules();
	}
}
