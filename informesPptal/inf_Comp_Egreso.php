<?php
##############################################################################################################
#        INFORME EGRESO 
#        Normales
#        Encabezado                                                                                                   
##############################################################################################################
#07/09/2017 | Erica G.   | Firmas y reestructuracion del codigo 
##############################################################################################################
header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require_once('../numeros_a_letras.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require_once('../numeros_a_letras.php');
session_start();
ob_start();
ini_set('max_execution_time', 0);
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

$sqlComp = "SELECT
  compCnt.id_unico,
  compCnt.numero,
  compCnt.fecha,
  compCnt.descripcion,
  compCnt.tipocomprobante,
  tipCom.sigla,
  tipCom.nombre,
  compCnt.tercero,
  tiClaseC.nombre,
  compCnt.numerocontrato,
  CONCAT(ELT(
      WEEKDAY(compCnt.fecha) + 1,
      'Lunes',
      'Martes',
      'Miercoles',
      'Jueves',
      'Viernes',
      'Sabado',
      'Domingo')) AS DIA_SEMANA,
  IF(CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos) IS NULL 
     OR CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos) = '',
    (tr.razonsocial),
    CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos)) AS NOMBRE,
  tr.numeroidentificacion,
  tl.valor Telefono,
  dir.direccion,
  tr.digitoverficacion, tipCom.comprobante_pptal 
FROM
  gf_comprobante_cnt compCnt
LEFT JOIN
  gf_tipo_comprobante tipCom ON compCnt.tipocomprobante = tipCom.id_unico
LEFT JOIN
  gf_clase_contrato tiClaseC ON compCnt.clasecontrato = tiClaseC.id_unico
LEFT JOIN
  gf_tercero tr ON compCnt.tercero = tr.id_unico
LEFT JOIN
  gf_telefono tl ON tr.id_unico = tl.tercero
LEFT JOIN
  gf_direccion dir ON tr.id_unico = dir.tercero
WHERE
  compCnt.id_unico = " . $_SESSION['idCompCnt'];

$comp = $mysqli->query($sqlComp);

$rowComp = mysqli_fetch_array($comp);
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$tipocomprobante = $rowComp[4]; //id tipo comprobante  
$codigo = $rowComp[5]; //Código de tipo comprobante  
$nombreCompr = $rowComp[6]; //Nombre de tipo comprobante  
$claseContra = $rowComp[8];
$numroContra = $rowComp[9];
$diaF = $rowComp[10];
$idComp = $rowComp[0];

$razonSoc = $rowComp[11];
if (empty($rowComp[15])) {
    $nit = $rowComp[12];
} else {
    $nit = $rowComp[12] . ' - ' . $rowComp[15];
}
$tel = $rowComp[13];
$dir = $rowComp[14];
$tipocomprobantepptal = $rowComp[16];
$fechaComprobante = $rowComp[2]; //Fecha       
$fechaC =$rowComp[2];

$sqlValorTot = 'SELECT SUM(valor) 
  FROM gf_detalle_comprobante dc 
  WHERE dc.comprobante = ' . $_SESSION['idCompCnt'];
$valortotDet = $mysqli->query($sqlValorTot);
$rowVTD = mysqli_fetch_array($valortotDet);
$totalValorDet = $rowVTD[0];

#Banco
$sqlMovFina = 'SELECT detComp.id_unico idDetalleComp, detComp.valor valorDetalle, 
    te.razonsocial banco, doc.numero, cuen.naturaleza naturalezaCuenta, cc.nombre, 
    cuen.clasecuenta
  FROM gf_detalle_comprobante detComp 
  LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta 
  LEFT JOIN gf_centro_costo cc ON detComp.centrocosto = cc.id_unico
  LEFT JOIN gf_cuenta_bancaria ctaB ON ctaB.cuenta = cuen.id_unico
  LEFT JOIN gf_tercero te ON ctaB.banco = te.id_unico
  LEFT JOIN gf_detalle_comprobante_mov doc ON detComp.comprobante = doc.comprobantecnt
  WHERE detComp.comprobante = ' . $_SESSION['idCompCnt'] . ' AND cuen.clasecuenta = 11';
