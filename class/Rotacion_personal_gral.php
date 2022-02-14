<?php
include_once '../libs/conOra.php';                                              /* CONEXION A LA BD */

class Rotacion {

  public function fechas(){                                                     /* OBTENER AÑOS PARA COMPARACION*/

    $conn = conexion::conectar();
    $res_array = array();

    $sql = "SELECT TO_CHAR(SYSDATE, 'yyyy')-1 as anio_ant, TO_CHAR(sysdate, 'yyyy')-2 as sdo_anio_ant FROM DUAL";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) { $res_array[]= $row; }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
  }

  public function plantilla_trabajadores($andFecha, $and_fecha2) {            /* OBTENER DATOS PARA GRAFICA DE PLANTILLA DE TRABAJADORES*/

    $conn = conexion::conectar();
    $res_array = array();
    $and_habilitado = " AND CAN.HABILITADO = 0  ";
    $in_plaza = "2,3,4,5,6,7,8,17,18";

    $sql ="SELECT PLA.N_MES,
                PLA.MES,
                    ( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
                             FROM rh_cancelacion_contrato can
                     INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
                     WHERE per.s_status = 0
                          AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                           AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$andFecha."'
                           --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                           AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
                           AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                           --AND PER.IID_NUMNOMINA <> 2
                           AND PER.IID_NUMNOMINA in (1,2)
                         ) AS BAJA ,
                    ( SELECT COUNT(per.iid_empleado) AS BAJA
                             FROM no_personal per
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                           AND con.iid_contrato = per.iid_contrato
                     LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
                           AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$andFecha."','mm/yyyy') )
                     WHERE per.iid_plaza IN(".$in_plaza.")
                           AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$andFecha."', 'mm/yyyy')))
                           AND RCAN.FECHA_CANCELACION IS NULL
                           AND per.iid_empleado not in(209)
                           --AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
                           --AND PER.IID_NUMNOMINA <> 2
                           AND PER.IID_NUMNOMINA in (1,2)
                         ) as ACTIVO,
                   ( SELECT COUNT(CAN.IID_EMPLEADO) AS EMPLEADO
                     FROM rh_cancelacion_contrato can
                     INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(".$in_plaza.")
                     INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
                     WHERE per.s_status = 0
                               --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                               AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                               AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '".$and_fecha2."'
                               AND TO_CHAR(CAN.FECHA_CANCELACION, 'MM') = PLA.N_MES " .$and_habilitado."
                               --AND PER.IID_NUMNOMINA <> 2
                               AND PER.IID_NUMNOMINA in (1,2)
                               AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)) AS BAJA_ANTERIOR,
                    ( SELECT COUNT(per.iid_empleado) AS BAJA
                      FROM no_personal per
                      INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
                      LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
                              AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO AND RCAN.FECHA_CANCELACION <= LAST_DAY(to_date(PLA.N_MES||'/".$and_fecha2."','mm/yyyy') )
                      WHERE per.iid_plaza IN(".$in_plaza.")
                              AND (PER.d_fecha_ingreso <= LAST_DAY(TO_DATE(PLA.N_MES||'/".$and_fecha2."', 'mm/yyyy')))
                              AND RCAN.FECHA_CANCELACION IS NULL
                              AND per.iid_empleado not in(209)
                              --AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
                              --AND PER.IID_NUMNOMINA <> 2
                              AND PER.IID_NUMNOMINA in (1,2)
                      ) as ACTIVO_ANTERIOR
                           FROM RH_MESES_GRAFICAS pla
                           GROUP BY PLA.N_MES, PLA.MES
                           ORDER BY pla.N_MES";

    $stid = oci_parse($conn, $sql);
            oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false) { $res_array[]= $row; }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
}

