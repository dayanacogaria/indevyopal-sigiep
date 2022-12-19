<?php
/**
 * Created by Alexander.
 * User: ALEXANDER
 * Date: 14/06/2017
 * Time: 8:48 AM
 *
 * registrar_RF_TRANSLADO_ALMACEN.php
 *
 * Formulario de registro y translado de elementos en el almacen
 *
 * @author Alexander Numpaque
 * @package Movimiento
 */

include ('head_listar.php');
include ('Conexion/conexion.php');
require_once ('modelAlmacen/traslado.php');
$compania = $_SESSION['compania'];              #Variable de sesión compañia
$param = $_SESSION['anno'];                     #Variable parametrización anno
$traslado = new traslado();
#Inicializamos las variables en vacio para capturar los valores en la url
$deptOrg = "";
$deptDes = "";
$resOrg = "";
$resDes = "";
$trass = "";
#Validamos que las variables en la url no estén vacias y cargamos las variables con su respectivo valor
if(!empty($_GET['valueOrig']) && !empty($_GET['valueRO']) && !empty($_GET['valueDestiny']) && !empty($_GET['valueRD'])) {
    $deptOrg = $_GET['valueOrig'];
    $deptDes = $_GET['valueDestiny'];
    $resOrg = $_GET['valueRO'];
    $resDes = $_GET['valueRD'];
}

list($dep, $res, $numero, $fecha, $tipo, $descripcion) = array("", "", "", "", "", "");

