<?php
/**
* © Argo Almacenadora ®
* Fecha: 05/06/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Presupuesto vs Venta Promotor
* Version --
*/
include_once '../libs/conOra.php';

class VentaAlmacen
{

	// ************************** SQL DINAMICO **************************  //

	function sql($sql)
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

	// ************************** /.SQL DINAMICO **************************  //



	// ************************** INICIA FUNCION PARA TABLA DE PRESUPUESTO **************************  //

	function tablaPresupuesto($pre,$fecha)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = " SELECT DISTINCT pre.id_promotor, pro.iid_empleado, pre.iid_plaza,
				DECODE(pro.v_nombre,null,pla.v_razon_social,pro.v_nombre) v_nombre, pro.v_apellido_pat,
				pro.v_apellido_mat ,NVL (pre.n_valor_mes1, 0) n_valor_mes1,NVL (pre.n_valor_mes2, 0) n_valor_mes2,
				NVL (pre.n_valor_mes3, 0) n_valor_mes3,NVL (pre.n_valor_mes4, 0) n_valor_mes4,NVL (pre.n_valor_mes5, 0) n_valor_mes5,
				NVL (pre.n_valor_mes6, 0) n_valor_mes6,NVL (pre.n_valor_mes7, 0) n_valor_mes7,NVL (pre.n_valor_mes8, 0) n_valor_mes8,
				NVL (pre.n_valor_mes9, 0) n_valor_mes9,NVL (pre.n_valor_mes10, 0) n_valor_mes10,NVL (pre.n_valor_mes11, 0) n_valor_mes11,
				NVL (pre.n_valor_mes12, 0) n_valor_mes12 ,pre.n_valor_anio
				FROM co_promotor_fac_vs_pre pre
				LEFT JOIN co_promotor pro ON pro.iid_promotor = pre.id_promotor
				LEFT JOIN plaza pla ON pla.iid_plaza = pre.iid_plaza
				WHERE pre.n_valor_anio = $fecha AND pre.n_tipo = $pre ";

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

	// ************************** TERMINA FUNCION PARA TABLA DE PRESUPUESTO **************************  //



	// ************************** WHERE PRESUPUESTO **************************  //

	function wherePresupuesto($pre,$fecha,$promotor,$plaza)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$andWhere = "";
		if ($pre == 3){
			if($plaza != "ALL"){$andWhere = " AND pre.iid_plaza = $plaza ";}
		}else{
			if($promotor != "ALL"){$andWhere = " AND pre.id_promotor = $promotor ";}
		}

		$sql = "SELECT  NVL (SUM(pre.n_valor_mes1), 0) n_valor_mes1,NVL (SUM(pre.n_valor_mes2), 0) n_valor_mes2,
				NVL (SUM(pre.n_valor_mes3), 0) n_valor_mes3,NVL (SUM(pre.n_valor_mes4), 0) n_valor_mes4,NVL (SUM(pre.n_valor_mes5), 0) n_valor_mes5,
				NVL (SUM(pre.n_valor_mes6), 0) n_valor_mes6,NVL (SUM(pre.n_valor_mes7), 0) n_valor_mes7,NVL (SUM(pre.n_valor_mes8), 0) n_valor_mes8,
				NVL (SUM(pre.n_valor_mes9), 0) n_valor_mes9,NVL (SUM(pre.n_valor_mes10), 0) n_valor_mes10,NVL (SUM(pre.n_valor_mes11), 0) n_valor_mes11,
				NVL (SUM(pre.n_valor_mes12), 0) n_valor_mes12
				FROM co_promotor_fac_vs_pre pre
				LEFT JOIN co_promotor pro ON pro.iid_promotor = pre.id_promotor
				LEFT JOIN plaza pla ON pla.iid_plaza = pre.iid_plaza
				WHERE pre.n_valor_anio = $fecha AND pre.n_tipo = $pre $andWhere ";

				#echo $sql;
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

	// ************************** /.WHERE PRESUPUESTO **************************  //



