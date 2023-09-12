<?php

namespace App\Http\Livewire;
use App\Models\Venta;
use App\Models\User;
use App\Models\StockArticulo;
use App\Models\Cliente;
use App\Models\Articulo;
use App\Models\ItemVenta;
use Carbon\Carbon;
use Livewire\WithPagination;

use Livewire\Component;

class EditarVentaComponent extends Component
{
    use WithPagination;

    public $originalStoPedi, $originalStoUni, $removedItems = [], $newItems = [], $tempChanges, $pageTitle, $showEditModal = false, $searchProduct, $itemsVenta = [], $componentName, $total = 0, $ruc, $search, $image, $selected_id, $client, $nombre, $fecha, $comprobante, $monto, $estado, $confirmingItemRemoval = false, $productIdToRemove;
    private $pagination = 7;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'Editar o Anular';
        $this->componentName = 'Pedidos';
    }

    public function render()
    {
        $sales = Venta::join('clientes as c', 'c.cli_cod', 'ventas.clien')
        ->select('ventas.id_ven','ventas.fec','ventas.clien', 'ventas.monto','ventas.compro', 'ventas.venes', 'c.cli_nom')
        ->where('ventas.venes','=', 7)
        ->orderBy('ventas.id_ven', 'desc')
        ->paginate($this->pagination);
        // Buscador de Productos
        if (strlen($this->searchProduct) > 0)
            $products = Articulo::join('stock_articulos as s', 's.sto_arti', 'ar_id')
                        ->select('articulos.*', 's.sto_uni', 's.sto_depo', 's.sto_arti')
                        ->where('articulos.ar_des','like', '%' .$this->searchProduct . '%')
                        ->orWhere('ar_codi','like', '%' .$this->searchProduct . '%')
                        ->orWhere('articulos.ar_codbar','like', '%' .$this->searchProduct . '%')
                        // ->orWhere('s.sto_uni','>',0)
                        ->orderBy('articulos.ar_des', 'asc')
                        ->paginate($this->pagination)
            ;
        else
            $products = Articulo::join('stock_articulos as s', 's.sto_arti', 'ar_id')
                ->select('articulos.*', 's.sto_uni','s.sto_depo', 's.sto_arti')
                ->where('s.sto_uni','>',0)
                ->where('s.sto_depo','=',1)
                ->orderBy('articulos.ar_des', 'asc')
                ->paginate($this->pagination)
            ;
        return view('livewire.editar.component', [
            'data' => $sales,
            'dataProduct' => $products,
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Edit(Venta $venta)
    {   
        // Reiniciar el arreglo $itemsVenta
        $this->itemsVenta = [];
        $this->total = 0;
        // Recuperar datos del itemventa y articulo de la base de datos
        $this->selected_id = $venta->id_ven;
        $cliente = Cliente::find($venta->clien);
        $itemsVenta = ItemVenta::where('idven', $this->selected_id)->get();
        
        $this->client = $venta->clien;
        $this->fecha = $venta->fec;
        $this->nombre = $cliente->cli_nom;
        $this->ruc = $cliente->cli_ruc;

        
        
        foreach ($itemsVenta as $item) {
            $articulo = Articulo::find($item->acti);
            $this->itemsVenta[] = [
                'item' => $item,
                'articulo' => $articulo,
                'cantInicial' => $item->cant, // Agregar la cantidad inicial del artículo al arreglo
            ];
            // Recalcular el total
            $this->calculateTotal();
        }
        // $this->emit('sale-ok', 'Venta actualizada con éxito');
        $this->emit('show-modal', 'Show modal');
        // return redirect(request()->header('Referer'));
    }

    public function resetUI() {
        $this->search ='';
        $this->selected_id = 0;
        $this->itemsVenta = [];
        $this->nombre = ""; 
        $this->ruc = "";
        $this->total;   
        $this->newItems = [];
    }

    public function updateQty($productId, $cant = 1)
    {
        // Buscar el artículo en $this->itemsVenta
        foreach ($this->itemsVenta as &$itemVenta) {
            if (isset($itemVenta['item']) && isset($itemVenta['articulo'])) {
                $item = &$itemVenta['item'];
                $articulo = &$itemVenta['articulo'];
                if ($articulo['ar_id'] == $productId) {
                    // Actualizar la cantidad del artículo
                    $item['cant'] = $cant;
                    break;
                }
            }
        }
    
        // Recalcular el total
        $this->calculateTotal();
    
        // Emitir un evento si es necesario
        $this->emit('scan-ok', 'Cantidad actualizada');
    }

    public function updatePrice($productId, $nwprice)
    {
        // Buscar el artículo en $this->itemsVenta
        // $id_itven = $this->itemsVenta[0]['item']['id_itven'];
        // dd($id_itven);
        foreach ($this->itemsVenta as &$itemVenta) {
            if (isset($itemVenta['item']['id_itven'])) {
                $id_itven = $itemVenta['item']['id_itven'];
                // Hacer algo con $id_itven
            } 
            if (isset($itemVenta['item']) && isset($itemVenta['articulo'])) {
                $item = &$itemVenta['item'];
                $articulo = &$itemVenta['articulo'];
                if ($articulo['ar_id'] == $productId) {
                    // Actualizar el precio de venta del artículo
                    $item['puni'] = $nwprice;
                    $item['it_pcos'] = $nwprice;

                    // dd($item);
                    // Guardar el cambio en la base de datos
                    $itemVentaModel = ItemVenta::find($id_itven);
                    $itemVentaModel->puni = $nwprice;
                    $itemVentaModel->it_pcos = $nwprice;
                    $itemVentaModel->save();         
                    break;
                }
            } else {
                // La clave 'item' o 'articulo' no existe en el elemento actual
            }
        }

        // Recalcular el total
        $this->calculateTotal();

        // Emitir un evento si es necesario
        $this->emit('scan-ok', 'Precio actualizado');
    }
    
    public function calculateTotal()
    {
        $this->total = 0;
        
        foreach ($this->itemsVenta as $itemVenta) {
            $subtotal = $itemVenta['item']['cant'] * $itemVenta['item']['puni'];
            $this->total += $subtotal;
        }
    }
    
    public function increaseQty($productId, $cant = 1)
    {
        // Buscar el artículo en $this->itemsVenta
        foreach ($this->itemsVenta as &$itemVenta) {
            if (isset($itemVenta['item']) && isset($itemVenta['articulo'])) {
                $item = &$itemVenta['item'];
                $articulo = &$itemVenta['articulo'];
                if ($articulo['ar_id'] == $productId) {
                    // Actualizar la cantidad del artículo
                    $item['cant'] += $cant;
                    break;
                }
            }
        }
    
        // Recalcular el total
        $this->calculateTotal();
    
        // Emitir un evento si es necesario
        $this->emit('scan-ok', 'Cantidad actualizada');
    }

    public function decreaseQty($productId, $cant = 1)
    {
        // Buscar el artículo en $this->itemsVenta
        foreach ($this->itemsVenta as &$itemVenta) {
            if (isset($itemVenta['item']) && isset($itemVenta['articulo'])) {
                $item = &$itemVenta['item'];
                $cantOriginal = $item['cant'];
                $articulo = &$itemVenta['articulo'];
                if ($articulo['ar_id'] == $productId) {
                    // Actualizar la cantidad del artículo
                    $item['cant'] -= $cant;
                    // // Verificar si la cantidad disminuyó a cero
                    // if ($item['cant'] <= 0) {
                    //     // Actualizar el stock en la base de datos
                    //     $stockArticulo = StockArticulo::where('sto_arti', $articulo)->first();
                    //     if ($stockArticulo) {
                    //         $stockArticulo->sto_uni += $cantOriginal;
                    //         $stockArticulo->sto_pedi -= $cantOriginal;
                    //         $stockArticulo->save();
                    //     }
                    //     $this->removeItem($productId);
                    // }
                    
                    break;
                }
            }
        }
    
        // Recalcular el total
        $this->calculateTotal();
    
        // Emitir un evento si es necesario
        $this->emit('scan-ok', 'Cantidad actualizada');
    }

    protected $listeners = [
        'scan-code' => 'ScanCode',
        'deleteRow' => 'removeItem',
        'saveSale' => 'Update',
        'cancelSale' => 'Cancel',
    ];

    public function ScanCode($barcode, $cant = 1)
    {
        $product = Articulo::join('stock_articulos as s', 's.sto_arti', 'articulos.ar_id')
            ->select('articulos.*', 's.sto_uni')
            ->where('articulos.ar_codbar', $barcode)
            ->where('s.sto_depo', '=', 1)
            ->first();

        if ($product == null || empty($product)) {
            $this->emit('scan-notfound', 'El producto no fue encontrado');
        } else {
            if ($this->InCart($product->ar_id)) {
                $this->increaseQty($product->ar_id);
                $this->emit('hide-modal-product', 'Hide modal product');
                return;
            }

            // if ($product->sto_uni < 1) {
            //     $this->emit('no-stock', 'Stock insuficiente');
            //     return;
            // }

            $itemVenta = [
                'item' => [
                    'cant' => $cant,
                    'puni' =>  $product->ar_preven,
                    'it_pcos' =>  $product->ar_preven,
                ],
                'articulo' => [
                    'ar_id' => $product->ar_id,
                    'ar_des' => $product->ar_des,
                    'ar_preven' => $product->ar_preven,
                    'ar_pre' => $product->ar_pre,
                ],
            ];

            $this->itemsVenta[] = $itemVenta;

            $this->calculateTotal();
            $this->emit('scan-ok', 'Producto agregado');
            $this->emit('hide-modal-product', 'Hide modal product');
        }
    }

    public function InCart($productId)
    {
        foreach ($this->itemsVenta as $itemVenta) {
            if ($itemVenta['articulo']['ar_id'] == $productId) {
                return true;
            }
        }
        return false;
    }

    public function addProduct($articuloId)
    {
        $barcode = Articulo::where('ar_id', $articuloId)->value('ar_codbar');
        $this->ScanCode($barcode);

        // Marcar el artículo como nuevo
        $newItem = end($this->itemsVenta);
        $newItem['nuevo'] = true; // Agregar la bandera 'nuevo'
        $this->newItems[] = $newItem;
    }

    public function removeItem($productId)
    {
        // Buscar el artículo en $this->itemsVenta
        foreach ($this->itemsVenta as $key => $itemVenta) {
            if (isset($itemVenta['articulo']) && $itemVenta['articulo']['ar_id'] == $productId) {
                $removedItem = $this->itemsVenta[$key];
                unset($this->itemsVenta[$key]);
                
                // Verificar si el artículo es nuevo
                if (in_array(true, $this->newItems)) {
                    // El artículo es nuevo, realizar acciones específicas
                    // Restaurar las cantidades originales de sto_uni y sto_pedi
                    dd("hola soy un articulo nuevo");
                    // $articuloId = $removedItem['articulo']['ar_id'];
                    // $cantidadEliminada = $removedItem['item']['cant'];
                    // $stockArticulo = StockArticulo::where('sto_arti', $articuloId)->first();
                    // if ($stockArticulo) {
                    //     $stockArticulo->sto_uni -= $cantidadEliminada;
                    //     $stockArticulo->sto_pedi += $cantidadEliminada;
                    //     $stockArticulo->save();
                    // }
                } else {
                    // El artículo no es nuevo, tomar acciones para eliminarlo permanentemente
                    $this->permanentRemoveItem($removedItem);
                    dd("hola soy un articulo viejo");
                }

                break;
            }
        }

        // Recalcular el total
        $this->calculateTotal();

        // Emitir un evento si es necesario
        $this->emit('scan-ok', 'Producto eliminado');
    }

    // Función para eliminar permanentemente o temporalmente un artículo
    private function permanentRemoveItem($removedItem)
    {
        // Obtener detalles del artículo
        $articuloId = $removedItem['articulo']['ar_id'];
        $cantidadEliminada = $removedItem['item']['cant'];

        // Realizar operaciones de eliminación permanente en la base de datos
        // Esto debe ajustar las cantidades en sto_uni y sto_pedi

        // Actualizar el stock del artículo en la base de datos
        $stockArticulo = StockArticulo::where('sto_arti', $articuloId)->first();
        if ($stockArticulo) {
            // Restar la cantidad eliminada de sto_uni y sto_pedi
            $stockArticulo->sto_uni += $cantidadEliminada;
            $stockArticulo->sto_pedi -= $cantidadEliminada;
            $stockArticulo->save();
        }

        // Eliminar el artículo de la base de datos
        $itemIdVenta = $removedItem['item']['id_itven'] ?? null;
        if ($itemIdVenta) {
            ItemVenta::where('acti', $articuloId)
                ->where('id_itven', $itemIdVenta)
                ->delete();
        }
    }

    public function Update()
    {
        $user = Auth()->user()->id;

        $device = User::join('device_user as du', 'du.user_id', 'users.id')
                ->join('devices as d', 'd.id', 'du.device_id')
                ->select('users.id', 'd.device_uuid', 'd.device_type', 'd.updated_at')
                ->where('users.id', $user)
                ->orderBy('d.updated_at','desc')
                ->first();

        // Actualizar la tabla "ventas"
        $venta = Venta::find($this->selected_id);
        $venta->v_u2 = $user;
        $venta->v_m2 = $device->device_type;
        $venta->v_f2 = Carbon::now()->format('d-m-Y');
        $venta->monto = $this->total;
        $venta->saldo = $this->total;
        $venta->save();

        foreach ($this->itemsVenta as $itemVenta) {
            if (isset($itemVenta['item']['id_itven'])) {
                
                $item = ItemVenta::find($itemVenta['item']['id_itven']);
                $item->cant = $itemVenta['item']['cant'];
                $item->puni = $itemVenta['item']['puni'];
                $item->it_pcos = $itemVenta['item']['it_pcos'];
                $item->iva10 = $itemVenta['item']['iva10'];
                $item->it_subtot = $this->total;
                // ...
                $item->save();
                
            }else {
                // Agregar nuevo item
                $id_ids = StockArticulo::where('sto_arti', $itemVenta['articulo']['ar_id'])->first();
                // dd($id_ids->sto_id);
                $nuevoItem = new ItemVenta();
                $nuevoItem->it_pu = 2;
                $nuevoItem->acti = $itemVenta['articulo']['ar_id'];
                $nuevoItem->cant = $itemVenta['item']['cant'];
                $nuevoItem->puni = $itemVenta['item']['puni'];
                $nuevoItem->iva5 = 0;
                $nuevoItem->iva10 = $this->total / 11;
                $nuevoItem->idven = $this->selected_id;
                $nuevoItem->dep = 1;
                $nuevoItem->tipo = 2;
                $nuevoItem->it_sit = 1;
                $nuevoItem->it_pcos = $itemVenta['item']['it_pcos'];
                $nuevoItem->it_por = 1;
                $nuevoItem->it_des = 0;
                $nuevoItem->it_subtot = $this->total;
                $nuevoItem->it_cca = 0;
                $nuevoItem->it_pca = 0;
                $nuevoItem->it_pcosc =0;
                $nuevoItem->it_v1 = Carbon::now()->format('d-m-Y');
                $nuevoItem->it_v2 = 0;
                $nuevoItem->it_ids = $id_ids->sto_id;
                // ...
                $nuevoItem->save();
            }
            

        }
        foreach ($this->itemsVenta as $itemVenta) {
            if (isset($itemVenta['item']['id_itven'])) {
                // Actualizar el item existente
                $item = ItemVenta::find($itemVenta['item']['id_itven']);
                $item->cant = $itemVenta['item']['cant'];
                // ...

                // Calcular la diferencia entre la cantidad inicial y la cantidad final
                $diferenciaCantidad = $itemVenta['cantInicial'] - $itemVenta['item']['cant'];
                // dd($diferenciaCantidad);
                
                // Verificar si se agregó o disminuyó la cantidad y tomar las acciones necesarias en "StockArticulo"
                if ($diferenciaCantidad > 0) {
                    // La cantidad se disminuyó, entonces actualizar "StockArticulo" restando la diferencia a "sto_uni" y sumando la diferencia a "sto_pedi"
                    $stockArticulo = StockArticulo::where('sto_arti', $itemVenta['articulo']['ar_id'])->first();
                    if ($stockArticulo) {
                        $stockArticulo->sto_uni += $diferenciaCantidad;
                        $stockArticulo->sto_pedi -= $diferenciaCantidad;
                        $stockArticulo->save();
                    }
                } elseif ($diferenciaCantidad < 0) {
                    // La cantidad se agregó, entonces actualizar "StockArticulo" sumando la diferencia a "sto_uni" y restando la diferencia a "sto_pedi"
                    $stockArticulo = StockArticulo::where('sto_arti', $itemVenta['articulo']['ar_id'])->first();
                    if ($stockArticulo) {
                        $stockArticulo->sto_uni -= abs($diferenciaCantidad); // Sumar el valor absoluto de la diferencia
                        $stockArticulo->sto_pedi += abs($diferenciaCantidad); // Restar el valor absoluto de la diferencia
                        $stockArticulo->save();
                    }
                }

                // Guardar el item actualizado
                $item->save();
            } else {
                // Agregamos el articulo en el caso que sea un item nuevo
                $stockArticulo = StockArticulo::where('sto_arti', $itemVenta['articulo']['ar_id'])->first();
                if ($stockArticulo) {
                        $stockArticulo->sto_uni = $stockArticulo->sto_uni - $itemVenta['item']['cant'];
                        $stockArticulo->sto_pedi = $stockArticulo->sto_pedi + $itemVenta['item']['cant'];
                        $stockArticulo->save();
                    }
            }
        }
        // Limpiar los datos del formulario o reiniciar las variables necesarias
        $this->resetUI(); // Puedes implementar esta función según tus necesidades

        // Recalcular el total
        $this->calculateTotal();

        // Emitir un mensaje o realizar otras acciones después de la actualización
        $this->emit('product-updated', 'Hide modal');
        $this->emit('sale-ok', '¡Venta actualizada exitosamente!');
    }

    public function Cancel(Venta $venta){

        $user = Auth()->user()->id;

        $device = User::join('device_user as du', 'du.user_id', 'users.id')
                ->join('devices as d', 'd.id', 'du.device_id')
                ->select('users.id', 'd.device_uuid', 'd.device_type', 'd.updated_at')
                ->where('users.id', $user)
                ->orderBy('d.updated_at','desc')
                ->first();

        $anulVen = Venta::find($venta->id_ven);

        $anulVen->update([
            'venes'        => 3,
            'id_ven' => $venta->id_ven,
            'v_u3' => $user, //usuario que anulo 
            'v_m3' => $device->device_type, //maquina que anulo.
            'v_f3' => Carbon::now()->format('d-m-Y'), //fecha que se anulo

        ]);
        if ($anulVen)
            {
                
                $anulItven = ItemVenta::where('idven', $venta->id_ven)->get();
        
                foreach ($anulItven as $detalle) {
                    $stock = StockArticulo::where('sto_arti', $detalle->acti)->first();
                    $stock->sto_uni += $detalle->cant;
                    $stock->sto_pedi -= $detalle->cant;
                    $stock->save();
                
                    $detalle->update([
                        'it_sit' => 2,
                    ]);
                }

            }

        $this->emit('sale-ok', 'Venta anulada con éxito');
        $this->emit('hide-modal', 'Hide modal');
    }
}
