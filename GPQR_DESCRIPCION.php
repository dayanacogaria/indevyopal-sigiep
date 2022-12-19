<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#13/12/2018 | Nestor B. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];

@$id_PQR = $_REQUEST['detalle'];
if(empty($_GET['id'])) {
    $titulo = "Registrar ";
    $titulo2= ".";
} else {
    $titulo = "Modificar ";
    $id     = $_GET['id'];
    $row    = $con->Listar("SELECT d.id_unico, d.descripcion, cd.id_unico, cd.nombre FROM gpqr_descripcion d
                            LEFT JOIN gpqr_clase_descripcion cd ON d.id_clase_descripcion = cd.id_unico
                            WHERE md5(d.id_unico)='$id'");
    $titulo2= $row[0][1];
}
?>
<title>Descipción</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label  #nombre-error, #sltClase-error {
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
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Descripción'?></h2>
                <a href="listar_GPQR_DESCRIPCION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <?php if(empty($_GET['id'])) { ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Descripción" title="Ingrese la Descripción">
                        </div>

                        <div class="form-group">
                            <label for="sltClase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Descripción:</label>
                            
                            <select required id="sltClase" name="sltClase" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Clase de Descripción">
                                <option value="">Clase Descripción</option>
                               <?php
                               $row = $con->Listar("SELECT  id_unico, nombre
                                FROM gpqr_clase_descripcion WHERE compania = '$compania' 
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
                        <input type="hidden" name="txtact" id="txtact" value="<?php echo $id_PQR; ?>" >
                    </form>
                    <script>
                        function registrar(){
                            var PQR = $("#txtact").val();
                            jsShowWindowLoad('Guardando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonPQR/gpqr_descripcionJson.php?action=2",
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
                                            if(PQR == 1){
                                                window.history.go(-1);
                                            }else{
                                                document.location='listar_GPQR_DESCRIPCION.php';
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
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Nombre" title="Ingrese Nombre" value="<?php echo $row[0][1]?>">
                        </div>

                        <div class="form-group">
                            <label for="sltClase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Descripción:</label>
                            <select required id="sltClase" name="sltClase" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Clase de Descripción">
                                <option value="<?php echo $row[0][2] ?>"><?php echo $row[0][3] ?></option>
                               <?php
                                $clas_des = "SELECT  id_unico, nombre
                                            FROM gpqr_clase_descripcion WHERE compania = '$compania' AND id_unico != '$row[0][2]'
                                            ORDER BY nombre ASC ";

                                $res = $mysqli->query($clas_des);            

                                while($rowC = mysqli_fetch_row($res)) {
                                   echo '<option value="'.$rowC[0].'">'.ucwords(mb_strtolower($rowC[1])).'</option>';
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
                                url: "jsonPQR/gpqr_descripcionJson.php?action=3",
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
                                            document.location='listar_GPQR_DESCRIPCION.php';
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
            <!--informacion adicional-->
                <div class="col-sm-2 col-sm-2 col-sm-offset-8" style="margin-top: -25.5%;">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                            </tr>    
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información Adicional</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a href="GPQR_CLASE_DESCRIPCION.php?desc=1" class="btn btn-primary btnInfo">CLASE DESCRIPCION </a>                                         
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
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



