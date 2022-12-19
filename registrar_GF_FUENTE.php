<?php 
###########################################################################################################
#                           MODIFICACIONES
#05/07/2017 |ERICA G. | PARAMETRIZACION                            
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas 
###########################################################################################################
require_once 'head.php';
require_once('Conexion/conexion.php');
$param=$_SESSION['anno'];
$tipo = "SELECT id_unico, nombre FROM gf_tipo_fuente ORDER BY nombre ASC";
$tipoF =  $mysqli->query( $tipo);
$recurso = "SELECT id_unico, nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $param ORDER BY nombre ASC";
$recursoF = $mysqli->query( $recurso);
$predecesor = "SELECT id_unico, nombre FROM gf_fuente WHERE parametrizacionanno = $param ORDER BY nombre ASC";
$prede = $mysqli->query($predecesor);

?>

<link href="css/select/select2.min.css" rel="stylesheet">
<title>Registrar Fuente</title>
</head>
<body>
    <!-- contenedor principal -->  
    <div class="container-fluid text-center">
        <div class="row content">
            <!-- Llamado al menu del formulario -->    
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-top:-0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: 4px;">Registrar Fuente</h2>
                <a href="listar_GF_FUENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Fuente</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <!-- inicio del formulario --> 
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFuenteJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="mov" class="col-sm-5 control-label" style="margin-top:-5px"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                            <input type="radio" name="mov" id="mov"  value="1" checked>SI
                            <input type="radio" name="mov" id="mov" value="2" checked>NO
                        </div>
                        <div class="form-group" style="margin-top:20px">
                            <label for="prede" class="col-sm-5 control-label">Predecesor:</label>
                            <select name="prede" id="prede" class="select2_single form-control" title="Seleccione predecesor">
                                <option value="">Predecesor</option>
                                <?php while($rowP = mysqli_fetch_assoc($prede)){?>
                                <option value="<?php echo $rowP['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowP['nombre'])));}?></option>;
                            </select> 
                        </div>
                        <div class="form-group">
                            <label for="tipoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Fuente:</label>
                            <select name="tipoF" id="tipoF" class="select2_single form-control" title="Seleccione Tipo Fuente" required>
                                <option value="">Tipo Fuente</option>
                                <?php while($rowF = mysqli_fetch_assoc($tipoF)){?>
                                <option value="<?php echo $rowF['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowF['nombre'])));}?></option>;
                            </select> 
                        </div>
                        <div class="form-group">
                            <label for="recurso" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Recurso Financiero:</label>
                            <select name="recurso" id="recurso" class=" select2_single form-control" title="Seleccione recurso financiero" required>
                                <option value="">Recurso Financiero</option>
                                <?php while($rowR = mysqli_fetch_assoc($recursoF)){?>
                                <option value="<?php echo $rowR['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowR['nombre'])));}?></option>;
                            </select> 
                        </div>
                        <div class="form-group" >
                                <label class="control-label col-sm-5">
                                    Equivalente:
                                </label>
                                <input class="form-control" placeholder="Equivalente" type="text" name="equivalente" id="equivalente" title="Ingrese el código equivalente" onkeypress="return txtValida(event, 'num')">
                        </div>
                        <div align="center">
                            <button type="submit" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: -10px; margin-bottom: 10px; margin-left: -50px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                <!-- Fin de división y contenedor del formulario -->           
                </div>     
            </div>
            <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                <table class="tablaC table-condensed" >
                    <thead>
                      <tr>
                        <th><h2 class="titulo" align="center">Consultas</h2></th>
                        <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td align="center">
                          <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#">MOVIMIENTO <br/>PRESUPUESTAL</a></div>
                        </td>
                        <td>
                            <a href="registrar_GF_TIPO_FUENTE.php" class="btn btn-primary btnInfo">TIPO FUENTE</a>
                        </td>
                      </tr>
                      <tr>
                        <td align="center">
                          <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> <br/>RESUMEN</a></div>
                        </td>
                        <td>
                            <a href="registrar_GF_RECURSO_FINANCIERO.php" class="btn btn-primary btnInfo">RECURSO FINANCIERO</a>
                        </td>
                      </tr>
                      <tr>
                        <td align="center">
                          <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> <br/>GRAFICOS</a></div>
                        </td>
                        <td></td>
                      </tr>
                    </tbody>
                </table>                
            </div>
        </div>
        <!-- Fin del Contenedor principal -->
    </div>
    <!-- Llamado al pie de pagina -->
    <?php require_once 'footer.php' ?>  
 
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<!-- select2 -->
 

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
</body>
</html>

