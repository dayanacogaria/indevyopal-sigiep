<?php
session_start();
    
require_once'fpdf/fpdf.php';
require'Conexion/conexion.php';
//require('registrar_GF_CUENTA_P.php');

class PDF extends FPDF
        {
        function Footer()
            {
            // Go to 1.5 cm from bottom
            $pdf->SetY(-35);
            // Select Arial italic 8
            $pdf->SetFont('Arial','I',8);
            // Print centered page number
            $pdf->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
            
            }
        }

//Creación de Archivo FPDF
$pdf = new FPDF('L','mm','Letter');

echo $compania = 1;//$_SESSION['compania'];

$consulta = "SELECT detComP.id_unico, con.nombre, rub.nombre, detComP.valor, rubFue.id_unico, fue.nombre     
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on rub.id_unico = conRub.rubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where detComP.comprobantepptal = 50"/*.$_SESSION['id_comp_pptal_MD']*/;
$cmp = $mysqli->query($consulta);

/* if(!empty($_SESSION['compania']))
  {
    $parametroAnno = $_SESSION['compania'];
  }
  else
  {
    $queryParam = "SELECT MIN(id_unico) FROM gf_parametrizacion_anno";
    $param = $mysqli->query($queryParam);
    $row = mysqli_fetch_row($param);
    $parametroAnno = $row[0]; 
  } */




$nomcomp = "";
$tipodoc = "";
$numdoc = 0;
while ($fila = mysqli_fetch_array($cmp))
{
 $nomcomp = $fila[0];       
 $tipodoc = $fila[1];       
 $numdoc = $fila[2];  
 
}
        
$nb=$pdf->AliasNbPages();

$pdf->AddPage();


$pdf->image('RECURSOS/Logo.jpg', 10,10,10,13,'JPG'); //Imagen Logotipo //Comentado
$pdf->Cell(120,13,'',0);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(20,10,$nomcomp,0);
$pdf->Cell(80,10,'',0);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(90,10,'CODIGO SGC',0);

$pdf->Ln(9);
$pdf->SetFont('Arial','',11);
$pdf->Cell(120,8,'',0);
$pdf->Cell(20,10,$tipodoc.': '.$numdoc,0);
$pdf->Cell(79,10,'',0);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(90,10,'VERSION SGC',0);

$pdf->Ln(5);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(100,8,'',0);
$pdf->Cell(42,10,'LISTADO PLAN DE CUENTAS',0);
$pdf->Cell(80,10,'',0);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(90,10,'FECHA SGC',0);

$pdf->Ln(15);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(19,5,'Codigo',1);
$pdf->Cell(95,5,'Nombre',1);
$pdf->Cell(15,5,'Naturaleza',1);
$pdf->Cell(9,5,'Clase',1);
$pdf->Cell(16,5,'Movimiento',1);
$pdf->Cell(18,5,'Centro Costo',1);
$pdf->Cell(18,5,'Aux. Tercero',1);
$pdf->Cell(19,5,'Aux. Proyecto',1);
$pdf->Cell(11,5,'Activa',1);
$pdf->Cell(17,5,'Cuenta CGN',1);
$pdf->Cell(19,5,'Predecesor',1);
$pdf->Ln(5);
//Consulta SQL
$sql = "SELECT detComP.id_unico, con.nombre, rub.nombre, detComP.valor, rubFue.id_unico, fue.nombre     
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on rub.id_unico = conRub.rubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where detComP.comprobantepptal = 50"/*.$_SESSION['id_comp_pptal_MD']*/;
$cp = $mysqli->query($sql);
$codp = 0;
$mov = "";
$cen = "";
$ater = "";
$aproy = "";
while ($fila = mysqli_fetch_row($cp)) 
        { 
         $codp = $codp + 1;
         $pdf->Cell(19,5,$fila[0],0);
         $pdf->Cell(95,5,$fila[1],0);
         $pdf->Cell(15,5,$fila[2],0);
         $pdf->Cell(9,5,$fila[3],0);
         
         /*switch($fila[0])
         {
          case 1:
          {
              $mov="SI";
              //$pdf->Image('http://chart.googleapis.com/chart?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World',60,30,90,0,'PNG');
              break;
          }
          case 2:
          {
              $mov="NO";
              //$mov->image('./RECURSOS/.jpg', 10,10,10,13,'JPG');
              break;
          }
         } */

         $pdf->Cell(16,5,$fila[0],0);
         
        /* switch($fila[1])
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
         }     */

         $pdf->Cell(18,5,$fila[1],0);
         
       /*  switch($fila[2])
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
         }    */

         $pdf->Cell(18,5,$fila[2],0);
         
       /*  switch($fila[3])
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
         }    */

         $pdf->Cell(19,5,$fila[3],0);
         
        /* switch($fila[4])
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
         }    */

         $pdf->Cell(11,5,$fila[4],0);
         
         $pdf->Cell(17,5,$fila[3],0);
         $pdf->Cell(19,5,$fila[4],0);
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
        
        $pdf->Cell(256,0.5,'',1);
        
        $pdf->Ln(70);

//Footer Vertical        
        $pdf->Cell(5,10,'',0);
        $pdf->Cell(50,10,'Fecha: '.date('d-m-Y').'',0);
        $pdf->Cell(30,10,'',0);
        $pdf->Cell(70,10,'Maquina: '.gethostname(),0);
        $pdf->Cell(80,10,'Usuario: '.  get_current_user(),0);
        $pdf->Cell(25,10,'Pagina: '.$pdf->PageNo(),0);                
//Fin Footer
        
        $pdf->write(-15,$pdf->Footer());

        ob_end_clean();
$pdf->Output();
?>

