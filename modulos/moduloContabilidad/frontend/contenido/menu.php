<?php
// Asegúrate de iniciar sesión en la parte superior de tu archivo PHP
session_start();
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="app.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa fa-lightbulb"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Sabios y Expertos</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="#" id="principal">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Principal</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div>

    <!-- Configuración visible solo para Administradores -->
    <?php if ($_SESSION['nombreTipo'] == 'Administrador'): ?>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Configuración</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Mantenimientos</h6>
                <a class="collapse-item" href="#" id="roles">Roles</a>
                <a class="collapse-item" href="#" id="usuarios">Usuarios</a>
                <!-- <a class="collapse-item" href="#" id="empresas">Empresas</a>-->
                <a class="collapse-item" href="#" id="periodo">Cierres</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Contabilidad visible para Administradores y Contadores -->
    <?php if ($_SESSION['nombreTipo'] == 'Administrador' || $_SESSION['nombreTipo'] == 'Contador'): ?>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsetree"
            aria-expanded="true" aria-controls="collapsetree">
            <i class="fas fa-landmark"></i>
            <span>Contabilidad</span>
        </a>
        <div id="collapsetree" class="collapse" aria-labelledby="headingtree" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Contabilidad</h6>
                <a class="collapse-item" href="#" id="catalogo">Catalogo</a>
                <a class="collapse-item" href="#" id="tipopartida">Tipo Partida</a>
            </div>
        </div>
    </li>


    <!-- Reporteria visible solo para Administradores -->
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsefour"
            aria-expanded="true" aria-controls="collapsefour">
            <i class="fas fa-print"></i>
            <span>Reporteria</span>
        </a>
        <div id="collapsefour" class="collapse" aria-labelledby="headingtree" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Reportes</h6>
                <a class="collapse-item" href="#" id="bitacora">Reportes del sistema</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

</ul>
