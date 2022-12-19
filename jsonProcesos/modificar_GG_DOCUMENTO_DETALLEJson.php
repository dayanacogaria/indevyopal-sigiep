<?php
require_once('../Conexion/conexion.php');
  session_start();
$id  = $mysqli->real_escape_string(''.$_POST['idMod'].'');

#DATOS ANTERIORES
$datosAnteriores="SELECT documento, numero_documento, ruta FROM gg_documento_detalle_proceso WHERE id_unico ='$id'";
$datosAnteriores = $mysqli->query($datosAnteriores);
$datosA = mysqli_fetch_row($datosAnteriores);
$proceso  = $mysqli->real_escape_string(''.$_POST['procesoMod'].'');
$documento  = $mysqli->real_escape_string(''.$_POST['documentoMod'].'');
#directorio
$directorio ='../documentos/detalle/';
  if(!empty($_POST['numeroMod'])){
      $numero  = $mysqli->real_escape_string(''.$_POST['numeroMod'].'');
  } else {
      $numero ='';
  }
  #SI HA SUBIDO UN ARCHIVO NUEVO
  if(!empty($_FILES['file']['name'])){
      #ELIMINAR ARCHIVO ANTERIOR
      $rutaEliminar = $datosA[2];
      $rutaEliminar = '../'.$rutaEliminar;
      $do = unlink($rutaEliminar);
      if($do == true){
         #GUARDAR ARCHIVO Y MODIFICAR DATOS 
         #TOMAR DATOS DEL ARCHIVO SUBIDO
        $nombre = $_FILES['file']['name'];
        $directorio ='../documentos/detalle/';
        $nombre =$id.$nombre;
        $ruta = 'documentos/detalle/'.$nombre;
        #ACTUALIZAR DATOS
        $update ="UPDATE gg_documento_detalle_proceso "
                . "SET documento = $documento, "
                . "numero_documento = '$numero',"
                . "ruta = '$ruta' WHERE id_unico ='$id'";
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
   # SI NO HA SUBIDO UN ARCHIVO NUEVO 
  } else {
      #SE ACTUALIZAN LOS DATOS
          $update ="UPDATE gg_documento_detalle_proceso "
                . "SET documento = $documento, "
                . "numero_documento = '$numero' WHERE id_unico ='$id'";
          $resultado = $mysqli->query($update);
      
  }
  
  
  echo json_encode($resultado);
?>