<?php
###################################################################################
#               INFORME MODIFICACIONES A DISPONIBILIDAD
#                               GENERAL
############################MODIFICACIONES#########################################
#09/06/2017 |ERICA G. |ARCHIVO CREADO
###################################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');     
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);

session_start();

$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
$id = $_GET['id'];
$sqlComp = "SELECT 
            comp.id_unico, 
            comp.numero, 
            DATE_FORMAT(comp.fecha,'%d/%m/%Y'), 
            comp.descripcion, 
            comp.fechavencimiento, 
            comp.tipocomprobante, 
            tipCom.codigo, 
            tipCom.nombre, 
            comp.tercero, 
            comp.usuario, 
            DATE_FORMAT(comp.fecha_elaboracion, '%d/%m/%Y')
      FROM 
        gf_comprobante_pptal comp 
      LEFT JOIN 
        gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
      WHERE  md5(comp.id_unico) ='$id'";
$comp = $mysqli->query($sqlComp);
$rowComp = mysqli_fetch_row($comp);
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$usuario = $rowComp[9];
$fechaElb = $rowComp[10];
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
   function Header()
    { 

    global $nomcomp;
    global $ruta;
    global $nombre;

    if($ruta != '')
    {
      $this->Image('../'.$ruta,10,8,30);
    } 
    $this->SetXY(20,13);
    $this->SetFont('Arial','B',15);
    $this->Cell(180, 5,mb_strtoupper($nombre),0,0,'C'); 
    $this->Ln(7);
    $this->Cell(200, 5,utf8_decode('Número: '.$nomcomp),0,0,'C'); 
    $this->Ln(7);
    
  }

  function Footer()
  {
    global $usuario;
    global $fechaElb;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->Cell(63,10,'Elaborado por: '.strtoupper($usuario),0);
    $this->Cell(64,10,'Fecha: '.$fechaElb,0,0,'C');
    $this->Cell(63,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
  }
}

$pdf = new PDF('P','mm','Letter');      
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','B',12);
$pdf->SetX(45);
$pdf->MultiCell(155, 5,utf8_decode('El suscrito , certifica que en la fecha existe saldo presupuestal libre de afectación para respaldar el siguiente compromiso:'),0,'C'); 
$pdf->SetFont('Arial','',9);

$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190, 15,utf8_decode('Fecha: '.$fechaComp),1, 0, 'L');
$pdf->Ln(18);
    
