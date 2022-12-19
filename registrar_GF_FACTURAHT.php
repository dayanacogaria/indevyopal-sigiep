<?php
require_once './Conexion/conexion.php';
require_once './Conexion/ConexionPDO.php';
require_once './head_listar.php';
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
list($id_factura,$numero_factura, $id_tipo_factura, $tipo_factura, $id_tercero, $tercero, 
    $fecha_factura, $fecha_vencimiento, $id_cc, $centro_costo, $descripcion, $id_estado_f, 
    $estado_factura,$id_vendedor, $vendedor, $cufe, $idCnt,$idPptal,$mov, $nr,$tfc, 
    $vtrm, $idfp, $fp,$idmp, $mp, $id_ingreso ) = array('', '', '', 'Tipo Factura','', 'Tercero','', 
    '', '', 'Centro Costo', '', '', 'Estado Factura', '', 'Vendedor', '', '', '', '', 0,0, 0, 0, 0, 0, 0, '');
 
$htmlcn  = '<option value="">Concepto</option>';
if(!empty($_REQUEST['factura'])){
    $df = $con->Listar("SELECT f.id_unico,f.numero_factura, tf.id_unico, CONCAT_WS(' - ',tf.prefijo,tf.nombre), 
	t.id_unico, IF(t.razonsocial IS NULL OR t.razonsocial ='', CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, ' - ',t.numeroidentificacion),CONCAT_WS(' - ',t.razonsocial, t.numeroidentificacion)) , 
        DATE_FORMAT(f.fecha_factura,'%d/%m/%Y'), DATE_FORMAT(f.fecha_vencimiento,'%d/%m/%Y'), cc.id_unico,cc.nombre,f.descripcion, ef.id_unico, ef.nombre, 
        v.id_unico,IF(v.razonsocial IS NULL OR v.razonsocial ='', CONCAT_WS(' ', v.nombreuno, v.nombredos, v.apellidouno, v.apellidodos,' - ',v.numeroidentificacion),CONCAT_WS(' - ',v.razonsocial, v.numeroidentificacion)), 
        f.cufe , tf.tipo_cambio , tr.valor , fp.id_unico, fp.nombre, f.forma_pago,  
        IF(f.forma_pago =1, 'Contado', IF(f.forma_pago =2,'Crédito','')) 
    FROM 
            gp_factura f
    LEFT JOIN 
            gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
    LEFT JOIN 
            gf_centro_costo cc ON f.centrocosto = cc.id_unico 
    LEFT JOIN 
            gf_tercero t ON f.tercero = t.id_unico 
    LEFT JOIN 
            gf_tercero v ON f.vendedor = v.id_unico 
    LEFT JOIN 
            gp_estado_factura ef ON f.estado_factura = ef.id_unico 
    LEFT JOIN 
            gf_tipo_cambio tc ON tf.tipo_cambio = tc.id_unico 
    LEFT JOIN 
            gf_trm tr ON tc.id_unico = tr.tipo_cambio AND f.fecha_factura = tr.fecha 
    LEFT JOIN 
            gf_forma_pago fp ON f.metodo_pago = fp.id_unico 
    WHERE MD5(f.id_unico)='".$_REQUEST['factura']."'");
    if(empty($df[0][0])){
        echo '<script>document.location ="registrar_GF_FACTURAHT.php"</script>';
    } else {
        list($id_factura,$numero_factura, $id_tipo_factura, $tipo_factura, $id_tercero, $tercero, 
        $fecha_factura, $fecha_vencimiento, $id_cc, $centro_costo, $descripcion, $id_estado_f, 
        $estado_factura,$id_vendedor, $vendedor, $cufe,$idfp, $fp,$idmp, $mp )= 
        array($df[0][0], $df[0][1], $df[0][2], $df[0][3],$df[0][4], $df[0][5],
        $df[0][6], $df[0][7], $df[0][8], $df[0][9], $df[0][10], $df[0][11], $df[0][12], 
        $df[0][13], $df[0][14],$df[0][15], $df[0][18], $df[0][19], $df[0][20],$df[0][21] );

        $rc = $con->Listar("SELECT DISTINCT dp.pago FROM gp_detalle_pago dp
                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
                WHERE df.factura=$id_factura");
        $nr = count($rc);   

        $rowc    = $con->Listar("SELECT    DISTINCTROW cnp.id_unico, cnp.nombre, unf.nombre, 
            pln.codigo_barras 
            FROM      gp_concepto_tarifa AS cont
            LEFT JOIN gp_concepto        AS cnp ON cont.concepto           = cnp.id_unico
            LEFT JOIN gf_plan_inventario AS pln ON cnp.plan_inventario     = pln.id_unico
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
            WHERE     cnp.id_unico IS NOT NULL 
            and cnp.compania = $compania ");
    }
}elseif(!empty ($_REQUEST['ingreso'])){
    $rowtrc =$con->Listar("SELECT  ter.id_unico, 
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '', ter.razonsocial,
          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))
        ), ter.numeroidentificacion 
        FROM gh_movimiento mov 
        LEFT JOIN gh_tipo_mov tpm ON mov.tipo = tpm.id_unico
        LEFT JOIN gf_tercero    as ter on mov.tercero = ter.id_unico
        WHERE md5(mov.id_unico)= '".$_REQUEST['ingreso']."' ");
    $id_tercero = $rowtrc[0][0];
    $tercero    = $rowtrc[0][1].' - '.$rowtrc[0][2];
}
#* Tipo 1 Factura - 2 Remisión
$rowtf = ''; 
$titulo1 = 'Factura';    
$rowtf = $con->Listar("SELECT id_unico, 
    CONCAT_WS(' - ',UPPER(prefijo), nombre ) 
    FROM gp_tipo_factura WHERE compania =$compania AND clase_factura = 3 ");
$informes = 'informes/inf_com_fac.php?factura='.md5($id_factura);

$informese = 'informes/inf_com_fac_excel.php?factura='.md5($id_factura);
#Tercero
$hrt   = '';
$htmlt = '';
if($id_tercero!=''){
    $htmlt .= '<option value="'.$id_tercero.'">'.$tercero.'</option>';
    $hrt = ' AND t.id_unico != '.$id_tercero;
}
$rowt = $con->Listar("SELECT t.id_unico, IF(t.razonsocial IS NULL OR t.razonsocial ='', 
    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, ' - ',t.numeroidentificacion,'  ', t.digitoverficacion),
    CONCAT_WS(' - ',t.razonsocial, t.numeroidentificacion, t.digitoverficacion)) 
    FROM gf_tercero t 
    WHERE t.compania = $compania $hrt ORDER BY numeroidentificacion = '9999999999' DESC "); 
