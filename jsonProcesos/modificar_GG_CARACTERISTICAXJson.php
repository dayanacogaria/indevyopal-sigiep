<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['id'].'');
  $valor  = $mysqli->real_escape_string(''.$_POST['valor'].'');
  
  $queryUA="SELECT descripcion, proceso, caracteristica FROM gg_caracteristica_x "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
  $queryU="SELECT * FROM gg_caracteristica_x "
          . "WHERE proceso='$numA[1]' AND caracteristica='$numA[2]' AND descripcion = '$valor'";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
  
   if($numA[0]==$valor){
        $update = "UPDATE gg_caracteristica_x "
              . "SET descripcion='$valor'"
              . "WHERE id_unico = '$id'";
        $resul = $mysqli->query($update);
        $resultado = true;
  } else {
        if($num == 0)
        {
        $update = "UPDATE gg_caracteristica_x "
              . "SET descripcion='$valor' "
              . "WHERE id_unico = '$id'";
         $resul = $mysqli->query($update);
         
         $resultado =true;
         } else {
             if($num > 0){
                 $resultado ='3';
             }else {
                 $resultado= false;
             }
         } 
  }
  echo json_encode($resultado);
?>