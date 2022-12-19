<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
if(empty($_GET['id'])) {
    $titulo = "Listar ";
    $titulo2= ".";
    $row = $con->Listar("SELECT id_unico, IF(tipo=1, 'Concepto de los Ingresos', 'Concepto de los Aportes'),
        numero, nombre 
        FROM gn_concepto_certificado WHERE compania =  $compania ");
} elseif(($_GET['id'])==1) {
    $titulo = "Registrar ";
    $titulo2= ".";
} elseif(($_GET['id'])==2) {
    $titulo = "Modificar ";
    $id     = $_GET['id_r'];
    $row    = $con->Listar("SELECT id_unico, tipo, 
        IF(tipo=1, 'Concepto de Ingresos', 'Concepto Aportes'),
        numero, nombre
        FROM gn_concepto_certificado 
        WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][3].' - '.$row[0][4];
}elseif(($_GET['id'])==3) {
    $titulo = "Configuración ";
    $id     = $_GET['id_r'];
    $row    = $con->Listar("SELECT id_unico, tipo, 
        IF(tipo=1, 'Concepto de Ingresos', 'Concepto Aportes'),
        numero, nombre
        FROM gn_concepto_certificado 
        WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][3].' - '.$row[0][4];
}
?>
<title>Conceptos Certificado Ingresos y Retenciones</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<style>
    label #nombre-error, #tipo-error, #numero-error{
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
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Conceptos Certificado Ingresos Y Retenciones'?></h2>
                <?php if(empty($_GET['id'])) { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tipo</strong></td>
                                    <td><strong>Número</strong></td>
                                    <td><strong>Nombre</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo</th>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GN_CONCEPTO_CERTIFICADO.php?id=2&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                            <a href="GN_CONCEPTO_CERTIFICADO.php?id=3&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Configuración" class="glyphicon glyphicon-cog" ></i></a>
                                        </td>
                                        <td><?php echo ucwords(mb_strtolower($row[$i][1])); ?></td>
                                        <td><?php echo $row[$i][2]; ?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[$i][3])); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="informes_nomina/INF_CONFIGURACION_CERTIFICADO.php" class="btn btn-primary"><i class="fa fa-file-excel-o"></i></a><a href="GN_CONCEPTO_CERTIFICADO.php?id=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px;">Registrar Nuevo</a> </div>       
                    </div>
                </div>
                <?php }  elseif(($_GET['id'])==1){ ?>
                    <a href="GN_CONCEPTO_CERTIFICADO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left">
                                    <label for="tipo" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="tipo" id="tipo" class="form-control select2" title="Seleccione Tipo Concepto"  required="required">
                                            <option value="">Tipo</option>
                                            <option value="1">De Los Ingresos</option>
                                            <option value="2">De Los Aportes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 0px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="numero" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="numero" id="numero" class="form-control " style=" width: 100%" required="required" placeholder="Número" title="Ingrese Número" onkeypress="return txtValida(event, 'num')" autocomplete="off">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="nombre" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="nombre" id="nombre" class="form-control " style=" width: 100%" required="required" placeholder="Nombre" title="Ingrese Nombre" onkeypress="return txtValida(event, 'num_car')" autocomplete="off">
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
                    <a href="GN_CONCEPTO_CERTIFICADO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]?>">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left">
                                    <label for="tipo" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="tipo" id="tipo" class="form-control select2" title="Seleccione Tipo Concepto"  required="required">
                                        <?php echo '<option value="'.$row[0][1].'">'.$row[0][2].'</option>';
                                        if($row[0][1]==1) {
                                            echo '<option value="2">De Los Aportes</option>';
                                        } else {
                                            echo '<option value="1">De Los Ingresos</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 0px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="numero" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Número:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="numero" id="numero" class="form-control " style=" width: 100%" required="required" placeholder="Número" title="Ingrese Número" onkeypress="return txtValida(event, 'num')" value="<?php echo $row[0][3]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -10px;" >
                                    <label for="nombre" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -10px;">
                                    <input type="text" name="nombre" id="nombre" class="form-control " style=" width: 100%" required="required" placeholder="Nombre" title="Ingrese Nombre" onkeypress="return txtValida(event, 'num_car')" value="<?php echo $row[0][4]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } elseif(($_GET['id'])==3){ ?>
                    <a href="GN_CONCEPTO_CERTIFICADO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrarC()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]?>">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left">
                                    <label for="concepto" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Conceptos Nómina:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="concepto[]" id="concepto[]" class="form-control select2" title="Seleccione Conceptos Nómina"  required="required" multiple="multiple">
                                        <?php $rowcn = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo, c.descripcion 
                                            FROM gn_novedad n 
                                            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                                            WHERE p.parametrizacionanno = $anno  
                                            AND c.id_unico NOT IN 
                                            (SELECT concepto_nomina FROM gn_configuracion_certificado 
                                            WHERE concepto_certificado =".$row[0][0]." AND parametrizacionanno =$anno  )
                                            ORDER BY cast(c.codigo as unsigned) ASC");
                                            for ($i = 0; $i < count($rowcn); $i++) {
                                                echo '<option value="'.$rowcn[$i][0].'">'.$rowcn[$i][1].' - '.$rowcn[$i][2].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>    
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px"></td>
                                        <td><strong>Concepto Nómina</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Concepto Nómina</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rowc = $con->Listar("SELECT cf.id_unico, c.codigo, c.descripcion FROM gn_configuracion_certificado cf 
                                    LEFT JOIN gn_concepto c ON cf.concepto_nomina = c.id_unico
                                    WHERE cf.concepto_certificado =".$row[0][0]." AND cf.parametrizacionanno =$anno");
                                    for ($i = 0; $i < count($rowc); $i++) { ?>
                                        <tr>
                                            <td style="display: none;"></td>
                                            <td>
                                                <a href="#" onclick="javascript:eliminarcf(<?php echo $rowc[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            </td>
                                            <td><?php echo $rowc[$i][1].' - '.ucwords(mb_strtolower($rowc[$i][2])); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div align="right"><a href="GN_CONCEPTO_CERTIFICADO.php?id=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                        </div>
                    </div>
                <?php }?>
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
                url: "jsonNomina/gn_certificadoJson.php?action=1",
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
                            document.location='GN_CONCEPTO_CERTIFICADO.php';
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
                url: "jsonNomina/gn_certificadoJson.php?action=2",
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
                            document.location='GN_CONCEPTO_CERTIFICADO.php';
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
                    url: "jsonNomina/gn_certificadoJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location='GN_CONCEPTO_CERTIFICADO.php';
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
    <script>
        function registrarC(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonNomina/gn_certificadoJson.php?action=4",
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
                            document.location='GN_CONCEPTO_CERTIFICADO.php';
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
        function eliminarcf(id){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                $("#modalEliminar").modal("hide");
                var form_data = {action:5, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonNomina/gn_certificadoJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location.reload();
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

