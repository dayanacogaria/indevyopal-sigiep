<?php
session_start();
    
require_once'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
//require('registrar_GF_CUENTA_P.php');



//Creación de Archivo FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

//$pdf->image('./RECURSOS/Logo.jpg', 10,10,10,13,'JPG');
$pdf->Cell(70,13,'',0);
$pdf->Cell(80,10,'Informe Dependencias',0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Fecha: '.date('d-m-Y').'',0);
$pdf->Ln(9);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(78,8,'',0);
$pdf->Cell(150,10,'LISTADO PRINCIPAL',0);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(75,8,'',0);
$pdf->Cell(150,10,'NOMBRE DE EMPRESA',0);

$pdf->Ln(15);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(10,5,'Item',1);
$pdf->Cell(80,5,'Nombre',1);
$pdf->Cell(10,5,'Sigla',1);
$pdf->Cell(16,5,'Movimiento',1);
$pdf->Cell(10,5,'Activa',1);
$pdf->Cell(25,5,'Predecesor',1);
$pdf->Cell(18,5,'Centro Costo',1);
$pdf->Cell(23,5,'Tipo Dependencia',1);

$pdf->Ln(5);
//Consulta SQL
$sql = "SELECT 		d.id_unico as id,
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
        LEFT JOIN gf_tipo_dependencia td ON d.tipodependencia = td.id_unico";
$cp = $mysqli->query($sql);
$codd = 0;
$mov = "";
$act = "";

while ($fila = mysqli_fetch_array($cp)) 
        { 
         $codd = $codd + 1;
         $pdf->Cell(19,5,$codd,0);
         $pdf->Cell(80,5,$fila['dnom'],0);
         $pdf->Cell(15,5,$fila['dsig'],0);
         
         switch($fila['dmov'])
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
         $pdf->Cell(16,5,$mov,0);
         
         switch($fila['dact'])
         {
          case 1:
          {
              $act="SI";
              break;
          }
          case 2:
          {
              $act="NO";
              break;
          }
         }    
         $pdf->Cell(11,5,$act,0);
         
         $pdf->Cell(18,5,$fila['denom'],0);
         $pdf->Cell(18,5,$fila['cnom'],0);
         $pdf->Cell(19,5,$fila['tnom'],0);
         
         
         $pdf->Ln(5);
       /*$pdf->Cell(18,8,'Naturaleza',1);
         $pdf->Cell(10,8,'Clase',1);
         $pdf->Cell(19,8,'Movimiento',1);
         $pdf->Cell(21,8,'Centro Costo',1);
         $pdf->Cell(21,8,'Aux. Tercero',1);
         $pdf->Cell(23,8,'Auxi. Proyecto',1);
         $pdf->Cell(12,8,'Activa',1);
         $pdf->Cell(17,8,'Dinámica',1);
         $pdf->Cell(26,8,'Tipo Cuenta CGN',1);
         $pdf->Cell(18,8,'Predecesor',1);*/
        }
        $pdf->Cell(192,0.5,'',1);

ob_end_clean();
$pdf->Output();
?>

