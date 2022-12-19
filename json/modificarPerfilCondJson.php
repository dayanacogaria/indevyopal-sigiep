<?php
  require_once('../Conexion/conexion.php');
  session_start();

$id = $_GET['p1'];
$perfil = $_GET['p2'];
$condicion = $_GET['p3'];
$obligatorio = $_GET['p4'];

$queryU="SELECT * FROM gf_perfil_condicion WHERE perfil = $perfil AND condicion = $condicion AND obligatorio=$obligatorio";
  $car = $mysqli->query($queryU);
 $num=mysqli_num_rows($car);

$queryB = "SELECT perfil, condicion, obligatorio FROM gf_perfil_condicion WHERE id_unico = $id";
$queryBus = $mysqli->query($queryB);
$busqu = mysqli_fetch_row($queryBus);
$perfilA = $busqu[0];
$condicionA = $busqu[1];
$obligatorioA = $busqu[2];
if($perfil==$perfilA && $condicion ==$condicionA && $obligatorio==$obligatorioA){
 $updateSQL = "UPDATE gf_perfil_condicion SET perfil = $perfil, condicion=$condicion, obligatorio= $obligatorio WHERE id_unico=$id";
 $resultado = $mysqli->query($updateSQL);

  echo json_encode($resultado);
} else {

  if($num == 0)
  {

    $updateSQL = "UPDATE gf_perfil_condicion SET perfil = $perfil, condicion=$condicion, obligatorio= $obligatorio WHERE id_unico=$id";
 $resultado = $mysqli->query($updateSQL);

  echo json_encode($resultado);

   }
  else
  {
    $resultado = '3';
    echo json_encode($resultado);
  }
}
?>
