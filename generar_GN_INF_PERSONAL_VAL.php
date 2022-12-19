<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$con      = new ConexionPDO();

?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltAnno-error, #sltMesf-error,#sltMesi-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
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
<title>Personal Categoria SUI</title>
</head>

<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Informe De Personal Por Categoría SUI</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_nomina/generar_INF_PERSONAL_VAL.php"  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltAnno" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                            <select required="required"  name="sltAnno" id="sltAnno" class="select2_single form-control" title="Seleccione año">
                                <option value="">Seleccione Año</option>
                                <?php
                                 $annio = "SELECT id_unico, anno 
                                         FROM gf_parametrizacion_anno 
                                         WHERE compania = $compania ORDER BY anno DESC ";
                                 $annio = $mysqli->query($annio);
                                 while ($row1 = mysqli_fetch_row($annio)) {
                                     echo "<option value='$row1[1]'>$row1[1]</option>";
                                 } ?>                                    
                            </select>
                        </div>                        
                        <div class="form-group text-center" style="margin-top:20px;">
                            <button type="submit" class="btn sombra btn-primary" title="Generar reporte" style="margin-top: 15px;"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>

                        </div>
                    </div>
                </form>
            </div>     
        </div>
    </div>
</div>
<script src="js/select/select2.full.js"></script>

<script>
 
 $("#sltAnno").select2();
 $("#sltMesf").select2();
 $("#sltMesi").select2();
 
function validarSelect(){ 
       $("#form").attr("action", "informes_nomina/generar_INF_PERSONAL_CONT.php");
}
</script>


    
</body>
</html>
<?php require_once 'footer.php'?>  