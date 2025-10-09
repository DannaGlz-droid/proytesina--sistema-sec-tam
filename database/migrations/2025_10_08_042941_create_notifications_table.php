<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // NOTIFICACIONES
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('recipient_user_id')->constrained('users'); // FK to users - id_usuario_destinatario
            $table->foreignId('sender_user_id')->nullable()->constrained('users'); // FK to users - id_usuario_remitente
            $table->foreignId('publication_id')->nullable()->constrained('publications'); // FK to publications - id_publicacion
            $table->string('type', 255); // varchar(255) NOT NULL - tipo
            $table->string('title', 255); // varchar(255) NOT NULL - titulo
            $table->text('message'); // text NOT NULL - mensaje
            $table->boolean('read')->default(false); // boolean DEFAULT false - leida
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
