$(document).ready(function(){
    
    var tipoPartidaId = $('#tipoPartidaId').val();
    $.ajax({
        url: "../../backend/Partidas/listardatos/listardatos.php",
        type: "POST",
        data: { tipoPartidaId },
        dataType: "json",
        success: function(response) {
            
            // Destruir la tabla existente antes de volver a crearla
            var table = $('#tablaPartida').DataTable();
            table.clear();
            table.rows.add(response.data);
            table.draw();
        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        }
    });

    $("#crearpartida").click(function(){
        guardarPartidas();
    });


})

function imprimirtablapartidas(){
    $('#tablaPartida').DataTable({
        columns: [
            {"data": "codigoPartida"},
            {"data": "fechaActual"},
            {"data": "fechacontable"},
            {"data": "estado"},
            {"data": "concepto"},
            {"data": null,
                "defaultContent": `
                    <button class='btn btn-primary btn-sm btn-frmcuerpo' title='Agregar' id='frmcuerpo'><i class="fas fa-folder-open"></i></button>
                    <button class='btn btn-danger btn-sm btn-deletesub' title='Eliminar'><i class='fa fa-trash'></i></button>
                `
            }
        ],
        columnDefs: [{ "targets": -1, "orderable": false, "className": "dt-center" }],
        order: [[1, 'asc']]
    });
}

$('#tablaPartida').on('click', '.btn-frmcuerpo', function() {
    var data = $('#tablaPartida').DataTable().row($(this).parents('tr')).data();
    var num = data.partidaId
    var codigo = data.codigoPartida
    $("#render").load("./load/adminFrmPartida.php", { partidaId: num, codigoPartida: codigo }, function() {
    });
});


/*
$('#tablaPartida').off('click').on('click', 'button.btn-frmcuerpo', function () {
    var data = $('#tablaPartida').DataTable().row($(this).parents('tr')).data();
    var id = data.partidaId;
    if (!id) {
        console.error("ID no está definido o es vacío");
        return; 
    }
    let url = "../../backend/Partidas/listardatos/obtenerdato.php";
    $("#render").load("./load/adminFrmPartida.php", function() {
        $.ajax({
            url: url,
            data: { id: id },
            type: "POST",
            success: function(response){
                const task = JSON.parse(response);
                $("#partidaId").val(task.partidaId);
                $("#cuerponombrePartida").val(task.nombrePartida);
                $("#cuerpoestado").val(task.estado);
                $("#cuerpofechacontable").val(task.fechacontable);
                $("#cuerpofechaActual").val(task.fechaActual);
                $("#cuerpoconcepto").val(task.concepto);
                $("#cuerpodebe").val(task.debe);
                $("#cuerpohaber").val(task.haber);
                $("#cuerpodiferencia").val(task.diferencia);
            },
        });
    });
});
*/

function guardarPartidas(){

    if($("#fechacontable").val()==""|| $("#concepto").val() == ""){
        Swal.fire({
            title: 'Error',
            text: 'NO deben de haber datos sin llenar',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
    } else {
        var url = "../../backend/Partidas/Add/AddPartida.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frmAddPartida").serialize(),
            success: function(data){
                console.log(data)
                Swal.fire({
                    icon: 'success',
                    title: '¡Tipo Partida Agregada!',
                    text: 'El Tipo de partida se agrego exitosamente.',
                }); 
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
    }  
}

