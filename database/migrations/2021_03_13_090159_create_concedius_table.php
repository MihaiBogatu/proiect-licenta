<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcediusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concedius', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->unsigned();
			$table->timestamp('data_inceput')->useCurrent();
			$table->timestamp('data_sfarsit')->useCurrent();
      $table->boolean('acceptat')->nullable();
      $table->string('tip_concediu')->default('fara-plata');
      $table->boolean('citit')->default(false);
      $table->integer('inlocuitor_id');
      $table->integer('acceptat_inlocuitor')->nullable();
      $table->boolean('cerere_editare')->default(false);
      $table->boolean('vazut_editare')->default(true);
      $table->string('motiv')->nullable();
      $table->integer('schimb_concediu_id')->nullable();

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
        Schema::dropIfExists('concedius');
    }
}
