<?php
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonPptal/funcionesPptal.php';
require_once '../funciones/funcionLiquidador.php';
session_start();
$con         = new ConexionPDO();
$anno        = $_SESSION['anno'];
$responsable = $_SESSION['usuario_tercero'];
//Registrar Acuerdo de pago
$fecha      = fechaC($_REQUEST['sltFechaA']);
$tipo       = $_REQUEST['sltTipo'];
$nacuerdo   = $_REQUEST['txtNumeroA'];
$ncuotas    = $_REQUEST['txtNumeroC'];
$porcentaje = $_REQUEST['txtPorcentaje'];
$valor      = $_REQUEST['txtValor'];
$nc         = $ncuotas;
#* Fecha 

$f      = explode("/", $_REQUEST['sltFechaA']);                                        
$f      = "'".$f[2].'-'.$f[1].'-'.$f[0]."'";
$fcm    = explode("/", $_REQUEST['sltFechaA']); 
$fcom   = "".$fcm[2].'-'.$fcm[1].'-'.$fcm[0]."";
$f1     = explode("/", $_REQUEST['sltFechaA']);  
$f2     = $f1[2].'-'.$f1[1];
$fcint  = explode("/", $_REQUEST['sltFechaA']);  
$fci    = $fcint[2].'-'.$fcint[1];
$d      = $f1[0];
#**************
$sql = "INSERT INTO ga_acuerdo(fecha,tipo,nrocuotas,
    porcentaje_apl,valor,parametrizacion, consecutivo)
   VALUES ('$fecha',$tipo,$ncuotas,$porcentaje,$valor,$anno, $nacuerdo)";
$resultado = $mysqli->query($sql);

