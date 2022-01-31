<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: DIEGO ALTAMIRANO SUAREZ
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class RotacionPersonal
{

	/*++++++++++++++++++++++++ GRAFICA PERSONAL CURSO ++++++++++++++++++++++++*/
public function grafica($anio,$n_semana){
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT RH.N_MES, RH.MES ,( SELECT SUM(MG.VALOR_TOTAL) FROM OP_MESES_GRAFICAS_AZUCAR MG
       WHERE TO_CHAR(MG.FECHA_REG, 'YYYY') = '".$anio."'
             AND MG.TIPE = 1 AND TO_CHAR(MG.FECHA_REG, 'MM') = RH.N_MES
             AND MG.FECHA_REG = (SELECT MAX(G.FECHA_REG)
                                      FROM OP_MESES_GRAFICAS_AZUCAR G
                                      WHERE TO_CHAR(G.FECHA_REG, 'YYYY') = '".$anio."'
                                      AND TO_CHAR(G.FECHA_REG, 'MM') = RH.N_MES
                                      AND G.TIPE = 1)
             ) AS VALOR FROM RH_MESES_GRAFICAS RH
             ORDER BY RH.N_MES";
				#echo $sql;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}

		#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);

			//	echo $sql;
				return $res_array;

	}

	public function lastday($fecha){
			$conn = conexion::conectar();
			$res_array = array();

			$sql = "SELECT  (NVL(SUM(CANTIDADN), 0) +  NVL(SUM(CANTIDADS), 0))/1000 AS CANTIDADTOTAL
     FROM (select CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN q.c_peso_total
               END as CANTIDADN,
               CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN q.c_peso_total
               END as CANTIDADS
           from op_in_recibo_deposito     t,
               op_in_recibo_deposito_det q,
               op_ce_tipo_cambio         r,
               almacen                   a,
               cliente                   s,
               op_in_partes              u,
               co_ume                    v
           where ((t.i_sal_cero = 1 and
               t.d_plazo_dep_ini <= to_date('$fecha', 'dd/mm/yyyy')) or
               (t.i_sal_cero = 0 and
               t.d_plazo_dep_ini <= to_date('$fecha', 'dd/mm/yyyy') and
               t.d_fec_sal_cero >  to_date('$fecha', 'dd/mm/yyyy')-1 ))
           and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
           and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') =
               to_char(r.did_fecha, 'dd/mm/yyyy'))
           and (t.iid_almacen = a.iid_almacen)
           and (t.iid_num_cliente = s.iid_num_cliente)
           and (q.vid_num_parte = u.vid_num_parte and
               u.v_parte_alternativa like '%ZAFRA%')
           and q.iid_um = v.iid_ume
           and s.iid_num_cliente <> 1261
					 and a.iid_almacen not in (1210)
           and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN  = 1468  AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     or (t.vid_certificado is null and t.i_administrativo = 1))
           union all
           select CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN (q.c_peso_total * -1)
               END as CANTIDADN,
               CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN (q.c_peso_total * -1)
               END AS CANTIDADS
           from op_in_ord_salida      t,
               op_in_ord_salida_det  q,
               op_in_recibo_deposito r,
               op_ce_tipo_cambio     s,
               almacen               a,
               cliente               c,
               op_in_partes          u,
               co_ume                v
           where t.vid_ord_sal = q.vid_ord_sal
           and t.v_status = 'P'
           AND T.D_FECHA_REG <=  to_date('$fecha', 'dd/mm/yyyy')
           and (t.vid_recibo = r.vid_recibo and
               ((r.i_sal_cero = 1 and
               r.d_plazo_dep_ini <=  to_date('$fecha', 'dd/mm/yyyy') ) or
               (r.i_sal_cero = 0 and
               r.d_plazo_dep_ini <=  to_date('$fecha', 'dd/mm/yyyy') and
               r.d_fec_sal_cero >  to_date('$fecha', 'dd/mm/yyyy')-1)))
           and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') =
               to_char(s.did_fecha, 'dd/mm/yyyy'))
           and (t.iid_almacen = a.iid_almacen)
           and (t.iid_num_cliente = c.iid_num_cliente)
           and (q.vid_num_parte = u.vid_num_parte and
               u.v_parte_alternativa like '%ZAFRA%')
           and q.iid_um = v.iid_ume
           And c.iid_num_cliente <> 1261
					 and a.iid_almacen not in (1210)
           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     or (r.vid_certificado is null and r.i_administrativo = 1))
           )";
					#echo $sql;
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);

					while (($row = oci_fetch_assoc($stid)) != false)
					{
						$res_array[]= $row;
					}

			#echo $sql;
					oci_free_statement($stid);
					oci_close($conn);

				//	echo $sql;
					return $res_array;

		}

	public function lastWeek(){
			$conn = conexion::conectar();
			$res_array = array();

			$sql = "SELECT  (NVL(SUM(CANTIDADN), 0) +  NVL(SUM(CANTIDADS), 0))/1000 AS CANTIDADTOTAL
     FROM (select CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN q.c_peso_total
               END as CANTIDADN,
               CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN q.c_peso_total
               END as CANTIDADS
           from op_in_recibo_deposito     t,
               op_in_recibo_deposito_det q,
               op_ce_tipo_cambio         r,
               almacen                   a,
               cliente                   s,
               op_in_partes              u,
               co_ume                    v
           where ((t.i_sal_cero = 1 and
               t.d_plazo_dep_ini <= SYSDATE) or
               (t.i_sal_cero = 0 and
               t.d_plazo_dep_ini <= SYSDATE and
               t.d_fec_sal_cero > SYSDATE -1 ))
           and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
           and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') =
               to_char(r.did_fecha, 'dd/mm/yyyy'))
           and (t.iid_almacen = a.iid_almacen)
           and (t.iid_num_cliente = s.iid_num_cliente)
           and (q.vid_num_parte = u.vid_num_parte and
               u.v_parte_alternativa like '%ZAFRA%')
           and q.iid_um = v.iid_ume
           and s.iid_num_cliente <> 1261
					 and a.iid_almacen not in (1210)
           and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN  = 1468  AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     or (t.vid_certificado is null and t.i_administrativo = 1))
           union all
           select CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN (q.c_peso_total * -1)
               END as CANTIDADN,
               CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN (q.c_peso_total * -1)
               END AS CANTIDADS
           from op_in_ord_salida      t,
               op_in_ord_salida_det  q,
               op_in_recibo_deposito r,
               op_ce_tipo_cambio     s,
               almacen               a,
               cliente               c,
               op_in_partes          u,
               co_ume                v
           where t.vid_ord_sal = q.vid_ord_sal
           and t.v_status = 'P'
           AND T.D_FECHA_REG <= SYSDATE
           and (t.vid_recibo = r.vid_recibo and
               ((r.i_sal_cero = 1 and
               r.d_plazo_dep_ini <= SYSDATE ) or
               (r.i_sal_cero = 0 and
               r.d_plazo_dep_ini <= SYSDATE and
               r.d_fec_sal_cero > SYSDATE -1)))
           and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') =
               to_char(s.did_fecha, 'dd/mm/yyyy'))
           and (t.iid_almacen = a.iid_almacen)
           and (t.iid_num_cliente = c.iid_num_cliente)
           and (q.vid_num_parte = u.vid_num_parte and
               u.v_parte_alternativa like '%ZAFRA%')
           and q.iid_um = v.iid_ume
           And c.iid_num_cliente <> 1261
					 and a.iid_almacen not in (1210)
           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     or (r.vid_certificado is null and r.i_administrativo = 1))
           )";
					#echo $sql;
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);

					while (($row = oci_fetch_assoc($stid)) != false)
					{
						$res_array[]= $row;
					}

			#echo $sql;
					oci_free_statement($stid);
					oci_close($conn);

				//	echo $sql;
					return $res_array;

		}

		public function lastWeekHabilitados(){
				$conn = conexion::conectar();
				$res_array = array();

				$sql = "SELECT  (NVL(SUM(CANTIDADN), 0) +  NVL(SUM(CANTIDADS), 0))/1000 AS CANTIDADTOTAL
	     FROM (select CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN q.c_peso_total
	               END as CANTIDADN,
	               CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN q.c_peso_total
	               END as CANTIDADS
	           from op_in_recibo_deposito     t,
	               op_in_recibo_deposito_det q,
	               op_ce_tipo_cambio         r,
	               almacen                   a,
	               cliente                   s,
	               op_in_partes              u,
	               co_ume                    v
	           where ((t.i_sal_cero = 1 and
	               t.d_plazo_dep_ini <= SYSDATE) or
	               (t.i_sal_cero = 0 and
	               t.d_plazo_dep_ini <= SYSDATE and
	               t.d_fec_sal_cero > SYSDATE -1 ))
	           and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
	           and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') =
	               to_char(r.did_fecha, 'dd/mm/yyyy'))
	           and (t.iid_almacen = a.iid_almacen)
	           and (t.iid_num_cliente = s.iid_num_cliente)
	           and (q.vid_num_parte = u.vid_num_parte and
	               u.v_parte_alternativa like '%ZAFRA%')
	           and q.iid_um = v.iid_ume
	           AND a.s_tipo_almacen in (3, 5, 15)
	           and s.iid_num_cliente <> 1261
						 and a.iid_almacen not in (1210)
	           and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
	                     or (t.vid_certificado is null and t.i_administrativo = 1))
	           union all
	           select CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN (q.c_peso_total * -1)
	               END as CANTIDADN,
	               CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN (q.c_peso_total * -1)
	               END AS CANTIDADS
	           from op_in_ord_salida      t,
	               op_in_ord_salida_det  q,
	               op_in_recibo_deposito r,
	               op_ce_tipo_cambio     s,
	               almacen               a,
	               cliente               c,
	               op_in_partes          u,
	               co_ume                v
	           where t.vid_ord_sal = q.vid_ord_sal
	           and t.v_status = 'P'
	           AND T.D_FECHA_REG <= SYSDATE
	           and (t.vid_recibo = r.vid_recibo and
	               ((r.i_sal_cero = 1 and
	               r.d_plazo_dep_ini <= SYSDATE ) or
	               (r.i_sal_cero = 0 and
	               r.d_plazo_dep_ini <= SYSDATE and
	               r.d_fec_sal_cero > SYSDATE -1)))
	           and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') =
	               to_char(s.did_fecha, 'dd/mm/yyyy'))
	           and (t.iid_almacen = a.iid_almacen)
	           and (t.iid_num_cliente = c.iid_num_cliente)
	           and (q.vid_num_parte = u.vid_num_parte and
	               u.v_parte_alternativa like '%ZAFRA%')
	           and q.iid_um = v.iid_ume
	           AND a.s_tipo_almacen in (3, 5, 15)
	           And c.iid_num_cliente <> 1261
						 and a.iid_almacen not in (1210)
	           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN  = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
	                     or (r.vid_certificado is null and r.i_administrativo = 1))
	           )";
						#echo $sql;
						$stid = oci_parse($conn, $sql);
						oci_execute($stid);

						while (($row = oci_fetch_assoc($stid)) != false)
						{
							$res_array[]= $row;
						}

				#echo $sql;
						oci_free_statement($stid);
						oci_close($conn);

					//	echo $sql;
						return $res_array;

			}

			public function lastWeekDirectos(){
					$conn = conexion::conectar();
					$res_array = array();

					$sql = "SELECT  (NVL(SUM(CANTIDADN), 0) +  NVL(SUM(CANTIDADS), 0))/1000 AS CANTIDADTOTAL
		     FROM (select CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN q.c_peso_total
		               END as CANTIDADN,
		               CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN q.c_peso_total
		               END as CANTIDADS
		           from op_in_recibo_deposito     t,
		               op_in_recibo_deposito_det q,
		               op_ce_tipo_cambio         r,
		               almacen                   a,
		               cliente                   s,
		               op_in_partes              u,
		               co_ume                    v
		           where ((t.i_sal_cero = 1 and
		               t.d_plazo_dep_ini <= SYSDATE) or
		               (t.i_sal_cero = 0 and
		               t.d_plazo_dep_ini <= SYSDATE and
		               t.d_fec_sal_cero > SYSDATE -1 ))
		           and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
		           and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') =
		               to_char(r.did_fecha, 'dd/mm/yyyy'))
		           and (t.iid_almacen = a.iid_almacen)
		           and (t.iid_num_cliente = s.iid_num_cliente)
		           and (q.vid_num_parte = u.vid_num_parte and
		               u.v_parte_alternativa like '%ZAFRA%')
		           and q.iid_um = v.iid_ume
		           AND a.s_tipo_almacen in (2, 6)
		           and s.iid_num_cliente <> 1261
							 and a.iid_almacen not in (1210)
		           and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
		                     or (t.vid_certificado is null and t.i_administrativo = 1))
		           union all
		           select CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN (q.c_peso_total * -1)
		               END as CANTIDADN,
		               CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN (q.c_peso_total * -1)
		               END AS CANTIDADS
		           from op_in_ord_salida      t,
		               op_in_ord_salida_det  q,
		               op_in_recibo_deposito r,
		               op_ce_tipo_cambio     s,
		               almacen               a,
		               cliente               c,
		               op_in_partes          u,
		               co_ume                v
		           where t.vid_ord_sal = q.vid_ord_sal
		           and t.v_status = 'P'
		           AND T.D_FECHA_REG <= SYSDATE
		           and (t.vid_recibo = r.vid_recibo and
		               ((r.i_sal_cero = 1 and
		               r.d_plazo_dep_ini <= SYSDATE ) or
		               (r.i_sal_cero = 0 and
		               r.d_plazo_dep_ini <= SYSDATE and
		               r.d_fec_sal_cero > SYSDATE -1)))
		           and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') =
		               to_char(s.did_fecha, 'dd/mm/yyyy'))
		           and (t.iid_almacen = a.iid_almacen)
		           and (t.iid_num_cliente = c.iid_num_cliente)
		           and (q.vid_num_parte = u.vid_num_parte and
		               u.v_parte_alternativa like '%ZAFRA%')
		           and q.iid_um = v.iid_ume
		           AND a.s_tipo_almacen in (2, 6)
		           And c.iid_num_cliente <> 1261
							 and a.iid_almacen not in (1210)
		           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
		                     or (r.vid_certificado is null and r.i_administrativo = 1))
		           )";
							#echo $sql;
							$stid = oci_parse($conn, $sql);
							oci_execute($stid);

							while (($row = oci_fetch_assoc($stid)) != false)
							{
								$res_array[]= $row;
							}

					#echo $sql;
							oci_free_statement($stid);
							oci_close($conn);

						//	echo $sql;
							return $res_array;

				}
  /*++++++++++++++++++++++++ GRAFICA AÑO PASADO ++++++++++++++++++++++++++++*/
