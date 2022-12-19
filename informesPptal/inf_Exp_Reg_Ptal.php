<?php
##############################################################################################################
#                                                                                       INFORME REGISTRO PPTAL
#                                                                                                         IRD
##############################################################################################################
#07/09/2017 | Erica G.   | Firmas y reestructuracion del codigo 
##############################################################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();

$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero, claCont.nombre as nombreContrato, comp.numerocontrato 
    FROM gf_comprobante_pptal comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
    LEFT JOIN gf_clase_contrato claCont ON comp.clasecontrato = claCont.id_unico 
    WHERE md5(comp.id_unico) ='$id'";
} else {
    $id = $_SESSION['id_comp_pptal_ER'];
    $sqlComp = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero, claCont.nombre as nombreContrato, comp.numerocontrato 
    FROM gf_comprobante_pptal comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
    LEFT JOIN gf_clase_contrato claCont ON comp.clasecontrato = claCont.id_unico 
    WHERE (comp.id_unico) ='$id'";
}

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
$tipoContra = $rowComp[9];
$numContra = $rowComp[10];
$fechaComprobante = $rowComp[2]; //Fecha       

$sqlTerc = "SELECT nombreuno, nombredos, apellidouno, apellidodos, razonsocial, numeroidentificacion 
      FROM gf_tercero
      WHERE id_unico = " . $terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0] . ' ' . $rowT[1] . ' ' . $rowT[2] . ' ' . $rowT[3] . ' ' . $rowT[4];
$nit = $rowT[5];

$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre, ter.razonsocial, ter.numeroidentificacion, ter.digitoverficacion, d.nombre    
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  LEFT JOIN gf_departamento d ON ciu.departamento = d.id_unico
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = ucwords(mb_strtolower($rowLogo[1].' '.$rowLogo[5]));
$compa = $rowLogo[2];
if (empty($rowLogo[4])) {
    $nitcom = $rowLogo[3];
} else {
    $nitcom = $rowLogo[3] . ' - ' . $rowLogo[4];
}

class PDF extends FPDF {
    #Función de pie de pagina

