<?php

include_once '../libs/conOra.php';                                              /* CONEXION A LA BD */

class Sgc {

  public function grafica_total_sgc($fechaInicio, $fechaFin) {                  /*GRAFICA     #1 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT * FROM
            (select count(t.iid_sacp) as todos from sacp_sgc t where t.iid_sacp>0 and t.d_fec_sol is not null and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY') and t.iid_status in ('REVISADO', 'REGISTRADO','PREREGISTRADO','CERRADO') ),
            (select count(t.iid_sacp) as cerrados from sacp_sgc t where t.iid_sacp>0 and t.d_fec_sol is not null and t.iid_status in ('CERRADO') and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')),
            (select count(t.iid_sacp) as abiertos from sacp_sgc t where t.iid_sacp>0 and t.d_fec_sol is not null and t.iid_status in ('REVISADO', 'REGISTRADO','PREREGISTRADO') and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY'))
            ";
    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
            }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

   public function grafica_abiertos_plaza($fechaInicio, $fechaFin) {            /*GRAFICA     #3 */

   $conn = conexion::conectar();
   $res_array = array();
   $sql = "SELECT * FROM(SELECT ID_PLAZA,PLAZA,

              (SELECT COUNT(S.IID_SACP) AS TOT_SACP FROM SACP_SGC S
              WHERE S.IID_SACP>0 AND S.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
              AND S.IID_PLAZA=ID_PLAZA GROUP BY S.IID_PLAZA) AS TOTAL,--TOTAL

              (SELECT COUNT(S.IID_SACP) AS TOT_AB FROM SACP_SGC S
              WHERE S.IID_SACP>0 AND S.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
              AND S.IID_STATUS IN('REVISADO', 'REGISTRADO','PREREGISTRADO')
              AND S.IID_PLAZA=ID_PLAZA
              GROUP BY S.IID_PLAZA) AS ABIERTOS,--TOTAL AB

              (SELECT COUNT(S.IID_SACP) AS TOT_CE FROM SACP_SGC S
              WHERE S.IID_SACP>0 AND S.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
              AND S.IID_STATUS IN('CERRADO')
              AND S.IID_PLAZA=ID_PLAZA
              GROUP BY S.IID_PLAZA) AS CERRADOS--CERRADOS

              FROM (
                SELECT DISTINCT(SG.IID_PLAZA) AS ID_PLAZA, REPLACE(p.v_razon_social, '(ARGO)') AS plaza
                FROM SACP_SGC SG, PLAZA P WHERE SG.IID_SACP>0
                AND SG.IID_PLAZA=P.IID_PLAZA
                AND SG.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
                ORDER BY SG.IID_PLAZA))
           WHERE ABIERTOS>0 ORDER BY PLAZA";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

