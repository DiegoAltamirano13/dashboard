<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
class NominaPagada{
	/*====================== SQL DINAMICO ======================*/
  /**tablas de ingresos 1°**/
public function tabla_Ingresos($fecha, $almacen){
  $mesIni = substr($fecha, 0, 2);
  $anioIni = substr($fecha, 3,4);
  	#echo $fecha_re2."<br />";

  			$andPlaza = "";
        $andAlmacen = "";
        if ($almacen == '1') {
            $andAlmacen = '8';
        }elseif ($almacen == '2') {
          $andAlmacen = '6';
        }elseif ($almacen == '3') {
          $andAlmacen = '7';
        }elseif ($almacen == '4') {
          $andAlmacen = '5';
        }elseif ($almacen == '5') {
          $andAlmacen = '3';
        }elseif ($almacen == '6') {
          $andAlmacen = '4';
        }elseif ($almacen == '7') {
          $andAlmacen = '18';
        }elseif ($almacen == '8') {
          $andAlmacen = '17';
        }

    	$conn = conexion::conectar();
    	$res_array = array();
    	$sql = "  SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                 AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                 SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                 NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                    FROM AD_FA_REP_PRES_ALMACEN,
                    ALMACEN
                    WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN = ALMACEN.IID_ALMACEN
                    AND ALMACEN.IID_PLAZA IN($andAlmacen)
                    AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                    AND AD_FA_REP_PRES_ALMACEN.MES1 = ".$mesIni."), '0.00') AS PRESUPUESTO,
                 SUM(0) AS CANTIDADS
                      FROM AD_FA_REP_ING_FACT_SERV_BP,
                           PLAZA,
                           ALMACEN
                     WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                           ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                           ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                           ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni." )) AND
                           PLAZA.IID_PLAZA IN (".$andAlmacen.")
                  GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                  ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
    			$stid = oci_parse($conn, $sql);
    			oci_execute($stid);

          if ($almacen == 1 ) {
            #echo $sql;
          }
    			while (($row = oci_fetch_assoc($stid)) != false)
    			{
    				$res_array[]= $row;
    			}
    			oci_free_statement($stid);
    			oci_close($conn);
    			return $res_array;
  	}
  	/*====================== /*TABLA DE NOMINA PAGADA ======================*/
    /*================================tabla 2 ==============================*/
  /**tablas de ingresos 2**/
public function tabla_Ingresos2($fecha, $almacen){
      $mesIni = substr($fecha, 0, 2);
      $anioIni = substr($fecha, 3,4);


      			$andPlaza = "";

            if ($almacen == '1') {
                $headsql = " 'VICTORIA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1521,1692,1750';
                $andPlaza = " AND PLAZA.IID_PLAZA = 8 ";
            }elseif ($almacen == '2') {
              $headsql = " 'MAYAB' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1377';
              $andPlaza = " AND PLAZA.IID_PLAZA = 6";
            }elseif ($almacen == '3') {
              $headsql = " 'BRALEMEX' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1479';
              $andPlaza = " AND PLAZA.IID_PLAZA = 7";
            }elseif ($almacen == '4') {
              $headsql = " 'BRALEMEX II' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1734';
              $andPlaza = " AND PLAZA.IID_PLAZA = 7";
            }elseif ($almacen == '5') {
              $headsql = " 'AGUA AZUL' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1550';
              $andPlaza = " AND PLAZA.IID_PLAZA = 17";
            }elseif ($almacen == '6') {
              $headsql = " 'GONZALES GALLO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1746';
              $andPlaza = " AND PLAZA.IID_PLAZA = 17";
            }elseif ($almacen == '7') {
              $headsql = " 'ULUA VITA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '25,1098,1102';
              $andPlaza = '   AND PLAZA.IID_PLAZA = 5';
            }elseif ($almacen == '8') {
              $headsql = " 'ACACIAS' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1719';
              $andPlaza = " AND PLAZA.IID_PLAZA = 5";
            }elseif ($almacen == '9') {
              $headsql = " 'PENUELA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1209,1210,1223,1370';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '10') {
              $headsql = " 'ATOYAQUILLO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1660';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '11') {
              $headsql = " 'KENWORTH' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1679';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '12') {
              $headsql = " 'LA GLORIA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1468';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '13') {
              $headsql = " 'TABLA HONDA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1700';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '14') {
              $headsql = " 'PANTACO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1174,1444,1728,1741';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '15') {
              $headsql = " 'CEYLAN' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1568';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '16') {
              $headsql = " 'AD-HOC-MTY' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1738';
              $andPlaza = " AND PLAZA.IID_PLAZA = 18";
            }
            else {
                $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
            }


        	$conn = conexion::conectar();
        	$res_array = array();
        	$sql = "  SELECT ".$headsql."
                     AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                     SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                     NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                        FROM AD_FA_REP_PRES_ALMACEN
                        WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN IN (".$andAlmacen.")
                        AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                        AND AD_FA_REP_PRES_ALMACEN.MES1 = ".$mesIni."), '0.00') AS PRESUPUESTO,
                     SUM(0) AS CANTIDADS
                          FROM AD_FA_REP_ING_FACT_SERV_BP,
                               PLAZA,
                               ALMACEN
                         WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                               ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                               ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                               ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni." )) AND
                               AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                      GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                      ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
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
        /**TABLAS DE INGRESOS 3 **/
