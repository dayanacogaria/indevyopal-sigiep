<?php
###########MODIFICACIONES#################
#11/05/2017 |ERICA G. | diseño
########################################## 
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');     
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
#Array de meses
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
#Consulta para obtener los datos del comprobante
$sqlComp = "SELECT comp.id_unico,comp.numero,comp.fecha,comp.descripcion,comp.fechavencimiento,comp.tipocomprobante,tipCom.codigo,tipCom.nombre,comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_SESSION['id_comp_pptal_OP'];
$comp = $mysqli->query($sqlComp);
#Definición de array númerico con los datos devueltos por la consulta
$rowComp = mysqli_fetch_array($comp);
#Definición de variables con los valores retornados por la consulta
$nomcomp = $rowComp[1];                 //Número de comprobante
$fechaComp = $rowComp[2];               //Fecha
$descripcion = $rowComp[3];             //Descripción
$fechaVen = $rowComp[4];                //Fecha de vencimiento
$tipocomprobante = $rowComp[5];         //id tipo comprobante 
$codigo = $rowComp[6];                  //Código de tipo comprobante
$nombre = $rowComp[7];                  //Nombre de tipo comprobante
$terceroComp = intval($rowComp[8]);     //Tercero del comprobante
#Obtención de datos del tercero
$sqlTerc = "SELECT  IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' 
                    OR CONCAT(  IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                    (ter.razonsocial),                                            
                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                    ti.nombre tipoI,
                    ter.numeroidentificacion numI,
                    dir.direccion,
                    car.nombre
FROM gf_tercero ter
LEFT JOIN gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
LEFT JOIN gf_cargo_tercero cart ON cart.tercero = ter.id_unico
LEFT JOIN gf_cargo car ON cart.cargo = car.id_unico
WHERE ter.id_unico = $terceroComp";
#Ejecución de consulta
$terc = $mysqli->query($sqlTerc);
#Definición de array númerico con los datos devueltos por la consulta
$rowT = mysqli_fetch_array($terc);
$tercero = $rowT[0];
$tipoI = $rowT[1];
$numI = $rowT[2];
$direccion = $rowT[3];
$rolFirma = $rowT[4];
#Captura de la variable compañia
$compania = $_SESSION['compania'];
#Consulta para obtener los datos de la compañia
$sqlRutaLogo =  "SELECT ter.ruta_logo, ciu.nombre
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = $compania";
#Ejecución de la consulta
$rutaLogo = $mysqli->query($sqlRutaLogo);
#Definición de array númerico con los datos devueltos por la consulta
$rowLogo = mysqli_fetch_array($rutaLogo);
#Definición de variables con los valores retornados por la consulta
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];
#Definición de variables con los valores retornados por la consulta
$razonSoc = $rowLogo[2]; 
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
    #Variable de usuario
    global $usuario;
    #Posición: a 1,5 cm del final
    $this->SetY(-15);
    #Tipo de letra en negrita tamaño 8
    $this->SetFont('Arial','B',8);
    #Definición de Variable $fecha con la fecha actual
    $dia = date('d');
    $mes = date('m');
    $anio = date('Y');
    $fecha = $dia.'/'.$mes.'/'.$anio;
    #Informe elaborado por
    $this->Cell(25,10,'Elaborado por: '.strtoupper($usuario),0);
    $this->Cell(50);
    //$this->Cell(35,10,'"DE LA MANO CON EL CAMPO"',0);
    #Conteo de páginas
    $this->Cell(50);
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
  }
  #Funcón cabeza de la página
  function Header()
  { 
    #Variables Globales
    global $fechaComp;
    global $ruta;
    global $descripcion;
    global $tercero;
    global $direccion;
    global $tipoI;
    global $numI;
    global $nombre;
    global $nomcomp;
    #Deformación de variable $fechaComp
    $fecha_div = explode("-", $fechaComp);
    $diaS = $fecha_div[2];
    $mesS = $fecha_div[1];
    $anioS = $fecha_div[0];
    #Estructuración de variable de $fecha
    $fechaComp = $diaS.'/'.$mesS.'/'.$anioS;
    #Validación de ruta de logo
    if($ruta != '')
    {
      $this->Image('../'.$ruta,10,8,30);
    }     
    $this->SetFont('Arial','B',15);
    $this->Cell(190, 5,utf8_decode($nombre),0,0,'C'); 
    $this->Ln(7);
    $this->Cell(190, 5,utf8_decode('Número: '.$nomcomp),0,0,'C'); 
    #Datos del beneficiario
    
  }
}
//Consulta SQL para Firma
/* */
$sqlTipoComp = "SELECT t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, t.numeroidentificacion, car.nombre 
  FROM gf_tipo_comprobante_pptal tcp
  LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
  LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
  LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
  LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
  LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
  LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
  WHERE codigo = 'APO'
  AND tipRel.nombre = 'Firma'";
