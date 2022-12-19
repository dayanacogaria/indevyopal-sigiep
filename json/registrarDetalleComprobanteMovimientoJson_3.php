<?php
######## ARCHIVO CREADO ########
#04/04/2018 | 12:30 | DAVID PADILLA

session_start();
require_once '../Conexion/conexion.php';

$id = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_mov";
$id = $mysqli->query($id);
$id= mysqli_fetch_row($id);
$id = $id[0]+1;

#SUBIDA DEL DOCUMENTO 
if(!empty($_FILES['file']['name'])) {
    $documento = $_FILES['file'];
    $nombre = $_FILES['file']['name'];
    $directorio ='../documentos/compMovi/';

    $nombre =$id.$nombre;
    $ruta = 'documentos/compMovi/'.$nombre;
} else {
    $ruta = NULL;
}


$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fechaM'].'').'';
$valorF = explode("/",$fechaT);
$fechaVencimiento    =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$tipoDocumento       = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoDocumento'].'').'"';
$numeroDoc           = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroDoc'].'').'"';
$valorMov            = '"'.$mysqli->real_escape_string(''.$_POST['txtValorMov'].'').'"';
$comprobantepptal    = 'NULL';
$comprobanteContable = 'NULL';
$idcomprobantepptal  = $_POST['txtIdc'];
$idcomprobantecnt    = 'NULL';
if($_REQUEST['almacen']==1){
    $sql = "INSERT INTO gf_detalle_comprobante_mov
            (numero,fechavencimiento,valor,tipodocumento,
           ruta,  movimiento) 
            VALUES($numeroDoc,$fechaVencimiento,
            $valorMov,$tipoDocumento,
            '$ruta',  $idcomprobantepptal)";
} else { 
    $sql = "INSERT INTO gf_detalle_comprobante_mov
            (numero,fechavencimiento,valor,tipodocumento,
           ruta,  id_comprobante_pptal) 
            VALUES($numeroDoc,$fechaVencimiento,
            $valorMov,$tipoDocumento,
            '$ruta',  $idcomprobantepptal)";
}
$result = $mysqli->query($sql);
if ($result==true || $result=='1'){
    if($ruta !=NULL) {
       // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
        move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
        }
 }
echo json_encode($result);
?>