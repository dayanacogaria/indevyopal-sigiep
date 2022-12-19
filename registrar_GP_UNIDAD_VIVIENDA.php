<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  require_once 'head.php'; 

//Predio
$predio = "SELECT codigo_catastral,id_unico FROM gp_predio1 ORDER BY codigo_catastral ASC";
$rsP = $mysqli->query($predio);

//Tipo Unidad vivienda
$tipo_unidad = "SELECT nombre,id_unico FROM gp_tipo_unidad_vivienda ORDER BY nombre ASC";
$rsTV = $mysqli->query($tipo_unidad);

//Tercero
 $ter="SELECT  IF(CONCAT(nombreuno,' ', nombredos, ' ', apellidouno, ' ', apellidodos)='',"
        . "(razonsocial),CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos)) AS 'NOMBRE', "
        . "id_unico, numeroidentificacion FROM gf_tercero ORDER BY NOMBRE ASC";
$tercero = $mysqli->query($ter);

//Uso
$uso = "SELECT nombre,id_unico FROM gp_uso ORDER BY nombre ASC";
$rsU = $mysqli->query($uso);

//Estrato
$estrato = "SELECT nombre,id_unico FROM gp_estrato ORDER BY id_unico ASC";
$rsE = $mysqli->query($estrato);

//Codigo interno
$codI= "SELECT MAX(codigo_interno) FROM gp_unidad_vivienda";
$codIn= $mysqli->query($codI);
$codigI= mysqli_fetch_row($codIn);
if($codigI[0]==NULL){
  $codigoInterno = '1';
  } else {
    $codigoInterno = $codigI[0]+1;
  }

//Tipo productor
$tipoP = "SELECT id_unico, nombre FROM gp_tipo_productor ORDER BY nombre ASC";
$tipoPr = $mysqli->query($tipoP);

//sector
$sec = "SELECT id_unico, nombre FROM gp_sector ORDER BY nombre ASC";
$sector = $mysqli->query($sec);

//seccion
$secc= "SELECT id_unico, nombre FROM gp_seccion ORDER BY nombre ASC";
$seccion = $mysqli->query($secc);

//Manzana
$man = "SELECT id_unico, nombre FROM gp_tipo_manzana ORDER BY nombre ASC";
$manzana = $mysqli->query($man);

//Lado Manzana
$ladoM = "SELECT id_unico, nombre FROM gp_tipo_lado_manzana ORDER BY nombre ASC";
$ladoMan= $mysqli->query($ladoM);

//Sector Hidráulico
$sectH= "SELECT id_unico, nombre FROM gp_tipo_sector_hidraulico ORDER BY nombre ASC";
$sectorH = $mysqli->query($sectH);

//MicroSector
$micS= "SELECT id_unico, nombre FROM gp_tipo_microsector ORDER BY nombre ASC";
$microS = $mysqli->query($micS);
?>

<style type="text/css">
  label {
    width: 180px;
    margin-top: -15px;
  }
  .select2-container {
    margin-top: -11px;
  }
  select2-container {
  margin-top: -11px;
  }
</style>

