<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 08/06/2017
 * Time: 9:51 AM
 *
 * inf_orden_compra_almacen.php
 * Archivo para generar el informe de entrada de almacen
 * @package Almacen
 * @param String $mov Id de movimiento
 * @version $Id: inf_orden_compra_almacen.php 001 2017-06-08 Alexander Numpaque$
 */
header("Content-Type: text/html;charset=utf-8");
session_start();
@ob_start();
//Archivos adjuntos
require '../fpdf/fpdf.php';
require '../Conexion/conexion.php';
require '../numeros_a_letras.php';
require_once ('../modelAlmacen/movimiento.php');
//Captura de variables
$mov = $_GET['mov'];
$compania = $_SESSION['compania'];
//Array para igualar los numeros de meses
$meses = array('no','01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
$movimiento = new mov();
//Consulta para obtener los datos de la compañia
$rowC = $movimiento->data_compania($compania);
$razonSocial = $rowC[0]; $tipoIdent = $rowC[1]; $numeroIdent = $rowC[2]; $ruta = $rowC[3];//Razon social, tipo de identificación, numero de identificación, Ruta de logo
//Consulta para obtener los datos del movimiento
$rowMov = $movimiento->data_movimiento($mov);
$id_mov = $rowMov[0]; $tipo_mov = $rowMov[1]; $numero_mov = $rowMov[2]; $dia_letras = $rowMov[3]; $n_dia = $rowMov[4];$n_mes = $rowMov[5]; $anno = $rowMov[6];
$ciudad = $rowMov[7]; $tipo_doc_aso = $rowMov[8]; $tercero = $rowMov[10]; $dependencia = $rowMov[11]; $descripcion = $rowMov[12]; $observaciones = $rowMov[13]; $id_tipo_mov = $rowMov[14];
$tercero2 = $rowMov[15];
if(!empty($tipo_doc_aso)) {
    switch ($tipo_doc_aso){
        case '1':
            $factura = $rowMov[9]; $remision = "0";
            break;
        case '2':
            $remision = $rowMov[9]; $factura = "0";
            break;
        case '3':
            $remision = "NO APLICA"; $factura = "NO APLICA";
            break;
    }
}else{
    $remision = "NO APLICA"; $factura = "NO APLICA";
}
//Consulta para obtener el detalle asociado de este movimiento, el cual a de ser la requisición
//dta hace referencia al detalle asociado, mov_a hace referencia al movimiento asociado
$num_aso = "";
$rowsD = $movimiento->data_asociado($id_mov);
$num_aso .= $rowsD[1]." ,";
$num_aso = substr($num_aso, 0, strlen($num_aso) - 1);


class PDF_MC_Table extends FPDF{
    var $widths;
    var $aligns;
    function SetWidths($w){
        $this->widths=$w;   //Obtenemos un  array con los anchos de las columnas
    }
    function SetAligns($a){
        $this->aligns=$a;   //Obtenemos un array con los alineamientos de las columnas
    }
    function fill($f){
        $this->fill=$f;     //Juego de arreglos de relleno
    }
    function Row($data){
        //Calculo del alto de una fila
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5*$nb;
        //Si una pagina tiene salto de linea
        $this->CheckPageBreak($h);
        //Dibujar las celdas de las fila
        for($i=0;$i<count($data);$i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Guardamos las posiciones actuales
            $x = $this->GetX(); $y = $this->GetY();
            //Dibujamos el borde
            /** @var String $style */
            $this->Rect($x, $y, $w, $h, '');
            //Imprimimos el texto
            /** @var String $fill */
            $this->MultiCell($w,4,$data[$i],'LTR', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h - 5);
    }
    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax=($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s=str_replace('\r','',$txt);
        $nb=strlen($s);
        if($nb > 0 and $s[$nb-1] == '\n')
            $nb–;
        $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
        while($i < $nb){
            $c=$s[$i];
            if($c == '\n'){
                $i++; $sep = -1; $j = $i; $l = 0; $nl++;
                continue;
            }
            if($c == '')
                $sep = $i;
            $l += $cw[$c];
            if($l > $wmax){
                if($sep == -1){
                    if($i == $j)
                        $i++;
                }else
                    $i = $sep + 1;
                $sep = -1; $j = $i; $l = 0; $nl++;
            }else
                $i++;
        }
        return $nl;
    }

    #Funcón cabeza de la página
    function header(){
        #Redeclaración de varibles
        global $razonSocial;	#Nombre de compañia
        global $tipoIdent;	    #Tipo de identificación
        global $numeroIdent;	#Nombre de comprobante
        global $ruta;			#Ruta de logo
        global $tipo_mov;       #Tipo de movimiento nombre
        global $numero_mov;     #Número de movimiento
        #Validación cuando la variable $ruta, la obtiene la ruta del logo no esta vacia
        if($ruta != '')  {
            $this->Image('../'.$ruta,10,10,18);
        }
        #Razón social
        $this->SetFont('Arial','B',12);
        $this->SetXY(40,15);
        $this->MultiCell(140,5,utf8_decode(strtoupper($razonSocial)),0,'C');
        #Tipo documento y número de documento
        $this->SetX(10);
        $this->Ln(1);
        $this->SetFont('Arial','B',10);
        $this->Cell(200,5,utf8_decode(strtoupper($tipoIdent).':'." ".$numeroIdent),0,0,'C');
        #Tipo de comprobante y número de comprobante
        $this->Ln(5);
        $this->SetFont('Arial','B',10);
        $this->Cell(200,5,utf8_decode(ucwords(strtoupper($tipo_mov." ".'Nª:')))." ".$numero_mov,0,0,'C');

        $this->Ln(5);
    }

    function Row_none($data){
        //Calculo del alto de una fila
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5*$nb;
        //Si una pagina tiene salto de linea
        $this->CheckPageBreak($h);
        //Dibujar las celdas de las fila
        for($i=0;$i<count($data);$i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Guardamos las posiciones actuales
            $x = $this->GetX();
            $y = $this->GetY();
            //Dibujamos el borde
            /** @var String $style */
            $this->Rect(0, 0, 0, 0, '');
            //Imprimimos el texto
            /** @var String $fill */
            $this->MultiCell($w,4,$data[$i],'', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h - 5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(70,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
        $this->Cell(70,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
        $this->Cell(60,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF_Mc_Table('P', 'mm', 'Letter');		#Creación del objeto pdf
$nb=$pdf->AliasNbPages();		#Objeto de número de pagina
$pdf->AddPage();				#Agregar página
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetWidths(array(35, 90, 35, 40));
$pdf->SetAligns(array('R', 'L', 'R', 'L'));
$pdf->Row_none(array('FECHA:', strtoupper("$ciudad $dia_letras, $n_dia $meses[$n_mes] $anno"), utf8_decode('FACTURA Nº:'), $factura));
$pdf->Ln(5);
$pdf->SetWidths(array(35, 90, 35, 40));
$pdf->SetAligns(array('R', 'L', 'R', 'L'));
$pdf->Row_none(array(utf8_decode('REQUISICIÓN:'), utf8_decode(strtoupper($num_aso)), utf8_decode('REMISIÓN Nº:'), $remision));
$pdf->Ln(5);
$pdf->SetWidths(array(35, 90, 35, 40));
$pdf->SetAligns(array('R', 'L', 'R', 'L'));
$pdf->Row_none(array('SOLICITANTE:', utf8_decode(mb_strtoupper($tercero)), utf8_decode('DEPENDENCIA:'), utf8_decode(mb_strtoupper($dependencia))));
$pdf->Ln(5);
$pdf->SetWidths(array(35, 165));
$pdf->SetAligns(array('R', 'L'));
$pdf->Row_none(array(utf8_decode('DESCRIPCIÓN:'), utf8_decode(ucfirst(strtolower($descripcion)))));
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 5, '', 'LTR', 0,'C');
$pdf->Cell(60, 5, utf8_decode('PLAN'), 'LTR', 0, 'C');
$pdf->Cell(10, 5, 'CANT', 'LTR', 0, 'C');
$pdf->Cell(40, 5, 'VALOR', 'LTR', 0, 'C');
$pdf->Cell(40, 5, 'VALOR UNITARIO', 'LTR', 0, 'C');
$pdf->Cell(40, 5, '', 'LTR', 0, 'C');
$pdf->Ln(5);
$pdf->Cell(10, 5, utf8_decode('Nª'), 'LRB', 0,'C');
$pdf->Cell(60, 5, 'INVENTARIO', 'LRB', 0, 'C');
$pdf->Cell(10, 5, 'IDAD', 'LRB', 0, 'C');
$pdf->Cell(40, 5, 'UNITARIO', 'LRB', 0, 'C');
$pdf->Cell(40, 5, 'IVA', 'LRB', 0, 'C');
$pdf->Cell(40, 5, 'SUBTOTAL', 'LRB', 0, 'C');
$sqlP = "SELECT   dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva
        FROM      gf_detalle_movimiento dtm
        LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
        WHERE     dtm.movimiento = $id_mov";
$resultP = $mysqli->query($sqlP);
$a = 0;
$valorTU = 0; $valorTI = 0; $valorTAA = 0;
while ($rowP = mysqli_fetch_row($resultP)) {
    $a++;
    $valorT    = ($rowP[3] + $rowP[4]) * $rowP[2];
    $valorTA   = number_format($valorT, 2, ',' , '.');
    $valorA    = number_format($rowP[3], 2, ',', '.');
    $valorI    = number_format($rowP[4], 2, ',', '.');
    $valorTU  += ($rowP[3] * $rowP[2]); $valorTI += ($rowP[4] * $rowP[2]);
    $valorTAA += $valorT;
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetWidths(array(10, 60, 10, 40, 40, 40));
    $pdf->SetAligns(array('C', 'L', 'C', 'R', 'R', 'R'));
    $pdf->Row(array($a, $rowP[1], number_format($rowP[2], 0), $valorA, $valorI, $valorTA));
}
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(160, 5, 'SUBTOTAL', 'LRT', 0, 'C');
$pdf->Cell(40, 5, number_format($valorTU, 2, ',', '.'), 'LTR', 0, 'R');
$pdf->Ln(5);
$pdf->Cell(160, 5, 'TOTAL IVA', 'LTR', 0, 'C');
$pdf->Cell(40, 5, number_format($valorTI, 2, ',', '.'), 'LTR', 0, 'R');
$pdf->Ln(5);
$pdf->Cell(160, 5, 'TOTAL', 'LTRB', 0, 'C');
$pdf->Cell(40, 5, number_format($valorTAA, 2, ',', '.'), 'LTRB', 0, 'R');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetAligns(array('R', 'L'));
$pdf->SetWidths(array(35, 165));
$pdf->Row(array(utf8_decode('OBSERVACIONES:'), utf8_decode(ucwords(mb_strtolower($observaciones)))));
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetAligns(array('R', 'L'));
$pdf->SetWidths(array(35, 165));
$pdf->Row(array(utf8_decode('VALOR EN LETRAS:'), utf8_decode(numtoletras($valorTAA))));
$pdf->Ln(30);
$yy1 = $pdf->GetY();
// $pdf->Cell(60,0,'','B');
// $pdf->Ln(3);
// $pdf->Cell(190,2,utf8_decode($tercero2),0,0,'L');
// $pdf->Ln(5);
// $pdf->Cell(190,2,utf8_decode("SOLICITADO POR"),0,0,'L');
$pdf->SetFont('Arial', 'B', 8);
$data_firmas = $movimiento->data_firmas($id_tipo_mov);
$xxx = 0;
foreach($data_firmas as $row_firma){
    if($xxx == 0){
        $yyy = $yy1;
    }
    $xxx++;
    if($xxx % 2 == 0){
        $pdf->SetXY(140, $yyy);
        $pdf->Cell(60, 0, '', 'B');
        $pdf->Ln(3);
        $pdf->SetX(140);
        $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetX(140);
        $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
        $pdf->Ln(40);
    }else{
        $yyy = $pdf->GetY();
        $pdf->Cell(60, 0, '', 'B');
        $pdf->Ln(3);
        $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
    }
}
// Final del documento
while (ob_get_length()) {
    ob_end_clean();#Limpieza del buffer
}

$nombre_doc = utf8_decode("informeOrdenCompraAlmacenNª$numero_mov.pdf");
$pdf->Output(0,$nombre_doc,0);