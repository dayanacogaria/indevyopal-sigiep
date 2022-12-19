<?php
######## MODIFICACIONES ########
#31/01/2017 | 12:30 | ERICA GONZALEZ
?>
<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $mysqli->real_escape_string(''.$_POST['id'].'');
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fechaVencimiento'].'').'';
$valorF = explode("/",$fechaT);
$fechaVencimiento =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$tipoDocumento = '"'.$mysqli->real_escape_string(''.$_POST['tipoDocumento'].'').'"';
$numeroDoc = '"'.$mysqli->real_escape_string(''.$_POST['numeroDocumento'].'').'"';
$valorMov = '"'.$mysqli->real_escape_string(''.$_POST['valorMovimiento'].'').'"';

#SI SE SUBE UN ARCHIVO NUEVO
$datosAnteriores="SELECT ruta FROM gf_detalle_comprobante_mov WHERE id_unico ='$id'";
$datosAnteriores = $mysqli->query($datosAnteriores);
$datosA = mysqli_fetch_row($datosAnteriores);

if(!empty($_FILES['file']['name'])){
      if(!empty($datosA[0])){
            $rutaEliminar = $datosA[0];
            $rutaEliminar = '../'.$rutaEliminar;
            $do = unlink($rutaEliminar);
          
      } else {
          $do=true;
      }
      if($do == true){
         #GUARDAR ARCHIVO Y MODIFICAR DATOS 
         #TOMAR DATOS DEL ARCHIVO SUBIDO
        $nombre = $_FILES['file']['name'];
        $directorio ='../documentos/compMovi/';
        $nombre =$id.$nombre;
        $ruta = 'documentos/compMovi/'.$nombre;
        #ACTUALIZAR DATOS
        $update="UPDATE gf_detalle_comprobante_mov "
                . "SET tipodocumento=$tipoDocumento,"
                . "numero=$numeroDoc,"
                . "fechavencimiento=$fechaVencimiento,"
                . "valor=$valorMov, ruta='$ruta' WHERE id_unico='$id'";
        $update = $mysqli->query($update);
        if($update == true || $update=='1'){
            #SE SUBE EL ARCHIVO NUEVO
            move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
            $resultado =true;
        } else {
            $resultado = false;
        }
      } else {
          $resultado=false;
      }
} else {
    $update="UPDATE gf_detalle_comprobante_mov "
                . "SET tipodocumento=$tipoDocumento,"
                . "numero=$numeroDoc,"
                . "fechavencimiento=$fechaVencimiento,"
                . "valor=$valorMov WHERE id_unico='$id'";
        $resultado = $mysqli->query($update);
}


echo json_encode($resultado);
?>