$pdf->SetFont('Arial','B',9,0,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',9,'C');
$y1 = $pdf->GetY();
$x1 = $pdf->GetX();
$pdf->MultiCell(35,5,utf8_decode("Disponibilidad  Afectada"),1,'C'); 
$y2 = $pdf->GetY();            
$h = $y2-$y1;
$px = $x1 + 35; 
$alt=($h);
$pdf->SetXY($px,$y1);
$pdf->Cell(60,$alt,utf8_decode('Rubro'),1,0,'C');
$pdf->Cell(60,$alt,utf8_decode('Fuente'),1,0,'C');
$pdf->Cell(35,$alt,utf8_decode('Valor'),1,0,'C');
$pdf->Ln($alt);

 $sqlDetall = "SELECT 
            dc.id_unico, 
            CONCAT(rub.codi_presupuesto, ' - ',LOWER(rub.nombre)), 
            CONCAT(rubFue.id_unico,' - ' ,LOWER(fue.nombre)), 
            dc.valor, 
            dc.comprobanteafectado, 
            CONCAT(UPPER(tcp.codigo), ' ',cpa.numero)
      FROM gf_detalle_comprobante_pptal dc 
      LEFT JOIN 
        gf_rubro_fuente rubFue on dc.rubrofuente = rubFue.id_unico 
      LEFT JOIN 
        gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      LEFT JOIN 
        gf_fuente fue on fue.id_unico = rubFue.fuente 
      LEFT JOIN 
        gf_detalle_comprobante_pptal dcpa ON dc.comprobanteafectado = dcpa.id_unico 
      LEFT JOIN 
        gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico 
      LEFT JOIN 
        gf_tipo_comprobante_pptal tcp ON tcp.id_unico = cpa.tipocomprobante 
      WHERE md5(dc.comprobantepptal) ='$id'";
$detalle = $mysqli->query($sqlDetall);
$totalValor = 0;
$pdf->SetFont('Arial','',9);
while ($rowD = mysqli_fetch_row($detalle)) 
{ 
  $yda = $pdf->GetY();
  $xda = $pdf->GetX();        
  $pdf->MultiCell(35,5,utf8_decode($rowD[5]),0,'J');
  $y2da = $pdf->GetY();
  $hda = $y2da-$yda;
  $pxda = $xda + 35;
  $pdf->Ln(-$hda);
  $pdf->SetX($pxda);
  
  $yr = $pdf->GetY();
  $xr = $pdf->GetX();
  $pdf->MultiCell(60,5,utf8_decode(ucwords($rowD[1])),0,'J');
  $y2r = $pdf->GetY(); 
  $hr = $y2r-$yr;
  $pxr = $xr + 60;
  $pdf->Ln(-$hr);
  $pdf->SetX($pxr);
  
  $yf = $pdf->GetY();
  $xf = $pdf->GetX();
  $pdf->MultiCell(60,5,utf8_decode(ucwords($rowD[2])),0,'J');
  $y2f = $pdf->GetY(); 
  $hf = $y2f-$yf;
  $pxf = $xf + 60;
  $pdf->Ln(-$hf);
  $pdf->SetX($pxf);
  
  $alt= MAX($hda, $hr, $hf);
  $pdf->Cell(35,$alt,utf8_decode(number_format($rowD[3], 2, '.',',')),0,0,'R');
  $totalValor +=$rowD[3];
  $pdf->Ln($alt);

}
$pdf->SetFont('Arial','B',9);
$pdf->Cell(155,5,utf8_decode('Total: '),0,0,'R');
$pdf->Cell(35,5,utf8_decode(number_format($totalValor, 2, '.',',')),0,0,'R');

$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
$xdes = $pdf->GetX();
$ydes = $pdf->GetY();
$pdf->Cell(20,5,utf8_decode('Descripción: '),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->SetX(40);
$pdf->MultiCell(120,5,utf8_decode($descripcion),0,'J');
$pdf->Ln(2);
$yddes=$pdf->GetY();
$altdes = $yddes-$ydes;
$pdf->SetXY($xdes, $ydes);
$pdf->Cell(190,$altdes,utf8_decode(''),1,0,'L');


$pdf->Ln(20);
$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int)$mesS;
$anioS = $fecha_div[2];
$pdf->SetFont('Arial','B',10);
$ciudadCompania = mb_strtoupper($ciudadCompania,'utf-8');
$pdf->Cell(60,13,utf8_decode('Se expide en '.$ciudadCompania.' a los '.$diaS.' días del mes de '.$meses[$mesS].' de '.$anioS),0,0,'L');
$pdf->Ln(10);
###################################################################################################
###################################################################################################

$sqlTipoComp = "SELECT CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos), ti.nombre, t.numeroidentificacion, car.nombre 
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
$pdf->SetY(-33);
$c=0;
while($cons = mysqli_fetch_row($resultF1)){
  $c++;
  }

  $tfirmas = ($c/2) * 33;
  
  if($tfirmas>$altofirma)
      $pdf->AddPage();
    $pdf->SetY(-33);
    $xt=10; 
    while($firma = mysqli_fetch_row($tipComp)){
    if($xt<50){
      #Construcción de linea firma
      $xm = 10; 
      $pdf->setX($xm);
      $pdf->SetFont('Arial','B',10);
      #Linea para firma
      $pdf->Cell(60,0,'',1);
      #Varibles x,y
      $x = $pdf->GetX();
      $y = $pdf->GetY();        
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Impresión de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
      $pdf->Ln(3);
      $pdf->setX($xm);
      $pdf->SetFont('Arial','',8);
      $pdf->Ln(3);
      $pdf->setX($xm);
      $pdf->SetFont('Arial','B',8);
      #Impresión de cargo de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
      $pdf->setX($xm);
      $x2 = $pdf->GetX();       
      $pdf->Ln(0);
      $xt = 120;
    }else{
      $xn = 120;
      $pdf->SetY($y);
      $pdf->SetFont('Arial','B',10);
      $pdf->setX($xn);
      $pdf->Cell(60,0,'',1);
      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->Ln(3);
      $pdf->setX($xn);
      $pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
      $pdf->Ln(3);
      $pdf->setX($xn);
      $pdf->SetFont('Arial','',8);
      $pdf->Ln(3);
      $pdf->setX($xn);
      $pdf->SetFont('Arial','B',8);
      $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
      $x2 = $pdf->GetX();
      $posicionY = $y-20;
      $pdf->SetXY($x2,$posicionY);
      $xt = 0;
    }
  }

###################################################################################################
while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'Informe_'.$nombre.'.pdf',0);

?>

