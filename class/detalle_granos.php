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
		$fecha_inicial2 = substr($fecha, 0, 10);
		$fecha_inicial = substr($fecha, 11, 10);
		$fecha_re = date($fecha_inicial);

		$fecha_re = str_replace('/', '-', $fecha_re);
		$fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

		$fecha_re = date('d/m/Y', strtotime($fecha_re));
		#echo $fecha_re;


		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";


		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA,SUM(CANTIDADCTD) AS CANTIDADCTD, SUM(CANTIDADN)AS CANTIDADN, SUM(CANTIDADS) AS CANTIDADS , CLIENTE FROM
		(
  	select a.v_nombre,
					t.iid_num_cliente as cliente,
         s.v_razon_social,
         CASE
              WHEN t.iid_almacen = 9999
                THEN q.c_peso_total
            END as CANTIDADCTD,
				 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL AND t.iid_almacen <> 9999
                        THEN q.c_peso_total
              END as CANTIDADN,
      	 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL AND t.iid_almacen <> 9999
                        THEN q.c_peso_total
              END as CANTIDADS,
         u.v_parte_alternativa
		  from op_in_recibo_deposito t,
		       op_in_recibo_deposito_det q,
		       op_ce_tipo_cambio r,
		       almacen a,
		       cliente s,
		       op_in_partes u,
		       co_ume v
		  where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
		                           or (t.i_sal_cero = 0
		                           and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
		                           and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
		        and (t.vid_recibo=q.vid_recibo and t.s_status='P')
		        and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
		        and (t.iid_almacen = a.iid_almacen)
		        and (t.iid_num_cliente = s.iid_num_cliente)
		        and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
		        and q.iid_um=v.iid_ume
		        and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
            and s.iid_num_cliente <> 1261
            AND t.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
					  union all
					  select a.v_nombre,
										t.iid_num_cliente as cliente,
					         c.v_razon_social,
                   CASE
                   WHEN t.iid_almacen = 9999
                        THEN(q.c_peso_total*-1)
                   END as CANTIDADCTD,
									 CASE
                  WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL AND t.iid_almacen <> 9999
                       THEN (q.c_peso_total*-1)
                  END as CANTIDADN,
             			CASE
                  WHEN  (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL AND t.iid_almacen <> 9999
                        THEN (q.c_peso_total*-1)
                  END AS CANTIDADS,
					          u.v_parte_alternativa
					  from op_in_ord_salida t,
					       op_in_ord_salida_det q,
					       op_in_recibo_deposito r,
					       op_ce_tipo_cambio s,
					       almacen a,
					       cliente c,
					       op_in_partes u,
					       co_ume v
					  where t.vid_ord_sal=q.vid_ord_sal
					        and t.v_status='P'
					        AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					            and (t.vid_recibo=r.vid_recibo
					                and ((r.i_sal_cero = 1
					                and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
					                or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					                and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
					        and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
					        and (t.iid_almacen = a.iid_almacen)
					        and (t.iid_num_cliente = c.iid_num_cliente)
					        and (q.vid_num_parte=u.vid_num_parte
					            and u.v_parte_alternativa like '%GRANELERA%')
					        and q.iid_um=v.iid_ume
					        and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                  and c.iid_num_cliente <> 1261
                  AND r.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
					)
					GROUP BY V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, CLIENTE
					ORDER BY CLIENTE, V_PARTE_ALTERNATIVA";
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
/*========================graficas_tenedor ============================*/
public function graficas_tenedor($fecha, $almacen, $tenedor){
    $mesIni = substr($fecha, 3, 2);
    $anioIni = substr($fecha, 6,4);
    $mesFin = substr($fecha, 14, 2);
    $anioFin = substr($fecha,17, 5);
    //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
		$fecha_inicial2 = substr($fecha, 0, 10);
		$fecha_inicial = substr($fecha, 11, 10);
		$fecha_re = date($fecha_inicial);

		$fecha_re = str_replace('/', '-', $fecha_re);
		$fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

		$fecha_re = date('d/m/Y', strtotime($fecha_re));
		#echo $fecha_re;

    $filtro_tenedor = "";
    if ($tenedor != "ALL") {
      if ($tenedor == 0) {
          $filtro_tenedor = " AND inf.nid_inst_financ IS NULL";
      }else {
          $filtro_tenedor = " AND inf.nid_inst_financ = $tenedor";
      }

    }

    $filtro_almacen = "";
    if ($almacen != "ALL") {
      $filtro_almacen = " AND t.iid_almacen = $almacen";
    }

		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";


		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT SUM(TOTAL_TENEDOR)/1000 AS TOTAL_TENDEDOR,
                   SUM(TOTAL_SIN_TENEDOR)/1000 AS TOTAL_SIN_TENEDOR FROM
		(
  	select a.v_nombre,
					t.iid_num_cliente as cliente,
         s.v_razon_social,
         CASE WHEN INF.V_NOMBRE IS NULL  THEN
                    Q.C_PESO_TOTAL
               END AS  TOTAL_SIN_TENEDOR,
               CASE WHEN INF.V_NOMBRE IS NOT NULL THEN
                    Q.C_PESO_TOTAL
               END AS TOTAL_TENEDOR
		  from op_in_recibo_deposito t,
		       op_in_recibo_deposito_det q,
		       op_ce_tipo_cambio r,
		       almacen a,
		       cliente s,
		       op_in_partes u,
		       co_ume v,
           ad_ce_cert_s              cs,
           ad_ce_inst_financ         inf
		  where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
		                           or (t.i_sal_cero = 0
		                           and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
		                           and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
		        and (t.vid_recibo=q.vid_recibo and t.s_status='P')
		        and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
		        and (t.iid_almacen = a.iid_almacen)
		        and (t.iid_num_cliente = s.iid_num_cliente)
		        and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
		        and q.iid_um=v.iid_ume
		        and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
            and s.iid_num_cliente <> 1261
            AND t.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
            and t.vid_recibo = cs.v_id_recibo(+)
            and cs.v_afavor_de = inf.nid_inst_financ(+)
            $filtro_tenedor
            $filtro_almacen
					  union all
					  select a.v_nombre,
										t.iid_num_cliente as cliente,
					         c.v_razon_social,
                   CASE WHEN INF.V_NOMBRE IS NULL  THEN
                              Q.C_PESO_TOTAL * -1
                         END AS TOTAL_SIN_TENEDOR,
                         CASE WHEN INF.V_NOMBRE IS NOT NULL THEN
                              Q.C_PESO_TOTAL * -1
                         END AS  TOTAL_TENEDOR
					  from op_in_ord_salida t,
					       op_in_ord_salida_det q,
					       op_in_recibo_deposito r,
					       op_ce_tipo_cambio s,
					       almacen a,
					       cliente c,
					       op_in_partes u,
					       co_ume v,
                 ad_ce_cert_s              cs,
                 ad_ce_inst_financ         inf
					  where t.vid_ord_sal=q.vid_ord_sal
					        and t.v_status='P'
					        AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					            and (t.vid_recibo=r.vid_recibo
					                and ((r.i_sal_cero = 1
					                and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
					                or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					                and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
					        and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
					        and (t.iid_almacen = a.iid_almacen)
					        and (t.iid_num_cliente = c.iid_num_cliente)
					        and (q.vid_num_parte=u.vid_num_parte
					            and u.v_parte_alternativa like '%GRANELERA%')
					        and q.iid_um=v.iid_ume
					        and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                  and c.iid_num_cliente <> 1261
                  AND r.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
                  and t.Vid_Recibo = CS.V_ID_RECIBO(+)
                  and cs.v_afavor_de = inf.nid_inst_financ(+)
                  $filtro_tenedor
                  $filtro_almacen
					)";
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
/*========================graficas_mercaNCIA ============================*/
public function graficas_merca($fecha, $almacen, $tenedor){
    $mesIni = substr($fecha, 3, 2);
    $anioIni = substr($fecha, 6,4);
    $mesFin = substr($fecha, 14, 2);
    $anioFin = substr($fecha,17, 5);
    //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
		$fecha_inicial2 = substr($fecha, 0, 10);
		$fecha_inicial = substr($fecha, 11, 10);
		$fecha_re = date($fecha_inicial);

		$fecha_re = str_replace('/', '-', $fecha_re);
		$fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

		$fecha_re = date('d/m/Y', strtotime($fecha_re));
		#echo $fecha_re;


		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

    $filtro_almacen = "";
    if($almacen != "ALL"){
      $filtro_almacen = " AND T.IID_ALMACEN = $almacen";
    }

    $filtro_tenedor = "";
    if ($tenedor != "ALL") {
      if ($tenedor == 0) {
          $filtro_tenedor = " AND inf.nid_inst_financ IS NULL ";
      }else {
          $filtro_tenedor = " AND inf.nid_inst_financ = $tenedor ";
      }

    }

		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT MERCH,
                   SUM(TOTAL_REAL)/1000 AS TOTAL_REAL FROM
		(
  	select U.V_PARTE_PROVEEDOR AS MERCH,
           Q.C_PESO_TOTAL AS TOTAL_REAL
		  from op_in_recibo_deposito t,
		       op_in_recibo_deposito_det q,
		       op_ce_tipo_cambio r,
		       almacen a,
		       cliente s,
		       op_in_partes u,
		       co_ume v,
           ad_ce_cert_s              cs,
           ad_ce_inst_financ         inf
		  where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
		                           or (t.i_sal_cero = 0
		                           and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
		                           and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
		        and (t.vid_recibo=q.vid_recibo and t.s_status='P')
		        and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
		        and (t.iid_almacen = a.iid_almacen)
		        and (t.iid_num_cliente = s.iid_num_cliente)
		        and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
		        and q.iid_um=v.iid_ume
		        and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
            and s.iid_num_cliente <> 1261
            AND t.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
            and t.vid_recibo = cs.v_id_recibo(+)
            and cs.v_afavor_de = inf.nid_inst_financ(+)
            $filtro_almacen
            $filtro_tenedor
					  union all
					  select U.V_PARTE_PROVEEDOR AS MERCH,
                   Q.C_PESO_TOTAL * -1 AS TOTAL_REAL
					  from op_in_ord_salida t,
					       op_in_ord_salida_det q,
					       op_in_recibo_deposito r,
					       op_ce_tipo_cambio s,
					       almacen a,
					       cliente c,
					       op_in_partes u,
					       co_ume v,
                 ad_ce_cert_s              cs,
                 ad_ce_inst_financ         inf
					  where t.vid_ord_sal=q.vid_ord_sal
					        and t.v_status='P'
					        AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					            and (t.vid_recibo=r.vid_recibo
					                and ((r.i_sal_cero = 1
					                and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
					                or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					                and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
					        and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
					        and (t.iid_almacen = a.iid_almacen)
					        and (t.iid_num_cliente = c.iid_num_cliente)
					        and (q.vid_num_parte=u.vid_num_parte
					            and u.v_parte_alternativa like '%GRANELERA%')
					        and q.iid_um=v.iid_ume
					        and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                  and c.iid_num_cliente <> 1261
                  AND r.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
                  and t.Vid_Recibo = CS.V_ID_RECIBO(+)
                  and cs.v_afavor_de = inf.nid_inst_financ(+)
                  $filtro_almacen
                  $filtro_tenedor
					) GROUP BY MERCH
          ORDER BY MERCH";
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
/*=======================filtro inst finan=================================*/
public function filtro_inst_finan($fecha){
    $mesIni = substr($fecha, 3, 2);
    $anioIni = substr($fecha, 6,4);
    $mesFin = substr($fecha, 14, 2);
    $anioFin = substr($fecha,17, 5);
    //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
		$fecha_inicial2 = substr($fecha, 0, 10);
		$fecha_inicial = substr($fecha, 11, 10);
		$fecha_re = date($fecha_inicial);

		$fecha_re = str_replace('/', '-', $fecha_re);
		$fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

		$fecha_re = date('d/m/Y', strtotime($fecha_re));
		#echo $fecha_re;


		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";


		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT case when id_inst is null then
            0
            else
                id_inst
            END as id_inst,
       case when nombre_finan is null then
            'SIN INSTITUCION'
            else
            nombre_finan
      END AS nombre_finan FROM
		(
  	select inf.nid_inst_financ as id_inst,
           inf.v_nombre as nombre_finan
		  from op_in_recibo_deposito t,
		       op_in_recibo_deposito_det q,
		       op_ce_tipo_cambio r,
		       almacen a,
		       cliente s,
		       op_in_partes u,
		       co_ume v,
           ad_ce_cert_s              cs,
           ad_ce_inst_financ         inf
		  where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
		                           or (t.i_sal_cero = 0
		                           and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
		                           and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
		        and (t.vid_recibo=q.vid_recibo and t.s_status='P')
		        and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
		        and (t.iid_almacen = a.iid_almacen)
		        and (t.iid_num_cliente = s.iid_num_cliente)
		        and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
		        and q.iid_um=v.iid_ume
		        and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
            and s.iid_num_cliente <> 1261
            AND t.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
            and t.vid_recibo = cs.v_id_recibo(+)
            and cs.v_afavor_de = inf.nid_inst_financ(+)
					  union all
					  select inf.nid_inst_financ as id_inst,
                   inf.v_nombre as nombre_finan
					  from op_in_ord_salida t,
					       op_in_ord_salida_det q,
					       op_in_recibo_deposito r,
					       op_ce_tipo_cambio s,
					       almacen a,
					       cliente c,
					       op_in_partes u,
					       co_ume v,
                 ad_ce_cert_s              cs,
                 ad_ce_inst_financ         inf
					  where t.vid_ord_sal=q.vid_ord_sal
					        and t.v_status='P'
					        AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					            and (t.vid_recibo=r.vid_recibo
					                and ((r.i_sal_cero = 1
					                and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
					                or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					                and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
					        and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
					        and (t.iid_almacen = a.iid_almacen)
					        and (t.iid_num_cliente = c.iid_num_cliente)
					        and (q.vid_num_parte=u.vid_num_parte
					            and u.v_parte_alternativa like '%GRANELERA%')
					        and q.iid_um=v.iid_ume
					        and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                  and c.iid_num_cliente <> 1261
                  AND r.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
                  and t.Vid_Recibo = CS.V_ID_RECIBO(+)
                  and cs.v_afavor_de = inf.nid_inst_financ(+)
					) GROUP BY id_inst, nombre_finan
            ORDER BY id_inst";
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
/*=====================================================================*/
public function tabla_toneladas2($fecha, $almacen, $tenedor){
  $mesIni = substr($fecha, 3, 2);
  $anioIni = substr($fecha, 6,4);
  $mesFin = substr($fecha, 14, 2);
  $anioFin = substr($fecha,17, 5);
  //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
  $fecha_inicial2 = substr($fecha, 0, 10);
  $fecha_inicial = substr($fecha, 11, 10);
  $fecha_re = date($fecha_inicial);

  $fecha_re = str_replace('/', '-', $fecha_re);
  $fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

  $fecha_re = date('d/m/Y', strtotime($fecha_re));

  $filtro_almacen = "";
  if ($almacen != "ALL") {
    $filtro_almacen = " AND T.IID_ALMACEN = $almacen";
  }

  $filtro_tenedor = "";
  if ($tenedor != "ALL") {
    if ($tenedor == 0) {
      $filtro_tenedor = " AND INF.nid_inst_financ IS NULL";
    }else {
      $filtro_tenedor = " AND INF.nid_inst_financ = $tenedor";
    }
  }


  $conn = conexion::conectar();
  $res_array = array();
  $sql = "SELECT V_NOMBRE,
                 V_RAZON_SOCIAL,
                 PARTE,
                 NINF,
                 SUM(CANTIDADCTD) AS CANTIDADCTD,
                 SUM(CANTIDADN)AS CANTIDADN,
                 SUM(CANTIDADS) AS CANTIDADS ,
                 CLIENTE FROM
  (
  select a.v_nombre,
        t.iid_num_cliente as cliente,
       s.v_razon_social,
       CASE
            WHEN t.iid_almacen = 9999
              THEN q.c_peso_total
          END as CANTIDADCTD,
       CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL AND t.iid_almacen <> 9999
                      THEN q.c_peso_total
            END as CANTIDADN,
       CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL AND t.iid_almacen <> 9999
                      THEN q.c_peso_total
            END as CANTIDADS,
            inf.v_nombre as ninf,
            u.v_parte_proveedor as parte
    from op_in_recibo_deposito t,
         op_in_recibo_deposito_det q,
         op_ce_tipo_cambio r,
         almacen a,
         cliente s,
         op_in_partes u,
         co_ume v,
         ad_ce_cert_s              cs,
         ad_ce_inst_financ         inf
    where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                             or (t.i_sal_cero = 0
                             and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                             and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
          and (t.vid_recibo=q.vid_recibo and t.s_status='P')
          and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
          and (t.iid_almacen = a.iid_almacen)
          and (t.iid_num_cliente = s.iid_num_cliente)
          and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
          and q.iid_um=v.iid_ume
          and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
          and s.iid_num_cliente <> 1261
          and t.iid_almacen = 9999
          AND t.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
          and t.vid_recibo = cs.v_id_recibo(+)
          and cs.v_afavor_de = inf.nid_inst_financ(+)
          $filtro_almacen
          $filtro_tenedor
          union all
          select a.v_nombre,
                  t.iid_num_cliente as cliente,
                 c.v_razon_social,
                 CASE
                 WHEN t.iid_almacen = 9999
                      THEN(q.c_peso_total*-1)
                 END as CANTIDADCTD,
                 CASE
                WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL AND t.iid_almacen <> 9999
                     THEN (q.c_peso_total*-1)
                END as CANTIDADN,
                CASE
                WHEN  (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL AND t.iid_almacen <> 9999
                      THEN (q.c_peso_total*-1)
                END AS CANTIDADS,
                inf.v_nombre as ninf,
                u.v_parte_proveedor as parte
          from op_in_ord_salida t,
               op_in_ord_salida_det q,
               op_in_recibo_deposito r,
               op_ce_tipo_cambio s,
               almacen a,
               cliente c,
               op_in_partes u,
               co_ume v,
               ad_ce_cert_s              cs,
               ad_ce_inst_financ         inf
          where t.vid_ord_sal=q.vid_ord_sal
                and t.v_status='P'
                AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                    and (t.vid_recibo=r.vid_recibo
                        and ((r.i_sal_cero = 1
                        and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                        or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                        and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
                and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
                and (t.iid_almacen = a.iid_almacen)
                and (t.iid_num_cliente = c.iid_num_cliente)
                and (q.vid_num_parte=u.vid_num_parte
                    and u.v_parte_alternativa like '%GRANELERA%')
                and q.iid_um=v.iid_ume
                and t.iid_almacen = 9999
                and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                and c.iid_num_cliente <> 1261
                AND r.vid_recibo NOT IN ('200803099990100422', '200903099990100173')
                and t.vid_recibo = cs.v_id_recibo(+)
                and cs.v_afavor_de = inf.nid_inst_financ(+)
                $filtro_almacen
                $filtro_tenedor
        )GROUP BY V_NOMBRE, V_RAZON_SOCIAL, PARTE, CLIENTE, NINF
        ORDER BY CLIENTE, PARTE";
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
/*========================TABLA TONELADAS 3 =============================*/
public function tabla_toneladas3($fecha, $almacen, $tenedor){
  $mesIni = substr($fecha, 3, 2);
  $anioIni = substr($fecha, 6,4);
  $mesFin = substr($fecha, 14, 2);
  $anioFin = substr($fecha,17, 5);
  //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
  $fecha_inicial2 = substr($fecha, 0, 10);
  $fecha_inicial = substr($fecha, 11, 10);
  $fecha_re = date($fecha_inicial);

  $fecha_re = str_replace('/', '-', $fecha_re);
  $fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

  $fecha_re = date('d/m/Y', strtotime($fecha_re));

  $filtro_almacen = "";
  if ($almacen != "ALL") {
    $filtro_almacen = " AND T.IID_ALMACEN = $almacen";
  }

  $filtro_tenedor = "";
  if ($tenedor != "ALL") {
    if ($tenedor == 0) {
      $filtro_tenedor = " AND INF.nid_inst_financ IS NULL";
    }else {
      $filtro_tenedor = " AND INF.nid_inst_financ = $tenedor";
    }
  }

  $conn = conexion::conectar();
  $res_array = array();
  $sql = "SELECT V_NOMBRE, V_RAZON_SOCIAL, PARTE,
       NINF,SUM(CANTIDADCTD) AS CANTIDADCTD, SUM(CANTIDADN)AS CANTIDADN, SUM(CANTIDADS) AS CANTIDADS , CLIENTE FROM
  (
  select a.v_nombre,
        t.iid_num_cliente as cliente,
       s.v_razon_social,
       CASE
            WHEN t.iid_almacen = 9999
              THEN q.c_peso_total
          END as CANTIDADCTD,
       CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL AND t.iid_almacen <> 9999
                      THEN q.c_peso_total
            END as CANTIDADN,
       CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL AND t.iid_almacen <> 9999
                      THEN q.c_peso_total
            END as CANTIDADS,
            inf.v_nombre as ninf,
            u.v_parte_proveedor as parte
    from op_in_recibo_deposito t,
         op_in_recibo_deposito_det q,
         op_ce_tipo_cambio r,
         almacen a,
         cliente s,
         op_in_partes u,
         co_ume v,
         ad_ce_cert_s              cs,
         ad_ce_inst_financ         inf
    where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                             or (t.i_sal_cero = 0
                             and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                             and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
          and (t.vid_recibo=q.vid_recibo and t.s_status='P')
          and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
          and (t.iid_almacen = a.iid_almacen)
          and (t.iid_num_cliente = s.iid_num_cliente)
          and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
          and q.iid_um=v.iid_ume
          and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
          and s.iid_num_cliente <> 1261
          and a.s_tipo_almacen in (5, 15)
          and t.iid_almacen <> 9999
          and t.vid_recibo = cs.v_id_recibo(+)
          and cs.v_afavor_de = inf.nid_inst_financ(+)
          $filtro_almacen
          $filtro_tenedor
          union all
          select a.v_nombre,
                  t.iid_num_cliente as cliente,
                 c.v_razon_social,
                 CASE
                 WHEN t.iid_almacen = 9999
                      THEN(q.c_peso_total*-1)
                 END as CANTIDADCTD,
                 CASE
                WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL AND t.iid_almacen <> 9999
                     THEN (q.c_peso_total*-1)
                END as CANTIDADN,
                CASE
                WHEN  (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL AND t.iid_almacen <> 9999
                      THEN (q.c_peso_total*-1)
                END AS CANTIDADS,
                inf.v_nombre as ninf,
                u.v_parte_proveedor as parte
          from op_in_ord_salida t,
               op_in_ord_salida_det q,
               op_in_recibo_deposito r,
               op_ce_tipo_cambio s,
               almacen a,
               cliente c,
               op_in_partes u,
               co_ume v,
               ad_ce_cert_s              cs,
               ad_ce_inst_financ         inf
          where t.vid_ord_sal=q.vid_ord_sal
                and t.v_status='P'
                AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                    and (t.vid_recibo=r.vid_recibo
                        and ((r.i_sal_cero = 1
                        and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                        or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                        and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
                and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
                and (t.iid_almacen = a.iid_almacen)
                and (t.iid_num_cliente = c.iid_num_cliente)
                and (q.vid_num_parte=u.vid_num_parte
                    and u.v_parte_alternativa like '%GRANELERA%')
                and q.iid_um=v.iid_ume
                and t.iid_almacen <> 9999
                and a.s_tipo_almacen in (5, 15)
                and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                and c.iid_num_cliente <> 1261
                and t.vid_recibo = cs.v_id_recibo(+)
                and cs.v_afavor_de = inf.nid_inst_financ(+)
                $filtro_almacen
                $filtro_tenedor
        )
        GROUP BY V_NOMBRE, V_RAZON_SOCIAL,PARTE, CLIENTE, NINF
        ORDER BY CLIENTE, PARTE";
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
/*========================TABLA TONELADAS 4 =============================*/
public function tabla_toneladas4($fecha, $almacen, $tenedor){
  $mesIni = substr($fecha, 3, 2);
  $anioIni = substr($fecha, 6,4);
  $mesFin = substr($fecha, 14, 2);
  $anioFin = substr($fecha,17, 5);
  //echo $mesIni. ' '. $anioIni. ' '. $mesFin.' '.$anioFin;
  $fecha_inicial2 = substr($fecha, 0, 10);
  $fecha_inicial = substr($fecha, 11, 10);
  $fecha_re = date($fecha_inicial);

  $fecha_re = str_replace('/', '-', $fecha_re);
  $fecha_re = date('Y-m-d', strtotime($fecha_re." -1 day"));

  $fecha_re = date('d/m/Y', strtotime($fecha_re));
  #echo $fecha_re;
  $filtro_almacen = "";
  if ($almacen != "ALL") {
    $filtro_almacen = " AND T.IID_ALMACEN = $almacen";
  }

  $filtro_tenedor = "";
  if ($tenedor != "ALL") {
    if ($tenedor == 0) {
      $filtro_tenedor = " AND INF.nid_inst_financ IS NULL";
    }else {
      $filtro_tenedor = " AND INF.nid_inst_financ = $tenedor";
    }
  }

  $conn = conexion::conectar();
  $res_array = array();
  $sql = "SELECT V_NOMBRE, V_RAZON_SOCIAL, PARTE,
       NINF,SUM(CANTIDADCTD) AS CANTIDADCTD, SUM(CANTIDADN)AS CANTIDADN, SUM(CANTIDADS) AS CANTIDADS , CLIENTE FROM
  (
  select a.v_nombre,
        t.iid_num_cliente as cliente,
       s.v_razon_social,
       CASE
            WHEN t.iid_almacen = 9999
              THEN q.c_peso_total
          END as CANTIDADCTD,
       CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL AND t.iid_almacen <> 9999
                      THEN q.c_peso_total
            END as CANTIDADN,
       CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL AND t.iid_almacen <> 9999
                      THEN q.c_peso_total
            END as CANTIDADS,
            inf.v_nombre as ninf,
            u.v_parte_proveedor as parte
    from op_in_recibo_deposito t,
         op_in_recibo_deposito_det q,
         op_ce_tipo_cambio r,
         almacen a,
         cliente s,
         op_in_partes u,
         co_ume v,
         ad_ce_cert_s              cs,
         ad_ce_inst_financ         inf
    where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                             or (t.i_sal_cero = 0
                             and t.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                             and t.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
          and (t.vid_recibo=q.vid_recibo and t.s_status='P')
          and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
          and (t.iid_almacen = a.iid_almacen)
          and (t.iid_num_cliente = s.iid_num_cliente)
          and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%GRANELERA%')
          and q.iid_um=v.iid_ume
          and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
          and s.iid_num_cliente <> 1261
          and a.s_tipo_almacen in (2, 6)
          and t.iid_almacen <> 9999
          and t.vid_recibo = cs.v_id_recibo(+)
          and cs.v_afavor_de = inf.nid_inst_financ(+)
          $filtro_almacen
          $filtro_tenedor
          union all
          select a.v_nombre,
                  t.iid_num_cliente as cliente,
                 c.v_razon_social,
                 CASE
                 WHEN t.iid_almacen = 9999
                      THEN(q.c_peso_total*-1)
                 END as CANTIDADCTD,
                 CASE
                WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL AND t.iid_almacen <> 9999
                     THEN (q.c_peso_total*-1)
                END as CANTIDADN,
                CASE
                WHEN  (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL AND t.iid_almacen <> 9999
                      THEN (q.c_peso_total*-1)
                END AS CANTIDADS,
                inf.v_nombre as ninf,
                u.v_parte_proveedor as parte
          from op_in_ord_salida t,
               op_in_ord_salida_det q,
               op_in_recibo_deposito r,
               op_ce_tipo_cambio s,
               almacen a,
               cliente c,
               op_in_partes u,
               co_ume v,
               ad_ce_cert_s              cs,
               ad_ce_inst_financ         inf
          where t.vid_ord_sal=q.vid_ord_sal
                and t.v_status='P'
                AND T.D_FECHA_REG <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                    and (t.vid_recibo=r.vid_recibo
                        and ((r.i_sal_cero = 1
                        and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                        or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_inicial." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                        and r.d_fec_sal_cero > to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
                and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
                and (t.iid_almacen = a.iid_almacen)
                and (t.iid_num_cliente = c.iid_num_cliente)
                and (q.vid_num_parte=u.vid_num_parte
                    and u.v_parte_alternativa like '%GRANELERA%')
                and q.iid_um=v.iid_ume
                and t.iid_almacen <> 9999
                and a.s_tipo_almacen in (2, 6)
                and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
                and c.iid_num_cliente <> 1261
                and t.vid_recibo = cs.v_id_recibo(+)
                and cs.v_afavor_de = inf.nid_inst_financ(+)
                $filtro_almacen
                $filtro_tenedor
        )
        GROUP BY V_NOMBRE, V_RAZON_SOCIAL, PARTE, CLIENTE, NINF
        ORDER BY CLIENTE, PARTE";
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
	/*========================TABLA TONELADAS 5 =============================*/
public function tabla_toneladas5($fecha){
	$fecha_inicial = substr($fecha, 11, 10);
	$mesFin = substr($fecha, 14, 2);
	$anioFin = substr($fecha,17, 5);

	#echo "año =".$anioFin." mes ". $mesFin;
	$month = $anioFin."-".$mesFin;
	#$aux = date('Y-m-d', strtotime("{$month} - 1 month"));
	$ultimo_dia = date('Y-m-d', strtotime("{$month} - 1 day"));
	$ultimo_dia2 = date('Y-m-d', strtotime("{$month} - 2 day"));

	$fecha_re = date('d/m/Y', strtotime($ultimo_dia));
	$fecha_re2 = date('d/m/Y', strtotime($ultimo_dia2));
	#echo $fecha_re."<br />";
	#echo $fecha_re2."<br />";

			$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";


	$conn = conexion::conectar();
	$res_array = array();
	$sql = "SELECT V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, SUM(CANTIDADN)AS CANTIDADN, SUM(CANTIDADS) AS CANTIDADS , CLIENTE FROM
	(
	select a.v_nombre,
				t.iid_num_cliente as cliente,
			 s.v_razon_social,
			 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN
											q.c_peso_total
								 WHEN(SUBSTR(t.vid_certificado, -1, 1) <> 'S') OR T.VID_CERTIFICADO IS NULL THEN
											q.c_peso_total
								 END
						as CANTIDADN,
						CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') OR T.VID_CERTIFICADO IS NULL THEN
											q.c_peso_total
								 WHEN(SUBSTR(t.vid_certificado, -1, 1) <> 'N') OR T.VID_CERTIFICADO IS NULL THEN
											q.c_peso_total
								 END
						as CANTIDADS,
			 u.v_parte_alternativa
		from op_in_recibo_deposito t,
				 op_in_recibo_deposito_det q,
				 op_ce_tipo_cambio r,
				 almacen a,
				 cliente s,
				 op_in_partes u,
				 co_ume v
		where ((t.i_sal_cero = 1 and t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
														 or (t.i_sal_cero = 0
														 and t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
														 and t.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
					and (t.vid_recibo=q.vid_recibo and t.s_status='P')
					and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
					and (t.iid_almacen = a.iid_almacen)
					and (t.iid_num_cliente = s.iid_num_cliente)
					and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
					and q.iid_um=v.iid_ume
					and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
					union all
					select a.v_nombre,
									t.iid_num_cliente as cliente,
								 c.v_razon_social,
								 CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN
												 (q.c_peso_total*-1)
										WHEN(SUBSTR(r.vid_certificado, -1, 1) <> 'S') OR r.vid_certificado IS NULL THEN
												 (q.c_peso_total*-1)
										END
								as CANTIDADN,
								CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') OR r.vid_certificado IS NULL THEN
												 (q.c_peso_total*-1)
										 WHEN(SUBSTR(r.vid_certificado, -1, 1) <> 'N') OR r.vid_certificado IS NULL THEN
												 (q.c_peso_total*-1)
								END
								AS CANTIDADS,
									u.v_parte_alternativa
					from op_in_ord_salida t,
							 op_in_ord_salida_det q,
							 op_in_recibo_deposito r,
							 op_ce_tipo_cambio s,
							 almacen a,
							 cliente c,
							 op_in_partes u,
							 co_ume v
					where t.vid_ord_sal=q.vid_ord_sal
								and t.v_status='P'
								AND T.D_FECHA_REG <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
										and (t.vid_recibo=r.vid_recibo
												and ((r.i_sal_cero = 1
												and r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
												or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
												and r.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
								and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
								and (t.iid_almacen = a.iid_almacen)
								and (t.iid_num_cliente = c.iid_num_cliente)
								and (q.vid_num_parte=u.vid_num_parte
										and u.v_parte_alternativa like '%ZAFRA%')
								and q.iid_um=v.iid_ume
								and (r.vid_certificado is not null or (r.vid_certificado is null and r.i_administrativo = 1))
				)
				GROUP BY V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, CLIENTE
				ORDER BY V_PARTE_ALTERNATIVA";
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
	/*====================== /*TABLA DE NOMINA PAGADA ======================*/


	/*====================== SQL DINAMICO ======================*/
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
  function select_Insf($tenedor){
    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT V_NOMBRE FROM AD_CE_INST_FINANC WHERE NID_INST_FINANC = $tenedor";
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
