<?php
####################MODIFICACIONES#########################
#11/05/2017 | ERICA G. | VALIDACION FECHA PARA CIERRRE 
  require_once('../Conexion/conexion.php');
  require_once('../Conexion/ConexionPDO.php'); 
  session_start();
  $con = new ConexionPDO();
  $compania = $_SESSION['compania'];
  $usuario    = $_SESSION['usuario'];
  
$val=0;
  //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante.
  $codigo  = '"'.$mysqli->real_escape_string(''.$_POST['codigo'].'').'"';
  $fecha  = '"'.$mysqli->real_escape_string(''.$_POST['fecha'].'').'"';
  $estado  = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
  $descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';

 
  $fecha = trim($fecha, '"');
  $fecha_div = explode("/", $fecha);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
     $ci="SELECT
    cp.id_unico
    FROM 
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
    $ci =$mysqli->query($ci);
    if(mysqli_num_rows($ci)>0){ 
        $resultado=false;
        $val=1;
    } else {
  $fecha = $anio."-".$mes."-".$dia;
  $fecha_ = new DateTime($fecha);

  $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Solicitud'";
  $sumDias = $mysqli->query($querySum);
  $rowS = mysqli_fetch_row($sumDias);
  $sumarDias = $rowS[0];

  $fecha_->modify('+'.$sumarDias.' day');
  $fechaVen = (string)$fecha_->format('Y-m-d');
  
  ///---->> vvv PROVISIONAL vvv <<---------
  //Consulta del ID de la tabla gf_tercero .
  $compania = $_SESSION['compania'];
  $queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania =$compania";
  $vario = $mysqli->query($queryVario);
  $row = mysqli_fetch_row($vario);
  $tercero = $row[0];
  $responsable = $row[0];

//Captura de parametrización año o consulta del ID de la tabla parametrización año en caso de ser vacío.
 $parametroAnno = $_SESSION['anno'];
  
  
  $tc = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania= $compania AND clasepptal = 11 and tipooperacion = 1");
  
  $tc = $tc[0][0];
  $tipocomprobante = $tc; // Tipo de comprobante 6, SOLICITUD DE DISPONIBILIDAD.


  if($descripcion == '""')
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno, tipocomprobante, tercero, estado, responsable,usuario) 
  VALUES($codigo, '$fecha', '$fechaVen', $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable,'$usuario')";
  }
  else
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable,usuario) 
  VALUES($codigo, '$fecha', '$fechaVen', $descripcion, $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable,'$usuario')";
  }

  //echo $insertSQL;
  $resultado = $mysqli->query($insertSQL);
  
  if($resultado == true)
  {
    $queryUltimo = "SELECT MAX(id_unico) Id FROM gf_comprobante_pptal WHERE numero = $codigo AND tipocomprobante = $tipocomprobante";
    $ultimo = $mysqli->query($queryUltimo);
    $row = mysqli_fetch_row($ultimo);
    $_SESSION['id_comprobante_pptal'] = $row[0];
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
            <?php if($val==0) { ?>
          <p>No se ha podido guardar la información. </p>
            <?php } else { ?>
          <p>El periodo para la fecha escogida ya está cerrado,por favor, escoja otra fecha.</p>
            <?php } ?>
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
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../registrar_GF_COMPROBANTE_PPTAL.php';
  });
</script>
<?php } ?>