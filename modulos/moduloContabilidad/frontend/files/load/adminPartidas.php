<?php
session_start();
$tipoPartidaId = $_REQUEST['tipoPartidaId'] ?? 'defaultID';
echo "<input type='hidden' id='tipoPartidaId' value='$tipoPartidaId'>";

if (isset($_SESSION['periodo'])) {
    $estadoId = $_SESSION['periodo']['estadoId'];

    echo "<script>
            var estadoId = $estadoId; 
          </script>";
         
}
?>
 
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Partida</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-success mb-3" id="frmAddPartidas">
            <i class="fa fa-plus"></i> Agregar Partida
        </button>
<!--
        <button class="btn btn-success mb-3" id="reporpar">
            <i class="fa fa-plus"></i> reporte
        </button>
-->
        <button class="btn btn-warning mb-3" id="regresarTipos">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <button class="btn btn-secondary float-right btn-sm" id="balance" title="Balance">
            <i class="fas fa-print"></i></button>

            <button class="btn btn-secondary float-right btn-sm" id="mayor" title="mayor">
            <i class="fas fa-print"></i></button>

            <button class="btn btn-secondary float-right btn-sm" id="pre" title="pre">
            <i class="fas fa-print"></i></button>


        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Buscar" aria-label="Username"
                aria-describedby="basic-addon1">
        </div>

        <table id="tablaPartida" class="table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">codigo Partida</th>
                    <th scope="col">fechaActual</th>
                    <th scope="col">fechacontable</th>
                    <th scope="col">concepto </th>
                    <th scope="col">estado </th>
                    <th scope="col">accion</th>

                </tr>
            </thead>
        </table>
    </div>
</div>
<script src="../lib/js/scripts/frmPartidas.js"></script>
<script>
$(document).ready(function() {

    imprimirtablapartidas()

    $("#regresarTipos").click(function(){
        $("#render").load("load/adminTipoPartidas.php");
    });
    

    $("#frmAddPartidas").click(function() {
        var tipoPartidaId = $("#tipoPartidaId").val();
    $.ajax({
        url: "load/form/Partidas/Add/frmAddPartida.php", 
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
    if (typeof estadoId !== 'undefined' && estadoId === 4) {
        $('#frmAddPartidas').hide();
    }
    
});

$("#balance").click(function() {
    var reportUrl = '../../backend/Reportes/BalanceGeneral/BalanceGeneral.php';
        window.open(reportUrl, '_blank');
    })

    $("#mayor").click(function() {
    var reportUrl = '../../backend/Reportes/libroMayor/libroMayor.php';
        window.open(reportUrl, '_blank');
    })

    $("#pre").click(function() {
    var reportUrl = '../../backend/Reportes/EstadoResultado/Estadoresultado.php';
        window.open(reportUrl, '_blank');
    })


</script>