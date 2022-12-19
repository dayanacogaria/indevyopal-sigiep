<?php

require_once('../Conexion/conexion.php');
session_start();

$id             = '"'.$mysqli->real_escape_string(''.$_POST['txtIdparametro'].'').'"';
$tipo_e    = ''.$mysqli->real_escape_string(''.$_POST['sltTipoEmpleado'].'').'';
$provision = ''.$mysqli->real_escape_string(''.$_POST['sltProvision'].'').'';

$salmin    = ''.$mysqli->real_escape_string(''.$_POST['txtsalmin'].'').'';
$auxtr     = ''.$mysqli->real_escape_string(''.$_POST['txtauxt'].'').'';
$primaA    = ''.$mysqli->real_escape_string(''.$_POST['txtprimaA'].'').'';
$primaM    = ''.$mysqli->real_escape_string(''.$_POST['txtprimaM'].'').'';
$asaempl   = ''.$mysqli->real_escape_string(''.$_POST['txtasempl'].'').'';
$asaempr   = ''.$mysqli->real_escape_string(''.$_POST['txtasempr'].'').'';
$apeempl   = ''.$mysqli->real_escape_string(''.$_POST['txtapempl'].'').'';
$apeempr   = ''.$mysqli->real_escape_string(''.$_POST['txtapempr'].'').'';
$fsol      = ''.$mysqli->real_escape_string(''.$_POST['txtafsol'].'').'';
$exret     = ''.$mysqli->real_escape_string(''.$_POST['txtextre'].'').'';
$acacom    = ''.$mysqli->real_escape_string(''.$_POST['txtacacom'].'').'';
$asena     = ''.$mysqli->real_escape_string(''.$_POST['txtasena'].'').'';
$aicbf     = ''.$mysqli->real_escape_string(''.$_POST['txtaicbf'].'').'';
$aesap     = ''.$mysqli->real_escape_string(''.$_POST['txtaesap'].'').'';
$amin      = ''.$mysqli->real_escape_string(''.$_POST['txtamin'].'').'';
$vuvt      = ''.$mysqli->real_escape_string(''.$_POST['txtvuvt'].'').'';
$talim     = ''.$mysqli->real_escape_string(''.$_POST['txttopal'].'').'';
$talimd    = ''.$mysqli->real_escape_string(''.$_POST['txttopald'].'').'';

$tauxt     = ''.$mysqli->real_escape_string(''.$_POST['slttoauxT'].'').'';

$incapa    = ''.$mysqli->real_escape_string(''.$_POST['txtinca'].'').'';
$rec_no    = ''.$mysqli->real_escape_string(''.$_POST['txtrecnoc'].'').'';
$rec_dom   = ''.$mysqli->real_escape_string(''.$_POST['txtrecdom'].'').'';
$hextdo    = ''.$mysqli->real_escape_string(''.$_POST['txthextdo'].'').'';
$hextddf   = ''.$mysqli->real_escape_string(''.$_POST['txthextdom'].'').'';
$hextno    = ''.$mysqli->real_escape_string(''.$_POST['txthextno'].'').'';
$hextndf   = ''.$mysqli->real_escape_string(''.$_POST['txthextndom'].'').'';

$hextnor   = ''.$mysqli->real_escape_string(''.$_POST['slthextnor'].'').'';

$redondeo  = ''.$mysqli->real_escape_string(''.$_POST['txtredondeo'].'').'';
$sldsena   = ''.$mysqli->real_escape_string(''.$_POST['txtsaludsena'].'').'';
$excento   = ''.$mysqli->real_escape_string(''.$_POST['excento'].'').'';
$diaspv    = ''.$mysqli->real_escape_string(''.$_POST['diaspv'].'').'';
$diasbr    = ''.$mysqli->real_escape_string(''.$_POST['diasbr'].'').'';
$limitbon  = ''.$mysqli->real_escape_string(''.$_POST['txtlimitbon'].'').'';

$salmin = str_replace(',', '', $salmin);
$auxtr  = str_replace(',', '', $auxtr);
$primaA = str_replace(',', '', $primaA);
$primaM = str_replace(',', '', $primaM);
$vuvt   = str_replace(',', '', $vuvt);
$talim  = str_replace(',', '', $talim);
$talimd = str_replace(',', '', $talimd);
$talimd = str_replace(',', '', $talimd);
$tauxt  = str_replace(',', '', $tauxt);

