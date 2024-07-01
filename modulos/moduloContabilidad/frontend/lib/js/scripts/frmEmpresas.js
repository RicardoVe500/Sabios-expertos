$(document).ready(function () {

    imprimirtablaempresa()

    $("#regresar").click(function () {
        regresar();
    });

    $("#guardarEmpresa").click(function () {
        guardarEmpresa()
    });

    $("#editarEmpresa").click(function () {
        editarEmpresa()
    });

});


function regresar() {
    $("#render").load("./load/adminEmpresas.php");
}


function guardarEmpresa() {

    if ($("#nombreEmpresa").val() == "" || $("#direccion").val() == "" || $("#correo").val() == "" || $("#telefono").val() == "") {
        Swal.fire({
            title: 'Error',
            text: 'NO deben de haber datos vacios',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
    } else {
        var url = "../../backend/Empresas/Add/AddEmpresas.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frmAddEmpresa").serialize(),
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Empresa Registrada!',
                    text: 'La Empresa se registro exitosamente.',
                });
                $("#render").load("./load/adminEmpresas.php");
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

function imprimirtablaempresa() {
    if ($.fn.DataTable.isDataTable('#tablaEmpresas')) {
        $('#tablaEmpresas').DataTable().destroy(); // Destruye la instancia anterior
    }
    $('#tablaEmpresas').DataTable({
        "ajax": "../../backend/Empresas/listardatos/listardatos.php",
        "columns": [
            { "data": "nombre" },
            { "data": "direccion" },
            { "data": "correo" },
            { "data": "telefono" },
            {
                "data": null,
                "defaultContent": `
                    <button class='btn btn-primary btn-sm btn-sucursales'><i class="fas fa-layer-group"></i> Sucursales</button>
                    <button class='btn btn-success btn-sm btn-modificarEmpresa'><i class='fas fa-edit'></i> Modificar</button>
                    <button class='btn btn-danger btn-sm btn-deleteEmpresa'><i class='fa fa-trash'></i> Eliminar</button>
                `
            }
        ],
        columnDefs: [
            { "targets": -1, "orderable": false, "className": "dt-center", "width": "300px" } // Ajusta el ancho según necesites
        ],
        order: [[1, 'asc']]
    });

}

$('#tablaEmpresas').on('click', '.btn-sucursales', function () {
    var data = $('#tablaEmpresas').DataTable().row($(this).parents('tr')).data();
    var num = data.empresaId
    $("#render").load("./load/adminFrmSucursales.php", { empresaId: num }, function () {
    });
});

$('#tablaEmpresas').on('click', 'button.btn-modificarEmpresa', function () {
    var data = $('#tablaEmpresas').DataTable().row($(this).parents('tr')).data();
    var id = data.empresaId
    let url = "../../backend/Empresas/listardatos/obtenerdato.php";
    $("#render").load("./load/form/Empresas/Edit/frmEditEmpresas.php");
    $.ajax({
        url,
        data: { id },
        type: "POST",
        success: function (response) {
            const task = JSON.parse(response)
            $("#empresaId").val(task.empresaId)
            $("#nombreEmpresa").val(task.nombre)
            $("#direccion").val(task.direccion)
            $("#correo").val(task.correo)
            $("#direccion").val(task.direccion)
            $("#telefono").val(task.telefono)

        },
    })
});

$('#tablaEmpresas').on('click', 'button.btn-deleteEmpresa', function () {
    var data = $('#tablaEmpresas').DataTable().row($(this).parents('tr')).data();
    var id = data.empresaId
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
            $.post("../../backend/Empresas/delete/deleteEmpresa.php", { id }, () => {
                $('#tablaEmpresas').DataTable().ajax.reload();
            });
        }
    });
})


function editarEmpresa() {
        const pData = {
            empresaId: $("#empresaId").val(),
            nombre: $("#nombreEmpresa").val(),
            direccion: $("#direccion").val(),
            correo: $("#correo").val(),
            telefono: $("#telefono").val()
        }

        $.ajax({
            url: "../../backend/Empresas/Edit/EditEmpresa.php",
            data: pData,
            type: "POST",
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualización exitosa!',
                    text: 'Los cambios se han guardado correctamente.',
                    confirmButtonText: 'Aceptar'
                });
                $("#render").load("./load/adminEmpresas.php");
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