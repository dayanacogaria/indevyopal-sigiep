<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
require'../fpdf/fpdf.php';
require_once("../Conexion/conexion.php");
list($proI, $proF, $fechaini, $fecha1, $hoy, $compania, $usuario)
    = array($_POST["sltEin"], $_POST["sltEfn"], $_POST["fechaini"], $_POST["fechaini"], date("d/m/Y"), $_SESSION['compania'], $_SESSION['usuario']);
$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            UPPER(ti.nombre) as tnom,
                            t.numeroidentificacion tnum
            FROM            gf_tercero t
            LEFT JOIN       gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE           t.id_unico = $compania";
$cmp      = $mysqli->query($consulta);
$fila     = mysqli_fetch_row($cmp);
$ff       = explode("/", $fechaini);
$fecha    = "$ff[2]-$ff[1]-$ff[0]";
list($nomcomp, $tipodoc, $numdoc) = array(utf8_decode($fila[0]), utf8_decode($fila[3]), utf8_decode($fila[4]));
class PDF extends FPDF{
    var $widths;
    var $aligns;

    function Footer(){
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(100,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
        $this->Cell(100,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
        $this->Cell(30,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }

    function SetWidths($w){
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function fill($f){
        //juego de arreglos de relleno
        $this->fill = $f;
    }

    function fila($data){
        //Calcula el alto de l afila
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5 * $nb;
        //Realiza salto de pagina si es necesario
        $this->CheckPageBreak($h);
        //Pinta las celdas de la fila
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Guarda la posicion actual
            $x = $this->GetX();
            $y = $this->GetY();
            //Pinta el border
            $this->Rect($x, $y, 0, 0, '');
            //Imprime el texto
            $this->MultiCell($w,5, $data[$i],'', $a, '');
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Hace salto de la pagina
        $this->Ln($h - 5);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw =&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ( $w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s  = str_replace('\r','', $txt);
        $nb = strlen($s);
        if( $nb > 0 and $s[$nb-1] == '\n' )
            $nb–;
        $sep = -1;
        $i   = 0;
        $j   = 0;
        $l   = 0;
        $nl  = 1;
        while( $i < $nb ){
            $c = $s[$i];
            if( $c == '\n' ){
                $i++;
                $sep =-1;
                $j   =$i;
                $l   =0;
                $nl++;
                continue;
            }
            if( $c == '' )
                $sep = $i;
            $l += $cw[$c];
            if( $l > $wmax ){
                if( $sep ==-1 ){
                    if($i == $j)
                        $i++;
                }else
                    $i = $sep+1;
                $sep =-1;
                $j   =$i;
                $l   =0;
                $nl++;
            }else
                $i++;
        }
        return $nl;
    }
}
$pdf = new PDF('L','mm','Letter');
$nb  = $pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
$pdf->SetY(10);
$pdf->MultiCell(260,5,utf8_decode($nomcomp).PHP_EOL.$tipodoc.': '.$numdoc.PHP_EOL.'EXISTENCIAS DE INVENTARIO'.PHP_EOL.utf8_decode('HASTA: '.$fecha1),0,'C');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(44,9,utf8_decode('CÓDIGO'),1,0,'C');
$pdf->Cell(110,9,utf8_decode('ELEMENTO'),1,0,'C');
$pdf->Cell(40,9,utf8_decode('UNIDAD'),1,0,'C');
$pdf->Cell(22,9,utf8_decode('CANTIDAD'),1,0,'C');
$pdf->Cell(44,9,utf8_decode('VALOR'),1,0,'C');
$pdf->Ln(10);
$pdf->SetFont('Arial','',8);
list($xCant, $xValorT) = array(0, 0);
$str = "SELECT    gpl.id_unico, gpl.codi, UPPER(gpl.nombre), UPPER(gum.nombre)
        FROM      gf_plan_inventario AS gpl
        LEFT JOIN gf_unidad_factor   AS gum ON gpl.unidad = gum.id_unico
        WHERE     (gpl.id_unico BETWEEN $proI AND $proF)
        AND       gpl.compania = $compania";
$res = $mysqli->query($str);
$dat = $res->fetch_all(MYSQLI_NUM);
foreach ($dat as $row){
    list($xsaldo, $xvalor) = array(0, 0);
    $str_x = "SELECT    gtm.clase, gdm.cantidad, gdm.valor
              FROM      gf_detalle_movimiento AS gdm
              LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
              LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
              WHERE     (gdm.planmovimiento = $row[0])
              AND       (gtm.clase IN (2,3))
              AND       (gmv.fecha <= '$fecha')
              AND       (gmv.compania = $compania)
              ORDER BY  gmv.fecha, gdm.hora, gtm.clase";
    $res_x = $mysqli->query($str_x);
    $dat_x = $res_x->fetch_all(MYSQLI_NUM);
    foreach ($dat_x as $rowX){
        switch ($rowX[0]) {
            case 2:
                $xsaldo += $rowX[1];
                $xvalor += ($rowX[2] * $rowX[1]);
                break;

            case 3:
                $xsaldo -= $rowX[1];
                $xvalor -= ($rowX[2] * $rowX[1]);
                break;
        }
    }
    $pdf->SetAligns(array('L', 'L', 'L', 'R', 'R'));
    $pdf->SetWidths(array(44, 110, 40, 22, 44));
    if($xsaldo > 0){
        $pdf->fila(array($row[1], mb_strtoupper($row[2]), $row[3], $xsaldo, number_format($xvalor, 2 , ',', '.')));
        $pdf->Ln(5);
        $xCant   += $xsaldo;
        $xValorT += $xvalor;
    }
}
$pdf->Cell(260,0,'',1);
$pdf->Ln(1);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(195,5,'Totales: ',0);
$pdf->Cell(22,5,$xCant,0,0,'C');
$pdf->Cell(44,5,number_format($xValorT,2,'.',','),0,0,'R');
ob_end_clean();
$pdf->Output(0,'Informe_Existencias_Inventario('.date('d/m/Y').').pdf',0);