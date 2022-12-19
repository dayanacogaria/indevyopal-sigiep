<?php

require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
if(empty($_GET['t'])){
    $titulo1 = "Tipo Cartera";
    $row = $con->Listar("SELECT * FROM gp_tipo_cartera");
} elseif($_GET['t']==1){
    $titulo1 = "Registrar Tipo Cartera";
    $t2      = '.';
    $act ='guardar()';
} elseif ($_GET['t']==2) {
    $titulo1    = "Modificar Tipo Cartera";
    $act        = 'modificar()';
    $id         = $_GET['id'];
    $row        = $con->Listar("SELECT * FROM gp_tipo_cartera WHERE md5(id_unico)='$id'");
    $t2         = $row[0][1];
}
?>
<title>Tipo Cartera</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #nombre-error, #diasI-error, #diasF-error, #error-error {
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
        });
        $(".cancel").click(function() {
            validator.resetForm();
        });
    });
</script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px"><?php echo $titulo1;?></h2>
                <?php if(empty($_GET['t'])){ ?>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td class="cabeza" width="30px" align="center"></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Días Vencimiento</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza">Días Vencimiento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < count($row); $i++) { ?>
                                        <tr>
                                            <td class="campos" style="display: none;"><?php echo $row[$i][0]?></td>
                                            <td class="campos">
                                                <a  href="#" class="campos" onclick="javascript:eliminar(<?php echo $row[$i][0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                                <a class="campos" href="GP_TIPO_CARTERA.php?t=2&id=<?php echo md5($row[$i][0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                            </td>
                                            <td><?php echo ucwords(mb_strtolower(($row[$i][1])));?></td>
                                            <td><?php echo $row[$i][2].' - '.$row[$i][3];?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div align="right"><a href="GP_TIPO_CARTERA.php?t=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                        </div>
                    </div>
                <?php } else { ?>
                    <a href="GP_TIPO_CARTERA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $t2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:<?php echo $act?>">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php if($_GET['t']==1){ ?>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                
                                
                                <div class="form-group form-inline ">
                                <label for="diasI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Días Vencimiento:</label>
                                <input type="text" name="diasI" id="diasI" style="width: 100px;" class="form-control col-sm-2 " maxlength="100" title="Ingrese Día Inicial " onkeypress="return txtValida(event, 'num')" placeholder="Día Inicial " required>                                
                                <input type="text" name="diasF" id="diasF" style="width: 100px; margin-left: 10px" class="form-control col-sm-2 " maxlength="100" title="Ingrese Día Final " onkeypress="return txtValida(event, 'num')" placeholder="Día Final " required>
                                <label for="diasF" id="diasFE" class="col-sm-3 control-label" style="margin-top: -10px;color: #bd081c;font-weight: bold;"></label>
                                
                                </div>
                            </div>
                            <?php } elseif ($_GET['t']==2) { ?>
                            <input type="hidden" name="id" id="id" value="<?php echo $row[0][0];?>">
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required value="<?php echo $row[0][1];?>">
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <div class="form-group form-inline ">
                                <label for="diasI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Días Vencimiento:</label>
                                <input type="text" name="diasI" id="diasI" class="form-control col-sm-2" style="width: 100px;" maxlength="100" title="Ingrese Día Inicial" onkeypress="return txtValida(event, 'num')" placeholder="Día Inicial" required value="<?php echo $row[0][2];?>"/>
                                <input type="text" name="diasF" id="diasF" class="form-control col-sm-2" style="width: 100px;margin-left: 10px" maxlength="100" title="Ingrese Día Final" onkeypress="return txtValida(event, 'num')" placeholder="Día Final" required value="<?php echo $row[0][3];?>"/>
                                <label for="diasF" id="diasFE" class="col-sm-3 control-label" style="margin-top: -10px;color: #bd081c;font-weight: bold;"></label>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>
                        </form>
                    </div>
                    <?php } ?>
                </div>
            </div>
    </div>
    <?php require_once ('footer.php'); ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });      
        });
    </script>
    <script>
        $("#diasF").change(function(){
            var diasI = parseInt($("#diasI").val());
            var diasF = parseInt($("#diasF").val());
            if(diasI !="" && diasF!=""){
                if(diasF < diasI){
                    $("#diasFE").html("El Día Final Tiene Que Ser Mayor A Día Inicial");
                    $("#diasF").val("");
                }
            }
        })
    </script>
    <script>
        function guardar(){
            var diasI = parseInt($("#diasI").val());
            var diasF = parseInt($("#diasF").val());
            if(diasI !="" && diasF!=""){
                if(diasF < diasI){
                    $("#diasFE").html("El Día Final Tiene Que Ser Mayor A Día Inicial");
                    $("#diasF").val("");
                } else {
                    var formData = new FormData($("#form")[0]);  
                    $.ajax({
                        type: 'POST',
                        url: "jsonPptal/gf_facturaJson.php?action=14",
                        data:formData,
                        contentType: false,
                        processData: false,
                        success: function(response)
                        {
                            console.log(response);
                            if(response==1){
                                $("#mensaje").html('Información Guardada Correctamente');
                                $("#modalMensaje").modal("show");
                                $("#btnMsjAc").click(function(){
                                    document.location="GP_TIPO_CARTERA.php";
                                })
                            } else {
                                $("#mensaje").html('No Se Ha Podido Guardar La Información');
                                $("#modalMensaje").modal("show");
                                $("#btnMsjAc").click(function(){
                                    $("#modalMensaje").modal("hide");
                                })
                            }
                        }
                    });
                }
            }
        }
        function modificar(){
            var diasI = parseInt($("#diasI").val());
            var diasF = parseInt($("#diasF").val());
            if(diasI !="" && diasF!=""){
                if(diasF < diasI){
                    
                    $("#diasFE").html("El Día Final Tiene Que Ser Mayor A Día Inicial");
                    $("#diasF").val("");
                } else {
                    console.log('fd');
                    var formData = new FormData($("#form")[0]);  
                    $.ajax({
                        type: 'POST',
                        url: "jsonPptal/gf_facturaJson.php?action=15",
                        data:formData,
                        contentType: false,
                        processData: false,
                        success: function(response)
                        {
                            console.log(response);
                            if(response==1){
                                $("#mensaje").html('Información Modificada Correctamente');
                                $("#modalMensaje").modal("show");
                                $("#btnMsjAc").click(function(){
                                    document.location="GP_TIPO_CARTERA.php";
                                })
                            } else {
                                $("#mensaje").html('No Se Ha Podido Modificar La Información');
                                $("#modalMensaje").modal("show");
                                $("#btnMsjAc").click(function(){
                                    $("#modalMensaje").modal("hide");
                                })
                            }
                        }
                    });
                }
            }
            
        }
        function eliminar(id){
            $("#modalEliminar").modal("show");
            $("#btnModalEliminarA").click(function(){
                var form_data = {action:16, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_facturaJson.php",
                    data:form_data,
                    success: function(response)
                    {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensaje").modal("show");
                            $("#btnMsjAc").click(function(){
                                document.location="GP_TIPO_CARTERA.php";
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                            $("#modalMensaje").modal("show");
                            $("#btnMsjAc").click(function(){
                                $("#modalMensaje").modal("hide");
                            })
                        }
                    }
                });
            });
            $("#btnModalEliminarC").click(function(){
                $("#modalEliminar").modal("hide");
            })
           
        }
    </script>
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight:normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMsjAc" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="font-weight:normal">¿Desea Eliminar El Registro Seleccionado?</label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModalEliminarA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnModalEliminarC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


