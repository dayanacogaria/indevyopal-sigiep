<?php
require_once './Conexion/conexion.php';
require_once './Conexion/ConexionPDO.php';
require_once './head_listar.php';
require_once('./jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$num_anno   = anno($_SESSION['anno']);

list($id_documento,$numero, $id_tipo, $tipo, $id_tercero, $tercero, 
    $fecha, $fecha_vencimiento,  $descripcion,$wt,$wtr) = 
array('', '', '', 'Tipo Documento','', 'Tercero',
    date('d/m/Y'), date('d/m/Y'), '','','');

if(!empty($_REQUEST['id'])){
    $rowdc = $con->Listar("SELECT d.id_unico, td.id_unico, td.sigla, td.nombre, 
            d.numero, DATE_FORMAT(d.fecha, '%d/%m/%Y'),DATE_FORMAT(d.fecha_vencimiento, '%d/%m/%Y'), 
            t.id_unico, CONCAT_WS(' ',COALESCE(t.nombreuno,''),COALESCE(t.nombredos,''),COALESCE(t.apellidouno,''),COALESCE(t.apellidodos,''),COALESCE(t.razonsocial,'')), 
            t.numeroidentificacion, d.descripcion,d.metodo_pago,fp.nombre,d.forma_pago
     FROM gf_documento_equivalente d 
     LEFT JOIN gf_tipo_documento_equivalente td ON d.tipo = td.id_unico 
     LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
     LEFT JOIN gf_forma_pago fp ON fp.id_unico=d.metodo_pago
     WHERE md5(d.id_unico)='".$_REQUEST['id']."'");

    $id_documento = $rowdc[0][0];
    $numero       = $rowdc[0][4];
    $id_tipo      = $rowdc[0][1];
    $tipo         = $rowdc[0][2].' - '.$rowdc[0][3];
    $id_tercero   = $rowdc[0][7];
    $tercero      = $rowdc[0][8].' - '.$rowdc[0][9];;
    $fecha        = $rowdc[0][5];
    $fecha_vencimiento=$rowdc[0][6];
    $descripcion  = $rowdc[0][10];
    $idfp         = $rowdc[0][11];
    $fp           = $rowdc[0][12];
    $idmp         = $rowdc[0][13];
    $wtr = " AND t.id_unico != $id_tercero";
    if ($idfp!=null) {
        $hmp = " WHERE id_unico != $idfp";
        $htmlmp .= '<option value="'.$idfp.'">'.ucwords(utf8_encode(strtolower($fp))).'</option>';   
    }else{
        $hmp = "";
        $htmlmp .="";   
    }

}


$rowmp = $con->Listar("SELECT id_unico,nombre FROM gf_forma_pago $hmp ");

$rowtf = $con->Listar("SELECT id_unico, sigla, nombre FROM gf_tipo_documento_equivalente "
        . "WHERE compania = $compania");

$rowU = $con->Listar("SELECT id_unico,nombre FROM gf_unidad_factor");


$rowt = $con->Listar("SELECT t.id_unico, IF(t.razonsocial IS NULL OR t.razonsocial ='', 
    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, ' - ',t.numeroidentificacion,'  ', t.digitoverficacion),
    CONCAT_WS(' - ',t.razonsocial, t.numeroidentificacion, t.digitoverficacion)) 
    FROM gf_tercero t 
    WHERE t.compania = $compania $wtr ORDER BY numeroidentificacion = '9999999999' DESC LIMIT 20"); 


#* DETALLES
$rowd = $con->Listar("SELECT dde.id_unico, dde.descripcion, dde.cantidad, dde.valor_unitario, dde.valor_iva, dde.valor_total, uf.nombre
                      FROM gf_detalle_documento_equivalente  dde
                      LEFT JOIN gf_unidad_factor uf ON uf.id_unico=dde.unidad_origen 
                      WHERE dde.documento_equivalente=$id_documento");
$informes   = 'informes/INF_DOCUMENTO_E.php?t=1&id='.md5($id_documento);
$informese  = 'informes/INF_DOCUMENTO_E.php?t=2&id='.md5($id_documento);
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
        <title>Documento Equivalente</title>
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
                <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                    <h2 align="center" style="
                    margin-top: -2px;" class="tituloform">Documento Equivalente</h2>
                    <div style="margin-top: -7px; border:4px solid #020324; border-radius: 10px;" class="client-form col-sm-12 col-lg-12 col-md-12">
                        <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="javaScript:guardarF()">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?= $id_documento; ?>" />
                            <div class="form-group">
                                <label for="sltTipo" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Tipo Documento:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltTipo" id="sltTipo" class="form-control"  title="Seleccione el tipo Documento" required="required">
                                        <?php $html = '';
                                        $html .='<option value="'.$id_tipo.'">'.$tipo.'</option>';
                                        if($id_tipo==''){
                                            for ($tf = 0; $tf < count($rowtf); $tf++) {
                                                $html .='<option value="'.$rowtf[$tf][0].'">'.$rowtf[$tf][1].'</option>';
                                            }
                                        }
                                        echo $html;?>
                                    </select>
                                </div>
                                <label for="txtNumeroF" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Nro:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtNumeroF" id="txtNumeroF" class="form-control" style="cursor:pointer; padding:2px;" title="Número de factura" placeholder="Nro de Factura" value="<?= $numero; ?>" required="" />
                                </div>
                                <label for="fecha" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Fecha:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control" value="<?= $fecha ?>" type="text" name="fechaF" id="fechaF"  title="Ingrese la fecha" placeholder="Fecha" readonly required>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Fecha Vto:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control" value="<?= $fecha_vencimiento ?>" type="text" name="fechaV" id="fechaV"  title="Ingrese la fecha" placeholder="Fecha Vencimiento" readonly required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-1 col-sm-1 col-lg-1 text-right"><strong class="obligado">*</strong>Tercero:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select2_single" name="sltTercero" id="sltTercero" id="single" title="Seleccione un tercero para consultar" required >
                                        <?php   
                                        if($id_tercero!=''){
                                            $htmlt .= '<option value="'.$id_tercero.'">'.$tercero.'</option>';
                                        }                                        
                                        for ($t = 0; $t < count($rowt); $t++) {
                                            $htmlt .='<option value="'.$rowt[$t][0].'">'.$rowt[$t][1].'</option>';
                                        }
                                        echo $htmlt;
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1 text-right" for="txtDescripcion">Descripción:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <textarea class="form-control" style="margin-top:0px;" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ><?=$descripcion ?></textarea>
                                </div>
                                <label class="cambio control-label col-sm-1 col-md-1 col-lg-1 text-right" for="sltMetodo"><strong class="obligado">*</strong>Método Pago:</label>
                                <div class="col-sm-2 col-md-2 c col-l-2">
                                    <select name="sltMetodo" id="sltMetodo" title="Método Pago" class="select2_single form-control" required="required">
                                        <?php 
                                        for ($mp = 0; $mp< count($rowmp); $mp++) {
                                            $htmlmp .='<option value="'.$rowmp[$mp][0].'">'.ucwords(utf8_encode(strtolower($rowmp[$mp][1]))).'</option>';
                                        } echo $htmlmp;?>
                                    </select>
                                </div>
                                <label class="col-sm-1 col-md-1 col-lg-1 text-rigth control-label"><strong class="obligado">*</strong>Forma Pago:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <?php 
                                    
                                        if($idmp==1){
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" checked="checked" style="margin-left:0px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px">Crédito';
                                        } elseif($idmp==2) {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:0px">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px" checked="checked">Crédito';
                                        } else {
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="1" style="margin-left:0px" required="required" checked="checked">Contado';
                                            echo '<input type="radio" name="txtFormaP" id="txtFormaP" tittle="Forma Pago" value="2" style="margin-left:10px" required="required">Crédito';
                                        }
                                    
                                    ?>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="cambio control-label col-sm-1 col-md-1 col-lg-1 text-right" for="sltBuscar">Buscar Documento Equivalente:</label>
                                <div class="col-sm-2 col-md-2 c col-l-2">
                                    <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Documento" class="select2_single form-control">
                                        <option value="">Tipo Documento</option>
                                        <?php $htmlb = '';
                                        for ($tf = 0; $tf < count($rowtf); $tf++) {
                                            $htmlb .='<option value="'.$rowtf[$tf][0].'">'.$rowtf[$tf][1].'</option>';
                                        } echo $htmlb;?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltBuscar" id="sltBuscar" title="Buscar Factura" class="select2_single form-control">
                                        <option value="">Buscar Documento Equivalente</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6 text-right">
                                    <a id="btnNuevo" onclick="javascript:nuevo()" class="btn btn-primary borde-sombra btn-group" title="Ingresar nueva factura"><li class="glyphicon glyphicon-plus"></li></a>
                                    <button type="submit" id="btnGuardar" class="btn btn-primary borde-sombra btn-group" title="Guardar factura"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                    <a href="<?=$informes;?>" class="btn btn-primary borde-sombra btn-group" id="btnImprimir" title="Imprimir" target="_blank"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                    <a href="<?=$informese;?>" class="btn btn-primary borde-sombra btn-group" id="btnImprimir" title="Imprimir" target="_blank"><li class="fa fa-file-excel-o"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnModificar" onclick="modificar()" title="Editar"><li class="glyphicon glyphicon glyphicon-edit"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnEliminar" onclick="eliminarDatos()" title="Eliminar"><li class="glyphicon glyphicon-remove"></li></a>
                                </div>
                            </div> 
                        </form>
                    </div>
                </div>
                <div class="col-sm-10 col-md-10 col-lg-10  text-left" style="margin-left:-20px;">
                <div class="client-form" style="margin-left:60px;" class="col-sm-12 col-md-12 col-lg-12">
                    <form name="form-detalle" id="form-detalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardarDetalles()"  style="margin-top:-5px;">
                        <input type="hidden" name="id" id="id" value="<?= $id_documento; ?>" />
                        <div class="col-sm-2" style="margin-right:11px; margin-left:-30px;width:200px;">
                            <div class="form-group"  align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Concepto:</label>
                                <input type="text" name="concepto" id="concepto" class="form-control"  title="Ingrese Concepto"  placeholder="Concepto" required style="display: inline; width: 200px" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;">
                            <div class="form-group" align="left">
                            <label class="control-label"><strong class="obligado">*</strong>Unidad:</label>
                                <select name="sltUnidad" id="sltUnidad" class="select2 form-control" placeholder="Unidad" style="width: 100%;" required="required">
                                    <option value=""></option>
                                    <?php
                                      $html="";
                                        for ($u = 0; $u < count($rowU); $u++) {
                                            $html .='<option value="'.$rowU[$u][0].'">'.ucwords(utf8_encode(strtolower($rowU[$u][1]))).'</option>';
                                        }
                                        echo $html;
                                     ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-right:11px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Cantidad:</label>
                                <input type="text" name="txtCantidad" class="form-control" placeholder="Cantidad" id="txtCantidad" maxlength="50" style="padding:2px;width:100%;" required="required" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-right:11px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Valor Unitario:</label>
                                
                                <input type="text" name="txtValorX" id="txtValorX" class="form-control" placeholder="Valor  Unitario" value="" id="txtValorB" maxlength="50" style="padding:2px;width:100%;" required="" onkeypress="return txtValida(event, 'dec','txtValorX','2' )" onkeyup="formatC('txtValorX');" autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="col-sm-2" style="margin-right:11px; ">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado"></strong>Valor Iva Unitario:</label>
                                <input type="text" name="txtIva" class="form-control" placeholder="Iva"  value="" id="txtIva" maxlength="50" style="padding:2px;width:100%;" onkeypress="return txtValida(event, 'dec','txtIva','2' )" onkeyup="formatC('txtIva');" autocomplete="off"/>
                            </div>
                        </div>
                        
                        <div class="col-sm-2" style="margin-right:12px;">
                            <div class="form-group" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Valor Total:</label>
                                <input type="text" name="txtValorA" class="form-control" placeholder="Valor Total"  id="txtValorA" maxlength="50" style="padding:2px; width:100%;" required="" onkeypress="return txtValida(event, 'dec','txtValorA','2' )" onkeyup="formatC('txtValorA');" autocomplete="off"/>
                            </div>
                        </div>
                        
                        <div class="col-sm-2" align="left" style="margin-top: 20px; width: 40px;">
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
                                <td class="cabeza"><strong>Valor Unitario</strong></td>
                                <td class="cabeza"><strong>Valor Iva Unitario</strong></td>
                                <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Concepto</th>
                                <th class="cabeza">Unidad</th>
                                <th class="cabeza">Cantidad</th>
                                <th class="cabeza">Valor Unitario</th>
                                <th class="cabeza">Valor Iva Unitario</th>
                                <th class="cabeza">Valor Total Ajustado</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php 
                            $htmld = '';
                            $sumaCantidad   = 0;
                            $sumaValortotal = 0;
                            for ($i = 0; $i < count($rowd); $i++) {
                                $sumaCantidad   += $rowd[$i][2];
                                $sumaValortotal += $rowd[$i][5];
                                $htmld  .= '<tr>';
                                $htmld  .= '<td class="oculto"></td>';
                                $htmld  .= '<td class="campos" onload="javascript:vd()" >';
                                $htmld  .= '<div id="tdEliminar'.$rowd[$i][0].'">';
                                $htmld  .= '<a class="eliminar" onclick="javascript:eliminar('.$rowd[$i][0].')" title="Eliminar"><li class="glyphicon glyphicon-trash"></li></a>';
                                $htmld  .= '</div>';
                                $htmld  .= '</td>';
                                $htmld  .= '<td class="campos text-left">'.$rowd[$i][1].'</td>';
                                $htmld  .= '<td class="campos text-left">'.ucwords(utf8_encode(strtolower($rowd[$i][6]))).'</td>';
                                $htmld  .= '<td class="campos text-left">'.$rowd[$i][2].'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][3], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][4], 2, '.', ',').'</td>';
                                $htmld  .= '<td class="campos text-right">'.number_format($rowd[$i][5], 2, '.', ',').'</td>';
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
    <script src="js/bootstrap.js"></script>
    <script src="js/txtValida.js"></script>
    <?php require_once 'footer.php' ;?>
    <script>
        
        $(function(){
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#fechaF").datepicker({changeMonth: true}).val();
            $("#fechaV").datepicker({changeMonth: true}).val();
        });
        function justNumbers(e){
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                return true;
            return /\d/.test(String.fromCharCode(keynum));
        }
        //Tipo Factura 
        $("#sltTipo").change(function(){
            let tipo = $("#sltTipo").val();
            if(tipo.length > 0){
                let form_data = {
                    tipo:$("#sltTipo").val(),
                    action:4
                };
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_TipoDocumentoEJson.php",
                    data: form_data,
                    success: function (data) {
                        $("#txtNumeroF").val(data);
                    }
                });
            }else{
                $("#txtNumeroF").val("");
            }
        });
        function nuevo(){
            document.location='GF_DOCUMENTO_EQUIVALENTE.php';
        }
       
        //SELECT2
        $(" #sltTercero,#sltBuscar,#sltTipo,#sltTipoBuscar,#sltMetodo,#sltUnidad").select2({placeholder:"Tercero",allowClear: true});

        //Función Guardar Encabezado Factura 
        function guardarF(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Guardando Información...');
            var form_data = { action:1 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_TipoDocumentoEJson.php?action=5",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response);
                    if(response ==0){
                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        }) 
                    } else {
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location =response;
                        }) 
                    }
                }
            })

        }
         // Buscar Facturas Por Tipo
        $("#sltTipoBuscar").change(function(){
            let form_data ={
                action:6,
                tipo: $("#sltTipoBuscar").val()
            }
            $.ajax({
                type:'POST',
                url:'jsonPptal/gf_TipoDocumentoEJson.php',
                data:form_data,
                success: function(data){
                    console.log('AAA'+data);
                   $("#sltBuscar").html(data);
                }
            });
        })
        //Funcion Buscar
        $("#sltBuscar").change(function(){
            console.log('aca');
            let factura = $("#sltBuscar").val();
            var form_data = { action:7, id:factura };
            $.ajax({
                type:'POST',
                url: "jsonPptal/gf_TipoDocumentoEJson.php?action=7",
                data:form_data,
                success: function(response)
                { 
                    document.location =response;
                }
            })
        })
        
        $("#txtCantidad").change(function(){
            cambiarValor();
        });
        $("#txtValorX").change(function(){
            cambiarValor();
        });
        $("#txtIva").change(function(){
            cambiarValor();
        });
        
        function cambiarValor(){
            let cantidad =  $("#txtCantidad").val();
            let valor    =  $("#txtValorX").val();
            let iva      =  $("#txtIva").val();
            if(cantidad!=='' && valor!==''){
                if(iva==''){
                    iva = 0;
                } else {
                    iva = parseFloat(iva.replace(/\,/g, ''));
                }
                let vt = (parseFloat(valor.replace(/\,/g, ''))+iva)* parseFloat(cantidad);
                let numero  = vt;
                numero      = numero.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
                numero      = numero.split('').reverse().join('').replace(/^[\,]/,'');
                $("#txtValorA").val(numero);
            }
        }
        function guardarDetalles(){
            var formData = new FormData($("#form-detalle")[0]);  
            jsShowWindowLoad('Guardando Información...');
            var form_data = { action:1 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_TipoDocumentoEJson.php?action=8",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response);
                    if(response ==0){
                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        }) 
                    } else {
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        }) 
                    }
                }
            })
        }
        
        //Modificar Encabezado
        function modificar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Modificando Información...');
            var form_data = { action:1 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_TipoDocumentoEJson.php?action=9",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response);
                    if(response ==0){
                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        }) 
                    } else {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                           document.location.reload();
                        }) 
                    }
                }
            })
        }
        
        function eliminarDatos(){
            $("#mensajeE").html("¿Desea Eliminar los Datos Del Documento Equivalente?");
            $("#myModalEliminar").modal("show");
            $("#btnEliminarModal").click(function(){
                var formData = new FormData($("#form")[0]);  
                jsShowWindowLoad('Eliminando Información...');
                var form_data = { action:1 };
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_TipoDocumentoEJson.php?action=10",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        if(response ==0){
                            $("#mensaje").html('No Se Ha Podido Eliminar Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                $("#modalMensajes").modal("hide");
                            }) 
                        } else {
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                               document.location.reload();
                            }) 
                        }
                    }
                })
            })
        }
        function eliminar(id){
            $("#mensajeE").html("¿Desea Eliminar El Registro Seleccionado?");
            $("#myModalEliminar").modal("show");
            $("#btnEliminarModal").click(function(){
                var formData = new FormData($("#form")[0]);  
                jsShowWindowLoad('Eliminando Información...');
                var form_data = { action:1 };
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_TipoDocumentoEJson.php?action=11&iddetalle="+id,
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        if(response ==0){
                            $("#mensaje").html('No Se Ha Podido Eliminar Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                $("#modalMensajes").modal("hide");
                            }) 
                        } else {
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                               document.location.reload();
                            }) 
                        }
                    }
                })
            })
        }
        $('#s2id_autogen2_search').on("keydown", function(e) {
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
                    $("#sltTercero").html(data);

                }
            }); 
        });
        $(document).ready(function() {
            let idf = $("#id").val();
            if(idf!==''){
                $("#btnGuardar").attr('disabled',true).removeAttr('onclick').removeAttr("href");
                
            } else {
                $("#btnGuardarDetalle,#btnImprimir,#btnModificar,#btnEliminar, .eliminar").attr('disabled',true).removeAttr('onclick').removeAttr("href");
            }
        })
    </script>
        
</html>