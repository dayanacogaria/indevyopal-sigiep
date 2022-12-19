<?php
##############################################################################################################
#                           INFORME CUENTA POR PAGAR
#                                   Normales
##############################################################################################################
#08/09/2017 | Erica G.   | Firmas y reestructuracion del codigo 
##############################################################################################################

header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require_once('../numeros_a_letras.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
ob_start();
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
#********Comprobante pptal ********#
$compp = "SELECT id_unico, numero, tipocomprobante , fecha FROM gf_comprobante_pptal WHERE id_unico=".$_SESSION['id_comp_pptal_CP'];
$compp = $mysqli->query($compp);
$compp = mysqli_fetch_row($compp);
$tipocomprobantepptal  = $compp[2];
$fechaComprobante = $compp[3];


$sqlComp = "SELECT
  compCnt.id_unico,
  compCnt.numero,
  compCnt.fecha,
  compCnt.descripcion,
  compCnt.fecha,
  compCnt.tipocomprobante,
  tipCom.sigla,
  tipCom.nombre,
  compCnt.tercero,
  compCnt.clasecontrato,
  compCnt.numerocontrato,
  clasec.nombre
FROM
  gf_comprobante_cnt compCnt
LEFT JOIN
  gf_tipo_comprobante tipCom ON compCnt.tipocomprobante = tipCom.id_unico
LEFT JOIN
  gf_clase_contrato clasec ON compCnt.clasecontrato = clasec.id_unico
LEFT JOIN 
  gf_tipo_contrato tcon ON tcon.id_unico = clasec.tipocontrato
WHERE
  compCnt.tipocomprobante = tipCom.id_unico AND compCnt.id_unico = " . $_SESSION['idCompCntV'];

$comp = $mysqli->query($sqlComp);
$rowComp = mysqli_fetch_array($comp);
$idComp = $rowComp[0];
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$terceroComp = intval($rowComp[8]); //Tercero del comprobante
$claseContra = $rowComp[9];
$numroContra = $rowComp[10];
$claseContraNom = ucwords(mb_strtolower($rowComp[11]));    

$sqlTerc = 'SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion, razonsocial  
      FROM gf_tercero
      WHERE id_unico = ' . $terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0] . ' ' . $rowT[1] . ' ' . $rowT[2] . ' ' . $rowT[3] . ' ' . $rowT[5];
$nit = $rowT[4];

$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];

$sqlValorTot = 'SELECT  SUM(valor) '
        . 'FROM gf_detalle_comprobante dc '
        . 'WHERE naturaleza=2  AND valor >0 AND dc.comprobante = ' . $_SESSION['idCompCntV'];
$valortotDet = $mysqli->query($sqlValorTot);
$rowVTD = mysqli_fetch_array($valortotDet);
$totalValorDet = $rowVTD[0];
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
        global $usuario;
        require'../Conexion/conexion.php';
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        // Número de página
        $dia = date('d');
        $mes = date('m');
        $anio = date('Y');
        $fecha = $dia . '/' . $mes . '/' . $anio;

        $this->Cell(35, 10, 'Elaborado por: ' . strtoupper($usuario), 0);

        ####SLOGAN####
        $slog = "SELECT valor FROM gs_parametros_basicos WHERE LOWER(nombre)='slogan'";
        $slog = $mysqli->query($slog);
        if (mysqli_num_rows($slog) > 0) {
            $slog = mysqli_fetch_row($slog);
            $slog = ucwords(mb_strtolower($slog[0]));
            $y1 = $this->GetY();
            $x1 = $this->GetX();
            $this->MultiCell(130, 5, utf8_decode('"' . $slog . '"'), 0, 'C'); //Slogan
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
        global $ruta;
        global $nombre;
        global $nomcomp;
        global $fechaComp;
        global $numpaginas;
         global $nombre_com;
        global $numICompañia;
        global $nombreCompr;
        $numpaginas = $numpaginas + 1;
        if ($ruta != '') {
                $this->Image('../' . $ruta, 10, 5, 25);
            }
            $this->Image('../logo/logoYopal.png', 175, 5, 40);
            $this->SetFont('Arial', 'B', 10);
            $this->SetX(35);
            $this->MultiCell(160, 4, utf8_decode(mb_strtoupper($nombre_com)),0, 'C');
            $this->Ln(2);
            $this->SetX(35);
            $this->Cell(160, 4, utf8_decode(('NIT:' . $numICompañia)), 0, 0, 'C');
            $this->Ln(6);
            $this->SetX(35);
            $this->Cell(160, 4, utf8_decode(mb_strtoupper($nombre) . ' ' . 'No: ' . $nomcomp), 0, 0, 'C');
            $this->Ln(6);
            $this->SetX(35);
            $this->Cell(160, 4, utf8_decode('Fecha:'.$fechaComp), 0, 0, 'C');
            $this->Ln(6);
    }

    //Arial bold 15
}

