<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#18/07/2018 |Erica G. | Centro Costo Presupuestal Privada
#23/01/2018 | Erica G. | Registros vigencia Anterior
#Modificado 01/03/2017 |Erica G. // Descripcion
#Modificado 03/02/2017 14:00 Ferney Pérez// Agregar fecha de vencimiento.
#Modificado 03/02/2017 11:00 Erica González //Registro Fecha
#Modificado 31/01/2017 10:30 Erica González //Modificacion contrato y numero
######################################################################################################
  require_once('../Conexion/conexion.php');
  require_once('../Conexion/ConexionPDO.php');
  $con = new ConexionPDO();
  session_start();
  $num=0;
  $anno = $_SESSION['anno'];
  $usuario = $_SESSION['usuario'];
  $fechaElab = date('Y-m-d');
  
###############SI ESTA VACIO EL REGISTRO###############
  if(empty($_POST['solicitudAprobada']) || $_POST['solicitudAprobada']=='N') {
        $parametroAnno = $_SESSION['anno'];
        $sqlAnno = 'SELECT anno 
          FROM gf_parametrizacion_anno 
          WHERE id_unico = '.$parametroAnno;
        $paramAnno = $mysqli->query($sqlAnno);
        $rowPA = mysqli_fetch_row($paramAnno);
        $numero = $rowPA[0];
        $id_tip_comp = $mysqli->real_escape_string(''.$_POST['tipoComprobante'].'');
        $numero = $_POST['numReg'];
        

        //==================================================================

        $fecha  =$mysqli->real_escape_string(''.$_POST['fecha'].'');
        
        if(!empty($_POST['descripcion'])) { 
            $descripcion= '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
        } else {
            $descripcion = 'NULL';
        } 

        if(!empty($_POST['claseContrato'])) { 
            $claseContrato= '"'.$mysqli->real_escape_string(''.$_POST['claseContrato'].'').'"';
        } else {
            $claseContrato ='NULL';
        } 
        if(!empty($_POST['numeroContrato'])) { 
            $numeroContrato= '"'.$mysqli->real_escape_string(''.$_POST['numeroContrato'].'').'"';
        } else {
            $numeroContrato ='NULL';
        } 
        
        if(!empty($_POST['proyecto'])) { 
            $proyecto= '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
        } else {
            $proyecto ='NULL';
        } 

        $tercero = $mysqli->real_escape_string(''.$_POST['tercero'].'');

        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];

        $fecha = $anio.'-'.$mes.'-'.$dia;
        $fecha_ = new DateTime($fecha);
        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
        $sumDias = $mysqli->query($querySum);
        $rowS = mysqli_fetch_row($sumDias);
        if(empty($rowS[0])){
            $sumarDias=28;
        } else {
         $sumarDias = $rowS[0];
        }

        $fecha_->modify('+'.$sumarDias.' day');
        $fechaVen = (string)$fecha_->format('Y-m-d');


        $estado = 3;

        $responsable = $_SESSION['usuario_tercero'];
        $parametroAnno = $_SESSION['anno'];

        $tipocomprobante = $id_tip_comp;
       
          $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
              descripcion, parametrizacionanno, tipocomprobante, tercero, estado, 
              responsable,  clasecontrato, numerocontrato, usuario, fecha_elaboracion, proyecto) 
            VALUES('$numero', '$fecha', '$fechaVen', $descripcion, '$parametroAnno', '$tipocomprobante', "
                  . "'$tercero', '$estado', '$responsable', $claseContrato, "
                  . "$numeroContrato, '$usuario', '$fechaElab', $proyecto)";
        
        $result = $mysqli->query($insertSQL);
        $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal where numero = '$numero' and tipocomprobante = '$tipocomprobante'";
        $ultimComp = $mysqli->query($queryUltComp);
        $rowUC = mysqli_fetch_row($ultimComp);
        $idNuevoComprobante = $rowUC[0]; 
        $num=1;
        $_SESSION['id_comp_pptal_CP'] = $idNuevoComprobante;
        $_SESSION['nuevo_CP'] = 1;
        $resultado = $result;
  
  } else { 
#######################################################  
  //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
  $idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['solicitudAprobada'].'').'"';
  $tipoComp  = '"'.$mysqli->real_escape_string(''.$_POST['tipoComprobante'].'').'"';
  $tercero = $mysqli->real_escape_string(''.$_POST['tercero'].'');
  $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero, comp.clasecontrato, comp.numerocontrato  
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico =  $idAnteriorComprobante";

  $comprobante = $mysqli->query($queryCompro);
  $rowComp = mysqli_fetch_row($comprobante);

  $id = $rowComp[0];

