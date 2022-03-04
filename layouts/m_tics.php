<li class="<?php if($active=="tic.php"||$active=="notificaciones.php"||$active=="upload_minuta.php"){echo "active";}?> treeview" title="TECNOLOGÍAS DE LA INFORMACIÓN">
  <a href="#">
    <i class="fa fa-laptop"></i> <span>Tecnologías De la Información</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!-- BOTTON CONTROL DE PROYECTOS -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '1'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="tic.php"||$active=="upload_minuta.php"){echo "active";}?>"><a class="click_modal_cargando" href="tic.php"><i class="fa fa-circle-o"></i> Control de Proyectos</a></li>
    <?php } ?>
  </ul>
  <ul class="treeview-menu" style="display:none;">
    <!-- BOTTON CONTROL DE PROYECTOS -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '50'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="Mttos_Tics.php"||$active=="Mttos_Tics.php"){echo "active";}?>"><a class="click_modal_cargando" href="Mttos_Tics.php"><i class="fa fa-circle-o"></i> Control de Mttos</a></li>
    <?php } ?>
  </ul>
</li>
