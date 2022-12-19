<?php 
############FORMATO RECAUDO######################
#25/05/2017 | ERICA G. | ARCHIVO CREADO
#####################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');     
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();

$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');

$idpptalGet = $_GET['id'];
 $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, 
     comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero, claCont.nombre as nombreContrato, comp.numerocontrato 
    FROM gf_comprobante_pptal comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
    LEFT JOIN gf_clase_contrato claCont ON comp.clasecontrato = claCont.id_unico 
    WHERE md5(comp.id_unico) = '$idpptalGet'";

$comp = $mysqli->query($sqlComp);

$rowComp = mysqli_fetch_array($comp);
$idpptal = $rowComp[0];
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$terceroComp = intval($rowComp[8]); //Tercero del comprobante
$tipoContra = $rowComp[9];
$numContra = $rowComp[10];

$sqlTerc = "SELECT nombreuno, nombredos, apellidouno, apellidodos, razonsocial, numeroidentificacion 
      FROM gf_tercero
      WHERE id_unico = ".$terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0].' '.$rowT[1].' '.$rowT[2].' '.$rowT[3].' '.$rowT[4]; 
$nit = $rowT[5]; 

$compania = $_SESSION['compania'];
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre, ter.razonsocial, ter.numeroidentificacion, ter.digitoverficacion   
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];
$compa=$rowLogo[2];
if(empty($rowLogo[4])) { 
    $nitcom=$rowLogo[3];
} else {
    $nitcom=$rowLogo[3].' - '.$rowLogo[4];
}

class PDF_MC_Table extends FPDF{
  var $widths;
  var $aligns;
  function SetWidths($w){
    //Set the array of column widths
    $this->widths=$w;
  }
  function SetAligns($a){
    //Set the array of column alignments
    $this->aligns=$a;
  }
  function fill($f){
    //juego de arreglos de relleno
    $this->fill=$f;
  }
  function Row($data){
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
    $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++){
      $w=$this->widths[$i];
      $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
      //Save the current position
      $x=$this->GetX();
      $y=$this->GetY();
      //Draw the border
      $this->Rect($x,$y,$w,$h,$style);
      //Print the text
      $this->MultiCell($w,5,$data[$i],'LTR',$a,$fill);
      //Put the position to the right of the cell
      $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h-5);
  }
  function CheckPageBreak($h){
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
      $this->AddPage($this->CurOrientation);
  }
  function NbLines($w,$txt){
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
      $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace('\r','',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=='\n')
      $nb–;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb){
      $c=$s[$i];
      if($c=='\n'){
        $i++;
        $sep=-1;
        $j=$i;
        $l=0;
        $nl++;
        continue;
      }
      if($c=='')
        $sep=$i;
      $l+=$cw[$c];
      if($l>$wmax){
        if($sep==-1){
          if($i==$j)
            $i++;
        }else
          $i=$sep+1;
        $sep=-1;
        $j=$i;
        $l=0;
        $nl++;
      }else
        $i++;
      }
    return $nl;
  }
  #Función de pie de pagina
  function Footer()
  {
    global $usuario;  
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->Cell(63,10,'Elaborado por: '.strtoupper($usuario),0,0,'L');
    $this->Cell(63,10,'Fecha: '.date('d/m/Y'),0,0,'C');
    $this->Cell(64,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
    
  }
  #Funcón cabeza de la página
  // Cabecera de página  
  function Header()
  { 
   global $nomcomp;
    global $fechaComp;
    global $razonSoc;
    global $nit;
    global $ruta;
    global $rolT;
    global $nombre;

    $fecha_div = explode("-", $fechaComp);
    $diaS = $fecha_div[2];
    $mesS = $fecha_div[1];
    $anioS = $fecha_div[0];

    $fechaCompF = $diaS.'/'.$mesS.'/'.$anioS;

    $dia = date('d');
    $mes = date('m');
    $anio = date('Y');
    $fecha = $dia.'/'.$mes.'/'.$anio;

    // Logo acá
    if($ruta != '')
    {
      $this->Image('../'.$ruta,10,10,20);
    } 
    $this->SetFont('Arial','B',12);
    $this->Cell(190, 5,utf8_decode(strtoupper($nombre)),0, 0, 'C');

    $this->Ln(8);
    $this->Cell(190, 5,utf8_decode('Número: '.$nomcomp),0, 0, 'C');

    
    $this->Ln(15);
    $this->SetFont('Arial','B',10);
    $this->Cell(190, 20,'',1, 0, 'L');
    $this->SetX(10);
    $this->Cell(190, 5,utf8_decode('FECHA: '.$fechaCompF),0, 0, 'L');
    $this->Ln(8);
    $y1 = $this->GetY();
    $x1 = $this->GetX();
    $this->Multicell(95, 5,utf8_decode('A FAVOR DE: '.$razonSoc),0,'L');
    $y2 = $this->GetY();
    $alto_de_fila = $y2-$y1;
    $posicionX = $x1 + 95;
    $this->SetXY($posicionX,$y1);
    $this->Cell(85, 5,utf8_decode('NIT: '.$nit),0, 0, 'R');

    
  }
}
// Creación del objeto de la clase heredada
$pdf = new PDF_MC_Table();  //Cabeza

