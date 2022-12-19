<?php
ini_set('max_execution_time', 0);
session_start();
ob_start();
require_once('../Conexion/conexion.php');
require ('../fpdf/fpdf.php');
require_once ('../modelFactura/facturaC.php');
if(!empty($_POST['txtFechaInicial']) && !empty($_POST['txtFechaFinal'])){
    $factura = new factura();

    $i = explode("/", $_POST['txtFechaInicial']);
    $f = explode("/", $_POST['txtFechaFinal']);
    $fecha_incial = "$i[2]-$i[1]-$i[0]";
    $fecha_final  = "$f[2]-$f[1]-$f[0]";

    $facturas = $factura->obtnerFacturas($fecha_incial, $fecha_final);

    $usuario = $_SESSION['usuario'];
    $compa   = $_SESSION['compania'];

    $comp = $factura->obtnerCompania($compa);
    $nombreCompania = $comp[0];
    $ruta = $comp[2];
    if(empty($comp[3])) {
        $nitCompania = $comp[1];
    } else {
        $nitCompania = $comp[1].' - '.$comp[3];
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
            $this->Cell(230, 5,utf8_decode("RELACIÓN DE FACTURACION ENTRE COMPROBANTES"),0,0,'C');
            $this->Ln(5);

            $this->SetFont('Arial','B',8);
            $this->Cell(20,5,'TIPO','LTR',0,'C');
            $this->Cell(20,5,utf8_decode('NÚMERO'),'LTR',0,'C');
            $this->Cell(90,5,'TERCERO','LTR',0,'C');
            $this->Cell(30,5,'VALOR','LTR',0,'C');
            $this->Cell(30,5,'VALOR','LTR',0,'C');
            $this->Cell(30,5,'VALOR','LTR',0,'C');
            $this->Cell(30,5,'VALOR','LTR',0,'C');
            $this->Ln(5);
            $this->Cell(20,5,'','LBR',0,'C');
            $this->Cell(20,5,'','LBR',0,'C');
            $this->Cell(90,5,'','LBR',0,'C');
            $this->Cell(30,5,'FACTURA','LBR',0,'C');
            $this->Cell(30,5,'TOTAL FACTURA','LBR',0,'C');
            $this->Cell(30,5,'CONTABLE','LBR',0,'C');
            $this->Cell(30,5,'PRESUPESTO','LBR',0,'C');
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

    for ($i = 0; $i < count($facturas); $i++) {
        $data = explode("," , $facturas[$i]);
        $id_tipo = $data[0]; $id_unico = $data[1]; $numero = $data[2]; $tercero = $data[3];

        $sqlt = "SELECT prefijo, tipo_comprobante FROM gp_tipo_factura WHERE id_unico = $id_tipo";
        $rest = $mysqli->query($sqlt);
        $rowt = mysqli_fetch_row($rest);
        $prefijo = $rowt[0]; $tipo_co = $rowt[1];

        $sqlr = "SELECT   IF(CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                             CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='',
                             (ter.razonsocial),
                             CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                          ) AS 'NOMBRE'
                 FROM     gf_tercero ter
                 WHERE    ter.id_unico = $tercero";
        $resr = $mysqli->query($sqlr);
        $rowr = mysqli_fetch_row($resr);
        $nombt = $rowr[0];
        if(!empty($tipo_co)){
            $valorF = $factura->valorDetallesFactura($id_unico);
            $valorT = $factura->obtnerValorTotalFacturIva($id_unico);
            $dataID = $factura->obtnerComprobantesCntP($id_unico);
            $id_cnt = $dataID[0]; $id_pptal = $dataID[1];
            if(!empty($id_cnt)){
                $valorC = $factura->obtnerValoresDetalleCnt($id_cnt);
                $valorP = $factura->obtnerValoresPptal($id_pptal);

                $pdf->setFont('Arial' ,'', 8);
                $pdf->SetAligns(array('C','R','L','R','R','R','R'));
                $pdf->SetWidths(array(20,20,90,30,30,30,30));
                $pdf->Row(array($prefijo, $numero, $nombt, "$".number_format($valorF,2,'.',','), "$".number_format($valorT,2,'.',','), "$".number_format($valorC,2,'.',','), "$".number_format($valorP,2,'.',',')));
                $pdf->Ln(5);
            }
            else{
                $dataID = $factura->obtnerComprobantesDesdeDetalles($id_unico);
                $id_cnt = $dataID[0]; $id_pptal = $dataID[1];
                $valorC = $factura->obtnerValoresDetalleCnt($id_cnt);
                $valorP = $factura->obtnerValoresPptal($id_pptal);

                $pdf->setFont('Arial' ,'', 8);
                $pdf->SetAligns(array('C','R','L','R','R','R','R'));
                $pdf->SetWidths(array(20,20,90,30,30,30,30));
                $pdf->Row(array($prefijo, $numero, $nombt, "$".number_format($valorF,2,'.',','), "$".number_format($valorT,2,'.',','), "$".number_format($valorC,2,'.',','), "$".number_format($valorP,2,'.',',')));
                $pdf->Ln(5);
            }
        }
    }

    while (ob_get_length()) {
      ob_end_clean();
    }

    $pdf->Output(0,"InformeRelacionEntreComprobantes.pdf",0);
}