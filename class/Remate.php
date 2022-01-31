<?php
/**
* © Argo Almacenadora ®
* Fecha: 27/07/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Administracion Remates
* Version --
*/
include_once '../libs/conOra.php';

class Concatena
{
	public $and_plaza;
	public $and_almacen;
	public $and_id_remate;

	function __construct($remates_plaza,$remates_almacen,$id_remate)
	{
		if ($remates_plaza == true)
		{
			$this->and_plaza = " AND pla.v_razon_social = '".$remates_plaza."' ";
		}
		if ($remates_almacen == true)
		{
			$this->and_almacen = " AND alm.v_nombre = '".$remates_almacen."' ";
		}
		if ($id_remate == true)
		{
			$this->and_id_remate = " AND remates.iid_consecutivo = ".$id_remate." ";
		}
	}
}

class Remate extends Concatena
{

	public function widgets()
	{
		$conn = conexion::conectar();
		$and_almacen = $this->and_almacen;
		$and_plaza = $this->and_plaza;

		$sql = "SELECT * FROM
				(
				SELECT COUNT(remates.n_status) AS registro
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				INNER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = remates.iid_num_cliente
				WHERE remates.n_status = 0 ".$and_plaza.$and_almacen."
				),
				(
				SELECT COUNT(remates.n_status) AS proceso
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				INNER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = remates.iid_num_cliente
				WHERE remates.n_status IN (1,2,3,4,5,6,7,8,9) ".$and_plaza.$and_almacen."
				),
				(
				SELECT COUNT(remates.n_status) AS adjudicada
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				INNER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = remates.iid_num_cliente
				WHERE remates.n_status = 10 ".$and_plaza.$and_almacen."
			  ),
				(
				SELECT COUNT(remates.n_status) AS venta
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				INNER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = remates.iid_num_cliente
				WHERE remates.n_status = 11 ".$and_plaza.$and_almacen."
			  ),
				(
				SELECT COUNT(remates.n_status) AS destruccion
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				INNER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = remates.iid_num_cliente
				WHERE remates.n_status = 12 ".$and_plaza.$and_almacen."
				)";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_widgets[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_widgets;
	}

	public function grafica_remates($grafica_pla_alm,$remate_status)
	{
		$conn = conexion::conectar();
		$and_plaza = $this->and_plaza;
		$and_almacen = $this->and_almacen;

		switch ($grafica_pla_alm) {
			case 'PLA':
				$graf_select = " ,pla.v_razon_social " ;
				break;
			case 'ALM':
				$graf_select = " ,alm.v_nombre " ;
				break;
		}

		$sql_grafica = "SELECT COUNT(remates.iid_consecutivo) AS total, remates.iid_plaza AS id_plaza, pla.v_siglas AS plaza_sig,
						DECODE(TO_CHAR(pla.iid_plaza),
						3,'#FBEC5D',
						4,'#99DAFD',
						5,'#32DADD',
						6,'#5AB1EF',
						7,'#849BE8',
						8,'#D87A80',
						17,'#FF6A6F',
						18,'#5D8AA8',
						21,'#5A6E83',
						23,'#B6A2DE', '#1A2226'
						) AS color ".$graf_select." AS pla_alm
						FROM ad_cxc_remates_mercancias remates
						INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
						INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
						INNER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = remates.iid_num_cliente
						WHERE remates.n_status IN (".$remate_status.") ".$and_plaza.$and_almacen."
						GROUP BY remates.iid_plaza, pla.v_siglas, pla.iid_plaza ".$graf_select." ";

						#echo $sql_grafica;
		$stid_grafica = oci_parse($conn, $sql_grafica);
		oci_execute($stid_grafica);

		while (($row = oci_fetch_array($stid_grafica)) !=false) {
			$res_grafica[]=$row;
		}

		oci_free_statement($stid_grafica);
		oci_close($conn);
		return $res_grafica;

	}

