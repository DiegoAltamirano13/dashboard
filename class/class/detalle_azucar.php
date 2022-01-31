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

		$fecha_inicial2 = substr($fecha, 0, 10);
		$fecha_inicial = substr($fecha, 11, 10);

		$fecha_re = date($fecha_inicial);
    $fecha_re2 = date($fecha_inicial);

		$fecha_re = str_replace('/', '-', $fecha_re);
		$fecha_re = date('Y-m-d', strtotime($fecha_re." -0 day"));

    $fecha_re2 = str_replace('/', '-', $fecha_re2);
		$fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -1 day"));

		$fecha_re = date('d/m/Y', strtotime($fecha_re));
    $fecha_re2 = date('d/m/Y', strtotime($fecha_re2));

		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";


        $almacen_rt = "";
        $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
        $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
        $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
        if($f1 <= $f2){
          //echo "aqui nena".$f1;
          $almacen_rt = ", 1210";
        #  echo "aqui nna";
        }
        if ($f3 == $f1) {
          $almacen_rt = " ";
        }


		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, SUM(CANTIDADN)AS CANTIDADN, SUM(CANTIDADS) AS CANTIDADS , CLIENTE FROM
		(
  	select a.v_nombre,
					t.iid_num_cliente as cliente,
         s.v_razon_social,
				 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL
                        THEN q.c_peso_total
              END as CANTIDADN,
      	 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL
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
		        and (t.vid_certificado is not null)
            and s.iid_num_cliente <> 1261
            and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt ) AND S.VID_CERTIFICADO LIKE '%-N%' )))
					  union all
					  select a.v_nombre,
										t.iid_num_cliente as cliente,
					         c.v_razon_social,
									 CASE
                  WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL
                       THEN (q.c_peso_total*-1)
                  END as CANTIDADN,
             			CASE
                  WHEN  (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL
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
					        and (r.vid_certificado is not null )
                  and c.iid_num_cliente <> 1261
                  and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt )AND S.VID_CERTIFICADO LIKE '%-N%' ))
					))
					GROUP BY V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, CLIENTE
					ORDER BY CLIENTE, V_PARTE_ALTERNATIVA";
          //and a.iid_almacen not in (1210) -- or (r.vid_certificado is null and r.i_administrativo = 1)
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
/*========================TABLA TONELADAS 2 ============================*/
public function tabla_toneladas2($fecha){
	$mesIni = substr($fecha, 3, 2);
	$anioIni = substr($fecha, 6,4);
	$mesFin = substr($fecha, 14, 2);
	$anioFin = substr($fecha,17, 5);
	$fecha_inicial2 = substr($fecha, 0, 10);
	$fecha_inicial = substr($fecha, 11, 10);
  $fecha_re = date($fecha_inicial);
  $fecha_re2 = date($fecha_inicial);

  $fecha_re = str_replace('/', '-', $fecha_re);
  $fecha_re = date('Y-m-d', strtotime($fecha_re." -0 day"));

  $fecha_re2 = str_replace('/', '-', $fecha_re2);
  $fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -1 day"));

  $fecha_re = date('d/m/Y', strtotime($fecha_re));
  $fecha_re2 = date('d/m/Y', strtotime($fecha_re2));



	$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

  $almacen_rt = "";
  $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
  $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
  $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
  if($f1 <= $f2){
    //echo "aqui nena".$fecha_re;
    $almacen_rt = ", 1210";
  }
  if ($f1 == $f3) {
    $almacen_rt = " ";

  }


	$conn = conexion::conectar();
	$res_array = array();
	$sql = "SELECT SUM(PESO_TOTAL) AS PESO_TOTAL, PARTE_ALTER
				FROM ( select q.c_peso_total AS PESO_TOTAL,
              u.v_parte_alternativa AS PARTE_ALTER
					       from op_in_recibo_deposito t,
					            op_in_recibo_deposito_det q,
					            op_ce_tipo_cambio r,
					            almacen a,
					            cliente s,
					            op_in_partes u,
					            co_ume v
					        where ((t.i_sal_cero = 1
					                             and t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
					                                 or (t.i_sal_cero = 0 and t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
					                                 and t.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
					              and (t.vid_recibo=q.vid_recibo and t.s_status='P')
					              and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
					              and (t.iid_almacen = a.iid_almacen)
					              and (t.iid_num_cliente = s.iid_num_cliente)
					              and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
					              and q.iid_um=v.iid_ume
					              and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt ) AND S.VID_CERTIFICADO LIKE '%-N%' )))
                        and s.iid_num_cliente <> 1261
					union all
					      select (q.c_peso_total*-1) AS PESO_TOTAL,
					             u.v_parte_alternativa AS PARTE_ALTER
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
                     and c.iid_num_cliente <> 1261
					           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt )AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     ) ) GROUP BY PARTE_ALTER ORDER BY PARTE_ALTER";
						$stid = oci_parse($conn, $sql);
            #echo $sql;
						oci_execute($stid);
						while (($row = oci_fetch_assoc($stid)) != false)
						{
							$res_array[]= $row;
						}
						oci_free_statement($stid);
						oci_close($conn);
						return $res_array;

}
/*========================TABLA TONELADAS 3 =============================*/
public function tabla_toneladas3($fecha){
	$mesIni = substr($fecha, 3, 2);
	$anioIni = substr($fecha, 6,4);
	$mesFin = substr($fecha, 14, 2);
	$anioFin = substr($fecha,17, 5);
	$fecha_inicial2 = substr($fecha, 0, 10);
	$fecha_inicial = substr($fecha, 11, 10);
	$fecha_re = date($fecha_inicial);
	$fecha_re2 = date($fecha_inicial);
	$fecha_re3 = date($fecha_inicial);

  //$fecha_re = date($fecha_inicial);
	$fecha_re = str_replace('/', '-', $fecha_re);
	$fecha_re = date('Y-m-d', strtotime($fecha_re." -0 day"));

	$fecha_re2 = str_replace('/', '-', $fecha_re2);
	$fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -1 day"));

	$fecha_re3 = str_replace('/', '-', $fecha_re3);
	$fecha_re3 = date('Y-m-d', strtotime($fecha_re3." -2 day"));

	$fecha_re = date('d/m/Y', strtotime($fecha_re));
	$fecha_re2 = date('d/m/Y', strtotime($fecha_re2));
	$fecha_re3 = date('d/m/Y', strtotime($fecha_re3));



			$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

      $almacen_rt = "";
      $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
      $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
      $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
      if($f1 <= $f2){
        //echo "aqui nena".$fecha_re;
        $almacen_rt = ", 1210";
      }
      if ($f1 == $f3) {
        $almacen_rt = "";
      }

	$conn = conexion::conectar();
	$res_array = array();
	$sql = "SELECT Z.V_NOMBRE, Z.V_RAZON_SOCIAL, Z.V_PARTE_ALTERNATIVA, SUM(Z.CDS_N_MES1) AS CDS_N_MES1, SUM(Z.CDS_N_MES2) AS CDS_N_MES2 FROM (
        SELECT T1.* FROM (
        SELECT X.V_NOMBRE, X.V_RAZON_SOCIAL, X.V_PARTE_ALTERNATIVA, SUM(X.CANTIDADN) AS CDS_N_MES1, NULL AS CDS_N_MES2
        FROM ( select a.v_nombre,
                      t.iid_num_cliente as cliente,
                     s.v_razon_social,
                     q.c_peso_total AS CANTIDADN ,
                      u.v_parte_alternativa
               from op_in_recibo_deposito t,
                    op_in_recibo_deposito_det q,
                    op_ce_tipo_cambio r,
                    almacen a,
                    cliente s,
                    op_in_partes u,
                    co_ume v
                where ((t.i_sal_cero = 1
                                     and t.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                                         or (t.i_sal_cero = 0 and t.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss')
                                         and t.d_fec_sal_cero > to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss')))
                      and (t.vid_recibo=q.vid_recibo and t.s_status='P')
                      and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
                      and (t.iid_almacen = a.iid_almacen)
                      and (t.iid_num_cliente = s.iid_num_cliente)
                      and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
                      and q.iid_um=v.iid_ume
                      and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN in (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
                      and s.iid_num_cliente <> 1261
        union all
              select a.v_nombre,
                     t.iid_num_cliente as cliente,
                     c.v_razon_social,
                     (q.c_peso_total*-1) AS CANTIDADN,
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
                    AND T.D_FECHA_REG <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss')
                    and (t.vid_recibo=r.vid_recibo
                        and ((r.i_sal_cero = 1
                        and r.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                        or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss')
                        and r.d_fec_sal_cero > to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
                   and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
                   and (t.iid_almacen = a.iid_almacen)
                   and (t.iid_num_cliente = c.iid_num_cliente)
                   and (q.vid_num_parte=u.vid_num_parte
                   and u.v_parte_alternativa like '%ZAFRA%')
                   and q.iid_um=v.iid_ume
                   and c.iid_num_cliente <> 1261
                   and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN in (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' ))) )X
        GROUP BY X.V_PARTE_ALTERNATIVA, X.V_NOMBRE, X.V_RAZON_SOCIAL
        ORDER BY  X.V_PARTE_ALTERNATIVA,  X.V_NOMBRE, X.V_RAZON_SOCIAL ASC  ) T1

        UNION ALL

        SELECT T2.* FROM (
        SELECT X.V_NOMBRE, X.V_RAZON_SOCIAL, X.V_PARTE_ALTERNATIVA, NULL AS CDS_N_MES1, SUM(X.CANTIDADS2) AS CDS_S_MES2 FROM ( select a.v_nombre,
                      t.iid_num_cliente as cliente,
                      s.v_razon_social,
                      q.c_peso_total as CANTIDADS2,
                      u.v_parte_alternativa
               from op_in_recibo_deposito t,
                    op_in_recibo_deposito_det q,
                    op_ce_tipo_cambio r,
                    almacen a,
                    cliente s,
                    op_in_partes u,
                    co_ume v
                where ((t.i_sal_cero = 1
                                     and t.d_plazo_dep_ini <= to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                                         or (t.i_sal_cero = 0 and t.d_plazo_dep_ini <= to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss')
                                         and t.d_fec_sal_cero > to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss')))
                      and (t.vid_recibo=q.vid_recibo and t.s_status='P')
                      and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
                      and (t.iid_almacen = a.iid_almacen)
                      and (t.iid_num_cliente = s.iid_num_cliente)
                      and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
                      and q.iid_um=v.iid_ume
                      and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN in (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
                      and s.iid_num_cliente <> 1261
        union all
              select a.v_nombre,
                     t.iid_num_cliente as cliente,
                     c.v_razon_social,
                     (q.c_peso_total*-1) as CANTIDADS2,
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
                    AND T.D_FECHA_REG <= to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss')
                    and (t.vid_recibo=r.vid_recibo
                        and ((r.i_sal_cero = 1
                        and r.d_plazo_dep_ini <= to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                        or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss')
                        and r.d_fec_sal_cero > to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
                   and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
                   and (t.iid_almacen = a.iid_almacen)
                   and (t.iid_num_cliente = c.iid_num_cliente)
                   and (q.vid_num_parte=u.vid_num_parte
                   and u.v_parte_alternativa like '%ZAFRA%')
                   and q.iid_um=v.iid_ume
                   and c.iid_num_cliente <> 1261
                   and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN in (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' ))) )X
        GROUP BY X.V_PARTE_ALTERNATIVA,  X.V_NOMBRE, X.V_RAZON_SOCIAL
        ORDER BY X.V_PARTE_ALTERNATIVA ,  X.V_NOMBRE, X.V_RAZON_SOCIAL ASC
        ) T2  ) Z

        GROUP BY Z.V_PARTE_ALTERNATIVA, Z.V_NOMBRE, Z.V_RAZON_SOCIAL
        ORDER BY Z.V_PARTE_ALTERNATIVA";
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
public function tabla_toneladas4($fecha){
	$fecha_inicial = substr($fecha, 11, 10);
	$mesFin = substr($fecha, 14, 2);
	$anioFin = substr($fecha,17, 5);

  $fecha_re = date($fecha_inicial);
	$fecha_re = str_replace('/', '-', $fecha_re);
	$fecha_re = date('Y-m-d', strtotime($fecha_re." -0 day"));
  $fecha_re2 = date($fecha_inicial);
	$fecha_re2 = str_replace('/', '-', $fecha_re2);
	$fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -1 day"));
  $fecha_re = date('d/m/Y', strtotime($fecha_re));
  $fecha_re2 = date('d/m/Y', strtotime($fecha_re2));

	$month = $anioFin."-".$mesFin;
	$aux = date('Y-m-d', strtotime("{$month} + 1 month"));

	$ultimo_dia3 = date('Y-m-d', strtotime("{$month} - 1 day"));
	$ultimo_dia4 = date('Y-m-d', strtotime("{$month} - 2 day"));



	$fecha_re3 = date('d/m/Y', strtotime($ultimo_dia3));
	$fecha_re4 = date('d/m/Y', strtotime($ultimo_dia4));

	$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

  $almacen_rt = "";
  $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
  $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
  $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
  if($f1 <= $f2){
    //echo "aqui nena".$fecha_re;
    $almacen_rt = ", 1210";
  }

  if ($f1 == $f3) {
    $almacen_rt = "";
  }


	$conn = conexion::conectar();
	$res_array = array();
	$sql = "SELECT Z.V_NOMBRE, Z.V_RAZON_SOCIAL, Z.V_PARTE_ALTERNATIVA, SUM(Z.CDS_N_MES1) AS CDN_MES1, SUM(Z.CDS_N_MES2) AS CDN_MES2 FROM (
				SELECT T1.* FROM (
				SELECT X.V_NOMBRE, X.V_RAZON_SOCIAL,X.V_PARTE_ALTERNATIVA, SUM(X.CANTIDADN) AS CDS_N_MES1, NULL AS CDS_N_MES2
				FROM ( select a.v_nombre,
				              t.iid_num_cliente as cliente,
				             s.v_razon_social,
				             q.c_peso_total AS CANTIDADN ,
				              u.v_parte_alternativa
				       from op_in_recibo_deposito t,
				            op_in_recibo_deposito_det q,
				            op_ce_tipo_cambio r,
				            almacen a,
				            cliente s,
				            op_in_partes u,
				            co_ume v
				        where ((t.i_sal_cero = 1
				                             and t.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss'))
				                                 or (t.i_sal_cero = 0 and t.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss')
				                                 and t.d_fec_sal_cero > to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss')))
				              and (t.vid_recibo=q.vid_recibo and t.s_status='P')
				              and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
				              and (t.iid_almacen = a.iid_almacen)
				              and (t.iid_num_cliente = s.iid_num_cliente)
				              and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
				              and q.iid_um=v.iid_ume
                      and s.iid_num_cliente <> 1261
                      and s.iid_num_cliente <> 1658
				              and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN in (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
				union all
				      select a.v_nombre,
				             t.iid_num_cliente as cliente,
				             c.v_razon_social,
				             (q.c_peso_total*-1) AS CANTIDADN,
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
				            AND T.D_FECHA_REG <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss')
				            and (t.vid_recibo=r.vid_recibo
				                and ((r.i_sal_cero = 1
				                and r.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss'))
				                or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('$fecha_re 23:59:59','dd/mm/yyyy hh24:mi:ss')
				                and r.d_fec_sal_cero > to_date('$fecha_re2 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
				           and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
				           and (t.iid_almacen = a.iid_almacen)
				           and (t.iid_num_cliente = c.iid_num_cliente)
				           and (q.vid_num_parte=u.vid_num_parte
				           and u.v_parte_alternativa like '%ZAFRA%')
				           and q.iid_um=v.iid_ume
                   and c.iid_num_cliente <> 1261
                   and c.iid_num_cliente <> 1658
				           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN in (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' ))) )X
				GROUP BY X.V_NOMBRE, X.V_RAZON_SOCIAL, X.V_PARTE_ALTERNATIVA
				ORDER BY  X.V_PARTE_ALTERNATIVA ASC  ) T1

				UNION ALL

				SELECT T2.* FROM (
				SELECT X.V_NOMBRE, X.V_RAZON_SOCIAL, X.V_PARTE_ALTERNATIVA, NULL AS CDS_N_MES1, SUM(X.CANTIDADS2) AS CDS_S_MES2 FROM ( select a.v_nombre,
				              t.iid_num_cliente as cliente,
				              s.v_razon_social,
				              q.c_peso_total as CANTIDADS2,
				              u.v_parte_alternativa
				       from op_in_recibo_deposito t,
				            op_in_recibo_deposito_det q,
				            op_ce_tipo_cambio r,
				            almacen a,
				            cliente s,
				            op_in_partes u,
				            co_ume v
				        where ((t.i_sal_cero = 1
				                             and t.d_plazo_dep_ini <= to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss'))
				                                 or (t.i_sal_cero = 0 and t.d_plazo_dep_ini <= to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss')
				                                 and t.d_fec_sal_cero > to_date('$fecha_re4 23:59:59','dd/mm/yyyy hh24:mi:ss')))
				              and (t.vid_recibo=q.vid_recibo and t.s_status='P')
				              and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
				              and (t.iid_almacen = a.iid_almacen)
				              and (t.iid_num_cliente = s.iid_num_cliente)
				              and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
				              and q.iid_um=v.iid_ume
                      and s.iid_num_cliente <> 1261
                      and s.iid_num_cliente <> 1658
				              and ((t.vid_certificado is not null and t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
				union all
				      select a.v_nombre,
				             t.iid_num_cliente as cliente,
				             c.v_razon_social,
				             (q.c_peso_total*-1) as CANTIDADS2,
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
				            AND T.D_FECHA_REG <= to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss')
				            and (t.vid_recibo=r.vid_recibo
				                and ((r.i_sal_cero = 1
				                and r.d_plazo_dep_ini <= to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss'))
				                or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('$fecha_re3 23:59:59','dd/mm/yyyy hh24:mi:ss')
				                and r.d_fec_sal_cero > to_date('$fecha_re4 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
				           and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
				           and (t.iid_almacen = a.iid_almacen)
				           and (t.iid_num_cliente = c.iid_num_cliente)
				           and (q.vid_num_parte=u.vid_num_parte
				           and u.v_parte_alternativa like '%ZAFRA%')
				           and q.iid_um=v.iid_ume
                   and c.iid_num_cliente <> 1261
                   and c.iid_num_cliente <> 1658
				           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' ))) )X
				GROUP BY X.V_NOMBRE, X.V_RAZON_SOCIAL,X.V_PARTE_ALTERNATIVA
				ORDER BY X.V_PARTE_ALTERNATIVA ASC
				) T2  ) Z
				GROUP BY Z.V_NOMBRE, Z.V_RAZON_SOCIAL,Z.V_PARTE_ALTERNATIVA
				ORDER BY Z.V_PARTE_ALTERNATIVA, Z.V_RAZON_SOCIAL";
//or (r.vid_certificado is null and r.i_administrativo = 1)
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
	/*========================TABLA TONELADAS 5 =============================*/
public function tabla_toneladas5($fecha){
	$fecha_inicial = substr($fecha, 11, 10);
	$mesFin = substr($fecha, 14, 2);
	$anioFin = substr($fecha,17, 5);

	$month = $anioFin."-".$mesFin;
	#$aux = date('Y-m-d', strtotime("{$month} - 1 month"));
	$ultimo_dia = date('Y-m-d', strtotime("{$month} - 0 day"));
	$ultimo_dia2 = date('Y-m-d', strtotime("{$month} - 1 day"));

	$fecha_re = date('d/m/Y', strtotime($ultimo_dia));
	$fecha_re2 = date('d/m/Y', strtotime($ultimo_dia2));
			$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

      $almacen_rt = "";
      $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
      $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
      $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
      if($f1 <= $f2){
        //echo "aqui nena".$fecha_re;
        $almacen_rt = ", 1210";
      }

      if ($f1 == $f3) {
        $almacen_rt = "";
      }

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
					and ((t.vid_certificado is not null and t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
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
								and ((r.vid_certificado is not null and r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
				)
				GROUP BY V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, CLIENTE
				ORDER BY V_PARTE_ALTERNATIVA";
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
	/*====================== /*TABLA DE NOMINA PAGADA ======================*/


	/*====================== SQL DINAMICO ======================*/

public function tabla_toneladas6($fecha, $almacen){
  $mesIni = substr($fecha, 3, 2);
  $anioIni = substr($fecha, 6,4);
  $mesFin = substr($fecha, 14, 2);
  $anioFin = substr($fecha,17, 5);

  $fecha_inicial2 = substr($fecha, 0, 10);
  $fecha_inicial = substr($fecha, 11, 10);

  $fecha_re = date($fecha_inicial);
  $fecha_re2 = date($fecha_inicial);

  $fecha_re = str_replace('/', '-', $fecha_re);
  $fecha_re = date('Y-m-d', strtotime($fecha_re));

  $fecha_re2 = str_replace('/', '-', $fecha_re2);
  $fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -1 day"));

  $fecha_re = date('d/m/Y', strtotime($fecha_re));
  $fecha_re2 = date('d/m/Y', strtotime($fecha_re2));

  			$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

        if ($almacen == '1211') {
            $andAlmacen = 'and t.iid_almacen in (1211, 1205, 1209, 1370, 1223, 1208, 1210)';
        }elseif ($almacen == '1468') {
           $andAlmacen = 'and t.iid_almacen in (1468)';
        }elseif ($almacen == '1657') {
          $andAlmacen = 'and t.iid_almacen in (1657)';
        }
        else {
            $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
        }

        $almacen_rt = "";
        $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
        $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
        $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
        if($f1 <= $f2){
          //echo "aqui nena".$fecha_re;
          $almacen_rt = ", 1210";
        }
        if ($f1 == $f3) {
          $almacen_rt = "";
        }



    	$conn = conexion::conectar();
    	$res_array = array();
    	$sql = "SELECT V_NOMBRE,
                       V_RAZON_SOCIAL,
                       V_PARTE_ALTERNATIVA,
                       SUM(CANTIDADN)AS CANTIDADN,
                       SUM(CANTIDADS) AS CANTIDADS ,
                       CLIENTE
                FROM ( select a.v_nombre,
                              t.iid_num_cliente as cliente,
                              s.v_razon_social,
                              CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN
                                        q.c_peso_total
                                   END as CANTIDADN,
                              CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN
                                        q.c_peso_total
                                   END as CANTIDADS,
                              u.v_parte_alternativa
                        from op_in_recibo_deposito t,
                             op_in_recibo_deposito_det q,
                             op_ce_tipo_cambio r,
                             almacen a,
                             cliente s,
                             op_in_partes u,
                             co_ume v
                        where ((t.i_sal_cero = 1
                                             and t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                                             or (t.i_sal_cero = 0 and t.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                                             and t.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59','dd/mm/yyyy hh24:mi:ss')))
                                and (t.vid_recibo=q.vid_recibo and t.s_status='P')
                                and (to_char(t.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(r.did_fecha,'dd/mm/yyyy'))
                                and (t.iid_almacen = a.iid_almacen)
                                and (t.iid_num_cliente = s.iid_num_cliente)
                                ".$andAlmacen."
                                and (q.vid_num_parte=u.vid_num_parte and u.v_parte_alternativa like '%ZAFRA%')
                                and q.iid_um=v.iid_ume
                                and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
                                and s.iid_num_cliente <> 1261
                union all select a.v_nombre,
                                 t.iid_num_cliente as cliente,
                                 c.v_razon_social,
                                 CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN
                                      (q.c_peso_total*-1)
                                 END as CANTIDADN,
                                 CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN
                                      (q.c_peso_total*-1)
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
                                 AND T.D_FECHA_REG <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                                 and (t.vid_recibo=r.vid_recibo and ((r.i_sal_cero = 1 and r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss'))
                                 or (r.i_sal_cero = 0 and r.d_plazo_dep_ini <= to_date('".$fecha_re." 23:59:59','dd/mm/yyyy hh24:mi:ss')
                                 and r.d_fec_sal_cero > to_date('".$fecha_re2." 23:59:59','dd/mm/yyyy hh24:mi:ss'))))
                                 and (to_char(r.d_plazo_dep_ini,'dd/mm/yyyy') = to_char(s.did_fecha,'dd/mm/yyyy'))
                                 and (t.iid_almacen = a.iid_almacen)
                                 and (t.iid_num_cliente = c.iid_num_cliente)
                                 ".$andAlmacen."
                                 and (q.vid_num_parte=u.vid_num_parte
                                 and u.v_parte_alternativa like '%ZAFRA%')
                                 and q.iid_um=v.iid_ume
                                 and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' ))) and c.iid_num_cliente <> 1261 )
                GROUP BY V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, CLIENTE ORDER BY CLIENTE, V_PARTE_ALTERNATIVA";

              #  echo $sql;
                if ($almacen == 1586) {
                  #echo $sql;
                }
    			$stid = oci_parse($conn, $sql);
    			oci_execute($stid);

          if ($almacen == 1660) {

          }
    			while (($row = oci_fetch_assoc($stid)) != false)
    			{
    				$res_array[]= $row;
    			}
    			oci_free_statement($stid);
    			oci_close($conn);
    			return $res_array;
  	}
  	/*====================== /*TABLA DE NOMINA PAGADA ======================*/



public function sql($option,$depto,$plaza){
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM DUAL";
	 	switch ($option) {
			case '1':
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0), 'DD/MM/YYYY') mes1, TO_CHAR(TRUNC(TO_DATE(SYSDATE, 'DD/MM/YYY') -1), 'DD/MM/YYYY') mes2 FROM DUAL";
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


  public function tabla_toneladas_espacioOcupado($fecha){
      $mesIni = substr($fecha, 3, 2);
      $anioIni = substr($fecha, 6,4);
      $mesFin = substr($fecha, 14, 2);
      $anioFin = substr($fecha,17, 5);

  		$fecha_inicial2 = substr($fecha, 0, 10);
  		$fecha_inicial = substr($fecha, 11, 10);

  		$fecha_re = date($fecha_inicial);
      $fecha_re2 = date($fecha_inicial);

  		$fecha_re = str_replace('/', '-', $fecha_re);
  		$fecha_re = date('Y-m-d', strtotime($fecha_re." -0 day"));

      $fecha_re2 = str_replace('/', '-', $fecha_re2);
  		$fecha_re2 = date('Y-m-d', strtotime($fecha_re2." -1 day"));

  		$fecha_re = date('d/m/Y', strtotime($fecha_re));
      $fecha_re2 = date('d/m/Y', strtotime($fecha_re2));



  		    $andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";

          $almacen_rt = "";
          $f1 = DateTime::createFromFormat('d/m/Y', $fecha_re);
          $f2 = DateTime::createFromFormat('d/m/Y', '19/09/2021');
          $f3 = DateTime::createFromFormat('d/m/Y', '05/09/2021');
          if($f1 <= $f2){
            //echo "aqui nena".$fecha_re;
            $almacen_rt = ", 1210";
          }
          if ($f1 == $f3) {
            $almacen_rt = "";
          }

  		$conn = conexion::conectar();
  		$res_array = array();
  		$sql = "SELECT V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, SUM(CANTIDADN)AS CANTIDADN, SUM(CANTIDADS) AS CANTIDADS , CLIENTE FROM
  		(
    	select a.v_nombre,
  					t.iid_num_cliente as cliente,
           s.v_razon_social,
  				 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL
                          THEN q.c_peso_total
                END as CANTIDADN,
        	 CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL
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
                      		        and (t.vid_certificado is not null)
                                  and s.iid_num_cliente <> 1261
                                  and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' )))
  					  union all
  					  select a.v_nombre,
  										t.iid_num_cliente as cliente,
  					         c.v_razon_social,
  									 CASE
                    WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL
                         THEN (q.c_peso_total*-1)
                    END as CANTIDADN,
               			CASE
                    WHEN  (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL
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
  					        and (r.vid_certificado is not null)
                    and c.iid_num_cliente <> 1261
                    and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN IN (1468 $almacen_rt) AND S.VID_CERTIFICADO LIKE '%-N%' ))) and c.iid_num_cliente <> 1261 )
  					)
  					GROUP BY V_NOMBRE, V_RAZON_SOCIAL, V_PARTE_ALTERNATIVA, CLIENTE
  					ORDER BY CLIENTE, V_PARTE_ALTERNATIVA";
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


}
