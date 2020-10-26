<?php

namespace App\Http\Controllers\Currency;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Models\Currency\Currency;
use App\Http\Requests\Currency\CreateCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Http\Requests\Currency\DeleteCurrencyRequest;

class CurrencyController extends ControllerAbstract
{
	/*
	 *  Create New Currency (Only By Admin )
	 */
    public function store(CreateCurrencyRequest $request) {
		try {
			$currency	= new Currency($request->validated());
			$currency->save();
			return $this->getResponse([
				'id'	=> $currency->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/*
	 *  Update Currency (Only By Admin )
	 */
	
	public function update(UpdateCurrencyRequest $request,Currency $currency)
	{
		try {
			$currency->update($request->validated());
			return $this->getResponse([], Response::HTTP_OK);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/*
	 *  Delete Currencies (Only By Admin )
	 */
	
	public function destroy(DeleteCurrencyRequest $request,Currency $currency)
	{
		try {
			$currency->delete($currency);
			return $this->getResponse([], Response::HTTP_OK);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/*
	 *  Show Specific Currency Accourding To Currency Id 
	 */
	
	public function show(Currency $currency)
	{
		try {
			$data	= $currency->toArray();
			return $this->getResponse($data);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
