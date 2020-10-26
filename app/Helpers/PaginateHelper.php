<?php
namespace App\Helpers;

use App\Helpers\Arr;
use App\Helpers\Str;

class PaginateHelper
{
	const NUMERIC_TYPE		= 1;
	const STRING_TYPE		= 2;
	const CUSTOM_TYPE		= 3;
	const CLOSURE_TYPE		= 4;
	const BETWEEN_TYPE		= 5;
	const LIKE_STRING_TYPE	= 6;
	const SCALAR_TYPE		= 7;

	const PAGE_ID			= 'page';
	const RECORD_COUNT		= 'record_count';
	const SELECT_FIELDS		= 'select_fields';
	const SORT				= 'sort';
	const ASC_SORT			= 'asc';
	const DESC_SORT			= 'desc';
	const GROUP_BY			= 'group_by';
	
	public static function getRules($defaultRules, array $selectFields=[], array $sortFields=[])
	{
		$appendedRules	= [
			self::RECORD_COUNT	=> 'integer|min:1',
			self::PAGE_ID		=> 'integer|min:1',
		];
		
		if(!empty($selectFields)) {
			$appendedRules[self::SELECT_FIELDS]			= 'array';
			$appendedRules[self::SELECT_FIELDS.'.*']	= 'in:'.Str::implode(',', $selectFields);
		}
		if(!empty($sortFields)) {
			$appendedRules[self::SORT]				= 'array';
			$appendedRules[self::SORT.'.*.field']	= 'in:'.Str::implode(',', $sortFields);
			$appendedRules[self::SORT.'.*.type']	= 'in:'.Str::implode(',', [self::ASC_SORT, self::DESC_SORT]);
		}
		
		foreach ($appendedRules as $key=>$value) {
			if(!Arr::exists($defaultRules, $key)) {
				$defaultRules[$key]	= $value;
			}
		}
		return $defaultRules;
	}
}
