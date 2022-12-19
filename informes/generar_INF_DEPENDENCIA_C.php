<?php

session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';

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

class PDF extends FPDF
{
// Cabecera de página Vertical  
function Header()
{ 
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 15
    $this->SetFont('Arial','B',10);
        // Título
    $this->Cell(190,10,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(200,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',10);
    $this->Cell(190,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(200,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(190,10,utf8_decode('LISTADO DEPENDENCIAS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(200,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(8);
    
    $this->SetFont('Arial','B',7);
    $this->Cell(10,9,utf8_decode('Item'),1,0,'C');
    $this->Cell(52,9,utf8_decode('Nombre'),1,0,'C');
    $this->Cell(10,9,utf8_decode('Sigla'),1,0,'C');
    $this->Cell(16,9,utf8_decode('Movimiento'),1,0,'C');
    $this->Cell(10,9,utf8_decode('Activa'),1,0,'C');
    $this->Cell(52,9,utf8_decode('Predecesor'),1,0,'C');
    $this->Cell(18,9,'',1);
    $this->Cell(23,9,'',1);
    
    $this->SetX(10);    
    
    $this->Cell(10,5,'',0);
    $this->Cell(52,5,'',0);
    $this->Cell(10,5,'',0);
    $this->Cell(16,5,'',0);
    $this->Cell(10,5,'',0);
    $this->Cell(52,5,'',0);
    $this->Cell(18,5,utf8_decode('Centro'),0,0,'C');
    $this->Cell(23,5,utf8_decode('Tipo'),0,0,'C');
    $this->Ln(4);    
    
    $this->Cell(10,4,'',0);
    $this->Cell(52,4,'',0);
    $this->Cell(10,4,'',0);
    $this->Cell(16,4,'',0);
    $this->Cell(10,4,'',0);
    $this->Cell(52,4,'',0);
    $this->Cell(18,4,utf8_decode('Costo'),0,0,'C');
    $this->Cell(23,4,utf8_decode('Dependencia'),0,0,'C');
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
    // Número de página
        $this->SetX(10);
        $this->Cell(25,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(68,10,utf8_decode('Máquina '.  gethostname()),0,0,'C');
        $this->Cell(68,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
        $this->Cell(29,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm','Letter');        

$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

//Consulta SQL
#$pdf->SetY(36);
$sql = "SELECT 		    d.id_unico as id,
                        d.nombre as dnom,
                        d.sigla as dsig,
                        d.movimiento as dmov,
                        d.activa as dact,
                        dep.id_unico as deid,	
                        dep.nombre as denom,
                        cc.id_unico as cid,
                        cc.nombre as cnom,
                        td.id_unico as tid,
                        td.nombre as tnom
        FROM gf_dependencia d
        LEFT JOIN gf_dependencia dep ON d.predecesor = dep.id_unico
        LEFT JOIN gf_centro_costo cc ON d.centrocosto = cc.id_unico
        LEFT JOIN gf_tipo_dependencia td ON d.tipodependencia = td.id_unico 
        WHERE d.compania = $compania";
$cp = $mysqli->query($sql);
$codd = 0;
$mov = "";
$act = "";
#Asignación de valores a las celdas que se usarán
$m = 10;
$mc0 = 10;
$mc1 = 52;
$mc2 = 10;
$mc3 = 16;
$mc4 = 10;
$mc5 = 52;
$mc6 = 18;
$mc7 = 23;

$pdf->SetFont('Arial','',8);
while ($fila = mysqli_fetch_array($cp)) 
        { 
         $codd = $codd + 1;
         #Celda 1
         $pdf->cellfitscale($mc0,5,utf8_decode($codd),0,0,'C');
         #Celda 2
         // Coordenadas Iniciales - Multicelda
         $y1 = $pdf->GetY();
         $x1 = $pdf->GetX();       
         //Trazado de MultiCell
         $pdf->MultiCell($mc1,5,utf8_decode($fila['dnom']),0);
         // Posición en Y
         $y2 = $pdf->GetY();
         $alto_de_fila = $y2-$y1;
         // Posicion en X adicional al ancho de celdas anterior
         $posicionX = $x1 + $mc1;
         // Posicionamiento en el inicio de fila y columna siguiente
         $pdf->SetXY($posicionX,$y1);                   
         #Celda 3
         if($fila['dsig']!="")             
            $pdf->cellfitscale($mc2,5,utf8_decode($fila['dsig']),0,0,'C');
         else
            $pdf->Cell($mc2,5,'',0,0,'C');
         if($fila['dmov']!="")
            $mov = "-";
         else{
         switch($fila['dmov'])
         {
          case 0:
          {
              $mov="SI";
              break;
          }
          case 1:
          {
              $mov="NO";
              break;
          }
         }
         }
         #Celda 4
         $pdf->Cell($mc3,5,utf8_decode($mov),0,0,'C');
         
         switch($fila['dact'])
         {
          case 0:
          {
              $act="SI";
              break;
          }
          case 1:
          {
              $act="NO";
              break;
          }
         }    
         $pdf->Cell($mc4,5,utf8_decode($act),0,0,'C');
         
         #Celda 5
         $pdf->MultiCell($mc5,5,utf8_decode($fila['denom']),0);
         // Posición en Y
         $y3 = $pdf->GetY();
         $alto2 = $y3-$y1;
         // Posicion en X adicional al ancho de celdas anterior
         $posicionX = $posicionX+$mc2+$mc3+$mc4+$mc5;
         // Posicionamiento en el inicio de fila y columna siguiente
         $pdf->SetXY($posicionX,$y1);
         #Celda 6
         if($fila['cnom']!="")
            $pdf->cellfitscale($mc6,5,utf8_decode($fila['cnom']),0,0,'L');
         else
            $pdf->cellfitscale($mc6,5,'',0,0,'L');
         #Celda 7
         if($fila['tnom']!="")
            $pdf->cellfitscale($mc7,5,utf8_decode($fila['tnom']),0,0,'C');
         else
            $pdf->cellfitscale($mc7,5,'',0,0,'C');
         
         $ymax = max($alto_de_fila,$alto2);
         $pdf->Ln($ymax);
     
        }
        $pdf->Cell(190,0.5,'',1);
        
        ob_end_clean();
$pdf->Output(0,'Informe_Dependencias ('.date('d/m/Y').').pdf',0);

?>
