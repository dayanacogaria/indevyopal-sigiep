<?php
#Modificado 10/02/2017 16:38 Ferney Pérez Cano // Agragada consulta a gf_detalle_comprobante y modificada la consulta gf_cuenta. Modificados los if de crédito y débito.
#Modificado 28/01/2017 11:46 Ferney Pérez Cano
require_once '../Conexion/conexion.php';
session_start();
$id=$_POST['id'];
//$cuenta=$_POST['cuenta'];
$tercero=$_POST['tercero'];
if(empty($_POST['centroC'])){
  $centroC ='NULL';
} else { 
$centroC=$_POST['centroC'];
}
if(empty($_POST['proyecto'])) {
  $protec  ='NULL';  
} else { 
    $protec=$_POST['proyecto'];
}
$debito=$_POST['debito'];
$credito=$_POST['credito'];
$valor = 0;

if(empty($_POST['cuenta'])) {
$cuenta = "SELECT cuenta FROM gf_detalle_comprobante WHERE id_unico =$id";
$cuenta = $mysqli->query($cuenta);
if(mysqli_num_rows($cuenta)>0){
    $cuenta = mysqli_fetch_row($cuenta);
    $cuenta=$cuenta[0];
    }else {
    $cuenta='';
}
} else {
    $cuenta = $_POST['cuenta'];
}
$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";

$rs = $mysqli->query($sql);
$nat = mysqli_fetch_row($rs);
$natural = $nat[0];
#naturaleza 1 débito, 2 crédito

  if(empty($_POST['debito'])) {
        if(empty($_POST['credito'])) { 
            $rs=2;
        } else {
            if ($nat[0] == 1) {
                $valor ='"'.$mysqli->real_escape_string('-'.$_POST['credito'].'').'"';
            }else{
                $valor ='"'.$mysqli->real_escape_string(''.$_POST['credito'].'').'"';
            }
        }

    } else {
        if ($nat[0]==2) {
            $valor =  '"'.$mysqli->real_escape_string('-'.$_POST['debito'].'').'"';
        }else{
            $valor =  '"'.$mysqli->real_escape_string(''.$_POST['debito'].'').'"';
        }   
    }


    $sql = "UPDATE gf_detalle_comprobante 
        SET valor = $valor, tercero = $tercero, proyecto = $protec, centrocosto = $centroC, cuenta = $cuenta 
        WHERE id_unico=$id";
    $rs = $mysqli->query($sql);
    echo json_encode($rs);    

?>
