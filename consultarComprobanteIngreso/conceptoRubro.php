<?php
session_start();
require_once '../Conexion/conexion.php';
$valor = $_POST['valor'];
switch ($valor) {
    case 1:
        $concepto = $_POST['concepto'];
        if($concepto == 0 || $concepto=='""'){
            echo '<option value="">Rubro Fuente</option>';
        }else{
            $sql = "SELECT DISTINCT rb.id_unico,rb.codi_presupuesto,rb.nombre,ft.nombre,rft.id_unico
                    FROM gf_concepto_rubro cr 
                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                    LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico
                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico
                    WHERE cr.concepto = $concepto AND rb.id_unico IS NOT NULL";
            $result = $mysqli->query($sql);            
            while ($row = mysqli_fetch_row($result)){
                echo '<option value="'.$row[4].'">'.utf8_encode(strtolower(ucwords($row[1].' '.$row[2].'-'.$row[3]))).'</option>';
            }
        }
        break;
    case 2:
        $concepto = $_POST['concepto'];
        if($concepto == 0 || $concepto=='""'){
            echo '';
        }else{
            $sql = "SELECT DISTINCT cr.id_unico
            FROM gf_concepto_rubro cr 
            LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
            LEFT JOIN gf_rubro_pptal rb ON rft.rubro = rb.id_unico
            LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico
            WHERE concepto = $concepto AND rb.id_unico IS NOT NULL";
            $result = $mysqli->query($sql);            
            $row = mysqli_fetch_row($result);
            echo $row[0];
        }
        break;
}
?>
