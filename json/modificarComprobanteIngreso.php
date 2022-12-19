<?php
##############################Modificaciones###############################
# Jhon Numpaque | 03-02-2017 | 10:20
#Se cambio parametro id de comprobantePptal se obtiene por consulta de los detalles relacionados a los detalles
#de comprobante contable
require_once '../Conexion/conexion.php';
session_start();

$id = $_POST['id'];
$tipoComprobante = $_POST['tipoComprobante'];
$numero = $_POST['numero'];
$tercero = $_POST['tercero'];
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
#Validación para nulos en clase contrato
if(!empty($_POST['claseContrato'])){
	$claseContrato = $_POST['claseContrato'];
}else{
	$claseContrato = 'NULL';
}
#Validación para nulos en numero contrato
if(!empty($_POST['numeroContrato'])){
	$numeroContrato = '"'.$mysqli->real_escape_string(''.$_POST['numeroContrato'].'').'"';
}else{
	$numeroContrato = 'NULL';
}

#Actualizar comprobante contable
$sql = "UPDATE gf_comprobante_cnt "
        . "SET numero=$numero,fecha=$fecha,descripcion=$descripcion,"
        . "numerocontrato=$numeroContrato,"
        . "tercero=$tercero,clasecontrato=$claseContrato WHERE id_unico = $id";
$result = $mysqli->query($sql);
#consulta de comprobante contable para obtener el comprobnate pptal
$sqlCP = "SELECT detComP.comprobantepptal FROM gf_comprobante_cnt comcnt 
LEFT JOIN gf_detalle_comprobante detCom ON comcnt.id_unico = detCom.comprobante
LEFT JOIN gf_detalle_comprobante_pptal detComP ON detComP.id_unico = detCom.detallecomprobantepptal
WHERE detCom.comprobante = $id";
$resultCP = $mysqli->query($sqlCP);
if(mysqli_num_rows($resultCP)>0) {
$comPP = mysqli_fetch_row($resultCP);
} else {
    ##BUSCA EL TIPO PPTAL ##
    $tipo = "SELECT comprobante_pptal, tipo_comp_hom FROM gf_tipo_comprobante WHERE id_unico = $tipoComprobante";
    $tipo = $mysqli->query($tipo);
    $tipo = mysqli_fetch_row($tipo);
    $tipo = $tipo[0]; 
    
    
    ##BUSCA COMRPOBANTE PPTAL 
    $pptal = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante = $tipo";
    $pptal = $mysqli->query($pptal);
    $comPP = mysqli_fetch_row($pptal);
}
#consulta de tipo comprobante
$tipoComprobantepptal = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipoComprobante";
$rs = $mysqli->query($tipoComprobantepptal);
$tipoCP = mysqli_fetch_row($rs);
#Actualizar comprobante pptal
if(!empty($_SESSION['idPptal'])){
	$compp = $_SESSION['idPptal'];
}else{
	$compp = $comPP[0];
}
$sql1 = "UPDATE gf_comprobante_pptal SET fecha=$fecha,descripcion=$descripcion,tipocomprobante=$tipoCP[0],tercero=$tercero WHERE id_unico=$compp";
$result = $mysqli->query($sql1);

##### Buscar Causación ###########
 $cs = "SELECT DISTINCT cc.id_unico FROM gf_comprobante_cnt cp 
LEFT JOIN gf_detalle_comprobante dt ON cp.id_unico = dt.comprobante
LEFT JOIN gf_detalle_comprobante dc ON dc.detalleafectado = dt.id_unico 
LEFT JOIN gf_comprobante_cnt cc ON dc.comprobante = cc.id_unico 
WHERE cp.id_unico = $id AND cc.id_unico IS NOT NULL";
$cs = $mysqli->query($cs);
if(mysqli_num_rows($cs)>0){
    $cs = mysqli_fetch_row($cs);
    $cs = $cs[0];
    if(!empty($cs)){
       #Actualizar comprobante contable
         $sql = "UPDATE gf_comprobante_cnt "
        . "SET fecha=$fecha,descripcion=$descripcion,"
        . "numerocontrato=$numeroContrato,"
        . "tercero=$tercero,clasecontrato=$claseContrato WHERE id_unico = $cs"; 
        $result = $mysqli->query($sql);
    }
}

echo json_encode($result);
?>

