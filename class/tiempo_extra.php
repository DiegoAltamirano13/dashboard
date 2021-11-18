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

	/*====================== WIDGETS ======================*/
	public function widgetsTiempoExtra($fecha,$plaza)
	{
		$andFecha = " AND h.d_periodo_in >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND h.d_periodo_fi < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";

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
		$sql = "SELECT NVL(SUM(PERD.c_Horas_Extra), 0) AS horas_dobles,
				       NVL(SUM(PERD.C_HORAS_EXTRA2), 0) AS horas_triples,
				       NVL(SUM(PERD.C_EXENTO), 0) AS recibo,
				       NVL(SUM(PERD.C_GRAVADO), 0) AS RECIBO_TRIPLE
				FROM NO_NOMINA h,
				     NO_NOM_PER_AD PERD ,
				     NO_PERSONAL NPO,
				     PLAZA PL,
				     NO_DEPOSITOS DEP
				WHERE h.IID_PLAZA = PERD.IID_PLAZA
				      AND PERD.IID_EMPLEADO = NPO.IID_EMPLEADO
				      AND h.C_ANIO = PERD.C_ANIO
				      AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
				      AND PL.IID_PLAZA = PERD.IID_PLAZA
				      AND PERD.IID_PLAZA = DEP.IID_PLAZA
				      AND PERD.C_ANIO = DEP.C_ANIO
				      AND PERD.C_CONSECUTIVO = DEP.C_CONSECUTIVO
				      AND PERD.IID_EMPLEADO = DEP.IID_EMPLEADO
				      ".$andFecha."
				      AND PERD.IID_CONCEPTO = 12
				      AND PL.IID_PLAZA in (".$in_plaza.") ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false){
							$res_array[]= $row;
		}
							oci_free_statement($stid);
							oci_close($conn);
							#echo $sql;
							return $res_array;
	}
	/*====================== /*WIDGETS ======================*/
	/*GRAFICA ANUAL DE HORAS*/
	public function graficaAnualComp($anio,$plaza)
	{
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
		$sql = "SELECT NVL(SUM(PERD.c_Horas_Extra), 0) AS horas_dobles,
				       NVL(SUM(PERD.C_HORAS_EXTRA2), 0) AS horas_triples,
				       NVL(SUM(PERD.C_EXENTO), 0) AS recibo,
				       NVL(SUM(PERD.C_GRAVADO), 0) AS RECIBO_TRIPLE
				FROM NO_NOMINA h,
				     NO_NOM_PER_AD PERD ,
				     NO_PERSONAL NPO,
				     PLAZA PL,
				     NO_DEPOSITOS DEP
				WHERE h.IID_PLAZA = PERD.IID_PLAZA
				      AND PERD.IID_EMPLEADO = NPO.IID_EMPLEADO
				      AND h.C_ANIO = PERD.C_ANIO
				      AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
				      AND PL.IID_PLAZA = PERD.IID_PLAZA
				      AND PERD.IID_PLAZA = DEP.IID_PLAZA
				      AND PERD.C_ANIO = DEP.C_ANIO
				      AND PERD.C_CONSECUTIVO = DEP.C_CONSECUTIVO
				      AND PERD.IID_EMPLEADO = DEP.IID_EMPLEADO
							AND to_char(h.d_periodo_in, 'yyyy') = '$anio'
							AND to_char(h.d_periodo_fi, 'yyyy') = '$anio'
				      AND PERD.IID_CONCEPTO = 12
				      AND PL.IID_PLAZA in (".$in_plaza.") ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);



		while (($row = oci_fetch_assoc($stid)) != false){
							$res_array[]= $row;
		}
							oci_free_statement($stid);
							oci_close($conn);
							#echo $sql;
							return $res_array;
	}

	/*====================== GRAFICA DE NOMINA PAGADA ======================*/
	public function graficaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
		$andFecha = " AND h.d_periodo_in >= to_date('".substr($fecha,0,10)."','dd/mm/yyyy')  AND h.d_periodo_fi <= to_date('".substr($fecha,11,10)."','dd/mm/yyyy')  ";

		$andPlaza = " ";
		switch ($plaza) {
		  	case 'CORPORATIVO': $andPlaza = " AND pl.iid_plaza = 2 "; break;
		    case 'CÓRDOBA': $andPlaza = " AND pl.iid_plaza = 3 "; break;
		    case 'MÉXICO': $andPlaza = " AND pl.iid_plaza = 4 "; break;
		    case 'GOLFO': $andPlaza = " AND pl.iid_plaza = 5 "; break;
		    case 'PENINSULA': $andPlaza = " AND pl.iid_plaza = 6 "; break;
		    case 'PUEBLA': $andPlaza = " AND pl.iid_plaza = 7 "; break;
		    case 'BAJIO': $andPlaza = " AND pl.iid_plaza = 8 "; break;
		    case 'OCCIDENTE': $andPlaza = " AND pl.iid_plaza = 17 "; break;
		    case 'NORESTE': $andPlaza = " AND pl.iid_plaza = 18 "; break;
		     default: $andPlaza = "AND pl.iid_plaza in (2,3,4,5,6,7,8,17,18)"; break;
		}

		$andTipo = " AND nompag.concepto IN (".$tipo.") ";
		$andStatus = " AND nom.s_status IN (".$status.") ";
		$andContrato = " AND con.s_tipo_contrato IN (".$contrato.") ";
		$tipoPag = " And nom.i_tipo_nom_esp IN (0,1,2,3,4,5,6) ";
		$andDepto = " ";
		if( $depto != "ALL" ){
			$andDepto = " AND con.iid_depto = ".$depto." ";
		}

		$andArea = " ";
		if( $area != "ALL" ){
			$andArea = " AND con.iid_area = ".$area." ";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT pl.iid_plaza,
       REPLACE(PL.V_RAZON_SOCIAL, ' (ARGO)') AS PLAZA ,
       PL.V_SIGLAS,
                    (SELECT NVL(SUM(PERD.C_HORAS_EXTRA), 0)
						          FROM NO_NOMINA h, NO_NOM_PER_AD PERD, NO_PERSONAL NP, NO_DEPOSITOS DEP
						         WHERE  h.IID_PLAZA = PERD.IID_PLAZA
						           AND PERD.IID_EMPLEADO = NP.IID_EMPLEADO
						           AND h.C_ANIO = PERD.C_ANIO
						           AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
						           AND H.IID_PLAZA = dep.iid_plaza
						           and h.c_anio = dep.c_anio
						           and h.c_consecutivo = dep.c_consecutivo
						           and perd.iid_empleado = dep.iid_empleado
                          ".$andFecha.$andPlaza."
                          AND PL.IID_PLAZA = PERD.IID_PLAZA
                          AND PERD.IID_CONCEPTO = 12) AS DOBLES,
                     (SELECT NVL(SUM(PERD.c_Horas_Extra2), 0)
                     FROM NO_NOMINA h, NO_NOM_PER_AD PERD
                     WHERE h.IID_PLAZA = PERD.IID_PLAZA
                           AND h.C_ANIO = PERD.C_ANIO
                           AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
                           ".$andFecha.$andPlaza."
                           AND PERD.IID_CONCEPTO = 12
                           AND PL.IID_PLAZA = PERD.IID_PLAZA) AS TRIPLES,
									(SELECT NVL(SUM(PERD.C_EXENTO), 0) + NVL(SUM(PERD.C_GRAVADO), 0) AS RECIBO_TRIPLE
			 				FROM NO_NOMINA h,
			 				     NO_NOM_PER_AD PERD ,
			 				     NO_PERSONAL NPO,
			 				     NO_DEPOSITOS DEP
			 				WHERE h.IID_PLAZA = PERD.IID_PLAZA
			 				      AND PERD.IID_EMPLEADO = NPO.IID_EMPLEADO
			 				      AND h.C_ANIO = PERD.C_ANIO
			 				      AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
			 				      AND PL.IID_PLAZA = PERD.IID_PLAZA
			 				      AND PERD.IID_PLAZA = DEP.IID_PLAZA
			 				      AND PERD.C_ANIO = DEP.C_ANIO
			 				      AND PERD.C_CONSECUTIVO = DEP.C_CONSECUTIVO
			 				      AND PERD.IID_EMPLEADO = DEP.IID_EMPLEADO
			 							".$andFecha.$andPlaza."
			 				      AND PERD.IID_CONCEPTO = 12
			               AND PL.IID_PLAZA = PERD.IID_PLAZA) AS TOTAL
      FROM plaza pl WHERE pl.iid_plaza IN (2,3,4,5,6,7,8,17,18)
			group by pl.iid_plaza, PL.V_RAZON_SOCIAL , PL.V_SIGLAS order by pl.iid_plaza ";

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
	}
	/*====================== /*GRAFICA DE NOMINA PAGADA ======================*/