#Centro Costo
$hcc    = '';
$htmlcc = '';
if($id_cc!=''){
    $hcc     = ' AND id_unico != '.$id_cc;
    $htmlcc .= '<option value="'.$id_cc.'">'.$centro_costo.'</option>';
}
$rowcc = $con->Listar("SELECT id_unico, CONCAT_WS(' - ',UPPER(sigla), nombre) FROM gf_centro_costo 
    WHERE parametrizacionanno = $anno $hcc ORDER BY nombre ='Varios' DESC"); 
#Vendedor
$hrv   = '';
$htmlv = '';
if($id_vendedor!=''){
    $htmlv .= '<option value="'.$id_vendedor.'">'.$vendedor.'</option>';
    $hrv    = ' AND t.id_unico != '.$id_vendedor;
}
$htmlmp = '';
$hmp    = '';
if(!empty($idfp)){
    $hmp = " WHERE id_unico != $idfp";
    $htmlmp .= '<option value="'.$idfp.'">'.$fp.'</option>';   
} else {
    if(!empty($id_factura)){
        $htmlmp = '<option value=""> - </option>';
    } else {
        $htmlmp = '';
    }
}
$rowmp = $con->Listar("SELECT id_unico,nombre FROM gf_forma_pago $hmp ");
        
$rowv = $con->Listar("SELECT DISTINCT t.id_unico, IF(t.razonsocial IS NULL OR t.razonsocial ='', 
    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, ' - ',t.numeroidentificacion,'  ', t.digitoverficacion),
    CONCAT_WS(' - ',t.razonsocial, t.numeroidentificacion, t.digitoverficacion)) 
    FROM gf_tercero t 
    LEFT JOIN gf_perfil_tercero pt on t.id_unico = pt.tercero 
    WHERE  (pt.perfil = 2 OR t.id_unico = ".$_SESSION['usuario_tercero']." ) AND t.compania = $compania $hrv ORDER BY t.id_unico =".$_SESSION['usuario_tercero']." DESC "); 


#* Cnt 

if(!empty($_GET['cnt'])){ 
    $rowcnt = $con->Listar("SELECT id_unico FROM gf_comprobante_cnt WHERE md5(id_unico)='".$_GET['cnt']."'");
    $idCnt = $rowcnt[0][0]; 
}
#* Pptal
if(!empty($_GET['pptal'])){
    $rowpptal = $con->Listar("SELECT id_unico FROM gf_comprobante_pptal WHERE md5(id_unico)='".$_GET['pptal']."'");
    $idPptal  = $rowpptal[0][0]; 
}
#* Movimiento 
$salida = '';
if(!empty($_GET['mov'])){
    $rowmov = $con->Listar("SELECT id_unico FROM gf_movimiento WHERE md5(id_unico)='".$_GET['mov']."'");
    $mov    = $rowmov[0][0]; 
    $salida = 'registrar_GR_SALIDA_ALMACEN.php?movimiento='.$_GET['mov'];
}
#* Parámetro 
$pc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE compania = $compania 
    AND indicador  = '2020001'");
