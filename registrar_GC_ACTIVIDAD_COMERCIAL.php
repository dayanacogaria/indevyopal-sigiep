<?php
#############################################################################################
#                                                                                           #
#Modificado por: Nestor B | 12/10/2017 | se modificó el diseño para hacerlo responsive      #
#                                                                                           #    
#############################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; ?>
<title>Registrar Actividad Comercial</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #cact-error,#descripcion-error,#ai-error,#af-error,#sector-error{
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
      
    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;"> Registrar Actividad Comercial</h2>
    <!--Volver-->
    <a href="listar_GC_ACTIVIDAD_COMERCIAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
    <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarActividadComercialJson.php" >
            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
            
                <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                
                <div class="form-group" style="margin-top: -10px;">
                    <label for="cact" class="control-label col-sm-5" ><strong class="obligado">*</strong>Código: </label>
                    <input  type="text" name="codigo" required id="cact" class="form-control" maxlength="25" title="Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código">
                </div>
                
                <div class="form-group" style="margin-top: -25px;">
                    <label for="descripcion" class="col-sm-5 control-label">Descripción:</label>    
                    <textarea name="descripcion"  placeholder="Descripción" id="descripcion"  class="form-control col-sm-1" rows="3" style="height: 50px" title="Descripción" maxlength="100"></textarea>                                
                </div>                         
 
                <?php
                    $cuentaI = "SELECT * FROM gf_sector ORDER BY nombre ASC";
                    $rsctai = $mysqli->query($cuentaI);
                ?>
 
                <div class="form-group" style="margin-top: -10px;">
                    <label for="sector" class="col-sm-5 control-label">Sector:</label>
                    <select  name="sector" id="sector"  style="height: 30px" class="select2_single form-control" title="Seleccione Sector">
                        <option value="">Sector</option>
                        <?php while($row=mysqli_fetch_array($rsctai)){ ?>
                                    <option value="<?php echo $row['id_unico']?>"><?php echo ucwords(mb_strtolower($row['nombre'])); ?></option>
                       <?php    } ?>
                    </select>
                </div>

                <?php
                    $cuentaI = "SELECT * FROM gc_anno_comercial ORDER BY vigencia ASC ";
                    $rsctai = $mysqli->query($cuentaI);
                ?>
                
                <div class="form-group" style="margin-top: -10px;">
                    <label for="ai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año Inicial:</label>
                    <select name="annoInicial" id="ai" required style="height: 30px" class="select2_single form-control" title="Seleccione Año Inicial">
                        <option value="">Año Inicial</option>
                        <?php while($row=mysqli_fetch_array($rsctai)){ ?>
                                    <option value="<?php echo $row['id_unico']?>"><?php echo $row['vigencia'] ?></option>
                        <?php   } ?>
                    </select>
                </div>

                <?php
                    $cuentaI = "SELECT * FROM gc_anno_comercial ORDER BY vigencia DESC";
                    $rsctai = $mysqli->query($cuentaI);
                ?>
                
                <div class="form-group" style="margin-top: -10px;">
                    <label for="af" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año Final:</label>
                    <select name="annoFinal" id="af" required style="height: 30px" class="select2_single form-control" title="Seleccione Año Final">
                        <option value="">Año Final</option>
                        <?php   while($row=mysqli_fetch_array($rsctai)){ ?>
                                    <option value="<?php echo $row['id_unico']?>"><?php echo $row['vigencia'] ?></option>
                        <?php   } ?>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label for="no" class="col-sm-5 control-label"></label>
                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                </div>


            
        </form>
    </div>
</div>

        <!-- Fin del Contenedor principal -->
        <!--Información adicional -->
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
                            <a href="registrar_GF_SECTOR.php" class="btn btn-primary btnInfo">Sector</a><br>
                             <a href="registrar_GC_ANNO_COMERCIAL.php" class="btn btn-primary btnInfo">Año</a>
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
    <script>
        function reporteExcel(){
            $('form').attr('action', 'informes/generar_INF_LIS_FAC_EXCEL.php');
        }

        function reportePdf(){
            $('form').attr('action', 'informes/generar_INF_LIS_FAC.php');
        }
    </script>
</div>
<?php require_once 'footer.php' ?>
</body>
</html>