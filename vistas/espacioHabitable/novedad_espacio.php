<?php
require './Conexion/conexion.php';
require_once './head.php';
?>
    <title>Novedad Espacios Habitables</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <link rel="stylesheet" href="css/toastr.css">
    <style>
        .cabeza{
            font-weight: 700;
        }
    </style>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 col-md-10 col-lg-10">
            <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Novedad Espacio <?php echo $espacio[1] ?></h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                <table id="tablaX" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <td class="cabeza" style="width: 7%;"></td>
                        <td class="cabeza">Novedad</td>
                        <td class="cabeza">Fecha Inicial</td>
                        <td class="cabeza">Fecha Final</td>
                        <td class="cabeza">Activo</td>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $html = "";
                    while($row = mysqli_fetch_row($data)) { ?>
                        <tr>
                            <td>
                                <a href="javascript:eliminar('access.php?controller=EspacioHabitable&action=eliminarNovedadEspacio&id=<?php echo $row[0] ?>')" ><i title="Eliminar" class="glyphicon glyphicon-trash" ></i></a>
                                <a onclick="modificarModal(<?php echo $row[0] . ',' . "'" . $row[2] . "'" . ',' . "'" . $row[3] . "'" . ',' . $row[5]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                            </td>
                            <td><?php echo utf8_decode((ucwords(strtolower($row[1])))); ?></td>
                            <td><?php echo utf8_decode((ucwords(strtolower($row[2])))); ?>
                            </td>
                            
                            <td>
                                <?php 
                                    if($row[3] != '0000-00-00 00:00:00'){
                                        echo utf8_decode((ucwords(strtolower($row[3])))); 
                                    }else{
                                        echo "";
                                    }  
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if($row[4] == 1){
                                        echo "No";
                                    }else{
                                        echo "Si";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 10px">
            <div class="col-sm-push-10 col-md-push-10 col-lg-push-10 col-sm-2 col-md-2 col-lg-2">
                <button class="btn btn-primary col-sm-2 borde-sombra" id="btnmodal" style="color: #fff;border-color: #1075C1; width: 100%">Registrar Nuevo <span class="glyphicon glyphicon-plus"></button>
                <input type="hidden" id="inforegister">
            </div>
        </div>
    </div>
</div>    
    
    <!--Modal registrar Novedad-->
<div class="modal fade" id="mdlregistrar_nov_esp" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header" id="forma-modal">
                <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Novedad Espacio</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="<?php echo "access.php?controller=EspacioHabitable&action=guardarNovedadEspacio" ?>" method="post" class="form-horizontal" id="formTercero" enctype="multipart/form-data" style="font-size: 10px !important;">
                        <p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <input type="hidden" id="ingreso" value="1">
                            <label for="sltNovedad" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Novedad:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">                                
                                <select name="sltNovedad" id="sltNovedad" class="form-control select2" title="Seleccione una novedad" required tabindex="5">
                                    <option value="">Novedad</option>
                                    <?php
                                    $html = "";
                                    while($row = mysqli_fetch_row($novedades)){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltEspacioH" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Espacio Habitable:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="hidden" id="idespacio" name="idespacio" value="<?php echo $espacio[0] ?>">
                                <input type="text" name="sltEspacioH" id="sltEspacioH" class="form-control" maxlength="100" title="Espacio Habitable" onkeypress="return txtValida(event,'car')" placeholder="Espacio Habitable" style="width: 100%; font-size: 10px !important;" tabindex="1" autocomplete="off" value="<?php echo $espacio[1] ?>" disabled="">
<!--                                <select name="sltEspacioH" id="sltEspacioH" class="form-control select2" title="Seleccione tipo identificación" required tabindex="5">
                                    <option value="">Tipo Identificación</option>
                                </select>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaI" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Fecha Inicial:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtFechaI" id="txtFechaI" class="form-control entre" placeholder="Fecha I" required="" title="Fecha Inicial" style="width: 100%;" >
                                <input type="hidden" name="txtFechaIval" id="txtFechaIval" class="form-control entre" style="width: 100%;" >                                
                            </div>
                            <label for="txtFechaF" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Fecha Final:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtFechaF" id="txtFechaF" class="form-control entre" placeholder="Fecha F" title="Fecha Final" style="width: 100%;" >
                            </div>
                        </div>                    
                </div>
            </div>
            <div class="modal-footer" id="forma-modal">
                <div class="row">
                    <div class="form-group">
                        <label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>
                        <div class="col-sm-1 col-md-1 col-lg-1 text-right">                            
                            <button type="submit" class="btn btn-default" id="btnregistrar"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </div>
                    </div>
                    <script>
                        $("#formTercero").submit(function (evt){
                            evt.preventDefault();
                            let espacio = $("#idespacio").val();
                            let fechaI = $("#txtFechaI").val();
                            $.ajax({
                                type: 'GET',
                                url:   'access.php?controller=EspacioHabitable&action=validacionIngreso&id=' + espacio + '&fechaI=' + fechaI,
                                success: function (data){
                                    if (data == 1){
                                        toastr.warning('La habitación tiene un ingreso');
                                    }else if(data == 2){
                                        toastr.warning('La habitación tiene una reserva');
                                    }else if (data == 3){
                                        toastr.warning('La habitación tiene esta bloqueada');
                                    }else {
                                        alert("envia");
                                    }
                                }                               
                            });
                        });
                    </script>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

   <!--Modal modificar Novedad-->
 <div class="modal fade" id="mdlmodificar_nov_esp" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header" id="forma-modal">
                <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar Novedad Espacio</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="<?php echo "access.php?controller=EspacioHabitable&action=actualizarNovedadEspacio" ?>" method="post" class="form-horizontal" id="formTercero" enctype="multipart/form-data" style="font-size: 10px !important;">
                        <p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <input type="hidden" id="idnovespacio" name="idnovespacio">
                            <label for="sltNovedadx" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Novedad:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltNovedadx" id="sltNovedadx" class="form-control select2" title="Seleccione una novedad" required tabindex="5">
                                    <option value="">Novedad</option>
                                    <?php
                                        $id = $_GET['id'];
                                        $sqlCn = "SELECT * FROM gh_novedad ";
                                        $resc = $mysqli->query($sqlCn);
                                        while ($row2 = mysqli_fetch_row($resc)) {
                                            echo '<option value="' . $row2[0] . '">' . $row2[1] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <label for="sltEspacioH" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Espacio Habitable:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="hidden" id="idespacio" name="idespacio" value="<?php echo $espacio[0] ?>">
                                <input type="text" name="sltEspacioH" id="sltEspacioH" class="form-control" maxlength="100" title="Espacio Habitable" onkeypress="return txtValida(event,'car')" placeholder="Espacio Habitable" style="width: 100%; font-size: 10px !important;" tabindex="1" autocomplete="off" value="<?php echo $espacio[1] ?>" disabled="">
<!--                                <select name="sltEspacioH" id="sltEspacioH" class="form-control select2" title="Seleccione tipo identificación" required tabindex="5">
                                    <option value="">Tipo Identificación</option>
                                </select>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaIx" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Fecha Inicial:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtFechaIx" id="txtFechaIx" class="form-control entre" placeholder="Fecha I" required="" title="Fecha Inicial" style="width: 100%;" >
                            </div>
                            <label for="txtFechaFx" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Fecha Final:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtFechaFx" id="txtFechaFx" class="form-control entre" placeholder="Fecha F" title="Fecha Final" style="width: 100%;" >
                            </div>
                        </div>                    
                </div>
            </div>
            <div class="modal-footer" id="forma-modal">
                <div class="row">
                    <div class="form-group">
                        <label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>
                        <div class="col-sm-1 col-md-1 col-lg-1 text-right">
                            <button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
   
    <!-- Modal Validar Novedad-->
<div class="modal fade" id="mdlnovedadvalida" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Debe digitar la Fecha Final de la ultima novedad.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnI" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
<?php require_once 'modales.php'; ?>
<?php require_once './vistas/espacioHabitable/caracteristicas.modal.php'; ?>
<script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/php-date-formatter.min.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<script src="js/script_date.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/script_validation.js"></script>
<script src="js/toastr.min.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="js/script.js"></script>
</body>
</head>
    <script type="text/javascript" charset="utf-8">  
        
        function modificarModal(id, fechaI, fechaF,idnovedad){
                $("#idnovespacio").val(id);
                $("#sltNovedadx").val(idnovedad);
                var fechaIni = fechaI.substr(0,10);
                var fechaFini = fechaF.substr(0,10);
                var form_data = {
                    fechaI: fechaIni,
                    fechaF: fechaFini,
                    id: id
                }
                $.ajax({
                    type: 'POST',
                    url:  'access.php?controller=EspacioHabitable&action=validarEdicionNovedad',
                    data: form_data,
                    success: function (data){
                        var jsonarray = JSON.parse(data);
                        var inicio = jsonarray[0] + " " + fechaI.substr(11,5);
                        var final = jsonarray[1] + " " + fechaF.substr(11,5);
                        $("#txtFechaIx").val(inicio);
                        if(fechaF != '0000-00-00 00:00:00'){
                            $("#txtFechaFx").val(final);
                        }  
                        if(jsonarray[2] != 0){
                            var fechaval = jsonarray[2];
                            $('#txtFechaIx').datetimepicker({
                                minDate: fechaval.substr(0,10)
                                //minTime: fechaval.substr(11,5)
                            });
                        }

                    }
                });
                $("#mdlmodificar_nov_esp").modal("show");
        }
                                
        $("#btnmodal").click(function (){
            var id = $("#idespacio").val();
            $.ajax({
                type: 'GET',
                url:  'access.php?controller=EspacioHabitable&action=validarNovedad&id=' + id,
                success: function(data){
                    if(data == 2){ // nuevo
                        $("#mdlregistrar_nov_esp").modal('show');
                    }else if(data == 0){ // sin fecha final
                        $("#mdlnovedadvalida").modal('show');                        
                    }else{                         
                        $("#inforegister").val(3);
                        $("#txtFechaI").val(data);
                        $("#txtFechaIval").val(data);
                        $("#mdlregistrar_nov_esp").modal('show')
                        $('#txtFechaI').datetimepicker({
                            minDate: data.substring(0,16)
                            //minTime: data.substring(11,5)
                        });
                    }
                }
            });
            
        });
        
        
        $("#txtFechaI").blur(function (e) {
            $("#txtFechaF").val('');            
        });  
        
        $("#txtFechaF").change(function (e) {
            var res = comparaFechas("txtFechaI", "txtFechaF");
        });
        
        $("#txtFechaIx").blur(function (e) {
            $("#txtFechaFx").val('');
        });
        
        $("#txtFechaFx").change(function (e) {
            var res = comparaFechas("txtFechaIx", "txtFechaFx");
        });
        
        $(document).ready(function () {
            var i = 1;
            $('#tablaX thead th').each(function (){
                if (i != 1){
                    var title = $(this).text();
                    switch (i){
                        case 2:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                            break;
                        case 3:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                            break;
                        case 4:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                            break;
                        case 5:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                            break;
                        case 6:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                            break;
                    }
                    i = i + 1;
                } else {
                    i = i + 1;
                }
            });

            var table = $('#tablaX').DataTable({
                "autoFill": true,
                "scrollX": true,
                "pageLength": 5,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center'
                }]
            });
            var i = 0;
            table.columns().every(function () {
                var that = this;
                if (i != 0) {
                    $('input', this.header()).on('keyup change', function () {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                    i = i + 1;
                } else {
                    i = i + 1;
                }
            });
        });

        
    </script>
    </body>
</html>