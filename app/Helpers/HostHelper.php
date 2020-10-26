<?php
namespace App\Helpers;

use App\Helpers\Str;
use App\Models\HostGroup\HostGroup as HostGroupModel;
use Kayer\Monitoring\MonitoringInterface;
use App\Models\Host\HostInterface;
use App\Models\Host\HostGroup;
use App\Models\Host\HostMacro;
use App\Models\Host\HostTemplate;
use App\Models\Product\Product;
use App\Models\Product\UserInventory;

class HostHelper
{
	const SNMP_COMMUNITY				= '{$SNMP_COMMUNITY}';
	const AUTHENTICATION_PASSWORD		= '{$AUTHENTICATION_PASSWORD}';
	const AUTHENTICATION_PROTOCOL		= '{$AUTHENTICATION_PROTOCOL}';
	const PRIVACY_PASSWORD				= '{$PRIVACY_PASSWORD}';
	const PRIVACY_PROTOCOL				= '{$PRIVACY_PROTOCOL}';
	const SECURITY_LEVEL				= '{$SECURITY_LEVEL}';
	const SECURITY_NAME					= '{$SECURITY_NAME}';
	
	const AGENT_INTERFAC				= 1;
	const SNMP_INTERFACE				= 2;
	const JMX_INTERFACE					= 3;
	const IPMI_INTERFACE				= 4;

	public static function getMacros()
	{
		return [
			self::AUTHENTICATION_PASSWORD,
			self::AUTHENTICATION_PROTOCOL,
			self::PRIVACY_PASSWORD,
			self::PRIVACY_PROTOCOL,
			self::SECURITY_LEVEL,
			self::SECURITY_NAME
		];
	}
	
	public static function getInterfaces()
	{
		return [
			self::AGENT_INTERFAC,
			self::SNMP_INTERFACE,
//			self::JMX_INTERFACE,
//			self::IPMI_INTERFACE
		];
	}
	
	public static function getRules()
	{
		$groupIds	= HostGroupModel::select('group_id')
			->where('user_id', request()->user()->id)
			->get()
			->implode('group_id', ',')
			;
		$templateIds	= Product::select(['entity_id'])
							->whereIn('product_id', function($query) {
								$userInventory = new UserInventory();
								$query->select(['product_id'])
									->from($userInventory->getTable())
									->where('user_id', request()->user()->id)
									->where('product_type', TEMPLATE);
							})
							->get()
							->implode('entity_id', ',')
							;

		$rules	= [
			'host'									=> "string|max:255",
			'interfaces'							=> 'required|array',
			'interfaces.*.type'						=> 'in:'.Str::implode(',', self::getInterfaces()),
			'interfaces.*.main'						=> 'in:0,1',
			'interfaces.*.useip'					=> 'in:0,1',
			'interfaces.*.ip'						=> 'nullable|ip|required_if:interfaces.*.useip,1|max:20',
			'interfaces.*.dns'						=> 'required_if:interfaces.*.useip,0',
			'interfaces.*.port'						=> 'integer',
			'interfaces.*.details'					=> 'array',
			'interfaces.*.details.version'			=> 'in:1,2,3',
			'interfaces.*.details.bulk'				=> 'in:0,1',
			'interfaces.*.details.community'		=> 'nullable|string',
			'interfaces.*.details.securityname'		=> 'nullable|string',
			'interfaces.*.details.contextname'		=> 'nullable|string',
			'interfaces.*.details.securitylevel'	=> 'in:0,1,2',
			'interfaces.*.details.authprotocol'		=> 'in:0,1',
			'interfaces.*.details.authpassphrase'	=> 'nullable|string',
			'interfaces.*.details.privprotocol'		=> 'in:0,1',
			'interfaces.*.details.privpassphrase'	=> 'nullable|string',
			'groups'								=> "required|array",
			'groups.*'								=> 'integer|in:'.$groupIds,
			'templates'								=> 'array',
			'templates.*'							=> 'integer|in:'.$templateIds,
			'macros'								=> 'array',
			'macros.*.macro'						=> 'max:100|in:'.Str::implode(',', self::getMacros()),
			'macros.*.value'						=> 'required',
		];
		return $rules;
	}
	
	public static function getHostGroupsRows(array $hostGroups)
	{
		$rows	= [];
		foreach ($hostGroups as $hostGroupId) {
			$rows[]	= [
				'group_id'	=> $hostGroupId
			];
		}
		return $rows;
	}
	
	public static function getHostMacrosRows(array $macros)
	{
		$rows	= [];
		foreach ($macros as $macro) {
			$rows[]	= [
				'macro'			=> $macro['macro'],
				'macro_value'	=> $macro['value'],
			];
		}
		return $rows;
	}
	
	public static function getHostTemplatesRows(array $templates)
	{
		$rows	= [];
		foreach ($templates as $templateId) {
			$rows[]	= [
				'template_id'	=> $templateId
			];
		}
		return $rows;
	}
}