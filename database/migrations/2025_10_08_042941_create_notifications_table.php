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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('recipient_user_id')->constrained('users'); // FK to users - id_usuario_destinatario
            $table->foreignId('sender_user_id')->nullable()->constrained('users'); // FK to users - id_usuario_remitente
            $table->foreignId('publication_id')->nullable()->constrained('publications'); // FK to publications - id_publicacion
            $table->string('type'); // varchar(255) NOT NULL - tipo
            $table->string('title'); // varchar(255) NOT NULL - titulo
            $table->text('message'); // text NOT NULL - mensaje
            $table->boolean('read')->default(false); // boolean DEFAULT false - leida
            $table->timestamp('created_at')->useCurrent(); // timestamp DEFAULT (now())
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
