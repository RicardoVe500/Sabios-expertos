<?php
include("../../../../../lib/config/conect.php");
if (!empty($_FILES['file']['name'])) {
    $fileTmpPath = $_FILES['file']['tmp_name'];
    require '../../../../../lib/vendor/autoload.php'; // Asegúrate de que PhpSpreadsheet está instalado
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmpPath);
    $data = $spreadsheet->getActiveSheet()->toArray();

    // Preparar y ejecutar la consulta para insertar los datos
    foreach ($data as $row) {
         // Aquí deberías adaptar los nombres de las columnas y el número de columnas según tu tabla 'catalogoCuentas'
         $movimientoId = mysqli_real_escape_string($con, $row[0]);
         $numeroCuenta = mysqli_real_escape_string($con, $row[1]);
         $cuentaDependiente = mysqli_real_escape_string($con, $row[2]);
         $nivelCuenta = mysqli_real_escape_string($con, $row[3]);
         $nombreCuenta = mysqli_real_escape_string($con, $row[4]);
 
         $query = "INSERT INTO catalogoCuentas (movimientoId, numeroCuenta, cuentaDependiente, nivelCuenta, nombreCuenta) VALUES ('$movimientoId', '$numeroCuenta', '$cuentaDependiente', '$nivelCuenta', '$nombreCuenta')";
         if (!mysqli_query($con, $query)) {
             echo "Error al insertar datos: " . mysqli_error($con);
         }
     }
     echo "Datos insertados correctamente.";
 } else {
     echo "No se ha subido ningún archivo.";
 }

