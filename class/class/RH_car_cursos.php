<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: DIEGO ALTAMIRANO SUAREZ
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class RotacionPersonal
{
 //SELECT DE TODOS LOS CURSOS
 function cursosSql(){
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
   $sql = "SELECT ID_CURSO, ID_CURSO || ' ' || S_DESCRIPCION AS V_NOMBRE FROM RH_CURSOS_CAT ORDER BY ID_CURSO";
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
 // EMPLEADOS POR Plaza
 function empSql($plaza){
   $conn = conexion::conectar();
   $res_array = array();
   switch($plaza){
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
   $sql = "SELECT IID_EMPLEADO, IID_EMPLEADO || ' ' || V_NOMBRE || ' ' || V_APE_PAT AS V_NOMBRE
            FROM NO_PERSONAL WHERE NO_PERSONAL.IID_PLAZA IN (".$in_plaza.") AND NO_PERSONAL.S_STATUS = 1
            ORDER BY IID_EMPLEADO";
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
  /*++++++++++++++++++++++++++++++++WIDGETS++++++++++++++++++++++++++++++++++++++++*/
  public function widget($plaza,$fil_check,$fecha,$curso,$empleado){
    $conn = conexion::conectar();
    $res_array = array();

    $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between TRUNC(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0)) AND TRUNC(ADD_MONTHS(LAST_DAY(TO_DATE(SYSDATE)), -1)) ) )";
    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between to_date('".substr($fecha, 0, 10)."',  'dd/mm/yyyy') AND to_date('".substr($fecha, 11, 10)."', 'dd/mm/yyyy') ) ) ";
      }
    }

    if ($curso == 'ALL') {
      $and_curso = '';
    } else {
      $and_curso = ' RH_CURSOS_CAT.ID_CURSO = '.$curso.' AND';
    }

    if ($empleado == 'ALL') {
      $and_empleado = '';
    }else {
      $and_empleado = ' AND RH_CURSOS_EMPL.IID_EMPLEADO = '.$empleado;
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
    $sql = "SELECT COUNT(RH_CURSOS_EMPL.IID_EMPLEADO) AS N_EMPLEADO
           FROM AD_CO_PROVEEDOR,
                RH_CURSOS_CAL,
                RH_CURSOS_CAT ,
                NO_PERSONAL,
                RH_CURSOS_EMPL,
           RH_CURSOS_PROG
          WHERE ( AD_CO_PROVEEDOR.IID_PROVEEDOR(+) = RH_CURSOS_CAL.IID_PROVEEDOR ) and
                (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO) and
                ( RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO ) and
           RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL AND
                ".$and_curso."
                RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO AND
                RH_CURSOS_EMPL.IID_EMPLEADO = (SELECT C.IID_EMPLEADO FROM NO_PERSONAL C WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO AND C.IID_PLAZA IN (".$in_plaza."))
                ".$and_fecha_curso."
                ".$and_empleado."";
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

  //++++++++++++++++++++++++++++++++++++++++++++++++++++++widgets tiempo trabajado ++++++++++++++++++++++++++++++++++++++++++++//
  public function widgettt($plaza,$fil_check,$fecha,$curso,$empleado){
    $conn = conexion::conectar();
    $res_array = array();

    $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between TRUNC(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0)) AND TRUNC(ADD_MONTHS(LAST_DAY(TO_DATE(SYSDATE)), -1)) ) )";
    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between to_date('".substr($fecha, 0, 10)."',  'dd/mm/yyyy') AND to_date('".substr($fecha, 11, 10)."', 'dd/mm/yyyy') ) ) ";
      }
    }

    if ($curso == 'ALL') {
      $and_curso = '';
    } else {
      $and_curso = ' RH_CURSOS_CAT.ID_CURSO = '.$curso.' AND';
    }

    if ($empleado == 'ALL') {
      $and_empleado = '';
    }else {
      $and_empleado = ' AND RH_CURSOS_EMPL.IID_EMPLEADO = '.$empleado;
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
    $sql = "SELECT SUM(RH_CURSOS_CAL.N_HORAS_DIA) AS TOTAL_HORAS
           FROM AD_CO_PROVEEDOR,
                RH_CURSOS_CAL,
                RH_CURSOS_CAT ,
                NO_PERSONAL,
                RH_CURSOS_EMPL,
           RH_CURSOS_PROG
          WHERE ( AD_CO_PROVEEDOR.IID_PROVEEDOR(+) = RH_CURSOS_CAL.IID_PROVEEDOR ) and
                (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO) and
                ( RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO ) and
           RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL AND
                ".$and_curso."
                RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO AND
                RH_CURSOS_EMPL.IID_EMPLEADO = (SELECT C.IID_EMPLEADO FROM NO_PERSONAL C WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO AND C.IID_PLAZA IN (".$in_plaza."))
                ".$and_fecha_curso."
                ".$and_empleado."";
        //echo $sql;
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

	/*++++++++++++++++++++++++ GRAFICA PERSONAL CURSO ++++++++++++++++++++++++*/
	public function grafica($plaza,$fil_check,$fecha,$curso,$empleado){
		$conn = conexion::conectar();
		$res_array = array();

		$and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between TRUNC(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0)) AND TRUNC(ADD_MONTHS(LAST_DAY(TO_DATE(SYSDATE)), -1)) ) )";
		if ($fil_check == 'on'){

			if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between to_date('".substr($fecha, 0, 10)."',  'dd/mm/yyyy') AND to_date('".substr($fecha, 11, 10)."', 'dd/mm/yyyy') ) ) ";
			}
		}

    if ($curso == 'ALL') {
      $and_curso = '';
    } else {
      $and_curso = ' RH_CURSOS_CAT.ID_CURSO = '.$curso.' AND';
    }

    if ($empleado == 'ALL') {
      $and_empleado = '';
    }else {
      $and_empleado = ' AND RH_CURSOS_EMPL.IID_EMPLEADO = '.$empleado;
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
		$sql = "SELECT COUNT(RH_CURSOS_CAT.ID_CURSO) AS ID_CURSO,
           RH_CURSOS_CAT.ID_CURSO|| '  '|| RH_CURSOS_CAT.S_DESCRIPCION AS DESCRIPCION
           FROM AD_CO_PROVEEDOR,
                RH_CURSOS_CAL,
                RH_CURSOS_CAT ,
                NO_PERSONAL,
                RH_CURSOS_EMPL,
           RH_CURSOS_PROG
          WHERE ( AD_CO_PROVEEDOR.IID_PROVEEDOR(+) = RH_CURSOS_CAL.IID_PROVEEDOR ) and
                (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO) and
                ( RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO ) and
           RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL AND
                ".$and_curso."
                RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO AND
                RH_CURSOS_EMPL.IID_EMPLEADO = (SELECT C.IID_EMPLEADO FROM NO_PERSONAL C WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO AND C.IID_PLAZA IN (".$in_plaza."))
                ".$and_fecha_curso."
                ".$and_empleado."
           GROUP BY RH_CURSOS_CAT.ID_CURSO, RH_CURSOS_CAT.S_DESCRIPCION";
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

  /**************************PERSONAL TOMO CURSO Y CUANTO TIEMPO LO TOMO ******************/
  public function horas_vs_empleados($anio, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

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
    $sql = "SELECT RM.N_MES,
       RM.MES,
       (SELECT SUM(RH_CURSOS_CAL.N_HORAS_DIA)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio'))
               /
               (SELECT COUNT(per.iid_empleado) AS BAJA
  				          FROM no_personal per
  				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
  				                                   AND con.iid_contrato = per.iid_contrato
  				          LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
  				                                                          CON.IID_CONTRATO
  				                                                      AND RCAN.IID_EMPLEADO =
  				                                                          CON.IID_EMPLEADO
  				                                                      AND RCAN.FECHA_CANCELACION <=
  				                                                          LAST_DAY(to_date(RM.N_MES ||
  				                                                                           '/$anio',
  				                                                                           'mm/yyyy'))
  				         WHERE per.iid_plaza IN ($in_plaza)
  				           AND (PER.d_fecha_ingreso <=
  				               LAST_DAY(TO_DATE(RM.N_MES || '/$anio', 'mm/yyyy')))
  				           AND RCAN.FECHA_CANCELACION IS NULL
  				           AND per.iid_empleado not in (209, 1, 2400)
  								 		)  AS HORAS_REALES
        FROM RH_MESES_GRAFICAS RM
       GROUP BY RM.N_MES, MES
       ORDER BY RM.N_MES";
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


  /*consultas general 5 AÑOS*/
  public function horas_general($anio, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.N_HORAS_DIA)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))
               /
       SUM((SELECT COUNT(per.iid_empleado) AS BAJA
				          FROM no_personal per
				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                                   AND con.iid_contrato = per.iid_contrato
				          LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
				                                                          CON.IID_CONTRATO
				                                                      AND RCAN.IID_EMPLEADO =
				                                                          CON.IID_EMPLEADO
				                                                      AND RCAN.FECHA_CANCELACION <=
				                                                          LAST_DAY(to_date(RM.N_MES ||
				                                                                           '/$anio',
				                                                                           'mm/yyyy'))
				         WHERE per.iid_plaza IN ($in_plaza)
				           AND (PER.d_fecha_ingreso <=
				               LAST_DAY(TO_DATE(RM.N_MES || '/$anio', 'mm/yyyy')))
				           AND RCAN.FECHA_CANCELACION IS NULL
				           AND per.iid_empleado not in (209, 1, 2400)
                 ))AS TOTAL
  FROM RH_MESES_GRAFICAS RM
 ORDER BY RM.N_MES";
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


  public function horas_general2($fecha, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

    $fecha_st = substr($fecha, 0, -10);
    $anio = substr($fecha, -7);
    $anio2 = substr($fecha, -4);
    $mes = substr($fecha, -7, 2);
    #echo $fecha_st;
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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.N_HORAS_DIA)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio2')))
               /
       SUM((SELECT COUNT(per.iid_empleado) AS BAJA
				          FROM no_personal per
				         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
				                                   AND con.iid_contrato = per.iid_contrato
				          LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
				                                                          CON.IID_CONTRATO
				                                                      AND RCAN.IID_EMPLEADO =
				                                                          CON.IID_EMPLEADO
				                                                      AND RCAN.FECHA_CANCELACION <=
				                                                          LAST_DAY(to_date(RM.N_MES ||
				                                                                           '/$anio2',
				                                                                           'mm/yyyy'))
				         WHERE per.iid_plaza IN ($in_plaza)
				           AND (PER.d_fecha_ingreso <=
				               LAST_DAY(TO_DATE(RM.N_MES ||'/$anio2', 'mm/yyyy')))
				           AND RCAN.FECHA_CANCELACION IS NULL
				           AND per.iid_empleado not in (209, 1, 2400)
                 ))AS TOTAL
  FROM RH_MESES_GRAFICAS RM
  WHERE RM.N_MES <= $mes
 ORDER BY RM.N_MES";
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

  public function horas_real_emp($anio, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.N_HORAS_DIA)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))/
          SUM((SELECT COUNT(RH_CURSOS_EMPL.IID_EMPLEADO)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio'))) AS TOTAL_REALES
  FROM RH_MESES_GRAFICAS RM
 ORDER BY RM.N_MES";
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

  public function horas_real_emp2($fecha, $plaza){
    $conn = conexion::conectar();
    $res_array = array();
    $anio = substr($fecha, -4);
    $mes = substr($fecha, -7, 2);
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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.N_HORAS_DIA)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))/
          SUM((SELECT COUNT(RH_CURSOS_EMPL.IID_EMPLEADO)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio'))) AS TOTAL_REALES
  FROM RH_MESES_GRAFICAS RM
  WHERE RM.N_MES <= $mes
 ORDER BY RM.N_MES";
      #  echo $sql;
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

  /*+++++++++++++++++++++++++GASTOS DE PERSONAL ++++++++++++++++++++++++++++*/
  public function costos_general($anio, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.C_COSTO)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))
               /
       SUM((SELECT COUNT(per.iid_empleado) AS BAJA
                  FROM no_personal per
                 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                                           AND con.iid_contrato = per.iid_contrato
                  LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
                                                                  CON.IID_CONTRATO
                                                              AND RCAN.IID_EMPLEADO =
                                                                  CON.IID_EMPLEADO
                                                              AND RCAN.FECHA_CANCELACION <=
                                                                  LAST_DAY(to_date(RM.N_MES ||
                                                                                   '/$anio',
                                                                                   'mm/yyyy'))
                 WHERE per.iid_plaza IN ($in_plaza)
                   AND (PER.d_fecha_ingreso <=
                       LAST_DAY(TO_DATE(RM.N_MES || '/$anio', 'mm/yyyy')))
                   AND RCAN.FECHA_CANCELACION IS NULL
                   AND per.iid_empleado not in (209, 1, 2400)
                 ))AS TOTAL
  FROM RH_MESES_GRAFICAS RM
 ORDER BY RM.N_MES";
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

  public function costos_general2($fecha, $plaza){
    $conn = conexion::conectar();
    $res_array = array();
    $anio = substr($fecha, -4);
    $mes = substr($fecha, -7, 2);
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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.C_COSTO)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))
               /
       SUM((SELECT COUNT(per.iid_empleado) AS BAJA
                  FROM no_personal per
                 INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                                           AND con.iid_contrato = per.iid_contrato
                  LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO =
                                                                  CON.IID_CONTRATO
                                                              AND RCAN.IID_EMPLEADO =
                                                                  CON.IID_EMPLEADO
                                                              AND RCAN.FECHA_CANCELACION <=
                                                                  LAST_DAY(to_date(RM.N_MES ||
                                                                                   '/$anio',
                                                                                   'mm/yyyy'))
                 WHERE per.iid_plaza IN ($in_plaza)
                   AND (PER.d_fecha_ingreso <=
                       LAST_DAY(TO_DATE(RM.N_MES || '/$anio', 'mm/yyyy')))
                   AND RCAN.FECHA_CANCELACION IS NULL
                   AND per.iid_empleado not in (209, 1, 2400)
                 ))AS TOTAL
  FROM RH_MESES_GRAFICAS RM
  WHERE RM.N_MES <= $mes
 ORDER BY RM.N_MES";
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
  /**************************/
  public function costos_general_emp_cap($anio, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.C_COSTO)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))
               /
               SUM((SELECT COUNT(RH_CURSOS_EMPL.IID_EMPLEADO)
                  FROM AD_CO_PROVEEDOR,
                       RH_CURSOS_CAL,
                       RH_CURSOS_CAT,
                       NO_PERSONAL,
                       RH_CURSOS_EMPL,
                       RH_CURSOS_PROG
                 WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
                       RH_CURSOS_CAL.IID_PROVEEDOR)
                   AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
                   AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
                   AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
                   AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
                   AND RH_CURSOS_EMPL.IID_EMPLEADO =
                       (SELECT C.IID_EMPLEADO
                          FROM NO_PERSONAL C
                         WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                           AND C.IID_PLAZA IN ($in_plaza))
                   AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
                       TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))AS TOTAL
  FROM RH_MESES_GRAFICAS RM
 ORDER BY RM.N_MES";
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

  public function costos_general_emp_cap2($fecha, $plaza){
    $conn = conexion::conectar();
    $res_array = array();

    $anio = substr($fecha, -4);
    $mes = substr($fecha, -7, 2);
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
    $sql = "SELECT SUM((SELECT SUM(RH_CURSOS_CAL.C_COSTO)
          FROM AD_CO_PROVEEDOR,
               RH_CURSOS_CAL,
               RH_CURSOS_CAT,
               NO_PERSONAL,
               RH_CURSOS_EMPL,
               RH_CURSOS_PROG
         WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
               RH_CURSOS_CAL.IID_PROVEEDOR)
           AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
           AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
           AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
           AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
           AND RH_CURSOS_EMPL.IID_EMPLEADO =
               (SELECT C.IID_EMPLEADO
                  FROM NO_PERSONAL C
                 WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                   AND C.IID_PLAZA IN ($in_plaza))
           AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
               TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))
               /
               SUM((SELECT COUNT(RH_CURSOS_EMPL.IID_EMPLEADO)
                  FROM AD_CO_PROVEEDOR,
                       RH_CURSOS_CAL,
                       RH_CURSOS_CAT,
                       NO_PERSONAL,
                       RH_CURSOS_EMPL,
                       RH_CURSOS_PROG
                 WHERE (AD_CO_PROVEEDOR.IID_PROVEEDOR(+) =
                       RH_CURSOS_CAL.IID_PROVEEDOR)
                   AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
                   AND (RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO)
                   AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL
                   AND RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
                   AND RH_CURSOS_EMPL.IID_EMPLEADO =
                       (SELECT C.IID_EMPLEADO
                          FROM NO_PERSONAL C
                         WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO
                           AND C.IID_PLAZA IN ($in_plaza))
                   AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND
                       TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'YYYY') = '$anio')))AS TOTAL
  FROM RH_MESES_GRAFICAS RM
  WHERE RM.N_MES <= $mes
 ORDER BY RM.N_MES";
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
  /*+++++++++++++++++++++++++TABLA PERSONAL TOMO CURSO +++++++++++++++++++++*/
  public function tabla_Curso($plaza,$fil_check,$fecha,$curso,$empleado){
    $conn = conexion::conectar();
    $res_array = array();

    $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between TRUNC(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0)) AND TRUNC(ADD_MONTHS(LAST_DAY(TO_DATE(SYSDATE)), -1)) ) )";
    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between to_date('".substr($fecha, 0, 10)."',  'dd/mm/yyyy') AND to_date('".substr($fecha, 11, 10)."', 'dd/mm/yyyy') ) ) ";
      }
    }

    if ($curso == 'ALL') {
      $and_curso = '';
    } else {
      $and_curso = ' RH_CURSOS_CAT.ID_CURSO = '.$curso.' AND';
    }

    if ($empleado == 'ALL') {
      $and_empleado = '';
    }else {
      $and_empleado = ' AND RH_CURSOS_EMPL.IID_EMPLEADO = '.$empleado;
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
    $sql = "SELECT RH_CURSOS_CAT.ID_CURSO|| ' '|| RH_CURSOS_CAT.S_DESCRIPCION AS DESCRIPCION,
           AD_CO_PROVEEDOR.V_NOMBRE AS NOMBRE_INSTRUCTOR,
           NO_PERSONAL.V_NOMBRE || ' ' ||  NO_PERSONAL.V_APE_PAT || ' ' || NO_PERSONAL.V_APE_MAT AS NOMBRE,
           (SELECT C.V_NOMBRE ||  ' ' || C.V_APE_PAT || ' ' || C.V_APE_MAT FROM NO_PERSONAL C WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO) AS TOMO_CURSO,
           TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'DD/MM/YYYY')AS INICIO,
           TO_CHAR(RH_CURSOS_CAL.D_FEC_FIN, 'DD/MM/YYYY') AS FIN,
           RH_CURSOS_CAL.N_HORAS_DIA || ' Hrs'  AS DURACION,
           (SELECT P.V_RAZON_SOCIAL FROM NO_PERSONAL C, PLAZA P WHERE C.IID_PLAZA = P.IID_PLAZA AND C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO) PLAZA_CURSO
           FROM AD_CO_PROVEEDOR,
                RH_CURSOS_CAL,
                RH_CURSOS_CAT ,
                NO_PERSONAL,
                RH_CURSOS_EMPL,
           RH_CURSOS_PROG
          WHERE ( AD_CO_PROVEEDOR.IID_PROVEEDOR(+) = RH_CURSOS_CAL.IID_PROVEEDOR ) and
                (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO) and
                ( RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO ) and
           RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL AND
                ".$and_curso."
                RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO AND
                RH_CURSOS_EMPL.IID_EMPLEADO = (SELECT C.IID_EMPLEADO FROM NO_PERSONAL C WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO AND C.IID_PLAZA IN (".$in_plaza."))
                ".$and_fecha_curso."
                ".$and_empleado."";
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

  //++++++++++++++++++++++++++GRAFICA MENSUAL +++++++++++++++++++++++++++++++++//
  public function graficaMensual($plaza,$fil_check,$fecha,$curso,$empleado){
    $conn = conexion::conectar();
    $res_array = array();

    //$and_fecha_curso = " AND ( ( TRUNC(RH_CURSOS_CAL.D_FEC_INICIO) between TRUNC(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0)) AND TRUNC(ADD_MONTHS(LAST_DAY(TO_DATE(SYSDATE)), -1)) ) )";

    $and_fecha_curso = " AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO,'YYYY') = TRUNC(TO_CHAR(SYSDATE, 'YYYY'))  ) ";
    if ($fil_check == 'on'){

      if ( $this->validateDate(substr($fecha,0,10)) AND $this->validateDate(substr($fecha,11,10)) ){
        $and_fecha_curso = " AND (TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO, 'MM') = RM.N_MES AND TO_CHAR(RH_CURSOS_CAL.D_FEC_INICIO,'YYYY') = '".substr($fecha,6,4)."' ) ";
      }
    }
    //echo $and_fecha_curso;
    if ($curso == 'ALL') {
      $and_curso = '';
    } else {
      $and_curso = ' RH_CURSOS_CAT.ID_CURSO = '.$curso.' AND';
    }

    if ($empleado == 'ALL') {
      $and_empleado = '';
    }else {
      $and_empleado = ' AND RH_CURSOS_EMPL.IID_EMPLEADO = '.$empleado;
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
    $sql = "SELECT RM.N_MES,
             RM.MES,
             (SELECT COUNT(RH_CURSOS_EMPL.IID_EMPLEADO)
             FROM AD_CO_PROVEEDOR, RH_CURSOS_CAL,
                  RH_CURSOS_CAT , NO_PERSONAL,
                  RH_CURSOS_EMPL,
                  RH_CURSOS_PROG
             WHERE ( AD_CO_PROVEEDOR.IID_PROVEEDOR(+) = RH_CURSOS_CAL.IID_PROVEEDOR )
                   AND (NO_PERSONAL.IID_EMPLEADO(+) = RH_CURSOS_CAL.IID_EMPLEADO)
                   AND ( RH_CURSOS_CAL.ID_CURSO = RH_CURSOS_CAT.ID_CURSO )
                   AND RH_CURSOS_PROG.IID_CURSO_CAL = RH_CURSOS_CAL.IID_CURSO_CAL AND
                   ".$and_curso."
                   RH_CURSOS_EMPL.NO_CURSO = RH_CURSOS_PROG.NO_CURSO
                   AND RH_CURSOS_EMPL.IID_EMPLEADO = (SELECT C.IID_EMPLEADO FROM NO_PERSONAL C WHERE C.IID_EMPLEADO = RH_CURSOS_EMPL.IID_EMPLEADO AND C.IID_PLAZA IN (".$in_plaza."))
                   ".$and_fecha_curso."
                   ".$and_empleado.") AS CONTADOR
              FROM RH_MESES_GRAFICAS RM
              GROUP BY RM.N_MES, MES
              ORDER BY RM.N_MES";

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

	/*++++++++++++++++++++++++ SQL FILTROS ++++++++++++++++++++++++*/
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



}
