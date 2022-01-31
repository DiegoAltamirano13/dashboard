<?php 
/**
* © Argo Almacenadora ®
* Fecha: 29/09/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Saldos clientes
* Version --
*/
include_once '../libs/conOra.php';

class Filtros
{
	public $and_saldo_dias;
	public $and_saldo_plaza;
	
	function __construct($saldo_dias,$saldo_cliente_plaza)
	{
		switch ($saldo_dias) {
			case 1:
				$this->and_saldo_dias = " AND (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) <= 30 and  (TO_DATE(TO_CHAR(sysdate, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) > 0 ";
				break;
			case 2:
				$this->and_saldo_dias = " AND (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) <= 60 AND  (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) > 30 ";
				break;
			case 3:
				$this->and_saldo_dias = " AND (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) <= 90 AND  (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) > 60 ";
				break;
			default:
				$this->and_saldo_dias = " AND (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY'))-  fac.fec_ven_factura) > 90 ";
				break;
		}

		// filtrado de plaza si selecciona la plaza
		if ( $saldo_cliente_plaza == true ){
			$this->and_saldo_plaza = " AND pla.v_razon_social = '".$saldo_cliente_plaza."' ";
		}else{
			$this->and_saldo_plaza = "";

		}
	}
}

 
class Saldo_cliente_detalle
{
	// ---------------------- FUNCION PARA GRAFICA SALDOS PLAZA DETALLE ---------------------- //
	public function saldos_plaza_det($plaza,$cliente)
	{
		if($plaza == true){
			$select_plaza = " fac.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, ";
			$and_plaza = " AND pla.v_razon_social = '".$plaza."' ";
			$group_plaza = " fac.iid_plaza, pla.v_razon_social ";
		}

		if($cliente == true){
			$select_cliente = " fac.iid_num_cliente AS id_cliente, cli.v_razon_social AS cliente, ";
			$and_cliente = " AND fac.iid_num_cliente = ".$cliente." ";
			$group_cliente = " fac.iid_num_cliente, cli.v_razon_social ";
		}

		if($plaza == true && $cliente == true){
			$coma = ",";
		}

		

		$conn = conexion::conectar();

		$sql = " SELECT ".$select_plaza.$select_cliente."  
				sum(case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end) AS saldo,
				sum(case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end) - sum(case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end) AS monto
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 0
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_no_vencido
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 0
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 0
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_no_vencido
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 15 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 0 
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_1_15
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 15 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 0 
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 15 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 0 
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_1_15  
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 30 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 15 
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_16_30
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 30 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 15 
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 30 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 15
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_16_30
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 60 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 30 
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_mas_31
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 60 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 30 
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 60 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 30
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_mas_31
		       ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 90 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 60 
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_mas_61
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 90 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 60 
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 90 AND  (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) > 60
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_mas_61
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) >90 
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_mas_91
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) >90
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) >90
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_mas_91
				FROM ad_cxc_movtos mov  
				INNER JOIN ad_fa_factura fac ON fac.iid_folio = mov.iid_folio AND fac.iid_plaza = mov.iid_plaza
				INNER JOIN plaza pla ON pla.iid_plaza = fac.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = fac.iid_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				LEFT JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				WHERE ( (mov.d_fecha_movto <= SYSDATE ) 
				AND (fac.status = 7)
				AND (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL)
				AND mov.n_status = 2 )
				".$and_plaza.$and_cliente."
				GROUP BY ".$group_plaza.$coma.$group_cliente;

		$stid =  oci_parse($conn, $sql);
		oci_execute($stid); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_saldos_plaza_det[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_saldos_plaza_det;
	}
}

