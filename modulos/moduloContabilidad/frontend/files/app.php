<?php  #include("../../../../lib/config/verificarSesion.php");?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="./../../../../lib/img/cropped-Asset-5.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Sabios y expertos</title>

    <!-- Bootstrap CSS -->
    <link href="./../../../../lib/css/bootstrap4.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="../../../../lib/css/datatablescss.css">
    <link rel="stylesheet" href="../../../../lib/css/datatablesbuttons.css">
    <link rel="stylesheet" href="../../../../lib/css/datatableselectcss.css">
    <link rel="stylesheet" href="../../../../lib/css/datepickercss.css">



    <!-- FontAwesome Icons -->
    <link href="../../../../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Google Fonts -->
    <link href="../../../../lib/css/fontawesomeicons.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../../../../lib/css/select2.css" rel="stylesheet">
    <link href="../../../../lib/css/themebootstrapselect2.css" rel="stylesheet">
    <link href="../../../../lib/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<div id="wrapper">
    <?php include("../contenido/menu.php");?>
    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">
            <?php include("../contenido/header.php");?>
            <?php include("modalperiodo.php");?>
            <div class="container-fluid">
                <div class="copyright my-auto" id="render">
                    <!-- Este div es el que renderiza todos los formularios -->
                </div>
            </div>
        </div>


        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Your Website 2024</span>
                </div>
            </div>
        </footer>


    </div>

</div>


<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


<!-- jQuery -->
<script src="../../../../lib/vendor/jquery/jquery.min.js"></script>
<script src="../../../../lib/js/Jquery.js"></script>

<!-- Bootstrap JS Bundle (Incluye Popper) -->
<script src="../../../../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables (Asegúrate de que estén después de jQuery y antes de inicializar cualquier tabla en tus scripts personalizados) -->

<script src="../../../../lib/js/datatables/datatables.js"></script>
<script src="../../../../lib/js/datatables/databootstrap.js"></script>
<script src="../../../../lib/js/datatables/datatablesbuttoms.js"></script>
<script src="../../../../lib/js/datatables/buttonsbootstrap4datatables.js"></script>
<script src="../../../../lib/js/datatables/selectdatatables.js"></script>

<!-- SweetAlert2 -->
<script src="../../../../lib/js/sweetalert2.js"></script>

<!-- Select2 -->
<script src="../../../../lib/js/select2.js"></script>
<script src="../../../../lib/js/datepickerjs.js"></script>
<script src="../../../../lib/js/datepickerespanol.js"></script>


<!-- Scripts Personalizados -->
<script src="../../../../lib/js/sb-admin-2.min.js"></script>
<script src="../lib/js/menu.js"></script>
<script src="../lib/js/periodo.js"></script>


</body>

</html>