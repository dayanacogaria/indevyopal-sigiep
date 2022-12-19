<?php
header("Content-Type: text/html;charset=utf-8");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../numeros_a_letras.php');
session_start(); 
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$sqlC = "SELECT     ter.razonsocial,
                    ti.nombre,
                    ter.numeroidentificacion,
                    ter.ruta_logo,
                    dr.direccion,
                    tl.valor, ter.razonsocial 
        FROM        gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti  ON ti.id_unico  = ter.tipoidentificacion
        LEFT JOIN   gf_direccion           dr  ON dr.tercero   = ter.id_unico
        LEFT JOIN   gf_telefono            tl ON tl.tercero    = ter.id_unico
        WHERE       ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
# Cargue de variables de compañia
$razonsocial    = $rowCompania[6];
$nombreTipoIden = $rowCompania[1];
$numeroIdent    = $rowCompania[2];
$ruta           = $rowCompania[3];
$direccion      = $rowCompania[4];
$telefono       = $rowCompania[5];

#* DATOS DOCUMENTO
$rowdc = $con->Listar("SELECT d.id_unico, td.id_unico, td.sigla, td.nombre, 
        d.numero, DATE_FORMAT(d.fecha, '%d/%m/%Y'),DATE_FORMAT(d.fecha_vencimiento, '%d/%m/%Y'), 
        t.id_unico, CONCAT_WS(' ',COALESCE(t.nombreuno,''),COALESCE(t.nombredos,''),COALESCE(t.apellidouno,''),COALESCE(t.apellidodos,''),COALESCE(t.razonsocial,'')), 
        t.numeroidentificacion, d.descripcion, td.resolucion, dr.direccion, t.email 
 FROM gf_documento_equivalente d 
 LEFT JOIN gf_tipo_documento_equivalente td ON d.tipo = td.id_unico 
 LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
 LEFT JOIN gf_direccion dr ON t.id_unico = dr.tercero 
 WHERE md5(d.id_unico)='".$_REQUEST['id']."'");
$id_documento = $rowdc[0][0];
$numero       = $rowdc[0][4];
$id_tipo      = $rowdc[0][1];
$sigla        = $rowdc[0][2];
$tipo         = $rowdc[0][3];
$id_tercero   = $rowdc[0][7];
$tercero      = $rowdc[0][8];
$num_ident    = $rowdc[0][9];;
$fecha        = $rowdc[0][5];
$fecha_vencimiento=$rowdc[0][6];
$descripcion  = $rowdc[0][10];
$resolucion   = $rowdc[0][11];
$direccion_t  = $rowdc[0][12];
$email        = $rowdc[0][13];

#* DETALLES
$rowd = $con->Listar("SELECT id_unico, descripcion, cantidad, valor_unitario, valor_iva, valor_total
    FROM gf_detalle_documento_equivalente WHERE documento_equivalente=$id_documento");

if($_REQUEST['t']==1){
    require'../fpdf/fpdf.php';
    ob_start();
    # Clase de diseño de formato
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
            //Calcula el alto de l afila
            $nb = 0;
            for($i = 0; $i < count($data); $i++)
                $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
            $h = 5 * $nb;
            //Realiza salto de pagina si es necesario
            $this->CheckPageBreak($h);
            //Pinta las celdas de la fila
            for($i = 0; $i < count($data); $i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Guarda la posicion actual
                $x = $this->GetX();
                $y = $this->GetY();
                //Pinta el border
                $this->Rect($x, $y, $w, $h, $style);
                //Imprime el texto
                $this->MultiCell($w,5,$data[$i],'LR', $a, $fill);
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Hace salto de la pagina
            $this->Ln($h - 5);
        }

        function fila($data){
            //Calcula el alto de l afila
            $nb = 0;
            for($i = 0; $i < count($data); $i++)
                $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h = 5 * $nb;
            //Realiza salto de pagina si es necesario
            $this->CheckPageBreak($h);
            //Pinta las celdas de la fila
            for($i = 0; $i < count($data); $i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Guarda la posicion actual
                $x = $this->GetX();
                $y = $this->GetY();
                //Pinta el border
                $this->Rect($x, $y, 0, 0, $style);
                //Imprime el texto
                $this->MultiCell($w,5, $data[$i],'', $a, $fill);
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Hace salto de la pagina
            $this->Ln($h - 5);
        }

        function CheckPageBreak($h){
            //If the height h would cause an overflow, add a new page immediately
            if($this->GetY()+$h>$this->PageBreakTrigger)
                $this->AddPage($this->CurOrientation);
        }
        function NbLines($w,$txt){
            //Computes the number of lines a MultiCell of width w will take
            $cw =&$this->CurrentFont['cw'];
            if($w == 0)
                $w = $this->w-$this->rMargin-$this->x;
            $wmax = ( $w - 2 * $this->cMargin) * 1000 / $this->FontSize;
            $s  = str_replace('\r','', $txt);
            $nb = strlen($s);
            if( $nb > 0 and $s[$nb-1] == '\n' )
                $nb–;
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

        #Funcón cabeza de la página
        function header(){
            #Redeclaración de varibles
            global $razonsocial; 
            global $nombreTipoIden; 
            global $ruta;           
            global $numeroIdent;    
            global $direccion;   
            global $telefono;    
            global $sigla;       
            global $tipo;    
            global $numero;    
            global $resolucion;
            
            $this->Cell(200, 25, '',1, 0, 'C');
            if($ruta != ''){
              $this->Image('../'.$ruta,15,11,20);
            }
            $this->SetFont('Helvetica','B',10);
            $this->SetXY(30,15);
            $this->MultiCell(150,5,utf8_decode($razonsocial),0,'C');
            $this->SetXY(170,15);
            $this->SetFont('Helvetica','B',8);
            $this->MultiCell(40,3,utf8_decode($tipo),0,'C');
            $this->SetXY(170,20);
            $this->SetFont('Helvetica','B',10);
            $this->MultiCell(40,5,utf8_decode('N° '.$sigla.' '.$numero),0,'C');
            $this->SetXY(170,25);
            $this->SetFont('Helvetica','B',6);
            $this->MultiCell(40,3,utf8_decode($resolucion),0,'C');
            $this->SetXY(40,20);
            $this->SetFont('Helvetica','B',10);
            $this->MultiCell(130,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono")),0,'C');
            $this->Ln(5);
        }
    }

    $pdf = new PDF('P','mm','Letter');      #Creación del objeto pdf
    $nb=$pdf->AliasNbPages();                       #Objeto de número de pagina
    $pdf->AddPage();                                #Agregar página
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetWidths(array(40, 60, 40, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array('FECHA', $fecha, 'FECHA VENCIMIENTO', $fecha_vencimiento));
    $pdf->Ln(5);
    $pdf->SetWidths(array(40, 60, 40, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array(utf8_decode('CLIENTE'), utf8_decode($tercero), utf8_decode('NIT'), utf8_decode($num_ident)));
    $pdf->Ln(5);
    $pdf->SetWidths(array(40, 60, 40, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array(utf8_decode('DIRECCIÓN'), $direccion_t, 'EMAIL', $email));
    $pdf->Ln(5);
    $pdf->SetWidths(array(40, 160));
    $pdf->SetAligns(array('L', 'L'));
    $pdf->Row(array(utf8_decode('OBSERVACIONES'), $descripcion));
    $pdf->Ln(5);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell(80, 5, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C');
    $pdf->Cell(30, 5, 'CANTIDAD', 1, 0, 'C');
    $pdf->Cell(30, 5, 'VALOR UNITARIO', 1, 0, 'C');
    $pdf->Cell(30, 5, 'VALOR IVA', 1, 0, 'C');
    $pdf->Cell(30, 5, 'TOTAL', 1, 0, 'C');
    $pdf->Ln(5);

    list($sumV, $sumIva, $sumT) = array(0, 0, 0);

    for ($i = 0; $i < count($rowd); $i++) {
        $sumV   += $rowd[$i][3]*$rowd[$i][2];
        $sumIva += $rowd[$i][4]*$rowd[$i][2];
        $sumT   += $rowd[$i][5];
        $pdf->SetFont('Helvetica','',8);
        $pdf->SetWidths(array(80, 30, 30, 30, 30));
        $pdf->SetAligns(array('L', 'L',  'R', 'R', 'R'));
        $pdf->Row(array($rowd[$i][1],$rowd[$i][2],number_format($rowd[$i][3], 2), number_format($rowd[$i][4], 2), number_format($rowd[$i][5], 2)));
        $pdf->Ln(5);
    }
    $valorLetras  = numtoletras($sumT);
    
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell(140, 5, 'SON: ', 'LTR', 0, 'L');
    $pdf->Cell(30, 5, 'SUBTOTAL',1, 0, 'L');
    $pdf->Cell(30, 5, number_format($sumV, 2), 1, 0, 'R');
    $pdf->Ln(5);
    $y = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'BI', 9);
    $pdf->MultiCell(140, 5, $valorLetras, 'LR', 'L');
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->SetXY(150,$y);
    $pdf->Cell(30, 5, 'VALOR IVA',1, 0, 'L');
    $pdf->Cell(30, 5, number_format($sumIva, 2), 1, 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(140, 5, '', 'LRB', 0, 'C');
    $pdf->Cell(30, 5, 'TOTAL FACTURA',1, 0, 'L');
    $pdf->Cell(30, 5, number_format($sumT, 2), 1, 0, 'R');
    $pdf->Ln(5);
    
    
    $y = $pdf->GetY();
    $pdf->Cell(70,30,'', 1, 0, 'R');
    $pdf->Cell(70,30,'', 1, 0, 'R');
    $pdf->Cell(60,30,'', 1, 0, 'R');
    $pdf->SetY($y+5);
    $pdf->Cell(70, 5, 'PROVEEDOR', '', 0, 'L');
    $pdf->Cell(70, 5,   utf8_decode('RESPONSABLE'), '', 0, 'L');
    $pdf->Cell(70, 5, 'SELLO RECIBIDO', '', 0, 'L');
    
    $pdf->Ln(13);
    $pdf->Cell(60, 5, '', 'B', 0,'C');
    $pdf->Ln(5);
    $pdf->Cell(60, 5, 'C.C.', 0, 0,'L');
    
    ob_end_clean();                                             #Limpieza del buffer
    $pdf->Output(0,'Informe_DocumentoE_'.$numero.'.pdf',0);       #Salida del documento
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_DocumentoE_".$numero.".xls");
    ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Documento Equivalente</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="3" ><CENTER><strong><?=($razonsocial);?>
              <br/>&nbsp;<?= $nombreTipoIden.': '.$numeroIdent;?>
                <br/>&nbsp;<?= $direccion.'TELEFONO : '.$telefono;?>
              <br/>&nbsp;
              </strong></CENTER>
            </td>
            <td colspan="2" style="width: 10%;"><CENTER><strong><?=($tipo);?>
                <br/>&nbsp;<?= 'N° '.$sigla.' '.$numero;?>
                <br/>&nbsp;<?= utf8_decode($resolucion);?>
                <br/>&nbsp;
                </strong></CENTER>
            </td>
        </tr>
        
        <tr>
            <td><strong>FECHA</strong></td>
            <td colspan="2"><?=$fecha;?></td>
            <td><strong>FECHA VENCIMIENTO</strong></td>
            <td><?=$fecha_vencimiento;?></td>
        </tr>
        <tr>
            <td><strong>CLIENTE</strong></td>
            <td colspan="2"><?=$tercero;?></td>
            <td><strong>NIT</strong></td>
            <td><?=$num_ident;?></td>
        </tr>
        <tr>
            <td><strong>DIRECCIÓN</strong></td>
            <td colspan="2"><?=$direccion_t;?></td>
            <td><strong>EMAIL</strong></td>
            <td><?=$email;?></td>
        </tr>
        <tr>
            <td><strong>OBSERVACIONES</strong></td>
            <td colspan="4"><?=$descripcion;?></td>
        </tr>
        <tr>
            <td><strong>DESCRIPCIÓN</strong></td>
            <td><strong>CANTIDAD</strong></td>
            <td><strong>VALOR UNITARIO</strong></td>
            <td><strong>VALOR IVA</strong></td>
            <td><strong>TOTAL</strong></td>
        </tr>
        <?php 
        list($sumV, $sumIva, $sumT) = array(0, 0, 0);
        for ($i = 0; $i < count($rowd); $i++) {
            $sumV   += $rowd[$i][3]*$rowd[$i][2];
            $sumIva += $rowd[$i][4]*$rowd[$i][2];
            $sumT   += $rowd[$i][5];
            echo '<tr>
                <td>'.$rowd[$i][1].'</td>
                <td>'.$rowd[$i][2].'</td>
                <td>'.number_format($rowd[$i][3], 2).'</td>
                <td>'.number_format($rowd[$i][4], 2).'</td>
                <td>'.number_format($rowd[$i][5], 2).'</td>
            </tr>';
        }
        $valorLetras  = numtoletras($sumT);
        echo '<tr>
            <td colspan="3" rowspan="3"><strong>SON: <i>'.$valorLetras.'</i></strong></td>
            <td><strong>SUBTOTAL:</strong></td>
            <td><strong>'.number_format($sumV, 2).'</strong></td>
        </tr>
        <tr><td><strong>VALOR IVA:</strong></td>
            <td><strong>'.number_format($sumIva, 2).'</strong></td></tr>
        <tr><td><strong>TOTAL FACTURA:</strong></td>
            <td><strong>'.number_format($sumT, 2).'</strong></td></tr>';
        echo '<tr>
                <td colspan="2"><strong>PROVEEDOR
                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;</strong></td>                
                <td colspan="2"><strong>RESPONSABLE
                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;</strong></td>
                <td><strong>SELLO RECIBIDO
                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;</strong></td>
            </tr>';
}