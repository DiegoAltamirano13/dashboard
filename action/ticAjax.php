<?php 

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
	header("Location: ../index.php");
	die();
}else{//solo entra con post

	include_once '../class/Tic.php';
	$instClass = new Tic();

	if ( isset($_POST["option"]) ) 
	{
		$mes1 = "01"; $mes2 = "02";
		switch ( $_POST["bimestre"] ) {
			case '1':
				$mes1 = "01"; $mes2 = "02"; break;
			case '2':
				$mes1 = "03"; $mes2 = "04"; break;
			case '3':
				$mes1 = "05"; $mes2 = "06"; break;
			case '4':
				$mes1 = "07"; $mes2 = "08"; break;
			case '5':
				$mes1 = "09"; $mes2 = "10"; break;
			case '6':
				$mes1 = "11"; $mes2 = "12"; break;
		}

		$resSql = $instClass->sql(" SELECT TO_CHAR(TRUNC( TO_DATE('".$mes1."-".$_POST["anio"]."','MM-YYYY'), 'MM' ),'DD-MM-YYYY') AS inicio, TO_CHAR(LAST_DAY( TRUNC( TO_DATE('".$mes2."-".$_POST["anio"]."','MM-YYYY') ) ),'DD-MM-YYYY') fin FROM DUAL ");
		 echo json_encode($resSql); 
		//echo "hola ajax anio= ".$resSql[0]["INICIO"]."-".$resSql[0]["FIN"];
		//var_dump( $resSql ) ;

	} 

}// /.solo entra con post