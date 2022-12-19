<?php
  require_once('../Conexion/conexion.php');
  session_start();
$anno = $_SESSION['anno'];
  $id = $_GET['p1'];
  $tipoActi = $_GET['p2'];
  $cuenta = $_GET['p3'];
  $cuentaA = $_GET["p4"];
  
  if($cuenta !='0'){
      $rs= "UPDATE gf_cuenta_bancaria SET cuenta ='$cuenta' WHERE id_unico ='$tipoActi'";
      $rs= $mysqli->query($rs);
  }
 
if($tipoActi == $cuentaA ){
  $updateSQL = "UPDATE gf_cuenta_bancaria_tercero SET cuentabancaria = '$tipoActi' WHERE tercero = '$id' AND cuentabancaria = '$cuentaA'";
 $resultado = $mysqli->query($updateSQL);
 if($resultado ==true){
     $resultado =1;
 } else {
     $resultado =2;
 }
} else {
    $bus= "Select * from gf_cuenta_bancaria_tercero WHERE cuentabancaria ='$tipoActi' AND parametrizacionanno = $anno ";
    $bus=$mysqli->query($bus);
    $bus= mysqli_num_rows($bus);
    if($bus>0){
        $resultado=3;
    } else{
        $updateSQL = "UPDATE gf_cuenta_bancaria_tercero SET cuentabancaria = '$tipoActi' WHERE tercero = '$id' AND cuentabancaria = '$cuentaA'";
        $resultado = $mysqli->query($updateSQL);
        if($resultado ==true){
            $resultado =1;
        } else {
            $resultado =2;
        }
    }
}
echo json_encode($resultado);
  
?>
