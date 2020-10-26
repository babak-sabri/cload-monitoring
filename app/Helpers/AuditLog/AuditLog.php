<?php
namespace App\Helpers\AuditLog;

class AuditLog extends AuditLogAbstract
{
	public function log(array $params = array())
	{
		
		return parent::log($this->validate($params));
	}
}