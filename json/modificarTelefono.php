<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $response = 0;
  $id = $_GET['p1'];
  $tipoActi = $_GET['p2'];
  $valor = $_GET['p3'];
  $valorx = $_GET['p4'];
  if ($valor != $valorx){
	$queryU = "SELECT id_unico FROM gf_telefono WHERE valor = $valor";
	$tipot = $mysqli->query($queryU);
	$num = mysqli_num_rows($tipot);	
	if ($num == 0){
		$updateSQL = "UPDATE gf_telefono SET tipo_telefono = '$tipoActi', valor='$valor' WHERE id_unico = '$id'";
 		$resultado = $mysqli->query($updateSQL);
 		if ($resultado){
 			$response = 1;
 		}else {
 			$response = 0;
 		}
	}else {
		$response = 2;
	}
  }else{
  	$updateSQL = "UPDATE gf_telefono SET tipo_telefono = '$tipoActi', valor='$valor' WHERE id_unico = '$id'";
 	$resultado = $mysqli->query($updateSQL);
 	if ($resultado){
 			$response = 1;
 		}else {
 			$response = 0;
 		}
  }
  echo json_encode($response);
  
?>
