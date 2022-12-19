<?php
 
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');     
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
#########################Modificaciones#######################
#08-02-2017|4:34|Jhon Numpaque
#Cambio de tipo de nombre
$sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_SESSION['id_comp_pptal_EO'];

$comp = $mysqli->query($sqlComp);
    
$rowComp = mysqli_fetch_array($comp);
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$terceroComp = intval($rowComp[8]); //Tercero del comprobante
$idComp = $rowComp[0];
$sqlTerc = "SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion 
      FROM gf_tercero
      WHERE id_unico = ".$terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0].' '.$rowT[1].' '.$rowT[2].' '.$rowT[3]; 
$nit = $rowT[4]; 

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
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    // Número de página 
        $dia = date('d');
        $mes = date('m');
        $anio = date('Y');
        $fecha = $dia.'/'.$mes.'/'.$anio;
        //$this->Cell(25,10,'Fecha: '.date('d-m-Y'),0,0,'L');
        $this->Cell(25,10,'Fecha: '.$fecha,0,0,'L');
        $this->Cell(50);
        $this->Cell(35,10,'Maquina '.  gethostname(),0);
        $this->Cell(40);
        $this->Cell(30,10,'Usuario '.get_current_user(),0);
        $this->Cell(50);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0);
  }
  #Funcón cabeza de la página
  function Header()
  { 
    $dia = date('d');
    $mes = date('m');
    $anio = date('Y');
    $fecha = $dia.'/'.$mes.'/'.$anio;
    // Logo
    
    $this->SetFont('Arial','B',10);
    $this->Cell(100);
        // Título
   

    /* */$this->Ln(4);
    $this->Cell(240,10,'',0);
    $this->SetFont('Arial','',8);
    $this->Cell(15,10,'',0); 

    $this->Ln(30);
    $this->SetFont('Arial','B',10);
    $this->Cell(190, 5,utf8_decode('Información Tercero'),1, 0, 'C');

    $this->Ln(5);
    $this->Cell(30, 30,utf8_decode(''),1, 0);
    $this->Cell(160, 30,utf8_decode(''),1, 0);

    $this->Ln(1);
    $this->Cell(50, 5,utf8_decode('Cédula / NIT:'),0, 0, 'L'); 
    $this->Cell(40, 5,utf8_decode($GLOBALS['nit']),0, 0, 'L');

    $this->Ln(5);
    $this->Cell(50, 2,utf8_decode('Razón Social:'),0, 0, 'L');
    $this->Cell(50, 5,utf8_decode($GLOBALS['razonSoc']),0, 0, 'L');

    $this->Ln(5);
    $this->Cell(50, 5,utf8_decode('Objeto:'),0, 0, 'L');

    $this->Ln(5);
    $this->Cell(50, 5,utf8_decode('Dirección:'),0, 0, 'L');

    $this->Ln(18);
    
    // rubro1, fuente4, tercero, proyecto5, valor2, valor afectado.
    $this->SetFont('Arial','B',7,0,'C');
    $this->Cell(40,5,'Rubro',1,0,'C');
    $this->Cell(50,5,'Fuente',1,0,'C');
    $this->Cell(30,5,'Tercero',1,0,'C');
    $this->Cell(30,5,'Proyecto',1,0,'C');
    $this->Cell(20,5,'Valor',1,0,'C');
    $this->Cell(20,5,'Valor Afectado',1,0,'C');
  }
}
// Creación del objeto de la clase heredada
$pdf = new PDF_MC_Table();        //Cabeza


$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);

//Cabecera para Página 1
$pdf->SetXY(180,8);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5,utf8_decode('Código SGC'),0,0,'L'); // Fecha: Descripción

$pdf->SetXY(180,14);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5,utf8_decode('Versión SGC'),0,0,'L'); // Fecha: Descripción

$pdf->SetXY(180,20);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5,utf8_decode('Fecha SGC'),0,0,'L'); // Fecha: Descripción


//Cabecera para Página 1
//$pdf->SetXY(125,13);
$pdf->SetXY(10,13);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190, 5,utf8_decode(ucwords(strtolower($nombre))),0,0,'C');

$pdf->SetXY(10,26);
$pdf->Cell(190, 0,'',1,0,''); //Línea 


$pdf->SetXY(10,28);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 5,'Fecha Comprobante: ',1,0,'L'); // Fecha: Descripción

$pdf->SetXY(40,28);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5, $fechaComp,1,0,'L'); // Fecha: Descripción

///===================================================
$pdf->SetXY(132,28);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(35, 5,'No. Comprobante: ',1,0,'L'); // Fecha: Descripción

$pdf->SetXY(10,34);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 5,'Fecha vencimiento: ',1,0,'L'); // Fecha: Descripción

$pdf->SetXY(40,34);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5,$fechaVen,1,0,'L'); // Fecha: Descripción

