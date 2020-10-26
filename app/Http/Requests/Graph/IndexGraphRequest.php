<?php
namespace App\Http\Requests\Graph;

use App\Base\BaseRequest;
use App\Helpers\PaginateHelper;

class IndexGraphRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return PaginateHelper::getRules([
			'groupid'	=> 'array',
			'groupid.*'	=> 'integer',
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