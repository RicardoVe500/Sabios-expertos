
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cierres de los periodos</h6>
    </div>
    <div class="card-body">

        <div class="input-group mb-3">
            <div class="input-group-prepend" style="display: none;">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
            </div>
            <!-- <input type="text" class="form-control" placeholder="Buscar" aria-label="Username"
                aria-describedby="basic-addon1"> -->
        </div>

        <table id="tablaperiodocierre" class="table" style="width:100%">
            <thead>
                <tr> 
                    <th scope="col">Mes</th>
                    <th scope="col">Año</th>
                    <th scope="col">Cerrar</th>
                </tr>
            </thead>
        </table>
    </div>
</div> 


<script>
    
$(document).ready(function() {


$('.btn-selectperiodo').on('click', function () {
    $(document).ready(function () {
        
        imprimirtablacierres()

    })
});

imprimirtablacierres()


    $("#frmAddPeriodo").click(function() {
        $("#render").load("load/form/Periodo/Add/frmAddPeriodo.php");
    })

   
})

function imprimirtablacierres() {
    if ($.fn.DataTable.isDataTable('#tablaperiodocierre')) { // comprueba si hay una instancia anterior
        $('#tablaperiodocierre').DataTable().destroy(); // Se destruye la instancia anterior
    }
$('#tablaperiodocierre').DataTable({
"ajax": "../../backend/Periodo/listardatos/listardatotabla.php",
"columns": [
    { "data": "mes" },
    { "data": "anio" },
    {
        "data": "estadoId", 
        "render": function(data, type, row) {
            if (data != 4) {
                return `<button class="btn btn-warning btn-sm btn-diario"><i class="fas fa-lock"></i> Cierre Diario</button>
                        <button class="btn btn-danger btn-sm btn-cerrarPeriodo"><i class="fas fa-lock"></i> Cerrar Mes</button>`;

            } else {
                // Aquí puedes definir otro botón u omitirlo si no necesitas otro botón
                return '<button class="btn btn-success btn-sm btn-abrirPeriodo"><i class="fas fa-unlock"></i> Abrir</button>';
            }
        }
    }
],
"columnDefs": [{
    "targets": -1,
    "orderable": false,
    "className": "dt-center"
}]
});
}

$('#tablaperiodocierre').on('click', '.btn-diario', function () {
    $("#render").load("load/form/Periodo/diario/diario.php");
});

 



$('#tablaperiodocierre').on('click', '.btn-cerrarPeriodo', function () {
    var data = $('#tablaperiodocierre').DataTable().row($(this).parents('tr')).data();
    var id = data.periodoId
    let url = "../../backend/Periodo/Cierre/Cierre.php";
    $.ajax({
        url: url,
        data: { id: id },
        type: "POST",
        success: function(response) {
            const task = JSON.parse(response);
            // Verifica si la respuesta es exitosa
            if (task.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Mes cerrado!',
                    text: task.message, // Usa el mensaje de la respuesta del servidor
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: task.message, // Usa el mensaje de error del servidor
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            // Maneja errores de AJAX como problemas de conexión, URL no encontrada, etc.
            Swal.fire({
                icon: 'error',
                title: 'Error en la conexión',
                text: 'No se pudo completar la solicitud. Por favor, inténtalo de nuevo.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
});


</script>

