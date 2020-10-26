<?php
namespace App\Http\Controllers\Invoice;

use Exception;
use App\Exceptions\PaymentVerificationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Models\Invoice\Invoice;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Helpers\PaymentHelper;
use App\Helpers\Payment\PaymentRequestInterface;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Requests\Invoice\DeleteInvoiceRequest;
use App\Http\Requests\Invoice\ShowInvoiceRequest;
use App\Http\Requests\Invoice\IndexInvoiceRequest;
use App\Helpers\PaginateHelper;

class InvoiceController extends ControllerAbstract
{
	public function __construct()
	{
		//Inject payment class based on pay type parameter
		app()->bind(PaymentRequestInterface::class, 'App\Helpers\Payment\Requests\\'.request()->payType.'Request');
	}
	
	public function pay(PaymentRequestInterface $request, $payType, Invoice $invoice)
	{
		DB::beginTransaction();
		try {
			PaymentHelper::pay($request, $invoice, $request->validated());
			$this->saveAuditLog(
				$invoice->getKey(),
				AuditLogAbstract::BANK_RESOURCE,
				AuditLogAbstract::UPDATE_ACTION,
				$invoice->toArray(),
				$invoice->amount
			);
			DB::commit();
			return $this->getResponse([], Response::HTTP_OK);
		} catch (PaymentVerificationException $e) {
			return $this->getResponse([], Response::HTTP_NOT_ACCEPTABLE);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function index(IndexInvoiceRequest $request)
	{
		try {
			$fetchParams	= $request->validated();
			return $this->getResponse(
				Invoice::userInvoices($request->user()->id)->fetchData($fetchParams)
					->paginate($fetchParams[PaginateHelper::RECORD_COUNT])
				);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function show(ShowInvoiceRequest $request, Invoice $invoice)
	{
		try {
			return $this->getResponse($invoice);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	/**
	 * create new host group
	 * 
	 * @param CreateInvoiceRequest $request invoice
	 * @return mixed
	 */
	public function store(CreateInvoiceRequest $request)
	{
		DB::beginTransaction();
		try {
			$invoice			= new Invoice($request->validated());
			$invoice->user_id	= $request->user()->id;
			$invoice->save();
			$this->saveAuditLog(
				$invoice->getKey(),
				AuditLogAbstract::INVOICE_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$invoice->toArray(),
				$invoice->amount
			);
			DB::commit();
			return $this->getResponse([
				'id'	=> $invoice->getKey()
			], Response::HTTP_CREATED);
		}catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function update(UpdateInvoiceRequest $request, Invoice $invoice)
	{
		DB::beginTransaction();
		try {
			if($invoice->payed_at!==null) {
				return $this->getResponse([
					'id'	=> $invoice->getKey()
				], Response::HTTP_LOCKED);
			}
			$oldData	= $request->toArray();
			$invoice->update($request->validated());
			$this->saveAuditLog(
				$invoice->getKey(),
				AuditLogAbstract::INVOICE_RESOURCE,
				AuditLogAbstract::UPDATE_ACTION,
				AuditLogAbstract::getDifference($oldData, $invoice->toArray(), [
					'sub'	=> []
				]),
				$invoice->amount
			);
			DB::commit();
			return $this->getResponse([]);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function delete(DeleteInvoiceRequest $request, Invoice $invoice)
	{
		DB::beginTransaction();
		try {
			if($invoice->payed_at!==null) {
				return $this->getResponse([
					'id'	=> $invoice->getKey()
				], Response::HTTP_LOCKED);
			}
			$invoice->delete();
			$this->saveAuditLog(
				$invoice->getKey(),
				AuditLogAbstract::INVOICE_RESOURCE,
				AuditLogAbstract::DELETE_ACTION,
				$invoice->toArray(),
				$invoice->amount
			);
			DB::commit();
			return $this->getResponse([]);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}