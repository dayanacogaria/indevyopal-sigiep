<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#29/05/2018 | Erica G. | Archivo Creado
####/################################################################################
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
header("Content-Disposition: attachment; filename=Informe_Configuracion_Conceptos.xls");
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
#   **********      Recepción Variables     ****************    #
$tipo   = $_REQUEST['tipo'];
$tcrta  = $con->Listar("SELECT id_unico,
        LOWER(nombre), 
        dia_inicial,
        dia_final 
        FROM gp_tipo_cartera WHERE id_unico = $tipo");
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
$ruta_logo    = $rowC[0][6];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Informe Configuración Conceptos Facturación</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="3" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/>Configuración Conceptos Facturación 
                    <br/>Tipo Cartera: <?PHP echo ucwords($tcrta[0][1]).'  '.$tcrta[0][2].' - '.$tcrta[0][3]; ?> 
                    <br/>&nbsp;</strong>
                </th>
                <?php 
                echo '<tr>';
                echo '<th class="cabeza cursor cb">Concepto Facturación</th>';
                echo '<th class="cabeza cursor cb">Concepto Financiero</th>';
                echo '<th class="cabeza cursor cb">Rubro Fuente</th>';
                echo '</tr>';
                $sql  = $con->Listar("SELECT DISTINCT cp.id_unico, LOWER(tc.nombre), LOWER(cp.nombre) 
                        FROM gp_concepto cp 
                        LEFT JOIN gp_tipo_concepto tc ON cp.tipo_concepto = tc.id_unico 
                        ORDER BY cp.nombre ASC");
                for ($s = 0; $s < count($sql); $s++) {
                    $concepto = $sql[$s][0];
                    echo '<tr>';
                    echo '<td>'.'Tipo: '.ucwords($sql[$s][1]).' - Concepto: '.ucwords(($sql[$s][2])).'</td>';
                    $conf = $con->Listar("SELECT
                        cf.id_unico,
                        cr.id_unico,
                        LOWER(c.nombre),
                        rb.codi_presupuesto,
                        LOWER(rb.nombre),
                        rf.id_unico,
                        rbc.codi_presupuesto,
                        LOWER(rbc.nombre),
                        LOWER(f.nombre)
                      FROM
                        gp_configuracion_concepto cf
                      LEFT JOIN
                        gf_concepto_rubro cr ON cf.concepto_rubro = cr.id_unico
                      LEFT JOIN
                        gf_concepto c ON cr.concepto = c.id_unico
                      LEFT JOIN
                        gf_rubro_pptal rb ON cr.rubro = rb.id_unico
                      LEFT JOIN
                        gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico
                      LEFT JOIN
                        gf_rubro_pptal rbc ON rf.rubro = rbc.id_unico
                      LEFT JOIN
                        gf_fuente f ON rf.fuente = f.id_unico
                      WHERE cf.concepto =$concepto 
                        AND cf.parametrizacionanno = $anno 
                        AND cf.tipo_cartera = $tipo");
                    echo '<td>';
                    if(count($conf)>0){
                        echo ucwords($conf[0][2]).' - '.$conf[0][3].' '.ucwords($conf[0][4]);
                    } else {
                        echo '';
                    }
                    echo '</td>';
                    echo '<td>';
                    if(count($conf)>0){
                        echo $conf[0][6].' '.ucwords($conf[0][7]).' - '.ucwords($conf[0][8]);
                    } else {
                        echo '';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </body>
    </html>