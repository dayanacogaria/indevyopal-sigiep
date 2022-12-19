<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#28/09/2018 | Erica G. | Archivo Creado
#####################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Efecty.xls");
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
        f.fecha_factura, f.periodo, 
        f.fecha_factura, uvs.id_unico 
        FROM gp_factura f
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        WHERE s.id_unico BETWEEN ".$_REQUEST['sector1']." AND ".$_REQUEST['sector2']." 
        AND f.periodo =".$_REQUEST['periodo']." ORDER BY f.numero_factura ASC");

$periodoa   = periodoA($_REQUEST['periodo']);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturación</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php 
            echo '<tr>';
            echo '<td>"01"|</td>';
            echo '<td>FACTURA</td>';
            echo '<td>|</td>';
            echo '<td>TOTAL</td>';
            echo '<td>|</td>';
            echo '<td>FECHA</td>';
            echo '<td>|</td>';
            echo '<td>NOMBRES</td>';
            echo '<td>|</td>';
            echo '<td>APELLIDO1</td>';
            echo '<td>|</td>';
            echo '<td>APELLIDO2</td>';
            echo '<td>|</td>';
            echo '<td>CODIGO</td>';
            echo '<td>|</td>';
            echo '<td>CAMPO5</td>';
            echo '</tr>';        
            for ($i = 0; $i < count($row); $i++) {
                $factura = $row[$i][0];
                $id_uvms = $row[$i][8];
                $fecha_fac =$row[$i][9];
                #*** Crear Código ***#
                if($i>=0 && $i<10){
                    $codigo ='A00'.($i+1);
                }
                if($i>=10 && $i<100){
                    $codigo ='A0'.($i+1);
                }
                if($i>=100){
                    $codigo ='A'.($i+1);
                }
                #*** Buscar Valor De la Factura **#
                 #** Buscar Valor Factura **#
                $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
                if(empty($vf[0][0])){
                    $valor =0;
                } else {
                    $valor =$vf[0][0];
                }
                $uvs  = $row[$i][12];
                #********* Buscar Unidad¿_v con otros medidores ********#
                $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                        WHERE unidad_vivienda_servicio = $uvs");
                $ids_uv = $ids_uv[0][0];
                #********* Buscar Si existe deuda anterior **********#
                $deuda_anterior = 0;
                $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $periodoa");
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
                
                echo '<tr>';
                    echo '<td>"02"|"</td>';
                    echo '<td>'.$row[$i][7].'</td>';
                    echo '<td>"|</td>';
                    echo '<td>'.$valor.'</td>';
                    echo '<td>|</td>';
                    echo '<td>'.$row[$i][9].' 8:00:00 a. m.'.'</td>';
                    echo '<td>|"</td>';
                    echo '<td>'.$row[$i][4].'</td>';
                    echo '<td>"|"</td>';
                    echo '<td>NA</td>';
                    echo '<td>"|"</td>';
                    echo '<td>NA</td>';
                    echo '<td>"|"</td>';
                    echo '<td>'.$codigo.'</td>';
                    echo '<td>"|"</td>';
                    echo '<td>NA"</td>';
                echo '</tr>';     

            }
            
            ?>
        </table>
    </body>
</html>