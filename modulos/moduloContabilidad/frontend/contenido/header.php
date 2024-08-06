
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" id="topsearch" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>




        <div>
            <!-- BOton de  -->
            <button type="button" class="btn btn-warning mt-3" id="resetPeriodo"><i class="far fa-calendar-alt"></i>
                Cambiar periodo</button>

        </div>

        <div class="topbar-divider d-none d-sm-block"></div>


  <!-- Nav Item - User Information -->
  <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <!-- Mostrar nombre y apellido del usuario -->
                    <em>Bienvenido: <?php echo $_SESSION['usuario']; ?></em>
                </span>
                <img class="img-profile rounded-circle" src="../../../../lib/img/undraw_profile.svg">
            </a>

            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Perfil
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="login.php" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Cerrar Sesión
                </a>
            </div>
        </li>
    </ul>

            <!-- Modal Perfil -->
        <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Perfil de Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="profileForm" onsubmit="return false;">
                            <div class="form-group">
                                <label for="nombre_perfil">Nombre del Usuario</label>
                                <input type="text" class="form-control" id="nombre_perfil" value="<?php echo $_SESSION['usuario']; ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="email_perfil">Correo</label>
                                <input type="email" class="form-control" id="email_perfil" value="<?php echo $_SESSION['email']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="current_password_perfil">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current_password_perfil" required>
                            </div>
                            <div class="form-group">
                                <label for="clave_perfil">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="clave_perfil" required>
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="updateProfile()">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para Salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">Seleccionó "Cerrar Sesión", ¿Esta seguro?.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-danger" href="../../../../lib/config/cerrarSesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>
    

</nav>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateProfile() {
    const email = document.getElementById('email_perfil').value;
    const clave = document.getElementById('clave_perfil').value;
    const currentPassword = document.getElementById('current_password_perfil').value;

    if (email && clave && currentPassword) {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('clave', clave);
        formData.append('currentPassword', currentPassword);

        fetch('../../backend/usuarios/Profile/profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    // Limpiar los campos del formulario
                    document.getElementById('clave_perfil').value = '';
                    document.getElementById('current_password_perfil').value = '';
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Ocurrió un error al actualizar el perfil', 'error');
        });
    } else {
        Swal.fire('Error', 'Todos los campos son requeridos', 'error');
    }
}
</script>