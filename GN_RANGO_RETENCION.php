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
    $row = $con->Listar("SELECT id_unico, rango_inicial, rango_final, uvt_descontar, uvt_sumar, tarifa_marginal 
    FROM gn_rango_retencion ");
} elseif(($_GET['id'])==1) {
    $titulo = "Registrar ";
    $titulo2= ".";
} elseif(($_GET['id'])==2) {
    $titulo = "Modificar ";
    $id     = $_GET['id_r'];
    $row    = $con->Listar("SELECT id_unico, rango_inicial, rango_final, uvt_descontar, uvt_sumar, tarifa_marginal 
    FROM gn_rango_retencion
    WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][1].' - '.$row[0][2];
}
?>
<title>Rango Retención</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<style>
    label #rangoI-error, #rangoF-error,#uvtd-error,#uvts-error,#tarifam-error{
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
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Rango Retención'?></h2>
                <?php if(empty($_GET['id'])) { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Rango Inicial</strong></td>
                                    <td><strong>Rango Final</strong></td>
                                    <td><strong>UVT Descontar</strong></td>
                                    <td><strong>UVT Sumar</strong></td>
                                    <td><strong>Tarifa Marginal</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Rango Inicial</th>
                                    <th>Rango Final</th>
                                    <th>UVT Descontar</th>
                                    <th>UVT Sumar</th>
                                    <th>Tarifa Marginal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GN_RANGO_RETENCION.php?id=2&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td><?= $row[$i][1]; ?></td>
                                        <td><?= $row[$i][2]; ?></td>
                                        <td><?= $row[$i][3]; ?></td>
                                        <td><?= $row[$i][4]; ?></td>
                                        <td><?= $row[$i][5]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GN_RANGO_RETENCION.php?id=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
                <?php }  elseif(($_GET['id'])==1){ ?>
                    <a href="GN_RANGO_RETENCION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="rangoI" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Rango Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="rangoI" id="rangoI" class="form-control " style=" width: 100%" required="required" placeholder="Rango Inicial" title="Ingrese Rango Inicial" onkeypress="return txtValida(event,'num')">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="rangoF" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Rango Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="rangoF" id="rangoF" class="form-control " style=" width: 100%" required="required" placeholder="Rango Final" title="Ingrese Rango Final" onkeypress="return txtValida(event,'num')">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="uvtd" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>UVT Descontar:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="uvtd" id="uvtd" class="form-control " style=" width: 100%" required="required" placeholder="UVT Descontar" title="Ingrese UVT Descontar" onkeypress="return txtValida(event,'num')">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="uvts" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>UVT Sumar:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="uvts" id="uvts" class="form-control " style=" width: 100%" required="required" placeholder="UVT Sumar" title="Ingrese UVT Sumar" onkeypress="return txtValida(event,'num')">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="tarifam" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tarifa Marginal:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="tarifam" id="tarifam" class="form-control " style=" width: 100%" required="required" placeholder="Tarifa Marginal" title="Ingrese Tarifa Marginal" onkeypress="return txtValida(event,'num')">
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
                    <a href="gn_rango_retencion.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?= $row[0][0]?>">
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="rangoI" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Rango Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="rangoI" id="rangoI" class="form-control " style=" width: 100%" required="required" placeholder="Rango Inicial" title="Ingrese Rango Inicial" onkeypress="return txtValida(event,'num')" value="<?=$row[0][1]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="rangoF" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Rango Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="rangoF" id="rangoF" class="form-control " style=" width: 100%" required="required" placeholder="Rango Final" title="Ingrese Rango Final" onkeypress="return txtValida(event,'num')" value="<?=$row[0][2]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="uvtd" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>UVT Descontar:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="uvtd" id="uvtd" class="form-control " style=" width: 100%" required="required" placeholder="UVT Descontar" title="Ingrese UVT Descontar" onkeypress="return txtValida(event,'num')" value="<?=$row[0][3]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="uvts" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>UVT Sumar:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="uvts" id="uvts" class="form-control " style=" width: 100%" required="required" placeholder="UVT Sumar" title="Ingrese UVT Sumar" onkeypress="return txtValida(event,'num')" value="<?=$row[0][4]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="tarifam" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tarifa Marginal:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="tarifam" id="tarifam" class="form-control " style=" width: 100%" required="required" placeholder="Tarifa Marginal" title="Ingrese Tarifa Marginal" onkeypress="return txtValida(event,'num')" value="<?=$row[0][5]?>">
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
        $("#clase").select2();
        $("#concepto").select2();
        $(".select2").select2({
            allowClear:true
        });
    </script>
    <script>
        function registrar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonNomina/gn_RetencionJson.php?action=1",
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
                            document.location='GN_RANGO_RETENCION.php';
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
                url: "jsonNomina/gn_RetencionJson.php?action=2",
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
                            document.location='GN_RANGO_RETENCION.php';
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
                var form_data = {action:3, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonNomina/gn_RetencionJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location='GN_RANGO_RETENCION.php';
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

