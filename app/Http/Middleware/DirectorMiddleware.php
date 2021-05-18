<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Concediu;
use App\User;
use Carbon\Carbon;

class DirectorMiddleware
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

      if($user->adjunct == 1){
        $sef_departament = User::where('rol', 'sef-departament')
                        ->where('departament_id', $user->departament_id)
                        ->get()[0];
        $concedii = Concediu::where('user_id', $sef_departament->id)->get();
        $now = Carbon::now();

        foreach($concedii as $value){
          if(Carbon::parse($value->data_inceput) <= $now && Carbon::parse($value->data_sfarsit)>=$now){
              Session::put('sef-concediu', true);
              return $next($request);
          }
        }
        Session::forget('sef-concediu');
        return redirect('/concedii');
      }

      if($user->rol != 'sef-departament' && $user->rol != 'director'){
        return redirect('/concedii');
      }

      return $next($request);
    }
}
