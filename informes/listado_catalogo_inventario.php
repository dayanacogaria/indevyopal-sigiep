<?php
ini_set('max_execution_time', 0);
session_start();
ob_start();
header("Content-Type: text/html;charset=utf-8");

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';

$compa = $_SESSION['compania'];
$comp = "SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico = $compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];

class PDF extends FPDF{
    function Header(){
        global $nombreCompania;
        global $nitcompania;
        global $numpaginas;
        global $ruta;
        $numpaginas=$this->PageNo();

        $this->SetFont('Arial','B',10);
        $this->SetY(10);
        if($ruta != ''){
            $this->Image('../'.$ruta,15,8,20);
        }

        $this->SetX(10);
        $this->Cell(330,5,utf8_decode($nombreCompania),0,0,'C');
        $this->Ln(5);

        $this->SetX(10);
        $this->Cell(330, 5,$nitcompania,0,0,'C');
        $this->Ln(5);

        $this->SetX(10);
        $this->Cell(330,5,utf8_decode('LISTADO CATALOGO INVENTARIO'),0,0,'C');
        $this->Ln(5);
        $this->SetX(10);
        $this->Cell(50,10,utf8_decode(''),1,0,'C');
        $this->Cell(65,10,utf8_decode(''),1,0,'C');
        $this->Cell(10,10,utf8_decode(''),1,0,'C');
        $this->Cell(40,10,utf8_decode(''),1,0,'C');
        $this->Cell(30,10,utf8_decode(''),1,0,'C');
        $this->Cell(40,10,utf8_decode(''),1,0,'C');
        $this->Cell(40,10,utf8_decode(''),1,0,'C');
        $this->Cell(30,10,utf8_decode(''),1,0,'C');
        $this->Cell(30,10,utf8_decode(''),1,0,'C');
        $this->SetX(10);
        $this->SetFont('Arial','B',8);
        $this->Cell(50,9,utf8_decode('CÓDIGO'),0,0,'C');
        $this->Cell(65,9,utf8_decode('NOMBRE'),0,0,'C');
        $this->Cell(10,9,utf8_decode('MOV'),0,0,'C');
        $this->Cell(40,9,utf8_decode('TIPO'),0,0,'C');
        $this->Cell(30,9,utf8_decode('PREDECESOR'),0,0,'C');
        $this->Cell(40,9,utf8_decode('UNIDAD'),0,0,'C');
        $this->Cell(40,9,utf8_decode('TIPO ACTIVO'),0,0,'C');
        $this->Cell(30,9,utf8_decode('FICHA'),0,0,'C');
        $this->Cell(30,9,utf8_decode('PLAN INVENTARIO'),0,0,'C');
        $this->Ln(5);
        $this->SetX(10);
        $this->Cell(50,5,utf8_decode(''),0,0,'C');
        $this->Cell(65,5,utf8_decode(''),0,0,'C');
        $this->Cell(10,5,utf8_decode(''),0,0,'C');
        $this->Cell(40,5,utf8_decode('MOVIMIENTO'),0,0,'C');
        $this->Cell(30,5,utf8_decode(''),0,0,'C');
        $this->Cell(40,5,utf8_decode('FACTOR'),0,0,'C');
        $this->Cell(40,5,utf8_decode(''),0,0,'C');
        $this->Cell(30,5,utf8_decode(''),0,0,'C');
        $this->Cell(30,5,utf8_decode('PADRE'),0,0,'C');
        $this->Ln(5);
    }

    function Footer(){
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(15);
        $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
        $this->Cell(70);
        $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(60);
        $this->Cell(30,10,utf8_decode('Usuario: '.$usuario),0);
        $this->Cell(70);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}

$pdf = new PDF('L','mm','Legal');
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);
$sql = "SELECT    pln.id_unico,
                  pln.codi,
                  pln.nombre,
                  pln.tienemovimiento,
                  ti.nombre,
                  pre.codi,
                  uni.nombre,
                  tpa.nombre,
                  fch.descripcion,
                  padre.codi
        FROM      gf_plan_inventario pln
        LEFT JOIN gf_tipo_inventario ti  ON pln.tipoinventario = ti.id_unico
        LEFT JOIN gf_unidad_factor uni   ON pln.unidad     = uni.id_unico
        LEFT JOIN gf_plan_inventario pre ON pln.predecesor = pre.id_unico
        LEFT JOIN gf_tipo_activo tpa     ON pln.tipoactivo = tpa.id_unico
        LEFT JOIN gf_ficha fch           ON pln.ficha      = fch.id_unico
        LEFT JOIN gf_plan_inventario_asociado aso ON pln.id_unico = aso.plan_hijo
        LEFT JOIN gf_plan_inventario padre ON aso.plan_padre = padre.id_unico
        WHERE     pln.compania = $compa
        ORDER BY  pln.codi ASC";

$res = $mysqli->query($sql);
while($row = mysqli_fetch_row($res)){
    $mov = "";
    switch ($row[3]) {
        case '1':
            $mov = "NO";
            break;

        case '2':
            $mov = "SI";
            break;
    }
    $nombre = ucwords(mb_strtolower($row[2]));
    $pdf->Cell(50,5,$row[1],0,0,'L');
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->MultiCell(65,5,utf8_decode($nombre),0,'L');
    $y2 = $pdf->GetY();
    $h = $y2-$y;
    $px = $x + 65;
    $pdf->Ln(-$h);
    $pdf->SetX($px);
    $pdf->Cell(10,5,$mov,0,0,'C');
    $pdf->Cell(40,5,ucwords(mb_strtolower($row[4])),0,0,'L');
    $pdf->Cell(30,5,$row[5],0,0,'L');
    $pdf->Cell(40,5,ucwords(mb_strtolower($row[6])),0,0,'L');
    $pdf->Cell(40,5,ucwords(mb_strtolower($row[7])),0,0,'L');
    $pdf->Cell(30,5,ucwords(mb_strtolower($row[8])),0,0,'L');
    $pdf->Cell(30,5,$row[9],0,0,'L');
    $pdf->Ln($h);
}

while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'listato_inventario.pdf',0);
?>