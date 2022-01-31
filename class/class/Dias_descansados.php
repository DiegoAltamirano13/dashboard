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
	public function widgetFaltas($fecha, $plaza, $tipo){
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}
		$andFecha = " t.d_fec_inicio >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND t.D_FEC_FIN <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) ";
		$andTipo = " AND t.ID_TIPO_FALTA IN (".$tipo.")";
		$andPlaza = " ";
		switch ($plaza) {
		  	case 'CORPORATIVO': $andPlaza = " AND p.iid_plaza = 2 "; break;
		    case 'CÓRDOBA': $andPlaza = " AND p.iid_plaza = 3 "; break;
		    case 'MÉXICO': $andPlaza = " AND p.iid_plaza = 4 "; break;
		    case 'GOLFO': $andPlaza = " AND p.iid_plaza = 5 "; break;
		    case 'PENINSULA': $andPlaza = " AND p.iid_plaza = 6 "; break;
		    case 'PUEBLA': $andPlaza = " AND p.iid_plaza = 7 "; break;
		    case 'BAJIO': $andPlaza = " AND p.iid_plaza = 8 "; break;
		    case 'OCCIDENTE': $andPlaza = " AND p.iid_plaza = 17 "; break;
		    case 'NORESTE': $andPlaza = " AND p.iid_plaza = 18 "; break;
		    default: $andPlaza = " "; break;
		}
		//Realiza conexion
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT SUM(T.C_DIAS_FALTA) AS TOTAL_FALTAS FROM RH_FALTAS T
       INNER JOIN NO_PERSONAL P ON P.IID_EMPLEADO = T.IID_EMPLEADO
       WHERE (".$andFecha.") ".$andPlaza.$andTipo."";
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
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}

		$andFecha = " d.d_fec_inicio >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND d.D_FEC_FIN <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) ";
		$andTipo = " AND D.ID_TIPO_FALTA IN (".$tipo.")";
		$andPlaza = " ";
		switch ($plaza) {
		  	case 'CORPORATIVO': $andPlaza = " AND np.iid_plaza = 2 "; break;
		    case 'CÓRDOBA': $andPlaza = " AND np.iid_plaza = 3 "; break;
		    case 'MÉXICO': $andPlaza = " AND np.iid_plaza = 4 "; break;
		    case 'GOLFO': $andPlaza = " AND np.iid_plaza = 5 "; break;
		    case 'PENINSULA': $andPlaza = " AND np.iid_plaza = 6 "; break;
		    case 'PUEBLA': $andPlaza = " AND np.iid_plaza = 7 "; break;
		    case 'BAJIO': $andPlaza = " AND np.iid_plaza = 8 "; break;
		    case 'OCCIDENTE': $andPlaza = " AND np.iid_plaza = 17 "; break;
		    case 'NORESTE': $andPlaza = " AND np.iid_plaza = 18 "; break;
		    default: $andPlaza = " "; break;
		}
		//Realiza conexion
		$conn = conexion::conectar();
		$res_array = array();
		//QUERY PARA CONSULTAR POR PLAZA
		$sql = "SELECT PLA.IID_PLAZA,
			       REPLACE(PLA.V_RAZON_SOCIAL, ' (ARGO)')AS PLAZA,
			       pla.v_siglas,
			       NVL((SELECT SUM(D.C_DIAS_FALTA)FROM RH_FALTAS D
			       INNER JOIN NO_PERSONAL NP ON D.IID_EMPLEADO = NP.IID_EMPLEADO
			       WHERE (".$andFecha.$andPlaza.$andTipo.")
			               AND NP.IID_PLAZA = PLA.IID_PLAZA), 0)AS DIAS_DESCANSADOS
						 FROM PLAZA PLA
						 WHERE PLA.IID_PLAZA IN (2, 3, 4, 5, 6, 7, 8, 17, 18)
						 ORDER BY PLA.IID_PLAZA";
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

