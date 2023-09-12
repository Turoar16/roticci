
@include('common.modalProductHead')

@include('common.searchboxProduct')

<div class="widget-content">
    <div class="table-responsive">
        <table class="table table-bordered table striped mt-1">
            <thead class="text-white" style="background: #3b3f5c">
            <tr>
                <th class="table-th text-white text-center">ACTIONS</th>
                <th class="table-th text-white text-center">REFERENCIA</th>
                <th class="table-th text-white text-center">COD. BARRA</th>
                <th class="table-th text-white text-center">DESCRIPCIÓN</th>
                <th class="table-th text-white text-center">PRECIO</th>
                <th class="table-th text-white text-center">CANT.</th>
                <th class="table-th text-white text-center">UBI.</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dataProduct as $product)
            <tr>
                <td class="text-center">
                    <a href="javascript:void(0)"
                        wire:click.prevent="addProduct({{$product->ar_id}})"
                        class="btn btn-dark mtmobile" title="Agregar Producto">
                        <i class="fas fa-cart-plus"></i>
                    </a>
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
                    <h6 class="text-center">{{$product->ar_preven}}</h6>
                    
                </td>
                <td>
                    <h6 class="text-center">{{$product->sto_uni}}</h6>
                </td>
                <td>
                    <h6 class="text-center">{{ $product->sto_depo == 1 ? 'Local' : 'Deposito' }}</h6>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $dataProduct->links() }}
    </div>
</div>
@include('common.modalProductFooter')
