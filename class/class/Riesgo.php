<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class NominaPagada
{
	public function widgetsRiesgo($plaza){
		$conn = conexion::conectar();
		$res_array = array();
		if ($plaza == "ALL") {
			$sql = "SELECT sum(dias_subsidiados) AS DIAS
							FROM NO_RIESGO_IMPORTE_EMPLEADOS
							WHERE TO_CHAR(FECHA_ALTA , 'yyyy') = to_char(sysdate , 'yyyy')";
		}
		else {
			$sql = "SELECT sum(dias_subsidiados) AS DIAS
							FROM NO_RIESGO_IMPORTE_EMPLEADOS
							WHERE TO_CHAR(FECHA_ALTA , 'yyyy') = to_char(to_date('".$plaza."', 'yyyy'), 'yyyy')";
		}

		#echo $sql;

			 $stid = oci_parse($conn, $sql);
			 oci_execute($stid);
			 while(($row= oci_fetch_assoc($stid))!=false ){
				 $res_array[]=$row;
			 }
			 #
			 oci_free_statement($stid);
			 oci_close($conn);
			 return $res_array;
	}

	public function widgetsRiesgo2($plaza){
		$conn = conexion::conectar();
		$res_array = array();
		if ($plaza == "ALL") {
			$sql = "SELECT N_PORCENTAJE FROM NO_RIESGO_IMPORTE WHERE N_ANIO = TO_CHAR(SYSDATE, 'YYYY')-1";
		}else {
			$sql = "SELECT N_PORCENTAJE FROM NO_RIESGO_IMPORTE WHERE N_ANIO = TO_CHAR(TO_DATE('".$plaza."', 'yyyy'), 'YYYY')-1";
		}

			 $stid = oci_parse($conn, $sql);
			 oci_execute($stid);
			 while(($row= oci_fetch_assoc($stid))!=false ){
				 $res_array[]=$row;
			 }
			 #echo $sql;
			 oci_free_statement($stid);
			 oci_close($conn);
			 return $res_array;
	}


	public function widgetsRiesgo3($plaza){
		$conn = conexion::conectar();
		$res_array = array();
		if ($plaza == "ALL") {
			$sql = "SELECT N_PORCENTAJE FROM NO_RIESGO_IMPORTE WHERE N_ANIO = TO_CHAR(SYSDATE, 'YYYY')";
		}else {
			$sql = "SELECT N_PORCENTAJE FROM NO_RIESGO_IMPORTE WHERE N_ANIO = TO_CHAR(TO_DATE('".$plaza."', 'yyyy'), 'YYYY')";
		}
			 $stid = oci_parse($conn, $sql);
			 oci_execute($stid);
			 while(($row= oci_fetch_assoc($stid))!=false ){
				 $res_array[]=$row;
			 }
			 #echo $sql;
			 oci_free_statement($stid);
			 oci_close($conn);
			 return $res_array;
	}

	/*====================== GRAFICA DE NOMINA PAGADA ======================*/
	public function graficaNomina($fecha,$plaza, $tipo)
	{
		//Realiza conexion
		$conn = conexion::conectar();
		$res_array = array();
		//QUERY PARA CONSULTAR POR PLAZA
		$sql = "SELECT N_ANIO, N_PORCENTAJE FROM NO_RIESGO_IMPORTE ORDER BY N_ANIO";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}
		#echo $sql;
		oci_free_statement($stid);
		oci_close($conn);
		//Regresa un arreglo
		return $res_array;
	}
	/*====================== /*GRAFICA DE NOMINA PAGADA ======================*/