public function motivos($andFecha) {                                        /* OBTENER LOS MOTIVOS DEL AÑO FILTRADOPARA GRAFICA DE CAUSAS DE BAJA*/

  $conn = conexion::conectar();
  $res_array = array();
  $and_habilitado = " AND CAN.HABILITADO = 0  ";

  $sql = "SELECT DISTINCT(CASE
                          WHEN CAN.N_MOTIVO_CANCELA=1 THEN 'CIERRE DE PLAZA'
                          WHEN CAN.N_MOTIVO_CANCELA=2 THEN 'FALTA'
                          WHEN CAN.N_MOTIVO_CANCELA=3 THEN 'ABANDONO DE TRABAJO'
                          WHEN CAN.N_MOTIVO_CANCELA=4 THEN 'PERIODO DE PRUEBAS'
                          WHEN CAN.N_MOTIVO_CANCELA=5 THEN 'RENUNCIA VOLUNTARIA'
                          WHEN CAN.N_MOTIVO_CANCELA=6 THEN 'CyV'
                          WHEN CAN.N_MOTIVO_CANCELA=7 THEN 'JEFE INMEDIATO'
                          WHEN CAN.N_MOTIVO_CANCELA IS NULL THEN 'OTROS'
                          END) AS MOTIVO, CAN.N_MOTIVO_CANCELA AS ID_MOTIVO
          FROM rh_cancelacion_contrato can
              INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(2,3,4,5,6,7,8,17,18)
              INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato
          WHERE per.s_status = 0
                AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$andFecha'
                --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                AND CAN.HABILITADO = 0
                AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                --AND PER.IID_NUMNOMINA <> 2
                AND PER.IID_NUMNOMINA in (1,2)
                ORDER BY CAN.N_MOTIVO_CANCELA";


  $stid = oci_parse($conn, $sql);
          oci_execute($stid);

  while (($row = oci_fetch_assoc($stid)) != false) { $res_array[]= $row; }

  oci_free_statement($stid);
  oci_close($conn);

  return $res_array;
}

public function causas_baja_anio_ant($motivos,$anio_ant) {                      /* OBTENER INFORMACION DE GRAFICA DE CAUSAS DE BAJA*/

  $conn = conexion::conectar();
  $res_array = array();
  $columnas="";

  for($x=0; $x<count($motivos); $x++){
    $nom_motivo = str_replace(" ", "_", $motivos[$x]['MOTIVO']);
    $id_motivo= $motivos[$x]['ID_MOTIVO'];

    if($x+1==count($motivos)){
      if($nom_motivo=="OTROS"){
        $columnas=$columnas.
        "(SELECT COUNT(CON.IID_EMPLEADO) AS TOTAL FROM rh_cancelacion_contrato can INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(2,3,4,5,6,7,8,17,18) INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza WHERE per.s_status = 0 AND (can.fecha_cancelacion -
        per.D_FECHA_INGRESO) > 5 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio_ant' /*AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)*/ AND CAN.HABILITADO=0 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL) AND CAN.IID_PUESTO=ID_PUESTO AND CAN.N_MOTIVO_CANCELA IS NULL AND PER.IID_NUMNOMINA in (1,2) GROUP BY pue.Iid_Puesto, PUE.V_DESCRIPCION) AS $nom_motivo";
      }else{
        $columnas=$columnas.
        "(SELECT COUNT(CON.IID_EMPLEADO) AS TOTAL FROM rh_cancelacion_contrato can INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(2,3,4,5,6,7,8,17,18) INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza WHERE per.s_status = 0 AND (can.fecha_cancelacion -
        per.D_FECHA_INGRESO) > 5 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio_ant' /*AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)*/ AND CAN.HABILITADO=0 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL) AND CAN.IID_PUESTO=ID_PUESTO AND CAN.N_MOTIVO_CANCELA = $id_motivo AND PER.IID_NUMNOMINA in (1,2) GROUP BY pue.Iid_Puesto, PUE.V_DESCRIPCION) AS $nom_motivo";
      }
    }else {
        $columnas=$columnas.
        "(SELECT COUNT(CON.IID_EMPLEADO) AS TOTAL FROM rh_cancelacion_contrato can INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND per.iid_contrato = can.iid_contrato AND per.iid_plaza IN(2,3,4,5,6,7,8,17,18) INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND con.iid_contrato = per.iid_contrato INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza WHERE per.s_status = 0 AND (can.fecha_cancelacion -
        per.D_FECHA_INGRESO) > 5 AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio_ant' /*AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)*/ AND CAN.HABILITADO=0 AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL) AND CAN.IID_PUESTO=ID_PUESTO AND CAN.N_MOTIVO_CANCELA = $id_motivo AND PER.IID_NUMNOMINA in (1,2) GROUP BY pue.Iid_Puesto, PUE.V_DESCRIPCION) AS $nom_motivo,";
    }
  }

  $sql = "SELECT ID_PUESTO, NOM_PUESTO, $columnas
          FROM (
          SELECT DISTINCT(pue.iid_puesto) AS ID_PUESTO, REPLACE(pue.v_descripcion, '-')AS NOM_PUESTO
          FROM rh_cancelacion_contrato can
               INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND
                          per.iid_contrato = can.iid_contrato AND
                          per.iid_plaza IN(2,3,4,5,6,7,8,17,18)
               INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND
                          con.iid_contrato = per.iid_contrato
               INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto
               INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza
          WHERE per.s_status = 0
                AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio_ant'
                --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                AND CAN.HABILITADO=0
                AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                AND PER.IID_NUMNOMINA in (1,2)
          GROUP BY pue.Iid_Puesto, PUE.V_DESCRIPCION
          ORDER BY ID_PUESTO, NOM_PUESTO)";