$banc = $mysqli->query($sqlMovFina);
$bc = mysqli_fetch_row($banc);
$banco = $bc[2];

$fecha_div = explode("-", $fechaComp);
$diaS = $fecha_div[2];
$mesS = $fecha_div[1];
$anioS = $fecha_div[0];

$fechaComp = $diaS . '/' . $mesS . '/' . $anioS;

$numpaginas = 0;
##COMPAÑIA##   
$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT
  ter.ruta_logo,
  ciu.nombre,
  ter.razonsocial,
  ter.numeroidentificacion,
  ter.digitoverficacion
FROM
  gf_tercero ter
LEFT JOIN
  gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico
WHERE
  ter.id_unico =' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];
$nombre_com = $rowLogo[2];
if (empty($rowLogo[4])) {
    $numICompañia = $rowLogo[3];
} else {
    $numICompañia = $rowLogo[3] . '-' . $rowLogo[4];
}

class PDF extends FPDF {

    #Función de pie de pagina

    function Footer() {
        require ('../Conexion/conexion.php');

        global $usuario;
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        // Número de página
        $dia = date('d');
        $mes = date('m');
        $anio = date('Y');
        $fecha = $dia . '/' . $mes . '/' . $anio;

        $this->Cell(40, 10, 'Elaborado por: ' . strtoupper($usuario), 0);
        // $this->Cell(35,10,'"DE LA MANO CON EL CAMPO"',0); //Slogan
        ####SLOGAN####
        $slog = "SELECT valor FROM gs_parametros_basicos WHERE LOWER(nombre)='slogan'";
        $slog = $mysqli->query($slog);
        if (mysqli_num_rows($slog) > 0) {
            $slog = mysqli_fetch_row($slog);
            $slog = ucwords(mb_strtolower($slog[0]));
            $y1 = $this->GetY();
            $x1 = $this->GetX();

            if (!empty($slog) || $slog != '') {
                $this->MultiCell(125, 5, utf8_decode('"' . mb_strtoupper($slog, 'utf-8') . '"'), 0, 'C'); //Slogan
            } else {
                $this->MultiCell(125, 5, utf8_decode(''), 0, 'C'); //Slogan
            }
            $y2 = $this->GetY();
            $alto_de_fila = $y2 - $y1;
            $posicionX = $x1 + 50;
            $this->SetXY($posicionX, $y1);
            $y5 = $this->GetY();
            $x4 = $this->GetX();
            $this->Cell(25);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
        } else {
            $this->Cell(25);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
        }
        ####FIN SLOGAN####
    }

    #Funcón cabeza de la página

