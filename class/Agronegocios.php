<?php
/**
* © Argo Almacenadora ®
* Fecha: 27/07/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Agronegocios
* Version --
*/
include_once "../libs/conOra.php";

class Filtros
{
	public $historial;
	public $almacen;
	public $cliente;

	function __construct($agro_plaza,$agro_historial, $fec_ini_agro, $fec_fin_agro, $agro_almacen, $agro_cliente)
	{
		//-----------------------------FILTRO POR FECHA-----------------------------//
		switch (true) {
			case ($agro_historial == true) && ($fec_ini_agro == true) && ($fec_fin_agro == true):
				$this->historial = " AND vista_ofc.det_fecha_salida >= trunc(to_date('".$fec_ini_agro."','dd-mm-yyyy') )
						   AND vista_ofc.det_fecha_salida < trunc(to_date('".$fec_fin_agro ."','dd-mm-yyyy') ) +1 ";
				break;
			default:
				$this->historial = " AND TO_CHAR(vista_ofc.det_fecha_salida, 'dd-mm-yyyy') = '".$agro_historial."' ";
				break;
		}
		//-----------------------------FILTRO POR ALMACEN-----------------------------//
		if ($agro_almacen == true){
			$this->almacen = " AND vista_ofc.alm_nom = '".$agro_almacen."' ";
		}else{
			$this->almacen = "";
		}
		//-----------------------------FILTRO POR CLIENTE-----------------------------//
		if ($agro_cliente == true){
			$this->cliente = " AND vista_ofc.nom_clie = '".$agro_cliente."' ";
		}else{
			$this->cliente = "";
		}
	}
}


