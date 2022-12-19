<?php 
###################################MODIFICACIONES#############################################################
#                          INFORME ADICION A APROPIACION GENERAL#
#############################################################################################################
#21/06/2017 | ERICA G. | CAMBIO CODIGO
#############################################################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');     
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
$id = $_GET['id'];
$sqlComp = "SELECT comp.id_unico, 
            comp.numero, 
            DATE_FORMAT(comp.fecha,'%d/%m/%Y'),  
            comp.descripcion, 
            comp.fechavencimiento, 
            comp.tipocomprobante, 
            tipCom.codigo, 
            tipCom.nombre, 
            comp.usuario,
            DATE_FORMAT(comp.fecha_elaboracion,'%d/%m/%Y') 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND md5(comp.id_unico) = '$id'";

$comp = $mysqli->query($sqlComp);
$rowComp = mysqli_fetch_array($comp);
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$nombretipoF = $rowComp[7];
$user = $rowComp[8];
$fechaElab = $rowComp[9];

$compania = $_SESSION['compania'];
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];

class PDF extends FPDF
{
  function Footer()
  {
        global $user ;
        global $fechaElab ;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(35,10,'Elaborado por: '.utf8_decode($user),0,0, 'L');
        $this->Cell(155,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
        
  }
  #Funcón cabeza de la página
  function Header()
  { 

    global $fechaComp;
    global $ruta;
    global $nombretipoF;
    global $nomcomp;

    // Logo acá
    if($ruta != '')
    {
      $this->Image('../'.$ruta,10,8,30);
    } 
    
    //Arial bold 15
    $this->SetFont('Arial','B',10);
    $this->Cell(100);
        // Título

    $this->Ln(4);
    $this->Cell(240,10,'',0);
    $this->SetFont('Arial','',8);
    $this->Cell(15,10,'',0); 

    $this->SetXY(10,13); //EStaba
    $this->SetFont('Arial','B',15);
    $this->Cell(190, 5,utf8_decode(strtoupper($nombretipoF)),0,0,'C'); 
    $this->Ln(7);
    $this->Cell(190, 5,utf8_decode('Número: '.$nomcomp),0,0,'C');
   
    $this->Ln(60);
    
    
  }
}

$pdf = new PDF('P','mm','Letter');      
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Ln(-32);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190, 15,utf8_decode('Fecha: '.$fechaComp),1, 0, 'L');   

$pdf->SetXY(10,13);
$pdf->Ln(15);
$pdf->SetFont('Arial','B',12);
$pdf->SetX(50);
$pdf->MultiCell(140, 5,utf8_decode('El suscrito, certifica que en la fecha existe saldo presupuestal libre de afectación para respaldar el siguiente compromiso:'),0,'C'); 
$pdf->Ln(30);
########TABLA#######
$pdf->SetFont('Arial','B',9,0,'C');
$pdf->Cell(20,5,utf8_decode('Código'),1,0,'C'); 
$pdf->Cell(60,5,utf8_decode('Rubro'),1,0,'C');
$pdf->Cell(60,5,utf8_decode('Fuente'),1,0,'C');

$pdf->Cell(25,5,utf8_decode('Crédito'),1,0,'C');
$pdf->cellfitscale(25,5,utf8_decode('Contracrédito'),1,0,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);

$sqlDetall = "SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, fue.nombre nombreFuente, detComP.valor, rub.tipoclase, rub.nombre      
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente
      left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase
      where md5(detComP.comprobantepptal) ='$id'";
$detalle = $mysqli->query($sqlDetall);
$totalValor = 0;
$totalCredito = 0;
$totalContacredito = 0;
while ($rowDetall = mysqli_fetch_array($detalle)) 
{ 
    $al = $pdf->GetY();
    if($al>250){
        $pdf->AddPage();
        $pdf->Ln(-32);
    }
    $ingresos = 0;
    $gastos = 0;

    if($rowDetall[4] == 6)
        $ingresos = $rowDetall[3];
    elseif($rowDetall[4] == 7)
       $gastos = $rowDetall[3];
    
    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->CellFitScale(20,5,'',0,0,'L');
    $pdf->MultiCell(60,5, utf8_decode(ucwords(mb_strtolower($rowDetall[5]))),0,'J'); 
    $y2 = $pdf->GetY();            
    $h = $y2-$y1;
    $px = $x1 + 80; 
    $pdf->SetXY($px,$y1);
    
    $y12 = $pdf->GetY();
    $x2 = $pdf->GetX();
    $pdf->MultiCell(60,5, utf8_decode(ucwords(mb_strtolower($rowDetall[2]))),0,'J'); 
    $y22 = $pdf->GetY();            
    $h2 = $y22-$y12;
    $px2 = $x12 + 60; 
    $paginactual22=$pdf->PageNo();
    $pdf->SetXY($px,$y1);
    
    $alto = max($h, $h2);
    $pdf->SetXY($x1,$y1);
    $pdf->CellFitScale(20,$alto,$rowDetall[1],1,0,'R');
    $pdf->Cell(60,$alto,'',1,0,'L');
    $pdf->Cell(60,$alto,'',1,0,'L');
    $pdf->CellFitScale(25,$alto,number_format($gastos,2,'.',','),1,0,'R');
    $pdf->CellFitScale(25,$alto,number_format($ingresos,2,'.',','),1,0,'R');
    
    $pdf->Ln($alto);
    $totalCredito = $totalCredito+$gastos;
    $totalContacredito = $totalContacredito+$ingresos;
}