public function grafica_anio_anterior($anio,$n_semana){
		$conn = conexion::conectar();
		$res_array = array();
    $anio = $anio -1;
		$sql = "SELECT RH.N_MES, RH.MES ,( SELECT SUM(MG.VALOR_TOTAL) FROM OP_MESES_GRAFICAS_AZUCAR MG
       			WHERE TO_CHAR(MG.FECHA_REG, 'YYYY') = '".$anio."'
             			AND MG.TIPE = 1 AND TO_CHAR(MG.FECHA_REG, 'MM') = RH.N_MES
             			AND MG.FECHA_REG = (SELECT MAX(G.FECHA_REG)
                                      FROM OP_MESES_GRAFICAS_AZUCAR G
                                      WHERE TO_CHAR(G.FECHA_REG, 'YYYY') = '".$anio."'
                                      AND TO_CHAR(G.FECHA_REG, 'MM') = RH.N_MES
                                      AND G.TIPE = 1)
             ) as VALOR FROM RH_MESES_GRAFICAS RH
             ORDER BY RH.N_MES";
				#echo $sql;
        #echo $anio;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$res_array[]= $row;
				}

		#echo $sql;
				oci_free_statement($stid);
				oci_close($conn);

			//	echo $sql;
				return $res_array;

	}

	/*++++++++++++++++++++++++ SQL DINAMICO FROM DUAL ++++++++++++++++++++++++*/

