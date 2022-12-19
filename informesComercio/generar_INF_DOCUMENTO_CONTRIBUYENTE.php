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
            
          $this->Image('../'.$ruta,23,8,20);
        }
    $this->SetX(23);
    $this->Cell(190,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(23);
    $this->Cell(190, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(23);
    $this->Cell(190,5,utf8_decode('LISTADO DOCUMENTO CONTRIBUYENTE'),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(23);
    $this->SetFont('Arial','B',8);
    $this->Cell(105,9,utf8_decode('CONTRIBUYENTE'),1,0,'C');
    $this->Cell(65,9,utf8_decode('TIPO DOCUMENTO'),1,0,'C');
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

$sql="
SELECT dc.id_unico,
IF(CONCAT_WS(' ',
t.nombreuno,
t.nombredos,
t.apellidouno,
t.apellidodos) 
IS NULL OR CONCAT_WS(' ',
t.nombreuno,
t.nombredos,
t.apellidouno,
t.apellidodos) = '',
(t.razonsocial),
CONCAT_WS(' ',
t.nombreuno,
t.nombredos,
t.apellidouno,
t.apellidodos)) AS NOMBRETERCERO,
tda.descripcion,
cd.nombre,
dc.ruta

FROM gc_documento_contribuyente  dc
LEFT JOIN gc_contribuyente c ON c.id_unico=dc.contribuyente
LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
LEFT JOIN gc_tipo_documento_adjunto tda ON tda.id_unico=dc.tipo_doc
LEFT JOIN gc_clase_documento cd ON cd.id_unico=tda.clase_doc";

$resultado=$mysqli->query($sql);

 while($row=mysqli_fetch_row($resultado)){


            $pdf->SetX(23);
            
            //llenar datos
            $pdf->SetFont('Arial','',8.3);

       
            //multicelda
            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(105,4,utf8_decode(ucwords(mb_strtolower($row[1]))),0,'L');
            $y22 = $pdf->GetY();
            $h1 = $y22-$y2;
            $px2 = $x2+105;

            if($numpaginas>$paginactual){
                $pdf->SetXY($px2,$yp);
                $h1=$y22-$yp;
            } else {
                $pdf->SetXY($px2,$y2);
            }

            $pdf->Cell(65,4,utf8_decode($row[2]."-".$row[3]),0,0,'C');


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
$pdf->Output(0,'Listado Documento Contribuyente('.date('d/m/Y').').pdf',0);
