//Login

//Boton de carga para registro AJAX
$(function() {
    $("#frmIngresar").click(function(event) {//Boton de click
        event.preventDefault(); // Evitar el comportamiento por defecto (en este caso, la recarga de la página)

        // Validaciones
        if ($("#email").val().trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Debes ingresar el correo'
            });
            return; // Detener la ejecución si el campo está vacío
        }

        if ($("#clave").val().trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Debes ingresar la contraseña'
            });
            return; // Detener la ejecución si el campo está vacío
        }

        // Si todas las validaciones pasan, enviar los datos a través de AJAX

        // Obtener los datos del formulario
        var email = $("#email").val();
        var clave = $("#clave").val();

        // Construir el objeto de datos que deseas enviar al servidor
        var datos = {
            email: email,
            clave: clave
        };

        // Enviar una solicitud AJAX al archivo "login.php" en el backend
        $.ajax({
            type: 'POST',
            url: 'lib/config/validarLogin.php', // Asegúrate de que esta ruta sea correcta
            data: datos,
            dataType: 'json', // Esperar una respuesta JSON del servidor
            success: function(response) {

           /*console.log(response); //Verificar errores de conexion */

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
                        title: 'Inicio de Sesión exitoso',
                        text: response.message
                    }).then(() => {
                        // Redirigir a la página principal después del inicio de sesión exitoso
                        window.location.href = 'modulos/moduloContabilidad/frontend/files/app.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error. Por favor, inténtelo de nuevo.'
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
    });
});


/* //Carga para iniciar sesion (Este boton se encuentra en register.php)
$(function() {
    $("#enviarRegistro").click(function(event) {//Boton de click
        event.preventDefault();

        // Obtener el valor de inicio de sesión (nos ayuda a migrar nuestros datos de registro a login)
        var inicio = $("#frmIngresar").val();

        $.ajax({
            type: 'POST',
            url: '../login/login.php',
            data: { inicio: inicio },
            success: function(response) {
                // Mostrar el mensaje de SW (carga)
                Swal.fire({
                    title: "Cambiando a Registro",
                    html: "Cargando...",
                    timer: 300, // Ajusta el tiempo en milisegundos si es necesario
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        // Redirigir a la página de inicio de sesión después de que se cierre el SweetAlert
                        window.location.href = '../register/register.php';
                    }
                });
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.'
                });
            }
        });
    });
}); */