public function graficaHabilitados($anio,$n_semana){
  		$conn = conexion::conectar();
  		$res_array = array();

  		$sql = "SELECT RH.N_MES, RH.MES ,( SELECT SUM(MG.VALOR_TOTAL) FROM OP_MESES_GRAFICAS_AZUCAR MG
         WHERE TO_CHAR(MG.FECHA_REG, 'YYYY') = '".$anio."'
               AND MG.TIPE = 2 AND TO_CHAR(MG.FECHA_REG, 'MM') = RH.N_MES
               AND MG.FECHA_REG = (SELECT MAX(G.FECHA_REG)
                                        FROM OP_MESES_GRAFICAS_AZUCAR G
                                        WHERE TO_CHAR(G.FECHA_REG, 'YYYY') = '".$anio."'
                                        AND TO_CHAR(G.FECHA_REG, 'MM') = RH.N_MES
                                        AND G.TIPE = 2)) AS VALOR FROM RH_MESES_GRAFICAS RH
               ORDER BY RH.N_MES";
  				#echo $sql;
  				$stid = oci_parse($conn, $sql);
  				oci_execute($stid);

  				while (($row = oci_fetch_assoc($stid)) != false)
  				{
  					$res_array[]= $row;
  				}

  		#echo $sql;
  				oci_free_statement($stid);
  				oci_close($conn);

  			//	echo $sql;
  				return $res_array;

  	}
    /*++++++++++++++++++++++++ GRAFICA AÑO PASADO ++++++++++++++++++++++++++++*/