//======================================================

$pdf->SetXY(132,34);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(35, 5,'Comprobante Afectado: ',1,0,'L'); // Fecha: Descripción
//Fin Cabecera Página 1

$pdf->SetXY(167,34);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 5,55555,1,0,'L'); // Fecha: Descripción

$pdf->SetXY(167,28);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 5,$nomcomp,1,0,'L'); // Fecha: Descripción




$pdf->SetFont('Arial','',7);

//Consulta SQL
$sqlDetall = "SELECT detComP.id_unico, rub.nombre, detComP.valor, rubFue.id_unico, fue.nombre, proy.nombre, detComP.tercero, detComP.proyecto      
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      left join gf_tercero terc on terc.id_unico = detComP.tercero 
      left join gf_proyecto proy on proy.id_unico = detComP.proyecto
      where detComP.comprobantepptal =  ".$_SESSION['id_comp_pptal_EO'];
$detalle = $mysqli->query($sqlDetall);

// Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);

$pdf->SetY(88);
// rubro, fuente, tercero, proyecto, valor, valor afectado.
while ($rowDetall = mysqli_fetch_array($detalle)) 
{ 
  $queryTerc = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                        FROM gf_tercero ter 
                        LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                        WHERE ter.id_unico = '$rowDetall[6]'";
                      $terc = $mysqli->query($queryTerc);
                      $rowTer = mysqli_fetch_row($terc);

                      if(in_array($rowTer[7], $natural))
                          {
                            
                          $tercero = ucwords(strtolower($rowTer[1])).' '.ucwords(strtolower($rowTer[2])).' '.ucwords(strtolower($rowTer[3])).' '.ucwords(strtolower($rowTer[4])).' '.$rowTer[6];
                        
                          }
                          elseif (in_array($rowTer[7], $juridica))
                          {
                            $tercero = ucwords(strtolower($rowTer[5])).' '.$rowTer[6]; 
                          }

  $codp = $codp + 1;
  #$pdf->Cell(40,5,utf8_decode($rowDetall[1]),0,0,'L'); //Rubro
  #$pdf->Cell(30,5,utf8_decode($rowDetall[4]),0,0,'L'); //Fuente
  #$pdf->Cell(30,5,utf8_decode($tercero),0,0,'L'); //Tercero
  #$pdf->Cell(30,5,utf8_decode($rowDetall[5]),0,0,'L'); //Proyecto
  $valor = number_format($rowDetall[2], 2, '.', ',');
  #$pdf->Cell(20,5,$valor,0,0,'R'); //Valor

  $valorAfectado = afectacionRegistro($rowDetall[0], $rowDetall[3], 14);
  $valorAfectado = number_format($valorAfectado, 2, '.', ',');
  #$pdf->Cell(20,5,$valorAfectado,0,0,'R'); //Valor Afectado
  $pdf->SetWidths(array(40,50,30,30,20,20));
  #Definición de alinamientos y cosntrucción de array
  $pdf->SetAligns(array('L','L','L','R','R'));
  #Llamado de clase filla y consutrucción de array con datos a imprimir
  $pdf->Row(array(utf8_decode($rowDetall[1]),utf8_decode($rowDetall[4]),utf8_decode($tercero),utf8_decode($rowDetall[5]),$valor,$valorAfectado));
  $pdf->Ln(5);
}

//Consulta SQL
$sqlTipoComp = "SELECT DISTINCT CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),t.numeroidentificacion,car.nombre 
FROM gf_tipo_comprobante_pptal tcp 
LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico
LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
LEFT JOIN gf_tipo_responsable trs ON rd.tiporesponsable = trs.id_unico 
LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
WHERE tcp.codigo = 'ROP' AND tipRel.nombre = 'Firma'";
$tipComp = $mysqli->query($sqlTipoComp); 
$resultF1= $mysqli->query($sqlTipoComp);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop-$altofinal;

//$rowTipComp = mysqli_fetch_array($tipComp);
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
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Tipo de texto
      $pdf->SetFont('Arial','',8);
      #Impresión de tipo de documento y numero documento
      #$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
      #$pdf->Cell(190,2,utf8_decode($firma[1]),0,0,'L');
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xm);
      #Tipo de texto
      $pdf->SetFont('Arial','B',8);
      #Impresión de cargo de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[2]),0,0,'L');
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
      #$pdf->Cell(190,2,utf8_decode($firma[1]),0,0,'L');
      #Salto de linea
      $pdf->Ln(3);
      $pdf->setX($xn);
      #Tipo de texto
      $pdf->SetFont('Arial','B',8);
      #Impresión de cargo de responsable de documento
      $pdf->Cell(190,2,utf8_decode($firma[2]),0,0,'L');
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

ob_end_clean();
$pdf->Output(0,'Informe_obligacion_presupuestal ('.$nomcomp.').pdf',0);

?>

