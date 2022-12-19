<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Dispersion_Fondos.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);
$ids    = $_REQUEST['ids'];
$row    = $con->Listar("SELECT DISTINCT cn.id_unico, 
    cb.numerocuenta, DATE_FORMAT(cn.fecha,'%d/%m/%Y'), cn.numero, 
    t.numeroidentificacion, c.codi_cuenta, 
    cn.descripcion, SUM(IF(dc.valor>0,dc.valor, dc.valor *-1))
    FROM gf_comprobante_cnt cn 
    LEFT JOIN gf_forma_pago fp ON cn.formapago = fp.id_unico 
    LEFT JOIN gf_tercero t ON t.id_unico  = cn.tercero 
    LEFT JOIN gf_cuenta_bancaria_tercero cbt ON t.id_unico = cbt.tercero 
    LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
    LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
    WHERE cn.id_unico IN ($ids) AND c.clasecuenta IN (11, 12)
    GROUP BY cn.id_unico");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Dispersión de Fondos</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php 
            echo '<tr>';
            echo '<td><strong>CUENTA BANCARIA TERCERO</strong></td>';              
            echo '<td><strong>FECHA</strong></td>';
            echo '<td><strong>NÚMERO COMPROBANTE</strong></td>';
            echo '<td><strong>NIT</strong></td>';
            echo '<td><strong>BANCO</strong></td>';
            echo '<td><strong>CONCEPTO</strong></td>';
            echo '<td><strong>VALOR</strong></td>';
            echo '</tr>';        
            for ($i = 0; $i < count($row); $i++) {
                echo '<tr>';
                echo '<td>'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.$row[$i][4].'</td>';
                echo '<td>'.$row[$i][5].'</td>';
                echo '<td>'.$row[$i][6].'</td>';
                echo '<td>'.$row[$i][7].'</td>';
                echo '</tr>';
            }
            
            ?>
        </table>
    </body>
</html>