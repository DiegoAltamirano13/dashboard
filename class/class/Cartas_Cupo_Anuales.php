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
		$sql = "SELECT D.ID_PLAZA, D.PLAZA_DECODE, COUNT(DISTINCT(D.TOTAL_CCE)) AS CCE_ANIO1, COUNT(D.total_cpe) AS CPE_ANIO1,
                       COUNT(DISTINCT(D.TOTAL_CCE2)) AS CCE_ANIO2, COUNT(D.TOTAL_CPE2) AS CPE_ANIO2,
                       COUNT(DISTINCT(D.TOTAL_CCE3)) AS CCE_ANIO3, COUNT(D.TOTAL_CPE3) AS CPE_ANIO3 FROM
(
    SELECT pla.iid_plaza AS id_plaza,
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
		       pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas,
           DECODE (cupo.v_status, 'RD',
           case when ( to_char(cupo.d_fecha_arribo, 'yyyy') = ".$andFecha.") then (dep.vno_ped_imp) end
           )  AS total_cce,
           DECODE (cupo.v_status, 'PE',
           case when (to_char(dep.d_plazo_dep_ini, 'yyyy') = ".$andFecha.") then (cupo.iid_almacen) end
           ) AS total_cpe,
           TO_CHAR(NULL) AS TOTAL_CCE2,
           TO_NUMBER(TO_CHAR(NULL)) AS TOTAL_CPE2,
           TO_CHAR(NULL) AS TOTAL_CCE3,
           TO_NUMBER(TO_CHAR(NULL)) AS TOTAL_CPE3
    FROM  plaza pla
    LEFT OUTER JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
    LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
    INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
    WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18)
UNION ALL
    SELECT pla.iid_plaza AS id_plaza,
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
		       pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE,
           DECODE (cupo.v_status, 'RD',
           case when ( to_char(cupo.d_fecha_arribo, 'yyyy') = ".$fecha2.") then (dep.vno_ped_imp) end
           )  AS total_cce2,
           DECODE (cupo.v_status, 'PE',
           case when (to_char(dep.d_plazo_dep_ini, 'yyyy') = ".$fecha2.") then (cupo.iid_almacen) end
           ) AS total_cpe2,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE
        FROM  plaza pla
        LEFT OUTER JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
        LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
        INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
        WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18)
UNION ALL
      SELECT pla.iid_plaza AS id_plaza,
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
		       pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE,
           DECODE (cupo.v_status, 'RD',
           case when ( to_char(cupo.d_fecha_arribo, 'yyyy') = ".$fecha3.") then (dep.vno_ped_imp) end
           )  AS total_cce3,
           DECODE (cupo.v_status, 'PE',
           case when (to_char(dep.d_plazo_dep_ini, 'yyyy') = ".$fecha3." ) then (cupo.iid_almacen) end
           ) AS total_cpe3
    FROM  plaza pla
    LEFT OUTER JOIN almacen alm ON alm.iid_plaza = pla.iid_plaza
    LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
    INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
    WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18)
) D
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
		$sql = "SELECT D.N_MES, D.MES, COUNT(DISTINCT(D.TOTAL_CCE)) AS CCE_ANO1, COUNT(D.total_cpe) AS CPE_ANIO1,
                       COUNT(DISTINCT(D.TOTAL_CCE2)) AS CCE_ANIO2, COUNT(D.TOTAL_CPE2) AS CPE_ANIO2,
                       COUNT(DISTINCT(D.TOTAL_CCE3)) AS CCE_ANIO3, COUNT(D.TOTAL_CPE3) AS CPE_ANIO3 FROM
(
    SELECT pla.n_mes,
           pla.mes,
           DECODE (cupo.v_status, 'RD',
           case when ( to_char(cupo.d_fecha_arribo, 'yyyy') = ".$andFecha."
                       AND to_char(cupo.d_fecha_arribo, 'mm') = pla.n_mes ) then (dep.vno_ped_imp) end
           )  AS total_cce,
           DECODE (cupo.v_status, 'PE',
           case when (to_char(dep.d_plazo_dep_ini, 'yyyy') = ".$andFecha."
                       AND to_char(dep.d_plazo_dep_ini, 'mm') = pla.n_mes) then (cupo.iid_almacen) end
           ) AS total_cpe,
           TO_CHAR(NULL) AS TOTAL_CCE2,
           TO_NUMBER(TO_CHAR(NULL)) AS TOTAL_CPE2,
           TO_CHAR(NULL) AS TOTAL_CCE3,
           TO_NUMBER(TO_CHAR(NULL)) AS TOTAL_CPE3
    FROM  rh_meses_graficas pla
    LEFT OUTER JOIN almacen alm ON alm.iid_plaza IN (".$in_plaza.")
    LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
    INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
UNION ALL
      SELECT pla.n_mes,
           pla.mes,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE,
           DECODE (cupo.v_status, 'RD',
           case when ( to_char(cupo.d_fecha_arribo, 'yyyy') = ".$fecha2."
                       AND to_char(cupo.d_fecha_arribo, 'mm') = pla.n_mes ) then (dep.vno_ped_imp) end
           )  AS total_cce2,
           DECODE (cupo.v_status, 'PE',
           case when (to_char(dep.d_plazo_dep_ini, 'yyyy') = ".$fecha2."
                       AND to_char(dep.d_plazo_dep_ini, 'mm') = pla.n_mes) then (cupo.iid_almacen) end
           ) AS total_cpe2,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE
    FROM  rh_meses_graficas pla
    LEFT OUTER JOIN almacen alm ON alm.iid_plaza IN (".$in_plaza.")
    LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
    INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
UNION ALL
      SELECT pla.n_mes,
           pla.mes,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE,
           TO_CHAR(NULL) TOTAL_CCE,
           TO_NUMBER(TO_CHAR(NULL)) TOTAL_CPE,
           DECODE (cupo.v_status, 'RD',
           case when ( to_char(cupo.d_fecha_arribo, 'yyyy') = ".$fecha3."
                       AND to_char(cupo.d_fecha_arribo, 'mm') = pla.n_mes ) then (dep.vno_ped_imp) end
           )  AS total_cce3,
           DECODE (cupo.v_status, 'PE',
           case when (to_char(dep.d_plazo_dep_ini, 'yyyy') = ".$fecha3."
                       AND to_char(dep.d_plazo_dep_ini, 'mm') = pla.n_mes) then (cupo.iid_almacen) end
           ) AS total_cpe3
    FROM  rh_meses_graficas pla
    LEFT OUTER JOIN almacen alm ON alm.iid_plaza IN (".$in_plaza.")
    LEFT OUTER JOIN op_ce_cartas_cupo cupo ON cupo.iid_almacen = alm.iid_almacen
    INNER JOIN op_in_recibo_deposito dep ON dep.vno_ped_imp = cupo.vno_pedimento
) D
    GROUP BY D.N_MES, D.MES
    ORDER BY D.N_MES";
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
public function sql($option,$depto){
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
