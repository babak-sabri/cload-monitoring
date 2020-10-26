<?php
namespace App\Helpers;

use Illuminate\Support\Str as MainStr;

class Str extends MainStr
{
	public static function implode(string $glue, array $pieces)
	{
		return implode($glue, $pieces);
	}
	
	public static function isNumeric($var)
	{
		return is_numeric($var);
	}
	
	public static function randomDigits($length=16)
	{
		$string	= '';
		for($i=0; $i<$length; $i++) {
			$string	.= random_int(0, 9);
		}
		return $string;
	}
}