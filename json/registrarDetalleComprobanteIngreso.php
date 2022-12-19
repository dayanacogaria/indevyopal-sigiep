<?php
#############################Modificaciones##########################
#21/07/2017 | Erica G    |No relacionaba bien los detalles cnt y pptal
# 17/02/2017 | Erica G. #Modificar Entrada de valores debito y credito
###### 12:00 | Jhon Numpaque | 03-02-2017
##### Variación de valores entre comprobante presupuestal(+) y contable (-)
#comprobate detalle contable y presupuestal

require_once '../Conexion/conexion.php';
session_start();
$concepto = '"'.$mysqli->real_escape_string(''.$_POST['sltConcepto'].'').'"';
$rubro = '"'.$mysqli->real_escape_string(''.$_POST['sltRubroFuente'].'').'"';
$anno = $_SESSION['anno'];
######################################################################
#fecha

#descripción
$sqlD = 'SELECT descripcion, fecha, numerocontrato, clasecontrato, tercero FROM gf_comprobante_cnt WHERE id_unico ='.$_SESSION['idComprobanteI'];
$f = $mysqli->query($sqlD);
$des = mysqli_fetch_row($f);
if(empty($des[0])) {
  $descripcion='NULL';
} else {
  $descripcion = "'".$des[0]."'";
}
$fechaD = $des[1];
if(empty($des[2])){
  $numcontra='NULL';
} else {
  $numcontra= $des[2];
}

if(empty($des[3])){
  $clasec='NULL';
} else {
  $clasec= $des[3];
}

$terceroCom= $des[4];

#Llenar por el usuario
$valorEjec = "0";
$sqlC = 'SELECT id_unico FROM gf_comprobante_cnt WHERE id_unico ='.$_SESSION['idComprobanteI'];
$res = $mysqli->query($sqlC);
$rw = mysqli_fetch_row($res);
$comprobante = $rw[0];
$cuenta = '"'.$mysqli->real_escape_string(''.$_POST['sltcuenta'].'').'"';
$detalleA = "NULL";

$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
$rs = $mysqli->query($sql);
$nat = mysqli_fetch_row($rs);
$natural = $nat[0];
$valorP = $mysqli->real_escape_string(''.$_POST['txtValor'].'');
if ($nat[0] == 1) {
    $valor = $valorP*-1;
} else {
    $valor = $valorP;
}

//Valor solo para registrar Detalle presupuesto
//$valor_DP = $mysqli->real_escape_string(''.$_POST['txtValor'].'');
//Validaciiones y casos para registro en el Valor para comprobante presupuestal
if($nat[0] == 1 && $valor < 0) {// Si la naturaleza es debito y el valor es menor que 0, es decir el valor es negativo
    $valorDP = $valor *-1;  //Lo convierte a positivo en presupuesto
}

if($nat[0] == 1 && $valor > 0) { //Si la naturaleza es debito y el valor es mayor que 0, es decir el valor es positivo
    $valorDP = $valor *-1; //Lo convierte a negativo en presupuesto    
}

if($nat[0] == 2 && $valor > 0) {//Si la naturaleza es credito y el valor es positivo, es decir el valor es mayor que 0
    $valorDP = $valor;//El valor es positivo en presupuesto
}

if($nat[0] == 2 && $valor < 0) {//Si la naturaleza es credito y el valor es negativo, es decir menor que 0
    $valorDP = $valor;//El valor es negativo en presupuesto
}


