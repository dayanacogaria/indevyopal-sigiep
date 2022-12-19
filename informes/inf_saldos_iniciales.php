<?php
#################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#09/04/2018 |Erica G. |Archivo Creado
################################################################################################################
require'../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $para";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno = $an[0];

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$rutalogoTer = $rowC[6];


#***********Consulta datos recibidos************#

$cng = "SELECT 
        CT.codi_cuenta,
        LOWER(CT.nombre),
        CT.naturaleza,
        IF(CONCAT_WS(' ',
        T.nombreuno,
        T.nombredos,
        T.apellidouno,
        T.apellidodos) 
        IS NULL OR CONCAT_WS(' ',
        T.nombreuno,
        T.nombredos,
        T.apellidouno,
        T.apellidodos) = '',
        (T.razonsocial),
        CONCAT_WS(' ',
        T.nombreuno,
        T.nombredos,
        T.apellidouno,
        T.apellidodos)) AS NOMBRE,
        T.numeroidentificacion,
        LOWER(CC.nombre),
        LOWER(PR.nombre),
        DT.valor
    FROM
        gf_detalle_comprobante DT
    LEFT JOIN
        gf_cuenta CT ON DT.cuenta = CT.id_unico
    LEFT JOIN
        gf_tercero T ON DT.tercero = T.id_unico
    LEFT JOIN
        gf_centro_costo CC ON DT.centrocosto = CC.id_unico
    LEFT JOIN
        gf_proyecto PR ON DT.proyecto = PR.id_unico
    LEFT JOIN gf_comprobante_cnt cnt ON DT.comprobante = cnt.id_unico 
    LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
    LEFT JOIN gf_clase_contable cct ON tc.clasecontable = cct.id_unico 
    WHERE cct.id_unico = 5 and cnt.parametrizacionanno = $para ORDER BY CT.codi_cuenta ASC";
$cng = $mysqli->query($cng);

