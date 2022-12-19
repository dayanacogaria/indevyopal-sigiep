<?php
    
require'fpdf/fpdf.php';
require'Conexion/conexion.php';
session_start();

//$compania = $_SESSION['compania'];
$sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = 50";

$comp = $mysqli->query($sqlComp);

    $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    
    while ($rowComp = mysqli_fetch_array($comp))
    {
        $nomcomp = $rowComp[1];       
        $tipodoc = $rowComp[2];       
        $numdoc = $rowComp[3];   
    }


class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 15
    $this->SetFont('Arial','B',10);
    $this->Cell(100);
        // Título
    $this->Cell(30,10,$nomcomp,0,0,'C');
    // Salto de línea
    $this->Cell(105,10,'',0);
    $this->SetFont('Arial','B',8);
    $this->Cell(90,10,'CODIGO SGC',0);

    $this->Ln(4);

    $this->SetFont('Arial','',10);
    $this->Cell(121,8,'',0);
    $this->Cell(20,10,$tipodoc.' '.$numdoc,0);
    $this->Cell(93,10,'',0);
    $this->SetFont('Arial','B',8);
    $this->Cell(90,10,'VERSION SGC',0);

    $this->Ln(4);

    $this->SetFont('Arial','',8);
    $this->Cell(105,8,'',0);
    $this->Cell(42,10,'LISTADO PLAN DE CUENTAS',0);
    $this->Cell(90,10,'',0);
    $this->SetFont('Arial','B',8);
    $this->Cell(90,10,'FECHA SGC',0);
    
    $this->Ln(8);
    
    $this->SetFont('Arial','B',7);
    $this->Cell(19,5,'Concepto',1,0,'C');
    $this->Cell(95,5,'Rubro',1);
    $this->Cell(15,5,'Fuente',1);
    $this->Cell(9,5,'Valor',1);
    //$this->Cell(16,5,'Movimiento',1);
    //$this->Cell(18,5,'Centro Costo',1);
    //$this->Cell(18,5,'Aux. Tercero',1);
    //$this->Cell(19,5,'Aux. Proyecto',1);
    $this->Cell(11,5,'Saldo Disponible',1);
    $this->Cell(17,5,'Cuenta CGN',1);
    $this->Cell(19,5,'Predecesor',1);
    $this->Ln(6);
    }
// Pie de página
function Footer()
    {
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    // Número de página
        //$pdf->Cell(50,10,'Fecha: '.date('d-m-Y').'',0);
        //$pdf->Cell(70,10,'Maquina: '.gethostname(),0);
        //$pdf->Cell(80,10,'Usuario: '.  get_current_user(),0);    
        $this->Cell(25,10,'Fecha: '.date('d-m-Y'),0,0,'L');
        $this->Cell(50);
        $this->Cell(35,10,'Maquina '.  gethostname(),0);
        $this->Cell(40);
        $this->Cell(30,10,'Usuario '.get_current_user(),0);
        $this->Cell(50);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0);
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Letter');        

$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
//Cabecera para Página 1
$pdf->SetXY(125,13);
$pdf->Cell(20, 5, $nomcomp,0,0,'C');
$pdf->SetXY(125,17);
$pdf->SetFont('Arial','',8);
$pdf->Cell(20, 5,$tipodoc.': '.$numdoc,0,0,'C');
//Fin Cabecera Página 1

$pdf->SetFont('Arial','',7);

//Consulta SQL
$sqlDetall = "SELECT detComP.id_unico, con.nombre, rub.nombre, detComP.valor, rubFue.id_unico, fue.nombre     
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on rub.id_unico = conRub.rubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where detComP.comprobantepptal = 50";
$detalle = $mysqli->query($sqlDetall);

$pdf->SetY(31);

while ($rowDetall = mysqli_fetch_array($detalle)) 
{ 
  $codp = $codp + 1;
  $pdf->Cell(19,5,$rowDetall[1],0);
  $pdf->Cell(95,5,$rowDetall[2],0);
  //$pdf->Cell(15,5,$rowDetall[3],0,0,'C');
  //$pdf->Cell(9,5,$rowDetall[4],0,0,'C');
  $pdf->Cell(15,5,$rowDetall[3],0,0,'C');
  $pdf->Cell(9,5,$rowDetall[4],0,0,'C');

  $pdf->Cell(7,5,$rowDetall[4],0,0,'C');
  $pdf->Cell(9,5,ucwords(utf8_encode($rowDetall[5])),0,0);

  $pdf->Ln(5);
}
        
$pdf->Cell(256,0.5,'',1);

ob_end_clean();
$pdf->Output('','',true);

?>

