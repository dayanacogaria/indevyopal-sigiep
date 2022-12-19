<?php
##############################################################################################################
#                                                                                       INFORME DISPONIBILIDAD
#                                                                                                    FOMVIDU
##############################################################################################################
#07/09/2017 | Erica G.   | Firmas y reestructuracion del codigo 
#26/05/2017 | ERICA G. | SE QUITO LA VARIABLE DE SESSION 
#22/03/2017 | ERICA G. | MODIFICACIONES DISEÑO, DIGITO VERIFICACION
#10/03/2017 | ERICA G. | ARCHIVO CREADO
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
    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, 
    comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, 
    comp.tercero,  UPPER(comp.usuario), DATE_FORMAT(comp.fecha_elaboracion,'%d/%m%/%Y')  
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND md5(comp.id_unico) = '$id'";
} else {

    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, 
    tipCom.codigo, tipCom.nombre, comp.tercero ,  UPPER(comp.usuario), DATE_FORMAT(comp.fecha_elaboracion,'%d/%m%/%Y')  
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND (comp.id_unico) = " . $_SESSION['id_comp_pptal_ED'];
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
$usuario = $rowComp[9];
$fechaElaboracion = $rowComp[10];

$sqlTerc = 'SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion 
      FROM gf_tercero
      WHERE id_unico = ' . $terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0] . ' ' . $rowT[1] . ' ' . $rowT[2] . ' ' . $rowT[3];
$nit = $rowT[4];

$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre , ter.razonsocial, ter.numeroidentificacion , 
    ter.digitoverficacion , d.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  LEFT JOIN gf_departamento d ON ciu.departamento = d.id_unico 
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = ucwords(mb_strtolower($rowLogo[1].' '.$rowLogo[5]));
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

        if ($ruta != '') {
            $this->Image('../' . $ruta, 20, 5, 20);
        }   
        
        $this->SetFont('Arial', 'B', 10);
        $y = $this->GetY();
        $this->SetX(60);
        $this->Cell(140, 5, utf8_decode('SISTEMA INTEGRADO DE GESTIÓN'),0,0, 'C');
        $this->Ln(7);
        $this->SetX(60);        
        $this->Cell(140, 5, utf8_decode('PROCESO GESTIÓN FINANCIERA'), 0, 0, 'C');
        $this->Ln(7);
        $this->SetX(60);
        $this->Cell(140, 5, utf8_decode('SOLICITUD DE CERTIFICADO DE DISPONIILIDAD PRESUPUESTAL'), 0, 0, 'C');
        $this->Ln(7);


        $this->SetY($y-5);
        $this->Cell(40,25, '', 1, 0, 'C');
        $this->SetXY(50,$y-5);
        $this->Cell(150, 10, '', 1, 0, 'C');
        $this->SetXY(50,$y+5);
        $this->Cell(150, 7, '', 1, 0, 'C');
        $this->SetXY(50,$y+12);
        $this->Cell(150, 8, '', 1, 0, 'C');
        $this->Ln(8);

        $this->Cell(40, 7, utf8_decode('CÓDIGO: AP2-FO-018'), 1, 0, 'C');
        $this->Cell(40, 7, utf8_decode('VERSIÓN:02'), 1, 0, 'C');
        $this->Cell(70, 7, utf8_decode('FECHA ACTUALIZACIÓN: 22/01/2021'), 1, 0, 'C');
        $this->Cell(40, 7, utf8_decode('PÁGINA ' . $this->PageNo() . '/{nb}'), 1, 0, 'C');
        $this->Ln(7);
    }

// Pie de página
    function Footer() {
        global $usuario;
        global $ciudadCompania;
        $this->SetY(-20);
        

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(63, 10, 'Elaborado por: ' . strtoupper($usuario), 0);
        $this->Cell(64, 10, '', 0, 0, 'C');
    }

}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial', 'B', 8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(30, 4, utf8_decode('DEPENDENCIA SOLICITANTE:'),  0, 'L');
$pdf->SetXY($x+70, $y);
$pdf->MultiCell(30, 4, utf8_decode('CÓDIGO DEL DOCUMENTO:'),  0, 'L');
$pdf->SetXY($x+130, $y);
$pdf->MultiCell(30, 4, utf8_decode('NÚMERO DEL DOCUMENTO:'),  0, 'L');
$pdf->SetXY($x+160, $y);
$pdf->MultiCell(30, 4, utf8_decode($nomcomp),  0, 'L');

