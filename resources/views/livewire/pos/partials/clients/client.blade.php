<div class="connect-sorting">
    <div class="connect-sorting-content">
        <div class="card simple-title-task ui-sortable-handle">
            <div class="card-body">
                <div class="widget-heading mt-3">
                    <ul class="tab tab-pills">
                        <li>
                            <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-md" data-toggle="modal" data-target="#theModal"  ><i class="fas fa-address-book"></i>
                                Agregar Cliente
                            </a>
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
                @if($ruc != "")
                    <div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
                        <div class="row">
                            <div class="col-sm-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <input type="text" wire:model.lazy="fecha" class="form-control" disabled>
                                    @error('fecha') <span class="text-danger er">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Condición</label>
                                    <select wire:model.lazy="cond" class="form-control" id="cond" disabled>
                                        <option value="N" selected>Contado </option>
                                        <option value="S" selected>Crédito</option>
                                    </select>
                                    @error('cond') <span class="text-danger er">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            
                            <div class="col-sm-12 col-md-4">
                                <div wire:ignore class="form-group">
                                    <label for="">RUC o C.I.</label>
                                    <input type="text" wire:model.lazy="ruc" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Nombre o Razón Social</label>
                                    <input type="text" wire:model.lazy="nombre" class="form-control" disabled>
                                    @error('nombre') <span class="text-danger er">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Moneda</label>
                                    <select wire:model.lazy="moneda" class="form-control" id="moneda" disabled>
                                        <option value="2" selected>Dólares</option>
                                        <option value="1" selected>Guaraní</option>
                                    </select>
                                    @error('moneda') <span class="text-danger er">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Cotización</label>
                                    <input type="text"  wire:model.lazy="cotizadol" class="form-control" placeholder="ej: 0.00" disabled>
                                    @error('cotizadol') <span class="text-danger er">{{ $message }}</span>@enderror
                                </div>
                            </div>                            
                        </div>
                    </div>
                @else
                    <h5 class="text-center text-muted">Agrega Cliente A La Venta</h5>
                @endif

                <div wire:loading.inline wire:target="saveSale">
                    <h4 class="text-danger text-center">Guardando Venta...</h4>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.pos.partials.clients.form')
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

