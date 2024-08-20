<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST['monthYearPicker'])) {

    $monthYearPicker = $_POST["monthYearPicker"];
    $estadoId = 1; // Se establece como predeterminado que sea el periodo abierto
    list($mes, $anio) = explode('/', $monthYearPicker);
    $fechaHoraActual = date("Y-m-d H:i:s");

    mysqli_begin_transaction($con);

    $checkQuery = "SELECT * FROM periodo WHERE anio = '$anio' AND mes = '$mes'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'message' => 'Este Periodo ya está registrado']);
    } else {
        $query = "INSERT INTO periodo(anio, mes, estadoId, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
                 VALUES ('$anio', '$mes', '$estadoId', '$usuario_sesion', '$fechaHoraActual', '$usuario_sesion', '$fechaHoraActual')";
        $result = mysqli_query($con, $query);

        if (!$result) {
            mysqli_rollback($con);
            echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        } else {
            mysqli_commit($con);
            echo json_encode(['status' => 'success', 'message' => 'Periodo agregado exitosamente']);
        }
    }
    exit;
}
?>