global $fechaComp;
global $razonSoc;
global $nit;
global $descripcion;
global $claseContra;
global $numroContra;

global $totalValorDet;
global $claseContraNom;

$fecha_div = explode("-", $fechaComp);
$diaS = $fecha_div[2];
$mesS = $fecha_div[1];
$anioS = $fecha_div[0];

$fechaComp = $diaS . '/' . $mesS . '/' . $anioS;
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$yp = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 10);
$usuario = $_SESSION['usuario'];

$pdf->Ln(-40);
$pdf->Ln(4);
$pdf->Ln(35);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 6, utf8_decode('1. DATOS DEL BENEFICIARIO'), 1, 0, 'C');
$pdf->Ln(6);
#***********Nombre***********#
$xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 6, utf8_decode('Nombre: ' ), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(160, 6, utf8_decode(ucwords(mb_strtolower($razonSoc))),  0, 'L');
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(190, $al,utf8_decode(''),1, 0, 'L');
 $pdf->Ln($al);
#***********Nit***********#
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 6, utf8_decode('CC o Nit: ' ), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(45, 6, utf8_decode( $nit),0, 0, 'L');
$pdf->Ln(6);
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(60, $al,utf8_decode(''),1, 0, 'L');
#***********Telefonos***********#
 $tel = "SELECT valor FROM gf_telefono WHERE tercero = $terceroComp";
 $tel = $mysqli->query($tel);
 if(mysqli_num_rows($tel)>0) { 
     $telef = "";
 while ($row = mysqli_fetch_row($tel)) {
    $telef =$telef." - ".$row[0];
 }
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
 $pdf->Cell(25, 6,utf8_decode('Teléfonos: '),0, 0, 'L');
 $pdf->SetFont('Arial', '', 10);
$pdf->Cell(105, 6, utf8_decode( $telef),0, 0, 'L');
$pdf->Ln(6);
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(130, $al,utf8_decode(''),1, 0, 'L');
 $pdf->Ln($al);
 } else {
 $pdf->SetFont('Arial', 'B', 10);
 $pdf->Cell(130, 6,utf8_decode('Teléfonos: '),1, 0, 'L');
 $pdf->Ln(6);
 }
 
 #***********Direccion***********#
 $dir = "SELECT CONCAT(d.direccion, '  ', c.nombre) "
         . "FROM gf_direccion d LEFT JOIN gf_ciudad c ON c.id_unico = d.ciudad_direccion "
         . "WHERE d.tercero = $terceroComp";
 $dir = $mysqli->query($dir);
 if(mysqli_num_rows($dir)>0) { 
     $direc = "";
 while ($rowD = mysqli_fetch_row($dir)) {
    $direc =$direc." - ".$rowD[0];
 }
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
 $pdf->Cell(30, 6,utf8_decode('Dirección: '),0, 0, 'L');
 $pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(160, 5,utf8_decode(ucwords(mb_strtolower($direc))),0, 'J');
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(190, $al,utf8_decode(''),1, 0, 'L');
 $pdf->Ln($al);
 } else {
 $pdf->SetFont('Arial', 'B', 10);    
 $pdf->Cell(190, 6,utf8_decode('Dirección: '),1, 0, 'L');
 $pdf->Ln(6);
 }

