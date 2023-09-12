<?php

use App\Http\Livewire\AsignarComponent;
use App\Http\Livewire\CoinsComponent;
use App\Http\Livewire\PermisosComponent;
use App\Http\Livewire\PosComponent;
use App\Http\Livewire\ProductsComponent;
use App\Http\Livewire\RolesComponent;
use App\Http\Livewire\UsersComponent;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\CategoriesComponent;
use App\Http\Livewire\CashoutComponent;
use App\Http\Livewire\EditarVentaComponent;
use App\Http\Livewire\TransferenciaComponent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth\login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function(){
    Route::get('categories', CategoriesComponent::class);
    Route::get('products', ProductsComponent::class);
    Route::get('coins', CoinsComponent::class);
    Route::get('pos', PosComponent::class);
    Route::get('edit', EditarVentaComponent::class);
    // Route::get('edit', TransferenciaComponent::class);

    Route::group(['middleware' => ['role:Admin']], function(){
        Route::get('roles', RolesComponent::class);
        Route::get('permisos', PermisosComponent::class);
        Route::get('asignar', AsignarComponent::class);
    });
    Route::get('users', UsersComponent::class);
    Route::get('cashout', CashoutComponent::class);
});