public function grafica_anio_anteriorHabilitadas($anio,$n_semana){
  		$conn = conexion::conectar();
  		$res_array = array();
      $anio = $anio -1;
  		$sql = "SELECT RH.N_MES, RH.MES ,( SELECT SUM(MG.VALOR_TOTAL) FROM OP_MESES_GRAFICAS_AZUCAR MG
         WHERE TO_CHAR(MG.FECHA_REG, 'YYYY') = '".$anio."'
               AND MG.TIPE = 2 AND TO_CHAR(MG.FECHA_REG, 'MM') = RH.N_MES
               AND MG.FECHA_REG = (SELECT MAX(G.FECHA_REG)
                                        FROM OP_MESES_GRAFICAS_AZUCAR G
                                        WHERE TO_CHAR(G.FECHA_REG, 'YYYY') = '".$anio."'
                                        AND TO_CHAR(G.FECHA_REG, 'MM') = RH.N_MES
                                        AND G.TIPE = 2)) as VALOR FROM RH_MESES_GRAFICAS RH
               ORDER BY RH.N_MES";

  				$stid = oci_parse($conn, $sql);
  				oci_execute($stid);

  				while (($row = oci_fetch_assoc($stid)) != false)
  				{
  					$res_array[]= $row;
  				}
  		#echo $sql;
  				oci_free_statement($stid);
  				oci_close($conn);

  			//	echo $sql;
  				return $res_array;

  	}

  	/*++++++++++++++++++++++++ SQL DINAMICO FROM DUAL ++++++++++++++++++++++++*/

