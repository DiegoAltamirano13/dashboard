<?php
/**
* © Argo Almacenadora ®
* Fecha: 19/04/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Comercial
* Version --
*/
include_once '../libs/conOra.php';

class Comercial
{
	function __construct()
	{
		$res_grafica_pastel = array();
		$res_t_pros_fact = array();
		$res_pros_cer_can = array();
		$res_t_ser_directos = array();
		$res_t_ser_habilitados = array();
		$res_tabla_prospectos = array();
		$res_prospecto_det = array();
		$res_contacto_prospecto = array();
	}

// *-*-*-*-*-*-*-* FUNCION PARA LA GRAFICA DE PASTE PROSPECTOS *-*-*-*-*-*-*-* //
	public function grafica_pastel($co_plaza,$anio_co,$t_servicio_co,$t_prospecto_co,$promotor_co,$fec_ini_co,$fec_fin_co,$status_pros_co_graf)
	{
		$conn = conexion::conectar();
		/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
		if ($co_plaza==true)
		{
			$where_sql_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
		}
		if ($anio_co==true)
		{
			if ( $fec_ini_co == true && $fec_fin_co == true ){
			$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
			}else{
			$where_sql_anio = " AND to_char(pros.d_fecha_registro, 'yyyy') =  ".$anio_co." ";
			}
		}
		if ($t_servicio_co==true)
		{
			$where_sql_t_servicio = " AND info.n_tipo_servicio =  ".$t_servicio_co." ";
		}
		if ($t_prospecto_co==true)
		{
			$where_sql_t_prospecto = " AND info.n_tipo_cliente =  ".$t_prospecto_co." ";
		}
		if ($promotor_co == true)
		{
			$where_sql_promotor_co = " AND pros.iid_promotor =  ".$promotor_co." ";
		}
		/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
		$sql = "SELECT COUNT( pros.v_razon_social ) AS total_pros, pros.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas,
				 decode(to_char(pros.iid_plaza),
				3,'#FBEC5D',
				4,'#F5C9C4',
				5,'#32DADD',
				6,'#5AB1EF',
				7,'#FFB980',
				8,'#D87A80',
				17,'#FF6A6F',
				18,'#5D8AA8',
				21,'#5A6E83',
				23,'#B6A2DE'
				) as color
				 FROM co_prospecto pros
				 LEFT JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto
				 LEFT JOIN plaza pla on pla.iid_plaza = pros.iid_plaza
				 WHERE pros.s_status IN (".$status_pros_co_graf.") ".$where_sql_plaza.$where_sql_anio.$where_sql_t_servicio.$where_sql_t_prospecto.$where_sql_promotor_co.$res_fec_ini_fin_co."
				 GROUP BY pros.iid_plaza, pla.v_razon_social, pla.v_siglas ORDER BY plaza_siglas ASC ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_grafica_pastel[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_grafica_pastel;
	}

// *-*-*-*-*-*-*-* FUNCION PARA WIDGETS PROSPECTOS Y FAC. ESTIMADA *-*-*-*-*-*-*-* //
	public function t_pros_fact($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$fec_ini_co,$fec_fin_co)
	{
		$conn = conexion::conectar();
		/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
		if ($co_plaza == true)
		{
			$res_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
		}
		if ($t_prospecto_co == true)
		{
			$res_t_prospecto_co = " AND info.n_tipo_cliente = ".$t_prospecto_co." ";
		}
		if ($t_servicio_co == true)
		{
			$res_t_servicio_co = " AND info.n_tipo_servicio = ".$t_servicio_co." ";
		}
		if ($promotor_co == true)
		{
			$res_promotor_co = " AND pros.iid_promotor = ".$promotor_co." ";
		}
		if ($anio_co == true)
		{
			if ( $fec_ini_co == true && $fec_fin_co == true ){
			$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND  pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
			}else{
			$res_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
			}
		}
		/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
		$sql = "SELECT COUNT(pros.iid_num_prospecto) AS t_prospectos, sum(info.n_facturacion) AS fac_estimada
				FROM co_prospecto_info info
				INNER JOIN co_prospecto pros ON pros.iid_num_prospecto = info.iid_num_prospecto
				WHERE pros.s_status in (0, 1, 2) ".$res_plaza.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co.$res_anio_co.$res_fec_ini_fin_co;

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_t_pros_fact[]=$row;
		}
			oci_free_statement($stid);
			#echo $sql;
			oci_close($conn);
			return $this->res_t_pros_fact;
	}
// *-*-*-*-*-*-*-* FUNCION PARA WIDGETS PROSPECTOS CERRADOS Y CANCELADOS *-*-*-*-*-*-*-* //
	public function t_pros_cer_can($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co,$t_prospecto_co,$t_servicio_co,$promotor_co)
	{
		/* ---------------- CONCATENACION WHERE SQL PROSPECTOS CERRADOS Y CANCELADOS ----------------*/
		if($anio_co == true )
		{
			if ($fec_ini_co == true && $fec_fin_co == true)
			{
			$sql_fec_ini_fin = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
			}else{
			$sql_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
			}
		}
		if($co_plaza == true )
		{
			$sql_co_plaza = "  AND pros.iid_plaza = ".$co_plaza." ";
		}/////////////////
		if ($t_prospecto_co == true)
		{
			$res_t_prospecto_co = " AND info.n_tipo_cliente = ".$t_prospecto_co." ";
		}
		if ($t_servicio_co == true)
		{
			$res_t_servicio_co = " AND info.n_tipo_servicio = ".$t_servicio_co." ";
		}
		if ($promotor_co == true)
		{
			$res_promotor_co = " AND pros.iid_promotor = ".$promotor_co." ";
		}
		/* ---------------- CONCATENACION WHERE SQL PROSPECTOS CERRADOS Y CANCELADOS ----------------*/
		$conn = conexion::conectar();

		$sql = " SELECT * FROM
							  (
							  SELECT COUNT(pros.iid_num_prospecto) AS t_pros_cerrados
							  FROM co_prospecto pros
							  LEFT JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto
							  /*LEFT JOIN cliente clie ON clie.iid_num_prospecto = pros.iid_num_prospecto*/
							  WHERE pros.s_status = 3  /*AND clie.s_status = 1*/ ".$sql_anio_co.$sql_co_plaza.$sql_fec_ini_fin.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co."
							  ),
							  (
							  SELECT COUNT(pros.iid_num_prospecto) AS t_pros_cancelados
							  FROM co_prospecto pros
							  LEFT JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto
							  WHERE pros.s_status = 4 ".$sql_anio_co.$sql_co_plaza.$sql_fec_ini_fin.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co."
							  ) ";

		$stid = oci_parse($conn, $sql);
							 oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_pros_cer_can[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_pros_cer_can;

	}

// *-*-*-*-*-*-*-* FUNCION PARA WIDGETS SERVICIOS DIRECTOS *-*-*-*-*-*-*-* //
	public function t_ser_directos($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$fec_ini_co,$fec_fin_co)
	{
		$conn = conexion::conectar();
		/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
		if ($co_plaza == true)
		{
			$res_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
		}
		if ($t_prospecto_co == true)
		{
			$res_t_prospecto_co = " AND info.n_tipo_cliente = ".$t_prospecto_co." ";
		}
		if ($t_servicio_co == true)
		{
			$res_t_servicio_co = " AND info.n_tipo_servicio = ".$t_servicio_co." ";
		}
		if ($promotor_co == true)
		{
			$res_promotor_co = " AND pros.iid_promotor = ".$promotor_co." ";
		}
		if ($anio_co == true)
		{
			if ( $fec_ini_co == true && $fec_fin_co == true ){
			$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
			}else{
			$res_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
			}
		}
		/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
		$sql = "SELECT * FROM
				(
				SELECT COUNT(info.iid_num_prospecto) AS directa_f
				FROM co_prospecto_info info
				INNER JOIN co_prospecto pros ON pros.iid_num_prospecto = info.iid_num_prospecto
				WHERE pros.s_status in(0, 1) AND info.n_tipo_servicio = 1 ".$res_plaza.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co.$res_fec_ini_fin_co."
				),
				(
				SELECT COUNT(info.iid_num_prospecto) AS directa_N
				FROM co_prospecto_info info
				INNER JOIN co_prospecto pros ON pros.iid_num_prospecto = info.iid_num_prospecto
				WHERE pros.s_status in(0, 1) AND info.n_tipo_servicio = 2 ".$res_plaza.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co.$res_fec_ini_fin_co."
				)";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_t_ser_directos[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_t_ser_directos;
	}

// *-*-*-*-*-*-*-* FUNCION PARA WIDGETS SERVICIOS HABILITADOS *-*-*-*-*-*-*-* //
	public function t_ser_habilitados($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co)
	{
		$conn = conexion::conectar();
		/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
		if ($co_plaza == true)
		{
			$res_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
		}
		if ($t_prospecto_co == true)
		{
			$res_t_prospecto_co = " AND info.n_tipo_cliente = ".$t_prospecto_co." ";
		}
		if ($t_servicio_co == true)
		{
			$res_t_servicio_co = " AND info.n_tipo_servicio = ".$t_servicio_co." ";
		}
		if ($promotor_co == true)
		{
			$res_promotor_co = " AND pros.iid_promotor = ".$promotor_co." ";
		}
		if ($anio_co == true)
		{
			if ( $fec_ini_co == true && $fec_fin_co == true ){
			$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
			}else{
			$res_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
			}
		}
		/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
		$sql = "SELECT * FROM
				(
				SELECT COUNT(info.iid_num_prospecto) AS habilitada_f
				FROM co_prospecto_info info
				INNER JOIN co_prospecto pros ON pros.iid_num_prospecto = info.iid_num_prospecto
				WHERE pros.s_status in(0, 1) AND info.n_tipo_servicio = 4 ".$res_plaza.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co.$res_anio_co.$res_fec_ini_fin_co."
				),
				(
				SELECT COUNT(info.iid_num_prospecto) AS habilitada_n
				FROM co_prospecto_info info
				INNER JOIN co_prospecto pros ON pros.iid_num_prospecto = info.iid_num_prospecto
				WHERE pros.s_status in(0, 1) AND info.n_tipo_servicio = 3 ".$res_plaza.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co.$res_anio_co.$res_fec_ini_fin_co."
				)";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_t_ser_habilitados[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_t_ser_habilitados;
	}

// *-*-*-*-*-*-*-* FUNCION PARA TABLA DE PROSPECTOS *-*-*-*-*-*-*-* //
	public function tabla_prospectos($co_plaza,$t_prospecto_co,$t_servicio_co,$promotor_co,$anio_co,$id_prospecto_co,$grafica_co_pros,$co_top_promo,$fec_ini_co,$fec_fin_co,$status_pros_co_graf)
	{
		$conn = conexion::conectar();
		/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
		if ($co_plaza == true)
		{
			$res_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
		}
		if ($t_prospecto_co == true)
		{
			$res_t_prospecto_co = " AND info.n_tipo_cliente = ".$t_prospecto_co." ";
		}
		if ($t_servicio_co == true)
		{
			$res_t_servicio_co = " AND info.n_tipo_servicio = ".$t_servicio_co." ";
		}
		if ($promotor_co == true)
		{
			$res_promotor_co = " AND prom.iid_promotor = ".$promotor_co." ";
		}
		if ($anio_co == true )
		{
			if ( $fec_ini_co == true && $fec_fin_co == true ){
			$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
			}else{
			$res_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
			}
		}
		if ($id_prospecto_co == true)
		{
			$res_id_prospecto_co = " AND pros.iid_num_prospecto = ".$id_prospecto_co." ";
		}
		if ($grafica_co_pros == 3 )
		{
			switch ($co_top_promo) {
					case 1:
						$res_top_promo = "3";
						break;
					case 2:
						$res_top_promo = " 0, 1, 2";
						break;
					case 3:
						$res_top_promo = "3";
						$res_top_promo_clie = " AND pros.s_status not in (1, 0, 2,  4)  ";
						break;

				}
		}
		if ($grafica_co_pros == 4 || $grafica_co_pros == 4 && $co_top_promo == 4){
			$res_top_promo = "4";
			$res_top_promo_clie = " AND pros.s_status not in (1, 0, 2,3 ) ";
			$join_cliente = " LEFT JOIN cliente cli ON cli.iid_num_prospecto = pros.iid_num_prospecto  ";
			$fec_cliente = ", TO_CHAR(cli.d_fecha_alta_cliente, 'dd-mm-yyyy') AS fec_reg_clie, cli.s_status AS status_cli";
		}
		if ($grafica_co_pros == 5){
			$res_top_promo = "5";
			$res_top_promo_clie = " AND pros.s_status not in (1, 0,2 ,3)  ";
		}
		if ($grafica_co_pros == 2 ){
			switch ($status_pros_co_graf) {
				case '1':
					$res_top_promo = "0, 1, 2";
					break;
				case '2':
					$res_top_promo = "0,1, 2";
					$res_top_promo_clie = " AND pros.s_status not in ( 3, 4) ";
					break;
				default:
					$res_top_promo = "0,1, 2";
					break;
			}
		}
		if ($grafica_co_pros == 10) {
			$res_top_promo = "3";
			$res_top_promo_clie = " AND pros.s_status in ( 3) ";
		}
		//echo $status_pros_co_graf. " ". $grafica_co_pros. " ".$res_top_promo;
		/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
		$sql = "SELECT
        		pla.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, pla.v_siglas AS plaza_siglas,
				pros.iid_num_prospecto AS id_prospecto, pros.v_razon_social AS prospecto, pros.s_status AS status_pros, pros.n_tipo_persona AS regimen_fis , pros.v_nacionalidad AS pais, pros.v_estado AS estado, pros.v_ciudad AS ciudad, pros.n_cp AS cp, pros.v_direccion AS direccion, pros.v_telefono_1 AS telefono1, pros.v_telefono_2 AS telefono2, pros.v_email AS email,
        		TO_CHAR(pros.d_fecha_registro, 'dd-mm-yyyy') AS fec_reg ".$fec_cliente."
				,info.v_almacen AS almacen, info.n_facturacion AS fac_estimada, info.n_tipo_cliente AS id_tipo_pros, info.v_origen AS origen ,
		        DECODE(info.n_tipo_cliente,
		        1,'ESTACIONAL',
		        2,'HABITUAL',
		        3,'EVENTUAL',
		        4,'PROYECTO',
		        5,'OCASIONAL',
		        'NO DEFINIDO') AS tipo_pros,
		        info.n_tipo_servicio AS id_tipo_servicio,
		        DECODE(info.n_tipo_servicio,
		        1,'DIRECTA FISCAL',
		        2,'DIRECTA NACIONAL',
		        3,'HABILITADA NACIONAL',
		        4,'HABILITADA FISCAL',
		        'NO DEFINIDO') AS tipo_servicio,
		        info.n_porcent_cierre AS porc_cierre, info.n_plazo_cierre  AS plazo_cierre, info.n_valor_aprox AS valor_apro, info.v_volumen AS volumen, info.v_rotacion AS rotacion, info.v_mercancia AS mercancia
				,prom.iid_promotor AS id_promo, prom.v_nombre AS nom_prom, prom.v_apellido_pat AS apepat_prom, prom.v_apellido_mat AS apemat_prom, prom.s_status AS status_prom, prom.v_celular AS cel_prom, prom.v_telefono AS tel_prom, prom.v_email AS email_prom, 
       pros.v_observaciones
				FROM co_prospecto pros
				LEFT JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto
				LEFT JOIN co_promotor prom ON prom.iid_promotor = pros.iid_promotor
				".$join_cliente."
            	LEFT JOIN plaza pla ON pla.iid_plaza = pros.iid_plaza
		        WHERE pros.s_status IN (".$res_top_promo.") ".$res_plaza.$res_t_prospecto_co.$res_t_servicio_co.$res_promotor_co.$res_anio_co.$res_id_prospecto_co.$res_top_promo_clie.$res_fec_ini_fin_co."
		        ORDER BY  pros.d_fecha_registro DESC NULLS LAST " ;

						#echo $sql;
						#echo $grafica_co_pros;
		$stid = oci_parse($conn, $sql);
				oci_execute($stid );


		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_prospectos[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_prospectos;
	}

// *-*-*-*-*-*-*-* FUNCION PARA DETALLES CONTACTO DE PROSPECTOS *-*-*-*-*-*-*-* //
	 public function contacto_prospecto($id_prospecto_co)
	 {
	 	$conn = conexion::conectar();

	 	$sql = "SELECT con_prom.v_nombre AS con_prom_nombre, con_prom.v_puesto AS con_prom_puesto, con_prom.v_telefono_1 AS con_prom_tel1, con_prom.v_telefono_2 AS con_prom_tel2, con_prom.v_email AS con_prom_email
				FROM co_prospecto_contacto con_prom
				WHERE con_prom.iid_num_prospecto = ".$id_prospecto_co;

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_contacto_prospecto[] = $row;
	 	}
	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_contacto_prospecto;
	 }

// *-*-*-*-*-*-*-* FUNCION PARA SELECT PROSPECTOS *-*-*-*-*-*-*-* //
	 public function select_promotor($co_plaza)
	 {
	 	$conn = conexion::conectar();
	 	/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
	 	switch (true) {
	 		case ($co_plaza == true):
	 			$where_sql = " AND promo.iid_plaza = ".$co_plaza." ";//AND promo.iid_plaza = ".$co_plaza."
	 			break;

	 		default:
	 			$where_sql = " ";
	 			break;
	 	}
	 	/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
	 	$sql = "SELECT promo.iid_promotor AS id_promotor, promo.v_nombre AS nombre, promo.v_apellido_pat AS ape_pat, promo.v_apellido_mat AS ape_mat
	 		 	,promo.s_status AS status
				FROM co_promotor promo
				WHERE promo.s_status IN (1,9)  ".$where_sql." ";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_select_prospecto[] = $row;
	 	}
	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_select_prospecto;
	 }

// *-*-*-*-*-*-*-* FUNCION PARA SELECT ANIO  *-*-*-*-*-*-*-* //
	 public function select_anio_co($co_plaza)
	 {
	 	$conn = conexion::conectar();
	 	/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
	 	switch (true) {
	 		case ($co_plaza == true):
	 			$where_sql = " AND pros.iid_plaza = ".$co_plaza." ";
	 			break;

	 		default:
	 			$where_sql = " ";
	 			break;
	 	}
	 	/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
	 	$sql = "SELECT DISTINCT to_char(pros.d_fecha_registro, 'yyyy') AS anio
				FROM co_prospecto pros
				WHERE pros.s_status in (1, 0) AND pros.d_fecha_registro IS NOT NULL ".$where_sql."
				ORDER BY anio DESC";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_select_anio_co[] = $row;
	 	}
	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_select_anio_co;
	 }



