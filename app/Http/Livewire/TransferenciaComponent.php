<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Articulo;

class TransferenciaComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name, $action, $obs, $quantity, $motivo, $depoTo, $depoFrom, $defaultValue, $barcode, $cost, $price, $stock, $alerts, $category_id, $search, $image, $selected_id, $pageTitle, $componentName;
    private $pagination = 10;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'Transferencia';
        $this->componentName = 'Listado';
        $this->defaultValue = 1;
        $this->depoTo = 0;
    }
    public function render()
    {
        if (strlen($this->search) > 0)
            $products = Articulo::join('stock_articulos as sa', 'sa.sto_arti', 'ar_id' )
                        ->select('articulos.*', 'sa.sto_depo', 'sa.sto_uni')
                        ->where('articulos.ar_des','like', '%' .$this->search . '%')
                        ->orWhere('articulos.ar_codi','like', '%' .$this->search . '%')
                        ->orderBy('articulos.ar_codi', 'asc')
                        ->paginate($this->pagination)
            ;
        else
            $products = Articulo::join('stock_articulos as sa', 'sa.sto_arti', 'ar_id' )
                ->select('articulos.*', 'sa.sto_depo', 'sa.sto_uni')
                ->orderBy('articulos.ar_des', 'asc')
                ->paginate($this->pagination)
            ;


        return view('livewire.products.component', [
            'data' => $products,
            // 'categories' => Category::orderBy('name', 'asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }
}