public function tabla_Ingresos3($fecha, $almacen){
      $mesIni = substr($fecha, 0, 2);
      $anioIni = substr($fecha, 3,4);
          #echo $fecha_re;
            #echo $fecha_re2."<br />";

                $andPlaza = "";
                //$andAlmacen = '1521,1692,1750,1377,1479,1734,1550,1746,25,1098,1102,1719,1209,1210,1223,1370,1660,1679,1468,1700,1174,1444,1728,1741,1568,1738';
                if ($almacen == '1') {
                    $headsql = " 'QUERETARO' AS V_RAZON_SOCIAL,";
                    $andAlmacen = '1387,1724';
                    $andPlaza = " AND  PLAZA.IID_PLAZA  = 8";
                }elseif ($almacen == '2') {
                  $headsql = " 'MERIDA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1142,1706,1136,1316,1554,1705,1140, 1142';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 6";
                }elseif ($almacen == '3') {
                  $headsql = " 'PUEBLA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1004';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  =7";
                }elseif ($almacen == '4') {
                  $headsql = " 'VERACRUZ' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1722,1754,43,1336,1562';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 5";
                }elseif ($almacen == '5') {
                  $headsql = " 'CORDOBA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1701,1506,1657,1735,1736,1737,1749,17,1102';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 3";
                }elseif ($almacen == '6') {
                  $headsql = " 'MEXICO' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1475,1686,1723';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 4";
                }elseif ($almacen == '7') {
                  $headsql = " 'MONTERREY' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1665, 1685';
                  $andPlaza = " AND PLAZA.IID_PLAZA = 18";
                }elseif ($almacen == '8') {
                  $headsql = " 'OCCIDENTE' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1128,1393,1436,1480,1497,1608,1740,1745';
                  $andPlaza = " AND PLAZA.IID_PLAZA = 17";
                }
                else {
                    $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
                }


              $conn = conexion::conectar();
              $res_array = array();
              $sql = "  SELECT ".$headsql."
                         AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                         SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                         (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                            FROM AD_FA_REP_PRES_ALMACEN
                            WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN IN (".$andAlmacen.")
                            AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                            AND AD_FA_REP_PRES_ALMACEN.MES1 = ".$mesIni."), '0.00')) AS PRESUPUESTO,
                         SUM(0) AS CANTIDADS
                              FROM AD_FA_REP_ING_FACT_SERV_BP,
                                   PLAZA,
                                   ALMACEN
                             WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                                   ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                                   ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                                   ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni." )) AND
                                   AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                          GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                          ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
                  $stid = oci_parse($conn, $sql);
                  oci_execute($stid);
                #	echo $sql;
                if ($almacen == 8) {
                  #echo $sql;
                }
                  while (($row = oci_fetch_assoc($stid)) != false)
                  {
                    $res_array[]= $row;
                  }
                  oci_free_statement($stid);
                  oci_close($conn);
                  return $res_array;
            }
 /**tablas de ingresos mes anterior**/

public function tabla_Ingresos4($fecha, $almacen){
  $mesIni = substr($fecha, 0, 2);
  $anioIni = substr($fecha, 3,4);

  if ($mesIni == '01') {
    $mesIni4 = '12';
    $anioIni = IntVal($anioIni)-1;
  }else {
      $mesIni4 = intVal($mesIni)-1;
  }
  #echo $anioIni."</br>".$mesIni4."</br>";
                 $andPlaza = "";

                 $andPlaza = "";

                 $andPlaza = "";
                 $andAlmacen = "";
                 if ($almacen == '1') {
                     $andAlmacen = '8';
                 }elseif ($almacen == '2') {
                   $andAlmacen = '6';
                 }elseif ($almacen == '3') {
                   $andAlmacen = '7';
                 }elseif ($almacen == '4') {
                   $andAlmacen = '5';
                 }elseif ($almacen == '5') {
                   $andAlmacen = '3';
                 }elseif ($almacen == '6') {
                   $andAlmacen = '4';
                 }elseif ($almacen == '7') {
                   $andAlmacen = '18';
                 }elseif ($almacen == '8') {
                   $andAlmacen = '17';
                 }


               $conn = conexion::conectar();
               $res_array = array();
               $sql = "  SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                           AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                           SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                           (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                              FROM AD_FA_REP_PRES_ALMACEN,
                              ALMACEN
                                WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN = ALMACEN.IID_ALMACEN
                                AND ALMACEN.IID_PLAZA IN($andAlmacen)
                              AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                              AND AD_FA_REP_PRES_ALMACEN.MES1 = ".$mesIni4."), '0.00')) AS PRESUPUESTO,
                           SUM(0) AS CANTIDADS
                                FROM AD_FA_REP_ING_FACT_SERV_BP,
                                     PLAZA,
                                     ALMACEN
                               WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                                     ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                                     ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                                     ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni4." )) AND
                                     PLAZA.IID_PLAZA IN (".$andAlmacen.")
                            GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                            ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
                   $stid = oci_parse($conn, $sql);
                   oci_execute($stid);
                   if ($almacen == 2 ) {
                     #echo $sql;
                   }

                   while (($row = oci_fetch_assoc($stid)) != false)
                   {
                     $res_array[]= $row;
                   }
                   oci_free_statement($stid);
                   oci_close($conn);
                   return $res_array;
             }
  /**tablas de ingresos mes anterior**/
