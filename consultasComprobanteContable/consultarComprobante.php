<?php
require_once '../Conexion/conexion.php';
session_start();
$numero = $_REQUEST['numero'];
$tipo = $_REQUEST['tipo'];
if(!(empty($numero))){
    $sql = "SELECT numero,id_unico from gf_comprobante_cnt WHERE numero = '$numero' AND tipocomprobante='$tipo' AND fecha != '2016-01-01'";
    $result = $mysqli->query($sql);
    $fila = $result->num_rows;
    $row = mysqli_fetch_row($result);
    if(empty($fila)){
        $_SESSION['num'] = "";
        $_SESSION['idNumeroC'] = "";
    }else{
        $_SESSION['num'] = $row[0];
        $_SESSION['idNumeroC'] = $row[1];                    
    }           
}else{
    $_SESSION['num'] = "";
    $_SESSION['idNumeroC'] = "";  
}
?>