// *-*-*-*-*-*-*-* FUNCION PARA PORCENTAJE CIERRE PROMOTOR  *-*-*-*-*-*-*-* //
	 public function cierre_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co)
	 {
	 	$conn = conexion::conectar();
	 	/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
	 	if ($anio_co == true ){
	 		if ( $fec_ini_co == true && $fec_fin_co == true ){
	 		$res_fec_ini_fin_co = " pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
	 		}else{
	 		$sql_anio_co = " to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
	 		}
	 	}
	 	if ($co_plaza == true ){
	 		$sql_co_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
	 	}
	 	/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
	 	$sql = "SELECT * FROM (
	 			SELECT COUNT(pros.iid_num_prospecto) AS total_prospectos, SUM(info.n_porcent_cierre) AS porcentaje, pros.iid_promotor AS id_promotor, promo.v_nombre AS promo_nombre, promo.v_apellido_pat AS promo_ape_pat, promo.v_apellido_mat AS promo_ape_mat, promo.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, promo.s_status AS status, promo.d_fecha_ingreso AS f_registro, pla.v_siglas AS plaza_siglas
				FROM co_prospecto pros
				LEFT JOIN co_prospecto_info info ON pros.iid_num_prospecto = info.iid_num_prospecto
				LEFT JOIN co_promotor promo ON promo.iid_promotor = pros.iid_promotor
				LEFT JOIN plaza pla ON pla.iid_plaza = promo.iid_plaza
				WHERE ".$sql_anio_co.$res_fec_ini_fin_co.$sql_co_plaza." AND PROS.S_STATUS = 3
				GROUP BY pros.iid_promotor, promo.v_nombre , promo.v_apellido_pat, promo.v_apellido_mat, promo.iid_plaza, pla.v_razon_social, promo.s_status, promo.d_fecha_ingreso,pla.v_siglas
				ORDER BY porcentaje DESC NULLS LAST, total_prospectos DESC
				)";

			//	echo $sql;
	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_cierre_promotor[] = $row;
	 	}
	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_cierre_promotor;
	 }

