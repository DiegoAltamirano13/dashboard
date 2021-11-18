<?php
/**
* © Argo Almacenadora ®
* Fecha: 26/12/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Comercial Comparativos ingresos de clientes
* Version --
*/
include_once '../libs/conOra.php';

class Comparativo
{

	function __construct()
	{
		# code...
	}

// ************************** FUNCION PARA CONSULTAR LA FECHA EN LA BASE **************************  //
	function consulta_fecha()
	{
		$conn = conexion::conectar();

		$sql = "SELECT TO_CHAR(SYSDATE, 'MM') AS mes, TO_CHAR(SYSDATE, 'YYYY') AS anio FROM DUAL";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_consulta_fecha[]=$row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		return $this->res_consulta_fecha;
	}

// ************************** FUNCION PARA CONSULTAR LOS INGRESOS **************************  //
	function consulta_cliente($giro_ingre_clie,$comp_cliente)
	{
		$conn = conexion::conectar();

		if ($comp_cliente == true){
			$and_cliente = " AND c.v_razon_social = '".$comp_cliente."' ";
		}

		$sql = "SELECT C.iid_num_cliente AS id_cliente, c.n_tipo_cliente AS giro, c.v_razon_social AS cliente
				FROM cliente c
				WHERE C.n_tipo_cliente = '".$giro_ingre_clie."' AND c.s_status = 1 ".$and_cliente."
				ORDER BY c.v_razon_social ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_cliente[]=$row;
		}

		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_cliente;
	}

// ************************** FUNCION PARA CONSULTAR LOS INGRESOS **************************  //
	function consulta_ingreso($opcion,$id_cliente,$anio,$mes)
	{
		$conn = conexion::conectar();

		//CONCATENA OPCIONES (1.-CONSULTA SALDO MES -- 2 CONSULTA SUMATORIA DE MESES)
		switch ($opcion) {
			case 1:
				$campo = " m.mes, ";
				$where = " WHERE m.mes = ".$mes." ";
				$group = " GROUP BY m.mes ";
				break;
			case 2:
				$campo = "";
				$where = " WHERE m.mes <= ".$mes." ";
				$group = "";
				break;
		}

		$sql = "SELECT ".$campo."
				DECODE ( SUM(r.cargos) - DECODE( SUM(r.abonos), NULL,0,sum(r.abonos)), NULL, 0, SUM(r.cargos) - DECODE( SUM(r.abonos), NULL,0,sum(r.abonos))  ) AS ingreso
				FROM vista_dashboard_mes_prospecto m
				LEFT OUTER JOIN vista_dashboard_ingre_cli r ON r.mes = m.mes AND r.iid_num_cliente = ".$id_cliente." AND r.anio = ".$anio."
				".$where."
				".$group."
				ORDER BY m.mes ";

				#echo $sql;
		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_ingreso[]=$row;
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_ingreso;

	}


	function consulta_ingresosProspecto($opcion,$id_cliente,$anio,$mes)
	{
		$conn = conexion::conectar();

		//CONCATENA OPCIONES (1.-CONSULTA SALDO MES -- 2 CONSULTA SUMATORIA DE MESES)
		switch ($opcion) {
			case 1:
				$campo = " m.mes, ";
				$where = " WHERE m.mes = ".$mes." ";
				$group = " GROUP BY m.mes ";
				break;
			case 2:
				$campo = "";
				$where = " WHERE m.mes <= ".$mes." ";
				$group = "";
				break;
		}

		$sql = "SELECT ".$campo."
				DECODE ( SUM(r.cargos) - DECODE( SUM(r.abonos), NULL,0,sum(r.abonos)), NULL, 0, SUM(r.cargos) - DECODE( SUM(r.abonos), NULL,0,sum(r.abonos))  ) AS ingreso
				FROM vista_dashboard_mes_prospecto m
				LEFT OUTER JOIN vista_dashboard_ingre_cli r ON r.mes = m.mes AND r.anio = ".$anio."
				LEFT OUTER JOIN CLIENTE C ON C.IID_NUM_CLIENTE = R.iid_num_cliente
  			LEFT OUTER JOIN CO_PROSPECTO CO ON CO.IID_NUM_PROSPECTO =  C.IID_NUM_PROSPECTO
				".$where."
				 AND CO.V_RAZON_SOCIAL LIKE '$id_cliente'
				".$group."
				ORDER BY m.mes ";

				#echo $sql;
		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_ingreso[]=$row;
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_ingreso;

	}

// ************************** FUNCION PARA CONSULTAR UNA FUNCION DESDE ORACLE **************************  //
	function consulta_funcion()
	{
		$conn = conexion::conectar();



		$sql = " SELECT f_test(10) AS mfrc FROM dual ";
		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($fila = oci_fetch_array($stid, OCI_ASSOC))) {
		    $rc = $fila['MFRC'];
		    oci_execute($rc);  // el valor de la columna devuelta por la consulta es un ref cursor

		    while (($row = oci_fetch_array($rc, OCI_ASSOC))) {
		        $res_consulta_funcion[] = $row;
		    }

		    oci_free_statement($rc);
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_funcion;

	}

