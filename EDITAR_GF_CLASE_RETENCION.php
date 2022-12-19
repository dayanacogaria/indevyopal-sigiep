<?php
###################################################################################################
#**************************************** Modificaciones ****************************************##
###################################################################################################
#31/01/2018 | Erica G. |Campo Clase Descuento
###################################################################################################
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
###################################################################################################
#27/09/2022 | Elkin O. |Campo Base Ingresos
###################################################################################################
require_once 'head.php';
require_once 'Conexion/conexion.php';
$id = "";
if (isset($_GET['ide'])) {
    $id = $_GET['ide'];
}
$sql = "SELECT Id_Unico,Nombre, clase_sia,base_ingresos FROM gf_clase_retencion WHERE md5(Id_Unico) = '$id'";
$rs = $mysqli->query($sql);
$row = mysqli_fetch_row($rs);

?>
<html>
    <head>
        <title>Modificar Clase Retención</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!--######VALIDACIONES#####-->
        <style>
            label #nombre-error, #sia-error {
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
        <style>
            .form-control {font-size: 12px;}
        </style>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"">Modificar Clase Retención</h2>
                    <a href="LISTAR_GF_CLASE_RETENCION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo 'Clase:'.ucwords(mb_strtolower($row[1])); ?> </h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/modificarClaseRetencionJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son oligatorios.
                            </p>
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label">
                                    <strong style="color:#03C1FB;">*</strong>Nombre:
                                </label>
                                <input type="text" name="nombre" maxlength="100" id="nombre" class="form-control" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" value="<?php echo ucwords(mb_strtolower($row[1])); ?>" placeholder="Nombre">
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sia" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Clase Descuento</label>
                                <select name="sia" id="sia" class="form-control select2_single" title="Seleccione Clase Descuento" required="required">
                                    <?php if(($row[2])==1) {
                                        echo '<option value="1">Descuento Retenciones</option>
                                              <option value="2">Otros Descuentos</option>';
                                    } else {
                                        echo '<option value="2">Otros Descuentos</option>
                                            <option value="1">Descuento Retenciones</option>  ';
                                    }?>
                                    
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sia" class="control-label col-sm-5"><strong style="color:#03C1FB;"></strong>Base Ingresos</label>
                                <?php 
                                    if($row[3]==1){ ?>
                                    <label for="optB" class="radio-inline"><input type="radio" name="optB" id="optB"  value="1" checked>Sí</label>
                                    <label for="optB" class="radio-inline"><input type="radio" name="optB" id="optB" value="2" >No</label>
                                <?php } else { ?>
                                    <label for="optB" class="radio-inline"><input type="radio" name="optB" id="optB"  value="1" >Sí</label>
                                    <label for="optB" class="radio-inline"><input type="radio" name="optB" id="optB" value="2" checked>No</label>
                                <?php } ?>
                            </div>
                            <br>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function() {
              $(".select2_single").select2({
                allowClear: true
              });
            });
        </script>
    <?php require_once 'footer.php'; ?>
    </body>
</html>