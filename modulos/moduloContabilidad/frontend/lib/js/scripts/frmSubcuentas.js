$(document).ready(function(){
    
    $("#regresarcat").click(function(){
        $("#render").load("./load/adminCatalogo.php");
    }); 

    ImprimirtablaSub()
  
});

    function ImprimirtablaSub() {
        var numeroCuenta = $('#numeroCuenta').val();
        $.ajax({
            url: "../../backend/SubCuentas/listardatos/listardatos.php",
            type: "POST",
            data: { numeroCuenta },
            dataType: "json",
            success: function(response) {
                // Destruir la tabla existente antes de volver a crearla
                var table = $('#tablasubcuenta').DataTable();
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
    }
    

        $('#tablasubcuenta').DataTable({
            columns: [
                {"data": "nombreCuenta"},
                {"data": "numeroCuenta"},
                {"data": "nivelCuenta"},
                {"data": "cuentaDependiente"},
                {"data": "movimiento"},
                {"data": null,
                    "defaultContent": `
                        <button class='btn btn-primary btn-sm btn-createsub' title='Agregar' id='frmAddSubcuenta'><i class='fas fa-plus'></i></button>
                        <button class='btn btn-success btn-sm btn-modificarsub' title='Modificar'><i class='fas fa-edit'></i></button>
                        <button class='btn btn-danger btn-sm btn-deletesub' title='Eliminar'><i class='fa fa-trash'></i></button>
                    `
                }
            ],
            columnDefs: [{ "targets": -1, "orderable": false, "className": "dt-center" }],
            order: [[1, 'asc']]
        });

        
    
        $('#tablasubcuenta').on('click', 'button.btn-deletesub', function () {
            var data = $('#tablasubcuenta').DataTable().row($(this).parents('tr')).data();
            var id = data.cuentaId;
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
                    $.post("../../backend/SubCuentas/delete/deleteSubCuentas.php", { id }, function(response) {
                        // Llamar a la función para actualizar la tabla después de eliminar
                        ImprimirtablaSub();
                    });
                }
            });
        });
  
        // Llenar el select con datos de movimientos al cargar la página
        $.ajax({
            url: "../../backend/SubCuentas/listardatos/select.php",
            type: "GET",
            success: function(response) {
                try {
                    // Intenta analizar la respuesta como JSON
                    let movimientos = JSON.parse(response);
                    // Variable para acumular opciones del select
                    let options = "";
                    // Iterar sobre cada movimiento y construir las opciones
                    movimientos.forEach(movimiento => {
                        options += `<option value="${movimiento.movimientoId}">${movimiento.movimiento}</option>`;
                    });
                    // Asignar todas las opciones al select una sola vez
                    $('#selectsubcuentas').html(options);
    
                } catch (error) {
                    console.error("Error al procesar la respuesta JSON:", error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error al obtener los movimientos:", textStatus, errorThrown);
            }
        });


    $('#tablasubcuenta').on('click', 'button.btn-createsub', function () {
            var data = $('#tablasubcuenta').DataTable().row($(this).parents('tr')).data();
            var id = data.cuentaId
            let url = "../../backend/SubCuentas/listardatos/obtenerdato.php";
            $("#render").load("./load/form/SubCatalogo/Add/frmAddSubCuenta.php");
        $.ajax({
            url,
            data: {id},
            type: "POST",
            success: function(response){
                const task = JSON.parse(response)
                console.log(response)
                $("#cuentaId").val()
                $("#nivelCuenta").val(task.nivelCuenta)
                $("#numeroCuenta").val(task.numeroCuenta)
                $("#nivelCuenta").val(task.nivelCuenta)

            },
        })
    
    });

    function guardarSubcuentas(){
        const pData = {
            cuentaId: $("#cuentaId").val(),
            numeroCuenta: $("#numeroCuenta").val(),
            nivelCuenta: $("#nivelCuenta").val(),
            nombreCuenta: $("#nombreCuenta").val(),
            movimientos: $("#selectsubcuentas").val(),
            tipoSaldo: $("#selectTipoSaldo").val()
        }
        $.ajax({
            url: "../../backend/SubCuentas/add/addSubCuentas.php",
            data: pData,
            type: "POST",
            success: function(response){
                console.log(response)
                Swal.fire({
                    icon: 'success',
                    title: '¡SubCuenta agregada!',
                    text: 'Los cambios se han guardado correctamente.',
                });
                    
            },
        })

    }
       
          
        
    $('#tablasubcuenta').on('click', 'button.btn-modificarsub', function () {
        var data = $('#tablasubcuenta').DataTable().row($(this).parents('tr')).data();
        var id = data.cuentaId
        let url = "../../backend/SubCuentas/listardatos/obtenerdato.php";
        $("#render").load("./load/form/SubCatalogo/Edit/frmEditCatalogo.php");
    $.ajax({
        url,
        data: {id},
        type: "POST",
        success: function(response){
            const task = JSON.parse(response)
            $("#cuentaId").val(task.cuentaId)
            $("#numeroCuenta").val(task.numeroCuenta)
            $("#editnombreCuenta").val(task.nombreCuenta)

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


function editarSubcuentas(){
    const pData = {
        cuentaId: $("#cuentaId").val(),
        numeroCuenta: $("#numeroCuenta").val(),
        nombreCuenta: $("#editnombreCuenta").val(),
        movimientos: $("#selectsubcuentas").val(),
        tipoSaldo: $("#selectTipoSaldo").val()

    }
    $.ajax({
        url: "../../backend/SubCuentas/edit/editSubCuentas.php",
        data: pData,
        type: "POST",
        success: function(response){
            Swal.fire({
                icon: 'success',
                title: '¡Actualización exitosa!',
                text: 'Los cambios se han guardado correctamente.',
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error desde el servidor:", textStatus, errorThrown);
            alert("Error en el servidor: " + textStatus);
        }
    })
}
    
       
 

function selectTipoSaldo(){
    $('#selectTipoSaldo').select2({
        ajax: {
            url: "../../backend/SubCuentas/listardatos/selectTipoSaldo.php",
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
        placeholder: 'Buscar Tipo de Saldo...',
        allowClear: true  // se establece el limpiado del select 
    });
}