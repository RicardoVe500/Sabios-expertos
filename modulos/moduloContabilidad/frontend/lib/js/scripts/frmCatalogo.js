$(document).ready(function(){

    $("#regresar").click(function(){
        regresar();
    });

});

$('#inputGroupFileAddon03').click(function() {
    var fileData = $('#archivoex').prop('files')[0];
    if (!fileData) {
        Swal.fire({
            title: 'Error!',
            text: 'No has seleccionado ningún archivo.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        return;
    }
    var formData = new FormData();
    formData.append('file', fileData);
    $.ajax({
        url: '../../backend/Catalogo/excel/importar.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire(
                '¡Éxito!',
                'Archivo subido y procesado correctamente.',
                'success'
            );
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error al subir el archivo!',
                text: 'Por favor, inténtalo de nuevo.',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
    });
});

function guardarDatos(){

    if($("#numeroCuenta").val()==""|| $("#nombreCuenta").val() == ""|| $("#selectTipoSaldo").val() == ""){
        Swal.fire({
            title: 'Error',
            text: 'NO deben de haber datos sin llenar',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
    } else {
        var url = "../../backend/Catalogo/add/AddCatalogo.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $("#frmAddCatalogo").serialize(),
            success: function(data){
                if(data==1){
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cuenta Agregada!',
                        text: 'La cuenta se agrego exitosamente.',
                    });                                        
                    regresar();
                } else if (data==2){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Datos ya existen.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo completar la solicitud. Error: ' + error
                    });
                }
            }
        });
    }  
}

function imprimirtabla(){
    $('#tablacatalogo').DataTable({
        "ajax": "../../../moduloContabilidad/backend/Catalogo/listardatos/listardatos.php",
        "columns": [
            {"data": "nombreCuenta"},
            {"data": "numeroCuenta"},
            {"data": "nivelCuenta" },
            {"data": "movimiento"},
            {"data": null,
                "defaultContent": `
                    <button class='btn btn-primary btn-sm btn-sub'><i class="fas fa-layer-group"></i> SubCuentas</button>
                    <button class='btn btn-success btn-sm btn-modificar'><i class='fas fa-edit'></i> Modificar</button>
                    <button class='btn btn-danger btn-sm btn-delete'><i class='fa fa-trash'></i> Eliminar</button>
                `
            }
        ],
        "columnDefs": [{
            "targets": -1,
            "orderable": false,
            "className": "dt-center"
        }]
    });
    
}

$('#tablacatalogo').on('click', '.btn-sub', function() {
    var data = $('#tablacatalogo').DataTable().row($(this).parents('tr')).data();
    var num = data.numeroCuenta
    $("#render").load("./load/adminSubCuentas.php", { numeroCuenta: num }, function() {
        // Este callback se ejecuta después de que la carga esté completa.
        // Aquí puedes realizar alguna inicialización si es necesario.
    });
});

$('#tablacatalogo').on('click', 'button.btn-delete', function () {
    var data = $('#tablacatalogo').DataTable().row($(this).parents('tr')).data();
    var id = data.cuentaId
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
            $.post("../../backend/Catalogo/delete/deleteCatalogo.php", { id }, () => {
                $('#tablacatalogo').DataTable().ajax.reload();
            });
        }
    });
})

$('#tablacatalogo').on('click', 'button.btn-modificar', function () {
    var data = $('#tablacatalogo').DataTable().row($(this).parents('tr')).data();
    var id = data.cuentaId
    let url = "../../backend/Catalogo/listardatos/obtenerdato.php";
    $("#render").load("./load/form/Catalogo/Edit/frmEditCatalogo.php");
$.ajax({
    url,
    data: {id},
    type: "POST",
    success: function(response){
        const task = JSON.parse(response)
        $("#cuentaId").val(task.cuentaId)
        $("#editnumeroCuenta").val(task.numeroCuenta)
        $("#editnombreCuenta").val(task.nombreCuenta)
        $("#selectTipoSaldo").val(task.nombreTipo)

        var newOption = {
            id: task.tipoSaldoId,
            text: task.nombreTipo,
            selected: true,
            title: task.nombreTipo
        };
        $("#selectTipoSaldo").empty().append(new Option(newOption.text, newOption.id, true, true)).trigger('change');

        

    },
})

});

function editarDatos(){
        const pData = {
            cuentaId: $("#cuentaId").val(),
            numeroCuenta: $("#editnumeroCuenta").val(),
            nombreCuenta: $("#editnombreCuenta").val(),
            selectTipoSaldo: $("#selectTipoSaldo").val(),
        }
        $.ajax({
            url: "../../backend/Catalogo/edit/editCatalogo.php",
            data: pData,
            type: "POST",
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualización exitosa!',
                    text: 'Los cambios se han guardado correctamente.',
                    confirmButtonText: 'Aceptar'
                });
                $("#render").load("./load/adminCatalogo.php");
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: 'No se pudo guardar los cambios. Por favor, intenta de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        
       }
    


function regresar(){
    $("#render").load("./load/adminCatalogo.php");
}

function selectTipoSaldo(){
    $('#selectTipoSaldo').select2({
        ajax: {
            url: "../../backend/Catalogo/listardatos/selectTipoSaldo.php",
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
                        id: item.tipoSaldoId,
                        text: item.nombreTipo // Aqui se concatena los campos para mostrarlos
                    }))
                };
            }
        },
        theme: "bootstrap",
        placeholder: 'Buscar cuenta...',
        allowClear: true  // se establece el limpiado del select 
    });
}