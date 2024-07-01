<?php
include("../../../../../lib/config/conect.php");
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario'];

  $empresaId = $_POST["empresaId"];
  $nombreSucursal = $_POST["nombreSucursal"];
  $direccion = $_POST["direccion"];
  $correo = $_POST["correo"];
  $telefono = $_POST["telefono"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 

  
  $query = "INSERT INTO sucursal(empresaId, nombre, direccion, correo, telefono, usuarioAgrega , fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$empresaId', '$nombreSucursal','$direccion','$correo', $telefono, '$usuario_sesion', '$fechaHoraActual','$usuario_sesion','$fechaHoraActual')";

  $result = mysqli_query($con, $query);

  if (!$result) {
    echo "Error en la consulta".mysqli_error($con);

  }else{

    $fechajson = date("Y-m-d");
    // Preparar datos para la bitácora
    $datos = [
      "accion" => "Registro_Sucursal",
      "Usuario agrega" => $usuario_sesion,
      "datosIngresados" => [
          "Nombre Empresa" => $nombreSucursal,
          "Direccion" => $direccion,
          "Correo" => $correo,
          "Telefono" => $telefono,

      ]
  ];

  $jsonDatos = json_encode($datos);

  // Verificar si ya existe un registro para el día actual
  $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechajson'";
  $resultBitacora = mysqli_query($con, $queryBitacora);
  if ($row = mysqli_fetch_assoc($resultBitacora)) {
      // Actualiza el registro existente
      $datosExistentes = json_decode($row["detalle"], true);
      $datosExistentes[] = $datos;
      $jsonDatos = json_encode($datosExistentes);
      $updateQuery = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
      mysqli_query($con, $updateQuery);
  } else {
      // Crea un nuevo registro en la bitácora
      $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechajson', '$jsonDatos')";
      mysqli_query($con, $insertQuery);
  }
  }
