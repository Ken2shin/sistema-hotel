<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('tipo');
            $table->integer('capacidad');
            $table->decimal('precio_noche', 10, 2);
            $table->decimal('precio_fin_semana', 10, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->json('servicios');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('path');
            $table->string('filename');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('rooms');
    }
};
