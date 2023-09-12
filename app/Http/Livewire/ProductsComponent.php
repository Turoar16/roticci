<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Articulo;
use App\Models\StockArticulo;
use App\Models\AjusteStock;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Transferencia;
use App\Models\ItemTransferencia;


class ProductsComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name, $ref, $action, $obs, $quantity, $motivo, $depoTo, $depoFrom, $defaultValue, $barcode, $cost, $price, $stock, $alerts, $category_id, $search, $image, $selected_id, $pageTitle, $componentName;
    private $pagination = 10;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'Stock';
        $this->componentName = 'Productos';
        $this->defaultValue = 0;
        $this->depoTo = 0;
    }
    public function render()
    {
        $query = Articulo::join('stock_articulos as sa', 'sa.sto_arti', 'ar_id')
                    ->select('articulos.*', 'sa.sto_depo', 'sa.sto_uni');
    
        if (strlen($this->search) > 0) {
            $query->where(function ($query) {
                $query->where('articulos.ar_des', 'like', '%' . $this->search . '%')
                    ->orWhere('articulos.ar_codi', 'like', '%' . $this->search . '%');
            });
            // Forzar la página a 1 después de la búsqueda
            $this->resetPage();
        }
    
        $products = $query->orderBy('articulos.ar_codi', 'asc')
                    ->paginate($this->pagination);
    
        // Obtener la página correcta para mostrar los resultados de búsqueda
        $pageNumber = $products->currentPage();
    
        // Si no hay resultados en la página actual y hay más de una página, buscar la página que contiene los resultados
        if ($products->isEmpty() && $products->lastPage() > 1) {
            $pageNumber = $query->paginate($this->pagination, ['*'], 'page')->lastPage();
            $products = $query->paginate($this->pagination, ['*'], 'page', $pageNumber);
        }
    
        return view('livewire.products.component', [
            'data' => $products,
        ])
        ->extends('layouts.theme.app')
        ->section('content');
        
    }

    public function Store()
    {
        $rules = [
            'name'        => 'required|unique:products|min:3',
            'cost'        => 'required',
            'price'       => 'required',
            'stock'       => 'required',
            'alerts'      => 'required',
            'category_id' => 'required|not_in:Seleccione',
        ];
        $messages =[
            'name.required' => 'Nombre del producto es requerido',
            'name.unique' => 'Ya existe el nombre del producto',
            'name.min' => 'El nombre del producto debe tener al menos 3 caracteres.',
            'cost.required' => 'Costo es requerido',
            'price.required' => 'Precio es requerido',
            'stock.required' => 'Stock es requerido',
            'alerts.required' => 'Ingresa el valor mínimo de existencias',
            'category_id.not_in' => 'Elige un nombre de categoría',
        ];

        $this->validate($rules, $messages);

        $product = Articulo::create([
            'name'        => $this->name,
            'cost'        => $this->cost,
            'price'       => $this->price,
            'barcode'     => $this->barcode,
            'stock'       => $this->stock,
            'alerts'      => $this->alerts,
            'category_id' => $this->category_id
        ]);

        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('/public/products', $customFileName);
            $product->image = $customFileName;
            $product->save();
        }
        $this->resetUI();
        $this->emit('product-added', 'Producto Registrado');
    }

    public function Edit(Articulo $product, $depo)
    {   
        $stock = StockArticulo::select('sto_depo', 'sto_uni')->where('sto_arti', $product->ar_id)->where('sto_depo', $depo)->first();
        // dd($stock);
        $this->selected_id = $product->ar_id;
        $this->name = $product->ar_des;
        $this->barcode = $product->ar_codbar;
        $this->cost = $product->ar_pre;
        $this->price = $product->ar_preven;
        $this->ref = $product->ar_codi;
        $this->alerts = $stock->sto_uni;
        $this->stock = $stock->sto_depo;
        $this->image = null;

        $this->emit('show-modal', 'Show modal');
    }

    public function Update()
    {
        $rules = [
            'name'        => "required|min:3|unique:products,name,{$this->selected_id}",
            'cost'        => 'required',
            'price'       => 'required',
            'stock'       => 'required',
            'alerts'      => 'required',
        ];
        $messages =[
            'name.required' => 'Nombre del producto es requerido',
            'name.unique' => 'Ya existe el nombre del producto',
            'name.min' => 'El nombre del producto debe tener al menos 3 caracteres.',
            'cost.required' => 'Costo es requerido',
            'price.required' => 'Precio es requerido',
            'stock.required' => 'Stock es requerido',
            'alerts.required' => 'Ingresa el valor mínimo de existencias',
        ];

        $this->validate($rules, $messages);

        $product = Articulo::find($this->selected_id);

        $product->update([
            'name'        => $this->name,
            'cost'        => $this->cost,
            'price'       => $this->price,
            'barcode'     => $this->barcode,
            'stock'       => $this->stock,
        ]);

        $stock = StockArticulo::where('sto_arti', $this->selected_id)->where('sto_depo', $this->stock)->first();
        if ($stock) {
            $stock->sto_uni = $this->alerts;
            $stock->save();
        }
        
        $this->resetUI();
        $this->emit('product-updated', 'Cantidad Actualizada');
    }

    public function moveStock()
    {
        // Validar que el depósito de origen y el depósito de destino sean diferentes
        if ($this->stock == $this->depoTo) {
            $this->emit('product-updated', 'Seleccione un deposito distinto');
            return;
        }

        // Obtener los registros de stock para el depósito de origen y el depósito de destino
        $stockFrom = StockArticulo::where('sto_arti', $this->selected_id)->where('sto_depo', $this->stock)->first();
        $stockTo = StockArticulo::where('sto_arti', $this->selected_id)->where('sto_depo', $this->depoTo)->first();

        // Validar que el artículo de origen exista en el depósito de destino
        if ($stockTo === null) {

            $this->emit('product-updated', 'El artículo no existe en el depósito de destino');
            return;
        }

        // Validar que haya suficiente stock en el depósito de origen
        if ($stockFrom->sto_uni <= $this->defaultValue) {
            
            $this->emit('product-updated', 'El stock disponible es menor a la cantidad deseada');
            return;

        } else{
            
            // Actualizar las cantidades en los depósitos correspondientes
            $stockFrom->sto_uni -= $this->defaultValue;
            $stockTo->sto_uni += $this->defaultValue;

            // Guardar los cambios en la base de datos
            $stockFrom->save();
            $stockTo->save();
        }

        //Obtén el último registro de la tabla 'Ajuste_stock' ordenando por la columna 'aju_cod' de manera descendente
        $ultimoMovimiento = Transferencia::orderBy('id_tra', 'desc')->first();
        // Crear un registro de transferencia en la tabla 'transferencia'
        $transferencia = new Transferencia();
        // Agrega los campos necesarios a la transferencia
        $transferencia->id_tra = $ultimoMovimiento->id_tra + 1;
        $transferencia->t_pu = '1';
        $transferencia->ori = '1';
        $transferencia->des = '2';
        $transferencia->fec = Carbon::now()->format('d-m-Y');
        $transferencia->est = '1';
        $transferencia->tran_obs = $this->obs;
        // Guardar la transferencia en la base de datos
        $transferencia->save();

        // Crear un registro en la tabla 'item_transferencia' para el depósito de origen
        $itemTransferenciaFrom = new ItemTransferencia();
        $itemTransferenciaFrom->it_pu = '1';
        $itemTransferenciaFrom->num = $transferencia->id_tra;
        $itemTransferenciaFrom->art = $this->selected_id;
        $itemTransferenciaFrom->cant = $this->defaultValue;
        // Guardar el item de transferencia en la base de datos
        $itemTransferenciaFrom->save();

        // Restablecer las propiedades a sus valores iniciales
        $this->reset();
        $this->emit('product-updated', 'Movimiento de Stock exitoso');
    }
    
    public function ajusteStock()
    {
        $this->validate([
            'motivo' => 'required',
            'selected_id' => 'required',
            'alerts' => 'required|numeric',
            'quantity' => 'required|numeric',
            'action' => 'required',
            'obs' => 'nullable',
        ]);

        $stock = StockArticulo::where('sto_arti', $this->selected_id)->where('sto_depo', $this->stock)->first();

        if ($stock) {
            if ($this->action == 1) {
                // Aumentar la cantidad de stock
                $stock->sto_uni += $this->quantity;
            } elseif ($this->action == 6) {
                // Disminuir la cantidad de stock
                $stock->sto_uni -= $this->quantity;
            }

            $stock->save();
            //Obtén el último registro de la tabla 'Ajuste_stock' ordenando por la columna 'aju_cod' de manera descendente
            $ultimoAjuste = AjusteStock::orderBy('aju_cod', 'desc')->first();
            // Crear un registro en AjusteStock
            $ajusteStock = new AjusteStock();
            $ajusteStock->aju_cod = $ultimoAjuste->aju_cod + 1;
            $ajusteStock->aju_pu = 1;
            $ajusteStock->aju_fec = Carbon::now()->format('d-m-Y');
            $ajusteStock->aju_ope = 2;
            $ajusteStock->aju_moti = $this->motivo;
            $ajusteStock->aju_arti = $this->selected_id;
            $ajusteStock->aju_dep = $this->stock;
            $ajusteStock->aju_cant = $this->quantity;
            $ajusteStock->aju_obs = $this->obs;
            $ajusteStock->aju_est = 1;
            $ajusteStock->aju_afe = 1;
            $ajusteStock->aju_venci = '1900-01-01';
            $ajusteStock->aju_cpc = 1;
            $ajusteStock->save();

            // Limpiar los datos del formulario o reiniciar las variables necesarias
            $this->reset();
            
            // Emitir un mensaje o realizar otras acciones después del ajuste de stock
            $this->emit('product-updated', '¡Ajuste de stock realizado exitosamente!');
        }
    }


    public function resetUI() {
        $this->name ='';
        $this->barcode ='';
        $this->cost ='';
        $this->price ='';
        $this->stock ='';
        $this->alerts ='';
        $this->category_id ='Seleccione';
        $this->image =null;
        $this->search ='';
        $this->selected_id = 0;
    }

    protected $listeners = [
        'deleteRow' => 'Destroy',
    ];

    public function Destroy(Articulo $product)
    {
        $imageTemp = $product->image;
        $product->delete();

        if ($imageTemp != null) {
            if (file_exists('storage/products/' . $imageTemp)) {
                unlink('storage/products/' . $imageTemp);
            }
        }

        $this->resetUI();
        $this->emit('product-deleted', 'Producto Eliminado');

    }
}
