<?php
   //$_COOKIE["valor"];
  // function envia_mail($valor_nombre){
          //$valor_nombre = $_POST["ejemplo"];
          $valor_nombre = $_GET["valor_nombre"];
          $num_cd = $_GET["numcd"];
          $fechas = $_GET["fechas_a"];
          $t_grano = $_GET["tipo_grano"];
          $finan = $_GET["financiera"];

           include_once '../libs/conOra.php';
           $conn = conexion::conectar();

          $res_array = array();

          $curs = oci_new_cursor($conn);
          $promotor = "ALL";
           $p_codes = "";
          //echo $pre.' '.$fecha.' '.$promotor.' '.$plaza;
          #$stid = oci_parse($conn, "begin PCK_DASHBOARD.enviar_mail_granos(:nombresilo, :p_code); end;");
          $stid = oci_parse($conn, "begin PCK_DASHBOARD.ENVIAR_MAIL_GRANOS(:silonombre, :cd_n , :fecha, :tipo_grano, :financiera,  :p_code); end;");
      		oci_bind_by_name($stid, ':silonombre', $valor_nombre); //3
      		oci_bind_by_name($stid, ':cd_n', $num_cd); //2019
      		oci_bind_by_name($stid, ':fecha', $fechas); // ALL
          oci_bind_by_name($stid, ':tipo_grano', $t_grano); // ALL
          oci_bind_by_name($stid, ':financiera', $finan); // ALL
           oci_bind_by_name($stid, ":p_code", $p_codes);
          oci_execute($stid);

          oci_free_statement($stid);
          oci_free_statement($curs);
          oci_close($conn);
          #echo $silonombre+ " "+ $cd_n + "  "+ $fecha;
          echo $p_codes;
  //  };
 ?>
