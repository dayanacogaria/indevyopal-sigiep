  <?php
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
  $rubro = '"'.$mysqli->real_escape_string(''.$_POST['rubro'].'').'"';
  $fuente = '"'.$mysqli->real_escape_string(''.$_POST['fuente'].'').'"';
  $valor = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';

  $valor = str_replace(',', '', $valor);

  $resultado = true;

  $queryRubFue = "SELECT id_unico 
    FROM gf_rubro_fuente 
    WHERE rubro = $rubro   
    AND fuente = $fuente";

  $rubroFuente = $mysqli->query($queryRubFue);
  $row = mysqli_fetch_row($rubroFuente);
  $id_rub_fue = $row[0];

  if($id_rub_fue == 0)
  {
    // No existe este registro así que puede guardarse.
     //Insert a la tabla gf_rubro_fuente
    $insertSQL = "INSERT INTO gf_rubro_fuente (rubro, fuente) 
      VALUES($rubro, $fuente)";
    $resultado = $mysqli->query($insertSQL);

    if($resultado == true)
    {
      $queryMaxID = "SELECT MAX(id_unico) FROM gf_rubro_fuente";
      $maxID = $mysqli->query($queryMaxID);
      $row = mysqli_fetch_row($maxID);
      $id_rubro_fuente = $row[0];
    }

  }
  else
  {
    //Ya existe un registro repetido.
    $id_rubro_fuente =  $id_rub_fue;

  }

  
      $id_comprobante_pptal = $_SESSION['idComPtalAdic'];
  

    if($resultado == true)
    {
      
        $descripcion = '"ADICIÓN APROPIACIÓN"';
        
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
          <p>No se ha podido guardar la información.</p>
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
    window.location='../ADICION_APROPIACION.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>