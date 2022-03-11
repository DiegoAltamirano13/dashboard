<li class="<?php if ($active == "rack.php" || $active == "manufactura.php"||$active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?> treeview">
  <a href="#">
    <i class="fa fa-truck"></i> <span>Operaciones</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!--TERMINA BOTTON MANUFACTURA -->
    <!-- BOTTON AGRONEGOCIOS -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6, 29, 32, 30, 38, 40, 43'); if ($modulos_valida > 0){ ?>
    <li class="<?php if ($active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?>" style="<?=$visible ?>">
      <a href="#"><i class="fa fa-circle-o"></i> AGRONEGOCIOS
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '6'); if ($modulos_valida > 0){ ?>
        <li class="<?php if ($active == "agronegocios.php"){echo "active";} ?>"><a class="click_modal_cargando" href="agronegocios.php"><i class="fa fa-circle-o"></i> VEHÍCULOS AGRONEGOCIOS</a></li>
        <?php } ?>
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '53'); if ($modulos_valida > 0){ ?>
        <li class="<?php if ($active == "agronegocios_capbodega.php"){echo "active";} ?>"><a class="click_modal_cargando" href="agronegocios_capbodega.php"><i class="fa fa-circle-o"></i> CAPACIDAD DESCARGA/CARGA</a></li>
          <?php } ?>
        <!--aZUCAr-->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '29'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "detalles_azucar.php"){echo "active";} ?>"><a class="click_modal_cargando" href="detalles_azucar.php"><i class="fa fa-circle-o"></i> RESUMEN AZÚCAR</a></li>
        <?php } ?>
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '32'); if ($modulos_valida > 0){ ?>
        <li>
          <a href="#"><i class="fa fa-circle-o"></i> GRAFICAS AZUCAR
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '32'); if ($modulos_valida > 0){ ?>
              <li class="<?php if ($active == "azucar_x_anio.php"){echo "active";} ?>"><a class="click_modal_cargando" href="azucar_x_anio.php"><i class="fa fa-circle-o"></i> GRAFICAS DIR. GRAL.</a></li>
            <?php } ?>
            <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '32'); if ($modulos_valida > 0){ ?>
              <li class="<?php if ($active == "azucar_x_anio2.php"){echo "active";} ?>"><a class="click_modal_cargando" href="azucar_x_anio2.php"><i class="fa fa-circle-o"></i> GRAFICAS AZUCAR</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <!--granel-->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '30'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "detalles_granos.php"){echo "active";} ?>"><a class="click_modal_cargando" href="detalles_granos.php"><i class="fa fa-circle-o"></i> RESUMEN GRANOS</a></li>
        <?php } ?>
        <!--Ubicacion de embarques -->
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '38'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "mapas_operaciones.php"){echo "active";} ?>"><a class="click_modal_cargando" href="mapas_operaciones.php"><i class="fa fa-circle-o"></i> BUQUES</a></li>
        <?php } ?>
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '40'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "tabla_ocupacion.php"){echo "active";} ?>"><a class="click_modal_cargando" href="tabla_ocupacion.php"><i class="fa fa-circle-o"></i> M2 EN ALMACEN</a></li>
        <?php } ?>
        <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '43'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "temperatura_Granos.php"){echo "active";} ?>"><a class="click_modal_cargando" href="temperatura_Granos.php"><i class="fa fa-circle-o"></i> CALIDAD GRANOS</a></li>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>
    <!--Manufactura-->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '5, 27, 28, 18, 45'); if ($modulos_valida > 0){ ?>
    <li class="<?php if ($active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?>">
      <a href="#"><i class="fa fa-circle-o"></i> MANUFACTURA
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
        <ul class="treeview-menu">
          <!-- BOTTON MANUFACTURA -->
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '5'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "manufactura.php"){echo "active";} ?>"><a class="click_modal_cargando" href="manufactura.php"><i class="fa fa-circle-o"></i>  VEHÍCULOS MANUFACTURA</a></li>
          <?php } ?>
          <!--Boton Costos-->
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '27'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "Gastos_Maquinaria.php"){echo "active";} ?>"><a class="click_modal_cargando" href="Gastos_Maquinaria.php"><i class="fa fa-circle-o"></i>  COSTOS</a></li>
          <?php } ?>
          <!--  ocupacion -->
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '28'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "calculo_Ocupacion.php"){echo "active";} ?>"><a class="click_modal_cargando" href="calculo_Ocupacion.php"><i class="fa fa-circle-o"></i>  OCUPACIÓN DE ALMACEN</a></li>
          <?php } ?>
          <!-- BOTTON UBICACIÓN DE MERCANCÍA -->
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '18'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "rack.php"){echo "active";} ?>"><a class="click_modal_cargando" href="rack.php"><i class="fa fa-circle-o"></i>  UBICACIÓN DE MERCANCÍA</a></li>
          <?php } ?>
          <!-- INFORMACION VIAS -->
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '45'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "vias_Informacion.php"){echo "active";} ?>"><a class="click_modal_cargando" href="vias_Informacion.php"><i class="fa fa-circle-o"></i>  INFORMACION DE VIAS</a></li>
          <?php } ?>

        </ul>
      </a>
    </li>
    <?php } ?>

    <!--OPERACIONES ALO-->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '34,36,41,42,47,49');
          $modulos_valida2 = Perfil::modulos_valida($iid_empleado, '47'); if ($modulos_valida > 0){ ?>
    <li class="<?php if ($active == "agronegocios.php"||$active == "agronegocios_capbodega.php"){echo "active";} ?>" style="<?= $visible2 ?>">
      <a href="#"><i class="fa fa-circle-o"></i> OPERACIONES ALO
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
        <ul class="treeview-menu">

          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '34'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "piezas_danadas.php"){echo "active";} ?>"><a class="click_modal_cargando" href="piezas_danadas.php"><i class="fa fa-circle-o"></i> Mercancia No Conforme</a></li>
          <?php } ?>
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '36'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "errores_captura.php"){echo "active";} ?>"><a class="click_modal_cargando" href="errores_captura.php"><i class="fa fa-circle-o"></i> Errores Captura </a></li>
          <?php } ?>
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '41'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "operaciones_manufactura_alo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="operaciones_manufactura_alo.php"><i class="fa fa-circle-o"></i> Efectividad Carga y Descarga (ALO)</a></li>
          <?php } ?>
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '42'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "OcupacionClienteAlo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="OcupacionClienteAlo.php"><i class="fa fa-circle-o"></i> Tiempo De Mercancia En Almacen (ALO)</a></li>
          <?php } ?>
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '42'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "CalculoOcupacionAlo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="CalculoOcupacionAlo.php"><i class="fa fa-circle-o"></i>  OcupaciÓn De Almacen (ALO)</a></li>
          <?php } ?>
          <!-- REPORTE ALO -->
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '47'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "reporte_alo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="reporte_alo.php"><i class="fa fa-circle-o"></i>  REPORTE (ALO)</a></li>
          <?php } ?>
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '49'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "Contenedores_Pendientes_ALO.php"){echo "active";} ?>"><a class="click_modal_cargando" href="Contenedores_Pendientes_ALO.php"><i class="fa fa-circle-o"></i>  REPORTE CONTENEDORES PENDIENTES/ASIGNADOS (ALO)</a></li>
          <?php } ?>
          <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '51'); if ($modulos_valida > 0){ ?>
          <li class="<?php if ($active == "rackAlo.php"){echo "active";} ?>"><a class="click_modal_cargando" href="rackAlo.php"><i class="fa fa-circle-o"></i> REPORTE UBICACIONES (ALO)</a></li>
          <?php } ?>
        </ul>
      </a>
    </li>
    <?php } ?>


    <!--TERMINA BOTTON AGRONEGOCIOS -->
  </ul>
</li>