/*
	Dioses del olimpo
	Este es el rap  de los Dioses


*/
/*GRAFICA POR MES DE DIAS DESCANSADOS DIEGO ALTAMIRANO SUAREZ ARGO 2019*/
	public function grafica_Mensual($fecha,$plaza,$tipo){
		$andFecha = substr($fecha, 6,4);
		#echo $andFecha;
		$andTipo = " AND D.ID_TIPO_FALTA IN (".$tipo.")";
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
		$sql = "SELECT PLA.N_MES AS NUMERO_MES,
       			PLA.MES AS NOMBRE_MES,
       			NVL((SELECT SUM(D.C_DIAS_FALTA)
                   FROM RH_FALTAS D
                   INNER JOIN NO_PERSONAL NP ON D.IID_EMPLEADO = NP.IID_EMPLEADO
                   WHERE ( TO_CHAR(D.D_FEC_INICIO , 'YYYY') = '".$andFecha."'
                           AND TO_CHAR(D.D_FEC_FIN , 'YYYY') = '".$andFecha."')
                   AND (TO_CHAR(D.D_FEC_INICIO, 'MM') = PLA.N_MES
                       AND TO_CHAR(D.D_FEC_FIN, 'MM')= PLA.N_MES)
                   ".$andTipo."
                   AND NP.IID_PLAZA IN (".$in_plaza.") ), 0)AS DIAS_DESCANSADOS
									 FROM RH_MESES_GRAFICAS PLA
									 ORDER BY PLA.N_MES";
									 ##echo $sql;
									 $stid = oci_parse($conn,$sql);
									 oci_execute($stid);

									 while (($row = oci_fetch_assoc($stid)) != false) {
									 		$res_array[] = $row;
									 }
									 oci_free_statement($stid);
									 oci_close($conn);
									 return $res_array;
	}

