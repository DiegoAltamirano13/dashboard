<?php
/**
* © Argo Almacenadora ®
* Fecha: 28/12/2018
* Developer: Jorge Tejeda J.
* Proyecto: Dashboard Talento Humano
* Version --
*/
include_once '../libs/conOra.php';
$iidconsecutivo = $_GET['iid_remate'];
$conn = conexion::conectar();
$res_array = array();
$sql = "SELECT T.V_FOTO_1, t.v_foto_2, t.v_foto_3, t.v_foto_4, t.v_foto_5, t.v_foto_6, t.v_foto_7, t.v_foto_8, t.v_foto_9, t.v_foto_10 FROM AD_CXC_REMATES_DESTRUCCION_IMG t WHERE t.iid_remate = $iidconsecutivo";;
 $stid = oci_parse($conn, $sql);
 oci_execute($stid);
 echo "<div id ='myCarousel' class='carousel slide' data-ride='carousel'>";
 echo "<div class='carousel-inner'>";
 $cont_slide = 0;
 while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
   $active = "";
   if ($cont_slide == 0) {
     $active = "active";
   }
   $valor = substr($row['V_FOTO_1'], -3, -1);

    echo "<div class='item $active'>";
    if ($valor == 'JP') {
      echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_1'], 0, -3)."JPG'/>";
    }
    else {
      echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_1'], 0, -3)."jpg'/>";
    }
    echo "</div>";
    $cont_slide++;

    if ($cont_slide == 0) {
      $active = "active";
    }else {
      $active = "";
    }

    if (!empty($row['V_FOTO_2'])) {
        $valor = substr($row['V_FOTO_2'], -3, -1);         
         echo "<div class='item $active'>";
         if ($valor == 'JP') {
           echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_2'], 0, -3)."JPG'/>";
         }
         else {
           echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_2'], 0, -3)."jpg'/>";
         }
         echo "</div>";
         $cont_slide++;
    }
    if (!empty($row['V_FOTO_3'])) {
       $valor = substr($row['V_FOTO_3'], -3, -1);
        echo "<div class='item $active'>";
        if ($valor == 'JP') {
          echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_3'], 0, -3)."JPG'/>";
        }
        else {
          echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_3'], 0, -3)."jpg'/>";
        }
        echo "</div>";
        $cont_slide++;
    }
    if (!empty($row['V_FOTO_4'])) {
        $valor = substr($row['V_FOTO_4'], -3, -1);
         echo "<div class='item $active'>";
         if ($valor == 'JP') {
           echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_4'], 0, -3)."JPG'/>";
         }
         else {
           echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_4'], 0, -3)."jpg'/>";
         }
         echo "</div>";
         $cont_slide++;
    }

    if (!empty($row['V_FOTO_5'])) {
       $valor = substr($row['V_FOTO_5'], -3, -1);
        echo "<div class='item $active'>";
        if ($valor == 'JP') {
          echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_5'], 0, -3)."JPG'/>";
        }
        else {
          echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_5'], 0, -3)."jpg'/>";
        }
        echo "</div>";
        $cont_slide++;
     }

     if (!empty($row['V_FOTO_6'])) {
          $valor = substr($row['V_FOTO_6'], -3, -1);
           echo "<div class='item $active'>";
           if ($valor == 'JP') {
             echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_6'], 0, -3)."JPG'/>";
           }
           else {
             echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_6'], 0, -3)."jpg'/>";
           }
           echo "</div>";
           $cont_slide++;
     }

     if (!empty($row['V_FOTO_7'])) {
         $valor = substr($row['V_FOTO_7'], -3, -1);
          echo "<div class='item $active'>";
          if ($valor == 'JP') {
            echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_7'], 0, -3)."JPG'/>";
          }
          else {
            echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_7'], 0, -3)."jpg'/>";
          }
          echo "</div>";
          $cont_slide++;

          $valor = substr($row['V_FOTO_8'], -3, -1);
           echo "<div class='item $active'>";
           if ($valor == 'JP') {
             echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_8'], 0, -3)."JPG'/>";
           }
           else {
             echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_8'], 0, -3)."jpg'/>";
           }
           echo "</div>";
           $cont_slide++;
      }

      if (!empty($row['V_FOTO_9'])) {
           $valor = substr($row['V_FOTO_9'], -3, -1);
            echo "<div class='item $active'>";
            if ($valor == 'JP') {
              echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_9'], 0, -3)."JPG'/>";
            }
            else {
              echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_9'], 0, -3)."jpg'/>";
            }
            echo "</div>";
            $cont_slide++;
      }

      if (!empty($row['V_FOTO_10'])) {
            $valor = substr($row['V_FOTO_10'], -3, -1);
             echo "<div class='item $active'>";
             if ($valor == 'JP') {
               echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_10'], 0, -3)."JPG'/>";
             }
             else {
               echo "<img class='d-block w-100' src='../imagenes_destruccion/".substr($row['V_FOTO_10'], 0, -3)."jpg'/>";
             }
             echo "</div>";
             $cont_slide++;
    }

 }
 echo "</div>";
 echo "<a class='left carousel-control' href='#myCarousel' data-slide='prev'>";
    echo "<span class='glyphicon glyphicon-chevron-left'></span>";
    echo "<span class='sr-only'>Previous</span>";
  echo "</a>";
  echo "<a class='right carousel-control' href='#myCarousel' data-slide='next'>";
    echo "<span class='glyphicon glyphicon-chevron-right'></span>";
    echo "<span class='sr-only'>Next</span>";
  echo "</a>";
 echo "</div>";

?>
