<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';  
$id = $_GET["id"];
$queryParam = "SELECT m.id_unico, 
                      m.mes, 
                      m.numero, 
                      em.id_unico,
                      em.nombre
  FROM gf_mes m 
  LEFT JOIN gf_estado_mes em  ON m.estadomes = em.id_unico
  WHERE md5(m.id_unico) = '$id'";
$resultado = $mysqli->query($queryParam);
$row = mysqli_fetch_row($resultado); 

?>
<html>
    <head>
        <title>Modificar Mes</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
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
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Mes</h2>
                    <a href="listar_GF_MES.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Mes: <?php echo $row[1] ?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/mesJson.php?action=2" >
                            <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 100%">Los campos marcados con <strong>*</strong> son obligatorios.</p>                                                            
                            <div class="form-group" style="font-size: 13px;">
                                <input type="hidden" value="<?php echo $row[0]; ?>" id="id" name="id">
                                <div class="form-group" style="margin-top: 8px;">
                                    <label for="txtmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes:</label>
                                    <input type="text" name="txtmes" id="txtmes" class="form-control" maxlength="100" required="required" title="Ingrese el mes" onkeypress="return txtValida(event,'car')" value="<?php echo ucwords(mb_strtolower($row[1])) ?>">
                                </div>                                    

                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="txtnmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong> Número de Mes:</label>
                                    <input type="text" name="txtnmes" id="txtnmes" class="form-control" required="required" title="Ingrese el número del mes" onkeypress="return txtValida(event,'num')" value="<?php echo $row[2] ?>" >
                                </div> 
                                <div class="form-group" style="margin-top: -5px">
                                    <label class="control-label col-sm-5">
                                        <strong class="obligado"></strong>Estado:
                                    </label>
                                    <select name="sltEstado" class="select2_single form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                                        <?php if (empty($row[3])) {
                                            $sl = "SELECT id_unico, nombre FROM gf_estado_mes";
                                            echo '<option value=""> - </option>';
                                        } else {
                                            $sl ="SELECT id_unico, nombre FROM gf_estado_mes WHERE id_unico !=".$row[3];
                                            echo '<option value ="'.$row[3].'">'.$row[4].'</option>';
                                        }
                                        $query = $mysqli->query($sl);
                                        while ($row1 = mysqli_fetch_row($query)) {
                                            echo '<option value ="'.$row1[0].'">'.$row1[1].'</option>';
                                        }
                                        ?>
                                    </select> 
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button type="submit"  class="btn btn-primary sombra" style=" margin-top: 8px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                                </div>
                            </div>  
                        </form>
                    </div>
                </div>
                <div class="col-sm-8 col-sm-2" style="margin-top: -3px;">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                </th>
                            </tr>
                            </tr>
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
        <?php require_once 'footer.php'; ?>
        <script src="js/select/select2.full.js"></script>
        <script>            
            $(".select2_single").select2({        
                allowClear: true
            });                
        </script>
    </body>
</html>
