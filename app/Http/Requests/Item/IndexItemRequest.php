<?php
namespace App\Http\Requests\Item;

use App\Base\BaseRequest;
use App\Models\Host\Host;

class IndexItemRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'hostids'	=> 'required|array',
			'hostids.*'	=> 'integer|in:'.Host::select('hostid')->get()->implode('hostid', ','),
			'name'		=> 'string',
			'key_'		=> 'string',
			'limit'		=> 'required|integer|max:100',
		];
	}
}