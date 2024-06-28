<?php
include("../../../../../lib/config/conect.php");
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario'];

if(isset($_POST['monthYearPicker'])){

  $monthYearPicker = $_POST["monthYearPicker"];
  // Se establece como predeterminado que sea el periodo abierto
  $estadoId = 1;

 //Se divide el dato ya que viene en yy/mm/dd
  list($mes, $anio) = explode('/', $monthYearPicker);

  $fechaHoraActual = date("Y-m-d H:i:s"); 
   
  //Se hace este select para verificar si no hay un periodo ya registrado con el mismo mes y año
   $checkQuery = "SELECT * FROM periodo WHERE anio = '$anio' AND mes = '$mes'";
   $checkResult = mysqli_query($con, $checkQuery);

   if (mysqli_num_rows($checkResult) > 0) {
       echo "Ya esta registrado";
   } else {
      //Se hace la insercion
       $query = "INSERT INTO periodo(anio, mes, estadoId, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
                 VALUES ('$anio', '$mes', '$estadoId', '$usuario_sesion ', '$fechaHoraActual', '$usuario_sesion ', '$fechaHoraActual')";
       $result = mysqli_query($con, $query);

       if (!$result) {
           echo "ERROR EN LA CONSULTA";
       } else {
           echo "Todo Bien";
       }
   }
   exit;
  }

?>