$pdf->SetXY($x, $y);
$pdf->Cell(70, 8, '', 1, 0, 'L');
$pdf->Cell(60, 8, '', 1, 0, 'L');
$pdf->Cell(60, 8, '', 1, 0, 'L');
$pdf->Ln(8);
$pdf->Cell(190, 8, utf8_decode('NÚMERO DE REGISTRO PROYECTO CÓDIGO SSEPI: '), 1, 0, 'L');
$pdf->Ln(8);
$pdf->Cell(190, 8, utf8_decode('NOMBRE DEL PROYECTO: '), 1, 0, 'L');
$pdf->Ln(8);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(190, 5, utf8_decode('OBJETO: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->SetX(35);
$pdf->MultiCell(165, 5, utf8_decode($descripcion), 0, 'J');
$pdf->Ln(5);
$y2 = $pdf->GetY();
$h = $y2 - $y;
$pdf->SetXY($x, $y);
$pdf->Cell(190, $h, '', 1, 0, 'L');
$pdf->SetX(10);
$pdf->Ln($h);
$pdf->SetFont('Arial', 'B', 8);

$pdf->Cell(30, 5, utf8_decode('FECHA:'), 1,0, 'L');
$pdf->SetFont('Arial', '', 9);
$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$anioS = $fecha_div[2];
$pdf->Cell(160, 5, utf8_decode($fechaComp), 1,0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(190, 5, utf8_decode('IMPUTACIÓN PRESUPUESTAL'), 1,0, 'C');

$sqlDetall = "SELECT detComP.id_unico, rub.codi_presupuesto numeroRubro, 
    rub.nombre nombreRubro, detComP.valor, rubFue.id_unico, fue.nombre, detComP.saldo_disponible 
      FROM gf_detalle_comprobante_pptal detComP 
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where (detComP.comprobantepptal) ='$idcomprobante'";
$detalle = $mysqli->query($sqlDetall);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9, 0, 'C');
$pdf->Cell(60, 5, 'Rubro', 1, 0, 'C');
$pdf->Cell(60, 5, 'Fuente', 1, 0, 'C');
$pdf->Cell(35, 5, 'Saldo Disponible', 1, 0, 'C');
$pdf->Cell(35, 5, 'Valor', 1, 0, 'C');

$pdf->Ln(5);

$totalValor = 0;
$pdf->SetFont('Arial', '', 8);
while ($rowDetall = mysqli_fetch_array($detalle)) {

    $totalValor += $rowDetall[3];

    $saldoDisponible = $rowDetall[6];
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
    $pdf->Cell(35, $alt, utf8_decode($saldoDis), 1, 0, 'R');
    $pdf->Cell(35, $alt, utf8_decode($valorR), 1, 0, 'R');

    $pdf->Ln($alt);
    $altY = $pdf->GetY();
    if ($altY > 245) {
        $pdf->AddPage();
    }
}




$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(155, 5, 'TOTAL DISPONIBILIDAD:', 0, 0, 'R'); //Rubro
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R'); //Valor Sí.
$pdf->SetFont('Arial', '', 10);
//$descripcion
$pdf->Ln(10);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, utf8_decode('Son: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(35);



if($totalValor==1000000000){
    $pdf->MultiCell(155, 5, utf8_decode('MIL MILLONES DE PESOS 00 M.C.'), 0, 'J');
}else{
    $valorLetras = numtoletras($totalValor);
    $pdf->MultiCell(155, 5, utf8_decode($valorLetras), 0, 'J');
}
$pdf->SetX(10);
$pdf->Ln(10);
$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int) $mesS;
$anioS = $fecha_div[2];

$pdf->Ln(25);

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
$resultF1 = $mysqli->query($sqlTipoComp);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop - $altofinal;

$c = 0;
while ($cons = mysqli_fetch_row($resultF1)) {
    $c++;
}

$tfirmas = ($c / 2) * 33;

if ($tfirmas > $altofirma)
    $pdf->AddPage();
$xt = 10;
while ($firma = mysqli_fetch_row($tipComp)) {

    if (!empty($firma[5])) {
        if ($fechaComprobante <= $firma[5]) {

            if ($xt < 50) {
                #Construcción de linea firma
                $xm = 10;
                $pdf->setX($xm);
                $pdf->SetFont('Arial', 'B', 10);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                $pdf->setX($xm);
                #Obtención de alto final        
                $x2 = $pdf->GetX();
                #Posición final de firma 2    
                $pdf->Ln(0);
                $xt = 120;
            } else {
                $xn = 120;
                $pdf->SetY($y);
                #Construcción de linea firma
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX($xn);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                #alto inicial
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                #Obtención de alto final      
                $x2 = $pdf->GetX();
                #Posición del ancho     
                $posicionY = $y - 20;
                #Ubicación firma 2
                $pdf->SetXY($x2, $posicionY);
                #Posición final de firma
                $xt = 0;
            }
        }
    } elseif (!empty($firma[4])) {

        if ($fechaComprobante >= $firma[4]) {
            if ($xt < 50) {
                #Construcción de linea firma
                $xm = 10;
                $pdf->setX($xm);
                $pdf->SetFont('Arial', 'B', 10);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xm);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                $pdf->setX($xm);
                #Obtención de alto final        
                $x2 = $pdf->GetX();
                #Posición final de firma 2    
                $pdf->Ln(0);
                $xt = 120;
            } else {
                $xn = 120;
                $pdf->SetY($y);
                #Construcción de linea firma
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX($xn);
                #Linea para firma
                $pdf->Cell(60, 0, '', 1);
                #Varibles x,y
                $x = $pdf->GetX();
                #alto inicial
                $y = $pdf->GetY();
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de responsable de documento
                $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', '', 8);
                #Salto de linea
                $pdf->Ln(3);
                $pdf->setX($xn);
                #Tipo de texto
                $pdf->SetFont('Arial', 'B', 8);
                #Impresión de cargo de responsable de documento
                if (!empty($firma[6])) {
                    $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
                } else {
                    $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
                }
                #Obtención de alto final      
                $x2 = $pdf->GetX();
                #Posición del ancho     
                $posicionY = $y - 20;
                #Ubicación firma 2
                $pdf->SetXY($x2, $posicionY);
                #Posición final de firma
                $xt = 0;
            }
        }
    } else {
        if ($xt < 50) {
            #Construcción de linea firma
            $xm = 10;
            $pdf->setX($xm);
            $pdf->SetFont('Arial', 'B', 10);
            #Linea para firma
            $pdf->Cell(60, 0, '', 1);
            #Varibles x,y
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xm);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de cargo de responsable de documento
            if (!empty($firma[6])) {
                $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
            } else {
                $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
            }
            $pdf->setX($xm);
            #Obtención de alto final        
            $x2 = $pdf->GetX();
            #Posición final de firma 2    
            $pdf->Ln(0);
            $xt = 120;
        } else {
            $xn = 120;
            $pdf->SetY($y);
            #Construcción de linea firma
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setX($xn);
            #Linea para firma
            $pdf->Cell(60, 0, '', 1);
            #Varibles x,y
            $x = $pdf->GetX();
            #alto inicial
            $y = $pdf->GetY();
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[0]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de responsable de documento
            $pdf->Cell(190, 2, utf8_decode($firma[3]), 0, 0, 'L');
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', '', 8);
            #Salto de linea
            $pdf->Ln(3);
            $pdf->setX($xn);
            #Tipo de texto
            $pdf->SetFont('Arial', 'B', 8);
            #Impresión de cargo de responsable de documento
            if (!empty($firma[6])) {
                $pdf->Cell(190, 2, utf8_decode('T.P:' . $firma[6]), 0, 0, 'L');
            } else {
                $pdf->Cell(190, 2, utf8_decode(''), 0, 0, 'L');
            }
            #Obtención de alto final      
            $x2 = $pdf->GetX();
            #Posición del ancho     
            $posicionY = $y - 20;
            #Ubicación firma 2
            $pdf->SetXY($x2, $posicionY);
            #Posición final de firma
            $xt = 0;
        }
    }
}


$pdf->SetFont('Arial', '', 10);


$pdf->Ln(40);
$pdf->Cell(160, 5, utf8_decode("Vigencia de la presente disponibilidad, ".$ciudadCompania.", Diciembre 31 del año ".$anioS), 0, 'J');
        


while (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
?>