	public function info_remates($remate_status)
	{
		$conn = conexion::conectar();
		$and_plaza = $this->and_plaza;
		$and_almacen = $this->and_almacen;
		$and_id_remate = $this->and_id_remate;

		$sql_info_remates = "SELECT remates.iid_consecutivo AS id_remate, to_char(remates.d_fecha_registro, 'dd-mm-yyyy') AS fec_reg, remates.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, remates.iid_num_cliente AS id_cliente, cli.v_razon_social AS cliente, remates.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,remates.v_promotor AS promotor, remates.v_tipo_mercancia AS tipo_mercancia, decode(remates.n_regimen,1,'NACIONAL',2,'FISCAL') AS regimen
        ,decode(remates.n_status,0,'REGISTRADO',1,'1RA ALMONEDA',2,'2DA ALMONEDA',3,'3RA ALMONEDA',4,'4TA ALMONEDA',5,'5TA ALMONEDA',6,'6TA ALMONEDA',7,'7MA ALMONEDA',8,'8VA ALMONEDA',9,'9NA ALMONEDA',10,'ADJUDICADO', 11,
              'VENDIDO',12,
              'DESTRUIDO','NO DEFINIDO') AS status, remates.n_status AS id_status
        ,to_char(remates.d_fec_almoneda_uno, 'dd-mm-yyyy') AS fec_almoneda1, to_char(remates.d_fec_almoneda_dos, 'dd-mm-yyyy') AS fec_almoneda2, to_char(remates.d_fec_almoneda_tres, 'dd-mm-yyyy') AS fec_almoneda3, to_char(remates.d_fec_almoneda_cuatro, 'dd-mm-yyyy') AS fec_almoneda4
        ,to_char(remates.d_fec_almoneda_cinco, 'dd-mm-yyyy') AS fec_almoneda5, to_char(remates.d_fec_almoneda_seis, 'dd-mm-yyyy') AS fec_almoneda6, to_char(remates.d_fec_almoneda_siete, 'dd-mm-yyyy') AS fec_almoneda7, to_char(remates.d_fec_almoneda_ocho, 'dd-mm-yyyy') AS fec_almoneda8, to_char(remates.d_fec_almoneda_nueve, 'dd-mm-yyyy') AS fec_almoneda9
        ,to_char(remates.d_fec_adjudicacion, 'dd-mm-yyyy') AS fec_adjudicado, remates.v_oportunidad_venta AS op_vta, remates.n_valor_adjudicacion AS val_adjudicado
        ,remates.n_costo_notario_uno AS notario1, remates.n_costo_notario_dos AS notario2, remates.n_costo_notario_tres AS notario3, remates.n_costo_notario_cuatro AS notario4, remates.n_costo_notario_cinco AS notario5, remates.n_costo_notario_seis AS notario6, remates.n_costo_notario_siete AS notario7, remates.n_costo_notario_ocho AS notario8, remates.n_costo_notario_nueve AS notario9
        ,remates.n_costo_publica_uno AS publica1, remates.n_costo_publica_dos AS publica2, remates.n_costo_publica_tres AS publica3, remates.n_costo_publica_cuatro AS publica4, remates.n_costo_publica_cinco AS publica5, remates.n_costo_publica_seis AS publica6, remates.n_costo_publica_siete AS publica7, remates.n_costo_publica_ocho AS publica8, remates.n_costo_publica_nueve AS publica9
        ,remates.n_valor_almoneda_uno AS v_almoneda1, remates.n_valor_almoneda_dos AS v_almoneda2, remates.n_valor_almoneda_tres AS v_almoneda3, remates.n_valor_almoneda_cuatro AS v_almoneda4, remates.n_valor_almoneda_cinco AS v_almoneda5, remates.n_valor_almoneda_seis AS v_almoneda6, remates.n_valor_almoneda_siete AS v_almoneda7, remates.n_valor_almoneda_ocho AS v_almoneda8, remates.n_valor_almoneda_nueve AS v_almoneda9,
				remates.n_saldo_deudor_cliente AS saldo_deudor, remates.v_seguimiento_venta_merc AS SEGUIMIENTO_MERCA,
				(SELECT sum(case
             when (fac.iid_moneda = 2) then
              		  nvl(mov.n_monto_cargo, 0)
             else
              			nvl(mov.n_monto_cargo, 0)
             end) AS saldo
			 FROM ad_fa_factura fac
			 INNER JOIN AD_CXC_MOVTOS mov ON fac.iid_folio = mov.iid_folio
			                              AND fac.iid_plaza = mov.iid_plaza
			 WHERE  fac.status = 7 AND fac.d_fecha_pago IS NULL AND FAC.IID_NUM_CLIENTE = REMATES.IID_NUM_CLIENTE) AS SUELDO_REAL,
			 (select count(*) from ad_cxc_remates_destruccion_img img where img.iid_remate = remates.iid_consecutivo) AS NUMERO_IMAGENES_DESTRUIDAS,
			 (select count(*) from AD_CXC_REMATES_MERCANCIAS_IMG img where img.iid_remate = remates.iid_consecutivo) AS NUMERO_IMAGENES
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN cliente cli ON cli.iid_num_cliente = remates.iid_num_cliente
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				WHERE remates.n_status IN (".$remate_status.") ".$and_plaza.$and_almacen.$and_id_remate." ";

		#echo $sql_info_remates;
		$stid_info = oci_parse($conn, $sql_info_remates);
		oci_execute($stid_info);

		while ( ($row = oci_fetch_array($stid_info)) !=false ) {
			$res_info[] = $row;
		}
//remates por notificar, remates en proceso , remates adjudicados ,
		oci_free_statement($stid_info);
		oci_close($conn);
		return $res_info;
	}

