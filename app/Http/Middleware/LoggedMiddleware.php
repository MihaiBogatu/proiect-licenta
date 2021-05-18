<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Session;

class LoggedMiddleware
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
		$logat = true;
		$user = null;
		
		if(!Session::has('user_id')){
			$logat=false;
		}else{
			$user_id = Session::get('user_id');
			$user = User::where('id', $user_id)->get();
			if(count($user)==0){
				$logat=false;
			}
		}
		
		if(!$logat){
			return redirect('/login')->with('status',"Logheaza-te inainte!");
		}			
		
        return $next($request);
    }
}
