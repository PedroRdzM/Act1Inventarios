<?php

use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => ['guest']], function () {
     
    Route::get('/','App\Http\Controllers\Auth\LoginController@showLoginForm');
    Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login')->name('login');

});

//redirige al login
Route::group(['middleware' => ['auth']], function () {

    Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

    Route::get('/home', 'App\Http\Controllers\HomeController@index');

      //vistas a las que puede acceder el comprador
    Route::group(['middleware' => ['Comprador']], function () {
         
        Route::resource('categoria', 'App\Http\Controllers\CategoriaController');
        Route::resource('producto', 'App\Http\Controllers\ProductoController');
        Route::resource('proveedor', 'App\Http\Controllers\ProveedorController');
        Route::resource('compra', 'App\Http\Controllers\CompraController');
        Route::get('/pdfCompra/{id}', 'App\Http\Controllers\CompraController@pdf')->name('compra_pdf');
          
    
    });
    //vistas a las que puede acceder el vendedor

    Route::group(['middleware' => ['Vendedor']], function () {

         Route::resource('categoria', 'App\Http\Controllers\CategoriaController');
         Route::resource('producto', 'App\Http\Controllers\ProductoController');
         Route::resource('cliente', 'App\Http\Controllers\ClienteController');
         Route::resource('venta', 'App\Http\Controllers\VentaController');
         Route::get('/pdfVenta/{id}', 'App\Http\Controllers\VentaController@pdf')->name('Venta_pdf');
         
         
         
    });
    //vistas a las que puede acceder el administrador
    Route::group(['middleware' => ['Administrador']], function () {
          
      Route::resource('categoria', 'App\Http\Controllers\CategoriaController');
      Route::resource('producto', 'App\Http\Controllers\ProductoController');
      Route::resource('proveedor', 'App\Http\Controllers\ProveedorController');
      Route::resource('cliente', 'App\Http\Controllers\ClienteController');
      Route::resource('rol', 'App\Http\Controllers\RolController');
      Route::resource('user', 'App\Http\Controllers\UserController');
      Route::resource('compra', 'App\Http\Controllers\CompraController');
      Route::resource('venta', 'App\Http\Controllers\VentaController');
      Route::get('/pdfCompra/{id}', 'App\Http\Controllers\CompraController@pdf')->name('compra_pdf');
      Route::get('/pdfVenta/{id}', 'App\Http\Controllers\VentaController@pdf')->name('venta_pdf');


	    
    
    });


});

