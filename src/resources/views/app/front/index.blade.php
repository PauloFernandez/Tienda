@extends('layouts.template-front')

@section('contenido')
        <!-- Catálogo de productos -->
    <section class="catalog">
        <h2>Catálogo de Productos</h2>
        <div class="products">
            @foreach ($products as $product)     
            <div class="product-card">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <h3>{{ $product->name }}</h3>
                <p>{{ $product->description }}</p>
                <p class="price">$ {{ $product->price }} </p>
            </div>
            @endforeach
        </div>
        <!-- Paginación -->
        <div class="pagination">
            {{ $products->links() }}
        </div>
    </section>
@endsection