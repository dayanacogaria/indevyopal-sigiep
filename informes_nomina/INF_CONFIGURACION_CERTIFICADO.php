<?php 
header("Content-Type: text/html;charset=utf-8");
header("Content-Disposition: attachment; filename=Configuracion_certificado_ingresos.xls");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time', 0);
session_start();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
$rowC       = $con->Listar("SELECT 
        ter.id_unico,
        ter.razonsocial,
        UPPER(ti.nombre),
        IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
            ter.numeroidentificacion, 
            CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
        dir.direccion,
        tel.valor,
        ter.ruta_logo 
    FROM            
        gf_tercero ter
    LEFT JOIN 	
        gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
    LEFT JOIN       
        gf_direccion dir ON dir.tercero = ter.id_unico
    LEFT JOIN 	
        gf_telefono  tel ON tel.tercero = ter.id_unico
    WHERE 
        ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Configuración Certificado de Ingresos y Retenciones</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="4" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>&nbsp;CONFIGURACIÓN CERTIFICADO INGRESOS Y RETENCIONES
        <br/>&nbsp;
        </strong>
    </th>
    <?php 
    echo '<tr>';
    echo '<td><strong>TIPO CONCEPTO</strong></td>';
    echo '<td><strong>NÚMERO</strong></td>';
    echo '<td><strong>NOMBRE</strong></td>';
    echo '<td><strong>CONCEPTOS NÓMINA</strong></td>';
    echo '</tr>';
    $row = $con->Listar("SELECT cf.id_unico, IF(cc.tipo=1, 'Concepto de los Ingresos', 'Concepto de los Aportes'), 
        cc.numero, cc.nombre,
    GROUP_CONCAT(c.codigo,' ',  c.descripcion)
    FROM gn_configuracion_certificado cf
    LEFT JOIN gn_concepto_certificado cc ON cf.concepto_certificado = cc.id_unico 
    LEFT JOIN gn_concepto c ON cf.concepto_nomina = c.id_unico 
    WHERE cf.parametrizacionanno = $anno
    GROUP BY cf.concepto_certificado");
    for ($i = 0; $i < count($row); $i++) {
        echo '<tr>';
        echo '<td>'.$row[$i][1].'</td>';
        echo '<td>'.$row[$i][2].'</td>';
        echo '<td>'.$row[$i][3].'</td>';
        echo '<td>'.str_replace(",","&nbsp;<br/>",$row[$i][4]).'</td>';
        echo '</tr>';
    }
    ?>
</table>
</body>
</html>
