<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#28/09/2018 | Erica G. | Archivo Creado
#####################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Lecturas.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

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

$row = $con->Listar("SELECT uvms.id_unico, p.codigo_catastral, 
         p.direccion , 
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
             t.apellidodos)) AS NOMBRE, 
             s.codigo, s.nombre 
        FROM gp_unidad_vivienda_medidor_servicio uvms 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        ORDER BY cast(s.codigo  as unsigned),cast(p.codigo_catastral  as unsigned)  ASC");
$periodoa =periodoA ($id_periodo);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturación</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="7" align="center"><strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            echo '<tr>';
            echo '<td><strong>Sector</strong></td>';
            echo '<td><strong>Código</strong></td>';
            echo '<td><strong>Dirección</strong></td>';
            echo '<td><strong>Tercero</strong></td>';
            echo '<td><strong>Valor Facturas</td>';
            echo '<td><strong>Valor Recaudos</td>';
            echo '<td><strong>Saldo</td>';
            echo '</tr>';        
            for ($i = 0; $i < count($row); $i++) {
                $id_uvms = $row[$i][0];
                $vf =$con->Listar("SELECT SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                    WHERE f.unidad_vivienda_servicio = $id_uvms");
                $vidsf =$con->Listar("SELECT GROUP_CONCAT(df.id_unico) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                    WHERE f.unidad_vivienda_servicio = $id_uvms");
                $id_f = $vidsf[0][0];
                #*** Buscar Valor Recaudos ***#
                $vr =$con->Listar("SELECT SUM(df.valor) 
                    FROM gp_detalle_pago df 
                    WHERE df.detalle_factura IN ($id_f)");
                $diferencia = $vf[0][0] -$vr[0][0];
                if($diferencia !=0){
                echo '<tr>';
                echo '<td>'.$row[$i][4].' - '.ucwords(mb_strtolower($row[$i][5])).'</td>';
                echo '<td style="mso-number-format:\@">'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
                echo '<td>'.number_format($vf[0][0],2,'.',',').'</td>';
                echo '<td>'.number_format($vr[0][0],2,'.',',').'</td>';
                
                echo '<td>'.number_format($diferencia,2,'.',',').'</label></td>';
                echo '</tr>';
                }

             }
            
            ?>
        </table>
    </body>
</html>