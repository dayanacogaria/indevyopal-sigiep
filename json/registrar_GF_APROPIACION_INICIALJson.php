<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php'); 
$con = new ConexionPDO();
session_start();

//Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
$rubro = '"'.$mysqli->real_escape_string(''.$_POST['rubro'].'').'"';
$fuente = '"'.$mysqli->real_escape_string(''.$_POST['fuente'].'').'"';
$valor = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';

$valor = str_replace(',', '', $valor);

$parametroAnno = $_SESSION['anno'];
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $parametroAnno";
$an = $mysqli->query($an);
$an= mysqli_fetch_row($an);
$annio = $an[0];
$anioAct = $annio."000001";
$N = 0;
$compania = $_SESSION['compania'];
$tc = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania= $compania AND clasepptal = 13 AND tipooperacion = 1");
$tc = $tc[0][0];
$querySql = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = $anioAct AND tipocomprobante = $tc AND parametrizacionanno = $parametroAnno";
$queryComprobante = $mysqli->query($querySql);
$N = $queryComprobante->num_rows;
$row = mysqli_fetch_row($queryComprobante);

if($N == 0)
{
//Fecha
$dia = "01";
$mes = "01";
$anio = $annio;

$numero = '"'.$anio.'000001"';

$fecha = $anio.$mes.$dia;
$fechavencimiento = $fecha;
$descripcion = '"APROPIACION INICIAL"';
$tipocomprobante = $tc;
$queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999";
$vario = $mysqli->query($queryVario);
$row = mysqli_fetch_row($vario);
$tercero = $row[0];
$responsable = $row[0];
$estado = 1;

$insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable) 
VALUES($numero, $fecha, $fechavencimiento, $descripcion, $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable)";
$resultado = $mysqli->query($insertSQL);

if($resultado == TRUE)
{
$queryMaxID = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numero and tipocomprobante = $tipocomprobante";
$maxID = $mysqli->query($queryMaxID);
$row = mysqli_fetch_row($maxID);
$id_comprobante_pptal = $row[0];
}


}
else
{
$id_comprobante_pptal = $row[0];
}



//Insert a la tabla gf_rubro_fuente
$insertSQL = "INSERT INTO gf_rubro_fuente (rubro, fuente) 
VALUES($rubro, $fuente)";
$resultado = $mysqli->query($insertSQL);

if($resultado == true)
{
//Seleccionar el ID del registro anteriormente ingresado en gf_rubro_fuente 
// para su posterior inserción en gf_detalle_comprobante_pptal.
$queryMaxID = "SELECT MAX(id_unico) FROM gf_rubro_fuente where rubro = $rubro and fuente =$fuente ";
$maxID = $mysqli->query($queryMaxID);
$row = mysqli_fetch_row($maxID);
$id_rubro_fuente = $row[0];



if($resultado == true)
{

$descripcion = '"APROPIACION INICIAL"';

//Tercero
//Consulta del ID de la tabla gf_tercero .
$queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999";
$vario = $mysqli->query($queryVario);
$row = mysqli_fetch_row($vario);
$tercero = $row[0];

$queryProy = "SELECT id_unico FROM gf_proyecto WHERE nombre = 'VARIOS'";
$proyecto = $mysqli->query($queryProy);
$row = mysqli_fetch_row($proyecto);
$id_proyecto = $row[0];

 $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (descripcion, valor, comprobantepptal, rubrofuente, tercero, proyecto) "
      . "VALUES ($descripcion, $valor, $id_comprobante_pptal, $id_rubro_fuente, $tercero, $id_proyecto)";
$resultado = $mysqli->query($insertSQL);
if($resultado ==true){

}

}

}

?>

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información. <?php echo $mysqli->error; ?></p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<!-- Script que redirige a la página inicial de aprobar solicitud. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../registrar_GF_APROPIACION_INICIAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../registrar_GF_APROPIACION_INICIAL.php';
  });
</script>
<?php } ?>