public function graficaHabilitadoSemana($anio,$n_semana){
      		$conn = conexion::conectar();
      		$res_array = array();

      		$sql = "SELECT T.NO_SEMANA, (SELECT Y.VALOR_TOTAL
                  										 FROM op_meses_graficas_azucar y
                                       WHERE to_char(y.fecha_reg, 'yyyy') = '".$anio."'
																			    	 AND y.tipe = 2
                                             AND to_char(y.fecha_reg, 'ww') = t.no_semana) as valor
                   FROM OP_NO_SEMANAS T
                   ORDER BY T.NO_SEMANA";
      				#echo $sql;
      				$stid = oci_parse($conn, $sql);
      				oci_execute($stid);

      				while (($row = oci_fetch_assoc($stid)) != false)
      				{
      					$res_array[]= $row;
      				}

      		#echo $sql;
      				oci_free_statement($stid);
      				oci_close($conn);

      			//	echo $sql;
      				return $res_array;

      	}
        /*++++++++++++++++++++++++ GRAFICA AÑO PASADO ++++++++++++++++++++++++++++*/
public function grafica_anio_anteriorHabilitadaSemana($anio,$n_semana){
      		$conn = conexion::conectar();
      		$res_array = array();
          $anio = $anio -1;
      		$sql = "SELECT T.NO_SEMANA, (select Y.VALOR_TOTAL
                     from op_meses_graficas_azucar y
                                       where to_char(y.fecha_reg, 'yyyy') = '".$anio."' and y.tipe = 2
                                             and to_char(y.fecha_reg, 'ww') = t.no_semana) as valor
                        FROM OP_NO_SEMANAS T
                  ORDER BY T.NO_SEMANA";
      				#echo $sql;
              #echo $anio;
      				$stid = oci_parse($conn, $sql);
      				oci_execute($stid);

      				while (($row = oci_fetch_assoc($stid)) != false)
      				{
      					$res_array[]= $row;
      				}

      		#echo $sql;
      				oci_free_statement($stid);
      				oci_close($conn);

      			//	echo $sql;
      				return $res_array;

      	}

public function graficaDirectaSemana($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();

				      		$sql = "SELECT T.NO_SEMANA, NVL((SELECT Y.VALOR_TOTAL
				                  										 FROM op_meses_graficas_azucar y
				                                       WHERE to_char(y.fecha_reg, 'yyyy') = '".$anio."'
																							    	 AND y.tipe = 1
				                                             AND to_char(y.fecha_reg, 'ww') = t.no_semana), 0) as valor
				                   FROM OP_NO_SEMANAS T
				                   ORDER BY T.NO_SEMANA";
				      				#echo $sql;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      		#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}



public function grafica_semana_ocupacion($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
				      		$sql = "SELECT (
													       	 SELECT SUM(Q.C_PESO_TOTAL)/1000 FROM OP_IN_RECIBO_DEPOSITO T
													         INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Q ON T.VID_RECIBO = Q.VID_RECIBO
													         INNER JOIN OP_IN_PARTES U ON U.VID_NUM_PARTE = Q.vid_num_parte
													         INNER JOIN CO_UME V ON Q.IID_UM = V.IID_UME
																	 INNER JOIN ALMACEN A ON T.IID_ALMACEN = A.IID_ALMACEN
													WHERE (t.d_plazo_dep_ini >= to_date(TO_CHAR(TRUNC(to_date((SELECT SEM.NO_SEMANA FROM OP_NO_SEMANAS SEM WHERE SEM.NO_SEMANA = Z.NO_SEMANA) *7 || '/$anio', 'DDD/YYYY'), 'DAY')-2, 'dd/mm/yyyy')|| ' 23:59:59', 'dd/mm/yyyy hh24:mi:ss')
													and t.d_plazo_dep_ini <= to_date(TO_CHAR(TRUNC(to_date( (SELECT SEM.NO_SEMANA FROM OP_NO_SEMANAS SEM WHERE SEM.NO_SEMANA = Z.NO_SEMANA) *7 || '/$anio', 'DDD/YYYY')+4, 'DAY')+1, 'dd/mm/yyyy')|| ' 23:59:59', 'dd/mm/yyyy hh24:mi:ss') )
													AND T.S_STATUS = 'P' AND U.V_PARTE_ALTERNATIVA like '%ZAFRA%'
													AND A.S_TIPO_ALMACEN IN (2, 6)
													AND A.IID_ALMACEN NOT IN (1210)
													and (t.vid_certificado is not null or (t.vid_certificado is null and t.i_administrativo = 1))
													) AS ENTRIES,
													Z.NO_SEMANA
													FROM OP_NO_SEMANAS Z";
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}


