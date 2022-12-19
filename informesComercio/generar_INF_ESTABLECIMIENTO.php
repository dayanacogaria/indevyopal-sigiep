<?php

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
$hoy = date('d/m/Y');

class PDF extends FPDF
{  
    function Header()
    {
        global $nomcomp;
        global $tipodoc;
        global $numdoc;
        global $numpaginas;
        global $ruta;
        $numpaginas=$numpaginas+1;
        $this->SetFont('Arial','B',10);
        
        if($ruta != '')
        {
          $this->Image('../'.$ruta,30,8,20);
        }
        $this->SetY(10);
        $this->SetX(25);
        $this->Cell(315,5,utf8_decode($nomcomp),0,0,'C');
        $this->setX(25);
        $this->SetFont('Arial','B',8);
        $this->Cell(315,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
        $this->Ln(5);
        $this->SetFont('Arial','',8);
        $this->SetX(25);
        $this->Cell(315, 5,$tipodoc.': '.$numdoc,0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->SetX(25);
        $this->Cell(315,10,utf8_decode('VERSIÓN SGC'),0,0,'R');
        $this->Ln(5);
        $this->SetFont('Arial','',8);
        $this->SetX(25);
        $this->Cell(315,5,utf8_decode('LISTADO DE ESTABLECIMIENTOS'),0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->SetX(25);
        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');
        $this->Ln(17);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(40,9,utf8_decode('Nombre'),1,0,'C');
        $this->Cell(40,9,utf8_decode('Contribuyente'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Cód Matrícula'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Fecha Inscripción'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Estrato'),1,0,'C');
        $this->Cell(30,9,utf8_decode('Dirección'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Código Catastral'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Ciudad'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Barrio'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Localización'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Tipo Entidad'),1,0,'C');
        $this->Cell(25,9,utf8_decode('Tamaño Entidad'),1,0,'C');
        $this->Ln(9);
    }

    function Footer()
    {
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
        $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('L','mm','Legal');     
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$consulta = "SELECT     t.razonsocial as traz,
        t.tipoidentificacion as tide,
        ti.id_unico as tid,
        ti.nombre as tnom,
        t.numeroidentificacion tnum, 
        t.ruta_logo as ruta  
FROM gf_tercero t
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE t.id_unico = $compania";
$cmp = $mysqli->query($consulta);
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
while ($fila = mysqli_fetch_array($cmp))
{
    $nomcomp = utf8_decode($fila['traz']);       
    $tipodoc = utf8_decode($fila['tnom']);       
    $numdoc  = utf8_decode($fila['tnum']);   
    $ruta    = $fila['ruta'];
}

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);

$yp         = $pdf->GetY();
$codd       = 0;
$totales    = 0;
$valorA     = 0;

if(empty($_GET['a'])){
$sql = "SELECT e.id_unico,
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
        t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
        e.nombre,
        DATE_FORMAT(e.fechainscripcion,'%d/%m/%Y') AS fechaFacConvertida,
        est.nombre,
        e.direccion,
        e.cod_catastral,
        ciu.nombre,
        b.nombre,
        l.nombre,
        te.nombre,
        tame.nombre,
        c.codigo_mat 
FROM gc_establecimiento e
LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
LEFT JOIN gp_estrato est ON est.id_unico=e.estrato
LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad 
ORDER BY e.fechainscripcion";
} else {
    $sql = "SELECT e.id_unico,
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
        t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
        e.nombre,
        DATE_FORMAT(e.fechainscripcion,'%d/%m/%Y') AS fechaFacConvertida,
        est.nombre,
        e.direccion,
        e.cod_catastral,
        ciu.nombre,
        b.nombre,
        l.nombre,
        te.nombre,
        tame.nombre,
        c.codigo_mat 
FROM gc_establecimiento e
LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
LEFT JOIN gp_estrato est ON est.id_unico=e.estrato
LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad 
WHERE YEAR(e.fechainscripcion) = '".$_GET['a']."' 
ORDER BY e.fechainscripcion";
}
$resultado=$mysqli->query($sql);

while($row=mysqli_fetch_row($resultado)){
    $pdf->SetX(10);
    $pdf->SetFont('Arial','',8);
    $y1 = $pdf->GetY();
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($row[2] ))),0,'L');
    $y2 = $pdf->GetY();
    $h = $y2-$y1;
    $px = $x1+40;
    $pdf->SetXY($px,$y1);
    $y2 = $pdf->GetY();
    $x2 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($row[1] ))),0,'L');
    $y22 = $pdf->GetY();
    $h1 = $y22-$y2;
    $px2 = $x2+40;
    $pdf->SetXY($px2,$y2);    
    $pdf->Cell(25,4,utf8_decode($row[12]),0,0,'C');
    $pdf->Cell(25,4,utf8_decode($row[3]),0,0,'C');
    $pdf->Cell(25,4,utf8_decode($row[4]),0,0,'C');
    $y3 = $pdf->GetY();
    $x3 = $pdf->GetX();
    $pdf->MultiCell(30,4,utf8_decode(ucwords(mb_strtolower($row[5]))),0,'L');
    $y23 = $pdf->GetY();
    $h3 = $y23-$y3;
    $px3 = $x3+30;
    $pdf->SetXY($px3,$y3);
    $pdf->Cell(25,4,utf8_decode($row[6]),0,0,'L');
    $pdf->Cell(25,4,utf8_decode($row[7]),0,0,'L');
    $y4 = $pdf->GetY();
    $x4 = $pdf->GetX();
    $pdf->MultiCell(25,4,utf8_decode(ucwords(mb_strtolower($row[8]))),0,'L');
    $y24 = $pdf->GetY();
    $h4 = $y24-$y4;
    $px4 = $x4+25;
    $pdf->SetXY($px4,$y4);
    $y5 = $pdf->GetY();
    $x5 = $pdf->GetX();
    $pdf->MultiCell(25,4,utf8_decode(ucwords(mb_strtolower($row[9]))),0,'L');
    $y25 = $pdf->GetY();
    $h5 = $y25-$y5;
    $px5 = $x5+25;
    $pdf->SetXY($px5,$y5);
    $y6 = $pdf->GetY();
    $x6 = $pdf->GetX();
    $pdf->MultiCell(25,4,utf8_decode(ucwords(mb_strtolower($row[10]))),0,'L');
    $y26 = $pdf->GetY();
    $h6 = $y26-$y6;
    $px6 = $x6+25;
    $pdf->SetXY($px6,$y6);
    $y7 = $pdf->GetY();
    $x7 = $pdf->GetX();
    $pdf->MultiCell(25,4,utf8_decode(ucwords(mb_strtolower($row[11]))),0,'L');
    $y27 = $pdf->GetY();
    $h7 = $y27-$y7;
    $px7 = $x7+25;
    $pdf->SetXY($px7,$y7);
    $alto = max($h,$h1,$h2,$h3,$h4,$h5,$h6,$h7);
    $pdf->Ln($alto);
    $alt = $pdf->GetY();
    if($alt>180){
        $pdf->AddPage();
    }
}

while (ob_get_length()) {
    ob_end_clean();
}  
$pdf->Output(0,'Listado Establecimiento('.date('d/m/Y').').pdf',0);


?>
