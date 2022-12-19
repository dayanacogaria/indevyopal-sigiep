<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 08/06/2017
 * Time: 9:51 AM
 *
 * inf_salida_almacen.php
 * Archivo para generar el informe de entrada de almacen
 * @package Almacen
 * @param String $mov Id de movimiento
 * @version $Id: inf_salida_almacen.php 001 2017-06-08 Alexander Numpaque$
 */

header("Content-Type: text/html;charset=utf-8");
session_start();
@ob_start();
//Archivos adjuntos
require '../fpdf/fpdf.php';
require '../Conexion/conexion.php';
require '../numeros_a_letras.php';
require_once ('../modelAlmacen/movimiento.php');
require_once("../Conexion/ConexionPDO.php");
$con    = new ConexionPDO();  
//Captura de variables
$mov = $_GET['mov'];
$compania = $_SESSION['compania'];
//Array para igualar los numeros de meses
$meses = array('no','01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
$movimiento = new mov();
//Consulta para obtener los datos de la compañia
$rowC = $movimiento->data_compania($compania);
list($razonSocial, $tipoIdent, $numeroIdent, $ruta) = array($rowC[0], $rowC[1], $rowC[2], $rowC[3]);//Razon social, tipo de identificación, numero de identificación, Ruta de logo
//Consulta para obtener los datos del movimiento
$rowMov = $movimiento->data_movimiento($mov);
$id_mov = $rowMov[0]; $tipo_mov = $rowMov[1]; $numero_mov = $rowMov[2]; $dia_letras = $rowMov[3]; $n_dia = $rowMov[4];$n_mes = $rowMov[5]; $anno = $rowMov[6];
$ciudad = $rowMov[7]; $tipo_doc_aso = $rowMov[8]; $tercero = $rowMov[10]; $dependencia = $rowMov[11]; $descripcion = $rowMov[12]; $observaciones = $rowMov[13]; $id_tipo_mov = $rowMov[14];
$tercero2 = $rowMov[15]; $id_tercero2 = $rowMov[16]; $centro_costo = $rowMov[17];
//Consulta para obtener el detalle asociado de este movimiento, el cual a de ser la entrada de almacen
//dta hace referencia al detalle asociado, mov_a hace referencia al movimiento asociado
$rowD = $movimiento->data_asociado($id_mov);
$id_aso = $rowD[0]; $num_aso = $rowD[1]; $dia_l_aso = $rowD[2]; $dia_n_aso = $rowD[3]; $mes_aso = $rowD[4]; $anno_aso = $rowD[5];

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
            $x = $this->GetX();
            $y = $this->GetY();
            //Dibujamos el borde
            $this->Rect($x, $y, $w, $h, $style);
            //Imprimimos el texto
            $this->MultiCell($w,4,$data[$i],'LTR', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h - 5);
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
            $this->Rect(0, 0, 0, 0, $style);
            //Imprimimos el texto
            /** @var String $fill */
            $this->MultiCell($w,4,$data[$i],'', $a, $fill);
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
        global $razonSocial;    #Nombre de compañia
        global $tipoIdent;      #Tipo de identificación
        global $numeroIdent;    #Nombre de comprobante
        global $ruta;           #Ruta de logo
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
        $this->SetFont('Arial','B',9);
        $this->Cell(200,5,utf8_decode(strtoupper($tipoIdent).':'." ".$numeroIdent),0,0,'C');
        #Tipo de comprobante y número de comprobante
        $this->Ln(5);
        $this->SetFont('Arial','B',10);
        $this->Cell(200,5,utf8_decode(ucwords(strtoupper($tipo_mov." ".'Nº')))." ".$numero_mov,0,0,'C');
        $this->Ln(5);
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

$pdf = new PDF_Mc_Table('P', 'mm', 'Letter');       #Creación del objeto pdf
$nb=$pdf->AliasNbPages();       #Objeto de número de pagina
$pdf->AddPage();                #Agregar página
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetWidths(array(35, 80, 45, 40));
$pdf->SetAligns(array('R', 'L', 'R', 'L'));
$pdf->Row_none(array('FECHA:', strtoupper("$ciudad $dia_letras, $n_dia $meses[$n_mes] $anno"), utf8_decode('CENTRO COSTO:'), $centro_costo));
$pdf->Ln(5);
$pdf->SetWidths(array(35, 165));
$pdf->SetAligns(array('R', 'L',));
$pdf->Row_none(array(utf8_decode('ENTRADA ALMACEN:'), utf8_decode(strtoupper("$num_aso del $dia_l_aso, $dia_n_aso $meses[$mes_aso] $anno_aso"))));
$pdf->Ln(5);
$pdf->SetWidths(array(35, 80, 45, 40));
$pdf->SetAligns(array('R', 'L', 'R', 'L'));
$pdf->Row_none(array('SOLICITANTE:', utf8_decode(mb_strtoupper($tercero)), utf8_decode('DEPENDENCIA:'), utf8_decode(mb_strtoupper($dependencia))));
$pdf->Ln(5);
$pdf->SetWidths(array(35, 165));
$pdf->SetAligns(array('R', 'L', 'R', 'L'));
$pdf->Row_none(array(utf8_decode('DESCRIPCIÓN:'), utf8_decode(ucfirst(strtolower($descripcion)))));
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 5, utf8_decode('Nª'), 1, 0,'C');
$pdf->Cell(60, 5, utf8_decode('PLAN INV.'), 1, 0, 'C');
$pdf->Cell(10, 5, 'CANT', 1, 0, 'C');
$pdf->Cell(40, 5, 'VALOR UNITARIO', 1, 0, 'C');
$pdf->Cell(40, 5, 'VALOR IVA', 1, 0, 'C');
$pdf->Cell(40, 5, 'VALOR TOTAL', 1, 0, 'C');
$pdf->Ln(5);
$devoltivos = array();
$sqlP = "SELECT DISTINCT dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva, dta.cantidad, pni.tipoinventario
FROM gf_detalle_movimiento dtm
LEFT JOIN gf_plan_inventario pni    ON pni.id_unico = dtm.planmovimiento
LEFT JOIN gf_detalle_movimiento dta ON dta.id_unico = dtm.detalleasociado
LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
WHERE  dtm.movimiento = $id_mov";
$resultP = $mysqli->query($sqlP);
$a = 0; $valorTU = 0; $valorTI = 0; $valorTAA = 0;
while ($rowP = mysqli_fetch_row($resultP)) {
    $a++;
    $valorT = ($rowP[2] * $rowP[3]) + $rowP[4];
    $valorTA = number_format($valorT, 2, ',' , '.');
    $valorA = number_format($rowP[3], 2, ',', '.');
    $valorI = number_format($rowP[4], 2, ',', '.');
    $valorTU += $rowP[3]; $valorTI += $rowP[4]; $valorTAA += $valorT;
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetWidths(array(10, 60, 10, 40, 40, 40));
    $pdf->SetAligns(array('C', 'L', 'C', 'R', 'R', 'R'));
    $pdf->Row(array($a, utf8_decode($rowP[1]), $rowP[2], $valorA, $valorI, $valorTA));
    $pdf->Ln(5);
    if($rowP[6] == 2){
        $devoltivos[] = $rowP[0];
    }
}
$pdf->SetX(130);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 5, 'TOTAL:', 1, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(array(40));
$pdf->SetAligns(array('R'));
$pdf->Row(array(number_format($valorTAA, 2, ',', '.')));
$pdf->Ln(10);
if(count($devoltivos) > 0){
    $xxx = 0;
    $yyy = 0;
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(200, 5,'LISTADO DE PROPIEDAD PLANTA Y EQUIPO', 1, 0, 'C');
    $pdf->Ln(5);
    $pdf->SetAligns(array('C', 'C', 'C', 'C'));
    $pdf->SetWidths(array(70, 20, 30, 80));
    $pdf->Row(array('ELEMENTO PLAN', 'PLACA', 'VALOR', utf8_decode('DESCRIPCIÓN')));
    $pdf->Ln(5);
    for ($i = 0; $i < count($devoltivos); $i++) {
        $rowDet = $movimiento->data_producto($devoltivos[$i]);
        foreach ($rowDet as $rowDD) {
            $xxx += $rowDD[3]; 
            #*** BUSCAR ESPECIFICACIONES DEL PRODUCTO;
            $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie 
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                    LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                    LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                    WHERE  mpr.detallemovimiento = ".$devoltivos[$i]." AND pre.fichainventario != 6");
            $descripcion = "";
            for ($j = 0; $j < count($vp); $j++) {
                $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
            }
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetAligns(array('L', 'R', 'R', 'L'));
            $pdf->SetWidths(array(70, 20, 30, 80));
            $pdf->Row(array(utf8_decode($rowDD[1]), $rowDD[2], number_format($rowDD[3], 2, ',', '.'), utf8_decode($descripcion)));
            $pdf->Ln(5);
        }
    }
    /*$pdf->SetFont('Arial', 'B', 9);
    $pdf->SetAligns(array('C', 'R', 'R'));
    $pdf->SetWidths(array(90, 30));
    $pdf->Row(array('TOTAL', number_format($xxx, 2, ',', '.')));
    $pdf->Ln(5);*/
}
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetAligns(array('R', 'L'));
$pdf->SetWidths(array(35, 165));
$pdf->Row(array(utf8_decode('OBSERVACIONES:'), utf8_decode(ucfirst(strtolower($observaciones)))));
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetAligns(array('R', 'L'));
$pdf->SetWidths(array(35, 165));
$pdf->Row(array(utf8_decode('VALOR EN LETRAS:'), utf8_decode(numtoletras($valorTAA))));
$pdf->Ln(15);
$yy1 = $pdf->GetY();
$data_firmas = $movimiento->data_firmas($id_tipo_mov);
$xxx = 0;
foreach($data_firmas as $row_firma){
    if($xxx == 0){
        $yyy = $yy1;
    }
    $xxx++;
    if($xxx % 2 == 0){
        
        $pdf->SetXY(140, $yyy);
        $pdf->Cell(190, 2, utf8_decode($row_firma[4]), 0, 0, 'L');
        $pdf->Ln(20);
        $pdf->SetXY(140, $yyy+20);
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
        $pdf->Cell(190, 2, utf8_decode($row_firma[4]), 0, 0, 'L');
        $pdf->Ln(20);
        $pdf->Cell(60, 0, '', 'B');
        $pdf->Ln(3);
        $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
    }
}
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(200,5,utf8_decode("HE RECIBIDO A SATISFACCIÓN LOS ELEMENTOS DESCRITOS ANTERIORMENTE"),0,0,'C');
#Final del documento
while (ob_get_length()) {
    ob_end_clean();#Limpieza del buffer
}
#Salida del documento
$nombre_doc = utf8_decode("informeSalidaAlmacenNª$numero_mov.pdf");
$pdf->Output(0,$nombre_doc,0);