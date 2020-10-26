<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kayer\Monitoring\MonitoringInterface;
use App\Models\Host\Host;
use App\Helpers\Arr;

class ItemExist implements Rule
{
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$monitoring	= resolve(MonitoringInterface::class);
		$item		= $monitoring->item()->get([
			'itemids'	=> $value
		]);
		if(empty($item)) {
			return false;
		}
		return Host::where('hostid', Arr::get($item, '0.hostid', 0))->count() == 1;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute is invalid or doesn`t exist.';
	}
}
