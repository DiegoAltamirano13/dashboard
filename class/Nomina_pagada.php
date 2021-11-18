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
public function cantidadEmpleados($fecha, $plaza){
		$fecha_ini = substr($fecha,0,10);
		$fecha_fin = substr($fecha, 11, 10);

		$and_fecha_act2 = " per.d_fecha_ingreso <= trunc(to_date('".$fecha_fin."','dd/mm/yyyy') )  AND ";
		$and_fecha_cancel = " and RCAN.FECHA_CANCELACION <= trunc(to_date('".$fecha_fin."','dd/mm/yyyy') )" ;
		$conn = conexion::conectar();
		$res_array = array();
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


			$sql = "SELECT count(per.iid_empleado) AS EMPLEADOS FROM no_personal per
       INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
       LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
			 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO
			 ".$and_fecha_cancel."
       WHERE ".$and_fecha_act2." RCAN.FECHA_CANCELACION IS NULL AND per.iid_empleado <> 209 AND PER.IID_EMPLEADO <> 1
       AND CON.IID_PLAZA IN(".$in_plaza.")" ;

				$stid = oci_parse($conn, $sql);
				#echo $sql;
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid)) != false) {
					$res_array[]= $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;

	}

 /* funcion tabla */

public function table_Funcion($fecha, $n_plaza, $tipo){
	 $andFecha = "  AND to_date(nom.i_mes || '/' || nom.c_anio, 'mm/yyyy') >= to_date('".substr($fecha,3,7)."', 'mm/yyyy') AND to_date(nom.i_mes || '/' || nom.c_anio, 'mm/yyyy') <= to_date('".substr($fecha,14,7)."', 'mm/yyyy')";

	if ($n_plaza == 40) {
		$in_plaza = "2,3,4,5,6,7,8,17,18";
	}else {
		$in_plaza = $n_plaza;
	}



	 $imp_manuales = "";
	 $fecha_ini3 = substr($fecha,3,7);
	 $fecha_fin3 = substr($fecha,14,7);
	 $andDepto = " ";

	 $fecha_ini3 = substr($fecha,3,7);
	 $fecha_fin3 = substr($fecha,14,7);
	 $nom_especial ="And nom.i_tipo_nom_esp IN (0, 1, 2, 3, 4, 5, 6, 9, 11)";
	 if ($tipo < 23 OR $tipo == 33 OR $tipo == 25) {
	 	$sql = "SELECT X.IID_PLAZA,  Y.V_RAZON_SOCIAL, X.total AS TOTAL
       			FROM (SELECT NVL(SUM(DEP.C_IMPORTE), 0) AS total,
					                    DEP.IID_PLAZA
					                    FROM NO_NOMINA_MOV DEP
					                    INNER JOIN NO_NOMINA NOM ON DEP.IID_PLAZA = NOM.IID_PLAZA
					                                 AND DEP.IID_NUMNOMINA = NOM.IID_NUMNOMINA
					                                 AND DEP.C_ANIO = NOM.C_ANIO
					                                 AND DEP.C_CONSECUTIVO = NOM.C_CONSECUTIVO
					                    INNER JOIN NO_CONCEPTOS COP ON COP.IID_CONCEPTO = DEP.IID_CONCEPTO
					                    WHERE dep.iid_plaza IN ($in_plaza)
					                                 $andFecha
					                                 AND nom.s_status IN (3)
					                                 And COP.IID_CONCEPTO IN ($tipo)
					                                 GROUP BY DEP.IID_PLAZA) X
																					 INNER JOIN PLAZA Y ON X.IID_PLAZA = Y.IID_PLAZA
					 GROUP BY X.IID_PLAZA, X.TOTAL, Y.V_RAZON_SOCIAL
					 ORDER BY X.IID_PLAZA";
					 if ($tipo == 1 ) {
					 		#echo $sql;
					 };
	 }elseif ($tipo == 1000) {
		 $sql = "SELECT X.IID_PLAZA,  Y.V_RAZON_SOCIAL, X.total AS TOTAL
        			FROM (SELECT NVL(SUM(DEP.C_IMPORTE), 0) AS total,
 					                    DEP.IID_PLAZA
 					                    FROM NO_NOMINA_MOV DEP
 					                    INNER JOIN NO_NOMINA NOM ON DEP.IID_PLAZA = NOM.IID_PLAZA
 					                                 AND DEP.IID_NUMNOMINA = NOM.IID_NUMNOMINA
 					                                 AND DEP.C_ANIO = NOM.C_ANIO
 					                                 AND DEP.C_CONSECUTIVO = NOM.C_CONSECUTIVO
 					                    INNER JOIN NO_CONCEPTOS COP ON COP.IID_CONCEPTO = DEP.IID_CONCEPTO
 					                    WHERE dep.iid_plaza IN ($in_plaza)
 					                                 $andFecha
 					                                 AND nom.s_status IN (3)
 					                                 And COP.IID_CONCEPTO IN (1, 8, 9, 12, 17, 22, 18 ,10 ,11, 2, 33, 25)
 					                                 GROUP BY DEP.IID_PLAZA) X
 																					 INNER JOIN PLAZA Y ON X.IID_PLAZA = Y.IID_PLAZA
 					 GROUP BY X.IID_PLAZA, X.TOTAL, Y.V_RAZON_SOCIAL
 					 ORDER BY X.IID_PLAZA";
	 }
	 elseif ($tipo >= 23 AND $tipo  <> 33 AND $tipo  <> 25 AND $tipo <> 1000 ) {
		 switch($tipo){
			 case 23:
			 $enc = "SELECT SUM(NVL(t.c_importe_642,0)) AS TOTAL";
			 break;
			 case 24:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_aguinaldo,0)),0) AS TOTAL";
			 break;
			 case 32:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_sar,0)),0) AS TOTAL";
			 break;
			 case 26:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_cyv,0)),0) AS TOTAL";
			 break;
			 case 27:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_imss,0)),0) AS TOTAL";
			 break;
			 case 28:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_infonavit,0)),0) AS TOTAL";
			 break;
			 case 29:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_3_isn_mes,0)),0) AS TOTAL";
			 break;
			 case 30:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_impto_fom_edu,0)),0) AS TOTAL";
			 break;
			 case 31:
			 $enc = "SELECT NVL(SUM(NVL(t.c_importe_provisiones,0)),0) AS TOTAL";
			 break;
	 	}
		$sql = "$enc , P.IID_PLAZA, P.V_RAZON_SOCIAL
            FROM NO_NOMINA_GASTO_MENSUAL_DET T
						INNER JOIN PLAZA P ON T.IID_PLAZA = P.IID_PLAZA
            WHERE (TO_DATE(T.I_MES || '/' || T.I_ANIO, 'MM/YYYY') >=
               TO_DATE('".$fecha_ini3."', 'MM/YYYY') AND
               TO_DATE(T.I_MES || '/' || T.I_ANIO, 'MM/YYYY') <=
               TO_DATE('".$fecha_fin3."', 'MM/YYYY')
               AND T.IID_PLAZA IN ($in_plaza))
							 GROUP BY P.IID_PLAZA, P.V_RAZON_SOCIAL ";
	}
	 $conn = conexion::conectar();
	 $res_array = array();

	 $stid = oci_parse($conn, $sql);
	 oci_execute($stid);
	 //if ($n_plaza == 2) {
	 	#echo $sql;
	 //}
	 #
	 while (($row = oci_fetch_assoc($stid)) != false)
	 {
		 $res_array[]= $row;
	 }

	 oci_free_statement($stid);
	 oci_close($conn);

	 return $res_array;
 }
	/*====================== WIDGETS ======================*/

