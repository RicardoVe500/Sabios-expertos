$(document).ready(function() {
    // Cargar roles al cargar la página
    cargarRoles();

    // Manejar el formulario de agregar rol
    $('#agregar-rol-form').on('submit', function(e) {
        e.preventDefault();
        
        var nombreTipo = $('#nombreTipo').val();
        var descripcion = $('#descripcion').val();
        
        if (!nombreTipo || !descripcion) {
            Swal.fire('Advertencia', 'El campo "Nombre" y "Descripción" son obligatorios', 'warning');
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: '../../backend/roles/Add/addRoles.php',
            data: JSON.stringify({ nombreTipo: nombreTipo, descripcion: descripcion }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Éxito', 'Rol agregado correctamente', 'success');
                    cargarRoles();
                    $('#agregar-rol-form')[0].reset();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX: ', status, error);
                Swal.fire('Error', 'No se pudo agregar el rol. Error en la solicitud.', 'error');
            }
        });
    });

    // Función para cargar roles desde el backend
    function cargarRoles() {
        $.ajax({
            type: 'GET',
            url: '../../backend/roles/Get/obtenerRoles.php',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var rolesList = $('#roles-list');
                    rolesList.empty();
                    response.data.forEach(function(rol) {
                        var rolRow = `
                            <tr>
                                <td>${rol.nombreTipo}</td>
                                <td>${rol.descripcion}</td>
                                <td>
                                    <button type="button" class="deleteRol btn btn-danger btn-sm" data-nombretipo="${rol.nombreTipo}">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>`;
                        rolesList.append(rolRow);
                    });
                } else {
                    Swal.fire('Error', 'No se pudieron cargar los roles', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX: ', status, error);
                Swal.fire('Error', 'No se pudieron cargar los roles. Error en la solicitud.', 'error');
            }
        });
    }

    // Manejar la eliminación de rol
    $(document).on('click', '.deleteRol', function() {
        var nombreTipo = $(this).data('nombretipo');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '../../backend/roles/Delete/deleteRoles.php',
                    data: JSON.stringify({ nombreTipo: nombreTipo }),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Eliminado!', response.message, 'success');
                            cargarRoles();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud AJAX: ', status, error);
                        Swal.fire('Error', 'No se pudo eliminar el rol. Error en la solicitud.', 'error');
                    }
                });
            }
        });
    });
});