/*TERMINA GRAFICA POR MES ARGO 2019*/

	/*====================== TABLA DE NOMINA PAGADA ======================*/
	public function tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT N_ANIO, N_PORCENTAJE FROM NO_RIESGO_IMPORTE ORDER BY N_ANIO";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		//echo $sql;
		return $res_array;
	}
	/*====================== /*TABLA DE NOMINA PAGADA ======================*/
	public function tablaNomina2()
	{
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT T.IID_EMPLEADO, T.V_NOMBRE || ' ' || T.V_APE_PAT || ' ' || T.V_APE_MAT AS NOMBRE ,P.V_RAZON_SOCIAL, RH.V_DESCRIPCION, T.V_CURP, T.V_IMSS ,
       			R.FECHA_ACCIDENTE, RHC.S_DESCRIPCION AS TIPO_RIESGO, R.DIAS_SUBSIDIADOS, R.PORCENTAJE_INCAPACIDAD, R.DEFUNCION, TO_CHAR(R.FECHA_ALTA, 'DD/MM/YYYY') AS FECHA_ALTA
       			FROM NO_PERSONAL T
       			INNER JOIN NO_RIESGO_IMPORTE_EMPLEADOS R ON T.IID_EMPLEADO = R.IID_EMPLEADO
            INNER JOIN PLAZA P ON P.IID_PLAZA = T.IID_PLAZA
            INNER JOIN NO_CONTRATO NC ON NC.IID_EMPLEADO = T.IID_EMPLEADO AND NC.IID_CONTRATO = T.IID_CONTRATO
            INNER JOIN RH_PUESTOS RH ON NC.IID_PUESTO = RH.IID_PUESTO
						INNER JOIN RH_FALTAS RH ON R.IID_EMPLEADO = RH.IID_EMPLEADO AND R.IID_CONSECUTIVO = RH.ID_FOLIO
						INNER JOIN rh_faltas_cat RHC ON RHC.ID_TIPO_FALTA = RH.ID_TIPO_FALTA
            WHERE TO_CHAR(R.FECHA_ALTA , 'yyyy') = TO_CHAR(SYSDATE, 'YYYY')-1
									AND RH.ID_TIPO_FALTA IN (3)
			 			ORDER BY R.FECHA_ALTA ASC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		//echo $sql;
		return $res_array;
	}


	public function tablaNomina3()
	{
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT T.IID_EMPLEADO, T.V_NOMBRE || ' ' || T.V_APE_PAT || ' ' || T.V_APE_MAT AS NOMBRE ,P.V_RAZON_SOCIAL, RH.V_DESCRIPCION, T.V_CURP, T.V_IMSS ,
       			R.FECHA_ACCIDENTE, RHC.S_DESCRIPCION AS TIPO_RIESGO, R.DIAS_SUBSIDIADOS, R.PORCENTAJE_INCAPACIDAD, R.DEFUNCION,  TO_CHAR(R.FECHA_ALTA, 'DD/MM/YYYY') AS FECHA_ALTA
       			FROM NO_PERSONAL T
       			INNER JOIN NO_RIESGO_IMPORTE_EMPLEADOS R ON T.IID_EMPLEADO = R.IID_EMPLEADO
            INNER JOIN PLAZA P ON P.IID_PLAZA = T.IID_PLAZA
            INNER JOIN NO_CONTRATO NC ON NC.IID_EMPLEADO = T.IID_EMPLEADO AND NC.IID_CONTRATO = T.IID_CONTRATO
            INNER JOIN RH_PUESTOS RH ON NC.IID_PUESTO = RH.IID_PUESTO
						INNER JOIN RH_FALTAS RH ON R.IID_EMPLEADO = RH.IID_EMPLEADO AND R.IID_CONSECUTIVO = RH.ID_FOLIO
						INNER JOIN rh_faltas_cat RHC ON RHC.ID_TIPO_FALTA = RH.ID_TIPO_FALTA
            WHERE TO_CHAR(R.FECHA_ALTA , 'yyyy') = TO_CHAR(SYSDATE, 'YYYY')
									AND RH.ID_TIPO_FALTA IN (3)
			 			ORDER BY R.FECHA_ALTA ASC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		//echo $sql;
		return $res_array;
	}
	public function tablaNomina4()
	{
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT T.IID_EMPLEADO, T.V_NOMBRE || ' ' || T.V_APE_PAT || ' ' || T.V_APE_MAT AS NOMBRE ,P.V_RAZON_SOCIAL, RH.V_DESCRIPCION, T.V_CURP, T.V_IMSS ,
       			R.FECHA_ACCIDENTE, RHC.S_DESCRIPCION AS TIPO_RIESGO, R.DIAS_SUBSIDIADOS, R.PORCENTAJE_INCAPACIDAD, R.DEFUNCION,  TO_CHAR(R.FECHA_ALTA, 'DD/MM/YYYY') AS FECHA_ALTA
       			FROM NO_PERSONAL T
       			INNER JOIN NO_RIESGO_IMPORTE_EMPLEADOS R ON T.IID_EMPLEADO = R.IID_EMPLEADO
            INNER JOIN PLAZA P ON P.IID_PLAZA = T.IID_PLAZA
            INNER JOIN NO_CONTRATO NC ON NC.IID_EMPLEADO = T.IID_EMPLEADO AND NC.IID_CONTRATO = T.IID_CONTRATO
            INNER JOIN RH_PUESTOS RH ON NC.IID_PUESTO = RH.IID_PUESTO
						INNER JOIN RH_FALTAS RH ON R.IID_EMPLEADO = RH.IID_EMPLEADO AND R.IID_CONSECUTIVO = RH.ID_FOLIO
						INNER JOIN rh_faltas_cat RHC ON RHC.ID_TIPO_FALTA = RH.ID_TIPO_FALTA
            WHERE TO_CHAR(R.FECHA_ALTA , 'yyyy') = TO_CHAR(SYSDATE, 'YYYY')
									AND RH.ID_TIPO_FALTA IN (13)
			 			ORDER BY R.FECHA_ALTA ASC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		//echo $sql;
		return $res_array;
	}
	public function tablaNomina5()
	{
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT T.IID_EMPLEADO, T.V_NOMBRE || ' ' || T.V_APE_PAT || ' ' || T.V_APE_MAT AS NOMBRE ,P.V_RAZON_SOCIAL, RH.V_DESCRIPCION, T.V_CURP, T.V_IMSS ,
       			R.FECHA_ACCIDENTE, RHC.S_DESCRIPCION AS TIPO_RIESGO, R.DIAS_SUBSIDIADOS, R.PORCENTAJE_INCAPACIDAD, R.DEFUNCION, TO_CHAR(R.FECHA_ALTA, 'DD/MM/YYYY') AS FECHA_ALTA
       			FROM NO_PERSONAL T
       			INNER JOIN NO_RIESGO_IMPORTE_EMPLEADOS R ON T.IID_EMPLEADO = R.IID_EMPLEADO
            INNER JOIN PLAZA P ON P.IID_PLAZA = T.IID_PLAZA
            INNER JOIN NO_CONTRATO NC ON NC.IID_EMPLEADO = T.IID_EMPLEADO AND NC.IID_CONTRATO = T.IID_CONTRATO
            INNER JOIN RH_PUESTOS RH ON NC.IID_PUESTO = RH.IID_PUESTO
						INNER JOIN RH_FALTAS RH ON R.IID_EMPLEADO = RH.IID_EMPLEADO AND R.IID_CONSECUTIVO = RH.ID_FOLIO
						INNER JOIN rh_faltas_cat RHC ON RHC.ID_TIPO_FALTA = RH.ID_TIPO_FALTA
            WHERE TO_CHAR(R.FECHA_ALTA , 'yyyy') = TO_CHAR(SYSDATE, 'YYYY')-1
									AND RH.ID_TIPO_FALTA IN (13)
			 			ORDER BY R.FECHA_ALTA ASC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		//echo $sql;
		return $res_array;
	}
	/*====================== SQL DINAMICO ======================*/
	public function sql($option,$depto)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM DUAL";
		switch ($option) {
			case '1':
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -1), 'DD/MM/YYYY') mes2 FROM DUAL";
				break;
			case '2':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ";
				break;
			case '3':
				$sql = " SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep ";
				break;
			case '4':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
				break;
			default:
				$sql = "SELECT * FROM DUAL";
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
	/*====================== /*SQL DINAMICO ======================*/


	/*====================== VALIDA SI ES FECHA  ======================*/
	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}
	/*====================== /.VALIDA SI ES FECHA  ======================*/

	//PERSONAL ACTIVO
	public function widgets($plaza, $fecha)
	{
		$conn = conexion::conectar();
		$res_array = array();


			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$and_fecha_act = " AND per.d_fecha_ingreso < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
				$and_fecha_can = " AND can.fecha_cancelacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND can.fecha_cancelacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
			}

		$and_plaza = "";
		switch ($plaza) {
				case 'CORPORATIVO': $and_plaza = " AND per.iid_plaza = 2 "; break;
				case 'CÓRDOBA': $and_plaza = " AND per.iid_plaza = 3 "; break;
				case 'MÉXICO': $and_plaza = " AND per.iid_plaza = 4 "; break;
				case 'GOLFO': $and_plaza = " AND per.iid_plaza = 5 "; break;
				case 'PENINSULA': $and_plaza = " AND per.iid_plaza = 6 "; break;
				case 'PUEBLA': $and_plaza = " AND per.iid_plaza = 7 "; break;
				case 'BAJIO': $and_plaza = " AND per.iid_plaza = 8 "; break;
				case 'OCCIDENTE': $and_plaza = " AND per.iid_plaza = 17 "; break;
				case 'NORESTE': $and_plaza = " AND per.iid_plaza = 18 "; break;
				default: $and_plaza = ""; break;
			}

		$sql = "SELECT (
				SELECT COUNT(*) AS activo
				FROM no_personal per
				INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
				WHERE per.s_status = 1 ".$and_fecha_act.$and_plaza.") AS activo,
				(
				SELECT COUNT(*)
				FROM rh_cancelacion_contrato can
				INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato
				INNER JOIN no_contrato con ON con.iid_contrato = per.iid_contrato AND con.iid_empleado = per.iid_empleado
				WHERE per.s_status = 0 ".$and_fecha_can.$and_plaza."
				) AS baja
				FROM DUAL";
#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
		//echo $sql;
	}
}
