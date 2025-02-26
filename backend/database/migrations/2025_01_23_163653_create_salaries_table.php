<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastname');
            $table->foreignId('uap_id')->constrained('uaps')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('formationIPC');
            $table->decimal('tauxPolyvalence',5,2);
            $table->boolean('tauxPolyvalencePotentiel');
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
        Schema::dropIfExists('salaries');
    }
};