class Consulta_carga extends Filtros
{

/* ----------------------- FUNCION PARA MOSTRAR TONELADAS EN EL WIDGETS ----------------------- */
	public function widgets_ton_cargas($agro_plaza)
	{
		$conn = conexion::conectar();

		$historial = $this->historial;
		$cliente =  $this->cliente;
		$almacen = $this->almacen;
		/*----------INICIA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
		if($agro_plaza == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					$sql_ton_carga = "SELECT SUM(vista_ofc.det_t_netas  ) AS t_netas_oracba
							  FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = '".$agro_plaza."' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente;
					break;
				case 'OCCIDENTE (ARGO)':
					$sql_ton_carga = "SELECT SUM(vista_ofc.det_t_netas) AS t_netas_orar06
							  FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = '".$agro_plaza."' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente;
					break;
			}
		}else{
			$sql_ton_carga = "SELECT * FROM
							  (SELECT SUM(vista_ofc.det_t_netas  ) AS t_netas_oracba
							  FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = 'CÓRDOBA (ARGO)' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente." ),
							  (SELECT SUM(vista_ofc.det_t_netas) AS t_netas_orar06
							  FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = 'OCCIDENTE (ARGO)' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente." ) ";
		}
		/*----------TERMINA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/

		$stid_widgets_ton = oci_parse($conn, $sql_ton_carga);
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
	public function widgets_t_sacos_carga($agro_plaza)
	{
		$conn = conexion::conectar();

		$historial = $this->historial;
		$almacen = $this->almacen;
		$cliente =  $this->cliente;
		/*----------INICIA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
		if($agro_plaza == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					$sql_t_sacos_carga = "SELECT vista_ofc.id_plaza AS id_plaza, vista_ofc.pla_siglas AS plaza_sig, vista_ofc.ume AS ume, vista_ofc.id_um AS id_ume, vista_ofc.factor 	AS factor, SUM(vista_ofc.det_bultos) AS bultos
										  FROM vista_dashboard_ofc vista_ofc
            							  WHERE vista_ofc.pla_r_social = '".$agro_plaza."' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente."
										  GROUP BY vista_ofc.id_plaza, vista_ofc.pla_siglas, vista_ofc.ume, vista_ofc.id_um, vista_ofc.factor";
					break;
				case 'OCCIDENTE (ARGO)':
					$sql_t_sacos_carga = "SELECT vista_ofc.id_plaza AS id_plaza, vista_ofc.pla_siglas AS plaza_sig, vista_ofc.ume AS ume, vista_ofc.id_um AS id_ume, vista_ofc.factor 	AS factor, SUM(vista_ofc.det_bultos) AS bultos
										  FROM vista_dashboard_ofc vista_ofc
            							  WHERE vista_ofc.pla_r_social = '".$agro_plaza."' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente."
										  GROUP BY vista_ofc.id_plaza, vista_ofc.pla_siglas, vista_ofc.ume, vista_ofc.id_um, vista_ofc.factor";
					break;
			}

			$stid_widgets_sacos = oci_parse($conn, $sql_t_sacos_carga);
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
			$sql_t_sacos_carga = "SELECT vista_ofc.id_plaza AS id_plaza, vista_ofc.pla_siglas AS plaza_sig, vista_ofc.ume AS ume, vista_ofc.id_um AS id_ume, vista_ofc.factor 	AS factor, SUM(vista_ofc.det_bultos) AS bultos
								  FROM vista_dashboard_ofc vista_ofc
    							  WHERE vista_ofc.pla_r_social = 'CÓRDOBA (ARGO)' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente."
								  GROUP BY vista_ofc.id_plaza, vista_ofc.pla_siglas, vista_ofc.ume, vista_ofc.id_um, vista_ofc.factor";

			$stid_widgets_sacos = oci_parse($conn, $sql_t_sacos_carga);
			oci_execute($stid_widgets_sacos );

			while (($row = oci_fetch_assoc($stid_widgets_sacos)) != false)
			{
				$res_widgets_sacos[]=$row;
			}
			/*------------------------OCCIDENTE----------------------------*/
			$sql_t_sacos_carga = "SELECT vista_ofc.id_plaza AS id_plaza, vista_ofc.pla_siglas AS plaza_sig, vista_ofc.ume AS ume, vista_ofc.id_um AS id_ume, vista_ofc.factor 	AS factor, SUM(vista_ofc.det_bultos) AS bultos
								  FROM vista_dashboard_ofc vista_ofc
    							  WHERE vista_ofc.pla_r_social = 'OCCIDENTE (ARGO)' AND vista_ofc.det_status = 2 ".$historial.$almacen.$cliente."
								  GROUP BY vista_ofc.id_plaza, vista_ofc.pla_siglas, vista_ofc.ume, vista_ofc.id_um, vista_ofc.factor";

			$stid_widgets_sacos = oci_parse($conn, $sql_t_sacos_carga);
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
	public function historial_carga($agro_plaza)
	{
		$conn = conexion::conectar();

		/*----------INICIA CONCATENACIÓN DEPENDIENDO SI ELIGIÓ PLAZA----------*/
		switch ($agro_plaza) {
			case 'CÓRDOBA (ARGO)':
				$sql = "SELECT DISTINCT TO_CHAR(ofc.det_fecha_salida, 'dd-mm-yyyy') AS fecha
						FROM vista_dashboard_ofc  ofc ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC";
				break;
			case 'OCCIDENTE (ARGO)':
				$sql = "SELECT DISTINCT TO_CHAR(ofc.det_fecha_salida, 'dd-mm-yyyy') AS fecha
						FROM vista_dashboard_ofc  ofc ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC";
				break;
			default:
				$sql = "SELECT * FROM
						(SELECT TO_CHAR(ofc.d_fecha_salida, 'dd-mm-yyyy') AS fecha
						FROM op_in_ofc_det ofc
						UNION
						SELECT TO_CHAR(ofc.d_fecha_salida, 'dd-mm-yyyy') AS fecha
						FROM op_in_ofc_det  ofc)
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

		switch ($agro_plaza) {
			case 'CÓRDOBA (ARGO)':
				$sql_select_almacen = " SELECT DISTINCT vista_ofc.alm_nom AS almacen, vista_ofc.alm_id_alm AS id_almacen, vista_ofc.id_plaza AS id_plaza
						FROM vista_dashboard_ofc vista_ofc
						WHERE  vista_ofc.pla_r_social = '".$agro_plaza."' ".$historial;
				break;
			case 'OCCIDENTE (ARGO)':
				$sql_select_almacen = " SELECT DISTINCT vista_ofc.alm_nom AS almacen, vista_ofc.alm_id_alm AS id_almacen, vista_ofc.id_plaza AS id_plaza
						FROM vista_dashboard_ofc vista_ofc
						WHERE  vista_ofc.pla_r_social = '".$agro_plaza."' ".$historial;
				break;
		}

		// Preparar la sentencia
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
				$sql_select_cliente = " SELECT DISTINCT vista_ofc.clie_id_clie AS id_cliente, vista_ofc.nom_clie AS cliente
										FROM vista_dashboard_ofc vista_ofc
										WHERE  vista_ofc.pla_r_social = '".$agro_plaza."' ".$historial.$almacen;
				break;
			case  'OCCIDENTE (ARGO)':
				$sql_select_cliente = " SELECT DISTINCT vista_ofc.clie_id_clie AS id_cliente, vista_ofc.nom_clie AS cliente
										FROM vista_dashboard_ofc vista_ofc
										WHERE  vista_ofc.pla_r_social = '".$agro_plaza."' ".$historial.$almacen;
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

/**
*
*/
class Consulta_ofc extends Filtros
{

	/* ----------------------- FUNCION PARA CONSULTAR DATOS DE CARGAS ----------------------- */
	public function consulta_carga($agro_plaza)
	{
		$historial = $this->historial;
		$almacen = $this->almacen;
		$cliente = $this->cliente;

		$conn = conexion::conectar();

		$sql = "SELECT TO_CHAR(vista_ofc.det_fecha_salida, 'dd-mm-yyyy HH24:MI:SS') AS r_vehiculo, vista_ofc.det_partida AS partida, vista_ofc.det_ref_ofc AS ofc, det_folio AS folio_det
				,vista_ofc.det_remision AS remision, vista_ofc.det_transportista AS transporte, vista_ofc.det_c_porte AS c_porte, vista_ofc.det_placas AS placas, vista_ofc.det_chofer AS chofer,
				vista_ofc.det_t_netas AS ton_netas, vista_ofc.det_bultos AS bultos, vista_ofc.ume AS ume, vista_ofc.factor AS factor,  vista_ofc.det_vehiculo AS vehiculo, TO_CHAR(vista_ofc.det_fecha_tara, 'dd-mm-yyyy HH24:MI:SS') AS bascula_vehiculo, TO_CHAR(vista_ofc.det_fecha_reg, 'dd-mm-yyyy HH24:MI:SS') AS inicia_carga,
				TO_CHAR(vista_ofc.det_fecha_neto, 'dd-mm-yyyy HH24:MI:SS') AS termina_carga, vista_ofc.det_status AS status_ofc_det
				,TO_CHAR(vista_ofc.ofc_fecha, 'dd-mm-yyyy') AS fecha_ofc, vista_ofc.ofc_obs AS observaciones, vista_ofc.ofc_folio AS folio_ofc, vista_ofc.ofc_parte AS num_parte, vista_ofc.des_parte AS des_parte, vista_ofc.ofc_status AS status_ofc, vista_ofc.id_ofc AS id_ofc
				,vista_ofc.nom_clie AS cliente, vista_ofc.pla_r_social AS plaza, vista_ofc.alm_nom AS almacen, TO_CHAR(vista_ofc.ofc_salida_fecha, 'dd-mm-yyyy HH24:MI:SS') AS fecha_documentacion, vista_ofc.id_plaza AS id_plaza";

		if ($agro_plaza == true){
			switch ($agro_plaza) {
				case 'CÓRDOBA (ARGO)':
					$sql_con_carga = $sql." FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = '".$agro_plaza."' AND vista_ofc.id_ofc_det = vista_ofc.id_ofc ".$historial.$almacen.$cliente;
					break;
				case  'OCCIDENTE (ARGO)':
					$sql_con_carga = $sql." FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = '".$agro_plaza."' AND vista_ofc.id_ofc_det = vista_ofc.id_ofc ".$historial.$almacen.$cliente;
					break;
			}

			$stid_con_carga = oci_parse($conn, $sql_con_carga);
			oci_execute($stid_con_carga);

			while ( ($row = oci_fetch_assoc($stid_con_carga)) != false ) {
				$res_con_cargas[]=$row;
			}

			oci_free_statement($stid_con_carga);
			oci_close($conn);
	 		return $res_con_cargas;
		}else{
			/*PLAZA CORDOBA*/
			$sql_con_carga = $sql." FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = 'CÓRDOBA (ARGO)' AND vista_ofc.id_ofc_det = vista_ofc.id_ofc ".$historial.$almacen.$cliente;

			$stid_con_carga = oci_parse($conn, $sql_con_carga);
			oci_execute($stid_con_carga);

			while ( ($row = oci_fetch_assoc($stid_con_carga)) != false ) {
				$res_con_cargas[]=$row;
			}
			/*PLAZA OCCIDENTE*/
			$sql_con_carga = $sql." FROM vista_dashboard_ofc vista_ofc WHERE vista_ofc.pla_r_social = 'OCCIDENTE (ARGO)' AND vista_ofc.id_ofc_det = vista_ofc.id_ofc ".$historial.$almacen.$cliente;

			$stid_con_carga = oci_parse($conn, $sql_con_carga);
			oci_execute($stid_con_carga);

			while ( ($row = oci_fetch_assoc($stid_con_carga)) != false ) {
				$res_con_cargas[]=$row;
			}
			/*------------------------*/

			oci_free_statement($stid_con_carga);
			oci_close($conn);
	 		return $res_con_cargas;

		}



	}


}

?>
