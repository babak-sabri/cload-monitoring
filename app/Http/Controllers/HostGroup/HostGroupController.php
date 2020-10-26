<?php
namespace App\Http\Controllers\HostGroup;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use Kayer\Monitoring\MonitoringInterface;
use App\Http\Requests\HostGroup\CreateHostGroupRequest;
use App\Http\Requests\HostGroup\UpdateHostGroupRequest;
use App\Http\Requests\HostGroup\DeleteHostGroupRequest;
use App\Exceptions\MonitoringException;
use App\Models\HostGroup\HostGroup;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLog\AuditLogAbstract;

class HostGroupController extends ControllerAbstract
{
	private $monitoring;

	/**
	 * 
	 * @param MonitoringInterface $monitoring monitoring handler
	 */
	public function __construct(MonitoringInterface $monitoring)
    {
		$this->monitoring	= $monitoring;
    }
	
	public function index()
	{
		return $this->getResponse(HostGroup::userGroups(request()->user()->id)->get());
	}
	
	public function tree()
	{
		return $this->getResponse(HostGroup::userGroups(request()->user()->id)->get()->toTree());
	}
	
	public function show(HostGroup $group)
	{
		return $this->getResponse($group);
	}

	/**
	 * create new host group
	 * 
	 * @param CreateHostGroupRequest $request host group
	 * @return mixed
	 */
	public function store(CreateHostGroupRequest $request)
	{
		DB::beginTransaction();
		try {
			$data					= $request->validated();
			$data['api_group_name']	= $this->monitoring->hostGroup()->generateGroupName(auth()->user()->id, $data['group_name']);
			$data['user_id']		= auth()->user()->id;
			$hostGroup				= new HostGroup($data);
			$hostGroup->group_id	= $this->monitoring->hostGroup()->create(['name' => $data['api_group_name']]);
			$hostGroup->parent_id	= $data['parent_id'];
			$hostGroup->save();
			
			//Save log
			$this->saveAuditLog(
				$hostGroup->getKey(),
				AuditLogAbstract::HOST_GROUP_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$hostGroup->toArray(),
				$hostGroup->group_name
			);
			DB::commit();
			return $this->getResponse([
				'id'	=> $hostGroup->getKey()
			], Response::HTTP_CREATED);
		} catch (MonitoringException $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_SERVICE_UNAVAILABLE);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/**
	 * update a host group
	 * 
	 * @param UpdateHostGroupRequest $request
	 * @param HostGroup $group
	 * @return type
	 */
	public function update(UpdateHostGroupRequest $request, HostGroup $group)
	{
		DB::beginTransaction();
		try {
			$oldData				= $group->toArray();
			$data					= $request->validated();
			$data['api_group_name']	= $this->monitoring->hostGroup()->generateGroupName(auth()->user()->id, $data['group_name']);
			$this->monitoring->hostGroup()->update($group->group_id, ['name' => $data['api_group_name']]);
			$group->fill($data);
			$group->save();
			//audit Log
			$this->saveAuditLog(
				$group->getKey(),
				AuditLogAbstract::HOST_GROUP_RESOURCE,
				AuditLogAbstract::UPDATE_ACTION,
				AuditLogAbstract::getDifference($oldData, $group->toArray()),
				$group->group_name
			);
			
			DB::commit();
			return $this->getResponse([]);
		} catch (MonitoringException $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_SERVICE_UNAVAILABLE);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);

		}
	}
	
	public function delete(DeleteHostGroupRequest $request, HostGroup $group)
	{
		DB::beginTransaction();
		try {
			$groupIds		= [];
			$nodes			= $group->descendantsAndSelf($group->getKey());
			foreach($nodes as $value) {
				$groupIds[]	= $value->getKey();
				//save logs
				$this->saveAuditLog(
					$value->getKey(),
					AuditLogAbstract::HOST_GROUP_RESOURCE,
					AuditLogAbstract::DELETE_ACTION,
					[],
					$value->group_name
				);
			}
			$this->monitoring->hostGroup()->delete($groupIds);
			$group->delete();
			DB::commit();
			return $this->getResponse([]);
		} catch (MonitoringException $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_SERVICE_UNAVAILABLE);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
