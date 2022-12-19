<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];
@$matriz = $_REQUEST['matriz'];
if(empty($_GET['id'])) {
    $titulo = "Registrar ";
    $titulo2= ".";
} else {
    $titulo = "Modificar ";
    $id     = $_GET['id'];
    $row    = $con->Listar("SELECT * FROM gy_tipo_pregunta
            WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][1];
    
    $clase = "SELECT p.id_unico, cp.id_unico, cp.nombre 
            FROM gy_tipo_pregunta p LEFT JOIN gy_clase_pregunta cp ON p.id_clase_pregunta = cp.id_unico
            WHERE md5(p.id_unico) = '$id'";
    
    $clas = $mysqli->query($clase);
    $cp = mysqli_fetch_row($clas);
}
?>
<title>Tipo Pregunta</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label  #nombre-error, #clase_pregunta-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
    font-size: 10px

}
body{
                font-size: 11px;
            } 
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
            font-family: Arial;}
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
<style>
    .form-control {font-size: 12px;}
</style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Tipo Pregunta'?></h2>
                <a href="listar_GY_PREGUNTA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <?php if(empty($_GET['id'])) { ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Nombre" title="Ingrese una Pregunta">
                        </div>
                        <div class="form-group" style="margin-top: 0px; ">
                            <label for="clase_pregunta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Pregunta:</label>
                            <select required id="clase_pregunta" name="clase_pregunta" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Clase de Pregunta">
                                <option value="">Clase Pregunta</option>
                               <?php
                               $row = $con->Listar("SELECT  id_unico, nombre
                                FROM gy_clase_pregunta WHERE compania = '$compania'
                                ORDER BY nombre ASC ");
                               for ($i = 0; $i < count($row); $i++) {
                                   echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                               }
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                        <input type="hidden" name="txtmat" id="txtmat" value="<?php echo $matriz; ?>" >
                    </form>
                    <script>
                        function registrar(){
                            var MR = $("#txtmat").val();
                            jsShowWindowLoad('Guardando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_tipo_preguntaJson.php?action=2",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Guardada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            if(MR == 1){
                                                window.history.go(-1);
                                            }else{
                                                document.location='listar_GY_TIPO_PREGUNTA.php';
                                            }
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
                        <div class="form-group">
                            <input type="hidden" name="id" id="id" value="<?php echo $row[0][0]?>">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Pregunta:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Nombre" title="Ingrese una Pregunta" value="<?php echo $row[0][1]?>">
                        </div>
                        <div class="form-group" style="margin-top: 0px; ">
                            <label for="clase_pregunta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Pregunta:</label>
                            <select required id="clase_pregunta" name="clase_pregunta" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Clase de Pregunta">
                                <option value="<?php echo $cp[1] ?>"><?php echo $cp[2] ?></option>
                               <?php
                               $row = $con->Listar("SELECT  id_unico, nombre
                                FROM gy_clase_pregunta
                                WHERE compania = '$compania' AND id_unico != '$cp[1]' 
                                ORDER BY nombre ASC");
                               for ($i = 0; $i < count($row); $i++) {
                                   echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                               }
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                    <script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_tipo_preguntaJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location='listar_GY_TIPO_PREGUNTA.php';
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
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
      $(document).ready(function () {
          $(".select2_single").select2({
              allowClear: true
          });
      });
    </script>
</body>
</html>