$nb = $pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);

$usuario = $_SESSION['usuario'];
$pdf->Ln(15);
    
$pdf->SetFont('Arial','B',10,0,'C');
$pdf->Cell(75,5,utf8_decode('Rubro'),1,0,'C');
$pdf->Cell(75,5,utf8_decode('Fuente'),1,0,'C');
$pdf->Cell(40,5,utf8_decode('Valor'),1,0,'C'); //Del comprobante

$pdf->Ln(5);
//Cabecera para Página 1

$pdf->SetFont('Arial','',8);

//Consulta SQL
$sqlDetall = "SELECT  detComP.id_unico, 
        rub.nombre, 
        detComP.valor, 
        rubFue.id_unico, 
        fue.nombre, 
        rub.codi_presupuesto as numRubro, fue.id_unico,IF(CONCAT_WS(' ',
tr.nombreuno,
tr.nombredos,
tr.apellidouno,
tr.apellidodos) 
IS NULL OR CONCAT_WS(' ',
tr.nombreuno,
tr.nombredos,
tr.apellidouno,
tr.apellidodos) = '',
(tr.razonsocial),
CONCAT_WS(' ',
tr.nombreuno,
tr.nombredos,
tr.apellidouno,
tr.apellidodos)) AS NOMBRE , 
cpa.numero, tca.codigo , 
detComP.comprobanteafectado 


FROM gf_detalle_comprobante_pptal detComP 
left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
left join gf_fuente fue on fue.id_unico = rubFue.fuente 
left join gf_comprobante_pptal compP on detComP.comprobantepptal = compP.id_unico
left join gf_tercero tr on tr.id_unico = compP.tercero 
LEFT JOIN gf_detalle_comprobante_pptal da ON da.id_unico = detComP.comprobanteafectado 
LEFT JOIN gf_comprobante_pptal cpa ON da.comprobantepptal = cpa.id_unico 
LEFT JOIN gf_tipo_comprobante_pptal tca ON tca.id_unico = cpa.tipocomprobante 
where detComP.comprobantepptal =".$idpptal;

$detalle = $mysqli->query($sqlDetall);

  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);

  $totalValor = 0;
