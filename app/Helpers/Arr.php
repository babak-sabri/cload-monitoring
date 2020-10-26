<?php
namespace App\Helpers;

use Illuminate\Support\Arr as MainArr;

class Arr extends MainArr
{
	public static function arrayValues(array $array)
	{
		return array_values($array);
	}
	
	public static function existsSubArray($checkArray, $dataRows)
	{
		foreach($dataRows as $row) {
			$tempArray	= $checkArray;
			foreach ($checkArray as $key=>$value) {
				if(isset($row[$key]) && $row[$key]==$value) {
					unset($tempArray[$key]);
				}
			}
			if(empty($tempArray)) {
				return $row;
			}
		}
		return false;
	}

	public static function isArray($var)
	{
		return is_array($var);
	}
	
	public static function arrayDiff(array $array1, array $array2)
	{
		return array_diff($array1, $array2);
	}
}