<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#Inclusión de campo formato | 9/02/2017 | 05:02 | Jhon Numpaque
######################################################################################################
require_once 'head.php'; 
  require_once('Conexion/conexion.php');
  $anno = $_SESSION['anno'];
  $compania = $_SESSION['compania'];
  //consulta para imprimir los datos 
  $bancos= "SELECT t.id_unico, t.razonsocial, t.tipoidentificacion, t.numeroidentificacion, t.digitoverficacion,ti.nombre 
      FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt 
      WHERE t.tipoidentificacion = ti.id_unico 
      AND t.id_unico = pt.tercero 
      AND pt.perfil = 9 AND t.compania = $compania 
      ORDER BY razonsocial ASC";
  $banco =   $mysqli->query($bancos);

  $tcuenta = " SELECT id_unico, nombre FROM gf_tipo_cuenta ORDER BY nombre ASC";
  $tipoC = $mysqli->query($tcuenta);
  #**** Recurso Financiero **#
  $recurso = " SELECT id_unico, nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $anno  ORDER BY nombre ASC";
  $recurso = $mysqli->query($recurso);
  #** Destinación **#
  $destinacion ="SELECT id_unico, nombre FROM gf_tipo_destinacion  ORDER BY nombre ASC";
  $destinacion = $mysqli->query($destinacion);
?>
<!-- Llamado a la cabecera del formulario -->
<title>Registrar Cuenta Bancaria</title>  
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>

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
label#banco-error, #numC-error, #descrip-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
<body><!-- contenedor principal -->  
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Cuenta Bancaria</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarCuentaBancariaJson.php" target="_parent">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-bottom:28px">
                            <input type="hidden" name="banco" id="banco" required title="Seleccione el banco">
                            <label for="banco" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Banco:</label>
                            <select name="banco1" id="banco1" class="select2_single form-control" title="Seleccione el banco" required onchange="llenar();">
                              <option value="">Banco</option>
                              <?php while($rowB = mysqli_fetch_row($banco))
                              {?>
                              <option value="<?php echo $rowB[0] ?>"> 
                                <?php echo ucwords((mb_strtolower ($rowB[1]."(".$rowB[3].")")));}?>
                              </option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Cuenta:</label>
                            <input type="text" name="numC" id="numC" class="form-control" maxlength="15" title="Ingrese número cuenta" onkeypress="return txtValida(event,'num')"   placeholder="Número Cuenta" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="descrip" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <input type="text" name="descrip" id="descrip" class="form-control" maxlength="500" title="Ingrese la descripción" onkeypress="return txtValida(event,'car')"   placeholder="Descripción" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="tipoC" class="col-sm-5 control-label">Tipo Cuenta:</label>
                            <select name="tipoC" id="tipoC" class="select2_single form-control" title="Seleccione el tipo cuenta" >
                                <option value="">Tipo Cuenta</option>
                                <?php while($rowTC = mysqli_fetch_assoc($tipoC)){?>
                                <option value="<?php echo $rowTC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowTC['nombre'])));}?></option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -4px;">
                            <label for="sltFormato" class="col-sm-5 control-label">Formato:</label>
                            <select name="sltFormato" id="sltFormato" class="select2_single form-control" title="Seleccione el formato para cheque" >
                                <option value="">Formato</option>
                                <?php 
                                $sqlC="SELECT DISTINCT id_unico,nombre FROM gf_formato ORDER BY nombre DESC";
                                $resultC = $mysqli->query($sqlC);                                
                                 ?>
                                <?php while($rowC = mysqli_fetch_row($resultC)){
                                  echo "<option value='".$rowC[0]."'>".ucwords(mb_strtolower(($rowC[1])))."</option>";
                                }
                                  ?>                                
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -4px;">
                            <label for="sltRecurso" class="col-sm-5 control-label">Recurso Financiero:</label>
                            <select name="sltRecurso" id="sltRecurso" class="select2_single form-control" title="Seleccione Recurso Financiero" >
                                <option value="">Recurso Financiero</option>
                                <?php while($rowR = mysqli_fetch_row($recurso)){
                                  echo "<option value='".$rowR[0]."'>".ucwords(mb_strtolower(($rowR[1])))."</option>";
                                }
                                  ?>                                
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -4px;">
                            <label for="sltDestinacion" class="col-sm-5 control-label">Destinación:</label>
                            <select name="sltDestinacion" id="sltDestinacion" class="select2_single form-control" title="Seleccione Destinación" >
                                <option value="">Destinación</option>
                                <?php while($rowD = mysqli_fetch_row($destinacion)){
                                  echo "<option value='".$rowD[0]."'>".ucwords(mb_strtolower(($rowD[1])))."</option>";
                                }
                                  ?>                                
                            </select> 
                        </div>
                        <div align="center">
                            <button type="submit" class="btn btn-primary sombra" style="margin-left: -45px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>         
                </div>    
            </div>
           <!-- Botones de consulta -->
            <div class="col-sm-7 col-sm-3">
                <table class="tablaC table-condensed" style="margin-left: -30px">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#">
                                        MOVIMIENTO CONTABLE
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="registrar_GF_BANCO_JURIDICA.php"><button class="btn btn-primary btnInfo">BANCOS</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas">
                                    <a href="#"> 
                                        CHEQUERAS
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TIPO_CUENTA.php">
                                  <button class="btn btn-primary btnInfo"> 
                                      TIPO CUENTA
                                  </button> 
                                </a><br/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Fin de Contenedor Principal -->
            <?php require_once('footer.php'); ?>
        </div>
    </div>
</body>
</html>
<script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true,
      });
    });
  </script>
  <script>
  function llenar(){
      var banco = document.getElementById('banco1').value;
      document.getElementById('banco').value= banco;
  }
  </script>