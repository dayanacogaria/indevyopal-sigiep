<?php 
  require_once('../Conexion/conexion.php');
  session_start();
   
$perfil = $_GET['id'];
$query = "SELECT td.nombre, pf.obligatorio FROM  gf_perfil_condicion pf  LEFT JOIN gf_condicion c ON pf.condicion= c.id_unico LEFT JOIN gf_tipo_dato td ON c.tipodato = td.id_unico WHERE pf.id_unico='$perfil'";
 $queryu = $mysqli->query($query);
 $row = mysqli_fetch_row($queryu);
$tipod= $row[0];
$oblig= $row[1];
$datos = array("tipo"=>$tipod,"obl"=>$oblig);

  echo json_encode($datos);
?>