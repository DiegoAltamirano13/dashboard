<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Calculo_Ocupacion
{
	public function graficaMensual($plaza, $fil_habilitado, $fecha){

		$conn = conexion::conectar();
		$res_array = array();

		switch($plaza){
 		 case 'CÓRDOBA': $in_plaza = 3; break;
 		 case 'MÉXICO': $in_plaza = 4; break;
 		 case 'GOLFO': $in_plaza = 5; break;
 		 case 'PENINSULA': $in_plaza = 6; break;
 		 case 'PUEBLA': $in_plaza = 7; break;
 		 case 'BAJIO': $in_plaza = 8; break;
 		 case 'OCCIDENTE': $in_plaza = 17; break;
 		 case 'NORESTE': $in_plaza = 18; break;
 		 default: $in_plaza = "3,4,5,6,7,8,17,18"; break;
 	 }

	 	//echo $fecha;

		$fecha_ini = substr($fecha, 0, 10);
		$fecha_fin = substr($fecha, 11, 10);
		#echo $fecha_ini.'<br>'.$fecha_fin;

		if ($fil_habilitado == 'on') {
			$filtro_hab = " AND (NVL2(k.idalo, 'SI', 'NO') = 'SI' and A.CD IS NOT NULL )";
		}else {
			$filtro_hab = " AND (NVL2(k.idalo, 'SI', 'NO') = 'NO' and A.CD IS NULL )";
		}
		$sql ="SELECT t.id,
						       t.containernumber,
						       t.invoicenumber,
						       t.partno,
						       t.quantity,
						       TO_CHAR(t.eta, 'dd/mm/yyyy') AS ETA,
						       a.arribo,
						       a.cd,
						       s.id_solicitud,
						       NVL2(k.idalo, 'SI', 'NO') AS SOLIC,
									 0 aviso,
						       n.vid_usuario,
						       n.d_fecha,
						       n.v_transportes,
						       n.v_nombre_chofer,
									 TO_CHAR(s.d_fec_llegada_real, 'dd/mm/yyyy') AS D_FEC_LLEGADA_REAL
			  from invoices_container@dlalo t,
			       op_in_solicitud_carga_descarga s,
						 op_in_notificacion_alo n,
			       (select distinct d.iid_arribo      arribo,
			                        d.v_coment        idalo,
			                        r.vid_certificado cd
			          from op_in_arribos_nad_det d, op_in_recibo_deposito r
			         where d.v_coment is not null
			           and d.vid_recibo = r.vid_recibo(+)) a,
			       (select distinct t.id_invoices_container idalo
			          from logs@dlalo t
			         where (t.evento = 1 or t.evento = 6)) k
			 where t.id = a.idalo(+)
			   and a.arribo = s.iid_arr_ret(+)
			   and t.id = k.idalo(+)
			   and t.numero_almacen in ($in_plaza)
 			 	 $filtro_hab
				 and t.id = n.id_invoices_container(+)
				 and (TO_DATE(t.fecha_registro , 'dd/mm/yyy') >= TO_DATE('$fecha_ini', 'dd/mm/yyyy')
			 				AND TO_DATE(t.fecha_registro , 'dd/mm/yyy') <= TO_DATE('$fecha_fin', 'dd/mm/yyyy') )
			 order by t.id";

			 #echo $sql;

				$stid = oci_parse($conn,$sql);
				oci_execute($stid);
				while (($row = oci_fetch_assoc($stid))!= false) {
					$res_array[] = $row;
				}
				oci_free_statement($stid);
				oci_close($conn);
				return $res_array;
	}

	public function sql($option,$depto,$plaza){
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM DUAL";
	 	switch ($option) {
			case '1':
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0), 'DD/MM/YYYY') mes1, TO_CHAR(SYSDATE, 'DD/MM/YYYY') mes2 FROM DUAL";
				break;
			case '2':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";
				break;
			case '3':
				$sql = " SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep ";
				break;
			case '4':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
				break;
			case '5':
				$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91) and v_sscuenta = 0
									union all
									select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
				break;
			//case '6':
				//break;
			default:
				$sql = "SELECT * FROM DUAL";
				break;
		}

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}


	/*++++++++++++++++++++++++ SQL DINAMICO FROM DUAL ++++++++++++++++++++++++*/
	public function dual($sql)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	public function filtros($option,$depto)
	{
		$conn = conexion::conectar();
		$res_array = array();

		switch ($option) {
			case '1':
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ";
				break;
			case '2':
				$sql = "SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep";
				break;
			case '3':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
				break;
		}

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}


	/*****************************ALMACEN **************************************************/
	function almacenSql($plaza){
		$conn = conexion::conectar();
		$res_array = array();
		switch($plaza){
 		 //case 'CORPORATIVO': $in_plaza = 2; break;
 		 case 'CÓRDOBA': $in_plaza = 3; break;
 		 case 'MÉXICO': $in_plaza = 4; break;
 		 case 'GOLFO': $in_plaza = 5; break;
 		 case 'PENINSULA': $in_plaza = 6; break;
 		 case 'PUEBLA': $in_plaza = 7; break;
 		 case 'BAJIO': $in_plaza = 8; break;
 		 case 'OCCIDENTE': $in_plaza = 17; break;
 		 case 'NORESTE': $in_plaza = 18; break;
 		 default: $in_plaza = "3,4,5,6,7,8,17,18"; break;
 	 }
		$sql = "SELECT DISTINCT(AL.IID_ALMACEN), AL.V_NOMBRE
						FROM ALMACEN AL
            INNER JOIN ALMACEN_CAPACIDAD ALC ON AL.IID_PLAZA = ALC.IID_PLAZA AND AL.IID_ALMACEN = ALC.IID_ALMACEN
						INNER JOIN PRUEBA_SUBIDA PRU ON ALC.IID_ALMACEN = PRU.IID_ALMACEN AND PRU.ID_CLIENTE = 2905
						WHERE AL.IID_PLAZA = ".$in_plaza." AND AL.IID_ALMACEN NOT IN (9998, 9999) ORDER BY AL.IID_ALMACEN";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}

	function almacenNombre($plaza,$almacen){
		$conn = conexion::conectar();
		$res_array = array();
		switch($plaza){
		 //case 'CORPORATIVO': $in_plaza = 2; break;
		 case 'CÓRDOBA': $in_plaza = 3; break;
		 case 'MÉXICO': $in_plaza = 4; break;
		 case 'GOLFO': $in_plaza = 5; break;
		 case 'PENINSULA': $in_plaza = 6; break;
		 case 'PUEBLA': $in_plaza = 7; break;
		 case 'BAJIO': $in_plaza = 8; break;
		 case 'OCCIDENTE': $in_plaza = 17; break;
		 case 'NORESTE': $in_plaza = 18; break;
		 default: $in_plaza = "3,4,5,6,7,8,17,18"; break;
	 }
		$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_ALMACEN = $almacen AND IID_PLAZA = $in_plaza AND IID_ALMACEN NOT IN (9998, 9999) ORDER BY IID_ALMACEN";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		#echo $sql;
		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
	}
}
