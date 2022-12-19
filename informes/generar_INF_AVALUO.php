<?php

header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
##########RECEPCION VARIABLES###############
$mes = $_POST['mes'];
$annio = $_SESSION['anno'];

 $mesN ="SELECT m.mes, pa.anno , m.id_unico "
        . "FROM gf_mes m "
        . "LEFT JOIN gf_parametrizacion_anno pa "
        . "ON m.parametrizacionanno = pa.id_unico "
        . "WHERE m.parametrizacionanno = '$annio' "
        . "AND m.numero = '$mes'";
$mesN = $mysqli->query($mesN);
$mesN = mysqli_fetch_row($mesN);
$mesNomn = $mesN[0];
$annoP = $mesN[1];
$mesId= $mesN[2];

$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $mes, $annoP); 
$fechaF= $annoP.'/'.$mes.'/'.$diaF;
$fechaI = $annoP.'/'.$mes.'/01';

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
    $this->Cell(190,5,utf8_decode('LISTADO AVALÚO'),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(8.2);
    $this->SetFont('Arial','B',8);
    $this->Cell(30,9,utf8_decode('VALOR'),1,0,'C');
    $this->Cell(50,9,utf8_decode('INDICADOR'),1,0,'C');
    $this->Cell(60,9,utf8_decode('TARIFA'),1,0,'C');
    $this->Cell(60,9,utf8_decode('PREDIO'),1,0,'C');

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
$sql="SELECT a.valor,a.indicador,tt.nombre AS nombreTipoTarifa,p.nombre AS nombrePredio
    FROM gr_avaluo a
    INNER JOIN gp_tarifa t ON t.id_unico=a.tarifa
    INNER JOIN gp_tipo_tarifa tt ON tt.id_unico=t.tipo_tarifa
    INNER JOIN gp_predio1 p ON p.id_unico=a.predio";

$resultado=$mysqli->query($sql);

while($row=mysqli_fetch_array($resultado)){

            //llenar datos
            $pdf->SetX(8.2);
            $pdf->SetFont('Arial','',8.3);
            $pdf->Cell(30,4,utf8_decode( $row['valor']),0,0,'C');

            $indicador="";
            if($row['indicador']==1){
                $indicador="SI";
            }else{
                $indicador="NO";
            }

            $pdf->Cell(50,4,utf8_decode($indicador),0,0,'C');
       
            //multicelda
            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(60,4,utf8_decode(ucwords(mb_strtolower($row['nombreTipoTarifa'] ))),0,'L');
            $y22 = $pdf->GetY();
            $h1 = $y22-$y2;
            $px2 = $x2+60;

            if($numpaginas>$paginactual){
                $pdf->SetXY($px2,$yp);
                $h1=$y22-$yp;
            } else {
                $pdf->SetXY($px2,$y2);
            }

            //multicelda
            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(60,4,utf8_decode(ucwords(mb_strtolower($row['nombrePredio'] ))),0,'L');
            $y22 = $pdf->GetY();
            $h1 = $y22-$y2;
            $px2 = $x2+60;

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
$pdf->Output(0,'Listado Avaluo ('.date('d/m/Y').').pdf',0);
