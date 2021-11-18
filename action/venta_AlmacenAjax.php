<?php
if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
	header("Location: ../index.php");
	die();
}else{/*solo entra con post*/

	include_once '../class/habilitaciones_bodega.php';
	$insObj = new VentaAlmacen();

	/* -------------- SELECT AÑO PRESUPUESTO ---------------- */
	if ( isset($_POST["selectAnio"]) ){

		$sql = "SELECT DISTINCT p.n_valor_anio AS anio FROM co_promotor_fac_vs_pre p WHERE p.n_valor_anio > 2017 ORDER BY anio DESC";
		$resModel = $insObj->sql($sql);

		echo json_encode($resModel);
	}
	/* -------------- /.SELECT AÑO PRESUPUESTO ---------------- */


	/* -------------- SELECT PROMOTOR PRESUPUESTO ---------------- */
	if ( isset($_POST["selectPromotor"]) ){

		$sql = "SELECT vta.id_promotor, pro.iid_empleado, NVL(pro.v_nombre,pla.v_razon_social) v_nombre,pro.v_apellido_pat, pro.v_apellido_mat, vta.iid_plaza, vta.n_tipo
				FROM co_promotor_fac_vs_pre vta
				LEFT JOIN co_promotor pro ON pro.iid_promotor = vta.id_promotor
				LEFT JOIN plaza pla ON pla.iid_plaza = vta.iid_plaza
				WHERE vta.n_tipo = ".$_POST["pre"]." AND vta.n_valor_anio = ".$_POST["fecha"]." ORDER BY v_nombre";
		$resModel = $insObj->sql($sql);

		echo json_encode($resModel);
	}
	/* -------------- /.SELECT PROMOTOR PRESUPUESTO ---------------- */


	/* -------------- SELECT PLAZA PRESUPUESTO ---------------- */
	if ( isset($_POST["selectPlaza"]) ){

		if ( $_POST["pre"] == 3){
			$sql = " SELECT NVL(pro.v_nombre,pla.v_razon_social) v_razon_social, vta.iid_plaza
				FROM co_promotor_fac_vs_pre vta
				LEFT JOIN co_promotor pro ON pro.iid_promotor = vta.id_promotor
				LEFT JOIN plaza pla ON pla.iid_plaza = vta.iid_plaza
				WHERE vta.n_tipo = ".$_POST["pre"]." AND vta.n_valor_anio = ".$_POST["fecha"]." ORDER BY v_razon_social ";
		}else{
			$sql = " (SELECT pla.iid_plaza, pla.v_razon_social, pla.v_siglas FROM co_promotor_fac_vs_pre vta
				INNER JOIN co_promotor pro ON pro.iid_promotor = vta.id_promotor INNER JOIN plaza pla ON pla.iid_plaza = pro.iid_plaza)
				UNION
				(SELECT pla.iid_plaza, pla.v_razon_social, pla.v_siglas FROM co_promotor pro
				INNER JOIN plaza pla ON pla.iid_plaza = pro.iid_plaza WHERE pro.iid_promotor in (173,172, 187,196,189,184,195) )
				ORDER BY v_razon_social ";
		}
		$resModel = $insObj->sql($sql);

		echo json_encode($resModel);
	}
	/* -------------- /.SELECT PLAZA PRESUPUESTO ---------------- */
	if (isset($_POST["selectAlmacen"])) {
		if ($_POST["pre"]== 3) {
			if ($_POST["plaza"] == "ALL") {
				$sql = " SELECT (ALM.V_NOMBRE) AS V_NOMBRE , ALM.IID_ALMACEN AS IID_ALMACEN FROM ALMACEN ALM WHERE ALM.S_STATUS = 1";
			}
			else {
				$sql = " SELECT ALM.V_NOMBRE AS V_NOMBRE , ALM.IID_ALMACEN AS IID_ALMACEN FROM ALMACEN ALM WHERE ALM.S_STATUS = 1 AND  ALM.IID_PLAZA =".$_POST["plaza"]."";
			}

		}
		$resModel = $insObj->sql($sql);
		echo json_encode($resModel);
	}

	if (isset($_POST["selectCliente"])) {
		if ($_POST["pre"]== 3) {
			if ($_POST["plaza"] == "ALL") {
				$sql = " SELECT DISTINCT(C.V_RAZON_SOCIAL) AS RAZONSOCIAL, C.IID_NUM_CLIENTE AS IID_CLIENTE
								 FROM CO_CONVENIO_CONTRATO_ALMACEN CCC
								 INNER JOIN CO_CONVENIO_CONTRATO CC ON CC.NID_FOLIO = CCC.NID_FOLIO
								 INNER JOIN ALMACEN A ON CCC.IID_ALMACEN = A.IID_ALMACEN
								 INNER JOIN CLIENTE C ON CC.IID_NUM_CLIENTE = C.IID_NUM_CLIENTE
								 ORDER BY C.V_RAZON_SOCIAL";
			}
			else {
				if ($_POST["almacen"] == "ALL") {
								$sql = " SELECT DISTINCT(C.V_RAZON_SOCIAL) AS RAZONSOCIAL, C.IID_NUM_CLIENTE AS IID_CLIENTE
												 FROM CO_CONVENIO_CONTRATO_ALMACEN CCC
												 INNER JOIN CO_CONVENIO_CONTRATO CC ON CC.NID_FOLIO = CCC.NID_FOLIO
												 INNER JOIN ALMACEN A ON CCC.IID_ALMACEN = A.IID_ALMACEN
												 INNER JOIN CLIENTE C ON CC.IID_NUM_CLIENTE = C.IID_NUM_CLIENTE
												 WHERE A.IID_PLAZA =  ".$_POST["plaza"]."
												 ORDER BY C.V_RAZON_SOCIAL";
				}else {
					      $sql = " SELECT DISTINCT(C.V_RAZON_SOCIAL) AS RAZONSOCIAL, C.IID_NUM_CLIENTE AS IID_CLIENTE
												 FROM CO_CONVENIO_CONTRATO_ALMACEN CCC
												 INNER JOIN CO_CONVENIO_CONTRATO CC ON CC.NID_FOLIO = CCC.NID_FOLIO
												 INNER JOIN ALMACEN A ON CCC.IID_ALMACEN = A.IID_ALMACEN
												 INNER JOIN CLIENTE C ON CC.IID_NUM_CLIENTE = C.IID_NUM_CLIENTE
											   WHERE A.IID_PLAZA =  ".$_POST["plaza"]." AND A.IID_ALMACEN = ".$_POST["almacen"]."
												 ORDER BY C.V_RAZON_SOCIAL";
				}

			}


		}
		$resModel = $insObj->sql($sql);
		echo json_encode($resModel);
	}

	/* -------------- INFORMACION DE PRESUPUESTOS ---------------- */
	if ( isset($_POST["tablaInfoPre"]) ){

		$resModel = $insObj->tablaPresupuesto($_POST["pre"],$_POST["fecha"]);
		echo json_encode($resModel);
	}
	/* -------------- /.INFORMACION DE PRESUPUESTOS ---------------- */



	/* -------------- WHERE PRESUPUESTOS ---------------- */
	if ( isset($_POST["wherePresupuesto"]) ){

		$resModel = $insObj->wherePresupuesto($_POST["v_pre"],$_POST["v_fecha"],$_POST["v_promotor"],$_POST["v_plaza"]);
		echo json_encode($resModel);

	}
	/* -------------- /.WHERE PRESUPUESTOS ---------------- */



	/* -------------- VENTA REAL ---------------- */
	if ( isset($_POST["ventaReal"]) ){

		$resModel = $insObj->ventaReal($_POST["v_pre"],$_POST["v_fecha"],$_POST["v_promotor"],$_POST["v_plaza"],$_POST["v_almacen"]);
		echo json_encode($resModel);

	}
	/* -------------- /.VENTA REAL ---------------- */


	/* -------------- INFO FAC ---------------- */
	if ( isset( $_POST["modalDetFac"] ) ){

		$res_selectDetFacturado = $insObj->tablaDetFacturado($_POST["pre"],$_POST["fecha"],$_POST["v_promotor"],$_POST["v_plaza"],$_POST["mes"],$_POST["v_almacen"]);
		echo json_encode($res_selectDetFacturado);

	}
	/* -------------- /.INFO FAC ---------------- */


	/* -------------- DETALLE FACTURACION CONSOLIDADA ---------------- */
	if ( isset( $_POST["DetFacConsol"] ) ){

		$res_selectDetFacturado = $insObj->histMesConsol($_POST["pre"],$_POST["fecha"],$_POST["v_promotor"],$_POST["v_plaza"],$_POST["mes"],$_POST["v_det"]);
		echo json_encode($res_selectDetFacturado);

	}
	/* -------------- /.DETALLE FACTURACION CONSOLIDADA ---------------- */



	/* -------------- DETALLE ACOMULADO CLIENTES NUEVOS ---------------- */

	if ( isset( $_POST["DetFacCliNew"] ) ){

		$res_selectDetFacturado = $insObj->histMesClieNew($_POST["pre"],$_POST["fecha"],$_POST["v_promotor"],$_POST["v_plaza"],$_POST["mes"],$_POST["v_det"]);
		echo json_encode($res_selectDetFacturado);

	}
	/* -------------- /.DETALLE ACOMULADO CLIENTES NUEVOS ---------------- */

}
