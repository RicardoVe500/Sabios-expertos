<div class="card shadow mb-4 ">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Editar Usuario</h6>
        </div>
        <div class="card-body">
            <button class="btn btn-warning mb-3" id="regresar">
                <i class="fa fa-arrow-left"></i> Regresar
            </button>
            <form name="frmEditUsuario" id="frmEditUsuario">
                <input type="hidden" name="userId" id="userId">
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
                        <label for="password">Nueva Contraseña</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control" name="clave" id="clave">
                        </div>
                        <button type="button" class="btn btn-success mb-3" id="guardarEdit">
                            <i class="fa fa-home"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
                        
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script src="../lib/js/scripts/frmEditUsuarios.js"></script>
                        