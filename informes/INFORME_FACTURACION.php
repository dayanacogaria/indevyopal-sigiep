<?php

require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];

//Datos de compañia
$rowC = $con->Listar
        ("
SELECT ter.id_unico,
    ter.razonsocial,
    UPPER(ti.nombre),
    ter.numeroidentificacion,
    dir.direccion,
    tel.valor,
    ter.ruta_logo,
    IF(CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos)
    IS NULL OR CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos) = '',
    (ter.razonsocial),
    CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos)) AS NOMBRE,
    reg.nombre
FROM gf_tercero ter
    LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
    LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
    LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
    LEFT JOIN   gf_tipo_regimen reg ON ter.tiporegimen = reg.id_unico
WHERE ter.id_unico = $compania
");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][7];
$telefonoTer = $rowC[0][5];
$ruta_logo = $rowC[0][6];
$regimen = strtolower($rowC[0][8]);


$fechaI = fechaC($_REQUEST['fechaInicial']);
$fechaF = fechaC($_REQUEST['fechaFinal']);

$tipoF = $_REQUEST['stltipoinf'];
if(empty($_REQUEST['stlfacI'])){
    $facturaI = 0;
} else {
    $facturaI = $_REQUEST['stlfacI'];
}
if(empty($_REQUEST['stlfacI'])){
    $facturaF = 0;
} else {
    $facturaF = $_REQUEST['stlfacF'];
}

