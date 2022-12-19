<?php
    
require('./fpdf151/fpdf.php');
require_once ('./Conexion/conexion.php');

session_start();

//contenido
//Creación de Archivo FPDF
$pdf = new FPDF('L','mm','Letter');
$pdf->AddPage();
$pdf->SetCompression(1);
$pdf->SetFont('Arial','B',14);
$pag = 0;

//$pdf->image('./RECURSOS/Logo.jpg', 10,10,10,13,'JPG');
$pdf->Cell(90,13,'',0);
$pdf->Cell(130,10,'Informe Cuentas Presupuestales',0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Fecha: '.date('d-m-Y').'',0);
$pdf->Ln(13);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(100,8,'',0);
$pdf->Cell(150,10,'LISTADO PLAN DE CUENTAS',0);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(105,8,'',0);
$pdf->Cell(100,10,'NOMBRE DE EMPRESA',0);
$pdf->Cell(20,8,'Pagina ',0);
$pdf->Ln(15);
/*
$pdf->SetFont('Arial','B',7);
$pdf->Cell(19,5,'Codigo',1);
$pdf->Cell(100,5,'Nombre',1);
$pdf->Cell(15,5,'Naturaleza',1);
$pdf->Cell(9,5,'Clase',1);
$pdf->Cell(16,5,'Movimiento',1);
$pdf->Cell(18,5,'Centro Costo',1);
$pdf->Cell(18,5,'Aux. Tercero',1);
$pdf->Cell(19,5,'Aux. Proyecto',1);
$pdf->Cell(11,5,'Activa',1);
//$pdf->Cell(15,5,'Dinamica',1);
$pdf->Cell(17,5,'Cuenta CGN',1);
$pdf->Cell(19,5,'Predecesor',1);

 */$pdf->Ln(5);

$pdf->Output();