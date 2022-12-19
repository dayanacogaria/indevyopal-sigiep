<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeFacturacion.xls");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
@session_start();
$con = new ConexionPDO();
list($rep, $dir_t, $ciu_t, $tel_t) = array("", "", "", "");
$compania = $_SESSION['compania'];
$meses    = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
$sqlC = "SELECT     ter.razonsocial,
                    ti.nombre,
                    ter.numeroidentificacion,
                    ter.ruta_logo,
                    dr.direccion,
                    tl.valor
        FROM        gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti  ON ti.id_unico  = ter.tipoidentificacion
        LEFT JOIN   gf_direccion           dr  ON dr.tercero   = ter.id_unico
        LEFT JOIN   gf_telefono            tl ON tl.tercero    = ter.id_unico
        WHERE       ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
# Cargue de variables de compañia
$razonsocial    = $rowCompania[0];
$nombreTipoIden = $rowCompania[1];
$numeroIdent    = $rowCompania[2];
$ruta           = $rowCompania[3];
$direccion      = $rowCompania[4];
$telefono       = $rowCompania[5];
# Captura de id de factura
$factura = $_GET['factura'];
# Consulta para obtener los datos de factura
# @sqlF {String}
$sqlF = "SELECT     fat.id_unico,
                    tpf.nombre,
                    fat.numero_factura,
                    CONCAT(ELT(WEEKDAY(fat.fecha_factura) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
                    fat.fecha_factura,
                    date_format(fat.fecha_vencimiento,'%d/%m/%Y'),
                    IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))=''
                    OR  CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                        (ter.razonsocial),
                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                    CONCAT_WS(' ', ter.numeroidentificacion, ter.digitoverficacion),
                    fat.descripcion,
                    tpf.resolucion,
                    gdr.direccion,
                    gtl.valor,
                    gci.nombre,
                    ter.nombre_comercial
        FROM        gp_factura      AS fat
        LEFT JOIN   gp_tipo_factura AS tpf ON tpf.id_unico         = fat.tipofactura
        LEFT JOIN   gf_tercero      AS ter ON ter.id_unico         = fat.tercero
        LEFT JOIN   gf_direccion    AS gdr ON gdr.tercero          = ter.id_unico
        LEFT JOIN   gf_telefono     AS gtl ON gtl.tercero          = ter.id_unico
        LEFT JOIN   gf_ciudad       AS gci ON gdr.ciudad_direccion = gci.id_unico
        WHERE       md5(fat.id_unico) = '$factura'";
$resultF = $mysqli->query($sqlF);
$rowF    = mysqli_fetch_row($resultF);
# Cargue de variables de factura
$fat_id      = $rowF[0];  $tip_fat     = $rowF[1];  $num_fat     = $rowF[2];
$dia_fat     = $rowF[3];  $fecha_fat   = $rowF[4];  $fechaV_fat  = $rowF[5];
$tercero_fat = $rowF[6];  $num_ter_f   = $rowF[7];  $desc_fat    = $rowF[8];
$resolucion  = $rowF[9];  $dir_t       = $rowF[10]; $tel_t       = $rowF[11];
$ciu_t       = $rowF[12]; $nomComerc   = $rowF[13]; $obser       = $rowF[14];
# Consulta de representante legal
$str_r = "SELECT    gtr.representantelegal,
                    (
                      IF(
                        CONCAT_WS(' ',grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos) = '',
                        grp.razonsocial,
                        CONCAT_WS(' ',grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos)
                      )
                    ) AS nom,
                    gtl.valor,
                    gdr.direccion,
                    gci.nombre
          FROM      gf_tercero   AS gtr
          LEFT JOIN gf_tercero   AS grp ON gtr.representantelegal = grp.id_unico
          LEFT JOIN gf_telefono  AS gtl ON gtl.tercero            = grp.id_unico
          LEFT JOIN gf_direccion AS gdr ON gdr.tercero            = grp.id_unico
          LEFT JOIN gf_ciudad    AS gci ON gdr.ciudad_direccion   = gci.id_unico
          WHERE     gtr.id_unico = $tercero_fat";
$res_r = $mysqli->query($str_r);
if($res_r->num_rows > 0){
    $row_r = $res_r->fetch_row();
    $rep   = $row_r[1];
    $tel_t = $row_r[2];
    $dir_t = $row_r[3];
    $ciu_t = $row_r[4];
}

if(empty($rep)){
    $rep = $tercero_fat;
}

$rep = !empty($nomComerc)?$rep.' / '.$nomComerc:$rep;
$str = "SELECT      pln.codi,
                    conp.nombre,
                    dtf.cantidad,
                    dtf.valor,
                    dtf.iva,
                    dtf.impoconsumo,
                    dtf.ajuste_peso,
                    dtf.valor_total_ajustado,
                    dtf.descuento, 
                    ud.nombre 
        FROM        gp_detalle_factura AS dtf
        LEFT JOIN   gp_concepto        AS conp ON conp.id_unico = dtf.concepto_tarifa
        LEFT JOIN   gf_plan_inventario AS pln  ON conp.plan_inventario = pln.id_unico
        LEFT JOIN   gf_unidad_factor   AS ud   ON dtf.unidad_origen = ud.id_unico 
        WHERE       md5(dtf.factura) = '".$_REQUEST['factura']."'";
$res  = $mysqli->query($str);
$data = $res->fetch_all(MYSQLI_NUM);

