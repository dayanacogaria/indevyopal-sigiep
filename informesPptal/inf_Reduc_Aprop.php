<?php
##############################################################################################################
#   *************** INFORME REDUCCION A APROPIACION **************
#                             GENERAL
##############################################################################################################
#015/02/2018 | Erica G.   | Reestructuracion del codigo 
##############################################################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
ob_start();
session_start();

$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND md5(comp.id_unico) = '$id'";
} else {

    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND (comp.id_unico) = " . $_SESSION['idComPtalReduc'];
}


$comp = $mysqli->query($sqlComp);

$rowComp = mysqli_fetch_array($comp);
$idcomprobante = $rowComp[0];
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$terceroComp = intval($rowComp[8]); //Tercero del comprobante
$fechaComprobante = $rowComp[2]; //Fecha       
$sqlTerc = 'SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion 
      FROM gf_tercero
      WHERE id_unico = ' . $terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0] . ' ' . $rowT[1] . ' ' . $rowT[2] . ' ' . $rowT[3];
$nit = $rowT[4];

$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre , ter.razonsocial, ter.numeroidentificacion , 
    ter.digitoverficacion 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];
$comp = $rowLogo[2];
if (empty($rowLogo[4])) {
    $nitcom = $rowLogo[3];
} else {
    $nitcom = $rowLogo[3] . ' - ' . $rowLogo[4];
}

$fecha_div = explode("-", $fechaComp);
$diaS = $fecha_div[2];
$mesS = $fecha_div[1];
$anioS = $fecha_div[0];

$fechaComp = $diaS . '/' . $mesS . '/' . $anioS;

$fecha_divV = explode("-", $fechaVen);
$diaSV = $fecha_divV[2];
$mesSV = $fecha_divV[1];
$anioSV = $fecha_divV[0];

$fechaV = $diaSV . '/' . $mesSV . '/' . $anioSV;
$anio = $_SESSION['anno'];
$anio2 = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = " . $anio;
$anio2 = $mysqli->query($anio2);
$anio1 = mysqli_fetch_row($anio2);
$anio1 = $anio1[0];


class PDF extends FPDF {

