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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id()->unique();
            $table->foreignId('salarie_id')->constrained('salaries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('tache_id')->constrained('taches')->onDelete('cascade')->onUpdate('cascade');
            // $table->unique(['salarie_id', 'tache_id']); // Placer après les foreign keys
            $table->char('noteEvaluation',1);
            $table->timestamp('dateEvaluation')->useCurrent()->useCurrentOnUpdate();
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
        Schema::dropIfExists('evaluations');
    }
};
