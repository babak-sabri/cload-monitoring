<?php
namespace App\Helpers;

use App\Models\Currency\Currency;

class CurrencyHelper
{
	public static function convertCurrencyById($currencyId, $count)
	{
		$money = Currency::where('currency_id',$currencyId)->first();
		return self::convertor($money->currency_price, $count);
	}
	
	public static function convertor($curencyPrice, $count)
	{
		return $curencyPrice*$count;
	}
}

