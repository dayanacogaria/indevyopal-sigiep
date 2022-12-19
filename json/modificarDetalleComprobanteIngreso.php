<?php
require_once '../Conexion/conexion.php';
session_start();
###################################################################################################################################
# Modificaciones
###################################################################################################################################
# Modificado    : Jhon Numpaque 
# Fecha         : 20/04/2017
# Descripcion   : Se cambio validación de modificación de valor por naturaleza
#
###################################################################################################################################
# Jhon Numpaque 16 | 02| 2017 
# Hora: 4:57
# Descripción : Se valido el llamado en la variable de sessión para modificar en el detalle pptal
#
###################################################################################################################################
$id=$_POST['id'];
$cuenta=$_POST['cuenta'];
$tercero=$_POST['tercero'];
$centroC=$_POST['centroC'];
$protec=$_POST['proyecto'];
$debito=$_POST['debito'];
$credito=$_POST['credito'];
$valor = 0;
$sql = "SELECT c.naturaleza FROM gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico WHERE dc.id_unico = $id";
$rs = $mysqli->query($sql);
$nat = mysqli_fetch_row($rs);
$naturaleza = $nat[0];
#naturaleza 1 Debito, 2 credito
if (empty($_POST['debito']) || $_POST['debito']=='0') {
    if(!empty($_POST['credito'])) {
        if($naturaleza == 1) {
            $valor = $_POST['credito']*-1;
        }else{
            $valor = $_POST['credito'];
        }
    }
}

if (empty($_POST['credito']) || $_POST['credito']=='0') {
    if(!empty($_POST['debito'])) {
        if($naturaleza == 2) {
            $valor = $_POST['debito']*-1;
        }else{
            $valor = $_POST['debito'];
        }
    }
}
//Validaciiones y casos para registro en el Valor para comprobante presupuestal
if($naturaleza == 1 && $valor < 0) {// Si la naturaleza es debito y el valor es menor que 0, es decir el valor es negativo
    $valorP = $valor *-1;  //Lo convierte a positivo en presupuesto
}

if($naturaleza == 1 && $valor > 0) { //Si la naturaleza es debito y el valor es mayor que 0, es decir el valor es positivo
    $valorP = $valor *-1; //Guarda positivo en presupuesto
}

if($naturaleza == 2 && $valor > 0) {//Si la naturaleza es credito y el valor es positivo, es decir el valor es mayor que 0
    $valorP = $valor;//El valor es positivo en presupuesto
}

if($naturaleza == 2 && $valor < 0) {//Si la naturaleza es credito y el valor es negativo, es decir menor que 0
    $valorP = $valor;//El valor es negativo en presupuesto
}
$sqlCP = "SELECT detComP.comprobantepptal FROM gf_comprobante_cnt comcnt 
LEFT JOIN gf_detalle_comprobante detCom ON comcnt.id_unico = detCom.comprobante
LEFT JOIN gf_detalle_comprobante_pptal detComP ON detComP.id_unico = detCom.detallecomprobantepptal
WHERE detCom.comprobante = $id";
$resultCP = $mysqli->query($sqlCP);
$comPP = mysqli_fetch_row($resultCP);
$rubrofuente = $_POST['rubroFuente'];
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Actualizar comprobante
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sql1 = "UPDATE gf_detalle_comprobante SET valor=$valor,cuenta=$cuenta,tercero=$tercero,proyecto=$protec,centrocosto=$centroC WHERE id_unico=$id";
$result1 = $mysqli->query($sql1);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//ingreso en detalle comprobante pptal
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sqlCP = "SELECT detallecomprobantepptal FROM gf_detalle_comprobante WHERE id_unico = $id";
$res = $mysqli->query($sqlCP);
$rw = mysqli_fetch_row($res);
$compp = $rw[0];
$sql = "UPDATE gf_detalle_comprobante_pptal SET valor=$valorP, rubrofuente = $rubrofuente WHERE id_unico = $compp";
$result = $mysqli->query($sql);
echo json_encode($result);
?>