<?php
session_start();
require_once '../Conexion/conexion.php';
$tercero = $_POST['tercero'];
$case = $_POST['case'];
switch ($case){
    case 1:        
            echo '<option value="">Tipo Factura</option>';
            $sql = "SELECT id_unico,nombre FROM gp_tipo_factura WHERE servicio = 2";
            $result = $mysqli->query($sql);
            while($row= mysqli_fetch_row($result)){
                echo '<option value="'.$row[0].'">'.ucwords(strtolower($row[1])).'</option>';
            }
        
        break;
    case 2:
        
            echo '<option value="">Factura</option>';
        
        break;
    case 3:
            echo '0';

}
?>

