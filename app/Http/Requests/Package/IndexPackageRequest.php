<?php
namespace App\Http\Requests\Package;

use App\Base\BaseRequest;
use App\Helpers\Str;
use App\Helpers\PaginateHelper;

class IndexPackageRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return PaginateHelper::getRules([
			'package_id'		=> 'array',
			'package_id.*'		=> 'integer',
			'title'				=> 'nullable|string|max:255',
			'description'		=> 'nullable|string',
			'status'			=> 'array',
			'status.*'			=> 'in:'.Str::implode(',', config('package.product-status')),
		],
		[
			'package_id',
			'title',
			'description',
			'status'
		],
		[
			'package_id',
			'title'
		]);
	}
}