public function tabla_Ingresos5($fecha, $almacen){
        $mesIni = substr($fecha, 0, 2);
        $anioIni = substr($fecha, 3,4);

        $mesIni = substr($fecha, 0, 2);
        $anioIni = substr($fecha, 3,4);

        if ($mesIni == '01') {
          $mesIni4 = '12';
          $anioIni = IntVal($anioIni)-1;
        }else {
            $mesIni4 = intVal($mesIni)-1;
        }



        if ($almacen == '1') {
            $headsql = " 'VICTORIA' AS V_RAZON_SOCIAL,";
            $andAlmacen = '1521,1692,1750';
            $andPlaza = " AND PLAZA.IID_PLAZA = 8 ";
        }elseif ($almacen == '2') {
          $headsql = " 'MAYAB' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1377';
          $andPlaza = " AND PLAZA.IID_PLAZA = 6";
        }elseif ($almacen == '3') {
          $headsql = " 'BRALEMEX' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1479';
          $andPlaza = " AND PLAZA.IID_PLAZA = 7";
        }elseif ($almacen == '4') {
          $headsql = " 'BRALEMEX II' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1734';
          $andPlaza = " AND PLAZA.IID_PLAZA = 7";
        }elseif ($almacen == '5') {
          $headsql = " 'AGUA AZUL' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1550';
          $andPlaza = " AND PLAZA.IID_PLAZA = 17";
        }elseif ($almacen == '6') {
          $headsql = " 'GONZALES GALLO' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1746';
          $andPlaza = " AND PLAZA.IID_PLAZA = 17";
        }elseif ($almacen == '7') {
          $headsql = " 'ULUA VITA' AS V_RAZON_SOCIAL,";
          $andAlmacen = '25,1098,1102';
          $andPlaza = '   AND PLAZA.IID_PLAZA = 5';
        }elseif ($almacen == '8') {
          $headsql = " 'ACACIAS' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1719';
          $andPlaza = " AND PLAZA.IID_PLAZA = 5";
        }elseif ($almacen == '9') {
          $headsql = " 'PENUELA' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1209,1210,1223,1370';
          $andPlaza = " AND PLAZA.IID_PLAZA = 3";
        }elseif ($almacen == '10') {
          $headsql = " 'ATOYAQUILLO' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1660';
          $andPlaza = " AND PLAZA.IID_PLAZA = 3";
        }elseif ($almacen == '11') {
          $headsql = " 'KENWORTH' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1679';
          $andPlaza = " AND PLAZA.IID_PLAZA = 3";
        }elseif ($almacen == '12') {
          $headsql = " 'LA GLORIA' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1468';
          $andPlaza = " AND PLAZA.IID_PLAZA = 3";
        }elseif ($almacen == '13') {
          $headsql = " 'TABLA HONDA' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1700';
          $andPlaza = " AND PLAZA.IID_PLAZA = 4";
        }elseif ($almacen == '14') {
          $headsql = " 'PANTACO' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1174,1444,1728,1741';
          $andPlaza = " AND PLAZA.IID_PLAZA = 4";
        }elseif ($almacen == '15') {
          $headsql = " 'CEYLAN' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1568';
          $andPlaza = " AND PLAZA.IID_PLAZA = 4";
        }elseif ($almacen == '16') {
          $headsql = " 'AD-HOC-MTY' AS V_RAZON_SOCIAL,";
          $andAlmacen = '1738';
          $andPlaza = " AND PLAZA.IID_PLAZA = 18";
        }else {
          $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
        }


          	$conn = conexion::conectar();
          	$res_array = array();
          	$sql = "  SELECT ".$headsql."
                       AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                       SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                       (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                              FROM AD_FA_REP_PRES_ALMACEN
                              WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN IN (".$andAlmacen.")
                              AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                              AND AD_FA_REP_PRES_ALMACEN.MES1 = ".$mesIni4."), '0.00')) AS PRESUPUESTO,
                       SUM(0) AS CANTIDADS
                            FROM AD_FA_REP_ING_FACT_SERV_BP,
                                 PLAZA,
                                 ALMACEN
                           WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                                 ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                                 ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                                 ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni4." )) AND
                                 AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                        GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                        ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
          			$stid = oci_parse($conn, $sql);
          			oci_execute($stid);
          		#	echo $sql;
          			while (($row = oci_fetch_assoc($stid)) != false)
          			{
          				$res_array[]= $row;
          			}
          			oci_free_statement($stid);
          			oci_close($conn);
          			return $res_array;
        	}
          /**TABLAS DE INGRESOS 3 **/
public function tabla_Ingresos6($fecha, $almacen){
                $mesIni = substr($fecha, 0, 2);
                $anioIni = substr($fecha, 3,4);
                    #echo $fecha_re;
                      #echo $fecha_re2."<br />";

                      $mesIni = substr($fecha, 0, 2);
                      $anioIni = substr($fecha, 3,4);

                      $mesIni = substr($fecha, 0, 2);
                      $anioIni = substr($fecha, 3,4);

                      if ($mesIni == '01') {
                        $mesIni4 = '12';
                        $anioIni = IntVal($anioIni)-1;
                      }else {
                          $mesIni4 = intVal($mesIni)-1;
                      }
                      $filtroUnico = "";
                          $andPlaza = "";

                          if ($almacen == '1') {
                              $headsql = " 'QUERETARO' AS V_RAZON_SOCIAL,";
                              $andAlmacen = '1387,1724';
                              $andPlaza = " AND  PLAZA.IID_PLAZA  = 8";
                          }elseif ($almacen == '2') {
                            $headsql = " 'MERIDA' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1142,1706,1136,1316,1554,1705, 1142';
                            $andPlaza = " AND  PLAZA.IID_PLAZA  = 6";
                          }elseif ($almacen == '3') {
                            $headsql = " 'PUEBLA' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1004';
                            $andPlaza = " AND  PLAZA.IID_PLAZA  =7";
                          }elseif ($almacen == '4') {
                            $headsql = " 'VERACRUZ' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1722,1754,43,1336,1562';
                            $andPlaza = " AND  PLAZA.IID_PLAZA  = 5";
                          }elseif ($almacen == '5') {
                            $headsql = " 'CORDOBA' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1701,1506,1657,1735,1736,1737,1749,17,1102';
                            $andPlaza = " AND  PLAZA.IID_PLAZA  = 3";
                          }elseif ($almacen == '6') {
                            $headsql = " 'MEXICO' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1475,1686,1723';
                            $andPlaza = " AND  PLAZA.IID_PLAZA  = 4";
                          }elseif ($almacen == '7') {
                            $headsql = " 'MONTERREY' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1665, 1685';
                            $andPlaza = " AND PLAZA.IID_PLAZA = 18";
                          }elseif ($almacen == '8') {
                            $headsql = " 'OCCIDENTE' AS V_RAZON_SOCIAL,";
                            $andAlmacen = '1128,1393,1436,1480,1497,1608,1740,1745';
                            $andPlaza = " AND PLAZA.IID_PLAZA = 17";
                          }
                          else {
                              $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
                          }


                        $conn = conexion::conectar();
                        $res_array = array();
                        $sql = "  SELECT ".$headsql."
                                   AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                                   SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                                   (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                                    FROM AD_FA_REP_PRES_ALMACEN
                                    WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN IN (".$andAlmacen.")
                                    AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                                    AND AD_FA_REP_PRES_ALMACEN.MES1 = ".$mesIni4."), '0.00')) AS PRESUPUESTO,
                                   SUM(0) AS CANTIDADS
                                        FROM AD_FA_REP_ING_FACT_SERV_BP,
                                             PLAZA,
                                             ALMACEN
                                       WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                                             ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                                             ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                                             ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni4." )) AND
                                             (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$filtroUnico." ".$andPlaza.")
                                    GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                                    ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
                            $stid = oci_parse($conn, $sql);
                            oci_execute($stid);
                          #
                          if ($almacen == "2") {
                            #echo $sql;
                          }
                            while (($row = oci_fetch_assoc($stid)) != false)
                            {
                              $res_array[]= $row;
                            }
                            oci_free_statement($stid);
                            oci_close($conn);
                            return $res_array;
}
           /**tablas de ingresos mes anterior**/
