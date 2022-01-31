<?php
/**
* © Argo Almacenadora ®
* Fecha: 19/01/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Operaciones Manufacturas
* Version --
*/
include_once '../libs/conOra.php';


//_°_°_°_°_°_°_°_°_°_°_°_°_°_°_°_° INICIA CLASE PARA VER EL TOTAL DELA INFO CARGAS(SALIDAS) _°_°_°_°_°_°_°_°__°_°_°_°_
class Op_in_car_des_info
{

	function __construct()
	{
		$res_cargas_info = array();
	}
	// -_-_-_-_-_-_-_-_- INICIA METODO PARA INFORMACION CARGAS(SALIDAS) 1 -_-_-_-_-_-_-_-_-
	public function car_des_info($plaza_manufac,$dia_manufac,$fec_ini_per_manufac,$fec_fin_per_manufac,$select_manufac_global_plaza)
	{
		/* CONCATENACION AND SQL */	
		if ($dia_manufac == true){
			switch (true) {
				case ( $fec_ini_per_manufac == true ) && ( $fec_fin_per_manufac == true ):
					$and_sql_fecha_op_manufac = " AND cargas.d_fec_recepcion >= trunc(to_date('".$fec_ini_per_manufac."','dd-mm-yyyy') ) AND cargas.d_fec_recepcion < trunc(to_date('".$fec_fin_per_manufac."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_sql_fecha_op_manufac = " AND TRUNC(cargas.d_fec_recepcion) = TO_DATE('".$dia_manufac."','dd-mm-yyyy') ";
					break;
			}
		}
		/* CONCATENACION AND SQL */
		$select_cd = " SELECT pla.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, alm.v_nombre AS almacen, cargas.id_almacen AS almacen_id, cli.v_razon_social AS rs, cli.v_nombre_corto AS cliente, cargas.n_status AS status,
				to_char(cargas.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS registrado, to_char(cargas.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS llega,
				to_char(cargas.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia, to_char(cargas.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS fin,
    			to_char(cargas.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho
				,cargas.id_tipo AS tipo, cargas.v_observaciones_salida AS obs, cargas.v_observaciones_cancelacion AS obs_can, veh.v_descripcion AS vehiculo, cargas.v_placas_vehiculo_real AS placas1, cargas.v_placas_vehiculo_dos AS placas2, cargas.id_anden AS anden
				,ume.v_descripcion AS ume, cargas.n_cantidad_ume AS cantidad, per.v_nombre AS almacenista_n, per.v_ape_pat AS almacenista_p, per.v_ape_mat AS almacenista_m
				,cargas.id_solicitud AS solicitud, cargas.iid_arr_ret AS arribo, (SELECT v.v_translado
        FROM op_in_retiros_nad_enc T,
             op_in_solicitud_carga_descarga s,
             op_in_retiros_nad_det u,
             op_in_vehiculos_recibidos v
        WHERE t.v_id = s.id_solicitud
              and t.iid_retiro = u.iid_retiro
              and u.vid_recibo = v.vid_recibo
              and s.id_solicitud = cargas.id_solicitud
              and rownum = 1 ) AS PROYECTO_RECIBO,
       (SELECT v.v_translado
          FROM OP_IN_ARRIBOS_NAD_ENC T,
               op_in_solicitud_carga_descarga s,
               op_in_vehiculos_recibidos v
          WHERE t.iid_arch_arribo = s.iid_arr_ret
                and t.vid_recibo_completo = v.vid_recibo
                and rownum = 1
                AND S.ID_SOLICITUD = cargas.id_solicitud) AS PROYECTO_ARRIBO ";

		$union_cordoba = "";
		$union_Mex = "";
		$union_Golfo = "";
		$union_Peninsula = "";
		$union_Puebla = "";
		$union_Bajio = "";
		$union_Occidente = "";
		$union_Noreste = "";
		$union_Leon = "";

		$arrayReal =array("Val"=>$select_manufac_global_plaza);

		$arrayCount = count($select_manufac_global_plaza);

		//print_r($arrayReal);
		//echo $arrayCount;
		//print_r($select_manufac_global_plaza);
		if (!empty($plaza_manufac)) {
					if ($plaza_manufac == 'CÓRDOBA (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "3";
						$union_cordoba = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 3
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'MÉXICO (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "4";
						$union_cordoba = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 4
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'GOLFO (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "5s";
						$union_Golfo = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 5
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'PENINSULA (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "6";
						$union_Peninsula = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 6
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'PUEBLA (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "7";
						$union_Puebla = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 7
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'BAJIO (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "8";
						$union_Bajio = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 8
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'OCCIDENTE (ARGO)') {
						$data_link = " ";
						$inplaza = $inplaza. "17";
						$union_Occidente = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 17
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}elseif ($plaza_manufac == 'NORESTE (ARGO)') {
						$data_link = "";
						$inplaza = $inplaza. "18";
						$union_Noreste = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
								LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
								LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
								LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
								LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
								LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
								LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
								WHERE cargas.id_tipo IN (1,2)
								AND pla.iid_plaza = 18
								".$and_sql_fecha_op_manufac."
								AND cargas.n_status IN (1,2,3,4,5,6)
								AND (cargas.n_virtual is null or cargas.n_virtual = 0)
								AND cargas.iid_regimen = 1
								AND CARGAS.N_CROSSDOCK = 0
								AND cli.iid_num_cliente = 2905
								UNION";
					}
		}else {

					$inplaza = "";
					for ($i=0; $i < $arrayCount; $i++) {
						switch ($select_manufac_global_plaza[$i]){
									////////////////////////////// CASO PARA PLAZA 3 (CORDOBA)//////////////////////////////////////

									case "3":
										$data_link = " ";
										$inplaza = $inplaza. "3,";
										$union_cordoba = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 3
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									////////////////////////////// CASO PARA PLAZA 4 (MEXICO)//////////////////////////////////////
									case "4":
										$data_link = " ";
										$inplaza = $inplaza. "4,";
										$union_Mex = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 4
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									////////////////////////////// CASO PARA PLAZA 5 (GOLFO)//////////////////////////////////////
									case "5":
										$data_link = " ";
										$inplaza = $inplaza. "5,";
										$union_Golfo = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 5
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									////////////////////////////// CASO PARA PLAZA 6 (PENINSULA)//////////////////////////////////////
									case "6":
										$data_link = " ";
										$inplaza = $inplaza. "6,";
										$union_Peninsula = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 6
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									////////////////////////////// CASO PARA PLAZA 7 (PUEBLA)//////////////////////////////////////
									case "7":
										$data_link = " ";
										$inplaza = $inplaza. "7,";
										$union_Puebla = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 7
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									//////////////////////////////// CASO PARA PLAZA 8 (BAJIO) ////////////////////////////////////
									case "8":
										$data_link = " ";
										$inplaza = $inplaza. "8,";
										$union_Bajio = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 8
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									////////////////////////////// CASO PARA PLAZA 17 (OCCIDENTE)//////////////////////////////////////
									case "17":
										$data_link = " ";
										$inplaza = $inplaza. "17,";
										$union_Occidente = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 17
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
									////////////////////////////// CASO PARA PLAZA 18 (NORESTE)//////////////////////////////////////
									case "18":
										$data_link = "";
										$inplaza = $inplaza. "18,";
										$union_Noreste = $select_cd." FROM op_in_solicitud_carga_descarga".$data_link." cargas
												LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
												LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
												LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
												LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
												LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
												LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
												WHERE cargas.id_tipo IN (1,2)
												AND pla.iid_plaza = 18
												".$and_sql_fecha_op_manufac."
												AND cargas.n_status IN (1,2,3,4,5,6)
												AND (cargas.n_virtual is null or cargas.n_virtual = 0)
												AND cargas.iid_regimen = 1
												AND CARGAS.N_CROSSDOCK = 0
												AND cli.iid_num_cliente = 2905
												UNION";
									break;
						}
					}
			$inplaza = substr($inplaza, 0, -1);
	}
		$conn = conexion::conectar();

		$sql =$union_cordoba.$union_Mex.$union_Golfo.$union_Peninsula.$union_Puebla.$union_Bajio.$union_Occidente.$union_Noreste.$union_Leon.
		    $select_cd." FROM op_in_solicitud_carga_descarga cargas
				LEFT JOIN almacen alm ON alm.iid_almacen = cargas.id_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = cargas.id_plaza
				LEFT JOIN cliente cli on cli.iid_num_cliente = cargas.id_cliente
				LEFT JOIN op_in_tipo_vehiculo veh on veh.iid_tipo = cargas.id_tipo_vehiculo_real
				LEFT JOIN co_ume ume on ume.iid_ume = cargas.iid_ume
				LEFT JOIN no_personal per on per.iid_empleado = cargas.id_almacenista
				WHERE cargas.id_tipo IN (1,2) AND pla.iid_plaza in (".$inplaza.")
				".$and_sql_fecha_op_manufac."
				AND cargas.n_status IN (1,2,3,4,5,6)
				AND (cargas.n_virtual is null or cargas.n_virtual = 0)
				AND cargas.iid_regimen = 2
				AND CARGAS.N_CROSSDOCK = 0
				AND cli.iid_num_cliente = 2905
				ORDER BY cliente ASC";


    //echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );
   	#echo $sql;
		#echo "LA PLAZA ES ".$plaza_manufac;
		#print_r($select_manufac_global_plaza);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_cargas_info[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_cargas_info;

	}
	// -_-_-_-_-_-_-_-_- TERMINA METODO PARA INFORMACION CARGAS(SALIDAS) 1 -_-_-_-_-_-_-_-_-


	// -_-_-_-_-_-_-_-_- INICIA METODO HISTORIAL DE OPERACIONES POR PLAZA -_-_-_-_-_-_-_-_-
	public function historial_plaza($plaza_manufac)
	{

		switch ($plaza_manufac){
			////////////////////////////// CASO PARA PLAZA 3 (CORDOBA)//////////////////////////////////////
			case "CÓRDOBA (ARGO)":
				$data_link = " ";
			break;
			////////////////////////////// CASO PARA PLAZA 4 (MEXICO)//////////////////////////////////////
			case "MÉXICO (ARGO)":
				$data_link = " ";
			break;
			////////////////////////////// CASO PARA PLAZA 5 (GOLFO)//////////////////////////////////////
			case "GOLFO (ARGO)":
				$data_link = " ";
			break;
			////////////////////////////// CASO PARA PLAZA 6 (PENINSULA)//////////////////////////////////////
			case "PENINSULA (ARGO)":
				$data_link = " ";
			break;
			////////////////////////////// CASO PARA PLAZA 7 (PUEBLA)//////////////////////////////////////
			case "PUEBLA (ARGO)":
				$data_link = " ";
			break;
			//////////////////////////////// CASO PARA PLAZA 8 (BAJIO) ////////////////////////////////////
			case "BAJIO (ARGO)":
				$data_link = " ";
			break;
			////////////////////////////// CASO PARA PLAZA 17 (OCCIDENTE)//////////////////////////////////////
			case "OCCIDENTE (ARGO)":
				$data_link = " ";
			break;
			////////////////////////////// CASO PARA PLAZA 18 (NORESTE)//////////////////////////////////////
			case "NORESTE (ARGO)":
				$data_link = "";
			break;
			////////////////////////////// CASO PARA PLAZA 23 (LEON)//////////////////////////////////////
			case "LEON (ARGO)":
				$data_link = " ";
			break;
		}

		$conn = conexion::conectar();

		$sql = "SELECT * FROM
				(SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga".$data_link." s_c_d INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza AND s_c_d.iid_regimen = 1
		        WHERE pla.v_razon_social = '".$plaza_manufac."'
						AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
						AND s_c_d.ID_CLIENTE = 2905
		        UNION
		        SELECT TO_CHAR( s_c_d.d_fec_recepcion,  'dd-mm-yyyy') AS fecha, s_c_d.id_plaza AS id_plaza
				FROM op_in_solicitud_carga_descarga s_c_d INNER JOIN plaza pla ON pla.iid_plaza = s_c_d.id_plaza AND s_c_d.iid_regimen = 2
						AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
						AND s_c_d.ID_CLIENTE = 2905
		        WHERE pla.v_razon_social = '".$plaza_manufac."'
				UNION
				SELECT TO_CHAR( s_o.d_fecha_registro,  'dd-mm-yyyy') AS fecha, s_o.id_plaza AS id_plaza
				FROM op_in_solicitud_otros".$data_link." s_o INNER JOIN plaza pla ON pla.iid_plaza = s_o.id_plaza
		        WHERE pla.v_razon_social = '".$plaza_manufac."' )
						AND (s_c_d.n_virtual is null or s_c_d.n_virtual = 0)
						AND s_c_d.ID_CLIENTE = 2905
		        ORDER BY TO_DATE(fecha,'dd-mm-yyyy') DESC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_plaza[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_plaza;

	}
	// -_-_-_-_-_-_-_-_- TERMINA METODO HISTORIAL DE OPERACIONES POR PLAZA -_-_-_-_-_-_-_-_-


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
//_°_°_°_°_°_°_°_°_°_°_°_°_°_°_°_° TERMINA CLASE PARA VER LA INFO CARGAS(SALIDAS) _°_°_°_°_°_°_°_°__°_°_°_°_


//_°_°_°_°_°_°_°_°_°_°_°_°_°_°_°_° INICIA CLASE PARA LA INFO DE OTROS(CROSS-ETIQUETADO-MARBETEO-OTROS) _°_°_°_°_°_°_°_°__°_°_°_°_
class Op_in_otros_info
{

	function __construct()
	{
		$fila = array();
	}
	// -_-_-_-_-_-_-_-_- INICIA METODO PARA INFORMACION OTROS(CROSS-ETIQUETADO-MARBETEO-OTROS) -_-_-_-_-_-_-_-_-
	public function otros_info($plaza_manufac,$dia_manufac,$fec_ini_per_manufac,$fec_fin_per_manufac,$select_manufac_global_plaza)
	{
		if ($dia_manufac == true){
			switch (true) {
				case ($fec_ini_per_manufac == true) && ($fec_fin_per_manufac == true):
					$and_slq_fecha = " AND s_otros.d_fec_recepcion >= trunc(to_date('".$fec_ini_per_manufac."','dd-mm-yyyy') ) AND s_otros.d_fec_recepcion < trunc(to_date('".$fec_fin_per_manufac."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_slq_fecha = " AND TRUNC(s_otros.d_fec_recepcion) = TO_DATE('".$dia_manufac."','dd-mm-yyyy') ";
					break;
			}
		}

		$arrayReal =array("Val"=>$select_manufac_global_plaza);

		$arrayCount = count($select_manufac_global_plaza);

		$inplaza = "";
		$union_cordoba = "";
		$union_Mex = "";
		$union_Golfo = "";
		$union_Peninsula = "";
		$union_Puebla = "";
		$union_Bajio = "";
		$union_Occidente = "";
		$union_Noreste = "";
		$union_Leon = "";

		//echo $arrayCount;

		for ($i=0; $i < $arrayCount; $i++) {
			switch ($select_manufac_global_plaza[$i]){
						case "3":
						//echo "3";
								$data_link = " ";
								$inplaza = $inplaza. "3,";
								$union_cordoba = "SELECT alm.v_nombre AS almacen,
													s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli,
													cli.v_nombre_corto  AS nc_cli ,
																tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																vehiculo.v_descripcion vehiculo,
																s_otros.v_placas_vehiculo AS placas1,
								 							  s_otros.v_placas_vehiculo_dos AS placas2,
																s_otros.n_status AS status,
																TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
													      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
													      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
													      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
													      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
													      per.v_nombre AS al_nom,
													      per.v_ape_pat AS al_apep,
													      per.v_ape_mat AS al_apem,
																s_otros.v_observaciones as v_obs,
																s_otros.ID_ANDEN AS ANDEN
																FROM op_in_solicitud_carga_descarga".$data_link." s_otros
																LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
																LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
																LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
																LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
																LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
																LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
																LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
																LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
																WHERE pla.iid_plaza = 3
																AND s_otros.n_crossdock = 1
																AND CLI.iid_num_cliente = 2905
																".$and_slq_fecha."
																UNION ";
								    break;
							case '4':
								$data_link = " ";
								$inplaza = $inplaza. "4,";
								$union_Mex = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																vehiculo.v_descripcion vehiculo,
																s_otros.v_placas_vehiculo AS placas1,
								 							  s_otros.v_placas_vehiculo_dos AS placas2,
																s_otros.n_status AS status,
																TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
													      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
													      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
													      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
													      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
													      per.v_nombre AS al_nom,
													      per.v_ape_pat AS al_apep,
													      per.v_ape_mat AS al_apem,
																s_otros.v_observaciones as v_obs,
																s_otros.ID_ANDEN AS ANDEN
																FROM op_in_solicitud_carga_descarga".$data_link." s_otros
																LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
																LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
																LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
																LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
																LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
																LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
																LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
																LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
																WHERE pla.iid_plaza = 4
																AND s_otros.n_crossdock = 1
																AND CLI.iid_num_cliente = 2905
																".$and_slq_fecha."
																UNION ";
								break;
								case '5':
									$data_link = " ";
									$inplaza = $inplaza. "5,";
									$union_Golfo = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																	tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																	s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																	vehiculo.v_descripcion vehiculo,
																	s_otros.v_placas_vehiculo AS placas1,
									 							  s_otros.v_placas_vehiculo_dos AS placas2,
																	s_otros.n_status AS status,
																	TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
														      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
														      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
														      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
														      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																	TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																	TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																	TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
														      per.v_nombre AS al_nom,
														      per.v_ape_pat AS al_apep,
														      per.v_ape_mat AS al_apem,
																	s_otros.v_observaciones as v_obs,
																	s_otros.ID_ANDEN AS ANDEN
																	FROM op_in_solicitud_carga_descarga".$data_link." s_otros
																	LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
																	LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
																	LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
																	LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
																	LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
																	LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
																	LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
																	LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
																	WHERE pla.iid_plaza = 5
																	AND s_otros.n_crossdock = 1
																	AND CLI.iid_num_cliente = 2905
																	".$and_slq_fecha."
																	UNION ";
									break;

									case '6':
										$data_link = " ";
										$inplaza = $inplaza. "6,";
										$union_Peninsula = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																		tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																		s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																		vehiculo.v_descripcion vehiculo,
																		s_otros.v_placas_vehiculo AS placas1,
										 							  s_otros.v_placas_vehiculo_dos AS placas2,
																		s_otros.n_status AS status,
																		TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
															      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
															      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
															      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
															      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																		TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																		TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																		TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
															      per.v_nombre AS al_nom,
															      per.v_ape_pat AS al_apep,
															      per.v_ape_mat AS al_apem,
																		s_otros.v_observaciones as v_obs,
																		s_otros.ID_ANDEN AS ANDEN
																		FROM op_in_solicitud_carga_descarga".$data_link." s_otros
																		LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
																		LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
																		LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
																		LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
																		LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
																		LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
																		LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
																		LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
																		WHERE pla.iid_plaza = 6
																		AND s_otros.n_crossdock = 1
																		AND CLI.iid_num_cliente = 2905
																		".$and_slq_fecha."
																		UNION ";
										break;

										case '7':
											$data_link = " ";
											$inplaza = $inplaza. "7,";
											$union_Puebla = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																			tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																			s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																			vehiculo.v_descripcion vehiculo,
																			s_otros.v_placas_vehiculo AS placas1,
											 							  s_otros.v_placas_vehiculo_dos AS placas2,
																			s_otros.n_status AS status,
																			TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
																      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
																      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
																      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
																      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																			TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																			TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																			TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
																      per.v_nombre AS al_nom,
																      per.v_ape_pat AS al_apep,
																      per.v_ape_mat AS al_apem,
																			s_otros.v_observaciones as v_obs,
																			s_otros.ID_ANDEN AS ANDEN
																			FROM op_in_solicitud_carga_descarga".$data_link." s_otros
																			LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
																			LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
																			LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
																			LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
																			LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
																			LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
																			LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
																			LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
																			WHERE pla.iid_plaza = 7
																			AND s_otros.n_crossdock = 1
																			AND CLI.iid_num_cliente = 2905
																			".$and_slq_fecha."
																			UNION ";
											break;

											case '7':
  											$data_link = " ";
  											$inplaza = $inplaza. "8,";
  											$union_Bajio = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																				tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																				s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																				vehiculo.v_descripcion vehiculo,
																				s_otros.v_placas_vehiculo AS placas1,
												 							  s_otros.v_placas_vehiculo_dos AS placas2,
																				s_otros.n_status AS status,
																				TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
																	      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
																	      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
																	      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
																	      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																				TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																				TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																				TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
																	      per.v_nombre AS al_nom,
																	      per.v_ape_pat AS al_apep,
																	      per.v_ape_mat AS al_apem,
																				s_otros.v_observaciones as v_obs,
																				s_otros.ID_ANDEN AS ANDEN
																				FROM op_in_solicitud_carga_descarga".$data_link." s_otros
  																			LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
  																			LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
  																			LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
  																			LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
  																			LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
  																			LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
  																			LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
  																			LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
  																			WHERE pla.iid_plaza = 8
																				AND s_otros.n_crossdock = 1
																				AND CLI.iid_num_cliente = 2905
  																			".$and_slq_fecha."
																				UNION ";
  											break;

												case '17':
	  											$data_link = " ";
	  											$inplaza = $inplaza. "17,";
	  											$union_Occidente = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																					tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																					s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																					vehiculo.v_descripcion vehiculo,
																					s_otros.v_placas_vehiculo AS placas1,
													 							  s_otros.v_placas_vehiculo_dos AS placas2,
																					s_otros.n_status AS status,
																					TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
																		      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
																		      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
																		      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
																		      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																					TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																					TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																					TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
																		      per.v_nombre AS al_nom,
																		      per.v_ape_pat AS al_apep,
																		      per.v_ape_mat AS al_apem,
																					s_otros.v_observaciones as v_obs,
																					s_otros.ID_ANDEN AS ANDEN
																					FROM op_in_solicitud_carga_descarga".$data_link." s_otros
	  																			LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
	  																			LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
	  																			LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
	  																			LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
	  																			LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
	  																			LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
	  																			LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
	  																			LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
	  																			WHERE pla.iid_plaza = 17
																					AND s_otros.n_crossdock = 1
																					AND CLI.iid_num_cliente = 2905
	  																			".$and_slq_fecha."
																					UNION ";
	  											break;

													case '18':
		  											$data_link = " ";
		  											$inplaza = $inplaza. "18,";
		  											$union_Noreste = "SELECT alm.v_nombre AS almacen, s_otros.id_almacen AS id_almacen, s_otros.id_solicitud AS solicitud, cli.v_razon_social AS rs_cli, cli.v_nombre_corto  AS nc_cli ,
																						tipo_par.v_descripcion AS mercancia, parte.v_descripcion AS des_mer,
																						s_otros.n_cantidad_ume AS cantidad, ume.v_descripcion AS ume,
																						vehiculo.v_descripcion vehiculo,
																						s_otros.v_placas_vehiculo AS placas1,
														 							  s_otros.v_placas_vehiculo_dos AS placas2,
																						s_otros.n_status AS status,
																						TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
																			      TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
																			      TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
																			      TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
																			      TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
																						TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
																						TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
																						TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
																			      per.v_nombre AS al_nom,
																			      per.v_ape_pat AS al_apep,
																			      per.v_ape_mat AS al_apem,
																						s_otros.v_observaciones as v_obs,
																						s_otros.ID_ANDEN AS ANDEN
																						FROM op_in_solicitud_carga_descarga".$data_link." s_otros
		  																			LEFT JOIN almacen".$data_link." alm ON alm.iid_almacen = s_otros.id_almacen
		  																			LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
		  																			LEFT JOIN cliente".$data_link." cli ON cli.iid_num_cliente = s_otros.id_cliente
		  																			LEFT JOIN op_in_partes".$data_link." parte ON parte.vid_num_parte = s_otros.v_mercancia
		  																			LEFT JOIN op_in_tipo_parte".$data_link." tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
		  																			LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
		  																			LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
		  																			LEFT JOIN no_personal".$data_link." per ON per.iid_empleado = s_otros.id_almacenista
		  																			WHERE pla.iid_plaza = 18
																						AND s_otros.n_crossdock = 1
																						AND CLI.iid_num_cliente = 2905
		  																			".$and_slq_fecha."
																						UNION ";
		  											break;
							}
				}
		/*---------------CONCATENACION SQL---------------*/
		$inplaza = substr($inplaza, 0, -1);
		$conn = conexion::conectar();



		$sql = $union_cordoba.$union_Mex.$union_Golfo.$union_Peninsula.$union_Puebla.$union_Bajio.$union_Occidente.$union_Noreste.
			 " SELECT alm.v_nombre AS almacen,
				       s_otros.id_almacen AS id_almacen,
				       s_otros.id_solicitud AS solicitud,
				       cli.v_razon_social AS rs_cli,
				       cli.v_nombre_corto AS nc_cli,
				       tipo_par.v_descripcion AS mercancia,
				       parte.v_descripcion AS des_mer,
				       s_otros.n_cantidad_ume AS cantidad,
				       ume.v_descripcion AS ume,
				       vehiculo.v_descripcion vehiculo,
				       s_otros.v_placas_vehiculo AS placas1,
							 s_otros.v_placas_vehiculo_dos AS placas2,
				       s_otros.n_status AS status,
							 TO_CHAR(s_otros.d_fec_recepcion, 'dd-mm-yyyy HH24:MI:SS') AS f_inicio,
							 TO_CHAR(s_otros.d_fec_llegada_real, 'dd-mm-yyyy HH24:MI:SS') AS f_llegadaR,
							 TO_CHAR(s_otros.d_fec_ini_car_des, 'dd-mm-yyyy HH24:MI:SS') AS inicia_Descarga,
							 TO_CHAR(s_otros.d_fec_fin_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Descarga,
							 TO_CHAR(s_otros.d_fec_ini_car, 'dd-mm-yyyy HH24:MI:SS') AS iniciaCarga,
							 TO_CHAR(s_otros.d_fec_fin_car_des, 'dd-mm-yyyy HH24:MI:SS') AS finaliza_Carga,
							 TO_CHAR(s_otros.d_fec_desp_vehic, 'dd-mm-yyyy HH24:MI:SS') AS despacho,
							 TO_CHAR(s_otros.D_FEC_CANCELA_SOL, 'dd-mm-yyyy HH24:MI:SS') AS f_cancelado,
				       per.v_nombre AS al_nom,
				       per.v_ape_pat AS al_apep,
				       per.v_ape_mat AS al_apem,
							 s_otros.v_observaciones as v_obs,
							 s_otros.ID_ANDEN AS ANDEN
											 FROM op_in_solicitud_carga_descarga s_otros
				LEFT JOIN almacen alm ON alm.iid_almacen = s_otros.id_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				LEFT JOIN cliente cli ON cli.iid_num_cliente = s_otros.id_cliente
				LEFT JOIN op_in_partes parte ON parte.vid_num_parte = s_otros.v_mercancia
				LEFT JOIN op_in_tipo_parte tipo_par ON tipo_par.iid_tipo_parte = parte.iid_tipo_parte
				LEFT JOIN co_ume ume ON ume.iid_ume = s_otros.iid_ume
				LEFT JOIN op_in_tipo_vehiculo vehiculo ON vehiculo.iid_tipo = s_otros.id_tipo_vehiculo
				LEFT JOIN no_personal per ON per.iid_empleado = s_otros.id_almacenista
				WHERE pla.iid_plaza in (".$inplaza.")
				AND s_otros.n_crossdock = 1
				AND CLI.iid_num_cliente = 2905
				".$and_slq_fecha."";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid );


  #  echo $sql;


		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->fila[]=$row;
		}
			oci_free_statement($stid);
			oci_close($stid);
			return $this->fila;


	}
	// -_-_-_-_-_-_-_-_- TERMINA METODO PARA INFORMACION OTROS(CROSS-ETIQUETADO-MARBETEO-OTROS) -_-_-_-_-_-_-_-_-

}
//_°_°_°_°_°_°_°_°_°_°_°_°_°_°_°_° TERMINA CLASE PARA LA INFO DE OTROS(CROSS-ETIQUETADO-MARBETEO-OTROS) _°_°_°_°_°_°_°_°__°_°_°_°_


//_°_°_°_°_°_°_°_°_°_°_°_°_° INICIA CLASE PARA VER INFORME DETALLES DE CARGAS Y DESCARGAS_°_°_°_°_°__°_°_°_°_
class Det_ret_arr
{

	function __construct()
	{
		$fila = array();
	}
	// -_-_-_-_-_-_-_-_- INICIA METODO VER LA INFORMACION DEL ARR-RET -_-_-_-_-_-_-_-_-
	public function info_det_arr_ret($plaza_manufac,$valor_almacen,$valor_arribo,$valor_arribo_descarga)
	{

		switch ($plaza_manufac){
			////////////////////////////// CASO PARA PLAZA 3 (CORDOBA)//////////////////////////////////////
			case "CÓRDOBA (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 4 (MEXICO)//////////////////////////////////////
			case "MÉXICO (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 5 (GOLFO)//////////////////////////////////////
			case "GOLFO (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 6 (PENINSULA)//////////////////////////////////////
			case "PENINSULA (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 7 (PUEBLA)//////////////////////////////////////
			case "PUEBLA (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			//////////////////////////////// CASO PARA PLAZA 8 (BAJIO) ////////////////////////////////////
			case "BAJIO (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = " AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 17 (OCCIDENTE)//////////////////////////////////////
			case "OCCIDENTE (ARGO)":
				$data_link = "@dlr06 ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 18 (NORESTE)//////////////////////////////////////
			case "NORESTE (ARGO)":
				$data_link = "";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			////////////////////////////// CASO PARA PLAZA 23 (LEON)//////////////////////////////////////
			case "LEON (ARGO)":
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
			default:
				$data_link = " ";
				$and_sql_sol_arr = "  AND s_retiro.id_solicitud = '".$valor_arribo."' ";
				$on_sql_join = " ON s_retiro.id_solicitud = enc.v_id ";
			break;
		}

		$conn = conexion::conectar();

		if($valor_arribo==true){
		$sql = "SELECT
				retiro.iid_retiro AS id_retiro, retiro.vid_recibo AS recibo,
				ume.v_descripcion AS ume, retiro.c_cantidad_ume AS cantidad,
				tipo_par.v_descripcion AS mercancia, par.v_descripcion AS des_mer,
				retiro.v_referencia_2 AS referencia, enc.vid_factura AS asn, enc.v_id AS id_solicitud, retiro.iid_arribo AS id_arribo
				FROM op_in_retiros_nad_det".$data_link." retiro
				LEFT JOIN co_ume".$data_link." ume on ume.iid_ume = retiro.iid_ume
				LEFT JOIN op_in_partes".$data_link." par on par.vid_num_parte = retiro.vid_num_parte
				LEFT JOIN op_in_tipo_parte".$data_link." tipo_par on tipo_par.iid_tipo_parte = par.iid_tipo_parte
				LEFT JOIN op_in_retiros_nad_enc".$data_link." enc ON enc.iid_retiro = retiro.iid_retiro
				LEFT JOIN op_in_solicitud_carga_descarga".$data_link." s_retiro ".$on_sql_join."
				LEFT JOIN plaza pla ON pla.iid_plaza = s_retiro.id_plaza
				INNER JOIN OP_IN_RETIROS_NAD_GRAL".$data_link." GRAL ON enc.iid_arch_retiro = GRAL.IID_ARCH_RETIRO
                                         AND GRAL.IID_PLAZA = PLA.IID_PLAZA
				WHERE  s_retiro.id_almacen = '".$valor_almacen."' AND s_retiro.id_tipo = 1
				AND (s_retiro.n_virtual is null or s_retiro.n_virtual = 0)
				AND s_retiro.id_cliente = 2905
				".$and_sql_sol_arr."
				";
				#pla.v_razon_social = '".$plaza_manufac."' AND
		}

		if($valor_arribo_descarga==true){
		$sql = "SELECT arribo.iid_arribo AS id_retiro, arribo.vid_recibo AS recibo, ume.v_descripcion AS ume, arribo.c_cantidad_ume_arribo AS cantidad,
				tipo_par.v_descripcion  AS mercancia, par.v_descripcion AS des_mer, arribo.v_referencia_2 AS referencia, arribo.vid_factura AS fac, s_descarga.iid_arr_ret AS id_arribo, s_descarga.id_solicitud as id_solicitud
				FROM op_in_arribos_nad_det".$data_link." arribo
				LEFT JOIN op_in_partes".$data_link." par on par.vid_num_parte = arribo.vid_num_parte
				LEFT JOIN op_in_tipo_parte".$data_link." tipo_par on tipo_par.iid_tipo_parte = par.iid_tipo_parte
				LEFT JOIN co_ume ume on ume.iid_ume = arribo.iid_ume
				LEFT JOIN op_in_solicitud_carga_descarga".$data_link." s_descarga ON s_descarga.iid_arr_ret = arribo.iid_arribo
        		LEFT JOIN plaza pla ON pla.iid_plaza = s_descarga.id_plaza
				WHERE s_descarga.id_almacen = '".$valor_almacen."' AND s_descarga.id_tipo = 2
				AND (s_descarga.n_virtual is null or s_descarga.n_virtual = 0)
				AND s_descarga.id_cliente = 2905
				AND s_descarga.id_solicitud = '".$valor_arribo_descarga."'
				UNION
				SELECT arribo.iid_arribo            AS id_retiro,
				       arribo.vid_recibo            AS recibo,
				       ume.v_descripcion            AS ume,
				       arribo.c_cantidad_ume_arribo AS cantidad,
				       tipo_par.v_descripcion       AS mercancia,
				       par.v_descripcion            AS des_mer,
				       arribo.v_referencia_2        AS referencia,
				       arribo.vid_factura           AS fac,
				       s_descarga.iid_arr_ret       AS id_arribo,
							 s_descarga.id_solicitud as id_solicitud
				  FROM op_in_arribos_nad_det arribo
				  LEFT JOIN op_in_partes par on par.vid_num_parte =
				                                      arribo.vid_num_parte
				  LEFT JOIN op_in_tipo_parte tipo_par on tipo_par.iid_tipo_parte =
				                                               par.iid_tipo_parte
				  LEFT JOIN co_ume ume on ume.iid_ume = arribo.iid_ume
				  LEFT JOIN op_in_solicitud_carga_descarga s_descarga ON s_descarga.iid_arr_ret =
				                                                               arribo.iid_arribo
				  LEFT JOIN plaza pla ON pla.iid_plaza = s_descarga.id_plaza
				 WHERE  s_descarga.id_almacen = '".$valor_almacen."'
				   AND s_descarga.id_tipo = 2
				   AND (s_descarga.n_virtual is null or s_descarga.n_virtual = 0)
					 AND s_descarga.id_cliente = 2905
				   AND s_descarga.id_solicitud ='".$valor_arribo_descarga."'
				";
		}

#--pla.v_razon_social = '".$plaza_manufac."'AND
//pla.v_razon_social = '".$plaza_manufac."' AND
		//echo $sql ;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->fila[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->fila;
	}
	// -_-_-_-_-_-_-_-_- TERMINA METODO PARA VER LA INFORMACION DEL ARR-RET -_-_-_-_-_-_-_-_-



}
//_°_°_°_°_°_°_°_°_°_°_°_°_° TERMINA CLASE PARA VER INFORME DETALLES DE CARGAS Y DESCARGAS_°_°_°_°_°__°_°_°_°_
