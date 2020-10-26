<?php
namespace Kayer\Monitoring\APIsAbstracts;

use App\Helpers\Arr;
use App\Models\Host\Host;
use App\Scopes\Host\HostScope;
use App\Models\Graph\GraphCache;
use Kayer\Monitoring\APIsInterfaces\GraphInterface;

abstract class GraphAbstract extends APIAbstract
{
	public static function getNamePostfix($userId, $graphName)
	{
		return "-{{$userId}-".sha1($graphName).'}';
	}
	
	public function generateHostName($userId, $graphName)
	{
		return $graphName.self::getNamePostfix($userId, $graphName);
	}
	
	public function sync($userIds=null)
	{
		$graphApi	= resolve(GraphInterface::class);
		if(is_null($userIds)) {
			$userIds	= request()->user()->getKey();
		}
		$userIds	= Arr::wrap($userIds);
		$hosts		= Host::withoutGlobalScope(HostScope::class)
						->whereIn('user_id', $userIds)
						->get()
						;

		if(!empty($hosts)) {
			$hostIds	= [];
			$userHosts	= [];
			
			foreach ($hosts as $host) {
				$hostIds[]					= $host->hostid;
				$userHosts[$host->hostid]	= $host->user_id;
			}
			
			$graphs	= $graphApi->get([
				'hostids'			=> $hostIds,
				'selectTemplates'	=> ['templateid', 'name']
			]);
			
			foreach ($graphs as &$graph) {
				$graph['user_id']	= $userHosts[$graph['hostid']];
			}
			
			if(!empty($graphs)) {
				GraphCache::whereIn('user_id', $userIds)->delete();
				GraphCache::insert($graphs);
			}
		}
	}
}