//====================================================================

$tipoComp = str_replace('"', '', $tipoComp);
$id_tip_comp = $tipoComp;

$parametroAnno = $_SESSION['anno'];
$sqlAnno = 'SELECT anno 
  FROM gf_parametrizacion_anno 
  WHERE id_unico = '.$parametroAnno;
$paramAnno = $mysqli->query($sqlAnno);
$rowPA = mysqli_fetch_row($paramAnno);
$numero = $rowPA[0];

$numero = $_POST['numReg'];

      
//==================================================================

$fecha  =$mysqli->real_escape_string(''.$_POST['fecha'].'');


  if(!empty($_POST['descripcion'])) { 
      $descripcion= '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  } else {
      if(!empty($rowComp[9])){
      $descripcion = '"'.$mysqli->real_escape_string(''.$rowComp[3].'').'"';
    } else {
        $descripcion ="NULL";
    }
  
  } 
  
  if(!empty($_POST['claseContrato'])) { 
      $claseContrato= '"'.$mysqli->real_escape_string(''.$_POST['claseContrato'].'').'"';
  } else {
    if(!empty($rowComp[9])){
      $claseContrato ="$rowComp[9]";
    } else {
        $claseContrato ="NULL";
    }
  } 
  if(!empty($_POST['numeroContrato'])) { 
      $numeroContrato= '"'.$mysqli->real_escape_string(''.$_POST['numeroContrato'].'').'"';
  } else {
      if(!empty($rowComp[10])) {
      $numeroContrato ="$rowComp[10]";
      } else {
          $numeroContrato ="NULL";
      }
  } 

  
