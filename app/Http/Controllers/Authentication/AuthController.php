<?php
namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Controllers\ControllerAbstract;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Kayer\Verification\Verifier\VerifierInterface;

class AuthController extends ControllerAbstract
{
	public function login(LoginRequest $request, VerifierInterface $verifier)
    {
		$credentials = request([$request->login_type, 'password']);
		if(!Auth::attempt($credentials)) {
            return $this->getResponse([
				'message'	=> 'Unauthorized'
			], Response::HTTP_UNAUTHORIZED);
		}
		
		$user			= $request->user();
		//If user not verified		
		if(!$verifier->isVerified($user)) {
			return $this->getResponse([
				'id'	=> $user->getKey()
			], Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
		}
		
		$tokenResult	= $user->createToken('Personal Access Token');
        $token			= $tokenResult->token;
		if ($request->remember_me) {
            $token->expires_at	= Carbon::now()->addWeeks(1);
		}
		else {
            $token->expires_at	= Carbon::now()->addDay();
		}
		$token->save();
		return $this->getResponse([
			'access_token'	=> $tokenResult->accessToken,
            'token_type'	=> 'Bearer',
            'expires_at'	=> Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
		], Response::HTTP_OK);
    }
	
	/**
	 * Logout user (Revoke the token)
	 * 
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
		try {
			$request->user()->token()->revoke(); 
			return $this->getResponse([]);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}