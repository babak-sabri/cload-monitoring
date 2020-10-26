<?php
namespace Kayer\Monitoring\APIsAbstracts;

use App\Helpers\Str;

abstract class ItemAbstract extends APIAbstract
{
	public function generateItemName($userId, $itemName)
	{
		return Str::implode(API_SEPARATOR, [
			$itemName,
			Str::uuid(),
			$userId
		]);
	}
	
}