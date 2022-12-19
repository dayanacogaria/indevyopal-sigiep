<?php
#########################Modificaciones#######################
#13/03/2017 |ERICA G. | ARCHIVO CREADO
################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();

$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');

$sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico
      AND comp.id_unico = ".$_GET['id'];

$comp = $mysqli->query($sqlComp);

$rowComp = mysqli_fetch_array($comp);
$nomcomp = $rowComp[1]; //Número de comprobante
$fechaComp = $rowComp[2]; //Fecha
$fechaComprobante = $rowComp[2];
$descripcion = $rowComp[3]; //Descripción
$fechaVen = $rowComp[4]; //Fecha de vencimiento
$tipocomprobante = $rowComp[5]; //id tipo comprobante
$codigo = $rowComp[6]; //Código de tipo comprobante
$nombre = $rowComp[7]; //Nombre de tipo comprobante
$nombretipoF = $rowComp[7];
$terceroComp = intval($rowComp[8]); //Tercero del comprobante

$sqlTerc = 'SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion
      FROM gf_tercero
      WHERE id_unico = '.$terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0].' '.$rowT[1].' '.$rowT[2].' '.$rowT[3];
$nit = $rowT[4];

$compania = $_SESSION['compania'];
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre
  FROM gf_tercero ter
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico
  WHERE ter.id_unico = '.$compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];

/**
* Clase pdf con herencia a fpdf
*/
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
      $this->MultiCell($w,4,$data[$i],'LTR',$a,$fill);
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
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    // Número de página
    $dia = date('d');
    $mes = date('m');
    $anio = date('Y');
    $fecha = $dia.'/'.$mes.'/'.$anio;

    $this->Cell(25,10,'Elaborado por: '.strtoupper($usuario),0);
    $this->Cell(50);
   // $this->Cell(35,10,'"DE LA MANO CON EL CAMPO"',0);

    $this->Cell(50);
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
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

// Creación del objeto de la clase heredada
$pdf = new PDF_MC_Table();        //Cabeza


$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
$fecha_div = explode("-", $fechaComp);
    $diaS = $fecha_div[2];
    $mesS = $fecha_div[1];
    $anioS = $fecha_div[0];

    $fechaComp = $diaS.'/'.$mesS.'/'.$anioS;
    $pdf->Ln(-32);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(190, 15,utf8_decode('Fecha: '.$fechaComp),1, 0, 'L');

$usuario = $_SESSION['usuario'];


//Cabecera para Página 1

$pdf->SetXY(10,13); //EStaba
$pdf->Ln(15);
$pdf->SetFont('Arial','B',12);
$pdf->SetX(50);
$pdf->MultiCell(140, 5,utf8_decode('El suscrito '.strtoupper($rol[0]).', certifica que en la fecha existe saldo presupuestal libre de afectación para respaldar el siguiente compromiso:'),0,'C');
$pdf->Ln(30); //EStaba
$pdf->SetFont('Arial','B',9,0,'C');
    $pdf->Cell(30,5,utf8_decode('Código'),1,0,'C');
    $pdf->Cell(60,5,utf8_decode('Rubro'),1,0,'C');
    $pdf->Cell(60,5,utf8_decode('Fuente'),1,0,'C');

    $pdf->Cell(20,5,utf8_decode('Crédito'),1,0,'C');
    $pdf->cellfitscale(20,5,utf8_decode('Contracrédito'),1,0,'C');
    $pdf->Ln(5);

$pdf->SetFont('Arial','',8);

$sqlDetall = 'SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro,
    fue.nombre nombreFuente, detComP.valor, rub.tipoclase, rub.nombre, fue.id_unico
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto
      left join gf_fuente fue on fue.id_unico = rubFue.fuente
      left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase
      where detComP.comprobantepptal ='.$_GET['id'];
$detalle = $mysqli->query($sqlDetall);

//$pdf->SetY(85);

$totalValor = 0;
$totalCredito = 0;
$totalContacredito = 0;
while ($rowDetall = mysqli_fetch_array($detalle))
{

  $ingresos = 0;
  $gastos = 0;

  if($rowDetall[3] < 0)
        $ingresos = $rowDetall[3];
    else
        $gastos = $rowDetall[3];

   $ingresos1=$ingresos*-1;
  #Llamado de clase de anchos y definición de anchos de columnas
  $pdf->SetWidths(array(30,60,60,20,20));
  #Definición de alinamientos y cosntrucción de array
  $pdf->SetAligns(array('L','L','L','R','R'));
  #Llamado de clase filla y consutrucción de array con datos a imprimir
  $pdf->Row(array($rowDetall[1], utf8_decode(ucwords(mb_strtolower($rowDetall[5]))), ucwords(mb_strtolower($rowDetall[6].' - '.$rowDetall[2])),number_format($gastos,2,'.',','),number_format($ingresos1,2,'.',',')));
  $pdf->Ln(5);
  $totalCredito = $totalCredito+$gastos;
  $totalContacredito = $totalContacredito+$ingresos;
}
$totalContacredito=$totalContacredito*-1;
#TOTALES
//$pdf->Ln(20);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(150,8,'TOTALES',0,0,'R');
$pdf->CellFitScale(20,8,number_format($totalCredito,2,'.',','),0,0,'R');
$pdf->CellFitScale(20,8,number_format($totalContacredito,2,'.',','),0,0,'R');
$pdf->SetX(10);

