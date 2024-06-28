<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Catalogo</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresar">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmEditEmpresa" id="frmEditEmpresa">
        <input type="hidden" class="form-control" placeholder="empresaId" id="empresaId"
        name="empresaId">
            <div class="row">
                <div class="col-md-6">
                    <label for="nombreEmpresa">Nombre de la empresa:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-font"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Nombre Empresa" id="nombreEmpresa"
                            name="nombreEmpresa">
                    </div>

                    <label for="Numero">Direccion:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Direccion de la empresa" id="direccion"
                            name="direccion">
                    </div>

                    <label for="Numero">Correo Electronico:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Correo de la empresa" id="correo"
                            name="correo">
                    </div>

                    <label for="Numero">Telefono:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Telefono de la empresa" id="telefono"
                            name="telefono">
                    </div>
                    
                </div>
            </div>

        </form>

        <button type="button" class="btn btn-success mb-3" id="editarEmpresa">
                        <i class="fa fa-plus"></i> Guardar
                    </button>
    </div>
</div>

<script src="../lib/js/scripts/frmEmpresas.js"></script>