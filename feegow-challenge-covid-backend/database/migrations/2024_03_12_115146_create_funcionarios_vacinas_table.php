<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('funcionarios_vacinas', function (Blueprint $table) {
            $table->foreignId('funcionario_cpf')->constrained('funcionarios')->onDelete('cascade');
            $table->foreignId('vacina_id')->constrained('vacinas')->onDelete('cascade');
            $table->date('data_dose');
            $table->integer('dose');
            $table->primary(['funcionario_cpf', 'vacina_id', 'dose']);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios_vacinas');
    }
};
