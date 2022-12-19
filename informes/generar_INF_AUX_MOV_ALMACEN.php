<?php
session_start();
ob_start();
require'../fpdf/fpdf.php';
require_once("../Conexion/conexion.php");
$compania = $_SESSION['compania'];

$elini          = $mysqli->real_escape_string(''.$_POST["sltEin"].'');
$elfin          = $mysqli->real_escape_string(''.$_POST["sltEfn"].'');
$movini         = $mysqli->real_escape_string(''.$_POST["sltmovi"].'');
$movfin         = $mysqli->real_escape_string(''.$_POST["sltmovf"].'');
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$str_ini        = "SELECT    MIN(gmv.fecha), DATE_FORMAT(MIN(gmv.fecha), '%d/%m/%Y')
                   FROM      gf_movimiento AS gmv
                   LEFT JOIN gf_tipo_movimiento AS gtm ON gmv.tipomovimiento = gtm.id_unico
                   WHERE     gtm.clase    = 2
                   AND       gmv.compania = $compania";
$res_ini        = $mysqli->query($str_ini);
$row_ini        = mysqli_fetch_row($res_ini);
$fecha1         = $row_ini[0];
$fechaini       = $row_ini[1];
$fecha_div      = explode("/", $fechafin);
$anio2          = $fecha_div[2];
$mes2           = $fecha_div[1];
$dia2           = $fecha_div[0];
$fecha2         = "$anio2-$mes2-$dia2";
$hoy            = date('d-m-Y');
$hoy            = trim($hoy, '"');
$fecha_div      = explode("-", $hoy);
$anioh          = $fecha_div[2];
$mesh           = $fecha_div[1];
$diah           = $fecha_div[0];
$hoy            = $diah.'/'.$mesh.'/'.$anioh;
$mov1   = "SELECT nombre FROM gf_clase WHERE id_unico = $movini";
$movi1  = $mysqli->query($mov1);
$filaM1 = mysqli_fetch_row($movi1);
$mv1    = utf8_decode($filaM1[0]);

$mov2   = "SELECT nombre FROM gf_clase WHERE id_unico = $movfin";
$movi2  = $mysqli->query($mov2);
$filaM2 = mysqli_fetch_row($movi2);
$mv2    = utf8_decode($filaM2[0]);

$strei = "SELECT codi FROM gf_plan_inventario WHERE id_unico = '$elini'";
$resi  = $mysqli->query($strei);
$rowei = mysqli_fetch_row($resi);
$nomei = $rowei[0];

$stref = "SELECT codi FROM gf_plan_inventario WHERE id_unico = '$elini'";
$resf  = $mysqli->query($stref);
$rowef = mysqli_fetch_row($resf);
$nomef = $rowef[0];


