
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

	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function grafica($fecha, $cliente, $plaza, $plazas)
	{
		$conn = conexion::conectar();
		$res_array = array();

		switch ($plaza) {
	    case 'COMPUTADOR':
				$plaza_in = 1;
				break;
	    case 'TELEFONO':
				$plaza_in = 2;
				break;
	    default:
				$plaza_in = "1,2";
				break;
	  }


		$andPlaza = " ";
		switch ($plazas) {
		  	case 'CORPORATIVO': $andPlaza = "2"; break;
		    case 'CÓRDOBA': $andPlaza = "3"; break;
		    case 'MÉXICO': $andPlaza = "4"; break;
		    case 'GOLFO': $andPlaza = "5"; break;
		    case 'PENINSULA': $andPlaza = "6"; break;
		    case 'PUEBLA': $andPlaza = "7"; break;
		    case 'BAJIO': $andPlaza = "8"; break;
		    case 'OCCIDENTE': $andPlaza = "17"; break;
		    case 'NORESTE': $andPlaza = "18"; break;
		    default: $andPlaza = "2,3, 4, 5, 6, 7, 8, 17, 18 "; break;
		}
	#	echo $plaza_in;

		$anio =  substr($fecha,0,4);
		//echo $ANIO;
		$mes =  substr($fecha,5,2);

		$sql = "SELECT SUM(S.N_MTTO_PROGRAMADOS) AS PROGRAMADOS,
									 SUM(S.N_MTTO_PROGRAMADOS)- SUM(S.N_MTTO_REALIZADOS) AS INEFECTIVIDAD,
									 SUM(S.N_MTTO_REALIZADOS) AS EFECTIVIDAD
					  FROM AD_SE_MANTENIMIENTOS S
						WHERE TO_CHAR(S.F_REALIZACION, 'YYYY') = '$anio'
							AND S.TIPO_SERVICIO IN ($plaza_in)
							AND S.IID_PLAZA IN ($andPlaza)";
		 		##echo $sql;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}
//Conseguirte
		#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);


				return $res_array;

	}

	function validateDate($date, $format = 'Y')
		{
		    $d = DateTime::createFromFormat($format, $date);
		    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		    return $d && $d->format($format) === $date;
		}

