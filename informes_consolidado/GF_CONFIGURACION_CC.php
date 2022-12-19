<?php
@session_start();
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);

$usuario    = $_SESSION['usuario'];
$fechaActual= date('d/m/Y');
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];//$_REQUEST['compania'];
$anno       = $_SESSION['anno'];//$_REQUEST['annio'];
$nanno      = anno($anno);

if($_REQUEST['t']==1){
    header("Content-Disposition: attachment; filename=Informe_Comparacion_Distribucion.xls");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Comparación Distribución Centros Costo</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="660" align="center"><strong>
            <?='COMPARACIÓN DISTRIBUCIÓN CENTROS DE COSTO';?>
            <br/>&nbsp;
            </strong>
        </th>
        <?php 
        echo '<tr>';
        $rowcp = $con->Listar("SELECT DISTINCT t.id_unico, pa.id_unico, t.razonsocial, t.numeroidentificacion 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE pa.anno = $nanno and t.id_unico = $compania");
        echo '<td colspan="4"><strong><center>'.$rowcp[0][2].' - '.$rowcp[0][3].'</center></strong></td>';
        $rowc = $con->Listar("SELECT DISTINCT t.id_unico, pa.id_unico, t.razonsocial, t.numeroidentificacion 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE pa.anno = $nanno and t.id_unico NOT IN (1,$compania) ORDER BY pa.id_unico ");
        
        for ($i = 0; $i < count($rowc); $i++) {
            echo '<td colspan="4"><strong><center>'.$rowc[$i][2].' - '.$rowc[$i][3].'</center></strong></td>';
        }
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong><center>CONCEPTO</center></strong></td>';
        echo '<td><strong><center>CENTRO COSTO</center></strong></td>';
        echo '<td><strong><center>CUENTA</center></strong></td>';
        echo '<td><strong><center>PORCENTAJE</center></strong></td>';
        for ($i = 0; $i < count($rowc); $i++) {
            echo '<td><strong><center>CONCEPTO</center></strong></td>';
            echo '<td><strong><center>CENTRO COSTO</center></strong></td>';
            echo '<td><strong><center>CUENTA</center></strong></td>';
            echo '<td><strong><center>PORCENTAJE</center></strong></td>';
        }
        echo '</tr>';
        $pac = $rowcp[0][1];
        $rowp = $con->Listar("SELECT DISTINCT cf.id_unico, c.id_unico, 
                c.nombre, cc.nombre, cta.codi_cuenta, cta.nombre , cf.porcentaje 
                FROM gf_configuracion_distribucion cf
                LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
                LEFT JOIN gf_centro_costo cc ON cf.centro_costo = cc.id_unico
                LEFT JOIN gf_cuenta cta ON cf.cuenta = cta.id_unico 
                WHERE c.parametrizacionanno = $pac ");
        for ($p = 0; $p < count($rowp); $p++) {
            echo '<tr>';
            echo '<td>'.$rowp[$p][2].'</td>';
            echo '<td>'.$rowp[$p][3].'</td>';
            echo '<td>'.$rowp[$p][4].' - '.$rowp[$p][5].'</td>';
            echo '<td>'.$rowp[$p][6].'</td>';
            for ($i = 0; $i < count($rowc); $i++) {
                $pc = $rowc[$i][1];
                $rowcn = $con->Listar("SELECT DISTINCT cf.id_unico, cpa.nombre, 
                    ccpa.nombre, ctapa.codi_cuenta ,ctapa.nombre ,
                    cca.porcentaje 
                    FROM gf_configuracion_distribucion cf
                    LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
                    LEFT JOIN gf_concepto cpa ON c.nombre = cpa.nombre AND cpa.parametrizacionanno = $pc
                    LEFT JOIN gf_centro_costo cc ON cf.centro_costo = cc.id_unico
                    LEFT JOIN gf_centro_costo ccpa ON cc.nombre = ccpa.nombre AND ccpa.parametrizacionanno = $pc 
                    LEFT JOIN gf_cuenta cta ON cf.cuenta = cta.id_unico 
                    LEFT JOIN gf_cuenta ctapa ON cta.codi_cuenta = ctapa.codi_cuenta AND ctapa.parametrizacionanno = $pc 
                    LEFT JOIN gf_configuracion_distribucion cca ON cca.concepto = cpa.id_unico AND cca.centro_costo = ccpa.id_unico AND cca.cuenta = ctapa.id_unico 
                    WHERE cf.id_unico = ".$rowp[$p][0]);
                echo '<td>'.$rowcn[0][1].'</td>';
                echo '<td>'.$rowcn[0][2].'</td>';
                echo '<td>'.$rowcn[0][3].' - '.$rowcn[0][4].'</td>';
                echo '<td>'.$rowcn[0][5].'</td>';
            }
            echo '</tr>';
        }
        
        ?>
    </table>
    </body>
    </html>

<?php }  else {
    header("Content-Disposition: attachment; filename=Informe_Comparacion_Traslados.xls");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Comparación Traslados</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="990" align="center"><strong>
            <?='COMPARACIÓN CONFIGURACIÓN TRASLADOS';?>
            <br/>&nbsp;
            </strong>
        </th>
        <?php 
        echo '<tr>';
        $rowcp = $con->Listar("SELECT DISTINCT t.id_unico, pa.id_unico, t.razonsocial, t.numeroidentificacion 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE pa.anno = $nanno and t.id_unico = $compania");
        echo '<td colspan="6"><strong><center>'.$rowcp[0][2].' - '.$rowcp[0][3].'</center></strong></td>';
        $rowc = $con->Listar("SELECT DISTINCT t.id_unico, pa.id_unico, t.razonsocial, t.numeroidentificacion 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE pa.anno = $nanno and t.id_unico NOT IN (1,$compania) ORDER BY pa.id_unico ");
        
        for ($i = 0; $i < count($rowc); $i++) {
            echo '<td colspan="6"><strong><center>'.$rowc[$i][2].' - '.$rowc[$i][3].'</center></strong></td>';
        }
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong><center>CUENTA TRASLADO</center></strong></td>';
        echo '<td><strong><center>CENTRO COSTO</center></strong></td>';
        echo '<td><strong><center>CUENTA DÉBITO</center></strong></td>';
        echo '<td><strong><center>CENTRO COSTO DÉBITO</center></strong></td>';
        echo '<td><strong><center>CUENTA CRÉDITO</center></strong></td>';
        echo '<td><strong><center>CENTRO COSTO CRÉDITO</center></strong></td>';
        
        for ($i = 0; $i < count($rowc); $i++) {
            echo '<td><strong><center>CUENTA TRASLADO</center></strong></td>';
            echo '<td><strong><center>CENTRO COSTO</center></strong></td>';
            echo '<td><strong><center>CUENTA DÉBITO</center></strong></td>';
            echo '<td><strong><center>CENTRO COSTO DÉBITO</center></strong></td>';
            echo '<td><strong><center>CUENTA CRÉDITO</center></strong></td>';
            echo '<td><strong><center>CENTRO COSTO CRÉDITO</center></strong></td>';
        }
        echo '</tr>';
        $pac = $rowcp[0][1];
        $rowp = $con->Listar("SELECT DISTINCT 
                cft.id_unico, CONCAT_WS(' - ',ct.codi_cuenta,ct.nombre), cc.nombre, 
                CONCAT_WS(' - ',ctd.codi_cuenta,ctd.nombre), ccd.nombre, 
                CONCAT_WS(' - ',ctc.codi_cuenta,ctc.nombre), ccc.nombre 
            FROM gf_configuracion_traslado cft 
            LEFT JOIN gf_cuenta ct ON cft.cuenta_traslado = ct.id_unico 
            LEFT JOIN gf_centro_costo cc ON cft.centro_costo = cc.id_unico 
            LEFT JOIN gf_cuenta ctd ON cft.cuenta_debito = ctd.id_unico 
            LEFT JOIN gf_centro_costo ccd ON cft.centro_costo_debito = ccd.id_unico 
            LEFT JOIN gf_cuenta ctc ON cft.cuenta_credito = ctc.id_unico 
            LEFT JOIN gf_centro_costo ccc ON cft.centro_costo_credito = ccc.id_unico 
            WHERE ct.parametrizacionanno = $pac ");
        for ($p = 0; $p < count($rowp); $p++) {
            echo '<tr>';
            echo '<td>'.$rowp[$p][1].'</td>';
            echo '<td>'.$rowp[$p][2].'</td>';
            echo '<td>'.$rowp[$p][3].'</td>';
            echo '<td>'.$rowp[$p][4].'</td>';
            echo '<td>'.$rowp[$p][5].'</td>';
            echo '<td>'.$rowp[$p][6].'</td>';
            for ($i = 0; $i < count($rowc); $i++) {
                $pc = $rowc[$i][1];
                $rowcn = $con->Listar("SELECT DISTINCT 
                cft.id_unico, CONCAT_WS(' - ',ctpa.codi_cuenta,ctpa.nombre), ccpa.nombre, 
                CONCAT_WS(' - ',ctdpa.codi_cuenta,ctdpa.nombre), ccdpa.nombre, 
                CONCAT_WS(' - ',ctcpa.codi_cuenta,ctc.nombre), cccpa.nombre 
                FROM gf_configuracion_traslado cft 
                LEFT JOIN gf_cuenta ct ON cft.cuenta_traslado = ct.id_unico 
                LEFT JOIN gf_cuenta ctpa ON ct.codi_cuenta = ctpa.codi_cuenta AND ctpa.parametrizacionanno = $pc 
                LEFT JOIN gf_centro_costo cc ON cft.centro_costo = cc.id_unico 
                LEFT JOIN gf_centro_costo ccpa ON cc.nombre = ccpa.nombre AND ccpa.parametrizacionanno = $pc 
                LEFT JOIN gf_cuenta ctd ON cft.cuenta_debito = ctd.id_unico 
                LEFT JOIN gf_cuenta ctdpa ON ctd.codi_cuenta = ctdpa.codi_cuenta AND ctdpa.parametrizacionanno = $pc 
                LEFT JOIN gf_centro_costo ccd ON cft.centro_costo_debito = ccd.id_unico 
                LEFT JOIN gf_centro_costo ccdpa ON ccd.nombre = ccdpa.nombre AND ccdpa.parametrizacionanno = $pc 
                LEFT JOIN gf_cuenta ctc ON cft.cuenta_credito = ctc.id_unico 
                LEFT JOIN gf_cuenta ctcpa ON ctc.codi_cuenta = ctcpa.codi_cuenta AND ctcpa.parametrizacionanno = $pc
                LEFT JOIN gf_centro_costo ccc ON cft.centro_costo_credito = ccc.id_unico 
                LEFT JOIN gf_centro_costo cccpa ON ccc.nombre = cccpa.nombre AND cccpa.parametrizacionanno = $pc 
                WHERE cft.id_unico = ".$rowp[$p][0]);
                echo '<td>'.$rowcn[0][1].'</td>';
                echo '<td>'.$rowcn[0][2].'</td>';
                echo '<td>'.$rowcn[0][3].'</td>';
                echo '<td>'.$rowcn[0][4].'</td>';
                echo '<td>'.$rowcn[0][5].'</td>';
                echo '<td>'.$rowcn[0][6].'</td>';
            }
            echo '</tr>';
        }
        
        ?>
    </table>
    </body>
    </html>
<?php } ?>