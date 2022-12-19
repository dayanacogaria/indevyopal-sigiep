<?php
ini_set('max_execution_time', 0);
session_start();
ob_start();

require ('../Conexion/conexion.php');
require ('../fpdf/fpdf.php');

require_once ('../modelAlmacen/producto.php');
require_once ('../modelAlmacen/depreciacion.php');

if(!empty($_POST['txtPeridoF'])){
    $dep = new depreciacion();
    $pro = new producto();
    $compania = $_SESSION['compania'];
    $usuario  = $_SESSION['usuario'];
    $datosC   = $dep->tercero_informe($compania);

    $nombreCompania = $datosC[0];
    $nitCompania    = $datosC[1]." - ".$datosC[3];
    $ruta           = $datosC[2];

    $DteF = $mysqli->real_escape_string($_REQUEST['txtPeridoF']);

    $ult = $dep->separarObjeto("/", $DteF);

    $fechaInicial = "1900-01-01";
    $fechaFinal   = $dep->ultimoDia($ult[1], $ult[0]);

    $perIni       = new DateTime($fechaFinal);

    $res_p = $dep->encontrarDepreciacionProductosPeriodo($fechaInicial, $fechaFinal);

    $pros = array();
    while($row = $res_p->fetch_row()){
        $id_unico = $row[0];
        $nombre   = $row[1];
        $codigo   = $row[2];
        $pros[]   = $id_unico.",".$nombre.",".$codigo;
    }

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
            global $DteI;
            global $DteF;
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
            $this->Cell(230, 5,utf8_decode("RELACIÓN DE BIENES DEPRECIACIADOS"),0,0,'C');
            $this->Ln(5);

            $this->SetX(35);
            $this->Cell(230, 5,"HASTA EL PERIODO $DteF",0,0,'C');
            $this->Ln(10);

            $this->SetFont('Arial','B',8);
            $this->Cell(20,5,'CODIGO','LTR',0,'C');
            $this->Cell(20,5,'SERIE','LTR',0,'C');
            $this->Cell(20,5,'PLACA','LTR',0,'C');
            $this->Cell(100,5,'NOMBRE','LTR',0,'C');
            $this->Cell(20,5,'FECHA','LTR',0,'C');
            $this->Cell(10,5,'VIDA','LTR',0,'C');
            $this->Cell(30,5,'VALOR','LTR',0,'C');
            $this->Cell(30,5,'ACUMULADO','LTR',0,'C');
            $this->Ln(5);
            $this->Cell(20,5,'PRODUCTO','LBR',0,'C');
            $this->Cell(20,5,'SISTEMA','LBR',0,'C');
            $this->Cell(20,5,'ANTERIOR','LBR',0,'C');
            $this->Cell(100,5,'PRODUCTO','LBR',0,'C');
            $this->Cell(20,5,utf8_decode('ADQUISICIÓN'),'LBR',0,'C');
            $this->Cell(10,5,'UTIL','LBR',0,'C');
            $this->Cell(30,5,'ENTRADA','LBR',0,'C');
            $this->Cell(30,5,'DETERIORO','LBR',0,'C');
            $this->Ln(5);
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

    $pdf = new PDF('L','mm','Letter');
    $nb  = $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);

    $totalV = 0;
    $totalD = 0;

    for ($i = 0; $i < count($pros); $i++) {
        $objPro   = $dep->separarObjeto(",",$pros[$i]);
        $id_unico = $objPro[0]; $nombre = $objPro[1]; $codigo = $objPro[2]; $serie = $pro->obtnerCodigoProducto($id_unico);
        $placa    = $pro->obtnerCodigoProductoPlaca($id_unico);             $fecha = $dep->obtnerFechaAquisicion($id_unico);
        $vida     = (int) $pro->obtnerVidaUtil($id_unico); $valorPro = $pro->obtnerValorEntrada($id_unico);
        $valorD   = $dep->obtnerValorAcumuladoDrp($id_unico, $fechaInicial, $fechaFinal);
        if(empty($fecha)){
            $fecha    = $pro->obtnerFechaEntrada($id_unico);
        }
        $totalV += $valorPro; $totalD += $valorD;

        $pdf->SetFont('Arial','',8);
        $pdf->SetAligns(array('R','R','R','L','R','C','R','R'));
        $pdf->SetWidths(array(20,20,20,100,20,10,30,30));
        $pdf->Row(array($codigo, $serie, $placa, $nombre, $fecha, $vida,"$".number_format($valorPro,2,',','.'), "$".number_format($valorD,2,',','.')));
        $pdf->Ln(5);
    }
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'TOTAL',0,0,'R');
    $pdf->Cell(30,5,"$".number_format($totalV,2,',','.'),0,0,'R');
    $pdf->Cell(30,5,"$".number_format($totalD,2,',','.'),0,0,'R');

    while (ob_get_length()) {
      ob_end_clean();
    }

    $pdf->Output(0,"InformeRelacionProductosDepreciacion.pdf",0);
}