#***********Descripcion***********#
 $pdf->SetFont('Arial', 'B', 10);
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->Cell(190, 6,utf8_decode('Descripción: '),0, 0, 'L');
 $pdf->SetFont('Arial','',10);
 $pdf->Ln(6);
 $pdf->Multicell(190, 4,utf8_decode($descripcion), 0, 'J');
 $pdf->Ln(2);
 $ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(190, $al,utf8_decode(''),1, 0, 'L');
 $pdf->Ln($al);
 #############################
 #***********Tipo de contrato***********#
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, utf8_decode('Tipo de contrato: ' ), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 6, utf8_decode( $claseContraNom),0, 0, 'L');
$pdf->Ln(6);
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(130, $al,utf8_decode(''),1, 0, 'L');
 
 
 #***********No de contrato***********#
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 6, utf8_decode('No de contrato: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(45, 6, utf8_decode( '   '.$numroContra),0, 0, 'L');
$pdf->Ln(6);
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(60, $al,utf8_decode(''),1, 0, 'L');
 $pdf->Ln(6);
 




if ($totalValorDet < 0) {
    $totalValorDet = $totalValorDet * -1;
} else {
    $totalValorDet = $totalValorDet;
}
 #***********Tipo de contrato***********#
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, utf8_decode('No de documento: ' ), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 6, utf8_decode('   '.$nomcomp),0, 0, 'L');
$pdf->Ln(6);
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(130, $al,utf8_decode(''),1, 0, 'L');
 
 
 #***********No de contrato***********#
 $xd=$pdf->GetX();
 $yd=$pdf->GetY();
 $pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 6, utf8_decode('Valor: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(45, 6, utf8_decode( '  $ '. number_format($totalValorDet, 2, '.', ',')),0, 0, 'L');
$pdf->Ln(6);
$ydd = $pdf->GetY();
 $al = $ydd-$yd;
 $pdf->SetXY($xd,$yd);
 $pdf->Cell(60, $al,utf8_decode(''),1, 0, 'L');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, utf8_decode('2. MOVIMIENTO PRESUPUESTAL'), 1, 0, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9, 0, 'C');
$y1 = $pdf->GetY();
$x1 = $pdf->GetX();
$pdf->MultiCell(25,5,utf8_decode("Disponibilidad  Presupuestal"),1,'C'); 
$y2 = $pdf->GetY();            
$h = $y2-$y1;
$px = $x1 + 25; 
$pdf->SetXY($px,$y1);
$y11 = $pdf->GetY();
$x11 = $pdf->GetX();
$pdf->MultiCell(25,5,utf8_decode("Registro  Presupuestal"),1,'C'); 
$y21 = $pdf->GetY();            
$h1 = $y21-$y11;
$px1 = $x11 + 25; 
$pdf->SetXY($px1,$y11);
$alt=max($h, $h1);

$pdf->Cell(25,$alt,utf8_decode('Código'),1,0,'C');
$pdf->Cell(45,$alt,utf8_decode('Nombre Rubro'),1,0,'C');
$pdf->Cell(40,$alt,utf8_decode('Fuente'),1,0,'C');
$pdf->Cell(30,$alt,utf8_decode('Valor'),1,0,'C');
$pdf->Ln($alt);



$pdf->SetFont('Arial', '', 8);
$sqlDetallPptal = "SELECT DISTINCT
  cpop.numero,
  cpr.numero,
  cpd.numero,
  dcpcx.valor,
  rpptal.codi_presupuesto,
  rpptal.nombre, 
  f.id_unico, 
  f.nombre, rf.id_unico, dcpcx.id_unico  
FROM
  gf_comprobante_cnt cn
LEFT JOIN
  gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante
LEFT JOIN
  gf_detalle_comprobante_pptal dcpcx ON dc.detallecomprobantepptal = dcpcx.id_unico
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
  dc.detallecomprobantepptal IS NOT NULL AND cn.id_unico=" . $_SESSION['idCompCntV'];

$detallePtal = $mysqli->query($sqlDetallPptal);