// *-*-*-*-*-*-*-* FUNCION PARA MAYOR PROMOTOR CON PROSPECTOS  *-*-*-*-*-*-*-* //
	 public function prospectos_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co)
	 {
	 	$conn = conexion::conectar();
	 	/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
	 	if ($anio_co == true ){
	 		if ( $fec_ini_co == true && $fec_fin_co == true ){
	 		$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
	 		}else{
	 		$sql_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
	 		}
	 	}
	 	if ($co_plaza == true ){
	 		$sql_co_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
	 	}
	 	/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
	 	$sql = "SELECT * FROM (
				SELECT count(pros.iid_num_prospecto) AS t_prospectos, pros.iid_promotor AS id_promotor, promo.v_nombre AS promo_nombre, promo.v_apellido_pat AS promo_ape_pat, promo.v_apellido_mat AS promo_ape_mat, promo.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, promo.s_status AS status
				FROM co_prospecto pros
				INNER JOIN co_promotor promo on promo.iid_promotor = pros.iid_promotor
				LEFT JOIN plaza pla ON pla.iid_plaza = promo.iid_plaza
        		WHERE promo.s_status = 1  AND pros.s_status in (1, 0) ".$sql_anio_co.$res_fec_ini_fin_co.$sql_co_plaza."
				GROUP BY  pros.iid_promotor, promo.v_nombre, promo.v_apellido_pat, promo.v_apellido_mat, promo.iid_plaza, pla.v_razon_social, promo.s_status
				ORDER BY t_prospectos DESC
				)
				WHERE ROWNUM <= 10";

	 	$stid = oci_parse($conn, $sql) ;
	 			oci_execute($stid);

	 	while ( ($row = oci_fetch_assoc($stid)) != false )
	 	{
	 		$this->res_prospectos_promotor[] = $row;
	 	}
	 		oci_free_statement($stid);
	 		oci_close($conn);
	 		return $this->res_prospectos_promotor;
	 }

