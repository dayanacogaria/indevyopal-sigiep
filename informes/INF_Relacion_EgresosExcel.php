<?php
##############################################################################################
# ************************************* Modificaciones ************************************* #
##############################################################################################
#13/02/2018 | Erica G. | Arreglo Para Que Salgan Cheque, Banco, Egresos Sin Afectación
#31/08/2017 | Erica G. | Archivo Creado
##############################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Relacion_Egresos_Sin_Presupuesto.xls");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
session_start();
$anno = $_SESSION['anno'];
#************Datos Recibe************#
$fechaIn = $_POST['fechaini'];
$fechaFi= $_POST['fechafin'];
$fechaI = fechaC($fechaIn);
$fechaF = fechaC($fechaFi);
if(empty($_POST['sltTi'])){
    $terceroI =0;
} else {
    $terceroI =$_POST['sltTi'];
}

if(empty($_POST['sltTf']))    {
    $terceroF =9;
} else {
    $terceroF =$_POST['sltTf'];
}

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relación Egresos Sin Presupuesto</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="13" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>RELACION EGRESOS SIN PRESUPUESTO
           <br/> Fechas entre <?php echo $fechaIn.' y '.$fechaFi;?>
           <br/>&nbsp;

        </th>
  </tr>
  <tr>
      <td align="center" rowspan="2"><strong>FECHA</strong></td>
        <td align="center" rowspan="2"><strong>TIPO EGRESO</strong></td>
        <td align="center" rowspan="2"><strong>NÚMERO EGRESO</strong></td>
        <td align="center" rowspan="2"><strong>BENEFICIARIO</strong></td>
        <td align="center" rowspan="2"><strong>CÉDULA O NIT</strong></td>
        <td align="center" rowspan="2"><strong>DETALLE PAGO</strong></td>
        <td align="center" rowspan="2"><strong>VALOR COMPROBANTE PAGO</strong></td>
        <td align="center" rowspan="2"><strong>DESCUENTOS</strong></td>
        <td align="center" colspan="2"><strong>NETO PAGADO</strong></td>
        <td align="center" rowspan="2"><strong>BANCO</strong></td>
        <td align="center" rowspan="2"><strong>N° DE CUENTA</strong></td>
        <td align="center" rowspan="2"><strong>NÚMERO DE CHEQUE</strong></td>
        
  </tr>
    <tr>
        <td align="center"><strong>DÉBITO</strong></td>
        <td align="center"><strong>CRÉDITO</strong></td>
    </tr>  
    <?php 
        #**********Buscar Egresos Entre valores recibidos************###
       if($terceroF==9){
           $egresos = "SELECT
                        cp.id_unico,
                        DATE_FORMAT(cp.fecha, '%d/%m/%Y'),
                        cp.numero,
                        tp.sigla,
                        IF(CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) IS NULL 
                           OR CONCAT_WS(' ',
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
                        t.numeroidentificacion,
                        t.digitoverficacion,
                        cp.descripcion,
                        tcb.razonsocial,
                        cb.numerocuenta,
                        IF((SELECT
                                    GROUP_CONCAT(DISTINCT ' ', dcm.numero)
                            FROM
                                gf_detalle_comprobante_mov dcm
                            WHERE
                                dcm.comprobantecnt = dc.id_unico) IS NULL,'ND',
                            (SELECT
                                GROUP_CONCAT(DISTINCT ' ', dcm.numero)
                            FROM
                                gf_detalle_comprobante_mov dcm
                            WHERE
                                dcm.comprobantecnt = dc.id_unico)) AS 'No. De Cheque',
                            (SELECT SUM(IF(dcv.valor < 0,dcv.valor * -1,dcv.valor))
                            FROM gf_detalle_comprobante dcv
                            LEFT JOIN gf_cuenta cd ON dcv.cuenta = cd.id_unico
                            WHERE dcv.comprobante = cp.id_unico AND cd.clasecuenta = 11 
                            AND(( cd.naturaleza = 1 AND dcv.valor < 0) 
                                OR(cd.naturaleza = 2 AND dcv.valor > 0))) AS 'Neto Pagado',
                            (IF((c.naturaleza=1 AND dc.valor>0) OR (c.naturaleza=2 AND dc.valor<0),(IF(dc.valor < 0,dc.valor * -1,dc.valor)), 0 )) as 'Débito', 
                        (IF((c.naturaleza=1 AND dc.valor<0) OR (c.naturaleza=2 AND dc.valor>0),(IF(dc.valor < 0,dc.valor * -1,dc.valor)), 0 )) as 'Credito' 
               FROM
                    gf_comprobante_cnt cp
                LEFT JOIN gf_tipo_comprobante tp ON
                    cp.tipocomprobante = tp.id_unico
                LEFT JOIN gf_tercero t ON
                    t.id_unico = cp.tercero
                LEFT JOIN gf_comprobante_pptal cpp ON
                    cpp.numero = cp.numero AND tp.comprobante_pptal = cpp.tipocomprobante
                LEFT JOIN gf_detalle_comprobante dc ON
                    dc.comprobante = cp.id_unico
                LEFT JOIN gf_cuenta c ON
                    dc.cuenta = c.id_unico
                LEFT JOIN gf_cuenta_bancaria cb ON
                    c.id_unico = cb.cuenta
                LEFT JOIN gf_tercero tcb ON
                    cb.banco = tcb.id_unico
                WHERE cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                AND t.numeroidentificacion >= '$terceroI' 
                AND tp.clasecontable = 14 AND c.clasecuenta = 11 
                AND cp.parametrizacionanno = $anno 
                AND (SELECT COUNT(dt.id_unico) FROM gf_detalle_comprobante_pptal dt 
                                WHERE dt.comprobantepptal = cpp.id_unico) =0 
                ORDER BY cp.fecha, cp.numero ASC ";
       } else {
           $egresos = "SELECT
                        cp.id_unico,
                        DATE_FORMAT(cp.fecha, '%d/%m/%Y'),
                        cp.numero,
                        tp.sigla,
                        IF(CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) IS NULL 
                           OR CONCAT_WS(' ',
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
                        t.numeroidentificacion,
                        t.digitoverficacion,
                        cp.descripcion,
                        tcb.razonsocial,
                        cb.numerocuenta,
                        IF((SELECT
                                    GROUP_CONCAT(DISTINCT ' ', dcm.numero)
                            FROM
                                gf_detalle_comprobante_mov dcm
                            WHERE
                                dcm.comprobantecnt = dc.id_unico) IS NULL,'ND',
                            (SELECT
                                GROUP_CONCAT(DISTINCT ' ', dcm.numero)
                            FROM
                                gf_detalle_comprobante_mov dcm
                            WHERE
                                dcm.comprobantecnt = dc.id_unico)) AS 'No. De Cheque',
                            (SELECT SUM(IF(dcv.valor < 0,dcv.valor * -1,dcv.valor))
                            FROM gf_detalle_comprobante dcv
                            LEFT JOIN gf_cuenta cd ON dcv.cuenta = cd.id_unico
                            WHERE dcv.comprobante = cp.id_unico AND cd.clasecuenta = 11 
                            AND(( cd.naturaleza = 1 AND dcv.valor < 0) 
                                OR(cd.naturaleza = 2 AND dcv.valor > 0))) AS 'Neto Pagado',
                            (IF((c.naturaleza=1 AND dc.valor>0) OR (c.naturaleza=2 AND dc.valor<0),(IF(dc.valor < 0,dc.valor * -1,dc.valor)), 0 )) as 'Débito', 
                        (IF((c.naturaleza=1 AND dc.valor<0) OR (c.naturaleza=2 AND dc.valor>0),(IF(dc.valor < 0,dc.valor * -1,dc.valor)), 0 )) as 'Credito' 
                FROM
                    gf_comprobante_cnt cp
                LEFT JOIN gf_tipo_comprobante tp ON
                    cp.tipocomprobante = tp.id_unico
                LEFT JOIN gf_tercero t ON
                    t.id_unico = cp.tercero
                LEFT JOIN gf_comprobante_pptal cpp ON
                    cpp.numero = cp.numero AND tp.comprobante_pptal = cpp.tipocomprobante
                LEFT JOIN gf_detalle_comprobante dc ON
                    dc.comprobante = cp.id_unico
                LEFT JOIN gf_cuenta c ON
                    dc.cuenta = c.id_unico
                LEFT JOIN gf_cuenta_bancaria cb ON
                    c.id_unico = cb.cuenta
                LEFT JOIN gf_tercero tcb ON
                    cb.banco = tcb.id_unico
                WHERE
                cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF' 
                AND tp.clasecontable = 14 AND c.clasecuenta = 11 
                AND cp.parametrizacionanno = $anno 
                AND (SELECT COUNT(dt.id_unico) FROM gf_detalle_comprobante_pptal dt 
                                WHERE dt.comprobantepptal = cpp.id_unico) =0 
                ORDER BY cp.fecha, cp.numero ASC ";
       }
       #echo $egresos;
    $egresos = $mysqli->query($egresos);
    if(mysqli_num_rows($egresos)>0){
        $x=0;
        while($row = mysqli_fetch_row($egresos)){ 
            if(empty($row[6])){
                $tercero = $row[5];
            } else {
                $tercero = $row[5].' - '.$row[6];
            }
            ##***Imprimir los datos del egreso 
            ?>
            <tr>
                <td  align="left"><?php echo $row[1]?></td>
                <td align="left"><?php echo mb_strtoupper($row[3])?></td>
                <td align="left"><?php echo $row[2]?></td>
                <td align="left"><?php echo ucwords(mb_strtolower($row[4]))?></td>
                <td align="left"><?php echo $tercero?></td>
                <td align="left"><?php echo $row[7]?></td>
                <td><?php echo number_format($row[13],2,'.',',')?></td>
                <td align="left">0.00</td>
                <td><?php echo number_format($row[12],2,'.',',')?></td>
                <td><?php echo number_format($row[13],2,'.',',')?></td>
                <td align="left"><?php echo $row[8]?></td>
                <td align="left"><?php echo $row[9]?></td>
                <td align="left"><?php echo $row[10]?></td>
          
         </tr>   
        <?php  }    
    }
?>
</table>
</body>
</html>

    
    