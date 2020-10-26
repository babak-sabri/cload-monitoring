<?php
namespace App\Helpers\AuditLog;
interface AuditLogInterface
{
	public function log(array $params = []);
	
	public static function getActions();
	
	public static function getResources();
	
	public static function getDifference(array $oldData, array $newData);
	
	public function validate(array $params);
}