<?php
######## MODIFICACIONES ########
#31/01/2017 | 12:30 | ERICA GONZALEZ
?>
<?php
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
$fechaVencimiento =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$tipoDocumento = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoDocumento'].'').'"';
$numeroDoc = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroDoc'].'').'"';
$valorMov = '"'.$mysqli->real_escape_string(''.$_POST['txtValorMov'].'').'"';
$comprobanteContable =  '"'.$mysqli->real_escape_string(''.$_POST['txtIdc'].'').'"';

$comprobantepptal = 'NULL';
$sql = "INSERT INTO gf_detalle_comprobante_mov("
        . "numero,fechavencimiento,valor,tipodocumento,"
        . "comprobantecnt,comprobantepptal, ruta) "
        . "VALUES($numeroDoc,$fechaVencimiento,"
        . "$valorMov,$tipoDocumento,$comprobanteContable,"
        . "$comprobantepptal, '$ruta')";
$result = $mysqli->query($sql);
if ($result==true || $result=='1'){
       // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
    if($ruta !=NULL) {
        move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
        $_SESSION['idComprobanteP']='';
    }
    }
echo json_encode($result);
?>