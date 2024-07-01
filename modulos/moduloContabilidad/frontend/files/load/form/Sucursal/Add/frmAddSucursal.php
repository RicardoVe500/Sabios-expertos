<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Sucursal</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresar">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmAddEmpresa" id="frmAddSucursal">
        <?php
            $empresaId = $_REQUEST['empresaId'] ?? 'defaultID';
            echo "<input type='text' id='empresaId' name='empresaId' value='$empresaId'>";
        ?>
            <div class="row">
                <div class="col-md-6">
                    <label for="nombreEmpresa">Nombre de la empresa:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-font"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Nombre Sucursal" id="nombreSucursal"
                            name="nombreSucursal">
                    </div>

                    <label for="Numero">Direccion:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Direccion de la Sucursal" id="direccion"
                            name="direccion">
                    </div>

                    <label for="Numero">Correo Electronico:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Correo de la Sucursal" id="correo"
                            name="correo">
                    </div>

                    <label for="Numero">Telefono:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Telefono de la Sucursal" id="telefono"
                            name="telefono">
                    </div>
                    
                </div>
            </div>

        </form>

        <button type="button" class="btn btn-success mb-3" id="guardarSucursal">
                        <i class="fa fa-plus"></i> Agregar
                    </button>
    </div>
</div>

<script src="../lib/js/scripts/frmSucursal.js"></script>