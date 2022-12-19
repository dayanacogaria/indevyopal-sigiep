<?php
#header("Content-Type: text/html;charset=utf-8");

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$nanno      = anno($anno);
$con        = new ConexionPDO();
$usuario    = $_SESSION['usuario'];

switch ($_REQUEST['tipo']){
    case 1:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_Sin_PPE.xls");
        $row = $con->Listar("SELECT DISTINCT SUBSTRING(pi.codi, 1,1), 
            CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial 
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_tercero t ON pi.compania = t.id_unico 
            GROUP BY pi.compania  
            having COUNT(DISTINCT SUBSTRING(pi.codi, 1,1))=1");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Instituciones Educativas Sin PPE</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="2" align="center"><strong>
                    <br/>&nbsp;INSTITUCIONES EDUCATIVAS SIN PROPIEDAD PLANTA Y EQUIPO
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
            </tr>
            <?php 
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][0]==1){
                    echo '<tr><td>'.$row[$i][1].'</td><td>'.$row[$i][2].'</td></tr>';
                }
            } ?>
        </table>
        </body>
        </html>
    <?php         
    break;
    #* Instituciones Educativas PPE en 0
    case 2:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_PPE_0.xls");
        $row = $con->Listar("SELECT DISTINCT SUBSTRING(pi.codi, 1,1), 
            CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial, 
            SUM(dm.valor)
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_tercero t ON pi.compania = t.id_unico 
            GROUP BY pi.compania , SUBSTRING( pi.codi, 1,1)
            HAVING SUM(dm.valor)=0 OR SUM(dm.valor) IS NULL");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Instituciones Educativas PPE en 0</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="2" align="center"><strong>
                    <br/>&nbsp;INSTITUCIONES EDUCATIVAS CON PROPIEDAD PLANTA Y EQUIPO EN 0
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
            </tr>
        <?php 
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][0]==2){
                    echo '<tr><td>'.$row[$i][1].'</td><td>'.$row[$i][2].'</td></tr>';
                }
            }?>
        </table>
        </body>
        </html>
    <?php 
    break;
    #Informe Instituciones Educativas PPE Sin Vida Útil
    case 3:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_Sin_Vida_Util.xls");
        $row = $con->Listar("SELECT DISTINCT SUBSTRING(pi.codi, 1,1), 
            CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial, 
            SUM(dm.vida_util)
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_tercero t ON pi.compania = t.id_unico 
            GROUP BY pi.compania , SUBSTRING( pi.codi, 1,1)
            HAVING SUM(dm.vida_util)=0 OR SUM(dm.vida_util) IS NULL");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Instituciones Educativas PPE Sin Vida Útil</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="2" align="center"><strong>
                    <br/>&nbsp;INSTITUCIONES EDUCATIVAS CON PROPIEDAD PLANTA Y EQUIPO SIN VIDA ÚTIL
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
            </tr>
        <?php 
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][0]==2){
                    echo '<tr><td>'.$row[$i][1].'</td><td>'.$row[$i][2].'</td></tr>';
                }
            }?>
        </table>
        </body>
        </html>
    <?php 
    break;    
    #Informe Instituciones Educativas Con Unica Dependencia
    case 4:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_Unica_Dependencia.xls");
        $row = $con->Listar("SELECT COUNT(DISTINCT m.dependencia ),
            CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial 
            FROM gf_movimiento m 
            LEFT JOIN gf_tercero t ON m.compania = t.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            WHERE tm.clase = 3 
            GROUP BY m.compania
            HAVING COUNT(DISTINCT m.dependencia )=1");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Instituciones Educativas PPE Única Dependecia</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="2" align="center"><strong>
                    <br/>&nbsp;INSTITUCIONES EDUCATIVAS CON ÚNICA DEPENDENCIA
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
            </tr>
        <?php 
            for ($i = 0; $i < count($row); $i++) {
                echo '<tr><td>'.$row[$i][1].'</td><td>'.$row[$i][2].'</td></tr>';
            }?>
        </table>
        </body>
        </html>
    <?php 
    break;
    #Resumen 
    case 5:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_PPE_0.xls");
        $row = $con->Listar("SELECT DISTINCT t.id_unico, 
        CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial 
        FROM gf_movimiento m 
        LEFT JOIN gf_tercero t ON m.compania = t.id_unico 
        LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Resumen Información Almacén</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="6" align="center"><strong>
                    <br/>&nbsp;RESUMEN INFORMACIÓN ALMACÉN
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
                <td><strong>NÚMERO DE ENTRADAS</strong></td>
                <td><strong>NÚMERO DE SALIDAS</strong></td>
                <td><strong>NÚMERO ELEMENTOS DEVOLUTIVOS</strong></td>
                <td><strong>NÚMERO ELEMENTOS PPE</strong></td>
            </tr>
        <?php 
            for ($i = 0; $i < count($row); $i++) {
                
                $rowe = $con->Listar("SELECT COUNT(m.id_unico) FROM gf_movimiento m 
                    LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                    WHERE m.compania = ".$row[$i][0]." AND tm.clase = 2");
                $rows = $con->Listar("SELECT COUNT(m.id_unico) FROM gf_movimiento m 
                    LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                    WHERE m.compania = ".$row[$i][0]." AND tm.clase = 3");
                $rowed = $con->Listar("SELECT COUNT(pi.id_unico) 
                FROM gf_detalle_movimiento dm 
                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                WHERE m.compania = ".$row[$i][0]." AND pi.codi LIKE '1%'");
                $rowpp = $con->Listar("SELECT SUM(dm.cantidad) 
                FROM gf_detalle_movimiento dm 
                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                WHERE m.compania = ".$row[$i][0]." AND pi.codi LIKE '2%'");
                echo '<tr>';
                echo '<td>'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$rowe[0][0].'</td>';
                echo '<td>'.$rows[0][0].'</td>';
                echo '<td>'.$rowed[0][0].'</td>';
                echo '<td>'.number_format($rowpp[0][0],0, '', '').'</td>';
                echo '</tr>';
                
            }?>
        </table>
        </body>
        </html>
    <?php 
    break;    
    #* Instituciones Educativas Devolutivo en 0
    case 6:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_Devolutivos_0.xls");
        $row = $con->Listar("SELECT DISTINCT SUBSTRING(pi.codi, 1,1), 
            CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial, 
            SUM(dm.valor)
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_tercero t ON pi.compania = t.id_unico 
            GROUP BY pi.compania , SUBSTRING( pi.codi, 1,1)
            HAVING SUM(dm.valor)=0 OR SUM(dm.valor) IS NULL");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Instituciones Educativas Devolutivos en 0</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="2" align="center"><strong>
                    <br/>&nbsp;INSTITUCIONES EDUCATIVAS CON DEVOLUTIVOS EN 0
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
            </tr>
        <?php 
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][0]==1){
                    echo '<tr><td>'.$row[$i][1].'</td><td>'.$row[$i][2].'</td></tr>';
                }
            }?>
        </table>
        </body>
        </html>
    <?php 
    break;
    
    #* Instituciones Movimiento
    case 7:
        header("Content-Disposition: attachment; filename=Instituciones_Educativas_Otros_Movimientos.xls");
        $row = $con->Listar("SELECT DISTINCT CONCAT_WS(' ',t.numeroidentificacion, t.digitoverficacion), t.razonsocial, 
            (SELECT COUNT(DISTINCT mt.id_unico) FROM gf_movimiento mt LEFT JOIN gf_tipo_movimiento tmm ON tmm.id_unico = mt.tipomovimiento 
             WHERE tmm.clase = 2 AND tmm.sigla != 'EDI' AND mt.compania = t.id_unico  ) as Entradas, 
            (SELECT COUNT(DISTINCT mt.id_unico) FROM gf_movimiento mt LEFT JOIN gf_tipo_movimiento tmm ON tmm.id_unico = mt.tipomovimiento 
             WHERE tmm.clase = 1 AND tmm.sigla != 'EDI' AND mt.compania = t.id_unico  ) as Orden, 
            (SELECT COUNT(DISTINCT mt.id_unico) FROM gf_movimiento mt LEFT JOIN gf_tipo_movimiento tmm ON tmm.id_unico = mt.tipomovimiento 
             WHERE tmm.clase = 5 AND tmm.sigla != 'EDI' AND mt.compania = t.id_unico  ) as Traslados
            FROM gf_movimiento m 
            LEFT JOIN gf_tercero t ON m.compania = t.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON tm.id_unico = m.tipomovimiento 
            WHERE tm.sigla != 'EDI' AND tm.clase not in (3)
            GROUP BY t.id_unico  
            ORDER BY `t`.`numeroidentificacion` ASC");

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Instituciones Educativas Con Movimientos</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="5" align="center"><strong>
                    <br/>&nbsp;INSTITUCIONES EDUCATIVAS CON MOVIMIENTOS
                    <br/>&nbsp;
                    </strong>
            </th>
            <tr>
                <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
                <td><strong>COMPAÑIA</strong></td>
                <td><strong>ENTRADAS</strong></td>
                <td><strong>ORDENES DE COMPRA</strong></td>
                <td><strong>TRASLADOS</strong></td>
            </tr>
        <?php 
            for ($i = 0; $i < count($row); $i++) {
                echo '<tr>';
                echo '<td>'.$row[$i][0].'</td>';
                echo '<td>'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.$row[$i][4].'</td>';
                echo '</tr>';
            }?>
        </table>
        </body>
        </html>
    <?php 
    break;
}
?>                