#Validar los vacios a 0 
$tipo_e    = empty($tipo_e) ? 'NULL' : $tipo_e;
$provision = empty($provision) ? 'NULL' : $provision;
$salmin    = empty($salmin  ) ? 0 : $salmin;
$auxtr     = empty($auxtr   ) ? 0 : $auxtr;  
$primaA    = empty($primaA  ) ? 0 : $primaA;  
$primaM    = empty($primaM  ) ? 0 : $primaM; 
$asaempl   = empty($asaempl ) ? 0 : $asaempl; 
$asaempr   = empty($asaempr ) ? 0 : $asaempr;
$apeempl   = empty($apeempl ) ? 0 : $apeempl;
$apeempr   = empty($apeempr ) ? 0 : $apeempr;
$fsol      = empty($fsol    ) ? 0 : $fsol;
$exret     = empty($exret   ) ? 0 : $exret;   
$acacom    = empty($acacom  ) ? 0 : $acacom;  
$asena     = empty($asena   ) ? 0 : $asena;
$aicbf     = empty($aicbf   ) ? 0 : $aicbf;  
$aesap     = empty($aesap   ) ? 0 : $aesap;  
$amin      = empty($amin    ) ? 0 : $amin;  
$vuvt      = empty($vuvt    ) ? 0 : $vuvt;   
$talim     = empty($talim   ) ? 0 : $talim;   
$talimd    = empty($talimd  ) ? 0 : $talimd;  
$tauxt     = empty($tauxt   ) ? 0 : $tauxt;  
$incapa    = empty($incapa  ) ? 0 : $incapa; 
$rec_no    = empty($rec_no ) ? 0 : $rec_no;
$rec_dom   = empty($rec_dom) ? 0 : $rec_dom;
$hextdo    = empty($hextdo ) ? 0 : $hextdo;
$hextddf   = empty($hextddf) ? 0 : $hextddf;
$hextno    = empty($hextno ) ? 0 : $hextno;
$hextndf   = empty($hextndf) ? 0 : $hextndf;
$hextnor   = empty($hextnor) ? 0 : $hextnor;
$redondeo  = empty($redondeo)? 0 : $redondeo;
$sldsena   = empty($sldsena) ? 0 : $sldsena;
$diaspv    = empty($diaspv) ? 0 : $diaspv;
$diasbr    = empty($diasbr) ? 0 : $diasbr;
$limitbon = str_replace(',', '', $limitbon);

  $insertSQL = " UPDATE gn_parametros_liquidacion 
 SET 
 salmin = $salmin,auxt =$auxtr, primaA =$primaA,
 primaM =$primaM, asaludemple =$asaempl, asaludempre =$asaempr,
 apensionemple =$apeempl,apensionempre =$apeempr,
 fodosol =$fsol,excentoret =$exret,acajacomp =$acacom,
 asena =$asena,aicbf =$aicbf,aesap =$aesap,aministerio =$amin,
 valoruvt =$vuvt,talimentacion =$talim,talimendoc =$talimd, 
 porce_inca = $incapa, excento = $excento,
 rec_noc = $rec_no, rec_dom = $rec_dom, hext_do = $hextdo,
 hext_ddf = $hextddf, hext_no = $hextno, hext_ndf = $hextndf,
 redondeo = $redondeo, saludsena = $sldsena,
 tipo_provision = $provision, tipo_empleado = $tipo_e,
 hora_extra_no = $hextnor, tope_aux_transporte=$tauxt , 
 dias_primav= $diaspv, dias_bon_recreacion= $diasbr , limite_bon_servicios= $limitbon 
 WHERE id_unico = $id"; 
  $resultado = $mysqli->query($insertSQL);




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
<!--Modal para informar al usuario que se ha modificado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Links para dar estilos a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GN_PARAMETRO_LIQUIDACION.php';
  });
</script>
<?php  }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver1").click(function(){
    $("#myModal2").modal('hide');
    window.location='../listar_GN_PARAMETRO_LIQUIDACION.php';
</script>
<?php } ?>
