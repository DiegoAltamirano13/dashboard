<li class="<?php if($active=="rotacion_personal.php" || $active=="nomina_pagada.php" ){echo "active";}?> treeview">
  <a href="#">
    <i class="fa fa-users"></i> <span>Talento Humano</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!-- BOTTON LISTA PERSONAL -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="lista_Personal.php"){echo "active";}?>"><a class="click_modal_cargando" href="lista_Personal.php"><i class="fa fa-circle-o"></i> Lista de Personal</a>
    </li>
    <?php } ?>
      <!-- BOTTON ROTACION DE PERSONAL -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="rotacion_personal.php"){echo "active";}?>"><a class="click_modal_cargando" href="rotacion_personal.php"><i class="fa fa-circle-o"></i> Rotación de Personal(Quincenal)</a>
    </li>
    <?php } ?>
    <!--TERMINA BOTTON ROTACION DE PERSONAL -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '19'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="rotacion_personal_semana_quincena.php"){echo "active";}?>"><a class="click_modal_cargando" href="rotacion_personal_semana_quincena.php"><i class="fa fa-circle-o"></i> Rotación de Personal (Semanal)</a>
    </li>
    <?php } ?>
    <!--INICIA BOTTON ROTACION DE PERSONAL DETALLE GENERAL-->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '57'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="rotacion_personal_gral.php"){echo "active";}?>"><a class="click_modal_cargando" href="rotacion_personal_gral.php"><i class="fa fa-circle-o"></i> Rotación de Personal (Detalle Gral.)</a>
    </li>
    <?php } ?>
    <!-- BOTTON OMINA PAGADA -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '20'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="nomina_pagada.php"){echo "active";}?>"><a class="click_modal_cargando" href="nomina_pagada.php"><i class="fa fa-circle-o"></i> Nomina Pagada</a></li>
    <?php } ?>
    <!--TERMINA BOTTON OMINA PAGADA -->
    <!-- Faltas -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '22'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="dias_descansados.php"){echo "active";}?>"><a class="click_modal_cargando" href="dias_descansados.php"><i class="fa fa-circle-o"></i> Faltas Por Ausentismo/Prestaciones</a></li>
    <?php } ?>
      <!-- Faltas -->
      <!--tiempo extra -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '23'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="tiempo_extra.php"){echo "active";}?>"><a class="click_modal_cargando" href="tiempo_extra.php"><i class="fa fa-circle-o"></i> Tiempo extra</a></li>
      <?php } ?>
      <!--tiempo extra -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '23'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="riesgo.php"){echo "active";}?>"><a class="click_modal_cargando" href="riesgo.php"><i class="fa fa-circle-o"></i> Prima de riesgo</a></li>
      <?php } ?>
      <!-- cursos -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '31'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="rh_cat_cursos.php"){echo "active";}?>"><a class="click_modal_cargando" href="rh_cat_cursos.php"><i class="fa fa-circle-o"></i> Capacitación</a></li>
      <?php } ?>
      <!-- Finiquitos -->
      <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '52'); if ($modulos_valida > 0){ ?>
      <li class="<?php if($active=="finiquitos_RH.php"){echo "active";}?>"><a class="click_modal_cargando" href="finiquitos_RH.php"><i class="fa fa-circle-o"></i> Finiquitos</a></li>
      <?php } ?>
  </ul>
</li>
