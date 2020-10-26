<?php
namespace App\Http\Controllers\Item;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use Kayer\Monitoring\MonitoringInterface;
use App\Exceptions\MonitoringException;
use App\Helpers\HostHelper;
use App\Models\Host\Host;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Helpers\PaginateHelper;
use App\Http\Requests\Item\IndexItemRequest;

class ItemController extends ControllerAbstract
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

    public function index(IndexItemRequest $request)
	{
		try {
			return $this->getResponse(
				$this->monitoring->item()->get($request->validated())
			);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}