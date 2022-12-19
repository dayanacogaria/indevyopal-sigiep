<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
######################################################################################################
require_once 'head.php';
require_once('Conexion/conexion.php');
?>
<html>
    <head>
        <title>Registrar Mes</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="lib/jquery.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <style>
        label#txtmes-error, #txtnmes-error
        {
            display: block;
            color: #155180;
            font-weight: normal;
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
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Mes</h2>
                    <a href="listar_GF_MES.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Mes</h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form  contenedorForma">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/mesJson.php?action=1" >
                            <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 100%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="font-size: 13px;">
                                <div class="form-group" style="margin-top: 8px;">
                                    <label for="txtmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes:</label>
                                    <input type="text" name="txtmes" id="txtmes" class="form-control" maxlength="100" title="Ingrese el mes" onkeypress="return txtValida(event, 'car')" placeholder="Mes" required="required">
                                </div>  
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="txtnmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong> Número de Mes:</label>
                                    <input type="text"  name="txtnmes" id="txtnmes" class="form-control"  title="Ingrese el número del mes" onkeypress="return txtValida(event, 'num');" placeholder=" Número de Mes" required="required"  >
                                </div> 
                                <?php
                                $es = "SELECT id_unico, nombre FROM gf_estado_mes";
                                $esta = $mysqli->query($es);
                                ?>
                                <div class="form-group" style="margin-top: -5px">
                                    <label class="control-label col-sm-5">
                                        <strong></strong>Estado:
                                    </label>
                                    <select name="sltEstado" class="select2_single form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                                        <option value="">Estado</option>
                                        <?php while ($filaES = mysqli_fetch_row($esta)) { ?>
                                            <option value="<?php echo $filaES[0]; ?>"><?php echo ucwords(mb_strtolower($filaES[1])); ?></option>
                                        <?php }  ?>
                                    </select> 
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                                </div>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
                <div class="col-sm-8 col-sm-2" style="margin-top: 6px" >
                    <table class="tablaC table-condensed text-center" align="center" >
                        <thead>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GF_ESTADO_MES.php">ESTADO</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({

                    allowClear: true
                });

            });
        </script>
        <?php require_once 'footer.php'; ?>
    </body>
</html>

