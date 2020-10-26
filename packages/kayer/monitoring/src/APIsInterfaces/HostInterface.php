<?php
namespace Kayer\Monitoring\APIsInterfaces;

interface HostInterface
{
	const DUPLICATED_TEMPLATES_ERROR	= -1;

	/**
	 * create new host group
	 * 
	 * @param array $params
	 * @return integer created host group id
	 */
	public function create(array $params);
	
	public function update(int $hostId, array $params);
	
	public function delete(array $hostIds);
	
	public function generateHostName($userId, $hostName);
	
	public function mock(array $result = []);
}