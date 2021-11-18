<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class Temperatura_Granos
{
	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function grafica($plaza,$fecha,$almacen,$silo,$fil_check)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$prueba_almacen = '';
		}
		else {
			$prueba_almacen = " AND T.IID_ALMACEN =".$almacen." ";
		}

		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$fecha_filtro_ini = substr($fecha, 0, 10);
				$fecha_filtro_fin = substr($fecha, 11, 10);
			}
		}
		else {
			$fecha_filtro_fin = date("d/m/Y");
			$fecha_inicial  = date("d-m-Y");
			$fecha_filtro_ini = date("d/m/Y", strtotime($fecha_inicial."-30 days"));
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}
		$and_plaza = " ";
		if ($plaza == "ALL") {
			$name_silo = " T.NOMBRE_SILO ||  ' DE PLAZA '|| P.V_RAZON_SOCIAL || ' ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";

		}else {
			$and_plaza = " AND P.IID_PLAZA = ".$in_plaza. " ";
			$name_silo = " T.NOMBRE_SILO ||  ' DE ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";
		}

		$sql = " SELECT $name_silo
				TO_CHAR(T.FECHA, 'DD/MM/YYYY')AS FECHA,
        T.TEMPERATURA,
        T.HUMEDAD,
				T.TIPO_GRANO,
       	T.FINANCIERA,
       	T.CD,
				T.NOTAS,
				CASE T.ESTATUS WHEN 1 THEN 'CD VIVO'
                       WHEN 0 THEN 'CD LIQUIDADO'
                       ELSE 'N/A' END AS ESTATUS
				FROM OP_IN_GR_SILOS_TEMP T
				INNER JOIN PLAZA P  ON T.IID_PLAZA = P.IID_PLAZA
  			INNER JOIN ALMACEN A ON T.IID_ALMACEN = A.IID_ALMACEN
				WHERE T.FECHA = (SELECT MAX(S.FECHA) FROM
				                         OP_IN_GR_SILOS_TEMP S
				                  WHERE S.NOMBRE_SILO = T.NOMBRE_SILO
				                        AND (S.FECHA >= TO_DATE('".$fecha_filtro_ini."', 'dd/mm/yyyy')
				                             AND S.FECHA <= TO_DATE('".$fecha_filtro_fin."', 'dd/mm/yyyy'))
																		 AND S.IID_PLAZA = T.IID_PLAZA
				                  )
													$and_plaza
													$prueba_almacen
													ORDER BY T.NOMBRE_SILO ";

												#echo $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		#echo $sql;
		oci_free_statement($stid);
		oci_close($conn);

		//	echo $sql;
		return $res_array;

	}

	public function grafica2($plaza,$fecha,$almacen,$silo,$fil_check)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$prueba_almacen = '';
		}
		else {
			$prueba_almacen = " AND T.IID_ALMACEN =".$almacen." ";
		}

		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$fecha_filtro_ini = substr($fecha, 0, 10);
				$fecha_filtro_fin = substr($fecha, 11, 10);
			}
		}
		else {
			$fecha_filtro_fin = date("d/m/Y");
			$fecha_inicial  = date("d-m-Y");
			$fecha_filtro_ini = date("d/m/Y", strtotime($fecha_inicial."-30 days"));
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}
		$and_plaza = " ";
		if ($plaza == "ALL") {
			$name_silo = " T.NOMBRE_SILO ||  ' DE PLAZA '|| P.V_RAZON_SOCIAL || ' ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";

		}else {
			$and_plaza = " AND P.IID_PLAZA = ".$in_plaza. " ";
			$name_silo = " T.NOMBRE_SILO ||  ' DE ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";
		}
		$wheresilus = "";
		$wheresilus2 = "";
		if ($silo <> "ALL") {
			$wheresilus = " AND T.NOMBRE_SILO = '".$silo."'";
			$wheresilus2 = " AND S.NOMBRE_SILO = '".$silo."'";
		}

		$sql = " SELECT $name_silo
				T.FECHA,
				T.TIPO_GRANO,
       	T.FINANCIERA,
				T.CD,
				T.IMPUREZAS,
	       T.QUEBRADOS,
	       T.DAÑADOS_X_CALOR AS CALOR,
	       T.GRANO_VERDE,
	       T.GRANOS_PODRIDOS AS PODRIDOS,
	       T.DATOS_X_INSECTOS AS INSECTOS,
	       T.OTROS_DAÑOS AS OTROS,
				 CASE T.ESTATUS WHEN 1 THEN 'CD VIVO'
												 WHEN 0 THEN 'CD LIQUIDADO'
												 ELSE 'N/A' END AS ESTATUS
				FROM OP_IN_GR_SILOS_CALIDAD T
				INNER JOIN PLAZA P  ON T.IID_PLAZA = P.IID_PLAZA
  			INNER JOIN ALMACEN A ON T.IID_ALMACEN = A.IID_ALMACEN
				WHERE T.FECHA = (SELECT MAX(S.FECHA) FROM
				                         OP_IN_GR_SILOS_CALIDAD S
				                  WHERE S.NOMBRE_SILO = T.NOMBRE_SILO
				                        AND (S.FECHA >= TO_DATE('".$fecha_filtro_ini."', 'dd/mm/yyyy')
				                             AND S.FECHA <= TO_DATE('".$fecha_filtro_fin."', 'dd/mm/yyyy'))
																		 AND S.IID_PLAZA = T.IID_PLAZA
																		 $wheresilus2
				                  )
													$and_plaza
													$prueba_almacen
													$wheresilus
													ORDER BY T.NOMBRE_SILO ";

													#echo $sql;

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);


		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}
		oci_free_statement($stid);
		oci_close($conn);

		//	echo $sql;
		return $res_array;

	}
	/****************************GRAFICA SEMANA*******************************/
	// ************************** VENTA REAL **************************  //
	function envioMails($silonombre, $cd_n, $fecha)
	{
		$conn = conexion::conectar();

		$res_array = array();

		$curs = oci_new_cursor($conn);
		$promotor = "ALL";
		//echo $pre.' '.$fecha.' '.$promotor.' '.$plaza;
		$stid = oci_parse($conn, "begin PCK_DASHBOARD.ENVIAR_MAIL_GRANOS(:silonombre, :cd_n , :fecha); end;");
		oci_bind_by_name($stid, ':silonombre', $silonombre); //3
		oci_bind_by_name($stid, ':cd_n', $cd_n); //2019
		oci_bind_by_name($stid, ':fecha', $fecha); // ALL
		oci_execute($stid);
		oci_execute($curs);  // Ejecutar el REF CURSOR como un ide de sentencia normal
		while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
				$res_array[]=$row;
		}

		oci_free_statement($stid);
		oci_free_statement($curs);
		oci_close($conn);
		return $res_array;

	}
	// ************************** /.VENTA REAL **************************  //
	public function graficaDetallesGranos($plaza,$fecha,$almacen,$silo,$fil_check)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");
		$nombre_fecha = "";
		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$prueba_almacen = '';
		}
		else {
			$prueba_almacen = " AND T.IID_ALMACEN =".$almacen." ";
		}

		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$fecha_filtro_ini = substr($fecha, 0, 10);
				$fecha_filtro_fin = substr($fecha, 11, 10);
			}
		}
		else {
			$fecha_filtro_fin = date("d/m/Y");
			$fecha_inicial  = date("d-m-Y");
			$fecha_filtro_ini = date("d/m/Y", strtotime($fecha_inicial."-30 days"));
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}
		$and_plaza = " ";
		if ($plaza == "ALL") {
			$name_silo = " T.NOMBRE_SILO ||  ' DE PLAZA '|| P.V_RAZON_SOCIAL || ' ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";

		}else {
			$and_plaza = " AND P.IID_PLAZA = ".$in_plaza. " ";
			$name_silo = " T.NOMBRE_SILO ||  ' DE ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";
		}
		if ($silo == 'ALL') {
			$andsilos = " ";
			$and_fecha_silo = " T.FECHA = (SELECT MAX(S.FECHA) FROM
															 OP_IN_TEMPERATURA_SILOS S
												WHERE S.NOMBRE_SILO = T.NOMBRE_SILO
															AND (S.FECHA >= TO_DATE('".$fecha_filtro_ini."', 'dd/mm/yyyy')
																	 AND S.FECHA <= TO_DATE('".$fecha_filtro_fin."', 'dd/mm/yyyy'))
																	 AND S.IID_PLAZA = T.IID_PLAZA
																	 AND S.PESO_ESPECIFICO > 0
												)";
		}
		else {
			$nombre_fecha = " T.FECHA AS FECHA, " ;
			$name_silo =  " T.NOMBRE_SILO ||  ' DE PLAZA '|| P.V_RAZON_SOCIAL || ' ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";
			$andsilos = " AND T.NOMBRE_SILO ='".$silo."' ";
			$and_fecha_silo = " T.FECHA >= TO_DATE('".$fecha_filtro_ini."', 'dd/mm/yyyy')
       										AND T.FECHA <= TO_DATE('".$fecha_filtro_fin."', 'dd/mm/yyyy')";
		}

		$sql = " SELECT $name_silo
				$nombre_fecha
        TEMPERATURA_MAXIMA,
        TEMPERATURA_MINIMA,
        TEMPERATURA,
        HUMEDAD,
				TIPO_GRANO,
				FINANCIERA,
				CD,
				IMPUREZAS ,
				QUEBRADOS ,
				CALOR ,
				GRANO_VERDE ,
				PODRIDOS ,
				INSECTOS,
				OTROS,
				PESO_ESPECIFICO,
				SANIDAD,
				FECHA
				FROM OP_IN_TEMPERATURA_SILOS T
				INNER JOIN PLAZA P  ON T.IID_PLAZA = P.IID_PLAZA
  			INNER JOIN ALMACEN A ON T.IID_ALMACEN = A.IID_ALMACEN
				WHERE $and_fecha_silo
													$and_plaza
													$prueba_almacen
													$andsilos
													AND T.PESO_ESPECIFICO > 0
													ORDER BY T.FECHA ";


		#echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		#echo $sql;
		oci_free_statement($stid);
		oci_close($conn);

		//	echo $sql;
		return $res_array;

	}




	public function graficaSilo($plaza,$fecha,$almacen,$silo,$fil_check)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = "  AND t.IID_ALMACEN = ".$almacen." ";
		}

		if ($fil_check == 'on'){
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$fecha_filtro_ini = substr($fecha, 0, 10);
				$fecha_filtro_fin = substr($fecha, 11, 10);
			}
		}
		else {
			$fecha_filtro_fin = date("d/m/Y");
			$fecha_inicial  = date("d-m-Y");
			$fecha_filtro_ini = date("d/m/Y", strtotime($fecha_inicial."-30 days"));
		}

		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}
		$and_plaza = " ";
		if ($plaza == "ALL") {
			$name_silo = " T.NOMBRE_SILO ||  ' DE PLAZA '|| P.V_RAZON_SOCIAL || ' ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";

		}else {
			$and_plaza = " AND P.IID_PLAZA = ".$in_plaza. " ";
			$name_silo = " T.NOMBRE_SILO ||  ' DE ALMACEN ' || A.V_NOMBRE AS NOMBRE_SILO ,";
		}

		if ($almacen == 'ALL') {
			$prueba_almacen = '';
		}
		else {
			$prueba_almacen = " AND T.IID_ALMACEN =".$almacen." ";
		}

		$andsilo = " ";
		if ($silo == "ALL") {
			$andsilo = " ";
		}else {

			$andsilo = " AND T.NOMBRE_SILO = '".$silo."'";
		}

			//CONTROL CALIDAD DE GRANOS
		$sql = "SELECT $name_silo
						A.V_NOMBRE AS NOMBRE_SILO,
						TEMPERATURA,
						HUMEDAD,
						TIPO_GRANO,
						FINANCIERA,
						CD,
						NOTAS,
						TO_DATE(FECHA, 'dd/mm/yyyy') AS FECHA,
						CASE T.ESTATUS WHEN 1 THEN 'CD VIV0'
                      WHEN 0 THEN 'CD LIQUIDADO'
                      ELSE 'N/A' END AS ESTATUS
			 FROM OP_IN_GR_SILOS_TEMP T
			 INNER JOIN PLAZA P  ON T.IID_PLAZA = P.IID_PLAZA
			 INNER JOIN ALMACEN A ON T.IID_ALMACEN = A.IID_ALMACEN
			 WHERE T.FECHA >= TO_DATE('".$fecha_filtro_ini."', 'dd/mm/yyyy')
			       AND T.FECHA <= TO_DATE('".$fecha_filtro_fin."', 'dd/mm/yyyy')
			       $and_plaza
						 $prueba_almacen
						 $andsilo
			 ORDER BY FECHA ";

			 #echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		#echo $sql;
		oci_free_statement($stid);
		oci_close($conn);

		//	echo $sql;
		return $res_array;

	}
	/*++++++++++++++++++++++++ GRAFICA PERSONAL ACTIVO ++++++++++++++++++++++++*/
	public function graficaAlmacen($plaza,$fecha,$fil_check)
	{
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");

		#	echo $mes." ".$mes2;

		if ($fil_check == 'on'){
				#echo substr($fecha,0,10)."    ".substr($fecha,11,10);
			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
				//echo $no_semana. " ". $no_semana_inf;
			}
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza){
				case 'CORPORATIVO': $in_plaza = 2; break;
				case 'CÓRDOBA': $in_plaza = 3; break;
				case 'MÉXICO': $in_plaza = 4; break;
				case 'GOLFO': $in_plaza = 5; break;
				case 'PENINSULA': $in_plaza = 6; break;
				case 'PUEBLA': $in_plaza = 7; break;
				case 'BAJIO': $in_plaza = 8; break;
				case 'OCCIDENTE': $in_plaza = 17; break;
				case 'NORESTE': $in_plaza = 18; break;
				default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		$conn = conexion::conectar();
		$res_array = array();
		$sql ="SELECT P.IID_ALMACEN,
       P.V_NOMBRE AS ALMACEN,
       NVL(SUM((T.PORCENTAJE) /
               (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                  FROM OP_IN_PORCENTAJE_OCUPACION S
                 WHERE S.IID_PLAZA = T.IID_PLAZA
                   AND S.ANIO = T.ANIO
                   AND S.SEMANA = T.SEMANA
								 	 AND S.IID_ALMACEN = P.IID_ALMACEN)),
           0) + NVL(SUM((T.PORCENTAJE_RACKS) /
                        (SELECT COUNT(DISTINCT S.IID_ALMACEN)
                           FROM OP_IN_PORCENTAJE_OCUPACION S
                          WHERE S.IID_PLAZA = T.IID_PLAZA
                            AND S.ANIO = T.ANIO
                            AND S.SEMANA = T.SEMANA
													AND S.IID_ALMACEN = P.IID_ALMACEN)),
                    0) AS RACK_PORCENTAJE
					  FROM OP_IN_PORCENTAJE_OCUPACION t
					 INNER JOIN ALMACEN P ON T.IID_ALMACEN = P.IID_ALMACEN
					 WHERE T.ANIO = $anio
					   AND T.SEMANA = (SELECT MAX(PRU2.SEMANA)
					                     FROM OP_IN_PORCENTAJE_OCUPACION PRU2
					                    WHERE PRU2.ANIO = $anio
					                      AND t.MES = PRU2.MES
					                      AND PRU2.MES = $mes2
					                      AND PRU2.IID_PLAZA = t.IID_PLAZA)
						  AND T.IID_PLAZA in ($in_plaza)
					 GROUP BY P.IID_ALMACEN, P.V_NOMBRE";
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

	public function graficaCliente($plaza,$fecha,$fil_check,$almacen)
	{
		$conn = conexion::conectar();
		$res_array = array();

		//CODIGO FECHAS SELECCIONAR # SEMANA :V SE MAMO
		$no_semana = date("W");
		$no_semana_inf = date("W")-4;

		$mes = date("m");
		$mes2 = date("m")-1;

		$anio = date("Y");
		$anio2 = date("Y");

		//echo $no_semana." ".$no_semana_inf;
		if ($almacen == 'ALL') {
			$and_alm = "";
			$and_alm2 = "";
		}
		else {
			$and_alm = "AND PRU.IID_ALMACEN = ".$almacen;
			$and_alm2 = "AND t.IID_ALMACEN = ".$almacen;
		}

		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
			}
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza) {
				case 'CORPORATIVO': $in_plaza = 2; break;
				case 'CÓRDOBA': $in_plaza = 3; break;
				case 'MÉXICO': $in_plaza = 4; break;
				case 'GOLFO': $in_plaza = 5; break;
				case 'PENINSULA': $in_plaza = 6; break;
				case 'PUEBLA': $in_plaza = 7; break;
				case 'BAJIO': $in_plaza = 8; break;
				case 'OCCIDENTE': $in_plaza = 17; break;
				case 'NORESTE': $in_plaza = 18; break;
				default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		$sql = "SELECT P.IID_NUM_CLIENTE,
       P.V_RAZON_SOCIAL AS CLIENTE,
       NVL(SUM((T.PORCENTAJE) /
               (SELECT COUNT(DISTINCT S.IID_CLIENTE)
                  FROM OP_IN_PORCENTAJE_OCUPACION S
                 WHERE S.IID_PLAZA = T.IID_PLAZA
                   AND S.ANIO = T.ANIO
                   AND S.SEMANA = T.SEMANA
								 	 AND S.IID_CLIENTE = P.IID_NUM_CLIENTE)),
           0) + NVL(SUM((T.PORCENTAJE_RACKS) /
                        (SELECT COUNT(DISTINCT S.IID_CLIENTE)
                           FROM OP_IN_PORCENTAJE_OCUPACION S
                          WHERE S.IID_PLAZA = T.IID_PLAZA
                            AND S.ANIO = T.ANIO
                            AND S.SEMANA = T.SEMANA
													  AND S.IID_CLIENTE = P.IID_NUM_CLIENTE)),
                    0) AS RACK_PORCENTAJE
					  FROM OP_IN_PORCENTAJE_OCUPACION t
					 INNER JOIN CLIENTE P ON T.IID_CLIENTE = P.IID_NUM_CLIENTE
					 WHERE T.ANIO = $anio
					   AND T.SEMANA = (SELECT MAX(PRU2.SEMANA)
					                     FROM OP_IN_PORCENTAJE_OCUPACION PRU2
					                    WHERE PRU2.ANIO = $anio
					                      AND t.MES = PRU2.MES
					                      AND PRU2.MES = $mes2
					                      AND PRU2.IID_PLAZA = t.IID_PLAZA)
					   $and_alm2
					 GROUP BY P.IID_NUM_CLIENTE, P.V_RAZON_SOCIAL";

					# echo $sql;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		#echo $sql;
		return $res_array;

	}


	public function graficaMensual($plaza,$fecha,$almacen,$fil_check){

		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");



		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
				//echo $no_semana. " ". $no_semana_inf;
			}
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza){
		  	case 'CORPORATIVO': $in_plaza = 2; break;
		    case 'CÓRDOBA': $in_plaza = 3; break;
		    case 'MÉXICO': $in_plaza = 4; break;
		    case 'GOLFO': $in_plaza = 5; break;
		    case 'PENINSULA': $in_plaza = 6; break;
		    case 'PUEBLA': $in_plaza = 7; break;
		    case 'BAJIO': $in_plaza = 8; break;
		    case 'OCCIDENTE': $in_plaza = 17; break;
		    case 'NORESTE': $in_plaza = 18; break;
		    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = " AND CAP.IID_ALMACEN = ".$almacen." ";
		}

		$conn = conexion::conectar();
		$res_array = array();
		#echo $fecha;

		$sql = "SELECT T.IID_PLAZA,
       P.V_RAZON_SOCIAL AS PLAZA,
       NVL(SUM((T.PORCENTAJE)/(SELECT COUNT(DISTINCT S.IID_ALMACEN ) FROM OP_IN_PORCENTAJE_OCUPACION S
               WHERE S.IID_PLAZA =T.IID_PLAZA
                      AND S.ANIO = T.ANIO
                      AND S.SEMANA = T.SEMANA)), 0)+
       NVL(SUM((T.PORCENTAJE_RACKS)/(SELECT COUNT(DISTINCT S.IID_ALMACEN ) FROM OP_IN_PORCENTAJE_OCUPACION S
               WHERE S.IID_PLAZA =T.IID_PLAZA
                      AND S.ANIO = T.ANIO
                      AND S.SEMANA = T.SEMANA)), 0 ) AS RACK_PORCENTAJE
				FROM OP_IN_PORCENTAJE_OCUPACION t
				     INNER JOIN PLAZA P ON T.IID_PLAZA = P.IID_PLAZA
				WHERE T.ANIO = ".$anio."
				      AND T.SEMANA = (SELECT MAX(PRU2.SEMANA)
																				 FROM OP_IN_PORCENTAJE_OCUPACION PRU2
																				 WHERE PRU2.ANIO = ".$anio."
																				 AND t.MES = PRU2.MES
																				 AND PRU2.MES = ".$mes2."
																				 AND PRU2.IID_PLAZA = t.IID_PLAZA)
				GROUP BY T.IID_PLAZA, P.V_RAZON_SOCIAL";

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


	public function datos($plaza,$fecha,$almacen,$fil_check){

		$no_semana = date("W");
		$no_semana_inf = date("W")-4;


		$mes = date("m")-1;
		$mes2 = date("m");

		$anio = date("Y");
		$anio2 = date("Y");



		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
				//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
				$dia = substr($fecha, 0,2);
				$mes = substr($fecha, 3,2);
				$anio = substr($fecha,6,4);
				$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

				$dia2 = substr($fecha, 11,2);
				$mes2 = substr($fecha, 14,2);
				$anio2 = substr($fecha,17,4);
				$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
				//echo $no_semana. " ". $no_semana_inf;
			}
		}
		$in_plaza = "2,3,4,5,6,7,8,17,18";
		switch ($plaza){
				case 'CORPORATIVO': $in_plaza = 2; break;
				case 'CÓRDOBA': $in_plaza = 3; break;
				case 'MÉXICO': $in_plaza = 4; break;
				case 'GOLFO': $in_plaza = 5; break;
				case 'PENINSULA': $in_plaza = 6; break;
				case 'PUEBLA': $in_plaza = 7; break;
				case 'BAJIO': $in_plaza = 8; break;
				case 'OCCIDENTE': $in_plaza = 17; break;
				case 'NORESTE': $in_plaza = 18; break;
				default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
		}

		if ($almacen == 'ALL') {
			$prueba_almacen = '';
			$prueba_capacidad = '';
		}
		else {
			$prueba_almacen = " AND PRU.IID_ALMACEN =".$almacen." ";
			$prueba_capacidad = " AND CAP.IID_ALMACEN = ".$almacen." ";
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql ="SELECT PLA.IID_PLAZA ,
		       REPLACE(PLA.V_RAZON_SOCIAL, ' (ARGO)') AS PLAZA,
		       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS)
		                    FROM PRUEBA_SUBIDA PRU
		                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
		                                               FROM PRUEBA_SUBIDA PRU2
		                                               WHERE PRU2.ANIO = ".$anio."
		                                               AND PRU.MES = PRU2.MES
		                                               AND PRU2.MES = ".$mes2."
		                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
		                    AND PRU.IID_PLAZA IN(".$in_plaza.")
		                    ".$prueba_almacen."
		                    AND PRU.IID_PLAZA = PLA.IID_PLAZA
		                    AND PRU.OCUPACION = 1
		                    AND PRU.ANIO = ".$anio."
		                    AND PRU.MES = ".$mes2."), 0) AS MTS_UTILIZADOS,
		       NVL(( SELECT SUM(PRU.MTS_UTILIZADOS_PASILLOS)
		                    FROM PRUEBA_SUBIDA PRU
		                    WHERE PRU.SEMANA = (SELECT MAX(PRU2.SEMANA)
		                                               FROM PRUEBA_SUBIDA PRU2
		                                               WHERE PRU2.ANIO = ".$anio."
		                                               AND PRU.MES = PRU2.MES
		                                               AND PRU2.MES = ".$mes2."
		                                               AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
		                    AND PRU.IID_PLAZA IN(".$in_plaza.")
		                    ".$prueba_almacen."
		                    AND PRU.IID_PLAZA = PLA.IID_PLAZA
		                    AND PRU.OCUPACION = 0
		                    AND PRU.ANIO = ".$anio."
		                    AND PRU.MES = ".$mes2."), 0) AS MTS_PASILLOS_ESPACIO,
		       NVL((SELECT SUM (CAP.MTS_RACKS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes2."), 0) AS MTS_RACKS,
		       NVL((SELECT SUM (CAP.CAPACIDAD_TOTAL)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes2."), 0) AS CAPACIDAD_TOTAL,
		       NVL((SELECT SUM (CAP.USO_VARIADOS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes2."), 0) AS USO_VARIADO,
		        NVL((SELECT SUM (CAP.AREA_RACKS)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes2."), 0) AS AREA_RACKS,
		        NVL((SELECT SUM (CAP.TAMANIO_BODEGA)
		                   FROM ALMACEN_CAPACIDAD CAP
		                   WHERE CAP.SEMANA = (SELECT MAX(CAP2.SEMANA)
		                                              FROM ALMACEN_CAPACIDAD CAP2
		                                              WHERE CAP2.ANIO = ".$anio."
		                                              AND CAP.MES = CAP2.MES
		                                              AND CAP2.MES = ".$mes2."
		                                              AND CAP2.IID_PLAZA = CAP.IID_PLAZA)
		                    AND CAP.IID_PLAZA IN (".$in_plaza.")
		                    ".$prueba_capacidad."
		                    AND CAP.IID_PLAZA = PLA.IID_PLAZA
		                    AND CAP.ANIO = ".$anio."
		                    AND CAP.MES = ".$mes2."), 0) AS TAMANIO_BODEGA
		        FROM PLAZA PLA
		        WHERE PLA.IID_PLAZA IN (2,3,4,5,6,7,8,17,18)
		        GROUP BY PLA.IID_PLAZA, PLA.V_RAZON_SOCIAL ORDER BY PLA.IID_PLAZA";

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
				$sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";
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


		/**************************************GRAFICA DE PIE ********************************************************/

	function capacidad_almacen($plaza,$fecha,$fil_check){
			$no_semana = date("W");
			$no_semana_inf = date("W")-4;


			$mes = date("m")-1;
			$mes2 = date("m");

			$anio = date("Y");
			$anio2 = date("Y");

		#	echo $mes." ".$mes2;


			if ($fil_check == 'on'){

				if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
					//AND per.d_fecha_ingreso >= trunc(to_date('".substr($fecha,0,10)."','dd/mm/yyyy') )
					$dia = substr($fecha, 0,2);
					$mes = substr($fecha, 3,2);
					$anio = substr($fecha,6,4);
					$no_semana_inf = date("W", mktime(0,0,0,$mes,$dia,$anio));

					$dia2 = substr($fecha, 11,2);
					$mes2 = substr($fecha, 14,2);
					$anio2 = substr($fecha,17,4);
					$no_semana = date("W", mktime(0,0,0,$mes2,$dia2,$anio2));
					//echo $no_semana. " ". $no_semana_inf;
				}
			}
			$in_plaza = "2,3,4,5,6,7,8,17,18";
			switch ($plaza){
			  	case 'CORPORATIVO': $in_plaza = 2; break;
			    case 'CÓRDOBA': $in_plaza = 3; break;
			    case 'MÉXICO': $in_plaza = 4; break;
			    case 'GOLFO': $in_plaza = 5; break;
			    case 'PENINSULA': $in_plaza = 6; break;
			    case 'PUEBLA': $in_plaza = 7; break;
			    case 'BAJIO': $in_plaza = 8; break;
			    case 'OCCIDENTE': $in_plaza = 17; break;
			    case 'NORESTE': $in_plaza = 18; break;
			    default: $in_plaza = "2,3,4,5,6,7,8,17,18"; break;
			}

			$conn = conexion::conectar();
			$res_array = array();
			$sql ="SELECT SUM(PRU.MTS_RACKS) AS MTS_RACKS,
										SUM(PRU.CAPACIDAD_TOTAL) AS CAPACIDAD_TOTAL,
										SUM(PRU.USO_VARIADOS) AS USO_VARIADOS,
										SUM(PRU.AREA_RACKS) AS AREA_RACKS,
										SUM(PRU.TAMANIO_BODEGA) AS TAMANIO_BODEGA
							FROM ALMACEN_CAPACIDAD PRU
							WHERE PRU.SEMANA = (SELECT MAX(SEMANA) FROM ALMACEN_CAPACIDAD PRU2 WHERE PRU2.MES =".$mes2." AND PRU2.ANIO = ".$anio2." AND PRU2.IID_PLAZA = PRU.IID_PLAZA)
							AND PRU.IID_PLAZA IN(".$in_plaza.")";

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
            INNER JOIN OP_IN_TEMPERATURA_SILOS ALC ON AL.IID_PLAZA = ALC.IID_PLAZA AND AL.IID_ALMACEN = ALC.IID_ALMACEN
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
