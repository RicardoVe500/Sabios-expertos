<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['tipoPartidaId'])) {

  $tipoPartidaId = $_POST['tipoPartidaId'];

  $query = "SELECT 
                p.partidaId, 
                tp.nombrePartida, 
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
                p.tipoPartidaId LIKE '$tipoPartidaId%';";

  $result = mysqli_query($con, $query);

  if (!$result) {
      die("Error en la consulta".mysqli_error($con));
      
  }
  
  $data = array();

  while ($row = mysqli_fetch_array($result)) {
      $data[] = array(

            "partidaId"=>$row["partidaId"],
            "codigoPartida"=>$row["codigoPartida"],
            "nombrePartida"=>$row["nombrePartida"],
            "estado"=>$row["estado"],
            "concepto"=>$row["concepto"],
            "fechacontable"=>$row["fechacontable"],
            "fechaActual"=>$row["fechaActual"],
      );
  }
  echo json_encode(array("data" => $data));

}else{
  echo "no se recibio nada";
}

?>
