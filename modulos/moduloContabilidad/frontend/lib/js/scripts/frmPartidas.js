$(document).ready(function () {

    datosprincipales()

    //boton para ejecutar la funcion de guardar la partida
    $("#crearpartida").click(function () {
        guardarPartidas();
    });

})

$('.btn-selectperiodo').on('click', function () {
    $(document).ready(function () {
        datosprincipales();
        imprimirtablapartidas();

    })
});


function datosprincipales() {
    var tipoPartidaId = $('#tipoPartidaId').val();
    $.ajax({
        url: "../../backend/Partidas/listardatos/listardatos.php",
        type: "POST",
        data: { tipoPartidaId },
        dataType: "json",
        success: function (response) {
            // Se destruye la tabla existente y se crea de nuevo, por si se llama multiples veces
            var table = $('#tablaPartida').DataTable();
            table.clear();
            table.rows.add(response.data);
            table.draw();
        },
        error: function () {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}



//Se crea una funcion para la estructura de la tabla
function imprimirtablapartidas() {
    if ($.fn.DataTable.isDataTable('#tablaPartida')) { // comprueba si hay una instancia anterior
        $('#tablaPartida').DataTable().destroy(); // Se destruye la instancia anterior
    }
    $('#tablaPartida').DataTable({
        columns: [
            { "data": "codigoPartida" },
            { "data": "fechaActual" },
            { "data": "fechacontable" },
            { "data": "concepto" },
            { "data": "estado" },
            {
                "data": null,
                "render": function (data, type, row) {
                    // Verifica el estadoId para decidir qué botones mostrar
                    if (row.estadoId == 1 || row.estadoId == 2 ) {
                        return `<button class='btn btn-primary btn-sm btn-frmcuerpo' title='Agregar' id='frmcuerpo'><i class="fas fa-folder-open"></i></button>
                                <button class='btn btn-danger btn-sm btn-deletepatidas' title='Eliminar'><i class='fa fa-trash'></i></button>`;
                    } else {
                        return `<button class='btn btn-primary btn-sm btn-imprimirpartida' title='Reporte'><i class="fas fa-print"></i></button>`;
                    }  
                    
                }
            },
        ],
        columnDefs: [{ "targets": -1, "orderable": false, "className": "dt-center" }],
        order: [[1, 'asc']],
        createdRow: function (row, data, dataIndex) {
            if (data.estadoId == 3) {
                $(row).css('background-color', '#e2e2e2');
            }
        }
    });
}


//mandamos la informacion del ID, tipo partida y tambien el codigo para poder hacer el filtrado mas adetalle
//en el otro campo a renderizar.
$('#tablaPartida').on('click', '.btn-frmcuerpo', function () {
    var data = $('#tablaPartida').DataTable().row($(this).parents('tr')).data();
    var num = data.partidaId
    var num2 = data.tipoPartidaId
    var codigo = data.codigoPartida
    $("#render").load("./load/adminFrmPartida.php", { partidaId: num, codigoPartida: codigo, tipoPartidaId:num2 }, function() {
    });
});

//Se establece un boton para imprimir el reporte de la partida.
$('#tablaPartida').on('click', '.btn-imprimirpartida', function () {
    var data = $('#tablaPartida').DataTable().row($(this).parents('tr')).data();
    var partidaId = data.partidaId
    var codigoPartida = data.codigoPartida
    var reportUrl = `../../backend/Partidas/reportes/partidasDetalle.php?partidaId=${partidaId}&codigoPartida=${codigoPartida}`;

    // Abrir el reporte en una nueva pestaña
    window.open(reportUrl, '_blank');
})

$("#reporpar").click(function () {
    reportUrl = "../../backend/Partidas/reportes/rep.php";
    window.open(reportUrl, '_blank');
});




//Funcion para guardar las partidas contables
function guardarPartidas() {

    if ($("#fechacontable").val() == "" || $("#concepto").val() == "") {
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
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Tipo Partida Agregada!',
                    text: 'El Tipo de partida se agrego exitosamente.',
                });
            },
            error: function (xhr, status, error) {
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

//Funcion para eliminar las partidas contables
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
            $.post("../../backend/Partidas/delete/deletepartida.php", { id }, function (response) {
                if (response.success) {
                    var tipoPartidaId = $('#tipoPartidaId').val();
                    $.ajax({
                        url: "../../backend/Partidas/listardatos/listardatos.php",
                        type: "POST",
                        data: { tipoPartidaId },
                        dataType: "json",
                        success: function (response) {
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
            }, "json").fail(function (jqXHR, textStatus, errorThrown) {
                Swal.fire('Error en la conexión o el servidor', 'No se pudo procesar la solicitud: ' + textStatus, 'error');
            });
        }
    });
});