$totalValor1 = 0;
while ($rowDetallPtal = mysqli_fetch_array($detallePtal)) {

    if (empty($rowDetallPtal[2])) {
        $numComPtalDisponibilidad = $rowDetallPtal[1];
        $numComPtalRegistro = $rowDetallPtal[0];
    } else {
        $numComPtalDisponibilidad = $rowDetallPtal[2];
        $numComPtalRegistro = $rowDetallPtal[1];
    }

    $fuente = $rowDetallPtal[7];
    //$detPtal = $rowDetallPtal[3];
    $idRubroFuen = $rowDetallPtal[8];
    $codRubro = $rowDetallPtal[4];
    $nomRubro = $rowDetallPtal[5];
    $halla = 0;
    $totalValor1 += $rowDetallPtal[3];
    $saldoDisponible = apropiacion($idRubroFuen) - disponibilidades($idRubroFuen);
    if (strlen($nomRubro) > 30 || strlen($fuente) > 30) {
        $altY = $pdf->GetY();
        if ($altY > 240) {
            $pdf->AddPage();
            $pdf->Ln(5);
        }
    }
    $pdf->Cell(25, 5, utf8_decode($numComPtalDisponibilidad), 0, 0, 'L');
    $pdf->Cell(25, 5, utf8_decode($numComPtalRegistro), 0, 0, 'L');
    $pdf->Cell(25, 5, utf8_decode($codRubro), 0, 0, 'L');
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->MultiCell(45, 4, utf8_decode(ucwords(mb_strtolower($nomRubro))), 0, 'J');
    $y2 = $pdf->GetY();
    $h = $y2 - $y;
    $px = $x + 45;
    $pdf->Ln(-$h);
    $pdf->SetX($px);

    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40, 4, utf8_decode(ucwords(mb_strtolower($fuente))), 0, 'J');
    $y21 = $pdf->GetY();
    $h1 = $y21 - $y1;
    $px1 = $x1 + 40;
    $pdf->Ln(-$h1);
    $pdf->SetX($px1);

    $pdf->Cell(30, 5, number_format($rowDetallPtal[3], 2, '.', ','), 0, 0, 'R');
    $alt = max($h1, $h);
    $pdf->Ln($alt);
    $altY = $pdf->GetY();
    if ($altY > 240) {
        $pdf->AddPage();
        $pdf->Ln(5);
    }
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, 'Total:', 0, 0, 'R'); //Total
$pdf->Cell(30, 5, number_format($totalValor1, 2, '.', ','), 0, 0, 'R'); //Valor total Sí.

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, '3. MOVIMIENTO FINANCIERO Y CONTABLE', 1, 0, 'C');

$pdf->Ln(5);
$pdf->Cell(25, 5, utf8_decode('Cuenta'), 1, 0, 'C');
$pdf->Cell(60, 5, utf8_decode('Nombre de la Cuenta'), 1, 0, 'C');
$pdf->Cell(55, 5, utf8_decode('Tercero'), 1, 0, 'C');
$pdf->Cell(25, 5, utf8_decode('Débito'), 1, 0, 'C');
$pdf->Cell(25, 5, utf8_decode('Crédito'), 1, 0, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 8);
$comp = $_SESSION['idCompCntV'];
$sqlMovFina = "SELECT DISTINCT detComp.id_unico idDetalleComp, detComp.valor valorDetalle, 
    cuen.nombre nombreCuenta, cuen.codi_cuenta codigoCuenta, 
    cuen.naturaleza naturalezaCuenta, IF( CONCAT_WS(' ',
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
                                        tr.apellidodos )) AS NOMBRE,
                                        claseC.nombre 
    FROM gf_detalle_comprobante detComp 
    LEFT JOIN gf_centro_costo cc ON detComp.centrocosto = cc.id_unico 
    LEFT JOIN gf_tercero tr ON detComp.tercero = tr.id_unico 
LEFT JOIN gf_retencion ret ON ret.comprobante = detComp.comprobante
LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta
LEFT JOIN gf_clase_cuenta claseC ON claseC.id_unico = cuen.clasecuenta
WHERE detComp.comprobante = $comp AND cuen.nombre IS NOT NULL";
$movimientoFinanciero = $mysqli->query($sqlMovFina);
$totalDebito = 0;
$totalCredito = 0;

