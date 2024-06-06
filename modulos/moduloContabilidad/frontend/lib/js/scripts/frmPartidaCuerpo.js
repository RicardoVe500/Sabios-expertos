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
        allowClear: true  // se establece el limpiado del select 
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
            $("#cuerpoestado").val(task.estado);
            $("#cuerpofechacontable").val(task.fechacontable);
            $("#cuerpofechaActual").val(task.fechaActual);
            $("#cuerpoconcepto").val(task.concepto);
            $("#cuerpodebe").val(task.debe);
            $("#cuerpohaber").val(task.haber);
            $("#cuerpodiferencia").val(task.diferencia);
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

function Imprimirtablacuerpo() {
    if ($.fn.DataTable.isDataTable('#tablaCuerpo')) {
        $('#tablaCuerpo').DataTable().destroy(); // Destruye la instancia anterior
    }
    $('#tablaCuerpo').DataTable({
        columns: [
            {
            "data": null,
                "title": "#", // Título de la columna de conteo
                "render": function(data, type, row, meta){
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
            $("#selectcomprobante").val(task.nombreComprobante)
            $("#numeroComprobante").val(task.numeroComprobante)
            $("#fechaComprobante").val(task.fechaComprobante)
            $("#selectcuentas").val(task.cuenta)
            $("#conceptoespecifico").val(task.concepto)
            $("#debeCuerpo").val(task.cargo)
            $("#haberCuerpo").val(task.abono)

            var newOption = new Option(task.nombreComprobante, task.tipoComprobanteId, true, true);
            $('#selectcomprobante').append(newOption).trigger('change');

            var newOption2 = new Option(task.cuenta, task.cuentaId, true, true);
            $('#selectcuentas').append(newOption2).trigger('change');

        },
    })
});

$('#tablaCuerpo').on('click', 'button.btn-deletecuerpo', function () {
    var data = $('#tablaCuerpo').DataTable().row($(this).parents('tr')).data();
    var id = data.partidaDetalleId
    var partidaId = data.partidaId

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
                cargadatospartida()
                $('#tablacatalogo').DataTable().ajax.reload();

            });
        }
    });
})

function editardatos() {


}