if(!empty($_GET['traslado'])){
    $data = $traslado->obtnerTraslado($_GET['traslado']);
    list($fecha, $numero, $dep, $resps, $descripcion) = array($data[1], $data[2], "$data[6] ".ucwords(mb_strtolower($data[7])), $data[8]." ".ucwords(mb_strtolower($data[9])), $data[10]);
    $tipo = "$data[4] ".ucwords(mb_strtolower($data[5]));
    $trass = $_GET['traslado'];

    $respo = "SELECT DISTINCT d.id_unico ,d.sigla,  d.nombre, t.id_unico,  IF(
                              concat_ws(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = ' ',
                              t.razonsocial,
                              concat_ws(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)
                           ) as ternom , dm.movimiento 
        FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_detalle_movimiento dma ON dm.detalleasociado = dma.id_unico 
        LEFT JOIN gf_movimiento ma ON dma.movimiento = ma.id_unico 
        LEFT JOIN gf_dependencia d ON ma.dependencia = d.id_unico 
        LEFT JOIN gf_tercero t ON ma.tercero = t.id_unico 
        WHERE md5(dm.movimiento) = '".$_GET['traslado']."'
        LIMIT 1";
    $codigo = $mysqli->query($respo);
    $rr = mysqli_fetch_row($codigo);
}
?>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript" src="js/md5.js" ></script>
    <title>Traslados</title>
    <script>
        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function(error, element) {
                    $( element )
                        .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });

        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
        });
    </script>
    <style>
        table.dataTable thead th,
        table.dataTable thead td{
            padding: 1px 18px;
        }

        table.dataTable tbody td,
        table.dataTable tbody td{
            padding: 1px 0px 0px 0px;
        }

        .dataTables_wrapper .ui-toolbar{
            padding: 2px 0px;
        }

        .shadow {
            box-shadow: 1px 1px 1px 1px gray;
            color: #fff;
            border-color: #1075C1;
        }

        #contForm {
            margin-bottom: 5px;
        }

        #contTable {
            margin: -5px 0px 0px 0px;
        }

        #form {
            margin-bottom: -10px;
        }

        label #sltDependenciaOrigen-error, #sltDependenciaDestino-error, #sltResponsableOrigen-error, #sltResponsableDestino-error {
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;
            float: right;
            overflow: hidden;
        }

        .contBM {
            float: right;
            overflow:visible;
        }

        .client-form input[type="text"]{
            width: 100% !important;
        }

        #btnT{
            overflow: hidden;
            float: right;
            margin-right: 10px;
            margin-top: 5px;
        }

        #form>.form-group{
            margin-bottom:5px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require ('menu.php');?>
            <div id="contForm" class="col-sm-10 col-lg-10 text-left">
                <h2 style="margin-top:  0px; text-align: center;" class="tituloform">Traslado Devolutivos</h2>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data">
                        <p align="center" class="parrafoO" style="margin-bottom: 5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltDependenciaOrigen" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Dependencia<br/>Origen:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltDependenciaOrigen" id="sltDependenciaOrigen" class="form-control select2" onchange="change_select_dept('sltDependenciaOrigen', 'sltResponsableOrigen', <?php echo $compania ?>)" title="Seleccione dependencia origen" required>
                                    <?php
                                    $html = "";
                                    if(!empty($deptOrg)) {
                                        $depor = $traslado->get_dept($deptOrg);
                                        $html .= "<option value=\"".$depor[0]."\">".ucwords(mb_strtolower($depor[1]))."</option>";
                                        $dataor = $traslado->get_dift_dept($deptOrg, $compania, 1);
                                        foreach($dataor as $rowDO){
                                            $html .= "<option value=\"".$rowDO[0]."\">".ucwords(mb_strtolower($rowDO[1]))."</option>";
                                        }
                                    }else{
                                        if(!empty($_GET['traslado'])){ 
                                            $html .= "<option value=\"".$rr[0]."\">".$rr[1].' '.ucwords(mb_strtolower($rr[2]))."</option>";
                                        }
                                        $html .= "<option value=\"\">Dependencia Origen</option>";
                                        $rowO = $traslado->get_allD($compania, "ASC");
                                        foreach ($rowO as $rowO) {
                                            $html .= "<option value=\"".$rowO[0]."\">".ucwords(mb_strtolower($rowO[1]))."</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltResponsableOrigen" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Responsable<br/>Origen:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltResponsableOrigen" id="sltResponsableOrigen" class="form-control select2" title="Seleccione responsable origen" required>
                                    <?php
                                    $html = "";
                                    if (!empty($resOrg)) {
                                        $resO = $traslado->get_resDep($resOrg, $compania);
                                        foreach ($resO as $row) {
                                            $html .= "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))." (".$row[2].")</option>";
                                        }
                                        $resOO = $traslado->get_resDep_Diff ($resOrg, $deptDes, $compania);
                                        foreach ($resOO as $row) {
                                            $html .= "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))." (".$row[2].")</option>";
                                        }
                                    }else{
                                        if(!empty($_GET['traslado'])){ 
                                            $html .= "<option value=\"".$rr[3]."\">".ucwords(mb_strtolower($rr[4]))."</option>";
                                        }
                                        $html .= "<option value=\"\">Responsable Origen</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltDependenciaDestino" class="col-sm-1 col-sm-1 col-lg-1 control-label"><strong class="obligado">*</strong>Dependencia<br/>Destino:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltDependenciaDestino" id="sltDependenciaDestino" class="form-control select2" onchange="change_select_dept('sltDependenciaDestino', 'sltResponsableDestino', <?php echo $compania ?>)" title="Seleccione dependencia destino" required>
                                    <?php
                                    $html = "";
                                    if(!empty($dep)){
                                        $html .= "<option value=\"\">$dep</option>";
                                    }else{
                                        if(!empty($deptDes)) {
                                            $depor = $traslado->get_dept($deptDes,"ASC");
                                            $html .= "<option value=\"".$depor[0]."\">".ucwords(mb_strtolower($depor[1]))."</option>";
                                            $dataor = $traslado->get_dift_dept($deptOrg, $compania, 1);
                                            foreach($dataor as $rowDO){
                                                $html .= "<option value=\"".$rowDO[0]."\">".ucwords(mb_strtolower($rowDO[1]))."</option>";
                                            }
                                        } else {
                                            $html .= "<option value=\"\">Dependencia Destino</option>";
                                            $rowO = $traslado->get_allD($compania, "DESC");
                                            foreach ($rowO as $rowO) {
                                                $html .= "<option value=\"".$rowO[0]."\">".ucwords(mb_strtolower($rowO[1]))."</option>";
                                            }
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltResponsableDestino" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Responsable<br/>Destino:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltResponsableDestino" id="sltResponsableDestino" class="form-control select2" title="Seleccione responsable destino" required>
                                    <?php
                                    $html = "";
                                    if(!empty($resps)){
                                        $html .= "<option value=\"\">$resps</option>";
                                    }else{
                                        if(!empty($resDes)) {
                                            $resD = $traslado->get_resDep($resDes, $compania);
                                            foreach ($resD as $row) {
                                                $html .= "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))." (".$row[2].")</option>";
                                            }
                                            $resDD = $traslado->get_resDep_Diff ($resDes, $deptDes, $compania);
                                            foreach ($resDD as $row) {
                                                $html .= "<option value=\"".$row[0]."\">".ucwords(mb_strtolower($row[1]))." (".$row[2].")</option>";
                                            }
                                        } else {
                                            $html .= "<option value=\"\">Responsable Destino</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltTipoT" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Tipo Traslado:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoT" id="sltTipoT" class="form-control select2 text-left" title="Seleccione movimiento de traslado" required>
                                    <?php
                                    $html = "";
                                    if(!empty($tipo)){
                                        $html .= "<option value=\"\">$tipo</option>";
                                    }else{
                                        $html .= "<option value=\"\">Tipo Traslado</option>";
                                        $rowTT = $traslado->tipoTraslado($_SESSION['compania']);
                                        foreach($rowTT as $rowT){
                                            $html .= "<option value=\"".$rowT[0]."\">".$rowT[1]." ".ucwords(mb_strtolower($rowT[2]))."</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="txtNumero" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Número:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" id="txtNumero" name="txtNumero" class="form-control" title="Número de Reintegro" placeholder="Número" required="" value="<?php echo $numero ?>" readonly="">
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="txtFecha" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" id="txtFecha" name="txtFecha" class="form-control" title="Fecha de Reintegro" placeholder="Fecha" value="<?php echo $fecha ?>" required="" readonly=""> 
                            </div>
                            <label for="txtDescripcion" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado"></strong>Descripción:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <textarea id="txtDescripcion" name="txtDescripcion" class="form-control" type="text" placeholder="Descripción" title="Descripción" style="margin-top: 0px;width: 100%;height: 34px"><?php echo $descripcion ?></textarea> 
                            </div>
                            <label for="sltBuscar" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Buscar:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltBuscar" id="sltBuscar" class="form-control select2">
                                    <option value="">Buscar</option>
                                </select>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 text-left">
                                <button type="button" class="btn btn-primary glyphicon glyphicon-plus shadow nuevo" id="btn-nuevo" onclick="nuevo()"></button>
                                <button type="button" class="btn btn-primary glyphicon glyphicon-print shadow imprimir" id="btn-imprimir" onclick="open_report(<?php echo $rr[5]; ?>)" ></button>
                            </div>
                            <script type="text/javascript">
                                function open_report(mov) {
                                    if(!isNaN(mov)) {
                                        $("#exportarEntrada").modal("show");   
                                    }
                                }
                                function exportarEntrada(){
                                    if($("#exportarE").val()==1){ 
                                        window.open('informes_almacen/inf_traslado_almacen.php?t=1&mov=<?php echo md5($rr[5])?>');
                                    } else {
                                        window.open('informes_almacen/inf_traslado_almacen.php?t=2&mov=<?php echo md5($rr[5])?>');
                                    }

                                }
                            </script>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-10 col-sm-10">
                <div class="form-group contBM">
                    <a id="btnST" href="javascript:void(0)" onclick="checked_all()" class="btn btn-primary shadow glyphicon glyphicon-ok" title="Marcar todos"></a>
                    <a id="btnSN" href="javascript:void(0)" onclick="not_checked_all()" class="btn btn-primary shadow glyphicon glyphicon-remove" title="Desmarcar todos"></a>
                </div>
            </div>
            <div class="col-sm-10 col-lg-10" id="contTable">
                <div class="table-responsive">
                    <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td class="cabeza"><strong>Elemento</strong></td>
                                <td class="cabeza"><strong>Serie</strong></td>
                                <td class="cabeza"><strong>Descripción</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza" width="3%"><strong></strong></td>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th width="3%"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $html = "";
                        if(!empty($_GET['traslado'])){
                            $datD = $traslado->obtnerDetallesTraslado($_GET['traslado']);
                            foreach($datD as $row){
                                $html .= "\n\t<tr>";
                                $html .= "\n\t\t<td style='text-align: left'>$row[2]</td>";
                                $html .= "\n\t\t<td class=\"text-center\">$row[4]</td>";
                                $html .= "\n\t\t<td class=\"text-left\">$row[3]</td>";
                                $html .= "\n\t\t<td class=\"text-right\">".number_format($row[1], 2, ',', '.')."</td>";
                                $html .= "\n\t\t<td class=\"text-center\"><a href=\"javascript:void(0)\" onclick=\"eliminarD($row[0],$row[5])\"><span class=\"glyphicon glyphicon-trash\"></span></a></td>";
                                $html .= "\n\t</tr>";
                            }
                        }else{
                            if (!empty($deptOrg) && !empty($resOrg)) {
                                $resP = $traslado->obtnerProductos();#Obtenemos los productos en gf_movimiento producto
                                foreach ($resP as $rowP) {
                                    $resUL = $traslado->obnterMaxPro($rowP[0]); #Obtenemos el ultimo detalle relacionado al producto
                                    foreach ($resUL as $rowUL) {
                                        $resDD = $traslado->obtnerMovProF($rowUL[0], $rowP[0], $deptOrg, $resOrg);
                                        foreach ($resDD as $rowDD) {
                                            $html .= "\n\t<tr>";
                                            $html .= "\n\t\t<td style='text-align: left'>$rowDD[8] $rowDD[9]</td>";
                                            $html .= "\n\t\t<td class=\"text-center\">$rowDD[1]</td>";
                                            $html .= "\n\t\t<td class=\"text-left\">$rowDD[3]</td>";
                                            $html .= "\n\t\t<td class=\"text-right\">".number_format($rowDD[2], 2, ',', '.')."</td>";
                                            $html .= "\n\t\t<td class=\"text-center\"><input type=\"checkbox\" name=\"chkT[]\" id=\"chkT".$rowDD[0]."\" title='Seleccione si desea transladar el elemento' value='$rowDD[0]-$rowDD[6]'/></td>";
                                            $html .= "\n\t</tr>";
                                        }
                                    }
                                }
                            }
                        }
                        echo $html;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <a id="btnT" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk" title="Transladar" onclick="get_id_detail(<?php echo $compania.",".$param ?>)"></a>
        </div>
    </div>
    <div class="modal fade" id="exportarEntrada" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content" style="width: 500px;">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informe</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <div class="form-group"  align="center">
                        <select style="font-size:15px;height: 40px;" name="exportarE" id="exportarE" class="form-control" title="Exportar A" required>
                            <option >Exportar A:</option>
                            <option value="1">PDF</option>
                            <option value="2">Excel</option>
                        </select>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" class="btn" onclick="exportarEntrada()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalGuardado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnG" onclick="exit_process()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalNoGuardo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p id="mensaje"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnGM" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalConfirmacion" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btn-del" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" onclick="window.location.reload()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalNoeliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require ('footer.php');?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();
        /**
         * change_select_dept
         *
         * Función para enviar ajax obteniendo el nombre del campo de origen y el campo de destino
         *
         * @param norigen Nombre de campo de origen
         * @param ndestino Nombre de campo de destino
         */
        function change_select_dept (norigen, ndestino, compania) {
            var origen = $("#"+norigen).val();
            var form_data = {
                mov:12,
                id_Dept: origen,
                compania: compania
            };

            $.ajax({
                type:'POST',
                url:'consultasBasicas/consulta_mov.php',
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#"+ndestino).append(data).fadeIn();
                    $("#"+ndestino).css('display','none');
                }
            }).error(function (jqXHR, textStatus, errorThrown) {
                alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
            });
        }

        /**
         * reload_page
         *
         * Función para recargar la pagina cuando se oprime el botón recargar, usando los nombres de los campos
         *
         * @param campoO String Nombre del dependencia de origen
         * @param campoD String Nombre del dependencia destino
         * @param respO String Nombre del responsable origen
         * @param respD String Nombre del responsable destino
         */
        function reload_page (campoO, respO, campoD, respD) {
            var origen = $("#"+campoO).val();
            var respOrigen = $("#"+respO).val();
            var destino = $("#"+campoD).val();
            var respDestino = $("#"+respD).val();

            if(origen.length > 0 && respOrigen.length > 0 && destino.length > 0 && respDestino.length > 0) {
                window.location = 'registrar_RF_TRANSLADO_ALMACEN.php?valueOrig='+md5(origen)+'&valueRO='+md5(respOrigen)+'&valueDestiny='+md5(destino)+'&valueRD='+md5(respDestino);
            }
        }

        /**
         * checked_all
         *
         * Función para marcar todos los checkbox
         */
        function checked_all() {
            $('input[type=checkbox]').prop('checked', true);
        }

        /**
         * not_checked_all
         *
         * Función para desmarcar todos los checkbox
         */
        function not_checked_all() {
            $('input[type=checkbox]').prop('checked', false);
        }

        /**
         * get_id_detail
         *
         * Función que valida si hay un tipo de translado seleccionado, obtiene el valor de los checkbox seleccionados, y envia un
         * array con un string con los checkbox seleccionados, el needle o aguja para detectar el proceso a realizar en el archivo
         * y el tipo de translado, es decir el tipo de movimiento para crear el movimiento
         */
        function get_id_detail(compania, paramA) {
            var numero = $("#txtNumero").val();
            var fecha  = $("#txtFecha").val();
            var descripcion = $("#txtDescripcion").val();
            if($("#sltTipoT").val().length > 0){                                            //Validamos que el objeto tipo translado tenga un valor seleccionado
                var dependenciaD = $("#sltDependenciaDestino").val();
                var responsableDestino = $("#sltResponsableDestino").val();
                var selected = '';                                                          //Inicializamos la variable selected
                //Capturamos los valores de los campos tipo checkbox donde este este marcado

                if(numero.length > 0 && fecha.length > 0 && dependenciaD.length > 0 && responsableDestino.length > 0){
                    $('input[type=checkbox]').each(function(){
                        if (this.checked) {
                            selected += $(this).val()+',';
                        }
                    });
                    if(selected.length > 0) {
                        var select = selected.substr(0, (selected.length) - 1);                     //Al String le quitamos la ultima ,
                        //Array con los valores a enviar, el cual se convertira en el data del ajax
                        var form_data = {
                            markeds:select,
                            mov:13,
                            tipoT:$("#sltTipoT").val(),
                            compania:compania,
                            paramA:paramA,
                            dependencia:dependenciaD,
                            responsable:responsableDestino,
                            numero:numero,
                            fecha:fecha, 
                            descripcion : descripcion
                        };
                        var result = "";
                        //Envio ajax
                        $.ajax({
                            type: "POST",
                            url:'access.php?controller=Traslado&action=registrar',
                            data: form_data,
                            success: function (data, textStatus, jqXHR) {
                                console.log(data);
                                result = JSON.parse(data);
                                if(result == true) {
                                    $("#modalGuardado").modal('show');
                                }else{
                                    $("#modalNoGuardado").modal('show');
                                }
                            }
                        }).error(function (jqXHR, textStatus, errorThrown) {
                            alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
                        });
                    } else {
                        $("#modalMensaje").modal('show');
                        $("#mensaje").html("<p>No ha seleccionado ningún producto</p>");
                    }
                    $("#s2id_sltTipoT").addClass('has-error');
                }else{
                    if($("#txtNumero").val().length == 0){
                        $("#txtNumero").parents(".col-lg-2").addClass("has-error");
                    }

                    if($("#txtFecha").val().length == 0){
                        $("#txtFecha").parents(".col-lg-2").addClass("has-error");
                    }

                    if($("#sltResponsableDestino").val().length == 0){
                        $("#s2id_sltResponsableDestino").addClass('has-error');
                    }

                    if($("#sltDependenciaDestino").val().length == 0){
                        $("#s2id_sltDependenciaDestino").addClass('has-error');
                    }
                }
            } else {
                $("#modalMensaje").modal('show');
                $("#mensaje").html("<p>No ha seleccionado tipo de movimiento traslado</p>");
            }
        }

        /**
         * exit_process
         *
         * Función para recargar la pagina con la url limpia
         */
        function exit_process() {
            window.location.reload();
        }

        function nuevo(){
            window.location = "registrar_RF_TRANSLADO_ALMACEN.php";
        }

        $("#sltResponsableDestino").change(function(){
            reload_page('sltDependenciaOrigen', 'sltResponsableOrigen', 'sltDependenciaDestino', 'sltResponsableDestino');
        });

        $("#sltTipoT").change(function(e){
            var tipo = e.target.value;
            var num  = "";
            $("#txtNumero").empty();
            $.getJSON("access.php?controller=Traslado&action=consecutivo&tipo="+tipo, function(data){
                num = JSON.parse(data);
                $("#txtNumero").val(num);
            });
        });

        function cargarBuscar(){
            $.get("access.php?controller=Traslado&action=cargarBusqueda&compania="+<?php echo $compania ?>+'&param='+<?php  echo $param?>, function(data){
                $("#sltBuscar").html(data);
            });
        }

        $("#sltBuscar").change(function(e){
            mov = e.target.value;
            if(mov.length > 0){
                window.location = 'registrar_RF_TRANSLADO_ALMACEN.php?traslado='+md5(mov)
            }
        });

        $(function(){
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if(dia < 10){
                dia = "0" + dia;
            }
            if(mes < 10){
                mes = "0" + mes;
            }
            //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            $("#txtFecha").datepicker({changeMonth: true}).val();

            cargarBuscar();
        });

        function eliminarD(detalle, producto){
            $("#modalConfirmacion").modal("show");
            $("#btn-del").click(function(){
                var result = "";
                $.ajax({
                    url:"access.php?controller=Traslado&action=eliminar&detalle="+detalle+"&producto="+producto,
                    type:"GET",
                    success: function(data){
                        result = JSON.parse(data);
                        if(result == true){
                            $("#modalEliminado").modal("show");
                        }else if(result == false){
                            $("#modalNoEliminado").modal("show");
                        }
                    }
                });
            });
        }

        <?php
        $html = "";
        if(!empty($_GET['valueOrig'])){
            $html .= "$(\"#btn-imprimir\").attr(\"disabled\", true);";
            $html .= "\n\t$(\"#btn-imprimir\").removeAttr('onclick');";
        }else{
            $html .= "$(\"#btnST, #btnSN, #btnT\").attr(\"disabled\", true);";
            $html .= "\n\t$(\"#btnST, #btnSN, #btnT\").removeAttr(\"onclick\");";
        }

        if(!empty($_GET['traslado'])){
            $html .= "\n\t$(\"#btnST, #btnSN\").css(\"display\", \"none\");";
            $html .= "\n\t$(\"#btnT, #sltDependenciaOrigen, #sltResponsableOrigen\").attr(\"disabled\", true);";
            $html .= "\n\t$(\"#btnT\").removeAttr(\"onclick\");";
        }else{
            $html .= "\n\t$(\"#btn-imprimir\").attr(\"disabled\", true);";
        }
        echo $html;
        ?>
    </script>
</body>
</html>