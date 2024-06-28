<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Usuario</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresarSub">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmEditCatalogoSub" id="frmEditCatalogoSub">
        <input type="hidden" class="form-control" placeholder="id" id="cuentaId" name="cuentaId">
                    <input type="hidden" class="form-control" placeholder="numero Cuenta" id="numeroCuenta"
                        name="numeroCuenta">

            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" class="form-control" placeholder="empresaId" id="empresaId" name="empresaId">

                    <label for="Numero">Nombre de la cuenta:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-font"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Nombre Cuenta" id="editnombreCuenta"
                            name="nombreCuenta">
                    </div>

                    <label for="Nombrecuenta" style="margin-top: 10px;">Movimientos:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-scroll"></i></span>
                        </div>
                        <select class="form-control" id="selectsubcuentas" name="selectsubcuentas">
                        </select>
                    </div>

                    <button type="button" class="btn btn-success mb-3" id="editarsubcuentas">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>
    </div>


    </form>
</div>
</div>
<script src="../lib/js/scripts/frmSubcuentas.js"></script>
<script>
$("#regresarSub").click(function() {

    
    // Obtener el valor de cuentaId
    var numeroCuenta = $("#numeroCuenta").val().charAt(0);
    // Realizar la petición AJAX
    $.ajax({
        url: "./load/adminSubcuentas.php", // Asumiendo que este es el endpoint correcto
        type: "POST",
        data: {
            numeroCuenta: numeroCuenta // Envía cuentaId como parte de los datos del cuerpo de la petición
        },
        success: function(response) {
            $("#render").html(response);

        },
        error: function(xhr, status, error) {
            console.error("Error al cargar la página: ", error);
        }
    });
});
</script>