// ************************** FUNCION PARA CONSULTAR LOS INGRESOS **************************  //
	function consulta_ingreso_cliente($opcion,$anio,$mes)
	{
		$conn = conexion::conectar();

		//CONCATENA OPCIONES (1.-CONSULTA SALDO MES -- 2 CONSULTA SUMATORIA DE MESES)
		switch ($opcion) {
			case 1:
				$and = " and rd.mes  = ".$mes." ";
				break;
			case 2:
				$and = " and rd.mes > ".$mes." ";
				break;
		}

		$sql = "SELECT r.iid_num_cliente AS id_cliente, c.v_razon_social AS cliente, r.n_tipo_cliente AS giro,
				(
				SELECT
				DECODE ( SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos)), NULL, 0, SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos))  )
				FROM vista_dashboard_ingre_cli rd
				WHERE rd.iid_num_cliente = r.iid_num_cliente and rd.anio = ".$anio." ".$and."
				) AS ingresos
				FROM cliente r
				WHERE r.s_status = 1
				ORDER BY c.v_razon_social";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_ingreso[]=$row;
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_ingreso;

	}

// ************************** FUNCION PARA CONSULTAR LOS INGRESOS **************************  //
	function consulta_ingreso_cliente_old($opcion,$anio_1,$mes_1,$anio_2,$mes_2)
	{
		$conn = conexion::conectar();

		//CONCATENA OPCIONES (1.-CONSULTA SALDO MES -- 2 CONSULTA SUMATORIA DE MESES)
		switch ($opcion) {
			case 1:
				$and_1 = " and rd.mes  = ".$mes_1." ";
				$and_2 = " and rd.mes  = ".$mes_2." ";
				break;
			case 2:
				$and_1 = " and rd.mes > ".$mes_1." ";
				$and_2 = " and rd.mes > ".$mes_2." ";
				break;
		}

		$sql = "SELECT c.iid_num_cliente AS id_cliente, c.v_razon_social AS cliente, c.n_tipo_cliente AS giro,
				(
				SELECT
				DECODE ( SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos)), NULL, 0, SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos))  )
				FROM vista_dashboard_ingre_cli rd
				WHERE rd.iid_num_cliente = c.iid_num_cliente and rd.anio = ".$anio_1." ".$and_1."
				) AS ingresos_1,
				(
				SELECT
				DECODE ( SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos)), NULL, 0, SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos))  )
				FROM vista_dashboard_ingre_cli rd
				WHERE rd.iid_num_cliente = c.iid_num_cliente and rd.anio = ".$anio_2." ".$and_2."
				) AS ingresos_2
				FROM cliente c  WHERE c.s_status = 1 ORDER BY c.v_razon_social";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_ingreso[]=$row;
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_ingreso;

	}
// ************************** FUNCION PARA CREACION DE LA GRAFICA POR CLIENTE **************************  //
	function grafica_monto($comp_id_cliente,$anio)
	{
		$conn = conexion::conectar();


		$sql = " SELECT m.mes,
				DECODE ( SUM(r.cargos) - DECODE( SUM(r.abonos), NULL,0,sum(r.abonos)), NULL, 0, SUM(r.cargos) - DECODE( SUM(r.abonos), NULL,0,sum(r.abonos))  ) AS ingreso
				FROM vista_dashboard_mes_prospecto m
				LEFT OUTER JOIN vista_dashboard_ingre_cli r ON r.mes = m.mes AND r.iid_num_cliente = ".$comp_id_cliente." AND r.anio = ".$anio."
				GROUP BY m.mes
				ORDER BY m.mes ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_grafica_monto[]=$row;
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_grafica_monto;

	}

// ************************** FUNCION PARA CONSULTAR LOS INGRESOS **************************  //
	function consulta_ingreso_cliente_new($opcion,$anio,$mes)
	{
		$conn = conexion::conectar();

		//CONCATENA OPCIONES (1.-CONSULTA SALDO MES -- 2 CONSULTA SUMATORIA DE MESES)
		switch ($opcion) {
			case 1:
				$and = " and rd.mes  = ".$mes." ";
				break;
			case 2:
				$and = " and rd.mes > ".$mes." ";
				break;
		}

		$sql = "SELECT c.iid_num_cliente AS id_cliente, c.v_razon_social AS cliente, c.n_tipo_cliente AS giro,
				(
				SELECT
				DECODE ( SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos)), NULL, 0, SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos))  )
				FROM vista_dashboard_ingre_cli rd
				WHERE rd.iid_num_cliente = c.iid_num_cliente and rd.anio = 2016 and rd.mes  = 10
				) AS ingresos_select
				FROM cliente c
				WHERE c.s_status = 1
				ORDER BY c.v_razon_social";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_ingreso_cliente[]=$row;
		}

		$sql_2 = "SELECT c.iid_num_cliente AS id_cliente, c.v_razon_social AS cliente, c.n_tipo_cliente AS giro,
				(
				SELECT
				DECODE ( SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos)), NULL, 0, SUM(rd.cargos) - DECODE( SUM(rd.abonos), NULL,0,sum(rd.abonos))  )
				FROM vista_dashboard_ingre_cli rd
				WHERE rd.iid_num_cliente = c.iid_num_cliente and rd.anio = 2016 and rd.mes  = 09
				) AS ingresos_mes
				FROM cliente c
				WHERE c.s_status = 1
				ORDER BY c.v_razon_social";

		$stid = oci_parse($conn, $sql_2);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_consulta_ingreso_cliente_new[]=$row.$res_consulta_ingreso_cliente;
		}


		oci_free_statement($stid);
		oci_close($conn);
		return $res_consulta_ingreso_cliente_new;

	}


}

?>
