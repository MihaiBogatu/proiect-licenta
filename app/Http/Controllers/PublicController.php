<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Concediu;
use App\Departament;
use Session;
use Carbon\Carbon;
use File;
use Hash;
use PDF;

class PublicController extends Controller
{

	public function index(Request $request){
		$evenimente = Concediu::join('users', 'concedius.user_id', '=', 'users.id')->orderBy("concedius.data_inceput")->get([
			'concedius.*',
			'users.name',
		]);

		$now = Carbon::now();
		$last_month = Carbon::now()->subMonth();
		$last_two_months = Carbon::now()->subMonth(2);
		$evenimente_luna_aceasta = Concediu::where('concedius.created_at', '>', $last_month)->join('users','concedius.user_id', '=', 'users.id')->get([
			'concedius.*',
			'users.name'
		]);

		$evenimente_luna_trecuta = Concediu::where('concedius.created_at', '>', $last_two_months)
										   ->where('concedius.created_at', '<', $last_month)
											 ->join('users','concedius.user_id', '=', 'users.id')
										   ->get([
										 			'concedius.*',
										 			'users.name'
										 		]);

		return view('dashboard', [
		'evenimente'=>$evenimente,
		'evenimente_luna' => $evenimente_luna_aceasta,
		'evenimente_2luni' => $evenimente_luna_trecuta,
		]);
	}

	public function mail_send($to_par, $subject_par, $message_par){

		$to = $to_par;
		$subject = $subject_par;

		$message ="
		<html>
		<head>
		<title>HTML email</title>
		</head>
		<body>
		".$message_par."
		</body>
		</html>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <'.env('MAIL_FROM_ADDRESS').'>' . "\r\n";

		mail($to,$subject,$message, $headers);
	}

	public function login_get(Request $request){
		return view('login');
	}

	public function register_get(Request $request){

		$departamente = Departament::get();

		return view('register', [
			'departamente' =>$departamente,
		]);
	}

	public function login_post(Request $request){
		$email = $request->input('email');
		$parola = $request->input('parola');

		$utilizator = User::where('email', $email)
						  ->get();

		if(count($utilizator)>0){

			//$utilizator[0]->email = 'a@k.com';
			//$utilizator[0]->save();

			//$utilizator[0]->delete();

			$utilizator = $utilizator[0];

			if(Hash::check($parola, $utilizator->password)){
				echo 'logat';
				Session::put('user_id', $utilizator->id);
				return redirect('/concedii');
			}else{
				echo 'incorect';
				return redirect('/login')->with('status', 'Date incorecte');
			}
		}else{
			echo 'incorect';
			return redirect('/login')->with('status', 'Date incorecte');
		}
	}

	public function register_post(Request $request){
		$email = $request->input('email');

		$emails = User::where('email', $email)
									 ->get();
		if(count($emails)>0){
			return redirect('/register')->with('status', "Utilizatorul deja exista!");
		}

		$departament = $request ->input('departament_id');
		if($departament==0){
			$departament = null;
		}
		$parola = $request->input('parola');
		$rol= $request->input('rol');
		$name = $request->input('nume');

		$adj_chk = $request->has('este-adjunct');
		$repetaparola = $request->input('repetaparola');
		if($parola != $repetaparola){
			return redirect('/register')->with('status', 'Parolele nu corespund');
		}

		$utilizator = new User;
		$utilizator->rol = $rol;
		if($rol == 'sef-departament'){
			$utilizator->sef= true;
			$adj_chk = false;
		}
		$utilizator->email= $email;
		if($utilizator->rol=='director'){
			$departament_parinte = Departament::whereNull('departament_id')->get()[0];
			$departament = $departament_parinte->id;
		}

		$utilizator->departament_id = $departament;

		$utilizator->name= $name;
		$utilizator->password= Hash::make($parola);
		$utilizator->adjunct = $adj_chk;
		$utilizator->save();

		//Session::put('user_id', $utilizator->id);

		return redirect('/register');

	}

	public function logout_get(Request $request){
		Session::forget('user_id');
		return redirect('/login')->with('status', 'Delogat');
	}