	// ************************** VENTA REAL **************************  //
	function ventaReal($pre,$fecha,$promotor,$plaza,$almacen)
	{
		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);
		$promotor = "ALL";
		//echo $pre.' '.$fecha.' '.$promotor.' '.$plaza;
		$stid = oci_parse($conn, "begin PCK_DASHBOARD.cur_vta_almacen(:n_tipo,:n_anio,:v_id_promotor,:n_id_plaza,:n_almacen,:cur_posicion); end;");
		oci_bind_by_name($stid, ':n_tipo', $pre); //3
		oci_bind_by_name($stid, ':n_anio', $fecha); //2019
		oci_bind_by_name($stid, ':v_id_promotor', $promotor); // ALL
		oci_bind_by_name($stid, ':n_id_plaza', $plaza); //ALL
		oci_bind_by_name($stid, ':n_almacen', $almacen); //ALL
		oci_bind_by_name($stid, ":cur_posicion", $curs, -1, OCI_B_CURSOR);
		oci_execute($stid);
		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		    $res_array[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		oci_close($conn);
		return $res_array;

	}
	// ************************** /.VENTA REAL **************************  //



	// ************************** INICIA FUNCION PARA TABLA DETALLE DE FACTURADO **************************  //

	function tablaDetFacturado($pre,$fecha,$promotor,$plaza,$mes,$almacen)
	{
		$obj = new VentaAlmacen();
		$conn = conexion::conectar();
		$res_array = array();

		$andPromotor = "";
		$andPlaza = "";
		$join = " INNER ";
		$sql_res = "";
		$sql_res1 = "";
		$sql_res2 = "";
		if ( $pre == 1){
			if ( $promotor == "ALL" ){
				$andPromotor = " ";
			}else{
				if ( $promotor == 0 ){
					$join = ' LEFT ';
					$andPromotor = " AND pro.iid_promotor IN (173, 172,187,196,189,184,195)  ";
				}else{
					$andPromotor = " AND pro.iid_promotor = ".$promotor."  ";
					$join = ' INNER ';
				}
			}

			if ( $plaza == "ALL" ){
				$andPlaza = " AND FAC.IID_PLAZA IN (SELECT VTA.IID_PLAZA FROM co_promotor_fac_vs_pre VTA WHERE VTA.N_TIPO = 3 AND VTA.N_VALOR_ANIO = $fecha)";
				//$andPlaza = " AND fac.iid_plaza in (3,4,5,6,7,8,17,18)";
			}else{
				$andPlaza = " AND pla.v_razon_social = '".$plaza."'  ";
			}

			if ($almacen =="ALL") {
				$and_alm = "";
			}
			else {
				$and_al = "AND AL.IID_ALMACEN = ".$almacen."";
			}


//AND fac.iid_almacen <> 43 AND fac.iid_almacen <> 1336
		$sql_res1 = "( SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes ,
					NVL((NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
																													 round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																													 round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06 )), 2)),
																							2, decode(fac.iva_ret, 0,
																													 round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																													 round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- 0.06)), 2))
														 )), 0))
						- (NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
																													 round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																													 round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- .06)), 2)),
																							2, decode(fac.iva_ret, 0 ,
																													 round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																													 round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06)), 2))
														 )), 0)), 0) AS factur
				FROM ad_cxc_movtos mov
				INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio AND fac.iid_almacen <> 1722
				INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				$join JOIN co_promotor_fac_vs_pre vta ON vta.id_promotor = pro.iid_promotor AND to_char(mov.d_fecha_movto, 'yyyy') = vta.n_valor_anio
				INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza
				WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
				AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE'
				AND to_char(mov.d_fecha_movto, 'yyyy') = ".$fecha." AND to_char(mov.d_fecha_movto,'mm') = '".$mes."'
				".$andPlaza."
				".$and_al."
				GROUP BY cli.v_razon_social,fac.iid_num_cliente, al.iid_almacen, al.v_nombre,pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm')
				)";
