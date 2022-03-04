<li class="<?php if($active=="sgc.php"||$active=="notificaciones.php"||$active=="upload_minuta.php"){echo "active";}?> treeview" title="SISTEMA DE GESTION DE CALIDAD">
  <a href="#">
    <i class="fa fa-line-chart"></i> <span>Sistema de Gestion de Calidad</span>
    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>
  <ul class="treeview-menu">
    <!-- BOTTON CONTROL DE SACP -->
    <?php $modulos_valida = Perfil::modulos_valida($iid_empleado, '56'); if ($modulos_valida > 0){ ?>
    <li class="<?php if($active=="sgc.php"||$active=="upload_minuta.php"){echo "active";}?>"><a class="click_modal_cargando" href="sgc.php"><i class="fa fa-circle-o"></i> SACP</a></li>
    <?php } ?>
  </ul>
</li>
