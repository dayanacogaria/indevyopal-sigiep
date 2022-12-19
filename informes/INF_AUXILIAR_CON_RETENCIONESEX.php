<?php
################################################################################################################
#                                                                                                   MODIFICACIONES
#19/07/2017 | ERICA G. | ARCHIVO CREADO * INFORME AUXILIAR CONTABLE RETENCIONES   EXCEL                                                                                          
################################################################################################################
header("Content-type: application/vnd.ms-excel");
require_once("../Conexion/conexion.php");
require_once("../Conexion/ConexionPDO.php");
$con = new ConexionPDO();
session_start();
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Contable_Retenciones.xls");
ini_set('max_execution_time', 0); 
session_start();

$para = $_SESSION['anno'];
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $para";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno = $an[0];
##########RECEPCION VARIABLES###############
#CUENTA INICIAL
if (empty($_POST['sltctai'])) {
    $cuentaI = '1';
} else {
    $cuentaI = $_POST['sltctai'];
}
#CUENTA FINAL
if (empty($_POST['sltctaf'])) {
    $cuentaF = '9';
} else {
    $cuentaF = $_POST['sltctaf'];
}

#FECHA INICIAL
if (empty($_POST['fechaini'])) {
    $fechaY = $anno;
    $fechaI = $fechaY . '/01/01';
    $fecha1 = '01/01/' . $anno;
} else {
    $fecha1 = $_POST['fechaini'];
    $fecha_div = explode("/", $fecha1);
    $dia1 = $fecha_div[0];
    $mes1 = $fecha_div[1];
    $anio1 = $fecha_div[2];
    $fechaI = $anio1 . '/' . $mes1 . '/' . $dia1;
}
#FECHA FINAL
if (empty($_POST['fechafin'])) {
    $fechaF = date('Y/m/d');
    $fecha2 = date('d/m/Y');
} else {
    $fecha2 = $_POST['fechafin'];
    $fecha_div2 = explode("/", $fecha2);
    $dia2 = $fecha_div2[0];
    $mes2 = $fecha_div2[1];
    $anio2 = $fecha_div2[2];
    $fechaF = $anio2 . '/' . $mes2 . '/' . $dia2;
}

##CONSULTA DATOS COMPAÑIA##
$compa = $_SESSION['compania'];
$comp = "SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if (empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1] . ' - ' . $comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
#CREACION  HEAD EXCEL
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Auxiliar Contable Retenciones</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <th colspan="7" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>AUXILIAR CONTABLE DE RETENCIONES          
        <br/>ENTRE <?php echo $fecha1.' - '.$fecha2 ?>
        <br/>&nbsp;                 
        </strong>
    </th>
        <tr></tr>    
    <tr>
        <td><center><strong>TIPO CXP</strong></center></td>
        <td><center><strong>NÚMERO CXP</strong></center></td>
        <td><center><strong>FECHA</strong></center></td>
        <td><center><strong>NOMBRE TERCERO</strong></center></td>
        <td><center><strong>DESCRIPCIÓN</strong></center></td>
        <td><center><strong>VALOR RETENCIÓN</strong></center></td>
        <td><center><strong>BASE GRAVABLE</strong></center></td>
    
    </tr>

<?php
###################CONSULTA CUENTAS###########
$ctas = "SELECT DISTINCT 
            c.id_unico, c.codi_cuenta, LOWER(c.nombre)
        FROM 
            gf_detalle_comprobante dc 
        LEFT JOIN 
            gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
        LEFT JOIN 
            gf_retencion r ON r.comprobante = cn.id_unico 
        LEFT JOIN 
            gf_tipo_retencion tc ON r.tiporetencion = tc.id_unico 
        LEFT JOIN 
            gf_cuenta c ON tc.cuenta = c.id_unico 
        WHERE 
            cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND c.codi_cuenta BETWEEN '$cuentaI' AND '$cuentaF' 
            AND c.parametrizacionanno = $para 
            AND cn.parametrizacionanno = $para  
        ORDER BY c.codi_cuenta ASC";
$ctas = $mysqli->query($ctas);
if (mysqli_num_rows($ctas) > 0) {
    while ($row = mysqli_fetch_row($ctas)) { ?>
        <tr>
            <td colspan="7">
                <i><strong><?php echo ('Cuenta Retención: ' . $row[1] . ' - ' . ucwords($row[2]));?></strong></i>
            </td>
        </tr>
        <?php
        #############BUSCAR ORDENES DE PAGO QUE TIENEN RETENCION Y ESA CUENTA##########
        $cp = "SELECT DISTINCT cn.id_unico, 
           tc.sigla, cn.numero, 
           DATE_FORMAT(cn.fecha, '%d/%m/%Y'), 
            r.valorretencion, r.retencionbase , 
            IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE , cn.descripcion,  r.id_unico 
            FROM gf_comprobante_cnt cn 
            LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante 
            LEFT JOIN gf_retencion r ON r.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_retencion tret ON r.tiporetencion = tret.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tercero tr ON tr.id_unico = cn.tercero 
            WHERE tret.cuenta = $row[0] AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND cn.parametrizacionanno = $para 
            ORDER BY tc.sigla, cn.numero, cn.fecha  ";
        $cp = $mysqli->query($cp);
        if (mysqli_num_rows($cp) > 0) {
            $subValor =0;
            $subBase   =0;
            while ($row1 = mysqli_fetch_row($cp)) {
                $cntTercero = ucwords(mb_strtolower($row1[6]));
                $descripcion = (($row1[7]));
                $cntTipo = mb_strtoupper($row1[1]);
                $cntN = $row1[2];
                $cntFecha = $row1[3];
                $valor = $row1[4];
                $base = $row1[5];
                ?>
                <tr>
                    <td align="right">
                        <?php echo ($cntTipo);?>
                    </td>
                    <td>
                        <?php echo ($cntN);?>
                    </td>
                    <td>
                        <?php echo ($cntFecha);?>
                    </td>
                    <td>
                        <?php echo ($cntTercero);?>
                    </td>
                    <td>
                        <?php echo ($descripcion);?>
                    </td>
                    <td align="right">
                        <?php echo number_format($valor, 2, '.', ',');?>
                    </td>
                    <td align="right">
                        <?php echo number_format($base, 2, '.', ',');?>
                    </td>
                </tr>
                <?php 
                $subValor = $subValor + $valor;
                $subBase = $subBase + $base;
                
            }
        }
        #SUBTOTALES
        ?>
    <tr>
        <td colspan="5" align="right">
            <strong>TOTAL</strong>
        </td>
        <td align="right"><strong><?php echo number_format($subValor, 2, '.', ',');?></strong></td>
        <td align="right"><strong><?php echo number_format($subBase, 2, '.', ',');?></strong></td>
    </tr>
<?php
    $totalV = $totalV + $subValor;
    $totalB = $totalB + $subBase;
    } ?>
    <tr>
        <td colspan="5" align="right">
            <strong>TOTAL FINAL</strong>
        </td>
        <td align="right"><strong><?php echo number_format($totalV, 2, '.', ',');?></strong></td>
        <td align="right"><strong><?php echo number_format($totalB, 2, '.', ',');?></strong></td>
    </tr>
    <?php 
}
?>
</table>
</body>
</html>