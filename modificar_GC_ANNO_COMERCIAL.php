




<?php


require_once('Conexion/conexion.php');
require_once 'head.php'; 


$id=$_GET['id'];
$sql = "SELECT ac.id_unico,
ac.vigencia,
ec.nombre,
ac.estado
FROM gc_anno_comercial ac 
LEFT JOIN gc_estado_anno_comercial ec ON ec.id_unico=ac.estado
WHERE md5(ac.id_unico)='$id'";
$resultado = $mysqli->query($sql);
$row=mysqli_fetch_row($resultado);





?>
<title>Modificar Año Comercial</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #vigencia-error,#estado-error{
        display: block;
        color: #155180;
        font-weight: normal;
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
            },
            rules: {
                sltmes: {
                    required: true
                },
                sltcni: {
                    required: true
                },
                sltAnnio: {
                    required: true
                }
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




<!-- contenedor principal -->
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once ('menu.php'); ?>
   <div class="col-sm-7 text-left" style="margin-top:-10px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Modificar Año Comercial</h2>

      <!--volver-->
      <a href="listar_GC_ANNO_COMERCIAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
      <!---->
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <!-- inicio del formulario --> 
          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarAnnoComercialJson.php" >                 <!-- <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFuenteJson.php">-->
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    
                        <div class="form-group" style="margin-top: -10px;">
                             <label for="vigencia" class="col-sm-5 control-label"><strong class="obligado">*</strong>Vigencia:</label>
                            <input type="text" name="vigencia" id="vigencia" class="form-control" minlength="4" maxlength="4" title="Ingrese la Vigencia" onkeypress="return txtValida(event,'num')" placeholder="Vigencia" required="" value="<?php echo $row[1] ?>">
                       </div>
                      
                        <div class="form-group" style="margin-top: -10px">
                            <?php   
                            //consultas para llenar los campos
                            $idAnnoComercial=$row[3];
                              $clase = "SELECT id_unico, nombre FROM gc_estado_anno_comercial WHERE id_unico!=$idAnnoComercial ORDER BY nombre ASC";
                              $estado =   $mysqli->query($clase);  ?>

                            <label for="estado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <select name="estado" id="estado" class="select2_single form-control" title="Seleccione el Estado" required>

                              <option value="<?php echo $row[3] ?>"><?php echo ucwords(mb_strtolower($row[2]));?></option>

                              <?php while($rowC = mysqli_fetch_assoc($estado)){?>
                              <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));}?></option>;
                            </select> 

                        </div><br>



                        <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                       </div>

            </form>
                <!-- Fin de división y contenedor del formulario -->           
                </div>     
            </div>
            <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                <table class="tablaC table-condensed" >
                    <thead>
                      <tr>
                        <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                    
                        <td>
                            <a href="registrar_GC_ESTADO_ANNO_COMERCIAL.php" class="btn btn-primary btnInfo">Estado</a>
                        </td>
                      </tr>
                      <tr>
                
                    
                      </tr>
                      <tr>
                  
                        <td></td>
                      </tr>
                    </tbody>
                </table>                
            </div>
     
        <!-- Fin del Contenedor principal -->
        <!--Información adicional -->

    </div>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
    <!-- Llamado al pie de pagina -->

</div>
<?php require_once 'footer.php' ?>
</body>
</html>


