$tipComp = $mysqli->query($sqlTipoComp); 
$i = 0;
while ($rowTipComp = mysqli_fetch_array($tipComp))
{
  $nombreTercero[$i] = $rowTipComp[0].' '.$rowTipComp[1].' '.$rowTipComp[2].' '.$rowTipComp[3];
  $numIdent[$i] = $rowTipComp[4];
  $rol[$i] = $rowTipComp[5];
  $i ++;
}


// Creación del objeto de la clase heredada
$pdf = new PDF_MC_Table();        //Cabeza

 
$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
$usuario = $_SESSION['usuario'];


//Cabecera para Página 1


$pdf->Ln(12);
$pdf->SetFont('Arial','B',12);
$rolFirmas = mb_strtoupper($rolFirma,'utf-8');
$pdf->SetX(50);
$pdf->MultiCell(140, 5,utf8_decode('El suscrito '.$rolFirmas.', certifica que en la fecha exite saldo presupuestal libre de afectación para respaldar el siguiente compromiso:'),0,'C'); 
$pdf->Ln(12);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(25,5,'Nombre: ',1, 0, 'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(165,5,$tercero,1,0,'L');
    $pdf->ln(5);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(25,5,'Tipo Ident.:',1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(40,5,utf8_decode($tipoI),1,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(35,5,utf8_decode('Número Identificación:'),1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(35,5,$numI,1,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(25,5,utf8_decode('Dirección:'),1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,5,$direccion,1,0,'L');    
    #Titulo de la tabla
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'Movimiento Presupuestal',1,0,'C');
    #Titulos de columnas de la tabla
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',9,0,'C');
    $pdf->Cell(25,5,'Rubro',1,0,'C');
    $pdf->Cell(65,5,'Nombre',1,0,'C');
		$pdf->Cell(65,5,'Fuente',1,0,C);		
    $pdf->Cell(35,5,'Valor',1,0,'C');    
    $pdf->Ln(5);
$pdf->SetFont('Arial','',9);

$sqlDetall = 'SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, rub.nombre nombreRubro, detComP.valor, rubFue.id_unico,fue.nombre     
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where detComP.comprobantepptal ='.$_SESSION['id_comp_pptal_OP'];
$detalle = $mysqli->query($sqlDetall);

//$pdf->SetY(89);


$totalValor = 0;
while ($rowDetall = mysqli_fetch_array($detalle)) 
{ 
  $codp = $codp + 1;  
  $totalValor += $rowDetall[3];  
  $pdf->SetFont('Arial','',8);
  $pdf->SetWidths(array(25,65,65,35));
  $pdf->SetAligns(array('L','L','L','R'));
  $pdf->Row(array($rowDetall[1],$rowDetall[2],$rowDetall[5],number_format($rowDetall[3], 2, '.', ',')));
  $pdf->Ln(5);
}
$pdf->Cell(155,5,'Total:',1,0,'R'); //Rubro
$pdf->Cell(35,5,number_format($totalValor, 2, '.', ','),1,0,'R'); //Valor Sí.
//$descripcion
$pdf->Ln(20);
//$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,28,'',1,0,'L');
$pdf->SetX(10);

//$pdf->Ln(-2);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('Descripción: '),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->SetX(10);
$pdf->MultiCell(190,5,utf8_decode('                       '.$descripcion),0,'L');

$pdf->Ln(42);

 $fecha_div = explode("/", $fechaComp);
    $diaS = $fecha_div[0];
    $mesS = $fecha_div[1];
    $mesS = (int)$mesS;
    $anioS = $fecha_div[2];


$pdf->SetFont('Arial','B',10);
$ciudadCompania = mb_strtoupper($ciudadCompania,'utf-8');
$pdf->Cell(60,13,utf8_decode('Se expide en '.$ciudadCompania.' a los '.$diaS.' días del mes de '.$meses[$mesS].' de '.$anioS),0,0,'L');
$pdf->SetY(-33);
$sqlTipoComp1 = "SELECT CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),ti.nombre,t.numeroidentificacion, car.nombre 
  FROM gf_tipo_comprobante_pptal tcp
  LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
  LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
  LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
  LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
  LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
  LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
  LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
  WHERE tcp.codigo = 'APO'
  AND tipRel.nombre = 'Firma'";        
$tipComp = $mysqli->query($sqlTipoComp1); 
$resultF1= $mysqli->query($sqlTipoComp1);
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

while (ob_get_length()) { 
  ob_end_clean();
}   
$pdf->Output(0,'Informe_certificado_disponibilidad_pptal ('.$nomcomp.').pdf',0);

?>

