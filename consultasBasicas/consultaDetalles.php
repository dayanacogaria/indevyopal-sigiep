<?php
session_start();
$detalle = $_GET['detalle'];
require_once '../Conexion/conexion.php';
switch ($detalle){
    case 1:
        $i=0;
        $tabla = "";
        $sqlIva = "SELECT valor FROM gs_parametros_basicos WHERE id_unico=2";
        $resultIva = $mysqli->query($sqlIva);
        $iva = mysqli_fetch_row($resultIva);
        $values = $_GET['values'];
        $sql = "SELECT 
                    dtm.id_unico, 
                    pl.id_unico,
                    CONCAT(pl.codi,' - ',pl.nombre) as planI,                                       
                    SUM(dtm.cantidad) as cantidad,
                    (dtm.valor) AS valor                                          
        FROM gf_detalle_movimiento dtm
        LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
        LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
        WHERE mv.id_unico IN ($values)
        GROUP BY pl.id_unico";
        $result = $mysqli->query($sql);
        $filas = mysqli_num_rows($result);
        if($filas>=0 || !empty($filas)){
            while($row = mysqli_fetch_row($result)){
                $i++;
                $total = $row[4]*$row[3];
                $valorIva = ($total*$iva[0])/100;
                $valorTotal = $total+$valorIva; 
                $item = $i;
                $tabla .='{"":"","Item":"'.$item.'","Plan Inventario":"'.$row[2].'","Cantidad":"'.$row[3].'","Valor Aproximado":"'.$row[4].'","Iva":"'.$valorIva.'","Valor Total":"'.$valorTotal.'"},';                
            }
            $tabla = substr($tabla,0, strlen($tabla) - 1);
            echo '{"data":['.$tabla.']}';
        }else{
            echo '<tr><td class="text-center" colspan="12" class="text-center"><p>No Existen Registros...</p><td><tr/>';
        }
        break;
}
?>

