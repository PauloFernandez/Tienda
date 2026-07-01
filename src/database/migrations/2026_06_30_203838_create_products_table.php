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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            //Datos basicos
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable(); //Codigo identificaion comercial
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            //Precios, inventario y estado
            $table->decimal('price', 8,2);
            $table->decimal('cost_price', 8,2)->nullable(); //Precio de compra
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(5); //alserta para avisar cuando quedan pocas unidades

            $table->boolean('status')->default(true);
            $table->boolean('featured')->boolean(false); //para destacar el producto en pagina de inicio

            $table->timestamps();
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