    function Header() {
        require ('../Conexion/conexion.php');
        global $fechaComp;
        global $ruta;

        global $razonSoc;
        global $nit;
        global $dir;
        global $tel;
        global $descripcion;
        global $claseContra;
        global $numroContra;
        global $nomcomp;
        global $totalValorDet;
        global $numpaginas1;
        $numpaginas1 = $this->PageNo();
        global $banco;
        global $cheque;
        global $nombre_com;
        global $numICompañia;
        global $nombreCompr;

        // Logo acá

        $this->SetFont('Arial', 'B', 10);

        if ($ruta != '') {
            $this->Image('../' . $ruta, 10, 5, 25);
        }
        $this->Image('../logo/logoYopal.png', 175, 5, 40);
        $this->SetFont('Arial', 'B', 12);
        $this->SetX(35);
        $this->MultiCell(160, 5, utf8_decode(mb_strtoupper($nombre_com)),0, 'C');
        $this->Ln(2);
        $this->SetX(35);
        $this->Cell(160, 5, utf8_decode(('NIT:' . $numICompañia)), 0, 0, 'C');
        $this->Ln(7);
        $this->SetX(35);
        $this->Cell(160, 5, utf8_decode(mb_strtoupper($nombreCompr) . ' ' . 'No: ' . $nomcomp), 0, 0, 'C');
        $this->Ln(7);
        
    }

}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$yp = $pdf->GetY() - 60;
$pdf->SetFont('Arial', 'B', 10);
$usuario = $_SESSION['usuario'];
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(5);
$pdf->Cell(35, 6, utf8_decode('Fecha: '), 0, 0, 'L');
$pdf->Cell(155, 6, utf8_decode($fechaComp), 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(35, 6, utf8_decode('Nombre: '), 0, 0, 'L');
$pdf->MultiCell(160, 6, utf8_decode($razonSoc),  0, 'L');
$pdf->Cell(35, 6,utf8_decode('CC o Nit: '),0, 0, 'L');
$pdf->Cell(60, 6,utf8_decode($nit),0, 0, 'L');

$pdf->Cell(30, 6,utf8_decode('Teléfonos: '),0, 0, 'L');
############TELEFONOS###########
 $tel = "SELECT valor FROM gf_telefono WHERE tercero = $terceroCom";
 $tel = $mysqli->query($tel);
 if(mysqli_num_rows($tel)>0) { 
     $telef = "";
 while ($row = mysqli_fetch_row($tel)) {
    $telef =$telef." - ".$row[0];
 }
 $pdf->CellFitScale(65, 6,utf8_decode($telef),0, 0, 'L');
 } else {
 $pdf->Cell(65, 6,utf8_decode(''),0, 0, 'L');
 }
$pdf->Ln(5);
$pdf->Cell(35, 6,utf8_decode('Dirección: '),0, 0, 'L');
 ##########DIRECCION###############
 $dir = "SELECT CONCAT(d.direccion, '  ', c.nombre) "
         . "FROM gf_direccion d LEFT JOIN gf_ciudad c ON c.id_unico = d.ciudad_direccion "
         . "WHERE d.tercero = $terceroCom";
 $dir = $mysqli->query($dir);
 if(mysqli_num_rows($dir)>0) { 
     $direc = "";
 while ($rowD = mysqli_fetch_row($dir)) {
    $direc =$direc." - ".$rowD[0];
 }
 $pdf->MultiCell(160, 5,utf8_decode($direc),0, 'J');
 } else {
 $pdf->Cell(160, 6,utf8_decode(''),0, 0, 'L');
 $pdf->Ln(5);
 }

$Ordenes = "";
#Ordenes de pago
$sqlOrdenesP = "SELECT
  comPtal.numero,
  tcp.codigo,
  comPtal.descripcion, 
  comPtal.id_unico 
FROM
  gf_comprobante_pptal comPtal
LEFT JOIN
  gf_detalle_comprobante_pptal detComPtal ON detComPtal.comprobantepptal = comPtal.id_unico
LEFT JOIN
  gf_detalle_comprobante detComp ON detComp.detallecomprobantepptal = detComPtal.id_unico
LEFT JOIN
  gf_tipo_comprobante_pptal tcp ON comPtal.tipocomprobante = tcp.id_unico
WHERE
  detComp.comprobante =" . $_SESSION['idCompCnt'];

$resultO = $mysqli->query($sqlOrdenesP);
$E = mysqli_num_rows($resultO);
while ($O = mysqli_fetch_array($resultO)) {
    if ($E < 1) {
        $Ordenes .= $O[0];
        $Ordenes .= ',' . $O[0];
    } else {
        $Ordenes = $O[1] . '   N°:' . $O[0];
    }
    $detalle = $O[2];
    $idComp = $O[3];
}


$pdf->Cell(35, 6, 'Orden Pago: ', 0, 'L');
$pdf->Cell(155, 6, utf8_decode($Ordenes), 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(35, 6, 'Concepto: ', 0, 'L');
$pdf->Multicell(155, 6, utf8_decode($descripcion), 0, 'L');
$pdf->Cell(35, 6, utf8_decode('Tipo de contrato: '), 0, 0, 'L');
$pdf->Cell(60, 6, utf8_decode($claseContra), 0, 0, 'L');
$pdf->Cell(30, 6, utf8_decode('No de contrato: '), 0, 0, 'L');
$pdf->Cell(65, 6, utf8_decode($numroContra), 0, 0, 'L');
$pdf->Ln(5);
$sqlMovFina = 'SELECT detComp.id_unico idDetalleComp, detComp.valor valorDetalle, 
te.razonsocial banco, doc.numero, cuen.naturaleza naturalezaCuenta, cc.nombre, 
cuen.clasecuenta  
FROM gf_detalle_comprobante detComp 
LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta 
LEFT JOIN gf_centro_costo cc ON detComp.centrocosto = cc.id_unico
LEFT JOIN gf_cuenta_bancaria ctaB ON ctaB.cuenta = cuen.id_unico
LEFT JOIN gf_tercero te ON ctaB.banco = te.id_unico
LEFT JOIN gf_detalle_comprobante_mov doc ON detComp.id_unico = doc.comprobantecnt
WHERE detComp.comprobante = '.$_SESSION['idCompCnt'].' AND cuen.clasecuenta = 11';
$banc = $mysqli->query($sqlMovFina);

while ($bc = mysqli_fetch_row($banc)) {
    $banco = $bc[2];
    $pdf->Cell(35,5,'Banco: ',0,'L'); 
    $pdf->CellFitScale(60,5, utf8_decode(ucwords(mb_strtolower($banco))),0,'L'); 
    $pdf->Cell(30, 6,utf8_decode('No Cheque :'),0, 0, 'L');
    $numCheque = $bc[3];
    $pdf->Cell(15, 6,utf8_decode($numCheque),0, 0, 'L');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'B', 10, 'C');
$sqlDetallPptal = "SELECT DISTINCT
  cpop.numero,
  cpr.numero,
  cpd.numero,
  dc.valor,
  rpptal.codi_presupuesto,
  rpptal.nombre, 
  f.id_unico, 
  f.nombre, dcpcx.id_unico 
FROM
  gf_comprobante_pptal cn
LEFT JOIN
  gf_detalle_comprobante_pptal dc ON cn.id_unico = dc.comprobantepptal 
LEFT JOIN
  gf_detalle_comprobante_pptal dcpcx ON dc.comprobanteafectado = dcpcx.id_unico
LEFT JOIN
  gf_comprobante_pptal cpcx ON dcpcx.comprobantepptal = cpcx.id_unico
LEFT JOIN
  gf_tipo_comprobante_pptal tccx ON tccx.id_unico = cpcx.tipocomprobante
LEFT JOIN
  gf_detalle_comprobante_pptal dcpop ON dcpcx.comprobanteafectado = dcpop.id_unico
LEFT JOIN
  gf_comprobante_pptal cpop ON dcpop.comprobantepptal = cpop.id_unico
LEFT JOIN
  gf_tipo_comprobante_pptal tcop ON cpop.tipocomprobante = tcop.id_unico
LEFT JOIN
  gf_detalle_comprobante_pptal dcpr ON dcpop.comprobanteafectado = dcpr.id_unico
LEFT JOIN
  gf_comprobante_pptal cpr ON dcpr.comprobantepptal = cpr.id_unico
LEFT JOIN
  gf_tipo_comprobante_pptal tcr ON cpr.tipocomprobante = tcr.id_unico
LEFT JOIN
  gf_detalle_comprobante_pptal dcpd ON dcpr.comprobanteafectado = dcpd.id_unico
LEFT JOIN
  gf_comprobante_pptal cpd ON dcpd.comprobantepptal = cpd.id_unico
LEFT JOIN
  gf_tipo_comprobante_pptal tcd ON cpd.tipocomprobante = tcd.id_unico
LEFT JOIN
  gf_rubro_fuente rf ON dcpop.rubrofuente = rf.id_unico
LEFT JOIN
  gf_rubro_pptal rpptal ON rf.rubro = rpptal.id_unico
LEFT JOIN
  gf_fuente f ON rf.fuente = f.id_unico
WHERE
  cn.id_unico=" . $_SESSION['id_comp_pptal_GE'];
$detalle = $mysqli->query($sqlDetallPptal);

$pptal = $_SESSION['id_comp_pptal_GE'];
$pp = "SELECT fechavencimiento ,  UPPER(usuario), DATE_FORMAT(fecha_elaboracion,'%d/%m%/%Y') FROM gf_comprobante_pptal WHERE id_unico = $pptal";
$pp = $mysqli->query($pp);
if (mysqli_num_rows($pp) > 0) {
    $pp = mysqli_fetch_row($pp);
    $ppf = $pp[0];
    $div = explode("-", $ppf);
    $anno = $div[0];
    $usuario = $pp[1];
    $fechaElab = $pp[2];
} else {
    $anno = '';
    $usuario = $_SESSION['user'];
    $fechaElab = date('d/m/Y');
}
$pdf->Cell(190, 5, utf8_decode('VIGENCIA PRESUPUESTAL ' . $anno), 1, 0, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9, 'C');
$y1 = $pdf->GetY();
$x1 = $pdf->GetX();
$pdf->MultiCell(25, 5, utf8_decode("Disponibilidad  Presupuestal"), 1, 'C');
$y2 = $pdf->GetY();
$h = $y2 - $y1;
$px = $x1 + 25;
$pdf->SetXY($px, $y1);
$y11 = $pdf->GetY();
$x11 = $pdf->GetX();
$pdf->MultiCell(25, 5, utf8_decode("Registro  Presupuestal"), 1, 'C');
$y21 = $pdf->GetY();
$h1 = $y21 - $y11;
$px1 = $x11 + 25;
$pdf->SetXY($px1, $y11);
$alt = max($h, $h1);

$pdf->Cell(30, $alt, utf8_decode('Código'), 1, 0, 'C');
$pdf->Cell(80, $alt, utf8_decode('Rubro Fuente'), 1, 0, 'C');
$pdf->Cell(30, $alt, utf8_decode('Valor'), 1, 0, 'C');
$pdf->Ln($alt);
$pdf->SetFont('Arial', '', 9);
$totalValor = 0;
while ($rowG = mysqli_fetch_array($detalle)) {
    $paginactual = $pdf->PageNo();
    $valor = 0;
    if (empty($rowG[2])) {
        $numComPtalDisponibilidad = $rowG[1];
        $numComPtalRegistro = $rowG[0];
    } else {
        $numComPtalDisponibilidad = $rowG[2];
        $numComPtalRegistro = $rowG[1];
    }
    $nombreCuenta = mb_strtolower($rowG[5] . ' - ' . $rowG[6] . ' ' . $rowG[7], 'utf-8');
    $nombreCuenta = ucwords($nombreCuenta);
    if (strlen($nombreCuenta) > 45) {
        $altY = $pdf->GetY();
        if ($altY > 310) {
            $pdf->AddPage();
            $paginactual = $pdf->PageNo();
            $paginactual2 = $pdf->PageNo();
            $pdf->Ln(5);
        }
    }
    
    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->Cell(80, 5, ' ', 0, 0, 'L');
    $pdf->MultiCell(80, 5, utf8_decode($nombreCuenta), 1, 'J');
    $y2 = $pdf->GetY();
    $h = $y2 - $y1;
    $px = $x1 + 80;
    $paginactual2 = $pdf->PageNo();
    if ($paginactual != $paginactual2) {
        //echo 'entraa';
        $pdf->SetXY($x1, $yp);
        $h = $y2 - $yp;
    } else {
        $pdf->SetXY($x1, $y1);
    }

    $pdf->Cell(25, $h, $numComPtalDisponibilidad, 1, 0, 'L');
    $pdf->Cell(25, $h, $numComPtalRegistro, 1, 0, 'L');
    $pdf->Cell(30, $h, $rowG[4], 1, 0, 'L');

    $xx = $pdf->GetX();
    $pdf->SetX($xx + 80);
    $valor = $rowG[3];
    $pdf->Cell(30, $h, number_format($valor, 2, '.', ','), 1, 0, 'R');
    $totalValor += $valor;
    $pdf->Ln($h);
    $altY = $pdf->GetY();
     if ($altY > 310) {
        $pdf->AddPage();
        $paginactual = $pdf->PageNo();
        $pdf->Ln(5);
    }
}
$x = $pdf->GetX();
$pdf->SetX($x);
$pdf->Cell(160, 5, 'Total:', 0, 0, 'R'); //Total
$pdf->Cell(30, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R');


$pdf->Cell(160, 5, 'Total:', 0, 0, 'R'); //Total
$pdf->Cell(30, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R'); //Valor total Sí.

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, 'MOVIMIENTO FINANCIERO Y CONTABLE', 1, 0, 'C');

$pdf->Ln(5);
$pdf->Cell(25, 5, utf8_decode('Cuenta'), 1, 0, 'C');
$pdf->Cell(60, 5, utf8_decode('Nombre de la Cuenta'), 1, 0, 'C');
$pdf->Cell(55, 5, utf8_decode('Tercero'), 1, 0, 'C');
$pdf->Cell(25, 5, utf8_decode('Débito'), 1, 0, 'C');
$pdf->Cell(25, 5, utf8_decode('Crédito'), 1, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 9);

$sqlMovFina = "SELECT detComp.id_unico idDetalleComp, 
    detComp.valor valorDetalle, cuen.nombre nombreCuenta, cuen.codi_cuenta codigoCuenta, 
    cuen.naturaleza naturalezaCuenta, 
    IF( CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos
      ) IS NULL OR CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) = '',
      (tr.razonsocial),
      CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos )) AS NOMBRE, cuen.clasecuenta  
  FROM gf_detalle_comprobante detComp 
  LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta 
  LEFT JOIN gf_tercero tr ON detComp.tercero = tr.id_unico
  WHERE detComp.comprobante = " . $_SESSION['idCompCnt'];
