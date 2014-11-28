<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		$a=$array('test');
		return View::make('hello',compact('a'));
	}
	
	public function miMetodo()
	{
		echo 'Hola Soy tu Metodo';
	}

	public function miMetodo1($parametro= 'vacio')
	{
		echo 'Hola Soy tu Metodo1:'.$parametro;
	}
}
