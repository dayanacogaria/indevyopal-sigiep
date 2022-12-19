<?php
session_start();
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
$conexion = new ConexionPDO();
@session_start();
switch ($_REQUEST['action']){
    case 1:
        $texto = $_GET['term'];
        $sql = "SELECT CONCAT_WS(' ',
                gpr.codigo_catastral,
                gps.nombres,
                gps.numero)
            FROM gp_predio1 gpr
            LEFT JOIN gp_tercero_predio gpt ON gpt.predio=gpr.id_unico
            LEFT JOIN gr_propietarios as gps ON gpt.tercero = gps.id_unico
            WHERE gpr.estado = 2 
                AND gpt.orden='001'
                AND CONCAT_WS(' ',gps.nombres,
                gpr.codigo_catastral, 
                gps.numero) LIKE '%$texto%' ORDER BY gpr.id_unico ASC ";
        $result = $mysqli->query($sql);
        if($result->num_rows > 0){
            while($fila =  mysqli_fetch_row($result)){
                $terceros[] = ucwords(strtolower($fila[0]));
            }    
            echo json_encode($terceros);
        }
    break;
    case 2:
        $texto = $_GET['term'];
        $sql = "SELECT CONCAT_WS(' ',
                gpr.codigo_catastral,
                gps.nombres,
                gps.numero)
            FROM gp_predio1 gpr
            LEFT JOIN gp_tercero_predio gpt ON gpt.predio=gpr.id_unico
            LEFT JOIN gr_propietarios as gps ON gpt.tercero = gps.id_unico
            WHERE gpr.estado = 2 
                AND gpt.orden='001'
                AND CONCAT_WS(' ',gps.nombres,
                gpr.codigo_catastral, 
                gps.numero) LIKE '%$texto%' ORDER BY gpr.id_unico DESC";
        $result = $mysqli->query($sql);
        if($result->num_rows > 0){
            while($fila =  mysqli_fetch_row($result)){
                $terceros[] = ucwords(strtolower($fila[0]));
            }    
            echo json_encode($terceros);
        }
    break;
    case 3:
        $texto = $_REQUEST['valor'];
        $sql = "SELECT gpr.codigo_catastral
            FROM gp_predio1 gpr
            LEFT JOIN gp_tercero_predio gpt ON gpt.predio=gpr.id_unico
            LEFT JOIN gr_propietarios as gps ON gpt.tercero = gps.id_unico
            WHERE 
            CONCAT_WS(' ',gpr.codigo_catastral, 
                gps.nombres,
                gps.numero) LIKE '%$texto%' ";
        $result = $mysqli->query($sql);
        $fila =  mysqli_fetch_row($result);
        $id = $fila[0];
                  
        echo ($id);
    break;
    
    case 4:
        #*** Variables Que Recibe ***#
        $predioI = $_REQUEST['predioI'];
        $predioF = $_REQUEST['predioF'];
        #*************************#
        $row = $conexion->Listar("SELECT gpr.id_unico 
            FROM gp_predio1 gpr 
            WHERE gpr.estado = 2 
                AND cast(gpr.codigo_catastral as unsigned) BETWEEN '$predioI' AND '$predioF'"); 
        $c=0;
        for ($i = 0;$i < count($row);$i++) {
            $xxxA =obtnerultimoannopago($row[$i][0]);
            if($xxxA < date("Y") || empty($xxxA)){
                $c+=1;
            }
        }
    echo $c;
    break;
}

function obtnerultimoannopago($predio){
    global $conexion;
    $ult = $conexion->Listar("SELECT max(anno) FROM gr_ultimo_ano_pago WHERE predio = $predio");
    return $ult[0][0];
}

?>