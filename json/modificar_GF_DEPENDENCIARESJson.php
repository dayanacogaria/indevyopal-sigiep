<?php
require_once '../Conexion/conexion.php';
session_start();
$respA= $_GET['respA'];
$respo=$_GET['respo'];
$depend=$_GET['depend'];
$estadA=$_GET['estadA'];
$est=$_GET['est'];
$movA=$_GET['movA'];
$movi=$_GET['movi'];

$queryU="SELECT dependencia FROM gf_dependencia_responsable WHERE dependencia = $depend AND responsable = $respo AND movimiento=$movi AND estado = $est";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($respA==$respo && $estadA == $est && $movA == $movi)
  {
   $sql = "UPDATE gf_dependencia_responsable SET dependencia=$depend,responsable=$respo,movimiento=$movi, estado=$est WHERE dependencia='$depend' AND responsable='$respA AND estado=$estadA, AND movimiento =$movA '";

	$result = $mysqli->query($sql);
   }
  else
  {
  	if($num == 0)
	  { 
		$sql = "UPDATE gf_dependencia_responsable SET dependencia=$depend,responsable=$respo,movimiento=$movi, estado=$est WHERE dependencia='$depend' AND responsable='$respA AND estado=$estadA, AND movimiento =$movA '";

		$result = $mysqli->query($sql);

	  } else {
	  	if($num>0){
	  		$result='3';
	  		
	  	} else {
	  	$result=false;
	  }
	}
}


echo json_encode($result);
?>