	public function info_costo_destruccion($id_remate)
	{
		$conn = conexion::conectar();
		$and_plaza = $this->and_plaza;
		$and_almacen = $this->and_almacen;
		$and_id_remate = $this->and_id_remate;

		$sql_info_remates = "SELECT c.d_fec_destruccion as D_FEC_DESTRUCCION, c.n_costo_destruccion as N_COSTO_DESTRUCCION FROM ad_cxc_remates_mercancias c where c.iid_consecutivo = $id_remate AND C.D_FEC_DESTRUCCION IS NOT NULL AND C.N_COSTO_DESTRUCCION IS NOT NULL";

		#echo $sql_info_remates;
		$stid_info = oci_parse($conn, $sql_info_remates);
		oci_execute($stid_info);

		while ( ($row = oci_fetch_array($stid_info)) !=false ) {
			$res_info[] = $row;
		}
	//remates por notificar, remates en proceso , remates adjudicados ,
		oci_free_statement($stid_info);
		oci_close($conn);
		return $res_info;
	}

	//DESTRUIDAS ADJUDICADAS
	public function info_remates2($remate_status)
	{
		$conn = conexion::conectar();
		$and_plaza = $this->and_plaza;
		$and_almacen = $this->and_almacen;
		$and_id_remate = $this->and_id_remate;

		$sql_info_remates = "SELECT remates.iid_consecutivo AS id_remate, to_char(remates.d_fecha_registro, 'dd-mm-yyyy') AS fec_reg, remates.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, remates.iid_num_cliente AS id_cliente, cli.v_razon_social AS cliente, remates.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,remates.v_promotor AS promotor, remates.v_tipo_mercancia AS tipo_mercancia, decode(remates.n_regimen,1,'NACIONAL',2,'FISCAL') AS regimen
        ,decode(remates.n_status,0,'REGISTRADO',1,'1RA ALMONEDA',2,'2DA ALMONEDA',3,'3RA ALMONEDA',4,'4TA ALMONEDA',5,'5TA ALMONEDA',6,'6TA ALMONEDA',7,'7MA ALMONEDA',8,'8VA ALMONEDA',9,'9NA ALMONEDA',10,'ADJUDICADO', 11,
              'VENDIDO',12,'DESTRUIDO','NO DEFINIDO') AS status, remates.n_status AS id_status
        ,to_char(remates.d_fec_almoneda_uno, 'dd-mm-yyyy') AS fec_almoneda1, to_char(remates.d_fec_almoneda_dos, 'dd-mm-yyyy') AS fec_almoneda2, to_char(remates.d_fec_almoneda_tres, 'dd-mm-yyyy') AS fec_almoneda3, to_char(remates.d_fec_almoneda_cuatro, 'dd-mm-yyyy') AS fec_almoneda4
        ,to_char(remates.d_fec_almoneda_cinco, 'dd-mm-yyyy') AS fec_almoneda5, to_char(remates.d_fec_almoneda_seis, 'dd-mm-yyyy') AS fec_almoneda6, to_char(remates.d_fec_almoneda_siete, 'dd-mm-yyyy') AS fec_almoneda7, to_char(remates.d_fec_almoneda_ocho, 'dd-mm-yyyy') AS fec_almoneda8, to_char(remates.d_fec_almoneda_nueve, 'dd-mm-yyyy') AS fec_almoneda9
        ,to_char(remates.d_fec_adjudicacion, 'dd-mm-yyyy') AS fec_adjudicado, remates.v_oportunidad_venta AS op_vta, remates.n_valor_adjudicacion AS val_adjudicado
        ,remates.n_costo_notario_uno AS notario1, remates.n_costo_notario_dos AS notario2, remates.n_costo_notario_tres AS notario3, remates.n_costo_notario_cuatro AS notario4, remates.n_costo_notario_cinco AS notario5, remates.n_costo_notario_seis AS notario6, remates.n_costo_notario_siete AS notario7, remates.n_costo_notario_ocho AS notario8, remates.n_costo_notario_nueve AS notario9
        ,remates.n_costo_publica_uno AS publica1, remates.n_costo_publica_dos AS publica2, remates.n_costo_publica_tres AS publica3, remates.n_costo_publica_cuatro AS publica4, remates.n_costo_publica_cinco AS publica5, remates.n_costo_publica_seis AS publica6, remates.n_costo_publica_siete AS publica7, remates.n_costo_publica_ocho AS publica8, remates.n_costo_publica_nueve AS publica9
        ,remates.n_valor_almoneda_uno AS v_almoneda1, remates.n_valor_almoneda_dos AS v_almoneda2, remates.n_valor_almoneda_tres AS v_almoneda3, remates.n_valor_almoneda_cuatro AS v_almoneda4, remates.n_valor_almoneda_cinco AS v_almoneda5, remates.n_valor_almoneda_seis AS v_almoneda6, remates.n_valor_almoneda_siete AS v_almoneda7, remates.n_valor_almoneda_ocho AS v_almoneda8, remates.n_valor_almoneda_nueve AS v_almoneda9,
				remates.n_saldo_deudor_cliente AS saldo_deudor, remates.v_seguimiento_venta_merc AS SEGUIMIENTO_MERCA,
				(SELECT sum(case
             when (fac.iid_moneda = 2) then
              (fac.total * fac.c_tpo_cambio)
             else
              (fac.total)
             end) AS saldo
			 FROM ad_fa_factura fac
			 INNER JOIN AD_CXC_MOVTOS mov ON fac.iid_folio = mov.iid_folio
			                              AND fac.iid_plaza = mov.iid_plaza
			 WHERE (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) > 90
			                               AND ((mov.d_fecha_movto <= SYSDATE) AND (fac.status = 7) AND
			                                   (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL) AND
			                                   mov.n_status = 2) AND FAC.IID_NUM_CLIENTE = REMATES.IID_NUM_CLIENTE) AS SUELDO_REAL,
				 (select count(*) from ad_cxc_remates_destruccion_img img where img.iid_remate = remates.iid_consecutivo) AS NUMERO_IMAGENES_DESTRUIDAS,
			   (select count(*) from AD_CXC_REMATES_MERCANCIAS_IMG img where img.iid_remate = remates.iid_consecutivo) AS NUMERO_IMAGENES
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN cliente cli ON cli.iid_num_cliente = remates.iid_num_cliente
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				WHERE remates.n_status IN (".$remate_status.") ".$and_plaza.$and_almacen.$and_id_remate." AND REMATES.V_OPORTUNIDAD_VENTA = 2";
#hasta la primera almoneda :3
		#echo $sql_info_remates;
		$stid_info = oci_parse($conn, $sql_info_remates);
		oci_execute($stid_info);

		while ( ($row = oci_fetch_array($stid_info)) !=false ) {
			$res_info[] = $row;
		}
//remates por notificar, remates en proceso , remates adjudicados ,
		oci_free_statement($stid_info);
		oci_close($conn);
		return $res_info;
	}