/*GRAFICA POR MES DE DIAS DESCANSADOS DIEGO ALTAMIRANO SUAREZ ARGO 2019*/
	public function grafica_Mensual($fecha,$plaza,$tipo){
		$andFecha = substr($fecha, 6,4);
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}

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
									 #echo $sql;
									 $stid = oci_parse($conn,$sql);
									 oci_execute($stid);

									 while (($row = oci_fetch_assoc($stid)) != false) {
									 		$res_array[] = $row;
									 }
									 oci_free_statement($stid);
									 oci_close($conn);
									 return $res_array;
	}

	public function grafica_Gral($anio, $plaza, $tipo){
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}

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
		/*$sql = "SELECT SUM(NVL((SELECT SUM(D.C_DIAS_FALTA)
             FROM RH_FALTAS D
            INNER JOIN NO_PERSONAL NP ON D.IID_EMPLEADO = NP.IID_EMPLEADO
            WHERE (TO_CHAR(D.D_FEC_INICIO, 'YYYY') = '$anio' AND
                  TO_CHAR(D.D_FEC_FIN, 'YYYY') = '$anio')
              AND (TO_CHAR(D.D_FEC_INICIO, 'MM') = PLA.N_MES AND
                  TO_CHAR(D.D_FEC_FIN, 'MM') = PLA.N_MES)
              AND D.ID_TIPO_FALTA IN ($tipo)
              AND NP.IID_PLAZA IN ($in_plaza)),
           0)/((SELECT dias_laborables( $anio, R.N_MES) FROM RH_MESES_GRAFICAS R WHERE R.N_MES = PLA.N_MES)*( SELECT COUNT(per.iid_empleado) AS BAJA
                             FROM no_personal per
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                           AND con.iid_contrato = per.iid_contrato
										 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
													 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/$anio','mm/yyyy') )
                     WHERE per.iid_plaza IN($in_plaza)
                           AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/$anio', 'mm/yyyy')))
													 AND RCAN.FECHA_CANCELACION IS NULL
													 AND per.iid_empleado not in(209, 1, 2400)
												 ))) AS TOTAL
						  FROM RH_MESES_GRAFICAS PLA
						 ORDER BY PLA.N_MES";*/

			$sql ="SELECT SUM(NVL((SELECT SUM(D.C_DIAS_FALTA)
             FROM RH_FALTAS D
            INNER JOIN NO_PERSONAL NP ON D.IID_EMPLEADO = NP.IID_EMPLEADO
            WHERE (TO_CHAR(D.D_FEC_INICIO, 'YYYY') = $anio AND
                  TO_CHAR(D.D_FEC_FIN, 'YYYY') = $anio)
              AND (TO_CHAR(D.D_FEC_INICIO, 'MM') = PLA.N_MES AND
                  TO_CHAR(D.D_FEC_FIN, 'MM') = PLA.N_MES)
             	AND D.ID_TIPO_FALTA IN ($tipo)
              AND NP.IID_PLAZA IN ($in_plaza)),
           0)) AS TOTAL
						  FROM RH_MESES_GRAFICAS PLA
						 ORDER BY PLA.N_MES";
									 #echo $sql;
									 $stid = oci_parse($conn,$sql);
									 oci_execute($stid);

									 while (($row = oci_fetch_assoc($stid)) != false) {
									 		$res_array[] = $row;
									 }
									 oci_free_statement($stid);
									 oci_close($conn);
									 return $res_array;
	}

	public function grafica_Gral2($anio, $plaza, $tipo){
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}

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
		$sql = "SELECT SUM(NVL((SELECT SUM(D.C_DIAS_FALTA)
						 FROM RH_FALTAS D
						INNER JOIN NO_PERSONAL NP ON D.IID_EMPLEADO = NP.IID_EMPLEADO
						WHERE (TO_CHAR(D.D_FEC_INICIO, 'YYYY') = '$anio' AND
									TO_CHAR(D.D_FEC_FIN, 'YYYY') = '$anio')
							AND (TO_CHAR(D.D_FEC_INICIO, 'MM') = PLA.N_MES AND
									TO_CHAR(D.D_FEC_FIN, 'MM') = PLA.N_MES)
							AND D.ID_TIPO_FALTA IN ($tipo)
							AND NP.IID_PLAZA IN ($in_plaza)),
					 0)) AS TOTAL
							FROM RH_MESES_GRAFICAS PLA
						 ORDER BY PLA.N_MES";
									 #echo $sql;
									 $stid = oci_parse($conn,$sql);
									 oci_execute($stid);

									 while (($row = oci_fetch_assoc($stid)) != false) {
											$res_array[] = $row;
									 }
									 oci_free_statement($stid);
									 oci_close($conn);
									 return $res_array;
	}

	public function grafica_Gral3($anio, $plaza, $tipo){
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}

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
		$sql = "SELECT SUM(NVL((SELECT SUM(D.C_DIAS_FALTA)
             FROM RH_FALTAS D
            INNER JOIN NO_PERSONAL NP ON D.IID_EMPLEADO = NP.IID_EMPLEADO
            WHERE (TO_CHAR(D.D_FEC_INICIO, 'YYYY') = '$anio' AND
                  TO_CHAR(D.D_FEC_FIN, 'YYYY') = '$anio')
              AND (TO_CHAR(D.D_FEC_INICIO, 'MM') = PLA.N_MES AND
                  TO_CHAR(D.D_FEC_FIN, 'MM') = PLA.N_MES)
              AND D.ID_TIPO_FALTA IN ($tipo)
              AND NP.IID_PLAZA IN ($in_plaza)),
           0))/sum(((( SELECT SUM(per.i_antiguedad) AS BAJA
                             FROM no_personal per
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                           AND con.iid_contrato = per.iid_contrato
										 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
													 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/$anio','mm/yyyy') )
                     WHERE per.iid_plaza IN($in_plaza)
                           AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/$anio', 'mm/yyyy')))
													 AND RCAN.FECHA_CANCELACION IS NULL
													 AND per.iid_empleado not in(209, 1, 2400)
												 )/( SELECT COUNT(PER.IID_EMPLEADO) AS BAJA
                             FROM no_personal per
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                           AND con.iid_contrato = per.iid_contrato
										 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
													 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/$anio','mm/yyyy') )
                     WHERE per.iid_plaza IN($in_plaza)
                           AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/$anio', 'mm/yyyy')))
													 AND RCAN.FECHA_CANCELACION IS NULL
													 AND per.iid_empleado not in(209, 1, 2400)
												 ))*100)) AS TOTAL
  				FROM RH_MESES_GRAFICAS PLA
 					ORDER BY PLA.N_MES";
									 #echo $sql;
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
		if ($tipo == 'ALL') {
			$tipo = '1,2,3,4,5,6,7,8,9,10,11,13';
		}
		$andFecha = " d.d_fec_inicio >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND d.D_FEC_FIN <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  ";
		$andFec2 = " t.d_fec_inicio >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') ) AND t.D_FEC_FIN <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') ) ";
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
		$sql = "SELECT t.iid_empleado as empleado,
       			(np.v_nombre || ' ' || np.v_ape_pat || ' ' || np.v_ape_mat) as nombre,
       			pl.v_razon_social AS plaza,
       			NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 1
		               AND(".$andFecha.")),0)
       	 		as permiso_con_sueldo,
       			NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 2
		               AND (".$andFecha.")),0)
		       as falta_justificada,
		       NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 3
		               AND (".$andFecha.")), 0) as riesgo_trabajo,
		       NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 4
		               AND (".$andFecha.")), 0) as enfermedad_en_gral,
		      NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 5
		               AND (".$andFecha.")), 0) as maternidad,
		      NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 6
		               AND (".$andFecha.")), 0) as injustificada,
		      NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 7
		               AND (".$andFecha.")), 0) as retardos,
		       NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 8
		               AND (".$andFecha.")), 0) as trabajos_plaza,
		      NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 9
		               AND (".$andFecha.")), 0) as vacaciones,
		       NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 10
		               AND (".$andFecha.")), 0) as tiempo_tiempo,
		      NVL((select sum(d.c_dias_falta) from rh_faltas d
		               where d.iid_empleado = t.iid_empleado
		               and d.id_tipo_falta = 11
		               AND (".$andFecha.")), 0) as paternidad,
					NVL((select sum(d.c_dias_falta) from rh_faltas d
				 		               where d.iid_empleado = t.iid_empleado
				 		               and d.id_tipo_falta = 13
				 		               AND (".$andFecha.")), 0) as RIESGO_TRAYECTO
		from rh_faltas t, no_personal np , plaza pl
		WHERE t.iid_empleado = np.iid_empleado
		      AND np.iid_plaza = pl.iid_plaza
		      AND (".$andFec2.")
					AND pl.iid_plaza in (".$in_plaza.")
		group by t.iid_empleado, pl.v_razon_social, np.v_nombre,  np.v_ape_pat,  np.v_ape_mat";
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
				$and_fecha_cancel = " and RCAN.FECHA_CANCELACION <= trunc(to_date('".substr($fecha, 11, 10)."','dd/mm/yyyy') )" ;
				$and_fecha_act2 = " per.d_fecha_ingreso <= trunc(to_date('".substr($fecha,11,10)."','dd/mm/yyyy') )  AND ";
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
						SELECT count(per.iid_empleado)  FROM no_personal per
					 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
					 LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
					 AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO ".$and_fecha_cancel."
					 WHERE ".$and_fecha_act2." RCAN.FECHA_CANCELACION IS NULL AND per.iid_empleado not in(209, 1, 2400) ".$and_fecha_act.$and_plaza.") AS activo,
				(
				SELECT COUNT(*)
				FROM rh_cancelacion_contrato can
				INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato
				INNER JOIN no_contrato con ON con.iid_contrato = per.iid_contrato AND con.iid_empleado = per.iid_empleado
				WHERE per.s_status = 0  AND per.iid_empleado not in(209, 1, 2400) ".$and_fecha_can.$and_plaza."
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
