<?php
#########################MODIFICACIONES####################################
#11/05/2017 |ERICA G. |REGISTRAR SALDO DISPONIBLE
  require_once('../Conexion/conexion.php');
  session_start();
  require_once('../estructura_apropiacion.php');
  require_once('../estructura_apropiacion_modf.php');
  require_once('../estructura_saldo_obligacion.php');

  //Captura de datos e instrucción SQL para su inserción en la tabla gf_destalle_comprobante_pptal.
  $rubroFuente  = '"'.$mysqli->real_escape_string(''.$_POST['rubroFuente'].'').'"';
  $IDRubroFuente = $rubroFuente;
  $saldoDisponible = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente);
  
  
  $conceptoRubro  = '"'.$mysqli->real_escape_string(''.$_POST['conceptoRubro'].'').'"';
  $valor = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';

  $valor = str_replace(',', '', $valor);

  $comprobantepptal = $_SESSION['id_comprobante_pptal'];

  //---->> vvv PROVISIONAL vvv <<---------
  //Consulta del ID de la tabla parametrización año.
  $queryDesc = "SELECT descripcion FROM gf_comprobante_pptal WHERE id_unico = ".$comprobantepptal;
  $desc = $mysqli->query($queryDesc);
  $row = mysqli_fetch_row($desc);
  $descripcion = '"'.$mysqli->real_escape_string(''.$row[0].'').'"';

  ///---->> vvv PROVISIONAL vvv <<---------1
  //Consulta del ID de la tabla gf_tercero .
  $queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999";
  $vario = $mysqli->query($queryVario);
  $row = mysqli_fetch_row($vario);
  $tercero = $row[0];

  ///---->> vvv PROVISIONAL vvv <<---------1
  //Consulta del ID de la tabla gf_proyecto.
  $queryProyec = "SELECT id_unico FROM gf_proyecto WHERE nombre = 'Varios'";
  $proyect = $mysqli->query($queryProyec);
  $row = mysqli_fetch_row($proyect);
  $proyecto = $row[0];
 

  $campo = "";
  $variable = "";
  if(($descripcion != '""') || ($descripcion != NULL))
  {
    $campo = "descripcion,";
    $variable = "$descripcion,";
  }

  $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, "
          . "conceptorubro, tercero, proyecto, saldo_disponible) "
          . "VALUES ($variable $valor, $comprobantepptal, $rubroFuente, $conceptoRubro,"
          . " $tercero, $proyecto, $saldoDisponible)";
  $resultado = $mysqli->query($insertSQL);
  
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
          <p>No se ha podido guardar la información. </p>
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
<!-- Script que redirige a la página inicial. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../registrar_GF_COMPROBANTE_PPTAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>