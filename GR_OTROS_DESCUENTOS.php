<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];

if(empty($_GET['t'])) {
    $titulo = "Listar ";
    $titulo2= ".";
    $row = $con->Listar("SELECT o.id_unico , c.codigo, c.nombre, 
        DATE_FORMAT(o.fecha_inicial, '%d/%m/%Y'),
        DATE_FORMAT(o.fecha_final, '%d/%m/%Y'), 
        o.porcentaje, 
        o.vigencia_inicial, o.vigencia_final 
        FROM gr_otros_descuentos o 
        LEFT JOIN gr_concepto c ON o.concepto = c.id_unico ");
}elseif(empty($_GET['id'])) {
    $titulo = "Registrar ";
    $titulo2= ".";
} else {
    $titulo = "Modificar ";
    $id     = $_GET['id'];
    $row    = $con->Listar("SELECT o.id_unico, 
            c.id_unico , c.codigo, c.nombre, 
            DATE_FORMAT(o.fecha_inicial, '%d/%m/%Y'),
            DATE_FORMAT(o.fecha_final, '%d/%m/%Y'), 
            o.porcentaje , 
            o.vigencia_inicial, o.vigencia_final 
        FROM gr_otros_descuentos o
        LEFT JOIN gr_concepto c ON o.concepto = c.id_unico 
        WHERE md5(o.id_unico)='$id'");
    $titulo2= $row[0][2].' - '.$row[0][3];
}
?>
<title>Otros Descuentos</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<style>

    label #fechaI-error,#concepto-error,#fechaF-error, #porcenjaje-error {
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
</style>
<script>
$().ready(function() {
    var validator = $("#form").validate({
        ignore: "",
        errorPlacement: function(error, element) {
            $( element )
                .closest( "form" )
                .find( "label[for='" + element.attr( "id" ) + "']" )
                .append( error );
        },
        rules: {
        }
    });
    $(".cancel").click(function() {
        validator.resetForm();
    });
});

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
        changeYear: true,
        yearSuffix: '',
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);

     
    $("#fechaI").datepicker({changeMonth: true}).val();
    $("#fechaF").datepicker({changeMonth: true}).val();
        
        
});
</script>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Otros Descuentos'?></h2>
                <?php if(empty($_GET['t'])) { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Concepto</strong></td>
                                    <td><strong>Fecha Inicial</strong></td>
                                    <td><strong>Fecha Final</strong></td>
                                    <td><strong>Porcentaje</strong></td>
                                    <td><strong>Vigencia Inicial</strong></td>
                                    <td><strong>Vigencia Final</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Concepto</th>
                                    <th>Fecha Inicial</th>
                                    <th>Fecha Final</th>
                                    <th>Porcentaje</th>
                                    <th>Vigencia Inicial</th>
                                    <th>Vigencia Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td><a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GR_OTROS_DESCUENTOS.php?t=1&id=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                                        <td><?php echo $row[$i][1].' - '.ucwords(mb_strtolower($row[$i][2])); ?></td>
                                        <td><?php echo $row[$i][3]; ?></td>
                                        <td><?php echo $row[$i][4]; ?></td>
                                        <td><?php echo $row[$i][5].'%'; ?></td>
                                        <td><?php echo $row[$i][6]; ?></td>
                                        <td><?php echo $row[$i][7]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GR_OTROS_DESCUENTOS.php?t=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
                <script>
                    function eliminar(id){
                        $("#modalEliminar").modal('show');
                        $("#aceptarE").click(function(){
                            $("#modalEliminar").modal('hide');
                            $.ajax({
                                type:"GET",
                                url: "jsonPptal/gr_otros_descuentos.php?action=3&id="+id,
                                success: function (data) {
                                    let response = JSON.parse(data);
                                    if(response==1){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location='GR_OTROS_DESCUENTOS.php';
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Eliminar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                        });    
                      }
                </script>
                <?php }else{ ?>
                <a href="GR_OTROS_DESCUENTOS.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <?php if(empty($_GET['id'])) { ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="concepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:-10px">
                            <select name="concepto" id="concepto" class="form-control select2" title="Seleccione Concepto" style="height: auto; width:300px" required>
                                <option value="">Concepto</option>
                                <?php 
                                $rowc = $con->Listar("SELECT id_unico, codigo, nombre FROM gr_concepto");
                                for ($i = 0; $i < count($rowc); $i++) {
                                echo '<option value="'.$rowc[$i][0].'">'.$rowc[$i][1].' - '.ucwords(mb_strtolower($rowc[$i][2])).'</option>';
                                }?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fechaI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Inicial:</label>
                            <input type="text" name="fechaI" id="fechaI" class="form-control" required="required" placeholder="Fecha Inicial" title="Ingrese Fecha Inicial" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="fechaF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Final:</label>
                            <input type="text" name="fechaF" id="fechaF" class="form-control" required="required" placeholder="Fecha Final" title="Ingrese Fecha Final" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="porcentaje" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje:</label>
                            <input type="text" name="porcentaje" id="porcentaje" class="form-control" placeholder="Porcentaje" onkeypress="return txtValida(event, 'dec', 'valor', '2');" required="required" title="Ingrese Porcentaje">
                        </div>
                        <div class="form-group">
                            <label for="vigenciaI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia Inicial:</label>
                            <input type="text" name="vigenciaI" id="vigenciaI" class="form-control" placeholder="Vigencia Inicial" onkeypress="return txtValida(event, 'dec', 'valor', '2');" required="required" title="Ingrese Vigencia Inicial">
                        </div>
                        <div class="form-group">
                            <label for="vigenciaF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia Final:</label>
                            <input type="text" name="vigenciaF" id="vigenciaF" class="form-control" placeholder="Vigencia Final" onkeypress="return txtValida(event, 'dec', 'valor', '2');" required="required" title="Ingrese Vigencia Final">
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                    <script>
                        function registrar(){
                            var formData = new FormData($("#form")[0]);  
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gr_otros_descuentos.php?action=1",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Guardada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location='GR_OTROS_DESCUENTOS.php';
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>
                    <?php } else { ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]?>">
                        <div class="form-group">
                            <label for="concepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:-10px">
                            <select name="concepto" id="concepto" class="form-control select2" title="Seleccione Concepto" style="height: auto; width:300px" required>
                                <?php 
                                echo '<option value="'.$row[0][1].'">'.$row[0][2].' - '.ucwords(mb_strtolower($row[0][3])).'</option>';
                                $rowc = $con->Listar("SELECT id_unico, codigo, nombre FROM gr_concepto WHERE id_unico != ".$row[0][1]);
                                for ($i = 0; $i < count($rowc); $i++) {
                                echo '<option value="'.$rowc[$i][0].'">'.$rowc[$i][1].' - '.ucwords(mb_strtolower($rowc[$i][2])).'</option>';
                                }?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fechaI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Inicial:</label>
                            <input type="text" name="fechaI" id="fechaI" class="form-control" required="required" placeholder="Fecha Inicial" title="Ingrese Fecha Inicial" autocomplete="off" value="<?= $row[0][4]?>">
                        </div>
                        <div class="form-group">
                            <label for="fechaF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha Final:</label>
                            <input type="text" name="fechaF" id="fechaF" class="form-control" required="required" placeholder="Fecha Final" title="Ingrese Fecha Final" autocomplete="off" value="<?= $row[0][5]?>">
                        </div>
                        <div class="form-group">
                            <label for="porcentaje" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje:</label>
                            <input type="text" name="porcentaje" id="porcentaje" class="form-control" placeholder="Porcentaje" onkeypress="return txtValida(event, 'dec', 'valor', '2');" required="required" title="Ingrese Porcentaje" value="<?= $row[0][6]?>">
                        </div>
                        <div class="form-group">
                            <label for="vigenciaI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia Inicial:</label>
                            <input type="text" name="vigenciaI" id="vigenciaI" class="form-control" placeholder="Vigencia Inicial" onkeypress="return txtValida(event, 'dec', 'valor', '2');" required="required" title="Ingrese Vigencia Inicial" value="<?= $row[0][7]?>">
                        </div>
                        <div class="form-group">
                            <label for="vigenciaF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia Final:</label>
                            <input type="text" name="vigenciaF" id="vigenciaF" class="form-control" placeholder="Vigencia Final" onkeypress="return txtValida(event, 'dec', 'valor', '2');" required="required" title="Ingrese Vigencia Final" value="<?= $row[0][8]?>">
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                    </form>
                    <script>
                        function modificar(){
                            var formData = new FormData($("#form")[0]);  
                            $.ajax({
                                type: 'POST',
                                url: "jsonPptal/gr_otros_descuentos.php?action=2",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location='GR_OTROS_DESCUENTOS.php';
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea Eliminar El Registro Seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="cancelarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
   
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/md5.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#concepto").select2();
    </script>
</body>
</html>



