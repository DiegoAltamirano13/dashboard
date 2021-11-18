<?php
if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
	header("Location: ../index.php");
	die();
}else{/*solo entra con post*/

	include_once '../class/Venta_promotor.php';
	$insObj = new VentaPromotor();

	/* ------------ AJAX PARA PLAZA ----------- */
	if ( isset($_POST["anio"]) && !isset($_POST["promotor"]) ){ 

		$res_anioMov = $insObj->anioMov();

			echo '<option></option>';
		for ($i=0; $i <count($res_anioMov) ; $i++) { 
			echo '<option value="'.$res_anioMov[$i]["ANIO"].'">'.$res_anioMov[$i]["ANIO"].'</option>';
		}

	}


	/* ------------ AJAX PARA PROMOTOR ----------- */
	if ( isset( $_POST["promotor"] ) ){ 
 
		$res_selectPromotor = $insObj->selectPromotor($_POST["anio"],$_POST["d17_pro_pla"]);

		echo json_encode($res_selectPromotor);
		
		// echo '<option></option>';
		// echo '<option value="ALL">ALL</option>';
		// for ($i=0; $i <count($res_selectPromotor) ; $i++) { 
		// 	echo '<option value="'.$res_selectPromotor[$i]["IID_PROMOTOR"].'">'.'('.$res_selectPromotor[$i]["IID_PROMOTOR"].')'.$res_selectPromotor[$i]["V_NOMBRE"].' '.$res_selectPromotor[$i]["V_APELLIDO_PAT"].' '.$res_selectPromotor[$i]["V_APELLIDO_MAT"].'</option>';
		// }

	}

	/* ------------ AJAX PARA PLAZA ----------- */
	if ( isset( $_POST["plaza"] ) ){ 
 
		$res_selectPlaza = $insObj->selectPlaza();

			echo '<option></option>';
			echo '<option value="ALL">ALL</option>';
		for ($i=0; $i <count($res_selectPlaza) ; $i++) {
			echo '<option value="'.$res_selectPlaza[$i]["V_RAZON_SOCIAL"].'">'.'('.$res_selectPlaza[$i]["IID_PLAZA"].')'.$res_selectPlaza[$i]["V_RAZON_SOCIAL"].'</option>';
		}

	}

	/* ------------ AJAX PARA DETALLE DE FACTURACION DE CADA CLIENTE ----------- */
	if ( isset( $_POST["btnAjaxModalDet"] ) ){ 

		$d17_pro_pla = $_POST["d17_pro_pla"];
		$det_anio =$_POST["det_anio"];
		$det_idPromotor =$_POST["det_idPromotor"];
		$det_idPlaza =$_POST["det_idPlaza"];
		$det_mes =$_POST["det_mes"]; 

		$res_selectDetFacturado = $insObj->tablaDetFacturado($d17_pro_pla,$det_anio,$det_idPromotor,$det_idPlaza,$det_mes);

		echo $res_selectDetFacturado;

	}

}