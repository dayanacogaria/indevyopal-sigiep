<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#Inclusión de formato | 8/02/2017 | 05:02
######################################################################################################
require_once 'head.php';  
require_once('Conexion/conexion.php');
$anno =$_SESSION['anno'];
$compania = $_SESSION['compania'];
$id_cuentaB = " ";
if (isset($_GET["id_cuentaB"])){ 
    $id_cuentaB= (($_GET["id_cuentaB"]));
    $queryCuentaB = "SELECT   cb.id_unico, 
            cb.banco, 
            t.id_unico, t.razonsocial, 
            t.numeroidentificacion, t.tipoidentificacion,  
            ti.nombre, 
            cb.numerocuenta, 
            cb.descripcion, cb.tipocuenta, 
            tc.nombre, cb.cuenta, 
            c.nombre, c.codi_cuenta, 
            cb.recursofinanciero, rf.nombre, 
            rf.codi, cb.formato, 
            fc.nombre, d.id_unico, d.nombre 
        FROM gf_cuenta_bancaria cb
        LEFT JOIN gf_tercero t ON cb.banco=t.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
        LEFT JOIN gf_tipo_cuenta tc ON cb.tipocuenta = tc.id_unico
        LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico
        LEFT JOIN gf_recurso_financiero rf ON cb.recursofinanciero = rf.id_unico
        LEFT JOIN gf_formato fc ON cb.formato = fc.id_unico 
        LEFT JOIN gf_tipo_destinacion d ON cb.destinacion = d.id_unico 
        WHERE md5(cb.Id_Unico) ='$id_cuentaB'";

}
$resultado = $mysqli->query($queryCuentaB);
$row = mysqli_fetch_row($resultado);
$bancos= "SELECT t.id_unico, t.razonsocial, t.tipoidentificacion, t.numeroidentificacion, t.digitoverficacion,ti.nombre 
      FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt 
      WHERE t.tipoidentificacion = ti.id_unico 
      AND t.id_unico = pt.tercero 
      AND pt.perfil = 9 
      AND t.id_unico != '$row[1]' 
          and t.compania = $compania 
      ORDER BY razonsocial ASC";
$banco =   $mysqli->query($bancos);
//tipo cuenta lleno
$tcuenta = " SELECT id_unico, nombre FROM gf_tipo_cuenta  WHERE id_unico !=$row[9] ORDER BY nombre ASC";
$tipoC = $mysqli->query($tcuenta);

