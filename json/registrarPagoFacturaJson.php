<?php
##########################################################################################
#   ****************************    Modificaciones      ****************************    #
##########################################################################################
#15/08/2018 |Erica G. |Guardar Usuario
##########################################################################################
session_start();
require_once '../Conexion/conexion.php';
require_once '../jsonPptal/funcionesPptal.php';
$id_usuario = $_SESSION['usuario_tercero'] ;
$anno = $_SESSION['anno'];
$nanno =anno($anno);
$tipoPago = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoPago'].'').'"';
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$numeroPago = trim($_POST['txtNumeroR']);
$responsable = ''.$mysqli->real_escape_string(''.$_POST['sltTercero'].'').'';
$banco = '"'.$mysqli->real_escape_string(''.$_POST['sltBanco'].'').'"';
$estado = 1;
$cupones = $mysqli->real_escape_string($_POST['txtCupones']);
$valorCupones = $mysqli->real_escape_string($_POST['txtValor']);
$sql = "INSERT INTO gp_pago(numero_pago,tipo_pago,"
        . "responsable,fecha_pago,banco,estado, parametrizacionanno, usuario) "
        . "VALUES('$numeroPago',$tipoPago,"
        . "$responsable,$fecha,$banco,$estado, $anno,$id_usuario)";
$resultadoP = $mysqli->query($sql);
$sqlConsulta = "SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago";
$resultado = $mysqli->query($sqlConsulta);
$idPago = mysqli_fetch_row($resultado);
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
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<?php if($resultado==true){
    #Consultamos si el tipo de pago tiene un tipo de comprobante cnt asociado
    #Y si tiene tipo de comprobante cnt asociado que registre en la tabla comprobante cnt
    $sql1="select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago";
    $result1=$mysqli->query($sql1);
    $tipoComprobanteCnt= mysqli_fetch_row($result1);
    if(!empty($tipoComprobanteCnt[0])){
        #Consultamos el ultimo numero de acuerdo al tipo de comprobante
        $sql2="select max(numero) from gf_comprobante_cnt where tipocomprobante=$tipoComprobanteCnt[0] AND parametrizacionanno = $anno";
        $result2=$mysqli->query($sql2);
        $numeroCnt= mysqli_fetch_row($result2);
        if(!empty($numeroCnt[0])){
            $numeroC=$numeroCnt[0]+1;
        }else{
            $numeroC=$nanno.'00001';
        }
        #Descripción del comprobante
        $descripcion= '"Comprobante de recaudo factura"';
        #variable de parametrización anno
        $param=$_SESSION['anno'];
        $compania = $_SESSION['compania'];
        #Estado del comprobante
        $estadoC='1';
        #Insertamos el comprobante
        $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) values($numeroPago,$fecha,$descripcion,$tipoComprobanteCnt[0],$param,$responsable,'1',$compania)";
        $resultInsertC=$mysqli->query($sqlInsertC);
        #Consultamos el ultimo comprobante ingresado
        $sqlUltimoCnt="select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipoComprobanteCnt[0] and numero=$numeroPago";
        $resultUltimoCnt=$mysqli->query($sqlUltimoCnt);
        $idCnt= mysqli_fetch_row($resultUltimoCnt);
        #Validamos que el tipo de comprobante cnt contenga asocidado un tipo de comprobante cnt o el campo comprobante_pptal no este vacio
        $sql3="select comprobante_pptal from gf_tipo_comprobante where id_unico=$tipoComprobanteCnt[0]";
        $result3=$mysqli->query($sql3);
        $tipoComPtal= mysqli_fetch_row($result3);
        #Validamos que el tipo de comprobante no venga vacio
        if(!empty($tipoComPtal[0])){
            #Consultamos el ultmo número registrado de acuerdo al tipo de comprobante pptal
            $sql4="select max(id_unico) from gf_comprobante_pptal where tipocomprobante=$tipoComPtal[0] AND parametrizacionanno = $anno";
            $result4=$mysqli->query($sql4);
            $numeroP= mysqli_fetch_row($result4);
            #Validamos si el valor consultado viene vacio que inicialize el conteo, de lo contrarop que sume uno al valor obtenido
            if(!empty($numeroP[0])){
                $numeroPp=$numeroP[0]+1;
            }else{
                $numeroPp=$nanno.'00001';
            }
            #Insertamos los datos en comprobante pptal
            $insertPptal="insert into gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) values($numeroPago,$fecha,$fecha,$descripcion,$param,$tipoComPtal[0],$responsable,'1',$responsable)";
            $resultInsertPptal=$mysqli->query($insertPptal);
            #Consultamos el ultimo comprobante pptal insertado
            $sql5="select id_unico from gf_comprobante_pptal where tipocomprobante=$tipoComPtal[0] and numero=$numeroPago";
            $result5=$mysqli->query($sql5);
            $idPPAL= mysqli_fetch_row($result5);
        }   
    }
    ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
    $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        <?php 
        #Validamos por medio de las ids que ruta enviar
        if(!empty($idCnt[0])){ 
            if(!empty($idPPAL[0])){ ?>
                window.location='<?php echo '../registrar_GF_RECAUDO_FACTURACION_2.php?recaudo='.md5($idPago[0]).'&cnt='. md5($idCnt[0]).'&pptal='. md5($idPPAL[0]).'&cupones='.$cupones.'&valor='.$valorCupones; ?>';
            <?php }else{ ?>
                window.location='<?php echo '../registrar_GF_RECAUDO_FACTURACION_2.php?recaudo='.md5($idPago[0]).'&cnt='. md5($idCnt[0]).'&cupones='.$cupones.'&valor='.$valorCupones; ?>';
           <?php } ?>            
        <?php }else{ ?>
            window.location='<?php echo '../registrar_GF_RECAUDO_FACTURACION_2.php?recaudo='.md5($idPago[0]).'&cupones='.$cupones.'&valor='.$valorCupones; ?>';
        <?php } ?>    
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../registrar_GF_RECAUDO_FACTURACION_2.php';
  });
</script>
<?php } ?>

