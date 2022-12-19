<?php
require'../fpdf/fpdf.php';
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

#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)) ,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 

$meses  = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$month1 = $meses[(int)$mesI];
$month2 = $meses[(int)$mesF];
class PDF extends FPDF
{ 
    function Header(){

        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $month1;
        global $month2;
        global $anno;
        global $ruta_logo;


        $this->SetFont('Arial','B',10);
        $this->SetY(10);
        if($ruta_logo != '')
        {
          $this->Image('../'.$ruta_logo,60,6,20);
        }
        $this->Cell(330,5,utf8_decode($razonsocial),0,0,'C');
        $this->setX(10);
        $this->SetFont('Arial','B',8);
        $this->Cell(330,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
        $this->Ln(5);

        $this->SetFont('Arial','',8);
        $this->Cell(330, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(330,10,utf8_decode('VERSIÓN SGC'),0,0,'R');
        $this->Ln(5);

        $this->SetFont('Arial','',8);
        $this->Cell(330,5,utf8_decode('BALANCE PRUEBA'),0,0,'C');
        $this->SetFont('Arial','B',8);

        $this->SetX(10);
        $this->Cell(330,10,utf8_decode('FECHA SGC'),0,0,'R');
        $this->Ln(3);

        $this->SetFont('Arial','',7);
        $this->Cell(332,5,utf8_decode('Entre '.$month1.' y '.$month2.' de '.$anno),0,0,'C');
        $this->Ln(5);
    }   
    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(15);
        $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
        $this->Cell(70);
        $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(60);
        $this->Cell(30,10,utf8_decode(''),0); 
        $this->Cell(70);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}

$pdf = new PDF('L','mm','Legal');        
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);

$codd       = 0;
$totales    = 0;
$valorA     = 0;
$pdf->Cell(45,10, utf8_decode(''),1,0,'C');
$pdf->Cell(92,10,utf8_decode(''),1,0,'C');
$pdf->Cell(64,10,utf8_decode(''),1,0,'C');
$pdf->Cell(64,10,utf8_decode(''),1,0,'C');
$pdf->Cell(64,10,utf8_decode(''),1,0,'C');
$pdf->Setx(10);
$pdf->Cell(45,10, utf8_decode('Código'),0,0,'C');
$pdf->Cell(92,10,utf8_decode('Nombre'),0,0,'C');
$pdf->Cell(64,5,utf8_decode('Saldo Inicial'),0,0,'C');
$pdf->Cell(64,5,utf8_decode('Movimientos'),0,0,'C');
$pdf->Cell(64,5,utf8_decode('Saldo Final'),0,0,'C');
$pdf->Ln(5);
$pdf->Cell(45,5, utf8_decode(''),0,0,'C');
$pdf->Cell(92,5,utf8_decode(''),0,0,'C');
$pdf->Cell(32,5,utf8_decode('Débito'),1,0,'C');
$pdf->Cell(32,5,utf8_decode('Crédito'),1,0,'C');
$pdf->Cell(32,5,utf8_decode('Débito'),1,0,'C');
$pdf->Cell(32,5,utf8_decode('Crédito'),1,0,'C');
$pdf->Cell(32,5,utf8_decode('Débito'),1,0,'C');
$pdf->Cell(32,5,utf8_decode('Crédito'),1,0,'C');
$pdf->Ln(5);
$pdf->Cell(326,5,'',0);
#Variables de valor de naturaleza

$pdf->SetY(39);
$cnt = 0;

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

$saldd  = 0;
$saldc  = 0;
$debit  = 0;
$credit = 0;
$nsaldd = 0;
$nsaldc = 0;

while ($filactas = mysqli_fetch_array($ccuentas)) 
{
        $a = $pdf->GetY();
        if($a>185)
        {
            $pdf->AddPage();            
            $pdf->Cell(45,10, utf8_decode(''),1,0,'C');
            $pdf->Cell(92,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(64,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(64,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(64,10,utf8_decode(''),1,0,'C');
            $pdf->Setx(10);
            $pdf->Cell(45,10, utf8_decode('Código'),0,0,'C');
            $pdf->Cell(92,10,utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(64,5,utf8_decode('Saldo Inicial'),0,0,'C');
            $pdf->Cell(64,5,utf8_decode('Movimientos'),0,0,'C');
            $pdf->Cell(64,5,utf8_decode('Saldo Final'),0,0,'C');
            $pdf->Ln(5);
            $pdf->Cell(45,5, utf8_decode(''),0,0,'C');
            $pdf->Cell(92,5,utf8_decode(''),0,0,'C');
            $pdf->Cell(32,5,utf8_decode('Débito'),1,0,'C');
            $pdf->Cell(32,5,utf8_decode('Crédito'),1,0,'C');
            $pdf->Cell(32,5,utf8_decode('Débito'),1,0,'C');
            $pdf->Cell(32,5,utf8_decode('Crédito'),1,0,'C');
            $pdf->Cell(32,5,utf8_decode('Débito'),1,0,'C');
            $pdf->Cell(32,5,utf8_decode('Crédito'),1,0,'C');
            $pdf->Ln(5);
            $pdf->Cell(326,5,'',0);
            $pdf->Ln(2);
            
        }
        
        $saldd      = (float)($filactas['salinid']);
        $saldc      = (float)($filactas['salinic']);
        $debit      = (float)($filactas['deb']);
        $credit     = (float)($filactas['cred']);
        $nsaldd     = (float)($filactas['nsald']);
        $nsaldc     = (float)($filactas['nsalc']);
        $codi_cuenta =$filactas['numcuen']; 
        $naturaleza = $filactas['naturaleza']; 
        
        if(strlen($codi_cuenta)<=1){
            $pdf->Cell(45,4,utf8_decode($filactas['numcuen']),0,0,'R');   
            $y = $pdf->GetY();
            $x = $pdf->GetX();        
            $pdf->MultiCell(92,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
            $y2 = $pdf->GetY();
            $h = $y2-$y;
            $px = $x + 92;
            $pdf->Ln(-$h);
            $pdf->SetX($px);
            
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
            
            $pdf->Cell(32,4,number_format($salddebito,2,'.',','),0,0,'R');
            $pdf->Cell(32,4,number_format($saldcredito,2,'.',','),0,0,'R');
            $pdf->Cell(32,4,number_format($debit,2,'.',','),0,0,'R');
            $pdf->Cell(32,4,number_format($credit,2,'.',','),0,0,'R');
            $pdf->Cell(32,4,number_format($nsalddebito,2,'.',','),0,0,'R');
            $pdf->Cell(32,4,number_format($nsaldcredito,2,'.',','),0,0,'R');
            $pdf->Ln($h);
         } 
         else { 
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
                        $pdf->Cell(45,4,utf8_decode($filactas['numcuen']),0,0,'R');   
                        $y = $pdf->GetY();
                        $x = $pdf->GetX();        
                        $pdf->MultiCell(92,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
                        $y2 = $pdf->GetY();
                        $h = $y2-$y;
                        $px = $x + 92;
                        $pdf->Ln(-$h);
                        $pdf->SetX($px);
                       
                       
                        $pdf->Cell(32,4,number_format($salddebito,2,'.',','),0,0,'R');
                        $pdf->Cell(32,4,number_format($saldcredito,2,'.',','),0,0,'R');
                        $pdf->Cell(32,4,number_format($debit,2,'.',','),0,0,'R');
                        $pdf->Cell(32,4,number_format($credit,2,'.',','),0,0,'R');
                        $pdf->Cell(32,4,number_format($nsalddebito,2,'.',','),0,0,'R');
                        $pdf->Cell(32,4,number_format($nsaldcredito,2,'.',','),0,0,'R');
                        $pdf->Ln($h);
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
                
                
                $pdf->Cell(45,4,utf8_decode($filactas['numcuen']),0,0,'R');   
                $y = $pdf->GetY();
                $x = $pdf->GetX();        
                $pdf->MultiCell(92,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
                $y2 = $pdf->GetY();
                $h = $y2-$y;
                $px = $x + 92;
                $pdf->Ln(-$h);
                $pdf->SetX($px);
                
                
                $pdf->Cell(32,4,number_format($salddebito,2,'.',','),0,0,'R');
                $pdf->Cell(32,4,number_format($saldcredito,2,'.',','),0,0,'R');
                $pdf->Cell(32,4,number_format($debit,2,'.',','),0,0,'R');
                $pdf->Cell(32,4,number_format($credit,2,'.',','),0,0,'R');
                $pdf->Cell(32,4,number_format($nsalddebito,2,'.',','),0,0,'R');
                $pdf->Cell(32,4,number_format($nsaldcredito,2,'.',','),0,0,'R');
                $pdf->Ln($h);
                }
            }  
            }
        }
    }
############TOTALES##########################
$pdf->SetFont('Arial','B',8);   
$pdf->Cell(329,0.5,utf8_decode(''),1,0,'C'); 
$pdf->Ln(2);
$pdf->Cell(137,4,utf8_decode('TOTALES'),0,0,'R');
$pdf->Cell(32,4,number_format($bl["totalsaldoID"],2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($bl["totalsaldoIC"],2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($bl["totaldeb"],2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($bl["totalcred"],2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($bl["totalsaldoFD"],2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($bl["totalsaldoFC"],2,'.',','),0,0,'R');
$pdf->Ln(10);

$pdf->AddPage();
$pdf->Ln(3);
 ##########################RESUMEN#################################################
 $rs = "SELECT DISTINCT id_unico as id, 
          numero_cuenta         as codigo, 
          nombre                as nombre, 
          saldo_inicial_debito  as iniciald, 
          saldo_inicial_credito as inicialc, 
          debito                as debito, 
          credito               as credito, 
          nuevo_saldo_debito    as nuevod,
          nuevo_saldo_credito   as nuevoc, 
          naturaleza            as naturalezaR 
          
        FROM temporal_balance$compania  
        WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
 $rs = $mysqli->query($rs);
 
 $pdf->Cell(329,4,utf8_decode('RESUMEN'),1,0,'C'); 
 $pdf->Ln(4);
 $pdf->Cell(45,8,utf8_decode('CÓDIGO'),1,0,'C'); 
 $pdf->Cell(92,8,utf8_decode('NOMBRE'),1,0,'C'); 
 $pdf->Cell(64,4,utf8_decode('SALDO INICIAL'),1,0,'C');
 $pdf->Cell(64,4,utf8_decode('MOVIMIENTOS'),1,0,'C');
 $pdf->Cell(64,4,utf8_decode('SALDO FINAL'),1,0,'C');
 $pdf->ln(4);
 $pdf->Cell(45,4, utf8_decode(''),0,0,'C');
 $pdf->Cell(92,4,utf8_decode(''),0,0,'C');
 $pdf->Cell(32,4,utf8_decode('Débito'),1,0,'C');
 $pdf->Cell(32,4,utf8_decode('Crédito'),1,0,'C');
 $pdf->Cell(32,4,utf8_decode('Débito'),1,0,'C');
 $pdf->Cell(32,4,utf8_decode('Crédito'),1,0,'C');
 $pdf->Cell(32,4,utf8_decode('Débito'),1,0,'C');
 $pdf->Cell(32,4,utf8_decode('Crédito'),1,0,'C');
 $pdf->Ln(4);
$anteriortotald =0;
$anteriortotalc =0;
$debitototal=0;
$creditototal=0;
$nuevototald=0;
$nuevototalc=0;
$pdf->SetFont('Arial','',8);  

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
        $pdf->Cell(45,4,utf8_decode($row1['codigo']),0,0,'R');   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(92,4,utf8_decode(ucwords(mb_strtolower($row1['nombre']))),0,'L');   
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 92;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        $pdf->Cell(32,4,number_format($salddebR,2,'.',','),0,0,'R');
        $pdf->Cell(32,4,number_format($saldcredR,2,'.',','),0,0,'R');
        $pdf->Cell(32,4,number_format($debit,2,'.',','),0,0,'R');
        $pdf->Cell(32,4,number_format($credit,2,'.',','),0,0,'R');
        $pdf->Cell(32,4,number_format($nsalddR,2,'.',','),0,0,'R');
        $pdf->Cell(32,4,number_format($nsaldcR,2,'.',','),0,0,'R');
        $pdf->Ln($h);
        
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
$pdf->SetFont('Arial','B',8);   
$pdf->Cell(329,0.5,utf8_decode(''),1,0,'C'); 
$pdf->Ln(2);
$pdf->Cell(137,4,utf8_decode('TOTALES'),0,0,'C'); 
$pdf->Cell(32,4,number_format($anteriortotald,2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($anteriortotalc,2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($debitototal,2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($creditototal,2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($nuevototald,2,'.',','),0,0,'R');
$pdf->Cell(32,4,number_format($nuevototalc,2,'.',','),0,0,'R');
$pdf->Ln(2);




  ################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(30);
 
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='balance de prueba' AND td.compania = $compania ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
 if(mysqli_num_rows($res)>0){
     $h=4;
     while ($row2 = mysqli_fetch_row($res)) {
         $ter = "SELECT IF(CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) = '',
                    (tr.razonsocial),
                    CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos)) AS NOMBREC, "
                 . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                 . "FROM gf_tercero tr "
                 . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                 . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                 . "WHERE tr.id_unico ='$row2[0]'";
         
         $ter = $mysqli->query($ter);
         $ter = mysqli_fetch_row($ter);
         if(!empty($ter[3])){
                 $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
         } else {
             $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
         }
         
         $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');
        
         if($i==1){
           $pdf->Ln(15);
           $x=130;
           $i=0;
         } else {
         $pdf->Ln(-25);
         $pdf->SetX($x);
         $x=$x+110;
          $i=$i+1;
         }
        
     }
     
 } 
 ##################################################################################
       
 while (ob_get_length()) {
  ob_end_clean();
}
$pdf->Output(0,utf8_decode('Informe_Balance_Prueba('.date('d-m-Y').').pdf'),0);
?>