<?php

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
	header("Location: ../index.php");
	die();
}else{//solo entra con post

	include_once '../class/RackAlo.php';
	$insObj = new Rack();

	if ( isset($_POST["DibMerca"]) )
	{

		$resController = $insObj->rackDetPosicion($_POST["id_plaza"],$_POST["id_almacen"],$_POST["id_cliente"],$_POST["mercancia"],$_POST["rack"],$_POST["profundidad"],$_POST["rtProyect"]);
		echo json_encode($resController);

	}

	if ( isset($_POST["detNoUniArribo"]) )
	{

		$resController = $insObj->detMercanciaNoUbi($_POST["detNoUniArribo"], $_POST["id_plaza"], $_POST["fil_cliente"], $_POST["id_almacen"], $_POST["fil_db"], $_POST["rtProyect"]);
		echo json_encode($resController);

	}


	if ( isset($_POST["detUbicacion"]) )
	{

		$resController = $insObj->rackDetalleUbica($_POST["ubicacion"],$_POST["id_plaza"],$_POST["fil_cliente"],$_POST["id_almacen"],$_POST["fil_db"]);
		echo json_encode($resController);

	}


}// /.solo entra con post
