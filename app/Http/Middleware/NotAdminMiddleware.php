<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\User;

class NotAdminMiddleware
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
      if(!Session::has('user_id')){
  			return redirect('/login');
  		}
  		$id = Session::get('user_id');
  		$user = User::find($id);
  		if($user->rol == "administrator"){
  			return redirect('/');
  		}
        return $next($request);
    }
}
