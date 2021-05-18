<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartJsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PublicController@index')->middleware('adminMiddle');

Route::get('/login', 'PublicController@login_get')->middleware('guestMiddle');

Route::post('/login', 'PublicController@login_post')->middleware('guestMiddle');

Route::get('/register', 'PublicController@register_get')->middleware('adminMiddle');

Route::post('/register', 'PublicController@register_post')->middleware('adminMiddle');

Route::get('/logout', 'PublicController@logout_get');

Route::get('/concedii', 'PublicController@concedii_get')->middleware('loggedMiddle')->middleware('notadminMiddle');

Route::post('/concedii', 'PublicController@concedii_post')->middleware('loggedMiddle')->middleware('notadminMiddle');

Route::get('/profile', 'PublicController@profile_get')->middleware('loggedMiddle');

Route::get('/profile/{id}', 'PublicController@other_profile_get')->middleware('directorMiddle');

Route::post('/profile', 'PublicController@profile_post')->middleware('loggedMiddle');

Route::get('/angajati', 'PublicController@angajati_get')->middleware('loggedMiddle');

Route::get('/delete-profile/{id}', 'PublicController@delete_profile_get')->middleware('adminMiddle');

Route::get('/verificare-concedii', 'PublicController@verificare_concedii_get')->middleware('directorMiddle');

Route::get('/acceptare-concediu/{id}', 'PublicController@acceptare_concediu_get')->middleware('loggedMiddle');;

Route::get('/departamente', 'PublicController@departamente_get')->middleware('adminMiddle');

Route::post('/departamente', 'PublicController@departamente_post')->middleware('adminMiddle');

Route::get('/sterge-concediu/{id}', 'PublicController@sterge_concediu_get')->middleware('loggedMiddle');

Route::post('/respingere-concediu/{id}', 'PublicController@respingere_concediu_post')->middleware('loggedMiddle');

Route::get('/gestionare-profil/{id}', 'PublicController@gestionare_profil_get')->middleware('directorMiddle');

Route::post('/gestionare-profil/{id}', 'PublicController@gestionare_profil_post')->middleware('directorMiddle');

Route::get('/schimbare-profil/{id}', 'PublicController@schimbare_profil_get')->middleware('loggedMiddle');

Route::post('/schimbare-profil/{id}', 'PublicController@schimbare_profil_post')->middleware('loggedMiddle');

Route::get('/cereri-inlocuitor', 'PublicController@cereri_inlocuitor_get')->middleware('loggedMiddle');

Route::get('/istoric-concedii', 'PublicController@istoric_concedii_get')->middleware('loggedMiddle');

Route::post('/editeaza-concediu', 'PublicController@editeaza_concediu_post')->middleware('loggedMiddle');

Route::post('/editare-zile/{id}', 'PublicController@editeaza_zile_post')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/concedii-tot', 'PublicController@total_concedii_get')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/concediu-edit/{id}', 'PublicController@concediu_edit_get')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/cerere-edit/{id}', 'PublicController@cerere_edit_get')->middleware('loggedMiddle');

Route::post('/cere-schimb-concediu', 'PublicController@cere_schimb_concediu_post')->middleware('loggedMiddle');

Route::post('/schimb-concediu', 'PublicController@schimb_concediu_post')->middleware('loggedMiddle');

Route::get('/raport/{departament_id}', 'PublicController@raport_get')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/download/{departament_id}', 'PublicController@download_get')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/concediu-obligatoriu/{id}', 'PublicController@concediu_obligatoriu_get')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::post('/concediu-obligatoriu/{id}', 'PublicController@concediu_obligatoriu_post')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/concediu-editare/{id}', 'PublicController@concediu_editare_get')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::post('/concediu-editare/{id}', 'PublicController@concediu_editare_post')->middleware('loggedMiddle')->middleware('directorMiddle');

Route::get('/istoric-departament', 'PublicController@istoric_departament_get')->middleware('loggedMiddle')->middleware('directorMiddle');
