<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
                <ul class="tab tab-pills">
                    <li>
                        <a href="javascript:void(0)" class="tabmenu btn btn-sm bg-dark" data-toggle="modal" data-target="#theModal">
                            Agregar
                        </a>
                    </li>
                </ul>
            </div>
            @include('common.searchbox')

            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-bordered table striped mt-1">
                        <thead class="text-white" style="background: #3b3f5c">
                            <tr>
                                <th class="table-th text-white text-center">NOMBRE</th>
                                <th class="table-th text-white text-center">USUARIO</th>
                                <th class="table-th text-white text-center">TELÉFONO</th>
                                <th class="table-th text-white text-center">EMAIL</th>
                                <th class="table-th text-white text-center">ESTATUS</th>
                                <th class="table-th text-white text-center">PERFIL</th>
                                <th class="table-th text-white text-center">IMAGEN</th>
                                <th class="table-th text-white text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $r)
                            <tr>
                                <td class="text-center"><h6>{{ $r->name }}</h6></td>
                                <td class="text-center"><h6>{{ $r->username }}</h6></td>
                                <td class="text-center"><h6>{{ $r->phone }}</h6></td>
                                <td class="text-center"><h6>{{ $r->email }}</h6></td>
                                <td class="text-center">
                                    <span class="badge {{ $r->status == 'Active' ? 'badge-success' : 'badge-danger' }} text-uppercase">{{ $r->status  }}</span>
                                </td>
                                <td class="text-center text-uppercase"><h6>{{ $r->profile }}</h6></td>
                                <td class="text-center">
                                    @if($r->image !=null)
                                    <img src="{{asset('storage/users/' .$r->image) }}" alt="imagen" class="card-img-top img-fluid">
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" wire:click="edit({{$r->id }})" class="btn btn-dark mtmobile" title="Editar Registro">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Confirm('{{$r->id}}')"class="btn btn-dark" title="Eliminar Registro">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
@include('livewire.users.form')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('user-added', Msg=>{
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('user-updated', Msg=>{
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('user-deleted', Msg=>{
            noty(Msg)
        })
        window.livewire.on('hide-modal', Msg=>{
            $('#theModal').modal('hide')
        })
        window.livewire.on('show-modal', Msg=>{
            $('#theModal').modal('show')
        })
        window.livewire.on('user-withsales', Msg=>{
            $('#theModal').modal('hide')
            noty(Msg)
        })
    });
    function Confirm(id)
    {
    
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