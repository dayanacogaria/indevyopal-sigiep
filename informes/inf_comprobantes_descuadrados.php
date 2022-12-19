<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
$anno = $_SESSION['anno'];
ob_start();
ini_set('max_execution_time', 0);
##########RECEPCION VARIABLES###############

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
    $numpaginas=$this->PageNo();
    
    $this->SetFont('Arial','B',10);
    $this->SetY(10);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,15,8,20);
        }
    $this->SetX(15);
    $this->Cell(190,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(15);
    $this->Cell(190, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(15);
    $this->Cell(190,5,utf8_decode('LISTADO COMPROBANTES DESCUADRADOS'),0,0,'C');
    $this->Ln(10);
    
    
    $this->SetX(15);
    $this->SetFont('Arial','B',8);
    $this->Cell(20,9,utf8_decode('FECHA'),1,0,'C');
    $this->Cell(30,9,utf8_decode('NUMERO'),1,0,'C');
    $this->Cell(50,9,utf8_decode('TIPO'),1,0,'C');
    $this->Cell(30,9,utf8_decode('DÉBITO'),1,0,'C');
    $this->Cell(30,9,utf8_decode('CRÉDITO'),1,0,'C');
    $this->Cell(30,9,utf8_decode('DIFERENCIA'),1,0,'C');
    
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

 $banco ="SELECT DISTINCT 
    cn.id_unico,
    cn.numero,
    tc.sigla,
    tc.nombre,
    date_format(cn.fecha,'%d/%m/%Y'),
    (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
FROM
    gf_comprobante_cnt cn 
LEFT JOIN
    gf_tipo_comprobante tc
ON
    cn.tipocomprobante = tc.id_unico  
WHERE 
    cn.parametrizacionanno =$anno 
ORDER BY cn.fecha ASC ";

$banco = $mysqli->query($banco);
$total =0;
$total2 =0;
$pdf->SetFont('Arial','',8);
while ($row = mysqli_fetch_row($banco)) {
    $paginactual = $numpaginas;
    $numero = $row[1];
    $tipo = mb_strtoupper($row[2]).' - '. ucwords(mb_strtolower($row[3]));
    $fecha = $row[4];
    $debito1 =$row[5];
    $debitoN =$row[8]*-1;
    $credito1 =$row[7];
    $creditoN =$row[6]*-1;
    $debito = $debito1+$debitoN;
    $credito = $credito1+$creditoN;
    
    $diferencia = ROUND(($debito -$credito),2);
    
    if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
    
    
    
            $pdf->SetX(15);
            $ypr=$pdf->GetY();
            
            $pdf->Cell(50,6,utf8_decode(' '),0,0,'C');
            
            
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->MultiCell(50,6,utf8_decode($tipo),0,'J');
            $y2=$pdf->GetY();
            $h = $y2-$y;
            $px = $x+50;
            if($numpaginas>$paginactual){
               $pdf->SetXY($px,$yp);
               $h=$y2-$yp;
            } else {
                $pdf->SetXY($px,$y);
            }
            
            $alto = ($h);
            
            $pdf->SetX(15);
            $pdf->Cell(20,$alto,utf8_decode($fecha),1,0,'C');
            $pdf->Cell(30,$alto,utf8_decode($numero),1,0,'C');
            $pdf->Cell(50,$alto,utf8_decode(' '),1,0,'C');
            $pdf->CellFitScale(30,$alto,utf8_decode('$'.number_format($debito,2,'.',',')),1,0,'R');
            $pdf->CellFitScale(30,$alto,utf8_decode('$'.number_format($credito,2,'.',',')),1,0,'R');
            $pdf->CellFitScale(30,$alto,utf8_decode('$'.number_format($diferencia,2,'.',',')),1,0,'R');   
            $pdf->Ln($alto);   
            
    }
    
}

while (ob_get_length()) {
  ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0,'Listado Comprobantes Descuadrados ('.date('d/m/Y').').pdf',0);