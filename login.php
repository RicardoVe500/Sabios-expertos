<!doctype html>
<html lang="es">

<head>
    <title>Inicio de Sesión</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="lib/css/lato.css" rel="stylesheet">
    <link href="lib/css/fontawesomeicons.css" rel="stylesheet">



    <link href="lib/css/bootstrap4.css" rel="stylesheet">
    <link rel="stylesheet" href="lib/css/style.css">



    <link href="lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">


    <link href="lib/css/select2.css" rel="stylesheet">
    <link href="lib/css/themebootstrapselect2.css" rel="stylesheet">
    <link href="lib/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="fondo">
    <section class="ftco-section">

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <!-- Cambia las clases para ajustar el tamaño -->
                <div class="card">
                    <div class="wrap d-md-flex flex-column align-items-center">
                        <div class="wrap p-4 p-lg-5">
                            <div class="d-flex">
                                <div class="w-100 mb-5">
                          <h4 class="text-center mb-3">¡Bienvenidos!</h4>
                                    <img src="./lib/img/loginlogo.png" alt=""
                                        style="width: 100%; max-width: 250px; height: auto;" class="mx-auto d-block">
                                </div>
                            </div>
                            <form class="signin-form" id="frmLogin">
                                <div class="form-group mb-3">
                                    <label class="label" for="email">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Correo Electrónico">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="clave">Contraseña</label>
                                    <input type="password" class="form-control" id="clave" name="clave"
                                        placeholder="Contraseña">
                                    <span id="togglePassword" class="toggle-clave">👁️‍🗨️</span>
                                    <!-- Se encuentra en Style.css muy abajo -->
                                </div>
                                <div class="form-group" id="frmEntrar">
                                    <button type="submit" class="form-control btn btn-primary submit px-3"
                                        id="frmIngresar">Ingresar</button>
                                </div>
                                <div class="form-group d-md-flex">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>


    <!-- jQuery -->
    <script src="lib/vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap JS Bundle (Incluye Popper) -->
    <script src="lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="lib/js/sweetalert2.js"></script>

    <!-- Scripts Personalizados -->
    <script src="lib/js/sb-admin-2.min.js"></script>
    <script src="modulos/moduloContabilidad/frontend/lib/js/login/login.js"></script>
    <script src="modulos/moduloContabilidad/frontend/lib/js/login/popper.js"></script>
    <script src="modulos/moduloContabilidad/frontend/lib/js/login/bootstrap.min.js"></script>
    <script src="modulos/moduloContabilidad/frontend/lib/js/login/jquery.min.js"></script>
    <script src="modulos/moduloContabilidad/frontend/lib/js/login/main.js"></script>
    <script src="modulos/moduloContabilidad/frontend/lib/js/scripts/frmVisualizar.js"></script>



</body>

</html>