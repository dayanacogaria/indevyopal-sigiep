<?php
  header("Content-Type: text/html;charset=utf-8");
  require_once('../Conexion/conexion.php');
	$ruta_archivo =  $_FILES['archivo']['tmp_name'];
	$separador = $_POST['separador'];
	$filaIni = $_POST['filaIni'];
	$filaFin = $_POST['filaFin'];
	$tabla = $_POST['tabla'];
  if($separador == "tab" || $separador == "TAB") {
    $separador = "\t";
  }
	$acerto = 0;
	$fallo = 0;
  $cont = 0;
  $campo = "";
  $abrir = fopen($ruta_archivo,'r+');
  $contenido = fread($abrir,filesize($ruta_archivo));
  fclose($abrir);
  // Separar línea por línea
  $contenido = explode("\n",$contenido);
  $sql = "SELECT column_name
          FROM INFORMATION_SCHEMA.COLUMNS
          WHERE table_name = '$tabla'
          AND column_key != 'PRI'";
  $resultadoS = $mysqli->query($sql);
	$num = $resultadoS->num_rows;
	$io = 0;
	while ($row = mysqli_fetch_row($resultadoS)) {
    $io += 1;
    $campo .= $row[0];
    if($io < $num) {
      $campo .= ", ";
    }
	}
  $valores = "";
  $insertSQL = "INSERT INTO ".$tabla."(";
  for($i = $filaIni - 1; $i < $filaFin; $i++) {
    $valores .= "(";
    $linea = $contenido[$i];
    $arrLinea = explode($separador, $linea);
    $no = count($arrLinea);
    foreach ($arrLinea as $valor) {
      $valores .= "'".trim($valor)."'";
      if($valor !== end($arrLinea)) {
        $valores .= ", ";
      } else  {
        $valores .= ")";
      }
    }
    if($i < $filaFin - 1) {
      $valores .= ", ";
    }
  }
  $insertSQL .= $campo.") VALUES".$valores;
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
<div class="modal fade" id="myModal1" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
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
<div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal3" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Algunos de los datos no se han podido guardar.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<?php if($resultado == true) { ?>
  <script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function(){
      $("#myModal1").modal('hide');
      window.location='../cargar_archivo.php';
    });
  </script>
<?php } else { ?>
  <script type="text/javascript">
    $("#myModal2").modal('show');
    $("#ver2").click(function(){
        $("#myModal2").modal('hide');
        window.location='../cargar_archivo.php';
    });
  </script>
<?php } ?>