class PDF extends FPDF{
    var $widths;
    var $aligns;
    function SetWidths($w){
        //Set the array of column widths
        $this->widths=$w;
    }
    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns=$a;
    }
    function fill($f){
        //juego de arreglos de relleno
        $this->fill=$f;
    }
    function Row($data){
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++){
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect(0,0,0,0,$style);
            //Print the text
            $this->MultiCell($w,5,$data[$i],'',$a,$fill);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h-5);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace('\r','',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=='\n')
            $nb–;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=='\n'){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c=='')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }else
                $i++;
        }
        return $nl;
    }
    // Cabecera de página
    function Header(){
        global $nomcomp;
        global $tipodoc;
        global $numdoc;
        global $nomei;
        global $nomef;
        global $mv1;
        global $mv2;
        global $fechaini;
        global $fechafin;
        $this->SetFont('Arial','B',10);
        $this->SetY(10);
        $this->Cell(330,5,utf8_decode($nomcomp),0,0,'C');
        $this->setX(10);
        $this->SetFont('Arial','B',8);
        $this->Cell(330,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
        $this->Ln(5);
        $this->SetFont('Arial','',8);
        $this->Cell(330, 5,$tipodoc.': '.$numdoc,0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(330,10,utf8_decode('VERSIÓN SGC'),0,0,'R');
        $this->Ln(5);
        $this->SetFont('Arial','',8);
        $this->Cell(330,5,utf8_decode('AUXILIAR DE MOVIMIENTO DE ALMACEN'),0,0,'C');
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(330,10,utf8_decode('FECHA SGC'),0,0,'R');
        $this->Ln(3);
        $this->SetFont('Arial','',7);
        $this->Cell(330,5,utf8_decode('Entre Elementos '.$nomei.' y '.$nomef),0,0,'C');
        $this->Ln(3);
        $this->SetFont('Arial','',7);
        $this->Cell(335,5,utf8_decode('Tipos de movimiento '.$mv1.' a '.$mv2),0,0,'C');
        $this->Ln(3);
        $this->SetFont('Arial','',7);
        $this->Cell(332,5,utf8_decode('y Fechas '.$fechaini.' a '.$fechafin),0,0,'C');
        $this->Ln(5);
        $this->SetX(25);
        $this->Cell(22,9,utf8_decode(''),1,0,'C');
        $this->Cell(40,9,utf8_decode(''),1,0,'C');
        $this->Cell(51,9,utf8_decode(''),1,0,'C');
        $this->Cell(40,9,utf8_decode(''),1,0,'C');
        $this->Cell(30,9,utf8_decode(''),1,0,'C');
        $this->Cell(66,9,utf8_decode(''),1,0,'C');
        $this->Cell(66,9,utf8_decode(''),1,0,'C');
        $this->SetX(25);
        $this->Cell(22,9,utf8_decode('Fecha'),0,0,'C');
        $this->Cell(40,5,utf8_decode('Comprobante'),1,0,'C');
        $this->Cell(51,9,utf8_decode('Nombre del Tercero'),0,0,'C');
        $this->Cell(40,9,utf8_decode('Descripción'),0,0,'C');
        $this->Cell(30,9,utf8_decode('Valor U.'),0,0,'C');
        $this->Cell(66,5,utf8_decode('Cantidad'),1,0,'C');
        $this->Cell(66,5,utf8_decode('Valor'),1,0,'C');
        $this->Ln(5);
        $this->SetX(25);
        $this->Cell(22,4,utf8_decode(''),0,0,'C');
        $this->Cell(20,4,utf8_decode('Numero'),1,0,'C');
        $this->Cell(20,4,utf8_decode('Tipo'),1,0,'C');
        $this->Cell(51,4,utf8_decode(''),0,0,'C');
        $this->Cell(40,4,utf8_decode(''),0,0,'C');
        $this->Cell(30,4,utf8_decode(''),0,0,'C');
        $this->Cell(22,4,utf8_decode('Entrada'),1,0,'C');
        $this->Cell(22,4,utf8_decode('Salida'),1,0,'C');
        $this->Cell(22,4,utf8_decode('Saldo'),1,0,'C');
        $this->Cell(22,4,utf8_decode('Entrada'),1,0,'C');
        $this->Cell(22,4,utf8_decode('Salida'),1,0,'C');
        $this->Cell(22,4,utf8_decode('Saldo'),1,0,'C');
        $this->Ln(5);
        $this->SetX(25);
    }

    function Footer(){
        global $hoy;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(15);
        $this->Cell(25,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(70);
        $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(60);
        $this->Cell(30,10,utf8_decode('Usuario: admin'),0);
        $this->Cell(70);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}
// Creación del objeto de la clase heredada
$pdf    = new PDF('L','mm','Legal');


$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";
$cmp     = $mysqli->query($consulta);
$fila    = mysqli_fetch_array($cmp);
list($nomcomp, $tipodoc, $numdoc) = array(utf8_decode($fila[0]), utf8_decode($fila[3]), utf8_decode($fila[4]));

$nb = $pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();

list($codd, $totales, $valorA, $entrada, $salida, $totalent, $totalsal, $saldoT, $saldoTT, $ele) = array(0, 0, 0, "", "", 0, 0, 0, 0, 0);
$pdf->SetXY(25,43);
$pdf->SetX(25);
$pdf->SetFont('Arial','B',7);
$elementos = "SELECT DISTINCT dm.planmovimiento AS dmplan, CONCAT( pi.codi, ' - ', pi.nombre ) AS codele, pi.tipoinventario as tipoI
              FROM      gf_detalle_movimiento dm
              LEFT JOIN gf_movimiento m       ON dm.movimiento     = m.id_unico
              LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
              LEFT JOIN gf_tercero t          ON pi.compania       = t.id_unico
              LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento  = tm.id_unico
              LEFT JOIN gf_clase cl           ON tm.clase          = cl.id_unico
              WHERE     (dm.valor IS NOT NULL)
              AND       (tm.clase BETWEEN $movini AND $movfin)
              AND       (pi.id_unico BETWEEN $elini AND $elfin)
              AND       (tm.clase BETWEEN $movini AND $movfin)
              AND       (m.fecha BETWEEN '$fecha1' AND '$fecha2')
              AND       (m.compania  = $compania)
              AND       (pi.compania = $compania)
              ORDER BY  pi.codi ASC";
$elemento = $mysqli->query($elementos);
while ($filaELS = mysqli_fetch_row($elemento)){
    list($entA, $cantEA, $totalEA, $entradaA, $salA, $cantSA, $totalSA, $totalC, $totalV, $planm, $codele) = array(0, 0, 0, 0, 0, 0, 0, 0, 0, $filaELS[0], $filaELS[1]);
    $pdf->SetX(25);
    $pdf->SetFont('Arial','B',7);
    $pdf->SetWidths(array(150, 80, 80));
    $pdf->SetAligns(array("L", "L", "L"));
    $pdf->Row(array(utf8_decode('Elemento: '.$codele), 'Cantidad Inicial: '.$totalC, 'Valor Inicial: '.$totalV));
    $pdf->Ln(5);
    $xce = 0; $xve = 0;
    switch ($filaELS[2]) {
        case 1:
        case 3:
            $str = "SELECT   DATE_FORMAT(mov.fecha, '%d/%m/%Y'), mov.numero, tpm.sigla,
                              IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                              (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                              mov.descripcion, dtm.cantidad, ((dtm.valor ) * dtm.cantidad), tpm.clase, dtm.hora, dtm.valor
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_movimiento         as mov ON dtm.movimiento     = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento    as tpm ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_tercero            as ter ON mov.tercero        = ter.id_unico
                    WHERE     (dtm.planmovimiento = $filaELS[0])
                    AND       (tpm.clase BETWEEN  $movini   AND $movfin)
                    AND       (mov.fecha BETWEEN  '$fecha1' AND '$fecha2')
                    AND       (mov.compania = $compania)
                    GROUP BY  dtm.id_unico
                    ORDER BY  mov.fecha, dtm.hora, tpm.clase";
            $res    = $mysqli->query($str);
            $xsaldo = 0;
            $xvalor = 0;
            while($row = mysqli_fetch_row($res)){
                $xcantS = 0;
                $xcantE = 0;
                $valorE = 0;
                $valorS = 0;
                switch ($row[7]) {
                    case 2:
                    case 5:
                        $xcantE  = $row[5];
                        $valorE  = $row[6];
                        $xsaldo += $xcantE;
                        $xvalor += $valorE;
                        break;

                    case 3:
                    case 7:
                        $xcantS  = $row[5];
                        $valorS  = $row[6];
                        $xsaldo -= $xcantS;
                        $xvalor -= $valorS;
                        break;
                }
                $pdf->SetX(25);
                $pdf->SetFont('Arial','',7);
                $pdf->SetWidths(array(22, 20, 20, 51, 40, 30, 22, 22, 22, 22, 22, 22));
                $pdf->SetAligns(array("L", "R", "C", "L", 'L', "L", "C", "C", "C", "R", "R", "R"));
                $pdf->Row(array("$row[0] $row[8]", $row[1], $row[2], $row[3], ucwords(mb_strtolower($row[4])), number_format($row[9]), $xcantE, $xcantS, $xsaldo, number_format($valorE, 2, ',', '.'), number_format($valorS, 2, ',', '.'), number_format($xvalor, 2, ',', '.')));
                $pdf->Ln(5);
            }
            break;
        case 2:
        case 4:
            $str = "SELECT    GROUP_CONCAT(mpr.producto), DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha, mov.numero as num, tpm.sigla,
                              (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                              CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                              (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) as nomter,
                              mov.descripcion as des, dtm.cantidad as cant, ((dtm.valor) * dtm.cantidad) as valor, dtm.hora, dtm.valor
                    FROM      gf_movimiento_producto as mpr
                    LEFT JOIN gf_detalle_movimiento  as dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario     as pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento          as mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     as tpm ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_tercero             as ter ON mov.tercero           = ter.id_unico
                    WHERE     (dtm.planmovimiento = $filaELS[0])
                    AND       (tpm.clase          IN(2, 5))
                    AND       (mov.fecha BETWEEN '$fecha1' AND '$fecha2')
                    AND       (mov.compania = $compania)
                    AND       (pln.compania = $compania)
                    GROUP BY  dtm.id_unico
                    ORDER BY  tpm.clase, mov.numero, mov.fecha, mpr.producto, dtm.hora";
            $res = $mysqli->query($str);
            while($row = mysqli_fetch_row($res)){
                $xcsaldo  = 0;
                $xvsaldo  = 0;
                $xcantE   = $row[6];
                $xvalE    = $row[7];
                $xcsaldo += $xcantE;
                $xvsaldo += $xvalE;
                $pdf->SetX(25);
                $pdf->SetFont('Arial','',7);
                $pdf->SetWidths(array(22, 20, 20, 51, 40, 30, 22, 22, 22, 22, 22, 22));
                $pdf->SetAligns(array("L", "R", "C", "L", 'L', "L", "C", "C", "C", "R", "R", "R"));
                $pdf->Row(array("$row[1]", $row[2], $row[3], utf8_decode($row[4]), utf8_decode(ucwords(mb_strtolower($row[5]))), $row[9],  $xcantE, 0, $xcsaldo, number_format($xvalE, 2, ',', '.'), 0, number_format($xvsaldo, 2, ',', '.')));
                $pdf->Ln(5);
                $xstr = "SELECT DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha, mov.numero as num, tpm.sigla,
                              (IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                              CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                              (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) as nomter,
                              mov.descripcion as des, dtm.cantidad as cant, ((dtm.valor) * dtm.cantidad) as valor, dtm.hora, dtm.valor
                    FROM      gf_movimiento_producto as mpr
                    LEFT JOIN gf_detalle_movimiento  as dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario     as pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento          as mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     as tpm ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_tercero             as ter ON mov.tercero           = ter.id_unico
                    WHERE     (mpr.producto in($row[0]))
                    AND       (tpm.clase    IN(3, 7))
                    AND       (pln.id_unico = $filaELS[0])
                    AND       (mov.fecha    BETWEEN '$fecha1' AND '$fecha2')
                    AND       (pln.compania = $compania)
                    AND       (mov.compania = $compania)
                    GROUP BY  dtm.id_unico
                    ORDER BY  tpm.clase, mov.numero, mov.fecha, dtm.hora";
                $xsres = $mysqli->query($xstr);
                $xssl    = $xcsaldo;
                $xvalsd  = $xvsaldo ;
                while($xsrow = mysqli_fetch_row($xsres)){
                    $xcantS  = $xsrow[5];
                    $xssl   -= $xcantS;
                    $xsaldo  = $xssl;
                    $xsval   = $xsrow[6];
                    $xvalsd -=  $xsval;
                    $pdf->SetX(25);
                    $pdf->SetFont('Arial','',7);
                    $pdf->SetWidths(array(22, 20, 20, 51, 40, 30, 22, 22, 22, 22, 22, 22));
                    $pdf->SetAligns(array("L", "R", "C", "L", 'L', "L", "C", "C", "C", "R", "R", "R"));
                    $pdf->Row(array("$xsrow[0]", $xsrow[1], $xsrow[2],  utf8_decode($xsrow[3]), utf8_decode(ucwords(mb_strtolower($xsrow[4]))),$xsrow[8],0, $xcantS, $xssl, 0, number_format($xvalsd, 2, ',', '.')));
                    $pdf->Ln(5);
                }
            }
            break;
    }
}
ob_end_clean();
$pdf->Output(0,'Informe_Auxiliar_Mov_Almacen('.date('d/m/Y').').pdf',0);