$pdf->SetFont('Arial','',8);
$tipocomprobanteA="";
while ($rowDetall = mysqli_fetch_row($detalle)) 
{
  $saldDisp = 0;
  $totalAfec = 0;
  $queryDetAfe = "SELECT valor   
  FROM gf_detalle_comprobante_pptal   
  WHERE comprobanteafectado = ".$rowDetall[0];
  $detAfec = $mysqli->query($queryDetAfe);
  $totalAfe = 0;
  while($rowDtAf = mysqli_fetch_row($detAfec))
  {
    $totalAfec += $rowDtAf[0];
  }
                      
  $saldDisp = $rowDetall[2] - $totalAfec;
  $valorPpTl = $saldDisp;


  if($rowDetall[10] != 0)
  { 
    $tipocomprobanteA = $rowDetall[9];
    $numComprobanteAfectado = $rowDetall[8];
  }
  else
  {
    $numComprobanteAfectado = '';
  }
  
  
  $numRub = $rowDetall[5].' - '.ucwords(mb_strtolower($rowDetall[1]));
  $fuente = $rowDetall[6].' - '.ucwords(mb_strtolower($rowDetall[4]));
  $valor  = $rowDetall[2];
  $ben = ucwords(mb_strtolower($rowDetall[7]));
          
  $x = $pdf->GetX();
  $y = $pdf->GetY();
  $pdf->MultiCell(75,4,utf8_decode($numRub),0,'J');
  $y2 = $pdf->GetY();
  $h= $y2-$y;
  $px = $x+75;
  $pdf->SetXY($px, $y);
  
  $x1 = $pdf->GetX();
  $y1 = $pdf->GetY();
  $pdf->MultiCell(75,4,utf8_decode($fuente),0,'J');
  $y21 = $pdf->GetY();
  $h1= $y21-$y1;
  $px1 = $x1+75;
  $pdf->SetXY($px1, $y1);
  
 
  
  $x3 = $pdf->GetX();
  $y3 = $pdf->GetY();
  $pdf->MultiCell(40,4,number_format($valor, 2, '.', ','),0,'R');
  $y23 = $pdf->GetY();
  $h3= $y23-$y3;
  $px3 = $x3+40;
  $pdf->SetXY($px3, $y3);
  
  $alt = max($h, $h1,  $h3);
  $pdf->SetXY($x, $y);
  $pdf->MultiCell(75,$alt,'',1,'C');
  $pdf->SetXY($x+75, $y);
  $pdf->MultiCell(75,$alt,'',1,'C');
  $pdf->SetXY($x+150, $y);
  $pdf->MultiCell(40,$alt,'',1,'C');
  
  $totalValor=$totalValor+$valor;
}
$pdf->SetFont('Arial','B',8);
$pdf->Cell(160,5,"TOTAL:",0,0,'R'); //Rubro
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,5,number_format($totalValor, 2, '.', ','),0,0,'R'); //Valor Sí.

$altod = $pdf->GetPageHeight();
$altoP = $pdf->GetY();
$altoC = $altod-$altoP; 
$pdf->LN(5);
if($altoC<80){
  $pdf->AddPage();
}
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,0,utf8_decode('Tipo de contratación:'),0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(150,0,utf8_decode($tipoContra),0,0,'L'); //Clase contrato.

$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,0,utf8_decode('Número del contrato:'),0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(150,0,utf8_decode($numContra),0,0,'L'); //Número de contrato

$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,0,utf8_decode('Objeto del contrato:'),0);
$pdf->SetFont('Arial','',10);
$pdf->Multicell(150,5,utf8_decode($descripcion),0,'J'); //Descripción

$pdf->Ln(5);

 $fecha_div = explode("-", $fechaComp);
    $diaSb = $fecha_div[2];
    $mesSb = $fecha_div[1];
    $mesSb = (int)$mesSb;
    $anioSb = $fecha_div[0];

    $fechaCompF = $diaSb.'/'.$mesSb.'/'.$anioSb;


$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,13,utf8_decode('Se expide en '.strtoupper($ciudadCompania).' a los '.$diaSb.' días del mes de '.$meses[$mesSb].' de '.$anioSb),0,0,'L');
$pdf->Ln(35);

$sqlTipoComp = "SELECT CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),ti.nombre,t.numeroidentificacion,trs.nombre 
      FROM gf_tipo_comprobante_pptal tcp 
      LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
      LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
      LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
      LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
      LEFT JOIN gf_tipo_responsable trs ON rd.tiporesponsable = trs.id_unico
      LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
      WHERE tcp.id_unico = $tipocomprobante";
    $tipComp = $mysqli->query($sqlTipoComp); 
    $resultF1= $mysqli->query($sqlTipoComp);
    $altofinal = $pdf->GetY();
    $altop = $pdf->GetPageHeight();
    $altofirma = $altop-$altofinal;
    $i = 0;
    #$pdf->SetY(-33);
$c=0;
while($cons = mysqli_fetch_row($resultF1)){
  $c++;
  }

  $tfirmas = ($c/2) * 33;
  
  if($tfirmas>$altofirma)
      $pdf->AddPage();
    
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
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Tipo de texto
      $pdf->SetFont('Arial','',8);
      #Impresión de tipo de documento y numero documento
      #$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
      #$this->Cell(190,2,utf8_decode($firma[1]),0,0,'L');
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
      #alto pdfial
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
      #$this->Cell(190,2,utf8_decode($firma[1]),0,0,'L');
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
  $pdf->Ln(25);
  
while (ob_get_length()){
ob_end_clean();
}

$pdf->Output(0,'Informe_certificado_recaudo_pptal.pdf',0);

?>
