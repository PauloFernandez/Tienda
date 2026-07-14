<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla Clientes
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('last_name');
            $table->enum('type_document', ['DNI', 'CI', 'PASSPORT'])->nullable();
            $table->string('number_document', 20)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['M', 'F', 'other'])->nullable();
            $table->string('avatar')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
