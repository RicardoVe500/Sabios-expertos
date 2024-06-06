<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST["tipoPartidaId"])) {
   
        $tipoPartidaId = $_POST["tipoPartidaId"];
        $nombrePartida = $_POST["nombrePartida"];
        $abreviacion = $_POST["abreviacion"];
        $descripcion = $_POST["descripcion"];
        $fechaHoraActual = date("Y-m-d H:i:s"); 

        $query = "UPDATE tipoPartida SET nombrePartida = '$nombrePartida', abreviacion = '$abreviacion', descripcion = '$descripcion', fechaModifica = '$fechaHoraActual' 
        WHERE tipoPartidaId = $tipoPartidaId ";

        $result = mysqli_query($con, $query);
      
        if (!$result) {
          echo "Error en la consulta".mysqli_error($con);
            
        }
        
      }

