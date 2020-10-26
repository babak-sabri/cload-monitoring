<?php
namespace App\Http\Controllers\User;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use App\Http\Requests\User\CreateFolkRequest;
use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;
use Kayer\Verification\Verifier\VerifierInterface;
use App\Helpers\Str;
use Illuminate\Support\Facades\DB;

class FolkController extends ControllerAbstract
{
	/**
	 * Store new user
	 * @param CreateDepartmentRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CreateFolkRequest $request, VerifierInterface $verifier)
	{
		DB::beginTransaction();
		try {
			$data				= $request->validated();
			$data['user_type']	= CUSTOMER_USER;
			$user				= new User($data);
			$user->password		= Hash::make($data['password']);
			$user->save();
			//Send verification code
			$verifier->sendCode(Str::randomDigits(6), $user);
			DB::commit();
			return $this->getResponse([
				'id'	=> $user->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE => $ex->getMessage()
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}