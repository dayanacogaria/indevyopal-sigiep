<?php
require('../fpdf.php');
require_once ('../Conexion/conexion.php');

/* comprobar la conexión */
 
//session_start();

//Creación de Archivo FPDF
$pdf = new FPDF('L','mm','Letter');
$pdf->AddPage();
$pdf->SetCompression(1);
$pdf->SetFont('Arial','B',14);
$pag = 0;

$pdf->Cell(90,13,'',0);
$pdf->Cell(130,10,'Informe Cuentas Presupuestales',0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Fecha: '.date('d-m-Y').'',0);
$pdf->Ln(10);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(100,8,'',0);
$pdf->Cell(150,10,'LISTADO PLAN DE CUENTAS',0);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(105,8,'',0);
$pdf->Cell(100,10,'NOMBRE DE EMPRESA',0);
$pdf->Cell(20,8,'Pagina',0);
$pdf->Ln(15);

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
$pdf->Ln(5);

//Consulta SQL
/*$sql = "SELECT 
                    RP.id_unico as id,
                    RP.codi_cuenta as codc,
                    RP.nombre as nom,       
                    RP.movimiento as mov,
                    RP.centrocosto as cenc,
                    RP.auxiliartercero as auxt,
                    RP.auxiliarproyecto as auxp,
                    RP.activa as acti,
                    RP.dinamica as din,
                    (SELECT H.codi_cuenta FROM gf_cuenta H WHERE RP.predecesor = H.id_unico) as hj,       
                    RP.naturaleza as nat,
                    NT.id_unico as nid,
                    NT.nombre as nnom,
                    RP.tipocuentacgn as cgn,
                    TPC.id_unico as tid,
                    TPC.nombre as tnom,
                    RP.clasecuenta as clc,
                    CC.id_unico as cid,
                    CC.nombre as cnom
        FROM gf_cuenta RP  
  LEFT JOIN gf_naturaleza NT        ON RP.naturaleza = NT.id_unico
  LEFT JOIN gf_tipo_cuenta_cgn TPC  ON RP.tipocuentacgn = TPC.id_unico
  LEFT JOIN gf_clase_cuenta CC      ON RP.clasecuenta = CC.id_unico";
//$cp = $mysqli->query($sql);*/

//$cp = $mysqli->query($sql);
$codp = 0;
$mov = "";
$cen = "";
$ater = "";
$aproy = "";
/*while ($fila = mysqli_fetch_array($cp)) 
        { 
         $codp = $codp + 1;   
         //$pdf->Cell(15,8,$codp,1);
         $pdf->Cell(19,5,$fila['codc'],1);
         $pdf->Cell(80,5,$fila['nom'],1);
         $pdf->Cell(15,5,$fila['nnom'],1);
         $pdf->Cell(9,5,$fila['cnom'],1);
         
         switch($fila['mov'])
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
         $pdf->Cell(16,5,$mov,1);
         
         switch($fila['cenc'])
         {
          case 1:
          {
              $cos="SI";
              break;
          }
          case 2:
          {
              $cos="NO";
              break;
          }
         }    
         $pdf->Cell(18,5,$cos,1);
         
         switch($fila['auxt'])
         {
          case 1:
          {
              $ater="SI";
              break;
          }
          case 2:
          {
              $ater="NO";
              break;
          }
         }    
         $pdf->Cell(18,5,$ater,1);
         
         switch($fila['auxp'])
         {
          case 1:
          {
              $aproy="SI";
              break;
          }
          case 2:
          {
              $aproy="NO";
              break;
          }
         }    
         $pdf->Cell(19,5,$aproy,1);
         
         switch($fila['acti'])
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
         $pdf->Cell(11,5,$act,1);
         
         $pdf->Cell(15,5,$fila['din'],1);
         $pdf->Cell(17,5,$fila['tnom'],1);
         $pdf->Cell(19,5,$fila['hj'],1);
         $pdf->Ln(5); */
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
    //    }

$pdf->Output();
?>
