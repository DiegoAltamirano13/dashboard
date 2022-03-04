<li class="<?php if($active=="comercial.php" || $active=="comp_ingre_clientes.php" || $active=="venta_promotor.php"|| $active == "habilitaciones_bodega.php"){echo "active";}?> treeview">
  <a href="#">
    <i class="fa fa-briefcase"></i> <span>Comercial</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!-- BOTTON COMERCIAL -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '7'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="comercial.php"){echo "active";}?>"><a class="click_modal_cargando" href="comercial.php"><i class="fa fa-circle-o"></i> Prospectos</a></li>
      <?php }//cierra if para ver modulo activado ?>
    <!--TERMINA BOTTON COMERCIAL -->
    <!-- BOTTON COMPARATIVO DE INGRESOS DE CLIENTES -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '16'); if ($modulos_valida > 0){ ?>
      <li title="Comparativos ingresos de clientes" class="<?php if($active=="comp_ingre_clientes.php"){echo "active";}?>"><a class="click_modal_cargando" href="comp_ingre_clientes.php"><i class="fa fa-circle-o"></i> Compara Ingresos de clientes</a></li>
      <?php }//cierra if para ver modulo activado ?>
    <!--TERMINA BOTTON COMPARATIVO DE INGRESOS DE CLIENTES -->
    <!-- BOTTON FACTURADO VS PRESUPUESTO PROMOTORES -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '17'); if ($modulos_valida > 0){ ?>
      <li title="Facturado vs Presupuesto Promotores" class="<?php if($active=="venta_promotor.php"){echo "active";}?>"><a class="click_modal_cargando" href="venta_promotor.php"><i class="fa fa-circle-o"></i> Fac.VSPresupuesto Promo.</a></li>
      <?php }//cierra if para ver modulo activado ?>
      <!-- POR PLAZAS HABILITADAS -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '21'); if ($modulos_valida > 0){ ?>
      <li title="Ingresos" class="<?php if($active=="habilitaciones_bodega.php"){echo "active";}?>"><a class="click_modal_cargando" href="habilitaciones_bodega.php"><i class="fa fa-circle-o"></i> Ingresos.</a></li>
      <?php }//cierra if para ver modulo activado ?>
    <!--TERMINA BOTTON FACTURADO VS PRESUPUESTO PROMOTORES -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '44'); if ($modulos_valida > 0){ ?>
    <li title="Facturacion en clientes" class="<?php if($active=="informacionClientesPresupuesto.php"){echo "active";}?>"><a class="click_modal_cargando" href="informacionClientesPresupuesto.php"><i class="fa fa-circle-o"></i> Facturacion Clientes.</a></li>
    <?php }//cierra if para ver modulo activado ?>
    <!--TERMINA BOTTON FACTURADO VS PRESUPUESTO PROMOTORES -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '48'); if ($modulos_valida > 0){ ?>
    <li title="Encuestas Realizadas" class="<?php if($active=="encuestas_realizadas.php"){echo "active";}?>"><a class="click_modal_cargando" href="encuestas_realizadas.php"><i class="fa fa-circle-o"></i> Encuestas Realizadas.</a></li>
    <?php }//cierra if para ver modulo activado ?>

  </ul>
</li>
