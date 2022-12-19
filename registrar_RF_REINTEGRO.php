<?php
require ('head.php');
require ('Conexion/conexion.php');
require_once ('./modelAlmacen/reintegro.php');

list($depOr, $depDe, $resOr, $resDe, $compania, $paramA) = array(0, 0, 0, 0, $_SESSION['compania'], $_SESSION['anno']);

if(!empty($_REQUEST['sltDepOr'])){
    $depOr = $_REQUEST['sltDepOr'];
}

if(!empty($_REQUEST['sltDepDe'])){
    $depDe = $_REQUEST['sltDepDe'];
}

if(!empty($_REQUEST['sltResOr'])){
    $resOr = $_REQUEST['sltResOr'];
}

if(!empty($_REQUEST['sltResDe'])){
    $resDe = $_REQUEST['sltResDe'];
}

$rig = new reintegro();

list($tipo, $numero, $fecha, $dependencia, $responsable) = array("", "", "", "", "");
if(!empty($_GET['reintegro'])){
    $data = $rig->obtnerReintegro($_GET['reintegro']);
    list($fecha, $numero, $dependencia, $responsable) = array($data[1], $data[2], "$data[6] ".ucwords(mb_strtolower($data[7])), ucwords(mb_strtolower($data[9]))." ".$data[9]);
    $tipo = "$data[4] ".ucwords(mb_strtolower($data[5]));
}
?>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script type="text/javascript" src="js/md5.js" ></script>
    <title>Reintegro de Almacén</title>
    <style type="text/css" media="screen">
        .btn{
            box-shadow: 1px 1px 1px 1px gray;
            color:#fff; border-color:#1075C1;
        }

        .contBM {
            float: right;
            overflow:visible;
        }

        #contButton {
            margin-top: 5px;
        }

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

        .client-form input[type="text"]{
            width: 100%;
        }

        #form>.form-group{
            margin-bottom:5px !important;
        }

        #btnT{
            overflow: hidden;
            float: right;
            margin-top: 5px;
        }
    </style>
    <script>
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
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require ('menu.php');?>
            <div id="contForm" class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 style="margin-top:  0px; text-align: center;" class="tituloform">Reintegro Almacén</h2>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="GET"  enctype="multipart/form-data" action="registrar_RF_REINTEGRO.php">
                        <p align="center" class="parrafoO" style="margin-bottom: 5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltDepOr" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Dependencia Origen:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltDepOr" id="sltDepOr" class="form-control select2" required="" onchange="obtnerResponsableOrg($(this).val())">
                                    <?php
                                    if(empty($depOr)){
                                        $html = "";
                                        $html .= "<option value=''>Dependencia Origen</option>";
                                        $rowO = $rig->obtnerDepOrg();
                                        foreach ($rowO as $row) {
                                            $html .= "<option value=\"".md5($row[0])."\">$row[1] ".ucwords(mb_strtolower($row[2]))."</option>";
                                        }
                                        echo $html;
                                    }else{
                                        $html = "";
                                        $rowO = $rig->ejecutarConsulta("SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE md5(dep.id_unico) = '$depOr'");
                                        foreach ($rowO as $row) {
                                            $html .= "<option value=\"".md5($row[0])."\">$row[1] ".ucwords(mb_strtolower($row[2]))."</option>";
                                        }
                                        $sql_dep = "SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE md5(dep.id_unico) != '$depOr' AND dep.tipodependencia = 6 AND dep.compania = $compania";
                                        $rowG = $rig->ejecutarConsulta($sql_dep);
                                        foreach ($rowG as $row) {
                                            $html .= "<option value=\"".md5($row[0])."\">$row[1] ".ucwords(mb_strtolower($row[2]))."</option>";
                                        }
                                        $html .= "";
                                        echo $html;
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="sltResOr" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Responsable Origen:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltResOr" id="sltResOr" class="form-control select2" required="">
                                    <?php
                                    $html = "";
                                    if(!empty($resOr)){
                                        $rowT  = $rig->obtnerDatosTercero($resOr);
                                        $html .= "<option value=\"".$rowT[0]."\">$rowT[1] ($rowT[2] - $rowT[3])</option>";
                                        $rowS  = $rig->obtnerDifResponsables($depOr, $resOr);
                                        foreach ($rowS as $row) {
                                            $html .= "<option value=\"".$row[0]."\">$row[1] ($row[2] - $row[3])</option>";
                                        }
                                    }else{
                                        $html .= "<option value=\"\">Responsable Origen</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltDepDe" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Dependencia Destino:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltDepDe" id="sltDepDe" class="form-control select2" required="" onchange="obnterResponsableDes($(this).val())">
                                    <?php
                                    $html = "";
                                    if(!empty($dependencia)){
                                        $html .= "<option value=''>$dependencia</option>";
                                    }else{
                                        if(empty($depDe)){
                                            $html .= "<option value=''>Dependencia Destino</option>";
                                            $rowD = $rig->obnterDepDes();
                                            foreach ($rowD as $row) {
                                                $html .= "<option value=\"".md5($row[0])."\">$row[1] ".ucwords(mb_strtolower($row[2]))."</option>";
                                            }
                                        }else{
                                            $sql  = "SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE md5(dep.id_unico) = '$depDe'";
                                            $rowD = $rig->ejecutarConsulta($sql);
                                            foreach ($rowD as $row) {
                                                $html .= "<option value=\"".md5($row[0])."\">$row[1] ".ucwords(mb_strtolower($row[2]))."</option>";
                                            }
                                            $sql  = "SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE md5(dep.id_unico) != '$depDe' AND dep.tipodependencia = 1 AND dep.compania = $compania";
                                            $rowT = $rig->ejecutarConsulta($sql);
                                            foreach ($rowT as $row) {
                                                $html .= "<option value=\"".$row[0]."\">$row[1] ".ucwords(mb_strtolower($row[2]))."</option>";
                                            }
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltResDe" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Responsable Destino:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltResDe" id="sltResDe" class="form-control select2" required="">
                                    <?php
                                    $html = "";
                                    if(!empty($responsable)){
                                        $html .= "<option value=\"\">$responsable</option>";
                                    }else{
                                        if(!empty($resDe)){
                                            $rowD  = $rig->obtnerDatosTercero($resDe);
                                            $html .= "<option value=\"".$rowD[0]."\">$rowD[1] ($rowD[2] - $rowD[3])</option>";
                                            $rowS  = $rig->obtnerDifResponsables($depDe, $resDe);
                                            foreach ($rowS as $row) {
                                                $html .= "<option value=\"".$row[0]."\">$row[1] ($row[2] - $row[3])</option>";
                                            }
                                        }else{
                                            $html .= "<option value=\"\">Responsable Destino</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltTipoT" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado">*</strong>Tipo Reintegro:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoT" id="sltTipoT" class="form-control select2 text-left" title="Seleccione movimiento de traslado" required>
                                    <?php
                                    $html = "";
                                    if(!empty($tipo)){
                                        $html .=  "<option value=\"\">$tipo</option>";
                                    }else{
                                        $html .=  "<option value=\"\">Tipo Reintegro</option>";
                                        $resT = $rig->obntenerTipoR($compania);
                                        foreach ($resT as $rowT) {
                                            $html .=  "<option value=\"".$rowT[0]."\">".$rowT[1]." ".ucwords(mb_strtolower($rowT[2]))."</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="txtNumero" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Número:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" id="txtNumero" name="txtNumero" class="form-control" title="Número de Reintegro" placeholder="Número" required="" value="<?php echo $numero ?>" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFecha" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" id="txtFecha" name="txtFecha" class="form-control" title="Fecha de Reintegro" placeholder="Fecha" value="<?php echo $fecha ?>" required="" readonly="">
                            </div>
                            <label for="sltBuscar" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Buscar:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltBuscar" id="sltBuscar" class="form-control select2">
                                    <option value="">Buscar</option>
                                </select>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4 text-right">
                                <button type="button" class="btn btn-primary glyphicon glyphicon-plus shadow nuevo" id="btn-nuevo" onclick="nuevo()"></button>
                                <button type="button" class="btn btn-primary glyphicon glyphicon-print shadow imprimir" id="btn-imprimir"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <div class="form-group contBM">
                    <a id="btnST" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-ok" title="Marcar Todos" onclick="checked_all()"></a>
                    <a id="btnSN" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-remove" title="Desmarcar Todos" onclick="not_checked_all()"></a>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" id="contTable">
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
                        if(!empty($_GET['reintegro'])){
                            $datD = $rig->obtnerDetallesReintegro($_GET['reintegro']);
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
                            if (!empty($depOr) && !empty($resOr)) {
                                $resP = $rig->obtnerProductos();
                                foreach ($resP as $rowP) {
                                    $resUL = $rig->obtnerDetallesR($rowP[0]);
                                    foreach ($resUL as $rowUL) {
                                        $resDD = $rig->obtnerProductosDetalleDependenciaTercero($rowUL[0], $rowP[0], $depOr, $resOr);
                                        #var_dump($resDD);
                                        foreach ($resDD as $rowDD) {
                                            $html .= "\n\t<tr>";
                                            $html .= "\n\t\t<td style='text-align: left'>$rowDD[9] $rowDD[8]</td>";
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
            <div id="contButton" class="col-sm-10 col-md-10 col-lg-10">
                <a id="btnT" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk" title="Reintegrar" onclick="get_id_detail($('#sltDepOr').val(), $('#sltDepDe').val(), $('#sltResOr').val(), $('#sltResDe').val(), <?php echo $compania ?>, <?php echo $paramA ?>, $('#sltTipoT').val())"></a>
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
            <?php require ('footer.php'); ?>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script>
        $(".select2").select2();

        $().ready(function(){
            var validator = $("#form").validate({
                ignore: "",
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
        });

        function obtnerResponsableOrg(dept){
            $.ajax({
                url:"access.php?controller=Reintegro&action=obtnerResponsableD",
                type:"POST",
                data:{
                    sltDep:dept
                },
                success: function(data, textStatus, jqXHR){
                    $("#sltResOr").append(data).fadeIn();
                    $("#sltResOr").css('display', 'none');
                }
            });
        }

        function obnterResponsableDes(dept){
            $.ajax({
                url:"access.php?controller=Reintegro&action=obtnerResponsableD",
                type:"POST",
                data:{
                    sltDep:dept
                },
                success: function(data, textStatus, jqXHR){
                    $("#sltResDe").append(data).fadeIn();
                    $("#sltResDe").css('display', 'none');
                }
            });
        }

        function checked_all() {
            $('input[type=checkbox]').prop('checked', true);
        }

        function not_checked_all() {
            $('input[type=checkbox]').prop('checked', false);
        }

        function get_id_detail (depOr, depDes, resOr, resDep, compania, paramA, tipo){
            var numero = $("#txtNumero").val();
            var fecha  = $("#txtFecha").val();
            if(tipo.length > 0){
                if(numero.length > 0 && fecha.length > 0){
                    var selected = '';
                    $('input[type=checkbox]').each(function(){
                        if (this.checked) {
                            selected += $(this).val()+',';
                        }
                    });
                    if(selected.length > 0) {
                        var select    = selected.substr(0, (selected.length) - 1);                     //Al String le quitamos la ultima ,
                        var form_data = {
                            tipo:        tipo,
                            compania:    compania,
                            param:       paramA,
                            dependencia: depDes,
                            responsable: resDep,
                            marcados:    select,
                            numero:      numero,
                            fecha:       fecha
                        };

                        var result = "";

                        $.ajax({
                            type:"POST",
                            url: "access.php?controller=Reintegro&action=registrar",
                            data: form_data,
                            success: function(data, textStatus, jqXHR){
                                result = JSON.parse(data);
                                if(result == true) {
                                    $("#modalGuardado").modal('show');
                                }else{
                                    $("#modalNoGuardado").modal('show');
                                }
                            }
                        });
                }else{
                    if($("#txtNumero").val().length == 0){
                        $("#txtNumero").parents(".col-lg-2").addClass("has-error");
                    }

                    if($("#txtFecha").val().length == 0){
                        $("#txtFecha").parents(".col-lg-2").addClass("has-error");
                    }
                }
                }else{
                    $("#modalMensaje").modal('show');
                    $("#mensaje").html("<p>No a seleccionado ningun elemento del inventario</p>");
                }
            }else{
                $("#modalMensaje").modal('show');
                $("#mensaje").html("<p>No a seleccionado tipo de movimiento de reintegro</p>");
            }
        }

        function exit_process() {
            window.location.reload();
        }

        $("#sltResDe").change(function(e){
            var dep1 = $("#sltDepOr").val();
            var res1 = $("#sltResOr").val();
            var dep2 = $("#sltDepDe").val();
            var res2 = $("#sltResDe").val();

            if(dep1.length > 0 && res1.length > 0 && dep2.length > 0 && res2.length > 0){
                window.location = 'registrar_RF_REINTEGRO.php?sltDepOr='+dep1+'&sltResOr='+res1+'&sltDepDe='+dep2+'&sltResDe='+res2;
            }
        });

        $("#sltTipoT").change(function(e){
            var tipo = e.target.value;
            var num  = "";
            $("#txtNumero").empty();
            $.getJSON("access.php?controller=Reintegro&action=consecutivo&tipo="+tipo, function(data){
                num = JSON.parse(data);
                $("#txtNumero").val(num);
            });
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

        function cargarBuscar(){
            $.get("access.php?controller=Reintegro&action=cargarBusqueda&compania="+<?php echo $compania ?>+'&param='+<?php  echo $paramA?>, function(data){
                $("#sltBuscar").html(data);
            });
        }

        $("#sltBuscar").change(function(e){
            mov = e.target.value;
            if(mov.length > 0){
                window.location = 'registrar_RF_REINTEGRO.php?reintegro='+md5(mov);
            }
        });

        function nuevo(){
            window.location = 'registrar_RF_REINTEGRO.php';
        }

        function eliminarD(detalle, producto){
            $("#modalConfirmacion").modal("show");
            $("#btn-del").click(function(){
                var result = "";
                $.ajax({
                    url:"access.php?controller=Reintegro&action=eliminar&detalle="+detalle+"&producto="+producto,
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
        if(!empty($_GET['sltDepOr'])){
            $html .= "$('#btn-imprimir').attr('disabled', true);";
        }

        if(!empty($_GET['reintegro'])){
            $html .= "\n\t$('#btnT, #sltDepOr, #sltResOr').attr('disabled', true);";
            $html .= "\n\t$('#btnT').removeAttr('onclick');";
            $html .= "\n\t$(\"#btnST, #btnSN\").css(\"display\", \"none\");";
        }
        echo $html;
        ?>
    </script>
</body>
</html>