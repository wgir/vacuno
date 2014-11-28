<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	$a='xxx';
	return View::make('hello',compact('a'));
});


Route::get('/test1', function()
{
	return Response::json(array('status'=>200,'message'=>'ok'));
});

Route::get('/test/{id}', function($id)
{
	return Response::json(array('status'=>200,'message'=>$id));
});


Route::get('/llamando/Al/Controller', 'HomeController@miMetodo');

Route::get('/llamando/Al/Controller/{param?}', 'HomeController@miMetodo1');


Route::get('users', function()
{
    return View::make('users');
});


Route::group(array('prefix' => 'api/v1'), function()
{
    Route::get('/meseros', array('uses' => 'PedidoController@meserosActivos')); 
    Route::get('/cantmesas', array('uses' => 'PedidoController@cantMesasEnJson')); 
    Route::get('/mesasdisponibles', array('uses' => 'PedidoController@mesasDisponibles')); 
    Route::get('/getproductos', array('uses' => 'PedidoController@getProductos')); 
    Route::get('/nuevopedido/{mesa?}/{mesero?}', array('uses' => 'PedidoController@nuevoPedido')); 
    Route::post('/addproducto/{id}',  array('uses' => 'PedidoController@addProducto'));
    Route::get('/getmesas/{meseroId?}',  array('uses' => 'PedidoController@getMesasxMesero'));
 });


