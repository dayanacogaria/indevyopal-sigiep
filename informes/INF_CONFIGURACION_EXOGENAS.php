<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#11/05/2018 | Erica G. | Archivo Creado
####/################################################################################
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
header("Content-Disposition: attachment; filename=Informe_Configuracion:Exogenas.xls");
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
#**********Recepción Variables ****************#
$formato    = $_REQUEST['formato'];
$fm = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE id_unico = $formato");
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
            <title>Informe Configuración Exógenas</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="2" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/>Configuración Exógenas 
                    <br/>Formato: <?PHP echo $fm[0][1].' - '.$fm[0][2]; ?> 
                    <br/>&nbsp;</strong>
                </th>
                <?php 
                echo '<tr>';
                echo '<th class="cabeza cursor cb">Cuenta</th>';
                echo '<th class="cabeza cursor cb">'.$fm[0][1].' - '.$fm[0][2].'</th>';
                echo '</tr>';
                $sql  = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) FROM gf_cuenta 
                            WHERE parametrizacionanno = $anno 
                            AND (movimiento=1 OR auxiliartercero =1 OR auxiliarproyecto = 1) 
                            ORDER BY codi_cuenta ASC");
                for ($s = 0; $s < count($sql); $s++) {
                    $cuenta = $sql[$s][0];
                    echo '<tr>';
                    echo '<td>'.$sql[$s][1].' - '.ucwords(($sql[$s][2])).'</td>';
                    echo '<td>';
                    # ** Buscar Si Existe Configuración ** #
                    $conf    = $con->Listar("SELECT cf.id_unico, cn.codigo, cn.nombre, 
                                cf.concepto_exogenas, cn.id_unico 
                                FROM gf_configuracion_exogenas cf 
                                LEFT JOIN gf_concepto_exogenas cn ON cf.concepto_exogenas = cn.id_unico 
                                WHERE cf.cuenta = $cuenta AND cn.formato= $formato");
                    if(count($conf)>0){
                        echo $conf[0][1].' - '.$conf[0][2];
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