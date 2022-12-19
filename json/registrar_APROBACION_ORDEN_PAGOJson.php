<?php
####################MODIFICACIONES#########################
#11/05/2017 | ERICA G. | VALIDACION FECHA PARA CIERRRE
#22/03/2017 | ERICA G. | MODIFICA LA FECHA
#############################################
require_once('../Conexion/conexion.php');
session_start();
require_once('../Conexion/ConexionPDO.php'); 
$con = new ConexionPDO();
$compania = $_SESSION['compania'];

    //Captura de datos e instrucción SQL para su inserción en la tabla gf_comprobante_pptal
    $idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['solicitudAprobada'].'').'"';
     $fecha = $_POST['fecha'];
    $val=0;
    ##DIVIDIR FECHA
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

  $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero, comp.numerocontrato, comp.clasecontrato  
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico =  $idAnteriorComprobante";

  $comprobante = $mysqli->query($queryCompro);
  $rowComp = mysqli_fetch_row($comprobante);

  $id = $rowComp[0];
  $numero = $rowComp[1];
  $fecha_div = explode("/", $fecha);
    $anio = $fecha_div[2];
    $mes = $fecha_div[1];
    $dia = $fecha_div[0];
$fecha = $anio."-".$mes."-".$dia;
  $descripcion = "'".$rowComp[3]."'" ;
  //$descripcion = $rowComp[3];
  $fechaVen = $rowComp[4];
  $tercero = $rowComp[8];
  $numerocontrato = $rowComp[9];
  $clasecontrato = $rowComp[10];
$tc = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania= $compania AND clasepptal = 20 AND tipooperacion = 1");
$tc = $tc[0][0];
  $tipocomprobante = $tc; //Tipo de comprobante 28, APO Aprobar orden de pago.

  //====================================================================

  //Hallar el número consecutivo del comprobante.

$id_tip_comp = $tipocomprobante;

$parametroAnno = $_SESSION['anno'];
$sqlAnno = 'SELECT anno 
  FROM gf_parametrizacion_anno 
  WHERE id_unico = '.$parametroAnno;
$paramAnno = $mysqli->query($sqlAnno);
$rowPA = mysqli_fetch_row($paramAnno);
$numero = $rowPA[0];

$queryNumComp = 'SELECT MAX(numero) 
  FROM gf_comprobante_pptal 
  WHERE tipocomprobante = '.$id_tip_comp .'
  AND numero LIKE \''.$numero.'%\'';
$numComp = $mysqli->query($queryNumComp);
$row = mysqli_fetch_row($numComp);

if($row[0] == 0)
{
        $numero .= '000001';
}
else
{
        $numero = $row[0] + 1;
}
      
//==================================================================

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
  

  if($descripcion == "''")
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno, tipocomprobante, tercero, estado, responsable, numerocontrato, clasecontrato) 
  VALUES('$numero', '$fecha', '$fechaVen', '$parametroAnno', '$tipocomprobante', '$tercero', '$estado', '$responsable', '$numerocontrato', '$clasecontrato')";
  }
  else
  {
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable, numerocontrato, clasecontrato) 
  VALUES('$numero', '$fecha', '$fechaVen', $descripcion, '$parametroAnno', '$tipocomprobante', '$tercero', '$estado', '$responsable', '$numerocontrato', '$clasecontrato')";
  }
  
  $resultado = $mysqli->query($insertSQL);


  if($resultado == true)
  {
    $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal where tipocomprobante = $tipocomprobante AND numero =$numero";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 


    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
        detComP.id_unico, detComP.conceptorubro, detComP.centro_costo  
      FROM gf_detalle_comprobante_pptal detComP
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);

    $comprobantepptal = $idNuevoComprobante;
    
    while($row = mysqli_fetch_row($resultado))
    {

      $saldDisp = $row[1];
      $totalAfec = 0;
      $queryDetAfe = "SELECT DISTINCT
        dcp.valor,
        tc.tipooperacion, dcp.id_unico 
      FROM
        gf_detalle_comprobante_pptal dcp
      LEFT JOIN
        gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
      LEFT JOIN
        gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
      WHERE
        dcp.comprobanteafectado =".$row[5];
      $detAfec = $mysqli->query($queryDetAfe);
      $totalAfe = 0;
      while($rowDtAf = mysqli_fetch_row($detAfec))
      {
          if(($rowDtAf[1] == 3) ){
                $saldDisp = $saldDisp - $rowDtAf[0];
          } elseif(($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)){
                  $saldDisp = $saldDisp + $rowDtAf[0];
              } else {
                 $saldDisp = $saldDisp- $rowDtAf[0]; 
         }
          
      }
        
      
      $valorPpTl = $saldDisp;

      if($valorPpTl > 0)
      {

        //$descripcion = '"'.$row[0].'"'; 

        $valor = $valorPpTl;
        $rubro = $row[2];
        $tercero = $row[3]; 
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
        $centro_costo = $row[7];
        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado, conceptorubro, centro_costo) VALUES ($variable '$valor', '$comprobantepptal', '$rubro', '$tercero', '$proyecto', '$idAfectado', '$conceptorubro', $centro_costo)";
        $resultadoInsert = $mysqli->query($insertSQL);

      }

    }

    $updateSQL = "UPDATE gf_comprobante_pptal  
      SET estado = $estado     
      WHERE id_unico = $idAnteriorComprobante";
    $resultadoUpdate = $mysqli->query($updateSQL);

    $_SESSION['id_comp_pptal_OP'] = $idNuevoComprobante;
    $_SESSION['nuevo_OP'] = 1;
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
          <p>No se ha podido guardar la información.</p>
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
<!-- Script que redirige a la página inicial de aprobar solicitud. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../APROBACION_ORDEN_PAGO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
  $("#myModal2").modal('hide');
    window.location='../APROBACION_ORDEN_PAGO.php';
  });
</script>
<?php } ?>