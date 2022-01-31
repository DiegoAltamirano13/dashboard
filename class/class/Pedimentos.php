<?php
/**
* © Argo Almacenadora ®
* Fecha: 30/05/2017
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Comercio Exterior
* Version --
*/
include_once '../libs/conOra.php';


class Pedimentos
{

	function __construct()
	{
		$res_widgets_ped = array();
		$res_graf_dona_ped = array();
		$res_tabla_ped_alm = array();
		$res_historial_pedimento = array();
	}

// ************************** FUNCION PARA FECHA DEL LA BASE **************************  //

	function date_base()
	{
		$conn = conexion::conectar();

		$sql = "SELECT TO_CHAR(SYSDATE, 'dd-mm-yyyy') AS FECHA FROM DUAL";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_row($stid)) != false) {
    		$this->res_date_base = $row["0"];
		}

			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_date_base;

	}

// ************************** FUNCION PARA WIDGETS PEDIMENTOS **************************  //
	function widgets_ped($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ( $ce_pedi_dia == true ){
			switch (true) {
				case ($fec_ini_ce_ped == true) && ($fec_fin_ce_ped == true):
					$and_sql_fecha = " AND fec.d_fecha >= trunc( to_date('".$fec_ini_ce_ped."','dd-mm-yyyy') )
        							   AND fec.d_fecha < trunc( to_date('".$fec_fin_ce_ped."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_sql_fecha = " AND to_char(fec.d_fecha, 'dd-mm-yyyy') = '".$ce_pedi_dia."' ";
					break;
			}
		}
		if ( $ce_pedi_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_pedi_plaza."' ";
		}
		if ( $ce_pedi_alm == true ){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_pedi_alm."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT COUNT(cot_enc.vid_cve_ped_ext) AS total_ped, cot_enc.vid_cve_ped_ext AS cve_ped
				FROM op_ce_fechas_ped_ext fec
				INNER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio
				INNER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE fec.iid_cve_tipo = 2 AND cot_enc.i_status >=4
		        ".$and_sql_fecha.$and_sql_plaza.$and_sql_almacen."
		        GROUP BY cot_enc.vid_cve_ped_ext";

					#echo $sql;

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_widgets_ped[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_widgets_ped;
	}


	///***************************************************widgets valor ***//////////////////////////////////////////
	function widgets_pedValue($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ( $ce_pedi_dia == true ){
			switch (true) {
				case ($fec_ini_ce_ped == true) && ($fec_fin_ce_ped == true):
					$and_sql_fecha = " AND fec.d_fecha >= trunc( to_date('".$fec_ini_ce_ped."','dd-mm-yyyy') )
												 AND fec.d_fecha < trunc( to_date('".$fec_fin_ce_ped."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_sql_fecha = " AND to_char(fec.d_fecha, 'dd-mm-yyyy') = '".$ce_pedi_dia."' ";
					break;
			}
		}
		if ( $ce_pedi_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_pedi_plaza."' ";
		}
		if ( $ce_pedi_alm == true ){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_pedi_alm."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = " SELECT SUM(CASE
                WHEN C_CUPO.S_TIPO = 2 THEN
                      ped_enc.c_valor_aduana * (SELECT R.C_TIPO_CAMBIO
                         FROM OP_CE_TIPO_CAMBIO R
                              WHERE R.DID_FECHA LIKE C_CUPO.D_FECHA_EXPEDICION
                              AND ROWNUM = 1)
                WHEN C_CUPO.S_TIPO = 1 THEN
                      ped_enc.c_valor_aduana
                END) AS TOTALG1,
                COT_ENC.vid_cve_ped_ext AS CVE_PED
 FROM OP_CE_FECHAS_PED_EXT FEC
 INNER JOIN Op_Ce_Cot_Ext_Enc COT_ENC ON COT_ENC.VNO_PED_IMP = FEC.VNO_PED_IMP AND COT_ENC.VID_FOLIO = FEC.VID_FOLIO
 INNER JOIN op_ce_ped_ext_enc ped_enc ON ped_enc.vno_ped_ext = cot_enc.vno_ped_ext
 INNER JOIN OP_CE_CARTAS_CUPO C_CUPO ON C_CUPO.VNO_PEDIMENTO = COT_ENC.VNO_PED_IMP
 INNER JOIN ALMACEN ALM ON ALM.IID_ALMACEN = C_CUPO.IID_ALMACEN
 INNER JOIN PLAZA PLA ON PLA.IID_PLAZA = ALM.IID_PLAZA
 WHERE FEC.IID_CVE_TIPO = 2
       AND COT_ENC.I_STATUS >= 4
			 ".$and_sql_fecha.$and_sql_plaza.$and_sql_almacen."
 GROUP BY cot_enc.vid_cve_ped_ext";

					#echo $sql;

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_widgets_ped[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_widgets_ped;
	}
// ************************** FUNCION PARA GRAFICA DE DONA PARA PEDIMENTOS **************************  //
	function graf_dona_ped($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ( $ce_pedi_dia == true ){
			switch (true) {
				case ($fec_ini_ce_ped == true) && ($fec_fin_ce_ped == true):
					$and_sql_fecha = " AND fec.d_fecha >= trunc( to_date('".$fec_ini_ce_ped."','dd-mm-yyyy') )
        							   AND fec.d_fecha < trunc( to_date('".$fec_fin_ce_ped."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_sql_fecha = " AND to_char(fec.d_fecha, 'dd-mm-yyyy') = '".$ce_pedi_dia."' ";
					break;
			}
		}
		if ( $ce_pedi_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_pedi_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT COUNT(cot_enc.vid_cve_ped_ext) AS t_pedimento, pla.iid_plaza AS id_plaza, pla.v_razon_social AS plaza
				,DECODE(TO_CHAR(pla.iid_plaza),
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
								) AS color, cot_enc.vid_cve_ped_ext AS cve_ped
				FROM op_ce_fechas_ped_ext fec
				INNER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio
				INNER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE fec.iid_cve_tipo = 2  AND cot_enc.i_status >=4
				".$and_sql_fecha.$and_sql_plaza."
				GROUP BY pla.iid_plaza, pla.v_razon_social, cot_enc.vid_cve_ped_ext ORDER BY Plaza desc";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_graf_dona_ped[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_graf_dona_ped;
	}
// ************************** FUNCION PARA GRAFICA DE DONA PARA PEDIMENTOS POR ALMACEN **************************  //
	function tabla_ped_alm($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ( $ce_pedi_dia == true ){
			switch (true) {
				case ($fec_ini_ce_ped == true) && ($fec_fin_ce_ped == true):
					$and_sql_fecha = " AND fec.d_fecha >= trunc( to_date('".$fec_ini_ce_ped."','dd-mm-yyyy') )
        							   AND fec.d_fecha < trunc( to_date('".$fec_fin_ce_ped."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_sql_fecha = " AND to_char(fec.d_fecha, 'dd-mm-yyyy') = '".$ce_pedi_dia."' ";
					break;
			}
		}
		if ( $ce_pedi_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_pedi_plaza."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT COUNT(cot_enc.vid_cve_ped_ext) AS t_pedimento, alm.iid_almacen AS id_almacen, alm.v_nombre AS almacen, alm.v_iniciales AS ini_almacen, pla.v_siglas AS plaza_sig, pla.v_razon_social AS plaza
				,DECODE(TO_CHAR(pla.iid_plaza),
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
								) AS color, cot_enc.vid_cve_ped_ext AS cve_ped
				FROM op_ce_fechas_ped_ext fec
				INNER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio
				INNER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE fec.iid_cve_tipo = 2  AND cot_enc.i_status >=4
				".$and_sql_fecha.$and_sql_plaza."
				GROUP BY alm.iid_almacen, alm.v_nombre, alm.v_iniciales, pla.v_siglas, pla.v_razon_social, pla.iid_plaza, cot_enc.vid_cve_ped_ext ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_ped_alm[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_ped_alm;
	}
// ************************** FUNCION PARA EL HISTORIAL DE PEDIMENTOS **************************  //
	function historial_pedimento($ce_pedi_plaza,$ce_pedi_alm)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ( $ce_pedi_plaza == true){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_pedi_plaza."' ";
		}
		if ( $ce_pedi_alm == true){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_pedi_alm."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT  DISTINCT(to_char(fec_ped.d_fecha, 'dd-mm-yyyy')) AS fecha, cot_enc.vid_cve_ped_ext AS cve_ped
				FROM op_ce_fechas_ped_ext fec_ped
				INNER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec_ped.vno_ped_imp AND cot_enc.vid_folio = fec_ped.vid_folio
				LEFT JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				LEFT JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				LEFT JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE fec_ped.iid_cve_tipo = 2 AND cot_enc.i_status >=4
				".$and_sql_plaza.$and_sql_almacen."
				ORDER BY TO_DATE(fecha, 'dd-mm-yyyy') DESC ";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_historial_pedimento[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_historial_pedimento;
	}

// ************************** FUNCION PARA WIDGETS PEDIMENTOS **************************  //
	function tabla_pedimentos($ce_pedi_dia,$fec_ini_ce_ped,$fec_fin_ce_ped,$ce_pedi_plaza,$ce_pedi_alm,$ce_cv_pedimento)
	{
		/* ----------------- CONCATENACION SQL ----------------- */
		if ( $ce_pedi_dia == true ){
			switch (true) {
				case ($fec_ini_ce_ped == true) && ($fec_fin_ce_ped == true):
					$and_sql_fecha = " AND fec.d_fecha >= trunc( to_date('".$fec_ini_ce_ped."','dd-mm-yyyy') )
        							   AND fec.d_fecha < trunc( to_date('".$fec_fin_ce_ped."','dd-mm-yyyy') ) +1 ";
					break;

				default:
					$and_sql_fecha = " AND to_char(fec.d_fecha, 'dd-mm-yyyy') = '".$ce_pedi_dia."' ";
					break;
			}
		}
		if ( $ce_pedi_plaza == true ){
			$and_sql_plaza = " AND pla.v_razon_social = '".$ce_pedi_plaza."' ";
		}
		if ( $ce_pedi_alm == true ){
			$and_sql_almacen = " AND alm.v_nombre = '".$ce_pedi_alm."' ";
		}
		if ( $ce_cv_pedimento == true ){
			$and_sql_cv_pedimento = " AND cot_enc.vid_cve_ped_ext = '".$ce_cv_pedimento."' ";
		}
		/* ----------------- CONCATENACION SQL ----------------- */

		$conn = conexion::conectar();

		$sql = "SELECT ped_enc.iid_importador AS id_importador, ped_enc.v_nombre_impo AS importador, ped_enc.c_valor_aduana AS v_aduana, ped_enc.c_total AS t_impuestos_pag
				,cot_enc.v_pedimento_extraccion AS n_pedimento, cot_enc.vno_ped_imp AS pedimento_a4, cot_enc.vid_cve_ped_ext AS cve_pedimento
				, to_char(fec.d_fecha, 'dd-mm-yyyy') AS fec_pago, fec.iid_cve_tipo AS tipo_ped
				,c_cupo.v_cve_sidefi AS n_sidefi, c_cupo.iid_almacen, pla.iid_plaza, pla.v_razon_social
				FROM op_ce_fechas_ped_ext fec
				INNER JOIN op_ce_cot_ext_enc cot_enc ON cot_enc.vno_ped_imp = fec.vno_ped_imp AND cot_enc.vid_folio = fec.vid_folio
				INNER JOIN op_ce_ped_ext_enc ped_enc ON ped_enc.vno_ped_ext = cot_enc.vno_ped_ext
				INNER JOIN op_ce_cartas_cupo c_cupo ON c_cupo.vno_pedimento = cot_enc.vno_ped_imp
				INNER JOIN almacen alm ON alm.iid_almacen = c_cupo.iid_almacen
				INNER JOIN plaza pla ON pla.iid_plaza = alm.iid_plaza
				WHERE fec.iid_cve_tipo = 2 AND cot_enc.i_status >=4
				".$and_sql_fecha.$and_sql_plaza.$and_sql_almacen.$and_sql_cv_pedimento."
				";

		$stid = oci_parse($conn, $sql);
				oci_execute($stid );

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$this->res_tabla_pedimentos[]=$row;
		}
			oci_free_statement($stid);
			oci_close($conn);
			return $this->res_tabla_pedimentos;
	}
/*----------------------------------------------------------------------------------------------------------*/
}
