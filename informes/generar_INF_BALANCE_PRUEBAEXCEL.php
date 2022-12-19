<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Balance_Prueba.xls");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
ob_start();
$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$parmanno       = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$anno           = anno($parmanno);
$mesI           = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$diaI           = '01';
$fechaInicial   = $anno.'-'.$mesI.'-'.$diaI;
$mesF           = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$diaF           = cal_days_in_month($calendario, $mesF, $anno); 
$fechaFinal     = $anno.'-'.$mesF.'-'.$diaF;
$fechaComparar  = $anno.'-'.'01-01';
$codigoI        = $mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF        = $mysqli->real_escape_string(''.$_POST['sltcodf'].'');
$bl             = generarBalance($anno, $parmanno, $fechaInicial, $fechaFinal, $codigoI, $codigoF, $compania, 1);


#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$meses  = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$month1 = $meses[(int)$mesI];
$month2 = $meses[(int)$mesF];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BALANCE PRUEBA</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="8" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/><?php 
                  echo 'BALANCE PRUEBA';
                  ?>
           <br/><?php echo 'Cuentas De '.$codigoI. ' A '.$codigoF?>
           <br/><?php echo 'Mes Inicial: '.$month1. ' - Mes Final: '.$month2?>
           
           <br/>&nbsp;
                 
             </strong>
    </th>
  </tr>
    <tr>
        <td rowspan="2"><center><strong>CÓDIGO</strong></center></td>
        <td rowspan="2"><center><strong>NOMBRE</strong></center></td>
        <td colspan="2"><center><strong>SALDO INICIAL</strong></center></td>
        <td colspan="2"><center><strong>VALOR</strong></center></td>
        <td colspan="2"><center><strong>SALDO FINAL</strong></center></td>
    
    </tr>
    <tr>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
    </tr>

 <?php   

#Consulta Cuentas
 #Consulta Cuentas
$hr ="";
if(!empty($_GET['digitos'])){
    $dig = $_GET['digitos'];
    $hr .=" WHERE LENGTH(tem.numero_cuenta)<=$dig  ";
}
$sql3 = "SELECT DISTINCT 
                tem.numero_cuenta           as numcuen, 
                tem.nombre                  as cnom,
                tem.saldo_inicial_debito    as salinid,
                tem.saldo_inicial_credito   as salinic,
                tem.debito                  as deb,
                tem.credito                 as cred,
                tem.nuevo_saldo_debito      as nsald,
                tem.nuevo_saldo_credito     as nsalc , 
                tem.naturaleza              as naturaleza, 
                cta.cuentapuente            as cpuente 
from temporal_balance$compania tem
LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta 
$hr 
ORDER BY tem.numero_cuenta ASC";

$ccuentas = $mysqli->query($sql3);

$sald = 0;
$debit = 0;
$credit = 0;
$nsald = 0;

$saldd = 0;
$saldc = 0;
$debit = 0;
$credit = 0;
$nsaldd = 0;
$nsaldc = 0;

