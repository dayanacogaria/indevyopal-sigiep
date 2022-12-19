<?php
/**
 * Created by Alexander Numpaque.
 * User: Alexander
 * Date: 08/06/2017
 * Time: 9:50 AM
 *
 * inf_requisiciones.php
 * Archivo para generar el informe de entrada de almacen
 * @package Almacen
 * @param String $mov Id de movimiento
 * @version $Id: inf_requisiciones.php 001 2017-06-08 Alexander Numpaque$
 */
header("Content-Type: text/html;charset=utf-8");
session_start();
@ob_start();
//Archivos adjuntos
require '../fpdf/fpdf.php';
require '../Conexion/conexion.php';
require '../numeros_a_letras.php';
//Captura de variables
$mov = $_GET['mov'];
$compania = $_SESSION['compania'];
//Array para igualar los numeros de meses
$meses = array('no','01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
//Consulta para obtener los datos de la compañia
$sqlC = "SELECT ter.razonsocial, tp.nombre, CONCAT(ter.numeroidentificacion,' - ',ter.digitoverficacion), ter.ruta_logo FROM gf_tercero ter
LEFT JOIN gf_tipo_identificacion tp ON tp.id_unico = ter.tipoidentificacion 
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonSocial = $rowC[0]; $tipoIdent = $rowC[1]; $numeroIdent = $rowC[2]; $ruta = $rowC[3];//Razon social, tipo de identificación, numero de identificación, Ruta de logo
//Consulta para obtener los datos del movimiento
$sqlMov = "SELECT mov.id_unico, tpm.nombre, mov.numero, CONCAT(ELT(WEEKDAY(mov.fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
DATE_FORMAT(mov.fecha,'%d'), DATE_FORMAT(mov.fecha,'%m'), DATE_FORMAT(mov.fecha,'%Y'), cid.nombre, mov.tipo_doc_sop, mov.numero_doc_sop, 
IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
(ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', dpt.nombre, mov.descripcion, mov.observaciones, mov.tipomovimiento
FROM gf_movimiento mov
LEFT JOIN gf_tipo_movimiento tpm ON tpm.id_unico = mov.tipomovimiento
LEFT JOIN gf_ciudad cid ON cid.id_unico = mov.lugarentrega 
LEFT JOIN gf_tercero ter ON ter.id_unico = mov.tercero
LEFT JOIN gf_dependencia dpt ON dpt.id_unico = mov.dependencia
WHERE md5(mov.id_unico) = '$mov'";
$resultMov = $mysqli->query($sqlMov);
$rowMov = mysqli_fetch_row($resultMov);
$id_mov = $rowMov[0]; $tipo_mov = $rowMov[1]; $numero_mov = $rowMov[2]; $dia_letras = $rowMov[3]; $n_dia = $rowMov[4];$n_mes = $rowMov[5]; $anno = $rowMov[6];
$ciudad = $rowMov[7]; $tipo_doc_aso = $rowMov[8]; $tercero = $rowMov[10]; $dependencia = $rowMov[11]; $descripcion = $rowMov[12]; $observaciones = $rowMov[13]; $id_tipo_mov = $rowMov[14];
//Consulta para obtener el detalle asociado de este movimiento, el cual a de ser la entrada de almacen
//dta hace referencia al detalle asociado, mov_a hace referencia al movimiento asociado
$sqlD = "SELECT mov_a.id_unico, mov_a.numero, CONCAT(ELT(WEEKDAY(mov_a.fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
DATE_FORMAT(mov_a.fecha, '%d'), DATE_FORMAT(mov_a.fecha, '%m'), DATE_FORMAT(mov_a.fecha, '%Y')
FROM gf_detalle_movimiento dtm
LEFT JOIN gf_detalle_movimiento dta ON dta.id_unico   = dtm.detalleasociado 
LEFT JOIN gf_movimiento mov_a       ON mov_a.id_unico = dta.movimiento  
WHERE dtm.movimiento = $id_mov";
$resultD = $mysqli->query($sqlD);
$rowD = mysqli_fetch_row($resultD);
$id_aso = $rowD[0]; $num_aso = $rowD[1]; $dia_l_aso = $rowD[2]; $dia_n_aso = $rowD[3]; $mes_aso = $rowD[4]; $anno_aso = $rowD[5];
if(!empty($tipo_doc_aso)) {
    switch ($tipo_doc_aso){
        case '1':
            $nom_o = "FACTURA Nº:";
            $cod_obj = $rowMov[9];
            break;
        case '2':
            $nom_o = "REMISIÓN Nº:";
            $cod_obj = $rowMov[9];
            break;
        case '3':
            $nom_o = "";
            $cod_obj = "";
            break;
    }
}else{
    $nom_o = "";
    $cod_obj = "";
}
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
            /** @var String $style */
            $this->Rect($x, $y, $w, $h, $style);
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
        $this->SetFont('Arial','',9);
        $this->Cell(200,5,utf8_decode($tipoIdent.':'.PHP_EOL.$numeroIdent),0,0,'C');
        #Tipo de comprobante y número de comprobante
        $this->Ln(5);
        $this->SetFont('Arial','B',10);
        $this->Cell(200,5,utf8_decode(ucwords(strtoupper($tipo_mov.PHP_EOL))).'Nro:'.PHP_EOL.$numero_mov,0,0,'C');
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

$pdf = new PDF_Mc_Table('P', 'mm', 'Letter');		#Creación del objeto pdf
$nb=$pdf->AliasNbPages();		#Objeto de número de pagina
$pdf->AddPage();				#Agregar página
$pdf->Ln(5);
$pdf->Cell(200, 30, '', 1, 0);
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 5, utf8_decode('FECHA DE REQ.:'), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(100, 5, strtoupper("$ciudad $dia_letras, $n_dia $meses[$n_mes] $anno"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 5, utf8_decode($nom_o), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(40, 5, $cod_obj, 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 5, 'SOLICITANTE:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(100, 5, utf8_decode(mb_strtoupper($tercero)), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 5, utf8_decode('DEPENDENCIA:'), 0, 0, 'R');
$pdf->SetFont('Arial','',9);
$pdf->Cell(40, 5, utf8_decode(mb_strtoupper($dependencia)), 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 5, utf8_decode('DESCRIPCIÓN:'), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(165 , 5,utf8_decode(ucwords(mb_strtolower($descripcion))), 0, 'L');
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 5, utf8_decode('Nª'), 1, 0,'C');
$pdf->Cell(60, 5, utf8_decode('PLAN INV.'), 1, 0, 'C');
$pdf->Cell(10, 5, 'CANT', 1, 0, 'C');
$pdf->Cell(40, 5, 'VALOR UNITARIO', 1, 0, 'C');
$pdf->Cell(40, 5, 'VALOR IVA', 1, 0, 'C');
$pdf->Cell(40, 5, 'VALOR TOTAL', 1, 0, 'C');
$sqlP = "SELECT dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva 
FROM gf_detalle_movimiento dtm
LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
WHERE  dtm.movimiento = $id_mov";
$resultP = $mysqli->query($sqlP);
$a = 0;
$valorTU = 0; $valorTI = 0; $valorTAA = 0;
while ($rowP = mysqli_fetch_row($resultP)) {
    $a++;
    $valorT = ($rowP[2] * $rowP[3]) + $rowP[4];
    $valorTA = number_format($valorT, 2, ',' , '.');
    $valorA = number_format($rowP[3], 2, ',', '.');
    $valorI = number_format($rowP[4], 2, ',', '.');
    $valorTU += $rowP[3]; $valorTI += $rowP[4]; $valorTAA += $valorT;
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetWidths(array(10, 60, 10, 40, 40, 40));
    $pdf->SetAligns(array('C', 'L', 'C', 'R', 'R', 'R'));
    $pdf->Row(array($a, $rowP[1], $rowP[2], $valorA, $valorI, $valorTA));
}
$pdf->Ln(5);
$pdf->SetX(130);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 5, 'TOTAL:', 1, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(array(40));
$pdf->SetAligns(array('R'));
$pdf->Row(array(number_format($valorTAA, 2, ',', '.')));
$pdf->Ln(10);
$pdf->Cell(200, 15, '', 1, 0);
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 5, utf8_decode('OBSERVACIONES:'), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(165 , 5,utf8_decode(ucwords(mb_strtolower($observaciones))), 0, 'L');
$pdf->Ln(10);
$pdf->Cell(200, 15, '', 1, 0);
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 5, utf8_decode('VALOR EN LETRAS:'), 0, 0, 'R');
$pdf->MultiCell(165 , 5,utf8_decode(numtoletras($valorTAA)), 0, 'L');
$sqlR = "SELECT CONCAT(ter.nombreuno, ' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos), car.nombre, ti.nombre, CONCAT(ter.numeroidentificacion, ' ',ter.digitoverficacion), UPPER(tpr.nombre)
FROM      gf_tipo_movimiento tpc 
LEFT JOIN gf_tipo_documento tpd         ON tpd.id_unico       = tpc.tipo_documento
LEFT JOIN gf_responsable_documento doc  ON doc.tipodocumento  = tpc.tipo_documento
LEFT JOIN gf_tipo_responsable tpr       ON tpr.id_unico       = doc.tiporesponsable 
LEFT JOIN gg_tipo_relacion tprl         ON doc.tipo_relacion  = tprl.id_unico 
LEFT JOIN gf_tercero ter                ON doc.tercero        = ter.id_unico 
LEFT JOIN gf_cargo_tercero cter         ON cter.tercero       = ter.id_unico 
LEFT JOIN gf_cargo car                  ON cter.cargo         = car.id_unico 
LEFT JOIN gf_tipo_identificacion ti     ON ti.id_unico        = ter.tipoidentificacion 
WHERE     tpc.id_unico = $id_tipo_mov   AND tpc.compania      = $compania
ORDER BY  doc.tipodocumento";
#Ejecutamos la consulta
$resultF= $mysqli->query($sqlR);
$resultF1= $mysqli->query($sqlR);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop-$altofinal;
$pdf->Ln(10);
#Carga de array $firma con los valores de consulta
$c=0;
while($cons = mysqli_fetch_row($resultF1)){
    $c++;
}
$tfirmas = ($c/2) * 33;
if($tfirmas>$altofirma) {
    $pdf->AddPage();
}
$xt=10;
while($firma = mysqli_fetch_row($resultF)){
    if($xt<50){
        #Construcción de linea firma
        $xm = 10; $pdf->setX($xm);
        $pdf->SetFont('Arial', 'B', 9);
        #Varibles x,y
        $x = $pdf->GetX(); $y = $pdf->GetY();
        #Salto de linea
        $pdf->Ln(7); $pdf->setX($xm);
        #Salto de linea
        $pdf->Ln(15); $pdf->setX($xm);
        #Linea para firma
        $pdf->Cell(60,0,'',1);
        $pdf->Ln(3); $pdf->setX($xm);
        $pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
        $pdf->Ln(5);
        #Impresión de responsable de documento
        $pdf->Cell(190,2,utf8_decode($firma[4]),0,0,'L');
        #Salto de linea
        $pdf->Ln(3);
        #Obtención de alto final
        $x2 = $pdf->GetX();
        #Posición final de firma 2
        $pdf->Ln(0); $xt = 120;
    }else{
        $xn = 120; $pdf->SetY($y);
        #Construcción de linea firma
        $pdf->SetFont('Arial', 'B', 9);
        #Varibles x,y
        $x = $pdf->GetX();
        #alto inicial
        $y = $pdf->GetY();
        #Salto de linea
        $pdf->Ln(7); $pdf->setX($xn); $pdf->Ln(15); $pdf->setX($xn);
        $pdf->Cell(60,0,'',1);
        $pdf->Ln(3); $pdf->setX($xn);
        $pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
        $pdf->Ln(5); $pdf->setX($xn);
        $pdf->Cell(190,2,utf8_decode($firma[4]),0,0,'L');
        #Obtención de alto final
        $x2 = $pdf->GetX();
        #Posición del ancho
        $posicionY = $y-20;
        #Ubicación firma 2
        $pdf->SetXY($x2,$posicionY);
        #Posición final de firma
        $xt = 0;
    }
}
#Final del documento
while (ob_get_length()) {
    ob_end_clean();#Limpieza del buffer
}
#Salida del documento
$nombre_doc = utf8_decode("informeRequicisionNª$numero_mov.pdf");
$pdf->Output(0,$nombre_doc,0);