// *-*-*-*-*-*-*-* FUNCION PARA MAYOR PROMOTOR CON CLIENTE  *-*-*-*-*-*-*-* //
	 function clientes_promotor($anio_co,$co_plaza,$fec_ini_co,$fec_fin_co)
	 {
	 	$conn = conexion::conectar();
	 	/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
	 	if ($anio_co == true ){
	 		if ( $fec_ini_co == true && $fec_fin_co == true ){
	 		$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
	 		}else{
	 		$sql_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
	 		}
	 	}
	 	if ($co_plaza == true ){
	 		$sql_co_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
	 	}
	 	/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
	 	$sql = "SELECT * FROM (
				SELECT COUNT(pros.iid_num_prospecto) AS t_clientes, pros.iid_promotor AS id_promotor, promo.v_nombre AS promo_nombre, promo.v_apellido_pat AS promo_ape_pat, promo.v_apellido_mat AS promo_ape_mat, promo.iid_plaza AS id_plaza, pla.v_razon_social AS plaza, promo.s_status AS status, promo.d_fecha_ingreso AS f_ingreso
				FROM co_prospecto pros
				INNER JOIN co_promotor promo on promo.iid_promotor = pros.iid_promotor
				LEFT JOIN plaza pla on pla.iid_plaza = promo.iid_plaza
				WHERE promo.s_status = 1 AND pros.s_status = 3 ".$sql_anio_co.$sql_co_plaza.$res_fec_ini_fin_co."
				GROUP BY  pros.iid_promotor, promo.v_nombre, promo.v_apellido_pat, promo.v_apellido_mat, promo.iid_plaza, pla.v_razon_social, promo.s_status, promo.d_fecha_ingreso
				ORDER BY t_clientes DESC, f_ingreso DESC
				)
				WHERE ROWNUM <= 10";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid);

		while ( ($row = oci_fetch_assoc($stid)) != false )
		{
			$this->res_clientes_promotor[] = $row ;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_clientes_promotor;
	 }

