<?php
namespace App\Helpers\AuditLog;

use App\Helpers\Arr;
use App\Helpers\Str;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuditLog\Repository\AdapterRepositoryInterface;

abstract class AuditLogAbstract implements AuditLogInterface
{
	const INSERT_ACTION	= 1;
	const UPDATE_ACTION	= 2;
	const DELETE_ACTION	= 3;

	const USER_RESOURCE			= 1;
	const HOST_GROUP_RESOURCE	= 2;
	const HOST_RESOURCE			= 3;
	const PACKAGE_RESOURCE		= 5;
	const INVOICE_RESOURCE		= 6;
	const BANK_RESOURCE			= 7;
	const PRODUCT_RESOURCE		= 8;
	const SHOPPIN_CART_RESOURCE		= 9;
	
	const MYSQL_LOGGER	= 'mysql';
	
	protected $repository;
	
	public function __construct(AdapterRepositoryInterface $repository)
	{
		$this->repository	= $repository;
	}
	
	public static function getActions()
	{
		return [
			self::INSERT_ACTION,
			self::UPDATE_ACTION,
			self::DELETE_ACTION,
		];
	}
	
	public static function getResources()
	{
		return [
			self::USER_RESOURCE,
			self::HOST_GROUP_RESOURCE,
			self::HOST_RESOURCE,
			self::PACKAGE_RESOURCE,
			self::INVOICE_RESOURCE,
			self::BANK_RESOURCE,
			self::PRODUCT_RESOURCE,
			self::SHOPPIN_CART_RESOURCE,
		];
	}
	
	public static function getDifference(array $oldData, array $newData, array $options=[])
	{
		$result	= [
			'changed'	=> [],
			'added'		=> [],
			'removed'	=> [],
		];
		foreach ($oldData as $oKey=>$oValue) {
			$viewKey	= false;
			if(isset($options['sub'][$oKey])) {
				continue;
			}
			foreach ($newData as $nKey=>$nValue) {
				if($oKey==$nKey) {
					$viewKey	= true;
					break;
				}
			}
			if(!$viewKey) {
				$result['removed'][]	= [
					'key'	=> $oKey
				];
			} else if($oldData[$oKey]!=$newData[$oKey]) {
				$result['changed'][]	= [
					'key'	=> $oKey,
					'old'	=> $oldData[$oKey],
					'new'	=> $newData[$oKey]
				];
			}
		}
		
		foreach ($newData as $nKey=>$nValue) {
			$viewKey	= false;
			if(isset($options['sub'][$nKey])) {
				continue;
			}
			foreach ($oldData as $oKey=>$oValue) {
				if($oKey==$nKey) {
					$viewKey	= true;
					break;
				}
			}
			if(!$viewKey) {
				$result['added'][]	= [
					'key'	=> $nKey,
					'value'	=> $nValue
				];
			}
		}
		
		if(isset($options['sub'])) {
			foreach ($options['sub'] as $field=>$row) {
				foreach ($oldData[$field] as $oRow) {
					$checkArray	= [];
					foreach($row['key'] as $key) {
						$checkArray[$key]	= $oRow[$key];
					}
					
					$subsetResult	= Arr::existsSubArray($checkArray, $newData[$field]);
					if($subsetResult) {
						foreach($row['check'] as $ch) {
							if($oRow[$ch]!=$subsetResult[$ch]) {
								$result['changed'][]	= [
									'key'	=> $field.'.'.$ch,
									'old'	=> $oRow[$ch],
									'new'	=> $subsetResult[$ch]
								];
							}
						}
					}
					else {
						$result['removed'][]	= [
							'key'	=> $field,
							'data'	=> $checkArray
						];
					}
				}
			}
			foreach ($options['sub'] as $field=>$row) {
				foreach ($newData[$field] as $nRow) {
					$checkArray	= [];
					foreach($row['key'] as $key) {
						$checkArray[$key]	= $nRow[$key];
					}
					if(!Arr::existsSubArray($checkArray, $oldData[$field])) {
						foreach($row['check'] as $ch) {
							$checkArray[$ch]	= $nRow[$ch];
						}
						$result['added'][]	= [
							'key'	=> $field,
							'data'	=> $checkArray
						];
					}
				}
			}
		}
		return $result;
	}
	
	public function validate(array $params)
	{
		return Validator::make($params, [
			'entity_id'		=> 'required',
			'user_id'		=> 'integer|min:0',
			'resource'		=> 'required|integer|in:'.Str::implode(',', self::getResources()),
			'action'		=> 'required|integer|in:'.Str::implode(',', self::getActions()),
			'details'		=> 'array',
			'description'	=> '',
			'action_time'	=> 'integer|min:1',
		])
		->validate()
		;
	}
	
	//@TODO check exist in plan or not
	private function enable($userId)
	{
		return true;
	}
	
	public function log(array $params=[])
	{
		$userId	= Arr::get($params, 'user_id', request()->user()->id);
		if($this->enable($userId))
		{
			$this->repository->saveLog($params);
		}
	}
}