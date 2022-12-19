<?php
#
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonPptal/funcionesPptal.php';
require_once '../funciones/funcionLiquidador.php';
session_start();
$con            = new ConexionPDO();
$anno           = $_SESSION['anno'];
$responsable    = $_SESSION['usuario_tercero'];
$f              = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
$fch            = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
$fchFac         = ''.$mysqli->real_escape_string(''.$_GET['sltFechaA'].'').'';
if(empty($f)){ } else {
    $f          = explode("/", $f);                                        
    $f          = "'".$f[2].'-'.$f[1].'-'.$f[0]."'";    
    $f1         = explode("/", $fch);  
    $f2         = $f1[2].'-'.$f1[1];
    $hoy        = date('Y-m-d');
    $f_fac      = explode("/", $fchFac);                                        
    $ffact      = "".$f_fac[2].'-'.$f_fac[1].'-'.$f_fac[0]."";
    $d          = $f1[0];
    
}
$nac    = ''.$mysqli->real_escape_string(''.$_GET['nacuerdo'].'').'';
$n      = ''.$mysqli->real_escape_string(''.$_GET['numero'].'').'';
$obs    = '"'.$mysqli->real_escape_string(''.$_GET['Observaciones'].'').'"';
$tp     = ''.$mysqli->real_escape_string(''.$_GET['tipo'].'').'';

$sql = "INSERT INTO ga_factura_acuerdo(fecha_ven,numero,observaciones,fecha_creacion,responsable) "
        . "VALUES ($f,$n,$obs,'$hoy',$responsable)";
$resultado = $mysqli->query($sql);

$queryTipoC = "SELECT max(id_unico) as acuerdo from ga_factura_acuerdo WHERE numero = $n";
$resultado = $mysqli->query($queryTipoC);
$id_fact = mysqli_fetch_row($resultado);   

$a_checks   =''.$mysqli->real_escape_string(''.$_GET["codigos"].'').'';
$cd         = explode(",", $a_checks);      
$fecha_venci= $ffact;     
for ($i=0;$i<count($cd);$i++) {
    $df         = $cd[$i];    
    $saldos_venc= 0;
    $vlr_rec    = 0;
    if(empty($df)){}else{        
        $sql_fec_cuota = "SELECT da.id_unico, 
            (SUM(da.valor) - IF(a.tipo=1, IF(SUM(dpp.valor) IS NULL, 0, SUM(dpp.valor)), IF(SUM(drc.valor) IS NULL, 0, SUM(dpp.valor)))) as Saldo, 
            da.concepto_deuda, da.fecha 
            FROM ga_acuerdo a 
            LEFT JOIN ga_detalle_acuerdo da ON a.id_unico = da.acuerdo 
            LEFT JOIN ga_detalle_factura df ON df.detalleacuerdo = da.id_unico 
            LEFT JOIN gr_detalle_pago_predial dpp ON df.iddetallerecaudo = dpp.id_unico 
            LEFT JOIN gc_detalle_recaudo drc ON df.iddetallerecaudo = drc.id_unico
            WHERE a.id_unico = $nac AND da.nrocuota='$df' 
            GROUP BY da.concepto_deuda, da.nrocuota 
            HAVING Saldo!=0";
        $res_cuo        = $mysqli->query($sql_fec_cuota);
        while($rows = mysqli_fetch_row($res_cuo)){
            $vlpac  = $rows[1];
            if(empty($vlpac)){$vlpac = 0;}
            $iddet  = $rows[0];
            $concp  = $rows[2];
            if($concp == $idrec[0]){
                $vlpac = $vlr_rec;
            }            
            $sql_det = "INSERT INTO ga_detalle_factura(factura,valor,detalleacuerdo) "
            . "VALUES ($id_fact[0],$vlpac,$iddet)";
            $resultado = $mysqli->query($sql_det);
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
  <!--Modal para informar al usuario que no se ha podido registrar -->
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
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php 

if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    
    window.location='../ver_GA_FACTURA_ACUERDO.php?id=<?php echo md5($id_fact[0])?>';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>
