<?php
/**
* © Argo Almacenadora ®
* Fecha: 15/07/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Operaciones Agronegocios
* Version --
*/
include_once "../libs/conOra.php";

class Turno_uno
{

/* ----------------------- FUNCION PARA MOSTRAR SELECT FILTRO ALMACEN ----------------------- */
	public function capacidad_bodega($inicio_cap_bodegas_agro,$fin_cap_bodegas_agro)
	{
		$conn = conexion::conectar();

		$sql = "SELECT carga.alm_id_alm AS id_almacen, carga.alm_nom AS almacen, SUM(carga.det_t_netas) AS toneladas, 'CARGA' AS tipo
				FROM vista_dashboard_ofc carga
				WHERE carga.det_status = 2 AND TO_DATE(TO_CHAR(carga.det_fecha_salida, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY carga.alm_id_alm,carga.alm_nom
				UNION
				SELECT descarga.alm_id_alm AS id_almacen, descarga.alm_nom AS almacen, SUM(descarga.det_t_netas) AS toneladas, 'DESCARGA' AS tipo
				FROM vista_dashboard_otfc descarga
				WHERE descarga.det_status = 2 AND TO_DATE(TO_CHAR(descarga.det_fecha_envio, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY descarga.alm_id_alm,descarga.alm_nom
				";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_turno1[] = $row;
	 	}

	 	$sql = "SELECT carga.alm_id_alm AS id_almacen, carga.alm_nom AS almacen, SUM(carga.det_t_netas) AS toneladas, 'CARGA' AS tipo
				FROM vista_dashboard_ofc carga
				WHERE carga.det_status = 2 AND TO_DATE(TO_CHAR(carga.det_fecha_salida, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY carga.alm_id_alm,carga.alm_nom
				UNION
				SELECT descarga.alm_id_alm AS id_almacen, descarga.alm_nom AS almacen, SUM(descarga.det_t_netas) AS toneladas, 'DESCARGA' AS tipo
				FROM vista_dashboard_otfc descarga
				WHERE descarga.det_status = 2 AND TO_DATE(TO_CHAR(descarga.det_fecha_envio, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY descarga.alm_id_alm,descarga.alm_nom
				";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_turno1[] = $row;
	 	}

	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_turno1;
	}

}

class Turno_dos
{

/* ----------------------- FUNCION PARA MOSTRAR SELECT FILTRO ALMACEN ----------------------- */
	public function capacidad_bodega($inicio_cap_bodegas_agro,$fin_cap_bodegas_agro)
	{
		$conn = conexion::conectar();

		$sql = "SELECT carga.alm_id_alm AS id_almacen, carga.alm_nom AS almacen, SUM(carga.det_t_netas) AS toneladas, 'CARGA' AS tipo
				FROM vista_dashboard_ofc carga
				WHERE carga.det_status = 2 AND TO_DATE(TO_CHAR(carga.det_fecha_salida, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY carga.alm_id_alm,carga.alm_nom
				UNION
				SELECT descarga.alm_id_alm AS id_almacen, descarga.alm_nom AS almacen, SUM(descarga.det_t_netas) AS toneladas, 'DESCARGA' AS tipo
				FROM vista_dashboard_otfc descarga
				WHERE descarga.det_status = 2 AND TO_DATE(TO_CHAR(descarga.det_fecha_envio, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY descarga.alm_id_alm,descarga.alm_nom
				";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_turno2[] = $row;
	 	}

	 	$sql = "SELECT carga.alm_id_alm AS id_almacen, carga.alm_nom AS almacen, SUM(carga.det_t_netas) AS toneladas, 'CARGA' AS tipo
				FROM vista_dashboard_ofc carga
				WHERE carga.det_status = 2 AND TO_DATE(TO_CHAR(carga.det_fecha_salida, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY carga.alm_id_alm,carga.alm_nom
				UNION
				SELECT descarga.alm_id_alm AS id_almacen, descarga.alm_nom AS almacen, SUM(descarga.det_t_netas) AS toneladas, 'DESCARGA' AS tipo
				FROM vista_dashboard_otfc descarga
				WHERE descarga.det_status = 2 AND TO_DATE(TO_CHAR(descarga.det_fecha_envio, 'dd-mm-yyyy hh:mi PM'), 'dd-mm-yyyy hh:mi PM')
				BETWEEN TO_DATE('".$inicio_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM') AND TO_DATE('".$fin_cap_bodegas_agro."', 'dd-mm-yyyy hh:mi PM')
				GROUP BY descarga.alm_id_alm,descarga.alm_nom
				";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_turno2[] = $row;
	 	}

	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_turno2;
	}

}

?>
