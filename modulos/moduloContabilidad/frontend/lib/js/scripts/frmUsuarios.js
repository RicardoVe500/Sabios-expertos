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



















/* $(document).ready(function(){

    $("#regresar").click(function(){
        regresar();
    });

    $("#guardarDatos").click(function(event) {
        event.preventDefault(); // Evitar el comportamiento por defecto (en este caso, la recarga de la página)

        // Obtener los datos del formulario
        var id = $("#userId").val();
        var nombre = $("#nombre").val();
        var apellidos = $("#apellidos").val();
        var email = $("#email").val();
        var clave = $("#clave").val();

        // Validaciones
        if (!nombre || nombre.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Debes ingresar el nombre'
            });
        } else if (!apellidos || apellidos.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Debes ingresar los apellidos'
            });
        } else if (!email || email.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Debes ingresar el correo'
            });
        } else if (!clave || clave.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Debes ingresar la contraseña'
            });
        } else if (/\d/.test(nombre)) {
            Swal.fire({
                icon: 'warning',
                title: 'Nombre inválido',
                text: 'El nombre no debe contener números'
            });
        } else if (/\d/.test(apellidos)) {
            Swal.fire({
                icon: 'warning',
                title: 'Apellidos inválidos',
                text: 'Los apellidos no deben contener números'
            });
        } else {
            // Si todas las validaciones pasan, enviar los datos a través de AJAX
                
                // Obtener los datos del formulario
                var id = $("#userId").val();
                var nombre = $("#nombre").val();
                var apellidos = $("#apellidos").val();
                var email = $("#email").val();
                var clave = $("#clave").val();

                // Construir el objeto de datos que deseas enviar al servidor
                var datos = {
                    id: id,
                    nombre: nombre,
                    apellidos: apellidos,
                    email: email,
                    clave: clave
                };

            // Enviar una solicitud AJAX al archivo "editUsuario.php" en el backend
            $.ajax({
                type: 'POST',
                url: '../../backend/addUsuario.php', // Asegúrate de que esta ruta sea correcta
                data: datos,
                dataType: 'json', // Esperar una respuesta JSON del servidor
                success: function(response) {
                    // Manejar la respuesta del servidor  
                    if (response.status === 'error') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    } else if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualización exitosa',
                            text: response.message
                        }).then(() => {
                            // Limpiar los campos del formulario
                            $("#nombre").val('');
                            $("#apellidos").val('');
                            $("#email").val('');
                            $("#clave").val('');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al actualizar los datos.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Manejar errores de la solicitud AJAX
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.'
                    });
                }
            });
        }
    });
});


function regresar(){
    $("#render").load("load/adminUsuarios.php");
} */