	public function concedii_get(Request $request){


		$user_id = Session::get('user_id');
		$user = User::where('id', $user_id)->get()[0];
		$user->citit_zile = true;
		$user->save();
		$zile_alocate = $user->zile_alocate;
		$users = User::where('departament_id', $user->departament_id)->where('id', '!=', $user_id)->get();
		$concedii = Concediu::where('user_id', $user_id)->where('acceptat', true)->get();
		$concedii_asteptare = Concediu::where('user_id', $user_id)
																	->whereNull('acceptat')
																	->get();
    $concedii_colegi_acceptate = Concediu::where('user_id', '!=', $user_id)
																			   ->join('users', 'users.id', '=', 'concedius.user_id')
																				 ->leftJoin('users as inlocuitori', 'inlocuitori.id', '=', 'concedius.inlocuitor_id')
																				 ->where('users.departament_id', $user->departament_id)
																	       ->where('acceptat', 1)
																				 ->get([
																					 'concedius.*',
																					 'inlocuitori.name as nume_inlocuitor',
																					 'users.name',
																				 ]);
		$concedii_colegi_asteptare = Concediu::join('users', 'users.id', '=', 'concedius.user_id')
																			   ->leftJoin('users as inlocuitori', 'inlocuitori.id', '=', 'concedius.inlocuitor_id')
																				 ->where('users.departament_id', $user->departament_id)
																				 ->whereNull('acceptat')
																				 ->get([
																					 'concedius.*',
																					 'inlocuitori.name as nume_inlocuitor',
																					 'users.name',
																				 ]);
		$concedii_editare = Concediu::where('user_id', $user_id)->where('vazut_editare', 0)->get();
		$concedii_colegi = [];
		$concedii_schimb = Concediu::join('users', 'users.id', '=', 'concedius.user_id')
																			   ->leftJoin('users as inlocuitori', 'inlocuitori.id', '=', 'concedius.inlocuitor_id')
																				 ->where('users.departament_id', $user->departament_id)
																				 ->where('users.id', '!=', $user_id)
																				 ->where('concedius.inlocuitor_id', '!=', $user_id)
																				 ->whereNull('acceptat')
																				 ->get([
																					 'concedius.*',
																					 'inlocuitori.name as nume_inlocuitor',
																					 'users.name'
																				 ]);

		foreach ($concedii_colegi_acceptate as $value) {
			array_push($concedii_colegi, $value);
		}

		foreach ($concedii_colegi_asteptare as $value) {
			array_push($concedii_colegi, $value);
		}

		foreach ($concedii_editare as $value){
			$value->vazut_editare = true;
			$value->save();
		}
		return view('concedii', [
			'concedii'=>$concedii,
			'concedii_schimb' => $concedii_schimb,
			'zile_alocate' =>$zile_alocate,
			'concedii_colegi' => $concedii_colegi,
			'users' => $users,
			'user'=>$user,
			'concedii_asteptare'=>$concedii_asteptare,
		]);
	}

