<?php
namespace App\Helpers\AuditLog\Repository;
use App\Models\AuditLog\AuditLog;
use App\Helpers\Arr;
use Illuminate\Support\Carbon;

class MysqlRepositoryAdapter extends AdapterRepositoryAbstract
{
	public function saveLog(array $params = [])
	{
		$auditLog	= new AuditLog([
			'user_id'		=> Arr::get($params, 'user_id', request()->user()->id),
			'entity_id'		=> Arr::get($params, 'entity_id'),
			'resource'		=> Arr::get($params, 'resource'),
			'action'		=> Arr::get($params, 'action'),
			'details'		=> json_encode(Arr::get($params, 'details', [])),
			'description'	=> Arr::get($params, 'description', null),
			'action_time'	=> Arr::get($params, 'action_time', Carbon::now()->timestamp),
		]);
		$auditLog->save();
		return $auditLog->getKey();
	}
}