    function Footer() {
        require ('../Conexion/conexion.php');
        global $usuario;
        // Posición: a 1,5 cm del final
        $this->SetY(-25);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(190, 5, utf8_decode('NOTA: SE ENTIENDE QUE ESTA CERTIFICACION ES ESTRICTAMENTE PRESUPUESTAL Y SOMETIDA AL CUMPLIMIENTO DEL PROCEDIMIENTO LEGAL ESTABLECIDO'), 0, 'J');
        $this->Ln(2);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        // Número de página
        $dia = date('d');
        $mes = date('m');
        $anio = date('Y');
        $fecha = $dia . '/' . $mes . '/' . $anio;

        $this->Cell(190, 10, 'Elaborado por: ' . strtoupper($usuario), 0, 0, 'L');
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    #Funcón cabeza de la página
    // Cabecera de página  

    function Header() {
        global $nomcomp;
        global $fechaComp;
        global $razonSoc;
        global $nit;
        global $ruta;
        global $rolT;
        global $nombre;
        global $compa;
        global $nitcom;

        $fecha_div = explode("-", $fechaComp);
        $diaS = $fecha_div[2];
        $mesS = $fecha_div[1];
        $anioS = $fecha_div[0];

        $fechaCompF = $diaS . '/' . $mesS . '/' . $anioS;

        $dia = date('d');
        $mes = date('m');
        $anio = date('Y');
        $fecha = $dia . '/' . $mes . '/' . $anio;

        if ($ruta != '') {
            $this->Image('../' . $ruta, 10, 8, 25);
        }
        $this->Image('../logo/logoYopal.png', 175, 5, 40);
         $this->SetFont('Arial', 'B', 10);
        $this->Cell(100);
        $this->SetXY(28, 13); //EStaba
        $this->SetFont('Arial', 'B', 12);
        $this->MultiCell(160, 5, utf8_decode(mb_strtoupper($compa)), 0, 'C');
        $this->SetX(28);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(160, 5, utf8_decode('NIT: ' . $nitcom), 0, 0, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(7);
        $this->SetX(28);
        $this->Cell(160, 5, mb_strtoupper($nombre), 0, 0, 'C');
        $this->Ln(7);
        $this->SetX(28);
        $this->Cell(160, 5, utf8_decode('210.26'), 0, 0, 'C');
        $this->Ln(10);
        $this->SetX(28);
        $this->Cell(160, 5, utf8_decode('Número: ' . $nomcomp), 0, 0, 'C');
        $this->Ln(10);
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 10);

$usuario = $_SESSION['usuario'];

#**

$fecha_div = explode("-", $fechaComp);
$diaSb = $fecha_div[2];
$mesSb = $fecha_div[1];
$mesSb = (int) $mesSb;
$anioSb = $fecha_div[0];

$fechaCompF = $diaSb . '/' . $mesSb . '/' . $anioSb;

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX(28);
$pdf->Cell(160, 5, utf8_decode('EL SUSCRITO CERTIFICA:'), 0, 0, 'C');

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell(190, 3, utf8_decode("QUE EN EL PRESUPUESTO ORDINARIO DE GASTOS E INVERSIONES DE LA "
                . "VIGENCIA FISCAL EN CURSO, HA QUEDADO REGISTRADO PRESUPUESTALMENTE UN COMPROMISO CON "
                . "CARGO AL (LOS) SIGUIENTE(S) RUBRO(S):"), 0, 'J');


$pdf->Ln(7);

$pdf->Cell(175, 5, utf8_decode('FECHA:'.$fechaCompF), 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9, 0, 'C');
$pdf->Cell(60, 5, utf8_decode('Rubro'), 1, 0, 'C');
$pdf->Cell(60, 5, utf8_decode('Fuente'), 1, 0, 'C');
$pdf->Cell(40, 5, utf8_decode('Beneficiario'), 1, 0, 'C');
$pdf->Cell(30, 5, utf8_decode('Valor'), 1, 0, 'C'); //Valor
$pdf->Ln(5);
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
tr.apellidodos)) AS NOMBRE 
FROM gf_detalle_comprobante_pptal detComP 
left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
left join gf_fuente fue on fue.id_unico = rubFue.fuente 
left join gf_comprobante_pptal compP on detComP.comprobantepptal = compP.id_unico
left join gf_tercero tr on tr.id_unico = compP.tercero 
where detComP.comprobantepptal =" . $_SESSION['id_comp_pptal_ER'];

$detalle = $mysqli->query($sqlDetall);

$natural = array(2, 3, 5, 7, 10);
$juridica = array(1, 4, 6, 8, 9);

$totalValor = 0;
$pdf->SetFont('Arial', '', 9);
while ($rowDetall = mysqli_fetch_array($detalle)) {

    $numRub = $rowDetall[5] . ' - ' . ucwords(mb_strtolower($rowDetall[1]));
    $fuente = $rowDetall[6] . ' - ' . ucwords(mb_strtolower($rowDetall[4]));
    $valor = $rowDetall[2];
    $ben = ucwords(mb_strtolower($rowDetall[7]));
    if (strlen($numRub) > 35 || strlen($fuente) > 35) {
        $altY = $pdf->GetY();
        if ($altY > 240) {
            $pdf->AddPage();
        }
    }
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(60, 4, utf8_decode($numRub), 0, 'J');
    $y2 = $pdf->GetY();
    $h = $y2 - $y;
    $px = $x + 60;
    $pdf->SetXY($px, $y);

    $x1 = $pdf->GetX();
    $y1 = $pdf->GetY();
    $pdf->MultiCell(60, 4, utf8_decode($fuente), 0, 'J');
    $y21 = $pdf->GetY();
    $h1 = $y21 - $y1;
    $px1 = $x1 + 60;
    $pdf->SetXY($px1, $y1);

    $x2 = $pdf->GetX();
    $y2 = $pdf->GetY();
    $pdf->MultiCell(40, 4, utf8_decode($ben), 0, 'J');
    $y22 = $pdf->GetY();
    $h2 = $y22 - $y2;
    $px2 = $x2 + 40;
    $pdf->SetXY($px2, $y2);

    $x3 = $pdf->GetX();
    $y3 = $pdf->GetY();
    $pdf->MultiCell(30, 4, number_format($valor, 2, '.', ','), 0, 'R');
    $y23 = $pdf->GetY();
    $h3 = $y23 - $y3;
    $px3 = $x3 + 30;
    $pdf->SetXY($px3, $y3);

    $alt = max($h, $h1, $h2, $h3);
    $pdf->SetXY($x, $y);
    $pdf->MultiCell(60, $alt, '', 1, 'C');
    $pdf->SetXY($x + 60, $y);
    $pdf->MultiCell(60, $alt, '', 1, 'C');
    $pdf->SetXY($x + 120, $y);
    $pdf->MultiCell(40, $alt, '', 1, 'C');
    $pdf->SetXY($x + 160, $y);
    $pdf->MultiCell(30, $alt, '', 1, 'C');

    $totalValor = $totalValor + $valor;
    $altY = $pdf->GetY();
    if ($altY > 245) {
        $pdf->AddPage();
    }
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, "TOTAL " . mb_strtoupper($nombre) . ":", 0, 0, 'R'); //Rubro
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(30, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R'); //Valor Sí.

$altod = $pdf->GetPageHeight();
$altoP = $pdf->GetY();
$altoC = $altod - $altoP;
$pdf->LN(5);
if ($altoC < 80) {
    $pdf->AddPage();
}

$dis = "SELECT DISTINCT
cpa.numero,
tcp.codigo,
IF(CONCAT_WS(' ',
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
tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion , td.nombre , 
cp.descripcion , cp.numerocontrato , clc.nombre, tr.digitoverficacion  
FROM
  gf_comprobante_pptal cp
LEFT JOIN
  gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal
LEFT JOIN
  gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
LEFT JOIN
  gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
LEFT JOIN
  gf_tercero tr ON tr.id_unico = cp.tercero 
LEFT JOIN 
  gf_tipo_identificacion td ON tr.tipoidentificacion = td.id_unico 
LEFT JOIN
  gf_tipo_comprobante_pptal tcp ON cpa.tipocomprobante = tcp.id_unico 
LEFT JOIN 
  gf_clase_contrato clc ON cp.clasecontrato =clc.id_unico 
WHERE
  cp.id_unico =" . $_SESSION['id_comp_pptal_ER'];
$dis = $mysqli->query($dis);
$row = mysqli_fetch_row($dis);
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 0, utf8_decode(mb_strtoupper($row[1]) . ':'), 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150, 0, utf8_decode($row[0]), 0, 0, 'L');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 0, utf8_decode('A NOMBRE DE: '), 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150, 0, utf8_decode(mb_strtoupper($row[2])), 0, 0, 'L');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->CellFitScale(40, 0, utf8_decode(mb_strtoupper('C.C O NIT :')), 0);
$pdf->SetFont('Arial', '', 9);
if (empty($row[8])) {
    $pdf->Cell(150, 0, utf8_decode($row[3]), 0, 'L');
} else {
    $pdf->Cell(150, 0, utf8_decode($row[3] . ' - ' . $row[8]), 0, 'L');
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->CellFitScale(40, 0, utf8_decode('TIPO DE CONTRATATACIÓN:'), 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150, 0, utf8_decode(ucwords(mb_strtoupper($row[7]))), 0, 'L');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->CellFitScale(40, 0, utf8_decode('NÚMERO CONTRATO:'), 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150, 0, utf8_decode($row[6]), 0, 'L');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(190, 5, utf8_decode('OBJETO: '), 1);
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(190, 5, utf8_decode(($row[5])), 1, 'J'); //Descripción
$pdf->Ln(5);





$pdf->Ln(10);

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

if ($tfirmas > $altofirma) {
    $pdf->AddPage();
    $pdf->ln(20);
} else {
    $pdf->ln(20);
}
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







$pdf->Ln(35);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 13, utf8_decode('Vigencia del presente registro, '.$ciudadCompania.', Diciembre 31 del año '.$anioSb), 0, 0, 'L');
ob_end_clean();

$pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
?>

