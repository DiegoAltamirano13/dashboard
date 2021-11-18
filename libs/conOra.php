<?php
class conexion {

	static function conectar(){
		$charset 	= 'UTF8';//192.168.1.64/orabck
		$conn 		= oci_connect('exodo', 'exodo2019', '10.10.1.205/ORACBA', $charset); //10.10.2.197/ORACBA
		//$conn 		= oci_connect('xds', 'xds2001', 'PRUEBA', $charset); //10.10.2.197/ORACBA
		//245

		if (!$conn) {
		    $e = oci_error();
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		} else
			return $conn;
	}


	static function cerrar($conn){

		if (!oci_close($conn)) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		} else
			return true;
	}

}
?>