public function widgetsNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
		$andFecha = "  AND to_date(nom.i_mes || '/' || nom.c_anio, 'mm/yyyy') >= to_date('".substr($fecha,3,7)."', 'mm/yyyy') AND to_date(nom.i_mes || '/' || nom.c_anio, 'mm/yyyy') <= to_date('".substr($fecha,14,7)."', 'mm/yyyy')";
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
		$imp_manuales = "";
		$fecha_ini3 = substr($fecha,3,7);
		$fecha_fin3 = substr($fecha,14,7);
		$andDepto = " ";
		if( $depto != "ALL" ){
			$andDepto = " AND con.iid_depto = ".$depto." ";
		}

		$andArea = " ";
		if( $area != "ALL" ){
			$andArea = " AND con.iid_area = ".$area." ";
		}

		if ($tipo == 'ALL') {
			//$filtro_tipo = " And COP.IID_CONCEPTO IN (1, 8, 12, 17, 22, 18, 10, 11)";// SE AGREGO 2 Y 33
			$filtro_tipo = " AND COP.IID_CONCEPTO IN (1, 8, 9, 12, 17, 22, 18 ,10 ,11, 2, 33, 25)";
			$imp_manuales = " UNION ALL
				SELECT (NVL(SUM(NVL(t.c_importe_642,0)),0) + NVL(SUM(NVL(t.c_importe_aguinaldo,0)),0) +
				NVL(SUM(NVL(t.c_importe_sar,0)),0) + NVL(SUM(NVL(t.c_importe_cyv,0)),0) +
				NVL(SUM(NVL(t.c_importe_imss,0)),0) + NVL(SUM(NVL(t.c_importe_infonavit,0)),0) +
				NVL(SUM(NVL(t.c_importe_3_isn_mes,0)),0) + NVL(SUM(NVL(t.c_importe_impto_fom_edu,0)),0) +
				NVL(SUM(NVL(t.c_importe_provisiones,0)),0) ) AS TOTAL
				FROM NO_NOMINA_GASTO_MENSUAL_DET T
				WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
				AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
				AND T.IID_PLAZA IN ($in_plaza)";
		}
		elseif($tipo < 23 OR $tipo == 33 OR $tipo == 25){
			$filtro_tipo = " And COP.IID_CONCEPTO IN (".$tipo.")";
			$imp_manuales = "";

		}elseif($tipo >= 23 AND $tipo <>   33 AND $tipo <> 25){
			//echo "33333";
			switch($tipo){
				case 23:
					$filtro_tipo = " And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT SUM(NVL(t.c_importe_642,0)) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
					AND T.IID_PLAZA IN ($in_plaza)";
				break;
				case 24:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_aguinaldo,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
					AND T.IID_PLAZA IN ($in_plaza)";
				break;
				case 32:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_sar,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))";
				break;
				case 26:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_cyv,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
					AND T.IID_PLAZA IN ($in_plaza)";
				break;
				case 27:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_imss,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
					AND T.IID_PLAZA IN ($in_plaza)";
				break;
				case 28:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_infonavit,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
					AND T.IID_PLAZA IN ($in_plaza)";
				break;
				case 29:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_3_isn_mes,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))
					AND T.IID_PLAZA IN ($in_plaza)";
				break;
				case 30:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_impto_fom_edu,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))";
				break;
				case 31:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					//$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$imp_manuales = " UNION ALL
					SELECT NVL(SUM(NVL(t.c_importe_provisiones,0)),0) AS TOTAL
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini3."', 'MM/YYYY')
					AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin3."', 'MM/YYYY'))";
				break;
				default:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					break;
			}

			//$filtro_tipo = ")";
		}
		else {
			$filtro_tipo = " And COP.IID_CONCEPTO IN (".$tipo.")";
			$imp_manuales = "";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT SUM(total) AS TOTAL FROM (SELECT NVL(SUM(DEP.C_IMPORTE), 0) AS total
						 FROM NO_NOMINA_MOV DEP
						 INNER JOIN NO_NOMINA NOM ON DEP.IID_PLAZA = NOM.IID_PLAZA
						                                 AND DEP.IID_NUMNOMINA = NOM.IID_NUMNOMINA
						                                 AND DEP.C_ANIO = NOM.C_ANIO
						                                 AND DEP.C_CONSECUTIVO = NOM.C_CONSECUTIVO
						 INNER JOIN NO_CONCEPTOS COP ON COP.IID_CONCEPTO = DEP.IID_CONCEPTO
						 WHERE dep.iid_plaza IN ($in_plaza)
						   ".$andFecha."
						    ".$filtro_tipo.$imp_manuales.")";


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
	/*====================== /*WIDGETS ======================*/
public function widgetFondoAhorro($fecha, $plaza)
	{
		$andFecha = " d_fecha_aplicacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND d_fecha_aplicacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
		$andPlaza = " ";
		switch ($plaza) {
				case 'CORPORATIVO': $andPlaza = " AND iid_plaza = 2 "; break;
				case 'CÓRDOBA': $andPlaza = " AND iid_plaza = 3 "; break;
				case 'MÉXICO': $andPlaza = " AND iid_plaza = 4 "; break;
				case 'GOLFO': $andPlaza = " AND iid_plaza = 5 "; break;
				case 'PENINSULA': $andPlaza = " AND iid_plaza = 6 "; break;
				case 'PUEBLA': $andPlaza = " AND iid_plaza = 7 "; break;
				case 'BAJIO': $andPlaza = " AND iid_plaza = 8 "; break;
				case 'OCCIDENTE': $andPlaza = " AND iid_plaza = 17 "; break;
				case 'NORESTE': $andPlaza = " AND iid_plaza = 18 "; break;
				default: $andPlaza = " "; break;
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT SUM(C_VALES) AS MONTO_VALES FROM NO_DEPOSITOS
						WHERE ".$andFecha.$andPlaza."";
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

public function widgetFondoAhorro2($fecha, $plaza)
	{
		$andFecha = " d_fecha_aplicacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND d_fecha_aplicacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
		$andPlaza = " ";
		switch ($plaza) {
				case 'CORPORATIVO': $andPlaza = " AND iid_plaza = 2 "; break;
				case 'CÓRDOBA': $andPlaza = " AND iid_plaza = 3 "; break;
				case 'MÉXICO': $andPlaza = " AND iid_plaza = 4 "; break;
				case 'GOLFO': $andPlaza = " AND iid_plaza = 5 "; break;
				case 'PENINSULA': $andPlaza = " AND iid_plaza = 6 "; break;
				case 'PUEBLA': $andPlaza = " AND iid_plaza = 7 "; break;
				case 'BAJIO': $andPlaza = " AND iid_plaza = 8 "; break;
				case 'OCCIDENTE': $andPlaza = " AND iid_plaza = 17 "; break;
				case 'NORESTE': $andPlaza = " AND iid_plaza = 18 "; break;
				default: $andPlaza = " "; break;
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT SUM(C_AHORRO_PATRON) AS FONDO FROM NO_DEPOSITOS
						WHERE ".$andFecha.$andPlaza."";
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

public function widgetFondoAhorro3($fecha, $plaza)
	{
		$fecha_inicial = substr($fecha, 6, 4);
		$fecha_inicial2 = '01/01/'.$fecha_inicial;

		#echo $fecha_inicial2;
		$andFecha = " d_fecha_aplicacion >= trunc(to_date('".$fecha_inicial2."','dd/mm/yyyy') ) AND d_fecha_aplicacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
		$andPlaza = " ";
		switch ($plaza) {
				case 'CORPORATIVO': $andPlaza = " AND iid_plaza = 2 "; break;
				case 'CÓRDOBA': $andPlaza = " AND iid_plaza = 3 "; break;
				case 'MÉXICO': $andPlaza = " AND iid_plaza = 4 "; break;
				case 'GOLFO': $andPlaza = " AND iid_plaza = 5 "; break;
				case 'PENINSULA': $andPlaza = " AND iid_plaza = 6 "; break;
				case 'PUEBLA': $andPlaza = " AND iid_plaza = 7 "; break;
				case 'BAJIO': $andPlaza = " AND iid_plaza = 8 "; break;
				case 'OCCIDENTE': $andPlaza = " AND iid_plaza = 17 "; break;
				case 'NORESTE': $andPlaza = " AND iid_plaza = 18 "; break;
				default: $andPlaza = " "; break;
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT SUM(C_VALES) AS VALES_ACUMULADO FROM NO_DEPOSITOS
						WHERE ".$andFecha.$andPlaza."";
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

public function widgetFondoAhorro4($fecha, $plaza)
	{
		$fecha_inicial = substr($fecha, 6, 4);
		$fecha_inicial2 = '01/01/'.$fecha_inicial;

		#echo $fecha_inicial2;
		$andFecha = " d_fecha_aplicacion >= trunc(to_date('".$fecha_inicial2."','dd/mm/yyyy') ) AND d_fecha_aplicacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
		$andPlaza = " ";
		switch ($plaza) {
				case 'CORPORATIVO': $andPlaza = " AND iid_plaza = 2 "; break;
				case 'CÓRDOBA': $andPlaza = " AND iid_plaza = 3 "; break;
				case 'MÉXICO': $andPlaza = " AND iid_plaza = 4 "; break;
				case 'GOLFO': $andPlaza = " AND iid_plaza = 5 "; break;
				case 'PENINSULA': $andPlaza = " AND iid_plaza = 6 "; break;
				case 'PUEBLA': $andPlaza = " AND iid_plaza = 7 "; break;
				case 'BAJIO': $andPlaza = " AND iid_plaza = 8 "; break;
				case 'OCCIDENTE': $andPlaza = " AND iid_plaza = 17 "; break;
				case 'NORESTE': $andPlaza = " AND iid_plaza = 18 "; break;
				default: $andPlaza = " "; break;
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT SUM(C_VALES) AS FONDO_ACUMULADO FROM NO_DEPOSITOS
						WHERE ".$andFecha.$andPlaza."";
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
	/*====================== GRAFICA DE NOMINA PAGADA ======================*/
public function graficaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
		//$andFecha = " AND NOM.D_PERIODO_FI >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND NOM.D_PERIODO_FI < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
		$andFecha = "  AND to_date(nom.i_mes || '/' || nom.c_anio, 'mm/yyyy') >= to_date('".substr($fecha,3,7)."', 'mm/yyyy') AND to_date(nom.i_mes || '/' || nom.c_anio, 'mm/yyyy') <= to_date('".substr($fecha,14,7)."', 'mm/yyyy')";

		$andPlaza = " ";
		$andPlazaNC = " ";
		switch ($plaza) {
		  	case 'CORPORATIVO': $andPlaza = " AND dep.iid_plaza = 2 ";
				 										$andPlazaNC = " AND T.IID_PLAZA = 2 "; break;
		    case 'CÓRDOBA': $andPlaza = " AND dep.iid_plaza = 3 ";
												$andPlazaNC = " AND T.IID_PLAZA = 3 "; break;
		    case 'MÉXICO': $andPlaza = " AND dep.iid_plaza = 4 ";
											 $andPlazaNC = " AND T.IID_PLAZA = 4 "; break;
		    case 'GOLFO': $andPlaza = " AND dep.iid_plaza = 5 ";
											$andPlazaNC = " AND T.IID_PLAZA = 5 "; break;
		    case 'PENINSULA': $andPlaza = " AND dep.iid_plaza = 6 ";
													$andPlazaNC = " AND T.IID_PLAZA = 6 "; break;
		    case 'PUEBLA': $andPlaza = " AND dep.iid_plaza = 7 ";
											 $andPlazaNC = " AND T.IID_PLAZA = 7 "; break;
		    case 'BAJIO': $andPlaza = " AND dep.iid_plaza = 8 ";
											$andPlazaNC = " AND T.IID_PLAZA = 8 "; break;
		    case 'OCCIDENTE': $andPlaza = " AND dep.iid_plaza = 17 ";
													$andPlazaNC = " AND T.IID_PLAZA = 17 "; break;
		    case 'NORESTE': $andPlaza = " AND dep.iid_plaza = 18 ";
												$andPlazaNC = " AND T.IID_PLAZA = 18 "; break;
		    default: $andPlaza = "  ";
				 				 $andPlazaNC = " AND T.IID_PLAZA IN (2,3,4,5,6,7,8,17,18)"; break;
		}

		$andTipo = " AND nompag.concepto IN (".$tipo.") ";
		$andStatus = " AND nom.s_status IN (2, 3) ";
		$andContrato = " AND con.s_tipo_contrato IN (".$contrato.") ";
		//$tipoPag = " And nom.i_tipo_nom_esp IN (0,1,2,3,4,5,6,7,11) ";
		$andDepto = " ";
		if( $depto != "ALL" ){
			$andDepto = " AND con.iid_depto = ".$depto." ";
		}

		$andArea = " ";
		if( $area != "ALL" ){
			$andArea = " AND con.iid_area = ".$area." ";
		}

		$importes_manuales = "";
		$totales = "";
		$fecha_ini2 = substr($fecha,3,7);
		$fecha_fin2 = substr($fecha,14,7);
		if ($tipo == "ALL") {
			$filtro_tipo = "And COP.IID_CONCEPTO IN (1,8,9,12,17,22,18,10,11,2,33,25)";
			$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
			$importes_manuales = ",  NVL((SELECT NVL((NVL(SUM(t.c_importe_642), 0) +
					NVL(SUM(T.C_IMPORTE_AGUINALDO), 0) +
					NVL(SUM(T.C_IMPORTE_SAR), 0) + NVL(SUM(t.c_importe_cyv), 0) +
					NVL(SUM(t.c_importe_imss), 0) +
					NVL(SUM(t.c_importe_infonavit), 0) +
					NVL(SUM(t.c_importe_3_isn_mes), 0) +
					NVL(SUM(t.c_importe_impto_fom_edu), 0) +
					NVL(SUM(t.c_importe_provisiones),0) ),0)
			FROM NO_NOMINA_GASTO_MENSUAL_DET T
			WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
			AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
				AND PLA.IID_PLAZA = T.IID_PLAZA $andPlazaNC),0) AS IMPORTES_MANUALES";
		}
		elseif($tipo >= 23  AND $tipo <>  33 AND $tipo <> 25 AND $tipo <> 2 ){
			switch($tipo) {
				case 23:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
					$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_642), 0)),0)
                  FROM NO_NOMINA_GASTO_MENSUAL_DET T
				  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
				  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
                  AND PLA.IID_PLAZA = T.IID_PLAZA
			    ".$andPlazaNC."),0) AS IMPORTES_MANUALES";
					break;
					case 24:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_aguinaldo), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 32:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_sar), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 26:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_cyv), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 27:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_imss), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 28:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_infonavit), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 29:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_3_isn_mes), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 30:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_impto_fom_edu), 0)),0)
					  FROM NO_NOMINA_GASTO_MENSUAL_DET T
					  WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
					  AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
					  AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
					case 31:
						$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
						$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
						$importes_manuales = ", NVL((SELECT NVL((NVL(SUM(t.c_importe_provisiones), 0)),0)
						FROM NO_NOMINA_GASTO_MENSUAL_DET T
						WHERE (TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') >= TO_DATE('".$fecha_ini2."', 'MM/YYYY')
						AND TO_DATE(T.I_MES|| '/' ||T.I_ANIO, 'MM/YYYY') <= TO_DATE('".$fecha_fin2."', 'MM/YYYY'))
						AND PLA.IID_PLAZA = T.IID_PLAZA
						".$andPlazaNC."),0) AS IMPORTES_MANUALES";
						break;
				default:
					$filtro_tipo = "And COP.IID_CONCEPTO IN (2000)";
					break;
			}

		}
		elseif ($tipo < 23  OR $tipo ==  33 OR $tipo == 25  OR $tipo == 2) {
			$filtro_tipo = "And COP.IID_CONCEPTO IN (".$tipo.")";
			$totales = ", IMPORTES_MANUALES, (PAGADO + IMPORTES_MANUALES) AS TOTAL";
			$importes_manuales = ",  0 AS IMPORTES_MANUALES";
		}
		else {
			$filtro_tipo = "And COP.IID_CONCEPTO IN (".$tipo.")";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql = " SELECT IID_PLAZA, PLAZA, PAGADO".$totales." FROM(
		SELECT pla.iid_plaza,
		       REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza,
		       pla.v_siglas,
		       (SELECT NVL(SUM(DEP.C_IMPORTE), 0) FROM NO_NOMINA_MOV DEP
		         INNER JOIN NO_NOMINA NOM ON DEP.IID_PLAZA = NOM.IID_PLAZA
		                                           AND DEP.IID_NUMNOMINA = NOM.IID_NUMNOMINA
		                                           AND DEP.C_ANIO = NOM.C_ANIO
		                                           AND DEP.C_CONSECUTIVO = NOM.C_CONSECUTIVO
		               INNER JOIN NO_CONCEPTOS COP ON COP.IID_CONCEPTO = DEP.IID_CONCEPTO
		        WHERE dep.iid_plaza = pla.iid_plaza ".$andFecha.$andPlaza.$andStatus.$andDepto.$andArea.$filtro_tipo."
				) AS pagado ".$importes_manuales."
				FROM plaza pla
				WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ORDER BY pla.iid_plaza)";

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

