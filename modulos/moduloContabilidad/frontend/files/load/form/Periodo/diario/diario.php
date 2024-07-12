<?php
session_start();
if (isset($_SESSION['periodo'])) {

$mesActual = $_SESSION['periodo']['mes'];
$anio = $_SESSION['periodo']['anio'];
echo "<script>
            var mesInicio = $mesActual;
            var anioInicio = $anio;
          </script>";
}  
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cierres Diarios</h6>
    </div>
    <div class="card-body">

        <form name="frmCierreDia" id="frmCierreDia">
            <label for="Numero">Dia a cerrar:</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
                <input type="text" id="fechaCierreDia" name="fechaCierreDia" class="datepickerdia form-control">
            </div>
        </form>

        <button class="btn btn-success mb-3" id="cierrediario">
            <i class="fa fa-plus"></i> Agregar Tipo Partida
        </button>

    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar el datepicker
    $('.datepickerdia').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        language: 'es',
        todayHighlight: true
    });
    // Validación para restringir las fechas contables
    if (typeof mesInicio !== 'undefined' && typeof anioInicio !== 'undefined') {
        var firstDay = new Date(anioInicio, mesInicio - 1, 1); // Ajuste de mes (0-indexed)
        var lastDay = new Date(anioInicio, mesInicio, 0); // Ajuste de mes (0-indexed)

        console.log('First Day:', firstDay.toISOString().split('T')[0]);
        console.log('Last Day:', lastDay.toISOString().split('T')[0]);

        var firstDayStr = firstDay.toISOString().split('T')[0];
        var lastDayStr = lastDay.toISOString().split('T')[0];

        $('.datepickerdia').datepicker('setStartDate', firstDayStr);
        $('.datepickerdia').datepicker('setEndDate', lastDayStr);
    } else {
        console.log("Variables de sesión no definidas.");
    }

});

$("#cierrediario").click(function() {
    let url = "la consulta en la que te va hacer el insert"
    $.ajax({
                type: "POST",
                url: url,
                data: $("#frmCierreDia").serialize(),
                success: function(data){
                    Swal.fire({
                        icon: 'success',
                        title: '¡TDIa cerrada!',
                        text: 'El Tipo de partida se agrego exitosamente.',
                    }); 

                    //$("#render").load("./load/adminTipoPartidas.php"); 

                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al crear',
                        text: 'No se pudo crear el tipo partida. Por favor, intenta de nuevo.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
});
</script>