// *-*-*-*-*-*-*-* FUNCION PARA GRAFICA DE BARRAS PROSPECTOS POR MES Y ANIO  *-*-*-*-*-*-*-* //
	 function grafica_mes_prospectos($anio_co,$co_plaza,$promotor_co,$t_servicio_co,$t_prospecto_co,$fec_ini_co,$fec_fin_co,$status_pros_co_graf)
	 {
	 	$conn = conexion::conectar();
	 	/* ------------ INICIA CONCATENACION WHERE SQL ------------ */
		if ($anio_co== true){
			if ( $fec_ini_co == true && $fec_fin_co == true ){
	 		$res_fec_ini_fin_co = " AND pros.d_fecha_registro >= trunc(to_date('".$fec_ini_co."','dd-mm-yyyy') ) AND pros.d_fecha_registro < trunc(to_date('".$fec_fin_co."','dd-mm-yyyy') ) +1 ";
	 		}else{
	 		$where_sql_anio_co = " AND to_char(pros.d_fecha_registro, 'yyyy') = ".$anio_co." ";
	 	    }
	 	}
	 	if ($co_plaza== true){
	 		$where_sql_plaza = " AND pros.iid_plaza = ".$co_plaza." ";
	 	}
	 	if ($promotor_co== true){
	 		$where_sql_promo = " AND pros.iid_promotor = ".$promotor_co." ";
	 	}
	 	if ($t_servicio_co == true || $t_prospecto_co == true){

	 		switch (true) {
	 			case ($t_servicio_co == true) && ($t_prospecto_co == false) :
	 				$inner_join = " INNER JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto and info.n_tipo_servicio = ".$t_servicio_co."  ";
	 				break;
	 			case ($t_prospecto_co == true) && ($t_servicio_co == false):
	 				$inner_join = " INNER JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto and info.n_tipo_cliente = ".$t_prospecto_co."  ";
	 				break;
	 			default:
	 				$inner_join = " INNER JOIN co_prospecto_info info ON info.iid_num_prospecto = pros.iid_num_prospecto and info.n_tipo_cliente = ".$t_prospecto_co." and info.n_tipo_servicio = ".$t_servicio_co." ";
	 				break;
	 		}
	 	}
	 	/* ------------ TERMINA CONCATENACION WHERE SQL ------------ */
	 	$sql = "SELECT mes.mes AS num_mes, mes.nom_mes AS mes, count(pros.iid_num_prospecto) AS t_prospecto
				FROM vista_dashboard_mes_prospecto mes
				LEFT OUTER JOIN co_prospecto pros ON to_char(pros.d_fecha_registro, 'mm')  = mes.mes
				AND pros.s_status IN (".$status_pros_co_graf.")  ".$where_sql_anio_co.$res_fec_ini_fin_co.$where_sql_plaza.$where_sql_promo.$inner_join."
            	GROUP BY mes.mes, mes.nom_mes
				ORDER BY mes.mes ASC";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid);

		while ( ($row = oci_fetch_assoc($stid)) != false )
		{
			$this->res_grafica_mes_prospectos[] = $row ;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_grafica_mes_prospectos;
	 }
/* ============ INICIA FUNCION PARA CALCULAR TIEMPO DE FECHAS ============ */
	function tiempoTranscurridoFechas($fechaInicio,$fechaFin)
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
	    if($fecha->d >= 0)
	    {
	        $tiempo .= $fecha->d;

	        if($fecha->d == 1)
	            $tiempo .= " día ";
	        else
	            $tiempo .= " días ";
	    }

	    // //horas
	    // if($fecha->h > 0)
	    // {
	    //     $tiempo .= $fecha->h;

	    //     if($fecha->h == 1)
	    //         $tiempo .= " hora, ";
	    //     else
	    //         $tiempo .= " horas, ";
	    // }

	    // //minutos
	    // if($fecha->i > 0)
	    // {
	    //     $tiempo .= $fecha->i;

	    //     if($fecha->i == 1)
	    //         $tiempo .= " minuto";
	    //     else
	    //         $tiempo .= " minutos";
	    // }
	    // else if($fecha->i == 0) //segundos
	    //     $tiempo .= $fecha->s." segundos";

	    return $tiempo;
	}
/* ============ TERMINA FUNCION PARA CALCULAR TIEMPO DE FECHAS ============ */

}
