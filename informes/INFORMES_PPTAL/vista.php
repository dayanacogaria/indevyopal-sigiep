<?php

require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 360);
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];

$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

    $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = $fila['traz'];       
        $tipodoc = $fila['tnom'];       
        $numdoc = $fila['tnum'];   
    }

$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anioh = $fecha_div[2];
$mesh = $fecha_div[1];
$diah = $fecha_div[0];
$hoy = $diah.'/'.$mesh.'/'.$anioh;

$numpaginas=0;
class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    global $numpaginas;
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    
    $numpaginas=$numpaginas+1;
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 10
    $this->SetFont('Arial','B',10);
    
        // Título
    $this->Cell(330,10,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(4);

    $this->SetFont('Arial','',10);
    $this->Cell(330,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(4);

    $this->SetFont('Arial','',8);
    $this->Cell(330,10,utf8_decode('CONFIGURACIÓN CONCEPTO'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(10);

    
    $this->SetFont('Arial','B',7);
    
    $this->SetX(10);
    
    $this->Cell(28,9,utf8_decode(''),1,0,'C');#
    $this->Cell(55,9,utf8_decode(''),1,0,'C');#
    $this->Cell(28,9,utf8_decode(''),1,0,'C');#
    $this->Cell(55,9,utf8_decode(''),1,0,'C');#
    $this->Cell(28,9,utf8_decode(''),1,0,'C');#
    $this->Cell(55,9,utf8_decode(''),1,0,'C');
    $this->Cell(28,9,utf8_decode(''),1,0,'C');
    $this->Cell(55,9,utf8_decode(''),1,0,'C');
    
    
        $this->SetX(10);
    
    $this->Cell(28,9,utf8_decode('CONCEPTO'),0,0,'C');#
    $this->Cell(55,7,utf8_decode('NOMBRE'),0,0,'C');#
    $this->Cell(28,7,utf8_decode('CÓDIGO'),0,0,'C');#
    $this->Cell(55,7,utf8_decode('NOMBRE'),0,0,'C');#
    $this->Cell(28,7,utf8_decode('CÓDIGO CUENTA'),0,0,'C');#
    $this->Cell(55,7,utf8_decode('NOMBRE CUENTA'),0,0,'C');#
    $this->Cell(28,7,utf8_decode('CÓDIGO CUENTA'),0,0,'C');#
    $this->Cell(55,7,utf8_decode('NOMBRE CUENTA'),0,0,'C');#
    
   
    $this->Ln(4);
    
    $this->SetX(10);
    
    $this->Cell(28,5,utf8_decode(''),0,0,'C');#
    $this->Cell(55,5,utf8_decode('CONCEPTO'),0,0,'C');#
    $this->Cell(28,5,utf8_decode('RUBRO'),0,0,'C');#
    $this->Cell(55,5,utf8_decode('RUBRO'),0,0,'C');
    $this->Cell(28,5,utf8_decode('DÉBITO'),0,0,'C');
    $this->Cell(55,5,utf8_decode('DÉBITO'),0,0,'C');
    $this->Cell(28,5,utf8_decode('CRÉDITO'),0,0,'C');#
    $this->Cell(55,5,utf8_decode('CRÉDITO'),0,0,'C');#
    

    
    $this->Ln(5);
    }
    // Pie de página
        function Footer()
            {
            // Posición: a 1,5 cm del final
            global $hoy;
            global $usuario;
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
    
 }

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$yp = $pdf->GetY();
$pdf->AliasNbPages();




#$pdf->SetY(33);
$pdf->SetFont('Arial','',7);
//Consulta SQL
$sql = "SELECT
  c.id_unico as id,
  c.nombre as nomC,
  rp.codi_presupuesto as codR,
  rp.nombre as nomR,
  cd.codi_cuenta as codCD,
  cd.nombre as nomCD,
  cc.codi_cuenta as codCC,
  cc.nombre as nomCC
FROM
  gf_concepto c
LEFT JOIN
  gf_concepto_rubro cr ON c.id_unico = cr.concepto
LEFT JOIN
  gf_rubro_pptal rp ON cr.rubro = rp.id_unico
LEFT JOIN
  gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cr.id_unico
LEFT JOIN
  gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
LEFT JOIN
  gf_cuenta cc ON cc.id_unico = crc.cuenta_credito WHERE c.id_unico <= '29' ORDER BY id ASC";
$cp = $mysqli->query($sql);
while ($fila = mysqli_fetch_array($cp)) 
        { 
    $paginactual = $numpaginas;
       if($fila['id']==''){
           $pdf->Cell(28,5,utf8_decode($fila['id']),0,0,'L');
       }else{
          $pdf->CellFitScale(28,5,utf8_decode($fila['id']),0,0,'L'); 
       }
       
       
        $y = $pdf->GetY();
        $x = $pdf->GetX();   
        $pdf->MultiCell(55,4,utf8_decode(ucwords(mb_strtolower($fila['nomC']))),0,'J');
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 55;
        if($numpaginas>$paginactual){
           $pdf->SetXY($px,$yp);
           $h=$y2-$yp;
        } else {
            $pdf->SetXY($px,$y);
        }
        
       if($fila['codR']==''){
           $pdf->Cell(28,5,utf8_decode($fila['codR']),0,0,'L');
       }else{
           $pdf->CellFitScale(28,5,utf8_decode($fila['codR']),0,0,'L');
       }
       
        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();        
        $pdf->MultiCell(55,4,utf8_decode(ucwords(mb_strtolower($fila['nomR']))),0,'L');
        $y21 = $pdf->GetY();
        $h1 = $y21-$y1;
        $px1 = $x1 + 55;
       
       if($numpaginas>$paginactual){
           $pdf->SetXY($px1,$yp);
           $h1=$y21-$yp;
        } else {
            $pdf->SetXY($px1,$y1);
        }
        
       if($fila['codCD']==''){
           $pdf->Cell(28,5,utf8_decode(ucwords(strtolower($fila['codCD']))),0,0,'L');
       }else{
          $pdf->CellFitScale(28,5,utf8_decode(ucwords(strtolower($fila['codCD']))),0,0,'L');
       }
        $y2 = $pdf->GetY();
        $x2 = $pdf->GetX();        
        $pdf->MultiCell(55,4,utf8_decode(ucwords(mb_strtolower($fila['nomCD']))),0,'L');
        $y22 = $pdf->GetY();
        $h2 = $y22-$y2;
        $px2 = $x2 + 55;
        
        if($numpaginas>$paginactual){
           $pdf->SetXY($px2,$yp);
           $h2=$y22-$yp;
        } else {
            $pdf->SetXY($px2,$y2);
        }
        
        
       if($fila['codCC']==''){
           $pdf->Cell(28,5,utf8_decode(ucwords(strtolower($fila['codCC']))),0,0,'L');
       }else{
           $pdf->CellFitScale(28,5,utf8_decode(ucwords(strtolower($fila['codCC']))),0,0,'L');
       }
       
        $y3 = $pdf->GetY();
        $x3 = $pdf->GetX();        
        $pdf->MultiCell(55,4,utf8_decode(ucwords(mb_strtolower($fila['nomCC']))),0,'L');
        $y23 = $pdf->GetY();
        $h3 = $y23-$y3;
        $px3 = $x3 + 55;
        if($numpaginas>$paginactual){
           $pdf->SetXY($px3,$yp);
           $h3=$y23-$yp;
        } else {
            $pdf->SetXY($px3,$y3);
        }
        //echo $numpaginas;
        
        $alt = max($h,$h1, $h2, $h3);
        $pdf->Ln($alt);
        
 }
 
 
ob_end_clean();
$pdf->Output(0,'Informe_Configuración_Concepto ('.date('d/m/Y').').pdf',0);

?>


