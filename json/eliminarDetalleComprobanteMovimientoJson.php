<?php
######## MODIFICACIONES ########
#31/01/2017 | 12:30 | ERICA GONZALEZ
?>
<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_GET['id'];
$ruta= $_GET['ruta'];
    $archivo ="SELECT * FROM gf_detalle_comprobante_mov WHERE ruta='$ruta'";
    $archivo = $mysqli->query($archivo);
    $num = mysqli_num_rows($archivo);
    if($num>1){
        $query = "DELETE FROM gf_detalle_comprobante_mov WHERE id_unico = '$id'";
        $resultado = $mysqli->query($query);
    } else {
        if(!empty($ruta)){
        $ruta = '../'.$ruta;
        $do = unlink($ruta);
        if($do == true){
            $query = "DELETE FROM gf_detalle_comprobante_mov WHERE id_unico = '$id'";
            $resultado = $mysqli->query($query);
        } else {
            $query = "DELETE FROM gf_detalle_comprobante_mov WHERE id_unico = '$id'";
            $resultado = $mysqli->query($query);
            }
        } else {
           $query = "DELETE FROM gf_detalle_comprobante_mov WHERE id_unico = '$id'";
           $resultado = $mysqli->query($query); 
        }
    }
    
    

echo json_encode($resultado);
?>

