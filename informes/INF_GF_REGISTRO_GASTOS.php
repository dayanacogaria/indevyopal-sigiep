<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#15/08/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$id    = $_REQUEST['id'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo    = $rowC[0][6];
#*******************************************************************************#
$row    = $con->Listar("SELECT 
        DATE_FORMAT(fecha, '%d/%m/%Y') as fecha,
        valor,  descripcion, tercero, descripcion, retenciones
        FROM gf_registro_gastos WHERE md5(id_unico) = '$id'");
$fecha          = $row[0]['fecha'];
$valor          = $row[0]['valor'];
$descripcion    = $row[0]['descripcion'];
$id_tercero     = $row[0]['tercero'];    
$val            = 0;
if(!empty($row[0]['retenciones'])){   
    $rt = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$row[0]['retenciones'].")");
    if(empty($rt[0][0])){
        $val =0;
    } else {
        $val= $rt[0][0];
    }
}

$tr  = $con->Listar("SELECT IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE 
     FROM gf_tercero t WHERE id_unico = $id_tercero");
$tercero         = ucwords(mb_strtolower($tr[0][0]));
$valor_neto      = $valor -$val;
$valorletras     = numtoletras($valor_neto);
#*******************************************************************************#    
require'../fpdf/fpdf.php';
ob_start();
class PDF extends FPDF
{
    function Header(){ 
        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $direccinTer;
        global $telefonoTer;
        global $ruta_logo;
        global $numpaginas;
        global $numero;
        $numpaginas=$numpaginas+1;

        $this->SetFont('Arial','B',10);

        if($ruta_logo != '')
        {
          $this->Image('../'.$ruta_logo,10,5,20);
        }
        $this->SetX(30);
        $this->SetFont('Arial','B',10);
        $x =$this->GetX();
        $y =$this->GetY();
        $this->MultiCell(95,5,utf8_decode($razonsocial),0,'C');		
        $this->SetX(30);
        $this->MultiCell(95,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,'C');
        $y1 = $this->GetY();
        $this->SetXY($x+95, $y);
        $this->Cell(80,5,utf8_decode('RECIBO ENTREGA'),0,0,'R');
        $this->Ln(5);
        $this->SetX($x+95);
        $this->Cell(80,5,utf8_decode('DE EFECTIVO'),0,0,'R');
        $y2 = $this->GetY();
        $h= max($y1, $y2);
        $this->SetY($y);
        $this->Ln($h);
    }  
}
$pdf = new PDF('P','mm','mcarta');   
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->Ln(-3);
$pdf->SetFont('Arial','B',10);
$xf = $pdf->GetX();
$yf = $pdf->GetY();
$pdf->Cell(30,10,utf8_decode('FECHA'),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(90,10,utf8_decode($fecha),0,0,'L');
$pdf->SetXY($xf,$yf);
$pdf->Cell(120,10,utf8_decode(''),1,0,'L');
$pdf->SetFont('Arial','B',10);
$xv = $pdf->GetX();
$yv = $pdf->GetY();
$pdf->Cell(30,10,utf8_decode('VALOR'),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(45,10,utf8_decode('$'.number_format($valor_neto,2,'.',',')),0,0,'R');
$pdf->SetXY($xv,$yv);
$pdf->Cell(75,10,utf8_decode(''),1,0,'L');
$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
$xp = $pdf->GetX();
$yp = $pdf->GetY();
$pdf->Cell(30,10,utf8_decode('PAGADO A'),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->CellFitScale(165,10,utf8_decode($tercero),0,'L');
$pdf->SetXY($xp,$yp);
$pdf->Cell(195,10,utf8_decode(''),1,0,'L');
$pdf->Ln(10);
$xc = $pdf->GetX();
$yc = $pdf->GetY();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,10,utf8_decode('CONCEPTO'),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(165,5,utf8_decode($descripcion),0,'J');
$ycs = $pdf->GetY();
$pdf->SetXY($xc, $yc);
$h = 70-$ycs;
$yy= $ycs-$yc;
$al = $h+$yy;
$pdf->Cell(195,$al,utf8_decode(''),1,0,'L');
$pdf->Ln(1);
$pdf->SetY(70);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(195,1,utf8_decode(''),0,0,'L');
$pdf->Ln(1);
$xv = $pdf->GetX();
$yv = $pdf->GetY();
$pdf->Cell(30,10,utf8_decode('SON'),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(165,10,utf8_decode($valorletras),0,'J');
$yvs = $pdf->GetY();
$pdf->SetXY($xv, $yv);
$hv = 90-$yvs;
$yyv= $yvs-$yv;
$alv = $hv+$yyv;
$pdf->Cell(195,$alv,utf8_decode(''),1,0,'L');
$pdf->Ln(1);
$pdf->SetY(90);
$pdf->Cell(195,1,utf8_decode(''),0,0,'L');
$pdf->Ln(1);
$xfi = $pdf->GetX();
$yfi = $pdf->GetY();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,5,utf8_decode('IMPUTACION'),0,0,'L');
$pdf->SetXY($xfi,$yfi);
$pdf->Cell(80,12,utf8_decode(''),1,0,'L');
$pdf->Ln(12);
$pdf->Cell(30,5,utf8_decode('APROBADO POR'),0,0,'L');
$pdf->SetXY($xfi,$yfi);
$pdf->Cell(80,24,utf8_decode(''),1,0,'L');
$pdf->SetXY($xfi+87,$yfi);
$pdf->Cell(30,5,utf8_decode('FIRMA RECIBIDO'),0,0,'L');
$pdf->Ln(15);
$pdf->SetX($xfi+87);
$pdf->Cell(100,0.5,utf8_decode(''),1,0,'L');
$pdf->Ln(3);
$pdf->SetX($xfi+87);
$pdf->Cell(30,2,utf8_decode('C.C. /NIT.'),0,0,'L');
$pdf->SetXY($xfi+85,$yfi);
$pdf->Cell(110,24,utf8_decode(''),1,0,'L');

ob_end_clean();		
$pdf->Output(0,'Recibo_Entrega_Efectivo.pdf',0);

