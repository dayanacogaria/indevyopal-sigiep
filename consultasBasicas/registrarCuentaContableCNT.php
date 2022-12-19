<?php
#########MODIFICACIONES ##############
#03/02/2017 || 11:00 ERICA G. //// AGREGAR CUENTA CONTABLE CAMPOS DEBITO Y CREDITO
#30/01/2017 ERICA G.
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
$con = new ConexionPDO();
session_start();

$compobantecnt = $_POST['comprobantecnt'];
#DESCRIPCION, FECHA
$sqlD = 'SELECT descripcion, fecha FROM gf_comprobante_cnt WHERE id_unico ='.$compobantecnt;
$f = $mysqli->query($sqlD);
$des = mysqli_fetch_row($f);
if(empty($des[0]) || $des[0] ==""){
    $descripcion = 'NULL';
} else {
    $descripcion = $des[0];
}
$fecha = $des[1];
//#Llenar por el usuario
//$valorEjec = "0";
$cuenta = '"'.$mysqli->real_escape_string(''.$_POST['sltcuenta'].'').'"';

if(!empty($cuenta)){
#BUSCAR NATURALEZA
$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
$rsc = $mysqli->query($sql);
$nat = mysqli_fetch_row($rsc);
$natural = $nat[0];

    if(empty($_POST['txtValorDebito'])) {
        if(empty($_POST['txtValorCredito'])) { 
            $rs=2;
        } else {
            if ($nat[0] == 1) {
                $valor ='"'.$mysqli->real_escape_string('-'.$_POST['txtValorCredito'].'').'"';
            }else{
                $valor ='"'.$mysqli->real_escape_string(''.$_POST['txtValorCredito'].'').'"';
            }
        }

    } else {
        if ($nat[0]==2) {
            $valor =  '"'.$mysqli->real_escape_string('-'.$_POST['txtValorDebito'].'').'"';
        }else{
            $valor =  '"'.$mysqli->real_escape_string(''.$_POST['txtValorDebito'].'').'"';
        }   
    }

    $compania   = $_SESSION['compania'];
    $anno       = $_SESSION['anno'];
    if(empty($_POST['slttercero'])){
        $bt = $con->Listar("SELECT * FROM gf_tercero WHERE compania = $compania AND numeroidentificacion = 9999999999");
        $tercero = $bt[0][0];
    }else{
        $tercero = '"'.$mysqli->real_escape_string(''.$_POST['slttercero'].'').'"';
    }
    if(empty($_POST['sltproyecto'])){
         $bp = $con->Listar("SELECT * FROM gf_proyecto WHERE compania = $compania AND nombre = 'Varios'");   
         if(count($bp)>0){
            $proyecto = $bp[0][0];
         } else {
             $proyecto = 'NULL';
         }
    }else{
        $proyecto = '"'.$mysqli->real_escape_string(''.$_POST['sltproyecto'].'').'"';
    }
    if(empty($_POST['sltcentroc'])){
        $bc = $con->Listar("SELECT * FROM gf_centro_costo WHERE parametrizacionanno = $anno AND nombre = 'Varios'");   
        $centroCosto = $bc[0][0];
        
    }else{
        $centroCosto = '"'.$mysqli->real_escape_string(''.$_POST['sltcentroc'].'').'"';
    }
    $valorEjec = "0";
    if($valor=="" || $cuenta=="" ) {
        $rs=2;
    } else {
      
    $sqli = "INSERT INTO gf_detalle_comprobante(fecha,descripcion,valor,
        valorejecucion,comprobante,cuenta,naturaleza,
        tercero,proyecto,centrocosto) 
            VALUES ('$fecha','$descripcion',$valor,$valorEjec,"
            . "$compobantecnt,$cuenta,$natural,$tercero,$proyecto,"
            . "$centroCosto)";
    
    $rs = $mysqli->query($sqli);
    }
} else {
    $rs=2;
}
echo $rs;
?>

