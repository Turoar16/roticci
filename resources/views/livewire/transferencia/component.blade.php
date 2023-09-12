<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
            </div>

            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-bordered table striped mt-1">
                        <thead class="text-white" style="background: #3b3f5c">
                        <tr>
                            <th class="table-th text-white text-center">ACTIONS</th>
                            <th class="table-th text-white text-center">CÓDIGO</th>
                            <th class="table-th text-white text-center">CLIENTE</th>
                            <th class="table-th text-white text-center">NOMBRE</th>
                            <th class="table-th text-white text-center">FECHA</th>
                            <th class="table-th text-white text-center">MONTO</th>
                            <th class="table-th text-white text-center">ESTADO</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $sale)
                        <tr>
                            <td class="text-center">
                                
                                <a href="javascript:void(0)"
                                    wire:click.prevent="Edit({{ $sale->id_ven }})"
                                    class="btn btn-dark mtmobile" title="Editar Venta">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)"
                                    onclick="Cancel('{{$sale->id_ven}}')"
                                    class="btn btn-dark" title="Cancelar Venta">
                                    <i class="fas fa-ban"></i>
                                </a>
                            </td>
                            <td>
                                <h6 class="text-center">{{$sale->id_ven}}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{$sale->clien}}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{$sale->cli_nom}}</h6>
                            </td>
                            <td>
                                
                                <h6 class="text-center">{{$sale->fec}}</h6>
                                
                            </td>
                            <td>
                                <h6 class="text-center">{{ number_format($sale->monto, 2, '.', ',' ) }}</h6>
                            </td>
                            <td>
                                <h6 class="text-center">{{$sale->venes == 7 ? 'Pendiente' : ''}}</h6>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('livewire.editar.form')
    @include('livewire.editar.formeditprod')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('product-added', msg =>{
            $('#theModal').modal('hide');
        });
        window.livewire.on('product-updated', msg =>{
            $('#theModal').modal('hide');
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
        window.livewire.on('hide-modal-product', msg =>{
            $('#theModalProduct').modal('hide');
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

    function Cancel(id, products)
    {
        if (products > 0)
        {
            swal('No se puede eliminar la categoria porque tiene productos relacionados.')
            return;
        }
        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMAS CANCELAR LA VENTA?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#fff',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3B3F5C'
        }).then(function (result) {
            if (result.value){
                window.livewire.emit('cancelSale', id)
                swal.close()
            }
        });
    }
</script>
<style>
    .modal-content {
        max-height: calc(120vh - 200px); /* Ajusta la altura máxima del modal según tus necesidades */
        overflow-y: auto; /* Habilita el desplazamiento vertical */
    }
</style>
@include('livewire.editar.scripts.events')

