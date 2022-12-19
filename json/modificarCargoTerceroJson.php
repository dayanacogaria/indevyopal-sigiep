<?php
  require_once('../Conexion/conexion.php');
  session_start();

  $tercero = $_GET['p1'];
  $cargo = $_GET['p2'];
  $cargoA = $_GET['cargoAn'];

  //Verificar cuantos cargos estan la ocupados
  $queryU = "SELECT COUNT(c.cargo) FROM gf_cargo_tercero c WHERE c.cargo= $cargo";
  $car = $mysqli->query($queryU);
  $rowCargo = mysqli_fetch_row($car);
  $numCa= $rowCargo[0];

  //Traer el numero de plazas que tiene el cargo
  $numP = "SELECT numero_plazas FROM gf_cargo WHERE id_unico = $cargo";
  $numCar = $mysqli->query($numP);
  $rownumCar = mysqli_fetch_row($numCar);
  $numPlazas= $rownumCar[0];

  // Preguntar si ya esta
  $query = "SELECT c.cargo FROM gf_cargo_tercero c WHERE c.cargo= $cargo AND c.tercero= $tercero";
  $ya = $mysqli->query($query);
  $rowya = mysqli_num_rows($ya);



  if($numCa<$numPlazas){
    if($rowya<=0){
      $updateSQL = "UPDATE gf_cargo_tercero SET cargo = '$cargo' WHERE tercero = '$tercero' AND cargo='$cargoA'";
  		$resultado = $mysqli->query($updateSQL);
    $resultado='1';
    } else {
      $resultado='2';
    }
    
  } else {
    $resultado = '3';
  }
  

  echo json_encode($resultado);
  
?>
