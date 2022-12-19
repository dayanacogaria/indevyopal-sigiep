<?php
header("Content-Type: text/html;charset=utf-8");
require_once('../numeros_a_letras.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
@session_start();

list($rep, $dir_t, $ciu_t, $tel_t) = array("", "", "", "");
$compania = $_SESSION['compania'];
$meses    = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
$sqlC = "SELECT     ter.razonsocial,
                    ti.nombre,
                    ter.numeroidentificacion,
                    ter.ruta_logo,
                    dr.direccion,
                    tl.valor
        FROM        gf_tercero ter
        LEFT JOIN   gf_tipo_identificacion ti  ON ti.id_unico  = ter.tipoidentificacion
        LEFT JOIN   gf_direccion           dr  ON dr.tercero   = ter.id_unico
        LEFT JOIN   gf_telefono            tl ON tl.tercero    = ter.id_unico
        WHERE       ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
# Cargue de variables de compañia
$razonsocial    = $rowCompania[0];
$nombreTipoIden = $rowCompania[1];
$numeroIdent    = $rowCompania[2];
$ruta           = $rowCompania[3];
$direccion      = $rowCompania[4];
$telefono       = $rowCompania[5];
# Captura de id de factura
$factura = $_GET['factura'];
# Consulta para obtener los datos de factura
# @sqlF {String}
$sqlF = "SELECT     fat.id_unico,
                    tpf.nombre,
                    fat.numero_factura,
                    CONCAT(ELT(WEEKDAY(fat.fecha_factura) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
                    fat.fecha_factura,
                    date_format(fat.fecha_vencimiento,'%d/%m/%Y'),
                    IF(ter.razonsocial IS NULL OR ter.razonsocial ='', 
                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos,ter.apellidouno,ter.apellidodos), ter.razonsocial) AS 'NOMBRE',
                    CONCAT_WS(' ', ter.numeroidentificacion, ter.digitoverficacion),
                    fat.descripcion,
                    tpf.resolucion,
                    gdr.direccion,
                    gtl.valor,
                    gci.nombre,
                    ter.nombre_comercial, 
                    (SELECT GROUP_CONCAT(DISTINCT tfaf.prefijo,' - ',faf.numero_factura,' ', DATE_FORMAT(fat.fecha_factura,'%d/%m/%Y')) FROM gp_detalle_factura df 
                    LEFT JOIN gp_detalle_factura dfa ON df.detalleafectado = dfa.id_unico 
                    LEFT JOIN gp_factura faf ON dfa.factura = faf.id_unico 
                    LEFT JOIN gp_tipo_factura tfaf ON faf.tipofactura = tfaf.id_unico 
                    WHERE df.factura =fat.id_unico ) AS fa
        FROM        gp_factura      AS fat
        LEFT JOIN   gp_tipo_factura AS tpf ON tpf.id_unico         = fat.tipofactura
        LEFT JOIN   gf_tercero      AS ter ON ter.id_unico         = fat.tercero
        LEFT JOIN   gf_direccion    AS gdr ON gdr.tercero          = ter.id_unico
        LEFT JOIN   gf_telefono     AS gtl ON gtl.tercero          = ter.id_unico
        LEFT JOIN   gf_ciudad       AS gci ON gdr.ciudad_direccion = gci.id_unico
        WHERE       md5(fat.id_unico) = '$factura'";
$resultF = $mysqli->query($sqlF);
$rowF    = mysqli_fetch_row($resultF);
# Cargue de variables de factura
$fat_id      = $rowF[0];  $tip_fat     = $rowF[1];  $num_fat     = $rowF[2];
$dia_fat     = $rowF[3];  $fecha_fat   = $rowF[4];  $fechaV_fat  = $rowF[5];
$tercero_fat = $rowF[6];  $num_ter_f   = $rowF[7];  $desc_fat    = $rowF[8];
$resolucion  = $rowF[9];  $dir_t       = $rowF[10]; $tel_t       = $rowF[11];
$ciu_t       = $rowF[12]; $nomComerc   = $rowF[13]; $obser       = $rowF[14];
# Consulta de representante legal
$str_r = "SELECT    gtr.representantelegal,
                    (
                      IF(
                        CONCAT_WS(' ',grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos) = '',
                        grp.razonsocial,
                        CONCAT_WS(' ',grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos)
                      )
                    ) AS nom,
                    gtl.valor,
                    gdr.direccion,
                    gci.nombre
          FROM      gf_tercero   AS gtr
          LEFT JOIN gf_tercero   AS grp ON gtr.representantelegal = grp.id_unico
          LEFT JOIN gf_telefono  AS gtl ON gtl.tercero            = grp.id_unico
          LEFT JOIN gf_direccion AS gdr ON gdr.tercero            = grp.id_unico
          LEFT JOIN gf_ciudad    AS gci ON gdr.ciudad_direccion   = gci.id_unico
          WHERE     gtr.id_unico = $tercero_fat";
$res_r = $mysqli->query($str_r);
if($res_r->num_rows > 0){
    $row_r = $res_r->fetch_row();
    $rep   = $row_r[1];
    $tel_t = $row_r[2];
    $dir_t = $row_r[3];
    $ciu_t = $row_r[4];
}

if(empty($rep)){
    $rep = $tercero_fat;
}

$rep = !empty($nomComerc)?$rep.' / '.$nomComerc:$rep;
$str = "SELECT      pln.codi,
                    conp.nombre,
                    dtf.cantidad,
                    dtf.valor,
                    dtf.iva,
                    dtf.impoconsumo,
                    dtf.ajuste_peso,
                    dtf.valor_total_ajustado,
                    dtf.valor_descuento, 
                    ud.nombre , 
                    tcn.nombre, 
                    dtf.valor_origen  
        FROM        gp_detalle_factura AS dtf
        LEFT JOIN   gp_concepto        AS conp ON conp.id_unico = dtf.concepto_tarifa
        LEFT JOIN   gf_plan_inventario AS pln  ON conp.plan_inventario = pln.id_unico
        LEFT JOIN   gf_unidad_factor   AS ud   ON dtf.unidad_origen = ud.id_unico 
        LEFT JOIN   gp_tipo_concepto   AS tcn  ON conp.tipo_concepto = tcn.id_unico 
        WHERE       md5(dtf.factura) = '".$_REQUEST['factura']."'";
$res  = $mysqli->query($str);
$data = $res->fetch_all(MYSQLI_NUM);

#BUSCAR RESOLUCIÓN
$resolucion = '';
$rs = "SELECT rf.descripcion, DATE_FORMAT(rf.fecha_inicial, '%d/%m/%Y'), rf.numero_inicial, rf.numero_final, tf.prefijo 
FROM gp_factura f 
LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
LEFT JOIN gp_resolucion_factura rf ON tf.id_unico = rf.tipo_factura 
WHERE md5(f.id_unico)='$factura' 
AND f.fecha_factura BETWEEN rf.fecha_inicial AND IF(rf.fecha_final IS NULL OR rf.fecha_final='0000-00-00', f.fecha_factura, rf.fecha_final)
AND f.numero_factura BETWEEN rf.numero_inicial AND rf.numero_final";
$rs = $mysqli->query($rs);
if(mysqli_num_rows($rs)>0){
    $rs = mysqli_fetch_row($rs);
    $resolucion = utf8_decode($rs[0].' - Fecha: '.$rs[1].' Autoriza Fact Pref '.$rs[4].' '.$rs[2].' AL '.$rs[3]);
}




if(empty($_REQUEST['t'])) { 


    ob_start();
    session_start();    # Session
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
            global $razonsocial;    #Nombre de compañia
            global $nombreTipoIden; #Tipo de identificación tercero
            global $tip_fat;        #Nombre de factura
            global $num_fat;        #Número de facutra
            global $ruta;           #Ruta de logo
            global $numeroIdent;    #Numero identificacion tercero
            global $resolucion;     #Resoulución de Factura
            global $direccion;      #Dirección Compañia
            global $telefono;       #Telefono Compañia;
            if($ruta != ''){
              $this->Image('../'.$ruta,10,8,20);
            }
            $this->SetFont('Helvetica','B',10);
            $this->SetXY(40,15);
            $this->MultiCell(140,5,utf8_decode($razonsocial),0,'C');
            $this->SetX(10);
            $this->MultiCell(200,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono")),0,'C');
            $this->SetFont('Helvetica','B',9);
            $this->Cell(200, 5, ($resolucion), 0, 0,'C');
            $this->Ln(5);
            $this->MultiCell(200,5,utf8_decode(ucwords(strtoupper($tip_fat))).' NRO: '.$num_fat,0,'C');
            $this->Ln(5);
        }
    }

    $pdf = new PDF('P','mm','Letter');      #Creación del objeto pdf
    $nb=$pdf->AliasNbPages();                       #Objeto de número de pagina
    $pdf->AddPage();                                #Agregar página
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetWidths(array(33.75, 60, 48.75, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array('FECHA', date("d/m/Y", strtotime($fecha_fat)), 'FECHA VENCIMIENTO', $fechaV_fat));
    $pdf->Ln(5);
    $pdf->SetWidths(array(33.75, 60, 48.75, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array(utf8_decode('SEÑOR(ES)'), utf8_decode($tercero_fat), utf8_decode('RAZÓN SOCIAL'), utf8_decode($rep)));
    $pdf->Ln(5);
    $pdf->SetWidths(array(33.75, 60, 48.75, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array(utf8_decode('NIT / CC'), $num_ter_f, utf8_decode('DIRECCIÓN'), utf8_decode($dir_t)));
    $pdf->Ln(5);
    $pdf->SetWidths(array(33.75, 60, 48.75, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row(array(utf8_decode('TELÉFONO'), $tel_t, 'CIUDAD', $ciu_t));
    $pdf->Ln(5);
    $pdf->SetWidths(array(33.75, 168.74));
    $pdf->SetAligns(array('L', 'L'));
    if (empty($desc_fat) || $desc_fat === 'NULL'){
        $desc_fat = '';
    }
    $pdf->Row(array(utf8_decode('OBSERVACIONES'), $desc_fat));
    $pdf->Ln(5);
    $pdf->Row(array(utf8_decode('FACTURA DE REFERENCIA: '), $rowF[14]));
    $pdf->Ln(15);
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell(30, 5, 'CODIGO', 'LTR', 0, 'C');
    $pdf->Cell(50, 5, 'PRODUCTO', 'LTR', 0, 'C');
    $pdf->Cell(20, 5, 'UNIDAD', 'LTR', 0, 'C');
    $pdf->Cell(20, 5, 'CANTIDAD', 'LTR', 0, 'C');
    $pdf->Cell(25, 5, 'PRECIO', 'LTR', 0, 'C');
    $pdf->Cell(25, 5, 'DESCUENTO', 'LTR', 0, 'C');
    $pdf->Cell(25, 5, 'TOTAL', 'LTR', 0, 'C');
    $pdf->Ln(5);
    $pdf->Cell(30, 5, '', 'LBR', 0, 'C');
    $pdf->Cell(50, 5, '', 'LBR', 0, 'C');
    $pdf->Cell(20, 5, '', 'LBR', 0, 'C');
    $pdf->Cell(20, 5, '', 'LBR', 0, 'C');
    $pdf->Cell(25, 5, 'UNITARIO', 'LBR', 0, 'C');
    $pdf->Cell(25, 5, '', 'LBR', 0, 'C');
    $pdf->Cell(25, 5, '', 'LBR', 0, 'C');
    $pdf->Ln(5);

    list($sumV, $sumIva, $sumImpo, $sumD) = array(0, 0, 0, 0);


    foreach ($data as $row){
        if($row[10]=='Exento'){
            $concepto = $row[1].'**';
        } else {
            $concepto = $row[1];
        }
        $sub      = $row[3] * $row[2];
        $sumV    += $sub;
        $sumIva  += ($row[4] * $row[2]);
        $sumImpo += ($row[5] * $row[2]);
        $pdf->SetFont('Helvetica','',8);
        $pdf->SetWidths(array(30, 50, 20, 20, 25, 25, 25));
        $pdf->SetAligns(array('L', 'L', 'R', 'L', 'R', 'R', 'R'));
        $pdf->fila(array($row[0], utf8_decode($concepto), utf8_decode($row[9]),  $row[2],number_format($row[11], 2), number_format($row[8], 2), number_format($sub, 2)));
        $pdf->Ln(5);
    }
    $pdf->SetFont('Helvetica', 'B', 9);
    $pdf->Cell(135, 5, '', 'LTR', 0, 'C');
    $pdf->Cell(30, 5, 'VENTA GRAVADA','TLR', 0, 'L');
    $pdf->Cell(30, 5, number_format($sumV, 2), 'TLR', 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(135, 5, '', 'LR', 0, 'C');
    $pdf->Cell(30, 5, 'VENTA EXCLUIDA','LR', 0, 'L');
    $pdf->Cell(30, 5, number_format($sumD, 2), 'LR', 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(135, 5, '', 'LR', 0, 'C');
    $pdf->Cell(30, 5, 'VALOR IVA','LR', 0, 'L');
    $pdf->Cell(30, 5, number_format($sumIva, 2), 'LR', 0, 'R');
    $pdf->Ln(5);
    $xxx = $sumIva + $sumV + $sumImpo;
    $pdf->Cell(135, 5, '', 'LRB', 0, 'C');
    $pdf->Cell(30, 5, 'NETO A PAGAR','LRB', 0, 'L');
    $pdf->Cell(30, 5, number_format($xxx, 2), 'LRB', 0, 'R');
    $pdf->Ln(5);
    $pdf->SetFont('Helvetica', '', 8);
    $pdf->Cell(115, 5, 'Esta Factura se asimila a la Letra de Cambio ART.774 Acepto la presente y declaro haber recibido el material mencionado', '', 0, 'L');
    $pdf->Ln(5);
    $pdf->SetFont('Helvetica', 'IB', 8);
    $pdf->Cell(115, 5, '** BIENES EXENTOS - DECRETO 417 DEL 17 DE MARZO DE 2020', '', 0, 'L');
    $pdf->Ln(15);
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetX(20);
    $pdf->Cell(50, 5, '', 'B', 0,'C');
    $pdf->SetX(80);
    $pdf->Cell(50, 5, '', 'B', 0,'C');
    $pdf->SetX(135);
    $pdf->Cell(50, 5, '', 'B', 0,'C');
    $pdf->Ln(5);
    $pdf->SetX(20);
    $pdf->Cell(50, 5, 'ELABORO', '', 0, 'C');
    $pdf->SetX(80);
    $pdf->Cell(50, 5,   utf8_decode('AUTORIZACIÓN'), '', 0, 'C');
    $pdf->SetX(135);
    $pdf->Cell(50, 5, 'RECIBIDO CLIENTE', '', 0, 'C');
    ob_end_clean();                                             #Limpieza del buffer
    $pdf->Output(0,'Informe_factura_'.$num_fat.'.pdf',0);       #Salida del documento
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_factura.xls");?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Factura</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="7" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreTipoIden.' : '.$numeroIdent."<br/>".$direccion.' TELÉFONO :'.$telefono ?>
                <br/>&nbsp;
                <br/><?php echo $resolucion;?>
                <br/><?php echo (ucwords(mb_strtoupper($tip_fat))).' NRO: '.$num_fat;?>
                <br/>&nbsp;
                </strong>
        </th>
        <?php 
        $html  = '';

        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>FECHA: </strong>'. date("d/m/Y", strtotime($fecha_fat)).'</td>';
        $html .= '<td colspan="3"><strong>FECHA VENCIMIENTO: </strong>'.$fechaV_fat.'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>SEÑOR(ES): </strong>'. utf8_decode($tercero_fat).'</td>';
        $html .= '<td colspan="3"><strong>RAZÓN SOCIAL:</strong>'.utf8_decode($rep).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>NIT / CC: </strong>'. ($num_ter_f).'</td>';
        $html .= '<td colspan="3"><strong>DIRECCIÓN:</strong>'.utf8_decode($dir_t).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>TELÉFONO: </strong>'. ($tel_t).'</td>';
        $html .= '<td colspan="3"><strong>DIRECCIÓN:</strong>'.utf8_decode($ciu_t).'</td>';
        $html .= '</tr>';
        if (empty($desc_fat) || $desc_fat === 'NULL'){
            $desc_fat = '';
        }
        $html .= '<tr>';
        $html .= '<td colspan="7"><strong>OBSERVACIONES: </strong>'. ($desc_fat).'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td><strong><center>CÓDIGO</center></strong></td>';
        $html .= '<td><strong><center>PRODUCTO</center></strong></td>';
        $html .= '<td><strong><center>UNIDAD</center></strong></td>';
        $html .= '<td><strong><center>CANTIDAD</center></strong></td>';
        $html .= '<td><strong><center>PRECIO UNITARIO</center></strong></td>';
        $html .= '<td><strong><center>DESCUENTO</center></strong></td>';
        $html .= '<td><strong><center>TOTAL</center></strong></td>';
        $html .= '</tr>';

        list($sumV, $sumIva, $sumImpo, $sumD) = array(0, 0, 0, 0);
        foreach ($data as $row){
            if($row[10]=='Exento'){
                $concepto = $row[1].'<strong>**</strong>';
            } else {
                $concepto = $row[1];
            }
            $sub      = $row[3] * $row[2];
            $sumV    += $sub;
            $sumIva  += ($row[4] * $row[2]);
            $sumImpo += ($row[5] * $row[2]);
            $html .= '<tr>';
            $html .= '<td>'.$row[0].'</td>';
            $html .= '<td>'.$concepto.'</td>';
            $html .= '<td>'.$row[9].'</td>';
            $html .= '<td>'.$row[2].'</td>';
            $html .= '<td>'.number_format($row[3], 2).'</td>';
            $html .= '<td>'.number_format($row[8], 2).'</td>';
            $html .= '<td>'.number_format($sub, 2).'</td>';
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>VENTA GRAVADA</strong></td>';
        $html .= '<td colspan="3"><strong><right>'.number_format($sumV, 2).'</right></strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>VENTA EXCLUIDA</strong></td>';
        $html .= '<td colspan="3"><strong><right>'.number_format($sumD, 2).'</right></strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>VENTA IVA</strong></td>';
        $html .= '<td colspan="3"><strong><right>'.number_format($sumIva, 2).'</right></strong></td>';
        $html .= '</tr>';
        $xxx = $sumIva + $sumV + $sumImpo;
        $html .= '<tr>';
        $html .= '<td colspan="4"><strong>NETO A PAGAR</strong></td>';
        $html .= '<td colspan="3"><strong><right>'.number_format($xxx, 2).'</right></strong></td>';
        $html .= '</tr>';
        
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';
        echo $html;

}