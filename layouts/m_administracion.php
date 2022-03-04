<li class="<?php if($active=="remates.php"||$active=="facturacion.php"){echo "active";}?> treeview">
  <a href="#">
    <i class="fa fa-folder-open"></i> <span>Administración</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!-- BOTTON ADMINISTRACION -->

    <!--TERMINA BOTTON ADMINISTRACION -->
    <!-- BOTTON REMATES -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '14'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="remates.php"){echo "active";}?>"><a class="click_modal_cargando" href="remates.php"><i class="fa fa-circle-o"></i> Remates </a></li>
    <?php } ?>

    <!-- BOTTON REGISTRO DE INFORMACION FINANCIERA -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '10'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="informacion_financiera.php"){echo "active";}?>"><a class="click_modal_cargando" href="informacion_financiera.php"><i class="fa fa-circle-o"></i> Registrar Inf. Financiera</a></li>
      <?php } ?>
    <!--TERMINA BOTTON REGISTRO DE INFORMACION FINANCIERA -->
    <!-- BOTTON TESORERÍA -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '11'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="tesoreria.php"){echo "active";}?>"><a class="click_modal_cargando" href="tesoreria.php"><i class="fa fa-circle-o"></i> Tesorería</a></li>
      <?php } ?>
    <!--TERMINA BOTTON TESORERÍA -->
    <!-- BOTTON PASIVOS -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '12'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="pasivos.php"){echo "active";}?>"><a class="click_modal_cargando" href="pasivos.php"><i class="fa fa-circle-o"></i> Pasivos Hipotecarios</a></li>
      <?php } ?>
    <!--TERMINA BOTTON PASIVOS -->
    <!-- BOTTON CAPs (Coberturas) -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '13'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="coberturas.php"){echo "active";}?>"><a class="click_modal_cargando" href="coberturas.php"><i class="fa fa-circle-o"></i> CAPs (Coberturas)</a></li>
      <?php } ?>
    <!--TERMINA BOTTON CAPs (Coberturas) -->
    <!-- BOTTON SALDOS DE CLIENTES -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '15'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="saldos_clientes.php"){echo "active";}?>"><a class="click_modal_cargando" href="saldos_clientes.php"><i class="fa fa-circle-o"></i> Saldos de Clientes</a></li>
      <?php } ?>
    <!--TERMINA BOTTON SALDOS DE CLIENTES -->
    <!--PRESUPUESTOS VS INGRESOS-->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '37'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="detalles_Ingresos.php"){echo "active";}?>"><a class="click_modal_cargando" href="detalles_Ingresos.php"><i class="fa fa-circle-o"></i> Presupuesto vs Ingresos</a></li>
    <?php } ?>
    <!--TERMINA BOTTON REMATES -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '46'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="facturacion_saldos.php"){echo "active";}?>"><a class="click_modal_cargando" href="facturacion_saldos.php"><i class="fa fa-circle-o"></i> Facturacion X Semana</a></li>
    <?php } ?>
    <!--TERMINA remates BONIFICACIONES -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '54'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="indicadores_por_bonificacion.php"){echo "active";}?>"><a class="click_modal_cargando" href="indicadores_por_bonificacion.php"><i class="fa fa-circle-o"></i> Indice de Emision de Notas de Credito</a></li>
    <?php } ?>
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '55'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="indicadores_por_error.php"){echo "active";}?>"><a class="click_modal_cargando" href="indicadores_por_error.php"><i class="fa fa-circle-o"></i> Indice de Cumplimiento en Facturación de Servicios</a></li>
    <?php } ?>
  </ul>
</li>