$movimientoFinanciero = $mysqli->query($sqlMovFina);

$totalDebito = 0;
$totalCredito = 0;
$totalCheque = 0;
while ($rowMF = mysqli_fetch_array($movimientoFinanciero)) {
    $debito = 0;
    $credito = 0;
    $nombCuen = mb_strtolower($rowMF[2], 'utf-8');
    $nombCuen = ucwords($nombCuen);
    $centroCost = mb_strtolower($rowMF[5], 'utf-8');
    $centroCost = ucwords($centroCost);
    $cod = $rowMF[3];

    if ($rowMF[4] == 1) {
        if ($rowMF[1] < 0) {
            $credito = substr($rowMF[1], 1);
        } else {
            $debito = $rowMF[1];
        }
    } elseif ($rowMF[4] == 2) {
        if ($rowMF[1] < 0) {
            $debito = substr($rowMF[1], 1);
        } else {
            $credito = $rowMF[1];
        }
    }
    if ($rowMF[6] == 11) {
        $totalCheque += $rowMF[1];
    }
   
    $xinicio = $pdf->GetX();
    $yinicio = $pdf->GetY();
    $pdf->Cell(25, 5, utf8_decode($cod), 0, 0, 'L');
    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->Multicell(60, 5, utf8_decode($nombCuen), 0, 'L');
    $y2 = $pdf->GetY();
    $alto_de_fila = $y2 - $y1;
    $posicionX = $x1 + 60;
    $pdf->SetXY($posicionX, $y1);
    $y3 = $pdf->GetY();
    $x3 = $pdf->GetX();
    $pdf->Multicell(55, $alto_de_fila, utf8_decode($centroCost), 0, 'L');
    $y4 = $pdf->GetY();
    $alto_de_fila1 = $y4 - $y3;
    $posicionX = $x3 + 55;
    $pdf->SetXY($posicionX, $y1);
    $pdf->Cell(25, 5, number_format($debito, 2, '.', ','), 0, 0, 'R');
    $pdf->Cell(25, 5, number_format($credito, 2, '.', ','), 0, 0, 'R');

    #Determinar Valor máximo de altura
    $max = max($alto_de_fila, $alto_de_fila1);
    #Salto de línea
    $pdf->Ln($max);
    $pdf->SetXY($xinicio, $yinicio);
    $pdf->Cell(25, $max, utf8_decode(''), 1, 0, 'L');
    $pdf->Cell(60, $max, utf8_decode(''), 1, 0, 'L');
    $pdf->Cell(55, $max, utf8_decode(''), 1, 0, 'L');
    $pdf->Cell(25, $max, utf8_decode(''), 1, 0, 'L');
    $pdf->Cell(25, $max, utf8_decode(''), 1, 0, 'L');
    $pdf->Ln($max);
    $totalDebito += $debito;
    $totalCredito += $credito;
    $altY = $pdf->GetY();
     if ($altY > 230) {
        $pdf->AddPage();
        $paginactual = $pdf->PageNo();
        $pdf->Ln(5);
    }
}

