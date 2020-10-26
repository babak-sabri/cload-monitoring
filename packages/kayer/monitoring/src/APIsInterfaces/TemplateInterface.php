<?php
namespace Kayer\Monitoring\APIsInterfaces;

interface TemplateInterface
{
	/**
	 * get templates list
	 * 
	 * @param array $params
	 * @return integer created host group id
	 */
	public function get(array $params=[]);
}