// total pagos
	public function graficaMensual($cliente, $fecha, $plaza, $plazas){

		switch ($plaza) {
	    case 'COMPUTADOR':
				$plaza_in = 1;
				break;
	    case 'TELEFONO':
				$plaza_in = 2;
				break;
	    default:
				$plaza_in = "1,2";
				break;
	  }

		$andPlaza = " ";
		switch ($plazas) {
		  	case 'CORPORATIVO': $andPlaza = "2"; break;
		    case 'CÓRDOBA': $andPlaza = "3"; break;
		    case 'MÉXICO': $andPlaza = "4"; break;
		    case 'GOLFO': $andPlaza = "5"; break;
		    case 'PENINSULA': $andPlaza = "6"; break;
		    case 'PUEBLA': $andPlaza = "7"; break;
		    case 'BAJIO': $andPlaza = "8"; break;
		    case 'OCCIDENTE': $andPlaza = "17"; break;
		    case 'NORESTE': $andPlaza = "18"; break;
		    default: $andPlaza = "2,3, 4, 5, 6, 7, 8, 17, 18 "; break;
		}

		$anio =  substr($fecha,0,4);
		//echo $ANIO;
		$mes =  substr($fecha,5,2);
		//echo $MES;

		$conn = conexion::conectar();
		$res_array = array();

$sql = "SELECT NVL(SUM(S.N_MTTO_PROGRAMADOS), 0) AS PROGRAMADOS,
       NVL(SUM(S.N_MTTO_PROGRAMADOS) - SUM(S.N_MTTO_REALIZADOS), 0) AS INEFECTIVIDAD,
       NVL(SUM(S.N_MTTO_REALIZADOS), 0) AS EFECTIVIDAD,
       P.V_RAZON_SOCIAL,
       P.IID_PLAZA
  FROM AD_SE_MANTENIMIENTOS S
  RIGHT OUTER JOIN PLAZA P ON S.IID_PLAZA = P.IID_PLAZA AND TO_CHAR(S.F_REALIZACION, 'YYYY') = '$anio'
																												AND S.TIPO_SERVICIO IN ($plaza_in)
																												AND S.IID_PLAZA IN ($andPlaza)
	 WHERE P.I_EMPRESA_PADRE = 1
	 GROUP BY P.V_RAZON_SOCIAL, P.IID_PLAZA
	 ORDER BY P.IID_PLAZA";

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

	public function graficaMensual2($cliente, $fecha, $plaza, $plazas){

		switch ($plaza) {
	    case 'COMPUTADOR':
				$plaza_in = 1;
				break;
	    case 'TELEFONO':
				$plaza_in = 2;
				break;
	    default:
				$plaza_in = "1,2";
				break;
	  }
		$andPlaza = " ";
		switch ($plazas) {
				case 'CORPORATIVO': $andPlaza = "2"; break;
				case 'CÓRDOBA': $andPlaza = "3"; break;
				case 'MÉXICO': $andPlaza = "4"; break;
				case 'GOLFO': $andPlaza = "5"; break;
				case 'PENINSULA': $andPlaza = "6"; break;
				case 'PUEBLA': $andPlaza = "7"; break;
				case 'BAJIO': $andPlaza = "8"; break;
				case 'OCCIDENTE': $andPlaza = "17"; break;
				case 'NORESTE': $andPlaza = "18"; break;
				default: $andPlaza = "2,3, 4, 5, 6, 7, 8, 17, 18 "; break;
		}

		$anio =  substr($fecha,0,4);
		//echo $ANIO;
		$mes =  substr($fecha,5,2);
		//echo $MES;

		$conn = conexion::conectar();
		$res_array = array();

$sql = "SELECT P.IID_PLAZA,
       P.V_RAZON_SOCIAL,
       NVL(SUM(S.N_MTTO_PROGRAMADOS), 0) AS PROGRAMADOS,
       NVL(SUM(S.N_MTTO_PROGRAMADOS) - SUM(S.N_MTTO_REALIZADOS), 0) AS INEFECTIVIDAD,
       NVL(SUM(S.N_MTTO_REALIZADOS), 0) AS EFECTIVIDAD,
       TO_CHAR(S.F_REALIZACION, 'DD/MM/YYYY') AS FECHA,
       NVL(SUM(S.N_COSTO), 0) AS COSTO,
       S.V_OBSERVACIONES,
			 S.TIPO_SERVICIO
  FROM AD_SE_MANTENIMIENTOS S
 RIGHT OUTER JOIN PLAZA P ON S.IID_PLAZA = P.IID_PLAZA
 WHERE P.I_EMPRESA_PADRE = 1
   AND TO_CHAR(S.F_REALIZACION, 'YYYY') = '$anio'
	 AND S.TIPO_SERVICIO IN ($plaza_in)
	 AND S.IID_PLAZA IN ($andPlaza)
 GROUP BY P.V_RAZON_SOCIAL, P.IID_PLAZA, S.F_REALIZACION, S.V_OBSERVACIONES, S.TIPO_SERVICIO
 ORDER BY P.IID_PLAZA";

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

	/*++++++++++++++++++++++++ SQL TABLA DETALLE BAJA ++++++++++++++++++++++++*/
	public function tablaBaja($cliente, $fecha)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND EE.ID_CLIENTE = $cliente";
		}


		$fecha_ini =  substr($fecha,0,10);
		$fecha_fin = substr($fecha,11,10);
		$where_fechas = " WHERE ENCE.FECHA_PROG >= TO_DATE('$fecha_ini', 'DD/MM/YYYY') AND ENCE.FECHA_PROG <= TO_DATE('$fecha_fin', 'DD/MM/YYYY')";



		$sql = "SELECT ENCE.ID_ENCUESTA, CL.V_RAZON_SOCIAL, ENCE.USUARIO, ENCE.PUESTO, TO_CHAR(ENCE.FECHA_PROG, 'DD/MM/YYYY') AS FECHA_PROG, TO_CHAR(ENCE.FECHA, 'DD/MM/YYYY') AS FECHA
       			FROM AD_SGC_ENCUESTA_ENC ENCE
       			INNER JOIN CLIENTE CL ON ENCE.ID_CLIENTE = CL.IID_NUM_CLIENTE
						$where_fechas
						$where_cliente
			 			ORDER BY ENCE.ID_ENCUESTA ";

			#	echo $sql;
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


	public function tablaBajaDet($encuesta)
	{
		$conn = conexion::conectar();
		$res_array = array();

		if ($cliente == "ALL") {
			$where_cliente = " ";
		}else {
			$where_cliente = " AND EE.ID_CLIENTE = $cliente";
		}


		$fecha_ini =  substr($fecha,0,10);
		$fecha_fin = substr($fecha,11,10);
		$where_fechas = "AND ENCE.FECHA_PROG >= TO_DATE('$fecha_ini', 'DD/MM/YYYY') AND ENCE.FECHA_PROG <= TO_DATE('$fecha_fin', 'DD/MM/YYYY')";


		$sql = "SELECT ENCP.PREGUNTA,
						       ENCP.RESP_SIONO,
						       ENCR.RESPUESTA,
						       ENCR.RESPUESTA2,
						       CASE
						            WHEN ENCP.RESP_SIONO = ENCR.RESPUESTA THEN 'NEGATIVA'
						            WHEN ENCP.RESP_SIONO <> ENCR.RESPUESTA THEN 'POSITIVA'
						       END AS TIPO_RES
						FROM AD_SGC_ENCUESTA_ENC ENCE
						         INNER JOIN AD_SGC_ENCUESTA_DET ENCD ON ENCE.ID_ENCUESTA = ENCD.ID_ENCUESTA
						         INNER JOIN AD_SGC_ENCUESTA_PREGUNTA ENCP ON ENCP.ID_PREGUNTA = ENCD.ID_PREGUNTA
						         LEFT OUTER JOIN AD_SGC_ENCUESTA_SUBPREGUNTA ENDS ON ENDS.ID_SUBPREGUNTA = ENCD.ID_SUBPREGUNTA AND ENDS.ID_PREGUNTA = ENCP.ID_PREGUNTA
						         INNER JOIN AD_SGC_ENCUESTA_RESPUESTA ENCR ON ENCR.ID_RESPUESTA = ENCD.ID_RESPUESTA
						         WHERE ENCE.ID_ENCUESTA =  $encuesta";

			#	echo $sql;
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
					echo $sql;
					break;
				case '2':
					$sql = "SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep";
					break;
				case '3':
					$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
					break;
					case '4':
						if ($depto <> "ALL"){
							$where_plaza = " AND REPLACE(P.v_razon_social, ' (ARGO)') LIKE '$depto'" ;
							$wheredef = "	= (SELECT P.IID_PLAZA FROM PLAZA P WHERE P.IID_PLAZA IN (2,3,4,5,6,7,8,17,18) $where_plaza)  AND ALM.S_STATUS = 1 ";
						}
						else {
							$wheredef = "  IN (2,3,4,5,6,7,8,17,18)  AND ALM.S_STATUS = 1 ";
						}
						$sql = "SELECT ALM.V_NOMBRE, ALM.IID_ALMACEN, ALM.IID_PLAZA FROM ALMACEN ALM WHERE ALM.IID_PLAZA $wheredef";
										echo $sql;
						break;

						case '5':
						if ($depto == "ALL") {
							$where = " ";
						}else {
							$where = " AND c.iid_almacen = $depto";
						}
							$sql = "SELECT DISTINCT a.iid_num_cliente, d.v_razon_social, d.v_nombre_corto
											  FROM co_convenio_contrato a
											 INNER JOIN co_convenio_contrato_anexos b ON b.nid_folio = a.nid_folio
											                                         AND b.s_status = 1
											 INNER JOIN co_convenio_contrato_almacen c ON c.nid_folio = a.nid_folio
											                                          AND c.s_tipo = a.s_tipo
											 INNER JOIN cliente d ON d.iid_num_cliente = a.iid_num_cliente
											 WHERE a.s_status IN (2, 3)
											   			$where";
															#echo $sql;
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

	public function sql($option,$depto,$plaza)
		{
			$conn = conexion::conectar();
			$res_array = array();

			$sql = "SELECT * FROM DUAL";
			switch ($option) {
				case '1':
					$sql = "SELECT TO_CHAR(TRUNC(SYSDATE, 'MM'), 'YYYY') mes1 FROM DUAL";
					break;
				case '2':
					$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";
					break;
				case '3':
					$sql = " SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep ";
					break;
				case '4':
					$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
					break;
				case '5':
					$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91 , 68, 9) and v_sscuenta = 0 and v_cuenta_activa = 1
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