/*grafica nomina tiempo extra diego altamirano suarez*/
	public function graficaMensualTiempoExtr($fecha,$plaza){
		$andFecha = substr($fecha, 6,4);

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
		$sql = "SELECT pl.N_MES AS NUMERO_MES,
       PL.MES AS NOMBRE_MES,
                    (SELECT NVL(SUM(PERD.C_HORAS_EXTRA), 0)
                            FROM NO_NOMINA h, NO_NOM_PER_AD PERD
                     WHERE h.IID_PLAZA = PERD.IID_PLAZA
                           AND h.C_ANIO = PERD.C_ANIO
                           AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
                           AND to_char(h.d_periodo_in, 'YYYY') = '".$andFecha."'
                           AND to_char(h.d_periodo_fi, 'YYYY') = '".$andFecha."'
                           AND to_char(h.d_periodo_in, 'MM') = PL.N_MES
                           AND TO_CHAR(H.D_PERIODO_FI, 'MM') = PL.N_MES
                           AND PERD.IID_PLAZA IN (".$in_plaza.")
                           AND PERD.IID_CONCEPTO = 12) AS DOBLES,
                     (SELECT NVL(SUM(PERD.c_Horas_Extra2), 0)
                             FROM NO_NOMINA h, NO_NOM_PER_AD PERD
                      WHERE h.IID_PLAZA = PERD.IID_PLAZA
                            AND h.C_ANIO = PERD.C_ANIO
                            AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
                            AND to_char(h.d_periodo_in, 'YYYY') = '".$andFecha."'
                            AND to_char(h.d_periodo_fi, 'YYYY') = '".$andFecha."'
                            AND to_char(h.d_periodo_in, 'MM') = PL.N_MES
                            AND TO_CHAR(H.D_PERIODO_FI, 'MM') = PL.N_MES
                            AND PERD.IID_CONCEPTO = 12
                            AND PERD.IID_PLAZA IN (".$in_plaza.")) AS TRIPLES
														FROM RH_MESES_GRAFICAS pl";
					$stid = oci_parse($conn,$sql);
					oci_execute($stid);
					#echo $sql;
					while (($row = oci_fetch_assoc($stid))!= false) {
						$res_array[] = $row;
					}
					oci_free_statement($stid);
					oci_close($conn);
					return $res_array;
	}

