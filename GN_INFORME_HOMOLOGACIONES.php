<?php 
  require_once('Conexion/conexion.php');
  require_once 'head_listar.php';

  $queryInforme = ""; 
  $resultado = "";
  $num_col = 0;
  $id = 0;
  $tipoInf = 0;

  if(!empty($_SESSION['consulta_gi'])) {
    $miarray = $_SESSION['columnas_gi'];

    $array_para_recibir_via_url = stripslashes($miarray);
    $columnas_gi = unserialize($array_para_recibir_via_url);
    $num_col = count($columnas_gi);

    $queryInforme = $_SESSION['consulta_gi'];
    $resultado = $mysqli->query($queryInforme);

    $id = $_SESSION['id_gi'];
    $tipoInf = $_SESSION['tipoInf_gi'];
  }
 
$sqlInforme = "SELECT id, nombre 
  FROM gn_tipo_informe  
  ORDER BY nombre ASC";
$informe = $mysqli->query($sqlInforme);
?>
	<title>Informe Homologaciones</title>
 
</head>
<body>
  <div class="container-fluid text-center">
    <div class="row content">   
      <?php require_once 'menu.php';?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 0px; margin-right: 4px; margin-left: 4px;width: 100%">Configuración de Informe</h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form col-sm-12">
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#">
            <p align="center" style="margin-bottom: 0px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <div class="form-group" style="margin-top: 5px">
                <label for="sltrubi" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Informe:</label>
                <select name="tipoInf" id="tipoInf" class="form-control" title="Tipo Informe" style="width: 250px;" required>
                  <option value="">Tipo Informe</option>
                  <?php 
                    while($row = mysqli_fetch_row($informe)) {
                      echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).'</option>';
                    }
                  ?>
                </select>  
            </div>
             <div class="form-group" style="margin-top: 5px">
                <label for="sltrubi" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Informe:</label>
                <select name="nombre" id="nombre" class="form-control col-sm-1 select2 cursor" title="Informe" style="width: 250px;" required>
                  <option value="">Informe</option>
                </select>                
              </div>           
            <div class="form-group text-center" style="margin-top:20px;">
                           
                            <div class="col-sm-1" style="margin-top:-34px;margin-left:670px">
                                <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>
        <script type="text/javascript">
          $("#tipoInf").change(function() {
            $("#consulta").val("");
            var opcion = '<option value="">Informe</option>';
            if($("#tipoInf").val() == "") {
              $("#nombre").html(opcion).fadeIn();
            } else {
              var tipoInf = $("#tipoInf").val();
              var form_data = { estruc: 5, tipoInf: tipoInf };  
              $.ajax({
                type: "POST",
                url: "estructura_gestor_informes.php",
                data: form_data,
                success: function(response) {
                  if(response != "") {
                    opcion += response;
                    $("#nombre").html(opcion).fadeIn().focus();
                  } else {
                    opcion = '<option value="">No hay Informe</option>';
                    $("#nombre").html(opcion).fadeIn();
                  }  
                }//Fin succes.
              }); //Fin ajax.
            }
          });
      
       
        </script>
        
        <script>
function reporteExcel(){
    
            $('form').attr('action', 'informes/generar_INF_GN_HOMOLOGACIONES.php');
       
    
}

</script>
      </div>
    </div>
  </div>
</div>


<?php  require_once 'footer.php';?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript" src="js/select2.js"></script>
<script src="js/bootstrap.min.js"></script>


</body>
</html>