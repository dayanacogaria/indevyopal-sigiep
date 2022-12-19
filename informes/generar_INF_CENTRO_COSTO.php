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
// Cabecera de página  
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
    $this->Cell(270,10,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(270,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',10);
    $this->Cell(270,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(270,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(270,10,utf8_decode('LISTADO CENTROS DE COSTO'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(270,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(8);
    
    $this->SetFont('Arial','B',7);
    $this->Cell(5);
    $this->Cell(10,5,utf8_decode('Item'),1,0,'C');
    $this->Cell(65,5,utf8_decode('Nombre'),1,0,'C');
    $this->Cell(16,5,utf8_decode('Movimiento'),1,0,'C');
    $this->Cell(20,5,utf8_decode('Sigla'),1,0,'C');
    $this->Cell(45,5,utf8_decode('Tipo'),1,0,'C');
    $this->Cell(65,5,utf8_decode('Predecesor'),1,0,'C');
    $this->Cell(30,5,utf8_decode('Clase Servicio'),1,0,'C');
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
                $this->Cell(65,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                $this->Cell(65,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                $this->Cell(65,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Letter');        

$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);

//Consulta SQL
$sql = "SELECT 		cc.Id_Unico as ccid, 
                        cc.Nombre as ccnom, 
                        cc.Movimiento as ccmov, 
                        cc.Sigla as ccsig, 
                        tcc.Nombre as tcnom, 
                        (select Nombre from gf_centro_costo where Id_Unico = cc.Predecesor) Predecesor, 
                        cs.Nombre as csnom
  FROM 		gf_centro_costo cc 
  LEFT JOIN gf_tipo_centro_costo tcc 	ON cc.TipoCentroCosto = tcc.Id_Unico
  LEFT JOIN gf_clase_servicio cs 	ON cc.ClaseServicio = cs.Id_Unico";
$cp = $mysqli->query($sql);
$codd = 0;
$mov = "";
$pred = "";
#Márgen Izquierdo
$m=15;
#Definición de Anchos de celda
$mc0=10;
$mc1=65;
$mc2=16;
$mc3=20;
$mc4=45;
$mc5=65;
$mc6=30;

while ($fila = mysqli_fetch_array($cp)) 
        { 
         $pdf->SetX($m);
         $codd = $codd + 1;     
         #Celda 1
         $pdf->cellfitscale($mc0,5,utf8_decode($codd),0,0,'C');
         #Celda 2
         // Coordenadas Iniciales - Multicelda
         $y1 = $pdf->GetY();
         $x1 = $pdf->GetX();       
         //Trazado de MultiCell
         $pdf->MultiCell($mc1,5,utf8_decode($fila['ccnom']),0);
         // Posición en Y
         $y2 = $pdf->GetY();
         $alto_de_fila = $y2-$y1;
         // Posicion en X adicional al ancho de celdas anterior
         $posicionX = $x1 + $mc1;
         // Posicionamiento en el inicio de fila y columna siguiente
         $pdf->SetXY($posicionX,$y1); 
         
         switch($fila['ccmov'])
         {
          case 1:
          {
              $mov="SI";
              break;
          }
          case 2:
          {
              $mov="NO";
              break;
          }
         }
         #Celda 3
         $pdf->Cell($mc2,5,utf8_decode($mov),0,0,'C');         
         #Celda 4
         $pdf->Cell($mc3,5,utf8_decode($fila['ccsig']),0,0,'C');
         #Celda 5
         if($fila['tcnom']!="")
            $pdf->cellfitscale(45,5,utf8_decode($fila['tcnom']),0,0,'L');
         else
            $pdf->Cell($mc4,5,'',1,0,'L');
         #Celda 6         
         $pdf->MultiCell($mc5,5,utf8_decode($fila['Predecesor']),0,'L');
         // Posición en Y
         $y3 = $pdf->GetY();
         $alto2 = $y3-$y1;
         // Posicion en X adicional al ancho de celdas anterior
         $posicionX = $posicionX+$mc2+$mc3+$mc4+$mc5;
         // Posicionamiento en el inicio de fila y columna siguiente
         $pdf->SetXY($posicionX,$y1); 
         #Celda 7
         if($fila['csnom']!="")
            $pdf->cellfitscale($mc6,5,utf8_decode($fila['csnom']),0,0,'L');
         else
            $pdf->Cell($mc6,5,'',0,0,'L');
         #Obtención salto de línea mayor
         $ymax = max($alto_de_fila,$alto2);
         #Salto de línea basado en la comparación
         $pdf->Ln($ymax);
        }
        $pdf->Cell(5,0.5,'',0);
        $pdf->Cell(250,0.5,'',1);


ob_end_clean();
$pdf->Output(0,'Informe_Centro_Costo ('.date('d/m/Y').').pdf',0);

?>


