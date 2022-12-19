<?php  

require_once('Conexion/conexion.php');
require_once('head_listar.php');
$anno = $_SESSION['anno'];?>
<!--Titulo de la página-->
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #file-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
}
label #sltPeriodo-error,#sltEmpleadoI-error,#sltEmpleadoF-error{
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
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

   <style>
    .form-control {font-size: 12px;}
    
</style>

<title>CONSOLIDADO BIOMETRICO</title>
</head>
<body>

 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Consolidado Biométrico</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target='_blank'>
          <?php
             $per = "SELECT  id_unico, codigointerno FROM gn_periodo
                     WHERE   id_unico != 1 
                     AND tipoprocesonomina = 1 
                     AND   parametrizacionanno = '$anno'";
             $periodo = $mysqli->query($per);
          ?>
          <div class="form-group" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px;">
                            <label for="sltPeriodo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                            <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="">Periodo</option>
                                    <?php
                                    while($rowE = mysqli_fetch_row($periodo)){
                                        echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                    }
                                    ?>
                            </select>
            </div>

            <?php  
             $emp = "SELECT                         
                            e.id_unico,
                                e.tercero,
                            t.id_unico,
                            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE e.id_unico!=2
                    GROUP BY e.id_unico ASC";
            $empleadoI = $mysqli->query($emp);
            ?>

            <div class="form-group" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px;">
            <label for="sltEmpleadoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Empleado Inicial:</label>
                            <select name="sltEmpleadoI" id="sltEmpleadoI" title="Seleccione Empleado Inicial" style="height: 30px;" class="select2_single form-control col-sm-1" required>
                                <option value="">Empleado Inicial</option>
                                <?php 
                                    while($rowE = mysqli_fetch_row($empleadoI))
                                    {
                                        echo "<option value=".($rowE[0]).">".$rowE[3]."</option>";
                                    }
                                ?>                                                          
                            </select>
            </div>
            <?php  
             $emp = "SELECT                         
                            e.id_unico,
                            e.tercero,
                            t.id_unico,
                            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE e.id_unico!=2
                    GROUP BY e.id_unico DESC";
            $empleadoI = $mysqli->query($emp);
            ?>

            <div class="form-group" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px;">
            <label for="sltEmpleadoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Empleado Final:</label>
                            <select name="sltEmpleadoF" id="sltEmpleadoF" title="Seleccione Empleado Final" style="height: 30px;" class="select2_single form-control col-sm-1" required>
                                <option value="">Empleado Final</option>
                                <?php 
                                    while($rowE = mysqli_fetch_row($empleadoI))
                                    {
                                        echo "<option value=".($rowE[0]).">".$rowE[3]."</option>";
                                    }
                                ?>                                                          
                            </select>
            </div>

            <div class="form-group text-center" style="margin-top:20px;">
                            <div class="col-sm-1" style="margin-top:0px;margin-left:620px">
                                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            </div>
                            <div class="col-sm-1" style="margin-top:-34px;margin-left:670px">
                                <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
          
          </form>
        </div>      
    </div>
  </div>
</div>
 <script src="js/select/select2.full.js"></script>
 <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>  
 
<script>
function reportePdf(){
   $('form').attr('action', 'informes_nomina/generar_INF_CONSOLIDADO_BIOMETRICO.php');
    
}
</script>
<script>
function reporteExcel(){
            $('form').attr('action', 'informes_nomina/generar_INF_CONSOLIDADO_BIOMETRICO_EXCEL.php');
    
}
</script>

<script>
$("#sltPeriodo").select2();
$("#sltEmpleadoI").select2();
$("#sltEmpleadoF").select2();
</script>

<?php require_once 'footer.php';?>
</body>
</html>