public function graficaPorMes($fecha,$plaza,$tipo)
	{
		 $andFecha = substr($fecha, 6,4);
		 $in_plaza = "2,3,4,5,6,7,8,17,18";
		 $fecha_ini4 = substr($fecha,3,7);
		 $fecha_fin4 = substr($fecha,14,7);
		 $importes_manu = "";
		 switch($plaza){
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

		 if ($tipo == 'ALL') {
			 $andTipo = " And COP.IID_CONCEPTO IN (1, 8,9,12,17,22,18,10,11, 2, 33, 25)";
			 $importes_manu = ", (
				SELECT NVL(SUM(t.c_importe_642),0) + NVL(SUM(t.c_importe_aguinaldo),0) +
				NVL(SUM(t.c_importe_sar),0) + NVL(SUM(t.c_importe_cyv),0) + NVL(SUM(t.c_importe_imss),0) +
				NVL(SUM(t.c_importe_infonavit),0) + NVL(SUM(t.c_importe_3_isn_mes),0) +
				NVL(SUM(t.c_importe_impto_fom_edu),0) + NVL(SUM(t.c_importe_provisiones),0)
				FROM NO_NOMINA_GASTO_MENSUAL_DET T WHERE T.I_ANIO = ".$andFecha."
				AND t.i_mes = pla.n_mes
				AND T.IID_PLAZA IN ($in_plaza)) AS MANUALES ";

		}elseif($tipo >= 23  AND $tipo <>   33 AND $tipo <> 25){
			switch ($tipo) {
				case 23:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_642),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					 AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 24:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_aguinaldo),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					 AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 32:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_sar),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					 AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 26:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_cyv),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					 AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 27:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_imss),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					 AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 28:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_infonavit),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					 AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 29:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_3_isn_mes),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 30:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_impto_fom_edu),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				case 31:
					$andTipo = " And COP.IID_CONCEPTO IN (2000)";
					$importes_manu = ", (SELECT NVL(SUM(t.c_importe_provisiones),0)
					FROM NO_NOMINA_GASTO_MENSUAL_DET T
					WHERE  T.I_ANIO = ".$andFecha."
					AND t.i_mes = pla.n_mes) AS MANUALES";
					break;
				default:
					$andTipo = " And COP.IID_CONCEPTO IN (".$tipo.")";
					break;
			}
		}elseif($tipo < 23  OR $tipo ===   33 OR $tipo === 25){
			$andTipo = " And COP.IID_CONCEPTO IN (".$tipo.")";
			$importes_manu = ", 0 AS MANUALES";
		}else {
			$andTipo = " And COP.IID_CONCEPTO IN (".$tipo.")";
			$importes_manu = ", 0 AS MANUALES";
		}


		 $conn = conexion::conectar();
		 $res_array = array();

		 $sql = "SELECT N_MES, MES, PAGADO, (PAGADO + MANUALES) AS TOTAL FROM(
		 SELECT pla.n_mes, pla.mes,
       	 (SELECT NVL(SUM(DEP.C_IMPORTE), 0)
         FROM NO_NOMINA_MOV DEP
         INNER JOIN NO_NOMINA NOM ON DEP.IID_PLAZA = NOM.IID_PLAZA
                                 AND DEP.IID_NUMNOMINA = NOM.IID_NUMNOMINA
                                 AND DEP.C_ANIO = NOM.C_ANIO
				                                 AND DEP.C_CONSECUTIVO = NOM.C_CONSECUTIVO
				         INNER JOIN NO_CONCEPTOS COP ON COP.IID_CONCEPTO = DEP.IID_CONCEPTO
				         WHERE dep.iid_plaza IN ($in_plaza)
				           AND nom.c_anio = ".$andFecha."
						   AND nom.i_mes = To_number(PLA.N_MES, '99')
				           ".$andTipo."
								 AND nom.s_status IN (2, 3)) AS PAGADO".$importes_manu."
				  FROM RH_MESES_GRAFICAS pla
				 ORDER BY pla.n_mes)";
					#	echo $sql;
						$stid = oci_parse($conn, $sql);
						oci_execute($stid);

						while (($row = oci_fetch_assoc($stid)) != false)
						{
							$res_array[]= $row;
						}

						oci_free_statement($stid);
						oci_close($conn);

						#echo $sql;
						return $res_array;
	}
	/*====================== /*GRAFICA DE NOMINA PAGADA ======================*/

