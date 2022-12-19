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
        $nomcomp = utf8_decode($fila['traz']);       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc = utf8_decode($fila['tnum']);   
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
    $this->Cell(330,10,utf8_decode('LISTADO TIPOS DE RETENCIONES'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(8);
    
    $this->SetFont('Arial','B',7);
    $this->Cell(8,9, utf8_decode('Item'),1,0,'C');
    $this->Cell(40,9,utf8_decode('Nombre'),1,0,'C');
    $this->Cell(8,9,utf8_decode(''),1,0,'C');
    $this->Cell(15,9,utf8_decode(''),1,0,'C');
    $this->Cell(10,9,utf8_decode(''),1,0,'C');
    $this->Cell(15,9,utf8_decode(''),1,0,'C');
    $this->Cell(15,9,utf8_decode(''),1,0,'C');
    $this->Cell(40,9,utf8_decode('Descripcion'),1,0,'C');
    $this->Cell(14,9,utf8_decode(''),1,0,'C');
    $this->Cell(13,9,utf8_decode(''),1,0,'C');
    $this->Cell(8,9,utf8_decode(''),1,0,'C');
    $this->Cell(36,9,utf8_decode('Clase Retención'),1,0,'C');
    $this->Cell(36,9,utf8_decode('Factor Aplicación'),1,0,'C');
    $this->Cell(36,9,utf8_decode(''),1,0,'C');
    $this->Cell(40,9,utf8_decode('Cuenta'),1,0,'C');
    
    $this->SetX(10);
    $this->Ln(1);
    
    $this->Cell(8,4, utf8_decode(' '),0,0,'C');
    $this->Cell(40,4,utf8_decode(' '),0,0,'C');
    $this->Cell(8,4,utf8_decode('%'),0,0,'C');
    $this->Cell(15,4,utf8_decode('Limite'),0,0,'C');
    $this->Cell(10,4,utf8_decode('%'),0,0,'C');
    $this->Cell(15,4,utf8_decode('Valor'),0,0,'C');
    $this->Cell(15,4,utf8_decode('Factor'),0,0,'C');
    $this->Cell(40,4,utf8_decode(''),0,0,'C');
    $this->Cell(14,4,utf8_decode('Modificar'),0,0,'C');
    $this->Cell(13,4,utf8_decode('Modificar'),0,0,'C');
    $this->Cell(8,4,utf8_decode('Ley'),0,0,'C');
    $this->Cell(36,4,utf8_decode(''),0,0,'C');
    $this->Cell(36,4,utf8_decode(''),0,0,'C');
    $this->Cell(36,4,utf8_decode('Tipo'),0,0,'C');
    $this->Cell(40,4,utf8_decode(''),0,0,'C');
    
    $this->Ln(3);
    
    $this->Cell(8,4, utf8_decode(' '),0,0,'C');
    $this->Cell(40,4,utf8_decode(' '),0,0,'C');
    $this->Cell(8,4,utf8_decode('Base'),0,0,'C');
    $this->Cell(15,4,utf8_decode('Inferior'),0,0,'C');
    $this->Cell(10,4,utf8_decode('Aplicar'),0,0,'C');
    $this->Cell(15,4,utf8_decode('Aplicar'),0,0,'C');
    $this->Cell(15,4,utf8_decode('Redondeo'),0,0,'C');
    $this->Cell(40,4,utf8_decode(''),0,0,'C');
    $this->Cell(14,4,utf8_decode('Retencion'),0,0,'C');
    $this->Cell(13,4,utf8_decode('Base'),0,0,'C');
    $this->Cell(8,4,utf8_decode('1450'),0,0,'C');
    $this->Cell(36,4,utf8_decode(''),0,0,'C');
    $this->Cell(36,4,utf8_decode(''),0,0,'C');
    $this->Cell(36,4,utf8_decode('Base'),0,0,'C');
    $this->Cell(40,4,utf8_decode(''),0,0,'C');
    
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
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',7);

#$pdf->SetY(33);

//Consulta SQL
$sql = "SELECT  
                                                tr.id_unico as trid, 
                                                tr.nombre as trnom, 
                                                tr.porcentajebase as trpb, 
                                                tr.limiteinferior as trlim, 
                                                tr.porcentajeaplicar as trapl, 
                                                tr.valoraplicar as trval, 
                                                tr.factorredondeo as trfred, 
                                                tr.descripcion as trdesc, 
                                                tr.modificarretencion as trmodr, 
                                                tr.modificarbase as trmodb, 
                                                tr.ley1450 as trley, 
                                                cr.id_unico as crid, 
                                                cr.nombre as crnom, 
                                                fa.id_unico as faid, 
                                                fa.nombre as fanom, 
                                                tb.id_unico as tbid, 
                                                tb.nombre as tbnom, 
                                                c.id_unico as cid, 
                                                c.codi_cuenta as ccod, 
                                                c.nombre cnom
                                                FROM gf_tipo_retencion tr
        LEFT JOIN gf_clase_retencion cr     ON tr.claseretencion=cr.id_unico 
        LEFT JOIN gf_factor_aplicacion fa   ON tr.factoraplicacion=fa.id_unico 
        LEFT JOIN gf_tipo_base tb           ON tr.tipobase = tb.id_unico
        LEFT JOIN gf_cuenta c               ON tr.cuenta= c.id_unico";
$cp      = $mysqli->query($sql);
$codd    = 0;
$modret  = "";
$modbas  = "";
$ley1450 = "";
$totales = 0;
$valorA = 0;


while ($fila = mysqli_fetch_array($cp)) 
        {
         $codd = $codd + 1;
        #############Celda 1############################# 
         $pdf->Cell(8,4,$codd,0,0,'C');
        #############Celda 2#############################
         // Coordenadas Iniciales - Multicelda
         $y1 = $pdf->GetY();
         $x1 = $pdf->GetX();
    
          // Trazado de celda multilinea.
         $pdf->MultiCell(40,4,utf8_decode(ucfirst(ucwords(strtoupper($fila['trnom'])))),0);
         #$pdf->cellfitscale(35,4,utf8_decode(ucfirst(ucwords(strtoupper($fila['trnom'])))),0,'L');        
        // Posición en Y
            $y2 = $pdf->GetY();
            $alto_de_fila = $y2-$y1;
        // Posicion en X adicional al ancho de celdas anterior
            $posicionX = $x1 + 40;
         // Posicionamiento en el inicio de fila y columna siguiente
            $pdf->SetXY($posicionX,$y1);
    
        #############Celda 3#############################
        if($fila['trpb']!="")
            $pdf->cellfitscale(8,4,utf8_decode($fila['trpb']),0,0,'C');
         else
            $pdf->Cell(8,4,"",0,'L');
         
        #############Celda 4#############################
        $lim = number_format($fila['trlim'], 2, '.', ',');
        if($fila['trlim']!="")
            $pdf->cellfitscale(15,4,$lim,0,0,'C');
         else
            $pdf->Cell(15,4,"",0,'L');
    
        #############Celda 5#############################
        if($fila['trapl']!="")
            $pdf->cellfitscale(10,4,utf8_decode($fila['trapl']),0,0,'C');
         else
            $pdf->Cell(10,4,"",0,'L');
         #############Celda 6#############################
        if($fila['trval']!="")
        {
            $valorA = number_format($fila['trval'], 2, '.', ',');
            $pdf->cellfitscale(15,4,utf8_decode($valorA),0,0,'R');
            $totales = $totales + $fila['trval'];
        }
         else
            $pdf->Cell(15,4,"",0,'L');
    
         #############Celda 7#############################
        if($fila['trfred']!="")
            $pdf->cellfitscale(15,4,utf8_decode($fila['trfred']),0,0,'C');
         else
            $pdf->Cell(15,4,"",0,'C');
    
         #############Celda 8#############################
        
         // Trazado de celda multilinea.
        $pdf->MultiCell(40,4,utf8_decode(strtoupper($fila['trdesc'])),0);                    
        // Posición en Y
            $y3 = $pdf->GetY();
            $altof2 = $y3-$y1;
        // Posicion en X adicional al ancho de celdas anterior
            $posicionX2 = $posicionX + 103;
        // Posicionamiento inicio celda siguiente
            $pdf->SetXY($posicionX2,$y1);
    
         switch($fila['trmodr'])
         {
          case 1:
          {
              $modret="SI";
              break;
          }
          case 2:
          {
              $modret="NO";
              break;
          }
         }
    
         #############Celda 9#############################
            $pdf->Cell(14,4,utf8_decode($modret),0,0,'C');  
         
         switch($fila['trmodb'])
         {
          case 1:
          {
              $modbas="SI";
              break;
          }
          case 2:
          {
              $modbas="NO";
              break;
          }
         }    
        
          
         #############Celda 10#############################
         $pdf->Cell(13,4,utf8_decode($modbas),0,0,'C');
         
         switch($fila['trley'])
         {
          case 1:
          {
              $ley1450="SI";              
              break;
          }
          case 2:
          {
              $ley1450="NO";
              break;
          }
         }    
         #############Celda 11############################# 
         $pdf->Cell(8,4,utf8_decode($ley1450),0,0,'C');
         
         #############Celda 12#############################
         // Trazado de la multicelda
         $pdf->MultiCell(36,4,utf8_decode($fila['crnom']),0);            
         // Posición en Y
            $y4 = $pdf->GetY();
            $altof3 = $y4-$y1;
         // Posición en X más ancho de filas posteriores a última multicelda
            $posicionX3 = $posicionX2 + 71;
        // Posicionamiento en el iniciod e la celda siguiente
            $pdf->SetXY($posicionX3,$y1);
         
         #############Celda 13#############################
         $pdf->MultiCell(36,4,utf8_decode($fila['fanom']),0);
            
        // Posición en Y
            $y5 = $pdf->GetY();
            $altof4 = $y5-$y1;
         // Posición en X más ancho de filas posteriores a última multicelda
            $posicionX4 = $posicionX3 + 36;
        // Posicionamiento en celda inmediatamente siguiente
            $pdf->SetXY($posicionX4,$y1);         
            
         #############Celda 14#############################
        $pdf->MultiCell(36,4,utf8_decode($fila['tbnom']),0);
            
        // Posición en Y y Obtención del alto inicial 
            $y6 = $pdf->GetY();
            $altof5 = $y6-$y1;
         // Posición anterior más ancho posterior a última multicelda
            $posicionX5 = $posicionX4 + 36;
        // Posicionamiento siguiente celda
            $pdf->SetXY($posicionX5,$y1);
    
         #############Celda 15#############################
         $pdf->MultiCell(40,4,utf8_decode(strtoupper($fila['ccod'].' - '.$fila['cnom'])),0,'L');
         //Alto Inicial
         $y7 = $pdf->GetY();         
         #Determinar registro anterior al ultimo salto de altura mayor
         $ymax = max($y1,$y2,$y3,$y4,$y5,$y6);    
         #Ubicar la posición en altura mas alta entre las anteriores y la última MultiCelll
         if($y7 > $ymax)
         {
             $dif=$y7-$ymax;
             $pdf->SetXY(10,$y7);           
         }
         else
         {
            $dif=$ymax-$y7;
            $pdf->SetXY(10,($ymax));
         }
        }
        
    $pdf->Ln(1);    
    $pdf->Cell(335,0.5,'',1);
         
ob_end_clean();
$pdf->Output(0,'Informe_Tipo_Retenciones ('.date('d/m/Y').').pdf',0);

?>