	public function concedii_post(Request $request){



		$start_date_string = $request -> input("start-date");
		$end_date_string = $request -> input("end-date");
		$inlocuitor_id = $request->input('inlocuitor-id');
		$tip_concediu = $request->input('tip-concediu');

		$user_id = Session::get('user_id');
		$user = User::find($user_id);
		$zile_alocate = $user->zile_alocate;
		$start_date = Carbon::createFromFormat('m/d/Y', $start_date_string);
		$end_date = Carbon::createFromFormat('m/d/Y', $end_date_string);



		$start_date->hour = 0;
		$start_date->minute = 0;
		$start_date->second = 0;
		$start_date->millisecond = 0;

		$end_date->hour = 0;
		$end_date->minute = 0;
		$end_date->second = 0;
		$end_date->millisecond = 0;

		$colucratori = [];

		$year = Carbon::now()->year;
		$start_year = Carbon::createFromFormat('d/m/Y', '01/01/'.strval($year));
		$end_year = Carbon::createFromFormat('d/m/Y', '31/12/'.strval($year));

		$start_year->hour = 0;
		$start_year->minute = 0;
		$start_year->second = 0;
		$start_year->millisecond = 0;

		$end_year->hour = 0;
		$end_year->minute = 0;
		$end_year->second = 0;
		$end_year->millisecond = 0;

		$concediu = Concediu::where('user_id', $user->id)
												->whereNull('acceptat')
												->get();

		if(count($concediu) > 0){
			return redirect('/concedii')->with('status', "Deja ai un concediu in asteptare!");
		}


		$concedii = Concediu::where('user_id', $user->id)
												  ->where('data_inceput', '>=', $start_year)
												  ->where('data_sfarsit', '<=', $end_year)
													->where('acceptat', true)
												  ->get();
		if(count($concedii)>=3){
			return redirect('/concedii')->with('status',"Ai depasit limita maxima de concedii(3)!");
		}

		$colucratori_sef = User::where('departament_id', $user->departament_id)
											 ->where('sef', 1)
											 ->join('concedius', 'users.id', '=', 'concedius.user_id')
											 ->where('concedius.acceptat', true)
											 ->get([
												 'data_inceput',
												 'data_sfarsit'
											 ]);

		$colucratori_adj = User::where('departament_id', $user->departament_id)
 											 ->where('adjunct', 1)
 											 ->join('concedius', 'users.id', '=', 'concedius.user_id')
											 ->where('concedius.acceptat', true)
 											 ->get([
 												 'data_inceput',
 												 'data_sfarsit'
 											 ]);

		$colucratori = [];
		foreach ($colucratori_sef as $value) {
			array_push($colucratori, $value);
		}
		foreach ($colucratori_adj as $value) {
			array_push($colucratori, $value);
		}

		foreach($colucratori as $concediu_loc){
			if(($concediu_loc->data_inceput >= $start_date && $concediu_loc->data_inceput <= $end_date) ||
				 ($concediu_loc->data_sfarsit >= $start_date && $concediu_loc->data_sfarsit <= $end_date) ||
				 ($start_date >= $concediu_loc->data_inceput && $start_date <= $concediu_loc->data_sfarsit) ||
	 			 ($end_date >= $concediu_loc->data_inceput && $end_date <= $concediu_loc->data_sfarsit)){
					 return redirect('/concedii')->with('status', 'Concediul tau incalca restrictia de suprapunere');
				 }
		}


		if($user->rol=='sef-departament' || $user->rol == 'angajat'){
			$start_date_loc = clone $start_date;

			$vector_zile_concediu = [];
			while($start_date_loc<=$end_date){
				$vector_zile_concediu[$start_date_loc->day.'/'.$start_date_loc->month.'/'.$start_date_loc->year] = 1;
				$start_date_loc->day++;
			}


			$angajati_loc = User::where('departament_id', $user->departament_id)
													->rightJoin('concedius','users.id','=','concedius.user_id')
													->where('concedius.acceptat', true)
													->get(['concedius.data_inceput', 'concedius.data_sfarsit']);

			$angajati = [];
			foreach($angajati_loc as $a){
				array_push($angajati, $a);
			}

			foreach($angajati as $a){
				$start_date_a_loc = Carbon::parse($a->data_inceput);
				$end_date_a_loc = $a->data_sfarsit;

				while($start_date_a_loc<=$end_date_a_loc){
					$cheie = $start_date_a_loc->day.'/'.$start_date_a_loc->month.'/'.$start_date_a_loc->year;
					if(array_key_exists($cheie, $vector_zile_concediu)){
						$vector_zile_concediu[$cheie]++;
					}
					$start_date_a_loc->addDay(1);
				}
			}

			$angajati_count = count(User::where('departament_id', $user->departament_id)->get());

			foreach ($vector_zile_concediu as $key => $value) {

				if($vector_zile_concediu[$key] > $angajati_count/2){
					return redirect('/concedii')->with('status',"Prea multe concedii in departament");
				}

			}

		}

		$concediu = new Concediu;
		$concediu -> user_id = $user->id;

		//dd($request->all());

		$concediu ->data_inceput = $start_date;
		$concediu ->data_sfarsit = $end_date;
		$concediu->inlocuitor_id = $inlocuitor_id;
		$data = $concediu->data_inceput;
		$now = Carbon::now();
		$diff = Carbon::parse($concediu ->data_sfarsit)->diffInDays(Carbon::parse($data))+1;
		//dd($diff);
		$zile_rezultat = $zile_alocate - $diff;
		if($zile_rezultat < 0 ){
			return redirect('/concedii')->with('status', 'Nu mai ai zile de concediu');
		}
		$user->zile_alocate = $zile_rezultat;
		$user->save();
		if($inlocuitor_id==0){
			$concediu->acceptat_inlocuitor=1;

		}
		$concediu->tip_concediu = $tip_concediu;
		$concediu->save();

		$sefdepartament = null;
		if($user->rol == 'angajat'){
			$sefdepartament = User::where("rol", 'sef-departament')->where("departament_id", $user->departament_id)->first();
		}
		else if($user->rol == 'sef-departament'){
			$departament_parinte = Departament::where("departament_id", $user->departament_id)->first();
			$sefdepartament = User::where("rol",'sef-departament')->where("departament_id", $departament_parinte->id)->first();

			if($sefdepartament==null){
				$sefdepartament = User::where('rol', 'director')->first();
			}
		}

		if($sefdepartament){
			$this->mail_send($sefdepartament->email,'Cerere de concediu', $user->name. ' a trimis o cerere de concediu in intervalul '.
			Carbon::parse($concediu->data_inceput)->format('d/m/Y')." - ".Carbon::parse($concediu->data_sfarsit)->format('d/m/Y'));
		}

		return redirect('/concedii');

	}

	public function profile_get(Request $request){
		$user_id = Session::get('user_id');
		$user=User::where('users.id', $user_id)->join('departaments','users.departament_id','=','departaments.id')->get([
			'users.*',
			'departaments.name as departament_name',
			])[0];
		return view('profile', [
			'user'=>$user,
		]);
	}

	public function profile_post(Request $request){

	}

	public function angajati_get(Request $request){

		$angajati = null;

		$user_id = Session::get('user_id');
		$user = User::where('users.id', $user_id)->get()[0];
		$departamente = Departament::get();
		if($user->rol == 'administrator' || $user->rol == 'director'){
			$angajati = User::whereNotNull('users.id');
		}else{
			$angajati = User::where('users.departament_id', $user->departament_id);
		}

		$search_q = $request->input('search');
		$order = $request->input('order');
		$dep_selectat=null;

		if($request->has('departament') && $request->input('departament')!=0){
			$dep_selectat = $request->input('departament');
			$angajati = $angajati->where('users.departament_id', $dep_selectat);
		}

		$angajati = $angajati->where('users.name', 'LIKE', '%'.$search_q.'%');

		if($order=='cresc'){
			$angajati = $angajati->orderBy('users.name');
		}else{
			$angajati = $angajati->orderBy('users.name', 'DESC');
		}

		$angajati_per_pagina = 7;
		$pagina_curenta = 1;
		if($request->has('page')){
			$pagina_curenta = $request->input('page');
		} // /angajati

		$angajati_count = count($angajati->get());
		$pagina_max = intval($angajati_count/$angajati_per_pagina);
		if($angajati_count%$angajati_per_pagina!=0){
			$pagina_max++;
		}
		$angajati = $angajati->join('departaments', 'users.departament_id', '=', 'departaments.id');
		$angajati = $angajati->skip(($pagina_curenta-1)*$angajati_per_pagina)->take($angajati_per_pagina)->get([
			'users.*',
			'departaments.name as nume_departament',
		]);

		return view('angajati', [
			'angajati' => $angajati,
			'dep_selectat' => $dep_selectat,
			'departamente' => $departamente,
			'pagina_curenta'=>$pagina_curenta,
			'pagina_max'=>$pagina_max,
		]);

	}
		public function other_profile_get(Request $request,$id){

			$user = User::where('id', $id)->get()[0];

			return view('profile', [
				'user'=>$user,
			]);

		}