<title>Registrar Unidad Vivienda</title>
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
label#sltPredio-error, #sltTipoUnidad-error, #tercero-error, #sltUso-error,  #sltEstrato-error,  #codRuta-error, 
#codInterno-error, #tipoProd-error, #sector-error, #seccion-error, #manzana-error, #ladoM-error, 
#sectorH-error, #microS-error, #desha-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
    margin-top:0.5px;
}
</style>
<body>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
  <!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
      <h2 align="center" class="tituloform">Registrar Unidad Vivienda</h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <!-- inicio del formulario --> 
          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarUnidadViviendaJson.php" style="margin-top: -22px; margin-left: 20px;" align="left">
            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <!--Predio -->           
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="sltPredio" class="control-label"><strong style="color:#03C1FB;">*</strong>Predio:</label>
              <select name="sltPredio" id="sltPredio" class="form-control" title="Seleccione Predio" style="height: 35px; width: 200px;" required="required">
                <option value="">Predio</option>
                   <?php while ($filaP = mysqli_fetch_row($rsP)) { ?>
                <option value="<?php echo $filaP[1];?>"><?php echo ucwords(($filaP[0]));?></option><?php } ?>
              </select>
              <!--Tipo Unidad Vivienda  -->  
              <label for="sltTipoUnidad" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Unidad:</label>
              <select name="sltTipoUnidad" id="sltTipoUnidad" class="form-control" title="Seleccione Tipo Unidad" style="height:35px; width: 200px;" required="required">
                <option value="">Tipo Unidad Vivienda</option>
                  <?php while ($filaTV = mysqli_fetch_row($rsTV)) { ?>
                <option value="<?php echo $filaTV[1];?>"><?php echo ucwords(strtolower($filaTV[0]));?></option><?php } ?>
              </select>
            </div>
            <!--Tercero-->          
            <div class="form-group form-inline" style="margin-top: -15px;">
                <input type="hidden" name="tercero" id="tercero" required="required" title="Seleccione tercero">
              <label for="tercero" class="control-label"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
              <div class="form-group form-inline" style="margin-left: 1px; margin-top: -11px">
                  <select name="tercero1" id="tercero1"  required="required" class="select2_single form-control" tabindex="-1" style="display:inline-block;  text-align-last:left; width: 200px; margin-top: -11px" title="Seleccione tercero" onchange="llenar();">
                <option value="">Tercero</option>
                <?php while($row = mysqli_fetch_row($tercero)){ ?>
                <option value="<?php echo $row[1] ?>"><?php echo ucwords((strtolower($row[0].' ('.$row[2].')'))); } ?></option>;
              </select>
              </div>
               <!--uso-->  
              <label for="sltUso" class="control-label" style="margin-left: 15px;"><strong style="color:#03C1FB; ">*</strong>Uso:</label>
              <select name="sltUso" id="sltUso" class="form-control" title="Seleccione Uso" style="height: 35px; width: 200px;" required="required" >
                <option value="">Uso</option>
                <?php while ($filaU = mysqli_fetch_row($rsU)) { ?>
                <option value="<?php echo $filaU[1];?>"><?php echo ucwords(strtolower($filaU[0]));?></option><?php } ?>
              </select>
            </div>
            <!--estrato-->          
            <div class="form-group form-inline" style="margin-top: -15px;" >
              <label for="sltEstrato" class="control-label"><strong style="color:#03C1FB;">*</strong>Estrato:</label>
              <select name="sltEstrato" id="sltEstrato" class="form-control " title="Seleccione Estrato" style="height: 35px; width: 200px; " required="required">
                <option value="">Estrato</option>
                <?php while ($filaE = mysqli_fetch_row($rsE)) { ?>
                <option value="<?php echo $filaE[1];?>"><?php echo ucwords(strtolower($filaE[0]));?></option><?php } ?>
              </select>
              <!--Numero de familias-->
              <label for="nro_familias" class="control-label"><strong class="obligado"></strong>Número de Familias:</label>
              <input type="text" name="nro_familias" id="nro_familias" class="form-control" maxlength="2" title="Número de familias" onkeypress="return txtValida(event,'num')" placeholder="Número de familias" style="display: inline; width: 200px; height: 35px">
            </div>
            <!--Numero de personas-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="nro_personas" class="control-label"><strong class="obligado"></strong>Número de Personas:</label>
              <input type="text" name="nro_personas" id="nro_personas" class="form-inline" maxlength="2" title="Número de personas" onkeypress="return txtValida(event,'num')" placeholder="Número de personas" style="display: inline;  width: 200px; height: 35px">
              <!--Codigo ruta-->
              <label for="codRuta" class="control-label"><strong class="obligado">*</strong>Código ruta:</label>
              <input type="text" name="codRuta" id="codRuta" class="form-inline" maxlength="100" title="Código de ruta" onkeypress="return txtValida(event,'sin_espcio')" placeholder="Código Ruta" required style="display: inline;  width: 200px; height: 35px" required="required">
            </div>
            
            <div class="form-group form-inline" style="margin-top: -15px;">
            <!--Codigo interno-->
              <label for="codInterno" class="control-label"><strong class="obligado">*</strong>Código interno:</label>
              <input type="text" name="codInterno" id="codInterno" class="form-control" maxlength="100" title="Código interno"  readonly="true" placeholder="Código Interno"  style="display: inline;  width: 200px; height: 35px" required="required">
            <!--Tipo productor-->
              <label for="tipoProd" class="control-label"><strong class="obligado">*</strong>Tipo Productor:</label>
              <select name="tipoProd" id="tipoProd" class="form-control " title="Seleccione Tipo Productor" style="height: 35px; width: 200px; " required="required">
                <option value="">Tipo Productor</option>
                <?php while ($rowProd = mysqli_fetch_row($tipoPr)) { ?>
                <option value="<?php echo $rowProd[0];?>"><?php echo ucwords(strtolower($rowProd[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Sector-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="sector" class="control-label"><strong class="obligado">*</strong>Sector:</label>
              <select name="sector" id="sector" class="form-control " title="Seleccione Sector" style="height: 35px; width: 200px; " required="required">
                <option value="">Sector</option>
                <?php while ($rowSect = mysqli_fetch_row($sector)) { ?>
                <option value="<?php echo $rowSect[0];?>"><?php echo ucwords(strtolower($rowSect[1]));?></option><?php } ?>
              </select>
            <!--Seccion-->
              <label for="seccion" class="control-label"><strong class="obligado">*</strong>Sección:</label>
              <select name="seccion" id="seccion" class="form-control " title="Seleccione Sector" style="height: 35px; width: 200px; " required="required">
                <option value="">Sección</option>
                <?php while ($rowSecc= mysqli_fetch_row($seccion)) { ?>
                <option value="<?php echo $rowSecc[0];?>"><?php echo ucwords(strtolower($rowSecc[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Manzana-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="manzana" class="control-label"><strong class="obligado">*</strong>Manzana:</label>
              <select name="manzana" id="manzana" class="form-control " title="Seleccione Manzana" style="height: 35px; width: 200px; " required="required">
                <option value="">Manzana</option>
                <?php while ($rowMan = mysqli_fetch_row($manzana)) { ?>
                <option value="<?php echo $rowMan[0];?>"><?php echo ucwords(strtolower($rowMan[1]));?></option><?php } ?>
              </select>
            <!--Lado manzana-->
              <label for="ladoM" class="control-label"><strong class="obligado">*</strong>Lado Manzana:</label>
              <select name="ladoM" id="ladoM" class="form-control " title="Seleccione Lado Manzana" style="height: 35px; width: 200px; " required="required">
                <option value="">Lado Manzana</option>
                <?php while ($rowladoM = mysqli_fetch_row($ladoMan)) { ?>
                <option value="<?php echo $rowladoM[0];?>"><?php echo ucwords(strtolower($rowladoM[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Sector hidraulico-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="sectorH" class="control-label"><strong class="obligado">*</strong>Sector Hidráulico:</label>
              <select name="sectorH" id="sectorH" class="form-control " title="Seleccione Sector Hidráulico" style="height: 35px; width: 200px; " required="required">
                <option value="">Sector Hidráulico</option>
                <?php while ($rowSectH = mysqli_fetch_row($sectorH)) { ?>
                <option value="<?php echo $rowSectH[0];?>"><?php echo ucwords(strtolower($rowSectH[1]));?></option><?php } ?>
              </select>
            <!--Micro sector-->
              <label for="microS" class="control-label"><strong class="obligado">*</strong>MicroSector:</label>
              <select name="microS" id="microS" class="form-control " title="Seleccione Sector" style="height: 35px; width: 200px; " required="required">
                <option value="">MicroSector</option>
                <?php while ($rowmicroS = mysqli_fetch_row($microS)) { ?>
                <option value="<?php echo $rowmicroS[0];?>"><?php echo ucwords(strtolower($rowmicroS[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Deshabilitado-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="desha" class="control-label"><strong class="obligado"></strong>Deshabilitado:</label>
              <input type="radio" name="desha" id="desha" value="1">Sí
              <input type="radio" name="desha" id="desha" value="2">No
              <a onclick="borrarRadio()"><i title="Borrar" class="glyphicon glyphicon-remove"></i></a>
            </div>

            <!--Boton-->
            <div class="form-group" style="margin-left:356px">
              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
          </form>
        </div>     
      </div>
    <script type="text/javascript">
  function borrarRadio(){
    document.getElementsByName("desha")[0].checked = false;
    document.getElementsByName("desha")[1].checked = false;
  }
</script>

    <!--Información adicional -->
    <div class="col-sm-2 col-sm-2"  style="margin-top: -22px">
      <table class="tablaC table-condensed" style="margin-left: -3px; ">
        <thead>
          <th><h2 class="titulo" align="center" style=" font-size:17px; height: 35px;">Adicional</h2></th>
        </thead>
        <tbody>
          <tr>
            <td><button class="btn btnInfo btn-primary" disabled="true">Servicio</button><br/></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  </div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php' ?>
</body>
<script src="js/select/select2.full.js"></script>
<!-- select2 -->
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
  <script type="text/javascript">
 valor= <?php echo $codigoInterno;?>;
 $("#codInterno").val(valor);
 
</script>
</html>
<script>
  function llenar(){
      var tercero = document.getElementById('tercero1').value;
      document.getElementById('tercero').value= tercero;
  }
  </script>