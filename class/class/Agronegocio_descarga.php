<?php
/**
* © Argo Almacenadora ®
* Fecha: 03/03/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Operaciones Agronegocios
* Version --
*/
include_once '../libs/conOra.php';

class Filtros_des
{
	public $historial;
	public $historial2;
	public $historial3;
	public $almacen;
	public $cliente;

	function __construct($agro_plaza,$agro_historial, $fec_ini_agro, $fec_fin_agro, $agro_almacen, $agro_cliente)
	{
		//-----------------------------FILTRO POR FECHA-----------------------------//
		switch (true) {
			case ($agro_historial == true) && ($fec_ini_agro == true) && ($fec_fin_agro == true):
				$this->historial = " AND vista_otfc.det_fecha_envio >= trunc(to_date('".$fec_ini_agro."','dd-mm-yyyy') )
						   AND vista_otfc.det_fecha_envio < trunc(to_date('".$fec_fin_agro ."','dd-mm-yyyy') ) +1 ";
				$this->historial2 = " AND TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy')  < '".$fec_ini_agro ."' ";
				$this->historial3 = " AND to_char(vista_otfc.det_fecha_neto, 'dd-mm-yyyy') = '".$fec_ini_agro."'
						   								AND vista_otfc.det_fecha_envio < trunc(to_date('".$fec_ini_agro ."','dd-mm-yyyy') )";
				break;
			default:
				$this->historial = " AND TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') = '".$agro_historial."' ";
				$this->historial2 = " AND TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') < '".$agro_historial."' ";
				$this->historial3 = " AND TO_CHAR(vista_otfc.det_fecha_neto, 'dd-mm-yyyy') = '".$agro_historial."'
															AND TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') < '".$agro_historial."' ";
				break;
		}
		//-----------------------------FILTRO POR ALMACEN-----------------------------//
		if ($agro_almacen == true){
			$this->almacen = " AND vista_otfc.alm_nom = '".$agro_almacen."' ";
		}else{
			$this->almacen = "";
		}
		//-----------------------------FILTRO POR CLIENTE-----------------------------//
		if ($agro_cliente == true){
			$this->cliente = " AND vista_otfc.nom_clie = '".$agro_cliente."' ";
		}else{
			$this->cliente = "";
		}
	}
}


