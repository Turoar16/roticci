@include('common.modalEditHead')

@include('common.searchboxEdit')

<div class="widget-content">
    <div class="table-responsive">
        <table class="table table-bordered table striped mt-1">
            <thead class="text-white" style="background: #3b3f5c">
            <tr>
                <th class="table-th text-white text-center">ACTIONS</th>
                <th class="table-th text-white text-center">CÓDIGO</th>
                <th class="table-th text-white text-center">CLIENTE</th>
                <th class="table-th text-white text-center">NOMBRE </th>
                <th class="table-th text-white text-center">FECHA</th>
                <th class="table-th text-white text-center">COMPROBANTE</th>
                <th class="table-th text-white text-center">MONTO</th>
                <th class="table-th text-white text-center">ESTADO</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dataSales as $sale)
            <tr>
                <td class="text-center">
                    <a href="javascript:void(0)"
                        wire:click.prevent="Edit({{$sale->id_ven}})"
                        class="btn btn-dark mtmobile" title="Editar Venta">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="javascript:void(0)"
                        wire:click.prevent="Cancel({{$sale->id_ven}})"
                        class="btn btn-dark mtmobile" title="Anular Venta">
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
                    <h6 class="text-center">{{$sale->compro}}</h6>
                </td>
                <td>
                    <h6 class="text-center">{{$sale->monto}}</h6>
                </td>
                <td>
                    <h6 class="text-center">{{ $sale->venes == 7 ? 'Pendiente' : '' }}</h6>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $dataSales->links() }}
    </div>
</div>
@include('common.modalClientFooter')
