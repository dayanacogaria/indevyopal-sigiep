<?php
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
  $idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['solicitudAprobada'].'').'"';
  $tipoComp  = '"'.$mysqli->real_escape_string(''.$_POST['tipoComprobante'].'').'"';

  $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico =  $idAnteriorComprobante";

  $comprobante = $mysqli->query($queryCompro);
  $rowComp = mysqli_fetch_row($comprobante);

  $id = $rowComp[0];
  $numero = $rowComp[1];
  $fecha = $rowComp[2];
  $descripcion = '"'.$rowComp[3].'"' ;
  $fechaVen = $rowComp[4];
  $tercero = $rowComp[8];

  $fecha_div = explode("-", $fecha);
  $anio = $fecha_div[0];
  $mes = $fecha_div[1];
  $dia = $fecha_div[2];
  
  $fecha = $anio.$mes.$dia;

  $fecha_div = explode("-", $fechaVen);
  $anio = $fecha_div[0];
  $mes = $fecha_div[1];
  $dia = $fecha_div[2];
  
  $fechaVen = $anio.$mes.$dia;

  $estado = 3;

 
    $responsable = $_SESSION['usuario_tercero'];
 
    $parametroAnno = $_SESSION['anno'];
  
  

  //$tipocomprobante = 28; //Tipo de comprobante 28, APO Aprobar orden de pago.
  $tipocomprobante = $tipoComp;

  if($descripcion == '""')
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno, tipocomprobante, tercero, estado, responsable) 
  VALUES($numero, $fecha, $fechaVen, $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable)";
  }
  else
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable) 
  VALUES($numero, $fecha, $fechaVen, $descripcion, $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable)";
  }
  $resultado = $mysqli->query($insertSQL);


  if($resultado == true)
  {
    $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 


    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico 
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
      left join gf_concepto con on con.id_unico = conRub.concepto 
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);

    $comprobantepptal = $idNuevoComprobante;
    
    while($row = mysqli_fetch_row($resultado))
    {

      $saldDisp = 0;
      $totalAfec = 0;
      $queryDetAfe = "SELECT valor   
      FROM gf_detalle_comprobante_pptal   
      WHERE comprobanteafectado = ".$row[5];
      $detAfec = $mysqli->query($queryDetAfe);
      $totalAfe = 0;
      while($rowDtAf = mysqli_fetch_row($detAfec))
      {
        $totalAfec += $rowDtAf[0];
      }
        
      $saldDisp = $row[1] - $totalAfec;
      $valorPpTl = $saldDisp;

      if($valorPpTl > 0)
      {

        $descripcion = '"'.$row[0].'"'; 

        $valor = $valorPpTl;
        $rubro = $row[2];
        $tercero = $row[3]; 
        $proyecto = $row[4];
        $idAfectado = $row[5];

        $campo = "";
        $variable = "";
        if(($descripcion != '""') || ($descripcion != NULL))
        {
          $campo = "descripcion,";
          $variable = "$descripcion,";
        }

        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado) VALUES ($variable $valor, $comprobantepptal, $rubro, $tercero, $proyecto, $idAfectado)";
        $resultadoInsert = $mysqli->query($insertSQL);

      }

    }

    $updateSQL = "UPDATE gf_comprobante_pptal  
      SET estado = $estado     
      WHERE id_unico = $idAnteriorComprobante";
    $resultadoUpdate = $mysqli->query($updateSQL); 

    $_SESSION['id_comp_pptal_GE'] = $idNuevoComprobante;
    $_SESSION['nuevo_GE'] = 1;
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
    window.location='../GENERAR_EGRESO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>