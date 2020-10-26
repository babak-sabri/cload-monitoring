<?php
namespace App\Http\Requests\Host;

use App\Base\BaseRequest;
use App\Helpers\HostHelper;

class CreateHostRequest extends BaseRequest
{
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
