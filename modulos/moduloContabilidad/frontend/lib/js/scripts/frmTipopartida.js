$(document).ready(function() {


    obtenerdatostipopartida()
    

    $("#CrearTipoPartida").click(function(){
        guardarTipoPartida();
    });

})

    function guardarTipoPartida(){

        if($("#nombrePartida").val()==""|| $("#abreviacion").val() == ""){
            Swal.fire({
                title: 'Error',
                text: 'NO deben de haber datos sin llenar',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        } else {
            var url = "../../backend/TipoPartida/Add/AddTipoPartida.php";
            $.ajax({
                type: "POST",
                url: url,
                data: $("#frmAddTipoPartida").serialize(),
                success: function(data){
                    Swal.fire({
                        icon: 'success',
                        title: '¡Tipo Partida Agregada!',
                        text: 'El Tipo de partida se agrego exitosamente.',
                    }); 
                    $("#render").load("./load/adminTipoPartidas.php");
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
    
    function imprimirtablaTipoPartida(){
        $('#tablaTipoPartida').DataTable({
            "ajax": "../../backend/TipoPartida/Listardatos/listardatos.php",
            "columns": [
                {"data": "nombrePartida"},
                {"data": "abreviacion"},
                {"data": "descripcion" },
                {"data": null,
                    "defaultContent": `
                        <button class='btn btn-primary btn-sm btn-partidas'><i class="fas fa-layer-group"></i> Partidas</button>
                        <button class='btn btn-success btn-sm btn-modificartipopartidas'><i class='fas fa-edit'></i> Modificar</button>
                        <button class='btn btn-danger btn-sm btn-deletetipopartidas'><i class='fa fa-trash'></i> Eliminar</button>
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

    $('#tablaTipoPartida').on('click', '.btn-partidas', function() {
        var data = $('#tablaTipoPartida').DataTable().row($(this).parents('tr')).data();
        var num = data.tipoPartidaId
        $("#render").load("./load/adminPartidas.php", { tipoPartidaId: num }, function() {
        });
    });

    function obtenerdatostipopartida(){
        $('#tablaTipoPartida').on('click', 'button.btn-modificartipopartidas', function () {
        var data = $('#tablaTipoPartida').DataTable().row($(this).parents('tr')).data();
        var id = data.tipoPartidaId
        let url = "../../backend/TipoPartida/listardatos/obtenerdato.php";
        $("#render").load("./load/form/TipoPartidas/Edit/frmEditTipoPartida.php");
        $.ajax({
            url,
            data: {id},
            type: "POST",
            success: function(response){
                const task = JSON.parse(response)
                $("#tipoPartidaId").val(task.tipoPartidaId)
                $("#editnombrePartida").val(task.nombrePartida)
                $("#editabreviacion").val(task.abreviacion)
                $("#editdescripcion").val(task.descripcion)
            },
        })
    });
}

    function editartipopartida(){
        
        $(document).on("click", "#EditTipoPartida", ()=>{
            const pData = {
                tipoPartidaId: $("#tipoPartidaId").val(),
                nombrePartida: $("#editnombrePartida").val(),
                abreviacion: $("#editabreviacion").val(),
                descripcion: $("#editdescripcion").val()
            }
            $.ajax({
                url: "../../backend/TipoPartida/Edit/EditTipoPartida.php",
                data: pData,
                type: "POST",
                success: function(response){
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualización exitosa!',
                        text: 'Los cambios se han guardado correctamente.',
                        confirmButtonText: 'Aceptar'
                    });
                    $("#render").load("./load/adminTipoPartidas.php");
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
            
        }) 
    }

    $('#tablaTipoPartida').on('click', 'button.btn-deletetipopartidas', function () {
        var data = $('#tablaTipoPartida').DataTable().row($(this).parents('tr')).data();
        var id = data.tipoPartidaId;
    
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
                $.post("../../backend/TipoPartida/delete/deleteTipoPartida.php", { id }, function(response) {
                    if (response.success) {
                        $('#tablaTipoPartida').DataTable().ajax.reload();
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
    
    




    