$tipo = $_GET['tipo'];
#**********PDF*********#
if($tipo=='pdf'){
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    ob_start();
    
    class PDF extends FPDF {
        function Header() {

            global $rutalogoTer;
            global $razonsocial;
            global $numeroIdent;
            global $anno;
            global $numpaginas;
            $numpaginas = $this->PageNo();
            if ($rutalogoTer != '') {
                $this->Image('../' . $rutalogoTer, 12, 6, 25);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', '', 10);
            $this->SetX(25);
            $this->Cell(320, 5, $numeroIdent, 0, 0, 'C');
            $this->SetFont('Arial', 'B', 10);
            $this->Ln(4);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('SALDOS INICIALES'), 0, 0, 'C');
            $this->Ln(4);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($anno), 0, 0, 'C');
            $this->Ln(10);

            $this->SetX(10);
            $this->SetFont('Arial', 'B', 8);

            $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(75, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(70, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(50, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(50, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');

            $this->SetX(10);

            $this->Cell(30, 9, utf8_decode('CÓDIGO'), 0, 0, 'C');
            $this->Cell(75, 9, utf8_decode('NOMBRE'), 0, 0, 'C');
            $this->Cell(70, 9, utf8_decode('TERCERO'), 0, 0, 'C');
            $this->Cell(50, 9, utf8_decode('CENTRO COSTO'), 0, 0, 'C');
            $this->Cell(50, 9, utf8_decode('PROYECTO'), 0, 0, 'C');
            $this->Cell(30, 9, utf8_decode('VALOR DÉBITO'), 0, 0, 'C');
            $this->Cell(30, 9, utf8_decode('VALOR CRÉDITO'), 0, 0, 'C');

            $this->Ln(9);
        }

        function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(10);
        $this->Cell(160, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
        $this->Cell(170, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

    }

    $pdf = new PDF('L', 'mm', 'Legal');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $totalD = 0;
    $totalC = 0;
    while ($row = mysqli_fetch_row($cng)) {
        $pdf->SetFont('Arial', '', 9);
        $alt = $pdf->GetY();
        if($alt>180){
            $pdf->AddPage();
        }
        $xp = $pdf->GetX();
        $yp = $pdf->GetY();
        $pdf->CellFitScale(30, 5, '', 0, 0, 'L');
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(75, 5, utf8_decode(ucwords(($row[1]))), 0, 'J');
        $y2 = $pdf->GetY();
        $h = $y2 - $y;
        $px = $x + 75;
        $pdf->SetXY($px, $y);
        $x2 = $pdf->GetX();
        $y2 = $pdf->GetY();
        $pdf->MultiCell(70, 5, utf8_decode(ucwords(mb_strtolower($row[3]))).' - '.$row[4], 0, 'J');
        $y22 = $pdf->GetY();
        $h2 = $y22 - $y2;
        $px2 = $x2 + 70;
        $alto = max($h, $h2);
        
        $naturaleza = $row[2];
        $valor      = $row[7];
        $debito     = 0;
        $credito    = 0;
        if($naturaleza==1){
            if($valor>0){
                $debito = $valor;
            } else {
                $credito = $valor *-1;
            }
        } elseif($naturaleza==2){
            if($valor>0){
                $credito = $valor;
            } else {
                $debito = $valor *-1;
            }
        }
        $pdf->SetXY($xp,$yp);
        $pdf->CellFitScale(30,$alto, utf8_decode($row[0]), 1, 0, 'L');
        $pdf->Cell(75,$alto, utf8_decode(''), 1, 0, 'C');
        $pdf->Cell(70,$alto, utf8_decode(''), 1, 0, 'C');
        $pdf->CellFitScale(50,$alto, utf8_decode(ucwords($row[5])), 1, 0, 'L');
        $pdf->CellFitScale(50,$alto, utf8_decode(ucwords($row[6])), 1, 0, 'L');
        $pdf->Cell(30, $alto, number_format($debito, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(30, $alto, number_format($credito, 2, '.', ','), 1, 0, 'R');
        $pdf->Ln($alto);
         
        $totalD +=$debito;
        $totalC +=$credito;
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(275,5, utf8_decode('TOTALES'), 1, 0, 'R');
    $pdf->CellFitScale(30, 5, number_format($totalD, 2, '.', ','), 1, 0, 'R');
    $pdf->CellFitScale(30, 5, number_format($totalC, 2, '.', ','), 1, 0, 'R');
    
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Saldos_Iniciales(' . date('d/m/Y') . ').pdf', 0);


}
#**********XLS*********#
elseif($tipo=='excel'){
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Saldos_Iniciales.xls");
    require_once("../Conexion/conexion.php");
    ini_set('max_execution_time', 0);
    session_start();
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Saldos Iniciales</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="7" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
               <br/>&nbsp;
               <br/>SALDOS INICIALES
               <br/><?php echo $anno?></strong>
                <br/>&nbsp;
            </th>
      </tr>
      <tr>
        <td><strong>CÓDIGO</strong></td>
        <td><strong>NOMBRE</strong></td>
        <td><strong>TERCERO</strong></td>
        <td><strong>CENTRO COSTO</strong></td>
        <td><strong>PROYECTO</strong></td>
        <td><strong>VALOR DÉBITO</strong></td>
        <td><strong>VALOR CRÉDITO</strong></td>       
      </tr>
    
    <?php
    $totalD = 0;
    $totalC = 0;
     while ($row = mysqli_fetch_row($cng)) { ?>
        <tr>
            <td><?php echo  $row[0]?></td>
            <td><?php echo ucwords(($row[1]))?></td>
            <td><?php echo  ucwords(mb_strtolower($row[3])).' - '.$row[4]?></td>
            <td><?php echo ucwords(($row[5]))?></td>
            <td><?php echo ucwords(($row[6]))?></td>
            <?php 
            $naturaleza = $row[2];
            $valor      = $row[7];
            $debito     = 0;
            $credito    = 0;
            if($naturaleza==1){
                if($valor>0){
                    $debito = $valor;
                } else {
                    $credito = $valor *-1;
                }
            } elseif($naturaleza==2){
                if($valor>0){
                    $credito = $valor;
                } else {
                    $debito = $valor *-1;
                }
            } ?>
            <td><?php echo number_format($debito, 2,  '.', ',');?></td>
            <td><?php echo number_format($credito, 2,  '.', ',');?></td>
        </tr>
         
    <?php   $totalD +=$debito;
            $totalC +=$credito;
                } ?>
        <tr>
            <td colspan="5"><strong>TOTALES</strong></td>
            <td><strong><?php echo number_format($totalD, 2,  '.', ',');?></strong></td>
            <td><strong><?php echo number_format($totalC, 2,  '.', ',');?></strong></td>
        </tr>
    </table>
    </body>
    </html>
<?php } ?>