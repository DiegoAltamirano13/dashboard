<?php

class filtros_operaciones {

  public function sql(){

    $conn = conexion::conectar();
    $res_array = array();

    $sql = " SELECT pla.iid_plaza, REPLACE(pla.v_razon_social, ' (ARGO)') AS plaza, pla.v_siglas FROM plaza pla WHERE pla.iid_plaza IN (3,4,5,6,7,8,17,18) ";

    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    while (($row = oci_fetch_assoc($stid)) != false){
      $res_array[]= $row;
    }

    oci_free_statement($stid);
    oci_close($conn);

    return $res_array;
  }

  public function mod_5_vehiculos_manufactura(){

    $parametro=$_SESSION['nomPlaza'];
    $select_manufac_global_plaza=array(0,0,0,0,0,0,0,0,0);

    if($parametro=="ALL"){
        $select_manufac_global_plaza = array(3,4,5,6,7,8,17,18,23);
      }else
      if($parametro=="CÓRDOBA"){
        $select_manufac_global_plaza[0]=3;
      }else
      if($parametro=="MÉXICO"){
        $select_manufac_global_plaza[1]=4;
      }else
      if($parametro=="GOLFO"){
        $select_manufac_global_plaza[2]=5;
      }else
      if($parametro=="PENINSULA"){
        $select_manufac_global_plaza[3]=6;
      }else
      if($parametro=="PUEBLA"){
        $select_manufac_global_plaza[4]=7;
      }else
      if($parametro=="BAJIO"){
        $select_manufac_global_plaza[5]=8;
      }else
      if($parametro=="OCCIDENTE"){
        $select_manufac_global_plaza[6]=17;
      }else
      if($parametro=="NORESTE"){
        $select_manufac_global_plaza[7]=18;
      }

      $_SESSION['select_manufac_global_plaza'] = $select_manufac_global_plaza;
      //$select_manufac_global_plaza = $_SESSION['select_manufac_global_plaza'];
      //return $select_manufac_global_plaza;
  }

  public function mod_30_operaciones_manufactura(){

    $plaza=$_SESSION['nomPlaza'];

    if($plaza=="CORPORATIVO"){
      $plaza="CÓRDOBA";
    }else {
      $plaza=$_SESSION['n_plaza'];
    }
  }

  public function mod_6_operaciones_veh_agro(){

      $plaza=$_SESSION['nomPlaza'];

      if($plaza=="CORPORATIVO" || $plaza=="CÓRDOBA"){
        $agro_plaza="CÓRDOBA (ARGO)";
      }elseif ($plaza=="OCCIDENTE") {
        $agro_plaza="OCCIDENTE (ARGO)";
      }elseif ($plaza=="ALL") {
        $agro_plaza="";
      }else {
        $agro_plaza=$_SESSION['nomPlaza'];
      }

      $_SESSION["agro_plaza"] = $agro_plaza;
    }

    public function mod_18_ubi_mercancia(){

        $plaza=$_SESSION['nomPlaza'];

        switch ($plaza) {
          case 'CORPORATIVO':
            $id_plaza=2;
            break;
            case 'CÓRDOBA':
            $id_plaza=3;
            break;
            case 'MÉXICO':
            $id_plaza=4;
            break;
            case 'GOLFO':
            $id_plaza=5;
            break;
            case 'PENINSULA':
            $id_plaza=6;
            break;
            case 'PUEBLA':
            $id_plaza=7;
            break;
            case 'BAJIO':
            $id_plaza=8;
            break;
            case 'OCCIDENTE':
            $id_plaza=17;
            break;
            case 'NORESTE':
            $id_plaza=18;
            break;
          default:
            $id_plaza=$_SESSION['i_plaza'];
            break;
        }

        $_SESSION["rackId_plaza"] = $id_plaza;
      }

}

 ?>
