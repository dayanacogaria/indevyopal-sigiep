<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#25/09/2018 | Erica G. | Archivo Creado
#####################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Lecturas.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once("../jsonServicios/funcionesServicios.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$id_sector       = $_REQUEST['s'];
if(empty($_REQUEST['s2'])){
    $id_sectorf       = $_REQUEST['s'];
} else {
    $id_sectorf       = $_REQUEST['s2'];
}
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
    WHERE id_unico =".$_REQUEST['s']);
$sector = $s[0][2].' - '.ucwords(mb_strtolower($s[0][1]));
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
             s.codigo, s.nombre, 
             uv.codigo_ruta  
        FROM gp_unidad_vivienda_medidor_servicio uvms 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uvs.estado_servicio = 1 
        AND uv.sector BETWEEN ".$id_sector." AND ".$id_sectorf." 
        AND m.estado_medidor != 3     
        ORDER BY cast(s.codigo as unsigned),cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");
$periodoa =periodoA ($id_periodo);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Lecturas</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php 
            if(empty($_REQUEST['t'])) { 
                if(empty($_REQUEST['s2'])){
                    echo '<th colspan="7" align="center"><strong>';
                } else {
                    echo '<th colspan="8" align="center"><strong>';
                }
                echo '<br/>'.$razonsocial;
                echo '<br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer;
                echo '<br/>&nbsp;';
                echo '<br/>TOMA DE LECTURAS';
                echo '<br/>PERIODO:'.$periodo; 
                if(empty($_REQUEST['s2'])){       
                echo '<br/>SECTOR:'.$sector;
                }
                echo '<br/>&nbsp;</strong>';
                echo '</th>';
                echo '<tr></tr>';
                echo '<tr>';
                echo '<td><strong>Código Sistema</strong></td>';
                if(!empty($_REQUEST['s2'])){
                    echo '<td><strong>Sector</strong></td>';
                }                
                echo '<td><strong>Código Ruta</strong></td>';
                echo '<td><strong>Dirección</strong></td>';
                echo '<td><strong>Tercero</strong></td>';
                echo '<td><strong>Lectura Anterior</strong></td>';
                echo '<td><strong>Lectura Actual</strong></td>';
                echo '<td><strong>Valor</td>';
                echo '</tr>';        
                for ($i = 0; $i < count($row); $i++) {
                    $id_uvms = $row[$i][0];
                    #*** Buscar Lectura Anterior ***#
                    $la = $con->Listar("SELECT valor FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $periodoa");
                    if(empty($la[0][0])){
                        $la = 0;
                    } else {
                        $la = $la[0][0];
                    }
                    #*** Buscar Lectura Actual ***#
                    $lac  ="";
                    $lact = $con->Listar("SELECT valor FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $id_periodo");

                    if(count($lact)>0) {
                        $lac = $lact[0][0];
                    }
                    if($lac==""){
                        $lectura  = "";
                    } else {
                        $lectura  = $lac -$la;
                        if($lectura ==($la*-1)){
                            $lectura = 0;
                        } 
                    }
                    
                    $lam        = 0;
                    $lactualm   = 0;
                    if(strlen($la)>3){  $lam = substr($la,-3);} else { $lam = $la;}
                    if(strlen($lac)>3){ $lactualm = substr($lac,-3); } else { $lactualm = $lac; }
                    echo '<tr>';
                    echo '<td style="mso-number-format:\@">'.$row[$i][1].'</td>';
                    if(!empty($_REQUEST['s2'])){
                        echo '<td>'.$row[$i][4].' - '.ucwords(mb_strtolower($row[$i][5])).'</td>';
                    }                    
                    echo '<td style="mso-number-format:\@">'.$row[$i][6].'</td>';
                    echo '<td>'.$row[$i][2].'</td>';
                    echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
                    echo '<td>'.$lam.'</td>';
                    echo '<td>'.$lactualm.'</td>';
                    echo '<td>'.$lectura.'</td>';
                    echo '</tr>';
                }
            } elseif($_REQUEST['t']==1){
                if(empty($_REQUEST['s2'])){
                    echo '<th colspan="8" align="center"><strong>';
                } else {
                    echo '<th colspan="9" align="center"><strong>';
                }
                echo '<br/>'.$razonsocial;
                echo '<br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer;
                echo '<br/>&nbsp;';
                echo '<br/>LECTURAS CRITICAS';
                echo '<br/>PERIODO:'.$periodo; 
                if(empty($_REQUEST['s2'])){       
                echo '<br/>SECTOR:'.$sector;
                }
                echo '<br/>&nbsp;</strong>';
                echo '</th>';
                echo '<tr></tr>';
                echo '<tr>';
                echo '<td><strong>Código Interno</strong></td>';
                if(!empty($_REQUEST['s2'])){
                    echo '<td><strong>Sector</strong></td>';
                }
                echo '<td><strong>Código Ruta</strong></td>';
                echo '<td><strong>Tercero</strong></td>';
                echo '<td><strong>Dirección</strong></td>';
                echo '<td><strong>Lectura Anterior</strong></td>';
                echo '<td><strong>Lectura Actual</strong></td>';
                echo '<td><strong>Valor</td>';
                echo '<td><strong>Observaciones</td>';
                echo '</tr>';        
                for ($i = 0; $i < count($row); $i++) {
                    $id_uvms = $row[$i][0];
                    #*** Buscar Lectura Anterior ***#
                    $la = $con->Listar("SELECT valor FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $periodoa");
                    if(empty($la[0][0])){
                        $la = 0;
                    } else {
                        $la = $la[0][0];
                    }
                    #*** Buscar Lectura Actual ***#
                    $lac  ="";
                    $lact = $con->Listar("SELECT valor FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $id_periodo");

                    if(count($lact)>0) {
                        $lac = $lact[0][0];
                    }
                    if($lac==""){
                        $lectura  = "";
                    } else {
                        $lectura  = $lac -$la;
                        if($lectura ==($la*-1)){
                            $lectura = 0;
                        } 
                    }
                    $m = 0;
                    $msj ="<td></td>";
                    if($lac == ""){
                        $m = 1;
                        $msj = '<td bgcolor="#95CAED">Sin Lectura</td>';
                    } else {
                        if($lectura ==0){
                            $m = 1;
                            $msj = '<td bgcolor="#4DDB8C">Cantidad en 0</td>';
                        } elseif($lectura<0) {
                            $m = 1;
                            $msj = '<td bgcolor="#F5A1A1">Cantidad menor que 0</td>';
                        }else {
                            #** Buscar Promedio **#
                            $promedio = promedioLectura($periodoa, $lectura,$id_uvms,$la);
                            if($promedio>0){
                                $vp = round(($lectura * 100)/$promedio);
                                if($vp>150){
                                    $m = 1;
                                    $msj = '<td bgcolor="#D7A24B">Valor excede el 50% del promedio</td>';
                                }
                            }
                        }
                    }
                    $lam        = 0;
                    $lactualm   = 0;
                    if(strlen($la)>3){  $lam = substr($la,-3);} else { $lam = $la;}
                    if(strlen($lac)>3){ $lactualm = substr($lac,-3); } else { $lactualm = $lac; }
                    if($m==1){
                        echo '<tr>';
                        echo '<td style="mso-number-format:\@">'.$row[$i][1].'</td>';
                        if(!empty($_REQUEST['s2'])){
                            echo '<td>'.$row[$i][4].' - '.ucwords(mb_strtolower($row[$i][5])).'</td>';
                        }
                        echo '<td style="mso-number-format:\@">'.$row[$i][6].'</td>';
                        echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
                        echo '<td>'.$row[$i][2].'</td>';                        
                        echo '<td>'.$lam.'</td>';
                        echo '<td>'.$lactualm.'</td>';
                        echo '<td>'.$lectura.'</td>';
                        echo $msj;
                        echo '</tr>';
                    }
                    
                }
            }
            ?>
        </table>
    </body>
</html>