$pdf->Ln(3);

$pdf->Cell(110, 15, '', 1, 0, 'R');
$pdf->Cell(40, 15, '', 1, 0, 'R');
$pdf->Cell(40, 15, '', 1, 0, 'R');


$pdf->SetX(-200);
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(110, 5, utf8_decode('Débitos'), 0, 0, 'R');
$pdf->Cell(40, 5, utf8_decode('Créditos'), 0, 0, 'R');
$pdf->Cell(40, 5, utf8_decode('Valor A Girar'), 0, 0, 'R');


$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);

if ($totalDebito < 0) {
    $totalDebito = substr($totalDebito, 1);
}

if ($totalCredito < 0) {
    $totalCredito = substr($totalCredito, 1);
}

if ($totalCheque < 0) {
    $totalCheque = substr($totalCheque, 1);
}

$totalChequeLetras = numtoletras($totalCheque);

$pdf->cellfitscale(110, 5, number_format($totalDebito, 2, '.', ','), 0, 0, 'R'); //Total débito
$pdf->cellfitscale(40, 5, number_format($totalCredito, 2, '.', ','), 0, 0, 'R'); //Total crédito
$pdf->cellfitscale(40, 5, number_format($totalCheque, 2, '.', ','), 0, 0, 'R'); //Total valor cheque