/*ACUMULADOS */
public function tabla_Ingresos7($fecha, $almacen){
  $mesIni = substr($fecha, 0, 2);
  $anioIni = substr($fecha, 3,4);
  	#echo $fecha_re2."<br />";

  			$andPlaza = "";

        if ($almacen == '1') {
            $andAlmacen = '8';
        }elseif ($almacen == '2') {
          $andAlmacen = '6';
        }elseif ($almacen == '3') {
          $andAlmacen = '7';
        }elseif ($almacen == '4') {
          $andAlmacen = '5';
        }elseif ($almacen == '5') {
          $andAlmacen = '3';
        }elseif ($almacen == '6') {
          $andAlmacen = '4';
        }elseif ($almacen == '7') {
          $andAlmacen = '18';
        }elseif ($almacen == '8') {
          $andAlmacen = '17';
        }


    	$conn = conexion::conectar();
    	$res_array = array();
    	$sql = "  SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                 AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                 SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                 (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                    FROM AD_FA_REP_PRES_ALMACEN,
                    ALMACEN
                     WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN = ALMACEN.IID_ALMACEN
                     AND ALMACEN.IID_PLAZA IN($andAlmacen)                    
                    AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                    AND AD_FA_REP_PRES_ALMACEN.MES1 <= ".$mesIni."), '0.00')) AS PRESUPUESTO,
                 SUM(0) AS CANTIDADS
                      FROM AD_FA_REP_ING_FACT_SERV_BP,
                           PLAZA,
                           ALMACEN
                     WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                           ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                           ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                           ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES <= ".$mesIni." )) AND
                           PLAZA.IID_PLAZA IN (".$andAlmacen.")".$andPlaza."
                  GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                  ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
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
  	/*====================== /*TABLA DE NOMINA PAGADA ======================*/
    /*================================tabla 2 ==============================*/
  /**tablas de ingresos 2**/
public function tabla_Ingresos8($fecha, $almacen){
      $mesIni = substr($fecha, 0, 2);
      $anioIni = substr($fecha, 3,4);


      			$andPlaza = "";

            if ($almacen == '1') {
                $headsql = " 'VICTORIA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1521,1692,1750';
                $andPlaza = " AND PLAZA.IID_PLAZA = 8 ";
            }elseif ($almacen == '2') {
              $headsql = " 'MAYAB' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1377';
              $andPlaza = " AND PLAZA.IID_PLAZA = 6";
            }elseif ($almacen == '3') {
              $headsql = " 'BRALEMEX' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1479';
              $andPlaza = " AND PLAZA.IID_PLAZA = 7";
            }elseif ($almacen == '4') {
              $headsql = " 'BRALEMEX II' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1734';
              $andPlaza = " AND PLAZA.IID_PLAZA = 7";
            }elseif ($almacen == '5') {
              $headsql = " 'AGUA AZUL' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1550';
              $andPlaza = " AND PLAZA.IID_PLAZA = 17";
            }elseif ($almacen == '6') {
              $headsql = " 'GONZALES GALLO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1746';
              $andPlaza = " AND PLAZA.IID_PLAZA = 17";
            }elseif ($almacen == '7') {
              $headsql = " 'ULUA VITA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '25,1098,1102';
              $andPlaza = '   AND PLAZA.IID_PLAZA = 5';
            }elseif ($almacen == '8') {
              $headsql = " 'ACACIAS' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1719';
              $andPlaza = " AND PLAZA.IID_PLAZA = 5";
            }elseif ($almacen == '9') {
              $headsql = " 'PENUELA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1209,1210,1223,1370';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '10') {
              $headsql = " 'ATOYAQUILLO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1660';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '11') {
              $headsql = " 'KENWORTH' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1679';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '12') {
              $headsql = " 'LA GLORIA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1468';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '13') {
              $headsql = " 'TABLA HONDA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1700';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '14') {
              $headsql = " 'PANTACO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1174,1444,1728,1741';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '15') {
              $headsql = " 'CEYLAN' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1568';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '16') {
              $headsql = " 'AD-HOC-MTY' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1738';
              $andPlaza = " AND PLAZA.IID_PLAZA = 18";
            }else {
              $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
            }


        	$conn = conexion::conectar();
        	$res_array = array();
        	$sql = "  SELECT ".$headsql."
                     AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                     SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                     (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                        FROM AD_FA_REP_PRES_ALMACEN
                        WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN IN (".$andAlmacen.")
                        AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                        AND AD_FA_REP_PRES_ALMACEN.MES1 <= ".$mesIni."), '0.00')) AS PRESUPUESTO,
                     SUM(0) AS CANTIDADS
                          FROM AD_FA_REP_ING_FACT_SERV_BP,
                               PLAZA,
                               ALMACEN
                         WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                               ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                               ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                               ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES <= ".$mesIni." )) AND
                               AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                      GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                      ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
        			$stid = oci_parse($conn, $sql);
        			oci_execute($stid);
        		#	echo $sql;
        			while (($row = oci_fetch_assoc($stid)) != false)
        			{
        				$res_array[]= $row;
        			}
        			oci_free_statement($stid);
        			oci_close($conn);
        			return $res_array;
      	}
        /**TABLAS DE INGRESOS 3 **/