   while (($row = oci_fetch_assoc($stid)) != false) {
          $res_array[]= $row;
          }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    public function grafica_areas_abiertos($fechaInicio, $fechaFin) {           /*GRAFICA     #5 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT SD.IID_DEPTO AS DEPTO,SD.IID_AREA as AREA,SD.V_NOM_DEPTO as proceso, COUNT(SG.IID_SACP) AS sacp_abiertos
              FROM
                   SACP_SGC SG, SGC_AREAS SA, SGC_AREA_DEPTO SD
              WHERE
                    SG.IID_AREA=SA.IID_AREA AND SG.IID_DEPTO=SD.IID_DEPTO AND SA.IID_AREA=SD.IID_AREA
                    AND SG.IID_STATUS IN ('REVISADO', 'REGISTRADO', 'PREREGISTRADO')
                    AND SG.D_FEC_SOL between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
              GROUP BY SD.IID_DEPTO,SD.IID_AREA, SD.V_NOM_DEPTO ORDER BY PROCESO";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
            }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    public function grafica_proceso($fechaInicio, $fechaFin, $area, $depto) {             /*GRAFICA     #11 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql="SELECT REPLACE(P.V_RAZON_SOCIAL,'(ARGO)') AS plaza, COUNT(SG.IID_SACP) TOTAL_PROC
          FROM SACP_SGC SG, SGC_AREAS SA, SGC_AREA_DEPTO SD, PLAZA P
          WHERE
               SG.IID_AREA=SA.IID_AREA AND SG.IID_DEPTO=SD.IID_DEPTO AND SA.IID_AREA=SD.IID_AREA
               AND SG.IID_STATUS IN ('REVISADO', 'REGISTRADO', 'PREREGISTRADO')
               AND SG.D_FEC_SOL between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
               AND SG.IID_AREA=$area AND SG.IID_DEPTO=$depto
               AND P.IID_PLAZA=SG.IID_PLAZA
          GROUP BY SD.V_NOM_DEPTO, P.V_RAZON_SOCIAL ORDER BY PLAZA";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
          }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    public function grafica_procesos_abiertos($fechaInicio, $fechaFin) {        /*GRAFICA     #6 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT SG.IID_PROCESO, SP.V_DESC_PROCESO AS proceso, COUNT(SG.IID_PROCESO) AS sacp_abiertos
            FROM SGC_PROCESOS SP, SACP_SGC SG
            WHERE SP.IID_PROCESO = SG.IID_PROCESO AND SG.IID_STATUS IN ('REVISADO', 'REGISTRADO', 'PREREGISTRADO')
            AND sg.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
            GROUP BY SG.IID_PROCESO, SP.V_DESC_PROCESO";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
          }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    public function grafica_capitulo($fechaInicio, $fechaFin, $idProceso) {             /*GRAFICA     #12 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql="SELECT REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS PLAZA,SG.IID_PLAZA, COUNT(SG.IID_PROCESO) AS sacp_abiertos
          FROM SGC_PROCESOS SP, SACP_SGC SG, PLAZA P
          WHERE SP.IID_PROCESO = SG.IID_PROCESO AND P.IID_PLAZA=SG.IID_PLAZA AND SG.IID_STATUS IN ('REVISADO', 'REGISTRADO', 'PREREGISTRADO')
          AND sg.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
          AND SG.IID_PROCESO=$idProceso
          GROUP BY SG.IID_PLAZA, P.V_RAZON_SOCIAL";

      $stid = oci_parse($conn, $sql);
              oci_execute($stid);

      while (($row = oci_fetch_assoc($stid)) != false) {
              $res_array[]= $row;
            }

      oci_free_statement($stid);
      oci_close($conn);

      return $res_array;
      }

    public function grafica_cerrados_plaza($fechaInicio, $fechaFin) {           /*GRAFICA     #8 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT * FROM(SELECT ID_PLAZA,PLAZA,

          (SELECT COUNT(S.IID_SACP) AS TOT_SACP FROM SACP_SGC S
          WHERE S.IID_SACP>0 AND S.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
          AND S.IID_PLAZA=ID_PLAZA GROUP BY S.IID_PLAZA) AS TOTAL,--TOTAL

          (SELECT COUNT(S.IID_SACP) AS TOT_AB FROM SACP_SGC S
          WHERE S.IID_SACP>0 AND S.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
          AND S.IID_STATUS IN('REVISADO', 'REGISTRADO','PREREGISTRADO')
          AND S.IID_PLAZA=ID_PLAZA
          GROUP BY S.IID_PLAZA) AS ABIERTOS,--TOTAL AB

          (SELECT COUNT(S.IID_SACP) AS TOT_CE FROM SACP_SGC S
          WHERE S.IID_SACP>0 AND S.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
          AND S.IID_STATUS IN('CERRADO')
          AND S.IID_PLAZA=ID_PLAZA
          GROUP BY S.IID_PLAZA) AS CERRADOS--CERRADOS

          FROM (
          SELECT DISTINCT(SG.IID_PLAZA) AS ID_PLAZA, REPLACE(p.v_razon_social, '(ARGO)') AS plaza
          FROM SACP_SGC SG, PLAZA P WHERE SG.IID_SACP>0
          AND SG.IID_PLAZA=P.IID_PLAZA
          AND SG.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio','DD/MM/YYYY') AND TO_DATE('$fechaFin','DD/MM/YYYY')
          ORDER BY SG.IID_PLAZA))
          WHERE CERRADOS>0--ABIERTOS>0";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
          }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    public function obtenerFecha() {

    $conn=conexion::conectar();
    $res_array=array();
    $sql="SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 0), 'DD/MM/YYYY') mes1, TO_CHAR(SYSDATE, 'DD/MM/YYYY') mes2 FROM DUAL";

    $stid=oci_parse($conn, $sql);
          oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
            }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    function validateDate($date, $format = 'd/m/Y'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
  	}

    function crearExcel($fechaInicio, $fechaFin){
    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT
             t.iid_sacp                          AS no_sacp,
             REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS plaza,
             sd.v_nom_depto                      AS proceso,
             S.V_DESC_PROCESO                    AS norma,
             T.V_DESCRPCION                      AS descripcion,
             T.V_NOM_EMISOR                      AS emisor,
             T.D_FEC_SOL                         AS fecha_solicitud,
             T.D_FEC_ACCION                      AS fecha_plan_accion,
             TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS duracion_dias,
             T.D_FEC_SOL                         AS fecha_solicitud1,
             T.D_FEC_VER                         AS fecha_cierre,
             TO_DATE(T.D_FEC_VER,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS duracion_dias2,
             T.IID_STATUS                        AS estatus
            FROM
                 SACP_SGC T, PLAZA P, SGC_PROCESOS S,SGC_AREA_DEPTO SD
            WHERE
                 T.IID_PLAZA = P.IID_PLAZA
                 AND T.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio', 'DD/MM/YYYY') AND TO_DATE('$fechaFin', 'DD/MM/YYYY')
                 AND S.IID_PROCESO=T.IID_PROCESO
                 AND t.iid_area=sd.iid_area AND T.IID_DEPTO=SD.IID_DEPTO
            ORDER BY
                  P.V_RAZON_SOCIAL, S.V_DESC_PROCESO";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
            }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    function exportar($datos, $fechaInicio, $fechaFin){

    header('Content-type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-Disposition: attachment; filename=SACP DEL $fechaInicio AL $fechaFin.xls"); //Indica el nombre del archivo resultante
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table>
            <tr>
              <th style='background:#74CF56; color:#000'>NO. SACP</th>
              <th style='background:#CCC; color:#000'>PLAZA</th>
              <th style='background:#CCC; color:#000'>PROCESO</th>
              <th style='background:#CCC; color:#000'>CAP. DE NORMA</th>
              <th style='background:#CCC; color:#000'>DESCRIPCION</th>
              <th style='background:#CCC; color:#000'>EMISOR</th>
              <th style='background:#91E885; color:#000'>FECHA SOLICITUD</th>
              <th style='background:#91E885; color:#000'>FECHA PLAN ACCION</th>
              <th style='background:#91E885; color:#000'>DURACION DIAS</th>
              <th style='background:#82ACD9; color:#000'>FECHA SOLICITUD</th>
              <th style='background:#82ACD9; color:#000'>FECHA CIERRE</th>
              <th style='background:#82ACD9; color:#000'>DURACION DIAS</th>
              <th style='background:#CCC; color:#000'>ESTATUS</th>
            </tr>";

            for ($i=0; $i <count($datos) ; $i++) {
                echo "<tr>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["NO_SACP"]."</td>
                        <td align='center' style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["PLAZA"], 'utf-16', 'utf-8')."</td>
                        <td align='center' style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["PROCESO"], 'utf-16', 'utf-8')."</td>
                        <td align='center' style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["NORMA"], 'utf-16', 'utf-8')."</td>
                        <td style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["DESCRIPCION"], 'utf-16', 'utf-8')."</td>
                        <td style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["EMISOR"], 'utf-16', 'utf-8')."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["FECHA_SOLICITUD"]."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["FECHA_PLAN_ACCION"]."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["DURACION_DIAS"]."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["FECHA_SOLICITUD1"]."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["FECHA_CIERRE"]."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["DURACION_DIAS2"]."</td>
                        <td align='center' style='vertical-align:middle'>".$datos[$i]["ESTATUS"]."</td>
                      </tr>";
              }
      echo "</table>";
      exit();
      }

      public function grafica_plazas($fechaInicio, $fechaFin, $id) {            /*PLAZAS DE GRAFICAS  #2, #7 Y #10 */

        $conn = conexion::conectar();
        $res_array = array();

        switch ($id) {
          case 1:
          $sql = "SELECT DISTINCT(t.iid_plaza) as id_plaza, REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') as razon
                  FROM SACP_SGC T, PLAZA P
                  WHERE t.iid_sacp>0 and t.d_fec_sol is not null AND P.IID_PLAZA=T.IID_PLAZA
                  AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                  ORDER BY RAZON";
          break;
          case 2:
          $sql="SELECT DISTINCT(t.iid_plaza) as id_plaza, REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') as razon
                FROM SACP_SGC T, PLAZA P
                WHERE t.iid_sacp>0 and t.d_fec_sol is not null AND P.IID_PLAZA=T.IID_PLAZA
                AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                AND T.IID_STATUS IN('CERRADO')
                ORDER BY RAZON";
          break;
          case 3:
          $sql="SELECT DISTINCT(t.iid_plaza) as id_plaza, REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') as razon
                  FROM SACP_SGC T, PLAZA P
                  WHERE t.iid_sacp>0 and t.d_fec_sol is not null AND P.IID_PLAZA=T.IID_PLAZA
                  and t.iid_status in ('REVISADO', 'REGISTRADO', 'PREREGISTRADO')
                  AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                  ORDER BY RAZON";
          break;
        }
        $stid = oci_parse($conn, $sql);
                oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
                $res_array[]= $row;
                }

