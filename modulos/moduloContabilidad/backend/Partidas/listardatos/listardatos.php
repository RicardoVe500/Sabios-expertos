<?php
include("../../../../../lib/config/conect.php");

$tipoPartidaId = $_POST["tipoPartidaId"];

$query = "SELECT 
p.partidaId, 
p.tipoPartidaId,
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
p.fechaModifica
FROM 
partidas p
LEFT JOIN 
tipoPartida tp ON p.tipoPartidaId = tp.tipoPartidaId
LEFT JOIN 
estado e ON p.estadoId = e.estadoId
WHERE 
p.tipoPartidaId LIKE '$tipoPartidaId%'";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $data = array();

    while ($row = mysqli_fetch_array($result)) {
        $data[] = array(
            
            "partidaId"=>$row["partidaId"],
            "tipoPartidaId"=>$row["tipoPartidaId"],
            "nombrePartida"=>$row["nombrePartida"],
            "estadoId"=>$row["estadoId"],
            "estado"=>$row["estado"],
            "codigoPartida"=>$row["codigoPartida"],
            "fechacontable"=>$row["fechacontable"],
            "fechaActual"=>$row["fechaActual"],
            "concepto"=>$row["concepto"],
        
        );
    }

    echo json_encode(array("data" => $data));


?>