//echo $sql;
  $stid = oci_parse($conn, $sql);
          oci_execute($stid);

  while (($row = oci_fetch_assoc($stid)) != false) { $res_array[]= $row; }

  oci_free_statement($stid);
  oci_close($conn);

  return $res_array;
}


public function comparativo_puestos_bajas($anio_ant, $sdo_anio_ant) {                      /* OBTENER INFORMACION DE GRAFICA DE CAUSAS DE BAJA*/

  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT ID_PUESTO, NOM_PUESTO,
              (SELECT COUNT(PER.IID_EMPLEADO) AS TOTAL
                    FROM rh_cancelacion_contrato can
                         INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND
                                    per.iid_contrato = can.iid_contrato AND
                                    per.iid_plaza IN(2,3,4,5,6,7,8,17,18)
                         INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND
                                    con.iid_contrato = per.iid_contrato
                         INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto
                         INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza
                    WHERE per.s_status = 0
                          AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                          AND TO_CHAR(can.fecha_cancelacion, 'YYYY') IN( $anio_ant)
                          AND CAN.IID_PUESTO=ID_PUESTO
                          --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                          AND CAN.HABILITADO=0
                          AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                          AND PER.IID_NUMNOMINA in (1,2)
                    GROUP BY PUE.IID_PUESTO,PUE.V_DESCRIPCION) AS TOTAL_ANT,
              (SELECT COUNT(PER.IID_EMPLEADO) AS TOTAL
                    FROM rh_cancelacion_contrato can
                        INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND
                              per.iid_contrato = can.iid_contrato AND
                              per.iid_plaza IN(2,3,4,5,6,7,8,17,18)
                        INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND
                              con.iid_contrato = per.iid_contrato
                        INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto
                        INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza
              WHERE per.s_status = 0
                       AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                       AND TO_CHAR(can.fecha_cancelacion, 'YYYY') IN( $sdo_anio_ant)
                       AND CAN.IID_PUESTO=ID_PUESTO
                       --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                       AND CAN.HABILITADO=0
                       AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                       AND PER.IID_NUMNOMINA in (1,2)
              GROUP BY PUE.IID_PUESTO,PUE.V_DESCRIPCION) AS TOTAL_ANIO_ANT
         FROM (
          SELECT DISTINCT(pue.iid_puesto) AS ID_PUESTO, REPLACE(PUE.V_DESCRIPCION, '-') AS NOM_PUESTO
                      FROM rh_cancelacion_contrato can
                           INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado AND
                                      per.iid_contrato = can.iid_contrato AND
                                      per.iid_plaza IN(2,3,4,5,6,7,8,17,18)
                           INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado AND
                                      con.iid_contrato = per.iid_contrato
                           INNER JOIN RH_PUESTOS pue ON pue.iid_puesto=con.iid_puesto
                           INNER JOIN PLAZA pl ON pl.iid_plaza=per.iid_plaza
                      WHERE per.s_status = 0
                            AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                            AND TO_CHAR(can.fecha_cancelacion, 'YYYY') IN( $anio_ant,$sdo_anio_ant)
                            --AND CAN.IID_EMPLEADO NOT IN (1930, 2272, 2074)
                            AND CAN.HABILITADO=0
                            AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                            AND PER.IID_NUMNOMINA in (1,2)
                      GROUP BY PUE.IID_PUESTO,PUE.V_DESCRIPCION
                      ORDER BY PUE.IID_PUESTO)";

  $stid = oci_parse($conn, $sql);
          oci_execute($stid);

  while (($row = oci_fetch_assoc($stid)) != false) { $res_array[]= $row; }

  oci_free_statement($stid);
  oci_close($conn);

  return $res_array;
}