        oci_free_statement($stid);
        oci_close($conn);

        return $res_array;
        }

        public function grafica_plazas_detalle($fechaInicio, $fechaFin,$id, $plaza) {              /*TABLAS DE GRAFICAS  #2, #7 Y #10 */

          $conn = conexion::conectar();
          $res_array = array();


          switch ($id) {
            case 1:
            $sql = "SELECT REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS PLAZA,T.IID_SACP,SA.V_NOM_AREA, SD.V_NOM_DEPTO, to_char(T.D_FEC_SOL, 'yyyy-mm-dd') as d_fec_sol, to_char(T.D_FEC_ACCION, 'yyyy-mm-dd') as D_FEC_ACCION, TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                    FROM SACP_SGC T, PLAZA P, SGC_AREAS SA, SGC_AREA_DEPTO SD
                    WHERE t.iid_sacp>0 and t.d_fec_sol is not null AND P.IID_PLAZA=T.IID_PLAZA
                    AND T.IID_AREA=SA.IID_AREA AND T.IID_DEPTO=SD.IID_DEPTO
                    AND T.IID_AREA=SD.IID_AREA
                    AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                    AND T.IID_PLAZA=$plaza
                    AND T.IID_STATUS IN('CERRADO','REVISADO', 'REGISTRADO', 'PREREGISTRADO')
                    ORDER BY P.V_RAZON_SOCIAL, DURACION";
                    /*"SELECT REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS PLAZA,T.IID_SACP, to_char(T.D_FEC_SOL, 'yyyy-mm-dd') as d_fec_sol, to_char(T.D_FEC_ACCION, 'yyyy-mm-dd') as D_FEC_ACCION, TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                    FROM SACP_SGC T, PLAZA P
                    WHERE t.iid_sacp>0 and t.d_fec_sol is not null AND P.IID_PLAZA=T.IID_PLAZA
                    AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                    AND T.IID_PLAZA=$plaza
                    AND T.IID_STATUS IN('CERRADO','REVISADO', 'REGISTRADO', 'PREREGISTRADO')
                    ORDER BY P.V_RAZON_SOCIAL, DURACION";*/      
              break;
              case 2:
              $sql = "SELECT REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS PLAZA,T.IID_SACP,SA.V_NOM_AREA, SD.V_NOM_DEPTO, to_char(T.D_FEC_VER, 'yyyy-mm-dd') as d_fec_ver, to_char(T.D_FEC_SOL, 'yyyy-mm-dd') as D_FEC_SOL, TO_DATE(T.D_FEC_VER,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                      FROM SACP_SGC T, PLAZA P, SGC_AREAS SA, SGC_AREA_DEPTO SD
                      WHERE t.iid_sacp>0 and t.d_fec_sol is not null AND P.IID_PLAZA=T.IID_PLAZA
                      AND T.IID_AREA=SA.IID_AREA AND T.IID_DEPTO=SD.IID_DEPTO
                      AND T.IID_AREA=SD.IID_AREA
                      AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY') AND T.IID_STATUS IN('CERRADO')
                      AND T.IID_PLAZA=$plaza
                      ORDER BY P.V_RAZON_SOCIAL, DURACION";
              break;
              case 3:
              $sql = "SELECT REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS PLAZA,T.IID_SACP,T.IID_SACP,SA.V_NOM_AREA, SD.V_NOM_DEPTO,to_char(T.D_FEC_ACCION, 'yyyy-mm-dd') as D_FEC_ACCION, to_char(T.D_FEC_SOL, 'yyyy-mm-dd') as d_fec_sol,TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                      FROM SACP_SGC T, PLAZA P, SGC_AREAS SA, SGC_AREA_DEPTO SD
                      WHERE t.iid_sacp>0 and t.d_fec_sol is not null
                      AND P.IID_PLAZA=T.IID_PLAZA
                      AND T.IID_AREA=SA.IID_AREA AND T.IID_DEPTO=SD.IID_DEPTO
                      AND T.IID_AREA=SD.IID_AREA
                      and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY') and t.iid_status in ('REVISADO', 'REGISTRADO', 'PREREGISTRADO')
                      AND T.IID_PLAZA=$plaza
                      order by p.v_razon_social, duracion";
              break;
          }

          $stid = oci_parse($conn, $sql);
                  oci_execute($stid);

          while (($row = oci_fetch_assoc($stid)) != false) {
                  $res_array[]= $row;
                  }

          oci_free_statement($stid);
          oci_close($conn);

          return $res_array;
          }

}
?>
