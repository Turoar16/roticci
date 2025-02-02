<div class="row mt-3">
    <div class="col-sm-12">

        <div class="connect-sorting">
            <h5 class="text-center mb-3">ACCIONES</h5>
            <div class="container">
                
            </div>
            <div class="connect-sorting-content mt-4">
                <div class="card simple-title-task ui-sortable-handle">
                    <div class="card-body">
                        <!-- <div class="input-group input-group-md mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-gp hideonsm" style="background: #3b3f5c;
                                color: white">
                                    EFECTIVO F8
                                </span>
                            </div>
                            <input type="number" id="cash"
                            wire:model="efectivo"
                            wire:keydown.enter="saveSale"
                            class="form-control text-center" value="{{$efectivo}}"
                            >
                            <div class="input-group-append">
                                <span wire:click="$set('efectivo',0)" class="input-group-text" style="background: #3b3f5c; color:white">
                                    <i class="fas fa-backspace fa-2x"></i>
                                </span>
                            </div>
                        </div> -->


                        <!-- <h4 class="text-muted">Cambio: {{number_format($change,2)}}</h4> -->

                        <div class="row justify-content-between mt-5">
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <button onclick="Confirm('','clearCart', '¿SEGURO QUE DESEA ELIMINAR EL CARRITO?')"
                                    class="btn btn-dark mtmobile">
                                    CANCELAR F4
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6">
                            @if ($error_message)
                                <button class="btn btn-dark btn-md btn-block" disabled>
                                    GUARDAR F9
                                </button>
                            @else
                                <button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">
                                    GUARDAR F9
                                </button>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