    function Header() {

        global $fechaComp;
        global $ruta;
        global $comp;
        global $nitcom;
        global $nombre;
        global $nomcomp;
        global $anio1;
        global $numP;

        $numP = $this->PageNo();

        if ($ruta != '') {
            $this->Image('../' . $ruta, 10, 8, 25);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(100);
        $this->SetXY(28, 13); //EStaba
        $this->SetFont('Arial', 'B', 13);
        $this->MultiCell(175, 7, utf8_decode(mb_strtoupper($comp)), 0, 'C');
        $this->SetX(28);
        $this->Cell(175, 5, utf8_decode('NIT: ' . $nitcom), 0, 0, 'C');
        $this->Ln(10);
        $this->SetX(28);
        $this->Cell(175, 5, mb_strtoupper($nombre), 0, 0, 'C');
        $this->Ln(7);
        $this->SetX(28);
        $this->Cell(175, 5, utf8_decode('Número: ' . $nomcomp), 0, 0, 'C');


        $this->Ln(10);
    }

// Pie de página
    function Footer() {
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(63, 10, 'Elaborado por: ' . strtoupper($usuario), 0);
        $this->Cell(64, 10, '', 0, 0, 'C');
        $this->Cell(63, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 10);
$usuario = $_SESSION['usuario'];

$pdf->SetFont('Arial', 'B', 12);

$pdf->SetX(20);
$pdf->MultiCell(200, 5, utf8_decode('El suscrito , certifica que en la fecha existe saldo presupuestal libre de
afectación para respaldar el siguiente compromiso:'), 0, 'C');
$pdf->Ln(5);
$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$anioS = $fecha_div[2];
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 5, utf8_decode('Fecha:'), 0, 'L');
$pdf->Cell(160, 5, utf8_decode($fechaComp), 0, 'L');
$pdf->Ln(5);
$pdf->Cell(190, 5, utf8_decode('Concepto: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(35);
$pdf->MultiCell(165, 5, utf8_decode($descripcion), 0, 'J');
$pdf->Ln(5);
$y2 = $pdf->GetY();
$h = $y2 - $y;
$pdf->SetXY($x, $y);
$pdf->Cell(190, $h, '', 1, 0, 'L');
$pdf->Ln($h);

$sqlDetall = "SELECT detComP.id_unico, 
        rub.codi_presupuesto numeroRubro, 
        rub.nombre nombreRubro, 
        detComP.valor, 
        rubFue.id_unico, 
        fue.nombre, 
        detComP.saldo_disponible, rub.tipoclase 
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where (detComP.comprobantepptal) ='$idcomprobante'";
$detalle = $mysqli->query($sqlDetall);

$pdf->Ln(4);
$pdf->Cell(240, 10, '', 0);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(15, 10, '', 0);


$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 9, 0, 'C');
$pdf->Cell(60, 5, 'Rubro', 1, 0, 'C');
$pdf->Cell(60, 5, 'Fuente', 1, 0, 'C');
$pdf->Cell(35, 5, utf8_decode('Crédito'), 1, 0, 'C');
$pdf->Cell(35, 5, utf8_decode('Contracrédito'), 1, 0, 'C');

$pdf->Ln(5);

$totalValorI = 0;
$totalValorG = 0;
$pdf->SetFont('Arial', '', 8);
while ($rowDetall = mysqli_fetch_array($detalle)) {

    

    $codiRub = $rowDetall[1];
    $nombreRub = ($rowDetall[2]);
    $fuente = ($rowDetall[5]);
    $valorR = number_format($rowDetall[3], 2, '.', ',');
    $saldoDis = number_format($saldoDisponible, 2, '.', ',');
    #Impresión de varibles y llamado de metodo
    if (strlen($nombreRub) > 35) {
        $altY = $pdf->GetY();
        if ($altY > 245) {
            $pdf->AddPage();
        }
    }

    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->MultiCell(60, 5, utf8_decode($codiRub . ' - ' . $nombreRub), 0, 'L');
    $y2 = $pdf->GetY();
    $h = $y2 - $y1;
    $px = $x1 + 60;
    $pdf->SetXY($px, $y1);
    $y11 = $pdf->GetY();
    $x11 = $pdf->GetX();
    $pdf->MultiCell(60, 5, utf8_decode($fuente), 0, 'L');
    $y21 = $pdf->GetY();
    $h1 = $y21 - $y11;
    $px1 = $x11 + 60;
    $pdf->SetXY($px1, $y11);
    $alt = max($h, $h1);

    $pdf->SetX($x1);
    $pdf->Cell(60, $alt, utf8_decode(''), 1, 0, 'L');
    $pdf->Cell(60, $alt, utf8_decode(''), 1, 0, 'L');
    $ingresos =0;
    $gastos =0;
    if($rowDetall[7] == 6)
        $ingresos = $rowDetall[3];
    elseif($rowDetall[7] == 7)
        $gastos = $rowDetall[3];
    
    $totalValorI += $ingresos;
    $totalValorG += $gastos;
    $pdf->Cell(35, $alt, number_format($ingresos, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(35, $alt, number_format($gastos, 2, '.', ','), 1, 0, 'R');

    $pdf->Ln($alt);
    $altY = $pdf->GetY();
    if ($altY > 245) {
        $pdf->AddPage();
    }
}




$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(120, 5, 'TOTAL:', 0, 0, 'R'); //Rubro
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 5, number_format($totalValorI, 2, '.', ','), 0, 0, 'R'); //Valor Sí.
$pdf->Cell(35, 5, number_format($totalValorG, 2, '.', ','), 0, 0, 'R'); //Valor Sí.
$pdf->SetFont('Arial', '', 10);
//$descripcion
$pdf->Ln(10);

$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int) $mesS;
$anioS = $fecha_div[2];


$pdf->SetFont('Arial', 'B', 10);
$ciudadCompania = mb_strtoupper($ciudadCompania, 'utf-8');
$pdf->Cell(60, 13, utf8_decode("NOTA: Este cerficado tiene validez para su utilización hasta $fechaV"), 0, 'J');
$pdf->Ln(40);


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
     rd.fecha_inicio, rd.fecha_fin , t.tarjeta_profesional,t.firma 
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

$tipComp = $mysqli->query($sqlTipoComp); 
$i = 0;

while ($rowTipComp = mysqli_fetch_array($tipComp))
{

     if(!empty($rowTipComp[5])){
            if($fechaComprobante <=$rowTipComp[5]){
                $firmaNom[$i] = $rowTipComp[0];
                $firmaCarg[$i] = $rowTipComp[3];
                $firmaTP[$i] = $rowTipComp[6];
                $firmaImg[$i] = $rowTipComp[7];
                $i++;
            } 
     } elseif(!empty($rowTipComp[4]) ) {
                if($fechaComprobante >= $rowTipComp[4]){
                    $firmaNom[$i] = $rowTipComp[0];
                    $firmaCarg[$i] = $rowTipComp[3];
                    $firmaTP[$i] = $rowTipComp[6];
                    $firmaImg[$i] = $rowTipComp[7];
                    $i++;
                }
         
     } else {
            $firmaNom[$i] = $rowTipComp[0];
            $firmaCarg[$i] = $rowTipComp[3];
            $firmaTP[$i] = $rowTipComp[6];
            $firmaImg[$i] = $rowTipComp[7];
            $i++;
     }
}

$numFirmas = $i;

if($numFirmas > 2)
  
  $numFirmas = 2;



$pdf->Ln(24);
$pdf->SetX(20);
for($i = 0; $i <= $numFirmas; $i++)
{
  $pdf->Cell(1,0,'',0,0,'L');
  $pdf->Cell(55,0,'',1,0,'L');
  $pdf->Cell(4,0,'',0,0,'L');
}


$pdf->Ln(2);
$pdf->SetX(20);
for($i = 0; $i <=$numFirmas; $i++)
{    
    if($firmaNom[$i]=='' || $firmaNom[$i]==""){
        $pdf->Cell(60,5,utf8_decode($firmaNom[$i]),0,0,'L');
    } else {
  $pdf->CellFitScale(60,5,utf8_decode($firmaNom[$i]),0,0,'L');
    }
    
}
  

$pdf->Ln(5);
$pdf->SetX(20);
for($i = 0; $i <= $numFirmas; $i++)
{
    if($firmaCarg[$i]=='' || $firmaCarg[$i]==""){
        $pdf->Cell(60,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    } else {
        $pdf->CellFitScale(60,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    }
 
}
$pdf->Ln(4);
for($i = 0; $i < $numFirmas; $i++)
{
    if($firmaTP[$i]=='' || $firmaTP[$i]==""){
        $pdf->Cell(60,5,utf8_decode(''),0,0,'L');
    } else {
        $pdf->CellFitScale(60,5,utf8_decode('T.P. :'.$firmaTP[$i]),0,0,'L');
    }
 
}
while (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
?>

