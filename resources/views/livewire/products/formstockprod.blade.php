
@include('common.modalStockHead')

<div>
    <!-- Mostrar el nombre del artículo -->
    <h2>{{ $name }}</h2>
</div>

<div class="row">
    <div class="col-sm-12 col-md-8">
        <div class="form-group">
            <label for="">Motivo del Ajuste</label>
            <input type="text" wire:model.lazy="motivo" class="form-control">
            @error('name') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Código del Artículo</label>
            <input type="text" wire:model.lazy="selected_id" class="form-control">
            @error('barcode') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Cantidad Actual</label>
            <input type="number" wire:model.lazy="alerts" class="form-control">
            @error('alerts') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Cantidad a Modificar</label>
            <input type="number"  wire:model.lazy="quantity" class="form-control">
            @error('price') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Acciones</label>
            <select wire:model.lazy="action" class="form-control">
                <option value="0" {{ $stock == 0 ? 'selected' : '' }}>Seleccionar</option>
                <option value="1" {{ $stock == 1 ? 'selected' : '' }}>Aumentar</option>
                <option value="6" {{ $stock == 6 ? 'selected' : '' }}>Disminuir</option>
            </select>
            @error('stock') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-8">
        <div class="form-group">
            <label for="">Observación</label>
            <input type="text"  wire:model.lazy="obs" class="form-control">
            @error('cost') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
</div>

@include('common.modalStockFooter')