while ($rowMF = mysqli_fetch_array($movimientoFinanciero)) {
    $paginactual = $numpaginas;
    $debito = 0;
    $credito = 0;
    $cod = $rowMF[3];

    $nombCuen = mb_strtolower($rowMF[2], 'utf-8');
    $nombCuen = ucwords($nombCuen);

    $centroCost = mb_strtolower($rowMF[5], 'utf-8');
    $centroCost = ucwords($centroCost);
    if ($rowMF[4] == 1) {
        if ($rowMF[1] >= 0) {
            $debito = $rowMF[1];
        } else {
            $debito = '0.00';
        }
    } else if ($rowMF[4] == 2) {
        if ($rowMF[1] <= 0) {
            $x = ($rowMF[1] * -1);
            $debito = $x;
        } else {
            $debito = 0;
        }
    }

    if ($rowMF[4] == 1) {
        if ($rowMF[1] >= 0) {

            $credito = '0.00';
        } else {
            $x = ($rowMF[1] * -1);
            $credito = $x;
        }
    } else if ($rowMF[4] == 2) {
        if ($rowMF[1] <= 0) {
            $credito = '0.00';
        } else {

            $credito = $rowMF[1];
        }
    }


    if (strcasecmp($rowMF[6], 'pasivo general') || strcasecmp($rowMF[6], 'cuentas por pagar')) {
        $totalValor = $rowMF[1];
    }

    $totalDebito += $debito;
    $totalCredito += $credito;
    if (strlen($nombCuen) > 30 || strlen($centroCost) > 30) {
        $altY = $pdf->GetY();
        if ($altY > 240) {
            $pdf->AddPage();
            $pdf->Ln(5);
        }
    }
    $pdf->Cell(25, 5, utf8_decode($cod), 0, 0, 'L');
    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->Multicell(60, 5, utf8_decode($nombCuen), 0, 'L');
    $y2 = $pdf->GetY();
    $alto_de_fila = $y2 - $y1;
    $posicionX = $x1 + 60;
    if ($numpaginas > $paginactual) {
        $pdf->SetXY($posicionX, $yp);
        $alto_de_fila = $y2 - $yp;
    } else {
        $pdf->SetXY($posicionX, $y1);
    }

    $y3 = $pdf->GetY();
    $x3 = $pdf->GetX();
    $pdf->Multicell(55, 5, utf8_decode($centroCost), 0, 'L');
    $y4 = $pdf->GetY();
    $alto_de_fila1 = $y4 - $y3;
    $posicionX = $x3 + 55;

    if ($numpaginas > $paginactual) {
        $pdf->SetXY($posicionX, $yp);
        $alto_de_fila1 = $y4 - $yp;
    } else {
        $pdf->SetXY($posicionX, $y3);
    }


    $pdf->Cell(25, 5, number_format($debito, 2, '.', ','), 0, 0, 'R');
    $pdf->Cell(25, 5, number_format($credito, 2, '.', ','), 0, 0, 'R');

    #Determinar Valor máximo de altura
    $max = max($alto_de_fila, $alto_de_fila1);
    #Salto de línea
    $pdf->Ln($max);
    $altY = $pdf->GetY();
    if ($altY > 240) {
        $pdf->AddPage();
        $pdf->Ln(5);
    }
}


$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 5, utf8_decode('Totales'), 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 8);
$pdf->cellfitscale(25, 5, number_format($totalDebito, 2, '.', ','), 0, 0, 'R'); //Total débito
$pdf->cellfitscale(25, 5, number_format($totalCredito, 2, '.', ','), 0, 0, 'R'); //Total crédito
$pdf->Ln(10);


#########################################################################################################
#VALIDAR SI EL TIPO DE COMPROBANTE CNT APLICA RETENCIÓN
$tc = "SELECT
  tc.retencion
FROM
  gf_comprobante_cnt cn
LEFT JOIN
  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
WHERE
  cn.id_unico = $comp";
$tc1 =$mysqli->query($tc);
if(mysqli_num_rows($tc1)>0){
    $r = mysqli_fetch_row($tc1);
    $ret=$r[0];
} else {
    $ret=0;
}
if($ret==1){
	if($pdf->GetY()>240){
		$pdf->AddPage();
		$pdf->Ln(5);
	}
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('4. RETENCIÓN Y DESCUENTOS'),1,0,'C');
$pdf->Ln(5);
$pdf->Cell(100,5,utf8_decode('Tipo Retención'),1,0,'C');
$pdf->Cell(30,5,utf8_decode('Porcentaje'),1,0,'C');
$pdf->Cell(30,5,utf8_decode('Valor Retención'),1,0,'C');
$pdf->Cell(30,5,utf8_decode('Retención Base'),1,0,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);

