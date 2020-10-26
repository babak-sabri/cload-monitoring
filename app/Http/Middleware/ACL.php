<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\PermissionsHandler;

class ACL
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$matches	= [];
		preg_match('/([a-z]*)@/i', $request->route()->getActionName(), $matches);
		$resource		= str_replace('Controller', '', $matches[1]);
		$action			= $request->route()->getActionMethod();
		$permission		= PermissionsHandler::check($resource, $action, auth()->user()->user_type);
		if(!$permission) {
			return response([], Response::HTTP_FORBIDDEN);
		}
		return $next($request);
	}
}