$tc = 1;
switch($pc[0][0]){
    case 'Valor Sin Iva':
        $tc = 1;
    break;
    case 'Iva Incluido':
        $tc = 2;
    break;
}
#* TRM 
if(!empty($df[0][16])){
    $tfc    = 1;
    $vtrm   = $df[0][17];
}
#Hotel
$rowo = $con->Listar("SELECT * FROM gp_factura WHERE id_unico = 0");
if(empty($_REQUEST['factura']) && empty($_REQUEST['ingreso'])){
    $rowo = $con->Listar("SELECT    md5(mov.id_unico), tpm.nombre,
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '', ter.razonsocial,
          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))
        ), DATE_FORMAT(mov.fecha, '%d/%m/%Y'),
        mov.id_unico, mov.numero
    FROM      gh_movimiento as mov
    LEFT JOIN gh_tipo_mov   as tpm on mov.tipo        = tpm.id_unico
    LEFT JOIN gh_clase_mov  as cls on tpm.clase       = cls.id_unico
    LEFT JOIN gf_tercero    as ter on mov.tercero = ter.id_unico
    LEFT JOIN gp_factura    as fac on mov.id_unico = fac.mov_hotel
    WHERE     cls.id_unico = 2 AND mov.parametrizacionanno = $anno
    AND       fac.mov_hotel IS NULL
    ORDER BY  mov.fecha DESC");
} elseif(empty($_REQUEST['factura']) && !empty($_REQUEST['ingreso'])){
    $rowo =$con->Listar("SELECT  md5(mov.id_unico) as id,tpm.nombre,
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '', ter.razonsocial,
          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))
        ), DATE_FORMAT(mov.fecha, '%d/%m/%Y'),
        mov.id_unico, mov.numero 
        FROM gh_movimiento mov 
        LEFT JOIN gh_tipo_mov tpm ON mov.tipo = tpm.id_unico
        LEFT JOIN gf_tercero    as ter on mov.tercero = ter.id_unico
        WHERE md5(mov.id_unico)= '".$_REQUEST['ingreso']."' ");
    $id_ingreso = $rowo[0][4];
} elseif(!empty($_REQUEST['factura'])) {
    $rowo =$con->Listar("SELECT  md5(mov.id_unico) as id,tpm.nombre,
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '', ter.razonsocial,
          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))
        ), DATE_FORMAT(mov.fecha, '%d/%m/%Y'),
        mov.id_unico, mov.numero 
        FROM gp_factura f 
        LEFT JOIN gh_movimiento mov ON f.mov_hotel = mov.id_unico
        LEFT JOIN gh_tipo_mov tpm ON mov.tipo = tpm.id_unico
        LEFT JOIN gf_tercero    as ter on mov.tercero = ter.id_unico
        WHERE f.id_unico= $id_factura ");
}
#* DETALLES
if(!empty($_REQUEST['ingreso'])){
    $rowd = $con->Listar("SELECT dmv.id_unico,
        dmv.movimiento, cp.id_unico, cp.nombre, 
        uf.nombre,  if((tar.porcentaje_iva + tar.porcentaje_impoconsumo)>0, ROUND(dmv.valor / (1+((tar.porcentaje_iva + tar.porcentaje_impoconsumo)/100))) , dmv.valor) as VB, 
        DATEDIFF(mv.fechaFinal,mv.fechaInicio) cantidad, 
        if(tar.porcentaje_iva >0, ROUND((dmv.valor / (1+((tar.porcentaje_iva + tar.porcentaje_impoconsumo)/100)))*(tar.porcentaje_iva / 100)) , 0) as VI, 
        if(tar.porcentaje_impoconsumo >0, ROUND((dmv.valor / (1+((tar.porcentaje_iva + tar.porcentaje_impoconsumo)/100)))*(tar.porcentaje_impoconsumo / 100)) , 0) as VIM,
        0, (dmv.valor *DATEDIFF(mv.fechaFinal,mv.fechaInicio) ) as VTA, 
        dmv.valor,
        0, 0, 0, 0, '', '', 0
    FROM gh_detalle_mov dmv
    LEFT JOIN gp_tarifa tar ON dmv.tarifa = tar.id_unico
    LEFT JOIN gp_concepto_tarifa cpt ON tar.id_unico = cpt.tarifa
    LEFT JOIN gp_concepto cp ON cpt.concepto = cp.id_unico
    LEFT JOIN gf_elemento_unidad eu ON cpt.elemento_unidad = eu.id_unico 
    LEFT JOIN gf_unidad_factor uf ON eu.unidad_empaque = uf.id_unico 
    LEFT JOIN gh_movimiento mv ON dmv.movimiento = mv.id_unico 
    WHERE  dmv.movimiento = $id_ingreso");
} else { 
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
    WHERE     dtf.factura = $id_factura");
}


?>
<html>
    <head>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/desing.css">
        
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <title><?=$titulo1?></title>
        <style>
            body{
                font-family: Arial;
                font-size: 10px;
            }

            .valorLabel{
                font-size: 10px;
                white-space:nowrap;
            }
            .valorLabel:hover{
                cursor: pointer;
                color: #1155cc;
            }
            .campos{
                padding: 0px;
                font-size: 10px;
            }
            .client-form input[type="text"]{
                width: 100%;
            }
            .client-form textarea{
                width: 100%;
                height: 34px;
            }
            .privada, .herencia{
                display: none;
            }
            #form>.form-group{
                margin-bottom: 0 !important;
            }
            .control-label{
                font-size: 11px;
            }
        </style>
    </head>    
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left">
                    <h2 align="center" style="
                    margin-top: -2px;" class="tituloform"><?=$titulo1;?></h2>
                    <div style="margin-top: -7px; border:4px solid #020324; border-radius: 10px;" class="client-form col-sm-12 col-lg-12 col-md-12">
                        <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javaScript:guardarF()">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?= $id_factura; ?>" />
                            <input type="hidden" name="idcnt" id="idcnt" value="<?= $idCnt; ?>" />
                            <input type="hidden" name="idpptal" id="idpptal" value="<?= $idPptal; ?>" />
                            <input type="hidden" name="idmov" id="idmov" value="<?= $mov; ?>" />
                            <input type="hidden" name="tipo" id="tipo" value="<?= $_REQUEST['t']; ?>" />
                            <input type="hidden" name="nr" id="nr" value="<?= $nr; ?>" />
                            <input type="hidden" name="cufe" id="cufe" value="<?= $cufe; ?>" />
                            <input type="hidden" name="trm" id="trm" value="<?= $tfc ; ?>" />
                            <input type="hidden" name="detalles" id="detalles" value="<?= count($rowd); ?>" />
                            <input type="hidden" name="htl" id="htl" value="1" />
                            <input type="hidden" name="id_ingreso" id="id_ingreso" value="<?=$id_ingreso?>" />
                            <div class="form-group">
                                <label for="sltIngreso" class="control-label col-sm-1 col-md-1 col-lg-1 text-right">Ingreso:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select2" name="sltIngreso" id="sltIngreso" id="single" title="Seleccione ingreso a facturar" >
                                        <?php 
                                        $htmlI ='';
                                        if(empty($_REQUEST['factura']) && empty($_REQUEST['ingreso'])){
                                            $htmlI = '<option value="">Ingreso</option>';
                                        }
                                        if(!empty($rowo[0][0])){
                                            for ($o = 0; $o < count($rowo); $o++) {
                                                $xstr = "SELECT id_unico FROM gp_factura WHERE mov_hotel = $rowo[4]";
                                                $resx = $mysqli->query($xstr);
                                                $fat  = mysqli_fetch_row($resx);
                                                if(empty($fat[0])){
                                                    $htmlI .= '<option value="'.$rowo[$o][0].'">'.ucwords(mb_strtolower($rowo[$o][5].' '.$rowo[$o][1].' '.$rowo[$o][2].' '.$rowo[$o][3])).'</option>';
                                                }
                                            }
                                        } else {
                                            $htmlI .= '<option value=""> - </option>';
                                        }
                                        echo $htmlI ;
                                        ?>
                                    </select>
                                </div>
                                
                                
                                <label for="sltTipoFactura" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Tipo Factura:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltTipoFactura" id="sltTipoFactura" class="form-control"  title="Seleccione el tipo de factura" required="required">
                                        <?php $html = '';
                                        $html .='<option value="'.$id_tipo_factura.'">'.$tipo_factura.'</option>';
                                        if($id_tipo_factura==''){
                                            for ($tf = 0; $tf < count($rowtf); $tf++) {
                                                $html .='<option value="'.$rowtf[$tf][0].'">'.$rowtf[$tf][1].'</option>';
                                            }
                                        }
                                        echo $html;?>
                                    </select>
                                </div>
                                <label for="txtNumeroF" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Nro:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtNumeroF" id="txtNumeroF" class="form-control" style="cursor:pointer; padding:2px;" title="Número de factura" placeholder="Nro de Factura" value="<?= $numero_factura; ?>" required="" readonly/>
                                </div>
                                <label for="fecha" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Fecha:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control" value="<?= $fecha_factura ?>" type="text" name="fechaF" id="fechaF" onchange="validarFecha(<?=$id_factura?>);change_date()" title="Ingrese la fecha" placeholder="Fecha" readonly required>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Fecha Vto:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control" value="<?= $fecha_vencimiento ?>" type="text" name="fechaV" id="fechaV" onchange="diferents_date()" title="Ingrese la fecha" placeholder="Fecha Vencimiento" readonly required>
                                </div>
                                <label class="control-label col-sm-1 col-sm-1 col-lg-1 text-right"><strong class="obligado">*</strong>Tercero:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select2" name="sltTercero" id="sltTercero" id="single" title="Seleccione un tercero para consultar" required>
                                        <?php                                         
                                        for ($t = 0; $t < count($rowt); $t++) {
                                            $htmlt .='<option value="'.$rowt[$t][0].'">'.$rowt[$t][1].'</option>';
                                        }
                                        echo $htmlt;
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1 text-right" for="sltVendedor">Vendedor:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select2" name="sltVendedor" id="sltVendedor" title="Seleccione un tercero para consultar" required>
                                        <?php                                         
                                        for ($v = 0; $v < count($rowv); $v++) {
                                            $htmlv .='<option value="'.$rowv[$v][0].'">'.$rowv[$v][1].'</option>';
                                        }
                                        echo $htmlv;
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Centro Costo:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltCentroCosto" id="sltCentroCosto" class="form-control select2" title="Seleccione centro de costo" style="padding:-2px;" required>
                                        <?php                                         
                                        for ($c = 0; $c < count($rowcc); $c++) {
                                            $htmlcc .='<option value="'.$rowcc[$c][0].'">'.$rowcc[$c][1].'</option>';
                                        }
                                        echo $htmlcc;
                                        ?>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-1 col-md-1 col-lg-1 text-right" for="txtDescripcion">Descripción:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <textarea class="form-control" style="margin-top:0px;" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ><?=$descripcion ?></textarea>
                                </div>
                                <label class="col-sm-1 col-md-1 col-lg-1 text-rigth control-label">% Descuento:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtDescuento" id="txtDescuento" style="width: 100%;" class="form-control" placeholder="% Descuento" disabled value="<?= empty($descuento)?0:$descuento; ?>">
                                </div>
                                <label class="cambio control-label col-sm-1 col-md-1 col-lg-1 text-right" for="sltMetodo"><strong class="obligado">*</strong>Método Pago:</label>
                                <div class="col-sm-2 col-md-2 c col-l-2">
                                    <select name="sltMetodo" id="sltMetodo" title="Método Pago" class="select2_single form-control" required="required">
                                        <?php 
                                        for ($mp = 0; $mp< count($rowmp); $mp++) {
                                            $htmlmp .='<option value="'.$rowmp[$mp][0].'">'.$rowmp[$mp][1].'</option>';
                                        } echo $htmlmp;?>
                                    </select>
                                </div>
                                <label class="col-sm-1 col-md-1 col-lg-1 text-rigth control-label"><strong class="obligado">*</strong>Forma Pago:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <?php 
                                    if(!empty($idmp)){
                                        if($idmp==1){
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" checked="checked" style="margin-left:10px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        } elseif($idmp==2) {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:10px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px" checked="checked">Crédito';
                                        } else {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:10px" required="required">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px" required="required">Crédito';
                                        }
                                    } else {
                                        if(!empty($id_factura)){
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:10px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        } else {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" checked="checked" style="margin-left:10px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        }
                                    }
                                    ?>
                                    
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="cambio control-label col-sm-1 col-md-1 col-lg-1 text-right" for="sltBuscar">Buscar Factura:</label>
                                <div class="col-sm-2 col-md-2 c col-l-2">
                                    <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Factura" class="select2_single form-control">
                                        <option value="">Tipo Factura</option>
                                        <?php $htmlb = '';
                                        for ($tf = 0; $tf < count($rowtf); $tf++) {
                                            $htmlb .='<option value="'.$rowtf[$tf][0].'">'.$rowtf[$tf][1].'</option>';
                                        } echo $htmlb;?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltBuscar" id="sltBuscar" title="Buscar Factura" class="select2_single form-control">
                                        <option value="">Buscar Factura</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6 text-right">
                                    <a id="btnNuevo" onclick="javascript:nuevo()" class="btn btn-primary borde-sombra btn-group" title="Ingresar nueva factura"><li class="glyphicon glyphicon-plus"></li></a>
                                    <button type="submit" id="btnGuardar" class="btn btn-primary borde-sombra btn-group" title="Guardar factura"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                    <a href="<?=$informes;?>" class="btn btn-primary borde-sombra btn-group" id="btnImprimir" title="Imprimir" target="_blank"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                    <a href="<?=$informese;?>" class="btn btn-primary borde-sombra btn-group" id="btnImprimir" title="Imprimir" target="_blank"><li class="fa fa-file-excel-o"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnModificar" onclick="modificar()" title="Editar"><li class="glyphicon glyphicon glyphicon-edit"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnEliminar" onclick="eliminarDatos()" title="Eliminar"><li class="glyphicon glyphicon-remove"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnRebuilt" onclick="reconstruirComprobantes()" title="Reconstruir comprobantes cnt y pptal"><i class="glyphicon glyphicon-retweet"></i></a>
                                </div>
                            </div> 
                        </form>
                    </div>
                </div>
                <div class="col-sm-8 col-md-8 col-lg-8 col-sm-2 col-md-2 col-lg-2">
                    <div class="col-sm-12 col-md-12">
                        <h2 class="titulo" align="center" style=" font-size:17px; margin-top: -3px;">Información<br/>adicional</h2>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12" id="btnCnt" style="margin-top: -13px;">
                        <?php if(!empty($_GET['cnt'])){ ?>
                            <a class="btn btn-primary btnInfo" href="#" onclick="return cargarComprobante(<?=$idCnt?>)">COMPROBANTE<br/>CONTABLE</a>
                        <?php } ?>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12" id="btnPto" style="margin-top: -4px;">
                        <?php if(!empty($_GET['pptal'])){?>
                            <a class="btn btn-primary btnInfo" href="#" onclick="return cargarPresupuestal(<?=$idPptal?>)">COMPROBANTE<br/>PRESUPUESTAL</a>
                        <?php } ?>
                    </div>
                    <div id="recaudo" style="display:none;margin-top: -4px;" class="col-sm-12 col-md-12 col-lg-12" >
                        <a class="btn btn-primary btnInfo" onclick="modalRecaudo()">REGISTRAR<br/>RECAUDO</a>
                        <input type="hidden" id="tiporecaudo" name="tiporecaudo">
                    </div>
                    <?php if(!empty($rc)){
                        if(count($rc)>0){ ?>
                        <div class="col-sm-12 col-md-12 col-lg-12"  style="margin-top: -4px;">
                            <input type="hidden" name="numR" id="numR" value="<?=count($rc);?>"/>
                            <a class="btn btn-primary btnInfo" onclick="abrirRecaudos(<?=$rc[0][0]?>)">VER<br/>RECAUDO</a>
                        </div>
                      <?php } 
                        } else {
                        if(!empty($_GET['cnt'])){ 
                            $tp = $con->Listar("SELECT id_unico,cuenta_bancaria  FROM gp_tipo_pago WHERE retencion = 1 AND compania = $compania AND cuenta_bancaria IS NOT NULL");
                            if(count($tp)>0){ ?>
                            <div class="col-sm-12 col-md-12 col-lg-12"  style="margin-top: -4px;">
                                <a class="btn btn-primary btnInfo "  id="btnRet" onclick="retenciones()">REGISTRAR <br/>RETENCIONES</a>
                            </div>
                        <?php } } }
                        if(!empty($_GET['cnt'])){
                            $re = $con->Listar("SELECT * FROM gf_retencion WHERE comprobante = $idCnt");
                            if(count($re)>0) { ?>
                            <div class="col-sm-12 col-md-12 col-lg-12"  style="margin-top: -4px;">
                                   <a class="btn btn-primary btnInfo "  id="btnVerRet" onclick="verretenciones()">VER <br/>RETENCIONES</a>
                            </div>
                    <?php } }
                    if(!empty($mov)) { ?>
                    <div class="col-sm-12 col-md-12 col-lg-12" id="btnSalida" style="margin-top: -4px;">
                        <a href="<?= $salida ?>" class="btn btn-primary btnInfo" target="_blank">SALIDA<br/>ALMACÉN</a>
                    </div>
                    <?php } ?>
                </div>
                <div class="col-sm-10 col-md-10 col-lg-10  text-left" style="margin-left:-20px;">
                <div class="client-form" style="margin-left:60px;" class="col-sm-12 col-md-12 col-lg-12">
                    <form name="form-detalle" id="form-detalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardarDetalles()"  style="margin-top:-5px;">
                        <input type="hidden" name="id" id="id" value="<?= $id_factura; ?>" />
                        <input type="hidden" name="idcnt" id="idcnt" value="<?= $idCnt; ?>" />
                        <input type="hidden" name="idpptal" id="idpptal" value="<?= $idPptal; ?>" />
                        <input type="hidden" name="idmov" id="idmov" value="<?= $mov; ?>" />
                        <input type="hidden" name="tipo_c" id="tipo_c" value="<?= $tc ; ?>" />
                        <input type="hidden" name="trm" id="trm" value="<?= $vtrm ; ?>" />
                        <input type="hidden" name="el" id="el" value="" />
                        <input type="hidden" name="id_ingreso" id="id_ingreso" value="<?=$id_ingreso?>" />
                        
                        <div class="col-sm-1" style="margin-right:11px; margin-left:-30px;width:150px;">
                            <div class="form-group"  align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Concepto:</label>
                                <input type="hidden" id="sltConcepto" name="sltConcepto" required title="Ingrese Concepto" onchange="javaScript:cambioConcepto()"/>
                                <input type="text" name="concepto" id="concepto" class="form-control"  title="Ingrese Concepto"  placeholder="Concepto" required style="display: inline; width: 150px" onchange="valorCambio();">
                                <input type="hidden" name="tipoInventario" id="tipoInventario">
                                
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Unidad:</label>
                                <select name="sltUnidad" id="sltUnidad" class="select2 form-control" placeholder="Unidad" style="width: 100%;" required="required">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;width: 40px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Cantidad:</label>
                                <input type="text" name="txtCantidad" class="form-control" placeholder="Cantidad" id="txtCantidad" maxlength="50" style="padding:2px;width:100%;" required="required" autocomplete="off" />
                                <input type="hidden" name="txtCantidadE" id="txtCantidadE">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:-8px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado"></strong>Tipo Dcto:</label>
                                <select class="form-control" name="sltTipoDes" id="sltTipoDes" title="Seleccione Tipo Dcto" style="width:80%; padding:2px;" onchange="javaScript:cambiarTD();">
                                    <option value="">Tipo Descuento</option>
                                    <option value="1">Porcentaje</option>
                                    <option value="2">Cantidad</option>
                                    <option value="3">Valor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1" style="width: 40px; margin-right: 11px;">
                            <div class="form-group" align="left">
                                <label for="txtXDescuento" class="control-label"><strong class="obligado"></strong>Dscto:</label>
                                <input type="text" name="txtXDescuento" id="txtXDescuento" placeholder="Descuento" style="width: 100%; padding:2px" class="form-control" value="" disabled="true">
                            </div>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1" style="width: 50px; margin-right: 11px;">
                            <div class="form-group" align="left">
                                <label for="txtValorDescuento" class="control-label"><strong class="obligado"></strong>Vlr Dscto:</label>
                                <input type="text" name="txtValorDescuento" id="txtValorDescuento" placeholder="Valor Descuento" style="width: 100%; padding:2px" class="form-control" value="" readonly="true">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Valor Unit.:</label>
                                <select class="form-control" name="sltValor" id="sltValor" title="Seleccione valor" style="width:100%; padding:2px;" required>
                                    <option value="">Valor Unitario</option>
                                </select>
                                <input type="hidden" name="txtValorX" id="txtValorX">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px; width: 50px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Valor BU:</label>
                                <input type="text" name="txtValorB" class="form-control" placeholder="Valor Base Unitario" onkeypress="return justNumbers(event);" value="" id="txtValorB" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px; width: 50px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Iva:</label>
                                <input type="text" name="txtIva" class="form-control" placeholder="Iva" onkeypress="return justNumbers(event);" value="" id="txtIva" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                                <input type="hidden" name="porcentajeIva" id="porcentajeIva">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px; width: 50px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Impo:</label>
                                <input type="text" name="txtImpoconsumo" class="form-control" placeholder="Impoconsumo" onkeypress="return justNumbers(event);" value="" id="txtImpoconsumo" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                                <input type="hidden" name="porcentajeImpoconsumo" id="porcentajeImpoconsumo">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px; width: 50px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Ajuste:</label>
                                <input type="text" name="txtAjustePeso" class="form-control" placeholder="Ajuste Peso" onkeypress="return justNumbers(event);" value="" id="txtAjustePeso" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:12px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Valor Total:</label>
                                <input type="text" name="txtValorA" class="form-control" placeholder="Valor Total" onkeypress="return justNumbers(event);" id="txtValorA" maxlength="50" style="padding:2px; width:100%;" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:3px;width: 50px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado"></strong>Descripción:</label>
                                <input type="text" name="descripcion" class="form-control" placeholder="Descripción"  id="descripcion" style="padding:2px; width:100%;" />
                            </div>
                        </div>
                        <div class="col-sm-1" align="left" style="margin-top: 20px; width: 40px;">
                            <button type="submit" id="btnGuardarDetalle" class="btn btn-primary borde-sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: -25px;">
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Concepto</strong></td>
                                <td class="cabeza"><strong>Unidad</strong></td>
                                <td class="cabeza"><strong>Cantidad</strong></td>
                                <td class="cabeza"><strong>Tipo Descuento</strong></td>
                                <td class="cabeza"><strong>Descuento</strong></td>
                                <td class="cabeza"><strong>Valor Descuento</strong></td>
                                <td class="cabeza"><strong>Valor Unitario</strong></td>
                                <td class="cabeza"><strong>Valor Base</strong></td>
                                <td class="cabeza"><strong>Iva</strong></td>
                                <td class="cabeza"><strong>Impoconsumo</strong></td>
                                <td class="cabeza"><strong>Ajuste del peso</strong></td>
                                <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                                <td class="cabeza"><strong>Descripción</strong></td>
                                <?php if ($tfc != 0) {
                                    echo '<td class="cabeza"><strong>Valor Unitario Conversión</strong></td>
                                    <td class="cabeza"><strong>Valor Conversión</strong></td>
                                    <td class="cabeza"><strong>TRM</strong></td>';
                                } ?>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Concepto</th>
                                <th class="cabeza">Unidad</th>
                                <th class="cabeza">Cantidad</th>
                                <th class="cabeza">Tipo Descuento</th>
                                <th class="cabeza">Descuento</th>
                                <th class="cabeza">Valor Descuento</th>
                                <th class="cabeza">Valor Unitario</th>
                                <th class="cabeza">Valor Base</th>
                                <th class="cabeza">Iva</th>
                                <th class="cabeza">Impoconsumo</th>
                                <th class="cabeza">Ajuste del peso</th>
                                <th class="cabeza">Valor Total Ajustado</th>
                                <th class="cabeza">Descripción</th>
                                <?php if ($tfc != 0) {
                                    echo '<th class="cabeza">Valor Unitario Conversión</th>
                                    <th class="cabeza">Valor Conversión</th>
                                    <th class="cabeza">TRM</th>';
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                           <?php 
                            $htmld = '';
                            $sumaCantidad   = 0;
                            $sumaValortotal = 0;
                            for ($i = 0; $i < count($rowd); $i++) {
                                $sumaCantidad   += $rowd[$i][6];
                                $sumaValortotal += $rowd[$i][10];
                                $htmld  .= '<tr>';
                                $htmld  .= '<td class="oculto"></td>';
                                $htmld  .= '<td class="campos" onload="javascript:vd()" >';
                                if(!empty($_REQUEST['ingreso'])){}else {
                                $htmld  .= '<div id="tdEliminar'.$rowd[$i][0].'">';
                                $htmld  .= '<a class="eliminar" onclick="javascript:eliminar('.$rowd[$i][0].')" title="Eliminar"><li class="glyphicon glyphicon-trash"></li></a>';
                                $htmld  .= '</div>';
                                }
                                $htmld  .= '</td>';
                                $htmld  .= '<td class="campos text-left">'.$rowd[$i][3].'</td>';
                                $htmld  .= '<td class="campos text-left">'.$rowd[$i][4].'</td>';
                                
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][6], 3, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-left">'.$rowd[$i][17].'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][12], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][18], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][11], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][5], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][7], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][8], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][9], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][10], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-left">'.$rowd[$i][16].'</td>';
                                if ($tfc != 0) {
                                    $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][13], 2, '.', ',').'</td>';
                                    $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][14], 2, '.', ',').'</td>';
                                    $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][15], 2, '.', ',').'</td>';
                                }
                                
                                $htmld  .= '</tr>';
                                        
                                     
                                     
                           }
                           echo $htmld;
                           ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-1 col-md-offset-2 col-lg-offset-2" style="margin-top : 5px;">
                <div class="col-sm-1 col-md-1 col-lg-1">
                    <div class="form-group" style="" align="left">
                        <label class="control-label">
                            <strong>Totales:</strong>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1 col-md-1 col-lg-1 text-right">
                    <label class="control-label valorLabel" title="Total cantidad"><?= $sumaCantidad; ?></label>
                </div>
                <div class="col-sm-7 col-md-7 col-lg-7 text-right">
                    <label class="control-label valorLabel" title="Total valor ajustado"><?= number_format($sumaValortotal, 2, '.', ','); ?></label>
                    <input type="hidden" name="valot" id="valot" value="<?=$sumaValortotal;?>">
                </div>
                
            </div>
            </div>
        </div>
    </body>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" >
                    <label id="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlRecaudos" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <?php
                    $rc = "SELECT DISTINCT dp.pago, pg.numero_pago FROM gp_detalle_pago dp
                            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
                            LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                            WHERE md5(df.factura)='".$_GET['factura']."'";
                    $rc = $mysqli->query($rc);
                    while ($row1 = mysqli_fetch_row($rc)) {
                        echo '<button onclick="cargarR('.$row1[0].')" class="btn btn-primary btnInfo">'.$row1[1].'</button><br/>';
                    }?>

                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlCantidad" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>La cantidad es mayor a la existente¿Esta seguro que desea realizar la factura?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCanApt" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCant" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlRecaudo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Banco</h4>
                </div>
                <div class="modal-body" >
                    <div class="form-group form-inline" style="margin-left:100px;">
                        <label for="sltBanco" class="control-label col-sm-2">
                            <strong class="obligado">*</strong>Banco:
                        </label>
                        <select name="sltBanco" id="sltBanco" class="select2_single col-sm-2 form-control input-sm" style="width:300px;cursor:pointer;height:30px;" title="Seleccione banco" required>
                            <?php
                            $html =  '<option value="">Banco</option>';
                            $rowb = $con->Listar("SELECT  ctb.id_unico,CONCAT_WS(' ',ctb.numerocuenta,ctb.descripcion)
                                        FROM gf_cuenta_bancaria ctb
                                        LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                        WHERE ctbt.tercero ='". $_SESSION['compania']."' 
                                        and ctb.parametrizacionanno ='". $_SESSION['anno']."' ORDER BY ctb.numerocuenta");
                            for ($b = 0; $b < count($rowb); $b++) {
                                $html .= '<option value="'.$rowb[$b][0].'">'.$rowb[$b][1].'</option>';
                            }
                            echo $html ;
                            ?>
                        </select>
                        <br/>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="registrarRecaudo" class="btn btn-default" data-dismiss="modal" >Registrar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" >
                    <label id="mensajeE"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnEliminarModal" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/scriptFacturacion.js"></script>
        <script src="js/bootstrap.js"></script>
        <?php require_once 'footer.php' ;
        require_once './MODAL_GF_RETENCIONES_FAC.php'; 
        require_once './GF_MODIFICAR_RETENCIONES_MODAL.php'; 
        require_once './modalConsultaComprobanteC.php';
        require_once './modalConsultaComprobanteP.php'; ?>
        <script>
            $('#s2id_autogen3_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};
                console.log('tercero');
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_tercerosJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '<option value=""> - </option>';
                        //console.log(data);
                         option = option+data;
                        $("#sltTercero").html(option);
                            
                    }
                }); 
            });
        </script>
</html>