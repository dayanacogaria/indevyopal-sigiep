<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);
#   ************   Datos Recibios   ************    #
$id   = $_REQUEST['id'];
$dr   = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_retencion WHERE md5(id_unico)='".$id."'");
$idtr = $dr[0][0];
$nr   = mb_strtoupper($dr[0][1]); 
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

$row = $con->Listar("SELECT  DATE_FORMAT(cn.fecha, '%d/%m/%Y'),tc.sigla, tc.nombre,  
    cn.numero, r.retencionbase,  r.valorretencion 
FROM gf_tipo_retencion tr 
LEFT JOIN gf_retencion r ON tr.id_unico = r.tiporetencion 
LEFT JOIN gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
WHERE tr.id_unico = $idtr   
ORDER BY cn.fecha, cn.numero  ASC");

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Retenciones_Aplicadas.xls");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Retenciones Aplicadas</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="5" align="center">
            <strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;<?php echo $nr;?>
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            <tr>
                <td><strong>FECHA</strong></td>
                <td><strong>TIPO COMPROBANTE</strong></td>
                <td><strong>NÚMERO</strong></td>
                <td><strong>VALOR BASE</strong></td>
                <td><strong>VALOR RETENCIÓN</strong></td>
            </tr>
            <?PHP 
            $tr = 0;
            for ($i = 0; $i < count($row); $i++) {
                echo '<tr>';
                echo '<td>'.$row[$i][0].'</td>';
                echo '<td>'.$row[$i][1].' - '.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.number_format($row[$i][4], 2, '.', ',').'</td>';
                echo '<td>'.number_format($row[$i][5], 2, '.', ',').'</td>';
                echo '</tr>';
                $tr +=$row[$i][5];
            }
            echo '<tr><strong><td colspan="4">TOTAL</td>';
            echo '<td>'.number_format($tr, 2, '.', ',').'</td></strong></tr>';
            ?>
        </table>
    </body>
</html>