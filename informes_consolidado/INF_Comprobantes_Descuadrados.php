<?php
header("Content-Type: text/html;charset=utf-8");
header("Content-Disposition: attachment; filename=Comprobantes_Descuadrado.xls");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
session_start();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
ob_start();
ini_set('max_execution_time', 0);
##########RECEPCION VARIABLES###############

##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
$t = $_REQUEST['t'];
$ni = '';
switch ($t){
    #** Saldos Iniciales
    case 1:
        $ni = 'SALDOS INICIALES';
        $banco ="SELECT DISTINCT 
            cn.id_unico,
            cn.numero,
            tc.sigla,
            tc.nombre,
            date_format(cn.fecha,'%d/%m/%Y'),
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2, 
            t.razonsocial, 
            t.numeroidentificacion, 
            cn.id_unico 
        FROM
            gf_comprobante_cnt cn 
        LEFT JOIN
            gf_tipo_comprobante tc
        ON
            cn.tipocomprobante = tc.id_unico  
        LEFT JOIN 
            gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        LEFT JOIN 
            gf_tercero t ON pa.compania = t.id_unico 
        WHERE 
            pa.anno ='$nanno' 
            AND tc.clasecontable = 5 
            AND (tc.consolidado is null OR tc.consolidado = 2)  
        ORDER BY t.id_unico, tc.id_unico, cn.fecha ASC ";
    break;
    #** Todos Comprobantes
    case 2:
        $banco ="SELECT DISTINCT 
            cn.id_unico,
            cn.numero,
            tc.sigla,
            tc.nombre,
            date_format(cn.fecha,'%d/%m/%Y'),
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2, 
            t.razonsocial, 
            t.numeroidentificacion, 
            cn.id_unico 
        FROM
            gf_comprobante_cnt cn 
        LEFT JOIN
            gf_tipo_comprobante tc
        ON
            cn.tipocomprobante = tc.id_unico  
        LEFT JOIN 
            gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        LEFT JOIN 
            gf_tercero t ON pa.compania = t.id_unico 
        WHERE 
            pa.anno ='$nanno' 
            AND tc.clasecontable != 5 
            AND (tc.consolidado is null OR tc.consolidado = 2)  
        ORDER BY t.id_unico, tc.id_unico, cn.fecha ASC ";
    break;
    #Comprobantes Consolidado
    case 3:
        $ni = 'CONSOLIDADOS';
        $banco ="SELECT DISTINCT 
            cn.id_unico,
            cn.numero,
            tc.sigla,
            tc.nombre,
            date_format(cn.fecha,'%d/%m/%Y'),
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2, 
            t.razonsocial, 
            t.numeroidentificacion, 
            cn.id_unico 
        FROM
            gf_comprobante_cnt cn 
        LEFT JOIN
            gf_tipo_comprobante tc
        ON
            cn.tipocomprobante = tc.id_unico  
        LEFT JOIN 
            gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        LEFT JOIN 
            gf_tercero t ON cn.tercero = t.id_unico 
        WHERE 
            pa.id_unico ='$anno' 
            AND tc.consolidado = 1 
        ORDER BY t.id_unico, tc.id_unico, cn.fecha ASC ";
    break;
    #Comprobantes 7 +0
    case 4:
        $ni = 'CONSOLIDADOS';
        $banco ="SELECT DISTINCT 
            cn.id_unico,
            cn.numero,
            tc.sigla,
            tc.nombre,
            date_format(cn.fecha,'%d/%m/%Y'),
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0 AND c1.codi_cuenta like '7%') AS debito1,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 AND c1.codi_cuenta like '7%') AS credito2,
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0 AND c1.codi_cuenta like '7%') AS credito, 
             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0 AND c1.codi_cuenta like '7%') AS debito2, 
            t.razonsocial, 
            t.numeroidentificacion, 
            cn.id_unico 
        FROM
            gf_comprobante_cnt cn 
        LEFT JOIN
            gf_tipo_comprobante tc
        ON
            cn.tipocomprobante = tc.id_unico  
        LEFT JOIN 
            gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        LEFT JOIN 
            gf_tercero t ON cn.tercero = t.id_unico 
        WHERE 
            pa.anno ='$nanno' 
            AND (tc.consolidado is null OR tc.consolidado = 2)  
        ORDER BY t.id_unico, tc.id_unico, cn.fecha ASC ";
    break;
}
 

$banco = $mysqli->query($banco);
$total =0;
$total2 =0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comprobantes Descuadrados</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="7" align="center"><strong>
            <br/>&nbsp;COMPROBANTES DESCUADRADOS
            <br/><?php echo $ni;?>
            <br/>&nbsp;
            </strong>
    </th>
    <tr>
        <?php if($t==3) { ?>
        <td><center><strong>TERCERO</strong></center></td>
        <?php }  else { ?>
        <td><center><strong>COMPAÑIA</strong></center></td>    
        <?php }?>
        <td><center><strong>TIPO COMPROBANTE</strong></center></td>
        <td><center><strong>NUMERO</strong></center></td>
        <td><center><strong>FECHA</strong></center></td>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
        <td><center><strong>DIFERENCIA</strong></center></td>
    </tr>
<?php
while ($row = mysqli_fetch_row($banco)) {
    $numero = $row[1];
    $tipo = mb_strtoupper($row[2]).' - '. ucwords(mb_strtolower($row[3]));
    $fecha = $row[4];
    $debito1 =$row[5];
    $debitoN =$row[8]*-1;
    $credito1 =$row[7];
    $creditoN =$row[6]*-1;
    $debito = $debito1+$debitoN;
    $credito = $credito1+$creditoN;
    $tercero = $row[9].' - '.$row[10];
    $diferencia = ROUND(($debito -$credito),2);
    
    if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
        echo '<tr>';
        echo '<td>'.$tercero.'</td>';
        echo '<td>'.$tipo.'</td>';
        echo '<td>'.$numero.'</td>';
        echo '<td>'.$fecha.'</td>';
        echo '<td>'.number_format($debito,2,'.',',').'</td>';
        echo '<td>'.number_format($credito,2,'.',',').'</td>';
        echo '<td>'.number_format($diferencia,2,'.',',').'</td>';
        echo '</tr>';
            
    }
    
}
?>
</table>
</body>
</html>