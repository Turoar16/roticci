<?php

namespace App\Http\Livewire;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\User;
use App\Models\Sale;

class UsersComponent extends Component
{

    use WithPagination;
    use WithFileUploads;

    public $name, $username, $phone, $email, $status, $image, $password, $selected_id, $fileLoaded, $profile;
    public $pageTitle, $componentName, $search;
    private $pagination = 3;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle='Listado';
        $this->componentName = 'Usuarios';
        $this->status = 'Elegir';
    }
    
    public function render()
    {
        if(strlen($this->search) > 0)
            $data = User::where('name', 'like', '%' . $this->search . '%')
            ->select('*')->orderby('name', 'asc')->paginate($this->pagination);
        else
            $data = User::select('*')->orderby('name', 'asc')->paginate($this->pagination);

        
        return view('livewire.users.component', [
            'data' => $data,
            'roles' => Role::orderby('name', 'asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function resetUI(){
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->image = '';
        $this->search = '';
        $this->status = 'Elegir';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }

    public function edit(User $user){

        $this->selected_id = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->phone = $user->phone;
        $this->profile = $this->profile;
        $this->status = $user->status;
        $this->email = $user->email;
        $this->password = '';

        $this->emit('show-modal', 'open!');
    }

    protected $listeners = [
        'deleteRow' => 'destroy',
        'resetUI' => 'resetUI',
    ];


    public function Store()
    {
        $rules = [
            'name'        => 'required|min:3',
            'username'        => 'required|unique:users|min:3',
            'email'        => 'required|unique:users|email',
            'status'       => 'required|not_in:Elegir',
            'profile'       => 'required|not_in:Elegir',
            'password'      => 'required|min:3',
        
        ];
        $messages =[
            'name.required' => 'Ingresa el nombre',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'username.required' => 'Ingresa el usuario',
            'username.min' => 'El usuario debe tener al menos 3 caracteres.',
            'username.unique' => 'El usuario ya existe',
            'email.required' => 'Ingresa el correo',
            'email.email' => 'Ingresa un correo válido',
            'email.unique' => 'El email ya existe',
            'status.required' => 'Seleccione el estatus del usuario',
            'status.not_in' => 'Seleccione el estatus',
            'profile.required' => 'Seleccione el perfil/role del usuario',
            'prolfile.not_in' => 'Seleccione el perfil/role distinto a elegir',
            'password.required' => 'Ingresa la contraseña',
            'password.min' => 'La contraseña debe tener al menos 3 caracteres'
        ];

        $this->validate($rules, $messages);

        $user = User::create([
            'name'       => $this->name,
            'username'       => $this->username,
            'email'       => $this->email,
            'phone'      => $this->phone,
            'status'    => $this->status,
            'profile'      => $this->profile,
            'password'     => bcrypt($this->password),
        ]);

        $user->syncRoles($this->profile);

        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('/public/users', $customFileName);
            $user->image = $customFileName;
            $user->save();
        }
        $this->resetUI();
        $this->emit('user-added', 'Usuario Registrado');
    }

    public function Update()
    {
        $rules = [
            'email'        => "required|email|unique:users,email,{$this->selected_id}",
            'name'        => 'required|min:3',
            'username'        => 'required|min:3',
            'status'       => 'required|not_in:Elegir',
            'profile'       => 'required|not_in:Elegir',
            'password'      => 'required|min:3',
        
        ];
        $messages =[
            'name.required' => 'Ingresa el nombre',
            'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres.',
            'username.required' => 'Ingresa el usuario',
            'username.min' => 'El usuario debe tener al menos 3 caracteres.',
            'email.required' => 'Ingresa el correo',
            'email.email' => 'Ingresa un correo válido',
            'email.unique' => 'El email ya existe',
            'status.required' => 'Seleccione el estatus del usuario',
            'status.not_in' => 'Seleccione el estatus',
            'profile.required' => 'Seleccione el perfil/role del usuario',
            'prolfile.not_in' => 'Seleccione el perfil/role distinto a elegir',
            'password.required' => 'Ingresa la contraseña',
            'password.min' => 'La contraseña debe tener al menos 3 caracteres'
        ];

        $this->validate($rules, $messages);

        $user = User::find($this->selected_id);
        $user->update([
            'name'       => $this->name,
            'username'       => $this->username,
            'email'       => $this->email,
            'phone'      => $this->phone,
            'status'    => $this->status,
            'profile'      => $this->profile,
            'password'     => bcrypt($this->password),
        ]);

        $user->syncRoles($this->profile);

        if ($this->image)
        {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('/public/users', $customFileName);
            $imageTemp = $user->image; //imagen temporal
            $user->image = $customFileName;
            $user->save();

            if ($imageTemp != null)
            {
                if (file_exists('storage/users/' . $imageTemp)) {
                    unlink('storage/users/' . $imageTemp);
                }
            }
        }
        $this->resetUI();
        $this->emit('user-updated', 'Usuario Actualizado');
    }

    public function destroy(User $user)
    {
        if($user){
            $sales = Sale::where('user_id', $user->id)->count();
            if($sales > 0){
                $this->emit('user-withsales', 'No es posible eliminar el usuario porque tiene ventas registradas');
            } else{
                $user->delete();
                $this->resetUI();
                $this->emit('user-deleted', 'Usuario Eliminado');
            }
        }

    }

}
