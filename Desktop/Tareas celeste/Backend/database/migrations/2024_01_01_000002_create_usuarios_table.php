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
        // IMPORTANTE: Asegúrate de que la migración de 'personas' se ejecute ANTES que esta
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            // id_persona debe ser del mismo tipo que el ID en la tabla personas
            $table->foreignId('id_persona')->constrained('personas')->onDelete('cascade'); 
            $table->string('pass'); 
            $table->boolean('admin')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};