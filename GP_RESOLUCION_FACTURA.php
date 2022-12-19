<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];
if(empty($_GET['id'])) {
    $titulo = "Listar ";
    $titulo2= ".";
    $row = $con->Listar("SELECT rf.id_unico, 
    tf.id_unico, tf.prefijo, tf.nombre, 
    DATE_FORMAT(rf.fecha_inicial, '%d/%m/%Y'), 
    DATE_FORMAT(rf.fecha_final, '%d/%m/%Y'), 
    rf.numero_inicial, rf.numero_final, 
    rf.descripcion, rf.numero_resolucion 
    FROM gp_resolucion_factura rf
    LEFT JOIN gp_tipo_factura tf ON rf.tipo_factura = tf.id_unico 
    WHERE tf.compania = $compania ");
} elseif(($_GET['id'])==1) {
    $titulo = "Registrar ";
    $titulo2= ".";
    $rtc    = $con->Listar("SELECT id_unico, prefijo, nombre FROM gp_tipo_factura WHERE compania = $compania ");
} elseif(($_GET['id'])==2) {
    $titulo = "Modificar ";
    $id     = $_GET['id_r'];
    $row    = $con->Listar("SELECT rf.id_unico,
        tf.id_unico, tf.prefijo, tf.nombre, 
        DATE_FORMAT(rf.fecha_inicial, '%d/%m/%Y'), 
        DATE_FORMAT(rf.fecha_final, '%d/%m/%Y'), 
        rf.numero_inicial, rf.numero_final, 
        rf.descripcion, rf.numero_resolucion 
        FROM gp_resolucion_factura rf
        LEFT JOIN gp_tipo_factura tf ON rf.tipo_factura = tf.id_unico 
        WHERE md5(rf.id_unico)='$id'");
    $titulo2= $row[0][2].' - '.$row[0][3];
    $rtc    = $con->Listar("SELECT id_unico, prefijo, nombre FROM gp_tipo_factura WHERE compania = $compania AND id_unico != ".$row[0][1]);
}
?>
<title>Resolución Factura</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<style>
    label #tipo-error, #numero-error, #numeroInicial-error, #numeroFinal-error , #fechaInicial-error, #fechaFinal-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
body{
    font-size: 12px;
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
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<script type="text/javascript">
    $(document).ready(function ()
    {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true,
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fechaInicial").datepicker({changeMonth: true}).val();
        $("#fechaFinal").datepicker({changeMonth: true}).val();
    });
</script>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Resolución Factura'?></h2>
                <?php if(empty($_GET['id'])) { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tipo Factura</strong></td>
                                    <td><strong>Número Resolución</strong></td>
                                    <td><strong>Fecha Inicial</strong></td>
                                    <td><strong>Fecha Final</strong></td>
                                    <td><strong>Número Inicial</strong></td>                                    
                                    <td><strong>Número Final</strong></td>
                                    <td><strong>Descripción</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo Factura</th>
                                    <th>Número Resolución</th>
                                    <th>Fecha Inicial</th>
                                    <th>Fecha Final</th>
                                    <th>Número Inicial</th>
                                    <th>Número Final</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GP_RESOLUCION_FACTURA.php?id=2&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td><?php echo mb_strtoupper($row[$i][2]).' - '.ucwords(mb_strtolower($row[$i][3])); ?></td>
                                        <td><?php echo $row[$i][9]; ?></td>
                                        <td><?php echo $row[$i][4]; ?></td>
                                        <td><?php echo $row[$i][5]; ?></td>
                                        <td><?php echo $row[$i][6]; ?></td>
                                        <td><?php echo $row[$i][7]; ?></td>
                                        <td><?php echo $row[$i][8]; ?></td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GP_RESOLUCION_FACTURA.php?id=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
                <?php }  elseif(($_GET['id'])==1){ ?>
                    <a href="GP_RESOLUCION_FACTURA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left">
                                    <label for="tipo" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Factura:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="tipo" id="tipo" class="form-control select2" title="Seleccione Tipo Factura"  required="required">
                                            <option value="">Tipo Factura</option>
                                            <?php for ($i = 0; $i < count($rtc); $i++) {
                                               echo '<option value="'.$rtc[$i][0].'">'.$rtc[$i][1].' - '.$rtc[$i][2].'</option>';
                                            }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" >
                                    <label for="numero" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número Resolución:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <input type="text" name="numero" id="numero" class="form-control " style=" width: 100%" required="required" placeholder="Número Resolución" title="Ingrese Número Resolución">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;">
                                    <label for="fechaInicial" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Fecha Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px; margin-top: -10px;">
                                    <input type="text" name="fechaInicial" id="fechaInicial" class="form-control " style=" width: 100%" placeholder="Fecha Inicial" title="Ingrese Fecha Inicial">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;">
                                    <label for="fechaFinal" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Fecha Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px; margin-top: -10px;">
                                    <input type="text" name="fechaFinal" id="fechaFinal" class="form-control " style=" width: 100%" placeholder="Fecha Final" title="Ingrese Fecha Final">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="numeroInicial" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="numeroInicial" id="numeroInicial" class="form-control " style=" width: 100%" required="required" placeholder="Número Inicial" title="Ingrese Número Inicial" onkeypress="return txtValida(event, 'num')">
                                </div>
                            </div>   
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="numeroFinal" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="numeroFinal" id="numeroFinal" class="form-control " style=" width: 100%" required="required" placeholder="Número Final" title="Ingrese Número Final" onkeypress="return txtValida(event, 'num')">
                                </div>
                            </div>  
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="descripcion" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Descripción:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <textarea type="text" name="descripcion" id="descripcion" class="form-control " style=" width: 100%; height:80px" placeholder="Descripción" title="Descripción"></textarea>
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } elseif(($_GET['id'])==2){  ?>
                    <a href="GP_RESOLUCION_FACTURA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]?>">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left">
                                    <label for="tipo" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Factura:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="tipo" id="tipo" class="form-control select2" title="Seleccione Tipo Factura"  required="required">
                                            <?php 
                                            echo '<option value="'.$row[0][1].'">'.$row[0][2].' - '.$row[0][3].'</option>';
                                            for ($i = 0; $i < count($rtc); $i++) {
                                               echo '<option value="'.$rtc[$i][0].'">'.$rtc[$i][1].' - '.$rtc[$i][2].'</option>';
                                            }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" >
                                    <label for="numero" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número Resolución:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <input type="text" name="numero" id="numero" class="form-control " style=" width: 100%" required="required" placeholder="Número Resolución" title="Ingrese Número Resolución" value="<?php echo $row[0][9]?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;">
                                    <label for="fechaInicial" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Fecha Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px; margin-top: -10px;">
                                    <input type="text" name="fechaInicial" id="fechaInicial" class="form-control " style=" width: 100%" placeholder="Fecha Inicial" title="Ingrese Fecha Inicial" value="<?php echo $row[0][4]?>">
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;">
                                    <label for="fechaFinal" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Fecha Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px; margin-top: -10px;">
                                    <input type="text" name="fechaFinal" id="fechaFinal" class="form-control " style=" width: 100%" placeholder="Fecha Final" title="Ingrese Fecha Final" value="<?php echo $row[0][5]?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="numeroInicial" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="numeroInicial" id="numeroInicial" class="form-control " style=" width: 100%" required="required" placeholder="Número Inicial" title="Ingrese Número Inicial" value="<?php echo $row[0][6]?>" onkeypress="return txtValida(event, 'num')">
                                </div>
                            </div>   
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="numeroFinal" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="numeroFinal" id="numeroFinal" class="form-control " style=" width: 100%" required="required" placeholder="Número Final" title="Ingrese Número Final" value="<?php echo $row[0][7]?>" onkeypress="return txtValida(event, 'num')">
                                </div>
                            </div>  
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="descripcion" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Descripción:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <textarea type="text" name="descripcion" id="descripcion" class="form-control " style=" width: 100%; height:80px" placeholder="Descripción" title="Descripción"><?php echo $row[0][8]?></textarea>
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } ?>
                </div>
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2({
            allowClear:true
        });
    </script>
    <script>
        function registrar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php?action=38",
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
                            document.location='GP_RESOLUCION_FACTURA.php';
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
    <script>
        function modificar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php?action=39",
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
                            document.location='GP_RESOLUCION_FACTURA.php';
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
    <script>
        function eliminar(id){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                $("#modalEliminar").modal("hide");
                var form_data = {action:40, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_facturaJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location='GP_RESOLUCION_FACTURA.php';
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                 $("#modalMensajes").modal("hide");
                            })
                        }
                    }
                });
            })
            $("#cancelarE").click(function(){
                $("#modalEliminar").modal("hide");
            })
        }
    </script>
</body>
</html>

