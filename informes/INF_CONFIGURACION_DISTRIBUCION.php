<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Configuracion_Distribucion_Costos.xls");
require_once("../Conexion/conexion.php");
require_once("../Conexion/ConexionPDO.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0); 
$con = new ConexionPDO();
session_start();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
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
$ruta_logo   = $rowC[0][6];

#* Consulta Principal
$row = $con->Listar("SELECT cf.id_unico, LOWER(c.nombre), 
	UPPER(cc.sigla), LOWER(cc.nombre), 
    cta.codi_cuenta, LOWER(cta.nombre), 
    cf.porcentaje 
FROM gf_configuracion_distribucion cf 
LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
LEFT JOIN gf_centro_costo cc ON cf.centro_costo = cc.id_unico 
LEFT JOIN gf_cuenta cta ON cf.cuenta = cta.id_unico 
WHERE c.parametrizacionanno = $anno  
ORDER BY c.id_unico ASC");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Configuración Distribución Costos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <th colspan="4" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>CONFIGURACIÓN DISTRIBUCIÓN DE COSTOS      
        <br/><?php $nanno ?>
        <br/>&nbsp;                 
        </strong>
    </th>
        <tr></tr>    
    <tr>
        <td><center><strong>CONCEPTO</strong></center></td>
        <td><center><strong>CENTRO DE COSTO</strong></center></td>
        <td><center><strong>CUENTA</strong></center></td>
        <td><center><strong>PORCENTAJE</strong></center></td>
    </tr>
    <?PHP 
    for ($i = 0; $i < count($row); $i++) {
        echo '<tr>';
        echo '<td>'.ucwords($row[$i][1]).'</td>';
        echo '<td>'.$row[$i][2].' - '.ucwords($row[$i][3]).'</td>';
        echo '<td>'.$row[$i][4].' - '.ucwords($row[$i][5]).'</td>';
        echo '<td>'.$row[$i][6].'%'.'</td>';
        echo '</tr>';
    }
    
    ?>    
</table>
</body>
</html>    