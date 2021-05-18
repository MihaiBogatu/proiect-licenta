<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
      			$table->string('rol');
            $table->integer('departament_id')->nullable();
            $table->boolean('adjunct')->default(false);
            $table->boolean('sef')->default(false);
            $table->string('contact')->default("fara-numar");
            $table->integer('zile_alocate')->default(21);
            $table->boolean('citit_zile')->default(true);
            $table->timestamp('data_resetare_zile')->default(DB::raw('CURRENT_TIMESTAMP'));

            /*$table->string('nume_departament')->nullable();
            $table->string('nume_departament_adjunct')->nullable();
      			$table->integer('sef_dep_id')->nullable();*/
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
