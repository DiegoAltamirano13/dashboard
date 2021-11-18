<?php
/**
* © Argo Almacenadora ®
* Fecha: 19/01/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Operaciones Manufacturas
* Version --
*/
include_once '../libs/conOra.php';

//_°_°_°_°_°_°_°_°_°_°_°_°°_°_°_°_°_°_°INICIA CLASE PARA VER EL TOTAL DE DESCARGAS(ARRIBOS) PLAZA CORDOBA _°_°_°_°_°_°_°_°__°_°_°_°_°_°_°_°°_°
class Info_gral_manufactura
{

	function __construct()
	{
		$res_info_gral_cor = array();
	}
	// -_-_-_-_-_-_-_-_- INICIA METODO PARA INFORMACION GENERAL PARA GRAFICAS PLAZA CORDOBA -_-_-_-_-_-_-_-_-
	public function info_gral_car_des_otr_cor($dia_manufac,$select_manufac_global_plaza,$fec_ini_per_manufac,$fec_fin_per_manufac)
	{

		$conn = conexion::conectar();

		/* SQL CONCATENACION */
		if ($dia_manufac == true){
			switch (true) {
				case ( $fec_ini_per_manufac == true ) && ( $fec_fin_per_manufac == true ):
					$and_fec_op_car_des = " AND s_c_d.d_fec_recepcion >= trunc(to_date('".$fec_ini_per_manufac."','dd-mm-yyyy') ) AND s_c_d.d_fec_recepcion < trunc(to_date('".$fec_fin_per_manufac."','dd-mm-yyyy') ) +1 ";
					$and_fec_op_otr = " AND s_o.d_fecha_registro >= trunc(to_date('".$fec_ini_per_manufac."','dd-mm-yyyy') ) AND s_o.d_fecha_registro < trunc(to_date('".$fec_fin_per_manufac."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_fec_op_car_des = " AND TRUNC(s_c_d.d_fec_recepcion) = TO_DATE('".$dia_manufac."','dd-mm-yyyy') ";
					$and_fec_op_otr = " AND TRUNC(s_o.d_fecha_registro) = TO_DATE('".$dia_manufac."','dd-mm-yyyy') ";
					break;
			}
		}
		/* SQL CONCATENACION */

		$select_cd = " SELECT s_c_d.n_status AS id_status, decode(to_char(s_c_d.n_status),1,'REGISTRADO',2,'LLEGA VEHICULO',3,'INICIA CARGA',4,'FINALIZA CARGA',5,'DESPACHO VEHICULO',6,'CANCELACION','DESCONOCIDO') AS status, s_c_d.id_anden AS anden
				,s_c_d.id_tipo AS tipo_op, to_char(s_c_d.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS registrado, to_char(s_c_d.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS h_llegada, to_char(s_c_d.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS h_despacho
				,s_c_d.id_plaza AS plaza_id, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_sig,
				s_c_d.id_almacen AS almacen_id, alm.v_nombre  AS almacen,
				s_c_d.id_cliente AS cliente_id, cli.v_razon_social AS cliente, s_c_d.id_solicitud AS solicitud ";
		$select_o = " SELECT s_o.n_status AS id_status, decode(to_char(s_o.n_status),1,'PENDIENTE',2,'INICIO',3,'EN PROCESO',4,'CONCLUIDO',5,'CANCELADO','DESCONOCIDO') AS status, s_o.id_anden AS anden
    			,s_o.id_tipo_operacion AS tipo_op, to_char(s_o.d_fecha_registro, 'dd-mm-yyyy HH24:MI:SS') AS registrado, to_char(s_o.d_fec_proceso, 'dd-mm-yyyy HH24:MI:SS') AS h_llegada, to_char(s_o.d_fec_concluido, 'dd-mm-yyyy HH24:MI:SS') AS h_despacho
				,s_o.id_plaza AS plaza_id, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_sig,
				s_o.id_almacen AS almacen_id, alm.v_nombre  AS almacen,
				s_o.id_cliente AScliente_id, cli.v_razon_social AS cliente, s_o.id_solicitud AS solicitud ";


	//-- ================================= PLAZA CORDOBA =================================--//
	if ( $select_manufac_global_plaza[0] == 3){
		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 3
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 3
				AND s_c_d.n_virtual is null or s_c_d.n_virtual = 0
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA MEXICO =================================--//
	if ( $select_manufac_global_plaza[1] == 4){
		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 4
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 1
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
				UNION
				".$select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 4
				AND s_c_d.iid_regimen = 2
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 4
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";


		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA GOLFO =================================--//
	if ( $select_manufac_global_plaza[2] == 5){

		$sql = $select_cd." FROM op_in_solicitud_carga_descarga  s_c_d
				INNER JOIN plaza  pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen  alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente  cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 5
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 1
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
				UNION
				".$select_cd." FROM op_in_solicitud_carga_descarga  s_c_d
				INNER JOIN plaza  pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen  alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente  cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 5
				AND s_c_d.iid_regimen = 2
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 5
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA PENINSULA =================================--//
	if ( $select_manufac_global_plaza[3] == 6 ){

		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 6
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 6
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA PUEBLA =================================--//
	if ( $select_manufac_global_plaza[4] == 7 ){

		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 7
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 1
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
				UNION
				".$select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 7
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 2
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 7
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA BAJIO =================================--//
	if ( $select_manufac_global_plaza[5] == 8 ){

		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 8
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 1
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
				UNION
				".$select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 8
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 2
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 8
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";

				#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA OCCIDENTE =================================--//
	if ( $select_manufac_global_plaza[6] == 17 ){

		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza  pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen  alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente  cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 17
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 1
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
				UNION
				".$select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 17
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.iid_regimen = 2
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 17
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}
	//-- ================================= PLAZA NORESTE =================================--//
	if ( $select_manufac_global_plaza[7] == 18 ){

		$sql = $select_cd." FROM op_in_solicitud_carga_descarga s_c_d
				INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_c_d.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_c_d.id_cliente
				WHERE s_c_d.id_plaza = 18
				AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				".$and_fec_op_car_des."
    			UNION ALL
				".$select_o."
				FROM op_in_solicitud_otros s_o
				INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
				INNER JOIN almacen alm ON alm.iid_almacen = s_o.id_almacen
				INNER JOIN cliente cli ON cli.iid_num_cliente = s_o.id_cliente
				WHERE s_o.id_plaza = 18
				AND CLI.IID_NUM_CLIENTE = 2905
				".$and_fec_op_otr."
				";

				//



		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_info_gral_cor[]=$row;
		}

	}

		oci_free_statement($stid);
		oci_close($conn);
		return $this->res_info_gral_cor;

	}

/* ============ INICIA FUNCION PARA CALCULAR TIEMPO DE FECHAS ============ */
	function calculo_tiempo($fechaInicio,$fechaFin)
	{
	    $fecha1 = new DateTime($fechaInicio);
	    $fecha2 = new DateTime($fechaFin);
	    $fecha = $fecha1->diff($fecha2);
	    $tiempo = "";

	    //años
	    if($fecha->y > 0)
	    {
	        $tiempo .= $fecha->y;

	        if($fecha->y == 1)
	            $tiempo .= " año, ";
	        else
	            $tiempo .= " años, ";
	    }

	    //meses
	    if($fecha->m > 0)
	    {
	        $tiempo .= $fecha->m;

	        if($fecha->m == 1)
	            $tiempo .= " mes, ";
	        else
	            $tiempo .= " meses, ";
	    }

	    //dias
	    if($fecha->d > 0)
	    {
	        $tiempo .= $fecha->d;

	        if($fecha->d == 1)
	            $tiempo .= " día, ";
	        else
	            $tiempo .= " días, ";
	    }

	    //horas
	    if($fecha->h > 0)
	    {
	        $tiempo .= $fecha->h;

	        if($fecha->h == 1)
	            $tiempo .= " hora, ";
	        else
	            $tiempo .= " horas, ";
	    }

	    //minutos
	    if($fecha->i > 0)
	    {
	        $tiempo .= $fecha->i;

	        if($fecha->i == 1)
	            $tiempo .= " minuto, ";
	        else
	            $tiempo .= " minutos, ";
	    }

	    if($fecha->s > 0)
	    {
	        $tiempo .= $fecha->s;

	        if($fecha->s == 1)
	            $tiempo .= " segundo";
	        else
	            $tiempo .= " segundos";
	    }

	    // else if($fecha->s == 0) //segundos
	    //     $tiempo .= $fecha->s." segundos";

	    return $tiempo;
	}
/* ============ TERMINA FUNCION PARA CALCULAR TIEMPO DE FECHAS ============ */

/* ============ INICIA FUNCION PARA CALCULAR TIEMPO EN  MINUTOS ============ */
	public function dif_minutos($fechaFin,$fechaInicio)
	{
		$dif_min = (strtotime($fechaFin)-strtotime($fechaInicio))/60;
		return $dif_min;
	}
/* ============ TERMINA FUNCION PARA CALCULAR TIEMPO EN  MINUTOS ============ */

}
// -_-_-_-_-_-_-_-_- TERMINA METODO PARA INFORMACION GENERAL PARA GRAFICAS PLAZA CORDOBA -_-_-_-_-_-_-_-_-

// ***************************** INICIA CLASE HISTORIAL FECHAS DE MANUFACTURA ***************************** //
class Historial_manufac
{

	function __construct()
	{
		$res_historial_manufac = array();
	}

	public function dia_select_manufac($select_manufac_global_plaza)
	{
		$conn = conexion::conectar();

	//-----------------------PLAZA CORDOBA-----------------------//
	if ( $select_manufac_global_plaza[0] == 3 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 3	AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
				AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 3 )
				AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA MEXICO-----------------------//
	if ( $select_manufac_global_plaza[1] == 4 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 4 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 1 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 4 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 2 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 4 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA GOLFO-----------------------//
	if ( $select_manufac_global_plaza[2] == 5 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 5 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 1 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 5 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 2 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 5 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA PENINSULA-----------------------//
	if ( $select_manufac_global_plaza[3] == 6 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 6 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 6  AND s_c_d.n_virtual is null or s_c_d.n_virtual = 0 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA PUEBLA-----------------------//
	if ( $select_manufac_global_plaza[4] == 7 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 7 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 1 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 7 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 2 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 7 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA BAJIO-----------------------//
	if ( $select_manufac_global_plaza[5] == 8 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 8 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 1 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 8 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 2 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 8 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA OCCIDENTE-----------------------//
	if ( $select_manufac_global_plaza[6] == 17 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 17 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 1 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 17 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.iid_regimen = 2 AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 17 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}
	//-----------------------PLAZA NORESTE-----------------------//
	if ( $select_manufac_global_plaza[7] == 18 ){
		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 18 AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0) AND s_c_d.id_cliente = 2905
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 18 ) AND s_c_d.id_cliente = 2905
				ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_manufac[]=$row;
		}
	}

	#echo $sql;
	//-----------------------PLAZA LEON-----------------------//
	// if ( $select_manufac_global_plaza[8] == 23 ){
	// 	$sql = "SELECT * FROM
	// 			(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
	// 			FROM op_in_solicitud_carga_descarga s_c_d WHERE s_c_d.id_plaza = 23
	// 			UNION
	// 			SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
	// 			FROM op_in_solicitud_otros s_o WHERE s_o.id_plaza = 23 ) ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC ";
	// 	$stid = oci_parse($conn, $sql);
	// 	oci_execute($stid );

	// 	while (($row = oci_fetch_assoc($stid)) != false)
	// 	{
	// 		$this->res_historial_manufac[]=$row;
	// 	}
	// }


			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_manufac;
	}

}
// ***************************** TERMINA CLASE HISTORIAL FECHAS DE MANUFACTURA ***************************** //
