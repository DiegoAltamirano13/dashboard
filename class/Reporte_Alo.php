<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Rep_Alo{
	/*====================== GRAFICA DE NOMINA PAGADA ======================*/
public function tabla_toneladas($plaza, $almacen, $proyecto, $fecha, $contenedor,$parte, $fil_check, $cond, $lote){

		$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";
		switch ($plaza) {
				case 'CORPORATIVO': $andPlaza = 2; break;
				case 'CÓRDOBA': $andPlaza = 3; break;
				case 'MÉXICO': $andPlaza = 4; break;
				case 'GOLFO': $andPlaza = 5; break;
				case 'PENINSULA': $andPlaza = 6; break;
				case 'PUEBLA': $andPlaza = 7; break;
				case 'BAJIO': $andPlaza = 8; break;
				case 'OCCIDENTE': $andPlaza = 17; break;
				case 'NORESTE': $andPlaza = 18; break;
				default: $andPlaza = "2,3,4,5,6,7,8,17,18"; break;
		}
    if ($almacen == "ALL") {
        $and_almacen = " ";
    }else {
        $and_almacen = "AND T.iid_almacen = $almacen ";
    }

    if ($proyecto == "ALL") {
        $and_proyecto = " ";
    }else {
        $and_proyecto = "AND T.PROYECTO LIKE '%".$proyecto."%'";
    }

		if ($contenedor == "ALL") {
        $and_contenedor = " ";
    }else {
        $and_contenedor = " AND CE.V_CONTENEDOR = '$contenedor'";
    }

		if ($parte == "ALL") {
        $and_parte = " ";
    }else {
        $and_parte = " AND T.MERCANCIA = '$parte'";
    }

		$fecha_inicio = substr($fecha,0,10);
		$fecha_fin = substr($fecha,11,10);

		if ($fil_check == "on") {
			$tiempo_filtrado = " and t.d_plazo_dep_ini <= to_date('$fecha_fin', 'dd/mm/yyyy')";
		}else {
			$tiempo_filtrado = " and t.d_plazo_dep_ini <= to_date('$fecha_fin', 'dd/mm/yyyy') ";
		}

		if ($cond == 'ALL') {
			$and_condicion = " ";
		}else {
			$and_condicion = " AND T.CALIDAD = '$cond'";
		}

		if ($lote =='ALL') {
			$andLote = " ";
		}else {
			$andLote =  " AND T.V_LOTE_SERIE = '$lote'";
		}

		$conn = conexion::conectar();
		$res_array = array();
				$sql ="SELECT c.v_razon_social,
											c.v_nombre_corto,
											a.v_nombre,
											t.mercancia,
											to_char(t.d_plazo_dep_ini, 'dd/mm/yyyy') AS d_plazo_dep_ini,
											t.vid_factura,
											t.SALDO,
											t.ume,
											t.certificado,
											t.arribo,
											t.proyecto,
											t.v_lote_serie,
											t.calidad,
											ce.v_contenedor,
											t.ubicacion
							from vista_wms_inventario t,
							     cliente c,
							     plaza p,
							     almacen a,
							     almacen_areas ar,
									 op_in_arribos_nad_enc ce
							where t.iid_num_cliente = 2905
							      and t.iid_num_cliente = c.iid_num_cliente
							      and t.iid_plaza = p.iid_plaza
							      and t.iid_almacen = a.iid_almacen
										and t.recibo = ce.vid_recibo_completo
   									and t.arribo = ce.iid_arribo
							      and t.s_area = ar.s_area
							      and a.iid_almacen = ar.iid_almacen
										and t.iid_plaza in ($andPlaza)
										$tiempo_filtrado
										$and_parte
										$and_contenedor
										$and_proyecto
										$and_almacen
										$and_condicion
										$andLote";

										#echo $sql;
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


	public function circle_graf($plaza, $almacen, $proyecto, $fecha, $contenedor,$parte, $fil_check, $cond, $lote){

			$andPlaza = "3, 4, 5, 6, 7, 8, 17, 18 ";
			switch ($plaza) {
					case 'CORPORATIVO': $andPlaza = 2; break;
					case 'CÓRDOBA': $andPlaza = 3; break;
					case 'MÉXICO': $andPlaza = 4; break;
					case 'GOLFO': $andPlaza = 5; break;
					case 'PENINSULA': $andPlaza = 6; break;
					case 'PUEBLA': $andPlaza = 7; break;
					case 'BAJIO': $andPlaza = 8; break;
					case 'OCCIDENTE': $andPlaza = 17; break;
					case 'NORESTE': $andPlaza = 18; break;
					default: $andPlaza = "2,3,4,5,6,7,8,17,18"; break;
			}
	    if ($almacen == "ALL") {
	        $and_almacen = " ";
	    }else {
	        $and_almacen = "AND T.iid_almacen = $almacen ";
	    }

	    if ($proyecto == "ALL") {
	        $and_proyecto = " ";
	    }else {
	        $and_proyecto = "AND T.PROYECTO LIKE '%".$proyecto."%'";
	    }

			if ($contenedor == "ALL") {
	        $and_contenedor = " ";
	    }else {
	        $and_contenedor = " AND CE.V_CONTENEDOR = '$contenedor'";
	    }

			if ($parte == "ALL") {
	        $and_parte = " ";
	    }else {
	        $and_parte = " AND T.MERCANCIA = '$parte'";
	    }

			$fecha_inicio = substr($fecha,0,10);
			$fecha_fin = substr($fecha,11,10);

			if ($fil_check == "on") {
				$tiempo_filtrado = " and t.d_plazo_dep_ini <= to_date('$fecha_fin', 'dd/mm/yyyy')";
			}else {
				$tiempo_filtrado = " and t.d_plazo_dep_ini <= to_date('$fecha_fin', 'dd/mm/yyyy')";
			}

			if ($cond == 'ALL') {
				$and_condicion = " ";
			}else {
				$and_condicion = " AND T.CALIDAD = '$cond'";
			}

			if ($lote =='ALL') {
				$andLote = " ";
			}else {
				$andLote =  " AND T.V_LOTE_SERIE = '$lote'";
			}

			$conn = conexion::conectar();
			$res_array = array();
					$sql ="SELECT t.calidad, sum (t.SALDO) as canti_cal
								from vista_wms_inventario t,
								     cliente c,
								     plaza p,
								     almacen a,
								     almacen_areas ar,
										 op_in_arribos_nad_enc ce
								where t.iid_num_cliente = 2905
								      and t.iid_num_cliente = c.iid_num_cliente
								      and t.iid_plaza = p.iid_plaza
								      and t.iid_almacen = a.iid_almacen
											and t.recibo = ce.vid_recibo_completo
	   									and t.arribo = ce.iid_arribo
								      and t.s_area = ar.s_area
								      and a.iid_almacen = ar.iid_almacen
											and t.iid_plaza in ($andPlaza)
											$tiempo_filtrado
											$and_parte
											$and_contenedor
											$and_proyecto
											$and_almacen
											$and_condicion
											$andLote
											and t.calidad is not null
									group by t.calidad";

										#	echo $sql;
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

public function sql($option,$depto,$plaza){
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM DUAL";
	 	switch ($option) {
			case '1':
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0), 'DD/MM/YYYY') mes1, TO_CHAR(SYSDATE, 'DD/MM/YYYY') mes2 FROM DUAL";
				break;
			case '2':
				$sql = " SELECT distinct p.iid_plaza AS PLAZA,  REPLACE(p.v_razon_social, ' (ARGO)') AS plaza, p.v_siglas
											  from vista_wms_inventario  t,
											       cliente               c,
											       plaza                 p,
											       almacen               a,
											       almacen_areas         ar,
											       op_in_arribos_nad_enc ce
											 where t.iid_num_cliente = 2905
											   and t.iid_num_cliente = c.iid_num_cliente
											   and t.iid_plaza = p.iid_plaza
											   and t.iid_almacen = a.iid_almacen
											   and t.recibo = ce.vid_recibo_completo
											   and t.arribo = ce.iid_arribo
											   and t.s_area = ar.s_area
											   and a.iid_almacen = ar.iid_almacen";
				break;
			case '3':
				$sql = " SELECT dep.iid_depto, dep.v_descripcion FROM rh_cat_depto dep ";
				break;
			case '4':
				$sql = "SELECT ar.iid_area, ar.v_descripcion FROM rh_cat_areas ar WHERE ar.iid_depto = ".$depto."";
				break;
			case '5':
				$sql = "SELECT v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(17, 50, 56, 57, 59, 60, 65, 73, 74, 77, 78, 83, 84, 85, 86, 88, 89, 91) and v_sscuenta = 0
									union all
									select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
				break;
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
		$sql = "SELECT distinct a.IID_ALMACEN, a.V_NOMBRE
					  from vista_wms_inventario  t,
					       cliente               c,
					       plaza                 p,
					       almacen               a,
					       almacen_areas         ar,
					       op_in_arribos_nad_enc ce
					 where t.iid_num_cliente = 2905
					   and t.iid_num_cliente = c.iid_num_cliente
					   and t.iid_plaza = p.iid_plaza
					   and t.iid_almacen = a.iid_almacen
					   and t.recibo = ce.vid_recibo_completo
					   and t.arribo = ce.iid_arribo
					   and t.s_area = ar.s_area
					   and a.iid_almacen = ar.iid_almacen
						 and t.iid_plaza in($in_plaza) AND a.IID_ALMACEN NOT IN (9998, 9999) ORDER BY a.IID_ALMACEN";

					#	 echo $sql;
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

  /*sacar proyecto X*/
  function proyectoSql(){
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT distinct(sd.v_translado) as proyecto from vista_in_rep_sdo s
            inner join op_in_vehiculos_recibidos sd on s.VID_RECIBO = sd.vid_recibo
            where s.IID_NUM_CLIENTE = 2905 and sd.v_translado is not null";
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
	/*SACAR CONTENEDOR*/
	function contSql(){
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT distinct(v_contenedor) AS CONTENEDOR
  FROM VISTA_IN_REP_SDO          T,
       CLIENTE                   C,
       ALMACEN                   A,
       OP_IN_PARTES              P,
       OP_IN_RECIBO_DEPOSITO     D,
       ALMACEN_AREAS             AA,
       CO_UME                    U,
       op_in_movimientos         m,
       op_in_vehiculos_recibidos VR,
       op_in_partes              pt,
       op_in_arribos_nad_enc     enc
 WHERE T.IID_NUM_CLIENTE = C.IID_NUM_CLIENTE
   AND T.IID_ALMACEN = A.IID_ALMACEN
   AND T.VID_NUM_PARTE = P.VID_NUM_PARTE
   AND T.VID_RECIBO = D.VID_RECIBO
   AND T.IID_NUM_CLIENTE = D.IID_NUM_CLIENTE
   AND T.IID_ALMACEN = D.IID_ALMACEN
   AND T.IID_ALMACEN = AA.IID_ALMACEN
   AND D.S_AREA = AA.S_AREA
   AND T.IID_UME = U.IID_UME
   and d.vid_recibo = VR.VID_RECIBO
   and (VR.v_translado NOT LIKE '%CUARENTENA%' or VR.v_translado = '' or
       VR.v_translado is null)
   and d.vid_recibo = m.vid_recibo
   and m.v_tipo_movto = 'ERD'
   and m.vid_movto = '001001001'
   AND T.IID_NUM_CLIENTE = 2905
   and d.vno_ped_imp = 'NACIONAL'
   and t.VID_NUM_PARTE = pt.vid_num_parte
   and (pt.v_descripcion_alternativa <> 'DISPOSITIVOS' or
       pt.v_descripcion_alternativa is null)
	 and D.VID_RECIBO = ENC.VID_RECIBO_COMPLETO
	 AND V_CONTENEDOR IS NOT NULL
 ORDER BY V_CONTENEDOR";
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
	/*MUMERO PARTE*/
	function parteSql(){
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT distinct(P.VID_NUM_PARTE) AS N_PARTE
  FROM VISTA_IN_REP_SDO          T,
       CLIENTE                   C,
       ALMACEN                   A,
       OP_IN_PARTES              P,
       OP_IN_RECIBO_DEPOSITO     D,
       ALMACEN_AREAS             AA,
       CO_UME                    U,
       op_in_movimientos         m,
       op_in_vehiculos_recibidos VR,
       op_in_partes              pt,
       op_in_arribos_nad_enc     enc
 WHERE T.IID_NUM_CLIENTE = C.IID_NUM_CLIENTE
   AND T.IID_ALMACEN = A.IID_ALMACEN
   AND T.VID_NUM_PARTE = P.VID_NUM_PARTE
   AND T.VID_RECIBO = D.VID_RECIBO
   AND T.IID_NUM_CLIENTE = D.IID_NUM_CLIENTE
   AND T.IID_ALMACEN = D.IID_ALMACEN
   AND T.IID_ALMACEN = AA.IID_ALMACEN
   AND D.S_AREA = AA.S_AREA
   AND T.IID_UME = U.IID_UME
   and d.vid_recibo = VR.VID_RECIBO
   and (VR.v_translado NOT LIKE '%CUARENTENA%' or VR.v_translado = '' or
       VR.v_translado is null)
   and d.vid_recibo = m.vid_recibo
   and m.v_tipo_movto = 'ERD'
   and m.vid_movto = '001001001'
   AND T.IID_NUM_CLIENTE = 2905
   and d.vno_ped_imp = 'NACIONAL'
   and t.VID_NUM_PARTE = pt.vid_num_parte
   and (pt.v_descripcion_alternativa <> 'DISPOSITIVOS' or
       pt.v_descripcion_alternativa is null)
	 and D.VID_RECIBO = ENC.VID_RECIBO_COMPLETO
	 AND P.VID_NUM_PARTE IS NOT NULL
 ORDER BY P.VID_NUM_PARTE";
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
	/*====================== /*SQL DINAMICO ======================*/
	function condicionSql(){
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT IID_NUMERO, V_DESCRIPCION FROM  OP_IN_CONDICION_MERC";
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

	function loteSerieSql(){
		$conn = conexion::conectar();
		$res_array = array();
		$sql = "SELECT DISTINCT V_LOTE_SERIE
					  FROM vista_wms_inventario
						WHERE iid_num_cliente = 2905
						AND v_lote_serie is not null";
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
	/*====================== VALIDA SI ES FECHA  ======================*/

	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}

}
