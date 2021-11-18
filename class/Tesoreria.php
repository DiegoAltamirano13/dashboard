<?php
/**
* © Argo Almacenadora ®
* Fecha: 16/08/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard (TESORERIA)
* Version --
*/
include_once '../libs/conOra.php';

class Tesoreria
{
	public function contingencia_inversiones($cuneta,$s_cuenta,$ss_cuenta)
	{
		$conn = conexion::conectar();
		$anio_actual = date("Y");
		$mes_actual = date("m");
		//echo $anio_actual."   ".$mes_actual;
		if ($mes_actual == 1 ) {
			$sql = "SELECT con_inv.iid_plaza AS id_plaza, con_inv.i_periodo_anio AS anio, con_inv.i_periodo_mes AS mes, con_inv.v_cuenta AS cuenta, con_inv.v_scuenta AS s_cuenta, con_inv.v_sscuenta AS ss_cuenta, con_inv.d_saldo_final AS saldo
					FROM ct_cg_libro_mayor con_inv
					WHERE con_inv.iid_plaza = 2 AND con_inv.v_cuenta = ".$cuneta." AND con_inv.v_scuenta = ".$s_cuenta." AND con_inv.v_sscuenta = ".$ss_cuenta."
					AND con_inv.i_periodo_anio = TO_CHAR(SYSDATE, 'YYYY') AND con_inv.i_periodo_mes = TO_CHAR(SYSDATE,'MM')";
					#echo $sql;
		}else {
			$sql = "SELECT con_inv.iid_plaza AS id_plaza, con_inv.i_periodo_anio AS anio, con_inv.i_periodo_mes AS mes, con_inv.v_cuenta AS cuenta, con_inv.v_scuenta AS s_cuenta, con_inv.v_sscuenta AS ss_cuenta, con_inv.d_saldo_final AS saldo
					FROM ct_cg_libro_mayor con_inv
					WHERE con_inv.iid_plaza = 2 AND con_inv.v_cuenta = ".$cuneta." AND con_inv.v_scuenta = ".$s_cuenta." AND con_inv.v_sscuenta = ".$ss_cuenta."
					AND con_inv.i_periodo_anio = TO_CHAR(SYSDATE, 'YYYY') AND con_inv.i_periodo_mes = TO_CHAR(ADD_MONTHS(SYSDATE,-1),'MM')";
		}



		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_array($stid)) != false )
		{
			$res_contingencia_inversiones[] = $row;
		}
		#echo $sql;
		oci_free_statement($stid);
		oci_close($conn);
		return $res_contingencia_inversiones;

	}

	public function autofinanciamiento()
	{
		$conn = conexion::conectar();

		$sql = "SELECT SUM(t.d_cargos) AS ahorradores, SUM(t.d_abonos) AS adjudicados
				FROM ct_cg_libro_mayor t
				WHERE t.iid_plaza = 2 AND t.v_cuenta = 1502 AND t.v_scuenta = 4 AND t.v_sscuenta = 17
				AND t.i_periodo_anio = TO_CHAR(sysdate, 'YYYY') AND t.i_periodo_mes <  TO_CHAR(sysdate,'MM')";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while ( ($row = oci_fetch_array($stid)) != false )
		{
			$res_autofinanciamiento[] = $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_autofinanciamiento;
	}

	public function bancos()
	{
		$conn = conexion::conectar();

		// $sql = "SELECT b.v_descripcion AS institucion_f, c.v_num_cuenta AS cta, c.v_tipo_cuenta AS concepto, t.n_saldo_hoy AS saldo, c.iid_cuenta
		// 		FROM ct_bn_rep_bancos_dash t, ct_bn_banco b, ct_bn_cuentas_bancarias c
		// 		WHERE t.iid_banco = b.iid_banco and t.iid_cuenta = c.iid_cuenta and t.iid_plaza = c.iid_plaza  and t.n_tipo_cuenta = 1";

		$sql = "SELECT banco.v_descripcion AS institucion_f, cta_bancarias.v_num_cuenta AS cta, cta_bancarias.v_tipo_cuenta AS concepto, rep.n_saldo_hoy AS saldo
				FROM ct_bn_rep_bancos_dash rep
				INNER JOIN ct_bn_banco banco ON banco.iid_banco = rep.iid_banco
				INNER JOIN ct_bn_cuentas_bancarias cta_bancarias ON cta_bancarias.iid_cuenta = rep.iid_cuenta AND cta_bancarias.iid_plaza = rep.iid_plaza
				WHERE rep.n_tipo_cuenta = 1";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while ( ($row = oci_fetch_array($stid)) != false ) {
			$res_bancos[] = $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_bancos;

	}

	public function widgets_ingresos_egresos()
	{
		$conn = conexion::conectar();

		$sql = "SELECT * FROM
				(
				SELECT t.d_saldo_final AS ingreso_cobranza
				FROM ct_cg_libro_mayor t
				WHERE t.iid_plaza = 1 AND t.i_periodo_anio = TO_CHAR(sysdate, 'YYYY') AND t.i_periodo_mes = TO_CHAR(sysdate,'MM') AND t.v_cuenta = 1501 AND t.v_scuenta = 8 AND t.v_sscuenta = 0
				),
				(
				SELECT t.d_saldo_final AS egresos_gastos
				FROM ct_cg_libro_mayor t
				WHERE t.iid_plaza = 1 AND t.i_periodo_anio = TO_CHAR(sysdate, 'YYYY') AND t.i_periodo_mes = TO_CHAR(sysdate,'MM') AND t.v_cuenta = 2311 AND t.v_scuenta = 50 AND t.v_sscuenta = 0
				)";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while ( ($row = oci_fetch_array($stid)) != false ) {
			$res_widgets_ingresos_egresos[] = $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_widgets_ingresos_egresos;

	}

//FUNCTION PARA LEER
	public function leer_excel_det()
	{
		$conn = conexion::conectar();

				$sql = "SELECT * FROM (
						SELECT excel.iid_excel_info_finan AS id_excel, excel.v_nombre_excel AS nombre_excel, excel.v_titulo_excel AS titulo_excel, TO_CHAR(excel.d_fecha_info_finan, 'dd-mm-yyyy') AS fecha
						FROM ct_dashboard_excel_info_finan excel
						WHERE   excel.n_status <> 3
						ORDER BY excel.iid_excel_info_finan desc
						) WHERE rownum = 1";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid );

				while (($row = oci_fetch_assoc($stid)) != false)
				{
					$this->res_leer[]=$row;
				}
					oci_free_statement($stid);
					oci_close($conn);
					return $this->res_leer;
	}


}
