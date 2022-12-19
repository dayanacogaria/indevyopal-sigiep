<?php
ini_set('max_execution_time', 0);
session_start();
ob_start();

require ('../Conexion/conexion.php');
require ('../fpdf/fpdf.php');

require_once ('../modelAlmacen/producto.php');
require_once ('../modelAlmacen/depreciacion.php');

if(!empty($_POST['txtPeridoF']) && !empty($_POST['sltProductoInicial']) &&
   !empty($_POST['sltProductoFinal'])){
    $dep = new depreciacion();
    $pro = new producto();
    $compania = $_SESSION['compania'];
    $usuario  = $_SESSION['usuario'];
    $datosC   = $dep->tercero_informe($compania);

    $nombreCompania = $datosC[0];
    $nitCompania    = $datosC[1]." - ".$datosC[3];
    $ruta           = $datosC[2];

    $DteI = $mysqli->real_escape_string($_REQUEST['txtPeridoI']);
    $DteF = $mysqli->real_escape_string($_REQUEST['txtPeridoF']);

    $proI = $mysqli->real_escape_string($_REQUEST['sltProductoInicial']);
    $proF = $mysqli->real_escape_string($_REQUEST['sltProductoFinal']);

    $prm = $dep->separarObjeto("/", $DteI);
    $ult = $dep->separarObjeto("/", $DteF);

    $fechaInicial = $dep->primerDia($prm[1], $prm[0]);
    $fechaFinal   = $dep->ultimoDia($ult[1], $ult[0]);

    $res_p = $dep->encontrarDepreciacionProductos("1990-01-01", $fechaFinal, $proI, $proF);

    $pros = array();
    while($row = $res_p->fetch_row()){
        $id_unico = $row[0];
        $nombre   = $row[1];
        $pros[]   = $id_unico.",".$nombre;
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

        function NbLines($w,$txt){
            //Computes the number of lines a MultiCell of width w will take
            $cw=&$this->CurrentFont['cw'];
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
            while($i<$nb){
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
            global $fechaInicial;
            global $fechaFinal;
            global $ruta;
            global $productoI;
            global $productoF;
            global $depenI;
            global $depenF;
            $numpaginas=$this->PageNo();
        }

        function Footer(){
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(15);
            $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
            $this->Cell(20);
            $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
            $this->Cell(20);
            $this->Cell(30,10,utf8_decode('Usuario: '.$usuario),0);
            $this->Cell(20);
            $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
        }
    }

    $pdf = new PDF('P','mm','Legal');
    $nb  = $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetY(10);
    if($ruta != ''){
        $pdf->Image('../'.$ruta,15,8,20);
    }

    $pdf->SetX(35);
    $pdf->Cell(160,5,utf8_decode($nombreCompania),0,0,'C');
    $pdf->Ln(5);

    $pdf->SetX(35);
    $pdf->Cell(160, 5,$nitCompania,0,0,'C');
    $pdf->Ln(5);

    $pdf->SetX(35);
    $pdf->Cell(160, 5,"INFORME DETALLADO DE DEPRECIACION",0,0,'C');
    $pdf->Ln(10);
    for ($i = 0; $i < count($pros); $i++) {
        $objPro   = $dep->separarObjeto(",", $pros[$i]);
        $id_unico = $objPro[0]; $nombre = $objPro[1];

        $serie    = $pro->obtnerCodigoProducto($id_unico);
        $valorP   = $pro->obtnerValorProducto($id_unico);
        $fechaA   = $dep->obtnerFechaAquisicion($id_unico);
        if(empty($fechaA)){
            $fechaA = $dep->fechaEntrada($id_unico);
        }
        $vidau = $pro->obtnerVidaUtil($id_unico);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(100,5,"SERIE: ",'LTR',0,'R');
        $pdf->Cell(100,5,$serie,'LTR',0,'L');
        $pdf->Ln(5);
        $pdf->Cell(100,5,"NOMBRE: ",'LR',0,'R');
        $pdf->Cell(100,5,utf8_decode($nombre),'LR',0,'L');
        $pdf->Ln(5);
        $pdf->Cell(100,5,utf8_decode("FECHA ADQUISICIÓN: "),'LR',0,'R');
        $pdf->Cell(100,5,$fechaA,'LR',0,'L');
        $pdf->Ln(5);
        $pdf->Cell(100,5,"VALOR: ",'LRB',0,'R');
        $pdf->Cell(100,5,"$".number_format($valorP,2,',','.'),'LRB',0,'L');
        $pdf->Ln(5);
        $pdf->Cell(100,5,utf8_decode("VIDA ÚTIL: "),'LRB',0,'R');
        $pdf->Cell(100,5,$vidau,'LRB',0,'L');
        $pdf->Ln(5);
        $pdf->Cell(100,5,utf8_decode("FECHA DETERIORO"),"LRB",0,"C");
        $pdf->Cell(100,5,utf8_decode("VALOR DETERIORO"),"LRB",0,"C");
        $pdf->Ln(5);
        $dataPro = $dep->obtnerDepreciacionesProducto($id_unico);
        $totalD  = 0;
        for ($x = 0; $x < count($dataPro); $x++) {
            $datosPro = $dep->separarObjeto("/", $dataPro[$x]);
            $fecha    = new DateTime($datosPro[0]);
            $dias_dep = $datosPro[1];
            $valorDep = $datosPro[2];
            $totalD  += $valorDep;
            $fecha    = $fecha->format('d/m/Y');
            $pdf->SetFont('Arial','',10);
            $pdf->SetAligns(array('C','R'));
            $pdf->SetWidths(array(100,100));
            $pdf->Row(array($fecha,  "$".number_format($valorDep,2,',','.')));
            $pdf->Ln(5);
        }
        $pdf->SetFont('Arial','B',10);
        $pdf->SetAligns(array('R','R'));
        $pdf->SetWidths(array(100,100));
        $pdf->Row(array("TOTAL", "$".number_format($totalD,2,',','.')));
        $pdf->Ln(5);
    }

    while (ob_get_length()) {
      ob_end_clean();
    }

    $pdf->Output(0,"InformeDetalladoDeDeterioros.pdf",0);
}