class Saldos_clientes extends Filtros
{  
// ---------------------- FUNCION PARA GRAFICA SALDOS PLAZA ---------------------- //
	public function saldos_plaza($saldo_cliente_plaza)
	{ 
		$and_saldo_dias = $this->and_saldo_dias;

		//grafica por almacen
		if ($saldo_cliente_plaza == true){
			$select_saldo_plaza_almacen = " fac.iid_almacen AS id_almacen, alm.v_nombre AS almacen, ";
			$and_saldo_plaza_almacen = " AND pla.v_razon_social = '".$saldo_cliente_plaza."' ";
			$group_by_saldo_plaza_almacen = " fac.iid_almacen, alm.v_nombre, ";
		}else{
			$select_saldo_plaza_almacen = " ";
			$and_saldo_plaza_almacen = " ";
			$group_by_saldo_plaza_almacen = " ";
		}

		$conn = conexion::conectar();

		$sql =" SELECT ".$select_saldo_plaza_almacen." fac.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, 
				sum(case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end) AS saldo,
				sum(case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end) - sum(case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end) AS monto, 
				decode(fac.iid_plaza, 3,'#FBEC5D',
				4,'#f86a60',
				5,'#32DADD',
				6,'#5AB1EF',
				7,'#FFB980',
				8,'#D87A80',
				17,'#F5C9C4',
				18,'#5D8AA8',
				21,'#5A6E83',
				23,'#B6A2DE') AS color
				,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 0
		        then (/**/case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end/**/) 
		        else (0) end) AS saldo_no_vencido
		        ,sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 0
		        then (/**/case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end/**/) 
		        else (0) end) - sum(case when (to_date(to_char(sysdate, 'dd/month/yyyy')) - fac.fec_ven_factura) <= 0
		        then (/**/ case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end /**/) 
		        else (0) end) AS monto_no_vencido
				FROM ad_cxc_movtos mov  
				INNER JOIN ad_fa_factura fac ON fac.iid_folio = mov.iid_folio AND fac.iid_plaza = mov.iid_plaza
				INNER JOIN plaza pla ON pla.iid_plaza = fac.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = fac.iid_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				LEFT JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				WHERE ( (mov.d_fecha_movto <= SYSDATE ) 
				AND (fac.status = 7)
				AND (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL)
				AND mov.n_status = 2 )
				".$and_saldo_dias.$and_saldo_plaza_almacen."
				GROUP BY ".$group_by_saldo_plaza_almacen." fac.iid_plaza, pla.v_razon_social
				ORDER BY saldo DESC ";

		$stid =  oci_parse($conn, $sql);
		oci_execute($stid); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_saldos_plaza[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_saldos_plaza;
	}
