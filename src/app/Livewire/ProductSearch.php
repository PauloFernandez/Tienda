<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    public $query = "";
    public $category = "";
    public $categories;

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        //Trae la consulta de busqueda
        $products = Product::query()
                    ->when($this->query, function ($query) {
                        $query->where('name', 'like', '%' . $this->query . '%');
                    })
                    ->when($this->category, function ($query) {
                        $query->where('category_id', $this->category);
                    })
                    ->paginate(3);

        return view('livewire.product-search', compact('products'));
    }
}