//$descripcion
$pdf->Ln(20);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,28,'',1,0,'L');
$pdf->SetX(10);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('Descripción: '),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->SetX(10);
$pdf->MultiCell(190,5,utf8_decode('                       '.$descripcion),0,'L');

$pdf->Ln(30);

$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int)$mesS;
$anioS = $fecha_div[2];

$ciudadCompania = mb_strtoupper($ciudadCompania,'utf-8');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,13,utf8_decode('Se expide en '.$ciudadCompania.' a los '.$diaS.' días del mes de '.$meses[$mesS].' de '.$anioS),0,0,'L');
#****************Consulta SQL para Firma****************#
 $sqlTipoComp = "SELECT IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     UPPER(t.razonsocial),
     CONCAT_WS(' ',
     UPPER(t.nombreuno),
     UPPER(t.nombredos),
     UPPER(t.apellidouno),
     UPPER(t.apellidodos))) AS NOMBRE, ti.nombre, t.numeroidentificacion, UPPER(car.nombre) ,
     rd.fecha_inicio, rd.fecha_fin , t.tarjeta_profesional
  FROM gf_tipo_comprobante_pptal tcp
  LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico
  LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento
  LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico
  LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
  LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
  LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
  LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
  WHERE tcp.id_unico = $tipocomprobante
  AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC";
//$fechaComp
$tipComp = $mysqli->query($sqlTipoComp);
$resultF1= $mysqli->query($sqlTipoComp);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop-$altofinal;
//$rowTipComp = mysqli_fetch_array($tipComp);
$pdf->SetY(-40);
$c=0;
while($cons = mysqli_fetch_row($resultF1)){
  $c++;
  }

  $tfirmas = ($c/2) * 33;

  if($tfirmas>$altofirma)
      $pdf->AddPage();
     $pdf->SetY(-40);
    $xt=10;
    while($firma = mysqli_fetch_row($tipComp)){

        if(!empty($firma[5])){
            if($fechaComprobante <=$firma[5]){

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
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                         #Impresión de responsable de documento
                        $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','',8);
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de cargo de responsable de documento
                        if(!empty($firma[6])){
                        $pdf->Cell(190,2,utf8_decode('T.P:'.$firma[6]),0,0,'L');
                        } else {
                            $pdf->Cell(190,2,utf8_decode(''),0,0,'L');
                        }
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
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de responsable de documento
                        $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','',8);
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de cargo de responsable de documento
                       if(!empty($firma[6])){
                        $pdf->Cell(190,2,utf8_decode('T.P:'.$firma[6]),0,0,'L');
                        } else {
                            $pdf->Cell(190,2,utf8_decode(''),0,0,'L');
                        }
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
        }  elseif(!empty($firma[4]) ) {

                if($fechaComprobante >= $firma[4]){
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
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                         #Impresión de responsable de documento
                        $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','',8);
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de cargo de responsable de documento
                        if(!empty($firma[6])){
                        $pdf->Cell(190,2,utf8_decode('T.P:'.$firma[6]),0,0,'L');
                        } else {
                            $pdf->Cell(190,2,utf8_decode(''),0,0,'L');
                        }
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
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de responsable de documento
                        $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','',8);
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de cargo de responsable de documento
                       if(!empty($firma[6])){
                        $pdf->Cell(190,2,utf8_decode('T.P:'.$firma[6]),0,0,'L');
                        } else {
                            $pdf->Cell(190,2,utf8_decode(''),0,0,'L');
                        }
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
        } else {
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
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                         #Impresión de responsable de documento
                        $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','',8);
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xm);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de cargo de responsable de documento
                        if(!empty($firma[6])){
                        $pdf->Cell(190,2,utf8_decode('T.P:'.$firma[6]),0,0,'L');
                        } else {
                            $pdf->Cell(190,2,utf8_decode(''),0,0,'L');
                        }
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
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de responsable de documento
                        $pdf->Cell(190,2,utf8_decode($firma[3]),0,0,'L');
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','',8);
                        #Salto de linea
                        $pdf->Ln(3);
                        $pdf->setX($xn);
                        #Tipo de texto
                        $pdf->SetFont('Arial','B',8);
                        #Impresión de cargo de responsable de documento
                       if(!empty($firma[6])){
                        $pdf->Cell(190,2,utf8_decode('T.P:'.$firma[6]),0,0,'L');
                        } else {
                            $pdf->Cell(190,2,utf8_decode(''),0,0,'L');
                        }
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

  }
while (ob_get_length()) {
ob_end_clean();
}
$pdf->Output(0,'Informe_certificado_disponibilidad_pptal ('.$nomcomp.').pdf',0);
?>