#********************
#* Buscar y crear conceptos si no exiten
$nanno = anno($anno);
#* COncepto Interés Acuerdo Predial
$rowc = $con->Listar("SELECT id_unico FROM gr_concepto_predial 
    WHERE gr_concepto_predial.anno='$nanno' AND gr_concepto_predial.id_concepto=10");
if(empty($rowc[0][0])){
    #* Registrarlo
    $sql_cpr = "INSERT INTO gr_concepto_predial(anno,id_concepto,formula,tipo,tarifa,
        concepto_financiero,tipo_predio,xvalor,aplicaInteresAcuerdo) 
        VALUES ($nanno,'10','0','1',NULL,NULL,NULL,'0','0')";
    $resultado = $mysqli->query($sql_cpr); 
    $rowc = $con->Listar("SELECT id_unico FROM gr_concepto_predial 
    WHERE gr_concepto_predial.anno='$nanno' AND gr_concepto_predial.id_concepto=10");
}
$c_interes = $rowc[0][0]; 
#* Concepto Mora Predial
$rowcm = $con->Listar("SELECT id_unico FROM gr_concepto_predial 
    WHERE gr_concepto_predial.anno='$nanno' AND gr_concepto_predial.id_concepto=11");
if(empty($rowc[0][0])){
    $sql_crec = "INSERT INTO gr_concepto_predial(anno,id_concepto,formula,tipo,tarifa,
        concepto_financiero,tipo_predio,xvalor,aplicaInteresAcuerdo) 
        VALUES ($nanno,'11','0','1',NULL,NULL,NULL,'0','0')";
    $rowcm = $con->Listar("SELECT id_unico FROM gr_concepto_predial 
    WHERE gr_concepto_predial.anno='$nanno' AND gr_concepto_predial.id_concepto=11");
}
$c_mora = $rowcm[0][0];

#********************
$sqla = "SELECT MAX(id_unico) FROM ga_acuerdo WHERE parametrizacion = $anno AND consecutivo = $nacuerdo";
$sqla = $mysqli->query($sqla);
$id_acd = mysqli_fetch_row($sqla);  

//Registrar Documento Acuerdo
$a_checks=$_REQUEST["codigos"];
$cd = explode(",", $a_checks);  

for ($i=0;$i<count($cd);$i++) {
    $df = $cd[$i];
    if(empty($df)){        
    } else {
        #Buscar Tipo Actualizar Estado * Insertar Detalles
        if($tipo==1){
            $sqle = "UPDATE gr_factura_predial fp 
            LEFT JOIN gp_predio1 p ON fp.predio = p.id_unico SET p.estado = 1 
            WHERE  fp.id_unico = $df";
            $upd = $mysqli->query($sqle);
            $bd = $con->Listar("SELECT numero FROM  gr_factura_predial WHERE id_unico = $df");
 
            #** Guardar Detalles
            $pc     = $porcentaje/100;
            $interes= pow((1+$pc),(1/365))-1;
            $rowd   = $con->Listar("SELECT DISTINCT dfp.concepto,
                SUM(dfp.valor)as vlor,
                SUM(dpp.valor)as vlor_p 
            FROM gr_factura_predial fp 
            LEFT JOIN gr_detalle_factura_predial dfp ON dfp.factura = fp.id_unico 
            LEFT JOIN gr_detalle_pago_predial dpp  ON dpp.detallefactura = dfp.id_unico
            WHERE fp.id_unico = $df GROUP BY dfp.concepto");
            for ($i = 0;$i < count($rowd);$i++) {
                $concepto = $rowd[$i][0];
                $vlpgo    = $rowd[$i][2];
                if(empty($vlpgo)){ $vlpgo = 0; }
                $total_concepto = $rowd[$i][1]-$vlpgo;
                $ttl_cuota      = ROUND($total_concepto/$nc,2);
                $ct             = 1;
                $fca            = $f2;
                while($ct <= $nc){
                    if($ct==1){
                        $fdt = explode("-", $f2);   
                    }else{
                        $fdt = explode("-", $fdt);  
                    }
                    
                    if($fdt[1]==12){
                        $m  =1;
                        $an =$fdt[0]+1;
                    }else{
                        $m  =$fdt[1]+1;
                        $an =$fdt[0];
                    }
                    $month  = "".$an.'-'.$m.""; 
                    $fdt    = $an.'-'.$m;      
                    $aux    = date('Y-m-d', strtotime("{$month} + 1 month"));
                    $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));
                    $last_d =explode("-", $last_day);
                    if($d <= $last_d[2]){
                        $fdtf  = "'".$an.'-'.$m.'-'.$d."'"; 
                    }else{
                         $fdtf = "'".$an.'-'.$m.'-'.$last_d[2]."'"; 
                    }
                    $cta    =   "'".$ct."'";
                    if($ttl_cuota!=0){
                        $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                        . "VALUES ($id_acd[0],$cta,$concepto,$fdtf,$ttl_cuota)";
                        $resultado = $mysqli->query($sql);            
                    }
                    $ct++;
                }
            }
            #* Intereses/Mora
            $ct     = 1;
            $fcint  = $fca;
            #* Total a calcular el interes acuerdo
            $sql = "SELECT DISTINCT 
                sum(dfp.valor)as vlor,
                sum(dpp.valor)as vlor_p          
                from gr_factura_predial fp 
                left join gr_detalle_factura_predial dfp on dfp.factura=fp.id_unico
                left join gr_detalle_pago_predial dpp on dpp.detallefactura=dfp.id_unico
                left join gr_concepto_predial c on c.id_unico=dfp.concepto
                where c.aplicaInteresAcuerdo='1' and fp.id_unico = $df";
            $resultado = $mysqli->query($sql);
            $tt_monto = mysqli_fetch_row($resultado); 
            $tt_pg  = $tt_monto[1];
            if(empty($tt_pg)){ $tt_pg  =0; }
            $Saldo_Anterior = $tt_monto[0]- $tt_pg;
            while($ct <= $nc){
                if($ct==1){ $fdt = explode("-", $fca);} else { $fdt = explode("-", $fdt); }                                       

                if($fdt[1]==12){
                    $m  = 1;
                    $an = $fdt[0]+1;
                }else{
                    $m  = $fdt[1]+1;
                    $an = $fdt[0];
                }
                $month      = "".$an."-".$m.""; 
                $fdt        = $an.'-'.$m;               
                $aux        = date('Y-m-d', strtotime("{$month} + 1 month"));
                $last_day   = date('Y-m-d', strtotime("{$aux} - 1 day"));
                $last_d     = explode("-", $last_day);
                if($d <= $last_d[2]){
                    $fdtf = "'".$an."-".$m."-".$d."'"; 
                }else{
                    $fdtf = "'".$an."-".$m."-".$last_d[2]."'"; 
                }
                $cta            ="'".$ct."'";
                $vlor_interes   = 0;
                if($ct==1){
                    $vl_in          = $interes * 30;
                    $vlor_interes   = $Saldo_Anterior * $vl_in;
                      $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                        . "VALUES ($id_acd[0],$cta,$c_interes,$fdtf,$vlor_interes)";

                      $resultado = $mysqli->query($sql);
                } else {
                    $sql = "select sum(da.valor)as vlr from ga_acuerdo a 
                        left join ga_detalle_acuerdo da on da.acuerdo=a.id_unico
                        left join gr_concepto_predial cp on cp.id_unico=da.concepto_deuda
                        where a.id_unico='$id_acd[0]' and cp.aplicaInteresAcuerdo='1' and da.nrocuota=1";
                    $resulta = $mysqli->query($sql);
                    while($rowsCP = mysqli_fetch_row($resulta)){
                        $tot_ct         = $rowsCP[0];
                        $Saldo_Anterior = $Saldo_Anterior - $tot_ct;

                        $vl_in          = $interes * 30;
                        $vlor_interes   = $Saldo_Anterior * $vl_in;
                        $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                          . "VALUES ($id_acd[0],$cta,$c_interes,$fdtf,$vlor_interes)";
                        $resultado = $mysqli->query($sql);
                    }

                }     
                $ct++;
            }

        } else {
            $sqle = "UPDATE gc_declaracion dc
            LEFT JOIN gc_contribuyente c ON dc.contribuyente = c.id_unico 
            SET c.estado = 30 
            WHERE  d.id_unico =  $df";
            $upd = $mysqli->query($sqle);
            $bd = $con->Listar("SELECT cod_dec FROM gc_declaracion WHERE id_unico = $df");
            //si es 2 es comercio
            $pc         =   $porcentaje/100;
            $interes    = pow((1+$pc),(1/365))-1;
            $sql="select DISTINCT dd.concepto, sum(dd.valor)as vlor, 
                cc.tipo_ope, sum(dr.valor) as vlr_p  
            FROM gc_declaracion d 
            LEFT JOIN gc_detalle_declaracion dd on dd.declaracion=d.id_unico
            LEFT JOIN gc_detalle_recaudo dr on dr.det_dec=dd.id_unico
            LEFT JOIN gc_concepto_comercial cc on cc.id_unico=dd.concepto
            WHERE d.id_unico = $df and dd.tipo_det=1 and cc.tipo_ope IN(2,3) group by dd.concepto";            
            $resul  = $mysqli->query($sql);
            $fe     = 0;
            while($rows = mysqli_fetch_row($resultado)){
                $sql_dec="SELECT fecha, (sum(dd.valor)-sum(dr.valor)) as vlr 
                FROM gc_declaracion d 
                left join gc_detalle_declaracion dd on dd.declaracion=d.id_unico
                left join gc_detalle_recaudo dr on dr.det_dec=dd.id_unico
                left join gc_concepto_comercial cc on cc.id_unico=dd.concepto 
                where d.id_unico = $df and cc.apli_inte=1 and dd.tipo_det=1 ";
                $re     = $mysqli->query($sql_dec);
                $ffc    = mysqli_fetch_row($re); 
                $fe     = $ffc[0];
                $vlrd   = $ffc[1];
                $vlrfaltanteint=0;
                if($fe<$fcom){
                    $vlrfaltanteint= Liquidador::liquidar_intereses2($vlrd, $fe, $fcom);
                }       
            }
            $sqlidinte      = "SELECT * FROM gc_concepto_comercial WHERE tipo=3 and tipo_ope=2";
            $rld            = $mysqli->query($sqlidinte);
            $conpinteres    = mysqli_fetch_row($rld); 
            $interes_mora   = FALSE;
            while($rowsC = mysqli_fetch_row($resul)){
                $concepto  ="'".$rowsC[0]."'";
                if($rowsC[0]   == $conpinteres[0]){
                    $vlr_pg    =   $rowsC[3];
                    if(empty($vlr_pg)){ $vlr_pg=0; }
                    $total_concepto = ($rowsC[1]-$vlr_pg)+$vlrfaltanteint;
                    $interes_mora   = TRUE;
                }else{
                    $vlr_pg = $rowsC[3];
                    if(empty($vlr_pg)){ $vlr_pg=0; }
                    $total_concepto =($rowsC[1]-$vlr_pg);
                }
                $tipop=$rowsC[2];
                if($tipop==2){
                    if($rowsC[0]==$conpinteres[0]){
                        $vlr_pg = $rowsC[3];
                        if(empty($vlr_pg)){ $vlr_pg = 0;}
                        $total_concepto = ($rowsC[1]-$vlr_pg)+$vlrfaltanteint;
                        $interes_mora   = TRUE;
                    }ELSE {
                        $vlr_pg = $rowsC[3];
                        if(empty($vlr_pg)){ $vlr_pg=0; }
                        $total_concepto = ($rowsC[1]-$vlr_pg);
                    }
                }else {
                    if($rowsC[0]==$conpinteres[0]){
                        $vlr_pg=$rowsC[3];
                        if(empty($vlr_pg)){$vlr_pg=0;}
                        $total_concepto = (($rowsC[1]-$vlr_pg)+$vlrfaltanteint)*(-1);
                        $interes_mora   = TRUE;
                    }ELSE{
                        $vlr_pg = $rowsC[3];
                        if(empty($vlr_pg)){ $vlr_pg=0; }
                        $total_concepto =($rowsC[1]-$vlr_pg)*(-1);    
                    }
                }
                $ttl_cuota = $total_concepto/$nc;
                $ttl_cuota = "'".$ttl_cuota."'";
                $ct        = 1;
                $fca       = $f2;
                while($ct<=$nc){
                    if($ct==1){ $fdt = explode("-", $f2);}else{ $fdt = explode("-", $fdt);  }
                    if($fdt[1]==12){
                        $m  = 1;
                        $an = $fdt[0]+1;
                    }else{
                        $m  = $fdt[1]+1;
                        $an = $fdt[0];
                    }
                    $month      = "".$an.'-'.$m.""; 
                    $fdt        = $an.'-'.$m;      
                    $aux        = date('Y-m-d', strtotime("{$month} + 1 month"));
                    $last_day   = date('Y-m-d', strtotime("{$aux} - 1 day"));
                    $last_d     = explode("-", $last_day);
                    if($d<=$last_d[2]){
                         $fdtf = "'".$an.'-'.$m.'-'.$d."'"; 
                    }else{
                         $fdtf = "'".$an.'-'.$m.'-'.$last_d[2]."'"; 
                    }
                    $cta="'".$ct."'";
                     $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                    . "VALUES ($id_acd[0],$cta,$concepto,$fdtf,$ttl_cuota)";
                    $resultado = $mysqli->query($sql);            
                    $ct++;
                }
            }
            if($interes_mora==FALSE && $vlrfaltanteint>0){
                $concepto   = $conpinteres[0];
                $ttl_cuota  = $vlrfaltanteint/$nc;
                $ttl_cuota  = "'".$ttl_cuota."'";
                $ct         = 1;
                $fca        = $fci;
                while($ct <= $nc){
                    if($ct==1){$fdt = explode("-", $fci);}else{$fdt = explode("-", $fdt);}
                    if($fdt[1]==12){
                        $m  = 1;
                        $an = $fdt[0]+1;
                    }else{
                        $m  = $fdt[1]+1;
                        $an = $fdt[0];
                    }
                    $month      = "".$an.'-'.$m.""; 
                    $fdt        = $an.'-'.$m;      
                    $aux        = date('Y-m-d', strtotime("{$month} + 1 month"));
                    $last_day   = date('Y-m-d', strtotime("{$aux} - 1 day"));
                    $last_d     = explode("-", $last_day);
                    if($d<=$last_d[2]){
                         $fdtf = "'".$an.'-'.$m.'-'.$d."'"; 
                    }else{
                         $fdtf = "'".$an.'-'.$m.'-'.$last_d[2]."'"; 
                    }
                    $cta="'".$ct."'";
                    $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                    . "VALUES ($id_acd[0],$cta,$concepto,$fdtf,$ttl_cuota)";
                    $resultado = $mysqli->query($sql);            
                    $ct++;
                }
            }
            $sql_cp     = "SELECT cc.id_unico from gc_concepto_comercial cc where cc.codigo=0 and cc.tipo=8";
            $resultado  = $mysqli->query($sql_cp);
            $idcp       = mysqli_fetch_row($resultado); 
            $sql_rec    = "SELECT cc.id_unico from gc_concepto_comercial cc where cc.codigo=1 and cc.tipo=8 ";
            $resultado  = $mysqli->query($sql_rec);
            $idrec      = mysqli_fetch_row($resultado); 
            $ct         = 1;
            $fcint      = $fca;
            //buscar el total a calcular el interes acuerdo
            $sql="select DISTINCT 
                    sum(dd.valor)as vlor from gc_declaracion d 
                    left join gc_detalle_declaracion dd on dd.declaracion=d.id_unico
                    left join gc_concepto_comercial cc on cc.id_unico=dd.concepto
                    where d.id_unico = $df and dd.tipo_det=1  and cc.aplicaInteresAcuerdo='1' ";       
            $resultado      = $mysqli->query($sql);
            $tt_monto       = mysqli_fetch_row($resultado); 
            $Saldo_Anterior = $tt_monto[0];
             while($ct <= $nc){
                if($ct==1){ $fdt = explode("-", $fca);}else{$fdt = explode("-", $fdt);  }                                       
                if($fdt[1]==12){
                    $m  = 1;
                    $an = $fdt[0]+1;
                }else{
                    $m  = $fdt[1]+1;
                    $an = $fdt[0];
                }
                $month      = "".$an."-".$m.""; 
                $fdt        = $an.'-'.$m;               
                $aux        = date('Y-m-d', strtotime("{$month} + 1 month"));
                $last_day   = date('Y-m-d', strtotime("{$aux} - 1 day"));
                $last_d     = explode("-", $last_day);
                if($d<=$last_d[2]){
                    $fdtf = "'".$an."-".$m."-".$d."'"; 
                }else{
                    $fdtf = "'".$an."-".$m."-".$last_d[2]."'"; 
                }
                $cta="'".$ct."'";
                $vlor_interes=0;
                if($ct==1){
                    $vl_in          = $interes * 30;
                    $vlor_interes   = $Saldo_Anterior * $vl_in;
                    $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                     . "VALUES ($id_acd[0],$cta,$idcp[0],$fdtf,$vlor_interes)";
                    $resultado = $mysqli->query($sql);
                 }else{
                    $sql="select sum(da.valor)as vlr from ga_acuerdo a 
                         left join ga_detalle_acuerdo da on da.acuerdo=a.id_unico
                         left join gc_concepto_comercial cc on cc.id_unico=da.concepto_deuda
                         where a.id_unico='$id_acd[0]'  and cc.aplicaInteresAcuerdo='1' and da.nrocuota=1";
                    $resulta = $mysqli->query($sql);
                    while($rowsCP = mysqli_fetch_row($resulta)){
                        $tot_ct         = $rowsCP[0];
                        $Saldo_Anterior = $Saldo_Anterior-$tot_ct;
                        $vl_in          = $interes * 30;
                        $vlor_interes   = $Saldo_Anterior * $vl_in;
                        $sql = "INSERT INTO ga_detalle_acuerdo(acuerdo,nrocuota,concepto_deuda,fecha,valor) "
                          . "VALUES ($id_acd[0],$cta,$idcp[0],$fdtf,$vlor_interes)";
                        $resultado = $mysqli->query($sql);
                    }
                }     
                $ct++;
            }   
        }
        $sql = "INSERT INTO ga_documento_acuerdo(acuerdo,soportedeuda) VALUES ($id_acd[0],".$bd[0][0].")";    
        $resultado = $mysqli->query($sql);
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
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<?php if($resultado==true){ ?>
<script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function(){
        $("#myModal1").modal('hide');    
        window.location='../ver_GA_ACUERDO.php?id=<?php echo md5($id_acd[0])?>';
    });
</script>
<?php } else { ?>
<script type="text/javascript">
    $("#myModal2").modal('show');
    $("#ver2").click(function(){
        $("#myModal2").modal('hide');      
        window.history.go(-1);
    });
</script>
<?php } ?>