$pdf->Ln(10);

#########################################RETENCIONES#############################################################
#VALIDAR SI LA RETENCION SE APLICA EN EL EGRESO O EN LA CUENTA POR PAGAR
$ret ="SELECT retencion FROM gf_tipo_comprobante WHERE id_unico =$tipocomprobante";
$ret =$mysqli->query($ret);
$ret = mysqli_fetch_row($ret);
$retencion = $ret[0];

if($retencion==1){
    $sqlR = "SELECT tpr.nombre,tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
    FROM gf_retencion rt 
    LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
    LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
    WHERE cnt.id_unico=".$_SESSION['idCompCnt'];; 
} else {
    $ID="SELECT DISTINCT dc.comprobante FROM  gf_detalle_comprobante dc 
    LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.id_unico = dc.detallecomprobantepptal 
    where dcp.comprobantepptal = $idComp and dc.detalleafectado IS NULL";
    $ID=$mysqli->query($ID);
    if(mysqli_num_rows($ID)>0){
    $idc= mysqli_fetch_row($ID);
    $idc=$idc[0];
    } else {
        $ID="SELECT DISTINCT dca.comprobante FROM gf_detalle_comprobante_pptal dcp 
                        LEFT JOIN gf_detalle_comprobante_pptal dcpcnt ON dcpcnt.id_unico = dcp.comprobanteafectado 
                        LEFT JOIN gf_detalle_comprobante dca ON dcpcnt.id_unico = dca.detallecomprobantepptal
                        WHERE dcp.comprobantepptal=".$_SESSION['id_comp_pptal_GE'];
        $ID=$mysqli->query($ID);
        if(mysqli_num_rows($ID)>0){
            $idc= mysqli_fetch_row($ID);
            $idc=$idc[0]; 
        } else {
            $idc=0; 
        }
    }

    $sqlR = "SELECT tpr.nombre,tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
    FROM gf_retencion rt 
    LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
    LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
    WHERE cnt.id_unico=$idc"; 
}

