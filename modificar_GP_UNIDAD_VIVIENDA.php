<?php 
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
require_once 'head.php'; 

$id = (($_GET["id"]));
$_SESSION['url'] = 'modificar_GP_UNIDAD_VIVIENDA.php?id='.$id;
//Query o sql de consulta 
  $queryUdV = "SELECT IF(CONCAT(t.nombreuno,' ', t.nombredos, ' ', t.apellidouno, ' ', t.apellidodos)='',(t.razonsocial),CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)) AS 'NOMBRE', t.id_unico, t.numeroidentificacion,uv.id_unico, p.id_unico, p.codigo_catastral,tuv.id_unico, tuv.nombre, uv.uso, u.id_unico, u.nombre, uv.estrato,  e.id_unico, e.nombre, uv.numero_familias, uv.numero_personas, uv.codigo_ruta, uv.codigo_interno, uv.tipo_productor, tp.id_unico, tp.nombre, ts.id_unico, ts.nombre, tsc.id_unico, tsc.nombre,tm.id_unico, tm.nombre, tlm.id_unico, tlm.nombre, tsh.id_unico, tsh.nombre, tmm.id_unico,  tmm.nombre, uv.deshabilitado
FROM gp_unidad_vivienda uv 
LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico
LEFT JOIN gp_tipo_unidad_vivienda tuv on uv.tipo_unidad = tuv.id_unico 
LEFT JOIN gp_uso u on uv.uso = u.id_unico   
LEFT JOIN gp_estrato e on uv.estrato = e.id_unico
LEFT JOIN gp_tipo_productor tp ON uv.tipo_productor = tp.id_unico
LEFT JOIN gp_sector ts ON uv.sector = ts.id_unico
LEFT JOIN gp_seccion tsc ON uv.seccion = tsc.id_unico
LEFT JOIN gp_tipo_manzana tm ON uv.manzana = tm.id_unico
LEFT JOIN gp_tipo_lado_manzana tlm ON uv.lado_manzana = tlm.id_unico
LEFT JOIN gp_tipo_sector_hidraulico tsh ON uv.sector_hidraulico = tsh.id_unico
LEFT JOIN gp_tipo_microsector tmm ON uv.microsector = tmm.id_unico
WHERE md5(uv.id_unico) = '$id'";
/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryUdV);
$row = mysqli_fetch_row($resultado);
//Predio
$predio = "SELECT codigo_catastral,id_unico FROM gp_predio1 WHERE id_unico != $row[4] ORDER BY codigo_catastral ASC";
$rsP = $mysqli->query($predio);

//Tipo Unidad vivienda
$tipo_unidad = "SELECT nombre,id_unico FROM gp_tipo_unidad_vivienda WHERE id_unico !=$row[6] ORDER BY nombre ASC";
$rsTV = $mysqli->query($tipo_unidad);

//Tercero
 $ter="SELECT  IF(CONCAT(nombreuno,' ', nombredos, ' ', apellidouno, ' ', apellidodos)='',"
        . "(razonsocial),CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos)) AS 'NOMBRE', "
        . "id_unico, numeroidentificacion FROM gf_tercero WHERE id_unico !=$row[1] ORDER BY NOMBRE ASC";
$tercero = $mysqli->query($ter);

//Uso
$uso = "SELECT nombre,id_unico FROM gp_uso WHERE id_unico !=$row[9] ORDER BY nombre ASC";
$rsU = $mysqli->query($uso);

//Estrato
$estrato = "SELECT nombre,id_unico FROM gp_estrato WHERE id_unico !=$row[12] ORDER BY id_unico ASC";
$rsE = $mysqli->query($estrato);


//Tipo productor
$tipoP = "SELECT id_unico, nombre FROM gp_tipo_productor WHERE id_unico !=$row[19] ORDER BY nombre ASC";
$tipoPr = $mysqli->query($tipoP);

//sector
$sec = "SELECT id_unico, nombre FROM gp_sector WHERE id_unico !=$row[21] ORDER BY nombre ASC";
$sector = $mysqli->query($sec);

//seccion
$secc= "SELECT id_unico, nombre FROM gp_seccion WHERE id_unico !=$row[23] ORDER BY nombre ASC";
$seccion = $mysqli->query($secc);

//Manzana
$man = "SELECT id_unico, nombre FROM gp_tipo_manzana WHERE id_unico !=$row[25] ORDER BY nombre ASC";
$manzana = $mysqli->query($man);

//Lado Manzana
$ladoM = "SELECT id_unico, nombre FROM gp_tipo_lado_manzana WHERE id_unico !=$row[27] ORDER BY nombre ASC";
$ladoMan= $mysqli->query($ladoM);

//Sector Hidráulico
$sectH= "SELECT id_unico, nombre FROM gp_tipo_sector_hidraulico WHERE id_unico !=$row[29] ORDER BY nombre ASC";
$sectorH = $mysqli->query($sectH);

//MicroSector
$micS= "SELECT id_unico, nombre FROM gp_tipo_microsector  WHERE id_unico !=$row[31]  ORDER BY nombre ASC";
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

