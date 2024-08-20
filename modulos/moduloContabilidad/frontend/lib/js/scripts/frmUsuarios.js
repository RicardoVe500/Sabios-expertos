$(document).ready(function(){
    // Función para cargar roles desde el servidor
    function cargarRoles() {
        $.ajax({
            type: 'GET',
            url: '../../backend/roles/Get/obtenerRoles.php', // Ruta al archivo PHP que obtiene los roles
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var rolesSelect = $("#rol");
                    rolesSelect.empty();
                    rolesSelect.append('<option value="">Seleccione un rol</option>');
                    response.data.forEach(function(rol) {
                        rolesSelect.append('<option value="' + rol.tipoUsuarioId + '">' + rol.nombreTipo + '</option>');
                    });
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

    // Llamar a la función para cargar los roles al cargar el formulario
    cargarRoles();

    $("#guardarDatos").click(function(event) {
        event.preventDefault();

        var nombre = $("#nombre").val();
        var apellidos = $("#apellidos").val();
        var email = $("#email").val();
        var clave = $("#clave").val();
        var rol = $("#rol").val();

        if (!nombre || nombre.trim() === "" || /\d/.test(nombre)) {
            Swal.fire({icon: 'warning', title: 'Nombre inválido', text: 'El nombre es requerido y no debe contener números.'});
        } else if (!apellidos || apellidos.trim() === "" || /\d/.test(apellidos)) {
            Swal.fire({icon: 'warning', title: 'Apellidos inválidos', text: 'Los apellidos son requeridos y no deben contener números.'});
        } else if (!email || email.trim() === "") {
            Swal.fire({icon: 'warning', title: 'Campo vacío', text: 'Debes ingresar el correo.'});
        } else if (!clave || clave.trim() === "") {
            Swal.fire({icon: 'warning', title: 'Campo vacío', text: 'Debes ingresar la contraseña.'});
        } else if (!rol) {
            Swal.fire({icon: 'warning', title: 'Campo vacío', text: 'Debes seleccionar un rol.'});
        } else {
            var datos = {
                nombre: nombre,
                apellidos: apellidos,
                email: email,
                clave: clave,
                rol: rol
            };

            $.ajax({
                type: 'POST',
                url: '../../backend/usuarios/Add/addUsuario.php', // Ruta al archivo PHP que guarda los datos del usuario
                data: datos,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registro exitoso',
                            text: response.message
                        }).then(() => {
                            $("#nombre").val('');
                            $("#apellidos").val('');
                            $("#email").val('');
                            $("#clave").val('');
                            $("#rol").val('');
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'Error', text: response.message});
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({icon: 'error', title: 'Error', text: 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.'});
                }
            });
        }
    });

    $("#regresar").click(function(){
        $("#render").load("load/adminUsuarios.php"); // Ruta para regresar al listado de usuarios
    });
});