public function inv_prom_semanal_zafra($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio -1;
				      		$sql = "SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
                          				 FROM op_meses_graficas_azucar y
                                   WHERE to_char(y.fecha_reg, 'yyyy') =  '$anio2'
                                   AND to_char(y.fecha_reg, 'ww') <= t.no_semana
                                   AND TO_CHAR(Y.FECHA_REG, 'ww')>= 41))/ROWNUM, 2) as valor, T.NO_SEMANA, ROWNUM AS NUMERO_FILA
												            FROM OP_NO_SEMANAS T
												            WHERE T.NO_SEMANA >= 41
												union
												SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
												                          				 FROM op_meses_graficas_azucar y
												                                   WHERE to_char(y.fecha_reg, 'yyyy') =  '$anio'
												                                   AND to_char(y.fecha_reg, 'ww') <= t.no_semana
												                                   AND TO_CHAR(Y.FECHA_REG, 'ww')< 41)+
												         (SELECT SUM(Y.VALOR_TOTAL)
												                                   FROM op_meses_graficas_azucar y
												                                   WHERE to_char(y.fecha_reg, 'yyyy') =  '$anio2'
												                                   AND to_char(y.fecha_reg, 'ww') >= 41))/ (ROWNUM + 12),2) as valor, T.NO_SEMANA, ROWNUM + 12 AS NUMERO_FILA
												            FROM OP_NO_SEMANAS T
												            WHERE T.NO_SEMANA < 41
												ORDER BY NUMERO_FILA";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}

public function inv_prom_semanal_zafra2011($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio +1;
				      		$sql = "SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
                          				 FROM op_meses_graficas_azucar y
                                   WHERE to_char(y.fecha_reg, 'yyyy') =  '$anio'
                                   AND to_char(y.fecha_reg, 'ww') <= t.no_semana
                                   AND TO_CHAR(Y.FECHA_REG, 'ww')>= 41))/ROWNUM, 2) as valor, T.NO_SEMANA, ROWNUM AS NUMERO_FILA
												            FROM OP_NO_SEMANAS T
												            WHERE T.NO_SEMANA >= 41
												union
												SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
												                          				 FROM op_meses_graficas_azucar y
												                                   WHERE to_char(y.fecha_reg, 'yyyy') =  '$anio2'
												                                   AND to_char(y.fecha_reg, 'ww') <= t.no_semana
												                                   AND TO_CHAR(Y.FECHA_REG, 'ww')< 41)+
												         (SELECT SUM(Y.VALOR_TOTAL)
												                                   FROM op_meses_graficas_azucar y
												                                   WHERE to_char(y.fecha_reg, 'yyyy') =  '$anio'
												                                   AND to_char(y.fecha_reg, 'ww') >= 41))/ (ROWNUM + 12),2) as valor, T.NO_SEMANA, ROWNUM + 12 AS NUMERO_FILA
												            FROM OP_NO_SEMANAS T
												            WHERE T.NO_SEMANA < 41
												ORDER BY NUMERO_FILA";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}
/*Primedio semanal en el ejercicio 2020*/
public function promedio_semanal_ejercicio($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio -1;
				      		$sql = "SELECT ROUND((nvl((SELECT SUM(Y.VALOR_TOTAL)
                 FROM op_meses_graficas_azucar y
                WHERE to_char(y.fecha_reg, 'yyyy') = '$anio'
                  AND to_char(y.fecha_reg, 'ww') <= t.no_semana
                  AND TO_CHAR(Y.FECHA_REG, 'ww') < 41), 0) +
							             (SELECT SUM(Y.VALOR_TOTAL)
							                 FROM op_meses_graficas_azucar y
							                WHERE to_char(y.fecha_reg, 'yyyy') = '$anio2'
							                  AND to_char(y.fecha_reg, 'ww') >= 41)) / (ROWNUM + 12),
							             2) as valor,
							       T.NO_SEMANA,
							       ROWNUM + 12 AS NUMERO_FILA
							  FROM OP_NO_SEMANAS T";
				      			#	echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}

