@include('common.modalClientHead')

@include('common.searchbox')

<div class="widget-content">
    <div class="table-responsive">
        <table class="table table-bordered table striped mt-1">
            <thead class="text-white" style="background: #3b3f5c">
                <tr>
                <th class="table-th text-white text-center">ACTIONS</th>
                <th class="table-th text-white text-center">CÓDIGO</th>
                <th class="table-th text-white text-center">NOMBRE</th>
                <th class="table-th text-white text-center">RUC O C.I.</th>
                <th class="table-th text-white text-center">DIRECCIÓN</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $client)
            <tr>
                <td class="text-center">
                    <a href="javascript:void(0)"
                        wire:click.prevent="addClient({{$client->cli_cod}})"
                        class="btn btn-dark mtmobile" title="Agregar Cliente">
                        <i class="fas fa-user-plus"></i>
                    </a>
                </td>
                <td>
                    <h6 class="text-center">{{$client->cli_cod}}</h6>
                </td>
                <td>
                    <h6 class="text-center">{{$client->cli_nom}}</h6>
                </td>
                <td>
                    <h6 class="text-center">{{$client->cli_ruc}}</h6>
                </td>
                <td>
                    <h6 class="text-center">{{$client->cli_dir}}</h6>
                    
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $data->links() }}
    </div>
</div>
@include('common.modalClientFooter')
