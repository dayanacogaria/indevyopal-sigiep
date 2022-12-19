<?php
header("Content-Type: text/html;charset=utf-8");
require_once '../numeros_a_letras.php';
require      '../fpdf/fpdf.php';
require      '../Conexion/conexion.php';
session_start(); 	# Session
$compania = $_SESSION['compania'];
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
$sqlC = "SELECT 	ter.razonsocial, ti.nombre, ter.numeroidentificacion, ter.ruta_logo
		FROM		gf_tercero ter
		LEFT JOIN	gf_tipo_identificacion ti
		ON 			ti.id_unico  = ter.tipoidentificacion
		WHERE		ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
list($razonsocial, $nombreTipoIden, $numeroIdent, $ruta, $factura)
    = array($rowCompania[0], $rowCompania[1], $rowCompania[2], $rowCompania[3], $_GET['factura']);

$sqlF = "SELECT 	fat.id_unico,
					tpf.nombre,
					fat.numero_factura,
					CONCAT(ELT(WEEKDAY(fat.fecha_factura) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
					fat.fecha_factura,
					date_format(fat.fecha_vencimiento,'%d/%m/%Y'),
					IF(	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
								IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
								IF(ter.apellidouno IS NULL,'',
								IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
								IF(ter.apellidodos IS NULL,'',ter.apellidodos))=''
					OR 	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
								IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
								IF(ter.apellidouno IS NULL,'',
								IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
								IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
						(ter.razonsocial),
					    CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
					    		IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
					    		IF(ter.apellidouno IS NULL,'',
					    		IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
					    		IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
					ter.numeroidentificacion,
					fat.descripcion, gdr.direccion, gtl.valor
		FROM		gp_factura       AS fat
		LEFT JOIN	gp_tipo_factura  AS tpf ON tpf.id_unico = fat.tipofactura
		LEFT JOIN	gf_tercero       AS ter	ON ter.id_unico = fat.tercero
		LEFT JOIN   gf_direccion     AS gdr ON ter.id_unico = gdr.tercero
		LEFT JOIN   gf_telefono      AS gtl ON ter.id_unico = gtl.tercero
		WHERE		md5(fat.id_unico) = '$factura'";
$resultF = $mysqli->query($sqlF);
$rowF    = mysqli_fetch_row($resultF);
list($fat_id, $tip_fat, $num_fat, $dia_fat, $fecha_fat, $fechaV_fat, $tercero_fat, $idt_fat, $desc_fat, $dir, $tel_ter)
    = array($rowF[0], $rowF[1], $rowF[2], $rowF[3], $rowF[4], $rowF[5], $rowF[6], $rowF[7], $rowF[8], $rowF[9], $rowF[10]);
$str_xr = "SELECT     gdp.pago
            FROM      gp_detalle_factura AS gdf
            LEFT JOIN gp_detalle_pago    AS gdp ON gdf.id_unico = gdp.detalle_factura
            WHERE     gdf.factura = $fat_id";
$res_xr = $mysqli->query($str_xr);
if($res_xr->num_rows > 0){
    $xprg = "SI";
}else{
    $xprg = "NO";
}
class PDF extends FPDF{
    var $widths;
    var $aligns;

    function SetWidths($w){
        $this->widths = $w;
    }

    function SetAligns($a){
        $this->aligns = $a;
    }

    function fill($f){
        $this->fill = $f;
    }

    function Row($data){
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h, '');
            $this->MultiCell($w,5,$data[$i],'LR', $a, '');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h - 5);
    }

    function fila($data){
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, 0, 0, '');
            $this->MultiCell($w,5, $data[$i],'', $a, '');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h - 5);
    }

    function CheckPageBreak($h){
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt){
        $cw =&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ( $w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s  = str_replace('\r','', $txt);
        $nb = strlen($s);
        if( $nb > 0 and $s[$nb-1] == '\n' )
            $nb--;
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

    function header(){
        global $razonsocial;	#Nombre de compañia
        global $nombreTipoIden;	#Tipo de identificación tercero
        global $tip_fat;		#Nombre de factura
        global $num_fat;		#Número de facutra
        global $ruta;			#Ruta de logo
        global $numeroIdent;	#Numero identificacion tercero
        if($ruta != '') {
            $this->Image('../'.$ruta,10,8,20);
        }
        $this->SetFont('Courier','B',10);
        $this->SetXY(40,15);
        $this->MultiCell(140,5,utf8_decode($razonsocial),0,'C');
        $this->SetX(10);
        $this->Cell(200,5,utf8_decode(mb_strtoupper($nombreTipoIden).':'.PHP_EOL.$numeroIdent),0,0,'C');
        $this->ln(5);
        $this->Cell(200,5,utf8_decode(ucwords(strtoupper($tip_fat.PHP_EOL))).'Nro:'.PHP_EOL.$num_fat,0,0,'C');
        $this->Ln(5);
    }
}
$pdf = new PDF('P','mm','Letter');
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$fechaD=explode("-",$fecha_fat);
$diaC = $fechaD[2];				#Dia
$mesC = (int) $fechaD[1];		#Mes
$annoC= $fechaD[0];				#Año
$pdf->SetFont('Courier', 'B', 9);
$pdf->SetAligns(array('L', 'L', 'L', 'R'));
$pdf->SetWidths(array(25, 90, 40, 35));
$pdf->Row(array('FECHA', $dia_fat.', '.$diaC.' de '.$meses[$mesC].' de '.$annoC, 'VENCE', $fechaV_fat));
$pdf->Ln(5);
$pdf->SetAligns(array('L', 'L', 'L', 'R'));
$pdf->SetWidths(array(25, 90, 40, 35));
$pdf->Row(array('RECIBI DE', $tercero_fat, 'C.C / NIT', $idt_fat));
$pdf->Ln(5);
$pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'R'));
$pdf->SetWidths(array(25, 38.32, 20, 31.66, 40, 35));
$pdf->Row(array('DIRECCION', $dir, 'TELEFONO', $tel_ter, 'PAGADA', $xprg));
$pdf->Ln(5);
$pdf->Cell(190,10,'',1,0);
$pdf->Ln(0);
$pdf->Cell(25,5,utf8_decode('DESCRIPCIÓN:'),0,0,'L');
$pdf->SetFont('Courier','',9);
$pdf->Multicell(165,5,utf8_decode($desc_fat),0,'L');
$pdf->Ln(5);
$pdf->SetFont('Courier','B',7);
$pdf->Cell(100,10,'CONCEPTO',1,0,'L');
$pdf->Cell(25,10,'CANTIDAD',1,0,'L');
$pdf->Cell(30,10,'VALOR',1,0,'L');
$pdf->Cell(35,10,'VALOR',1,0,'C');
$pdf->SetX(10);
$pdf->Cell(100,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Cell(30,5,'',0,0,'L');
$pdf->Cell(35,5,'',0,0,'L');
$pdf->Ln(5);
$pdf->Cell(100,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Cell(30,5,'UNITARIO',0,0,'L');
$pdf->Cell(35,5,'TOTAL',0,0,'C');
$pdf->Ln(5);
$sumCantidad = 0;
$sumValor = 0;
$sumIva = 0;
$sumImpo = 0;
$sumAjuste = 0;
$sumValorT = 0;
$str = "SELECT      CONCAT_WS(' ', pln.codi, conp.nombre),
                    dtf.cantidad,
                    dtf.valor,
                    dtf.iva,
                    dtf.impoconsumo,
                    dtf.ajuste_peso,
                    dtf.valor_total_ajustado
        FROM		gp_detalle_factura AS dtf
        LEFT JOIN	gp_concepto        AS conp ON conp.id_unico = dtf.concepto_tarifa
        LEFT JOIN   gf_plan_inventario AS pln  ON conp.plan_inventario = pln.id_unico
        WHERE		md5(dtf.factura) = '".$_REQUEST['factura']."'";
$res  = $mysqli->query($str);
$data = $res->fetch_all(MYSQLI_NUM);
foreach ($data as $row){
    $pdf->SetFont('Courier','',7);
    $cantidad    = $row[1];
    $valor       = $row[2];
    $iva         = $row[3];
    $impoconsumo = $row[4];
    $ajuste      = $row[5];
    $valorTotal  = $row[6];
    $pdf->CellFitScale(100,4,utf8_decode($row[0]),0,0,'L');
    $pdf->CellFitScale(25,4, utf8_decode($cantidad),0,0,'R');
    $pdf->CellFitScale(30,4,number_format(($valor + $iva + $impoconsumo + $ajuste),2,',','.'),0,0,'R');
    $pdf->CellFitScale(35,4,number_format($valorTotal,2,',','.'),0,0,'R');
    $pdf->Ln(5);
    $sumCantidad += $cantidad;
    $sumValor    += $valor;
    $sumIva      += $iva;
    $sumImpo     += $impoconsumo;
    $sumAjuste   += $ajuste;
    $sumValorT   += round(($valor + $iva + $impoconsumo + $ajuste) * $cantidad, 0);
}
$pdf->SetFont('Courier','B',9);
$pdf->SetWidths(array(50, 140));
$pdf->SetAligns(array('R', 'R'));
$pdf->Row(array('TOTAL A PAGAR', number_format($sumValorT,0,',','.')));;
$pdf->Ln(5);
$pdf->SetWidths(array(50, 140));
$pdf->SetAligns(array('R', 'R'));
$pdf->Row(array('VALOR EN LETRAS', numtoletras($sumValorT)));;
$pdf->Ln(15);
$pdf->Cell(50,0,'',1);
$pdf->SetX(150);
$pdf->Cell(50,0,'',1);
$pdf->Ln(1);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(50,5,'FIRMA LIQUIDADOR',0,0,'C');
$pdf->SetX(150);
$pdf->Cell(50,5,'RECIBIDO POR',0,0,'C');
ob_end_clean();
$pdf->Output(0,'Informe_factura_'.$num_fat.'.pdf',0);