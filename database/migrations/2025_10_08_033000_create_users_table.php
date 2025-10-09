<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // USUARIOS, SESIONES Y RECUPERACION DE CONTRASEÑA
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // int PRIMARY KEY AUTO_INCREMENT
            $table->string('name', 255); // varchar(255) NOT NULL - nombre
            $table->string('first_last_name', 255); // varchar(255) NOT NULL - apellido_paterno
            $table->string('second_last_name', 255)->nullable(); // varchar(255) - apellido_materno
            $table->string('email', 320 )->unique(); // varchar(320) UNIQUE NOT NULL - correo_electronico
            $table->string('phone', 20)->nullable(); // varchar(20) - telefono
            $table->string('username', 255)->unique(); // varchar(255) UNIQUE NOT NULL - usuario
            $table->timestamp('email_verified_at')->nullable(); // timestamp - verificacion email
            $table->string('password'); // varchar(512) NOT NULL - password
            $table->boolean('is_active')->default(true); // boolean DEFAULT true - activo ************
            $table->date('registration_date')->nullable(); // date - fecha_alta
            $table->datetime('last_session')->nullable(); // datetime - ultima_sesion
            $table->foreignId('position_id')->constrained('positions'); // FK to positions - id_cargo
            $table->foreignId('jurisdiction_id')->constrained('jurisdictions'); // FK to jurisdictions - id_jurisdiccion  
            $table->foreignId('role_id')->constrained('roles'); // FK to roles - id_rol
            $table->rememberToken(); // varchar(255) - remember_token
            $table->timestamps(); // created_at, updated_at
        });        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
