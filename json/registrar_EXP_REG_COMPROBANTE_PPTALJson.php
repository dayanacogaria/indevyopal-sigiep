<?php
  require_once('../Conexion/conexion.php');
  require_once('../estructura_apropiacion.php');
  session_start();
  $usuario = $_SESSION['usuario'];
  $fechaElab = date('Y-m-d');
  if(empty($_SESSION['agregar_ER']) || $_SESSION['agregar_ER'] != 3)
  {

  //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
  $idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['idComPtal'].'').'"';

  if(empty($_POST['noDisponibilidad']) or $_POST['noDisponibilidad']==' '){
      $numero = 'NULL';
  }  else {
    $numero   = '"'.$mysqli->real_escape_string(''.$_POST['noDisponibilidad'].'').'"';
  }
  $fecha  = '"'.$mysqli->real_escape_string(''.$_POST['fecha'].'').'"';
  $fechaVen  = '"'.$mysqli->real_escape_string(''.$_POST['fechaVen'].'').'"';
  $descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  $estado = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
  $tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipoComPtal'].'').'"';

  $claseContrato = '"'.$mysqli->real_escape_string(''.$_POST['claseCont'].'').'"';
  $tercero = '"'.$mysqli->real_escape_string(''.$_POST['terceroB'].'').'"';
  $numeroContrato = '"'.$mysqli->real_escape_string(''.$_POST['noContrato'].'').'"'; 

  //Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
  $fecha = trim($fecha, '"');
  $fecha_div = explode("/", $fecha);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fecha = $anio.$mes.$dia;

  //Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
  $fechaVen = trim($fechaVen, '"');
  $fecha_div = explode("/", $fechaVen);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fechaVen = $anio.$mes.$dia;