		public function delete_profile_get(Request $request,$id){
			$user = User::where('id', $id)->get()[0];
			$user->delete();

			if($id == Session::get("user_id")){
				Session::forget("user_id");
				return redirect('/login');
			}else{
				return back()->with('status', 'Sters');
			}
		}

		public function verificare_concedii_get(Request $request){
			$id = Session::get('user_id');
			$user = User::where('id', $id)->get()[0];

			$concedii_neacceptate = [];

			if($user->sef){
				$concedii_neacceptate1 = Concediu::join('users', 'users.id', '=', 'concedius.user_id')
																	 ->where('users.departament_id', $user->departament_id)
																	 ->where('users.sef', false)
																	 ->where('acceptat_inlocuitor', 1)
																	 ->whereNull('concedius.acceptat')
																   ->get([
																	  'concedius.*',
																		'users.name',
																	 ]);

				 $concedii_neacceptate2 = Concediu::join('users', 'users.id', '=', 'concedius.user_id')
															->join('departaments', 'departaments.id', '=', 'users.departament_id')
															->where('users.sef', true)
															->where('acceptat_inlocuitor', 1)
															->whereNull('concedius.acceptat')
															->where('departaments.departament_id', $user->departament_id)
															->get([
															 'concedius.*',
															 'users.name',
															]);
					foreach ($concedii_neacceptate1 as $value) {
						array_push($concedii_neacceptate, $value);
					}


					foreach($concedii_neacceptate2 as $value){
						array_push($concedii_neacceptate, $value);
					}
			}
			else if($user->rol == 'director'){

				$concedii_neacceptate = Concediu::join('users', 'users.id', '=', 'concedius.user_id')
														->join('departaments', 'departaments.id', '=', 'users.departament_id')
														->where('users.sef', true)
														->where('acceptat_inlocuitor', 1)
														->whereNull('concedius.acceptat')
														->where('departaments.departament_id', $user->departament_id)
														->get([
														 'concedius.*',
														 'users.name',
														]);
			}


			foreach ($concedii_neacceptate as $value) {
				$value->citit = true;
				$value->save();
			}

			return view('verificare-concedii',[
				'concedii_neacceptate' => $concedii_neacceptate,
			]);
		}

		public function acceptare_concediu_get(Request $request,$id){


			$user_id = Session::get('user_id');
			$user = User::where('id', $user_id)->get()[0];
			$concediu = Concediu::whereNull('acceptat')
													->where('id', $id)
													->get()[0];
			$user_concediu_id = $concediu->user_id;
			$user_concediu = User::find($user_concediu_id);

			if($user->id == $concediu->inlocuitor_id ){
				$concediu->acceptat_inlocuitor = true;
				$concediu->save();
			}

			if(($user->rol == 'sef-departament' || $user->rol == 'director') && $concediu->acceptat_inlocuitor){
				$concediu->acceptat = true;
			}

			$this->mail_send($user_concediu->email, "Raspuns Cerere Concediu", "<p>Concediul tau a fost acceptat</p><br>Vei avea concediu de pe ".Carbon::parse($concediu->data_inceput)->format('d/m/Y')." pana pe ".Carbon::parse($concediu->data_sfarsit)->format('d/m/Y'));

			$concediu->citit = false;
			$concediu->save();

			return back()->with('status', 'Concediu acceptat');
		}

		public function departamente_get(Request $request){

			$departamente = Departament::get();

			return view('departamente',[
				'departamente' => $departamente,
			]);

		}

		public function departamente_post(Request $request){

			$action = $request->input('action');
			if($action == 'add'){
				$departament = new Departament;
				$name = $request -> input('nume_departament');
				$parinte = $request -> input('parinte-departament');
				if($parinte==0){
					$parinte=null;
				}
				if(count(Departament::where('name', $name)->get())>0){
					return redirect('/departamente')->with('status',"Departament deja existent!");
				}
				$departament->name = $name;
				$departament->departament_id = $parinte;
				$departament->save();

				return redirect('/departamente')->with('status', "Departament adaugat");

			}
			else{
				$departament_id = $request->input('id-departament');
				$departament = Departament::find($departament_id); // sau Departament::where('id', $departament_id)->get()[0]
				$parinte = Departament::where('id', $departament->departament_id)->get()[0];
				$copii = Departament::where('departament_id', $departament_id)->get();
				$users = User::where('departament_id', $departament->id)->get();
				//dd($parinte->id);
				foreach ($copii as $value) {
					$value ->departament_id = $parinte->id;
					$value ->save();
				}

				foreach ($users as $value) {

					$value->departament_id = $parinte->id;
					$value->save();

				}

				$departament->delete();

				return back()->with('status', "Departament sters");

			}
		}