public function rotacion_plaza($anio_ant, $sdo_anio_ant) {                      /* OBTENER INFORMACION DE GRAFICA DE CAUSAS DE BAJA*/

  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza,

              	(SELECT COUNT(CAN.IID_EMPLEADO) FROM rh_cancelacion_contrato can
                       INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado
                       AND per.iid_contrato = can.iid_contrato
                       AND per.iid_plaza = pla.iid_plaza
                       INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                       AND con.iid_contrato = per.iid_contrato
                WHERE per.s_status = 0
                       AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                       AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$anio_ant'
                       AND CAN.HABILITADO = 0 /*AND can.iid_empleado not in (1930, 2272, 2074)*/
                       AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                       AND PER.IID_NUMNOMINA IN(1,2) ) AS baja_ant ,

              	(SELECT count(per.iid_empleado) FROM no_personal per
                       INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                       AND con.iid_contrato = per.iid_contrato
                       LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
                       AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO
                       AND RCAN.FECHA_CANCELACION <= trunc(to_date('01/01/$anio_ant','dd/mm/yyyy') )
                WHERE per.d_fecha_ingreso < trunc(to_date('31/12/$anio_ant','dd/mm/yyyy') )
                      AND RCAN.FECHA_CANCELACION IS NULL AND per.iid_plaza = pla.iid_plaza
                      AND per.iid_empleado not in(209)
                      --AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
                      AND PER.IID_NUMNOMINA IN(1,2) ) as activo_ant,

              	(SELECT COUNT(CAN.IID_EMPLEADO) FROM rh_cancelacion_contrato can
                       INNER JOIN no_personal per ON per.iid_empleado = can.iid_empleado
                       AND per.iid_contrato = can.iid_contrato
                       AND per.iid_plaza = pla.iid_plaza
                       INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                       AND con.iid_contrato = per.iid_contrato
                WHERE per.s_status = 0
                       AND (can.fecha_cancelacion - per.D_FECHA_INGRESO) > 5
                       AND TO_CHAR(can.fecha_cancelacion, 'YYYY') = '$sdo_anio_ant'
                       AND CAN.HABILITADO = 0 /*AND can.iid_empleado not in (1930, 2272, 2074)*/
                       AND (CAN.N_MOTIVO_CANCELA NOT IN (1) OR CAN.N_MOTIVO_CANCELA IS NULL)
                       AND PER.IID_NUMNOMINA IN(1,2) ) AS baja ,

              	(SELECT count(per.iid_empleado) FROM no_personal per
                       INNER JOIN no_contrato con ON con.iid_empleado = per.iid_empleado
                       AND con.iid_contrato = per.iid_contrato
                       LEFT OUTER JOIN RH_CANCELACION_CONTRATO RCAN ON RCAN.IID_CONTRATO = CON.IID_CONTRATO
                       AND RCAN.IID_EMPLEADO = CON.IID_EMPLEADO
                       AND RCAN.FECHA_CANCELACION <= trunc(to_date('01/01/$sdo_anio_ant','dd/mm/yyyy') )
                WHERE per.d_fecha_ingreso < trunc(to_date('31/12/$sdo_anio_ant','dd/mm/yyyy') )
                      AND RCAN.FECHA_CANCELACION IS NULL AND per.iid_plaza = pla.iid_plaza
                      AND per.iid_empleado not in(209)
                      --AND per.iid_empleado not in(209, 1, 2400, 1930, 2272, 2074)
                      AND PER.IID_NUMNOMINA IN(1,2) ) as activo
          FROM plaza pla WHERE pla.iid_plaza IN (2,3,4,5,6,7,8,17,18) ORDER BY pla.iid_plaza";

  $stid = oci_parse($conn, $sql);
          oci_execute($stid);

  while (($row = oci_fetch_assoc($stid)) != false) { $res_array[]= $row; }

  oci_free_statement($stid);
  oci_close($conn);

  return $res_array;
}


}
 ?>
