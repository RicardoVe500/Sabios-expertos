$(document).ready(function() {
    // Función para cargar roles desde el servidor
    function cargarRoles(callback) {
        $.ajax({
            type: 'GET',
            url: '../../backend/roles/Get/obtenerRoles.php', // Ruta al archivo PHP que obtiene los roles
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    callback(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los roles.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar los roles. Por favor, inténtalo de nuevo.'
                });
            }
        });
    }

    // Búsqueda de usuario
    $("#searchUser").click(function() {
        var email = $("#searchEmail").val();
        $.ajax({
            type: 'POST',
            url: '../../backend/usuarios/Search/buscarUsuario.php',
            data: { email: email },
            dataType: 'json',
            success: function(response) {
                var tbody = $("#userTableBody");
                tbody.empty();
                if (response.status === 'success') {
                    response.data.forEach(function(user) {
                        var row = "<tr>" +
                            "<td>" + user.nombre + "</td>" +
                            "<td>" + user.apellidos + "</td>" +
                            "<td>" + user.email + "</td>" +
                            "<td>" + (user.nombreTipo ? user.nombreTipo : 'Sin rol asignado') + "</td>" + // Mostrar 'Sin rol asignado' si el rol es null o undefined
                            "<td>" +
                                "<button class='btn btn-warning btn-sm frmEditUsuario' data-usuarioid='" + user.usuarioId + "' data-nombre='" + user.nombre + "' data-apellidos='" + user.apellidos + "' data-email='" + user.email + "' data-nombreTipo='" + user.nombreTipo + "'><i class='fa fa-edit'></i> Editar</button> " +
                                "<button class='btn btn-danger btn-sm deleteUser' data-usuarioid='" + user.usuarioId + "'><i class='fa fa-trash'></i> Eliminar   </button> " +
                            "</td>" +
                        "</tr>";
                        tbody.append(row);
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No encontrado',
                        text: 'No se encontraron usuarios.'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al buscar el usuario. Por favor, inténtalo de nuevo.'
                });
            }
        });
    });

    // Eliminar usuario
    $(document).on('click', '.deleteUser', function() {
        var userId = $(this).data('usuarioid');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '../../backend/usuarios/Delete/deleteUsuario.php',
                    data: { usuarioId: userId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Eliminado!',
                                response.message,
                                'success'
                            );
                            // Vuelve a buscar usuarios para actualizar la tabla
                            $("#searchUser").click();
                        } else {
                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la solicitud AJAX: ", status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al eliminar el usuario. Por favor, inténtalo de nuevo.'
                        });
                    }
                });
            }
        });
    });

    // Editar usuario
    $(document).on('click', '.frmEditUsuario', function() {
        var userId = $(this).data('usuarioid');
        var nombre = $(this).data('nombre');
        var apellidos = $(this).data('apellidos');
        var userEmail = $(this).data('email');
        var userTipoUsuarioId = $(this).data('tipousuarioid');

        // Obtener roles
        cargarRoles(function(roles) {
            var rolesOptions = roles.map(function(rol) {
                return `<option value="${rol.tipoUsuarioId}" ${userTipoUsuarioId == rol.tipoUsuarioId ? 'selected' : ''}>${rol.nombreTipo}</option>`;
            }).join('');

            Swal.fire({
                title: 'Editar Usuario',
                html: `
                    <input id="swal-input1" class="swal2-input" value="${nombre}" type="text" placeholder="Nuevo Nombre">
                    <input id="swal-input2" class="swal2-input" value="${apellidos}" type="text" placeholder="Nuevo Apellido">
                    <input id="swal-input3" class="swal2-input" value="${userEmail}" type="email" placeholder="Nuevo Correo">
                    <br><br>
                    <select id="swal-input4" class="swal2-input">${rolesOptions}</select>
                    <input id="swal-input5" class="swal2-input" type="password" placeholder="Confirmar Contraseña">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText:"Actualizar",
                confirmButtonColor: "#2FC11B",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancelar",
                
                preConfirm: () => {
                    const email = document.getElementById('swal-input3').value;
                // Validación de correo electrónico con expresión regular
                if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
                    Swal.showValidationMessage("Por favor, introduce un correo electrónico válido.");
                    return false;
                }
                    return [
                        document.getElementById('swal-input1').value,
                        document.getElementById('swal-input2').value,
                        document.getElementById('swal-input3').value,
                        document.getElementById('swal-input4').value,
                        document.getElementById('swal-input5').value
                    ]
                    
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var nombre = result.value[0];
                    var apellidos = result.value[1];
                    var email = result.value[2];
                    var rol = result.value[3];
                    var clave = result.value[4];

                    $.ajax({
                        type: 'POST',
                        url: '../../backend/usuarios/Edit/editUsuario.php',
                        data: { usuarioId: userId, nombre: nombre, apellidos: apellidos, email: email, rol: rol, clave: clave },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Actualizado!',
                                    response.message,
                                    'success'
                                );
                                // Vuelve a buscar usuarios para actualizar la tabla
                                $("#searchUser").click();
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error en la solicitud AJAX: ", status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al actualizar el usuario. Por favor, inténtalo de nuevo.'
                            });
                        }
                    });
                }
            });
        });
    });

    // Llamar a la función para cargar los roles al cargar la página
    cargarRoles(function() {
    });
});