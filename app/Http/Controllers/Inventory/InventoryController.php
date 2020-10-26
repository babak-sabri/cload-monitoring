<?php
namespace App\Http\Controllers\Inventory;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Http\Requests\Inventory\IndexInventoryRequest;
use App\Helpers\PaginateHelper;
use App\Models\Product\UserInventory;

class InventoryController extends ControllerAbstract
{
	public function index(IndexInventoryRequest $request, $all=null)
	{
		try {
			$fetchParams	= $request->validated();
			if($all=='all') {
				return $this->getResponse(
					UserInventory::userInventories($request->user()->id)
						->fetchData($fetchParams)
						->get()
					);
			}
			return $this->getResponse(
				UserInventory::userInventories($request->user()->id)
					->fetchData($fetchParams)
					->paginate($fetchParams[PaginateHelper::RECORD_COUNT])
				);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}