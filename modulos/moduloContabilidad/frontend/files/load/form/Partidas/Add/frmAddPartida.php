<?php
session_start();
if (isset($_SESSION['periodo'])) {
    $mes = $_SESSION['periodo']['mes'];
    $anio = $_SESSION['periodo']['anio'];
    echo "<script>
            var mesInicio = $mes - 1; // Ajuste porque JavaScript cuenta los meses desde 0
            var anioInicio = $anio;
          </script>";
         
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Partidas</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresarpartida">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmAddPartida" id="frmAddPartida">

            <div class="row">
                <div class="col-md-6">
                    <?php
                    $tipoPartidaId = $_REQUEST['tipoPartidaId'] ?? 'defaultID';
                    echo "<input type='hidden' id='tipoPartidaId' name='tipoPartidaId' value='$tipoPartidaId'>";
                    ?>
                    <label for="Numero">Fecha Contable:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" id="fechacontable" name="fechacontable" class="form-control">


                    </div>

                    <label for="Numero">Concepto:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Concepto" id="concepto" name="concepto">
                    </div>

                    <button type="button" class="btn btn-success mb-3" id="crearpartida">
                        <i class="fa fa-plus"></i> Crear
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="../lib/js/scripts/frmPartidas.js"></script>
<script>
$(document).ready(function() {

    //validacion para poder restringir las fechas contables y no pasen de la fecha de trabajo establecida 
    if (typeof mesInicio !== 'undefined' && typeof anioInicio !== 'undefined') {
        var firstDay = new Date(anioInicio, mesInicio, 1);
        var lastDay = new Date(anioInicio, mesInicio + 1, 0);

        console.log('First Day:', firstDay.toISOString().split('T')[0]);
        console.log('Last Day:', lastDay.toISOString().split('T')[0]);

        var firstDayStr = firstDay.toISOString().split('T')[0];
        var lastDayStr = lastDay.toISOString().split('T')[0];

        var fechaContable = document.getElementById('fechacontable');
        fechaContable.min = firstDayStr;
        fechaContable.max = lastDayStr;
    } else {
        console.log("Variables de sesión no definidas.");
    }


    //enableEnterKeySubmission("#frmAddTipoPartida", guardarTipoPartida);

    $("#regresarpartida").click(function() {
        var tipoPartidaId = $("#tipoPartidaId").val();
        $.ajax({
            url: "./load/adminPartidas.php",
            type: "POST",
            data: {
                tipoPartidaId: tipoPartidaId
            },
            success: function(response) {
                $("#render").html(response);

            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al mostrar',
                    text: 'No se pudo cargar el contenido Por favor, intenta de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });

    })
})
</script>