// AND fac.iid_almacen <> 43 AND fac.iid_almacen <> 1336
		$sql_res2 = "(SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes ,
								NVL((NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
																										 round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																										 round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06 )), 2)),
																				2, decode(fac.iva_ret, 0,
																										 round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																										 round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- 0.06)), 2))
											 )), 0))
							- (NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
																										 round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																										 round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- .06)), 2)),
																				2, decode(fac.iva_ret, 0 ,
																										 round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																										 round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06)), 2))
											 )), 0)), 0) AS facturado_total
	            FROM ad_cxc_movtos mov
	            INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio AND fac.iid_almacen <> 1722
	            INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
	            INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
	            LEFT JOIN co_promotor_fac_vs_pre vta ON vta.id_promotor = pro.iid_promotor/*add*/ AND to_char(mov.d_fecha_movto, 'yyyy') = vta.n_valor_anio
	            INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen/*add*/
	            INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza
	            WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
	            AND to_char(mov.d_fecha_movto, 'yyyy') = ".$fecha."
							".$and_al."
	            AND to_char(mov.d_fecha_movto,'mm') = '".$mes."'  AND pro.iid_promotor IN (173,172, 187,196,189,184,195)
	            AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE' /*add*/
	            GROUP BY fac.iid_num_cliente, cli.v_razon_social, al.iid_almacen, al.v_nombre, pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm' )
        		) ";


        		if ( $plaza == "ALL" AND $promotor == "ALL"  ){
								$sql_res = $sql_res1." UNION ".$sql_res2;
						}else{
								$sql_res = $sql_res1;
						}
						#echo $sql_res;
		}else{



			if ( $plaza == "ALL" ){
				$andPlaza = " AND fac.iid_plaza in (3,4,5,6,7,8,17,18)";
			}else{
				$andPlaza = " AND fac.iid_plaza = '".$plaza."'";
			}

			if ($almacen =="ALL") {
				$and_al = "";
			}
			else {
				$and_al = "AND AL.IID_ALMACEN = ".$almacen."";
			}
			// AND fac.iid_almacen <> 43 AND fac.iid_almacen <> 1336
			// AND fac.iid_almacen <> 43 AND fac.iid_almacen <> 1336
			$sql_res = " (SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes ,
			NVL((NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
																														 round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																														 round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06 )), 2)),
																								2, decode(fac.iva_ret, 0,
																														 round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																														 round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- 0.06)), 2))
															 )), 0))
							- (NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
																														 round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																														 round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- .06)), 2)),
																								2, decode(fac.iva_ret, 0 ,
																														 round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
																														 round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06)), 2))
															 )), 0)), 0) AS facturado_total
				FROM ad_cxc_movtos mov
				INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio AND fac.iid_almacen <> 1722
				INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza
				WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
				AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE'
				AND FAC.OBSERVACION <> 'VENTA DE 13,559 PIEZAS Y JUEGOS DE CALENTADORES SOLARES; EN EL ESTADO EN QUE SE ENCUENTRAN'
				AND to_char(mov.d_fecha_movto, 'yyyy') = ".$fecha."
				AND to_char(mov.d_fecha_movto,'mm') = '".$mes."' ".$andPlaza."
				".$and_al."
				GROUP BY cli.v_razon_social,fac.iid_num_cliente, al.iid_almacen, al.v_nombre,pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm')
				 )
				UNION
				(
				SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes ,
				NVL((NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
                                                               round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
                                                               round(mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06 )), 2)),
                                                  2, decode(fac.iva_ret, 0,
                                                               round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
                                                               round(mov.c_tipo_cambio * mov.n_monto_cargo / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- 0.06)), 2))
                                 )), 0))
                - (NVL(sum(decode(fac.iid_moneda, 1, decode(fac.iva_ret, 0,
                                                               round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
                                                               round(mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa- .06)), 2)),
                                                  2, decode(fac.iva_ret, 0 ,
                                                               round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2),
                                                               round(mov.c_tipo_cambio * mov.n_monto_abono / (1 + decode(fac.iva_tasa, .01, 0, fac.iva_tasa - .06)), 2))
                                 )), 0)), 0) AS facturado_total
	            FROM ad_cxc_movtos mov
	            INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio  AND fac.iid_almacen <> 1722
	            INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
	            INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
	            INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen
	            INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza
	            WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
	            AND to_char(mov.d_fecha_movto, 'yyyy') = ".$fecha."
	            AND to_char(mov.d_fecha_movto,'mm') = '".$mes."' ".$andPlaza."
							".$and_al."
	            AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE'
							AND FAC.OBSERVACION <> 'VENTA DE 13,559 PIEZAS Y JUEGOS DE CALENTADORES SOLARES; EN EL ESTADO EN QUE SE ENCUENTRAN'
	            GROUP BY fac.iid_num_cliente, cli.v_razon_social, al.iid_almacen, al.v_nombre, pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm' ) ) ";
		}

		#echo $sql_res;
		$sql = $sql_res;

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



	// ************************** HISTORIAL ACOMULADO DEL MES **************************  //
	function histMesConsol($pre,$fecha,$promotor,$plaza,$mes,$v_det)
	{
		switch ($mes){
			case 'Ene': $mes = '01'; break;
			case 'Feb': $mes = '02'; break;
			case 'Mar': $mes = '03'; break;
			case 'Abr': $mes = '04'; break;
			case 'May': $mes = '05'; break;
			case 'Jun': $mes = '06'; break;
			case 'Jul': $mes = '07'; break;
			case 'Ago': $mes = '08'; break;
			case 'Sep': $mes = '09'; break;
			case 'Oct': $mes = '10'; break;
			case 'Nov': $mes = '11'; break;
			case 'Dic': $mes = '12'; break;
		}
		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);

		$stid = oci_parse($conn, "begin PCK_DASHBOARD.PCK_HIST_VTA_CONSOL_ALMACEN(:n_tipo,:n_anio,:v_id_promotor,:n_id_plaza,:v_mes,:v_det,:cur_posicion); end;");
		$pre = 3;
		oci_bind_by_name($stid, ':n_tipo', $pre);
		oci_bind_by_name($stid, ':n_anio', $fecha);
		oci_bind_by_name($stid, ':v_id_promotor', $promotor);
		oci_bind_by_name($stid, ':n_id_plaza', $plaza);
		oci_bind_by_name($stid, ':v_mes', $mes);
		oci_bind_by_name($stid, ':v_det', $v_det);
		oci_bind_by_name($stid, ":cur_posicion", $curs, -1, OCI_B_CURSOR);
		oci_execute($stid);

		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		    $res_array[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		oci_close($conn);
		return $res_array;

	}
	// ************************** /.HISTORIAL ACOMULADO DEL MES **************************  //



	// ************************** HISTORIAL ACOMULADO DEL MES **************************  //
	function histMesClieNew($pre,$fecha,$promotor,$plaza,$mes,$v_det)
	{
		switch ($mes){
			case 'Ene': $mes = '01'; break;
			case 'Feb': $mes = '02'; break;
			case 'Mar': $mes = '03'; break;
			case 'Abr': $mes = '04'; break;
			case 'May': $mes = '05'; break;
			case 'Jun': $mes = '06'; break;
			case 'Jul': $mes = '07'; break;
			case 'Ago': $mes = '08'; break;
			case 'Sep': $mes = '09'; break;
			case 'Oct': $mes = '10'; break;
			case 'Nov': $mes = '11'; break;
			case 'Dic': $mes = '12'; break;
		}
		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);

		$stid = oci_parse($conn, "begin PCK_DASHBOARD.PCK_HIST_VTA_NEW_CLIE_ALMACEN(:n_tipo,:n_anio,:v_id_promotor,:n_id_plaza,:v_mes,:v_det,:cur_posicion); end;");
		$pre = 3;
		oci_bind_by_name($stid, ':n_tipo', $pre);
		oci_bind_by_name($stid, ':n_anio', $fecha);
		oci_bind_by_name($stid, ':v_id_promotor', $promotor);
		oci_bind_by_name($stid, ':n_id_plaza', $plaza);
		oci_bind_by_name($stid, ':v_mes', $mes);
		oci_bind_by_name($stid, ':v_det', $v_det);
		oci_bind_by_name($stid, ":cur_posicion", $curs, -1, OCI_B_CURSOR);
		oci_execute($stid);

		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		    $res_array[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		oci_close($conn);
		return $res_array;

	}
	// ************************** /.HISTORIAL ACOMULADO DEL MES **************************  //




}
