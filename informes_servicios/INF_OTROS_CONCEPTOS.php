<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#28/09/2018 | Erica G. | Archivo Creado
#####################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Otros_Conceptos.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$id_sector       = $_REQUEST['s1'];
$id_sectorf      = $_REQUEST['s2'];
$id_periodo      = $_REQUEST['p'];
$p = $con->Listar("SELECT DISTINCT id_unico, 
    nombre, 
    DATE_FORMAT(fecha_inicial, '%d/%m/%Y'),
    DATE_FORMAT(fecha_final, '%d/%m/%Y')                                       
    FROM gp_periodo p 
    WHERE id_unico=".$_REQUEST['p']);
$periodo =ucwords(mb_strtolower($p[0][1])).'  '.$p[0][2].' - '.$p[0][3];
$s = $con->Listar("SELECT DISTINCT id_unico, 
    nombre, codigo
    FROM gp_sector 
    WHERE id_unico =".$_REQUEST['s1']);
$sector = $s[0][2].' - '.ucwords(mb_strtolower($s[0][1]));
$s2 = $con->Listar("SELECT DISTINCT id_unico, 
    nombre, codigo
    FROM gp_sector 
    WHERE id_unico =".$_REQUEST['s2']);
$sector2 = $s2[0][2].' - '.ucwords(mb_strtolower($s2[0][1]));
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

$row = $con->Listar("SELECT DISTINCT 
    s.nombre, s.codigo, 
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
    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
         t.numeroidentificacion, 
    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
    f.id_unico, uvs.id_unico,  f.periodo, 
    uv.codigo_ruta, p.codigo_catastral 
    FROM gp_factura f
    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
    LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
    LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
    WHERE s.id_unico BETWEEN ".$_REQUEST['s1']." AND ".$_REQUEST['s2']." 
    AND f.periodo =".$_REQUEST['p']." 
    ORDER BY cast(s.codigo as unsigned),cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");

$periodoa   = periodoA($_REQUEST['p']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Otros Conceptos</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
           <th colspan="10" align="center"><strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>OTROS CONCEPTOS
                <br/>PERIODO: <?php echo $periodo; ?>
                <br/>ENTRE SECTOR:<?php echo $sector.' Y ',$sector2; ?>
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            echo '<tr>';
            echo '<td><strong>SECTOR</strong></td>';
            echo '<td><strong>CÓDIGO SISTEMA</strong></td>';
            echo '<td><strong>CÓDIGO RUTA</strong></td>';
            echo '<td><strong>TERCERO</strong></td>';
            echo '<td><strong>CONCEPTO</strong></td>';
            echo '<td><strong>TOTAL CUOTAS</strong></td>';
            echo '<td><strong>VALOR CUOTA</strong></td>';
            echo '<td><strong>CUOTAS PAGAS</strong></td>';
            echo '<td><strong>CUOTAS PENDIENTES</strong></td>';
            echo '<td><strong>SALDO</strong></td>';
            echo '</tr>';        
            for ($i = 0; $i < count($row); $i++) {
                $ids_uv     = $row[$i][5];
                $id_factura = $row[$i][4];
                $periodo    = $row[$i][6];
                #*** Buscar Otros Conceptos ***#
                $otc = $con->Listar("SELECT c.nombre, o.valor_total, o.valor_cuota, 
                        o.cuotas_pagas, o.cuotas_pendientes, c.id_unico, o.id_unico  
                    FROM gf_otros_conceptos o 
                    LEFT JOIN gp_concepto c ON o.concepto = c.id_unico 
                    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON o.unidad_vivienda_ms = uvms.id_unico 
                    WHERE uvms.unidad_vivienda_servicio = $ids_uv AND c.id_unico != 23 ");
                for ($ot = 0; $ot < count($otc); $ot++) { 
                    #* Buscar si concepto esta relacionado a la factura 
                    $crf = $con->Listar("SELECT * FROM gp_detalle_factura 
                        WHERE factura = $id_factura AND otros_conceptos = ".$otc[$ot][6]);
                    if(count($crf)>0) { 
                        #** Buscar Facturas siguientes con ese concepto ****#
                        $df = $con->Listar("SELECT DISTINCT df.* 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
                            WHERE uvms.unidad_vivienda_servicio IN ($ids_uv) 
                            AND f.periodo >$periodo
                            AND df.concepto_tarifa =".$otc[$ot][5]);
                        $dr  =0;
                        if(count($df)>0){ $dr = count($df);} else {$dr = 0;}
                        $vr = $otc[$ot][2]*$dr;
                        $saldo = $otc[$ot][2]*($otc[$ot][4]+$dr);
                        echo '<tr>';
                        echo '<td>'.$row[$i][1].' - '.$row[$i][0].'</td>';
                        echo '<td>'.$row[$i][8].'</td>';
                        echo '<td>'.$row[$i][7].'</td>';
                        echo '<td>'.ucwords(mb_strtolower($row[$i][2])).' - '.$row[$i][3].'</td>';
                        echo '<td>'.$otc[$ot][0].'</td>';                        
                        echo '<td>'.($otc[$ot][3]+$otc[$ot][4]).'</td>';                        
                        echo '<td>'.number_format($otc[$ot][2],2,'.',',').'</td>';                        
                        echo '<td>'.(($otc[$ot][3] + $otc[$ot][4]) - ($otc[$ot][4]+$dr)).'</td>';
                        echo '<td>'.($otc[$ot][4]+$dr).'</td>';
                        echo '<td>'.number_format($saldo,2,'.',',').'</td>';
                        echo '</tr>';
                    }
                }
            }
            
            ?>
        </table>
    </body>
</html>