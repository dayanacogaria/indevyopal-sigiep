<?php
ini_set('max_execution_time', 0);
session_start();
ob_start();

require ('../Conexion/conexion.php');
require ('../fpdf/fpdf.php');

require_once ('../modelAlmacen/producto.php');
require_once ('../modelAlmacen/depreciacion.php');

class PDF extends FPDF{
    var $widths;
    var $aligns;

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

    function Row($data){
        //Calculate the height of the row
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x,$y,$w,$h,$style);
            //Print the text

            $this->MultiCell($w, 5, $data[$i], 0, $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h-5);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw =&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s  = str_replace('\r','',$txt);
        $nb = strlen($s);
        if($nb > 0 and $s[$nb-1] == '\n')
            $nb–;
        $sep =-1;
        $i  = 0;
        $j  = 0;
        $l  = 0;
        $nl = 1;
        while($i < $nb){
            $c = $s[$i];
            if($c == '\n'){
                $i++;
                $sep =-1;
                $j = $i;
                $l = 0;
                $nl++;
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
                    $i = $sep+1;
                $sep =-1;
                $j = $i;
                $l = 0;
                $nl++;
            }else
                $i++;
            }
        return $nl;
    }

    #Funcón cabeza de la página
    function Header(){
        global $nombreCompania;
        global $nitCompania;
        global $numpaginas;
        global $per;
        global $ruta;
        global $productoI;
        global $productoF;
        global $depenI;
        global $depenF;
        $numpaginas=$this->PageNo();

        $this->SetFont('Arial','B',10);
        $this->SetY(10);
        if($ruta != ''){
            $this->Image('../'.$ruta,15,8,20);
        }

        $this->SetX(35);
        $this->Cell(230,5,utf8_decode($nombreCompania),0,0,'C');
        $this->Ln(5);

        $this->SetX(35);
        $this->Cell(230, 5,$nitCompania,0,0,'C');
        $this->Ln(5);

        $this->SetX(35);
        $this->Cell(230, 5,utf8_decode("INFORME DEVOLUTIVOS"),0,0,'C');
        $this->Ln(5);

        $this->SetX(35);
        $this->Cell(230, 5,"HASTA EL PERIODO $per",0,0,'C');
        $this->Ln(10);

        $this->SetFont('Arial','B',8);
        $this->Cell(20,5,'CODIGO','LTR',0,'C');
        $this->Cell(50,5,'NOMBRE','LTR',0,'C');
        $this->Cell(50,5,'DESCRIPCION','LTR',0,'C');
        $this->Cell(20,5,'PLACA','LTR',0,'C');
        $this->Cell(20,5,'FECHA','LTR',0,'C');
        $this->Cell(10,5,'CANT','LTR',0,'C');
        $this->Cell(30,5,'VALOR','LTR',0,'C');
        $this->Cell(30,5,'DETERIORO','LTR',0,'C');
        $this->Cell(30,5,'VALOR','LTR',0,'C');
        $this->Ln(5);
        $this->Cell(20,5,'CUENTA','LBR',0,'C');
        $this->Cell(50,5,'CUENTA','LBR',0,'C');
        $this->Cell(50,5,'PRODUCTO','LBR',0,'C');
        $this->Cell(20,5,'PRODUCTO','LBR',0,'C');
        $this->Cell(20,5,utf8_decode('ADQUISICIÓN'),'LBR',0,'C');
        $this->Cell(10,5,'IDAD','LBR',0,'C');
        $this->Cell(30,5,'UNITARIO','LBR',0,'C');
        $this->Cell(30,5,'ACUMULADO','LBR',0,'C');
        $this->Cell(30,5,'RESIDUAL','LBR',0,'C');
        $this->Ln(5);;
    }

    function Footer(){
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(20);
        $this->Cell(25,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
        $this->Cell(40);
        $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(40);
        $this->Cell(30,10,utf8_decode('Usuario: '.$usuario),0);
        $this->Cell(40);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}

$dep = new depreciacion();
$pro = new producto();

$ano = $_SESSION['anno'];
$per = $_REQUEST['txtPeriodoX'];

$compania = $_SESSION['compania'];
$usuario  = $_SESSION['usuario'];
$datosC   = $dep->tercero_informe($compania);

$nombreCompania = $datosC[0];
$nitCompania    = $datosC[1]." - ".$datosC[3];
$ruta           = $datosC[2];

$pdf = new PDF('L','mm','Letter');
$nb  = $pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

$ff    = explode("/", $_REQUEST['txtPeriodoX']);
$dia   = date("d", (mktime(0, 0, 0, $ff[1] + 1, 1, $ff[0]) - 1));
$fecha = "$ff[0]-$ff[1]-$dia";

$str = "SELECT plan_inventario, tipo_movimiento, cuenta_debito FROM gf_configuracion_almacen WHERE parametrizacion_anno = $ano";
$res = $mysqli->query($str);
while ($value = mysqli_fetch_row($res)) {
    $sql = "SELECT id_unico, nombre FROM gf_plan_inventario WHERE predecesor = $value[0]";
    $rst = $mysqli->query($sql);
    if(mysqli_num_rows($rst) > 0){
        while($item = mysqli_fetch_row($rst)){
            $stl = "SELECT    mpr.producto FROM gf_movimiento_producto AS mpr
                    LEFT JOIN gf_detalle_movimiento    AS dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento            AS mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_plan_inventario       AS pln ON dtm.planmovimiento    = pln.id_unico
                    WHERE     dtm.planmovimiento = $item[0]
                    AND       mov.tipomovimiento = $value[1]
                    AND       pln.tipoinventario IN (2, 3, 4)";
            $rt1 = $mysqli->query($stl);
            if(mysqli_num_rows($rt1) > 0){
                while($row = mysqli_fetch_row($rt1)){
                    $strC = "SELECT codi_cuenta, nombre FROM gf_cuenta WHERE id_unico = $value[2]";
                    $resC = $mysqli->query($strC);
                    $rowC = mysqli_fetch_row($resC);
                    $strP = "SELECT descripcion, valor, DATE_FORMAT(fecha_adquisicion, '%d/%m/%Y') FROM gf_producto WHERE id_unico = $row[0]";
                    $resP = $mysqli->query($strP);
                    $rowP = mysqli_fetch_row($resP);
                    $strE = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row[0] AND fichainventario = 6";
                    $resE = $mysqli->query($strE);
                    $rowE = mysqli_fetch_row($resE);
                    $pdf->SetFont('Arial','',8);
                    $pdf->SetWidths(array(20, 50, 50, 20, 20, 10, 30, 30, 30));
                    $pdf->SetAligns(array("L", "L", "L", "R", "C", "C", "R", "R", "R"));
                    $xxx  = 0;
                    $strX = "SELECT valor FROM ga_depreciacion WHERE producto = $row[0] AND fecha_dep >= '$fecha'";
                    $resX = $mysqli->query($strX);
                    if(mysqli_num_rows($resX) > 0){
                        while($rowX = mysqli_fetch_row($resX)){
                            $xxx += $rowX[0];
                        }
                    }
                    $valorR = $rowP[1] - $xxx;
                    $pdf->Row(array($rowC[0], utf8_decode($rowC[1]), utf8_decode($item[1]), $rowE[0], $rowP[2], 1, number_format($rowP[1], 2), number_format($xxx, 2), number_format($valorR, 2)));
                    $pdf->Ln(5);
                }
            }
        }
    }
}

while (ob_get_length()) {
  ob_end_clean();
}
$pdf->Output(0,"InformeRelacionProductosDepreciacionDigitos.pdf",0);