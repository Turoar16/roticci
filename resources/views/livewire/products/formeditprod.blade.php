
@include('common.modalMoverHead')

<div>
    <!-- Mostrar el nombre del artículo -->
    <h2>Referencia: {{ $ref }}</h2>

    <!-- Mostrar los depósitos actuales -->
    <!-- <h3>Depósitos actuales:</h3>
    <ul>
        <li>{{ $stock == 1 ? 'Local' : 'Depósito' }}: {{ $alerts }}</li>
    </ul> -->

    <!-- Mostrar el formulario para especificar la cantidad y el depósito de destino -->
    <!-- <form wire:submit.prevent="moveStock('{{ $stock }}', '{{ $depoTo }}')">
        <label for="quantity">Cantidad:</label>
        <input type="number" id="quantity" wire:model="defaultValue">

        <label for="deposito">Depósito de destino:</label>
        <select id="deposito" wire:model="depoTo"> -->
            <!-- Opciones para los depósitos -->
            <!-- <option value="0">Seleccione..</option>
            <option value="1">Local</option>
            <option value="6">Depósito</option>
        </select>

        <button type="submit">Mover Stock</button>
    </form> -->
</div>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Deposito Origen</label>
            <select wire:model.lazy="stock" class="form-control">
                <option value="0" {{ $stock == 1 ? 'selected' : '' }}>Seleccionar</option>
                <option value="1" {{ $stock == 1 ? 'selected' : '' }}>L'INSTANT PCC</option>
                <option value="6" {{ $stock == 6 ? 'selected' : '' }}>DEPOSITO</option>
            </select>
            @error('stock') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Deposito Destino</label>
            <select wire:model.lazy="depoTo" class="form-control">
                <option value="0" {{ $stock == 1 ? 'selected' : '' }}>Seleccionar</option>
                <option value="1" {{ $stock == 1 ? 'selected' : '' }}>L'INSTANT PCC</option>
                <option value="6" {{ $stock == 6 ? 'selected' : '' }}>DEPOSITO</option>
            </select>
            @error('depoTo') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label for="">Observaciones</label>
            <textarea id="observacion" rows="2" wire:model="obs" class="form-control"></textarea>
            @error('obs') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-2">
        <div class="form-group">
            <label for="">Código</label>
            <input type="text" wire:model.lazy="selected_id" class="form-control">
            @error('selected_id') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <!-- <div class="col-sm-12 col-md-2">
        <div class="form-group">
            <label for="">Referencia</label>
            <input type="text"  wire:model.lazy="barcode" class="form-control">
            @error('price') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div> -->
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Descripcion del Articulo</label>
            <input type="text"  wire:model.lazy="name" class="form-control">
            @error('name') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-2">
        <div class="form-group">
            <label for="">Cantidad</label>
            <input type="number"  wire:model.lazy="defaultValue" class="form-control">
            @error('defaultValue') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-2">
        <div class="form-group">
            <label for="">Precio Uni.</label>
            <input type="text"  wire:model.lazy="price" class="form-control">
            @error('price') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
</div>

@include('common.modalMoverFooter')
