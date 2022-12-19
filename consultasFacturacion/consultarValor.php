<?php
session_start();
require_once '../Conexion/conexion.php';
/**
 * consultarValor.php
 * 
 * @author Alexander Numpaque
 * @package Factura
 *
 **/

/**
 * Modificaciones
 * Fecha: 17-05-2017
 * Modificado: Alexander Numpaque
 * Descripción: Se cambio la variable que recibe en los casos 2 y 3 se cambio por el id de la tarifa, en las consultas se cambio el where para que tome el id 
 * de la tarifa. en el caso para imprimir las tarifas en el campo value se imprime el valor y el id de la tarifa separado por /
 **/
$proceso = $_POST['proceso'];
switch ($proceso) {
    case 1:
        $concepto = $_POST['concepto'];
        $sql = "SELECT DISTINCT gptr.valor,gptr.porcentaje_iva,gptr.porcentaje_impoconsumo,gptr.id_unico
        FROM gp_concepto gpc
        LEFT JOIN gp_concepto_tarifa gpct ON gpct.concepto = gpc.id_unico
        LEFT JOIN gp_tarifa gptr ON gpct.tarifa = gptr.id_unico
        WHERE gpct.concepto = $concepto";
        $result = $mysqli->query($sql);        
        $fila = $result->num_rows;
        echo "<option value=\"0/0\">Valor Unitario</option>";
        while($row = mysqli_fetch_row($result)){
            echo "<option value=\"".$row[0]."/".$row[3]."\">$row[0]</option>";
        }        
        break;        
    case 2:
        $tarifa = $_POST['tarifa'];
         $sql1 = "SELECT DISTINCT gptr.porcentaje_iva,gptr.porcentaje_impoconsumo
        FROM gp_concepto gpc
        LEFT JOIN gp_concepto_tarifa gpct ON gpct.concepto = gpc.id_unico
        LEFT JOIN gp_tarifa gptr ON gpct.tarifa = gptr.id_unico
        WHERE gpct.id_unico = $tarifa";
        $result1 = $mysqli->query($sql1);
        $row1 = mysqli_fetch_row($result1);
        $iva  = $row1[0];
        echo $iva;
        break;
    case 3:
        $tarifa = $_POST['tarifa'];
        $sql2 = "SELECT DISTINCT gptr.porcentaje_impoconsumo
        FROM gp_concepto gpc
        LEFT JOIN gp_concepto_tarifa gpct ON gpct.concepto = gpc.id_unico
        LEFT JOIN gp_tarifa gptr ON gpct.tarifa = gptr.id_unico
        WHERE gpct.id_unico = $tarifa";
        $result2 = $mysqli->query($sql2);
        $row2 = mysqli_fetch_row($result2);
        $impo=$row2[0];
        echo $impo;
        break;
        
}

?>