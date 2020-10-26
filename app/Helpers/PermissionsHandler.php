<?php
namespace App\Helpers;

use Illuminate\Support\Arr as MainArr;

class PermissionsHandler extends MainArr
{
	public static function check($resource, $actionName, $userType)
	{
		$action	= config("app-menu.{$resource}.actions.{$actionName}", false);
		return $action;
	}
}