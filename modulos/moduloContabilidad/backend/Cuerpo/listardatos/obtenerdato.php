<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['partidaId'])) {
$partidaId = $_POST["partidaId"];

$query = "SELECT 
        p.partidaId, 
        tp.nombrePartida,
        p.estadoId,
        e.estado, 
        p.codigoPartida, 
        p.fechacontable, 
        p.fechaActual, 
        p.concepto, 
        p.usuarioAgrega, 
        p.fechaAgrega, 
        p.usuarioModifica, 
        p.fechaModifica,
        p.debe,
        p.haber,
        p.diferencia
        FROM 
        partidas p
        LEFT JOIN 
        tipoPartida tp ON p.tipoPartidaId = tp.tipoPartidaId
        LEFT JOIN 
        estado e ON p.estadoId = e.estadoId
        WHERE 
        p.partidaId = $partidaId ";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $json = array();


    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(
            
            "partidaId"=>$row["partidaId"],
            "nombrePartida"=>$row["nombrePartida"],
            "estadoId"=>$row["estadoId"],
            "estado"=>$row["estado"],
            "codigoPartida"=>$row["codigoPartida"],
            "fechacontable"=>$row["fechacontable"],
            "fechaActual"=>$row["fechaActual"],
            "concepto"=>$row["concepto"],
            "debe"=>$row["debe"],
            "haber"=>$row["haber"],
            "diferencia"=>$row["diferencia"],

        );
    }

    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
  }