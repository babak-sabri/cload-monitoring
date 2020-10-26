<?php
namespace Kayer\Monitoring\APIsAbstracts;

use Kayer\Monitoring\AdaptorInterface;

abstract class APIAbstract
{
	protected $adaptor;
	
	public function __construct(AdaptorInterface $adaptor)
	{
		$this->adaptor	= $adaptor;
	}
}