$resultR = $mysqli->query($sqlR);
$valorR = 0;
if (mysqli_num_rows($resultR)) {
    $altY = $pdf->GetY();
     if ($altY > 300) {
            $pdf->AddPage();
            $paginactual = $pdf->PageNo();
            $paginactual2 = $pdf->PageNo();
            $pdf->Ln(5);
        }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(190, 5, utf8_decode(' RETENCIÓN Y DESCUENTOS'), 1, 0, 'C');
    $pdf->Ln(5);
    $pdf->Cell(100, 5, utf8_decode('Tipo Retención'), 1, 0, 'C');
    $pdf->Cell(30, 5, utf8_decode('Porcentaje'), 1, 0, 'C');
    $pdf->Cell(30, 5, utf8_decode('Valor Retención'), 1, 0, 'C');
    $pdf->Cell(30, 5, utf8_decode('Retención Base'), 1, 0, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    while ($f = mysqli_fetch_row($resultR)) {
        
        $tipo = utf8_decode($f[0]);
        if (strlen($tipo) > 45 ) {
            $altY = $pdf->GetY();
            if ($altY > 300) {
                $pdf->AddPage();
                $paginactual = $pdf->PageNo();
                $paginactual2 = $pdf->PageNo();
                $pdf->Ln(5);
            }
        }
        $pdf->SetFont('Arial', '', 10);
        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();
        $pdf->Multicell(100, 5, ($tipo), 0, 'L');
        $y2 = $pdf->GetY();
        $alto_de_fila = $y2 - $y1;
        $posicionX = $x1 + 100;
        $pdf->SetXY($posicionX, $y1);
        $pdf->Cell(30, 5, utf8_decode($f[1]), 0, 0, 'R');
        $pdf->Cell(30, 5, number_format($f[2], 2, '.', ','), 0, 0, 'R');
        $pdf->Cell(30, 5, number_format($f[3], 2, '.', ','), 0, 0, 'R');
        $pdf->Ln($alto_de_fila);
        $valorR = $f[2];
        $altY = $pdf->GetY();
        if ($altY > 300) {
        $pdf->AddPage();
        $paginactual = $pdf->PageNo();
        $paginactual2 = $pdf->PageNo();
        $pdf->Ln(5);
    }
    }
    $pdf->Ln(5);
     
}



$pdf->SetFont('Arial', 'B', 10);
$pdf->cellfitscale(190, 5, utf8_decode('Valor a girar'), 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(190, 5, utf8_decode($totalChequeLetras), 0, 'L');

$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int) $mesS;
$anioS = $fecha_div[2];

$pdf->SetFont('Arial', 'B', 10);

#*******************Firmas*******************#
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
  WHERE tcp.id_unico = $tipocomprobantepptal
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
                $i++;
            } 
     } elseif(!empty($rowTipComp[4]) ) {
                if($fechaComprobante >= $rowTipComp[4]){
                    $firmaNom[$i] = $rowTipComp[0];
                    $firmaCarg[$i] = $rowTipComp[3];
                    $firmaTP[$i] = $rowTipComp[6];
                    $i++;
                }
         
     } else {
            $firmaNom[$i] = $rowTipComp[0];
            $firmaCarg[$i] = $rowTipComp[3];
            $firmaTP[$i] = $rowTipComp[6];
            $i++;
     }
}

