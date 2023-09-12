@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Nombre</label>
            <input type="text" wire:model.lazy="name" class="form-control" placeholder="ej: Juan Perez">
            @error('name') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Usuario</label>
            <input type="text" wire:model.lazy="username" class="form-control" placeholder="ej: admin">
            @error('username') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Teléfono</label>
            <input type="text" wire:model.lazy="phone" class="form-control" placeholder="ej: 0981 903214" maxlength="10">
            @error('phone') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Email</label>
            <input type="text" wire:model.lazy="email" class="form-control" placeholder="ej: admin@admin.com">
            @error('email') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Contraseña</label>
            <input type="password" wire:model.lazy="password" class="form-control">
            @error('password') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Estatus</label>
            <select wire:model.lazy="status" class="form-control">
                <option value="Elegir" selected>Elegir</option>
                <option value="Active" selected>Activo</option>
                <option value="Locked" selected>Bloqueado</option>
            </select>
            @error('status') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Asignar Rol</label>
            <select wire:model.lazy="profile" class="form-control">
                <option value="Elegir" selected>Elegir</option>
                @foreach($roles as $role)
                    <option value="{{$role->name}}" selected>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('profile') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label for="">Imágen de Perfil</label>
            <input type="file" wire:model="image" accept="image/x-png, image/jpg, image/gif" class="form-control">
            @error('image') <span class="text-danger er">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
@include('common.modalFooter')
