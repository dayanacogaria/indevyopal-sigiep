<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID e instrucción SQL para su eliminación de la tabla gf_perfil_tercero.
   $id = $_GET['id'];
   if(empty($_GET['cta'])){
    $query = "DELETE FROM gf_cuenta_bancaria_tercero WHERE cuentabancaria = '$id'";
    $resultado = $mysqli->query($query);
   } else {
       $cuenta ="UPDATE gf_cuenta_bancaria SET cuenta = NULL WHERE id_unico = '$id'";
       $cuenta = $mysqli->query($cuenta);
    $query = "DELETE FROM gf_cuenta_bancaria_tercero WHERE cuentabancaria = '$id'";
    $resultado = $mysqli->query($query);
    
   }

  echo json_encode($resultado);
?>