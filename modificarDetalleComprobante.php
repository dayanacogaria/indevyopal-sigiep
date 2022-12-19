<?php
require_once '../Conexion/conexion.php';
session_start();
$id=$_POST['id'];
$cuenta=$_POST['cuenta'];
$tercero=$_POST['tercero'];
$centroC=$_POST['centroC'];
$protec=$_POST['proyecto'];
$debito=$_POST['debito'];
$credito=$_POST['credito'];
$valor = 0;
$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $id";
$rs = $mysqli->query($sql);
$nat = mysqli_fetch_row($rs);
$natural = $nat[0];
#naturaleza 1 Debito, 2 credito
if (empty($_POST['debito']) || $_POST['debito']=='0') {
    if ($_POST['credito'] != '""' || $_POST['credito'] != '0') {
        if ($nat[0] == 1) {
            if($_POST['credito'] < 0){ 
                $valor ='"'.$mysqli->real_escape_string(''.$_POST['credito'].'').'"'; 
            }elseif($_POST['credito'] > 0){ 
                $valor ='"'.$mysqli->real_escape_string('-'.$_POST['credito'].'').'"'; 
            }             
        }else{
            if ($_POST['credito'] > 0) {
                $valor ='"'.$mysqli->real_escape_string('-'.$_POST['credito'].'').'"'; 
            }else{
                $valor ='"'.$mysqli->real_escape_string(''.$_POST['credito'].'').'"'; 
            }
        }
        
    }
}
if (empty($_POST['credito']) || $_POST['credito']=='0') {
    if($_POST['debito'] != '""' || $_POST['credito'] != '0'){
        if ($nat[0]==2) {
            if ($_POST['debito'] < 0) {
                $valor =  '"'.$mysqli->real_escape_string('-'.$_POST['debito'].'').'"';
            }else if ($_POST['debito'] > 0){
                $valor =  '"'.$mysqli->real_escape_string(''.$_POST['debito'].'').'"';
            }            
        }else{
           $valor =  '"'.$mysqli->real_escape_string(''.$_POST['debito'].'').'"';
        }        
    }
}


 $sql = "UPDATE gf_detalle_comprobante SET valor=$valor,cuenta=$cuenta,tercero=$tercero,proyecto=$protec,centrocosto=$centroC WHERE id_unico=$id";
    $rs = $mysqli->query($sql);
    echo json_encode($rs);    

?>
