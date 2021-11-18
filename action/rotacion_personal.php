<?php

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
	header("Location: ../index.php");
	die();
}else{/*solo entra con post*/

	include_once '../class/Rotacion_personal.php';
	$insObj = new RotacionPersonal();

	if (isset($_POST["depto"])){
		$resModel = $insObj->filtros(3,$_POST["depto"]);
		 echo json_encode($resModel);
	}	 

}