	public function info_remates3($remate_status)
	{
		$conn = conexion::conectar();
		$and_plaza = $this->and_plaza;
		$and_almacen = $this->and_almacen;
		$and_id_remate = $this->and_id_remate;

		$sql_info_remates = "SELECT remates.iid_consecutivo AS id_remate, to_char(remates.d_fecha_registro, 'dd-mm-yyyy') AS fec_reg, remates.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, remates.iid_num_cliente AS id_cliente, cli.v_razon_social AS cliente, remates.iid_almacen AS id_almacen, alm.v_nombre AS almacen
				,remates.v_promotor AS promotor, remates.v_tipo_mercancia AS tipo_mercancia, decode(remates.n_regimen,1,'NACIONAL',2,'FISCAL') AS regimen
        ,decode(remates.n_status,0,'REGISTRADO',1,'1RA ALMONEDA',2,'2DA ALMONEDA',3,'3RA ALMONEDA',4,'4TA ALMONEDA',5,'5TA ALMONEDA',6,'6TA ALMONEDA',7,'7MA ALMONEDA',8,'8VA ALMONEDA',9,'9NA ALMONEDA',10,'ADJUDICADO',11,
              'VENDIDO',12,'DESTRUIDO','NODEFINIDO') AS status, remates.n_status AS id_status
        ,to_char(remates.d_fec_almoneda_uno, 'dd-mm-yyyy') AS fec_almoneda1, to_char(remates.d_fec_almoneda_dos, 'dd-mm-yyyy') AS fec_almoneda2, to_char(remates.d_fec_almoneda_tres, 'dd-mm-yyyy') AS fec_almoneda3, to_char(remates.d_fec_almoneda_cuatro, 'dd-mm-yyyy') AS fec_almoneda4
        ,to_char(remates.d_fec_almoneda_cinco, 'dd-mm-yyyy') AS fec_almoneda5, to_char(remates.d_fec_almoneda_seis, 'dd-mm-yyyy') AS fec_almoneda6, to_char(remates.d_fec_almoneda_siete, 'dd-mm-yyyy') AS fec_almoneda7, to_char(remates.d_fec_almoneda_ocho, 'dd-mm-yyyy') AS fec_almoneda8, to_char(remates.d_fec_almoneda_nueve, 'dd-mm-yyyy') AS fec_almoneda9
        ,to_char(remates.d_fec_adjudicacion, 'dd-mm-yyyy') AS fec_adjudicado, remates.v_oportunidad_venta AS op_vta, remates.n_valor_adjudicacion AS val_adjudicado
        ,remates.n_costo_notario_uno AS notario1, remates.n_costo_notario_dos AS notario2, remates.n_costo_notario_tres AS notario3, remates.n_costo_notario_cuatro AS notario4, remates.n_costo_notario_cinco AS notario5, remates.n_costo_notario_seis AS notario6, remates.n_costo_notario_siete AS notario7, remates.n_costo_notario_ocho AS notario8, remates.n_costo_notario_nueve AS notario9
        ,remates.n_costo_publica_uno AS publica1, remates.n_costo_publica_dos AS publica2, remates.n_costo_publica_tres AS publica3, remates.n_costo_publica_cuatro AS publica4, remates.n_costo_publica_cinco AS publica5, remates.n_costo_publica_seis AS publica6, remates.n_costo_publica_siete AS publica7, remates.n_costo_publica_ocho AS publica8, remates.n_costo_publica_nueve AS publica9
        ,remates.n_valor_almoneda_uno AS v_almoneda1, remates.n_valor_almoneda_dos AS v_almoneda2, remates.n_valor_almoneda_tres AS v_almoneda3, remates.n_valor_almoneda_cuatro AS v_almoneda4, remates.n_valor_almoneda_cinco AS v_almoneda5, remates.n_valor_almoneda_seis AS v_almoneda6, remates.n_valor_almoneda_siete AS v_almoneda7, remates.n_valor_almoneda_ocho AS v_almoneda8, remates.n_valor_almoneda_nueve AS v_almoneda9,
				remates.n_saldo_deudor_cliente AS saldo_deudor, remates.v_seguimiento_venta_merc AS SEGUIMIENTO_MERCA,
				(SELECT sum(case
             when (fac.iid_moneda = 2) then
              (fac.total * fac.c_tpo_cambio)
             else
              (fac.total)
             end) AS saldo
			 FROM ad_fa_factura fac
			 INNER JOIN AD_CXC_MOVTOS mov ON fac.iid_folio = mov.iid_folio
			                              AND fac.iid_plaza = mov.iid_plaza
			 WHERE (TO_DATE(TO_CHAR(SYSDATE, 'DD/MONTH/YYYY')) - fac.fec_ven_factura) > 90
			                               AND ((mov.d_fecha_movto <= SYSDATE) AND (fac.status = 7) AND
			                                   (fac.d_fecha_pago >= SYSDATE OR fac.d_fecha_pago IS NULL) AND
			                                   mov.n_status = 2) AND FAC.IID_NUM_CLIENTE = REMATES.IID_NUM_CLIENTE) AS SUELDO_REAL,
				 (select count(*) from ad_cxc_remates_destruccion_img img where img.iid_remate = remates.iid_consecutivo) AS NUMERO_IMAGENES_DESTRUIDAS,
			   (select count(*) from AD_CXC_REMATES_MERCANCIAS_IMG img where img.iid_remate = remates.iid_consecutivo) AS NUMERO_IMAGENES
				FROM ad_cxc_remates_mercancias remates
				INNER JOIN plaza pla ON pla.iid_plaza = remates.iid_plaza
				INNER JOIN cliente cli ON cli.iid_num_cliente = remates.iid_num_cliente
				INNER JOIN almacen alm ON alm.iid_almacen = remates.iid_almacen
				WHERE remates.n_status IN (".$remate_status.") ".$and_plaza.$and_almacen.$and_id_remate." AND REMATES.V_OPORTUNIDAD_VENTA = 1";

#		echo $sql_info_remates;
		$stid_info = oci_parse($conn, $sql_info_remates);
		oci_execute($stid_info);

		while ( ($row = oci_fetch_array($stid_info)) !=false ) {
			$res_info[] = $row;
		}
//remates por notificar, remates en proceso , remates adjudicados ,
		oci_free_statement($stid_info);
		oci_close($conn);
		return $res_info;
	}

