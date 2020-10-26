<?php
namespace Kayer\Monitoring\APIsInterfaces;

interface HostGroupInterface
{
	/**
	 * create new host group
	 * 
	 * @param array $params
	 * @return integer created host group id
	 */
	public function create(array $params);
	
	public function update(int $hostGroupId, array $params);
	
	public function delete(array $hostGroupIds);
	
	public function generateGroupName($userId, $groupName);
	
	public function mock(array $params=[]);
}