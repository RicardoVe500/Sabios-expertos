$(document).ready(function () {

 
    //Se captura el dato del campo partidaId que se mando desde Partidas

    $('#selectcomprobante').select2({
        ajax: {
            url: "../../backend/Cuerpo/listardatos/selectComprobante.php",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // Término de búsqueda enviado al servidor
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.tipoComprobanteId,
                        text: item.nombreComprobante // Aqui se concatena los campos para mostrarlos
                    }))
                };
            }
        },
        theme: "bootstrap",
        placeholder: 'Buscar cuenta...',
        allowClear: true  // se establece el limpiado del select 
    });


    $('#selectcuentas').select2({
        ajax: {
            url: "../../backend/Cuerpo/listardatos/select.php",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // Término de búsqueda enviado al servidor
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.cuentaId,
                        text: `${item.numeroCuenta} | ${item.nombreCuenta}` // Aqui se concatena los campos para mostrarlos
                    }))
                };
            }
        },
        theme: "bootstrap",
        placeholder: 'Buscar cuenta...',
        allowClear: true,  // se establece el limpiado del select
        minimumInputLength: 2
    });


})

var partidaId = $('#partidaId').val();
var codigoPartida = $('#codigoPartida').val();

function cargadatospartida() {
    $.ajax({
        url: "../../backend/Cuerpo/listardatos/obtenerdato.php", //direccion donde mandamos el dato del ID
        type: "POST",
        data: { partidaId },
        success: function (response) {
            const task = JSON.parse(response);
            //llenamos los campos de nuestro formulario del archivo JSON que recibimos
            $("#partidaId").val(task.partidaId);
            $("#cuerponombrePartida").val(task.nombrePartida);
            $("#estadoId").val(task.estadoId);
            $("#cuerpoestado").val(task.estado);
            $("#cuerpofechacontable").val(task.fechacontable);
            $("#cuerpofechaActual").val(task.fechaActual);
            $("#cuerpoconcepto").val(task.concepto);
            $("#cuerpodebe").val(task.debe);
            $("#cuerpohaber").val(task.haber);
            $("#cuerpodiferencia").val(task.diferencia);

            if (task.estadoId == '2') {
                $('#cerrarCuenta').show();
            } else {
                $('#cerrarCuenta').hide();
            }

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

    $.ajax({
        url: "../../backend/Cuerpo/listardatos/listardatos.php",
        type: "POST",
        data: { partidaId: partidaId, codigoPartida: codigoPartida },
        dataType: "json",
        success: function (response) {
            // Destruir la tabla existente antes de volver a crearla
            var table = $('#tablaCuerpo').DataTable();
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




function guardarCuerpoPartida() {
    var url = "../../backend/Cuerpo/Add/addCuerpoPartida.php";
    $.ajax({
        type: "POST",
        url: url,
        data: $("#frmcuerpo").serialize(),
        success: function (data) {
            Swal.fire({
                icon: 'success',
                title: 'Se agrego a la partida',
                text: 'Se agrego correctamente.',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    agregarSaldo()
                    $('#frmcuerpo')[0].reset(); // Resetear el formulario
                    $('#selectcomprobante').val('').trigger('change');
                    $('#selectcuentas').val('').trigger('change');
                    cargadatospartida();
                    $('#tablaPartida').DataTable().ajax.reload();
                }
            });
        },
        error: function (xhr, status, error) {
            console.log(error)
            Swal.fire({
                icon: 'error',
                title: 'Error al crear',
                text: 'No se pudo crear el tipo partida. Por favor, intenta de nuevo.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}


function validarCampos() {
    var debe = parseFloat(document.getElementById('debeCuerpo').value);
    var haber = parseFloat(document.getElementById('haberCuerpo').value);

    // Comprueba si ambos campos tienen valores mayores que 0
    if (debe > 0 && haber > 0) {
        Swal.fire({
            title: 'Error',
            text: 'No puedes ingresar saldos en debe y haber ha la vez',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        document.getElementById('debeCuerpo').value = 0;
        document.getElementById('haberCuerpo').value = 0;
    }
}





function Imprimirtablacuerpo() {
    if ($.fn.DataTable.isDataTable('#tablaCuerpo')) {
        $('#tablaCuerpo').DataTable().destroy(); // Destruye la instancia anterior
    }
    $('#tablaCuerpo').DataTable({
        columns: [
            {
                "data": null,
                "title": "#", // Título de la columna de conteo
                "render": function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { "data": "cuenta" },
            { "data": "concepto" },
            { "data": "cargo" },
            { "data": "abono" },
            {
                "data": null,
                "defaultContent": `
                    <button class='btn btn-primary btn-sm btn-editarcuerpo' title='Editar' id='frmcuerpo'><i class="fas fa-edit"></i></button>
                    <button class='btn btn-danger btn-sm btn-deletecuerpo' title='Eliminar'><i class='fa fa-trash'></i></button>
                `
            }
        ],
        columnDefs: [{ "targets": -1, "orderable": false, "className": "dt-center" }],
        order: [[1, 'asc']]
    });
}

$('#tablaCuerpo').on('click', '.btn-editarcuerpo', function () {
    var data = $('#tablaCuerpo').DataTable().row($(this).parents('tr')).data();
    var id = data.partidaDetalleId
    let url = "../../backend/Cuerpo/listardatos/obtenerdatoedit.php";
    $.ajax({
        url,
        data: { id },
        type: "POST",
        success: function (response) {
            const task = JSON.parse(response)
            $("#partidaDetalleId").val(task.partidaDetalleId)
            $("#numeroComprobante").val(task.numeroComprobante)
            $("#fechaComprobante").val(task.fechaComprobante)
            $("#conceptoespecifico").val(task.concepto)
            $("#debeCuerpo").val(task.cargo)
            $("#haberCuerpo").val(task.abono)

            // Asegúrate de que select2 reconozca la opción cargada correctamente
            var newOption = {
                id: task.tipoComprobanteId,
                text: task.nombreComprobante,
                selected: true,
                title: task.nombreComprobante
            };
            $("#selectcomprobante").empty().append(new Option(newOption.text, newOption.id, true, true)).trigger('change');

            var newOption2 = {
                id: task.cuentaId,
                text: task.cuenta,
                selected: true,
                title: task.cuenta
            };
            $("#selectcuentas").empty().append(new Option(newOption2.text, newOption2.id, true, true)).trigger('change');

            $('#dato').data('mode', 'edit');

            $('#dato').text('Actualizar');
        },
    })
});

$('#tablaCuerpo').on('click', 'button.btn-deletecuerpo', function () {
    var data = $('#tablaCuerpo').DataTable().row($(this).parents('tr')).data();
    var id = data.partidaDetalleId
    var partidaId = data.partidaId
    var cuentaId = data.cuentaId
console.log(cuentaId)

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
            $.post("../../backend/Cuerpo/delete/deleteCuerpo.php", { id, partidaId }, () => {
                $.ajax({
                    url: "../../backend/Saldos/Saldos.php",
                    data: {cuentaId: cuentaId},
                    type: "POST",
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Se guardo el saldo!',
                            text: 'Los cambios se han guardado correctamente.',
                        });
                    },
                    error: function (xhr, status, error) {
                        console.log(error)
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al hacer saldo',
                            text: 'No se pudo modificar la partida. Por favor, intenta de nuevo.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
                cargadatospartida()
                $('#tablacatalogo').DataTable().ajax.reload();
            });
        }
    });
})

function editardatos() {
    const pData = {
        partidaDetalleId: $("#partidaDetalleId").val(),
        partidaId: $("#partidaId").val(),
        cuentaId: $("#selectcuentas").val(),
        tipoComprobanteId: $("#selectcomprobante").val(),
        numeroComprobante: $("#numeroComprobante").val(),
        fechaComprobante: $("#fechaComprobante").val(),
        concepto: $("#conceptoespecifico").val(),
        cargo: $("#debeCuerpo").val() || '0',
        abono: $("#haberCuerpo").val() || '0',
    }
    $.ajax({
        url: "../../backend/Cuerpo/edit/editCuerpoPartida.php",
        data: pData,
        type: "POST",
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: '¡SubCuenta agregada!',
                text: 'Los cambios se han guardado correctamente.',
            });
            agregarSaldo()
            $('#frmcuerpo')[0].reset(); // Resetear el formulario
            $('#selectcomprobante').val('').trigger('change');
            $('#selectcuentas').val('').trigger('change');
            cargadatospartida();
            $('#tablaPartida').DataTable().ajax.reload();
            $('#dato').data('mode', 'add');
            $('#dato').text('Agregar');

        },
        error: function (xhr, status, error) {
            console.log(error)
            Swal.fire({
                icon: 'error',
                title: 'Error al Modificar',
                text: 'No se pudo modificar la partida. Por favor, intenta de nuevo.',
                confirmButtonText: 'Aceptar'
            });
        }
    })
}

$('#cerrarCuenta').click(function () {
    // Usamos SweetAlert para confirmar la acción
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Quieres cerrar esta cuenta? Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrarla!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Usuario confirma que quiere cerrar la cuenta
            var partidaId = $('#partidaId').val();  // Asegúrate de que este es el modo correcto de obtener el ID
            $.ajax({
                url: "../../backend/Cuerpo/edit/updateestado.php",
                type: "POST",
                data: {
                    partidaId: partidaId,
                },
                dataType: "json", // Asegúrate de especificar que esperas un JSON
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Se ha cerrado la partida!',
                            text: 'NO se podrán hacer modificaciones en esta partida.',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirección como el botón "regresarpartidas"
                                var tipoPartidaId = $("#tipoPartidaId").val();
                                $.ajax({
                                    url: "load/adminPartidas.php",
                                    type: "POST",
                                    data: {
                                        tipoPartidaId: tipoPartidaId
                                    },
                                    success: function(response) {

                                        var fechacontable = $("#cuerpofechacontable").val();
                                        $.ajax({
                                            url: "../../backend/mayorizacion/mayorizacion.php",
                                            type: "POST",
                                            data: {
                                                fechacontable: fechacontable
                                            },
                                            success: function(response) {                                            
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'La mayorizacion se ejecuto',
                                                    text: 'Se ejecuto la mayorizacion.',
                                                    confirmButtonText: 'Aceptar'
                                                });

                                                $("#render").html(response);
                                            },
                                            error: function(xhr, status, error) {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error mayorizacion',
                                                    text: 'intenta de nuevo.',
                                                    confirmButtonText: 'Aceptar'
                                                });
                                            }
                                        });

                                        $("#render").html(response);
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error al mostrar',
                                            text: 'No se pudo cargar el contenido. Por favor, intenta de nuevo.',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al cerrar',
                            text: response.message, // Muestra el mensaje de error del servidor
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la conexión',
                        text: 'Hubo un problema con la conexión al servidor. Por favor, verifica tu red o intenta más tarde.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
});



function agregarSaldo(){
    var cuentaId = $('#selectcuentas').val();
    $.ajax({
        url: "../../backend/Saldos/Saldos.php",
        data: {cuentaId: cuentaId},
        type: "POST",
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: '¡Se guardo el saldo!',
                text: 'Los cambios se han guardado correctamente.',
            });
        },
        error: function (xhr, status, error) {
            console.log(error)
            Swal.fire({
                icon: 'error',
                title: 'Error al hacer saldo',
                text: 'No se pudo modificar la partida. Por favor, intenta de nuevo.',
                confirmButtonText: 'Aceptar'
            });
        }
    })
}