$sqlR = "SELECT tpr.nombre,tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
FROM gf_retencion rt 
LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
WHERE cnt.id_unico=$idComp";
$resultR = $mysqli->query($sqlR);
$valorR = 0;

while($f= mysqli_fetch_row($resultR)){
  $tipo = utf8_decode($f[0]);
  $pdf->SetFont('Arial','',8);
  $y1 = $pdf->GetY();
  $x1 = $pdf->GetX();
  $pdf->Multicell(100,4,($tipo),0,'L');
  $y2 = $pdf->GetY();            
  $alto_de_fila = $y2-$y1;
  $posicionX = $x1 + 100;
  $pdf->SetXY($posicionX,$y1);
  $pdf->Cell(30,4,utf8_decode($f[1]),0,0,'R');
  $pdf->Cell(30,4,number_format($f[2], 2, '.', ','),0,0,'R');
  $pdf->Cell(30,4,number_format($f[3], 2, '.', ','),0,0,'R');
  $pdf->Ln($alto_de_fila); 
  $valorR = $f[2];
}
$pdf->Ln(5);
}

//Valor total en números y letras.


$sqlValorT = "SELECT SUM(valor) 
FROM
  gf_detalle_comprobante dc
LEFT JOIN 
  gf_cuenta c ON dc.cuenta = c.id_unico 
WHERE
  (c.clasecuenta = 4 OR c.clasecuenta=8) AND valor >0 AND dc.comprobante =" . $_SESSION['idCompCntV'];
$ValorT = $mysqli->query($sqlValorT);
if (mysqli_num_rows($ValorT) > 0) {
    $ValorT = mysqli_fetch_row($ValorT);
    if (empty($ValorT[0])) {
        $ValorT = 0.00;
    } else {
        $ValorT = $ValorT[0];
    }
} else {
    $ValorT = 0.00;
}


$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 5, 'Total: $ ', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(160, 5, number_format($ValorT, 2, '.', ','), 0, 0, 'L');

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 5, 'Valor en letras: ', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$valorLetras = numtoletras($ValorT);
$pdf->MultiCell(170, 5, $valorLetras, 0, 'L');

$fecha_div = explode("/", $fechaComp);
$diaS = $fecha_div[0];
$mesS = $fecha_div[1];
$mesS = (int) $mesS;
$anioS = $fecha_div[2];

$pdf->SetFont('Arial', 'B', 10);
$ciudadCompania = mb_strtoupper($ciudadCompania, 'utf-8');
$pdf->Cell(60, 13, utf8_decode('Se expide en ' . $ciudadCompania . ' a los ' . $diaS . ' días del mes de ' . $meses[$mesS] . ' de ' . $anioS), 0, 0, 'L');


//Líneas para firmas
$pdf->Ln(15);

$pdf->SetFont('Arial', 'B', 8);

#************************FIRMAS****************************#
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
$numFirmas = $i;

if($numFirmas > 3)
  $numFirmas = 3;

for($i = 0; $i < $numFirmas; $i++)
{
  $pdf->Cell(60,40,'',1,0,'C');
  
}

$pdf->Ln(24);
for($i = 0; $i < $numFirmas; $i++)
{
  $pdf->Cell(1,0,'',0,0,'L');
  $pdf->Cell(55,0,'',1,0,'L');
  $pdf->Cell(4,0,'',0,0,'L');
}


$pdf->Ln(2);
for($i = 0; $i < $numFirmas; $i++)
{
    if($firmaNom[$i]=='' || $firmaNom[$i]==""){
        $pdf->Cell(60,5,utf8_decode($firmaNom[$i]),0,0,'L');
    } else {
  $pdf->CellFitScale(60,5,utf8_decode($firmaNom[$i]),0,0,'L');
    }
    
}
  

$pdf->Ln(4);
for($i = 0; $i < $numFirmas; $i++)
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

 ob_end_clean();

$pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
?>