public function promedio_semanal_ejercicio2011($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio +1;
				      		$sql = "SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
                 FROM op_meses_graficas_azucar y
                WHERE to_char(y.fecha_reg, 'yyyy') = '$anio2'
                  AND to_char(y.fecha_reg, 'ww') <= t.no_semana
                  AND TO_CHAR(Y.FECHA_REG, 'ww') < 41) +
							             (SELECT SUM(Y.VALOR_TOTAL)
							                 FROM op_meses_graficas_azucar y
							                WHERE to_char(y.fecha_reg, 'yyyy') = '$anio'
							                  AND to_char(y.fecha_reg, 'ww') >= 41)) / (ROWNUM + 12),
							             2) as valor,
							       T.NO_SEMANA,
							       ROWNUM + 12 AS NUMERO_FILA
							  FROM OP_NO_SEMANAS T";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}
/*Histograma Lineal*/
public function histograma_lineal($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio -1;
				      		$sql = "SELECT * FROM (SELECT SUM(H.VALOR_TOTAL) AS VALOR_TOTAL, TO_CHAR(H.FECHA_REG, 'MM/YYYY') AS FECHA
													FROM OP_MESES_GRAFICAS_AZUCAR H WHERE H.FECHA_REG = (SELECT MAX(HH.FECHA_REG) FROM OP_MESES_GRAFICAS_AZUCAR HH WHERE TO_CHAR(HH.FECHA_REG, 'MM/YYYY') = TO_CHAR(H.FECHA_REG, 'MM/YYYY')
																																							AND TO_CHAR(HH.FECHA_REG, 'MM/YYYY') <> TO_CHAR(SYSDATE, 'MM/YYYY'))
													GROUP BY H.FECHA_REG
													ORDER BY H.FECHA_REG)
UNION ALL
SELECT  (NVL(SUM(CANTIDADN), 0) +  NVL(SUM(CANTIDADS), 0))/1000 AS VALOR_TOTAL, TO_CHAR(SYSDATE, 'MM/YYYY') AS FECHA
     FROM (select CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'N') OR T.VID_CERTIFICADO IS NULL THEN q.c_peso_total
               END as CANTIDADN,
               CASE WHEN (SUBSTR(t.vid_certificado, -1, 1) = 'S') AND T.VID_CERTIFICADO IS NOT NULL THEN q.c_peso_total
               END as CANTIDADS
           from op_in_recibo_deposito     t,
               op_in_recibo_deposito_det q,
               op_ce_tipo_cambio         r,
               almacen                   a,
               cliente                   s,
               op_in_partes              u,
               co_ume                    v
           where ((t.i_sal_cero = 1 and
               t.d_plazo_dep_ini <= SYSDATE) or
               (t.i_sal_cero = 0 and
               t.d_plazo_dep_ini <= SYSDATE and
               t.d_fec_sal_cero > SYSDATE -1 ))
           and (t.vid_recibo = q.vid_recibo and t.s_status = 'P')
           and (to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') =
               to_char(r.did_fecha, 'dd/mm/yyyy'))
           and (t.iid_almacen = a.iid_almacen)
           and (t.iid_num_cliente = s.iid_num_cliente)
           and (q.vid_num_parte = u.vid_num_parte and
               u.v_parte_alternativa like '%ZAFRA%')
           and q.iid_um = v.iid_ume
           and s.iid_num_cliente <> 1261
           and ((t.vid_certificado is not null AND t.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     or (t.vid_certificado is null and t.i_administrativo = 1))
           union all
           select CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'N') OR r.vid_certificado IS NULL THEN (q.c_peso_total * -1)
               END as CANTIDADN,
               CASE WHEN (SUBSTR(r.vid_certificado, -1, 1) = 'S') AND r.vid_certificado IS NOT NULL THEN (q.c_peso_total * -1)
               END AS CANTIDADS
           from op_in_ord_salida      t,
               op_in_ord_salida_det  q,
               op_in_recibo_deposito r,
               op_ce_tipo_cambio     s,
               almacen               a,
               cliente               c,
               op_in_partes          u,
               co_ume                v
           where t.vid_ord_sal = q.vid_ord_sal
           and t.v_status = 'P'
           AND T.D_FECHA_REG <= SYSDATE
           and (t.vid_recibo = r.vid_recibo and
               ((r.i_sal_cero = 1 and
               r.d_plazo_dep_ini <= SYSDATE ) or
               (r.i_sal_cero = 0 and
               r.d_plazo_dep_ini <= SYSDATE and
               r.d_fec_sal_cero > SYSDATE -1)))
           and (to_char(r.d_plazo_dep_ini, 'dd/mm/yyyy') =
               to_char(s.did_fecha, 'dd/mm/yyyy'))
           and (t.iid_almacen = a.iid_almacen)
           and (t.iid_num_cliente = c.iid_num_cliente)
           and (q.vid_num_parte = u.vid_num_parte and
               u.v_parte_alternativa like '%ZAFRA%')
           and q.iid_um = v.iid_ume
           And c.iid_num_cliente <> 1261
           and ((r.vid_certificado is not null AND r.vid_certificado NOT IN (SELECT S.VID_CERTIFICADO FROM op_in_recibo_deposito S WHERE S.IID_ALMACEN = 1468 AND S.VID_CERTIFICADO LIKE '%-N%' ))
                     or (r.vid_certificado is null and r.i_administrativo = 1)))";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}