		public function sterge_concediu_get(Request $request, $id){

			$concediu = Concediu::whereNull('acceptat')
													->where('id', $id)
													->get()[0];

			$diff = Carbon::parse($concediu->data_sfarsit)->diffInDays(Carbon::parse($concediu->data_inceput))+1;

			$user = User::where('id', $concediu->user_id)->first();
			$user->zile_alocate += $diff;
			$user->save();

			$concediu->delete();

			return back()->with('status', "Concediu sters!");

		}

		public function respingere_concediu_post(Request $request, $id){



			$user_id = Session::get('user_id');
			$user = User::where('id', $user_id)->get()[0];
			$concediu = Concediu::whereNull('acceptat')
													->where('id', $id)
													->get()[0];

			$user_concediu_id = $concediu->user_id;
			$user_concediu = User::find($user_concediu_id);

			if($user->id == $concediu->inlocuitor_id ){
				$concediu->acceptat_inlocuitor = false;
				$concediu->save();
			}

			if(($user->rol == 'sef-departament' || $user->rol == 'director') && $concediu->acceptat_inlocuitor){
				$concediu->acceptat = false;
			}
			$motiv = $request->input('motiv');
			$concediu->motiv = $motiv;
			$concediu->citit = false;
			$this->mail_send($user_concediu->email, "Raspuns Cerere Concediu", "<p>Concediul tau a fost respins</p><br>Motiv: ".$concediu->motiv);
			$concediu->save();

			return back()->with('status', 'Concediu respins');


		}

		public function gestionare_profil_get(Request $request,$id){
			$user = User::where('id', $id)->get()[0];
			$departamente = Departament::get();
			return view('gestionare-profil', [
				'user'=> $user,
				'departamente'=>$departamente,
			]);
		}

		public function gestionare_profil_post(Request $request,$id){

			$user = User::where('id', $id)->get()[0];
			$rol = $request->input('rol');
			$departament = $request->input('departament_id');
			$adjunct = $request->has('este-adjunct');

			$user->rol = $rol;
			if($user->rol == 'sef-departament'){
				$user->sef = true;
			}else{
				$user->sef = false;
			}
			$user->departament_id = $departament;
			$user->adjunct = $adjunct;

			$user->save();

			return back()->with('status', "User schimbat");



		}

		public function schimbare_profil_get(Request $request,$id){

			$user = User::where('id', $id)->get()[0];

			return view('profile', [
				'user'=> $user,
			]);

		}

		public function schimbare_profil_post(Request $request,$id){
			$user = User::where('id', $id)->get()[0];
			$user->name = $request->input('nume');
			$user->email = $request->input('email');
			$user->contact = $request->input('contact');

			if($request->hasFile('profil-img')){
				$cale = public_path().'/poze-profil';

				$poza = $request->file('profil-img');
				$ext = $poza->getClientOriginalExtension();

				if(File::exists($cale.'/'.$user->id.'.'.$ext)){
					File::delete($cale.'/'.$user->id.'.'.$ext);
				}

				$poza->move($cale, $user->id.'.'.$ext);
			}
			$user->save();

			return redirect('/profile')->with('status', "Profil schimbat");
		}

		public function cereri_inlocuitor_get(Request $request){

			$user_id = Session::get('user_id');

			$concedii = Concediu::whereNull('acceptat_inlocuitor')
													->where('inlocuitor_id', $user_id)
													->join('users', 'users.id', '=', 'concedius.user_id')
													->get(['concedius.*', 'users.name']);

			return view('cereri-inlocuitor', [
				'concedii' => $concedii,
			]);

		}

		public function istoric_concedii_get(Request $request){

			$user_id = Session::get('user_id');

			$concedii_tot = Concediu::where('user_id', $user_id)
															->orderBy('data_inceput', 'DESC')
															->whereNotNull('acceptat')
															->get();

			foreach($concedii_tot as $value){
				$value->citit = true;
				$value->save();
			}

			return view('istoric-concedii',[
				'concedii_tot' => $concedii_tot
			]);

		}