while ($filactas = mysqli_fetch_array($ccuentas)) 
{
       $saldd      = (float)($filactas['salinid']);
        $saldc      = (float)($filactas['salinic']);
        $debit      = (float)($filactas['deb']);
        $credit     = (float)($filactas['cred']);
        $nsaldd     = (float)($filactas['nsald']);
        $nsaldc     = (float)($filactas['nsalc']);
        $codi_cuenta =$filactas['numcuen']; 
        $naturaleza = $filactas['naturaleza']; 
        
        if(strlen($codi_cuenta)<=1){
          
            
            if($naturaleza ==1) {
                $totalsI = $saldd -$saldc;
                if($totalsI>0){
                  $salddebito  =$totalsI;
                  $saldcredito =0;
                } else {
                    $salddebito  =0;
                    $saldcredito =$totalsI*-1;
                }
                $totalnS = $nsaldd-$nsaldc;
                if($totalnS>0){
                  $nsalddebito  =$totalnS;
                  $nsaldcredito =0;
                } else {
                    $nsalddebito  =0;
                    $nsaldcredito =$totalnS*-1;
                }
            }  else {
                $totalsI = $saldc -$saldd;
                if($totalsI>0){
                  $saldcredito  =$totalsI;
                  $salddebito =0;
                } else {
                    $saldcredito  =0;
                    $salddebito =$totalsI*-1;
                }
                $totalnS = $nsaldc-$nsaldd;
                if($totalnS>0){
                  $nsaldcredito  =$totalnS;
                  $nsalddebito =0;
                } else {
                    $nsaldcredito  =0;
                    $nsalddebito =$totalnS*-1;
                }
            
                
            }?>
            <tr>
                <td><?php echo $filactas['numcuen']; ?></td>
                <td><?php echo ucwords(mb_strtolower($filactas['cnom']));?></td>
                <td><?php echo number_format($salddebito,2,'.',',');?></td>
                <td><?php echo number_format($saldcredito,2,'.',',');?></td>
                <td><?php echo number_format($debit,2,'.',',');?></td>
                <td><?php echo number_format($credit,2,'.',',');?></td>
                <td><?php echo number_format($nsalddebito,2,'.',',');?></td>
                <td><?php echo number_format($nsaldcredito,2,'.',',');?></td>
             </tr>       
        <?php
        } else { 
          
            if($filactas['cpuente']==1){
        
            } else {
            if ($saldd == 0  && $saldc == 0  && $debit == 0  && $credit == 0 )
            {   
                #########si los hijos tienen saldo####
                $sh = "SELECT id_unico, 
                        SUM(IF(saldo_inicial_debito<0, saldo_inicial_debito*-1,saldo_inicial_debito))   as salID,
                        SUM(IF(saldo_inicial_credito<0, saldo_inicial_credito*-1,saldo_inicial_credito))   as salIC,
                        SUM(IF(debito<0, debito*-1,debito))   as debI, 
                        SUM(IF(credito<0, credito*-1,credito))   as credI,
                        SUM(IF(nuevo_saldo_debito<0, nuevo_saldo_debito*-1,nuevo_saldo_debito))   as salID , 
                        SUM(IF(nuevo_saldo_credito<0, nuevo_saldo_credito*-1,nuevo_saldo_credito))   as salIC  
                        FROM temporal_balance$compania 
                        WHERE saldo_inicial_debito IS NOT NULL AND saldo_inicial_credito IS NOT NULL 
                        AND debito IS NOT NULL AND credito IS NOT NULL 
                        AND nuevo_saldo_debito IS NOT NULL AND nuevo_saldo_credito IS NOT NULL
                        AND cod_predecesor = ".$filactas['numcuen']; 
                $sh = $mysqli->query($sh);
                if(mysqli_num_rows($sh)>0){
                    $rowc = mysqli_fetch_row($sh);
                    if ($rowc[1] == 0  && $rowc[2] == 0  && $rowc[3] == 0 && $rowc[4] == 0 && $rowc[5] == 0 && $rowc[6] == 0)
                    {   
                    } else {
                        if($naturaleza ==1) {
                             $totalsI = $saldd -$saldc;
                            if($totalsI>0){
                              $salddebito  =$totalsI;
                              $saldcredito =0;
                            } else {
                                $salddebito  =0;
                                $saldcredito =$totalsI*-1;
                            }
                            $totalnS = $nsaldd-$nsaldc;
                            if($totalnS>0){
                              $nsalddebito  =$totalnS;
                              $nsaldcredito =0;
                            } else {
                                $nsalddebito  =0;
                                $nsaldcredito =$totalnS*-1;
                            }
                        }  else {
                             $totalsI = $saldc -$saldd;
                            if($totalsI>0){
                              $saldcredito  =$totalsI;
                              $salddebito =0;
                            } else {
                                $saldcredito  =0;
                                $salddebito =$totalsI*-1;
                            }
                            $totalnS = $nsaldc-$nsaldd;
                            if($totalnS>0){
                              $nsaldcredito  =$totalnS;
                              $nsalddebito =0;
                            } else {
                                $nsaldcredito  =0;
                                $nsalddebito =$totalnS*-1;
                            }

                        }
                         if ($salddebito == 0  && $saldcredito == 0  && $debit == 0 && $credit == 0 && $nsalddebito == 0 && $nsaldcredito == 0)
                        {   
                        } else {
                        ?>
                        <tr>
                            <td><?php echo $filactas['numcuen']; ?></td>
                            <td><?php echo ucwords(mb_strtolower($filactas['cnom']));?></td>
                            <td><?php echo number_format($salddebito,2,'.',',');?></td>
                            <td><?php echo number_format($saldcredito,2,'.',',');?></td>
                            <td><?php echo number_format($debit,2,'.',',');?></td>
                            <td><?php echo number_format($credit,2,'.',',');?></td>
                            <td><?php echo number_format($nsalddebito,2,'.',',');?></td>
                            <td><?php echo number_format($nsaldcredito,2,'.',',');?></td>
                         </tr> 
                    <?php }
                    }
                } else {

                }

            } else {
                 if($naturaleza ==1) {
                    $totalsI = $saldd -$saldc;
                    if($totalsI>0){
                      $salddebito  =$totalsI;
                      $saldcredito =0;
                    } else {
                        $salddebito  =0;
                        $saldcredito =$totalsI*-1;
                    }
                    $totalnS = $nsaldd-$nsaldc;
                    if($totalnS>0){
                      $nsalddebito  =$totalnS;
                      $nsaldcredito =0;
                    } else {
                        $nsalddebito  =0;
                        $nsaldcredito =$totalnS*-1;
                    }
                }  else {
                    $totalsI = $saldc -$saldd;
                    if($totalsI>0){
                      $saldcredito  =$totalsI;
                      $salddebito =0;
                    } else {
                        $saldcredito  =0;
                        $salddebito =$totalsI*-1;
                    }
                    $totalnS = $nsaldc-$nsaldd;
                    if($totalnS>0){
                      $nsaldcredito  =$totalnS;
                      $nsalddebito =0;
                    } else {
                        $nsaldcredito  =0;
                        $nsalddebito =$totalnS*-1;
                    }


                }
                
                if ($salddebito == 0  && $saldcredito == 0  && $debit == 0 && $credit == 0 && $nsalddebito == 0 && $nsaldcredito == 0)
                {   
                } else {
                ?>
                        <tr>
                            <td><?php echo $filactas['numcuen']; ?></td>
                            <td><?php echo ucwords(mb_strtolower($filactas['cnom']));?></td>
                            <td><?php echo number_format($salddebito,2,'.',',');?></td>
                            <td><?php echo number_format($saldcredito,2,'.',',');?></td>
                            <td><?php echo number_format($debit,2,'.',',');?></td>
                            <td><?php echo number_format($credit,2,'.',',');?></td>
                            <td><?php echo number_format($nsalddebito,2,'.',',');?></td>
                            <td><?php echo number_format($nsaldcredito,2,'.',',');?></td>
                         </tr> 
                    <?php
                }
                    
               }
             
            }
        }
        
    }
    
    
 ############TOTALES##########################?>
 <tr>
        <td colspan="2"><center><strong>TOTALES</strong></center></td>
        <td><strong><?php echo number_format($bl["totalsaldoID"],2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($bl["totalsaldoIC"],2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($bl["totaldeb"],2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($bl["totalcred"],2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($bl["totalsaldoFD"],2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($bl["totalsaldoFC"],2,'.',',')?></strong></td>
</tr>
<?php 

 ##########################RESUMEN#################################################
 $rs = "SELECT DISTINCT id_unico as id, 
          numero_cuenta as codigo, 
          nombre as nombre, 
          saldo_inicial_debito as iniciald, 
          saldo_inicial_credito as inicialc, 
          debito as debito, 
          credito as credito, 
          nuevo_saldo_debito as nuevod,
          nuevo_saldo_credito as nuevoc, 
          naturaleza    as naturalezaR 
        FROM temporal_balance$compania  
        WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
 $rs = $mysqli->query($rs);
 
 ?>
    <tr>
        <td colspan="8"><center><strong>RESUMEN</strong></center></td>
    </tr>
    <tr>
        <td rowspan="2"><center><strong>CÓDIGO</strong></center></td>
        <td rowspan="2"><center><strong>NOMBRE</strong></center></td>
        <td colspan="2"><center><strong>SALDO INICIAL</strong></center></td>
        <td colspan="2"><center><strong>VALOR</strong></center></td>
        <td colspan="2"><center><strong>SALDO FINAL</strong></center></td>
    
    </tr>
    <tr>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
        <td><center><strong>DÉBITO</strong></center></td>
        <td><center><strong>CRÉDITO</strong></center></td>
    </tr>  
<?php 
$anteriortotald =0;
$anteriortotalc =0;
$debitototal=0;
$creditototal=0;
$nuevototald=0;
$nuevototalc=0;

 while ($row1 = mysqli_fetch_array($rs)) {
       $saldd   = (float)($row1['iniciald']);
        $saldc   = (float)($row1['inicialc']);
        $debit  = (float)($row1['debito']);
        $credit = (float)($row1['credito']);
        $nsaldd  = (float)($row1['nuevod']);
        $nsaldc  = (float)($row1['nuevoc']);
        $naturalezaR = $row1['naturalezaR'];
        
        if($naturalezaR ==1) {
            $totalsalID =$saldd-$saldc;
            if($totalsalID>0){
                $salddebR = $totalsalID;
                $saldcredR =0;
            } else {
                $salddebR =0;
                $saldcredR =$totalsalID*-1;
            }

            $totalsalIN =$nsaldd-$nsaldc;
            if($totalsalIN>0){
                $nsalddR = $totalsalIN;
                $nsaldcR =0;
            } else {
                $nsalddR =0;
                $nsaldcR =$totalsalIN*-1;
            }
        } else {
            $totalsalID =$saldc-$saldd;
            if($totalsalID>0){
                $saldcredR = $totalsalID;
                $salddebR =0;
            } else {
                $saldcredR =0;
                $salddebR =$totalsalID*-1;
            }

            $totalsalIN =$nsaldc-$nsaldd;
            if($totalsalIN>0){
                $nsaldcR = $totalsalIN;
                $nsalddR =0;
            } else {
                $nsaldcR =0;
                $nsalddR =$totalsalIN*-1;
            }
        }
        ?>
        <tr>
            <td><?php echo $row1['codigo']; ?></td>
            <td><?php echo ucwords(mb_strtolower($row1['nombre']));?></td>
            <td><?php echo number_format($salddebR,2,'.',',');?></td>
            <td><?php echo number_format($saldcredR,2,'.',',');?></td>
            <td><?php echo number_format($debit,2,'.',',');?></td>
            <td><?php echo number_format($credit,2,'.',',');?></td>
            <td><?php echo number_format($nsalddR,2,'.',',');?></td>
            <td><?php echo number_format($nsaldcR,2,'.',',');?></td>
         </tr> 
        <?php
        
        $debitototal +=$debit;
        $creditototal +=$credit;
        switch ($row1['codigo']){
            case 1:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            case 2:
                $anteriortotald -=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald -=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            case 3:
                $anteriortotald -=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald -=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            case 4:
                $anteriortotald -=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald -=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            case 5:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            case 6:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            case 7:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            default :
                $anteriortotald +=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            
            
        }
        
        
 }
 ##################################################################################
 ##############################TOTALES#############################################
 ?>
 <tr>
        <td colspan="2" align="center"><center><strong>TOTALES</strong></center></td>
        <td><strong><?php echo number_format($anteriortotald,2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($anteriortotalc,2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($debitototal,2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($creditototal,2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($nuevototald,2,'.',',')?></strong></td>
        <td><strong><?php echo number_format($nuevototalc,2,'.',',')?></strong></td>
    </tr>
    
 </table>
</body>
</html>