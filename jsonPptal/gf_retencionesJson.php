<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#28/09/2017 |Erica G. | CALCULO AL INGRESAR RETENCIONES 
#21/09/2017 |Erica G. | Creación case 1 y 2 para ingresar retenciones en comprobantes ya creados
#09/09/2017 |Erica G. | Archivo Creado para validar retenciones
#######################################################################################################
require_once '../Conexion/conexion.php';
require_once './funcionesPptal.php';
session_start();
$anio= $_SESSION['anno'];
switch ($_POST['action']){
    #***************Cargar retenciones según clase************************#
    case(1):
        $retencion = $_POST['ret'];
        $sql = "SELECT id_unico, CONCAT(nombre, ' ', porcentajeaplicar, '%') "
                . "FROM gf_tipo_retencion WHERE claseretencion = $retencion and parametrizacionanno = $anio";
        $sql = $mysqli->query($sql);
        echo '<option>Tipo Retención</option>';
        while ($row = mysqli_fetch_row($sql)) {
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).'</option>';
        }
        
    break;
    #***************Guardar Retenciones Cuando no se guardaron en la interfaz normal************************#
    case(2):
        $num = $_POST['numR'];
        $tipo = $_POST['tipo'];
        $base  = $_POST['base'];
        $valor = $_POST['valor'];
        $compr = $_POST['compr'];
        $g=0;
        for ($i = 0; $i < $num; $i++) {
            #***Buscar cuenta del tipo retencion**#
            $tr = "SELECT 
                    id_unico, 
                    porcentajeaplicar, 
                    cuenta 
                FROM 
                    gf_tipo_retencion 
                WHERE 
                    id_unico =".$tipo[$i];
            $tr = $mysqli->query($tr);
            if(mysqli_num_rows($tr)>0){
                $tret = mysqli_fetch_row($tr);
                $porcentaje = ($tret[1]/100);
                $cuenta     = $tret[2];
                $valorr     = $valor[$i];
                $baser      = $base[$i];
                $tipor      = $tipo[$i];
                $insert = "INSERT INTO gf_retencion ("
                        . "valorretencion, retencionbase, "
                        . "porcentajeretencion, cuentadescuentoretencion, "
                        . "comprobante, tiporetencion) "
                        . "VALUES('$valorr', '$baser', "
                        . "'$porcentaje','$cuenta', "
                        . "'$compr', '$tipor' )";
                $insert = $mysqli->query($insert);
                if($insert==true){
                    $g = $g+1;
                }
                
            } 
        }
        if($num==$g){
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
    break;
    #***************Validar si la retención escogida aplica Ley************************#
    case(3):
        $retencion = $_POST['ret'];
        $sql = "SELECT ley1450 FROM gf_tipo_retencion WHERE id_unico = $retencion";
        $sql = $mysqli->query($sql);
        $sql = mysqli_fetch_row($sql);
        $ret = $sql[0];
        echo json_decode($ret);
    break;
    #***************Eliminar Deducciones ************************#
    case(4):
        $id = $_POST['id'];
        $sql = "DELETE FROM gf_deducciones WHERE id_unico = $id";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
    #***************Registrar Deducciones ************************#
    case(5):
        $nombre = $_POST['nombre'];
        if(empty($_POST['descripcion'])){
            $descripcion = 'NULL';
        }else {
            $descripcion = "'".$_POST['descripcion']."'";
        }
        $sql = "INSERT INTO gf_deducciones (nombre, descripcion,parametrizacion) "
                . "VALUES('$nombre', $descripcion, $anio)";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;
    #***************Actualizar Deducciones ************************#
    case(6):
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        if(empty($_POST['descripcion'])){
            $descripcion = 'NULL';
        }else {
            $descripcion = "'".$_POST['descripcion']."'";
        }
        $sql = "UPDATE gf_deducciones SET nombre= '$nombre', descripcion=$descripcion 
                WHERE id_unico = $id";
        $res = $mysqli->query($sql);
        echo json_decode($res);
    break;

}
