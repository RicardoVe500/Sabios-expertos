<!-- HTML para ingresar usuario -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Usuario</h6>
    </div>
    <div class="card-body">
        <button class="btn btn-warning mb-3" id="regresar">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>
        <form name="frmAddUsuario" id="frmAddUsuario">
            <div class="row">
                <div class="col-md-6">
                    <label for="nombre">Nombres</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nombre" id="nombre">
                    </div>
                    <label for="apellidos">Apellidos</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="apellidos" id="apellidos">
                    </div>
                    <label for="email">Email</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    <label for="clave">Contraseña</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="clave" id="clave">
                    </div>
                    <!-- Campo de selección de rol -->
                    <label for="rol">Rol</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user-tag"></i></span>
                        </div>
                        <select class="form-control" name="rol" id="rol">
                            <option value="">Seleccione un rol</option>
                            <!-- Los roles se cargarán aquí -->
                        </select>
                    </div>
                    <button type="button" class="btn btn-success mb-3" id="guardarDatos">
                        <i class="fa fa-home"></i> Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="../lib/js/scripts/frmUsuarios.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('email').addEventListener('blur', function() {
    var email = this.value;
    var pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/; // Patrón básico para email

    if (email.match(pattern)) {
     
    } else {
        // Si el email no es válido, muestra un mensaje de error
        Swal.fire('Error', 'El email no es valido', 'error');
        this.value = ''; 
    }
});
</script>