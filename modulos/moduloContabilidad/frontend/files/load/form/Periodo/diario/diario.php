<?php
session_start();
if (isset($_SESSION['periodo'])) {
    $mesActual = $_SESSION['periodo']['mes'];
    $anio = $_SESSION['periodo']['anio'];
    echo "<script>
                var mesInicio = " . json_encode($mesActual) . ";
                var anioInicio = " . json_encode($anio) . ";
          </script>";
}
?>

<div class="card shadow mb-4" data-periodo-id="<?php echo $mesActual . $anio; ?>">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cierres Diarios</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresarperiodo">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmCierreDia" id="frmCierreDia">
            <label for="Numero">Día a cerrar:</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
                <input type="text" id="fechaCierreDia" name="fechaCierreDia" class="datepickerdia form-control">
            </div>

            <?php $periodoId = $_REQUEST['periodoId'] ?? 'defaultID';
                  echo "<input type='hidden' id='periodoId' name='periodoId' value='$periodoId'>"; 
            ?>

        </form>
        <button class="btn btn-danger" id="cerrarDia">
                    <i class="fas fa-lock"></i> Cerrar Día
                </button>

    </div>
</div>

<script>
$("#regresarperiodo").click(function() {
    // Obtener el valor de periodoId
    var periodoId = $(".card").data("periodo-id").charAt(0);
    console.log("periodoId:", periodoId); // Verificar que periodoId se está obteniendo correctamente

    // Realizar la petición AJAX
    $.ajax({
        url: "./load/adminPeriodo.php", // Asumiendo que este es el endpoint correcto
        type: "POST",
        data: {
            periodoId: periodoId // Envía periodoId como parte de los datos del cuerpo de la petición
        },
        success: function(response) {
            //console.log("Respuesta del servidor:", response); // Verificar la respuesta del servidor
            $("#render").html(response);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar la página: ", error);
        }
    });
});

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

$("#cerrarDia").click(function() {
    var fechaCierre = $("#fechaCierreDia").val();
    var periodoId = $("#periodoId").val();
    if (!fechaCierre) {
        Swal.fire({
            icon: 'warning',
            title: 'Fecha requerida',
            text: 'Por favor, es necesario seleccionar la fecha exacta para el cierre del dia.',
            confirmButtonText: 'Aceptar'
        });
        return;
    }
    
    $.ajax({
        url: "../../backend/Periodo/Cierre/cierreDiario.php", // Cambia esto al endpoint real donde se verifica el estado de las partidas
        type: "POST",
        data: { fechaCierre : fechaCierre, periodoId : periodoId },
        success: function(response) {
            if (response.todasCerradas) {
                Swal.fire({
                    icon: 'success',
                    title: 'Cierre Diario Completo',
                    text: 'Todas las partidas están cerradas.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cierre Diario Incompleto',
                    text: 'Todas las partidas diarias tienen que estar cerradas.',
                    confirmButtonText: 'Aceptar'
                });
             
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al verificar el estado de las partidas. Por favor, intenta de nuevo.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
});
</script>