#BUSCAR RESOLUCIÓN
$resolucion = '';
$rs = "SELECT rf.descripcion, DATE_FORMAT(rf.fecha_inicial, '%d/%m/%Y'), rf.numero_inicial, rf.numero_final, tf.prefijo 
FROM gp_factura f 
LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
LEFT JOIN gp_resolucion_factura rf ON tf.id_unico = rf.tipo_factura 
WHERE md5(f.id_unico)='$factura' 
AND f.fecha_factura BETWEEN rf.fecha_inicial AND IF(rf.fecha_final IS NULL OR rf.fecha_final='0000-00-00', f.fecha_factura, rf.fecha_final)
AND f.numero_factura BETWEEN rf.numero_inicial AND rf.numero_final";
$rs = $mysqli->query($rs);
if(mysqli_num_rows($rs)>0){
    $rs = mysqli_fetch_row($rs);
    $resolucion = utf8_decode($rs[0].' - Fecha: '.$rs[1].' Autoriza Fact Pref '.$rs[4].' '.$rs[2].' AL '.$rs[3]);
}

$rowd = $con->Listar("SELECT dtf.id_unico,
    dtf.factura, dtf.concepto_tarifa, cnp.nombre, 
    uf.nombre, dtf.valor, dtf.cantidad, dtf.iva, dtf.impoconsumo, 
    dtf.ajuste_peso, dtf.valor_total_ajustado, dtf.valor_origen, 
    dtf.descuento, dtf.valoru_conversion, dtf.valor_conversion, 
    dtf.valor_trm, dtf.descripcion, 
    if(dtf.tipo_descuento=1,'Porcentaje', IF(dtf.tipo_descuento=2,'Cantidad',
    IF(dtf.tipo_descuento=3,'Valor',''))), dtf.valor_descuento 
    FROM      gp_detalle_factura dtf
    LEFT JOIN gp_concepto cnp ON cnp.id_unico = dtf.concepto_tarifa
    LEFT JOIN gf_unidad_factor uf ON dtf.unidad_origen = uf.id_unico
    WHERE     dtf.factura = $fat_id");

?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Factura</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="12" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreTipoIden.' : '.$numeroIdent."<br/>".$direccion.' TELÉFONO :'.$telefono ?>
                <br/>&nbsp;
                <br/><?php echo $resolucion;?>
                <br/><?php echo (ucwords(mb_strtoupper($tip_fat))).' NRO: '.$num_fat;?>
                <br/>&nbsp;
                </strong>
        </th>
        <?php 
        $html  = '';

        $html .= '<tr>';
        $html .= '<td colspan="6"><strong>FECHA: </strong>'. date("d/m/Y", strtotime($fecha_fat)).'</td>';
        $html .= '<td colspan="6"><strong>FECHA VENCIMIENTO: </strong>'.$fechaV_fat.'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="6"><strong>SEÑOR(ES): </strong>'. utf8_decode($tercero_fat).'</td>';
        $html .= '<td colspan="6"><strong>RAZÓN SOCIAL:</strong>'.utf8_decode($rep).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="6"><strong>NIT / CC: </strong>'. ($num_ter_f).'</td>';
        $html .= '<td colspan="6"><strong>DIRECCIÓN:</strong>'.utf8_decode($dir_t).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="6"><strong>TELÉFONO: </strong>'. ($tel_t).'</td>';
        $html .= '<td colspan="6"><strong>DIRECCIÓN:</strong>'.utf8_decode($ciu_t).'</td>';
        $html .= '</tr>';
        if (empty($desc_fat) || $desc_fat === 'NULL'){
            $desc_fat = '';
        }
        $html .= '<tr>';
        $html .= '<td colspan="12"><strong>OBSERVACIONES: </strong>'. ($desc_fat).'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td><strong><center>CONCEPTO</center></strong></td>';
        $html .= '<td><strong><center>UNIDAD</center></strong></td>';
        $html .= '<td><strong><center>CANTIDAD</center></strong></td>';
        $html .= '<td><strong><center>TIPO DESCUENTO</center></strong></td>';
        $html .= '<td><strong><center>DESCUENTO</center></strong></td>';
        $html .= '<td><strong><center>VALOR DESCUENTO</center></strong></td>';
        $html .= '<td><strong><center>VALOR UNITARIO</center></strong></td>';
        $html .= '<td><strong><center>VALOR BASE</center></strong></td>';
        $html .= '<td><strong><center>IVA</center></strong></td>';
        $html .= '<td><strong><center>IMPOCONSUMO </center></strong></td>';
        $html .= '<td><strong><center>AJUSTE AL PESO</center></strong></td>';
        $html .= '<td><strong><center>VALOR TOTAL AJUSTADO</center></strong></td>';
        $html .= '</tr>';
        $sumaCantidad   = 0;
        $sumaValortotal = 0;
        for ($i = 0; $i < count($rowd); $i++) {
            $sumaCantidad   += $rowd[$i][6];
            $sumaValortotal += $rowd[$i][10];
            $html  .= '<tr>';
            $html  .= '<td>'.$rowd[$i][3].'</td>';
            $html  .= '<td>'.$rowd[$i][4].'</td>';
            $html  .= '<td>'.number_format($rowd[$i][6], 3, '.', ',').'</td>';
            $html  .= '<td>'.$rowd[$i][17].'</td>';
            $html  .= '<td>'.number_format($rowd[$i][12], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][18], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][11], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][5], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][7], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][8], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][9], 2, '.', ',').'</td>';
            $html  .= '<td>'.number_format($rowd[$i][10], 2, '.', ',').'</td>';
            $html  .= '</tr>';
        }
        $html .= '<td colspan="11"><strong>NETO A PAGAR</strong></td>';
        $html .= '<td ><strong><right>'.number_format($sumaValortotal, 2).'</right></strong></td>';
        $html .= '</tr>';
        
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';
        echo $html;