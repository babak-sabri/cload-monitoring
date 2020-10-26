<?php
namespace App\Http\Controllers\Package;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Models\Package\Package;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Package\CreatePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Http\Requests\Package\DeletePackageRequest;
use App\Http\Requests\Package\IndexPackageRequest;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\Product\Product;
use App\Helpers\PaginateHelper;
use App\Helpers\Arr;

class PackageController extends ControllerAbstract
{
	public function index(IndexPackageRequest $request)
	{
		try {
			$fetchParams	= $request->validated();
			return $this->getResponse(
					Package::fetchData($fetchParams)
					->with('productItems')
					->paginate(Arr::get($fetchParams, PaginateHelper::RECORD_COUNT, DEFAULT_RECORD_COUNT))
				);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage().' : '.$e->getFile().' : '.$e->getLine(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	/*
	 *  Create New Template (Only By Admin )
	 */
    public function store(CreatePackageRequest $request) 
	{
		DB::beginTransaction();
		try {
			$productArray	= [];
			$products	= Product::all();
			foreach($products as $product) {
				$productArray[$product->getKey()]	= $product->toArray();
			}
			
			$data		= $request->validated();
			foreach ($data['product_items'] as $key => $value)
			{
				if($productArray[$value['product_id']]['product_cat'] == PERMANENT)
				{
					$data['product_items'][$key]['count'] = 1;
				}
			}
			$package	= new Package($data);
			$package->save();
			$package->productItems()->createMany($data['product_items']);
			$package->load('productItems');
			$this->saveAuditLog(
				$package->getKey(),
				AuditLogAbstract::PACKAGE_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$package->toArray(),
				$package->title
			);
			DB::commit();
			return $this->getResponse([
				'id'	=> $package->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/*
	 *  Update Templates (Only By Admin )
	 */
	
	public function update(UpdatePackageRequest $request,Package $package)
	{
		DB::beginTransaction();
		try {
			$productArray	= [];
			$products	= Product::all();
			foreach($products as $product) {
				$productArray[$product->getKey()]	= $product->toArray();
			}
			$package->load('productItems');
			$oldData	= $package->toArray();
			$data		= $request->validated();
			$package->update($data);
			foreach ($data['product_items'] as $key => $value){
				if($productArray[$value['product_id']]['product_cat'] == PERMANENT)
				{
					$data['product_items'][$key]['count'] = 1;
				}
			}
			
			if(!empty($data['product_items'])) {
				$package->productItems()->delete();
				$package->productItems()->createMany($data['product_items']);
			}
			$package->load('productItems');

			//Save audit log
			$this->saveAuditLog(
				$package->getKey(),
				AuditLogAbstract::PACKAGE_RESOURCE,
				AuditLogAbstract::UPDATE_ACTION,
				AuditLogAbstract::getDifference($oldData, $package->toArray(), [
//					'sub'	=> [
//						'product_items'	=> [
//							'key'	=> [
//								'resource',
//								'action'
//							],
//							'check'	=> [
//								'params'
//							]
//						]
//					]
				]),
				$package->title
			);
			DB::commit();
			return $this->getResponse([]);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/*
	 *  Delete Templates (Only By Admin )
	 */
	
	public function destroy(DeletePackageRequest $request,Package $package)
	{
		//@TODO check if a user has this template it can not be deleted 
		try {
			$package->delete($package);
			return $this->getResponse([], Response::HTTP_OK);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/*
	 *  Show Specific Template Accourding To Template Id 
	 */
	
	public function show(Package $package)
	{
		try {
			$package->load('productItems');
			return $this->getResponse($package);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
