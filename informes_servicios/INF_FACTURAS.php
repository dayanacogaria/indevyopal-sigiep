<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#28/09/2018 | Erica G. | Archivo Creado
#####################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Facturacion.xls");
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
if(!empty($_REQUEST['uso'])){
    $row = $con->Listar("SELECT f.id_unico, 
        s.nombre, s.codigo, p.codigo_catastral, 
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
        CONCAT_WS(' - ',p.codigo_catastral,p.nombre), f.numero_factura, uvms.id_unico, 
        DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.periodo, 
        f.fecha_factura , uvs.id_unico, 
        uv.codigo_ruta 
        FROM gp_factura f
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        WHERE uv.uso = ".$_REQUEST['uso']." AND s.id_unico BETWEEN ".$_REQUEST['s1']." AND ".$_REQUEST['s2']." 
        AND f.periodo =".$_REQUEST['p']." 
        ORDER BY cast(s.codigo as unsigned),cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC ");
} else {
$row = $con->Listar("SELECT f.id_unico, 
        s.nombre, s.codigo, p.codigo_catastral, 
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
        CONCAT_WS(' - ',p.codigo_catastral,p.nombre), f.numero_factura, uvms.id_unico, 
        DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), f.periodo, 
        f.fecha_factura , uvs.id_unico, 
        uv.codigo_ruta 
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
}
$periodoa   = periodoA($_REQUEST['p']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturación</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
           <th colspan="6" align="center"><strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>FACTURACIÓN SERVICIOS PÚBLICOS
                <br/>PERIODO: <?php echo $periodo; ?>
                <br/>ENTRE SECTOR:<?php echo $sector.' Y ',$sector2; ?>
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            echo '<tr>';
            echo '<td><strong>Sector</strong></td>';
            echo '<td><strong>Factura</strong></td>';
            echo '<td><strong>Código Sistema</strong></td>';
            echo '<td><strong>Código Ruta</strong></td>';
            echo '<td><strong>Tercero</strong></td>';
            echo '<td><strong>Total</strong></td>';
            echo '</tr>';        
            for ($i = 0; $i < count($row); $i++) {
                $factura    = $row[$i][0];
                $fecha_f    = $row[$i][9];
                $periodo    = $row[$i][10];
                $id_uvms    = $row[$i][8];
                $fecha_fac  = $row[$i][11];
                $uvs        = $row[$i][12];
                echo '<tr>';
                echo '<td>'.$row[$i][2].' - '.ucwords(mb_strtolower($row[$i][1])).'</td>';                  
                echo '<td>'.$row[$i][7].'</td>';
                echo '<td>'.$row[$i][6].'</td>';                   
                echo '<td>'.$row[$i][13].'</td>';                   
                echo '<td>'.ucwords(mb_strtolower($row[$i][4])).' - '.$row[$i][5].'</td>';  
                #** Buscar Valor Factura **#
                $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
                if(empty($vf[0][0])){
                    $valor =0;
                } else {
                    $valor =$vf[0][0];
                }
                #********* Buscar Unidad_v con otros medidores ********#
                $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                        WHERE unidad_vivienda_servicio = $uvs");
                $ids_uv = $ids_uv[0][0];
                #********* Buscar Si existe deuda anterior **********#
                $deuda_anterior = 0;
                $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.unidad_vivienda_servicio IN($ids_uv) AND f.periodo <= $periodoa");
                if(count($da)>0){
                    #*** Buscar Recaudo ***#
                    $id_df      = $da[0][0];
                    $valor_f    = $da[0][1];
                    $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE p.fecha_pago <= '$fecha_fac' AND dp.detalle_factura IN ($id_df)");
                    if(count(($rc))<=0){
                        $recaudo = 0;
                    }elseif(empty ($rc[0][0])){
                        $recaudo = 0;
                    } else {
                        $recaudo = $rc[0][0];
                    }
                    $deuda_anterior = $valor_f -$recaudo;
                }
                $valor = $valor+$deuda_anterior;
                echo '<td>'. $valor.'</td>';
                echo '</tr>';
             }
            
            ?>
        </table>
    </body>
</html>