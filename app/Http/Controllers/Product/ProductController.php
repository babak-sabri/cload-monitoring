<?php
namespace App\Http\Controllers\Product;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\DeleteProductRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Product\Product;
use App\Helpers\PaginateHelper;

class ProductController extends ControllerAbstract
{
	public function index(IndexProductRequest $request, $all=null)
	{
		try {
			$fetchParams	= $request->validated();
			if($all=='all') {
				return $this->getResponse(
					Product::fetchData($fetchParams)
					->get()
				);
			}
			return $this->getResponse(
				Product::fetchData($fetchParams)
					->paginate($fetchParams[PaginateHelper::RECORD_COUNT])
				);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function show(Product $product)
	{
		return $this->getResponse($product);
	}
	
	public function store(CreateProductRequest $request)
	{
		DB::beginTransaction();
		try {
			$product	= new Product($request->validated());
			$product->save();
			
			$this->saveAuditLog(
				$product->getKey(),
				AuditLogAbstract::PRODUCT_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$product->toArray(),
				$product->title
			);
			DB::commit();
			return $this->getResponse([
				'id'	=> $product->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function update(UpdateProductRequest $request, Product $product)
	{
		DB::beginTransaction();
		try {
			$product->update($request->validated());
			$this->saveAuditLog(
				$product->getKey(),
				AuditLogAbstract::PRODUCT_RESOURCE,
				AuditLogAbstract::UPDATE_ACTION,
				$product->toArray(),
				$product->title
			);
			DB::commit();
			return $this->getResponse([]);
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function destroy(DeleteProductRequest $request, Product $product)
	{
		DB::beginTransaction();
		try {
			$this->saveAuditLog(
				$product->getKey(),
				AuditLogAbstract::PRODUCT_RESOURCE,
				AuditLogAbstract::DELETE_ACTION,
				$product->toArray(),
				$product->title
			);
			$product->delete();
			DB::commit();
			return $this->getResponse([]);
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}