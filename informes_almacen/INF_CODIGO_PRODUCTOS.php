<?php

header("Content-Type: text/html;charset=utf-8");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require '../barcode.php';
ini_set('max_execution_time', 360);
session_start();
ob_start();
$con = new ConexionPDO();
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$n_compania = $rowC[0][1].' - '.$rowC[0][3];

$pi     = $_REQUEST['productoI'];
$pf     = $_REQUEST['productoF'];
$pdf    = new PDF_BARCODE('P','mm','A4');		
$pdf->AliasNbPages();		
$pdf->AddPage();
$yp = $pdf->GetY();
$xp = $pdf->GetX();
$y  = $pdf->GetY();
$x  = $pdf->GetX();
$row = $con->Listar("SELECT DISTINCT trim(rtrim(pe.valor)), p.descripcion
    FROM gf_producto_especificacion pe 
    LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
    LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
    LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
    LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
    LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
    WHERE m.compania = $compania AND fi.elementoficha = 6  
    AND pe.valor BETWEEN $pi AND $pf 
    GROUP BY pe.valor 
    ORDER BY CAST(pe.valor AS UNSIGNED)  ASC");
for ($i = 0; $i < count($row); $i++) {
    $cod = trim($row[$i][0]);
    $pdf->SetFont('Arial','',6);
    $pdf->SetXY($x, $y);
    $pdf->MultiCell(38,2,utf8_decode($n_compania),0,'C');
    $ydp = $pdf->GetY();
    $pdf->SetXY($x, $ydp);
    $pdf->MultiCell(38,2,utf8_decode($row[$i][0].' - '.$row[$i][1]),0,'C');
    $ydp = $pdf->GetY();
    $pdf->EAN13($x,$ydp, $cod ,14,0.4,8);
    if($x>140){
        $pdf->Ln(28);
        $pdf->SetX($xp);
        $x  = $pdf->GetX();
        $y  = $pdf->GetY();
    } else {
        $x  += 50;
    }
    if($pdf->GetY()>270){
        $pdf->AddPage();
        $x  = $pdf->GetX();
        $y  = $pdf->GetY();
    }
}
/*$pdf->SetXY($xp, $yp-5);
$y  = $pdf->GetY();
$x  = $pdf->GetX();
for ($i = 0; $i < 100; $i++) {
    $cod = $i;
    $pdf->SetFont('Arial','B',6);
    $pdf->Cell(50,$y,utf8_decode($compania),0,0,'L');
    if($x>180){
        $pdf->Ln(20);
        $x  = $pdf->GetX();
        $y  = $pdf->GetY();
    } else {
        $x  += 50;
    }
    if($pdf->GetY()>100){
        $pdf->AddPage();
        $x  = $pdf->GetX();
        $y  = $pdf->GetY();
    }
}


*/


ob_end_clean();
$pdf->Output(0,'Informe_Codigo_Producto.pdf',0);
