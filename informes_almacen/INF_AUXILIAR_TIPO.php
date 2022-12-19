<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Almacen.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once("../jsonServicios/funcionesServicios.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);


$tipoI = $_REQUEST['sltTci'];
$tipoF = $_REQUEST['sltTcf'];
$fecha = fechaC($_REQUEST['fechafin']);
$elemI = $_REQUEST['sltctai'];
$elemF = $_REQUEST['sltctaf'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 

#* Buscar Movimientos 
$row = $con->Listar("SELECT m.id_unico, tm.sigla, tm.nombre, m.numero, 
        DATE_FORMAT(m.fecha, '%d/%m/%Y'), pi.codi, pi.nombre, dm.cantidad, dm.valor, 
        (dm.cantidad*dm.valor) vt, 
        IF(CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos) 
             IS NULL OR CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos) = '',
             (t.razonsocial),
             CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos)) AS NOMBRE ,CONCAT_WS(' ', d.sigla, d.nombre), 
             dm.id_unico , COUNT(DISTINCT mp.producto) 
        FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
        LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
        LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
        LEFT JOIN gf_tercero t ON m.tercero2 = t.id_unico 
        LEFT JOIN gf_dependencia d ON m.dependencia = d.id_unico 
        LEFT JOIN gf_movimiento_producto mp ON mp.detallemovimiento = dm.id_unico 
        WHERE (tm.sigla BETWEEN '$tipoI' AND '$tipoF' )
        AND (pi.codi BETWEEN '$elemI' AND '$elemF')
        AND m.parametrizacionanno = $anno 
        AND m.fecha <= '$fecha' 
        GROUP BY dm.id_unico 
        ORDER BY tm.sigla, m.numero, m.fecha, pi.codi ");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Auxiliar Almacén</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php 
            echo '<th colspan="12" align="center"><strong>';
            echo '<br/>'.$razonsocial;
            echo '<br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer;
            echo '<br/>&nbsp;';
            echo '<br/>AUXILIAR ALMACÉN POR TIPO';
            echo '<br/>FECHA :'.$_REQUEST['fechafin']; 
            echo '<br/>&nbsp;</strong>';
            echo '</th>';
            echo '<tr></tr>';
            echo '<tr>';
            echo '<td><strong>TIPO MOVIMIENTO</strong></td>';              
            echo '<td><strong>NOMBRE TIPO MOVIMIENTO</strong></td>';
            echo '<td><strong>NÚMERO</strong></td>';
            echo '<td><strong>RESPONSABLE</strong></td>';
            echo '<td><strong>DEPENDENCIA</strong></td>';
            echo '<td><strong>FECHA</strong></td>';
            echo '<td><strong>ELEMENTO</strong></td>';
            echo '<td><strong>ESPECIFICACIONES</strong></td>';
            echo '<td><strong>CANTIDAD</strong></td>';
            echo '<td><strong>TOTAL PRODUCTOS</strong></td>';
            echo '<td><strong>VALOR UNITARIO</strong></td>';
            echo '<td><strong>VALOR TOTAL</strong></td>';
            echo '</tr>';        
            $vu = 0;
            $vt = 0;
            $tc = 0;
            $tp = 0;
            for ($i = 0; $i < count($row); $i++) {                
                echo '<tr>';
                echo '<td>'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.$row[$i][10].'</td>';
                echo '<td>'.$row[$i][11].'</td>';
                echo '<td>'.$row[$i][4].'</td>';
                echo '<td>'.$row[$i][5].' - '.$row[$i][6].'</td>';
                $be = $con->Listar("SELECT DISTINCT p.id_unico, p.descripcion FROM gf_movimiento_producto mp 
                LEFT JOIN gf_producto p ON mp.producto = p.id_unico 
                WHERE mp.detallemovimiento = ".$row[$i][12]);
                echo '<td>'.$be[0][1].'</td>';
                echo '<td>'. number_format($row[$i][7], '2', '.', ',').'</td>';
                echo '<td>'. number_format($row[$i][13], '2', '.', ',').'</td>';
                echo '<td>'. number_format($row[$i][8], '2', '.', ',').'</td>';
                echo '<td>'. number_format($row[$i][9], '2', '.', ',').'</td>';
                #echo '<td>'.$row[$i][12].'</td>';
                echo '</tr>';
                $vu += $row[$i][8];
                $vt += $row[$i][9];
                $tc += $row[$i][7];
                $tp += $row[$i][13];
            }
            echo '<tr>';
            echo '<td colspan="8"><strong>TOTAL</strong></td>';
            echo '<td><strong>'. number_format($tc, '2', '.', ',').'</strong></td>';
            echo '<td><strong>'. number_format($tp, '2', '.', ',').'</strong></td>';
            echo '<td><strong>'. number_format($vu, '2', '.', ',').'</strong></td>';
            echo '<td><strong>'. number_format($vt, '2', '.', ',').'</strong></td>';
            echo '</tr>';
            ?>
        </table>
    </body>
</html>