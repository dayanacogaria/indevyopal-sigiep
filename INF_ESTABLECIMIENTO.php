<?php
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
?>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
   <title>Generación Informe Establecimiento</title>
    <style>
        label #sltEmpleado-error, #sltPeriodo-error {
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
</head>
    <body>
        <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Generación Informe Establecimientos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                        <div class="form-group">
                            <?php 
                                $cuentaI = "SELECT DISTINCT YEAR(e.fechainscripcion)
                                FROM gc_establecimiento e 
                                ORDER BY cast(YEAR(e.fechainscripcion) as unsigned) DESC";
                                $rsctai = $mysqli->query($cuentaI);?>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="anno" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Año Inscripción:</label>
                                <select name="anno" id="anno"  style="height: auto;" class="select2_single form-control" title="Seleccione Año Inscripción">
                                    <option value="">Año Inscripción</option>
                                    <?php while ($filactai= mysqli_fetch_row($rsctai)) { ?>
                                    <option value="<?php echo $filactai[0];?>"><?php echo ($filactai[0]);?></option>                                
                                    <?php } ?>                                    
                                </select>
                            </div>     

                            <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            </div>
                        </div>
                    </form>
                  </div>
            </div>
      </div>                                    
    </div>
    <?php require_once './footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
    </script>
    <script>
    function reportePdf(){
        var anno = $("#anno").val();
        if(anno !=""){
            $('form').attr('action', 'informesComercio/generar_INF_ESTABLECIMIENTO.php?a='+anno);
        } else {
            $('form').attr('action', 'informesComercio/generar_INF_ESTABLECIMIENTO.php');
        }
    }
    </script>
</body>
</html>