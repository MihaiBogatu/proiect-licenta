<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Session;
use App\User;
use App\Concediu;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public static function get_user(){
		$user = null;

		if(Session::has('user_id')){
			$user = User::find(Session::get('user_id'));
		}

		return $user;

	}

	public static function get_Users()
    {

		$users = null;
        $users = User::all();

        return $users;
    }

	public static function in_concediu($user_id){

		$user = User::find($user_id);

		$concedii = Concediu::where('user_id', $user_id)->get();
		$prezent = Carbon::now();

		foreach($concedii as $concediu){
			if($concediu->data_inceput < $prezent && $concediu->data_sfarsit > $prezent){
				return true;
			}
		}

		return false;
	}

  public static function ap_concediu($user_id){
    $user = User::find($user_id);
    $concediu = Concediu::where('user_id', $user_id)
                        ->where('acceptat', true)
                        ->orderBy('data_inceput')->first();
    return $concediu;
    }
}
