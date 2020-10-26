<?php
namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\ControllerAbstract;
use Kayer\Monitoring\MonitoringInterface;
use App\Helpers\Arr;

class MonitoringController extends ControllerAbstract
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
	
	public function templates()
	{
		return $this->getResponse(Arr::arrayValues($this->monitoring->template()->get()));
	}
}