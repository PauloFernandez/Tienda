<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla Producto
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->enum('gender', ['HOMBRE', 'MUJER', 'UNISEX', 'NIÑO', 'NIÑA'])->default('UNISEX');
            $table->string('material')->nullable();
            $table->boolean('outstanding')->default(false);
            $table->boolean('active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['active', 'outstanding']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
