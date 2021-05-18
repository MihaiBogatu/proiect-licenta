<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class VerificareConcediuCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'VerifConcediu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

      $sefi = \App\User::where('rol', 'sef-departament')->get();

      // Seful

      foreach($sefi as $user){
       $concedii_neacceptate1 = \App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
                                  ->where('users.departament_id', $user->departament_id)
                                  ->where('users.sef', false)
                                  ->where('acceptat_inlocuitor', 1)
                                  ->whereNull('concedius.acceptat')
                                  ->where('concedius.citit', false)
                                  ->get([
                                   'concedius.*',
                                   'users.name',
                                  ]);

        $concedii_neacceptate2 = \App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
                             ->join('departaments', 'departaments.id', '=', 'users.departament_id')
                             ->where('users.sef', true)
                             ->where('acceptat_inlocuitor', 1)
                             ->whereNull('concedius.acceptat')
                             ->where('concedius.citit', false)
                             ->where('departaments.departament_id', $user->departament_id)
                             ->get([
                              'concedius.*',
                              'users.name',
                             ]);

         $cereri_editare_concediu1 = \App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
                                    ->where('users.departament_id', $user->departament_id)
                                    ->where('users.sef', false)
                                    ->whereNotNull('concedius.acceptat')
                                    ->where('concedius.cerere_editare', 1)
                                    ->get([
                                     'concedius.*',
                                     'users.name',
                                    ]);

         $cereri_editare_concediu2 = \App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
                               ->join('departaments', 'departaments.id', '=', 'users.departament_id')
                               ->where('users.sef', true)
                               ->whereNotNull('concedius.acceptat')
                               ->where('concedius.cerere_editare', 1)
                               ->where('departaments.departament_id', $user->departament_id)
                               ->get([
                                'concedius.*',
                                'users.name',
                               ]);

         $angajati_zile_ramase1 = \App\User::where('users.departament_id', $user->departament_id)
                                            ->where('users.sef', false)
                                            ->where('users.zile_alocate', '>', 0)
                                            ->get([
                                             'users.*',
                                            ]);

         $angajati_zile_ramase2 = \App\User::join('departaments', 'departaments.id', '=', 'users.departament_id')
                                           ->where('users.sef', true)
                                           ->where('users.zile_alocate', '>', 0)
                                           ->where('departaments.departament_id', $user->departament_id)
                                           ->get([
                                            'users.*',
                                           ]);

         /*foreach ($concedii_neacceptate1 as $value) {
           array_push($concedii_sef, $value);
         }*/

         if(count($concedii_neacceptate1)>0){
           (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Concedii neacceptate angajati',
            'Ai '.count($concedii_neacceptate1).' concedii neacceptate de la angajati. <a href="atmscheduler.com">Intra acum pe platforma</a>');
         }


         /*foreach($concedii_neacceptate2 as $value){
           array_push($concedii_sef, $value);
         }*/

         if(count($concedii_neacceptate2)>0){
           (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Concedii neacceptate sefi',
            'Ai '.count($concedii_neacceptate1).' concedii neacceptate de la sefi. <a href="atmscheduler.com">Intra acum pe platforma</a>');
         }

         /*foreach ($cereri_editare_concediu1 as $value) {
           array_push($cereri_editare_concediu, $value);
         }*/

         if(count($cereri_editare_concediu1)>0){
           (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Cereri editare concediu angajati',
            'Ai '.count($cereri_editare_concediu1).' cereri de editare in asteptare. <a href="atmscheduler.com">Intra acum pe platforma</a>');
         }

         /*foreach ($cereri_editare_concediu2 as $value) {
           array_push($cereri_editare_concediu, $value);
         }*/

         if(count($cereri_editare_concediu2)>0){
           (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Cereri editare concediu sefi',
            'Ai '.count($cereri_editare_concediu2).' cereri de editare in asteptare de la sefi. <a href="atmscheduler.com">Intra acum pe platforma</a>');
         }

         /*foreach($angajati_zile_ramase1 as $value){
           array_push($concedii_obligatorii, $value);
         }*/

         if(count($angajati_zile_ramase1)>0){
           (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Angajati cu zile ramase de concediu',
            'Ai '.count($angajati_zile_ramase1).' angajati cu zile de concediu ramase. <a href="atmscheduler.com">Intra acum pe platforma</a>');
         }

         /*foreach($angajati_zile_ramase2 as $value){
           array_push($concedii_obligatorii, $value);
         }*/

         if(count($angajati_zile_ramase2)>0){
           (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Sefi cu zile ramase de concediu',
            'Ai '.count($angajati_zile_ramase2).' sefi cu zile de concediu ramase. <a href="atmscheduler.com">Intra acum pe platforma</a>');
         }
       }

         // DIRECTOR
       $user = User::where('rol', 'director')->first();

       $concedii_sef = \App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
                           ->join('departaments', 'departaments.id', '=', 'users.departament_id')
                           ->where('users.sef', true)
                           ->where('acceptat_inlocuitor', 1)
                           ->whereNull('concedius.acceptat')
                           ->where('concedius.citit', false)
                           ->where('departaments.departament_id', $user->departament_id)
                           ->get([
                            'concedius.*',
                            'users.name',
                           ]);

       if(count($concedii_sef)>0){
         (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Sefi departament cu cereri de concediu',
         'Ai '.count($concedii_sef).' sefi cu cereri de concediu. <a href="atmscheduler.com">Intra acum pe platforma</a>');
       }
       $cereri_editare_concediu = \App\Concediu::join('users', 'users.id', '=', 'concedius.user_id')
                           ->join('departaments', 'departaments.id', '=', 'users.departament_id')
                           ->where('users.sef', true)
                           ->whereNotNull('concedius.acceptat')
                           ->where('concedius.cerere_editare', 1)
                           ->where('departaments.departament_id', $user->departament_id)
                           ->get([
                            'concedius.*',
                            'users.name',
                           ]);
       if(count($cereri_editare_concediu)>0){
         (new \App\Http\Controllers\PublicController)->mail_send($user->email, 'Sefi departament cu cereri de concediu',
           'Ai '.count($cereri_editare_concediu).' sefi cu cereri de editare de concediu. <a href="atmscheduler.com">Intra acum pe platforma</a>');
        }

        return 0;
    }
}
