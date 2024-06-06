<?php
include("../../../../../lib/config/conect.php");


  $nombrePartida = $_POST["nombrePartida"];
  $abreviacion = $_POST["abreviacion"];
  $descripcion = $_POST["descripcion"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 

  
  $query = "INSERT INTO tipoPartida(nombrePartida, abreviacion, descripcion, usuarioAgrega , fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$nombrePartida','$abreviacion','$descripcion','','$fechaHoraActual','','$fechaHoraActual')";

  $result = mysqli_query($con, $query);

  if (!$result) {
    echo "Error en la consulta".mysqli_error($con);
      
  }


?>
