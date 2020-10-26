<?php
namespace App\Http\Requests\Graph;

use App\Base\BaseRequest;
use App\Rules\ItemExist;

class CreateGraphRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name'				=> 'required|string|max:255',
			'gitems'			=> 'required|array',
			'gitems.*.itemid'	=> ['required', 'distinct' ,'integer', new ItemExist()],
			'gitems.*.color'	=> 'required|regex:/^[a-fA-F0-9]{6}$/',
		];
	}
}
