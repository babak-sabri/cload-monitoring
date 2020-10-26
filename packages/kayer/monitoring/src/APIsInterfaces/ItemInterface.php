<?php
namespace Kayer\Monitoring\APIsInterfaces;

interface ItemInterface
{
	public function get(array $params);
	
	public function create(array $params);
	
	public function update(int $hostId, array $params);
	
	public function delete(array $hostIds);
	
	public function generateItemName($userId, $itemName);
	
	public function mock(array $result = []);
}