<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
session_start();
ob_start();
class PDF_MC_Table extends FPDF{
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
            $this->Rect($x,$y,$w,$h,$style);
            //Print the text
            $this->MultiCell($w,3,$data[$i],'LTR',$a,$fill);
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

    #Funcón cabeza de la página
    function header(){
        ##################################################################################################################
        # Array de meses
        #
        ##################################################################################################################
        $meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
        ##################################################################################################################
        # Paginación
        #
        ##################################################################################################################
        $this->SetFont('Arial','B',8);
        $this->Cell(190,3,'Pagina '.$this->PageNo().PHP_EOL.'de'.PHP_EOL.'{nb}',0,0,'L');
        ##################################################################################################################
        # Fecha Actual
        #
        ##################################################################################################################
        $this->Ln(3);
        $mes = (int) date('m');
        $this->Cell(190,3,date('d').PHP_EOL.'de'.PHP_EOL.$meses[$mes].PHP_EOL.'de'.PHP_EOL.date('Y'),0,0,'L');
        ##################################################################################################################
        # Titulo
        #
        ##################################################################################################################
        $this->Ln(3);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,2,'Listado de Comprobantes Consolidado',0,0,'C');
        $this->SetY(20);
    }
}

$pdf = new PDF_MC_Table('P','mm','Letter');     #Creación del objeto pdf
$nb  = $pdf->AliasNbPages();                    #Objeto de número de pagina
$pdf->AddPage();                                #Agregar página
$pdf->SetFont('Arial','B',10);

if(!empty($_REQUEST['sltTipoComprobanteInicial']) && !empty($_REQUEST['sltTipoComprobanteFinal'])){
    list($tipoI, $tipoF, $fechaI, $fechaF) = array($_REQUEST['sltTipoComprobanteInicial'], $_REQUEST['sltTipoComprobanteFinal'], $_REQUEST['txtFechaInicial'], $_REQUEST['txtFechaFinal']);

    $fechaI = explode("/", $fechaI); $fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
    $fechaF = explode("/", $fechaF); $fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";

    $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.sigla
            FROM            gf_tipo_comprobante tpc
            LEFT JOIN       gf_comprobante_cnt     cnt  ON cnt.tipocomprobante = tpc.id_unico
            LEFT JOIN       gf_detalle_comprobante dtc  ON dtc.comprobante = cnt.id_unico
            WHERE           tpc.id_unico BETWEEN $tipoI    AND $tipoF
            AND             cnt.fecha    BETWEEN '$fechaI' AND '$fechaF'
            ORDER BY        cnt.fecha   ASC";
    $res = $mysqli->query($sql);

    while($row = $res->fetch_row()){
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(55,5,(strtoupper($row[1])).PHP_EOL.$row[2],0,0,'L');
        $pdf->Ln(8);

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30,5,'No','LTR',0,'C');
        $pdf->Cell(20,5,'FECHA','LTR',0,'C');
        $pdf->Cell(60,5,'TERCERO','LTR',0,'C');
        $pdf->Cell(30,5,'VALOR','LTR',0,'C');
        $pdf->Cell(30,5,'VALOR','LTR',0,'C');
        $pdf->Cell(30,5,'DIFERENCIA','LTR',0,'C');
        $pdf->Ln(5);
        $pdf->Cell(30,5,'','LRB',0,'C');
        $pdf->Cell(20,5,'','LRB',0,'C');
        $pdf->Cell(60,5,'','LRB',0,'C');
        $pdf->Cell(30,5,'CONTABILIDAD','LRB',0,'C');
        $pdf->Cell(30,5,'PRESUPUESTO','LRB',0,'C');
        $pdf->Cell(30,5,'COMPROBANTES','LRB',0,'C');

        $sql_t = "  SELECT DISTINCT     cnt.id_unico,
                                        cnt.numero,
                                        date_format(cnt.fecha,'%d/%m/%Y'),
                                        IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                    IF(ter.apellidouno IS NULL,'',
                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos))=''
                                        OR  CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                    IF(ter.apellidouno IS NULL,'',
                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                                        (ter.razonsocial),
                                            CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                    IF(ter.apellidouno IS NULL,'',
                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                        cnt.descripcion,
                                        tpc.comprobante_pptal
                    FROM                gf_comprobante_cnt          cnt
                    LEFT JOIN           gf_tercero                  ter  ON cnt.tercero                 = ter.id_unico
                    LEFT JOIN           gf_detalle_comprobante      dtc  ON dtc.comprobante             = cnt.id_unico
                    LEFT JOIN           gf_tipo_comprobante         tpc  ON cnt.tipocomprobante         = tpc.id_unico
                    WHERE               cnt.tipocomprobante = $row[0]
                    AND                 cnt.fecha >= '$fechaI' AND cnt.fecha <= '$fechaF'
                    AND                 dtc.valor IS NOT NULL
                    ORDER BY            cnt.numero ASC
                ";
            $res_t = $mysqli->query($sql_t);
            while ($rowD = $res_t->fetch_row()) {
                list ($vCnt, $vPtl, $vTot) = array(0, 0, 0);

                $pdf->Ln(5);

                $sumar=0; $sumaT=0; $valorD = 0; $valorC =0;
                $sqlR = "SELECT DISTINCT cnt.naturaleza, dtc.valor , dtc.id_unico
                        FROM            gf_detalle_comprobante dtc
                        LEFT JOIN       gf_cuenta cnt ON dtc.cuenta = cnt.id_unico
                        WHERE           dtc.comprobante = $rowD[0]";
                $resR = $mysqli->query($sqlR);
                while($row = mysqli_fetch_row($resR)){
                    if($row[0] == 1){
                        if($row[1] >= 0){
                            $sumar += $row[1];
                        }
                    }else if($row[0] == 2){
                        if($row[1] <= 0){
                            $x = (float) substr($row[1],'1');
                            $sumar += $x;
                        }
                    }

                    if ($row[0] == 2) {
                        if($row[1] >= 0){
                            $sumaT += $row[1];
                        }
                    }else if($row[0] == 1){
                        if($row[1] <= 0){
                            $x = (float) substr($row[1],'1');
                            $sumaT += $x;
                        }
                    }
                }
                $valorD = $sumar;
                $vCnt  = $valorD;

                $sql_p = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = $rowD[1] AND tipocomprobante = $rowD[5]";
                $res_p = $mysqli->query($sql_p);
                $rowPt = $res_p->fetch_row();

                $sql_d = "SELECT valor FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $rowPt[0]";
                $res_d = $mysqli->query($sql_d);
                while($row_d = mysqli_fetch_row($res_d)){
                    $vPtl += $row_d[0];
                }

                list($numero, $fecha, $tercero) = array(
                    $rowD[1],
                    $rowD[2],
                    utf8_decode(strtoupper($rowD[3]))
                );

                $pdf->SetFont('Arial','',9);
                $pdf->SetWidths(array(30, 20, 60, 30, 30, 30));
                $pdf->SetAligns(array('L','L','L','R','R','R'));

                $vTot = $vCnt - $vPtl;
                $pdf->Row(array($numero, $fecha, $tercero, number_format($vCnt, 2, ',', '.'), number_format($vPtl, 2, ',', '.'), number_format($vTot, 2, ',', '.')));

            }
        $pdf->Ln(5);
    }
}

while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'Listado_consolidado_ingresos.pdf',0);