	public function info_remates4($iid_remate, $OPORTU_VENTA){
			$conn = conexion::conectar();

			if ($OPORTU_VENTA == 1 ) {
							$sql = "SELECT T.IID_NUMERO_REGISTRO, PRO.V_NOMBRE  AS NOMBRE,
							T.COMENTARIOS, T.FECHA_INI_PROSPECCION, T.FECHA_LIMITE_VENTA
							FROM ad_cxc_seguimiento_remates T
							INNER JOIN se_usuarios PRO ON T.IID_RESP_VENTA = PRO.iid_empleado
							WHERE T.IID_CONSECUTIVO= $iid_remate ";
			}else {
						$sql = "SELECT T.IID_NUMERO_REGISTRO, PRO.V_NOMBRE || ' '  || PRO.V_APELLIDO_PAT || ' '  || PRO.V_APELLIDO_MAT AS NOMBRE,
       			T.COMENTARIOS, T.FECHA_INI_PROSPECCION, T.FECHA_LIMITE_VENTA
         		FROM AD_CXC_SEGUIMIENTO_VENTA T
         		INNER JOIN CO_PROMOTOR PRO ON T.IID_RESP_VENTA = PRO.IID_PROMOTOR
         		WHERE T.IID_CONSECUTIVO= $iid_remate ";
		 }
		 #echo $sql;

						$stid_info = oci_parse($conn, $sql);
						oci_execute($stid_info);

						while ( ($row = oci_fetch_array($stid_info)) !=false ) {
							$res_info[] = $row;
						}
				//remates por notificar, remates en proceso , remates adjudicados ,
						oci_free_statement($stid_info);
						oci_close($conn);
						return $res_info;
	}

}?>
