<?php  
###################MODIFICACIONES#############################
#09/06/2017 |ERICA G. |CAMBIO DE CARPETA 
#######################################################################################
#18/05/2017 | ERICA G. |ARREGLO SI LOS REGISTROS TIENEN AFECTACIONES
#01/03/2017 | ERICA G. | AGREGAR VARIABLE DE SESION PARA VALIDAR SI ESTA REGISTRADO EL COMPROBANTE MODIFICAR DETALLES
##############################################################
require_once('../Conexion/conexion.php');
require_once('../jsonPptal/funcionesPptal.php');
session_start();
$idAnteriorComprobante  = '"'.$mysqli->real_escape_string(''.$_POST['solicitudAprobada'].'').'"';
$noDisponibilidad  = '"'.$mysqli->real_escape_string(''.$_POST['noDisponibilidad'].'').'"';
$fecha  = $mysqli->real_escape_string(''.$_POST['fecha'].'');
$fechaVen  = $mysqli->real_escape_string(''.$_POST['fechaVen'].'');
$estado  = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
if(!empty($_POST['descripcion'])) {
$descripcion = '"'.$_POST['descripcion'].'"';
} else {
   $descripcion = 'NULL'; 
}
$tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipoComPtal'].'').'"'; 
//Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
$fecha = trim($fecha, '"');
$fecha_div = explode("/", $fecha);
$dia = $fecha_div[0];
$mes = $fecha_div[1];
$anio = $fecha_div[2];
$fecha = $anio.'-'.$mes.'-'.$dia;
// Fecha de vencimiento.
$fechaVen = trim($fechaVen, '"');
$fecha_div = explode("/", $fechaVen);
$dia = $fecha_div[0];
$mes = $fecha_div[1];
$anio = $fecha_div[2];
$fechaVen = $anio.'-'.$mes.'-'.$dia;
$tercero = 2;
$responsable = 2;
$parametroAnno = $_SESSION['anno'];
$compania =$_SESSION['compania'];
$user =$_SESSION['usuario'];
$fecha_elaboracion =date('Y-m-d');
  
 $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
        parametrizacionanno, tipocomprobante, tercero, estado, responsable, compania, 
        descripcion, usuario, fecha_elaboracion) 
  VALUES($noDisponibilidad, '$fecha', '$fechaVen', $parametroAnno, $tipocomprobante, "
            . "$tercero, $estado, $responsable, $compania, "
        . "$descripcion, '$user','$fecha_elaboracion')";

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
            . "WHERE numero =$noDisponibilidad AND tipocomprobante =$tipocomprobante";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0]; 

    ########BUSCAR DATOS DE DISPONIBILIDAD#########
   $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
        detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
        detComP.id_unico , detComP.conceptoRubro 
      FROM gf_detalle_comprobante_pptal detComP 
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);

    $comprobantepptal = $idNuevoComprobante;
    
    while($row = mysqli_fetch_row($resultado))
    {
      
     #########SI ES REDUCCION #########
     if($tipoO==3){
      $saldDisp = $row[1];
      $totalAfec = 0;
      $queryDetAfe = "SELECT
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
          if($rowDtAf[1]==3){
                $saldDisp = $saldDisp - $rowDtAf[0];
          } else {
              if($rowDtAf[1]==2){
                  $saldDisp = $saldDisp + $rowDtAf[0];
              } else {
                  $saldDisp = $saldDisp- $rowDtAf[0];
              }
          }
           
          $id=$rowDtAf[2];
            $selec="  SELECT
            dcp.valor,
            tc.tipooperacion
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
          LEFT JOIN
            gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
          WHERE
            dcp.comprobanteafectado = $id AND tc.tipooperacion != 1  ";  
          $select =$mysqli->query($selec);
          if(mysqli_num_rows($select)>0){ 
              while($afect = mysqli_fetch_row($select)) {
                $val =$afect[0];
                $to = $afect[1];
                if($to==3){
                    $saldDisp +=$val;
                } else {
                    $saldDisp -=$val;
                }
              }

          }
          
          
      }
     
      $valorPpTl = $saldDisp;
   

      if($valorPpTl > 0)
      {

        

        $valor = $valorPpTl;
        $rubro = $row[2];
        $tercero = $row[3]; 
        $proyecto = $row[4];
        $idAfectado = $row[5];
        $conceptoRubro = $row[6];
        $campo = "";
        $variable = "";
        if(!empty($row[0]))
        {
          $descripcionD = $row[0];
        }

        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado, conceptoRubro) VALUES ($variable $valor, $comprobantepptal, $rubro, $tercero, $proyecto, $idAfectado,$conceptoRubro)";
        $resultadoInsert = $mysqli->query($insertSQL);
      }
    } else {
        if($tipoO==2){
            $rubro = $row[2];
            $saldoDis = apropiacion($rubro) - disponibilidades($rubro);
            $saldoDisponible = $saldoDis;
            if($saldoDisponible>0){
            $tercero = $row[3]; 
            $proyecto = $row[4];
            $idAfectado = $row[5];
            $conceptoRubro = $row[6]; 
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
          <p>No se ha podido guardar la información. </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!-- Script que redirige a la página inicial de Tipo Dirección. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    document.location = '../MODIFICACION_DISPONIBILIDAD_PPTAL.php?mod=<?php echo md5($idNuevoComprobante)?>';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
     window.location=window.history.back(-1);
  })
</script>
<?php } ?>