//tipo cuenta vacio
$tcuentav = " SELECT id_unico, nombre FROM gf_tipo_cuenta ORDER BY nombre ASC";
$tipoCv = $mysqli->query($tcuentav);
?>
<title>Modificar Cuenta Bancaria</title>
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
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content"> 
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-7 text-left" style="margin-top: -20px; margin-left: -10px">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Cuenta Bancaria</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -10px" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarCuentaBancariaJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="hidden" name="banco" id="banco" value="<?php  echo $row[1]  ?>" required title="Seleccione el banco">
                        <label for="banco" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Banco:</label>
                        <select name="banco1" id="banco1" class="select2_single form-control" title="Seleccione el banco" required onchange="llenar();">
                            <option value="<?php echo $row[1] ?>"><?php echo ucwords(mb_strtolower($row[3]."(".$row[4].")"));?></option>
                            <?php while($rowB = mysqli_fetch_row($banco)){?>
                            <option value="<?php echo $rowB[0] ?>">
                              <?php echo ucwords((mb_strtolower ($rowB[1]."(".$rowB[3].")")));}?>
                            </option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="numC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Cuenta:</label>
                        <input type="text" name="numC" id="numC" class="form-control" maxlength="15" title="Ingrese número cuenta"  value="<?php echo $row[7]?>" onkeypress="return txtValida(event,'num')"   placeholder="Número Cuenta" required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="descrip" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                        <input type="text" name="descrip" id="descrip" class="form-control" maxlength="500" title="Ingrese la descripción" value="<?php echo ucwords(mb_strtolower($row[8]))?>" onkeypress="return txtValida(event,'car')"   placeholder="Descripción" required="required">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="tipoC" class="col-sm-5 control-label">Tipo Cuenta:</label>
                        <select name="tipoC" id="tipoC" class="form-control" title="Seleccione el tipo cuenta" >
                            <?php if (empty($row[9])) { ?>
                            <option value="">Tipo Cuenta</option>
                            <?php while($rowTCv = mysqli_fetch_assoc($tipoCv)){?>
                            <option value="<?php echo $rowTCv['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowTCv['nombre'])));}?></option>
                            <?php } else { ?>
                            <option value="<?php echo $row[9]?>"><?php echo ucwords(mb_strtolower($row[10]));?></option>
                            <?php while($rowTC = mysqli_fetch_assoc($tipoC)){?>
                            <option value="<?php echo $rowTC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowTC['nombre'])));}?></option>
                            <option value=""></option>
                            <?php } ?>
                            
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="sltFormato" class="col-sm-5 control-label">Formato:</label>
                        <select name="sltFormato" id="sltFormato" class="select2_single form-control" title="Seleccione el formato para cheque" >
                            <?php 
                            if(!empty($row[17])){
                              echo "<option value=".$row[17].">".$row[18]."</option>";
                              $sqlF = "SELECT DISTINCT id_unico,nombre FROM gf_formato WHERE id_unico != $row[17]";
                              $resultF = $mysqli->query($sqlF);
                              while ($filaF =mysqli_fetch_row($resultF)) {
                                echo "<option value=".$filaF[0].">".ucwords(mb_strtolower(($filaF[1])))."</option>";
                              }
                            }else{
                              echo "<option value=''>Formato</option>";
                              $sqlF = "SELECT DISTINCT id_unico,nombre FROM gf_formato";
                              $resultF = $mysqli->query($sqlF);
                              while ($filaF =mysqli_fetch_row($resultF)) {
                                echo "<option value=".$filaF[0].">".ucwords(mb_strtolower(($filaF[1])))."</option>";
                              }
                            }
                             ?>                                                        
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -0px;">
                        <label for="sltRecurso" class="col-sm-5 control-label">Recurso Financiero:</label>
                        <select name="sltRecurso" id="sltRecurso" class="select2_single form-control" title="Seleccione Recurso Financiero" >
                            <?php 
                            if(!empty($row[14])){
                              echo "<option value=".$row[14].">".$row[15]."</option>";
                              echo "<option value=''> - </option>";
                              $sqlF = "SELECT DISTINCT id_unico,nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $anno AND id_unico != $row[14]";
                              $resultF = $mysqli->query($sqlF);
                              while ($filaF =mysqli_fetch_row($resultF)) {
                                echo "<option value=".$filaF[0].">".ucwords(mb_strtolower(($filaF[1])))."</option>";
                              }
                            }else{
                              echo "<option value=''> - </option>";
                              $sqlF = "SELECT DISTINCT id_unico,nombre FROM gf_recurso_financiero WHERE parametrizacionanno = $anno";
                              $resultF = $mysqli->query($sqlF);
                              while ($filaF =mysqli_fetch_row($resultF)) {
                                echo "<option value=".$filaF[0].">".ucwords(mb_strtolower(($filaF[1])))."</option>";
                              }
                            }
                             ?>                                                        
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -0px;">
                        <label for="sltDestinacion" class="col-sm-5 control-label">Destinación:</label>
                        <select name="sltDestinacion" id="sltDestinacion" class="select2_single form-control" title="Seleccione Destinación" >
                            <?php 
                            if(!empty($row[19])){
                              echo "<option value=".$row[19].">".$row[20]."</option>";
                              echo "<option value=''> - </option>";
                              $sqlF = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_destinacion WHERE id_unico != $row[19]";
                              $resultF = $mysqli->query($sqlF);
                              while ($filaF =mysqli_fetch_row($resultF)) {
                                echo "<option value=".$filaF[0].">".ucwords(mb_strtolower(($filaF[1])))."</option>";
                              }
                            }else{
                              echo "<option value=''> - </option>";
                              $sqlF = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_destinacion";
                              $resultF = $mysqli->query($sqlF);
                              while ($filaF =mysqli_fetch_row($resultF)) {
                                echo "<option value=".$filaF[0].">".ucwords(mb_strtolower(($filaF[1])))."</option>";
                              }
                            }
                             ?>                                                        
                        </select> 
                    </div>
                    <div align="center">
                        <button type="submit" class="btn btn-primary sombra" style="margin-left: -47px">Guardar</button>
                    </div>
                   <input type="hidden" name="MM_insert" >
                </form>
            </div>     
        </div>
        <!-- Botones de consulta -->
        <div class="col-sm-7 col-sm-3" style="margin-top:-22px; margin-left: 10px">
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