#TOTALES
$pdf->SetFont('Arial','B',8);
$pdf->Cell(140,8,'TOTALES',0,0,'R');
$pdf->CellFitScale(25,8,number_format($totalCredito,2,'.',','),0,0,'R');
$pdf->CellFitScale(25,8,number_format($totalContacredito,2,'.',','),0,0,'R');

$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('Descripción: '),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->SetX(10);
$pdf->MultiCell(190,5,utf8_decode('                       '.$descripcion),0,'L');

$pdf->Ln(10);

$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int)$mesS;
$anioS = $fecha_div[2];

$ciudadCompania = mb_strtoupper($ciudadCompania,'utf-8');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,13,utf8_decode('Se expide en '.$ciudadCompania.' a los '.$diaS.' días del mes de '.$meses[$mesS].' de '.$anioS),0,0,'L');



#########################################FIRMA ###################################################################

$sqlTipoComp = "SELECT CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),ti.nombre,t.numeroidentificacion, car.nombre
  FROM gf_tipo_comprobante_pptal tcp
  LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
  LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
  LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
  LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
  LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
  LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
  LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
  WHERE tcp.id_unico = $tipocomprobante";
$tipComp = $mysqli->query($sqlTipoComp); 
$resultF1= $mysqli->query($sqlTipoComp);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop-$altofinal;
//$rowTipComp = mysqli_fetch_array($tipComp);
$pdf->ln(30);
$c=0;
while($cons = mysqli_fetch_row($resultF1)){
  $c++;
  }

  $tfirmas = ($c/2) * 33;
  
  if($tfirmas>$altofirma)
      
    $xt=10; 
    while($firma = mysqli_fetch_row($tipComp)){
    if($xt<50){
      $xm = 10; 
      $pdf->setX($xm);
      $pdf->SetFont('Arial','B',10);
      $pdf->Cell(60,0,'',1);
     
      $x = $pdf->GetX();
      $y = $pdf->GetY();        
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Impresión de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Tipo de texto
      $pdf->SetFont('Arial','',8);
      #Impresión de tipo de documento y numero documento
      #$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
      #$pdf->Cell(190,2,utf8_decode($firma[1].':'.$firma[2]),0,0,'L');
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Tipo de texto
      $pdf->SetFont('Arial','B',8);
      #Impresión de cargo de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
      $pdf->setX($xm);
      #Obtención de alto final        
      $x2 = $pdf->GetX();       
      #Posición final de firma 2    
      $pdf->Ln(0);
      $xt = 120;
    }else{
      $xn = 120;
      $pdf->SetY($y);
      #Construcción de linea firma
      $pdf->SetFont('Arial','B',10);
      $pdf->setX($xn);
      #Linea para firma
      $pdf->Cell(60,0,'',1);
      #Varibles x,y
      $x = $pdf->GetX();
      #alto inicial
      $y = $pdf->GetY();
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xn);
      #Impresión de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xn);
      #Tipo de texto
      $pdf->SetFont('Arial','',8);
      #Impresión de tipo de documento y numero documento
      #$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
      #$pdf->Cell(190,2,utf8_decode($firma[1].':'.$firma[2]),0,0,'L');
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xn);
      #Tipo de texto
      $pdf->SetFont('Arial','B',8);
      #Impresión de cargo de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
      #Obtención de alto final      
      $x2 = $pdf->GetX();
      #Posición del ancho     
      $posicionY = $y-20;
      #Ubicación firma 2
      $pdf->SetXY($x2,$posicionY);
      #Posición final de firma
      $xt = 0;
    }
  }
#########################################FIRMA ###################################################################
while (ob_get_length()) {
  ob_end_clean();
}
$pdf->Output(0,'Informe_certificado_'.$nomcomp.').pdf',0);
?>

