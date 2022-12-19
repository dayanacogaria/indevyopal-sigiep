<?php 
#######################################################################################################
#       **********************      Modificaciones      **********************
#       **********************     Normal Santiago      **********************
#######################################################################################################
#20/06/2018 |Erica G.|Disponibilidad- Registro-CuentaPagar-Egreso
#16/06/2018 |Erica G.|Archivo Creado 
#######################################################################################################
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
session_start();
ob_start();
$con    = new ConexionPDO();
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                CONCAT_WS('-',ter.numeroidentificacion,ter.digitoverficacion),
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                dir.ciudad_direccion, 
                c.nombre 
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico 
LEFT JOIN       gf_ciudad c ON dir.ciudad_direccion = c.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];
$ciudad_com  = $rowC[0][8];

#*** Recibir Variables ***#
$fecha_inicial  = fechaC($_REQUEST['fechaI']);
$fecha_final    = fechaC($_REQUEST['fechaF']);
$tipo_compr     = $_REQUEST['tipoComprobante'];
$tipo           = $_REQUEST['tipo'];
$anno           = $_SESSION['anno'];
$row    = $con->Listar("SELECT cp.id_unico, 
    cp.numero, 
    DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
    cp.fecha, 
    cp.descripcion, 
    cp.fechavencimiento, 
    tcp.codigo, tcp.nombre, 
    IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE,
    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
     t.numeroidentificacion, 
    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
    tcp.id_unico, 
    UPPER(cp.usuario), 
    cp.numerocontrato, 
    tc.nombre,
    GROUP_CONCAT(tel.valor),
    GROUP_CONCAT(dir.direccion), 
    cp.fechavencimiento,
    YEAR(cp.fechavencimiento),
    DATE_FORMAT(cp.fechavencimiento,'%d/%m/%Y')  
FROM gf_comprobante_pptal cp 
LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
LEFT JOIN gf_clase_contrato tc ON cp.clasecontrato = tc.id_unico 
LEFT JOIN gf_telefono tel ON t.id_unico = tel.tercero 
LEFT JOIN gf_direccion dir ON dir.tercero = t.id_unico 
WHERE cp.parametrizacionanno = $anno  
AND cp.fecha BETWEEN '$fecha_inicial' AND '$fecha_final'  
AND cp.tipocomprobante = $tipo_compr 
GROUP BY cp.id_unico 
ORDER BY cp.numero ASC"); 
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');


switch ($tipo):
    # ** Disponibilidades ** #
    case 1:
        class PDF extends FPDF {
            function Header() {
                global $ruta_logo ;
                if ($ruta_logo != '') {
                    $this->Image('../' . $ruta_logo, 10, 8, 25);
                }
            }
            function Footer() {
                global $usuario ;
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->Cell(35,10,'Elaborado por: '.utf8_decode($usuario),0,0, 'L');
                $this->Cell(155,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
            }
        }
        $pdf = new PDF('P', 'mm', 'Letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $nombre ="";
        $nombre ="Disponiblidad";
        $datos = count($row);
        for ($i = 0; $i < count($row); $i++) {
            $usuario = $row[$i][11];
            $tipo_c  = $row[$i][7];
            $numero  = $row[$i][1];
            #*********Encabezado********#
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(100);
            $pdf->SetXY(28, 13);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
            $pdf->SetX(28);
            $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->Ln(10);
            $pdf->SetX(28);
            $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
            $pdf->Ln(7);
            $pdf->SetX(28);
            $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
            $pdf->Ln(10);
            #**************************#
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetX(28);
            $pdf->MultiCell(175, 5, utf8_decode('EL SUSCRITO CERTIFICA:'), 0, 'C');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(180, 5, utf8_decode("Que una vez revisado el libro "
                . "de control de presupuesto correspondiente a la vigencia fiscal "
                . "del año ".$row[$i][17]." se encontró que existe disponibilidad presupuestal para cubrir "
                . "el siguiente gasto:"), 0, 'J');
            $pdf->Ln(4);
            $pdf->Cell(20, 5, utf8_decode('Fecha:'), 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(160, 5, utf8_decode($row[$i][2]), 0, 'L');
            $pdf->Ln(8);
            #*********** Detalles *************#
            $pdf->SetFont('Arial', 'B', 9, 0, 'C');
            $pdf->Cell(60, 5, 'Rubro', 1, 0, 'C');
            $pdf->Cell(60, 5, 'Fuente', 1, 0, 'C');
            $pdf->Cell(35, 5, 'Saldo Disponible', 1, 0, 'C');
            $pdf->Cell(35, 5, 'Valor', 1, 0, 'C');
            $pdf->Ln(5);
            $detalle = $con->Listar("SELECT dc.id_unico, 
                    rub.codi_presupuesto, 
                    rub.nombre, 
                    dc.valor, 
                    rubFue.id_unico, 
                    fue.nombre, 
                    dc.saldo_disponible 
                  FROM gf_detalle_comprobante_pptal dc  
                  left join gf_rubro_fuente rubFue on dc.rubrofuente = rubFue.id_unico 
                  left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
                  left join gf_concepto_rubro conRub on conRub.id_unico = dc.conceptorubro
                  left join gf_concepto con on con.id_unico = conRub.concepto 
                  left join gf_fuente fue on fue.id_unico = rubFue.fuente 
                  where dc.comprobantepptal =".$row[$i][0]);
            $totalValor =0;
            $pdf->SetFont('Arial', '', 9);
            for ($j = 0; $j < count($detalle); $j++) {
                $totalValor     += $detalle[$j][3];
                $saldoDisponible = $detalle[$j][6];
                $codiRub         = $detalle[$j][1];
                $nombreRub       = ($codiRub . ' - ' . $detalle[$j][2]);
                $altY            = $pdf->GetY();
                if ($altY > 240) {
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(100);
                    $pdf->SetXY(28, 13);
                    $pdf->SetFont('Arial', 'B', 13);
                    $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                    $pdf->SetX(28);
                    $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                    $pdf->SetFont('Arial', 'B', 15);
                    $pdf->Ln(10);
                    $pdf->SetX(28);
                    $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                    $pdf->Ln(7);
                    $pdf->SetX(28);
                    $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                    $pdf->Ln(10);
                }
                $fuente     = ($detalle[$j][5]);
                $valorR     = number_format($detalle[$j][3], 2, '.', ',');
                $saldoDis   = number_format($saldoDisponible, 2, '.', ',');
                $y11 = $pdf->GetY();
                $x11 = $pdf->GetX();
                $pdf->MultiCell(60, 5, utf8_decode(ucwords(mb_strtolower($nombreRub))), 0, 'J');
                $y21 = $pdf->GetY();
                $h1 = $y21 - $y11;
                $px1 = $x11 + 60;
                if ($numpaginas > $paginactual) {
                    $pdf->SetXY($px1, $yp);
                    $h1 = $y21 - $yp;
                } else {
                    $pdf->SetXY($px1, $y11);
                }
                $y1 = $pdf->GetY();
                $x1 = $pdf->GetX();
                $pdf->MultiCell(60, 5, utf8_decode(ucwords(mb_strtolower($fuente))), 0, 'J');
                $y2 = $pdf->GetY();
                $h = $y2 - $y1;
                $px = $x1 + 60;
                if ($numpaginas > $paginactual) {
                    $pdf->SetXY($px, $yp);
                    $h = $y2 - $yp;
                } else {
                    $pdf->SetXY($px, $y1);
                }
                $alto = max($h1, $h);
                $pdf->Cell(35, $alto, $saldoDis, 0, 0, 'R');
                $pdf->Cell(35, $alto, $valorR, 0, 0, 'R');
                $pdf->SetXY($x11, $y11);
                $pdf->Cell(60, $alto, '', 1, 0, 'R');
                $pdf->Cell(60, $alto, '', 1, 0, 'R');
                $pdf->Cell(35, $alto, '', 1, 0, 'R');
                $pdf->Cell(35, $alto, '', 1, 0, 'R');

                $pdf->Ln($alto);
                $altY = $pdf->GetY();
            }
            #***********************************#
            #*********** Totales *************#
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(155, 5, 'TOTAL DISPONIBILIDAD:', 0, 0, 'R'); //Rubro
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(35, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R'); //Valor Sí.
            $altY            = $pdf->GetY();
            if ($altY > 240) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(100);
                $pdf->SetXY(28, 13);
                $pdf->SetFont('Arial', 'B', 13);
                $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                $pdf->SetX(28);
                $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Ln(10);
                $pdf->SetX(28);
                $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                $pdf->Ln(10);
            }
            $pdf->Ln(10);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(190, 5, utf8_decode('Concepto: '), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetX(35);
            $pdf->MultiCell(165, 5, utf8_decode($row[$i][4]), 0, 'J');
            $pdf->Ln(5);
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
            $pdf->SetXY($x, $y);
            $pdf->Cell(190, $h, '', 1, 0, 'L');
            $pdf->SetX(10);

            $pdf->Ln($h);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(190, 5, utf8_decode('Son: '), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetX(35);
            $valorLetras = numtoletras($totalValor);
            $pdf->MultiCell(155, 5, utf8_decode($valorLetras), 0, 'J');
            $pdf->SetX(10);
            $pdf->Ln(10);
            if ($altY > 220) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(100);
                $pdf->SetXY(28, 13);
                $pdf->SetFont('Arial', 'B', 13);
                $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                $pdf->SetX(28);
                $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Ln(10);
                $pdf->SetX(28);
                $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                $pdf->Ln(20);
            } 
            $fecha_div = explode("/", $row[$i][2]);
            $diaS = $fecha_div[0];
            $mesS = $fecha_div[1];
            $mesS = (int) $mesS;
            $anioS = $fecha_div[2];
            $pdf->SetFont('Arial', 'B', 10);
            $ciudadCompania = mb_strtoupper($ciudad_com, 'utf-8');
            $pdf->Cell(60, 13, utf8_decode("NOTA: Este cerficado tiene validez para su utilización hasta ".$row[$i][18]), 0, 'J');
            $pdf->Ln(10);
            $altY            = $pdf->GetY();
            if ($altY > 220) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(100);
                $pdf->SetXY(28, 13);
                $pdf->SetFont('Arial', 'B', 13);
                $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                $pdf->SetX(28);
                $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Ln(10);
                $pdf->SetX(28);
                $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                $pdf->Ln(20);
            } else {
                $pdf->Ln(30);
            }
            #****************Consulta SQL para Firma****************#
            $rowc = $con->Listar("SELECT IF(CONCAT_WS(' ',
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
              WHERE tcp.id_unico = ".$row[$i][10]." 
              AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC");
            
            $altofinal = $pdf->GetY();
            $altop = $pdf->GetPageHeight();
            $altofirma = $altop - $altofinal;

            $c = count($rowc);
            $tfirmas = ($c / 2) * 33;

            $xt = 10;
            for ($z = 0; $z < count($rowc); $z++) {
                if (!empty($rowc[$z][5])) {
                    if ($row[$i][3] <= $rowc[$z][5]) {

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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][3]), 0, 0, 'L');
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
                            if (!empty($rowc[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $rowc[$z][6]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][3]), 0, 0, 'L');
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
                            if (!empty($rowc[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $rowc[$z][6]), 0, 0, 'L');
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
                } elseif (!empty($rowc[$z][4])) {

                    if ($row[$i][3] >= $rowc[$z][4]) {
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][3]), 0, 0, 'L');
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
                            if (!empty($rowc[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $rowc[$z][6]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($rowc[$z][3]), 0, 0, 'L');
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
                            if (!empty($rowc[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $rowc[$z][6]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($rowc[$z][0]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($rowc[$z][3]), 0, 0, 'L');
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
                        if (!empty($rowc[$z][6])) {
                            $pdf->Cell(190, 2, utf8_decode('T.P:' . $rowc[$z][6]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($rowc[$z][0]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($rowc[$z][3]), 0, 0, 'L');
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
                        if (!empty($rowc[$z][6])) {
                            $pdf->Cell(190, 2, utf8_decode('T.P:' . $rowc[$z][6]), 0, 0, 'L');
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
            if($i == ($datos-1)){
            }else {
                $pdf->AddPage();
            }
        }
        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
    break;
    # ** Registros ** #  
    case 2:
        class PDF extends FPDF {
            function Header() {
                global $ruta_logo ;
                if ($ruta_logo != '') {
                    $this->Image('../' . $ruta_logo, 10, 8, 25);
                }
            }
            function Footer() {
                global $usuario;
                $this->SetY(-25);
                $this->SetFont('Arial', '', 8);
                $this->MultiCell(190, 5, utf8_decode('NOTA: SE ENTIENDE QUE ESTA CERTIFICACION ES ESTRICTAMENTE PRESUPUESTAL Y SOMETIDA AL CUMPLIMIENTO DEL PROCEDIMIENTO LEGAL ESTABLECIDO'), 0, 'J');
                $this->Ln(2);
                $this->SetFont('Arial', 'B', 8);
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->Cell(35,10,'Elaborado por: '.utf8_decode($usuario),0,0, 'L');
                $this->Cell(155,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
            }
        }
        $pdf = new PDF('P', 'mm', 'Letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $nombre ="";
        $nombre ="Registro";
        $datos =count($row);
        for ($i = 0; $i < count($row); $i++) {
            $usuario    =  $row[$i][11];
            $totalValor = 0;
            #*********** Encabezado *************#
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(100);
            $pdf->SetXY(28, 13);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
            $pdf->SetX(28);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
            $pdf->Ln(7);
            $pdf->SetX(28);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
            $pdf->Ln(7);
            $pdf->SetX(28);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
            $pdf->Ln(10);
            #***********************************#
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetX(28);
            $pdf->Cell(175, 5, utf8_decode('EL SUSCRITO CERTIFICA'), 0, 0, 'C');
            $pdf->Ln(7);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(190, 3, utf8_decode("QUE EN EL PRESUPUESTO ORDINARIO DE GASTOS E INVERSIONES DE LA "
                            . "VIGENCIA FISCAL EN CURSO, HA QUEDADO REGISTRADO PRESUPUESTALMENTE UN COMPROMISO CON "
                            . "CARGO AL (LOS) SIGUIENTE(S) RUBRO(S):"), 0, 'J');
            $pdf->Ln(7);
            #****************Detalles*******************#
            $pdf->SetFont('Arial', 'B', 9, 0, 'C');
            $pdf->Cell(60, 5, utf8_decode('Rubro'), 1, 0, 'C');
            $pdf->Cell(60, 5, utf8_decode('Fuente'), 1, 0, 'C');
            $pdf->Cell(40, 5, utf8_decode('Beneficiario'), 1, 0, 'C');
            $pdf->Cell(30, 5, utf8_decode('Valor'), 1, 0, 'C'); //Valor
            $pdf->Ln(5);
            $rowd = $con->Listar("SELECT  detComP.id_unico, 
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
                left join gf_tercero tr on tr.id_unico = detComP.tercero 
                LEFT JOIN gf_detalle_comprobante_pptal da ON da.id_unico = detComP.comprobanteafectado 
                LEFT JOIN gf_comprobante_pptal cpa ON da.comprobantepptal = cpa.id_unico 
                LEFT JOIN gf_tipo_comprobante_pptal tca ON tca.id_unico = cpa.tipocomprobante 
                where detComP.comprobantepptal =".$row[$i][0]);
            for ($j = 0; $j < count($rowd); $j++) {
                $pdf->SetFont('Arial', '', 9);
                $altY = $pdf->GetY();
                if ($altY > 240) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(100);
                    $pdf->SetXY(28, 13);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                    $pdf->SetX(28);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                    $pdf->Ln(7);
                    $pdf->SetX(28);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                    $pdf->Ln(7);
                    $pdf->SetX(28);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                    $pdf->Ln(10);
                    
                }
                $saldDisp = 0;
                $totalAfec = 0;
                $valora = $con->Listar("SELECT SUM(valor)   
                    FROM gf_detalle_comprobante_pptal   
                    WHERE comprobanteafectado = ".$rowd[$j][0]);
                $valorPpTl = $rowd[$j][2] - $valora[0][0];
                if ($rowd[$j][10] != 0) {
                    $tipocomprobanteA = $rowd[$j][9];
                    $numComprobanteAfectado = $rowd[$j][8];
                } else {
                    $numComprobanteAfectado = '';
                }
                $numRub = $rowd[$j][5] . ' - ' . ucwords(mb_strtolower($rowd[$j][1]));
                $fuente = $rowd[$j][6] . ' - ' . ucwords(mb_strtolower($rowd[$j][4]));
                $tercerod =ucwords(mb_strtolower($rowd[$j][7]));

                $valor = $rowd[$j][2];
                $ben = ucwords(mb_strtolower($rowd[$j][7]));
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(60, 4, utf8_decode($numRub), 0, 'L');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                $px = $x + 60;
                $pdf->SetXY($px, $y);

                $x1 = $pdf->GetX();
                $y1 = $pdf->GetY();
                $pdf->MultiCell(60, 4, utf8_decode($fuente), 0, 'L');
                $y21 = $pdf->GetY();
                $h1 = $y21 - $y1;
                $px1 = $x1 + 60;
                $pdf->SetXY($px1, $y1);

                $x2 = $pdf->GetX();
                $y2 = $pdf->GetY();
                $pdf->MultiCell(40, 4, utf8_decode($tercerod), 0, 'J');
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

                $alt = max($h, $h1, $h2, $h3, $h5,$h11);
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(60, $alt, '', 1, 'C');
                $pdf->SetXY($x + 60, $y);
                $pdf->MultiCell(60, $alt, '', 1, 'C');
                $pdf->SetXY($x + 120, $y);
                $pdf->MultiCell(40, $alt, '', 1, 'C');
                $pdf->SetXY($x + 160, $y);
                $pdf->MultiCell(30, $alt, '', 1, 'C');

                $totalValor = $totalValor + $valor;
            }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(160, 5, "TOTAL " . mb_strtoupper($row[$i][7]) . ":", 0, 0, 'R'); //Rubro
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(30, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R');
            $altod = $pdf->GetPageHeight();
            $altoP = $pdf->GetY();
            $altoC = $altod - $altoP;
            $pdf->LN(5);
            if ($altoC < 80) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(100);
                $pdf->SetXY(28, 13);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                $pdf->SetX(28);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                $pdf->Ln(10);
            }
            $dis = $con->Listar("SELECT DISTINCT
            cpa.numero,
            tcp.codigo 
            FROM
              gf_comprobante_pptal cp
            LEFT JOIN
              gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal
            LEFT JOIN
              gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
            LEFT JOIN
              gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
            LEFT JOIN
              gf_tipo_comprobante_pptal tcp ON cpa.tipocomprobante = tcp.id_unico 
            WHERE
              cp.id_unico =".$row[$i][0]);
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 0, utf8_decode(mb_strtoupper($dis[0][1]) . ':'), 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(150, 0, utf8_decode($dis[0][0]), 0, 0, 'L');

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 0, utf8_decode('A NOMBRE DE: '), 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(150, 0, utf8_decode(mb_strtoupper($row[$i][8])), 0, 0, 'L');

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->CellFitScale(40, 0, utf8_decode(mb_strtoupper('C.C O NIT :')), 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(150, 0, utf8_decode($row[$i][9]), 0, 'L');
            

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->CellFitScale(40, 0, utf8_decode('TIPO DE CONTRATATACIÓN:'), 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(150, 0, utf8_decode(ucwords(mb_strtoupper($row[$i][13]))), 0, 'L');

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->CellFitScale(40, 0, utf8_decode('NÚMERO CONTRATO:'), 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(150, 0, utf8_decode($row[$i][12]), 0, 'L');

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(190, 5, utf8_decode('OBJETO: '), 1);
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(190, 5, utf8_decode(($row[$i][4])), 1, 'J'); //Descripción
            $pdf->Ln(5);

            $pdf->Ln(5);
            $fechaCompF = $row[$i][2];
            $fecha_div = explode("-", $row[$i][3]);
            $diaSb = $fecha_div[2];
            $mesSb = $fecha_div[1];
            $mesSb = (int) $mesSb;
            $anioSb = $fecha_div[0];

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(60, 13, utf8_decode('Se expide en ' . strtoupper($ciudad_com) . ' a los ' . $diaSb . ' días del mes de ' . $meses[$mesSb] . ' de ' . $anioSb), 0, 0, 'L');
            $pdf->Ln(30);
            #*******************************************#
            #****************Firmas*********************#
            $row_t = $con->Listar("SELECT IF(CONCAT_WS(' ',
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
              WHERE tcp.id_unico = ".$row[$i][10]."  
              AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC");          
            
            $altofinal  = $pdf->GetY();
            $altop      = $pdf->GetPageHeight();
            $altofirma  = $altop - $altofinal;
            $c = count($row_t);
            $tfirmas = ($c / 2) * 33;

            if ($tfirmas > $altofirma) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(100);
                $pdf->SetXY(28, 13);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->MultiCell(175, 7, utf8_decode(mb_strtoupper($razonsocial)), 0, 'C');
                $pdf->SetX(28);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(175, 5, utf8_decode('NIT: ' . $numeroIdent), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(175, 5, mb_strtoupper($row[$i][7]), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(28);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(175, 5, utf8_decode('Número: ' . $row[$i][1]), 0, 0, 'C');
                $pdf->Ln(10);
            } else {
                $al = $pdf->GetY();
                if($al<190){
                    $pdf->ln(25);
                } else {
                    $pdf->ln(10);
                }
            }
            $xt = 10;
            for ($z = 0; $z < count($row_t); $z++) {
                if (!empty($row_t[$z][5])) {
                    if ($row[$i][3] <= $row_t[$z][5]) {
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][3]), 0, 0, 'L');
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
                            if (!empty($row_t[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $row_t[$z][6]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][3]), 0, 0, 'L');
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
                            if (!empty($row_t[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $row_t[$z][6]), 0, 0, 'L');
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
                } elseif (!empty($row_t[$z][4])) {

                    if ($row[$i][3] >= $row_t[$z][4]) {
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][3]), 0, 0, 'L');
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
                            if (!empty($row_t[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $row_t[$z][6]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][0]), 0, 0, 'L');
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
                            $pdf->Cell(190, 2, utf8_decode($row_t[$z][3]), 0, 0, 'L');
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
                            if (!empty($row_t[$z][6])) {
                                $pdf->Cell(190, 2, utf8_decode('T.P:' . $row_t[$z][6]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($row_t[$z][0]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($row_t[$z][3]), 0, 0, 'L');
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
                        if (!empty($row_t[$z][6])) {
                            $pdf->Cell(190, 2, utf8_decode('T.P:' . $row_t[$z][6]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($row_t[$z][0]), 0, 0, 'L');
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
                        $pdf->Cell(190, 2, utf8_decode($row_t[$z][3]), 0, 0, 'L');
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
                        if (!empty($row_t[$z][6])) {
                            $pdf->Cell(190, 2, utf8_decode('T.P:' . $row_t[$z][6]), 0, 0, 'L');
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
            #*******************************************#
            if($i == ($datos-1)){
            }else {
                $pdf->AddPage();
            }
        }
        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
    break;
    # ** Cuentas X Pagar ** #
    case 3:
        $nombre ="Cuenta Por Pagar";
        class PDF extends FPDF {
            function Header() {
                global $ruta_logo ;
                if ($ruta_logo != '') {
                    $this->Image('../' . $ruta_logo, 10, 5, 25);
                }
            }
            function Footer() {
                global $usuario;
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->Cell(35,10,'Elaborado por: '.utf8_decode($usuario),0,0, 'L');
                $this->Cell(155,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
            }
        }
        $pdf = new PDF('P', 'mm', 'Letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $nombre ="";
        $nombre ="Registro";
        $datos =count($row);
        for ($i = 0; $i < count($row); $i++) {
            $totalValor1 =0;
            # Buscar Id_CNT
            $id_cnt ="";
            $row_idd = $con->Listar("SELECT DISTINCT cn.id_unico, cn.numero, tc.nombre, tc.id_unico   
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                LEFT JOIN gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico 
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE dp.comprobantepptal =".$row[$i][0]." AND tc.clasecontable = 13");
            if(count($row_idd)>0){
                $id_cnt =$row_idd[0][0];
            } else {
                $row_idd = $con->Listar("SELECT DISTINCT cn.id_unico, cn.numero, tc.nombre, tc.id_unico 
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico  
                WHERE tc.comprobante_pptal=".$row[$i][10]." AND cn.numero = ".$row[$i][1]);
                if(count($row_idd)>0){
                    $id_cnt =$row_idd[0][0];
                }
            }
            if(!empty($id_cnt)){
                $usuario    =  $row[$i][11];
                $totalValor = $con->Listar("SELECT  SUM(valor) 
                        FROM gf_detalle_comprobante dc 
                        WHERE naturaleza=2  
                        AND valor >0 
                        AND dc.comprobante =$id_cnt");
                $totalValor = $totalValor[0][0];
                #************Encabezado*************#
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetX(35);
                $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                $pdf->Ln(2);
                $pdf->SetX(35);
                $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                $pdf->Ln(6);
                $pdf->SetX(35);
                $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                $pdf->Ln(6);
                $pdf->SetX(35);
                $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                $pdf->Ln(6);
                #**********************************#
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(190, 6, utf8_decode('1. DATOS DEL BENEFICIARIO'), 1, 0, 'C');
                $pdf->Ln(6);
                #***********Nombre***********#
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 6, utf8_decode('Nombre: ' ), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(160, 6, utf8_decode(ucwords(mb_strtolower($row[$i][8]))),  0, 'L');
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
                $pdf->Cell(45, 6, utf8_decode($row[$i][9]),0, 0, 'L');
                $pdf->Ln(6);
                $ydd = $pdf->GetY();
                $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(60, $al,utf8_decode(''),1, 0, 'L');
                #***********Telefonos***********#
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 6,utf8_decode('Teléfonos: '),0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(105, 6, utf8_decode( $row[$i][14]),0, 0, 'L');
                $pdf->Ln(6);
                $ydd = $pdf->GetY();
                 $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(130, $al,utf8_decode(''),1, 0, 'L');
                $pdf->Ln($al);
                #***********Direccion***********#
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 6,utf8_decode('Dirección: '),0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(160, 5,utf8_decode(ucwords(mb_strtolower($row[$i][15]))),0, 'J');
                $ydd = $pdf->GetY();
                $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(190, $al,utf8_decode(''),1, 0, 'L');
                $pdf->Ln($al);
                #***********Descripcion***********#
                $pdf->SetFont('Arial', 'B', 10);
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->Cell(190, 6,utf8_decode('Descripción: '),0, 0, 'L');
                $pdf->SetFont('Arial','',10);
                $pdf->Ln(6);
                $pdf->Multicell(190, 6,utf8_decode($row[$i][4]), 0, 'J');
                $ydd = $pdf->GetY();
                $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(190, $al,utf8_decode(''),1, 0, 'L');
                $pdf->Ln($al);
                #***********Tipo de contrato***********#
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 6, utf8_decode('Tipo de contrato: ' ), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(100, 6, utf8_decode( $row[$i][13]),0, 0, 'L');
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
                $pdf->Cell(45, 6, utf8_decode( '   '.$row[$i][12]),0, 0, 'L');
                $pdf->Ln(6);
                $ydd = $pdf->GetY();
                $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(60, $al,utf8_decode(''),1, 0, 'L');
                $pdf->Ln(6);
                #***********N° Documento***********#
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 6, utf8_decode('No de documento: ' ), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(100, 6, utf8_decode('   '.$row[$i][1]),0, 0, 'L');
                $pdf->Ln(6);
                $ydd = $pdf->GetY();
                $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(130, $al,utf8_decode(''),1, 0, 'L');
                #***********Valor***********#
                if($totalValor<0){
                    $totalValor =$totalValor*-1;
                }
                $xd=$pdf->GetX();
                $yd=$pdf->GetY();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 6, utf8_decode('Valor: '), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(45, 6, utf8_decode( '  $ '. number_format($totalValor, 2, '.', ',')),0, 0, 'L');
                $pdf->Ln(6);
                $ydd = $pdf->GetY();
                $al = $ydd-$yd;
                $pdf->SetXY($xd,$yd);
                $pdf->Cell(60, $al,utf8_decode(''),1, 0, 'L');
                #*********** Movimiento Presupuestal ***********#
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
                $rowD = $con->Listar("SELECT DISTINCT
                    cpop.numero,
                    cpr.numero,
                    cpd.numero,
                    dcpcx.valor,
                    rpptal.codi_presupuesto,
                    rpptal.nombre, 
                    f.id_unico, 
                    f.nombre, rf.id_unico, dcpcx.id_unico  
                FROM
                  gf_detalle_comprobante_pptal dcpcx 
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
                WHERE dcpcx.comprobantepptal=".$row[$i][0]);
                for ($j = 0; $j < count($rowD); $j++) {
                    $altY = $pdf->GetY();
                    if ($altY > 240) {
                        $pdf->AddPage();
                        #************Encabezado*************#
                        $pdf->SetFont('Arial', 'B', 12);
                        $pdf->SetX(35);
                        $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                        $pdf->Ln(2);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                        $pdf->Ln(6);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                        $pdf->Ln(6);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                        $pdf->Ln(6);
                    }

                    if (empty($rowD[$j][2])) {
                        $numComPtalDisponibilidad = $rowD[$j][1];
                        $numComPtalRegistro = $rowD[$j][0];
                    } else {
                        $numComPtalDisponibilidad = $rowD[$j][2];
                        $numComPtalRegistro = $rowD[$j][1];
                    }
                    $fuente = $rowD[$j][7];
                    $idRubroFuen = $rowD[$j][8];
                    $codRubro = $rowD[$j][4];
                    $nomRubro = $rowD[$j][5];
                    $halla = 0;
                    $totalValor1 += $rowD[$j][3];
                    $saldoDisponible = apropiacion($idRubroFuen) - disponibilidades($idRubroFuen);
                    $pdf->Cell(25, 5, utf8_decode($numComPtalDisponibilidad), 0, 0, 'L');
                    $pdf->Cell(25, 5, utf8_decode($numComPtalRegistro), 0, 0, 'L');
                    $pdf->Cell(25, 5, utf8_decode($codRubro), 0, 0, 'L');
                    $y = $pdf->GetY();
                    $x = $pdf->GetX();
                    $pdf->MultiCell(45, 5, utf8_decode(ucwords(mb_strtolower($nomRubro))), 0, 'J');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    $px = $x + 45;
                    $pdf->Ln(-$h);
                    $pdf->SetX($px);

                    $y1 = $pdf->GetY();
                    $x1 = $pdf->GetX();
                    $pdf->MultiCell(40, 5, utf8_decode(ucwords(mb_strtolower($fuente))), 0, 'J');
                    $y21 = $pdf->GetY();
                    $h1 = $y21 - $y1;
                    $px1 = $x1 + 40;
                    $pdf->Ln(-$h1);
                    $pdf->SetX($px1);

                    $pdf->Cell(30, 5, number_format($rowD[$j][3], 2, '.', ','), 0, 0, 'R');
                    $alt = max($h1, $h);
                    $pdf->Ln($alt);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(160, 5, 'Total:', 0, 0, 'R'); 
                $pdf->Cell(30, 5, number_format($totalValor1, 2, '.', ','), 0, 0, 'R'); //Valor total Sí.
                $altY = $pdf->GetY();
                if ($altY > 240) {
                    $pdf->AddPage();
                    #************Encabezado*************#
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetX(35);
                    $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                    $pdf->Ln(2);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                    $pdf->Ln(6);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                    $pdf->Ln(6);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                    $pdf->Ln(6);
                }
                #********* Movimiento Contable ************#
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
                $rowdc = $con->Listar("SELECT DISTINCT 
                    detComp.id_unico idDetalleComp, detComp.valor valorDetalle, 
                    cuen.nombre nombreCuenta, cuen.codi_cuenta codigoCuenta, 
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
                        tr.apellidodos )) AS NOMBRE,
                    claseC.nombre 
                FROM gf_detalle_comprobante detComp 
                LEFT JOIN gf_centro_costo cc    ON detComp.centrocosto = cc.id_unico 
                LEFT JOIN gf_tercero tr         ON detComp.tercero = tr.id_unico 
                LEFT JOIN gf_retencion ret      ON ret.comprobante = detComp.comprobante
                LEFT JOIN gf_cuenta cuen        ON cuen.id_unico = detComp.cuenta
                LEFT JOIN gf_clase_cuenta claseC ON claseC.id_unico = cuen.clasecuenta
                WHERE detComp.comprobante = $id_cnt AND cuen.nombre IS NOT NULL");
                $totalDebito  = 0;
                $totalCredito = 0;
                for ($d = 0; $d < count($rowdc); $d++) {
                    $altY = $pdf->GetY();
                    if ($altY > 240) {
                        $pdf->AddPage();
                        #************Encabezado*************#
                        $pdf->SetFont('Arial', 'B', 12);
                        $pdf->SetX(35);
                        $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                        $pdf->Ln(2);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                        $pdf->Ln(6);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                        $pdf->Ln(6);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                        $pdf->Ln(6);
                    }
                    $debito = 0;
                    $credito = 0;
                    $cod = $rowdc[$d][3];
                    $nombCuen   = ucwords(mb_strtolower($rowdc[$d][2]));
                    $centroCost = ucwords(mb_strtolower($rowdc[$d][5]));
                    $centroCost = ucwords($centroCost);
                    if ($rowdc[$d][4] == 1) {
                        if ($rowdc[$d][1] >= 0) {
                            $debito = $rowdc[$d][1];
                        } else {
                            $debito = '0.00';
                        }
                    } else if ($rowdc[$d][4] == 2) {
                        if ($rowdc[$d][1] <= 0) {
                            $x = ($rowdc[$d][1] * -1);
                            $debito = $x;
                        } else {
                            $debito = 0;
                        }
                    }
                    if ($rowdc[$d][4] == 1) {
                        if ($rowdc[$d][1] >= 0) {
                            $credito = '0.00';
                        } else {
                            $x = ($rowdc[$d][1] * -1);
                            $credito = $x;
                        }
                    } else if ($rowdc[$d][4] == 2) {
                        if ($rowdc[$d][1] <= 0) {
                            $credito = '0.00';
                        } else {
                            $credito = $rowdc[$d][1];
                        }
                    }
                    if (strcasecmp($rowdc[$d][6], 'pasivo general') || strcasecmp($rowdc[$d][6], 'cuentas por pagar')) {
                        $totalValor = $rowdc[$d][1];
                    }
                    $totalDebito  += $debito;
                    $totalCredito += $credito;
                    
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
                    $pdf->Multicell(55, 5, utf8_decode($centroCost), 0, 'L');
                    $y4 = $pdf->GetY();
                    $alto_de_fila1 = $y4 - $y3;
                    $posicionX = $x3 + 55;
                    $pdf->SetXY($posicionX, $y3);
                    
                    $pdf->Cell(25, 5, number_format($debito, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(25, 5, number_format($credito, 2, '.', ','), 0, 0, 'R');

                    #Determinar Valor máximo de altura
                    $max = max($alto_de_fila, $alto_de_fila1);
                    #Salto de línea
                    $pdf->Ln($max);
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(140, 5, utf8_decode('Totales'), 0, 0, 'R');
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->cellfitscale(25, 5, number_format($totalDebito, 2, '.', ','), 0, 0, 'R'); //Total débito
                $pdf->cellfitscale(25, 5, number_format($totalCredito, 2, '.', ','), 0, 0, 'R'); //Total crédito
                $pdf->Ln(10);
                
                #******************************* Retenciones ******************************#
                #Validar Si Aplica Retencion
                $tc = "SELECT
                  tc.retencion
                FROM
                  gf_comprobante_cnt cn
                LEFT JOIN
                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                WHERE
                  cn.id_unico = ".$id_cnt;
                $tc1 =$mysqli->query($tc);
                if(mysqli_num_rows($tc1)>0){
                    $r = mysqli_fetch_row($tc1);
                    $ret=$r[0];
                } else {
                    $ret=0;
                }
                if($ret==1){
                    $rowR = $con->Listar("SELECT tpr.nombre,
                        tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
                    FROM gf_retencion rt 
                    LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
                    LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
                    WHERE cnt.id_unico = $id_cnt");
                    if(count($rowR)>0){
                        $altY = $pdf->GetY();
                        if ($altY > 240) {
                            $pdf->AddPage();
                            #************Encabezado*************#
                            $pdf->SetFont('Arial', 'B', 12);
                            $pdf->SetX(35);
                            $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                            $pdf->Ln(2);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                            $pdf->Ln(6);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                            $pdf->Ln(6);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                            $pdf->Ln(6);
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
                        $valorR = 0;
                        for ($r = 0; $r < count($rowR);$r++) {
                            $altY = $pdf->GetY();
                            if ($altY > 240) {
                                $pdf->AddPage();
                                #************Encabezado*************#
                                $pdf->SetFont('Arial', 'B', 12);
                                $pdf->SetX(35);
                                $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                                $pdf->Ln(2);
                                $pdf->SetX(35);
                                $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                                $pdf->Ln(6);
                                $pdf->SetX(35);
                                $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                                $pdf->Ln(6);
                                $pdf->SetX(35);
                                $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                                $pdf->Ln(6);
                            }
                            $tipo = ($rowR[$r][0]);
                            $pdf->SetFont('Arial','',10);
                            $y1 = $pdf->GetY();
                            $x1 = $pdf->GetX();
                            $pdf->Multicell(100,5,utf8_decode($tipo),0,'L');
                            $y2 = $pdf->GetY();            
                            $alto_de_fila = $y2-$y1;
                            $posicionX = $x1 + 100;
                            $pdf->SetXY($posicionX,$y1);
                            $pdf->Cell(30,5,utf8_decode($rowR[$r][1]),0,0,'R');
                            $pdf->Cell(30,5,number_format($rowR[$r][2], 2, '.', ','),0,0,'R');
                            $pdf->Cell(30,5,number_format($rowR[$r][3], 2, '.', ','),0,0,'R');
                            $pdf->Ln($alto_de_fila); 
                            $valorR = $rowR[$r][2];
                        }
                        $pdf->Ln(5);
                    }
                }
                #****** Totales **********#
                $ValorT = $con->Listar("SELECT SUM(valor) 
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN 
                  gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE
                  (c.clasecuenta = 4 OR c.clasecuenta=8) 
                  AND valor >0 AND dc.comprobante =$id_cnt");
                if (count($ValorT) > 0) {
                    if (empty($ValorT[0][0])) {
                        $ValorT = 0.00;
                    } else {
                        $ValorT = $ValorT[0][0];
                    }
                } else {
                    $ValorT = 0.00;
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(15, 5, 'Total: $ ', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(160, 5, number_format($ValorT, 2, '.', ','), 0, 0, 'L');

                $pdf->Ln(8);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 5, 'Valor en letras: ', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $valorLetras = numtoletras($ValorT);
                $pdf->MultiCell(170, 5, $valorLetras, 0, 'L');
                if ($altY > 220) {
                    $pdf->AddPage();
                    #************Encabezado*************#
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetX(35);
                    $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                    $pdf->Ln(2);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                    $pdf->Ln(6);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                    $pdf->Ln(6);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                    $pdf->Ln(6);
                }
                
                $fechaCompF = $row[$i][2];
                $fecha_div = explode("-", $row[$i][3]);
                $diaSb = $fecha_div[2];
                $mesSb = $fecha_div[1];
                $mesSb = (int) $mesSb;
                $anioSb = $fecha_div[0];
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(60, 13, utf8_decode('Se expide en ' . strtoupper($ciudad_com) . ' a los ' . $diaSb . ' días del mes de ' . $meses[$mesSb] . ' de ' . $anioSb), 0, 0, 'L');
                $pdf->Ln(15); 
                $altY = $pdf->GetY();
                if ($altY > 220) {
                    $pdf->AddPage();
                    #************Encabezado*************#
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetX(35);
                    $pdf->MultiCell(160, 4, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                    $pdf->Ln(2);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode(('NIT:' . $numeroIdent)), 0, 0, 'C');
                    $pdf->Ln(6);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row[$i][1]), 0, 0, 'C');
                    $pdf->Ln(6);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 4, utf8_decode('Fecha:'.$row[$i][2]), 0, 0, 'C');
                    $pdf->Ln(6);
                }
                $pdf->SetFont('Arial', 'B', 8);
                
                #************************FIRMAS****************************#
                $rowf = $con->Listar("SELECT IF(CONCAT_WS(' ',
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
                  WHERE tcp.id_unico = ".$row[$i][10]." 
                  AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC");
                $fr = 0;
                $firmaNom   = array();
                $firmaCarg  = array();
                $firmaTP    = array();
                for ($f = 0; $f < count($rowf); $f++) {
                    if(!empty($rowf[$f][5])){
                        if($row[$i][3] <=$rowf[$f][5]){
                            echo $firmaNom[$fr]  = $rowf[$f][0];
                            if(!empty($rowf[$f][3])){
                                $firmaCarg[$fr]   = $rowf[$f][3];
                            }
                            if(!empty($rowf[$f][6])){
                                $firmaTP[$fr]   = $rowf[$f][6];
                            }
                            $fr++;
                        } 
                     } elseif(!empty($rowTipComp[4]) ) {
                        if($row[$i][3] >= $rowf[$f][4]){
                           echo $firmaNom[$fr]  = $rowf[$f][0];
                            if(!empty($rowf[$f][3])){
                                $firmaCarg[$fr]   = $rowf[$f][3];
                            }
                            if(!empty($rowf[$f][6])){
                                $firmaTP[$fr]   = $rowf[$f][6];
                            }
                            $fr++;
                        }

                     } else {
                            $firmaNom[$fr]  = $rowf[$f][0];
                            if(!empty($rowf[$f][3])){
                                $firmaCarg[$fr]   = $rowf[$f][3];
                            }
                            if(!empty($rowf[$f][6])){
                                $firmaTP[$fr]   = $rowf[$f][6];
                            }
                            $fr++;
                     }
                }
                $numFirmas = $fr;
                if($numFirmas > 3)
                  $numFirmas = 3;
                for($zf = 0; $zf < $numFirmas; $zf++)
                {   
                   $pdf->Cell(60,40,'',1,0,'C');

                }
                $pdf->Ln(24);
                for($zf = 0; $zf < $numFirmas; $zf++)
                {
                    $pdf->Cell(1,0,'',0,0,'L');
                    $pdf->Cell(55,0,'',1,0,'L');
                    $pdf->Cell(4,0,'',0,0,'L');
                }
                $pdf->Ln(2);
                for($zf = 0; $zf < $numFirmas; $zf++)
                {
                    if($firmaNom[$zf]=='' || $firmaNom[$zf]==""){
                        $pdf->Cell(60,5,utf8_decode($firmaNom[$zf]),0,0,'L');
                    } else {
                  $pdf->CellFitScale(60,5,utf8_decode($firmaNom[$zf]),0,0,'L');
                    }
                }
                $pdf->Ln(4);
                for($zf = 0; $zf < $numFirmas; $zf++)
                {
                    if($firmaCarg[$zf]=='' || $firmaCarg[$zf]==""){
                        $pdf->Cell(60,5,utf8_decode($firmaCarg[$zf]),0,0,'L');
                    } else {
                        $pdf->CellFitScale(60,5,utf8_decode($firmaCarg[$zf]),0,0,'L');
                    }

                }
                $pdf->Ln(4);
                for($zf = 0; $zf < $numFirmas; $zf++)
                {
                    if($firmaTP[$zf]=='' || $firmaTP[$zf]==""){
                        $pdf->Cell(60,5,utf8_decode(''),0,0,'L');
                    } else {
                        $pdf->CellFitScale(60,5,utf8_decode('T.P. :'.$firmaTP[$zf]),0,0,'L');
                    }
                }
                if($i == ($datos-1)){
                }else {
                    $pdf->AddPage();
                }
            } else {
                $datos -=1;
            }                    
        }
        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
    break;
    # ** Egresos ** #
    case 4:
        $nombre ="Egreso";
        class PDF extends FPDF {
            function Header() {
            }
            function Footer() {
                global $usuario;
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->Cell(35,10,'Elaborado por: '.utf8_decode($usuario),0,0, 'L');
                $this->Cell(155,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
            }
        }
        $pdf = new PDF('P', 'mm', 'Letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $datos =count($row);
        for ($i = 0; $i < count($row); $i++) {
            $totalValor1 =0;
            # Buscar Id_CNT
            $id_cnt ="";
            $row_idd = $con->Listar("SELECT DISTINCT cn.id_unico, cn.numero, tc.nombre, 
                tc.id_unico, fp.nombre 
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                LEFT JOIN gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico 
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                LEFT JOIN gf_forma_pago fp ON cn.formapago = fp.id_unico 
                WHERE dp.comprobantepptal =".$row[$i][0]." AND tc.clasecontable = 14");
            if(count($row_idd)>0){
                $id_cnt =$row_idd[0][0];
            } else {
                $row_idd = $con->Listar("SELECT DISTINCT cn.id_unico, cn.numero, tc.nombre, 
                    tc.id_unico,fp.nombre 
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico  
                LEFT JOIN gf_forma_pago fp ON cn.formapago = fp.id_unico 
                WHERE tc.comprobante_pptal=".$row[$i][10]." AND cn.numero = ".$row[$i][1]);
                if(count($row_idd)>0){
                    $id_cnt =$row_idd[0][0];
                }
            }
            if(!empty($id_cnt)){
                $usuario    =  $row[$i][11];
                $rowbanco = $con->Listar("SELECT 
                    detComp.id_unico idDetalleComp, 
                    detComp.valor valorDetalle, 
                    te.razonsocial banco, doc.numero, 
                    cuen.naturaleza naturalezaCuenta, cc.nombre, 
                    cuen.clasecuenta  
                    FROM gf_detalle_comprobante detComp 
                    LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta 
                    LEFT JOIN gf_centro_costo cc ON detComp.centrocosto = cc.id_unico
                    LEFT JOIN gf_cuenta_bancaria ctaB ON ctaB.cuenta = cuen.id_unico
                    LEFT JOIN gf_tercero te ON ctaB.banco = te.id_unico
                    LEFT JOIN gf_detalle_comprobante_mov doc ON detComp.id_unico = doc.comprobantecnt
                    WHERE detComp.comprobante = $id_cnt AND cuen.clasecuenta = 11");
                
                
                #********** Encabezado *************#
                if ($ruta_logo != '') {
                    $pdf->Image('../' . $ruta_logo, 10, 5, 25);
                }                
                $pdf->SetFont('Arial', 'B', 12);  
                $pdf->SetX(35);
                $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                $pdf->Ln(2);
                $pdf->SetX(35);
                $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                $pdf->Ln(7);
                $pdf->SetX(35);
                $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                $pdf->Ln(10);
                #*********************************************#
                $pdf->SetFont('Arial', 'B', 10);
                #** Fecha **#
                $pdf->Cell(35, 6, utf8_decode('Fecha: '), 0, 0, 'L');
                $pdf->Cell(155, 6, utf8_decode($row[$i][2]), 0, 0, 'L');
                #** Identificacion **#
                $pdf->Ln(5);
                #** Nombre **#
                $pdf->Cell(35, 6, utf8_decode('A favor de: '), 0, 0, 'L');
                $pdf->Cell(155, 6, utf8_decode($row[$i][8]), 0, 0, 'L');
                #** Identificacion **#
                $pdf->Ln(5);
                $pdf->Cell(35, 6,utf8_decode('CC o Nit: '),0, 0, 'L');
                $pdf->Cell(155, 6,utf8_decode($row[$i][9]),0, 0, 'L');
                $pdf->Ln(5);
                #** Banco Cheque **#
                
                for ($b = 0; $b < count($rowbanco); $b++) {
                    $pdf->Cell(35, 6,utf8_decode('Cheque N°: '),0, 0, 'L');
                    $pdf->Cell(55, 6,utf8_decode($rowbanco[$b][3]),0, 0, 'L');
                    $pdf->Cell(35, 6, utf8_decode('Cheque por valor: '), 0, 0, 'L');
                    if($rowbanco[$b][1]<0){
                        $rowbanco[$b][1] =$rowbanco[$b][1]*-1;
                    }
                    $pdf->Cell(65, 6, number_format($rowbanco[$b][1], 2, '.', ','), 0, 0, 'L');
                    $pdf->Ln(5);
                    $valorLetrasCheque = numtoletras($rowbanco[$b][1]);
                    $pdf->Cell(35, 6, utf8_decode('Por valor de : '), 0, 0, 'L');
                    $pdf->MultiCell(155, 6, utf8_decode($valorLetrasCheque), 0, 'L');
                }   
                #** Descripcion **#
                $pdf->Cell(35, 6, 'Concepto: ', 0, 'L');
                $pdf->Multicell(155, 6, utf8_decode($row[$i][4]), 0, 'L');
                #** Cuenta Por pagar Relacionada **#
                $ord = $con->Listar("SELECT 
                    GROUP_CONCAT(DISTINCT(CONCAT_WS(' ',tc.codigo,'N°:',cn.numero)))
                    FROM gf_comprobante_pptal cn 
                    LEFT JOIN gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN gf_detalle_comprobante_pptal dc ON cn.id_unico= dc.comprobantepptal 
                    LEFT JOIN gf_detalle_comprobante_pptal dce ON dc.id_unico = dce.comprobanteafectado 
                    WHERE dce.comprobantepptal = ".$row[$i][0]);
                if(count($ord)>0){
                    $pdf->Cell(35, 6, 'Afectado: ', 0, 'L');
                    $pdf->Cell(155, 6, utf8_decode($ord[0][0]), 0, 0, 'L');
                    $pdf->Ln(6);
                }
                
                #** Tipo Contrato **#
                $pdf->Cell(35, 6, utf8_decode('Tipo de contrato: '), 0, 0, 'L');
                $pdf->Cell(60, 6, utf8_decode($row[$i][13]), 0, 0, 'L');
                #** Numero Contrato **#
                $pdf->Cell(30, 6, utf8_decode('No de contrato: '), 0, 0, 'L');
                $pdf->Cell(65, 6, utf8_decode($row[$i][12]), 0, 0, 'L');
                $pdf->Ln(6);
                
                #************* Movimiento Presupuestal ********************#
                $pdf->SetFont('Arial', 'B', 10, 'C');
                $rowp = $con->Listar("SELECT DISTINCT
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
                  cn.id_unico=".$row[$i][0]);
                if(count($rowp)>0){
                    $pdf->Cell(190, 5, utf8_decode('VIGENCIA PRESUPUESTAL ' .$row[$i][17]), 1, 0, 'C');
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
                    for ($p = 0; $p < count($rowp); $p++) {
                        $alt = $pdf->GetY();
                        if($alt>240){
                            $pdf->AddPage();
                            #********** Encabezado *************#
                            if ($ruta_logo != '') {
                                $pdf->Image('../' . $ruta_logo,  10, 5, 25);
                            }                
                            $pdf->SetFont('Arial', 'B', 12);  
                            $pdf->SetX(35);
                            $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                            $pdf->Ln(2);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                            $pdf->Ln(7);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                            $pdf->Ln(10);
                            #*********************************************#
                        }
                        if (empty($rowp[$p][2])) {
                            $numComPtalDisponibilidad = $rowp[$p][1];
                            $numComPtalRegistro = $rowp[$p][0];
                        } else {
                            $numComPtalDisponibilidad = $rowp[$p][2];
                            $numComPtalRegistro = $rowp[$p][1];
                        }
                        $nombreCuenta = mb_strtolower($rowp[$p][5] . ' - ' . $rowp[$p][6] . ' ' . $rowp[$p][7]);
                        $nombreCuenta = ucwords($nombreCuenta);
                        $y1 = $pdf->GetY();
                        $x1 = $pdf->GetX();
                        $pdf->Cell(80, 5, ' ', 0, 0, 'L');
                        $pdf->MultiCell(80, 5, utf8_decode($nombreCuenta), 1, 'J');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y1;
                        $px = $x1 + 80;
                        $pdf->SetXY($x1, $y1);
                        $pdf->Cell(25, $h, $numComPtalDisponibilidad, 1, 0, 'L');
                        $pdf->Cell(25, $h, $numComPtalRegistro, 1, 0, 'L');
                        $pdf->Cell(30, $h, $rowp[$p][4], 1, 0, 'L');

                        $xx = $pdf->GetX();
                        $pdf->SetX($xx + 80);
                        $valor = $rowp[$p][3];
                        $pdf->Cell(30, $h, number_format($valor, 2, '.', ','), 1, 0, 'R');
                        $totalValor += $valor;
                        $pdf->Ln($h);
                        
                    }
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(160, 5, 'Total:', 0, 0, 'R'); 
                $pdf->Cell(30, 5, number_format($totalValor, 2, '.', ','), 0, 0, 'R');
                
                #************** Movimiento Contable ******************#
                $pdf->Ln(10);
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
                $rowc = $con->Listar("SELECT detComp.id_unico idDetalleComp, 
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
                  WHERE detComp.comprobante = $id_cnt");
                $totalDebito    = 0;
                $totalCredito   = 0;
                $totalCheque    = 0;
                for ($c = 0; $c < count($rowc); $c++) {
                    $alt = $pdf->GetY();
                    if($alt>240){
                        $pdf->AddPage();
                        #********** Encabezado *************#
                        if ($ruta_logo != '') {
                            $pdf->Image('../' . $ruta_logo,  10, 5, 25);
                        }                
                        $pdf->SetFont('Arial', 'B', 12);  
                        $pdf->SetX(35);
                        $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                        $pdf->Ln(2);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                        $pdf->Ln(7);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                        $pdf->Ln(10);
                        #*********************************************#
                    }
                    $debito     = 0;
                    $credito    = 0;
                    $nombCuen   = mb_strtolower($rowc[$c][2]);
                    $nombCuen   = ucwords($nombCuen);
                    $centroCost = mb_strtolower($rowc[$c][5], 'utf-8');
                    $centroCost = ucwords($centroCost);
                    $cod        = $rowc[$c][3];
                    if ($rowc[$c][4] == 1) {
                        if ($rowc[$c][1] < 0) {
                            $credito = substr($rowc[$c][1], 1);
                        } else {
                            $debito = $rowc[$c][1];
                        }
                    } elseif ($rowc[$c][4] == 2) {
                        if ($rowc[$c][1] < 0) {
                            $debito = substr($rowc[$c][1], 1);
                        } else {
                            $credito = $rowc[$c][1];
                        }
                    }
                    if ($rowc[$c][6] == 11) {
                        $totalCheque += $rowc[$c][1];
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
                    $pdf->SetXY($posicionX, $yinicio);
                    $y3 = $pdf->GetY();
                    $x3 = $pdf->GetX();
                    $pdf->Multicell(55, 5, utf8_decode($centroCost), 0, 'L');
                    $y4 = $pdf->GetY();
                    $alto_de_fila1 = $y4 - $y3;
                    $posicionX = $x3 + 55;
                    $pdf->SetXY($posicionX, $yinicio);
                    $pdf->Cell(25, 5, number_format($debito, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(25, 5, number_format($credito, 2, '.', ','), 0, 0, 'R');
                    $max = max($alto_de_fila, $alto_de_fila1);
                    $pdf->Ln($max);
                    $totalDebito += $debito;
                    $totalCredito += $credito;
                }
                $pdf->Ln(5);
                $pdf->Cell(110, 15, '', 1, 0, 'R');
                $pdf->Cell(40, 15, '', 1, 0, 'R');
                $pdf->Cell(40, 15, '', 1, 0, 'R');
                $pdf->SetX(-200);
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(110, 5, utf8_decode('Débitos'), 0, 0, 'R');
                $pdf->Cell(40, 5, utf8_decode('Créditos'), 0, 0, 'R');
                $pdf->Cell(40, 5, utf8_decode('Valor Cheque'), 0, 0, 'R');
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
                $pdf->cellfitscale(110, 5, number_format($totalDebito, 2, '.', ','), 0, 0, 'R');
                $pdf->cellfitscale(40, 5, number_format($totalCredito, 2, '.', ','), 0, 0, 'R');
                $pdf->cellfitscale(40, 5, number_format($totalCheque, 2, '.', ','), 0, 0, 'R'); 
                $pdf->Ln(10);
                
                #************************Retenciones*********************#
                #* Validar Tipo Comprobante donde se aplica Retencion
                $ret =$con->Listar("SELECT retencion FROM gf_tipo_comprobante WHERE id_unico =".$row_idd[0][3]);
                $retencion = $ret[0][0];
                #Si Aplica En Egreso
                $rowr ="";
                if($retencion==1){
                    $rowr = $con->Listar("SELECT tpr.nombre,tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
                    FROM gf_retencion rt 
                    LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
                    LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
                    WHERE cnt.id_unico = $id_cnt"); 
                #Si Aplica En CXP
                } else {
                    $afec = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.id_unico) 
                        FROM gf_comprobante_cnt cn 
                        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                        LEFT JOIN gf_comprobante_pptal cp ON tc.comprobante_pptal = cp.tipocomprobante AND cn.numero = cp.numero 
                        LEFT JOIN gf_detalle_comprobante_pptal dc ON cp.id_unico = dc.comprobantepptal 
                        LEFT JOIN gf_detalle_comprobante_pptal dce ON dce.comprobanteafectado = dc.id_unico 
                        WHERE dce.comprobantepptal =".$row[$i][0]);
                    if(count($afec)>0){
                        $rowr = $con->Listar("SELECT tpr.nombre,tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
                        FROM gf_retencion rt 
                        LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
                        LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
                        WHERE cnt.id_unico IN (".$afec[0][0].")"); 
                    }
                }
               
                if(count($rowr)>0){
                    $alt = $pdf->GetY();
                    if($alt>240){
                        $pdf->AddPage();
                        #********** Encabezado *************#
                        if ($ruta_logo != '') {
                            $pdf->Image('../' . $ruta_logo,  10, 5, 25);
                        }                
                        $pdf->SetFont('Arial', 'B', 12);  
                        $pdf->SetX(35);
                        $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                        $pdf->Ln(2);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                        $pdf->Ln(7);
                        $pdf->SetX(35);
                        $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                        $pdf->Ln(10);
                        #*********************************************#
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
                    for ($r = 0; $r < count($rowr); $r++) {
                        $alt = $pdf->GetY();
                        if($alt>250){
                            $pdf->AddPage();
                            #********** Encabezado *************#
                            if ($ruta_logo != '') {
                                $pdf->Image('../' . $ruta_logo,  10, 5, 25);
                            }                
                            $pdf->SetFont('Arial', 'B', 12);  
                            $pdf->SetX(35);
                            $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                            $pdf->Ln(2);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                            $pdf->Ln(7);
                            $pdf->SetX(35);
                            $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                            $pdf->Ln(10);
                            #*********************************************#
                        }
                        $tipo = utf8_decode($rowr[$r][0]);
                        $pdf->SetFont('Arial', '', 10);
                        $y1 = $pdf->GetY();
                        $x1 = $pdf->GetX();
                        $pdf->Multicell(100, 5, ($tipo), 0, 'L');
                        $y2 = $pdf->GetY();
                        $alto_de_fila = $y2 - $y1;
                        $posicionX = $x1 + 100;
                        $pdf->SetXY($posicionX, $y1);
                        $pdf->Cell(30, 5, utf8_decode($rowr[$r][1]), 0, 0, 'R');
                        $pdf->Cell(30, 5, number_format($rowr[$r][2], 2, '.', ','), 0, 0, 'R');
                        $pdf->Cell(30, 5, number_format($rowr[$r][3], 2, '.', ','), 0, 0, 'R');
                        $pdf->Ln($alto_de_fila);
                    }
                    $pdf->Ln(5);
                }
                if($alt>240){
                    $pdf->AddPage();
                    #********** Encabezado *************#
                    if ($ruta_logo != '') {
                        $pdf->Image('../' . $ruta_logo,  10, 5, 25);
                    }                
                    $pdf->SetFont('Arial', 'B', 12);  
                    $pdf->SetX(35);
                    $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                    $pdf->Ln(2);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                    $pdf->Ln(7);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                    $pdf->Ln(10);
                    #*********************************************#
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->cellfitscale(190, 5, utf8_decode('Valor a girar'), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(190, 5, utf8_decode($totalChequeLetras), 0, 'L');
                $pdf->Ln(5);
                
                #*******************Firmas*******************#
                $rowf = $con->Listar("SELECT IF(CONCAT_WS(' ',
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
                  WHERE tcp.id_unico = ".$row[$i][10]." 
                  AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC");
                $ff = 0;
                $pdf->SetFont('Arial', 'B', 10);
                for ($f = 0; $f < count($rowf); $f++) {
                    if(!empty($rowf[$f][5])){
                        if($row[$i][3] <=$rowf[$f][5]){
                            $firmaNom[$ff]  = $rowf[$f][0];
                            $firmaCarg[$ff] = $rowf[$f][3];
                            $firmaTP[$ff]   = $rowf[$f][6];
                            $ff++;
                        } 
                    } elseif(!empty($rowTipComp[4]) ) {
                            if($row[$i][3] >= $rowf[$f][4]){
                                $firmaNom[$ff] = $rowf[$f][0];
                                $firmaCarg[$ff] = $rowf[$f][3];
                                $firmaTP[$ff] = $rowf[$f][6];
                                $ff++;
                            }

                    } else {
                        $firmaNom[$ff] = $rowf[$f][0];
                        $firmaCarg[$ff] = $rowf[$f][3];
                        $firmaTP[$ff] = $rowf[$f][6];
                        $ff++;
                    }
                }
                $alt = $pdf->GetY();
                if($alt>220){
                    $pdf->AddPage();
                    #********** Encabezado *************#
                    if ($ruta_logo != '') {
                        $pdf->Image('../' . $ruta_logo,  10, 5, 25);
                    }                
                    $pdf->SetFont('Arial', 'B', 12);  
                    $pdf->SetX(35);
                    $pdf->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
                    $pdf->Ln(2);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 5, utf8_decode(( $numeroIdent)), 0, 0, 'C');
                    $pdf->Ln(7);
                    $pdf->SetX(35);
                    $pdf->Cell(160, 5, utf8_decode(mb_strtoupper($row_idd[0][2]) . ' ' . 'No: ' . $row_idd[0][1]), 0, 0, 'C');
                    $pdf->Ln(10);
                    #*********************************************#
                }
                $firmaNom[$ff]  = 'FIRMA BENEFICIARIO';
                $firmaNum[$ff]  = 'C.C. ó NIT';
                $firmaCarg[$ff] = 'C.C. ó NIT';

                $numFirmas = $ff;

                if($numFirmas > 3)
                  $numFirmas = 3;

                for($z = 0; $z <= $numFirmas; $z++)
                {
                  $pdf->Cell(60,40,'',1,0,'C');

                }

                $pdf->Ln(24);
                for($z = 0; $z <= $numFirmas; $z++)
                {
                  $pdf->Cell(1,0,'',0,0,'L');
                  $pdf->Cell(55,0,'',1,0,'L');
                  $pdf->Cell(4,0,'',0,0,'L');
                }
                $pdf->Ln(2);
                for($z = 0; $z <=$numFirmas; $z++)
                {
                    if($firmaNom[$z]=='' || $firmaNom[$z]==""){
                        $pdf->Cell(60,5,utf8_decode($firmaNom[$z]),0,0,'L');
                    } else {
                        $pdf->CellFitScale(60,5,utf8_decode($firmaNom[$z]),0,0,'L');
                    }
                }
                $pdf->Ln(4);
                for($z = 0; $z <= $numFirmas; $z++)
                {
                    if($firmaCarg[$z]=='' || $firmaCarg[$z]==""){
                        $pdf->Cell(60,5,utf8_decode($firmaCarg[$z]),0,0,'L');
                    } else {
                        $pdf->CellFitScale(60,5,utf8_decode($firmaCarg[$z]),0,0,'L');
                    }
                }
                $pdf->Ln(4);
                for($z = 0; $z < $numFirmas; $z++)
                {
                    if($firmaTP[$z]=='' || $firmaTP[$z]==""){
                        $pdf->Cell(60,5,utf8_decode(''),0,0,'L');
                    } else {
                        $pdf->CellFitScale(60,5,utf8_decode('T.P. :'.$firmaTP[$z]),0,0,'L');
                    }

                }
                #********************************************************#
                if($i == ($datos-1)){
                }else {
                    $pdf->AddPage();
                }
            } else {
                $datos -=1;
            }
            
        }
        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0, 'Informe_' . $nombre . '.pdf', 0);
    break;
    
endswitch;

