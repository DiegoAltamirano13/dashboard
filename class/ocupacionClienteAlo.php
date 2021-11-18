<?php
include_once '../libs/conOra.php';
class OcupacionCliente
{
public function graficaDonut(){
  $conn = conexion::conectar();
  $res_array = array();

    $sql = "SELECT (COUNT(V_TRANSLADO)/
(SELECT COUNT(V_TRANSLADO)
AS ENTRE_0_30
FROM (SELECT NVL(REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ), 'NO PROYECTO') AS V_TRANSLADO
  FROM OP_IN_RECIBO_DEPOSITO Y
  INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
  INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
  INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
 WHERE Y.IID_NUM_CLIENTE = 2905
 AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
   AND Y.I_SAL_CERO = 1
GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO
))) * 100
AS ENTRE_0_30,
V_TRANSLADO AS TIEMPOESTADIA
FROM (SELECT NVL(REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ), 'NO PROYECTO') AS V_TRANSLADO
  FROM OP_IN_RECIBO_DEPOSITO Y
  INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
  INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
  INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
 WHERE Y.IID_NUM_CLIENTE = 2905
 AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
   AND Y.I_SAL_CERO = 1
GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO
) GROUP BY V_TRANSLADO ";


           $stid = oci_parse($conn, $sql);
           oci_execute($stid);

           while (($row = oci_fetch_assoc($stid)) != false)
           {
             $res_array[]= $row;
           }

           #echo $sql;
           oci_free_statement($stid);
           oci_close($conn);

           #echo $sql;
           return $res_array;

}


public function tabla30(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'ALINK'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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

public function tabla60(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'BMW'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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

public function tabla90(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'DICASTAL'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


public function tabla120(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'FCA'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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

public function tabla150(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'FORD'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


public function tablaViva(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'HANDS'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


public function tablaHANKOOK(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'HANKOOK'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


public function tablaHONDA(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'HONDA'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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

public function tablaLINGLONG(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'LING-LONG'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


public function tablaLIUFENG(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'LIUFENG'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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

public function tablaSAAA(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
         AND REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) = 'SAAA'
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


public function tablaSinProyecto(){
  $conn = conexion::conectar();
  $res_array = array();

  $sql = "SELECT T.VID_RECIBO,
       Y.VID_CERTIFICADO,
       Z.V_DESCRIPCION,
       Y.D_PLAZO_DEP_INI,
       TRUNC(TO_DATE(TO_CHAR(SYSDATE,
                                                'DD/MM/YYYY hh24:mi:ss'),
                                                'DD/MM/YYYY hh24:mi:ss')) -
               TRUNC(TO_DATE(TO_CHAR(Y.D_PLAZO_DEP_INI,
                                      'DD/MM/YYYY hh24:mi:ss'),
                                      'DD/MM/YYYY hh24:mi:ss')) AS N_DIAS,
      (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
           NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0)) AS EXISTENCIAS_VIVAS,
       T.VID_FACTURA,
       REPLACE(VE.V_TRANSLADO, '/CUARENTENA', '' ) AS TRANSLADO,
       CASE WHEN (INSTR(VE.V_TRANSLADO,  '/CUARENTENA') ) > 0 THEN
            'CUARENTENA'
       ELSE
            ''
       END  AS TRANSLADO_CUARENTENA
        FROM OP_IN_RECIBO_DEPOSITO Y
        INNER JOIN OP_IN_RECIBO_DEPOSITO_DET Z ON Y.VID_RECIBO = Z.VID_RECIBO
        INNER JOIN OP_IN_MOVIMIENTOS T ON T.VID_RECIBO = Y.VID_RECIBO
        INNER JOIN OP_IN_VEHICULOS_RECIBIDOS VE ON VE.VID_RECIBO = Y.VID_RECIBO
       WHERE Y.IID_NUM_CLIENTE = 2905
       AND (NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'ERD' AND K.VID_FACTURA = T.VID_FACTURA), 0) -
                 NVL((SELECT SUM(K.C_CANTIDAD_UME) FROM OP_IN_MOVIMIENTOS K WHERE K.VID_RECIBO = T.VID_RECIBO AND K.V_TIPO_MOVTO = 'SOS' AND K.VID_FACTURA = T.VID_FACTURA), 0))> 0
         AND Y.I_SAL_CERO = 1
            AND (VE.V_TRANSLADO IS NULL OR VE.V_TRANSLADO = 'CUARENTENA')
      GROUP BY T.VID_RECIBO, Y.VID_CERTIFICADO, Z.V_DESCRIPCION, Y.D_PLAZO_DEP_INI, T.VID_FACTURA, VE.V_TRANSLADO";

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


}
?>