class Consulta_descarga extends Filtros_des
{

/* ----------------------- FUNCION PARA MOSTRAR TONELADAS EN EL WIDGETS ----------------------- */
	public function widgets_ton_descargas($agro_plaza)
	{
		$conn = conexion::conectar();

		$historial = $this->historial;
		$almacen = $this->almacen;
		$cliente =  $this->cliente;

		/*----------INICIA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
		if($agro_plaza == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					$sql_ton_descarga = "SELECT SUM(vista_otfc.det_t_netas  ) AS t_netas_oracba FROM vista_dashboard_otfc vista_otfc
							  WHERE vista_otfc.pla_r_social = '".$agro_plaza."' AND vista_otfc.det_status = 2 ".$historial.$almacen.$cliente;
					break;
				case 'OCCIDENTE (ARGO)':
					$sql_ton_descarga = "SELECT SUM(vista_otfc.det_t_netas) AS t_netas_orar06 FROM vista_dashboard_otfc vista_otfc
							  WHERE vista_otfc.pla_r_social = '".$agro_plaza."' AND vista_otfc.det_status = 2 ".$historial.$almacen.$cliente;
					break;
			}
		}else{
			$sql_ton_descarga = "SELECT * FROM
							  (SELECT SUM(vista_otfc.det_t_netas  ) AS t_netas_oracba FROM vista_dashboard_otfc vista_otfc
							  WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)' AND vista_otfc.det_status = 2 ".$historial." ),
							  (SELECT SUM(vista_otfc.det_t_netas) AS t_netas_orar06 FROM vista_dashboard_otfc vista_otfc
							  WHERE vista_otfc.pla_r_social = 'OCCIDENTE (ARGO)' AND vista_otfc.det_status = 2 ".$historial." ) ";
		}
		/*----------TERMINA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/

		$stid_widgets_ton = oci_parse($conn, $sql_ton_descarga);
		oci_execute($stid_widgets_ton );

		while (($row = oci_fetch_assoc($stid_widgets_ton)) != false)
		{
			$res_widgets_ton[]=$row;
		}
			oci_free_statement($stid_widgets_ton);
			oci_close($conn);
			return $res_widgets_ton;
	}

/* ----------------------- FUNCION PARA MOSTRAR TOTAL DE SACOS EN EL WIDGETS ----------------------- */
	public function widgets_t_sacos_descarga($agro_plaza)
	{
		$conn = conexion::conectar();

		$historial = $this->historial;
		$almacen = $this->almacen;
		$cliente =  $this->cliente;
		/*----------INICIA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
		if($agro_plaza == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					$sql_t_sacos_descarga = "SELECT vista_otfc.id_plaza AS id_plaza, vista_otfc.pla_siglas AS plaza_sig, vista_otfc.ume_ume AS ume, vista_otfc.otfc_id_ume AS id_ume, vista_otfc.otfc_factor AS factor, SUM(vista_otfc.det_bultos) AS bultos
										 FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = '".$agro_plaza."' AND vista_otfc.det_status = 2 ".$historial.$almacen.$cliente."
										 GROUP BY vista_otfc.id_plaza, vista_otfc.pla_siglas, vista_otfc.ume_ume, vista_otfc.otfc_id_ume, vista_otfc.otfc_factor";
					break;
				case 'OCCIDENTE (ARGO)':
					$sql_t_sacos_descarga = "SELECT vista_otfc.id_plaza AS id_plaza, vista_otfc.pla_siglas AS plaza_sig, vista_otfc.ume_ume AS ume, vista_otfc.otfc_id_ume AS id_ume, vista_otfc.otfc_factor AS factor, SUM(vista_otfc.det_bultos) AS bultos
										 FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = '".$agro_plaza."' AND vista_otfc.det_status = 2 ".$historial.$almacen.$cliente."
										 GROUP BY vista_otfc.id_plaza, vista_otfc.pla_siglas, vista_otfc.ume_ume, vista_otfc.otfc_id_ume, vista_otfc.otfc_factor";
					break;
			}
			#echo $historial;

			$stid_widgets_sacos = oci_parse($conn, $sql_t_sacos_descarga);
			oci_execute($stid_widgets_sacos );

			while (($row = oci_fetch_assoc($stid_widgets_sacos)) != false)
			{
				$res_widgets_sacos[]=$row;
			}
			oci_free_statement($stid_widgets_sacos);
			oci_close($conn);
			return $res_widgets_sacos;

		}else{
			/*------------------------CORDOBA----------------------------*/
			$sql_t_sacos_descarga = "SELECT vista_otfc.id_plaza AS id_plaza, vista_otfc.pla_siglas AS plaza_sig, vista_otfc.ume_ume AS ume, vista_otfc.otfc_id_ume AS id_ume, vista_otfc.otfc_factor AS factor, SUM(vista_otfc.det_bultos) AS bultos
										 FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)' AND vista_otfc.det_status = 2 ".$historial.$almacen.$cliente."
										 GROUP BY vista_otfc.id_plaza, vista_otfc.pla_siglas, vista_otfc.ume_ume, vista_otfc.otfc_id_ume, vista_otfc.otfc_factor";

			$stid_widgets_sacos = oci_parse($conn, $sql_t_sacos_descarga);
			oci_execute($stid_widgets_sacos );

			while (($row = oci_fetch_assoc($stid_widgets_sacos)) != false)
			{
				$res_widgets_sacos[]=$row;
			}
			/*------------------------OCCIDENTE----------------------------*/
			$sql_t_sacos_descarga = "SELECT vista_otfc.id_plaza AS id_plaza, vista_otfc.pla_siglas AS plaza_sig, vista_otfc.ume_ume AS ume, vista_otfc.otfc_id_ume AS id_ume, vista_otfc.otfc_factor AS factor, SUM(vista_otfc.det_bultos) AS bultos
										 FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = 'OCCIDENTE (ARGO)' AND vista_otfc.det_status = 2 ".$historial.$almacen.$cliente."
										 GROUP BY vista_otfc.id_plaza, vista_otfc.pla_siglas, vista_otfc.ume_ume, vista_otfc.otfc_id_ume, vista_otfc.otfc_factor";

			$stid_widgets_sacos = oci_parse($conn, $sql_t_sacos_descarga);
			oci_execute($stid_widgets_sacos );

			while (($row = oci_fetch_assoc($stid_widgets_sacos)) != false)
			{
				$res_widgets_sacos[]=$row;
			}
			/*----------------------------------------------------------*/
			oci_free_statement($stid_widgets_sacos);
			oci_close($conn);
			return $res_widgets_sacos;
		}
		/*----------TERMINA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
	}

/* ----------------------- FUNCION PARA MOSTRAR LISTA DEL HISTORIAL ----------------------- */
	public function select_historial_descarga($agro_plaza)
	{
		$conn = conexion::conectar();

		$almacen = $this->almacen;
		$cliente =  $this->cliente;
		/*---------- INICIA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
		switch ($agro_plaza) {
			case 'CÓRDOBA (ARGO)':
				$sql = "SELECT DISTINCT TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') AS fecha
						FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)' ".$almacen.$cliente."
						ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC";
				break;
			case 'OCCIDENTE (ARGO)':
				$sql = "SELECT DISTINCT TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') AS fecha
						FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = 'OCCIDENTE (ARGO)' ".$almacen.$cliente."
						ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC";
				break;
			default:
				$sql = "SELECT * FROM
						(SELECT DISTINCT TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') AS fecha
						FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
						UNION
						SELECT DISTINCT TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') AS fecha
						FROM vista_dashboard_otfc vista_otfc WHERE vista_otfc.pla_r_social = 'OCCIDENTE (ARGO)')
						ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC";
				break;
		}
		/*----------TERMINA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/

		$stid_historial = oci_parse($conn, $sql);
		oci_execute($stid_historial );

		while (($row = oci_fetch_assoc($stid_historial)) != false)
		{
			$res_historial[]=$row;
		}
			oci_free_statement($stid_historial);
			oci_close($conn);
			return $res_historial;
	}

	/* ----------------------- FUNCION PARA MOSTRAR SELECT FILTRO ALMACEN ----------------------- */
	public function select_almacen($agro_plaza)
	{
		$conn = conexion::conectar();

		$historial = $this->historial;
		$historial2 = $this->historial2;

		switch ($agro_plaza) {
			case 'CÓRDOBA (ARGO)':
				$sql_select_almacen = "SELECT DISTINCT vista_otfc.alm_nom AS almacen, vista_otfc.alm_id_alm AS id_almacen, vista_otfc.id_plaza AS id_plaza
									   FROM vista_dashboard_otfc vista_otfc
									   WHERE vista_otfc.pla_r_social = '".$agro_plaza."' ".$historial ."
										 UNION ALL
											SELECT DISTINCT vista_otfc.alm_nom    AS almacen,
											                vista_otfc.alm_id_alm AS id_almacen,
											                vista_otfc.id_plaza   AS id_plaza
											  FROM vista_dashboard_otfc vista_otfc
											 WHERE vista_otfc.pla_r_social = '".$agro_plaza."' ".$historial2 ."
											   AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy')  >=  '2021'
											   AND vista_otfc.vh_reci_det_recibo is null ";
				break;
			case 'OCCIDENTE (ARGO)':
				$sql_select_almacen = "SELECT DISTINCT vista_otfc.alm_nom AS almacen, vista_otfc.alm_id_alm AS id_almacen, vista_otfc.id_plaza AS id_plaza
									   FROM vista_dashboard_otfc vista_otfc
									   WHERE vista_otfc.pla_r_social = '".$agro_plaza."' ".$historial ." UNION ALL
											SELECT DISTINCT vista_otfc.alm_nom    AS almacen,
											                vista_otfc.alm_id_alm AS id_almacen,
											                vista_otfc.id_plaza   AS id_plaza
											  FROM vista_dashboard_otfc vista_otfc
											 WHERE vista_otfc.pla_r_social = '".$agro_plaza."' ".$historial2 ."
											   AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy')  >=  '2021'
											   AND vista_otfc.vh_reci_det_recibo is null ";
				break;
		}
		################################################################################################################################################################################################################################
		####################################################################################################diego altamirano suarez ####################################################################################################
		################################################################################################################################################################################################################################
		// Preparar la sentencia
		#echo $sql_select_almacen;
		$stid_sel_almacen = oci_parse($conn, $sql_select_almacen);
		oci_execute($stid_sel_almacen);

		while ( ($row = oci_fetch_assoc($stid_sel_almacen)) != false ) {
			$res_select_almacen[]=$row;
		}
		oci_free_statement($stid_sel_almacen);
		oci_close($conn);
 		return $res_select_almacen;
	}

/* ----------------------- FUNCION PARA MOSTRAR SELECT FILTRO CLIENTE ----------------------- */
	public function select_cliente($agro_plaza,$agro_almacen)
	{
		$historial = $this->historial;
		$almacen = $this->almacen;
		$conn = conexion::conectar();

		if ($agro_almacen == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					$sql_select_cliente = " SELECT DISTINCT vista_otfc.clie_id_clie AS id_cliente, vista_otfc.nom_clie AS cliente
											FROM vista_dashboard_otfc vista_otfc
											WHERE vista_otfc.pla_r_social =  '".$agro_plaza."' ".$historial.$almacen;
					break;
				case  'OCCIDENTE (ARGO)':
					$sql_select_cliente = " SELECT DISTINCT vista_otfc.clie_id_clie AS id_cliente, vista_otfc.nom_clie AS cliente
											FROM vista_dashboard_otfc vista_otfc
											WHERE vista_otfc.pla_r_social =  '".$agro_plaza."' ".$historial.$almacen;
					break;
			}
		}

		$stid_sel_cliente = oci_parse($conn, $sql_select_cliente);
		oci_execute($stid_sel_cliente);

		while ( ($row = oci_fetch_assoc($stid_sel_cliente)) != false ) {
			$res_select_cliente[]=$row;
		}

		oci_free_statement($stid_sel_cliente);
		oci_close($conn);
 		return $res_select_cliente;

	}

}

