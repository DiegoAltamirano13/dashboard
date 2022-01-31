<?php
/**
* © Argo Almacenadora ®
* Fecha: 30/11/2016
* Modificado total: 17/01/2019
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Sistemas (TIC)
* Version --
*/
include_once '../libs/conOra.php';

class Tic
{

	/*--------------- WIDGETS DASHBOARD ---------------*/
	public function widgets($user,$bimestre_sel, $fecha)
	{
		#echo $user;
		$perfil = $this->sql(" SELECT P.IID_PERFIL perfil FROM SS_PERMISOS_MODULOS P WHERE P.IID_MODULO = 1 AND P.IID_EMPLEADO = $user ");
		$andUser = "";
		if ( $perfil[0]["PERFIL"] != 1 ){
			$andUser = " AND das.IID_EMPLEADO = $user ";
		}

		if ($bimestre_sel == 1) {
			$fecha_ini = '01/01/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
			//echo $fecha_fin;
		}elseif ($bimestre_sel == 2 ) {
			$fecha_ini = '01/03/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('04/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 3) {
			$fecha_ini = '01/05/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('06/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 4) {
			$fecha_ini = '01/07/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('08/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 5) {
			$fecha_ini = '01/09/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('10/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 6) {
			$fecha_ini = '01/11/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('12/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}


		$andFecha = " AND pro.d_fecha_inicio >= trunc(to_date('".$fecha_ini."','dd-mm-yyyy') ) AND pro.d_fecha_inicio < trunc(to_date('".$fecha_fin."','dd-mm-yyyy') ) +1  ";

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT * FROM
				(SELECT COUNT(distinct pro.iid_proyecto) AS activos
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE  pro.iid_status <> 3),
				(SELECT COUNT(distinct pro.iid_proyecto) AS proceso
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE  pro.iid_status IN (2,4) ),
				(SELECT COUNT(distinct pro.iid_proyecto) AS iniciar
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE  pro.iid_status = 1),
				(SELECT  COUNT(distinct pro.iid_proyecto) AS desfasados
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE (pro.d_fecha_fin < NVL(pro.d_fecha_fin_real,SYSDATE -1) OR pro.iid_status = 4) AND pro.iid_status <> 1 AND PRO.IID_STATUS <5 ),
				(SELECT  COUNT(distinct pro.iid_proyecto) AS terminados
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE pro.iid_status = 3),
				(SELECT  COUNT(distinct pro.iid_proyecto) AS todos
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE pro.iid_status IN (1,2,3,4) ),
				(SELECT  COUNT(distinct pro.iid_proyecto) AS proceso
				FROM ss_proyecto pro
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser $andFecha
				WHERE pro.d_fecha_fin_real is null)";

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
	/*--------------- /. WIDGETS DASHBOARD ---------------*/


	/*--------------- INFORMACION DE PROYECTOS ---------------*/
	public function proyecto($user,$bimestre_sel,$fecha,$status,$pro)
	{
		$perfil = $this->sql(" SELECT P.IID_PERFIL perfil FROM SS_PERMISOS_MODULOS P WHERE P.IID_MODULO = 1 AND P.IID_EMPLEADO = $user ");
		$andUser = "";
		if ( $perfil[0]["PERFIL"] != 1 ){
			$andUser = " AND das.IID_EMPLEADO = $user ";
		}

		if ($bimestre_sel == 1) {
			$fecha_ini = '01/01/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
			//echo $fecha_fin;
		}elseif ($bimestre_sel == 2 ) {
			$fecha_ini = '01/03/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('04/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 3) {
			$fecha_ini = '01/05/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('06/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 4) {
			$fecha_ini = '01/07/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('08/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 5) {
			$fecha_ini = '01/09/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('10/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 6) {
			$fecha_ini = '01/11/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('12/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}

		$andFecha = "";
		if( $pro == "ALL" ){
			$andFecha = " AND pro.d_fecha_inicio >= trunc(to_date('".$fecha_ini."','dd-mm-yyyy') ) AND pro.d_fecha_inicio <= trunc(to_date('".$fecha_fin."','dd-mm-yyyy') )   ";
		}

		$andStatus = "";
		if( $status != "ALL" ){
			switch ($status) {
				case '1': $andStatus = " AND pro.iid_status <> 3 "; break;
				case '2': $andStatus = " AND pro.iid_status = 1 "; break;
				case '4': $andStatus = " AND ( pro.d_fecha_fin < NVL(pro.d_fecha_fin_real,SYSDATE -1) OR pro.iid_status = 4 ) AND pro.iid_status <> 1 "; break;
				case '3': $andStatus = " AND pro.iid_status = 3 "; break;
				case '5': $andStatus = " AND pro.d_fecha_fin_real <= pro.d_fecha_fin AND pro.iid_status = 3 "; break;

			}
		}else{
			$andStatus = " AND pro.iid_status IN (1,2,3,4) ";
		}

		$andPro = "";
		if( $pro != "ALL" ){
			$andPro = " AND pro.iid_proyecto = $pro ";
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT pro.iid_proyecto, pro.v_nombre, pro.iid_empleado_solicita, TO_CHAR(pro.d_fecha_solicitud, 'DD-MM-YYYY') d_fecha_solicitud, pro.iid_empleado_lider,
				TO_CHAR(pro.d_fecha_inicio, 'DD-MM-YYYY') d_fecha_inicio, TO_CHAR(pro.d_fecha_fin, 'DD-MM-YYYY') d_fecha_fin, TO_CHAR(pro.d_fecha_fin_real, 'DD-MM-YYYY') d_fecha_fin_real,
				pro.n_porcentaje, pro.iid_status, TO_CHAR(pro.d_fecha_ini_real,'DD-MM-YYYY') d_fecha_ini_real,pro.v_alcance, pro.v_observaciones, pro.v_descripcion, pro.v_justificacion,
				sol.v_nombre||' '||sol.v_ape_pat||' '||sol.v_ape_mat solicita, lid.v_nombre||' '||lid.v_ape_pat||' '||lid.v_ape_mat lider
				FROM ss_proyecto pro
				INNER JOIN no_personal sol ON sol.iid_empleado = pro.iid_empleado_solicita
				INNER JOIN no_personal lid ON lid.iid_empleado = pro.iid_empleado_lider
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser
				WHERE das.IID_PROYECTO = pro.iid_proyecto $andPro $andFecha $andStatus
				ORDER BY pro.iid_proyecto";

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
	/*--------------- /.INFORMACION DE PROYECTOS ---------------*/



	/*--------------- TAREAS DE PROYECTOS ---------------*/
	public function tareas($user,$pro)
	{
		$perfil = $this->sql(" SELECT P.IID_PERFIL perfil FROM SS_PERMISOS_MODULOS P WHERE P.IID_MODULO = 1 AND P.IID_EMPLEADO = $user ");
		$andUser = "";
		if ( $perfil[0]["PERFIL"] != 1 ){
			$andUser = " AND das.IID_EMPLEADO = $user ";
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT DISTINCT tar.iid_proyecto, tar.iid_tarea, tar.v_nombre, TO_CHAR(tar.d_fecha_inicio, 'DD-MM-YYYY') d_fecha_inicio,
				TO_CHAR(tar.d_fecha_fin,'DD-MM-YYYY') d_fecha_fin, TO_CHAR(tar.d_fecha_fin_real,'DD-MM-YYYY') d_fecha_fin_real,
				tar.n_porcentaje, tar.iid_status, sta.v_tipo status, TO_CHAR(tar.d_fecha_ini_real, 'DD-MM-YYYY') d_fecha_ini_real
				FROM ss_tareas tar
				INNER JOIN ss_proyecto pro ON pro.iid_proyecto = tar.iid_proyecto
				LEFT JOIN ss_status sta ON sta.iid_status = tar.iid_status
				INNER JOIN vista_dashboard_sistemas_empleado das ON das.IID_PROYECTO = pro.iid_proyecto $andUser
				WHERE pro.iid_proyecto = $pro ORDER BY tar.iid_tarea";

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
	/*--------------- /.TAREAS DE PROYECTOS ---------------*/



	/*--------------- ACTIVIDADES DE LAS TAREAS ---------------*/
	public function actividades($user,$pro,$tarea)
	{
		$perfil = $this->sql(" SELECT P.IID_PERFIL perfil FROM SS_PERMISOS_MODULOS P WHERE P.IID_MODULO = 1 AND P.IID_EMPLEADO = $user ");
		$andUser = "";
		if ( $perfil[0]["PERFIL"] != 1 ){
			$andUser = " AND das.IID_EMPLEADO = $user ";
		}

		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT act.iid_proyecto, act.iid_tarea, act.iid_actividad, act.v_nombre actividad
				,n_res.v_nombre||' '||n_res.v_ape_pat||' '||n_res.v_ape_mat responsable
				,TO_CHAR(act.d_fecha_inicio, 'DD-MM-YYYY') d_fecha_inicio, TO_CHAR(act.d_fecha_fin, 'DD-MM-YYYY') d_fecha_fin
				,TO_CHAR(act.d_fecha_fin_real, 'DD-MM-YYYY') d_fecha_fin_real, act.n_porcentaje, act.iid_status, act.v_observaciones
				,TO_CHAR(act.d_fecha_ini_real, 'DD-MM-YYYY') d_fecha_ini_real, act.n_porcentaje_valor, act.b_minuta
				,tar.v_nombre,
				act.V_Observaciones
				FROM ss_actividades act
				LEFT JOIN ss_tareas tar ON tar.iid_proyecto = act.iid_proyecto AND tar.iid_tarea = act.iid_tarea
				LEFT JOIN ss_responsables res ON res.iid_proyecto = act.iid_proyecto AND res.iid_empleado_resp = act.iid_empleado_resp
				LEFT JOIN no_personal n_res ON n_res.iid_empleado = res.iid_empleado
				WHERE act.iid_proyecto = $pro AND act.iid_tarea = $tarea ORDER BY act.iid_actividad";

		$stid = oci_parse($conn, $sql);
		oci_execute($stid);

		//while (($row = oci_fetch_assoc($stid)) != false)
		while (($row = oci_fetch_array($stid,OCI_BOTH+OCI_RETURN_LOBS)) != false )//OCI_RETURN_LOBS PARA TIPO DE DATOS BLOB
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;

	}
	/*--------------- /.ACTIVIDADES DE LAS TAREAS ---------------*/



	/*--------------- DESVIACIONES DE LAS ACTIVIDADES ---------------*/
	public function desviciones($pro,$tarea,$actividad)
	{
		$conn = conexion::conectar();
		$res_array = array();

		$sql = "SELECT des.iid_proyecto, des.iid_tarea, des.iid_actividad, des.iid_desviacion,
				des.v_nombre desviacion, des.v_razon, des.iid_empleado_resp, des.v_observaciones, act.v_nombre
				,per.v_nombre||' '||per.v_ape_pat||' '||per.v_ape_mat responsable
				FROM ss_desviaciones des
				INNER JOIN ss_proyecto pro ON pro.iid_proyecto = des.iid_proyecto
				INNER JOIN ss_actividades act ON act.iid_proyecto = des.iid_proyecto AND act.iid_tarea = des.iid_tarea AND act.iid_actividad = des.iid_actividad
				LEFT JOIN ss_responsables res ON res.iid_empleado_resp = des.iid_empleado_resp AND res.iid_proyecto = des.iid_proyecto
				LEFT JOIN no_personal per ON per.iid_empleado = res.iid_empleado
				WHERE des.iid_proyecto = $pro AND des.iid_tarea = $tarea AND des.iid_actividad = $actividad ORDER BY des.iid_desviacion ";

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
	/*--------------- /.DESVIACIONES DE LAS ACTIVIDADES ---------------*/



	/*--------------- COUNT PROYECTO DESFASADO/ENTIEMPO DEL BIMESTRE ---------------*/
	public function countBim($fecha, $bimestre_sel)
	{
		$conn = conexion::conectar();
		$res_array = array();

		/*if ($bimestre_sel == 1 ) {
			$bimestre_sel = 6;
			$fecha = $fecha -1;
		}
		else {
			$bimestre_sel = $bimestre_sel -1;
		}*/

		if ($bimestre_sel == 1) {
			$fecha_ini = '01/01/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('02/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
			//echo $fecha_fin;
		}elseif ($bimestre_sel == 2 ) {
			$fecha_ini = '01/03/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('04/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 3) {
			$fecha_ini = '01/05/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('06/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 4) {
			$fecha_ini = '01/07/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('08/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 5) {
			$fecha_ini = '01/09/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('10/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}elseif ($bimestre_sel == 6) {
			$fecha_ini = '01/11/'.$fecha;
			$cons_fecha_fin = $this->sql(" SELECT TO_CHAR(LAST_DAY(to_date('12/$fecha','MM/YYYY')), 'dd/mm/yyyy') AS F_FIN from dual");
			$fecha_fin = $cons_fecha_fin[0]["F_FIN"];
		}


		$sql = "SELECT (SELECT COUNT(*)
				FROM ss_proyecto pro
				WHERE pro.d_fecha_fin_real > pro.d_fecha_fin
				AND pro.d_fecha_inicio >= trunc(to_date('".$fecha_ini."','dd-mm-yyyy') ) AND pro.d_fecha_inicio < trunc(to_date('".$fecha_fin."','dd-mm-yyyy') ) +1 ) AS desfasado,
				(SELECT COUNT(*)
				FROM ss_proyecto pro
				WHERE pro.d_fecha_fin_real <= pro.d_fecha_fin
				AND pro.d_fecha_inicio >= trunc(to_date('".$fecha_ini."','dd-mm-yyyy') ) AND pro.d_fecha_inicio < trunc(to_date('".$fecha_fin."','dd-mm-yyyy') ) +1 ) AS entiempo
				FROM DUAL ";

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
	/*--------------- /.COUNT PROYECTO DESFASADO/ENTIEMPO DEL BIMESTRE ---------------*/


	/*---------------------- BIMESTRE ACTUAL ----------------------*/
 	public function bimestre($fecha=null){
      if ( is_null($fecha) ){ $fecha = $this->sql(" SELECT TO_CHAR(SYSDATE,'DD-MM-YYYY') fecha FROM DUAL "); $fecha = $fecha[0]["FECHA"]; }
      $mes = substr($fecha, 3,2) ;
      $bim= floor(($mes-1) / 2)+1;

      switch ($bim) {
      case '1':
      	$fechaComp = $this->sql(" SELECT TO_CHAR(LAST_DAY( TRUNC( TO_DATE('02','MM') ) ),'DD-MM-YYYY') fecha FROM DUAL ") ;
        if ( strtotime($fecha) >= strtotime($fechaComp[0]["FECHA"]) ) { $bim = 1; }else{ $bim = 6; }
        break;
      case '2':
      	$fechaComp = $this->sql(" SELECT TO_CHAR(LAST_DAY( TRUNC( TO_DATE('04','MM') ) ),'DD-MM-YYYY') fecha FROM DUAL ") ;
        if ( strtotime($fecha) >= strtotime($fechaComp[0]["FECHA"]) ) { $bim = 2; }else{ $bim = 1; }
        break;
      case '3':
      	$fechaComp = $this->sql(" SELECT TO_CHAR(LAST_DAY( TRUNC( TO_DATE('06','MM') ) ),'DD-MM-YYYY') fecha FROM DUAL ") ;
        if ( strtotime($fecha) >= strtotime($fechaComp[0]["FECHA"]) ) { $bim = 3; }else{ $bim = 2; }
        break;
      case '4':
      	$fechaComp = $this->sql(" SELECT TO_CHAR(LAST_DAY( TRUNC( TO_DATE('08','MM') ) ),'DD-MM-YYYY') fecha FROM DUAL ") ;
        if ( strtotime($fecha) >= strtotime($fechaComp[0]["FECHA"]) ) { $bim = 4; }else{ $bim = 3; }
        break;
      case '5':
      	$fechaComp = $this->sql(" SELECT TO_CHAR(LAST_DAY( TRUNC( TO_DATE('10','MM') ) ),'DD-MM-YYYY') fecha FROM DUAL ") ;
        if ( strtotime($fecha) >= strtotime($fechaComp[0]["FECHA"]) ) { $bim = 5; }else{ $bim = 4; }
        break;
      case '6':
      	$fechaComp = $this->sql(" SELECT TO_CHAR(LAST_DAY( TRUNC( TO_DATE('12','MM') ) ),'DD-MM-YYYY') fecha FROM DUAL ") ;
        if ( strtotime($fecha) >= strtotime($fechaComp[0]["FECHA"]) ) { $bim = 6; }else{ $bim = 5; }
        break;
      }
      return $bim;
    }
    /*---------------------- /.BIMESTRE ACTUAL ----------------------*/


    /*--------------- FECHA DE INICIO Y FIN DEL BIMESTRE ---------------*/
    public function bimestreFecha($bimestre){

    	$sqlNew = null;

	    switch ($bimestre) {
	      case '1':
	      	$f1 = "01"; $f2 = "02"; break;
	      case '2':
	      	$f1 = "03"; $f2 = "04"; break;
	      case '3':
	      	$f1 = "05"; $f2 = "06"; break;
	      case '4':
	      	$f1 = "07"; $f2 = "08"; break;
	      case '5':
	      	$f1 = "09"; $f2 = "10"; break;
	      case '6':
	      	$f1 = "11"; $f2 = "12"; break;
	    }

	    if ( $bimestre == '6' ){
	    	$op = $this->sql(" SELECT TO_CHAR( SYSDATE, 'DD/MM' ) fecha FROM DUAL ");
	      	if( $op[0]["FECHA"] == '31/12' ){
	      		$sqlNew = 0;
	      	}else{
	      		$sqlNew = 1;
	      	}
	    }

    	$fechas = $this->sql(" SELECT TO_CHAR(TRUNC( TO_DATE('".$f1."','MM'), 'MM' ),'DD-MM-YYYY') AS inicio, TO_CHAR(LAST_DAY( TRUNC( TO_DATE('".$f2."','MM') ) ),'DD-MM-YYYY') fin FROM DUAL ");

    	if( $sqlNew == 1 ){
      		$fechas = $this->sql("SELECT TO_CHAR( ADD_MONTHS( TRUNC( TO_DATE('".$f1."','MM'), 'MM' ),-12) , 'DD-MM-YYYY') AS inicio, TO_CHAR( ADD_MONTHS( LAST_DAY( TRUNC( TO_DATE('".$f2."','MM') ) ),-12 ),'DD-MM-YYYY') fin  FROM DUAL") ;
      	}

    	return $fechas;

    }
    /*--------------- /.FECHA DE INICIO Y FIN DEL BIMESTRE ---------------*/


    /*--------------------------- SQL DINAMICO ---------------------------*/
    public function sql($sql)
    {
		$conn = conexion::conectar();
		$res_array = array();

		$sql = $sql;
		$stid = oci_parse($conn, $sql);

		#echo $sql;
		oci_execute($stid);

		while (($row = oci_fetch_assoc($stid)) != false)
		{
			$res_array[]= $row;
		}

		oci_free_statement($stid);
		oci_close($conn);

		return $res_array;
    }
    /*--------------------------- /.SQL DINAMICO ---------------------------*/



    /*====================== VALIDA SI ES FECHA  ======================*/
	function validateDate($date, $format = 'Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}
	/*====================== /.VALIDA SI ES FECHA  ======================*/



}
