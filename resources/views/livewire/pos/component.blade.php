<div>
    <style></style>
    <div class="row layout-top-spacing">
        <div class="col-sm-12 col-md-8">
            <!-- CLIENTES -->
            @include('livewire.pos.partials.clients.client')
            <!--DETALLES-->
            @include('livewire.pos.partials.details.detail')
        </div>

        <div class="col-sm-12 col-md-4">
            <!--TOTAL-->
        @include('livewire.pos.partials.total')

        <!--DENOMIACIONES-->
            @include('livewire.pos.partials.coins')
        </div>
    </div>
</div>

<script src="{{ asset('js/keypress.js')}}"></script>
<script src="{{ asset('js/onscan.js')}}"></script>

<!-- @include('livewire.pos.scripts.clients') -->
@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
@include('livewire.pos.scripts.scan')
