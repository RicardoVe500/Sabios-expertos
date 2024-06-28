$(document).ready(function() {

    inicializarDataTable()
    Cargardatostabla()

    $("#frmAddsucursal").click(function () {
        guardarSucursal();
    });

   
});


function inicializarDataTable() {
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


function guardarPartidas() {

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