<title>Modificar Unidad Vivienda</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>
 
    <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Modificar Unidad Vivienda</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
<!-- Inicio del formulario --> 
<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarUnidadViviendaJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
          <input type="hidden" name="id" id="id" value="<?php echo $row[3];?>">
          <!--Predio -->           
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="sltPredio" class="control-label"><strong style="color:#03C1FB;">*</strong>Predio:</label>
              <select name="sltPredio" id="sltPredio" class="form-control" title="Seleccione Predio" style="height: 35px; width: 200px;" required="required">
                <option value="<?php echo $row[4];?>"><?php echo strtoupper($row[5])?></option>
                   <?php while ($filaP = mysqli_fetch_row($rsP)) { ?>
                <option value="<?php echo $filaP[1];?>"><?php echo ucwords(($filaP[0]));?></option><?php } ?>
              </select>
              <!--Tipo Unidad Vivienda  -->  
              <label for="sltTipoUnidad" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Unidad:</label>
              <select name="sltTipoUnidad" id="sltTipoUnidad" class="form-control" title="Seleccione Tipo Unidad" style="height:35px; width: 200px;" required="required">
                <option value="<?php echo $row[6]?>"><?php echo ucwords(strtolower($row[7]))?></option>
                  <?php while ($filaTV = mysqli_fetch_row($rsTV)) { ?>
                <option value="<?php echo $filaTV[1];?>"><?php echo ucwords(strtolower($filaTV[0]));?></option><?php } ?>
              </select>
            </div>
            <!--Tercero-->       
            <div class="form-group form-inline" style="margin-top: -15px;">
               <input type="hidden" name="tercero" id="tercero" required="required" title="Seleccione tercero" value="<?php echo $row[1];?>">
              <label for="tercero" class="control-label"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
              <div class="form-group form-inline" style="margin-left: 1px; margin-top: -11px">   
              <select name="tercero" id="tercero"  required="required" class="select2_single form-control" tabindex="-1" style="display:inline-block;  text-align-last:left; width: 200px; margin-top: -11px" title="Seleccione tercero" onchange="llenar();">
                <option value="<?php echo $row[1]?>"><?php echo ucwords(strtolower($row[0].'('.$row[2].')'))?></option>
                <?php while($rowT = mysqli_fetch_row($tercero)) { ?>
                <option value="<?php echo $rowT[1]; ?>"><?php echo ucwords((strtolower($rowT[0].' ('.$rowT[2].')'))); } ?></option></select>
              </div>
               <!--uso-->  
              <label for="sltUso" class="control-label" style="margin-left: 15px;"><strong style="color:#03C1FB; ">*</strong>Uso:</label>
              <select name="sltUso" id="sltUso" class="form-control" title="Seleccione Uso" style="height: 35px; width: 200px;" required="required" >
                 <option value="<?php echo $row[9]?>"><?php echo ucwords(strtolower($row[10]))?></option>
                <?php while ($filaU = mysqli_fetch_row($rsU)) { ?>
                <option value="<?php echo $filaU[1];?>"><?php echo ucwords(strtolower($filaU[0]));?></option><?php } ?>
                </select>
            </div>
            <!--estrato-->          
            <div class="form-group form-inline" style="margin-top: -15px;" >
              <label for="sltEstrato" class="control-label"><strong style="color:#03C1FB;">*</strong>Estrato:</label>
              <select name="sltEstrato" id="sltEstrato" class="form-control " title="Seleccione Estrato" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[12]?>"><?php echo ucwords(strtolower($row[13]));?></option>
                <?php while ($filaE = mysqli_fetch_row($rsE)) { ?>
                <option value="<?php echo $filaE[1];?>"><?php echo ucwords(strtolower($filaE[0]));?></option><?php } ?>
              </select>
              <!--Numero de familias-->
              <label for="nro_familias" class="control-label"><strong class="obligado"></strong>Número de Familias:</label>
              <input type="text" name="nro_familias" id="nro_familias" class="form-control" maxlength="2" title="Número de familias" onkeypress="return txtValida(event,'num')" placeholder="Número de familias" style="display: inline; width: 200px; height: 35px" value="<?php if($row[14]!= 0){ echo $row[14]; }?>">
            </div>
            <!--Numero de personas-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="nro_personas" class="control-label"><strong class="obligado"></strong>Número de Personas:</label>
              <input type="text" name="nro_personas" id="nro_personas" class="form-inline" maxlength="2" title="Número de personas" onkeypress="return txtValida(event,'num')" placeholder="Número de personas" style="display: inline;  width: 200px; height: 35px" value="<?php if($row[15]!= 0){ echo $row[15]; }?>">
              <!--Codigo ruta-->
              <label for="codRuta" class="control-label"><strong class="obligado">*</strong>Código ruta:</label>
              <input type="text" name="codRuta" id="codRuta" class="form-inline" maxlength="100" title="Código de ruta" onkeypress="return txtValida(event,'sin_espcio')" placeholder="Código Ruta" required style="display: inline;  width: 200px; height: 35px" required="required" value="<?php echo strtoupper($row[16])?>">
            </div>
            
            <div class="form-group form-inline" style="margin-top: -15px;">
            <!--Codigo interno-->
              <label for="codInterno" class="control-label"><strong class="obligado">*</strong>Código interno:</label>
              <input type="text" name="codInterno" id="codInterno" class="form-control" maxlength="100" title="Código interno"  readonly="true" placeholder="Código Interno" required style="display: inline;  width: 200px; height: 35px" required="required" value ="<?php echo $row[17];?>">
            <!--Tipo productor-->
              <label for="tipoProd" class="control-label"><strong class="obligado">*</strong>Tipo Productor:</label>
              <select name="tipoProd" id="tipoProd" class="form-control " title="Seleccione Tipo Productor" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[19]?>"><?php echo ucwords(strtolower($row[20]));?></option>
                <?php while ($rowProd = mysqli_fetch_row($tipoPr)) { ?>
                <option value="<?php echo $rowProd[0];?>"><?php echo ucwords(strtolower($rowProd[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Sector-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="sector" class="control-label"><strong class="obligado">*</strong>Sector:</label>
              <select name="sector" id="sector" class="form-control " title="Seleccione Sector" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[21]?>"><?php echo ucwords(strtolower($row[22]));?></option>
                <?php while ($rowSect = mysqli_fetch_row($sector)) { ?>
                <option value="<?php echo $rowSect[0];?>"><?php echo ucwords(strtolower($rowSect[1]));?></option><?php } ?>
              </select>
            <!--Seccion-->
              <label for="seccion" class="control-label"><strong class="obligado">*</strong>Sección:</label>
              <select name="seccion" id="seccion" class="form-control " title="Seleccione Sector" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[23]?>"><?php echo ucwords(strtolower($row[24]));?></option>
                <?php while ($rowSecc= mysqli_fetch_row($seccion)) { ?>
                <option value="<?php echo $rowSecc[0];?>"><?php echo ucwords(strtolower($rowSecc[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Manzana-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="manzana" class="control-label"><strong class="obligado">*</strong>Manzana:</label>
              <select name="manzana" id="manzana" class="form-control " title="Seleccione Manzana" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[25]?>"><?php echo ucwords(strtolower($row[26]));?></option>
                <?php while ($rowMan = mysqli_fetch_row($manzana)) { ?>
                <option value="<?php echo $rowMan[0];?>"><?php echo ucwords(strtolower($rowMan[1]));?></option><?php } ?>
              </select>
            <!--Lado manzana-->
              <label for="ladoM" class="control-label"><strong class="obligado">*</strong>Lado Manzana:</label>
              <select name="ladoM" id="ladoM" class="form-control " title="Seleccione Lado Manzana" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[27]?>"><?php echo ucwords(strtolower($row[28]));?></option>
                <?php while ($rowladoM = mysqli_fetch_row($ladoMan)) { ?>
                <option value="<?php echo $rowladoM[0];?>"><?php echo ucwords(strtolower($rowladoM[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Sector hidraulico-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="sectorH" class="control-label"><strong class="obligado">*</strong>Sector Hidráulico:</label>
              <select name="sectorH" id="sectorH" class="form-control " title="Seleccione Sector Hidráulico" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[29]?>"><?php echo ucwords(strtolower($row[30]));?></option>
                <?php while ($rowSectH = mysqli_fetch_row($sectorH)) { ?>
                <option value="<?php echo $rowSectH[0];?>"><?php echo ucwords(strtolower($rowSectH[1]));?></option><?php } ?>
              </select>
            <!--Micro sector-->
              <label for="microS" class="control-label"><strong class="obligado">*</strong>MicroSector:</label>
              <select name="microS" id="microS" class="form-control " title="Seleccione Sector" style="height: 35px; width: 200px; " required="required">
                <option value="<?php echo $row[31]?>"><?php echo ucwords(strtolower($row[32]));?></option>
                <?php while ($rowmicroS = mysqli_fetch_row($microS)) { ?>
                <option value="<?php echo $rowmicroS[0];?>"><?php echo ucwords(strtolower($rowmicroS[1]));?></option><?php } ?>
              </select>
            </div>
            <!--Deshabilitado-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <label for="desha" class="control-label"><strong class="obligado"></strong>Deshabilitado:</label>
              <?php if ($row[33]== '1') { ?>
              <input type="radio" name="desha" id="desha" value="1" checked ="checked">Sí
              <input type="radio" name="desha" id="desha" value="2">No
              <?php } else {  if($row[33]=='2') { ?>
              <input type="radio" name="desha" id="desha" value="1" >Sí
              <input type="radio" name="desha" id="desha" value="2" checked>No
              <?php } else { ?>
              <input type="radio" name="desha" id="desha" value="1">Sí
              <input type="radio" name="desha" id="desha" value="2">No
              <?php }  } ?>
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
            <td><a href="GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>"><button class="btn btnInfo btn-primary" >Servicio</button></a><br/></td>
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
</html>

<script>
  function llenar(){
      var tercero = document.getElementById('tercero1').value;
      document.getElementById('tercero').value= tercero;
  }
  </script>