@include('common.modalEditHead')

<div class="row">
    <div class="col-sm-12 col-md-3">
        <div class="form-group">
            <label for="">Fecha</label>
            <input type="text" wire:model.lazy="fecha" class="form-control">
            @error('fecha') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Nombre</label>
            <input type="text" wire:model.lazy="nombre" class="form-control" >
            @error('nombre') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">RUC</label>
            <input type="text"  wire:model.lazy="ruc" class="form-control">
            @error('ruc') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Precio</label>
            <input type="text"  wire:model.lazy="price" class="form-control" placeholder="ej: 0.00">
            @error('price') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="connect-sorting">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="table-responsive tblscroll" style="max-height: 650px; overflow-y: auto;">
                            <div class="table-responsive tblscroll" style="max-height: 650px;">
                                <table class="table table-bordered table-striped mt-1">
                                    <thead class="text-white" style="background: #3b3f5c">
                                    <tr>
                                        <th width="5%">ACTIONS</th>
                                        <th class="table-th text-center text-white">CANTIDAD</th>
                                        <th class="table-th text-center text-white">CÓDIGO</th>
                                        <th class="table-th text-left text-white">DESCRIPCION</th>
                                        <th class="table-th text-center text-white">PRECIO.VENTA</th>
                                        <th class="table-th text-center text-white">IMPORTE</th>
                                    </tr>
                                    </thead>
                                    <ul class="tab tab-pills">
                                        <li>
                                            <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-sm" data-toggle="modal" data-target="#theModalProduct"><i class="fas fa-cart-plus"></i>
                                                Agregar Producto
                                            </a>
                                        </li>
                                    </ul>
                                    <tbody>
                                    @foreach ($itemsVenta as $itemVenta)

                                        <tr>
                                            <td class="text-center">
                                                <button onclick="Confirm('{{$itemVenta['articulo']['ar_id']}}')" class="btn btn-dark mbmobile"><i class="fas fa-trash-alt"></i>
                                                </button>
                                                <button wire:click.prevent="decreaseQty({{ $itemVenta['articulo']['ar_id']}}, 1)" class="btn btn-dark mbmobile">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button wire:click.prevent="increaseQty({{ $itemVenta['articulo']['ar_id']}}, 1)" class="btn btn-dark mbmobile">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input type="text"
                                                    id="r{{$itemVenta['articulo']['ar_id']}}"
                                                    wire:change="updateQty({{$itemVenta['articulo']['ar_id']}}, $('#r' +{{$itemVenta['articulo']['ar_id']}}).val())"
                                                    style="font-size: 1rem!important"
                                                    class="form-control text-center"
                                                    value="{{ intval($itemVenta['item']['cant']) }}"
                                                    onclick="if(this.value=='{{ intval($itemVenta['item']['cant']) }}') this.value=''"
                                                >
                                            </td>
                                            <td class="text-center">
                                            <h6>{{$itemVenta['articulo']['ar_id']}}</h6>
                                            </td>
                                            <td><h6>{{$itemVenta['articulo']['ar_des']}}</h6></td>
                                            <td>
                                                <input type="text" id="p{{$itemVenta['articulo']['ar_id']}}" wire:change="updatePrice({{$itemVenta['articulo']['ar_id']}}, parseFloat($('#p' +{{$itemVenta['articulo']['ar_id']}}).val()))"
                                                    style="font-size: 1rem!important"
                                                    class="form-control text-center"
                                                    value="{{ number_format($itemVenta['item']['puni'], 2, '.', '') }}"
                                                    onclick="if(this.value=='{{ number_format($itemVenta['item']['puni'], 2, '.', '') }}') this.value=''"
                                                >
                                            </td>
                                            <td class="text-center">
                                                <h6>
                                                    ${{number_format($itemVenta['item']['puni'] * $itemVenta['item']['cant'],2, '.', ',')}}
                                                </h6>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div><h6>Total: ${{ $total }}</h6></div>
                    
                    <div wire:loading.inline wire:target="saveSale">
                        <h4 class="text-danger text-center">Guardando Venta...</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('common.modalEditFooter')