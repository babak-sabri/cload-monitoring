<?php
namespace App\Http\Controllers\User;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\User\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;


class ProfileController extends ControllerAbstract
{
	/**
	 * get user profile
	 * 
	 * @param type $id
	 * @return type
	 */
	public function show()
    {
		try {
			return $this->getResponse(Auth::user(), Response::HTTP_OK);
		} catch (ModelNotFoundException $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_NOT_FOUND);
		} catch (\Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage().' : '.$e->getLine(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
    }
	
	/**
	 * update user profile
	 * 
	 * @param UpdateProfileRequest $request
	 * @return type
	 */
	public function update(UpdateProfileRequest $request)
	{
		try {
			$data	= $request->validated();
			Auth::user()->fill($data);
			Auth::user()->save();
			return $this->getResponse([], Response::HTTP_OK);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	/**
	 * change user password
	 * 
	 * @param UpdateProfileRequest $request
	 * @return type
	 */
	public function changePassword(ChangePasswordRequest $request)
	{
		try {
			$data					= $request->validated();
			Auth::user()->password	= Hash::make(($data['new_password']));
			Auth::user()->save();
			return $this->getResponse([], Response::HTTP_OK);
		} catch (Exception $ex) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}