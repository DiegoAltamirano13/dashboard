<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class NominaPagada{
	/*====================== GRAFICA DE NOMINA PAGADA ======================*/
public function tabla_toneladas($fecha){
    $mesIni = substr($fecha, 3, 2);
    $anioIni = substr($fecha, 6,4);
    $mesFin = substr($fecha, 14, 2);
    $anioFin = substr($fecha,17, 5);
    //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
		$fecha_inicial = substr($fecha, 0, 10);

		$fecha_re = date($fecha_inicial);
    $fecha_re2 = date($fecha_inicial);

		$fecha_re = str_replace('/', '-', $fecha_re);
		$fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

    $fecha_re2 = str_replace('/', '-', $fecha_re2);
		$fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -2 day"));

		$fecha_re = date('d/m/Y', strtotime($fecha_re));
    $fecha_re2 = date('d/m/Y', strtotime($fecha_re2));
		#echo $fecha_re;

    #echo $fecha_re."</br>".$fecha_re2;
		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";


		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT U.V_NOMBRE,
                   U.IID_ALMACEN,
                   U.TOTAL_SACOS AS TOTAL_SACOS,
                   U.TOTAL_SUPER_SACOS AS TOTAL_SUPER_SACOS,
                   SUM(Y.N_TON_SACOS) AS SACOS,
                   SUM(Y.N_TON_SUPERSACOS) AS SUPERSACOS,
                   SUM(Y.I_M2) AS METROS_CUADRADOS
             FROM (SELECT V_NOMBRE,
                         IID_ALMACEN,
                       (SUM(nvl(CANTIDADN, 0)) + SUM(nvl(CANTIDADS, 0)))/1000 AS TOTAL_SACOS,
                       (SUM(nvl(CANTIDADN70, 0)) + SUM(nvl(CANTIDADS70, 0)))/1000 AS TOTAL_SUPER_SACOS
                   FROM (select a.v_nombre,
                                a.iid_almacen,
                               CASE
                               WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN
                                  nvl(q.c_peso_total, 0)
                               END as CANTIDADN,
                               CASE
                               WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN
                                  nvl(q.c_peso_total, 0)
                               END as CANTIDADS,
                               0 AS CANTIDADN70,
                               0 AS CANTIDADS70
                         from op_in_recibo_deposito     t,
                              op_in_recibo_deposito_det q,
                              op_ce_tipo_cambio         r,
                              almacen                   a,
                              cliente                   s,
                              op_in_partes              u,
                              co_ume                    v,
                              ALMACEN_AREAS              az
                        where ((t.i_sal_cero = 1 and
                                  t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')) or
                                  (t.i_sal_cero = 0 and
                                   t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss') and
                                   t.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')))
                              and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
                              and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') = to_char(r.did_fecha, 'dd/mm/yyyy'))
                              and (t.iid_almacen = a.iid_almacen)
                              and (t.iid_num_cliente = s.iid_num_cliente)
                              and (q.vid_num_parte = u.vid_num_parte and
                                   u.v_parte_alternativa like '%ZAFRA%')
                              and q.iid_um = v.iid_ume
                              and (t.vid_certificado is not null or
                                  (t.vid_certificado is null and
                                   t.i_administrativo = 1))
                              and s.iid_num_cliente <> 1261
                              and q.iid_um = 59
                              and t.iid_almacen = az.iid_almacen
                              and az.i_status = 1
                              and az.V_CVE_SIDEFI  is NULL
                              AND AZ.S_AREA = (SELECT MIN(S.S_AREA) FROM ALMACEN_AREAS S WHERE S.IID_ALMACEN = AZ.IID_ALMACEN AND S.V_CVE_SIDEFI IS NULL)
                              and a.iid_almacen in (1211, 1205, 1209, 1210, 1370, 1223, 1208, 1468, 1660)
                              and a.iid_almacen not in (1554, 1657)
                              union all
                              select a.v_nombre,
                                     a.iid_almacen,
                                     CASE
                                     WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN
                                          nvl((q.c_peso_total * -1), 0)
                                     END as CANTIDADN,
                                     CASE
                                     WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN
                                          nvl((q.c_peso_total * -1), 0)
                                     END AS CANTIDADS,
                                     0 AS CANTIDADN70,
                                     0 AS CANTIDADS70
                              from op_in_ord_salida      t,
                                   op_in_ord_salida_det  q,
                                   op_in_recibo_deposito r,
                                   op_ce_tipo_cambio     s,
                                   almacen               a,
                                   cliente               c,
                                   op_in_partes          u,
                                   co_ume                v,
                                   ALMACEN_AREAS         az
                               where t.vid_ord_sal = q.vid_ord_sal
                                    and t.v_status = 'P'
                                    AND T.D_FECHA_REG <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')
                                    and (t.vid_recibo = r.vid_recibo and
                                        ((r.i_sal_cero = 1 and
                                          r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')) or
                                          (r.i_sal_cero = 0 and
                                           r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss') and
                                           r.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59', 'dd/mm/yyyy hh24:mi:ss'))))
                                    and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') = to_char(s.did_fecha, 'dd/mm/yyyy'))
                                    and (t.iid_almacen = a.iid_almacen)
                                    and (t.iid_num_cliente = c.iid_num_cliente)
                                    and (q.vid_num_parte = u.vid_num_parte and
                                         u.v_parte_alternativa like '%ZAFRA%')
                                    and q.iid_um = v.iid_ume
                                    and (r.vid_certificado is not null or
                                        (r.vid_certificado is null and r.i_administrativo = 1))
                                    and c.iid_num_cliente <> 1261
                                    and q.iid_um = 59
                                    and t.iid_almacen = az.iid_almacen
                                    and az.i_status = 1
                                    and az.V_CVE_SIDEFI  is NULL
                                    and a.iid_almacen in (1211, 1205, 1209, 1210, 1370, 1223, 1208, 1468, 1660)
                                    and a.iid_almacen not in (1554, 1657)
                                    AND AZ.S_AREA = (SELECT MIN(S.S_AREA) FROM ALMACEN_AREAS S WHERE S.IID_ALMACEN = AZ.IID_ALMACEN AND S.V_CVE_SIDEFI IS NULL)
                              UNION ALL
                                    select a.v_nombre,
                                           a.iid_almacen,
                                           0 AS CANTIDADN,
                                           0 AS CANTIDADS,
                                           CASE
                                           WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN
                                                  nvl(q.c_peso_total, 0)
                                           END as CANTIDADN70,
                                           CASE
                                           WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN
                                                  nvl(q.c_peso_total, 0)
                                           END as CANTIDADS70
                                    from op_in_recibo_deposito     t,
                                         op_in_recibo_deposito_det q,
                                         op_ce_tipo_cambio         r,
                                         almacen                   a,
                                         cliente                   s,
                                         op_in_partes              u,
                                         co_ume                    v,
                                         ALMACEN_AREAS             az
                                    where ((t.i_sal_cero = 1 and
                                                t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')) or
                                               (t.i_sal_cero = 0 and
                                                t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss') and
                                                t.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')))
                                           and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
                                           and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') = to_char(r.did_fecha, 'dd/mm/yyyy'))
                                           and (t.iid_almacen = a.iid_almacen)
                                           and (t.iid_num_cliente = s.iid_num_cliente)
                                           and (q.vid_num_parte = u.vid_num_parte and
                                                u.v_parte_alternativa like '%ZAFRA%')
                                           and q.iid_um = v.iid_ume
                                           and (t.vid_certificado is not null or
                                               (t.vid_certificado is null and t.i_administrativo = 1))
                                           and s.iid_num_cliente <> 1261
                                           and q.iid_um in (70, 14)
                                           and t.iid_almacen = az.iid_almacen
                                           and az.i_status = 1
                                           and az.V_CVE_SIDEFI  is NULL
                                           and a.iid_almacen not in (1554, 1657)
                                           AND AZ.S_AREA = (SELECT MIN(S.S_AREA) FROM ALMACEN_AREAS S WHERE S.IID_ALMACEN = AZ.IID_ALMACEN AND S.V_CVE_SIDEFI IS NULL)
                               union all
                                    select  a.v_nombre,
                                            a.iid_almacen,
                                            0 AS CANTIDADN,
                                            0 AS CANTIDADS,
                                            CASE
                                            WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN
                                                  nvl((q.c_peso_total * -1), 0)
                                            END as CANTIDADN70,
                                            CASE
                                            WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN
                                                  nvl((q.c_peso_total * -1), 0)
                                            END AS CANTIDADS70
                                    from op_in_ord_salida      t,
                                         op_in_ord_salida_det  q,
                                         op_in_recibo_deposito r,
                                         op_ce_tipo_cambio     s,
                                         almacen               a,
                                         cliente               c,
                                         op_in_partes          u,
                                         co_ume                v,
                                         ALMACEN_AREAS  az
                                     where t.vid_ord_sal = q.vid_ord_sal
                                            and t.v_status = 'P'
                                            AND T.D_FECHA_REG <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')
                                            and (t.vid_recibo = r.vid_recibo and
                                                    ((r.i_sal_cero = 1 and
                                                      r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss')) or
                                                     (r.i_sal_cero = 0 and
                                                      r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59', 'dd/mm/yyyy hh24:mi:ss') and
                                                      r.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59', 'dd/mm/yyyy hh24:mi:ss'))))
                                            and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') = to_char(s.did_fecha, 'dd/mm/yyyy'))
                                            and (t.iid_almacen = a.iid_almacen)
                                            and (t.iid_num_cliente = c.iid_num_cliente)
                                            and (q.vid_num_parte = u.vid_num_parte and
                                                 u.v_parte_alternativa like '%ZAFRA%')
                                            and q.iid_um = v.iid_ume
                                            and (r.vid_certificado is not null or
                                                (r.vid_certificado is null and r.i_administrativo = 1))
                                            and c.iid_num_cliente <> 1261
                                            and q.iid_um in (70, 14)
                                            and t.iid_almacen = az.iid_almacen
                                            and az.i_status = 1
                                            and az.V_CVE_SIDEFI  is NULL
                                            AND AZ.S_AREA = (SELECT MIN(S.S_AREA) FROM ALMACEN_AREAS S WHERE S.IID_ALMACEN = AZ.IID_ALMACEN AND S.V_CVE_SIDEFI IS NULL)
                                            and a.iid_almacen in (1211, 1205, 1209, 1210, 1370, 1223, 1208, 1468, 1660)
                                            and a.iid_almacen not in (1554, 1657)) X
                                            GROUP BY V_NOMBRE, X.IID_ALMACEN) U
                                              INNER JOIN ALMACEN_AREAS Z ON U.IID_ALMACEN = Z.IID_ALMACEN AND Z.V_CVE_SIDEFI IS NULL
                                              INNER JOIN ALMACEN_AREAS_ESPACIOS Y ON Z.IID_ALMACEN = Y.IID_ALMACEN AND Y.S_AREA = Z.S_AREA
                                            GROUP BY U.IID_ALMACEN, U.V_NOMBRE,U.TOTAL_SACOS, U.TOTAL_SUPER_SACOS";
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




public function sql($option,$depto,$plaza){
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM DUAL";
	 	switch ($option) {
			case '1':
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0), 'DD/MM/YYYY') mes1, TO_CHAR(SYSDATE, 'DD/MM/YYYY') mes2 FROM DUAL";
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
				$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91) and v_sscuenta = 0
									union all
									select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
				break;
      case '6':
  				$sql = "SELECT TO_CHAR(SYSDATE, 'DD/MM/YYYY') mes1 FROM DUAL";
  				break;
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
		$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_PLAZA = $in_plaza AND IID_ALMACEN NOT IN (9998, 9999) ORDER BY IID_ALMACEN";
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
	/*====================== /*SQL DINAMICO ======================*/

	function almacenNombre($plaza,$almacen){
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
		$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_ALMACEN = $almacen AND IID_PLAZA = $in_plaza AND IID_ALMACEN NOT IN (9998, 9999) ORDER BY IID_ALMACEN";
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
	/*====================== VALIDA SI ES FECHA  ======================*/
	function nombreTipo($tipo){
		$conn = conexion::conectar();
		$res_array = array();
		if ($tipo == 87 ) {
			$sql = "select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
		}
		else {
			$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in($tipo) and v_sscuenta = 0";
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

	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}


}