class Consulta_status_descarga extends Filtros_des
{
	/* ----------------------- FUNCION PARA CONSULTAR DATOS DE DESCARGAS ----------------------- */
	public function consulta_descarga_status($agro_plaza,$par,$ofc,$fol)
	{
		$historial = $this->historial;
		$historial2 = $this->historial2;
		$historial3 = $this->historial3;
		$almacen = $this->almacen;
		$cliente = $this->cliente;

		$conn = conexion::conectar();

		$sql = "SELECT
				TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy HH24:MI:SS') AS r_vehiculo, vista_otfc.det_partida AS partida, vista_otfc.det_ref_otfc AS otfc, det_folio AS folio_det
				,vista_otfc.det_remision AS remision, vista_otfc.det_transportista AS transporte, vista_otfc.det_c_porte AS c_porte, vista_otfc.det_placas AS placas, vista_otfc.det_chofer AS chofer,
				vista_otfc.det_t_netas AS ton_netas, vista_otfc.det_bultos AS bultos, vista_otfc.ume_ume AS ume, vista_otfc.otfc_factor AS factor, vista_otfc.det_vehiculo AS vehiculo, TO_CHAR(vista_otfc.det_fecha_peso, 'dd-mm-yyyy HH24:MI:SS') AS bascula_vehiculo, TO_CHAR(vista_otfc.det_fecha_reg, 'dd-mm-yyyy HH24:MI:SS') AS inicia_descarga, TO_CHAR(vista_otfc.det_fecha_neto, 'dd-mm-yyyy HH24:MI:SS') AS termina_descarga,
				vista_otfc.det_status AS status_otfc_det
				,TO_CHAR(vista_otfc.otfc_fecha, 'dd-mm-yyyy') AS fecha_otfc, vista_otfc.otfc_obs AS observaciones, vista_otfc.otfc_folio AS folio_otfc, vista_otfc.otfc_parte AS num_parte, vista_otfc.des_parte AS des_parte, vista_otfc.otfc_status AS status_otfc, vista_otfc.id_otfc AS id_otfc
				,vista_otfc.nom_clie AS cliente, vista_otfc.pla_r_social AS plaza, vista_otfc.alm_nom AS almacen, vista_otfc.id_plaza AS id_plaza
				,vista_otfc.vh_reci_pbruto AS veh_pbruto, vista_otfc.vh_reci_ptara AS veh_ptara, vista_otfc.vh_reci_pneto AS veh_pneto
				,vista_otfc.vid_movto_idveh AS mov_id_vehiculo, vista_otfc.vid_movto_bulto AS mov_bulto, vista_otfc.vh_reci_det_recibo AS recibo_det, vista_otfc.vh_reci_det_id_veh AS id_vehiculo, vista_otfc.vh_reci_det_bulto AS id_bulto";

		if ($agro_plaza == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					if ($par == true && $ofc == true && $fol == true){
						$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   	   WHERE vista_otfc.det_partida= '".$par."' AND vista_otfc.det_ref_otfc = '".$ofc."' AND vista_otfc.det_folio = '".$fol."' ";
					}else{
						$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   WHERE vista_otfc.pla_r_social = '".$agro_plaza."' AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
											   ".$historial.$almacen.$cliente." UNION ALL
											    ".$sql. " FROM vista_dashboard_otfc vista_otfc
													 WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
													   AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
													   ".$historial2.$almacen.$cliente."
													   AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy') = '2021'
													   AND VISTA_OTFC.vh_reci_det_recibo is  null
														 UNION ALL
 	 												 ".$sql. " FROM vista_dashboard_otfc vista_otfc
 	 													WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
 	 														AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
 	 														".$historial3.$almacen.$cliente."
 	 														AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy') = '2021'
 	 														AND VISTA_OTFC.vh_reci_det_recibo is NOT null ";

														 /*UNION ALL
 	 												 ".$sql. " FROM vista_dashboard_otfc vista_otfc
 	 													WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
 	 														AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
 	 														".$historial3.$almacen.$cliente."
 	 														AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy') = '2021'
 	 														AND VISTA_OTFC.vh_reci_det_recibo is NOT null  */

													//ORDER BY vista_otfc.det_fecha_envio DESC
													//AND TO_CHAR(vista_otfc.det_fecha_envio, 'dd-mm-yyyy') < '30-03-2021'
					}
				break;
				case  'OCCIDENTE (ARGO)':
					if ($par == true && $ofc == true && $fol == true){
					$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   	   WHERE vista_otfc.det_partida= '".$par."' AND vista_otfc.det_ref_otfc = '".$ofc."' AND vista_otfc.det_folio = '".$fol."' ";
					}else{
					$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   WHERE vista_otfc.pla_r_social = '".$agro_plaza."' AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
											   ".$historial.$almacen.$cliente."
												 UNION ALL
												 ".$sql. " FROM vista_dashboard_otfc vista_otfc
													WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
														AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
														".$historial2.$almacen.$cliente."
														AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy') = '2021'
														AND VISTA_OTFC.vh_reci_det_recibo is  null
														UNION ALL
														".$sql. " FROM vista_dashboard_otfc vista_otfc
														WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
														 AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
														 ".$historial3.$almacen.$cliente."
														 AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy') = '2021'
														 AND VISTA_OTFC.vh_reci_det_recibo is NOT null ";/*
														 UNION ALL
														 ".$sql. " FROM vista_dashboard_otfc vista_otfc
														 WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)'
														 	AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
														 	".$historial3.$almacen.$cliente."
														 	AND TO_CHAR(vista_otfc.det_fecha_envio, 'yyyy') = '2021'
														 	AND VISTA_OTFC.vh_reci_det_recibo is NOT null
														 */
					}
					break;
			}
			#echo $historial2;
	    #echo $sql_con_descarga;

