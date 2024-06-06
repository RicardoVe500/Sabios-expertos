<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Usuario</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresar">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmAddCatalogo" id="frmEditCatalogo">

            <div class="row">
                <div class="col-md-6">
                    <label for="Numero">Numero de la cuenta:</label>
                    <div class="input-group mb-3">
                    <input type="hidden" class="form-control" placeholder="Numero Cuenta" id="cuentaId"
                            name="cuentaId">

                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Numero Cuenta" id="editnumeroCuenta"
                            name="numeroCuenta" onkeypress="return isNumberKey(event)">
                    </div>
                    <label for="Numero">Nombre de la cuenta:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-font"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Nombre Cuenta" id="editnombreCuenta"
                            name="nombreCuenta">
                    </div>

                    <button type="button" class="btn btn-success mb-3" id="editarcatalogo">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
<script src="../lib/js/scripts/frmCatalogo.js"></script>
<script>

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
$(document).ready(function() {
        editarDatos()
    })
</script>




   