public function tabla_Ingresos9($fecha, $almacen){
      $mesIni = substr($fecha, 0, 2);
      $anioIni = substr($fecha, 3,4);
          #echo $fecha_re;
            #echo $fecha_re2."<br />";

                $andPlaza = "";

                if ($almacen == '1') {
                    $headsql = " 'QUERETARO' AS V_RAZON_SOCIAL,";
                    $andAlmacen = '1387,1724';
                    $andPlaza = " AND  PLAZA.IID_PLAZA  = 8";
                }elseif ($almacen == '2') {
                  $headsql = " 'MERIDA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1142,1706,1136,1316,1554,1705, 1142';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 6";
                }elseif ($almacen == '3') {
                  $headsql = " 'PUEBLA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1004';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  =7";
                }elseif ($almacen == '4') {
                  $headsql = " 'VERACRUZ' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1722,1754,43,1336,1562';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 5";
                }elseif ($almacen == '5') {
                  $headsql = " 'CORDOBA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1701,1506,1657,1735,1736,1737,1749,17,1102';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 3";
                }elseif ($almacen == '6') {
                  $headsql = " 'MEXICO' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1475,1686,1723';
                  $andPlaza = " AND  PLAZA.IID_PLAZA  = 4";
                }elseif ($almacen == '7') {
                  $headsql = " 'MONTERREY' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1665, 1685';
                  $andPlaza = " AND PLAZA.IID_PLAZA = 18";
                }elseif ($almacen == '8') {
                  $headsql = " 'OCCIDENTE' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1128,1393,1436,1480,1497,1608,1740,1745';
                  $andPlaza = " AND PLAZA.IID_PLAZA = 17";
                }
                else {
                    $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
                }



              $conn = conexion::conectar();
              $res_array = array();
              $sql = "  SELECT ".$headsql."
                         AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                         SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT,
                         (NVL((SELECT SUM(AD_FA_REP_PRES_ALMACEN.PRESUPUESTO) AS PRESUPUESTO
                            FROM AD_FA_REP_PRES_ALMACEN
                            WHERE AD_FA_REP_PRES_ALMACEN.IID_ALMACEN IN (".$andAlmacen.")
                            AND AD_FA_REP_PRES_ALMACEN.ANIO = ".$anioIni."
                            AND AD_FA_REP_PRES_ALMACEN.MES1 <= ".$mesIni."), '0.00')) AS PRESUPUESTO,
                         SUM(0) AS CANTIDADS
                              FROM AD_FA_REP_ING_FACT_SERV_BP,
                                   PLAZA,
                                   ALMACEN
                             WHERE ( AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA ) and
                                   ( AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN ) and
                                   ( ( AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni." ) AND
                                   ( AD_FA_REP_ING_FACT_SERV_BP.IID_MES <= ".$mesIni." )) AND
                                   AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                          GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                          ORDER BY AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA ASC";
                  $stid = oci_parse($conn, $sql);
                  oci_execute($stid);
                #	echo $sql;
                  while (($row = oci_fetch_assoc($stid)) != false)
                  {
                    $res_array[]= $row;
                  }
                  oci_free_statement($stid);
                  oci_close($conn);
                  return $res_array;
            }
 /**tablas de ingresos mes anterior**/

public function tabla_Ingresos10($fecha, $almacen){
   $mesIni = substr($fecha, 0, 2);
   $anioIni = substr($fecha, 3,4);
   	#echo $fecha_re2."<br />";

    $anioIni2 = IntVal($anioIni)- 1;
   			$andPlaza = "";

        $andAlmacen = "";
        if ($almacen == '1') {
            $andAlmacen = '8';
        }elseif ($almacen == '2') {
          $andAlmacen = '6';
        }elseif ($almacen == '3') {
          $andAlmacen = '7';
        }elseif ($almacen == '4') {
          $andAlmacen = '5';
        }elseif ($almacen == '5') {
          $andAlmacen = '3';
        }elseif ($almacen == '6') {
          $andAlmacen = '4';
        }elseif ($almacen == '7') {
          $andAlmacen = '18';
        }elseif ($almacen == '8') {
          $andAlmacen = '17';
        }


     	$conn = conexion::conectar();
     	$res_array = array();
     	$sql = " SELECT T.V_RAZON_SOCIAL, SUM(ANIO_ANTERIOR) AS ANIO_ANTERIOR, SUM(TOTAL_FACT) AS TOTAL_FACT FROM (SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                     AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                     SUM(0) AS ANIO_ANTERIOR,
                     SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT
                FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
               WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                 and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                 and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                     (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni."))
                 AND PLAZA.IID_PLAZA IN (".$andAlmacen.")
              GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
              UNION ALL
              SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                     AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                     SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS ANIO_ANTERIOR,
                     SUM(0) AS TOTAL_FACT
                FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
               WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                 and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                 and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni2.") AND
                     (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni." ))
                 AND PLAZA.IID_PLAZA IN (".$andAlmacen.")
               GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA) T
              GROUP BY T.V_RAZON_SOCIAL";
     			$stid = oci_parse($conn, $sql);
     			oci_execute($stid);

          if ($almacen == 2) {
          #  echo $sql;
          }

     			while (($row = oci_fetch_assoc($stid)) != false)
     			{
     				$res_array[]= $row;
     			}
     			oci_free_statement($stid);
     			oci_close($conn);
     			return $res_array;
   	}
   	/*====================== /*TABLA DE NOMINA PAGADA ======================*/
     /*================================tabla 2 ==============================*/
   /**tablas de ingresos 2**/
public function tabla_Ingresos11($fecha, $almacen){
       $mesIni = substr($fecha, 0, 2);
       $anioIni = substr($fecha, 3,4);
       $anioIni2 = IntVal($anioIni)- 1;

       			$andPlaza = "";

            if ($almacen == '1') {
                $headsql = " 'VICTORIA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1521,1692,1750';
                $andPlaza = " AND PLAZA.IID_PLAZA = 8 ";
            }elseif ($almacen == '2') {
              $headsql = " 'MAYAB' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1377';
              $andPlaza = " AND PLAZA.IID_PLAZA = 6";
            }elseif ($almacen == '3') {
              $headsql = " 'BRALEMEX' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1479';
              $andPlaza = " AND PLAZA.IID_PLAZA = 7";
            }elseif ($almacen == '4') {
              $headsql = " 'BRALEMEX II' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1734';
              $andPlaza = " AND PLAZA.IID_PLAZA = 7";
            }elseif ($almacen == '5') {
              $headsql = " 'AGUA AZUL' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1550';
              $andPlaza = " AND PLAZA.IID_PLAZA = 17";
            }elseif ($almacen == '6') {
              $headsql = " 'GONZALES GALLO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1746';
              $andPlaza = " AND PLAZA.IID_PLAZA = 17";
            }elseif ($almacen == '7') {
              $headsql = " 'ULUA VITA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '25,1098,1102';
              $andPlaza = '   AND PLAZA.IID_PLAZA = 5';
            }elseif ($almacen == '8') {
              $headsql = " 'ACACIAS' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1719';
              $andPlaza = " AND PLAZA.IID_PLAZA = 5";
            }elseif ($almacen == '9') {
              $headsql = " 'PENUELA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1209,1210,1223,1370';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '10') {
              $headsql = " 'ATOYAQUILLO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1660';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '11') {
              $headsql = " 'KENWORTH' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1679';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '12') {
              $headsql = " 'LA GLORIA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1468';
              $andPlaza = " AND PLAZA.IID_PLAZA = 3";
            }elseif ($almacen == '13') {
              $headsql = " 'TABLA HONDA' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1700';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '14') {
              $headsql = " 'PANTACO' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1174,1444,1728,1741';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '15') {
              $headsql = " 'CEYLAN' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1568';
              $andPlaza = " AND PLAZA.IID_PLAZA = 4";
            }elseif ($almacen == '16') {
              $headsql = " 'AD-HOC-MTY' AS V_RAZON_SOCIAL,";
              $andAlmacen = '1738';
              $andPlaza = " AND PLAZA.IID_PLAZA = 18";
            }else {
              $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
            }



         	$conn = conexion::conectar();
         	$res_array = array();
         	$sql = "SELECT T.V_RAZON_SOCIAL, SUM(ANIO_ANTERIOR) AS ANIO_ANTERIOR, SUM(TOTAL_FACT) AS TOTAL_FACT FROM
                  (SELECT ".$headsql."
                         AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                         SUM(0) AS ANIO_ANTERIOR,
                         SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT
                    FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                   WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                     and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                     and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                         (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni."))
                     AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                  GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                  UNION ALL
                  SELECT ".$headsql."
                         AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                         SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS ANIO_ANTERIOR,
                         SUM(0) AS TOTAL_FACT
                    FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                   WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                     and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                     and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni2.") AND
                         (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni." ))
                     AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.") ".$andPlaza."
                   GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA) T
                  GROUP BY T.V_RAZON_SOCIAL";
         			$stid = oci_parse($conn, $sql);
         			oci_execute($stid);
         		#	echo $sql;

         			while (($row = oci_fetch_assoc($stid)) != false)
         			{
         				$res_array[]= $row;
         			}
         			oci_free_statement($stid);
         			oci_close($conn);
         			return $res_array;
       	}
         /**TABLAS DE INGRESOS 3 **/
public function tabla_Ingresos12($fecha, $almacen){
       $mesIni = substr($fecha, 0, 2);
       $anioIni = substr($fecha, 3,4);
       $anioIni2 = IntVal($anioIni)- 1;
           #echo $fecha_re;
             #echo $fecha_re2."<br />";

                 $andPlaza = "";

                 if ($almacen == '1') {
                     $headsql = " 'QUERETARO' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1387,1724';
                     $andPlaza = " AND  PLAZA.IID_PLAZA  = 8";
                 }elseif ($almacen == '2') {
                   $headsql = " 'MERIDA' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1142,1706,1136,1316,1554,1705, 1142';
                   $andPlaza = " AND  PLAZA.IID_PLAZA  = 6";
                 }elseif ($almacen == '3') {
                   $headsql = " 'PUEBLA' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1004';
                   $andPlaza = " AND  PLAZA.IID_PLAZA  =7";
                 }elseif ($almacen == '4') {
                   $headsql = " 'VERACRUZ' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1722,1754,43,1336,1562';
                   $andPlaza = " AND  PLAZA.IID_PLAZA  = 5";
                 }elseif ($almacen == '5') {
                   $headsql = " 'CORDOBA' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1701,1506,1657,1735,1736,1737,1749,17,1102';
                   $andPlaza = " AND  PLAZA.IID_PLAZA  = 3";
                 }elseif ($almacen == '6') {
                   $headsql = " 'MEXICO' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1475,1686,1723';
                   $andPlaza = " AND  PLAZA.IID_PLAZA  = 4";
                 }elseif ($almacen == '7') {
                   $headsql = " 'MONTERREY' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1665, 1685';
                   $andPlaza = " AND PLAZA.IID_PLAZA = 18";
                 }elseif ($almacen == '8') {
                   $headsql = " 'OCCIDENTE' AS V_RAZON_SOCIAL,";
                   $andAlmacen = '1128,1393,1436,1480,1497,1608,1740,1745';
                   $andPlaza = " AND PLAZA.IID_PLAZA = 17";
                 }
                 else {
                     $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
                 }


               $conn = conexion::conectar();
               $res_array = array();
               $sql = "SELECT T.V_RAZON_SOCIAL, SUM(ANIO_ANTERIOR) AS ANIO_ANTERIOR, SUM(TOTAL_FACT) AS TOTAL_FACT FROM
                       (SELECT ".$headsql."
                              AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                              SUM(0) AS ANIO_ANTERIOR,
                              SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT
                         FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                        WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                          and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                          and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                              (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni."))
                          AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                       GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                       UNION ALL
                       SELECT ".$headsql."
                              AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                              SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS ANIO_ANTERIOR,
                              SUM(0) AS TOTAL_FACT
                         FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                        WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                          and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                          and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni2.") AND
                              (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni." ))
                          AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                        GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA) T
                       GROUP BY T.V_RAZON_SOCIAL";
                   $stid = oci_parse($conn, $sql);
                   oci_execute($stid);
                 #	echo $sql;
                   while (($row = oci_fetch_assoc($stid)) != false)
                   {
                     $res_array[]= $row;
                   }
                   oci_free_statement($stid);
                   oci_close($conn);
                   return $res_array;
             }
  /**tablas de ingresos mes anterior**/

public function tabla_Ingresos13($fecha, $almacen){
     $mesIni = substr($fecha, 0, 2);
     $anioIni = substr($fecha, 3,4);
     	#echo $fecha_re2."<br />";
      if ($mesIni == '01') {
        $mesIni2 = '12';
        $anioIni = IntVal($anioIni)-1;
      }else {
          $mesIni2 = intVal($mesIni)-1;
      }

     			$andPlaza = "";

          $andAlmacen = "";
          if ($almacen == '1') {
              $andAlmacen = '8';
          }elseif ($almacen == '2') {
            $andAlmacen = '6';
          }elseif ($almacen == '3') {
            $andAlmacen = '7';
          }elseif ($almacen == '4') {
            $andAlmacen = '5';
          }elseif ($almacen == '5') {
            $andAlmacen = '3';
          }elseif ($almacen == '6') {
            $andAlmacen = '4';
          }elseif ($almacen == '7') {
            $andAlmacen = '18';
          }elseif ($almacen == '8') {
            $andAlmacen = '17';
          }

       	$conn = conexion::conectar();
       	$res_array = array();
       	$sql = " SELECT T.V_RAZON_SOCIAL, SUM(ANIO_ANTERIOR) AS ANIO_ANTERIOR, SUM(TOTAL_FACT) AS TOTAL_FACT FROM (SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                       AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                       SUM(0) AS ANIO_ANTERIOR,
                       SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT
                  FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                 WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                   and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                   and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                       (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni."))
                   AND PLAZA.IID_PLAZA IN (".$andAlmacen.")
                GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                UNION ALL
                SELECT PLAZA.V_RAZON_SOCIAL AS V_RAZON_SOCIAL,
                       AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                       SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS ANIO_ANTERIOR,
                       SUM(0) AS TOTAL_FACT
                  FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                 WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                   and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                   and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                       (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni2." ))
                   AND PLAZA.IID_PLAZA IN (".$andAlmacen.")
                 GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA) T
                GROUP BY T.V_RAZON_SOCIAL";
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
     	/*====================== /*TABLA DE NOMINA PAGADA ======================*/
       /*================================tabla 2 ==============================*/
     /**tablas de ingresos 2**/
public function tabla_Ingresos14($fecha, $almacen){
         $mesIni = substr($fecha, 0, 2);
         $anioIni = substr($fecha, 3,4);
         if ($mesIni == '01') {
           $mesIni2 = '12';
           $anioIni = IntVal($anioIni)-1;
         }else {
             $mesIni2 = intVal($mesIni)-1;
         }

         			$andPlaza = "";

              if ($almacen == '1') {
                  $headsql = " 'VICTORIA' AS V_RAZON_SOCIAL,";
                  $andAlmacen = '1521,1692,1750';
                  $andPlaza = " AND PLAZA.IID_PLAZA = 8 ";
              }elseif ($almacen == '2') {
                $headsql = " 'MAYAB' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1377';
                $andPlaza = " AND PLAZA.IID_PLAZA = 6";
              }elseif ($almacen == '3') {
                $headsql = " 'BRALEMEX' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1479';
                $andPlaza = " AND PLAZA.IID_PLAZA = 7";
              }elseif ($almacen == '4') {
                $headsql = " 'BRALEMEX II' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1734';
                $andPlaza = " AND PLAZA.IID_PLAZA = 7";
              }elseif ($almacen == '5') {
                $headsql = " 'AGUA AZUL' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1550';
                $andPlaza = " AND PLAZA.IID_PLAZA = 17";
              }elseif ($almacen == '6') {
                $headsql = " 'GONZALES GALLO' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1746';
                $andPlaza = " AND PLAZA.IID_PLAZA = 17";
              }elseif ($almacen == '7') {
                $headsql = " 'ULUA VITA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '25,1098,1102';
                $andPlaza = '   AND PLAZA.IID_PLAZA = 5';
              }elseif ($almacen == '8') {
                $headsql = " 'ACACIAS' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1719';
                $andPlaza = " AND PLAZA.IID_PLAZA = 5";
              }elseif ($almacen == '9') {
                $headsql = " 'PENUELA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1209,1210,1223,1370';
                $andPlaza = " AND PLAZA.IID_PLAZA = 3";
              }elseif ($almacen == '10') {
                $headsql = " 'ATOYAQUILLO' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1660';
                $andPlaza = " AND PLAZA.IID_PLAZA = 3";
              }elseif ($almacen == '11') {
                $headsql = " 'KENWORTH' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1679';
                $andPlaza = " AND PLAZA.IID_PLAZA = 3";
              }elseif ($almacen == '12') {
                $headsql = " 'LA GLORIA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1468';
                $andPlaza = " AND PLAZA.IID_PLAZA = 3";
              }elseif ($almacen == '13') {
                $headsql = " 'TABLA HONDA' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1700';
                $andPlaza = " AND PLAZA.IID_PLAZA = 4";
              }elseif ($almacen == '14') {
                $headsql = " 'PANTACO' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1174,1444,1728,1741';
                $andPlaza = " AND PLAZA.IID_PLAZA = 4";
              }elseif ($almacen == '15') {
                $headsql = " 'CEYLAN' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1568';
                $andPlaza = " AND PLAZA.IID_PLAZA = 4";
              }elseif ($almacen == '16') {
                $headsql = " 'AD-HOC-MTY' AS V_RAZON_SOCIAL,";
                $andAlmacen = '1738';
                $andPlaza = " AND PLAZA.IID_PLAZA = 18";
              }else {
                $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
              }


           	$conn = conexion::conectar();
           	$res_array = array();
           	$sql = "SELECT T.V_RAZON_SOCIAL, SUM(ANIO_ANTERIOR) AS ANIO_ANTERIOR, SUM(TOTAL_FACT) AS TOTAL_FACT FROM
                    (SELECT ".$headsql."
                           AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                           SUM(0) AS ANIO_ANTERIOR,
                           SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT
                      FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                     WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                       and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                       and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                           (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni."))
                       AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.") ".$andPlaza."
                    GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                    UNION ALL
                    SELECT ".$headsql."
                           AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                           SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS ANIO_ANTERIOR,
                           SUM(0) AS TOTAL_FACT
                      FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                     WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                       and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                       and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                           (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni2." ))
                       AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.") ".$andPlaza."
                     GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA) T
                    GROUP BY T.V_RAZON_SOCIAL";
           			$stid = oci_parse($conn, $sql);
           			oci_execute($stid);
           		#	echo $sql;
           			while (($row = oci_fetch_assoc($stid)) != false)
           			{
           				$res_array[]= $row;
           			}
           			oci_free_statement($stid);
           			oci_close($conn);
           			return $res_array;
         	}
           /**TABLAS DE INGRESOS 3 **/
public function tabla_Ingresos15($fecha, $almacen){
         $mesIni = substr($fecha, 0, 2);
         $anioIni = substr($fecha, 3,4);
         if ($mesIni == '01') {
           $mesIni2 = '12';
           $anioIni = IntVal($anioIni)-1;
         }else {
             $mesIni2 = intVal($mesIni)-1;
         }
             #echo $fecha_re;
               #echo $fecha_re2."<br />";

                   $andPlaza = "";

                   if ($almacen == '1') {
                       $headsql = " 'QUERETARO' AS V_RAZON_SOCIAL,";
                       $andAlmacen = '1387,1724';
                       $andPlaza = " AND  PLAZA.IID_PLAZA  = 8";
                   }elseif ($almacen == '2') {
                     $headsql = " 'MERIDA' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1142,1706,1136,1316,1554,1705';
                     $andPlaza = " AND  PLAZA.IID_PLAZA  = 6";
                   }elseif ($almacen == '3') {
                     $headsql = " 'PUEBLA' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1004';
                     $andPlaza = " AND  PLAZA.IID_PLAZA  =7";
                   }elseif ($almacen == '4') {
                     $headsql = " 'VERACRUZ' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1722,1754,43,1336,1562';
                     $andPlaza = " AND  PLAZA.IID_PLAZA  = 5";
                   }elseif ($almacen == '5') {
                     $headsql = " 'CORDOBA' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1701,1506,1657,1735,1736,1737,1749,17,1102';
                     $andPlaza = " AND  PLAZA.IID_PLAZA  = 3";
                   }elseif ($almacen == '6') {
                     $headsql = " 'MEXICO' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1475,1686,1723';
                     $andPlaza = " AND  PLAZA.IID_PLAZA  = 4";
                   }elseif ($almacen == '7') {
                     $headsql = " 'MONTERREY' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1665, 1685';
                     $andPlaza = " AND PLAZA.IID_PLAZA = 18";
                   }elseif ($almacen == '8') {
                     $headsql = " 'OCCIDENTE' AS V_RAZON_SOCIAL,";
                     $andAlmacen = '1128,1393,1436,1480,1497,1608,1740,1745';
                     $andPlaza = " AND PLAZA.IID_PLAZA = 17";
                   }
                   else {
                       $andAlmacen = 'and t.iid_almacen in ('.$almacen.')';
                   }



                 $conn = conexion::conectar();
                 $res_array = array();
                 $sql = "SELECT T.V_RAZON_SOCIAL, SUM(ANIO_ANTERIOR) AS ANIO_ANTERIOR, SUM(TOTAL_FACT) AS TOTAL_FACT FROM
                         (SELECT ".$headsql."
                                AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                                SUM(0) AS ANIO_ANTERIOR,
                                SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS TOTAL_FACT
                           FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                          WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                            and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                            and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                                (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni."))
                            AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                         GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA
                         UNION ALL
                         SELECT ".$headsql."
                                AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA,
                                SUM(AD_FA_REP_ING_FACT_SERV_BP.C_TOTAL) AS ANIO_ANTERIOR,
                                SUM(0) AS TOTAL_FACT
                           FROM AD_FA_REP_ING_FACT_SERV_BP, PLAZA, ALMACEN
                          WHERE (AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA = PLAZA.IID_PLAZA)
                            and (AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN = ALMACEN.IID_ALMACEN)
                            and ((AD_FA_REP_ING_FACT_SERV_BP.IID_YEAR = ".$anioIni.") AND
                                (AD_FA_REP_ING_FACT_SERV_BP.IID_MES = ".$mesIni2." ))
                            AND AD_FA_REP_ING_FACT_SERV_BP.IID_ALMACEN IN (".$andAlmacen.")".$andPlaza."
                          GROUP BY PLAZA.V_RAZON_SOCIAL, AD_FA_REP_ING_FACT_SERV_BP.IID_PLAZA) T
                         GROUP BY T.V_RAZON_SOCIAL";
                     $stid = oci_parse($conn, $sql);
                     oci_execute($stid);
                   #	echo $sql;
                     while (($row = oci_fetch_assoc($stid)) != false)
                     {
                       $res_array[]= $row;
                     }
                     oci_free_statement($stid);
                     oci_close($conn);
                     return $res_array;
               }
    /**tablas de ingresos mes anterior**/

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
				$sql = "SELECT TO_CHAR(ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -0), 'MM/YYYY') mes1 FROM DUAL";
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
		$sql = "SELECT IID_ALMACEN, V_NOMBRE FROM ALMACEN WHERE IID_PLAZA = $in_plaza AND IID_ALMACEN NOT IN (9998, 9999) ORDER BY IID_ALMACEN";
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
function nombreTipo($tipo){
		$conn = conexion::conectar();
		$res_array = array();
		if ($tipo == 87 ) {
			$sql = "select v_scuenta as cuenta, UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in(87) and v_sscuenta = 1";
		}
		else {
			$sql = "select v_scuenta as cuenta,UPPER(v_descripcion) as DESCRIPCION from CT_CG_CAT_CUENTAS WHERE v_cuenta = 5105 and v_scuenta in($tipo) and v_sscuenta = 0";
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

	function validateDate($date, $format = 'd/m/Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}


}