/**/
public function grafica_semana_hab_directas($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
				      		$sql = "SELECT T.NO_SEMANA, (SELECT SUM(Y.VALOR_TOTAL)
                  										 FROM op_meses_graficas_azucar y
                                       WHERE to_char(y.fecha_reg, 'yyyy') =  '".$anio."'
                                             AND to_char(y.fecha_reg, 'ww') = t.no_semana) as valor
                   FROM OP_NO_SEMANAS T
                   ORDER BY T.NO_SEMANA";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}

public function media_historica($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio +1;
				      		$sql = "SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
                 FROM op_meses_graficas_azucar y
                WHERE to_char(y.fecha_reg, 'yyyy') = '2014'
                  AND to_char(y.fecha_reg, 'ww') <= t.no_semana) +
	              (SELECT SUM(Y.VALOR_TOTAL)
	                 FROM op_meses_graficas_azucar y
	                WHERE to_char(y.fecha_reg, 'yyyy') = '2016'
	                  AND to_char(y.fecha_reg, 'ww') <= t.no_semana))/ 2,
	             2) as VALOR
  				 			FROM OP_NO_SEMANAS T
								ORDER BY T.NO_SEMANA ";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}

public function media_historica2($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio +1;
				      		$sql = "SELECT ROUND(((SELECT SUM(Y.VALOR_TOTAL)
                 FROM op_meses_graficas_azucar y
                WHERE to_char(y.fecha_reg, 'yyyy') = '2014'
                  AND to_char(y.fecha_reg, 'ww') = t.no_semana) +
	              (SELECT SUM(Y.VALOR_TOTAL)
	                 FROM op_meses_graficas_azucar y
	                WHERE to_char(y.fecha_reg, 'yyyy') = '2016'
	                  AND to_char(y.fecha_reg, 'ww') = t.no_semana))/ 2,
	             2) as VALOR
  				 			FROM OP_NO_SEMANAS T
								ORDER BY T.NO_SEMANA ";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
				      				return $res_array;

}

public function media_historica2_2020($anio,$n_semana){
				      		$conn = conexion::conectar();
				      		$res_array = array();
				          //$anio = $anio -1;
									$anio2 = $anio -1;
				      		$sql = "SELECT ROUND((NVL((SELECT SUM(Y.VALOR_TOTAL)
                 FROM op_meses_graficas_azucar y
                WHERE to_char(y.fecha_reg, 'yyyy') = '$anio'
                  AND TO_CHAR(Y.FECHA_REG, 'ww') = 1), 0) +
							             (SELECT SUM(Y.VALOR_TOTAL)
							                 FROM op_meses_graficas_azucar y
							                WHERE to_char(y.fecha_reg, 'yyyy') = '$anio2'
							                  AND to_char(y.fecha_reg, 'ww') >= 41)),
							             2) as valor
							  FROM OP_NO_SEMANAS T
                WHERE ROWNUM = 1 ";
				      				#echo $sql;
				              #echo $anio;
				      				$stid = oci_parse($conn, $sql);
				      				oci_execute($stid);

				      				while (($row = oci_fetch_assoc($stid)) != false)
				      				{
				      					$res_array[]= $row;
				      				}

				      				#echo $sql;
				      				oci_free_statement($stid);
				      				oci_close($conn);

				      			//	echo $sql;
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

	/*++++++++++++++++++++++++ SQL FILTROS ++++++++++++++++++++++++*/
public function filtros($option,$depto)
	{
		$conn = conexion::conectar();
		$res_array = array();

		switch ($option) {
			case '1':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ";
				break;
			case '2':
				$sql = "SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep";
				break;
			case '3':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
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
function validateDate($date, $format = 'd/m/Y'){
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}



}
