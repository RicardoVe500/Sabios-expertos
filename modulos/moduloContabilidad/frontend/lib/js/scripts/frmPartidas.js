$(document).ready(function(){
    
    //al nomas cargar los datos se manda el ajax para que se listen los datos y se muestren en la tabla
    var tipoPartidaId = $('#tipoPartidaId').val();
    $.ajax({
        url: "../../backend/Partidas/listardatos/listardatos.php",
        type: "POST",
        data: { tipoPartidaId },
        dataType: "json",
        success: function(response) {
            // Se destruye la tabla existente y se crea de nuevo, por si se llama multiples veces
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

//boton para ejecutar la funcion de guardar la partida
    $("#crearpartida").click(function(){
        guardarPartidas();
    });


})

//Se crea una funcion para la estructura de la tabla
function imprimirtablapartidas(){
    $('#tablaPartida').DataTable({
        columns: [
            {"data": "codigoPartida"},
            {"data": "fechaActual"},
            {"data": "fechacontable"},
            {"data": "concepto"},
            {"data": "estado"},
            {
                "data": null,
                "render": function(data, type, row) {
                    // Verifica el estadoId para decidir qué botones mostrar
                    
                    if (row.estadoId != 2) {
                        return `<button class='btn btn-primary btn-sm btn-frmcuerpo' title='Agregar' id='frmcuerpo'><i class="fas fa-folder-open"></i></button>
                                <button class='btn btn-danger btn-sm btn-deletepatidas' title='Eliminar'><i class='fa fa-trash'></i></button>`;
                    } else {
                        return `<button class='btn btn-primary btn-sm btn-imprimir' title='imprimir'><i class="fas fa-print"></i></button>
                                <button class='btn btn-danger btn-sm btn-deletepatidas' title='Eliminar'><i class='fa fa-trash'></i></button>`;
                    }
                }
            },
        ],
        columnDefs: [{ "targets": -1, "orderable": false, "className": "dt-center" }],
        order: [[1, 'asc']],
        createdRow: function (row, data, dataIndex) {
            if (data.estadoId == 2) {
                $(row).css('background-color', '#e2e2e2'); 
            }
        }
    });
}

$('#tablaPartida').on('click', '.btn-frmcuerpo', function() {
    var data = $('#tablaPartida').DataTable().row($(this).parents('tr')).data();
    var num = data.partidaId
    var num2 = data.tipoPartidaId
    var codigo = data.codigoPartida
    $("#render").load("./load/adminFrmPartida.php", { partidaId: num, codigoPartida: codigo, tipoPartidaId:num2 }, function() {
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

$('#tablaPartida').on('click', 'button.btn-deletepatidas', function () {
    var data = $('#tablaPartida').DataTable().row($(this).parents('tr')).data();
    var id = data.partidaId;
    Swal.fire({
        title: '¿Quieres eliminar este elemento?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../backend/Partidas/delete/deletepartida.php", { id }, function(response) {
                if (response.success) {
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
                    });
                    $('#tablaPartida').DataTable().row($(this).parents('tr')).remove().draw();
                    Swal.fire('Eliminado!', response.message, 'success');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error en la conexión o el servidor', 'No se pudo procesar la solicitud: ' + textStatus, 'error');
            });
        }
    });
});


