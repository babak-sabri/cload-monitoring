<?php
namespace Kayer\Monitoring\APIsInterfaces;

interface GraphInterface
{
	public function get(array $params);
	
	public function create(array $params);
	
	public function update(int $hostId, array $params);
	
	public function delete(array $hostIds);
	
	public function generateHostName($userId, $hostName);
	
	public function mock(array $result = []);
}