		public function editeaza_concediu_post(Request $request){

			$start_date_string = $request -> input("start-date-modify");
			$end_date_string = $request -> input("end-date-modify");
			$inlocuitor_id = $request->input('inlocuitor-id-modify');
			$tip_concediu = $request->input('tip-concediu-modify');

			$user_id = Session::get('user_id');
			$user = User::find($user_id);

			$start_date = Carbon::createFromFormat('m/d/Y', $start_date_string);
			$end_date = Carbon::createFromFormat('m/d/Y', $end_date_string);



			$start_date->hour = 0;
			$start_date->minute = 0;
			$start_date->second = 0;
			$start_date->millisecond = 0;

			$end_date->hour = 0;
			$end_date->minute = 0;
			$end_date->second = 0;
			$end_date->millisecond = 0;

			$colucratori = [];

			$year = Carbon::now()->year;
			$start_year = Carbon::createFromFormat('d/m/Y', '01/01/'.strval($year));
			$end_year = Carbon::createFromFormat('d/m/Y', '31/12/'.strval($year));

			$start_year->hour = 0;
			$start_year->minute = 0;
			$start_year->second = 0;
			$start_year->millisecond = 0;

			$end_year->hour = 0;
			$end_year->minute = 0;
			$end_year->second = 0;
			$end_year->millisecond = 0;

			$concediu = Concediu::where('user_id', $user->id)
													->whereNull('acceptat')
													->get()[0];


			$concedii = Concediu::where('user_id', $user->id)
													  ->where('data_inceput', '>=', $start_year)
													  ->where('data_sfarsit', '<=', $end_year)
														->where('acceptat', true)
													  ->get();

			$colucratori_sef = User::where('departament_id', $user->departament_id)
												 ->where('sef', 1)
												 ->join('concedius', 'users.id', '=', 'concedius.user_id')
												 ->where('concedius.acceptat', true)
												 ->get([
													 'data_inceput',
													 'data_sfarsit'
												 ]);

			$colucratori_adj = User::where('departament_id', $user->departament_id)
	 											 ->where('adjunct', 1)
	 											 ->join('concedius', 'users.id', '=', 'concedius.user_id')
												 ->where('concedius.acceptat', true)
	 											 ->get([
	 												 'data_inceput',
	 												 'data_sfarsit'
	 											 ]);

			$colucratori = [];
			foreach ($colucratori_sef as $value) {
				array_push($colucratori, $value);
			}
			foreach ($colucratori_adj as $value) {
				array_push($colucratori, $value);
			}

			foreach($colucratori as $concediu_loc){
				if(($concediu_loc->data_inceput >= $start_date && $concediu_loc->data_inceput <= $end_date) ||
					 ($concediu_loc->data_sfarsit >= $start_date && $concediu_loc->data_sfarsit <= $end_date) ||
					 ($start_date >= $concediu_loc->data_inceput && $start_date <= $concediu_loc->data_sfarsit) ||
		 			 ($end_date >= $concediu_loc->data_inceput && $end_date <= $concediu_loc->data_sfarsit)){
						 return redirect('/concedii')->with('status', 'Concediul tau incalca restrictia de suprapunere');
					 }
			}


			if($user->rol=='sef-departament' || $user->rol == 'angajat'){
				$start_date_loc = clone $start_date;

				$vector_zile_concediu = [];
				while($start_date_loc<=$end_date){
					$vector_zile_concediu[$start_date_loc->day.'/'.$start_date_loc->month.'/'.$start_date_loc->year] = 1;
					$start_date_loc->day++;
				}


				$angajati_loc = User::where('departament_id', $user->departament_id)
														->rightJoin('concedius','users.id','=','concedius.user_id')
														->where('concedius.acceptat', true)
														->get(['concedius.data_inceput', 'concedius.data_sfarsit']);

				$angajati = [];
				foreach($angajati_loc as $a){
					array_push($angajati, $a);
				}

				foreach($angajati as $a){
					$start_date_a_loc = Carbon::parse($a->data_inceput);
					$end_date_a_loc = $a->data_sfarsit;

					while($start_date_a_loc<=$end_date_a_loc){
						$cheie = $start_date_a_loc->day.'/'.$start_date_a_loc->month.'/'.$start_date_a_loc->year;
						if(array_key_exists($cheie, $vector_zile_concediu)){
							$vector_zile_concediu[$cheie]++;
						}
						$start_date_a_loc->addDay(1);
					}
				}

				$angajati_count = count(User::where('departament_id', $user->departament_id)->get());

				foreach ($vector_zile_concediu as $key => $value) {

					if($vector_zile_concediu[$key] > $angajati_count/2){
						return redirect('/concedii')->with('status',"Prea multe concedii in departament");
					}

				}




			}

			$diff = Carbon::parse($concediu->data_sfarsit)->diffInDays($concediu->data_inceput) + 1;
			$user->zile_alocate = $user->zile_alocate + $diff;
			$user->save();
			$diff_final = $end_date->diffInDays($start_date) + 1;

			if($diff_final > $user->zile_alocate){
				return redirect('/concedii')->with('status','Ai depasit numarul de zile alocate');
			}
			$user->zile_alocate = $user->zile_alocate - $diff_final;
			$user->save();
			$concediu ->data_inceput = $start_date;
			$concediu ->data_sfarsit = $end_date;
			$concediu->inlocuitor_id = $inlocuitor_id;

			if($inlocuitor_id==0){
				$concediu->acceptat_inlocuitor=1;

			}

			$concediu->tip_concediu = $tip_concediu;
			$concediu->save();

			return redirect('/concedii')->with('status','Concediu Modificat');

		}

		public function editeaza_zile_post(Request $request, $id){

			$zile = $request->input('editeaza-zile');
			$user = User::where('id', $id)->first();
			$user->citit_zile = false;
			$user->zile_alocate = $zile;
			$user->save();

			return back()->with('status','Zile alocate modificate');
		}