if(!empty($_POST['proyecto'])) { 
    $proyectoe = '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
} else {
    $proyectoe ='NULL';
} 
  $fecha_div = explode("/", $fecha);
  $dia = $fecha_div[0];
  $mes = $fecha_div[1];
  $anio = $fecha_div[2];
  
  $fecha = $anio.'-'.$mes.'-'.$dia;
  $fecha_ = new DateTime($fecha);
  $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
  $sumDias = $mysqli->query($querySum);
  if(mysqli_num_rows($sumDias)>0){
  $rowS = mysqli_fetch_row($sumDias);
  
  $sumarDias = $rowS[0];
  } else {
      $sumarDias=30;
  }
  
  $fecha_->modify('+'.$sumarDias.' day');
  $fechaVen = $fecha_->format('Y-m-d');


  $estado = 3;
  
  $responsable = $_SESSION['usuario_tercero'];
  $parametroAnno = $_SESSION['anno'];
    
  $tipocomprobante = $tipoComp;

  if($descripcion == '""')
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno, 
        tipocomprobante, tercero, estado, responsable, clasecontrato, numerocontrato, usuario, fecha_elaboracion, proyecto) 
      VALUES('$numero', '$fecha', '$fechaVen', '$parametroAnno', '$tipocomprobante', "
            . "'$tercero', '$estado', '$responsable',  $claseContrato, $numeroContrato, '$usuario', "
            . "'$fechaElab', $proyectoe)";
  }
  else
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
        descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable,  
        clasecontrato, numerocontrato, usuario, fecha_elaboracion, proyecto) 
      VALUES('$numero', '$fecha', '$fechaVen', $descripcion, $parametroAnno, "
            . "'$tipocomprobante', '$tercero', '$estado', '$responsable', "
            . "$claseContrato, $numeroContrato, '$usuario', '$fechaElab', $proyectoe)";
  }
  $resultado = $mysqli->query($insertSQL);


  if($resultado == true)
  {
    $queryUltComp = "SELECT cp.id_unico , pr.nombre 
    FROM gf_comprobante_pptal cp 
    LEFT JOIN gf_proyecto pr ON cp.proyecto = pr.id_unico 
    where cp.numero = '$numero' and cp.tipocomprobante = '$tipocomprobante' AND cp.parametrizacionanno = $parametroAnno ";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 
    $proyectocomprobante = $rowUC[1]; 
    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, 
        detComP.valor, 
        detComP.rubrofuente, detComP.tercero, 
        detComP.proyecto, detComP.id_unico, 
        detComP.conceptorubro, rub.codi_presupuesto, 
        cc.id_unico, cc.nombre, 
        p.nombre 
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
      left join gf_concepto con on con.id_unico = conRub.concepto 
      LEFT JOIN gf_centro_costo cc ON detComP.centro_costo = cc.id_unico 
      LEFT JOIN gf_proyecto p ON detComP.proyecto = p.id_unico 
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);

    $comprobantepptal = $idNuevoComprobante;
    
    #Validar Si es VIgencia Anterior 
    if($_POST['vigenciaa']==1){
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

            $valor = $valorPpTl;
            $idrubro = $row[2];
            $codRubro = $row[7];
            $idconceptorubro = $row[6];
            #Buscar Rubro Equivalente Vigencia Actual 
            $re = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE equivalente = '$codRubro' AND tipoclase = 16 AND tipovigencia = 6 AND parametrizacionanno = $anno");
            if(count($re)>0){
                $rb = $re[0][0];
                $rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rb");
                if(count($rf)>0){
                    $rubro = $rf[0][0];
                    #Buscar Concepto Rubro
                    $cr = $con->Listar("SELECT id_unico FROM gf_concepto_rubro WHERE rubro = $rb");
                    if(count($cr)>0){
                        $conceptorubro =$cr[0][0];
                        $tercero = $row[3]; 
                        if($row[10]=='Varios' || $row[10]=='VARIOS'){
                            $proyecto = $proyectoe;
                        } else {
                            $proyecto = $row[4];
                        }
                        $idAfectado = $row[5];


                        $campo = "";
                        $variable = "";
                        if(($descripcion != '""') || ($descripcion != NULL))
                        {
                          $campo = "descripcion,";
                          $variable = "$descripcion,";
                        }
                        $centrcn = $row[9];
                        if(empty($row[8])){
                            #** Buscar Centro Costo Varios **#
                            $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                            WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                            if(count($cv)>0){
                                $cc = $cv[0][0];
                            } else {
                                $cc = 'NULL';
                            }
                        } else {
                            #** Buscar Centro Costo Nombre **#
                            $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                            WHERE parametrizacionanno = $anno AND nombre ='$centrcn'");
                            if(count($cv)>0){
                                $cc = $cv[0][0];
                            } else {
                                $cc = 'NULL';
                            }
                        }
                        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal 
                            ($campo valor, comprobantepptal, rubrofuente, tercero, 
                            proyecto, comprobanteafectado, conceptorubro, centro_costo) 
                            VALUES ($variable '$valor', '$comprobantepptal', '$rubro', 
                            '$tercero', $proyecto, '$idAfectado', '$conceptorubro', $cc)";
                        $resultadoInsert = $mysqli->query($insertSQL);
                    }
                }
            }
          }

        }
    } else {
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

            $valor = $valorPpTl;
            $rubro = $row[2];
            $tercero = $row[3]; 
            if($row[10]=='Varios' || $row[10]=='VARIOS'){
                $proyecto = $proyectoe;
            } else {
                $proyecto = $row[4];
            }
            $idAfectado = $row[5];
            $conceptorubro = $row[6];

            $campo = "";
            $variable = "";
            if(($descripcion != '""') || ($descripcion != NULL))
            {
              $campo = "descripcion,";
              $variable = "$descripcion,";
            }
            $centrcn = $row[9];
            if(empty($row[8])){
                #** Buscar Centro Costo Varios **#
                $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                if(count($cv)>0){
                    $cc = $cv[0][0];
                } else {
                    $cc = 'NULL';
                }
            } else {
                #** Buscar Centro Costo Nombre **#
                $cv =$con->Listar("SELECT * FROM gf_centro_costo 
                WHERE parametrizacionanno = $anno AND nombre ='$centrcn'");
                if(count($cv)>0){
                    $cc = $cv[0][0];
                } else {
                    $cc = 'NULL';
                }
            }
            $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal 
            ($campo valor, comprobantepptal, rubrofuente, tercero, 
            proyecto, comprobanteafectado, conceptorubro, centro_costo) 
            VALUES ($variable '$valor', '$comprobantepptal', '$rubro', '$tercero', 
            $proyecto, '$idAfectado', '$conceptorubro', $cc)"; 
            $resultadoInsert = $mysqli->query($insertSQL);
          }
        }
    }

    $updateSQL = "UPDATE gf_comprobante_pptal  
      SET estado = $estado     
      WHERE id_unico = $idAnteriorComprobante";
    $resultadoUpdate = $mysqli->query($updateSQL); 

    $_SESSION['id_comp_pptal_CP'] = $idNuevoComprobante;
    $_SESSION['nuevo_CP'] = 1;
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
          <?php if($num==1) { ?>
          <p>Cuenta Por Pagar Sin Registro Guardada Correctamente.</p>
          <?php } else { ?>
          <p>Información guardada correctamente.</p>
          <?php } ?>
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

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!-- Script que redirige a la página inicial de aprobar solicitud. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../GENERAR_CUENTA_PAGAR.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../GENERAR_CUENTA_PAGAR.php';
  });
</script> 
<?php } ?>