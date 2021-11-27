<?php

include_once '../libs/conOra.php';                                              /* CONEXION A LA BD */

class Sgc {

  public function grafica_total_sgc($fechaInicio, $fechaFin) {                  /* TABLA         #1 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT * FROM
            (select count(t.iid_sacp) as todos    from sacp_sgc t where t.iid_sacp>0 and t.d_fec_sol is not null and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')),
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

  public function grafica_abiertos_plaza($fechaInicio, $fechaFin) {             /* TABLA         #2 */

      $conn = conexion::conectar();
      $res_array = array();
      $sql = "SELECT REPLACE(p.v_razon_social, '(ARGO)') AS plaza, COUNT (t.iid_sacp) as sacp_abiertos FROM sacp_sgc t, plaza p
              WHERE t.iid_status in ('REVISADO', 'REGISTRADO', 'PREREGISTRADO') and t.iid_plaza = p.iid_plaza
              AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
              GROUP BY p.v_razon_social ORDER BY p.v_razon_social";

      $stid = oci_parse($conn, $sql);
      oci_execute($stid);

      while (($row = oci_fetch_assoc($stid)) != false) {
              $res_array[]= $row;
            }

      oci_free_statement($stid);
      oci_close($conn);

      return $res_array;
    }

  public function grafica_cerrados_plaza($fechaInicio, $fechaFin) {             /* TABLA         #3 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT REPLACE(p.v_razon_social, '(ARGO)') AS plaza, COUNT (t.iid_sacp) as sacp_cerrados FROM sacp_sgc t, plaza p WHERE t.iid_status in ('CERRADO')
            AND t.iid_plaza = p.iid_plaza AND t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
            GROUP BY p.v_razon_social ORDER BY p.v_razon_social";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
          }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

  public function grafica_procesos_abiertos($fechaInicio, $fechaFin) {          /* TABLA         #4 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT SP.V_DESC_PROCESO AS proceso, COUNT(SG.IID_PROCESO) AS sacp_abiertos FROM SGC_PROCESOS SP, SACP_SGC SG
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

  public function grafica_plan_accion($fechaInicio, $fechaFin) {                /* TABLA         #5 */

    $conn = conexion::conectar();
    $res_array = array();
    $sql="SELECT DURACION, COUNT(DURACION)AS CANT_SACP
          FROM(
            SELECT D_FEC_ACCION, D_FEC_SOL,ABS(DURACION) AS DURACION
            FROM (
              SELECT T.D_FEC_ACCION, T.D_FEC_SOL,TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
              FROM SACP_SGC T
              WHERE t.iid_sacp>0 and t.d_fec_sol is not null and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
            )
          )
          WHERE DURACION>=0 GROUP BY DURACION ORDER BY DURACION";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) {
            $res_array[]= $row;
          }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
    }

    public function grafica_plan_accion_abiertos($fechaInicio, $fechaFin) {                /* TABLA         #6 */

      $conn = conexion::conectar();
      $res_array = array();
      $sql="SELECT DURACION, COUNT(DURACION)AS CANT_SACP
            FROM(
              SELECT D_FEC_ACCION, D_FEC_SOL,ABS(DURACION) AS DURACION
              FROM (
                SELECT T.D_FEC_ACCION, T.D_FEC_SOL,TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                FROM SACP_SGC T
                WHERE t.iid_sacp>0 and t.d_fec_sol is not null and t.iid_status in ('REVISADO', 'REGISTRADO', 'PREREGISTRADO') and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
              )
            )
            WHERE DURACION>=0 GROUP BY DURACION ORDER BY DURACION";

      $stid = oci_parse($conn, $sql);
              oci_execute($stid);

      while (($row = oci_fetch_assoc($stid)) != false) {
              $res_array[]= $row;
            }

      oci_free_statement($stid);
      oci_close($conn);

      return $res_array;
      }

    public function grafica_fecha_cierre($fechaInicio, $fechaFin) {             /* TABLA         #7 */

      $conn = conexion::conectar();
      $res_array = array();
      $sql = "SELECT DURACION, COUNT(DURACION)AS CANT_SACP
              FROM(
                SELECT D_FEC_VER, D_FEC_SOL,ABS(DURACION) AS DURACION
                FROM (
                  SELECT T.D_FEC_VER, T.D_FEC_SOL,TO_DATE(T.D_FEC_VER,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                  FROM SACP_SGC T
                  WHERE t.iid_sacp>0 and t.d_fec_sol is not null and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY') AND T.IID_STATUS IN('CERRADO')
                )
              )
              WHERE DURACION>=0 GROUP BY DURACION ORDER BY DURACION";

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

  function ObtenerMax($indice,$fechaInicio, $fechaFin){

    $conn=conexion::conectar();
    $res_array=array();

      switch ($indice) {
        case 5:
          $sql="SELECT MAX(DURACION) AS vmax, COUNT(CANT_SACP) AS creg FROM
                (SELECT DURACION, COUNT(DURACION)AS CANT_SACP
                      FROM(
                        SELECT D_FEC_ACCION, D_FEC_SOL,ABS(DURACION) AS DURACION
                        FROM (
                          SELECT T.D_FEC_ACCION, T.D_FEC_SOL,TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                          FROM SACP_SGC T
                          WHERE t.iid_sacp>0 and t.d_fec_sol is not null and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                        )
                      )
                      WHERE DURACION>=0 GROUP BY DURACION ORDER BY DURACION)";
          break;
          case 6:
          $sql="SELECT MAX(DURACION) AS vmax, COUNT(CANT_SACP) AS creg FROM
                (SELECT DURACION, COUNT(DURACION)AS CANT_SACP
                      FROM(
                        SELECT D_FEC_ACCION, D_FEC_SOL,ABS(DURACION) AS DURACION
                        FROM (
                          SELECT T.D_FEC_ACCION, T.D_FEC_SOL,TO_DATE(T.D_FEC_ACCION,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                          FROM SACP_SGC T
                          WHERE t.iid_sacp>0 and t.d_fec_sol is not null and t.iid_status in ('REVISADO', 'REGISTRADO', 'PREREGISTRADO') and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY')
                        )
                      )
                      WHERE DURACION>=0 GROUP BY DURACION ORDER BY DURACION)";
          break;
          case 7:
          $sql="SELECT MAX(DURACION) AS vmax, COUNT(CANT_SACP) AS creg FROM
              (SELECT DURACION, COUNT(DURACION)AS CANT_SACP
                      FROM(
                        SELECT D_FEC_VER, D_FEC_SOL,ABS(DURACION) AS DURACION
                        FROM (
                          SELECT T.D_FEC_VER, T.D_FEC_SOL,TO_DATE(T.D_FEC_VER,'DD/MM/YYYY') - TO_DATE(T.D_FEC_SOL,'DD/MM/YYYY') AS DURACION
                          FROM SACP_SGC T
                          WHERE t.iid_sacp>0 and t.d_fec_sol is not null and t.d_fec_sol between to_date('$fechaInicio', 'DD/MM/YYYY') and to_date('$fechaFin', 'DD/MM/YYYY') AND T.IID_STATUS IN('CERRADO')
                        )
                      )
                      WHERE DURACION>=0 GROUP BY DURACION ORDER BY DURACION)";
           break;
        default:
          break;
      }

      $stid=oci_parse($conn, $sql);
            oci_execute($stid);

      while (($row = oci_fetch_assoc($stid)) != false) {
              $res_array[]= $row;
            }

      oci_free_statement($stid);
      oci_close($conn);

      return $res_array;
	}

  function crearExcel($fechaInicio, $fechaFin){
    $conn = conexion::conectar();
    $res_array = array();
    $sql = "SELECT
           REPLACE(P.V_RAZON_SOCIAL, '(ARGO)') AS plaza,
           S.V_DESC_PROCESO                    AS proceso ,
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
               SACP_SGC T, PLAZA P, SGC_PROCESOS S
          WHERE
               T.IID_PLAZA = P.IID_PLAZA
               AND T.D_FEC_SOL BETWEEN TO_DATE('$fechaInicio', 'DD/MM/YYYY') AND TO_DATE('$fechaFin', 'DD/MM/YYYY')
               AND S.IID_PROCESO=T.IID_PROCESO
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
              <th style='background:#CCC; color:#000'>PLAZA</th>
              <th style='background:#CCC; color:#000'>PROCESO</th>
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
                        <td align='center' style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["PLAZA"], 'utf-16', 'utf-8')."</td>
                        <td align='center' style='vertical-align:middle'>".mb_convert_encoding($datos[$i]["PROCESO"], 'utf-16', 'utf-8')."</td>
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

}
?>
