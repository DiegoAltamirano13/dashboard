<li class="<?php if($active=="cartas_cupo.php"||$active=="pedimentos.php"){echo "active";}?> treeview">
  <a href="#">
    <i class="fa fa-ship"></i> <span>Comercio exterior</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!-- BOTTON CARTAS CUPO -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '8'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="cartas_cupo.php"){echo "active";}?>"><a class="click_modal_cargando" href="cartas_cupo.php"><i class="fa fa-circle-o"></i> Cartas cupo</a></li>
    <?php } ?>
    <!--TERMINA BOTTON CARTAS CUPO -->
    <!-- BOTTON PEDIMENTOS-->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '9'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="pedimentos.php"){echo "active";}?>"><a class="click_modal_cargando" href="pedimentos.php"><i class="fa fa-circle-o"></i> Pedimentos</a></li>
    <?php } ?>
    <!--TERMINA BOTTON PEDIMENTOS-->
    <!--CARTAS CUPO -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '24'); if ($modulos_valida > 0){ ?>
        <li class="<?php if($active=="cartas_cupo_anuales.php"){echo "active";}?>"><a class="click_modal_cargando" href="cartas_cupo_anuales.php"><i class="fa fa-circle-o"></i> Cartas Cupo Por Año</a></li>
      <?php } ?>
    <!--CARTAS CUPO -->
    <!--CARTAS CUPO -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '25'); if ($modulos_valida > 0){ ?>
        <li class="<?php if($active=="pedimentos_anuales.php"){echo "active";}?>"><a class="click_modal_cargando" href="pedimentos_anuales.php"><i class="fa fa-circle-o"></i> Pedimentos Por Año</a></li>
      <?php } ?>
    <!--CARTAS CUPO -->

    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '39'); if ($modulos_valida > 0){ ?>
    <li class="<?php if ($active == "cartas_cupo_anuales_crossdock.php"){echo "active";} ?>"><a class="click_modal_cargando" href="cartas_cupo_anuales_crossdock.php"><i class="fa fa-circle-o"></i> Cartas Cupo CrossDock </a></li>
    <?php } ?>

  </ul>
</li>
