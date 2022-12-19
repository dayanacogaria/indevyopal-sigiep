<?php


require_once('Conexion/conexion.php');
require_once 'head.php'; 

$id=$_GET['id'];

$sql="SELECT td.id_unico,td.descripcion,cd.nombre,td.clase_doc 
FROM gc_tipo_documento_adjunto td
LEFT JOIN gc_clase_documento cd ON cd.id_unico=td.clase_doc
WHERE md5(td.id_unico)='$id';
";

$resultado = $mysqli->query($sql);

$row=mysqli_fetch_row($resultado);


?>
<title>Modificar Tipo Documento Adjunto</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltctai-error,#sltctaiT-error,#sltctaiR-error,#observaciones-error{
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Modificar Tipo Documento Adjunto</h2>

      <!--volver-->
      <a href="listar_GC_TIPO_DOCUMENTO_ADJUNTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
      <!---->
          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <!-- inicio del formulario --> 
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarTipoDocumentoAdjuntoJson.php" >                 <!-- <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFuenteJson.php">-->
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

                   <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              
             

                  <div class="form-group">
                                  
                                  <?php

                                  $idClaseDocumento=$row[3];
                                  $cuentaI = "SELECT *
                                  from gc_clase_documento
                                  WHERE id_unico!=$idClaseDocumento
                                   ORDER BY nombre ASC" ;
                                  $rsctai = $mysqli->query($cuentaI);
                                  ?>
                              

                                <div class="form-group" style="margin-left: 223px;">
                                          <label for="observaciones" class="col-sm-2 control-label" style="width: 102px;"><strong style="color:#03C1FB;">*</strong> Descripción:</label>
                                         <textarea title="Ingrese Descripción" required name="descripcion" placeholder="Descripción" id="observaciones"  class="form-control col-sm-1" rows="3" style="margin-top: 0px; width: 52%; height: 50px;"><?php echo ucwords(mb_strtolower($row[1] )) ?></textarea> 
                                  </div>
                              


                                  <div class="form-group" style="margin-top: -10px">
                                      
                                      <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Documento</label>
                                      <select name="claseDocumento" id="sltctai" required="true" style="height: auto" class="select2_single form-control"  title="Seleccione Clase Documento">
                                              <option value="<?php echo $row[3]?>"><?php echo $row[2] ?></option>

                                          <?php while($rowcd=mysqli_fetch_row($rsctai)){ ?> 
                                              <option value="<?php echo $rowcd[0]?>"><?php echo $rowcd[1] ?></option>
                                          <?php } ?>
                                      </select><br><br><br>

                                  </div>
            

                                  <div class="form-group" style="margin-top: 10px;">
                                        <label for="no" class="col-sm-5 control-label"></label>
                                         <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                                 </div>



                        
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
                    <a href="registrar_GC_CLASE_DOCUMENTO.php" class="btn btn-primary btnInfo">Clase Documento</a>
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