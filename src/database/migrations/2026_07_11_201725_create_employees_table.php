<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla Empleados
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->enum('type_document', ['DNI', 'CI', 'PASSPORT'])->nullable();
            $table->string('number_document', 20)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('position')->nullable();
            $table->date('date_hiring')->nullable();
            $table->decimal('salary', 10,2)->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