$betweenfac = "";
if ($facturaI > 0 && $facturaF > 0){
    $betweenfac = "AND fac.id_unico BETWEEN $facturaI AND $facturaF";
}
$tipoF = $_REQUEST['stltipoinf'];
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
require'../fpdf/fpdf.php';
ob_start();
class PDF extends FPDF {
    function Header() {                          
    }
    function Footer() {
    }
}            
switch ($tipoF){
    #Consulta Facturas 
    case 0:            
        $tipoF = $_REQUEST['tipofac'];
        #DatosI
        $sqlfac1 = "SELECT 
        fac.id_unico,
         fac.numero_factura,
        DATE_FORMAT(fac.fecha_factura, '%d/%m/%Y'), 
        DATE_FORMAT(fac.fecha_vencimiento, '%d/%m/%Y'),
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',ter.razonsocial,
        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))) as tercero,
        ter.numeroidentificacion            
        FROM gp_factura fac
        LEFT JOIN gp_tipo_factura tpf ON fac.tipofactura = tpf.id_unico
        LEFT JOIN gf_tercero ter ON fac.tercero = ter.id_unico
        LEFT JOIN gf_direccion dir ON ter.id_unico = dir.tercero
        LEFT JOIN gf_telefono tel ON ter.id_unico = tel.tercero
        WHERE fac.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
        AND tpf.id_unico = $tipoF";
        $resc1 = $mysqli->query($sqlfac1);
        $datosI = $resc1->fetch_all(MYSQLI_NUM);
        #DatosF
        $sqlfac2 = "SELECT 
        fac.id_unico,
         fac.numero_factura,
        DATE_FORMAT(fac.fecha_factura, '%d/%m/%Y'), 
        DATE_FORMAT(fac.fecha_vencimiento, '%d/%m/%Y'),
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',ter.razonsocial,
        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))) as tercero,
        ter.numeroidentificacion            
        FROM gp_factura fac
        LEFT JOIN gp_tipo_factura tpf ON fac.tipofactura = tpf.id_unico
        LEFT JOIN gf_tercero ter ON fac.tercero = ter.id_unico
        LEFT JOIN gf_direccion dir ON ter.id_unico = dir.tercero
        LEFT JOIN gf_telefono tel ON ter.id_unico = tel.tercero
        WHERE fac.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
        AND tpf.id_unico = $tipoF
        ORDER BY id_unico DESC";
        $resc2 = $mysqli->query($sqlfac2);
        $datosF = $resc2->fetch_all(MYSQLI_NUM);
        $datos = array("datosI" => $datosI, "datosF" => $datosF);
        echo json_encode($datos);
    break;
    #Mercaplaza
    case 2:  
    IF($_REQUEST['chkform']==0)  {
        $pdf=new PDF('P','mm','Legal');
        $nb = $pdf->AliasNbPages();   
        $sqlfac = "SELECT 
        fac.id_unico, fac.fecha_factura,
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',ter.razonsocial,
        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))) as tercero,
        ter.numeroidentificacion, tel.valor, dir.direccion,
        ter.id_unico, fac.numero_factura, DATE_FORMAT(fac.fecha_factura, '%d/%m/%Y'), DATE_FORMAT(fac.fecha_vencimiento, '%d/%m/%Y'), fac.id_espacio_habitable
        FROM gp_factura fac
        LEFT JOIN gp_tipo_factura tpf ON fac.tipofactura = tpf.id_unico
        LEFT JOIN gf_tercero ter ON fac.tercero = ter.id_unico
        LEFT JOIN gf_direccion dir ON ter.id_unico = dir.tercero
        LEFT JOIN gf_telefono tel ON ter.id_unico = tel.tercero            
        WHERE fac.fecha_factura BETWEEN '$fechaI' AND '$fechaF' $betweenfac
        AND tpf.id_unico = $tipoF";            
        $resc = $mysqli->query($sqlfac);
        while ($rowfc = mysqli_fetch_row($resc)) {  
            $factura = $rowfc[0]; 
            $tercero = $rowfc[6];
            $espacio = $rowfc[10];
            $m = substr($rowfc[8],3,2);
            if ($m < 10){
                $m = substr($m,1,1);
            }
            $nombremes = $meses[$m];
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 9);
            /*********Factura N1*********/
            $pdf->SetXY(31, 27);
            $pdf->Cell(60, 5, utf8_decode($rowfc[7]), 0, 0, 'L'); #Numero factura
            $pdf->SetXY(62, 27);
            $pdf->Cell(60, 5, utf8_decode($nombremes), 0, 0, 'L'); #Mes facturado
            $pdf->SetXY(97, 27);
            $pdf->Cell(60, 5, utf8_decode($rowfc[8]), 0, 0, 'L'); #Fechafactura
            $pdf->SetXY(139, 27);
            $pdf->Cell(60, 5, utf8_decode($rowfc[9]), 0, 0, 'L'); #FechaVencimiento
            $pdf->SetXY(25, 35);
            $pdf->Cell(60, 5, utf8_decode($rowfc[2]), 0, 0, 'L'); #Tercero
            $pdf->SetXY(114, 35);
            $pdf->Cell(20, 5, utf8_decode($rowfc[3]), 0, 0, 'L'); #CC
            $pdf->SetXY(169, 35);
            ////$rowfc[4] = 3003475833;
            $pdf->Cell(25, 5, utf8_decode($rowfc[4]), 0, 0, 'L'); #Telefono
            $pdf->SetXY(25, 43);
            //$rowfc[5] = "Cll 14 # 9-18 Duitama";
            $pdf->Cell(60, 5, utf8_decode($rowfc[5]), 0, 0, 'L'); #Dirección
            $sqlsph = "SELECT sph.codigo
                    FROM gph_espacio_habitable_factura shfac
                    LEFT JOIN gh_espacios_habitables sph ON shfac.espacio_habitable = sph.id_unico
                    WHERE shfac.factura = $factura";
            $ressph = $mysqli->query($sqlsph);
            $habitables = "";
            while ($rowh = mysqli_fetch_row($ressph)) {
                $habitables .= $rowh[0].',';
            }
            $habitables = substr ($habitables, 0, strlen($cad) - 1);
            $pdf->SetXY(90, 43);
            $pdf->CellFitScale(60, 5, utf8_decode($habitables), 0, 0, 'L'); #Puesto local
            $sqldtf = "SELECT 
            con.nombre,
            SUM(dtf.valor), SUM(dtf.iva), SUM(dtf.valor_total_ajustado)
            FROM gp_detalle_factura dtf
            LEFT JOIN gp_concepto con ON dtf.concepto_tarifa = con.id_unico
            WHERE factura = $factura
            GROUP BY dtf.concepto_tarifa";            
            $rescdf = $mysqli->query($sqldtf);
            $x1 = 55;
            $x2 = 55;
            $x3 = 55;
            $saldofactura = 0;
            while ($rowdf = mysqli_fetch_row($rescdf)) {
                $pdf->SetXY(25, $x1);
                $pdf->Cell(70, 5, utf8_decode($rowdf[0]), 0, 0, 'L');
                $pdf->SetXY(148, $x2);
                $pdf->Cell(18, 5, utf8_decode(number_format($rowdf[1], 2, '.', ',')), 0, 0, 'R');
                $pdf->SetXY(181, $x3);
                $pdf->Cell(18, 5, utf8_decode(number_format($rowdf[3], 2, '.', ',')), 0, 0, 'R');
                $x1 += 5;
                $x2 += 5;
                $x3 += 5;
                $saldofactura += $rowdf[3];
            }
            $facturaant = 0;
            $pago = 0;
            $sqldf1 = "SELECT
                    dtf.id_unico,
                    dtf.valor_total_ajustado                        
                    FROM gp_factura fac
                    LEFT JOIN gp_detalle_factura dtf ON fac.id_unico = dtf.factura
                    WHERE fac.fecha_factura < '$fechaI'
                    AND fac.tercero = $tercero AND tipofactura  = $tipoF";            
            $rescdf = $mysqli->query($sqldf1);
            while ($rowdf = mysqli_fetch_row($rescdf)) {
                $facturaant += $rowdf[1];
                $sqldpago = "SELECT SUM(dtp.valor+dtp.iva+dtp.impoconsumo+dtp.ajuste_peso) 
                FROM gp_detalle_pago dtp
                LEFT JOIN gp_pago p ON dtp.pago = p.id_unico 
                WHERE dtp.detalle_factura = $rowdf[0] AND p.fecha_pago <'".$rowfc[1]."'";
                $rescpago = $mysqli->query($sqldpago);
                while ($rowpg = mysqli_fetch_row($rescpago)) {
                    $pago += $rowpg[0];
                }

            }
            $deuda = $facturaant - $pago;
            $total = $saldofactura + $deuda;
            $pdf->SetXY(25, $x1);
            $pdf->Cell(70, 5, utf8_decode("Deuda anterior"), 0, 0, 'L');
            $pdf->SetXY(181, $x3);
            $pdf->Cell(18, 5, utf8_decode(number_format($deuda, 2, '.', ',')), 0, 0, 'R');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(181, 88);
            $pdf->Cell(18, 5, utf8_decode(number_format($total, 2, '.', ',')), 0, 0, 'R');
            $pdf->SetFont('Arial', '', 9);

            /***********Factura2**************/                
            $pdf->SetXY(28, 139);
            $pdf->Cell(60, 5, utf8_decode($rowfc[7]), 0, 0, 'L'); #Numero factura
            $pdf->SetXY(62, 139);
            $pdf->Cell(60, 5, utf8_decode($nombremes), 0, 0, 'L'); #Mes facturado
            $pdf->SetXY(97, 140);
            $pdf->Cell(60, 5, utf8_decode($rowfc[8]), 0, 0, 'L'); #Fechafactura
            $pdf->SetXY(138, 140);
            $pdf->Cell(60, 5, utf8_decode($rowfc[9]), 0, 0, 'L'); #FechaVencimiento
            $pdf->SetXY(25, 148);
            $pdf->Cell(60, 5, utf8_decode($rowfc[2]), 0, 0, 'L'); #Tercero
            $pdf->SetXY(114, 148);
            $pdf->Cell(20, 5, utf8_decode($rowfc[3]), 0, 0, 'L'); #CC
            $pdf->SetXY(169, 148);
            //$rowfc[4] = 3003475833;
            $pdf->Cell(25, 5, utf8_decode($rowfc[4]), 0, 0, 'L'); #Teléfono
            $pdf->SetXY(25, 156);
            //$rowfc[5] = "Cll 14 # 9-18 Duitama";
            $pdf->Cell(60, 5, utf8_decode($rowfc[5]), 0, 0, 'L'); #Dirección
            $pdf->SetXY(90, 156);
            $pdf->CellFitScale(60, 5, utf8_decode($habitables), 0, 0, 'L'); #Puesto local
            $sqldtf = "SELECT 
            con.nombre,
            SUM(dtf.valor), SUM(dtf.iva), SUM(dtf.valor_total_ajustado)
            FROM gp_detalle_factura dtf
            LEFT JOIN gp_concepto con ON dtf.concepto_tarifa = con.id_unico
            WHERE factura = $factura
            GROUP BY dtf.concepto_tarifa";            
            $rescdf = $mysqli->query($sqldtf);
            $x1 = 168;
            $x2 = 168;
            $x3 = 168;
            $saldofactura = 0;
            while ($rowdf = mysqli_fetch_row($rescdf)) {
                $pdf->SetXY(25, $x1);
                $pdf->Cell(70, 5, utf8_decode($rowdf[0]), 0, 0, 'L');
                $pdf->SetXY(148, $x2);
                $pdf->Cell(18, 5, utf8_decode(number_format($rowdf[1], 2, '.', ',')), 0, 0, 'R');
                $pdf->SetXY(181, $x3);
                $pdf->Cell(18, 5, utf8_decode(number_format($rowdf[3], 2, '.', ',')), 0, 0, 'R');
                $x1 += 5;
                $x2 += 5;
                $x3 += 5;
                $saldofactura += $rowdf[3];
            }
            $facturaant = 0;
            $pago = 0;
            $sqldf1 = "SELECT
                    dtf.id_unico,
                    dtf.valor_total_ajustado                        
                    FROM gp_factura fac
                    LEFT JOIN gp_detalle_factura dtf ON fac.id_unico = dtf.factura
                    WHERE fac.fecha_factura < '$fechaI'
                    AND fac.tercero = $tercero AND tipofactura  = $tipoF";            
            $rescdf = $mysqli->query($sqldf1);
            while ($rowdf = mysqli_fetch_row($rescdf)) {
                $facturaant += $rowdf[1];
                $sqldpago = "SELECT SUM(dtp.valor+dtp.iva+dtp.impoconsumo+dtp.ajuste_peso) 
                FROM gp_detalle_pago dtp
                LEFT JOIN gp_pago p ON dtp.pago = p.id_unico 
                WHERE dtp.detalle_factura = $rowdf[0] AND p.fecha_pago < '".$rowfc[1]."'";
                $rescpago = $mysqli->query($sqldpago);
                while ($rowpg = mysqli_fetch_row($rescpago)) {
                    $pago += $rowpg[0];
                }

            }
            $deuda = $facturaant - $pago;
            $total = $saldofactura + $deuda;
            $pdf->SetXY(25, $x1);
            $pdf->Cell(70, 5, utf8_decode("Deuda anterior"), 0, 0, 'L');
            $pdf->SetXY(181, $x3);
            $pdf->Cell(18, 5, utf8_decode(number_format($deuda, 2, '.', ',')), 0, 0, 'R');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(181, 201);
            $pdf->Cell(18, 5, utf8_decode(number_format($total, 2, '.', ',')), 0, 0, 'R');
            $pdf->SetFont('Arial', '', 9);

            /************Footer1**************/
            $pdf->SetXY(29, 255);
            $pdf->Cell(60, 5, utf8_decode($rowfc[7]), 0, 0, 'L'); #Numero factura
            $pdf->SetXY(69, 246);
            $pdf->Cell(60, 5, utf8_decode($rowfc[2]), 0, 0, 'L'); #Tercero
            $pdf->SetXY(183, 246);
            $pdf->Cell(60, 5, utf8_decode($rowfc[3]), 0, 0, 'L'); #CC
            $pdf->SetXY(29, 263);
            $pdf->Cell(60, 5, utf8_decode($nombremes), 0, 0, 'L'); #Mes facturado
            $pdf->SetXY(114, 265);
            $pdf->Cell(60, 5, utf8_decode($rowfc[9]), 0, 0, 'L'); #Pague hasta
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(181, 265);
            $pdf->Cell(18, 5, utf8_decode(number_format($total, 2, '.', ',')), 0, 0, 'R'); #Neto a pagar
            $pdf->SetFont('Arial', '', 9);

            /************Footer2**************/
            $pdf->SetXY(29, 308);
            $pdf->Cell(60, 5, utf8_decode($rowfc[7]), 0, 0, 'L'); #Numero factura
            $pdf->SetXY(69, 294);
            $pdf->Cell(60, 5, utf8_decode($rowfc[2]), 0, 0, 'L'); #Tercero
            $pdf->SetXY(183, 294);
            $pdf->Cell(60, 5, utf8_decode($rowfc[3]), 0, 0, 'L'); #CC
            $pdf->SetXY(29, 318);
            $pdf->Cell(60, 5, utf8_decode($nombremes), 0, 0, 'L'); #Mes facturado
            $pdf->SetXY(114, 318);
            $pdf->Cell(60, 5, utf8_decode($rowfc[9]), 0, 0, 'L'); #Pague hasta
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(181, 318);
            $pdf->Cell(18, 5, utf8_decode(number_format($total, 2, '.', ',')), 0, 0, 'R'); #Neto a pagar
            $pdf->SetFont('Arial', '', 9);
            //break;
        }

    } ELSE { 
        $pdf=new PDF('P','mm','A4');
        $nb = $pdf->AliasNbPages();   
        $sqlfac = "SELECT 
        fac.id_unico, fac.fecha_factura,
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',ter.razonsocial,
        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))) as tercero,
        ter.numeroidentificacion, tel.valor, dir.direccion,
        ter.id_unico, fac.numero_factura, DATE_FORMAT(fac.fecha_factura, '%d/%m/%Y'), DATE_FORMAT(fac.fecha_vencimiento, '%d/%m/%Y'), fac.id_espacio_habitable
        FROM gp_factura fac
        LEFT JOIN gp_tipo_factura tpf ON fac.tipofactura = tpf.id_unico
        LEFT JOIN gf_tercero ter ON fac.tercero = ter.id_unico
        LEFT JOIN gf_direccion dir ON ter.id_unico = dir.tercero
        LEFT JOIN gf_telefono tel ON ter.id_unico = tel.tercero            
        WHERE fac.fecha_factura BETWEEN '$fechaI' AND '$fechaF' $betweenfac
        AND tpf.id_unico = $tipoF";            
        $resc = $mysqli->query($sqlfac);
        while ($rowfc = mysqli_fetch_row($resc)) {  
            $factura = $rowfc[0]; 
            $tercero = $rowfc[6];
            $espacio = $rowfc[10];
            $m = substr($rowfc[8],3,2);
            if ($m < 10){
                $m = substr($m,1,1);
            }
            $nombremes = $meses[$m];
            $pdf->AddPage();
             $sqlsph = "SELECT sph.codigo
                    FROM gph_espacio_habitable_factura shfac
                    LEFT JOIN gh_espacios_habitables sph ON shfac.espacio_habitable = sph.id_unico
                    WHERE shfac.factura = $factura";
            $ressph = $mysqli->query($sqlsph);
            $habitables = "";
            while ($rowh = mysqli_fetch_row($ressph)) {
                $habitables .= $rowh[0].',';
            }
            $habitables = substr ($habitables, 0, strlen($cad) - 1);
            
            $yl = 5;
            for ($r = 0; $r < 2; $r++) {
                if($r==0){
                    if(!empty($ruta_logo)){
                        $pdf->Image('../' . $ruta_logo, 10, $yl, 25);
                    }
                } else {
                    if(!empty($ruta_logo)){
                        $pdf->Image('../' . $ruta_logo, 10, $yl-5, 25);
                    }
                }
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetX(40);
                $rsocial = strtolower($razonsocial);
                $pdf->MultiCell(150,7, utf8_decode(ucwords($rsocial)), 0, 'C'); #Razon social
                $pdf->SetX(40);
                $pdf->Cell(150, 7, utf8_decode($nombreIdent . ': ' . $numeroIdent), 0, 0, 'C');#nombre identificación, tipo identificaión
                $pdf->Ln(15);
                #********* FIN **************/
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                #********* Encabezado *************/
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(15, 4, utf8_decode('Factura:'),0,'L'); #factura
                $pdf->SetXY($x, $y+4);
                $pdf->Cell(15, 4, utf8_decode('  N° PM'),0,'L'); #factura
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY($x + 15, $y+2);
                $pdf->MultiCell(20, 6, utf8_decode($rowfc[7]),0,'L'); #factura
                
                $pdf->SetXY($x + 40, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 4, utf8_decode('Mes'), 0, 'L'); #mes facturado
                $pdf->SetXY($x + 40, $y+4);
                $pdf->Cell(15, 4, utf8_decode('Facturado: '), 0, 'L'); #mes facturado
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY($x + 60, $y+2);
                $pdf->MultiCell(20, 6, utf8_decode($nombremes), 0, 'L'); #mes facturado
                
                $pdf->SetXY($x + 80, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 4, utf8_decode('Fecha de '), 0, 'L'); #mes facturado
                $pdf->SetXY($x + 80, $y+4);
                $pdf->Cell(15, 4, utf8_decode('Expedición: '), 0, 'L'); #mes facturado
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY($x + 100, $y+2);
                $pdf->MultiCell(20, 6, utf8_decode($rowfc[8]), 0, 'L'); #mes facturado
                
                
                $pdf->SetXY($x + 120, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 4, utf8_decode('Fecha de '), 0, 'L'); #mes facturado
                $pdf->SetXY($x + 120, $y+4);
                $pdf->Cell(15, 4, utf8_decode('Vencimiento: '), 0, 'L'); #mes facturado
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY($x + 140, $y+2);
                $pdf->MultiCell(20, 6, utf8_decode($rowfc[9]), 0, 'L'); #mes facturado
                
                $pdf->SetXY($x + 160, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 6, utf8_decode('Contrato N° '), 0, 'L'); #contratoN°
                $pdf->SetXY($x, $y);
                $pdf->Cell(40, 8, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(40, 8, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(40, 8, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(40, 8, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(30, 8, utf8_decode(''), 1,0, 'L');
                $pdf->Ln(10);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(20, 4, utf8_decode('Arrendatario:'),0,'L'); 
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(100, 4, utf8_decode($rowfc[2]),0,'L'); 
                $pdf->SetXY($x + 100, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 4, utf8_decode('C.C ó Nit:'),0,'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(30, 4, utf8_decode($rowfc[3]),0,'L'); 
                $pdf->SetXY($x + 145, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 4, utf8_decode('Celular:'),0,'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(30, 4, utf8_decode($rowfc[4]),0,'L'); 
                $pdf->SetXY($x, $y-2);
                $pdf->Cell(100, 6, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(45, 6, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(45, 6, utf8_decode(''), 1,0, 'L');
                $pdf->Ln(8);
                
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(20, 4, utf8_decode('Dirección:'),0,'L'); 
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(80, 4, utf8_decode($rowfc[5]),0,'L'); 
                $pdf->SetXY($x + 80, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(25, 4, utf8_decode('Puesto/Local:'),0,'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(40, 4, utf8_decode($habitables),0,'L'); 
                $pdf->SetXY($x + 145, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(15, 4, utf8_decode('Área:'),0,'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(30, 4, utf8_decode(''),0,'L'); 
                $pdf->SetXY($x, $y-2);
                $pdf->Cell(80, 6, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(65, 6, utf8_decode(''), 1,0, 'L');
                $pdf->Cell(45, 6, utf8_decode(''), 1,0, 'L');
                $pdf->Ln(6);
                

               $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(120, 5, utf8_decode('DETALLE'), 1,0, 'C'); #detalle
                $pdf->Cell(35, 5, utf8_decode('VALOR'), 1,0, 'C'); #valor
                $pdf->Cell(35, 5, utf8_decode('TOTAL'), 1,0, 'C'); #total
                $pdf->Ln(5);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Cell(190, 50, utf8_decode(''), 1, 0, 'L'); #Container conceptos
                $pdf->Ln(1);
                $sqldtf = "SELECT 
                con.nombre,
                SUM(dtf.valor), SUM(dtf.iva), SUM(dtf.valor_total_ajustado)
                FROM gp_detalle_factura dtf
                LEFT JOIN gp_concepto con ON dtf.concepto_tarifa = con.id_unico
                WHERE factura = $factura
                GROUP BY dtf.concepto_tarifa";
                $rescdf = $mysqli->query($sqldtf);
                $saldofactura = 0;
                $pdf->SetFont('Arial', '', 9);
                while ($rowdf = mysqli_fetch_row($rescdf)) {
                    $pdf->Cell(120, 5, utf8_decode($rowdf[0]), 0, 0, 'L'); #Detalle
                    $pdf->Cell(35, 5, utf8_decode(number_format($rowdf[1], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Cell(35, 5, utf8_decode(number_format($rowdf[3], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Ln(5);
                    $saldofactura += $rowdf[3];
                }
                $facturaant = 0;
                $pago = 0;
                $sqldf1 = "SELECT
                        dtf.id_unico,
                        dtf.valor_total_ajustado                        
                        FROM gp_factura fac
                        LEFT JOIN gp_detalle_factura dtf ON fac.id_unico = dtf.factura
                        WHERE fac.fecha_factura < '$fechaI'
                        AND fac.tercero = $tercero AND tipofactura  = $tipoF";
                $rescdf = $mysqli->query($sqldf1);
                while ($rowdf = mysqli_fetch_row($rescdf)) {
                    $facturaant += $rowdf[1];
                    $sqldpago = "SELECT SUM(dtp.valor+dtp.iva+dtp.impoconsumo+dtp.ajuste_peso) 
                    FROM gp_detalle_pago dtp
                    LEFT JOIN gp_pago p ON dtp.pago = p.id_unico 
                    WHERE dtp.detalle_factura = $rowdf[0] and p.fecha_pago <'".$rowfc[1]."'
                    ";
                    $rescpago = $mysqli->query($sqldpago);
                    while ($rowpg = mysqli_fetch_row($rescpago)) {
                        $pago += $rowpg[0];
                    }

                }
                $deuda = $facturaant - $pago;
                $total = $saldofactura + $deuda;
                $pdf->Cell(155, 5, utf8_decode("Deuda anterior"), 0, 0, 'L');
                $pdf->Cell(35, 5, utf8_decode(number_format($deuda, 2, '.', ',')), 0, 0, 'R');
                $yd  = $pdf->GetY();
                $dif = ($y + 50)-$yd;
                $pdf->Ln($dif);
                #************ INICIO PIE DE PAGINA FACTURA 1 *****/
                $valorl = numtoletras($total);
                $pdf->SetFont('Arial', 'B', 9);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Ln(1);
                $pdf->MultiCell(120, 3, utf8_decode('SON:'.$valorl), 0, 'L'); #son
                $pdf->SetXY($x+120, $y);
                $pdf->Cell(35, 5, utf8_decode('Neto a Pagar:'), 0, 'L'); #neto a pagar
                $pdf->Cell(35, 5, utf8_decode('$ ' . number_format($total, 2, '.', ',')), 0,0, 'R'); #valor neto a pagar
                $pdf->SetXY($x, $y);
                $pdf->Cell(120, 8, utf8_decode(''), 1, 'L');
                $pdf->Cell(35, 8, utf8_decode(''), 1, 'L');
                $pdf->Cell(35, 8, utf8_decode(''), 1, 'L');
                $pdf->Ln(8);
                $pdf->SetFont('Arial', '', 6);
                $pdf->MultiCell(83, 3, utf8_decode('NOTA: El presente recibo de pago corresponde únicamente a derechos de uso adminisrativo y aprovechamiento económico y/o Servicios Públicos del mes indicado.'), 1, 'L');
                $pdf->Ln(20);
                $yl = $pdf->GetY();
            }
        }
    }
        ob_end_clean();
        $pdf->Output(0, 'Informe_facturacion_Mercaplaza.pdf', 0); 
    break;             
    #* Terminal
    case 3:  
        $pdf=new PDF('P','mm','A4');
        $nb = $pdf->AliasNbPages();               
        $sqlfac = "SELECT 
        fac.id_unico, fac.fecha_factura,
        (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',ter.razonsocial,
        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))) as tercero,
        ter.numeroidentificacion, tel.valor, dir.direccion,
        ter.id_unico, fac.numero_factura, DATE_FORMAT(fac.fecha_factura, '%d/%m/%Y'), 
        DATE_FORMAT(fac.fecha_vencimiento, '%d/%m/%Y'), fac.id_espacio_habitable, 
        cc.nombre, tpf.resolucion, cb.numerocuenta, cb.descripcion, tpc.nombre
        FROM gp_factura fac
        LEFT JOIN gp_tipo_factura tpf ON fac.tipofactura = tpf.id_unico
        LEFT JOIN gf_tercero ter ON fac.tercero = ter.id_unico
        LEFT JOIN gf_direccion dir ON ter.id_unico = dir.tercero
        LEFT JOIN gf_telefono tel ON ter.id_unico = tel.tercero
        LEFT JOIN gf_centro_costo cc ON fac.centrocosto = cc.id_unico
        LEFT JOIN gp_tipo_pago tpp ON tpf.tipo_recaudo = tpp.id_unico
        LEFT JOIN gf_cuenta_bancaria cb ON tpp.cuenta_bancaria = cb.id_unico
        LEFT JOIN gf_tipo_cuenta tpc ON cb.tipocuenta = tpc.id_unico
        WHERE fac.fecha_factura BETWEEN '$fechaI' AND '$fechaF' $betweenfac      
        AND tpf.id_unico = $tipoF ";            
        $resc = $mysqli->query($sqlfac);
        while ($rowfc = mysqli_fetch_row($resc)) {  
            $pdf->AddPage();  
            $factura        = $rowfc[0]; 
            $tercero        = $rowfc[6];
            $espacio        = $rowfc[10];
            $centrocosto    = $rowfc[11];
            $numero_factura = $rowfc[7];
            $resolucion     = $rowfc[12];
            $numero_cuenta  = $rowfc[13];
            $descripcion_cuenta = $rowfc[14];
            $tipo_cuenta    = $rowfc[15];
            #* eespacios 
            $rows = $con->Listar("SELECT CONCAT(te.nombre,': ', eh.codigo) FROM gph_espacio_habitable_factura ehf 
                LEFT JOIN gh_espacios_habitables eh ON eh.id_unico = ehf.espacio_habitable 
                LEFT JOIN gh_tipo_espacio te ON eh.tipo = te.id_unico 
                WHERE ehf.factura = $factura");
            $espacios = '';
            for ($e = 0; $e < count($rows); $e++) {
                $espacios .= $rows[$e][0].'     ';
            }

            $yl = 5;
            for ($r = 0; $r < 2; $r++) {
                if($r==0){
                    if(!empty($ruta_logo)){
                        $pdf->Image('../' . $ruta_logo, 10, $yl, 28);
                    }
                } else {
                    if(!empty($ruta_logo)){
                        $pdf->Image('../' . $ruta_logo, 10, $yl-5, 28);
                    }
                }
                
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetX(42);
                $y = $pdf->GetY();
                $pdf->MultiCell(80, 5, utf8_decode(ucwords(strtolower($razonsocial))), 0, 'C'); #Razon social
                $pdf->SetX(42);
                $pdf->Cell(80, 5, utf8_decode('ESDU'), 0, 0, 'C');                
                $pdf->Ln(5);
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetX(42);
                $pdf->Cell(80, 5, utf8_decode($nombreIdent . ': ' . $numeroIdent .' - Régimen '.ucwords($regimen)), 0, 0, 'C'); #Nit
                $pdf->SetFont('Arial', '', 5);
                $pdf->SetXY(165,$y);                
                $pdf->MultiCell(35, 3, utf8_decode($resolucion), 0, 'C');
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Ln(0);
                $pdf->SetX(165);
                $pdf->Cell(40, 5, utf8_decode('FACTURA DE VENTA'), 0, 0, 'C');
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Ln(5);
                $pdf->SetX(165);                
                $pdf->Cell(40, 8, utf8_decode(''), 1, 0, 'C'); #Conten
                $pdf->Ln(0);
                $pdf->SetFont('Arial', 'B', 15);
                $pdf->SetXY(165, $y+15);
                $pdf->Cell(15, 5, utf8_decode('N° AE '), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 14);
                $pdf->SetXY(185, $y+15);
                $pdf->Cell(15, 5, utf8_decode($numero_factura), 0, 0, 'L'); 
                $pdf->Ln(7);
                
                #*************Fin******************/
                $m = substr($rowfc[8],3,2);
                if ($m < 10){
                    $m = substr($m,1,1);
                }
                $nombremes = $meses[$m];
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(120, 7, utf8_decode('Señor(es):   '.$rowfc[2]), 1, 0, 'L');
                $pdf->Ln(7);
                $pdf->Cell(120 , 7, utf8_decode('Dirección:   '.$rowfc[5]), 1, 0, 'L');
                $pdf->Cell(10, 7, utf8_decode(), 0, 0, 'L'); #Dirección
                $pdf->Cell(60, 7, utf8_decode('Fecha de Expedición         '.$rowfc[8]), 1, 0, 'L'); #Fechafactura
                $pdf->Ln(7);
                $pdf->Cell(60 , 7, utf8_decode('Teléfono:   '.$rowfc[4]), 1, 0, 'L');
                $pdf->Cell(60 , 7, utf8_decode('C.C ó Nit:  '.$rowfc[3]), 1, 0, 'L');
                $pdf->Cell(10, 7, utf8_decode(), 0, 0, 'L'); 
                $pdf->Cell(60, 7, utf8_decode('Fecha de Vencimiento       '.$rowfc[9]), 1, 0, 'L'); #FechaVencimiento
                $pdf->Ln(7);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(190, 7, utf8_decode('Edificio Administrativo  Oficina 504  Teléfono: '.$telefonoTer.' Duitama - Boyacá'), 0, 0, 'C');                
                $pdf->Ln(7);
                /********Conceptos*********/
                $sqldtf = "SELECT 
                con.nombre,
                SUM(dtf.valor), SUM(dtf.iva), SUM(dtf.valor_total_ajustado), SUM(sphc.iva)
                FROM gp_factura fac 
                LEFT JOIN  gp_detalle_factura dtf ON fac.id_unico = dtf.factura
                LEFT JOIN gp_concepto con ON dtf.concepto_tarifa = con.id_unico
                LEFT JOIN gph_espacio_habitable_factura sphf ON fac.id_unico = sphf.factura 
                AND fac.id_espacio_habitable = sphf.espacio_habitable
                LEFT JOIN gph_espacio_habitable_concepto sphc ON con.id_unico = sphc.id_concepto 
                AND sphf.espacio_habitable = sphc.id_espacio_habitable
                WHERE dtf.factura = $factura
                GROUP BY dtf.concepto_tarifa";            
                $rescdf = $mysqli->query($sqldtf);
                $saldofactura = 0;
                $porcentajeiva = 0;
                $iva  = 0;
                $base = 0;
                $cont =0;
                $x1f = $pdf->GetX();
                $y1f = $pdf->GetY();
                $pdf->Cell(190, 39, utf8_decode(''), 1, 0, 'L'); #Container conceptos
                $pdf->SetXY($x1f,$y1f);
                $pdf->Ln(2);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(190, 5, utf8_decode('CONCEPTO: Facturación '.$espacios), 0, 0, 'L');
                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 9);
                while ($rowdf = mysqli_fetch_row($rescdf)) {
                    $pdf->SetX(30);
                    $pdf->Cell(70, 5, utf8_decode($rowdf[0]), 0, 0, 'L'); #Concepto
                    $pdf->Cell(25, 5, utf8_decode(number_format($rowdf[1], 2, '.', ',')), 0, 0, 'R'); #Valor
                    $pdf->Cell(25, 5, utf8_decode(number_format($rowdf[2], 2, '.', ',')), 0, 0, 'R'); #iva
                    $pdf->Cell(25, 5, utf8_decode(number_format(0, 2, '.', ',')), 0, 0, 'R'); # Rubro
                    $pdf->Cell(25, 5, utf8_decode(number_format($rowdf[3], 2, '.', ',')), 0, 0, 'R'); #Total ajustado
                    $saldofactura += $rowdf[3];
                    $porcentajeiva += $rowdf[4];
                    $iva += $rowdf[2];
                    $base += $rowdf[1];
                    if (!empty($rowdf[4])){
                        $cont++;
                    }  
                    $pdf->Ln(5);
                }                
                $facturaant = 0;
                $pago = 0;
                $sqldf1 = "SELECT
                        dtf.id_unico,
                        dtf.valor_total_ajustado                        
                        FROM gp_factura fac
                        LEFT JOIN gp_detalle_factura dtf ON fac.id_unico = dtf.factura
                        WHERE fac.fecha_factura < '$fechaI'
                        AND fac.tercero = $tercero AND tipofactura  = $tipoF";            
                $rescdf = $mysqli->query($sqldf1);
                while ($rowdf = mysqli_fetch_row($rescdf)) {
                    $facturaant += $rowdf[1];
                    $sqldpago = "SELECT SUM(dtp.valor+dtp.iva+dtp.impoconsumo+dtp.ajuste_peso) 
                    FROM gp_detalle_pago dtp
                    LEFT JOIN gp_pago p ON dtp.pago = p.id_unico 
                    WHERE dtp.detalle_factura = $rowdf[0] AND p.fecha_pago < '".$rowfc[1]."'";
                    $rescpago = $mysqli->query($sqldpago);
                    while ($rowpg = mysqli_fetch_row($rescpago)) {
                        $pago += $rowpg[0];
                    }
                }
                $deuda = $facturaant - $pago;
                $total = $saldofactura + $deuda;
                if ($cont > 0){
                    $porcentajeiva = $porcentajeiva/$cont;
                }
                $pdf->SetX(30);
                $pdf->Cell(145, 5, utf8_decode("Deuda anterior"), 0, 0, 'L'); #Deuda anterior
                $pdf->Cell(25, 5, utf8_decode(number_format($deuda, 2, '.', ',')), 0, 0, 'R'); #Total ajustado
                $pdf->Ln(5);
                $yd  = $pdf->GetY();
                $dif = ($y1f + 39)-$yd;
                $pdf->Ln($dif);
                $pdf->Cell(100, 10, utf8_decode(), 1, 0, 'L'); # Conten Sb-Iva
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(25, 5, utf8_decode('SUB TOTAL $'), 0, 0, 'R');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(65, 5, utf8_decode(number_format($base, 2, '.', ',')), 1, 0, 'R'); #sub total
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(100, 0, utf8_decode(), 0, 0, 'L'); # Conten Sb-Iva
                $pdf->Cell(25, 5, utf8_decode('IVA ('.$porcentajeiva.')%'), 'BR', 0, 'C');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(65, 5, utf8_decode(number_format($iva, 2, '.', ',')), 1, 0, 'R'); # iva
                $pdf->Ln(5);
                
                $pdf->Cell(100, 15, utf8_decode(), 1, 0, 'L'); # Conten Sb-Iva
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(25, 5, utf8_decode('INTERESES'), 0, 0, 'R');
                $pdf->Cell(65, 5, utf8_decode(''), 1, 0, 'R');
                $pdf->Ln(5);
                $pdf->Cell(100, 0, utf8_decode(), 0, 0, 'L'); # Conten Sb-Iva
                $pdf->Cell(25, 5, utf8_decode('OTROS'), 0, 0, 'R');
                $pdf->Cell(65, 5, utf8_decode(''), 1, 0, 'R');
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(100, 0, utf8_decode(), 0, 0, 'L'); # Conten Sb-Iva
                $pdf->Cell(25, 5, utf8_decode('TOTAL A PAGAR $'), 'BR', 0, 'R');
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(65, 5, utf8_decode(number_format($total, 2, '.', ',')), 1, 0, 'R'); #total                
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 8.5);
                $pdf->Cell(190, 5, utf8_decode('Favor consignar a la cuenta de: '.$descripcion_cuenta.' N° '.$numero_cuenta.' '.$tipo_cuenta), 0, 0, 'C');
                $pdf->Ln(5);
                $pdf->SetFont('Arial', '', 7.5);
                $pdf->MultiCell(190, 3, utf8_decode('Esta factura Cambiaria de compra - Venta se asimila para todos sus efectos legales a la Letra de Cambio conforme al Artículo 774 del Código de Comercio y causará interés banciario a partir de la fecha de vencimiento.'), 0, 'C');
                $pdf->SetFont('Arial', 'B', 9);
                IF($r==0){
                    $pdf->Cell(190, 5, utf8_decode('USUARIO'), 0, 0, 'C');
                } else {
                    $pdf->Cell(190, 5, utf8_decode('EMPRESA'), 0, 0, 'C');
                }
                $pdf->Ln(11);
                $yl = $pdf->GetY();
                }
            }            
            ob_end_clean();
            $pdf->Output(0, 'Informe_facturacion_terminal.pdf', 0);
        break;
       

    }    