	public function total_concedii_get(Request $request){

		$concedii_tot = Concediu::whereNotNull('acceptat')->join('users', 'concedius.user_id', '=', 'users.id')->get([
			'concedius.*',
			'users.name',
		]);
		$user_id = Session::get('user_id');
		$user = User::where('id', $user_id)->first();
		$departament = Departament::where('id', $user->departament_id)->first();

		$colegi = [];

		if($user->sef){
			$colegi1 = User::where('users.departament_id', $user->departament_id)
										 ->where('users.sef', false)
										 ->get([
											'users.*',
										 ]);


			$colegi2 = User::join('departaments', 'departaments.id', '=', 'users.departament_id')
								    		 ->where('users.sef', true)
												 ->where('users.zile_alocate', '>', 0)
												 ->where('departaments.departament_id', $user->departament_id)
												 ->get([
												  'users.*',
												 ]);

			foreach($colegi1 as $value){
				array_push($colegi, $value);
			}

			foreach($colegi2 as $value){
				array_push($colegi, $value);
			}

		 }else if($user->rol=='director'){
			 $colegi = User::join('departaments', 'departaments.id', '=', 'users.departament_id')
												 ->where('users.sef', true)
												 ->where('users.zile_alocate', '>', 0)
												 ->where('departaments.departament_id', $user->departament_id)
												 ->get([
													'users.*',
												 ]);
		 }

		return view('concedii-tot',[
			'concedii_tot' => $concedii_tot,
			'colegi'=>$colegi,
		]);
	}

	public function concediu_edit_get(Request $request, $id){

		$concediu = Concediu::find($id);

		$concediu->acceptat = null;
		$concediu->cerere_editare = 0;
		$concediu->vazut_editare = 0;

		$concediu->motiv = null;

		$concediu->save();

		return back()->with('status','Editare permisa');

	}

	public function cerere_edit_get(Request $request, $id){

		$concediu = Concediu::find($id);

		$concediu->cerere_editare = true;

		$concediu->save();

		return back()->with('status', 'Cerere de editare trimisa!');

	}

	public function cere_schimb_concediu_post(Request $request){

		$concediu_curent_id = $request->input('concediu-curent');
		$concediu_schimb_id = $request->input('schimb');

		$concediu_curent = Concediu::where('id',$concediu_curent_id)->first();
		$concediu_schimb = Concediu::where('id',$concediu_schimb_id)->first();

		$concediu_schimb->schimb_concediu_id = $concediu_curent_id;
		$concediu_schimb -> save();

		return back()->with('status', 'Cerere de schimb trimisa');
	}



	public function schimb_concediu_post(Request $request){

		$action = $request->input('action');


		$concediu_actual_id = $request->input('concediu-id');
		$concediu_actual = Concediu::where('id',$concediu_actual_id)->first();
		$schimb_concediu_id = $concediu_actual->schimb_concediu_id;
		$concediu_schimb = Concediu::where('id', $schimb_concediu_id)->first();

		$mesaj = 'Schimb refuzat';
		if($action == 'accept'){
			$temp = $concediu_actual->user_id;
			$concediu_actual->user_id = $concediu_schimb->user_id;
			$concediu_schimb->user_id = $temp;
			$mesaj = 'Concedii schimbate';
			$data_inceput_actual = Carbon::parse($concediu_actual->data_inceput);
			$data_inceput_schimb = Carbon::parse($concediu_schimb->data_inceput);
			$data_sfarsit_actual = Carbon::parse($concediu_actual->data_sfarsit);
			$data_sfarsit_schimb = Carbon::parse($concediu_schimb->data_sfarsit);

			$diff1 = $data_sfarsit_actual->diffInDays($data_inceput_actual);
			$diff2 = $data_sfarsit_schimb->diffInDays($data_inceput_schimb);

			$user_actual = User::where('id',$concediu_actual->user_id)->first();
			$user_schimb = User::where('id',$concediu_schimb->user_id)->first();
			$user_actual->zile_alocate = $user_actual->zile_alocate - $diff1 + $diff2;
			$user_schimb->zile_alocate = $user_schimb->zile_alocate - $diff2 + $diff1;
			$user_actual->save();
			$user_schimb->save();
		}
		$concediu_actual->schimb_concediu_id= null;

		$concediu_actual->save();
		$concediu_schimb->save();

		return back()->with('status',$mesaj);
	}


	public function raport_get(Request $request, $departament_id){

		$now = Carbon::now();
		$user_id = Session::get('user_id');
		$user = User::where('id', $user_id)->get()[0];
		$departament = Departament::where('id', $departament_id)->first();
		$concedii = Concediu::where(function($query){
			$query->whereNull('acceptat')
						->orWhere('acceptat', 1);
		})
		->join('users', 'concedius.user_id', '=', 'users.id')
		->leftJoin('users as inlocuitori', 'concedius.inlocuitor_id', '=', 'users.id')
		->where('data_inceput', '>=', $now)
		->where('users.departament_id', $departament_id)
		->get([
		'concedius.*',
		'users.name',
		'users.rol',
		'users.zile_alocate',
		'inlocuitori.name as inlocuitor',
		]);

		$pdf = PDF::loadView('pdf.raport', [
			'concedii' => $concedii,
			'departament' => $departament,
			'user' => $user,
		]);

		return view('pdf.raport',[
			'concedii' => $concedii,
			'departament' => $departament,
			'user' => $user,
		]);

	//	return $pdf->download('raport.pdf');
	}

