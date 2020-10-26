<?php
namespace App\Http\Controllers\User;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\VerifyUserRequest;
use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;
use Kayer\Verification\Verifier\VerifierInterface;
use App\Helpers\Str;
use Illuminate\Support\Facades\DB;
use Kayer\Verification\Exceptions\ExpiredCodeException;
use Kayer\Verification\Exceptions\NotFoundException;

class UserController extends ControllerAbstract
{
	/**
	 * Store new user
	 * @param CreateDepartmentRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CreateUserRequest $request, VerifierInterface $verifier)
	{
		try {
			$data				= $request->validated();
			$data['user_type']	= CUSTOMER_USER;
			$user				= new User($data);
			$user->password		= Hash::make($data['password']);
			$user->save();
			//Send verification code
			$verifier->sendCode(Str::randomDigits(6), $user);
			return $this->getResponse([
				'id'	=> $user->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function verify(VerifyUserRequest $request, User $user, VerifierInterface $verifier)
	{
		DB::beginTransaction();
		try {
			$data	= $request->validated();
			$verifier->verify($data['verification_code'], $user);
			DB::commit();
			return $this->getResponse([]);
		} catch (NotFoundException $ex) {
			DB::rollBack();
			return $this->getResponse([], Response::HTTP_FAILED_DEPENDENCY);
		} catch (ExpiredCodeException $ex) {
			DB::rollBack();
			return $this->getResponse([], Response::HTTP_NOT_ACCEPTABLE);
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function resendVerifyCode(User $user, VerifierInterface $verifier)
	{
		try {
			$verifier->resendCode(Str::randomDigits(6), $user);
			return $this->getResponse([]);
		} catch (ExpiredCodeException $ex) {
			return $this->getResponse([
				'remainTime'	=> $ex->getCode()
			], Response::HTTP_NOT_ACCEPTABLE);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/**
	 * Store new user
	 * @param CreateDepartmentRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function storeFolk(CreateUserRequest $request)
	{
		try {
			$data				= $request->validated();
			$data['user_type']	= ADMIN_USER;
			$user				= new User($data);
			$user->password		= Hash::make($data['password']);
			$user->save();
			return $this->getResponse([
				'id'	=> $user->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}