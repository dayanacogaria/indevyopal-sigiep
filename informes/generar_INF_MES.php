<?php

header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);


##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
#CREACION PDF, HEAD AND FOOTER

class PDF extends FPDF
{
function Header()
{ 
    
    global $fecha1;
    global $fecha2;
    global $cuentaI;
    global $cuentaF;
    global $nombreCompania;
    global $nitcompania;
    global $numpaginas;
    global $ruta;
    global $mesNomn;
    $numpaginas=$this->PageNo();
    
    $this->SetFont('Arial','B',10);
    $this->SetY(10);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,8.2,8,20);
        }
    $this->SetX(8.2);
    $this->Cell(190,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(8.2);
    $this->Cell(190, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(8.2);
    $this->Cell(190,5,utf8_decode('LISTADO MES'),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(8.2);
    $this->SetFont('Arial','B',8);
    $this->Cell(50,9,utf8_decode('AÑO'),1,0,'C');
    $this->Cell(50,9,utf8_decode('NÚMERO'),1,0,'C');
    $this->Cell(50,9,utf8_decode('MES'),1,0,'C');
    $this->Cell(50,9,utf8_decode('ESTADO'),1,0,'C');

    /*$this->Cell(36,9,utf8_decode('SALDO EXTRACTO'),1,0,'C');
    $this->Cell(36,9,utf8_decode('DIFERENCIA'),1,0,'C');*/
    
    $this->Ln(9);
    
    
    }      
    
    function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    global $usuario;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    $this->SetX(15);
    $this->Cell(40,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(50,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(50,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(40,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('P','mm','Letter');   
$pdf->AddPage();
$pdf->AliasNbPages();
$yp=$pdf->GetY();


//CONSULTA
$sql="SELECT m.id_unico,pa.anno,m.numero,m.mes,em.nombre FROM gf_mes m 
LEFT JOIN gf_estado_mes em ON em.id_unico=m.estadomes 
LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=m.parametrizacionanno";

$resultado=$mysqli->query($sql);

while($row=mysqli_fetch_array($resultado)){

            //llenar datos
            $pdf->SetX(8.2);
            $pdf->SetFont('Arial','',8.3);
            $pdf->Cell(50,4,utf8_decode( $row['anno']),0,0,'C');

     

            $pdf->Cell(50,4,utf8_decode($row['numero']),0,0,'C');
       
            //multicelda
            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(50,4,utf8_decode(ucwords(mb_strtolower($row['mes'] ))),0,'L');
            $y22 = $pdf->GetY();
            $h1 = $y22-$y2;
            $px2 = $x2+50;

            if($numpaginas>$paginactual){
                $pdf->SetXY($px2,$yp);
                $h1=$y22-$yp;
            } else {
                $pdf->SetXY($px2,$y2);
            }

            //multicelda
            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(50,4,utf8_decode(ucwords(mb_strtolower($row['nombre'] ))),0,'L');
            $y22 = $pdf->GetY();
            $h1 = $y22-$y2;
            $px2 = $x2+50;

            if($numpaginas>$paginactual){
                $pdf->SetXY($px2,$yp);
                $h1=$y22-$yp;
            } else {
                $pdf->SetXY($px2,$y2);
            }

            //salto
            $alto = max($h,$h1);
            $pdf->Ln($alto);
            $paginactual=$numpaginas;


            $yal= $pdf->GetY();
            if($yal>250){
                $pdf->AddPage();
            }
    
}



while (ob_get_length()) {
  ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0,'Listado Mes ('.date('d/m/Y').').pdf',0);
