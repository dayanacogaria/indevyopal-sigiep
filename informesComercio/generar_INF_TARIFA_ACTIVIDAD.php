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
    $this->Cell(190,5,utf8_decode('LISTADO TARIFA ACTIVIDAD'),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(23);
    $this->SetFont('Arial','B',8);
    $this->Cell(56.6,9,utf8_decode('ACTIVIDAD COMERCIAL'),1,0,'C');
    $this->Cell(56.6,9,utf8_decode('TARIFA'),1,0,'C');
    $this->Cell(56.6,9,utf8_decode('REGIMEN'),1,0,'C');
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

$sql = "SELECT ta.id_unico,
acom.cod_ciiu,
acom.descripcion,
tt.nombre,
t.valor,
t.periodo,
trg.nombre


FROM gc_tarifa_actividad ta
LEFT JOIN  gc_actividad_comercial acom ON acom.id_unico=ta.act_comer
LEFT JOIN gp_tarifa t ON t.id_unico=ta.tarifa
LEFT JOIN gp_tipo_tarifa tt ON tt.id_unico=t.tipo_tarifa
LEFT JOIN gf_tipo_regimen trg ON trg.id_unico=ta.regimen 

";

$resultado=$mysqli->query($sql);

 while($row=mysqli_fetch_row($resultado)){


            $pdf->SetX(23);
            
            //llenar datos
            $pdf->SetFont('Arial','',8.3);

       


            $pdf->Cell(56.6,4,utf8_decode($row[1]."-".$row[2]),0,0,'L');
            $pdf->Cell(56.6,4,utf8_decode($row[3]."-".$row[4]."-".$row[5]),0,0,'L');
            $pdf->Cell(56.6,4,utf8_decode($row[6]),0,0,'C');



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
$pdf->Output(0,'Listado Tarifa Actividad('.date('d/m/Y').').pdf',0);