$compania =$_SESSION['compania'];
  $queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania = $compania";
  $vario = $mysqli->query($queryVario);
  $row = mysqli_fetch_row($vario);
  $responsable = $row[0];

  $parametroAnno = $_SESSION['anno'];

  if($descripcion == '""')
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
        parametrizacionanno, tipocomprobante, tercero, estado, responsable, clasecontrato, 
        numerocontrato,usuario, fecha_elaboracion) 
  VALUES($numero, $fecha, $fechaVen, $parametroAnno, $tipocomprobante, $tercero, "
            . "$estado, $responsable, $claseContrato, $numeroContrato, '$usuario', '$fechaElab')";
  }
  else
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
        descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable, 
        clasecontrato, numerocontrato,usuario, fecha_elaboracion) 
  VALUES($numero, $fecha, $fechaVen, $descripcion, $parametroAnno, $tipocomprobante, "
            . "$tercero, $estado, $responsable, $claseContrato, $numeroContrato, '$usuario', '$fechaElab')";
  }

  //echo $insertSQL;
  $resultado = $mysqli->query($insertSQL);


  if($resultado == true)
  {
    $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal where tipocomprobante = $tipocomprobante ";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 
    $comprobante = $idNuevoComprobante;
    $disponibilidad = $idAnteriorComprobante;
    $query = "SELECT descripcion, valor, 
        rubrofuente,
        tercero, 
        proyecto, 
        id_unico, 
        conceptorubro, centro_costo  
      FROM gf_detalle_comprobante_pptal 
      where comprobantepptal =$disponibilidad";
    
    $resultado1 = $mysqli->query($query);
    
    
   while($row = mysqli_fetch_row($resultado1)){
       $valorRep=0;
      ################VALOR DISPONIBLE##########
        $totalSaldDispo = 0;
        $valorRep +=$row[1];
        $saldo =0;
        ########AFECTACIONES A DISPONBILIDAD#########
        $afec = "SELECT tc.tipooperacion, dc.valor, dc.id_unico FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                WHERE dc.comprobanteafectado = $row[5]";
        $afec = $mysqli->query($afec);
        while ($row2 = mysqli_fetch_row($afec)) {
            if($row2[0]==2){
                $valorRep +=$row2[1];
            } elseif($row2[0]==1) {
                   $valorRep -=$row2[1];
                    ########AFECTACIONES A REGISTRO#########
                    $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc 
                            LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $row2[2]";
                    $afecR = $mysqli->query($afecR);
                    if(mysqli_num_rows($afecR)>0){
                    while ($row2R = mysqli_fetch_row($afecR)) {
                        if($row2R[0]==2){
                            $valorRep -=$row2R[1];
                        } 
                        elseif($row2R[0]==3) {
                            $valorRep +=$row2R[1];
                        }
                    }
                    }
            }elseif($row2[0]==3) {
                $valorRep-=$row2[1];
            }
        }
        $totalSaldDispo += $valorRep;
        $valor = $valorRep;
     ####################################################
      if($valor>0){
      $datos = "SELECT descripcion, tercero FROM gf_comprobante_pptal WHERE id_unico ='$comprobante'";
      $datos =$mysqli->query($datos);
      
      if($row[1] > 0){
          if(mysqli_num_rows($datos)>0){
           $datos = mysqli_fetch_row($datos);
           if(empty($datos[1])){
               $tercero = $row[3];
           }else {
           $tercero = $datos[1];
           }
           if(empty($datos[0])){
             $descripcion = $row[0];  
           }else {
           $descripcion = $datos[0];
           }
           
          } else {
          $descripcion = $row[0];
          $tercero= $row[3];
          }
          
          $rubro = $row[2];
          $proyecto = $row[4];
          $idAfectado = $row[5];
          $conceptorubro = $row[6];
          $var=0;
          if(empty($row[7])){
            $centro_costo = 'NULL';
          } else {
            $centro_costo = $row[7];
          }
          $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor,descripcion, "
                  . "comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado, conceptorubro, centro_costo) "
                  . "VALUES ($valor, '$descripcion',$comprobante, $rubro, $tercero, $proyecto, $idAfectado, $conceptorubro, $centro_costo)";
          $resultado = $mysqli->query($insertSQL);
          
      } else {
        $resultado = false;  
      }
      }
     
   }

    $_SESSION['id_comp_pptal_ER'] = $idNuevoComprobante;
    $_SESSION['nuevo_ER'] = 1;
  }

  }
  elseif($_SESSION['agregar_ER'] == 3) //En caso de tener un comprobante vacía y se vaya a insertar el detalle de un comprobante seleccionado.
  {
    $tercero = '"'.$mysqli->real_escape_string(''.$_POST['terceroB'].'').'"';


    $comprobantepptal = $_SESSION['id_comp_pptal_ER'];
    $detalleComprobante = $_SESSION['id_comp_pptal_ER_Detalle'];

    $sqlUpdateTer = "UPDATE gf_comprobante_pptal
      SET tercero = $tercero
      WHERE id_unico = $comprobantepptal";
    $resultadoInsert = $mysqli->query($sqlUpdateTer);

    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, detComP.conceptorubro  
      FROM gf_detalle_comprobante_pptal detComP
      where detComP.comprobantepptal = $detalleComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);
    
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

        $descripcion = '"'.$mysqli->real_escape_string(''.$row[0].'').'"';

        $valor = $valorPpTl;
        $rubro = $row[2];
        $proyecto = $row[4];
        $idAfectado = $row[5];
        $conceptorubro = $row[6];

        $campo = "";
        $variable = "";
        if(($descripcion != '""') && ($descripcion != NULL))
        {
          $campo = "descripcion,";
          $variable = "$descripcion,";
        }

         $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado, conceptorubro) VALUES ($variable $valor, $comprobantepptal, $rubro, $tercero, $proyecto, $idAfectado, $conceptorubro)";
        $resultadoInsert = $mysqli->query($insertSQL);
      }
    }

    $_SESSION['id_comp_pptal_ER'] = $comprobantepptal;
    $_SESSION['id_comp_pptal_ER_Detalle'] = '';
    $_SESSION['nuevo_ER'] = 1;
    $_SESSION['agregar_ER'] = "";
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
          <p>No se ha podido guardar la información.<?php echo $mysqli->error;?></p>
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
    window.location='../EXPEDIR_REGISTRO_PPTAL.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../EXPEDIR_REGISTRO_PPTAL.php';
  });
</script>
<?php } ?>