	public function download_get(Request $request, $departament_id){

	$now = Carbon::now();
	$user_id = Session::get('user_id');
	$user = User::where('id', $user_id)->get()[0];
	$departament = Departament::where('id', $departament_id)->first();
	$concedii = Concediu::where(function($query){
		$query->whereNull('acceptat')
					->orWhere('acceptat', 1);
	})
	->join('users', 'concedius.user_id', '=', 'users.id')
	->leftJoin('users as inlocuitori', 'concedius.inlocuitor_id', '=', 'users.id')
	->where('data_inceput', '>=', $now)
	->where('users.departament_id', $departament_id)
	->get([
	'concedius.*',
	'users.name',
	'users.rol',
	'users.zile_alocate',
	'inlocuitori.name as inlocuitor',
	]);

	$pdf = PDF::loadView('pdf.download', [
		'concedii' => $concedii,
		'departament' => $departament,
		'user' => $user,
	]);

	return $pdf->download('raport.pdf');

  }

	public function concediu_obligatoriu_get(Request $request, $id){
		$user_id = $id;

		$now = Carbon::now()->month;
		if(env('APP_DEBUG')){
			$now=Carbon::parse('11/11/2021')->month;
		}
		$user = User::find($id);
		$users = User::where('departament_id', $user->departament_id)->where('id', '!=', $user_id)->get();
		if(($now != 11 && $now != 12) || $user->zile_alocate <= 0){
			return back()->with('status', 'Nu ai ce cauta!');
		}

		return view('concediu-obligatoriu',[
			'user'=> $user,
			'users' => $users,
			'user_id'=>$id,
		]);

	}

	public function concediu_obligatoriu_post(Request $request, $id){

		$user_id = $id;

		$start_date_string = $request -> input("start-date");
		$end_date_string = $request -> input("end-date");
		$inlocuitor_id = $request->input('inlocuitor-id');
		$tip_concediu = $request->input('tip-concediu');

		$user_id = $id;
		$user = User::find($user_id);
		$zile_alocate = $user->zile_alocate;
		$start_date = Carbon::createFromFormat('m/d/Y', $start_date_string);
		$end_date = Carbon::createFromFormat('m/d/Y', $end_date_string);

		$concediu = new Concediu;
		$concediu->data_inceput = $start_date;
		$concediu->data_sfarsit = $end_date;
		$user->zile_alocate = $user->zile_alocate - ($end_date->diffInDays($start_date)+1);
		$user->save();
		$concediu->tip_concediu = $tip_concediu;
		$concediu->inlocuitor_id = $inlocuitor_id;
		$concediu->user_id = $user_id;
		$concediu->acceptat = true;
		$concediu->acceptat_inlocuitor = true;

		$concediu->save();

		return redirect('/concedii')->with('status', 'Concediu obligatoriu salvat');

	}

	public function concediu_editare_get(Request $request, $id){

		$concediu = Concediu::where('concedius.id', $id)->leftJoin('users', 'users.id', '=', 'concedius.inlocuitor_id')->get([
			'concedius.*',
			'users.name',
		])[0];
		$user_concediu = User::where('id', $concediu->user_id)->first();
		$user_id = Session::get('user_id');
		$user = User::find($user_id);

		$inlocuitori = User::where('departament_id', $user->departament_id)->get();
		return view('concediu-editare',[
			'concediu' => $concediu,
			'user' => $user_concediu,
			'inlocuitori' => $inlocuitori,
		]);

	}

	public function concediu_editare_post(Request $request, $id){
		$concediu = Concediu::find($id);
		$start_date_string = $request -> input("start-date-modify");
		$end_date_string = $request -> input("end-date-modify");
		$inlocuitor_id = $request->input('inlocuitor-id-modify');
		$tip_concediu = $request->input('tip-concediu-modify');

		$user = User::where('id', $concediu->user_id)->first();
		$zile_alocate = $user->zile_alocate;
		$start_date = Carbon::createFromFormat('m/d/Y', $start_date_string);
		$end_date = Carbon::createFromFormat('m/d/Y', $end_date_string);

		$diff = $end_date->diffInDays($start_date);




		$old_diff = Carbon::parse($concediu->data_sfarsit)->diffInDays(Carbon::parse($concediu->data_inceput));
		$zile_alocate = $zile_alocate - $diff + $old_diff;
		if($zile_alocate < 0 ){
			return back()->with('status', 'Concediul depaseste zilele alocate');
		}
		$user->zile_alocate = $zile_alocate;
		$user->save();
		$concediu->data_inceput = $start_date;
		$concediu->data_sfarsit = $end_date;
		$concediu->tip_concediu = $tip_concediu;
		$concediu->inlocuitor_id = $inlocuitor_id;

		$concediu->save();
		return back()->with('status','Concediu modificat');
	}

	public function istoric_departament_get(Request $request){

		$user_id = Session::get('user_id');
		$user = User::find($user_id);
		$concedii_tot = Concediu::join('users', 'users.id', '=', 'concedius.user_id')
														->where('departament_id', $user->departament_id)
														->orderBy('data_inceput', 'DESC')
														->whereNotNull('acceptat')
														->get(
															'concedius.*',
															'users.departament_id',
															'users.name',
														);

		foreach($concedii_tot as $value){
			$value->citit = true;
			$value->save();
		}

		return view('istoric-departament',[
			'concedii_tot' => $concedii_tot
		]);

	}
}
