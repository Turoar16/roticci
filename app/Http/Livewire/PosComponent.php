<?php

namespace App\Http\Livewire;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Articulo;
use App\Models\Cotizacion;
use App\Models\StockArticulo;
use App\Models\Venta;
use App\Models\ItemVenta;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Darryldecode\Cart\Facades\CartFacade as CartItem;
use DB;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class PosComponent extends Component
{
    use WithPagination;

    public $total, $fecha, $itemsQuantity, $moneda, $cond, $search, $cotizadol, $cotizareal, $cotizape, $searchProduct, $efectivo, $change,  $error_message, $pageTitle, $componentName, $selected_id, $nombre, $ruc, $ventas_id, $searchEdit, $cart=[];
    private $pagination = 7;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
    
    public function mount()
    {
        $this->efectivo = 0;
        $this->error_message = null;
        $this->change = 0;
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();    
        $this->fecha = Carbon::now()->format('d-m-Y');
        $this->pageTitle = 'Listado';
        $this->componentName = 'Listado';
    }

    

    public function render()
    {
        // Buscador de Clentes
        if (strlen($this->search) > 0)
            $clients = Cliente::select('clientes.*')
                        ->where('clientes.cli_nom','like', '%' .$this->search . '%')
                        ->orWhere('clientes.cli_ruc','like', '%' .$this->search . '%')
                        ->orderBy('clientes.cli_nom', 'asc')
                        ->paginate($this->pagination)
            ;
        else
            $clients = Cliente::select('clientes.*')
                ->orderBy('clientes.cli_nom', 'asc')
                ->paginate($this->pagination)
            ;

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
        
        // Buscador de Ventas
        // if (strlen($this->searchEdit) > 0)
        //     $sales = Venta::join('clientes as c', 'c.cli_cod', 'ventas.clien')
        //                 ->select('ventas.*', 'c.cli_nom')
        //                 ->orWhere('ventas.id_ven','like', '%' .$this->searchEdit . '%')
        //                 ->orWhere('ventas.clien','like', '%' .$this->searchEdit . '%')
        //                 ->orWhere('c.cli_nom','like', '%' .$this->searchEdit . '%')
        //                 ->orderBy('ventas.id_ven', 'desc')
        //                 ->paginate($this->pagination)
        //     ;
        // else
            $sales = Venta::join('clientes as c', 'c.cli_cod', 'ventas.clien')
                        ->select('ventas.id_ven','ventas.fec','ventas.clien', 'ventas.monto','ventas.compro', 'ventas.venes', 'c.cli_nom')
                        ->where('ventas.venes','=', 7)
                        ->orderBy('ventas.id_ven', 'desc')
                        ->paginate($this->pagination)
            ;

        // Cotizaciones
        $cotiza = Cotizacion::orderBy('co_id', 'asc')->first();
        $this->cotizadol = $cotiza->co_dol;
        $this->cotizareal = $cotiza->co_rea;
        $this->cotizape = $cotiza->co_pes;
        $this->cart =Cart::getContent()->sortBy('ar_id'); // muestra el carrito de compras al usuario
        return view('livewire.pos.component', [
                //'denominations' => Denomination::orderBy('value', 'desc')->get(),
                // 'cotiza' => Cotizacion::orderBy('co_id', 'asc')->get(),
                // 'cart' => Cart::getContent()->sortBy('ar_id'),
                'data' => $clients,
                'dataProduct' => $products,
                'dataSales' => $sales,
                // 'cotiza'=> $cotiza,
            ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ACash($value)
    {
        $this->efectivo += ($value == 0 ? $this->total : $value);
        $this->change = ($this->efectivo - $this->total);
    }

    protected $listeners = [
        'scan-code' => 'ScanCode',
        'removeItem' => 'removeItem',
        'clearCart' => 'clearCart',
        'saveSale' => 'saveSale'
    ];


    public function ScanCode($barcode, $cant = 1)
    {
        //dd($barcode); //** LLega el barcode OK!!
        $product = Articulo::join('stock_articulos as s', 's.sto_arti', 'ar_id')
                    ->select('articulos.*', 's.sto_uni', 's.sto_arti')
                    ->where('ar_codbar', $barcode)
                    ->where('s.sto_depo','=', 1)
                    ->first();
        if ($product == null || empty($product)){
            $this->emit('scan-notfound', 'El producto no fue encontrado');
        }else{
            if ($this->InCart($product->ar_id))
            {
                $this->increaseQty($product->ar_id);
                return;
            }

            $this->error_message = null; // Initialize error message variable

            // Validar que $cant sea un número entero
            if (!filter_var($cant, FILTER_VALIDATE_INT) || $cant <= 0) {
                $this->error_message = 'La cantidad debe ser un número entero mayor a cero'; // Set error message
                
            }

            if ($this->error_message) {
                $this->emit('no-stock', $this->error_message);
                return;
            }

            // if ($product->sto_uni < 1)
            // {
            //     $this->emit('no-stock', 'Stock insuficiente');
            //     return;
            // }

            Cart::add($product->ar_id, $product->ar_des, $product->ar_preven, $cant);
            /*$carro = Cart::getContent();
            dd($carro);*/
            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();

            $this->emit('scan-ok', 'Producto agregado');
        }
    }

    public function InCart($productId)
    {
        $exist = Cart::get($productId);
        if ($exist)
            return true;
        else
            return false;
    }

    public function increaseQty($productId)
    {
        $item = Cart::get($productId);
        Cart::remove($productId);
        $newQty = ($item->quantity) + 1;
        if ($newQty > 0)
            Cart::add($item->id, $item->name, $item->price, $newQty);

        $this->error_message = null; // Initialize error message variable

        // Validar que $cant sea un número entero
        if (!filter_var($newQty, FILTER_VALIDATE_INT) || $newQty <= 0) {
            $this->error_message = 'La cantidad debe ser un número entero mayor a cero'; // Set error message
            
        }
    
        if ($this->error_message) {
            $this->emit('no-stock', $this->error_message);
            return;
        }
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->emit('scan-ok', 'Cantidad actualizada');

        // $title = '';
        // // $product = Articulo::find($productId);
        // $product = Articulo::join('stock_articulos as s', 's.sto_arti', 'ar_id')
        //             ->select('articulos.*', 's.sto_uni')
        //             ->where('ar_id', $productId)
        //             ->where('s.sto_depo','=', 1)
        //             ->first();
        // $exist = Cart::get($productId);
        // if ($exist)
        //     $title = 'Cantidad actualizada';
        // else
        //     $title = 'Producto agregado';

        // $this->error_message = null; // Initialize error message variable

        // // Validar que $cant sea un número entero
        // if (!filter_var($cant, FILTER_VALIDATE_INT) || $cant <= 0) {
        //     $this->error_message = 'La cantidad debe ser un número entero mayor a cero'; // Set error message
            
        // }
    
        // if ($this->error_message) {
        //     $this->emit('no-stock', $this->error_message);
        //     return;
        // }

        // // if ($product->sto_uni < ($cant + $exist->quantity))
        // // {
        // //     $this->emit('no-stock', 'Stock insuficiente');
        // //     return;
        // // }

        // Cart::add($product->ar_id, $product->ar_des, $product->ar_preven, $cant);
        // $this->total = Cart::getTotal();
        // $this->itemsQuantity = Cart::getTotalQuantity();
        // $this->emit('scan-ok', $title);
    }

    public function updatePrice($productId, $nwprice){
        $title = '';
        // $product = Articulo::find($productId);
        $product = Articulo::join('stock_articulos as s', 's.sto_arti', 'ar_id')
                    ->select('articulos.*', 's.sto_uni', 's.sto_id')
                    ->where('ar_id', $productId)
                    ->where('s.sto_depo','=', 1)
                    ->first();
        $exist = Cart::get($productId);
        if ($exist)
            $title = 'Precio actualizado';

        $this->error_message = null; // Initialize error message variable

        if ($product->ar_premin > $nwprice)
        {
            $this->error_message = 'Precio Venta muy bajo'; // Set error message
        }
    
        if ($this->error_message) {
            $this->emit('no-stock', $this->error_message);
            return;
        }

        Cart::update($product->ar_id, array(
            'price' => $nwprice,
        ));
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->emit('scan-ok', $title);
    }
    public function updateQty($productId, $cant = 1)
    {
        $title = '';
        // $product = Articulo::find($productId);
        $product = Articulo::join('stock_articulos as s', 's.sto_arti', 'ar_id')
                    ->select('articulos.*', 's.sto_uni')
                    ->where('ar_id', $productId)
                    ->where('s.sto_depo','=', 1)
                    ->first();
        $exist = Cart::get($productId);
        if ($exist)
            $title = 'Cantidad actualizada';
        else
            $title = 'Producto agregado';
            
        // if ($exist)
        // {
        //     if ($product->sto_uni < $cant)
        //     {
        //         $this->emit('no-stock', 'Stock insuficiente');
        //         return;
        //     }
        // }
       
        $this->error_message = null; // Initialize error message variable

        // Validar que $cant sea un número entero
        if (!filter_var($cant, FILTER_VALIDATE_INT) || $cant <= 0) {
            $this->error_message = 'La cantidad debe ser un número entero mayor a cero'; // Set error message
            
        }
    
        if ($this->error_message) {
            $this->emit('no-stock', $this->error_message);
            return;
        }

        $this->removeItem($productId);
        if ($cant > 0)
        {
            Cart::add($product->ar_id, $product->ar_des, $product->ar_preven, $cant);
            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();
            $this->emit('scan-ok', $title);
        }else{
            $this->emit('no-stock', 'Cantidad debe ser mayor a cero');
        }
    }

    public function removeItem($productId)
    {
        Cart::remove($productId);
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->emit('scan-ok', 'Producto eliminado');
    }

    public function decreaseQty($productId)
    {
        $item = Cart::get($productId);
        Cart::remove($productId);
        $newQty = ($item->quantity) - 1;
        if ($newQty > 0)
            Cart::add($item->id, $item->name, $item->price, $newQty);

        $this->error_message = null; // Initialize error message variable

        // Validar que $cant sea un número entero
        if (!filter_var($newQty, FILTER_VALIDATE_INT) || $newQty <= 0) {
            $this->error_message = 'La cantidad debe ser un número entero mayor a cero'; // Set error message
            
        }
    
        if ($this->error_message) {
            $this->emit('no-stock', $this->error_message);
            $this->removeItem($productId);
            if ($newQty > 0)
            return;
        }

        
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->emit('scan-ok', 'Cantidad actualizada');
    }

    public function clearCart()
    {
        Cart::clear();
        $this->nombre = ""; 
        $this->ruc = "";
        $this->error_message = null;
        $this->efectivo = 0;
        $this->change = 0;
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->emit('scan-ok', 'Carro vacío');
    }

    public function saveSale()
    {
        //Obtiene id de ventas para la variable publica
        $ventas= Venta::orderBy('id_ven', 'desc')->first();
        $this->ventas_id = $ventas->id_ven;

        $user = Auth()->user()->id;

        $device = User::join('device_user as du', 'du.user_id', 'users.id')
                ->join('devices as d', 'd.id', 'du.device_id')
                ->select('users.id', 'd.device_uuid', 'd.device_type', 'd.updated_at')
                ->where('users.id', $user)
                ->orderBy('d.updated_at','desc')
                ->first();

        if ($this->total <= 0){
            $this->emit('sale-error', 'AGREGA PRODUCTOS A LA VENTA');
            return;
        }
       
        // if ($this->efectivo <= 0)
        // {
        //     $this->emit('sale-error', 'INGRESA EL EFECTIVO');
        //     return;
        // }
        // if ($this->total > $this->efectivo)
        // {
        //     $this->emit('sale-error', 'EL EFECTIVO DEBE SER MAYOR O IGUAL AL TOTAL');
        //     return;
        // }
        DB::beginTransaction();
        try {
            $sale = Venta::create([
                'id_ven' => $this->ventas_id + 1,
                'v_pu' => 2,
                'fec' => $this->fecha,
                'tipcom' => 9,
                'compro' => $this->ventas_id +1,
                'timb' => 0,
                'cond' => 1,
                'venci' => 0,
                'clien' => $this->selected_id,
                'tipven' => 1,
                'mone' => 2,
                'monto' => $this->total,
                'saldo' => $this->total,
                'venes' =>  7,
                'sel' => 0,
                'cajaid' => 0,
                'porce' => 0,
                'reten' => 0,
                've_obs' =>" ",
                've_emp' => 1,
                've_des' => 0,
                've_coti' => 0,
                've_nc' => 0,
                've_cos' => 1,
                'v_u1' => $user,
                'v_m1' => $device->device_type,
                'v_f1' => $this->fecha,
                'v_u2' => 0, //usuario que editó
                'v_m2' => 0, //maquina de cual se editó
                'v_f2' => 0, //fecha de edición
                'v_u3' => 0, //usuario que anulo 
                'v_m3' => 0, //maquina que anulo.
                'v_f3' => 0, //fecha que se anulo
                'v_hor' => Carbon::now()->format('H:m:s'),
                'v_sit' => 7,
                'v_d' => $this->total,
                'v_r' => $this->total * $this->cotizareal,
                'v_p' => $this->total * $this->cotizape,
                'v_g' => $this->total * $this->cotizadol,
            ]);
            if ($sale)
            {
                $items = Cart::getContent();
                foreach ($items as $item) {
                    $preciocosto = Articulo::find($item->id);
                    $id_ids = StockArticulo::where('sto_arti', $item->id)->first();
                    // dd($id_ids);
                    ItemVenta::create([
                        'it_pu' => 2,
                        'acti' => $item->id,
                        'cant' => $item->quantity,
                        'puni' => $item->price,
                        'iva5' => 0,
                        'iva10' => $this->total / 11,
                        'idven' => $this->ventas_id + 1,
                        'dep' => 1,
                        'tipo' => 2,
                        'it_sit' => 1,
                        'it_pcos' => $item->price,
                        'it_por' => 1,
                        'it_des' => 0,
                        'it_subtot' => $this->total,
                        'it_cca' => 0,
                        'it_pca' => 0,
                        'it_pcosc' => 0,
                        'it_v1' => $this->fecha,
                        'it_v2' => 0,
                        'it_ids' => $id_ids->sto_id,
                        
                    ]);

                    //update STOCK
                    // $product = Articulo::find($item->id);
                    $product = StockArticulo::select('sto_id','sto_depo', 'sto_uni', 'sto_pedi')->where('sto_arti', $item->id)->first();
                    
                    // dd($product);
                    $sto_id = $product->sto_id;
                    if ($product->sto_id === $sto_id){

                        $product->sto_uni = $product->sto_uni - $item->quantity;
                        $product->sto_pedi = $product->sto_pedi + $item->quantity;
                        $product->save();
                    }
                }
            }
            DB::commit();

            Cart::clear();
            $this->nombre = ""; 
            $this->ruc = "";
            $this->error_message = null;
            $this->efectivo = 0;
            $this->change = 0;
            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();

            $this->emit('sale-ok', 'Venta registrada con éxito');
            // $this->emit('print-ticket', $sale->id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->emit('sale-error', $e->getMessage());
        }
    }

    public function printTicket($sale)
    {
        return Redirect::To("print://$sale->id");
    }

    public function resetUI() {
        $this->search ='';
        $this->selected_id = 0;
        $this->searchProduct = '';
        $this->nombre = ""; 
        $this->ruc = "";
    }
    public function addClient(Cliente $client)
    {
        $this->selected_id = $client->cli_cod;
        $this->nombre = $client->cli_nom;
        $this->ruc = $client->cli_ruc;

        $this->emit('hide-modal', 'Hide modal');
    }

    public function addProduct(Articulo $articulo)
    {
        $barcode = $articulo->ar_codbar;
        $this->ScanCode($barcode);        
        $this->emit('hide-modal', 'Hide modal');

    }

    public function Edit(Venta $venta)
    {
        // Recuperar datos del itemventa y articulo de la base de datos
        $this->selected_id = $venta->id_ven;
        $cliente = Cliente::find($venta->clien);
        $itemsVenta = ItemVenta::where('idven', $this->selected_id)->get();
        
        
        foreach ($itemsVenta as $item) {
            $articulo = Articulo::find($item->acti);
            $this->cart= [
                'id' => $articulo->acti,
                'name' => $articulo->ar_des,
                'price' => $articulo->ar_preven,
                'quantity' => $item->cant,
            ];
        }
        $this->emit('sale-ok', 'Venta actualizada con éxito');
        $this->emit('hide-modal', 'Hide modal');
        // return redirect(request()->header('Referer'));

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
            'v_f3' => $this->fecha, //fecha que se anulo

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
