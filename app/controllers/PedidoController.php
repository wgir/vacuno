<?php

class PedidoController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		return 'Hola soy index';
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		return 'Hola soy show y el id es:'.$id;
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function meserosActivos()
	{
		
		/*$sth = mysqli_query("SELECT ...");
		$rows = array();
		while($r = mysqli_fetch_assoc($sth)) {
    		$rows[] = $r;
		}
		print json_encode($rows);
		*/
		 $meseros = Mesero::where('estcodigo',1)->orderBy('mesnombre', 'ASC')->get();
		 //echo json_encode( $meseros->toArray());
		 return Response::json($meseros->toArray());
	}

	public function cantMesas()
	{
 		$paramList=Paramet::all();
    	foreach($paramList as $param)
       		 {
       		   return  $param->ParMesas;
        	 }
       		 return 0;
    }

	public function cantMesasEnJson()
	{
		
 	 try
		 {
       		 $statusCode = 200;
        	 $response = ['mesas'  => []];
       		 $response['mesas'][] = ['cant' =>$this->cantMesas(),];
       	   		 }catch (Exception $e){
        	$statusCode = 400;
   		 }finally{
        return Response::json($response, $statusCode);
    	}
    }


	public function mesasDisponibles()
	{
  	 $cantMesas=$this->cantMesas();
 	 $mesasOcupadas= DB::table('pedido')->select('mesa')->whereNull('faccodigo')->get();
 	 $statusCode = 200;
 	 $response = [];
  		for ($i = 1; $i <= $cantMesas; $i++) 
  		{
    	$esta=0;
    	 foreach($mesasOcupadas as $mesa)
       		{
    	      if($i==$mesa->mesa)
    	      	$esta=1;
          	}
          	if($esta==0)
          	 	$response[] = ['mesa' =>$i,];
  		}
 		return Response::json($response, $statusCode);
	}


	public function getProductos()
	{

		try
		 {
		 	$q="select p.grupcodigo,grupnombre,procodigo,pronombre,
				(select FLOOR(min(insexisten/iXPCANTIDA)) as existen from insumo as a,insxproducto as b
 				 where a.inscodigo=b.inscodigo
  				 and b.procodigo=p.procodigo) as cant
				 from producto p,grupoproduc g
				 where p.grupcodigo=g.grupcodigo
				 and (select FLOOR(min(insexisten/iXPCANTIDA)) as existen from insumo as a,insxproducto as b
 				 where a.inscodigo=b.inscodigo
  				 and b.procodigo=p.procodigo)>0
				 order by grupnombre,pronombre";

       		// $statusCode = 200;
        	// $response = [];
        	 $results = DB::select(DB::raw($q), array());
           
 			 echo   json_encode( $results);
 			 //return Response::json( json_encode( $results), $statusCode);
   		 }catch (Exception $e)
   		  {
        	$statusCode = 400;
   		  }finally{
          // return Response::json($response, $statusCode);
    	 }

	}
	public function nuevoPedido($mesa= '0',$mesero='0')
	{
	 try
		 {
       		 $statusCode = 200;
        	 $response =[];
        	 $results = DB::select("select nextval('pedido') as pedidoid", array());
             foreach($results as $r)
       		 {
                $response[] = ['id' => $r->pedidoid];
                $pedidoId=$r->pedidoid;
 			 }
 			 $dtz = new DateTimeZone("America/Bogota"); //Your timezone
			 $now = new DateTime(date("Y-m-d H:i:s"), $dtz);

 			 $pedido=new Pedido;
 			 $pedido->pedidoId=$pedidoId;
 			 $pedido->pedfecha=$now;
 			 $pedido->mescodigo=$mesero;
 			 $pedido->estcodigo=1;
 			 $pedido->mesa=$mesa;
			 $pedido->save();

 			 return Response::json($response, $statusCode);
   		 }catch (Exception $e)
   		  {
        	$statusCode = 400;
   		  }finally{
           return Response::json($response, $statusCode);
    	 }
    }

    public function addProducto($id= '0')
    {
    	 $input = Input::json()->all();
    	 $tmpItemId=0;
    	 $statusCode = 200;
         $response =[];
         
    	 $results = DB::select("select nextval('itempedido') as itemid", array());
          foreach($results as $r)
       		 {
                 $response[] = ['itemid' => $r->itemid];
                 $tmpItemId=$r->itemid;

 			 }
		
    	 $detpedido=new DetPedido;
 		 $detpedido->pedidoId=$id;
 		 $detpedido->procodigo=  $input['procodigo']; //Request::get('procodigo');
 		 $detpedido->detpedcant= $input['detpedcant'];
 		 $detpedido->peditem= $tmpItemId;
 		 $detpedido->save();
 		 
 		return Response::json($response, $statusCode);
 		// return Response::json(array('error' => false,'urls' => $response),200);
    }

    public function getMesasxMesero($meseroId='0')
	{

		try
		 {
		 	$q="select p.pedidoid,p.mescodigo,p.mesa,g.grupcodigo,g.grupnombre,d.peditem,d.procodigo,
				pd.pronombre,detpedcant
				from pedido p left join detpedido d
				on p.pedidoid=d.pedidoid left join producto pd
				on pd.procodigo=d.procodigo left join grupoproduc g
				on pd.grupcodigo=g.grupcodigo
				where faccodigo is null
				and mescodigo='$meseroId'
				order by p.pedidoid";
	        	 $results = DB::select(DB::raw($q), array());
    			 echo   json_encode( $results);
 			 //return Response::json( json_encode( $results), $statusCode);
   		}catch (Exception $e)
   		  {
        	$statusCode = 400;
   		  }
   		  finally
   		  	{
          		// return Response::json($response, $statusCode);
    	 	}

	}

}
