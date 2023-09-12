@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-8">
        <div class="form-group">
            <label for="">Nombre</label>
            <input type="text" wire:model.lazy="name" class="form-control">
            @error('name') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Código</label>
            <input type="text" wire:model.lazy="barcode" class="form-control">
            @error('barcode') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Costo</label>
            <input type="text"  wire:model.lazy="cost" class="form-control">
            @error('cost') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Precio</label>
            <input type="text"  wire:model.lazy="price" class="form-control">
            @error('price') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Ubicación</label>
            <select wire:model.lazy="stock" class="form-control">
                <option value="1" {{ $stock == 1 ? 'selected' : '' }}>Local</option>
                <option value="6" {{ $stock == 6 ? 'selected' : '' }}>Depósito</option>
            </select>
            @error('stock') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label for="">Cant. Stock</label>
            <input type="number" wire:model.lazy="alerts" class="form-control">
            @error('alerts') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-8">
        <div class="form-group custom-file">
            <input type="file" class="custom-file-input form-control" wire:model="image"
            accept="image/x-png, image/gif, image/jpg">
            <label class="custom-file-label">Imágen {{ $image }}</label>
            @error('image') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <ul class="tab tab-pills">
        <li>
                <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-sm" data-toggle="modal" data-target="#theModalStock"><i class="fas fa-cart-plus"></i>
                    Ajustar Stock
                </a>
        </li>
    </ul>
    <ul class="tab tab-pills">
        <li>
            <a href="javascript:void(0)" class="tabmenu bg-dark btn btn-sm" data-toggle="modal" data-target="#theModalProduct"><i class="fas fa-cart-plus"></i>
                Mover Producto
            </a>
        </li>
    </ul>
</div>
@include('common.modalFooter')
