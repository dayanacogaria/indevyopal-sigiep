<?php
require_once '../Conexion/conexion.php';
session_start();
########################################################################################################################################################
# Modificaciones
#
########################################################################################################################################################
# Fecha : 22/05/2017
# Modificó : Jhon Numpaque
# Descripción : Se agrego el campo tercero
#
########################################################################################################################################################
# Fecha : 31/03/2017
# Modificó : Jhon Numpaque
# Descripción : Se quito agrego validación para registro de los campos, para poder ingresar valores vacios, y se agrego consulta para solo buscar cuando
## solo se ingresa cuenta debito para verificar que el valor no sea repetido
#
########################################################################################################################################################
# Fecha : 24/02/2017
# Hora  : 03:57 p.m
# Modificó : Jhon Numpaque
# Descripción : Se agrego campos cuenta iva, cuenta impoconsumo.
#
########################################################################################################################################################
$id= $_GET['id'];
$cuentaD=$_GET['cuentaD'];

if(empty($_GET['cuentaC'])){
    $cuentaC='NULL';
}else{
    $cuentaC=$_GET['cuentaC'];
}

if(empty($_GET['centro'])){
    $centro='NULL';
}else{
    $centro=$_GET['centro'];
}

if(empty($_GET['proyecto'])){
    $proyecto='NULL';
}else{
    $proyecto=$_GET['proyecto'];    
}

if(empty($_GET['cuentaI'])){
    $cuentaIva = 'NULL';
}else{    
    $cuentaIva = $_GET['cuentaI'];    
}

if(empty($_GET['cuentaIMP'])){
    $cuentaImpo = 'NULL';
}else{
    $cuentaImpo = $_GET['cuentaIMP'];
}

if(!empty($_GET['tercero'])){
    $tercero = $_GET['tercero'];
}else{
    $tercero = 'NULL';
}

$queryA="SELECT cuenta_debito, cuenta_credito, centrocosto, proyecto, cuenta_iva, cuenta_impoconsumo FROM gf_concepto_rubro_cuenta WHERE id_unico = '$id'";
$carA = $mysqli->query($queryA);
$numA=mysqli_fetch_row($carA);

$queryU="SELECT cuenta_debito,cuenta_credito,centrocosto,proyecto,cuenta_iva,cuenta_impoconsumo FROM gf_concepto_rubro_cuenta 
        WHERE cuenta_debito     = '$cuentaD'
        AND cuenta_credito      = '$cuentaC' 
        AND centrocosto         = '$centro'
        AND proyecto            = '$proyecto'
        AND cuenta_iva          = '$cuentaIva'
        AND cuenta_impoconsumo  = '$cuentaImpo'";
$car = $mysqli->query($queryU);
$num=mysqli_num_rows($car);
  
if($numA[0] == $cuentaD && $numA[1] == $cuentaC && $numA[2] == $centro && $numA[3] == $proyecto && $numA[4] == $cuentaIva && $numA[5] == $cuentaImpo){
    $sql = "UPDATE  gf_concepto_rubro_cuenta 
            SET     cuenta_debito       = $cuentaD ,
                    cuenta_credito      = $cuentaC ,
                    centrocosto         = $centro ,
                    proyecto            = $proyecto ,
                    cuenta_iva          = $cuentaIva ,
                    cuenta_impoconsumo  = $cuentaImpo,
                    tercero             = $tercero
            WHERE   id_unico            = $id";
    $result = $mysqli->query($sql); 
} else {
    if($num == 0) { 
        $sql = "UPDATE  gf_concepto_rubro_cuenta 
                SET     cuenta_debito       = $cuentaD ,
                        cuenta_credito      = $cuentaC ,
                        centrocosto         = $centro, 
                        proyecto            = $proyecto, 
                        cuenta_iva          = $cuentaIva, 
                        cuenta_impoconsumo  = $cuentaImpo,
                        tercero             = $tercero
                WHERE   id_unico            = $id";
        $result = $mysqli->query($sql);  
    } else {
	  	if($num > 0){
	  		$result=3;
	  	} else {
	  	    $result = false;
	    }
    }
}
  echo json_encode($result);
?>