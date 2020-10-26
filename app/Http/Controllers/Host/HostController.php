<?php
namespace App\Http\Controllers\Host;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use Kayer\Monitoring\MonitoringInterface;
use App\Http\Requests\Host\CreateHostRequest;
use App\Http\Requests\Host\UpdateHostRequest;
use App\Http\Requests\Host\DeleteHostRequest;
use App\Http\Requests\Host\IndexHostRequest;
use App\Exceptions\MonitoringException;
use App\Helpers\HostHelper;
use App\Models\Host\Host;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Helpers\PaginateHelper;
use Kayer\Monitoring\APIsInterfaces\HostInterface;

class HostController extends ControllerAbstract
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

    public function index(IndexHostRequest $request)
	{
		try {
			$fetchParams	= $request->validated();
			return $this->getResponse(
				Host::fetchData($fetchParams)
					->paginate($fetchParams[PaginateHelper::RECORD_COUNT])
				);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * create new host group
	 * 
	 * @param CreateHostRequest $request host group
	 * @return mixed
	 */
	public function store(CreateHostRequest $request)
	{
		DB::beginTransaction();
		try {
			$data					= $request->validated();
			$data['api_host_name']	= $this->monitoring->host()->generateHostName(auth()->user()->id, $data['host']);
			$data['hostid']			= $this->monitoring->host()->create($data);
			if($data['hostid']==HostInterface::DUPLICATED_TEMPLATES_ERROR) {
				return $this->getResponse([], Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL);
			}
			$host			= new Host($data);
			$host->hostid	= $data['hostid'];
			$host->user_id	= auth()->user()->id;
			$host->save();

			//Save Interfaces
			if(!empty($data['interfaces'])) {
				$host->hostInterfaces()->createMany($data['interfaces']);
			}
			
			//Save host groups
			if(!empty($data['groups'])) {
				$host->hostGroups()->createMany(HostHelper::getHostGroupsRows($data['groups']));
			}
			
			//Save host macros
			if(!empty($data['macros'])) {
				$host->hostMacros()->createMany(HostHelper::getHostMacrosRows($data['macros']));
			}
			
			//Save host templates
			if(!empty($data['templates'])) {
				$host->hostTemplates()->createMany(HostHelper::getHostTemplatesRows($data['templates']));
			}
			
			//Save Audit log
			$host->load([
				'hostInterfaces',
				'hostGroups',
				'hostMacros',
				'hostTemplates'
			]);
			$this->saveAuditLog(
				$host->getKey(),
				AuditLogAbstract::HOST_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$host->toArray(),
				$host->host
			);
			
			DB::commit();
			return $this->getResponse([
				'id'	=> $host->getKey()
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
	
	public function update(UpdateHostRequest $request, Host $hostObject)
	{
		DB::beginTransaction();
		try {
			$hostObject->load([
				'hostInterfaces',
				'hostGroups',
				'hostMacros',
				'hostTemplates'
			]);
			$oldData				= $hostObject->toArray();
			$data					= $request->validated();
			$data['api_host_name']	= $this->monitoring->host()->generateHostName(auth()->user()->id, $data['host']);
			if($this->monitoring->host()->update($hostObject->hostid, $data)== HostInterface::DUPLICATED_TEMPLATES_ERROR) {
				return $this->getResponse([], Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL);
			}
			$hostObject->fill($data);
			$hostObject->save();
			//Save Interfaces
			if(!empty($data['interfaces'])) {
				$hostObject->hostInterfaces()->delete();
				$hostObject->hostInterfaces()->createMany($data['interfaces']);
			}
			
			//Save host groups
			if(!empty($data['groups'])) {
				$hostObject->hostGroups()->delete();
				$hostObject->hostGroups()->createMany(HostHelper::getHostGroupsRows($data['groups']));
			}
			
			//Save host macros
			if(!empty($data['macros'])) {
				$hostObject->hostMacros()->delete();
				$hostObject->hostMacros()->createMany(HostHelper::getHostMacrosRows($data['macros']));
			}
			
			//Save host templates
			if(!empty($data['templates'])) {
				$hostObject->hostTemplates()->delete();
				$hostObject->hostTemplates()->createMany(HostHelper::getHostTemplatesRows($data['templates']));
			}
			
			//Save Audit log
			$hostObject->load([
				'hostInterfaces',
				'hostGroups',
				'hostMacros',
				'hostTemplates'
			]);

			$this->saveAuditLog(
				$hostObject->getKey(),
				AuditLogAbstract::HOST_RESOURCE,
				AuditLogAbstract::UPDATE_ACTION,
				AuditLogAbstract::getDifference($oldData, $hostObject->toArray(), [
					'sub'	=> [
						'host_interfaces'	=> [
							'key'	=> ['hostid'],
							'check'	=> ['type', 'main', 'useip', 'ip', 'dns', 'port']
						],
						'host_groups'	=> [
							'key'	=> ['hostid'],
							'check'	=> ['group_id']
						],
						'host_macros'	=> [
							'key'	=> ['hostid'],
							'check'	=> ['macro', 'macro_value']
						],
						'host_templates'	=> [
							'key'	=> ['hostid'],
							'check'	=> ['template_id']
						],
					]
				]),
				$hostObject->host
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
	
	public function delete(DeleteHostRequest $request, Host $hostObject)
	{
		DB::beginTransaction();
		try {
			$this->monitoring->host()->delete($hostObject->hostid);
			//Save Audit log
			$hostObject->load([
				'hostInterfaces',
				'hostGroups',
				'hostMacros',
				'hostTemplates'
			]);
			$this->saveAuditLog(
				$hostObject->getKey(),
				AuditLogAbstract::HOST_RESOURCE,
				AuditLogAbstract::DELETE_ACTION,
				$hostObject->toArray(),
				$hostObject->host
			);
			
			$hostObject->delete();
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