			$stid_con_descarga = oci_parse($conn, $sql_con_descarga);
			oci_execute($stid_con_descarga);

			while ( ($row = oci_fetch_assoc($stid_con_descarga)) != false ) {
				$res_con_descargas[]=$row;
			}

			oci_free_statement($stid_con_descarga);
			oci_close($conn);
	 		return $res_con_descargas;
		}else{
			/*PLAZA CORDOBA*/
			if ($par == true && $ofc == true && $fol == true){
			$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   	   WHERE vista_otfc.det_partida= '".$par."' AND vista_otfc.det_ref_otfc = '".$ofc."' AND vista_otfc.det_folio = '".$fol."' ";
			}else{
			$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   WHERE vista_otfc.pla_r_social = 'CÓRDOBA (ARGO)' AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
											   ".$historial.$almacen.$cliente."
											   ORDER BY vista_otfc.det_fecha_envio DESC ";
			}

			$stid_con_descarga = oci_parse($conn, $sql_con_descarga);
			oci_execute($stid_con_descarga);

			while ( ($row = oci_fetch_assoc($stid_con_descarga)) != false ) {
				$res_con_descargas[]=$row;
			}
			/*PLAZA OCCIDENTE*/
			if ($par == true && $ofc == true && $fol == true){
			$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   	   WHERE vista_otfc.det_partida= '".$par."' AND vista_otfc.det_ref_otfc = '".$ofc."' AND vista_otfc.det_folio = '".$fol."' ";
			}else{
			$sql_con_descarga = $sql." FROM vista_dashboard_otfc vista_otfc
											   WHERE vista_otfc.pla_r_social = 'OCCIDENTE (ARGO)' AND vista_otfc.id_otfc_det = vista_otfc.id_otfc
											   ".$historial.$almacen.$cliente."
											   ORDER BY vista_otfc.det_fecha_envio DESC ";
			}


			$stid_con_descarga = oci_parse($conn, $sql_con_descarga);
			oci_execute($stid_con_descarga);

			while ( ($row = oci_fetch_assoc($stid_con_descarga)) != false ) {
				$res_con_descargas[]=$row;
			}
			/*------------------------*/

			oci_free_statement($stid_con_descarga);
			oci_close($conn);
	 		return $res_con_descargas;

		}

	}

	public function consulta_descarga_mov($plaza_mov_des,$vid_recibo_des,$vid_movto_des){

		$conn = conexion::conectar();

		switch ($plaza_mov_des) {
			case 3:
				$tabla_sql = " op_in_movimientos ";
				break;
			case 17:
				$tabla_sql = " op_in_movimientos ";
				break;
		}

		$sql_movimiento_des = " SELECT to_char(mov.d_fecha_mvto, 'dd-mm-yyyy HH24:MI:SS') AS pre_registro
            FROM ".$tabla_sql." mov
            WHERE mov.vid_recibo = '".$vid_recibo_des."' AND mov.v_referencia = '".$vid_recibo_des."'
            and mov.vid_movto like  '".$vid_movto_des."%' ";

						#echo $sql_movimiento_des;
 		$stid_cto_new = oci_parse($conn, $sql_movimiento_des);
            oci_execute($stid_cto_new);
            $row_movimiento_des = oci_fetch_array($stid_cto_new, OCI_BOTH);

            return $row_movimiento_des[0];

	}
}


class Consulta_mov_descarga
{

	public function consulta_descarga_mov($plaza_mov_des,$vid_recibo_des,$vid_movto_des){

		$conn = conexion::conectar();

		switch ($plaza_mov_des) {
			case 3:
				$tabla_sql = " op_in_movimientos ";
				break;
			case 17:
				$tabla_sql = " op_in_movimientos ";
				break;
		}

		$sql_movimiento_des = " SELECT to_char(mov.d_fecha_mvto, 'dd-mm-yyyy HH24:MI:SS') AS pre_registro
            FROM ".$tabla_sql." mov
            WHERE mov.vid_recibo = '".$vid_recibo_des."' AND mov.v_referencia = '".$vid_recibo_des."'
            and mov.vid_movto like  '".$vid_movto_des."%' ";


 		$stid_cto_new = oci_parse($conn, $sql_movimiento_des);
            oci_execute($stid_cto_new);
            $row_movimiento_des = oci_fetch_array($stid_cto_new, OCI_BOTH);

            return $row_movimiento_des[0];

	}
}