public function tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
		$andFecha = " AND dep.d_fecha_aplicacion >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND dep.d_fecha_aplicacion < trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) +1 ";
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

		$fecha_Anual = substr($fecha, 6, 4);

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

		if ($tipo == 'ALL') {
			$andTipo = " AND nompag.concepto IN (1, 8, 9,  12, 17, 22, 18, 10, 11) ";
		}
		else {
			$andTipo = " AND nompag.concepto IN (".$tipo.") ";
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT per.iid_empleado, per.v_nombre||' '||per.v_ape_pat||' '||per.v_ape_mat AS nombre, per.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas
				,pue.v_descripcion AS puesto, DECODE(con.s_tipo_contrato, 0,'DETERMINADO',1,'TIEMPO INDETERMINADO',2,'CONTRATO TERMINADO',3,'POR OBRA DETERMINADA','NO REGISTRADO') AS contrato
				,depa.v_descripcion AS departamento, ar.v_descripcion AS area, TO_CHAR(dep.d_fecha_aplicacion,'DD/MM/YYYY') d_fecha_aplicacion, TO_CHAR(nom.d_periodo_in,'DD/MM/YYYY') d_periodo_in, TO_CHAR(nom.d_periodo_fi,'DD/MM/YYYY') d_periodo_fi
				,DECODE(nompag.concepto,1,'NORMAL',10,'VACACIONES',21,'AGUINALDO',9,'PTU',18,'FINIQUITO',14,'CALCULO ANUAL','NO REGISTRADO') AS tipo_nomina
				,DECODE(nom.s_status,0,'REGISTRADA',1,'CALCULADA',2,'PAGADA/TIMBRADA',3,'CERRADA','NO REGISTRADO') AS status_nom
				,NVL(nompag.IMPORTE,0) AS deposito, NVL(dep.c_vales,0) AS vales, NVL(dep.c_ahorro_patron,0) AS ahorro_patron
				,(NOMPAG.IMPORTE) AS total, nom.v_descripcion AS detalle,
				vis_an_pag.MONTO_ANUAL
				FROM no_depositos dep
				INNER JOIN no_nomina nom ON nom.iid_plaza = dep.iid_plaza AND nom.c_anio = dep.c_anio AND nom.c_consecutivo = dep.c_consecutivo
				INNER JOIN no_personal per ON per.iid_empleado = dep.iid_empleado
				INNER JOIN no_contrato con ON con.iid_contrato = per.iid_contrato AND con.iid_empleado = per.iid_empleado
				INNER JOIN plaza pla ON pla.iid_plaza = dep.iid_plaza
				INNER JOIN rh_puestos pue ON pue.iid_puesto = con.iid_puesto
				INNER JOIN rh_cat_depto depa ON depa.iid_depto = con.iid_depto
				INNER JOIN nominas_pagadas nompag ON nompag.c_consecutivo = dep.c_consecutivo and nompag.c_anio = dep.c_anio and nompag.iid_empleado = dep.iid_empleado
				LEFT JOIN rh_cat_areas ar ON ar.iid_area = con.iid_area AND ar.iid_depto = con.iid_depto
				INNER JOIN vista_no_pagada_anual vis_an_pag ON vis_an_pag.IID_EMPLEADO = per.iid_empleado AND vis_an_pag.C_ANIO = ".$fecha_Anual."
				WHERE dep.iid_plaza IN (".$in_plaza.") ".$andFecha.$andTipo.$andStatus.$andContrato.$andDepto.$andArea." ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);


		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		#echo $sql;
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
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -0), 'DD/MM/YYYY') mes1, TO_CHAR(ADD_MONTHS(LAST_DAY( TO_DATE(SYSDATE) ), -0), 'DD/MM/YYYY') mes2 FROM DUAL";
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
			case '5':
					$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -0), 'MM/YYYY') mes1 FROM DUAL";
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

function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}
}
	/*====================== /.VALIDA SI ES FECHA  ======================*/
