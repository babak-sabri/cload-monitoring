<?php
namespace App\Helpers\AuditLog\Repository;

interface AdapterRepositoryInterface
{
	public function saveLog(array $params=[]);
}