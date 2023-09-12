<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
                <ul class="tab tab-pills">
                    @can('Product_Create')
                    <li>
                        <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-sm" data-toggle="modal" data-target="#theModal">
                            Agregar
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @can('Product_Search')
            @include('common.searchbox')
            @endcan

            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-bordered table striped mt-1">
                        <thead class="text-white" style="background: #3b3f5c">
                        <tr>
                            <th class="table-th text-white text-center">ACTIONS</th>
                            <th class="table-th text-white text-center">CÓDIGO</th>
                            <th class="table-th text-white text-center">BARCODE</th>
                            <th class="table-th text-white text-center">DESCRIPCION</th>
                            <th class="table-th text-white text-center">PRE. VENTA</th>
                            <th class="table-th text-white text-center">CANTIDAD</th>
                            <th class="table-th text-white text-center">UBICACIÓN</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $product)
                        <tr>
                            <td class="text-center">
                                @can('Product_Update')
                                <a href="javascript:void(0)"
                                    wire:click.prevent="Edit({{ $product->ar_id }}, '{{ $product->sto_depo }}')"
                                    class="btn btn-dark mtmobile" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('Product_Destroy')
                                <a href="javascript:void(0)"
                                    onclick="Confirm('{{$product->ar_id}}')"
                                    class="btn btn-dark" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                @endcan
                            </td>
                            <td>
                                <h6 class="text-center">{{$product->ar_codi}}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{$product->ar_codbar}}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{$product->ar_des}}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{ number_format($product->ar_preven, 2, '.', ',' ) }}</h6>
                                
                            </td>
                            <td>
                                <h6 class="text-center">{{$product->sto_uni}}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{ $product->sto_depo == 1 ? 'Local' : 'Deposito' }}</h6>
                            </td>
                            
                        </tr>
                        @endforeach
                        @if ($data->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">No hay resultados</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('livewire.products.form')
    @include('livewire.products.formeditprod')
    @include('livewire.products.formstockprod')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('product-added', msg =>{
            $('#theModal').modal('hide');
        });
        window.livewire.on('product-updated', msg =>{
            $('#theModal').modal('hide');
        });
        window.livewire.on('product-updated', msg =>{
            $('#theModalProduct').modal('hide');
        });
        window.livewire.on('product-updated', msg =>{
            $('#theModalStock').modal('hide');
        });
        window.livewire.on('product-deleted', msg =>{
            //noty
        });
        window.livewire.on('show-modal', msg =>{
            $('#theModal').modal('show');
        });
        window.livewire.on('hide-modal', msg =>{
            $('#theModal').modal('hide');
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
@include('livewire.products.scripts.events')
