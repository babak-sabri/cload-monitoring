<?php
namespace Kayer\Monitoring\APIsAbstracts;

use App\Helpers\Str;

abstract class HostGroupAbstract extends APIAbstract
{
	public function generateGroupName($userId, $groupName)
	{
		return Str::implode(API_SEPARATOR, [
			$groupName,
			Str::uuid(),
			$userId
		]);
	}
}