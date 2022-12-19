<?php
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
ob_start();
session_start();

$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$parmanno       = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$anno           = anno($parmanno);
$mesI           = '01';
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
        if($ruta_logo != ''){
             $this->Image('../'.$ruta_logo,20,6,20);
        }
        $this->Cell(170,5,($razonsocial),0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->Ln(5);
    
        $this->SetFont('Arial','',8);
        $this->Cell(170, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
        $this->SetFont('Arial','B',8);
        $this->Ln(5);

        $this->SetFont('Arial','',8);
        $this->Cell(170,5,utf8_decode('BALANCE GENERAL'),0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->Ln(3);
        $this->SetFont('Arial','',7);
        $this->Cell(170,5,utf8_decode('Entre '.$month1.' y '.$month2.' de '.$anno),0,0,'C');
        $this->Ln(7);
        $this->SetFont('Arial','B',8);
        $this->Cell(47,10, utf8_decode(''),1,0,'C');
        $this->Cell(94,10,utf8_decode(''),1,0,'C');
        $this->Cell(47,10,utf8_decode(''),1,0,'C');
        $this->Setx(10);
        $this->Cell(47,10, utf8_decode('Código'),0,0,'C');
        $this->Cell(94,10,utf8_decode('Nombre'),0,0,'C');
        $this->Cell(47,10,utf8_decode('Saldo Final'),0,0,'C');
        $this->Ln(5);
        $this->Cell(47,5, utf8_decode(''),0,0,'C');
        $this->Cell(94,5,utf8_decode(''),0,0,'C');
        $this->Cell(47,5,utf8_decode(''),0,0,'C');
        $this->Ln(5);
        $this->Cell(326,5,'',0);
        $this->Ln(1);
    }      
    
    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(20,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
        $this->Cell(25);
        $this->Cell(15,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(50);
        $this->Cell(30,10,utf8_decode(''),0); //.get_current_user()
        $this->Cell(25);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}

$pdf = new PDF();        
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);

$hr ="";
if(!empty($_GET['digitos'])){
    $dig = $_GET['digitos'];
    $hr .=" WHERE LENGTH(numero_cuenta)<=$dig  ";
}
$sql3 = "SELECT DISTINCT 
    numero_cuenta   as numcuen, 
    nombre          as cnom,
    nuevo_saldo     as nsal
from temporal_balance$compania 
$hr  ORDER BY numero_cuenta ASC";

$ccuentas = $mysqli->query($sql3);

$sald   = 0;
$debit  = 0;
$credit = 0;
$nsald  = 0;

while ($filactas = mysqli_fetch_array($ccuentas)) 
{
    $sald   = (float)($filactas['nsal']);

    if ($sald == 0)
    { } else {
    $pdf->Cell(47,4,utf8_decode($filactas['numcuen']),0,0,'R');
    $y = $pdf->GetY();
    $x = $pdf->GetX();        
    $pdf->MultiCell(94,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');
    $y2 = $pdf->GetY();
    $h = $y2-$y;
    $px = $x + 94;
    $pdf->Ln(-$h);
    $pdf->SetX($px);
    $pdf->Cell(47,4,number_format($sald,2,'.',','),0,0,'R');#number_format($xA,2,'.',',')
    $pdf->Ln($h);
    }
 }
$pdf->Cell(190,0.5,utf8_decode(''),1,0,'C');
$pdf->Ln(30);

  
  ################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(10);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='balance general' AND td.compania = $compania  ORDER BY rd.orden ASC";
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
  
  
while (ob_get_length()) {
  ob_end_clean();
}
 $pdf->Output(0,utf8_decode('Informe_Balance_General('.date('d-m-Y').').pdf'),0);
?>