if(empty($_POST['slttercero'])){
    $tercero = '"2"';
}else{
    $tercero = '"'.$mysqli->real_escape_string(''.$_POST['slttercero'].'').'"';
}
if(empty($_POST['sltproyecto'])){
    if(!empty($_SESSION['proyecto'])){
        $proyecto = $_SESSION['proyecto'];
    }  else {
        $proyecto = '"2147483647"';
    }
}else{
    $proyecto = '"'.$mysqli->real_escape_string(''.$_POST['sltproyecto'].'').'"';
}
if(empty($_POST['sltcentroc'])){
    if(!empty($_SESSION['centrocosto'])){
        $centroCosto = $_SESSION['centrocosto'];
    }else{
        #** Buscar Cnetro Costo Varios 
        $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo 
            WHERE nombre = 'varios' AND parametrizacionanno = $anno ORDER BY nombre ASC";
        $a = $mysqli->query($sqlCC);
        $filaC = mysqli_fetch_row($a);
        $centroCosto = $filaC[0];
    }
}else{
    $centroCosto = '"'.$mysqli->real_escape_string(''.$_POST['sltcentroc'].'').'"';
}
$conceptoRubro = '"'.$mysqli->real_escape_string(''.$_POST['txtConceptoR'].'').'"';
#ingreso en detalle comprobante pptal
//$comprobanteIng = $_SESSION['numeroCI'];
//$sqlCP = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero =$comprobanteIng";
//$result = $mysqli->query($sqlCP);
//$fila102 = mysqli_fetch_row($result);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Consulta hecha por Erica Gonzalez, para obtener el tipo de comprobante y el número de comprobante
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$tipoComprobantepptal="SELECT tc.comprobante_pptal , cn.numero "
        . "FROM gf_comprobante_cnt cn "
        . "LEFT JOIN gf_tipo_comprobante  tc ON cn.tipocomprobante = tc.id_unico  "
        . "WHERE cn.id_unico =".$_SESSION['idComprobanteI'];
$tipo = $mysqli->query($tipoComprobantepptal);
$tipo = mysqli_fetch_row($tipo);
$num = $tipo[1];
$ti=$tipo[0];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Consulta hecha por Erica Gonzalez, para obtener el comprobante presupuestal
$par = $_SESSION['anno'];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pptal = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = '$num' AND tipocomprobante = '$ti'";
$pptal = $mysqli->query($pptal);
if(mysqli_num_rows($pptal)>0) {
  $pptal = mysqli_fetch_row($pptal);
  $pptal = $pptal[0];
} else {
  $in="INSERT INTO gf_comprobante_pptal (numero, fecha, descripcion, numerocontrato, parametrizacionanno, clasecontrato, tipocomprobante, tercero, estado, responsable) 
  VALUES ('$num','$fechaD', $descripcion , $numcontra, '$par',$clasec,  '$ti', $terceroCom, '3', '2')";
  $in = $mysqli->query($in);
  $pptal = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = '$num' AND tipocomprobante = '$ti'";
  $pptal = $mysqli->query($pptal);
  $pptal = mysqli_fetch_row($pptal);
  $pptal = $pptal[0];
}


 $sql = "insert into gf_detalle_comprobante_pptal(descripcion,valor,comprobantepptal,rubrofuente,conceptoRubro,tercero,proyecto,comprobanteafectado) values
($descripcion,$valorDP,$pptal,$rubro,$conceptoRubro,$terceroCom,$proyecto,NULL)";
$rs = $mysqli->query($sql);
#Ingreso de detalle comprobante contable
 $sqlD = "select max(id_unico) from gf_detalle_comprobante_pptal WHERE comprobantepptal =$pptal";
$result = $mysqli->query($sqlD);
$detallePP = mysqli_fetch_row($result);
 $sqli = "INSERT INTO gf_detalle_comprobante(fecha,descripcion,valor,valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto,detalleafectado,detallecomprobantepptal) 
        VALUES ('$fechaD',$descripcion,$valor,$valorEjec,$comprobante,$cuenta,$natural,$terceroCom,$proyecto,$centroCosto,$detalleA,$detallePP[0])";
$rs = $mysqli->query($sqli);
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
<!--Modal para informar al usuario que se ha registrado-->
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
  <!--Modal para informar al usuario que no se ha podido registrar la informacion-->
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
<!--Links para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($rs==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.history.go(-1);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
    $("#myModal2").modal('show');
  //window.history.go(-1);
</script>
<?php } ?>