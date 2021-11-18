<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Calculo_Ocupacion
{
	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function grafica($plaza,$fecha,$almacen,$fil_check)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha_ini = " WHERE FECHA_REG >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND FECHA_REG < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";

    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_ini = " WHERE FECHA_REG >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND FECHA_REG < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
      }
    }

		if ($plaza != 'ALL') {
			$and_plaza = " AND PROYECTO = '".$plaza."'";
		}
		else {
			$and_plaza = " ";
		}

		$sql = "SELECT ID_PIEZAS_DAÑADAS, N_PARTE, N_FACTURA, N_PIEZAS_DAÑADAS, N_TOTAL_PIEZAS, FECHA_REG, PROYECTO FROM OP_IN_PIEZAS_DAÑADAS $and_fecha_ini".$and_plaza;

					# echo $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		#echo $sql;
		oci_free_statement($stid);
		oci_close($conn);

		//	echo $sql;
		return $res_array;

	}
	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function tabla($plaza,$fecha,$fil_check)
	{

		$and_fecha_ini = " WHERE FECHA_REG >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND FECHA_REG < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";

    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_ini = " WHERE FECHA_REG >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND FECHA_REG < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
      }
    }

		if ($plaza != 'ALL') {
			$and_plaza = " AND PROYECTO = '".$plaza."'";
		}
		else {
			$and_plaza = " ";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT ID_PIEZAS_DAÑADAS, N_PARTE, N_FACTURA, N_PIEZAS_DAÑADAS, N_TOTAL_PIEZAS, FECHA_REG, PROYECTO FROM OP_IN_PIEZAS_DAÑADAS $and_fecha_ini".$and_plaza;


		$stid = oci_parse($conn,$sql);
		oci_execute($stid);
		while (($row = oci_fetch_assoc($stid))!= false) {
		$res_array[] = $row;
		}
		oci_free_statement($stid);
		oci_close($conn);
		return $res_array;
	}

	/*++++++++++++++++++++++++ SQL DINAMICO FROM DUAL ++++++++++++++++++++++++*/
	public function dual($sql)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	public function filtros($option,$depto)
	{
		$conn = conexion::conectar();
		$res_array = array();

		switch ($option) {
			case '1':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ";
				break;
			case '2':
				$sql = "SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep";
				break;
			case '3':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
				break;
		}

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}


		/**************************************GRAFICA DE PIE ********************************************************/

	function capacidad_almacen($plaza,$fecha,$fil_check){
			$no_semana = date("W");
			$no_semana_inf = date("W")-4;


			$mes = date("m")-1;
			$mes2 = date("m");

			$anio = date("Y");
			$anio2 = date("Y");

		#	echo $mes." ".$mes2;


			if ($fil_check == 'on'){

				if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
					//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
					$dia = substr($fecha, 0,2);
					$mes = substr($fecha, 3,2);
					$anio = substr($fecha,6,4);
					$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

					$dia2 = substr($fecha, 11,2);
					$mes2 = substr($fecha, 14,2);
					$anio2 = substr($fecha,17,4);
					$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
					//echo $no_semana. " ". $no_semana_inf;
				}
			}
			$in_plaza = "2,3,4,5,6,7,8,17,18";
			switch ($plaza){
			  	case 'CORPORATIVO': $in_plaza = 2; break;
			    case 'CÓRDOBA': $in_plaza = 3; break;
			    case 'MÉXICO': $in_plaza = 4; break;
			    case 'GOLFO': $in_plaza = 5; break;
			    case 'PENINSULA': $in_plaza = 6; break;
			    case 'PUEBLA': $in_plaza = 7; break;
			    case 'BAJIO': $in_plaza = 8; break;
			    case 'OCCIDENTE': $in_plaza = 17; break;
			    case 'NORESTE': $in_plaza = 18; break;
			    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
			}

			$conn = conexion::conectar();
			$res_array = array();
			$sql ="SELECT SUM(PRU.MTS_RACKS) AS MTS_RACKS,
										SUM(PRU.CAPACIDAD_TOTAL) AS CAPACIDAD_TOTAL,
										SUM(PRU.USO_VARIADOS) AS USO_VARIADOS,
										SUM(PRU.AREA_RACKS) AS AREA_RACKS,
										SUM(PRU.TAMANIO_BODEGA) AS TAMANIO_BODEGA
							FROM ALMACEN_CAPACIDAD PRU
							WHERE PRU.SEMANA = (SELECT MAX(SEMANA) FROM ALMACEN_CAPACIDAD PRU2 WHERE PRU2.MES =".$mes2." AND PRU2.ANIO = ".$anio2." AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
							AND PRU.IID_PLAZA IN(".$in_plaza.")";

			#echo $sql;
			$stid = oci_parse($conn,$sql);
			oci_execute($stid);
			while (($row = oci_fetch_assoc($stid))!= false) {
				$res_array[] = $row;
			}
			oci_free_statement($stid);
			oci_close($conn);
			return $res_array;
		}

	/*****************************ALMACEN **************************************************/
	function almacenSql($plaza){
		$conn = conexion::conectar();
		$res_array = array();
		switch($plaza){
 		 //case 'CORPORATIVO': $in_plaza = 2; break;
 		 case 'CÓRDOBA': $in_plaza = 3; break;
 		 case 'MÉXICO': $in_plaza = 4; break;
 		 case 'GOLFO': $in_plaza = 5; break;
 		 case 'PENINSULA': $in_plaza = 6; break;
 		 case 'PUEBLA': $in_plaza = 7; break;
 		 case 'BAJIO': $in_plaza = 8; break;
 		 case 'OCCIDENTE': $in_plaza = 17; break;
 		 case 'NORESTE': $in_plaza = 18; break;
 		 default: $in_plaza = "3,4,5,6,7,8,17,18"; break;
 	 }
		$sql = "SELECT DISTINCT(AL.IID_ALMACEN), AL.V_NOMBRE
						FROM ALMACEN AL
            INNER JOIN ALMACEN_CAPACIDAD ALC ON AL.IID_PLAZA = ALC.IID_PLAZA AND AL.IID_ALMACEN = ALC.IID_ALMACEN
						WHERE AL.IID_PLAZA = ".$in_plaza." AND AL.IID_ALMACEN NOT IN (9998, 9999) ORDER BY AL.IID_ALMACEN";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	public function sql($option,$depto,$plaza)
		{
			$conn = conexion::conectar();
			$res_array = array();

			$sql = "SELECT * FROM DUAL";
			switch ($option) {
				case '1':
					$sql = "SELECT TO_CHAR(TRUNC(SYSDATE, 'MM'), 'DD/MM/YYYY') mes1, TO_CHAR(LAST_DAY( TO_DATE(SYSDATE) ), 'DD/MM/YYYY') mes2 FROM DUAL";
					break;
				case '2':
					$sql = " SELECT DISTINCT(T.PROYECTO) as PLAZA FROM OP_IN_PIEZAS_DAÑADAS T ";
					break;
				case '3':
					$sql = " SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep ";
					break;
				case '4':
					$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
					break;
				case '5':
					$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91 , 68, 9) and v_sscuenta = 0
										union all
										select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
					break;
				//case '6':
					//break;
				default:
					$sql = "SELECT * FROM DUAL";
					break;
			}

			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			#echo $sql;
			while (($row = oci_fetch_assoc($stid)) != false)
			{
				$res_array[]= $row;
			}

			oci_free_statement($stid);
			oci_close($conn);

			return $res_array;
		}
}
