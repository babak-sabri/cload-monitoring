<?php
namespace Kayer\Monitoring\APIsAbstracts;

use App\Helpers\Str;

abstract class HostAbstract extends APIAbstract
{
	public function generateHostName($userId, $hostName)
	{
		return Str::implode(API_SEPARATOR, [
			$hostName,
			Str::uuid(),
			$userId
		]);
	}
}