// ---------------------- FUNCION PARA WIDGETS CLIENTES CON SALDO ---------------------- //
	public function widgets_saldos_clientes()
	{ 
		$conn = conexion::conectar();

		$and_saldo_dias = $this->and_saldo_dias;
		$and_saldo_plaza = $this->and_saldo_plaza;  

		$sql =" SELECT COUNT(DISTINCT(fac.iid_num_cliente)) AS clientes_saldo
				FROM ad_cxc_movtos mov  
				INNER JOIN ad_fa_factura fac ON fac.iid_folio = mov.iid_folio AND fac.iid_plaza = mov.iid_plaza
				INNER JOIN plaza pla ON pla.iid_plaza = fac.iid_plaza
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				LEFT JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				WHERE ( (mov.d_fecha_movto <= SYSDATE ) 
				AND (fac.status = 7)
				AND (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL)
				AND mov.n_status = 2 ) 
				".$and_saldo_dias.$and_saldo_plaza."
				";

		$stid =  oci_parse($conn, $sql);
		oci_execute($stid); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_widgets_saldos_clientes[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_widgets_saldos_clientes;
	}
// ---------------------- FUNCION PARA WIDGETS SALDO ---------------------- //
	public function widgets_saldos()
	{ 
		$and_saldo_dias = $this->and_saldo_dias;
		$and_saldo_plaza = $this->and_saldo_plaza; 

		$conn = conexion::conectar();

		$sql =" SELECT sum(case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end) AS saldo_monto,
				sum(case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end) - sum(case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end) AS monto
				FROM ad_cxc_movtos mov  
				INNER JOIN ad_fa_factura fac ON fac.iid_folio = mov.iid_folio AND fac.iid_plaza = mov.iid_plaza
				INNER JOIN plaza pla ON pla.iid_plaza = fac.iid_plaza
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				LEFT JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				WHERE ( (mov.d_fecha_movto <= SYSDATE ) 
				AND (fac.status = 7)
				AND (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL)
				AND mov.n_status = 2 )  
				".$and_saldo_dias.$and_saldo_plaza."
				";

		$stid =  oci_parse($conn, $sql);
		oci_execute($stid); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_widgets_saldos[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_widgets_saldos;
	}
// ---------------------- FUNCION PARA WIDGETS VALOR DE MERCANCIA ---------------------- //
	public function widgets_v_mercancia()
	{ 
		$and_saldo_dias = $this->and_saldo_dias;

		$conn = conexion::conectar();

		$sql =" SELECT SUM(d.valor_mn) AS valor_mer
				FROM vista_cmop_2 d 
				WHERE d.i_sal_cero = 1 AND d.rd_admin=0 ";

		$stid =  oci_parse($conn, $sql);
		oci_execute($stid); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_widgets_v_mercancia[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_widgets_v_mercancia;
	}
// ---------------------- FUNCION PARA TABLA SALDOS CLIENTES ---------------------- //
	public function tabla_saldos_clientes($saldo_cliente_plaza,$importe_filtro_resumen,$group,$cliente)
	{ 
		$and_saldo_dias = $this->and_saldo_dias;

		if($saldo_cliente_plaza == true){
			$and_plaza = " AND pla.v_razon_social = '".$saldo_cliente_plaza."' ";
			//$select_plaza = " fac.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, ";
			//$group_plaza = " fac.iid_plaza, pla.v_razon_social, ";
		}

		if($importe_filtro_resumen == true){
			$selec_resumen_ini = " SELECT * FROM ( ";
			$selec_resumen_fin = " )WHERE saldo > '".$importe_filtro_resumen."' ORDER BY saldo DESC ";
		}

		if($group == true ){
			$select_plaza = " fac.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, ";
			$group_plaza = " fac.iid_plaza, pla.v_razon_social, ";
		}

		if($cliente == true ){
			$and_cliente = " AND fac.iid_num_cliente = ".$cliente; 
		}

		$conn = conexion::conectar();

		$sql = $selec_resumen_ini." SELECT ".$select_plaza." fac.iid_num_cliente AS id_cliente, cli.v_razon_social AS cliente, sum(case when (fac.iid_moneda = 2) then (fac.total * fac.c_tpo_cambio) else (fac.total) end) AS saldo,
				sum(case when (fac.iid_moneda = 2) then (mov.n_monto_cargo * fac.c_tpo_cambio) else (mov.n_monto_cargo) end) - sum(case when (fac.iid_moneda = 2) then (mov.n_monto_abono * fac.c_tpo_cambio) else (mov.n_monto_abono) end) AS monto,
				pro.v_nombre AS nom_pro, pro.v_apellido_pat AS ape_pat_pro, pro.v_apellido_mat AS ape_mat_pro, sum(distinct( fac.IID_REGIMEN ) ) AS regimen ,rem.n_status AS status_remate, rem.n_valor_impuestos AS impuestos
				FROM ad_cxc_movtos mov  
				INNER JOIN ad_fa_factura fac ON fac.iid_folio = mov.iid_folio AND fac.iid_plaza = mov.iid_plaza
				INNER JOIN plaza pla ON pla.iid_plaza = fac.iid_plaza
				INNER JOIN cliente cli ON cli.iid_num_cliente = fac.iid_num_cliente
				LEFT JOIN co_promotor pro ON pro.iid_promotor = cli.iid_promotor
				LEFT JOIN ad_cxc_remates_mercancias rem ON rem.iid_num_cliente = fac.iid_num_cliente and rem.iid_plaza = fac.iid_plaza
				WHERE ( (mov.d_fecha_movto <= SYSDATE ) 
				AND (fac.status = 7)
				AND (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL)
				AND mov.n_status = 2 )
				".$and_saldo_dias.$and_plaza.$and_cliente."
				GROUP BY ".$group_plaza." fac.iid_num_cliente, cli.v_razon_social, pro.v_nombre, pro.v_apellido_pat, pro.v_apellido_mat, rem.n_status, rem.n_valor_impuestos
				ORDER BY saldo desc ".$selec_resumen_fin ;

		$stid =  oci_parse($conn, $sql);
		oci_execute($stid); 

		while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_tabla_saldos_clientes[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_tabla_saldos_clientes;
	}
// ---------------------- FUNCION PARA VALOR DE MERCANCIA CLIENTES ---------------------- //
	public function valor_mercancia($id_plaza,$id_cliente)
	{  

		$conn = conexion::conectar();

		$sql =" SELECT SUM(d.valor_mn) AS valor_mercancia
				FROM vista_cmop_2 d 
				WHERE d.i_sal_cero = 1 AND d.rd_admin=0
				AND d.iid_plaza = ".$id_plaza."
				AND d.iid_num_cliente = ".$id_cliente." "; 

		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        $res_valor_mercancia = oci_fetch_array($stid, OCI_BOTH);

        oci_free_statement($stid);
		oci_close($conn);
        return $res_valor_mercancia[0];

	}		
// ---------------------- FUNCION PARA SACAR ID_FOLIO TABLA AD_FA_FACTURA ---------------------- //
	public function ad_fa_factura($id_plaza,$id_cliente)
	{  

		$conn = conexion::conectar();

		$sql =" SELECT  adf.iid_folio AS id_folio
				FROM ad_fa_factura adf
				WHERE adf.iid_plaza = ".$id_plaza." AND adf.iid_num_cliente = ".$id_cliente." AND adf.n_status_pago < 3 AND adf.status = 7";
				
		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_ad_fa_factura[]= $row;
		}

        oci_free_statement($stid);
		oci_close($conn);
		return $res_ad_fa_factura;
	}
// ---------------------- FUNCION PARA SACAR NUM_CD N TABLA AD_FA_CER_FACTURA ---------------------- //
	public function ad_fa_cer_factura_n($iid_plaza,$iid_folio)
	{ 
		 
		$conn = conexion::conectar();

		$sql ="	SELECT DISTINCT(REPLACE(t.num_cd,'-N','')) AS num_cd_n
				FROM ad_fa_cer_factura t
				WHERE t.iid_plaza = ".$iid_plaza." AND t.iid_folio IN (".$iid_folio.") AND t.num_cd like '%-N%' ";
				
		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_ad_fa_cer_factura_n[]= $row;
		}

        oci_free_statement($stid);
		oci_close($conn);
		return $res_ad_fa_cer_factura_n;
	}
// ---------------------- FUNCION PARA SACAR NUM_CD S TABLA AD_FA_CER_FACTURA ---------------------- //
	public function ad_fa_cer_factura_s($iid_plaza,$iid_folio)
	{ 
		 
		$conn = conexion::conectar();

		$sql ="	SELECT DISTINCT(REPLACE(t.num_cd,'-S','')) AS num_cd_s
				FROM ad_fa_cer_factura t
				WHERE t.iid_plaza = ".$iid_plaza." AND t.iid_folio IN (".$iid_folio.") AND t.num_cd like '%-S%' ";
				
		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_ad_fa_cer_factura_s[]= $row;
		}

        oci_free_statement($stid);
		oci_close($conn);
		return $res_ad_fa_cer_factura_s;
	}	
// ---------------------- FUNCION PARA TIPO DE MERCANCIA N EN AD_CE_CERT_N_DET ---------------------- //
	public function ad_ce_cert_n_det($iid_num_cert_n)
	{ 
		 
		$conn = conexion::conectar();

		$sql ="	SELECT DISTINCT(t.v_nat_y_calid) AS mercancia
				FROM ad_ce_cert_n_det t
				WHERE t.iid_num_cert_n IN (".$iid_num_cert_n.") ";
				
		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_ad_ce_cert_n_det[]= $row;
		}

        oci_free_statement($stid);
		oci_close($conn);
		return $res_ad_ce_cert_n_det;
	}
