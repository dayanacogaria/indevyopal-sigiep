<?php
  require_once('../Conexion/conexion.php');
  session_start();

  $tercero = $_GET['p1'];
  $perfil = $_GET['p2'];
  if($_GET['p3']==" " || $_GET['p3']==NULL || $_GET['p3']=='NULL' ||$_GET['p3']==''){
    $valor = NULL;
  }else {
    $valor =$_GET['p3'];
  }
  $perfilA = $_GET['p4'];


 $queryU="SELECT perfilcondicion FROM gf_condicion_tercero WHERE perfilcondicion = $perfil AND tercero = $tercero";
  $car = $mysqli->query($queryU);
$num=mysqli_num_rows($car);

if($perfil==$perfilA){
 $updateSQL = "UPDATE gf_condicion_tercero SET perfilcondicion = $perfilA, valor='$valor' WHERE tercero = $tercero AND perfilcondicion=$perfilA";
 $resultado = $mysqli->query($updateSQL);

  echo json_encode($resultado);
} else {

  if($num == 0)
  {

    $updateSQL = "UPDATE gf_condicion_tercero SET perfilcondicion = '$perfil', valor='$valor' WHERE tercero = '$tercero' AND perfilcondicion='$perfilA'";
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
