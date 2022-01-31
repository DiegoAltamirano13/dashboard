<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class RotacionPersonal
{

	/*++++++++++++++++++++++++ WIDGETS ++++++++++++++++++++++++*/
  public function tabla($plaza,$fil_check,$fecha)
	{
		$conn = conexion::conectar();
		$res_array = array();

    $and_fecha_ini = " AND Y.FECHA_SOLICITUD >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND Y.FECHA_SOLICITUD < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";

    #	$and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) +1) ";
    #	$fecha_cancel =" AND RCAN.FECHA_CANCELACION < trunc(ADD_MONTHS(TRUNC(SYSDATE, 'MM'),-0) ) ";
    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_ini = " AND Y.FECHA_SOLICITUD >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND Y.FECHA_SOLICITUD < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
      }
    }

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
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

		$sql = "SELECT Y.IID_NO_SERVICIO AS N_SERVICIO, NOP.V_NOMBRE || ' '  || NOP.V_APE_PAT || ' ' || NOP.V_APE_MAT AS NOMBRE ,
                   Y.V_DESCRIPCION AS DESCRIPCION,
                   Y.IID_PLAZA, P.V_RAZON_SOCIAL AS PLAZA FROM AD_SE_SOLICITUD_REGISTRO Y
            INNER JOIN PLAZA P ON P.IID_PLAZA = Y.IID_PLAZA
            INNER JOIN NO_PERSONAL NOP ON NOP.IID_EMPLEADO = Y.IID_USUARIO_SOLICITA
            WHERE Y.TIPO_SERVICIO = 4 AND Y.PROCESO = 4
            AND Y.STATUS <> 7
            AND P.IID_PLAZA IN (".$in_plaza.")
            ".$and_fecha_ini."";
				#echo $sql;
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
	public function grafica($plaza,$fil_check,$fecha)
	{
		$conn = conexion::conectar();
		$res_array = array();

    $and_fecha_ini = " AND Y.FECHA_SOLICITUD >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND Y.FECHA_SOLICITUD < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),0) ) ";

    #	$and_fecha_can = " AND can.fecha_cancelacion >= TRUNC( ADD_MONTHS(TRUNC(SYSDATE, 'MM'),0) ) AND can.fecha_cancelacion < TRUNC( ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ),-1) +1) ";
    #	$fecha_cancel =" AND RCAN.FECHA_CANCELACION < trunc(ADD_MONTHS(TRUNC(SYSDATE, 'MM'),-0) ) ";
    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_ini = " AND Y.FECHA_SOLICITUD >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND Y.FECHA_SOLICITUD < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
      }
    }

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
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

		$sql = "SELECT Y.IID_PLAZA, P.V_RAZON_SOCIAL AS PLAZA , COUNT(*) AS CANTIDAD FROM AD_SE_SOLICITUD_REGISTRO Y
            INNER JOIN PLAZA P ON P.IID_PLAZA = Y.IID_PLAZA
            WHERE Y.TIPO_SERVICIO = 4 AND Y.PROCESO = 4
            AND Y.STATUS <> 7
            AND P.IID_PLAZA IN (".$in_plaza.")
            ".$and_fecha_ini."
            GROUP BY Y.IID_PLAZA, P.V_RAZON_SOCIAL";
				#echo $sql;
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

	public function graficaMensual($plaza,$fecha,$fil_check){
		if ($fil_check == 'on'){
			$andFecha = substr($fecha, 6,4);
			$and_fecha2 = $andFecha-1;
		}
		else{
			$andFecha = date("Y");
			$and_fecha2 = $andFecha -1;
		}


		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
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
		$sql ="SELECT MES.N_MES, MES.MES, (SELECT COUNT(*) FROM AD_SE_SOLICITUD_REGISTRO Y
                            INNER JOIN PLAZA P ON P.IID_PLAZA = Y.IID_PLAZA
                            WHERE Y.TIPO_SERVICIO = 4
                            AND Y.PROCESO = 4
                            AND Y.STATUS <> 7
                            AND P.IID_PLAZA IN (".$in_plaza.")
                            AND TO_CHAR(Y.FECHA_SOLICITUD, 'MM') = MES.N_MES AND TO_CHAR(Y.FECHA_SOLICITUD, 'YYYY') = ".$andFecha.") as TOTAL
                            FROM RH_MESES_GRAFICAS MES";//315
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
	/*++++++++++++++++++++++++ SQL FILTROS ++++++++++++++++++++++++*/
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


}
