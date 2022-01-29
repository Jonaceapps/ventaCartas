<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id');
            $table->string('nombre');
            $table->Integer('cantidad');
            $table->Float('precioTotal');
            $table->unsignedBigInteger('carta_asociada');
            $table->unsignedBigInteger('usuario_asociado');
            $table->foreign('usuario_asociado')->references('id')->on('usuarios');
            $table->foreign('carta_asociada')->references('id')->on('cartas');
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
        Schema::dropIfExists('ventas');
    }
}
