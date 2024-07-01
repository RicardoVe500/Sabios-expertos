$(document).ready(function() {

    Cargardatostabla()
    inicializarDataTable()


    $("#frmAddsucursal").click(function() {
        var empresaId = $("#empresaId").val();  // Obtiene el valor del input
        $("#render").load("./load/form/Sucursal/Add/frmAddSucursal.php", {
            empresaId: empresaId  // Envía este dato con la solicitud
        });
    });

    $("#guardarSucursal").click(function() {
        guardarsucursales()
    })

    $("#editSucursal").click(function() {
        editarSucursal()
    })

    $("#regresar").click(function() {
        var empresaId = $("#empresaId").val();  
        $("#render").load("./load/adminFrmSucursales.php", {
            empresaId: empresaId  
        });
    })

});

function Cargardatostabla() {
    var empresaId = $('#empresaId').val();
    $.ajax({
        url: "../../backend/Sucursal/listardatos/listardatos.php",
        type: "POST",
        data: { empresaId },
        dataType: "json",
        success: function (response) {
            var table = $('#tablaSucursal').DataTable();
            table.clear();
            table.rows.add(response.data);
            table.draw();
        },
        error: function () {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al cargar los datos',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

function inicializarDataTable() {
    if ($.fn.DataTable.isDataTable('#tablaSucursal')) {
        $('#tablaSucursal').DataTable().destroy(); // Destruye la instancia anterior
    }
    $('#tablaSucursal').DataTable({
        "columns": [
            {"data": "nombre"},
            {"data": "direccion"},
            {"data": "correo"},
            {"data": "telefono"},
            {"data": null, "defaultContent": `
                <button class='btn btn-success btn-sm btn-editSucursal'><i class='fas fa-edit'></i> Modificar</button>
                <button class='btn btn-danger btn-sm btn-deleteSucursal'><i class='fa fa-trash'></i> Eliminar</button>
            `}
        ],
        columnDefs: [
            { "targets": -1, "orderable": false, "className": "dt-center", "width": "300px" } // Ajusta el ancho según necesites
        ],
        order: [[1, 'asc']]
    });
}

$('#tablaSucursal').on('click', 'button.btn-editSucursal', function () {
    var data = $('#tablaSucursal').DataTable().row($(this).parents('tr')).data();
    var id = data.sucursalId
    let url = "../../backend/Sucursal/listardatos/obtenerdato.php";
    $("#render").load("./load/form/Sucursal/Edit/frmEditSucursal.php");
    $.ajax({
        url,
        data: { id },
        type: "POST",
        success: function (response) {
            const task = JSON.parse(response)
            $("#sucursalId").val(task.sucursalId)
            $("#empresaId").val(task.empresaId)
            $("#nombreSucursal").val(task.nombre)
            $("#direccion").val(task.direccion)
            $("#correo").val(task.correo)
            $("#direccion").val(task.direccion)
            $("#telefono").val(task.telefono)

        },
    })
});




function guardarsucursales() {
    if ($("#nombreSucursal").val() == "" || $("#direccion").val() == "" || $("#correo").val() == "" || $("#telefono").val() == "") {
        Swal.fire({
            title: 'Error',
            text: 'NO deben de haber datos vacios',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
    } else {
        var url = "../../backend/Sucursal/Add/AddSucursal.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frmAddSucursal").serialize(),
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Empresa Registrada!',
                    text: 'La Empresa se registro exitosamente.',
                });
                

            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear',
                    text: 'No se pudo registrar la empresa. Por favor, intenta de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    }
}

function editarSucursal() {
        const pData = {
            sucursalId: $("#sucursalId").val(),
            nombre: $("#nombreSucursal").val(),
            direccion: $("#direccion").val(),
            correo: $("#correo").val(),
            telefono: $("#telefono").val()
        }
        $.ajax({
            url: "../../backend/Sucursal/edit/EditSucursal.php",
            data: pData,
            type: "POST",
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualización exitosa!',
                    text: 'Los cambios se han guardado correctamente.',
                    confirmButtonText: 'Aceptar'
                });
               
                $('#tablaSucursal').DataTable().ajax.reload();
                var empresaId = $("#empresaId").val();  // Obtiene el valor del input
            $("#render").load("./load/adminFrmSucursales.php", {
                empresaId: empresaId  // Envía este dato con la solicitud
            });

            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: 'No se pudo guardar los cambios. Por favor, intenta de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
            }
        })

}