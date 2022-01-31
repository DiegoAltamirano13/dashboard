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
	public function graficaNomina($fecha,$plaza)
	{
		$andFecha = substr($fecha, 6,4);
		$fecha2 = $andFecha -1 ;
		$fecha3 = $fecha2 -1;
		#echo $andFecha;
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
		//Realiza conexion
		$conn = conexion::conectar();
		$res_array = array();
		//QUERY PARA CONSULTAR POR PLAZA
		$sql = "SELECT D.ID_PLAZA AS ID_PLAZA ,
						D.PLAZA_DECODE, COUNT(D.TOTAL_PED_ANO1) as TOTAL_PED_ANO1, COUNT(D.TOTAL_PED_ANO2) as TOTAL_PED_ANO2, COUNT(D.TOTAL_PED_ANO3) as TOTAL_PED_ANO3 FROM(
SELECT  PLA.IID_PLAZA AS ID_PLAZA,
					decode(to_char(pla.iid_plaza),
					3,'CÓRDOBA',
					4,'MÉXICO',
					5,'GOLFO',
					6,'PENINSULA',
					7,'PUEBLA',
					8,'BAJIO',
					17,'OCCIDENTE',
					18,'NORESTE'
								) as plaza_decode,
				cot_enc.vid_cve_ped_ext AS total_ped_ano1, TO_CHAR(NULL) as total_ped_ano2, to_char(null) as total_ped_ano3
        FROM op_ce_fechas_ped_ext fec
				LEFT OUTER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio AND (COT_ENC.VID_CVE_PED_EXT = 'G1' OR COT_ENC.VID_CVE_PED_EXT = 'E1' OR COT_ENC.VID_CVE_PED_EXT = 'K2') AND cot_enc.i_status >=4
				LEFT OUTER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
        WHERE pla.iid_plaza in (2,3,4,5,6,7,8,17,18) AND fec.iid_cve_tipo = 2  AND to_char(fec.d_Fecha, 'yyyy') = ".$andFecha."
UNION ALL
SELECT  PLA.IID_PLAZA AS ID_PLAZA,
					decode(to_char(pla.iid_plaza),
					3,'CÓRDOBA',
					4,'MÉXICO',
					5,'GOLFO',
					6,'PENINSULA',
					7,'PUEBLA',
					8,'BAJIO',
					17,'OCCIDENTE',
					18,'NORESTE'
								) as plaza_decode,
				TO_CHAR(NULL) AS total_ped_ano1, cot_enc.vid_cve_ped_ext as total_ped_ano2, to_char(null) as total_ped_ano3
        FROM op_ce_fechas_ped_ext fec
				LEFT OUTER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio AND (COT_ENC.VID_CVE_PED_EXT = 'G1' OR COT_ENC.VID_CVE_PED_EXT = 'E1' OR COT_ENC.VID_CVE_PED_EXT = 'K2') AND cot_enc.i_status >=4
				LEFT OUTER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
        WHERE pla.iid_plaza in (2,3,4,5,6,7,8,17,18) AND fec.iid_cve_tipo = 2  AND to_char(fec.d_fecha, 'yyyy') = ".$fecha2."
UNION ALL
SELECT  PLA.IID_PLAZA AS ID_PLAZA,
					decode(to_char(pla.iid_plaza),
					3,'CÓRDOBA',
					4,'MÉXICO',
					5,'GOLFO',
					6,'PENINSULA',
					7,'PUEBLA',
					8,'BAJIO',
					17,'OCCIDENTE',
					18,'NORESTE'
								) as plaza_decode,
				TO_CHAR(NULL) AS total_ped_ano1,  to_char(null)  as total_ped_ano2, COT_ENC.VID_CVE_PED_EXT as total_ped_ano3
        FROM op_ce_fechas_ped_ext fec
				LEFT OUTER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio AND (COT_ENC.VID_CVE_PED_EXT = 'G1' OR COT_ENC.VID_CVE_PED_EXT = 'E1' OR COT_ENC.VID_CVE_PED_EXT = 'K2') AND cot_enc.i_status >=4
				LEFT OUTER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
        WHERE pla.iid_plaza in (2,3,4,5,6,7,8,17,18) AND fec.iid_cve_tipo = 2  AND to_char(fec.d_fecha, 'yyyy') = ".$fecha3."
)D
GROUP BY D.ID_PLAZA, D.PLAZA_DECODE
ORDER BY D.ID_PLAZA";
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
	public function grafica_Mensual($fecha,$plaza){
		$andFecha = substr($fecha, 6,4);
		$fecha2 = $andFecha -1 ;
		$fecha3 = $fecha2 -1;
		#echo $andFecha;
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
		$sql = "SELECT D.N_MES, D.MES, COUNT(D.TOTAL_PED_ANO1) as TOTAL_PED_ANO1, COUNT(D.TOTAL_PED_ANO2) as TOTAL_PED_ANO2, COUNT(D.TOTAL_PED_ANO3) as TOTAL_PED_ANO3 FROM(
SELECT  RM.N_MES, RM.MES, cot_enc.vid_cve_ped_ext AS total_ped_ano1, TO_CHAR(NULL) as total_ped_ano2, to_char(null) as total_ped_ano3
        FROM RH_MESES_GRAFICAS RM
        LEFT OUTER JOIN op_ce_fechas_ped_ext fec ON TO_CHAR(FEC.D_FECHA, 'MM') = RM.N_MES and rm.n_mes in(01,02,03,04,05,06,07,08,09,10,11,12) AND fec.iid_cve_tipo = 2  AND to_char(fec.d_fecha, 'yyyy') = ".$andFecha."
				LEFT OUTER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio AND (COT_ENC.VID_CVE_PED_EXT = 'G1' OR COT_ENC.VID_CVE_PED_EXT = 'E1' OR COT_ENC.VID_CVE_PED_EXT = 'K2') AND cot_enc.i_status >=4
				LEFT OUTER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza  and pla.iid_plaza in (".$in_plaza.")
UNION ALL
SELECT  RM.N_MES, RM.MES, TO_CHAR(NULL) AS total_ped_ano1, cot_enc.vid_cve_ped_ext as total_ped_ano2, to_char(null) as total_ped_ano3
        FROM RH_MESES_GRAFICAS RM
        LEFT OUTER JOIN op_ce_fechas_ped_ext fec ON TO_CHAR(FEC.D_FECHA, 'MM') = RM.N_MES and rm.n_mes in(01,02,03,04,05,06,07,08,09,10,11,12) AND fec.iid_cve_tipo = 2  AND to_char(fec.d_fecha, 'yyyy') = ".$fecha2."
				LEFT OUTER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio AND (COT_ENC.VID_CVE_PED_EXT = 'G1' OR COT_ENC.VID_CVE_PED_EXT = 'E1' OR COT_ENC.VID_CVE_PED_EXT = 'K2') AND cot_enc.i_status >=4
				LEFT OUTER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza  and pla.iid_plaza in (".$in_plaza.")
UNION ALL
SELECT  RM.N_MES, RM.MES, TO_CHAR(NULL) AS total_ped_ano1,  to_char(null)  as total_ped_ano2, COT_ENC.VID_CVE_PED_EXT as total_ped_ano3
        FROM RH_MESES_GRAFICAS RM
        LEFT OUTER JOIN op_ce_fechas_ped_ext fec ON TO_CHAR(FEC.D_FECHA, 'MM') = RM.N_MES and rm.n_mes in(01,02,03,04,05,06,07,08,09,10,11,12) AND fec.iid_cve_tipo = 2  AND to_char(fec.d_fecha, 'yyyy') = ".$fecha3."
				LEFT OUTER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio AND (COT_ENC.VID_CVE_PED_EXT = 'G1' OR COT_ENC.VID_CVE_PED_EXT = 'E1' OR COT_ENC.VID_CVE_PED_EXT = 'K2') AND cot_enc.i_status >=4
				LEFT OUTER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza and pla.iid_plaza in (".$in_plaza.")
)D
GROUP BY D.N_MES, D.MES
ORDER BY D.N_MES";
									#echo $sql;
									 $stid = oci_parse($conn,$sql);
									 oci_execute($stid);

									 while (($row = oci_fetch_assoc($stid)) != false) {
									 		$res_array[] = $row;
									 }
									 oci_free_statement($stid);
									 oci_close($conn);
									 #echo $sql;
									 return $res_array;
	}

/*TERMINA GRAFICA POR MES ARGO 2019*/

	/*====================== TABLA DE NOMINA PAGADA ======================*/
	public function tablaNomina($fecha,$plaza,$tipo,$status,$contrato,$depto,$area)
	{
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
		               AND (".$andFecha.")), 0) as paternidad
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
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";
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