// ---------------------- FUNCION PARA TIPO DE MERCANCIAS EN AD_CE_CERT_S_DET ---------------------- //
	public function ad_ce_cert_s_det($iid_num_cert_s)
	{ 
		 
		$conn = conexion::conectar();

		$sql ="	SELECT DISTINCT(t.v_nat_y_calid) AS mercancia
				FROM ad_ce_cert_s_det t
				WHERE t.iid_num_cert_s IN (".$iid_num_cert_s.") ";
				
		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_ad_ce_cert_s_det[]= $row;
		}

        oci_free_statement($stid);
		oci_close($conn);
		return $res_ad_ce_cert_s_det;
	}
// ---------------------- FUNCION PARA FECHA DE DEPOSITO EN OP_IN_RECIBO_DEPOSITO ---------------------- //
	public function op_in_recibo_deposito($iid_num_cliente)
	{ 
		 
		$conn = conexion::conectar();

		$sql ="	SELECT DISTINCT (FIRST_VALUE(t.vid_certificado) OVER (ORDER BY T.d_plazo_dep_ini))  AS certificado, FIRST_VALUE(to_char(t.d_plazo_dep_ini,'dd/mm/yyyy')) OVER (ORDER BY t.d_plazo_dep_ini) AS fecha_ini_cer
				FROM op_in_recibo_deposito t
				WHERE t.iid_num_cliente = '".$iid_num_cliente."' AND t.i_sal_cero = 1 ";
				
		$stid =  oci_parse($conn, $sql); 
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) 
		{
			$res_op_in_recibo_deposito[]= $row;
		}

        oci_free_statement($stid);
		oci_close($conn);
		return $res_op_in_recibo_deposito;
	}	

}

?>