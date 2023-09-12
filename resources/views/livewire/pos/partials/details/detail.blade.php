<div class="connect-sorting">
    <div class="connect-sorting-content">
        <div class="card simple-title-task ui-sortable-handle">
            <div class="card-body">
                <div class="widget-heading mt-3">
                    <ul class="tab tab-pills">
                        <li>
                            <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-md" data-toggle="modal" data-target="#theModalProduct"><i class="fas fa-cart-plus"></i>
                                Agregar Producto
                            </a>
                        </li>
                        <!-- <br>
                        <li>
                            <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-md" data-toggle="modal" data-target="#theModalEdit"><i class="fas fa-ban"></i>
                                Anular Venta
                            </a> -->
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="connect-sorting">
    <div class="connect-sorting-content">
        <div class="card simple-title-task ui-sortable-handle">
            <div class="card-body">
                    @if($total >0)
                        <div class="table-responsive tblscroll" style="max-height: 650px;">
                            <table class="table table-bordered table-striped mt-1">
                                <thead class="text-white" style="background: #3b3f5c">
                                <tr>
                                    <th width="5%" >ACTIONS</th>
                                    <th class="table-th text-center text-white">CANTIDAD</th>
                                    <th class="table-th text-center text-white">CÓDIGO</th>
                                    <th class="table-th text-left text-white">DESCRIPCION</th>
                                    <th class="table-th text-center text-white">PRECIO.VENTA</th>
                                    <th class="table-th text-center text-white">IMPORTE</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cart as $item)

                                    <tr>
                                        <td class="text-center">
                                            <button onclick="Confirm('{{$item->id}}', 'removeItem', '¿CONFIRMAS ELIMNAR EL REGISTRO?')" class="btn btn-dark mbmobile"><i class="fas fa-trash-alt"></i>
                                            </button>
                                            @if ($error_message)
                                                <button wire:click.prevent="decreaseQty({{$item->id}})" class="btn btn-dark mbmobile" disabled>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button wire:click.prevent="increaseQty({{$item->id}})" class="btn btn-dark mbmobile" disabled>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @else
                                                <button wire:click.prevent="decreaseQty({{$item->id}})" class="btn btn-dark mbmobile">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button wire:click.prevent="increaseQty({{$item->id}})" class="btn btn-dark mbmobile">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" id="r{{$item->id}}" wire:change="updateQty({{$item->id}}, $('#r' +{{$item->id}}).val() )"
                                            style="font-size: 1rem!important"
                                            class="form-control text-center"
                                            value="{{$item->quantity}}"
                                            onclick = "if(this.value=='{{$item->quantity}}') this.value=''">
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                // Realizar consulta adicional para obtener otros datos usando $item->id
                                                $otherData = App\Models\Articulo::where('ar_id', $item->id)->first();
                                                ?>
                                            <h6>{{$otherData->ar_codi}}</h6>
                                        </td>
                                        <td><h6>{{$item->name}}</h6></td>
                                        <td>
                                            <input type="text" id="p{{$item->id}}" wire:change="updatePrice({{$item->id}}, $('#p' +{{$item->id}}).val() )"
                                                style="font-size: 1rem!important"
                                                class="form-control text-center"
                                                value="{{number_format($item->price,2)}}"
                                                onclick = "if(this.value=='{{number_format($item->price,2)}}') this.value=''"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <h6>
                                                ${{number_format($item->price * $item->quantity,2)}}
                                            </h6>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <h5 class="text-center text-muted">Agrega Productos A La Venta</h5>
                    @endif

                    <div wire:loading.inline wire:target="saveSale">
                        <h4 class="text-danger text-center">Guardando Venta...</h4>
                    </div>
            </div>
        </div>
    </div>
    @include('livewire.pos.partials.details.form')
    @include('livewire.pos.partials.details.editform')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('product-added', msg =>{
            $('#theModalProduct').modal('hide');
        });
        window.livewire.on('product-updated', msg =>{
            $('#theModalProduct').modal('hide');
        });
        window.livewire.on('product-deleted', msg =>{
            //noty
        });
        window.livewire.on('show-modal', msg =>{
            $('#theModalProduct').modal('show');
        });
        window.livewire.on('hide-modal', msg =>{
            $('#theModalProduct').modal('hide');
        });
        window.livewire.on('hidden.bs.modal', msg =>{
            $('.er').css('display', 'none')
        });

        window.livewire.on('product-added', msg =>{
            $('#theModalEdit').modal('hide');
        });
        window.livewire.on('product-updated', msg =>{
            $('#theModalEdit').modal('hide');
        });
        window.livewire.on('product-deleted', msg =>{
            //noty
        });
        window.livewire.on('show-modal', msg =>{
            $('#theModalEdit').modal('show');
        });
        window.livewire.on('hide-modal', msg =>{
            $('#theModalEdit').modal('hide');
        });
        window.livewire.on('hidden.bs.modal', msg =>{
            $('.er').css('display', 'none')
        });
    });
    function Confirm(id, products)
    {
        if (products > 0)
        {
            swal('No se puede eliminar la categoria porque tiene productos relacionados.')
            return;
        }
        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#fff',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3B3F5C'
        }).then(function (result) {
            if (result.value){
                window.livewire.emit('deleteRow', id)
                swal.close()
            }
        });
    }
</script>
