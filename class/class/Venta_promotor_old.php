<?php
/**
* © Argo Almacenadora ®
* Fecha: 05/06/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Presupuesto vs Venta Promotor
* Version --
*/
include_once '../libs/conOra.php'; 

class VentaPromotor
{
	
	// ************************** INICIA FUNCION PARA GENERAR LA GRAFICA DE VENTA **************************  //

	function graficaVenta($d17_pro_pla,$d17_anio,$d17_promotor,$d17_plaza)
	{
		$conn = conexion::conectar();

		$res_array = array();  

		$curs = oci_new_cursor($conn);/**/
		
		$curs = oci_new_cursor($conn);

		$stid = oci_parse($conn, "begin PCK_DASHBOARD.CUR_VENTA_VS_PRESUPUESTO(:n_tipo,:n_anio,:v_id_promotor,:n_id_plaza,:cur_posicion); end;");
		oci_bind_by_name($stid, ':n_tipo', $d17_pro_pla);
		oci_bind_by_name($stid, ':n_anio', $d17_anio);
		oci_bind_by_name($stid, ':v_id_promotor', $d17_promotor);
		oci_bind_by_name($stid, ':n_id_plaza', $d17_plaza);
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

	// ************************** TERMINA FUNCION PARA GENERAR LA GRAFICA DE VENTA **************************  //

	// ************************** INICIA FUNCION PARA TABLA DE PRESUPUESTO **************************  //

	function tablaPresupuesto($d17_pro_pla,$dash17_anio)
	{
		$conn = conexion::conectar(); 
		$res_array = array();  

		$sql = "SELECT DISTINCT pre.id_promotor, pro.iid_empleado, pre.iid_plaza, DECODE(pro.v_nombre,null,pla.v_razon_social,pro.v_nombre) v_nombre, pro.v_apellido_pat, pro.v_apellido_mat
				,NVL (pre.n_valor_mes1, 0) n_valor_mes1,NVL (pre.n_valor_mes2, 0) n_valor_mes2,NVL (pre.n_valor_mes3, 0) n_valor_mes3,NVL (pre.n_valor_mes4, 0) n_valor_mes4,NVL (pre.n_valor_mes5, 0) n_valor_mes5,NVL (pre.n_valor_mes6, 0) n_valor_mes6,NVL (pre.n_valor_mes7, 0) n_valor_mes7,NVL (pre.n_valor_mes8, 0) n_valor_mes8,NVL (pre.n_valor_mes9, 0) n_valor_mes9,NVL (pre.n_valor_mes10, 0) n_valor_mes10,NVL (pre.n_valor_mes11, 0) n_valor_mes11,NVL (pre.n_valor_mes12, 0) n_valor_mes12
				,pre.n_valor_anio
				FROM ad_cxc_movtos mov
				LEFT JOIN co_promotor_fac_vs_pre pre ON pre.n_valor_anio = to_char(mov.d_fecha_movto, 'yyyy')
				LEFT JOIN co_promotor pro ON pro.iid_promotor = pre.id_promotor
        		LEFT JOIN plaza pla ON pla.iid_plaza = pre.iid_plaza
				WHERE to_char(mov.d_fecha_movto, 'yyyy') = $dash17_anio AND pre.n_tipo = $d17_pro_pla";

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

	// ************************** INICIA FUNCION PARA ANIO MOVTO **************************  //

	function anioMov()
	{
		$conn = conexion::conectar(); 
		$res_array = array();  

		$sql = "SELECT DISTINCT p.n_valor_anio AS anio
				FROM co_promotor_fac_vs_pre p
				ORDER BY anio DESC";

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

	// ************************** TERMINA FUNCION PARA ANIO MOVTO **************************  //

	// ************************** INICIA FUNCION PARA SELECT PROMOTOR **************************  //

	function selectPromotor($anio,$d17_pro_pla)
	{
		$conn = conexion::conectar(); 
		$res_array = array();

		$sql = "SELECT vta.id_promotor, pro.iid_empleado, NVL(pro.v_nombre,pla.v_razon_social) v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, vta.iid_plaza, vta.n_tipo
				FROM co_promotor_fac_vs_pre vta
				LEFT JOIN co_promotor pro ON pro.iid_promotor = vta.id_promotor
        		LEFT JOIN plaza pla ON pla.iid_plaza = vta.iid_plaza
				WHERE vta.n_tipo = $d17_pro_pla AND vta.n_valor_anio = $anio ORDER BY v_nombre";

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

	// ************************** TERMINA FUNCION PARA SELECT PROMOTOR **************************  //


	// ************************** INICIA FUNCION PARA SELECT PLAZA **************************  //

	function selectPlaza()
	{
		$conn = conexion::conectar(); 
		$res_array = array();  

		$sql = "(SELECT pla.iid_plaza, pla.v_razon_social, pla.v_siglas
				FROM co_promotor_fac_vs_pre vta
				INNER JOIN co_promotor pro ON pro.iid_promotor = vta.id_promotor
				INNER JOIN plaza pla ON pla.iid_plaza = pro.iid_plaza)
				UNION
				(SELECT pla.iid_plaza, pla.v_razon_social, pla.v_siglas
				FROM co_promotor pro 
				INNER JOIN plaza pla ON pla.iid_plaza = pro.iid_plaza 
				WHERE pro.iid_promotor in (173,187,196,189,184,195))
				ORDER BY v_razon_social  ";	

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

	// ************************** TERMINA FUNCION PARA SELECT PLAZA **************************  //

	// ************************** INICIA FUNCION PARA TABLA DE PRESUPUESTO VS VENTA REAL **************************  //

	function tablaPresupuestoVta($d17_pro_pla,$d17_anio,$d17_promotor,$d17_plaza)
	{
		$conn = conexion::conectar(); 
		$res_array = array(); 

		if ( $d17_pro_pla == 1 ){
			$andTipo = " AND pre.id_promotor = $d17_promotor ";
		}else{
			if ( $d17_plaza == 'ALL' ){
				$andTipo = "  ";
			}else{
				$andTipo = " AND pre.iid_plaza = $d17_plaza ";	
			}
			
		}

	 

		$sql = "SELECT DISTINCT pre.id_promotor, pro.iid_empleado, pre.iid_plaza, DECODE(pro.v_nombre,null,pla.v_razon_social,pro.v_nombre) v_nombre, pro.v_apellido_pat, pro.v_apellido_mat
				,NVL (pre.n_valor_mes1, 0) n_valor_mes1,NVL (pre.n_valor_mes2, 0) n_valor_mes2,NVL (pre.n_valor_mes3, 0) n_valor_mes3,NVL (pre.n_valor_mes4, 0) n_valor_mes4,NVL (pre.n_valor_mes5, 0) n_valor_mes5,NVL (pre.n_valor_mes6, 0) n_valor_mes6,NVL (pre.n_valor_mes7, 0) n_valor_mes7,NVL (pre.n_valor_mes8, 0) n_valor_mes8,NVL (pre.n_valor_mes9, 0) n_valor_mes9,NVL (pre.n_valor_mes10, 0) n_valor_mes10,NVL (pre.n_valor_mes11, 0) n_valor_mes11,NVL (pre.n_valor_mes12, 0) n_valor_mes12
				,pre.n_valor_anio
				FROM ad_cxc_movtos mov
				LEFT JOIN co_promotor_fac_vs_pre pre ON pre.n_valor_anio = to_char(mov.d_fecha_movto, 'yyyy')
				LEFT JOIN co_promotor pro ON pro.iid_promotor = pre.id_promotor
        		LEFT JOIN plaza pla ON pla.iid_plaza = pre.iid_plaza
				WHERE to_char(mov.d_fecha_movto, 'yyyy') = $d17_anio ".$andTipo." AND pre.n_tipo = $d17_pro_pla ";

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

	// ************************** TERMINA FUNCION PARA TABLA DE PRESUPUESTO VS VENTA REAL **************************  //



	// ************************** INICIA FUNCION PARA TABLA DETALLE DE FACTURADO **************************  //

	function tablaDetFacturado($d17_pro_pla,$det_anio,$det_idPromotor,$det_idPlaza,$det_mes)
	{
		$obj = new VentaPromotor();
		$conn = conexion::conectar(); 
		$res_array = array(); 

		$andPromotor = "";
		$andPlaza = "";
		$join = " INNER ";
		$sql_res = "";
		$sql_res1 = "";
		$sql_res2 = "";
		if ( $d17_pro_pla == 1){
			if ( $det_idPromotor == "ALL" ){
				$andPromotor = " "; 
			}else{
				if ( $det_idPromotor == 0 ){
					$join = ' LEFT ';
					$andPromotor = " AND pro.iid_promotor IN (173,187,196,189,184,195)  "; 
				}else{
					$andPromotor = " AND pro.iid_promotor = ".$det_idPromotor."  "; 
					$join = ' INNER ';
				}
			}

			if ( $det_idPlaza == "ALL" ){
				$andPlaza = " "; 
			}else{
				$andPlaza = " AND pla.v_razon_social = '".$det_idPlaza."'  "; 
			}



		$sql_res1 = "( SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes
				,NVL (
				( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_cargo/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ),2), 2, round(mov.c_tipo_cambio * mov.n_monto_cargo / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )-
				( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_abono/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ) ,2), 2, round(mov.c_tipo_cambio * mov.n_monto_abono / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )
				,0) AS facturado_total
				FROM ad_cxc_movtos mov
				INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio
				INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				$join JOIN co_promotor_fac_vs_pre vta ON vta.id_promotor = pro.iid_promotor AND to_char(mov.d_fecha_movto, 'yyyy') = vta.n_valor_anio
				INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza
				WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
				AND al.s_tipo_almacen in (2,6) AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE'  
				AND to_char(mov.d_fecha_movto, 'yyyy') = ".$det_anio." AND to_char(mov.d_fecha_movto,'mm') = '".$det_mes."'  
				".$andPromotor.$andPlaza."   
				GROUP BY cli.v_razon_social,fac.iid_num_cliente, al.iid_almacen, al.v_nombre,pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm') 
				)";

		$sql_res2 = "(SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes
	            ,NVL (
	            ( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_cargo/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ),2), 2, round(mov.c_tipo_cambio * mov.n_monto_cargo / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )-
	            ( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_abono/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ) ,2), 2, round(mov.c_tipo_cambio * mov.n_monto_abono / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )
	            ,0) AS facturado_total
	            FROM ad_cxc_movtos mov
	            INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio
	            INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
	            INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
	            LEFT JOIN co_promotor_fac_vs_pre vta ON vta.id_promotor = pro.iid_promotor/*add*/ AND to_char(mov.d_fecha_movto, 'yyyy') = vta.n_valor_anio
	            INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen/*add*/
	            INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza  
	            WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
	            AND to_char(mov.d_fecha_movto, 'yyyy') = ".$det_anio."
	            AND to_char(mov.d_fecha_movto,'mm') = '".$det_mes."'  AND pro.iid_promotor IN (173,187,196,189,184,195)
	            AND al.s_tipo_almacen in (2,6) AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE' /*add*/
	            GROUP BY fac.iid_num_cliente, cli.v_razon_social, al.iid_almacen, al.v_nombre, pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm' ) 
        		) ";

        		if ( $det_idPlaza == "ALL" AND $det_idPromotor == "ALL"  ){
					$sql_res = $sql_res1." UNION ".$sql_res2;
				}else{
					$sql_res = $sql_res1;
				}

		}else{



			if ( $det_idPlaza == "ALL" ){

				$tipo2P = $obj->plazaTipo2($det_anio);
				$plaza = implode(",", $tipo2P);

				$andPlaza = " AND fac.iid_plaza in (".$plaza.") "; 
			}else{
				$andPlaza = " AND fac.iid_plaza = '".$det_idPlaza."'  "; 
			}

			$sql_res = " (SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes
				,NVL (
				( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_cargo/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ),2), 2, round(mov.c_tipo_cambio * mov.n_monto_cargo / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )-
				( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_abono/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ) ,2), 2, round(mov.c_tipo_cambio * mov.n_monto_abono / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )
				,0) AS facturado_total
				FROM ad_cxc_movtos mov
				INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio
				INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				INNER JOIN co_promotor_fac_vs_pre vta ON vta.id_promotor = pro.iid_promotor AND to_char(mov.d_fecha_movto, 'yyyy') = vta.n_valor_anio
				INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza
				WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
				AND al.s_tipo_almacen in (2,6) AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE'  
				AND to_char(mov.d_fecha_movto, 'yyyy') = ".$det_anio." AND to_char(mov.d_fecha_movto,'mm') = '".$det_mes."' ".$andPlaza."  
				GROUP BY cli.v_razon_social,fac.iid_num_cliente, al.iid_almacen, al.v_nombre,pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm') 
				 )
				UNION
				(
				SELECT fac.iid_num_cliente, cli.v_razon_social AS cliente, al.iid_almacen, al.v_nombre AS almacen, pla.iid_plaza, pla.v_razon_social AS plaza, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat,  to_char(mov.d_fecha_movto,'yyyy') AS anio ,to_char(mov.d_fecha_movto,'mm') as mes
	            ,NVL (
	            ( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_cargo/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ),2), 2, round(mov.c_tipo_cambio * mov.n_monto_cargo / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )-
	            ( NVL (sum ( decode(fac.iid_moneda, 1, round(mov.n_monto_abono/(1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa) ) ,2), 2, round(mov.c_tipo_cambio * mov.n_monto_abono / (1+ decode(fac.iva_tasa, .01, 0, fac.iva_tasa)), 2)) ) ,0) )
	            ,0) AS facturado_total
	            FROM ad_cxc_movtos mov
	            INNER JOIN ad_fa_factura fac ON fac.iid_plaza = mov.iid_plaza AND fac.iid_folio = mov.iid_folio
	            INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
	            INNER JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
	            LEFT JOIN co_promotor_fac_vs_pre vta ON vta.id_promotor = pro.iid_promotor/*add*/ AND to_char(mov.d_fecha_movto, 'yyyy') = vta.n_valor_anio
	            INNER JOIN almacen al ON al.iid_almacen = fac.iid_almacen/*add*/
	            INNER JOIN plaza pla On pla.iid_plaza = fac.iid_plaza  
	            WHERE fac.status = 7 AND mov.n_status = 2 AND mov.n_tipo_movto IN (1,3,4)
	            AND to_char(mov.d_fecha_movto, 'yyyy') = ".$det_anio."
	            AND to_char(mov.d_fecha_movto,'mm') = '".$det_mes."' AND pro.iid_promotor IN (173,187,196,189,184,195) ".$andPlaza."
	            AND al.s_tipo_almacen in (2,6) AND fac.observacion <> 'GASTOS NOTARIALES POR NOTIFICACION DE REMATE' /*add*/
	            GROUP BY fac.iid_num_cliente, cli.v_razon_social, al.iid_almacen, al.v_nombre, pla.iid_plaza, pla.v_razon_social, pro.iid_promotor, pro.iid_empleado, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, to_char(mov.d_fecha_movto,'yyyy') ,to_char(mov.d_fecha_movto,'mm' ) ) ";
		}

		$sql = $sql_res;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		//return $res_array;
		return  json_encode($res_array);

	}

	// ************************** TERMINA FUNCION PARA TABLA DETALLE DE FACTURADO **************************  //

	public function plazaTipo2($anio)
	{
		$conn = conexion::conectar(); 
		$res_array = array(); 

		$sql = "SELECT p.iid_plaza FROM co_promotor_fac_vs_pre p  WHERE p.n_tipo = 2 AND p.n_valor_anio = $anio";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_array[]= $row["IID_PLAZA"];
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
	 
}

