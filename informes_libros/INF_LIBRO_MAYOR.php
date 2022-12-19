<?php

require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
ob_start();
session_start();
$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$parmanno       = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$foliador       = $_POST['foliador'];
$annionom       = anno($parmanno);
$calendario     = CAL_GREGORIAN;
$anno           = $annionom; 
$mesI           = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$mesf           = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$diaI           = '01';
$fechaInicial   = $anno.'-'.$mesI.'-'.$diaI;
$diaF           = cal_days_in_month($calendario, $mesf, $anno); 
$fechaFinal     = $anno.'-'.$mesf.'-'.$diaF;
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


$usuario = $_SESSION['usuario'];
$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');

$mesnom  = mb_strtoupper($meses[$mesI]);
$mesnomF = mb_strtoupper($meses[$mesf]);


if($_REQUEST['tipo']==1){
    require'../fpdf/fpdf.php';
    class PDF extends FPDF
    {
        function Header()
        { 
        global $foliador;
        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $numpaginas;
        $numpaginas = $this->PageNo();
        global $tam;
        global $ruta;
        global $mesnom;
        global $mesnomF;
        global $annionom;
        if($foliador=='Si'){

            if ($ruta != '') {
                $this->Image('../'.$ruta,10,8,20);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetX(35);
            $this->MultiCell($tam, 5, utf8_decode($razonsocial), 0, 'C');
            $this->SetX(35);
            $this->Cell($tam, 5, $nombreIdent.':'.$numeroIdent, 0, 0, 'C');
            $this->Ln(4);
            $this->SetX(35);
            $this->Cell($tam, 5, utf8_decode('LIBRO MAYOR Y BALANCES'), 0, 0, 'C');
            $this->Ln(4);
            $this->SetX(35);
            $this->Cell($tam, 5, utf8_decode($mesnom.' A '.$mesnomF.' DE '.$annionom), 0, 0, 'C');
            $this->Ln(10);
        } else {
            $this->SetFont('Arial', 'B', 10);
            $this->Ln(15);
            $this->SetX(35);
            $this->Cell($tam, 5, utf8_decode($mesnom.' A '.$mesnomF.' DE '.$annionom), 0, 0, 'C');
            $this->Ln(10);
        }
    } 
    }
    
    $pdf = new PDF('L','mm','Legal');        
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',8);
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
    #Variables de valor de naturaleza
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
                $pdf->SetFont('Arial','B',8);
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
            $pdf->SetFont('Arial','',8);
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
    $pdf->Ln(3);    
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
    $pdf->Ln(3);

    ################################ ESTRUCTURA FIRMAS ##########################################
    ######### BUSQUEDA RESPONSABLE #########
     $pdf->SetFont('Arial','B',9);
     $pdf->Ln(30);

     $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
            LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
            LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
            LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
            WHERE LOWER(td.nombre) ='libro mayor' AND td.compania = $compania ORDER BY rd.orden ASC";
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
    $pdf->Output(0,utf8_decode('Libro_Mayor_balances('.date('d-m-Y').').pdf'),0);
} else { 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Libro_Mayor_Balance.xls");
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LIBRO MAYOR</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="8" align="center"><strong>
            <br/>&nbsp; 
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>LIBRO MAYOR Y BALANCES
           <br/><?php echo 'Cuentas De '.$codigoI. ' A '.$codigoF?>
           <br/><?php echo 'Mes Inicial: '.$mesnom. ' - Mes Final: '.$mesnomF?>
           
           <br/>&nbsp;
                 
             </strong>
                 
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
    
 </table>
</body>
</html>

<?php } ?>