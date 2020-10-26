<?php
namespace App\Http\Requests\Host;

use App\Base\BaseRequest;
use App\Helpers\PaginateHelper;

class IndexHostRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return PaginateHelper::getRules([
			'hostid'	=> 'array',
			'hostid.*'	=> 'integer',
			'host'		=> 'string|max:255',
		],
		[
			'hostid',
			'host'
		],
		[
			'hostid',
			'host'
		]);
	}
}