$firmaNom[$i] = 'FIRMA BENEFICIARIO'; //$rowTipComp[0];
$firmaNum[$i] = 'C.C. ó NIT';//$rowTipComp[1];
$firmaCarg[$i] = 'C.C. ó NIT';//$rowTipComp[2];

$numFirmas = $i;

if($numFirmas > 3)
  $numFirmas = 3;

for($i = 0; $i <= $numFirmas; $i++)
{
  $pdf->Cell(50,35,'',1,0,'C');
  
}

$pdf->Ln(24);
for($i = 0; $i <= $numFirmas; $i++)
{
  $pdf->Cell(1,0,'',0,0,'L');
  $pdf->Cell(45,0,'',1,0,'L');
  $pdf->Cell(4,0,'',0,0,'L');
}


$pdf->Ln(2);
for($i = 0; $i <=$numFirmas; $i++)
{
    if($firmaNom[$i]=='' || $firmaNom[$i]==""){
        $pdf->Cell(50,5,utf8_decode($firmaNom[$i]),0,0,'L');
    } else {
  $pdf->CellFitScale(50,5,utf8_decode($firmaNom[$i]),0,0,'L');
    }
    
}
  

$pdf->Ln(4);
for($i = 0; $i <= $numFirmas; $i++)
{
    if($firmaCarg[$i]=='' || $firmaCarg[$i]==""){
        $pdf->Cell(50,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    } else {
        $pdf->CellFitScale(50,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    }
 
}
$pdf->Ln(4);
for($i = 0; $i < $numFirmas; $i++)
{
    if($firmaTP[$i]=='' || $firmaTP[$i]==""){
        $pdf->Cell(50,5,utf8_decode(''),0,0,'L');
    } else {
        $pdf->CellFitScale(50,5,utf8_decode('T.P. :'.$firmaTP[$i]),0,0,'L');
    }
 
}

##################################################################################

ob_end_clean();
$pdf->Output(0, 'Informe_Certificado_Egreso (' . $nomcomp . ').pdf', 0);
?>


