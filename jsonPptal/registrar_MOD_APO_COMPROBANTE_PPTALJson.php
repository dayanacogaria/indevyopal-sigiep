<?php
############################MODIFICACIONES#############################################
#09/06/2017 |ERICA G. |CAMBIO DE CARPETA    ARREGLO DE CÓDIGO 
#######################################################################################
  require_once('../Conexion/conexion.php');
  require_once('../estructura_apropiacion.php'); 
  session_start(); 
 $idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['solicitudAprobada'].'').'"';

  $numero   = '"'.$mysqli->real_escape_string(''.$_POST['noDisponibilidad'].'').'"';
  $fecha  = '"'.$mysqli->real_escape_string(''.$_POST['fecha'].'').'"';
  $fechaVen  = '"'.$mysqli->real_escape_string(''.$_POST['fechaVen'].'').'"';
  if(empty($_POST['descripcion'])){
    $descripcion = "NULL";
  } else {
    $descripcion = '"'.$_POST['descripcion'].'"';
  }
  
  $tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipoComPtal'].'').'"';
  $claseContrato = '"'.$mysqli->real_escape_string(''.$_POST['claseCont'].'').'"';
  $tercero = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
  $numeroContrato = '"'.$mysqli->real_escape_string(''.$_POST['noContrato'].'').'"'; 

  $fecha = trim($fecha, '"');
  $fecha_div = explode("/", $fecha);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fecha = $anio.'-'.$mes.'-'.$dia;

  //Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
  $fechaVen = trim($fechaVen, '"');
  $fecha_div = explode("/", $fechaVen);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fechaVen =  $anio.'-'.$mes.'-'.$dia;
  $responsable = 2;
  $parametroAnno = $_SESSION['anno'];
  $fechaElab = date('Y-m-d');
  $usuario = $_SESSION['usuario'];
  
 
  $insertSQL = "INSERT INTO gf_comprobante_pptal 
      (numero, fecha, fechavencimiento, descripcion, 
      parametrizacionanno, tipocomprobante, tercero, responsable, 
      clasecontrato, numerocontrato, fecha_elaboracion, usuario) 
  VALUES($numero, '$fecha', '$fechaVen', $descripcion, $parametroAnno, "
          . "$tipocomprobante, $tercero,  $responsable, "
          . "$claseContrato, $numeroContrato, '$fechaElab', '$usuario')";
  $resultado = $mysqli->query($insertSQL);
  if($resultado == true)
  {
      #########TIPO DE OPERACION DEL COMPROBANTE##########
    $tipoO ="SELECT tipooperacion FROM gf_tipo_comprobante_pptal "
            . "WHERE id_unico = $tipocomprobante";
    $tipoO =$mysqli->query($tipoO);
    $tipoO = mysqli_fetch_row($tipoO);
    $tipoO= $tipoO[0];
    #####BUSCAR COMPROBANTE ACABAMOS DE INGRESAR #########
    $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal "
            . "WHERE numero =$numero AND tipocomprobante =$tipocomprobante";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 
    ########BUSCAR DATOS DE REGISTRO#########
    $queryAntiguoDetallPttal = "SELECT detComP.id_unico,
        detComP.valor, detComP.rubrofuente, 
        detComP.proyecto, detComP.conceptorubro 
      FROM gf_detalle_comprobante_pptal detComP
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);
 
    $comprobantepptal = $idNuevoComprobante;
     
    
    while($row = mysqli_fetch_row($resultado))
    {
      #########SI ES REDUCCION #########
     if($tipoO==3){
        $valorD = $row[1];
        $afect = "SELECT dc.valor, tc.tipooperacion "
                . "FROM gf_detalle_comprobante_pptal dc "
                . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                . "WHERE dc.comprobanteafectado = '$row[0]'";
        $afect = $mysqli->query($afect);
        while ($af = mysqli_fetch_row($afect)) {
            $to = $af[1];
            if(($to == 2) || ($to == 4))
            {
                    $valorD += $af[0];
            }
            elseif($to == 3)
            {
                    $valorD -= $af[0];
            } 
            elseif($to == 1){
                   $valorD -= $af[0];
            }

        }

        $saldoDisponible = $valorD; 
     
      $valorPpTl = $saldoDisponible;
   

      if($valorPpTl > 0)
      {
        $valor = $valorPpTl;
        $rubro = $row[2]; 
        $proyecto = $row[3];
        $idAfectado = $row[0];
        $conceptoRubro = $row[4];

        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ("
                . " valor, comprobantepptal, rubrofuente, tercero, proyecto, "
                . "comprobanteafectado, conceptoRubro, descripcion) VALUES "
                . "( $valor, $comprobantepptal, $rubro, $tercero, $proyecto, "
              . "$idAfectado,$conceptoRubro, $descripcion)";
        $resultadoInsert = $mysqli->query($insertSQL);
      }
    } else {
        if($tipoO==2){
            $valorDis ="SELECT DISTINCT dcp.id_unico, dcp.comprobanteafectado ,dcp.valor, dca.valor 
                    FROM gf_detalle_comprobante_pptal dcp 
                    LEFT JOIN gf_detalle_comprobante_pptal dca 
                    ON dcp.comprobanteafectado = dca.id_unico
                    WHERE dcp.id_unico = $row[0]";
        $valorDis = $mysqli->query($valorDis);
        $valorDisp=0;
        $afectado =0;
        $valorD=0;
        $valorRep=0;
        while($rowDetComp = mysqli_fetch_row($valorDis))
        {
            ####AFECTACIONES DISPONIBILIDAD######
            $valorD = $rowDetComp[3];
            $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                    . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                    . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                    . "WHERE dc.comprobanteafectado = $rowDetComp[1] "
                    . "and tc.tipooperacion !=1";
            $afr = $mysqli->query($afr);
            while($row4 = mysqli_fetch_row($afr)){
                if($row4[1]==3){
                    $valorD =$valorD-$row4[0];
                } elseif($row4[1]==2){
                    $valorD =$valorD+$row4[0];
                }
            }
            ####AFECTACIONES REGISTRO######
            $valorRep = $rowDetComp[2];
            $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                    . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                    . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                    . "WHERE dc.comprobanteafectado = $rowDetComp[0] "
                    . "and tc.tipooperacion !=1";
            $afr = $mysqli->query($afr);
            while($row4 = mysqli_fetch_row($afr)){
                if($row4[1]==3){
                    $valorRep =$valorRep-$row4[0];
                } elseif($row4[1]==2){
                    $valorRep =$valorRep+$row4[0];
                }
            }

            $saldoDis  = $valorD-$valorRep;
            $valorDisp +=$saldoDis;

        }
        $saldoDisponible = $valorDisp; 
        $rubro = $row[3];
        if($saldoDisponible>0){
            $rubro = $row[2]; 
            $proyecto = $row[3];
            $idAfectado = $row[0];
            $conceptoRubro = $row[4];
            $valor = 0;
            $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor, "
                    . "comprobantepptal, rubrofuente, tercero, proyecto,"
                    . " comprobanteafectado, conceptoRubro, descripcion ) "
                    . "VALUES ($valor, $comprobantepptal, "
                    . "$rubro, $tercero, $proyecto, $idAfectado,$conceptoRubro, $descripcion)";
            $resultadoInsert = $mysqli->query($insertSQL);
            }
        }
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
    window.location='../MODIFICACION_APROBACION.php?mod=<?php echo md5($comprobantepptal)?>';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal1").modal('hide');
    window.location=window.history.back(-1);
  });
</script>
<?php } ?>