/*grafica nomina tiempo extra diego altamirano suarez*/

	/*====================== TABLA DE NOMINA PAGADA ======================*/
	public function tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
		$andFecha = " AND h.d_periodo_in >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND h.d_periodo_fi < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
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

		$andTipo = " AND nompag.concepto IN (".$tipo.") ";
		$andStatus = " AND nom.s_status IN (".$status.") ";
		$andContrato = " AND con.s_tipo_contrato IN (".$contrato.") ";

		$andDepto = " ";
		if( $depto != "ALL" ){
			$andDepto = " AND con.iid_depto = ".$depto." ";
		}

		$andArea = " ";
		if( $area != "ALL" ){
			$andArea = " AND con.iid_area = ".$area." ";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql = $sql = "SELECT PERD.IID_EMPLEADO,
       npo.v_nombre || ' ' || npo.v_ape_pat || ' ' || npo.v_ape_mat as nombre,
       PL.IID_PLAZA,
       REPLACE(PL.V_RAZON_SOCIAL, ' (ARGO)') AS PLAZA,
       NVL(SUM(PERD.c_Horas_Extra), 0) AS horas_dobles,
       NVL(SUM(PERD.C_HORAS_EXTRA2), 0) AS horas_triples,
			 NVL(SUM(PERD.C_EXENTO), 0) AS pagado_doble,
			NVL(SUM(PERD.C_GRAVADO), 0) AS PAGO_TRIPLE
                     FROM NO_NOMINA h, NO_NOM_PER_AD PERD , NO_PERSONAL NPO, PLAZA PL, NO_DEPOSITOS DEP
                     WHERE h.IID_PLAZA = PERD.IID_PLAZA
                           AND PERD.IID_EMPLEADO = NPO.IID_EMPLEADO
                           AND h.C_ANIO = PERD.C_ANIO
                           AND h.C_CONSECUTIVO = PERD.C_CONSECUTIVO
                           AND PL.IID_PLAZA = PERD.IID_PLAZA
													 AND PERD.IID_PLAZA = DEP.IID_PLAZA
														AND PERD.C_ANIO = DEP.C_ANIO
														AND PERD.C_CONSECUTIVO = DEP.C_CONSECUTIVO
														AND PERD.IID_EMPLEADO = DEP.IID_EMPLEADO
                        	 ".$andFecha."
                           AND PERD.IID_CONCEPTO = 12
                           AND PL.IID_PLAZA in (".$in_plaza.")
                           GROUP BY PERD.IID_EMPLEADO,  npo.v_nombre , npo.v_ape_pat , npo.v_ape_mat, pl.iid_plaza, PL.V_RAZON_SOCIAL
                           ORDER BY perd.IID_EMPLEADO";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
	#	echo $sql;
		return $res_array;
	}
	/*====================== /*TABLA DE NOMINA PAGADA ======================*/


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


}
