<?php

header("Content-Type: text/html;charset=utf-8");
session_start();
require '../Conexion/conexion.php';
require_once("../Conexion/ConexionPDO.php");
require '../numeros_a_letras.php';
require_once ('../modelAlmacen/movimiento.php');
$con    = new ConexionPDO(); 

//Captura de variables
$mov = $_GET['mov'];
$compania = $_SESSION['compania'];
//Array para igualar los numeros de meses
$meses = array('no','01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
$movimiento = new mov();
//Consulta para obtener los datos de la compañia
$rowC = $movimiento->data_compania($compania);
$razonSocial = $rowC[0]; $tipoIdent = $rowC[1]; $numeroIdent = $rowC[2]; $ruta = $rowC[3];//Razon social, tipo de identificación, numero de identificación, Ruta de logo
$rowm = $con->Listar("SELECT DISTINCT mov.id_unico ,
            mov.numero,
            tm.sigla, tm.nombre, 
            CONCAT(
                ELT(
                    WEEKDAY(mov.fecha) + 1,
                    'Lunes',
                    'Martes',
                    'Miercoles',
                    'Jueves',
                    'Viernes',
                    'Sabado',
                    'Domingo'
                )
            ) AS DIA_SEMANA,
            DATE_FORMAT(mov.fecha, '%d') as dia,
            DATE_FORMAT(mov.fecha, '%m') as mes,
            DATE_FORMAT(mov.fecha, '%Y') as anno,
            d.sigla,  d.nombre as origen, t.id_unico,  IF(
              concat_ws(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = ' ',
              t.razonsocial,
              concat_ws(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)
           ) as resorigen , dm.movimiento, 
           dd.sigla,  dd.nombre as destino, IF(
              concat_ws(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = ' ',
              tr.razonsocial,
              concat_ws(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos)
           ) as resdestino, 
           mov.descripcion , tm.id_unico 
        FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_movimiento mov ON dm.movimiento = mov.id_unico 
        LEFT JOIN gf_tipo_movimiento tm ON mov.tipomovimiento = tm.id_unico 
        LEFT JOIN gf_dependencia dd ON mov.dependencia = dd.id_unico
        LEFT JOIN gf_tercero tr ON mov.tercero = tr.id_unico 
        LEFT JOIN gf_detalle_movimiento dma ON dm.detalleasociado = dma.id_unico 
        LEFT JOIN gf_movimiento ma ON dma.movimiento = ma.id_unico 
        LEFT JOIN gf_dependencia d ON ma.dependencia = d.id_unico 
        LEFT JOIN gf_tercero t ON ma.tercero = t.id_unico 
        WHERE md5(dm.movimiento) = '".$_GET['mov']."'
        LIMIT 1");
$id_mov         = $rowm[0][0];
$tipo_mov       = $rowm[0][2].' - '.$rowm[0][3];
$numero_mov     = $rowm[0][1];
$dia_letras     = $rowm[0][4];
$n_dia          = $rowm[0][5];
$anno           = $rowm[0][7];
$id_tipo_mov    = $rowm[0][17];
if($_REQUEST['t']==1) { 
    @ob_start();
    //Archivos adjuntos
    require '../fpdf/fpdf.php';
    class PDF_MC_Table extends FPDF{
        var $widths;
        var $aligns;
        function SetWidths($w){
            $this->widths=$w;   //Obtenemos un  array con los anchos de las columnas
        }
        function SetAligns($a){
            $this->aligns=$a;   //Obtenemos un array con los alineamientos de las columnas
        }
        function fill($f){
            $this->fill=$f;     //Juego de arreglos de relleno
        }

        function Row_none($data){
            //Calculo del alto de una fila
            $nb=0;
            for($i=0;$i<count($data);$i++)
                $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h = 5*$nb;
            //Si una pagina tiene salto de linea
            $this->CheckPageBreak($h);
            //Dibujar las celdas de las fila
            for($i=0;$i<count($data);$i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Guardamos las posiciones actuales
                $x = $this->GetX();
                $y = $this->GetY();
                //Dibujamos el borde
                /** @var String $style */
                $this->Rect(0, 0, 0, 0, '');
                //Imprimimos el texto
                /** @var String $fill */
                $this->MultiCell($w, 4, $data[$i], '', $a, '');
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Go to the next line
            $this->Ln($h - 5);
        }

        function Row($data){
            //Calculo del alto de una fila
            $nb=0;
            for($i=0;$i<count($data);$i++)
                $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h = 5*$nb;
            //Si una pagina tiene salto de linea
            $this->CheckPageBreak($h);
            //Dibujar las celdas de las fila
            for($i=0;$i<count($data);$i++){
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Guardamos las posiciones actuales
                $x = $this->GetX();
                $y = $this->GetY();
                //Dibujamos el borde
                /** @var String $style */
                $this->Rect($x, $y, $w, $h, '');
                //Imprimimos el texto
                /** @var String $fill */
                $this->MultiCell($w, 4, $data[$i], 'LTR', $a, '');
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
            }
            //Go to the next line
            $this->Ln($h - 5);
        }
        function CheckPageBreak($h){
            //If the height h would cause an overflow, add a new page immediately
            if($this->GetY()+$h>$this->PageBreakTrigger)
                $this->AddPage($this->CurOrientation);
        }

        function NbLines($w, $txt){
            //Computes the number of lines a MultiCell of width w will take
            $cw=&$this->CurrentFont['cw'];
            if($w == 0)
                $w = $this->w-$this->rMargin-$this->x;
            $wmax=($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
            $s=str_replace('\r','',$txt);
            $nb=strlen($s);
            if($nb > 0 and $s[$nb-1] == '\n')
                $nb--;
            $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
            while($i < $nb){
                $c=$s[$i];
                if($c == '\n'){
                    $i++; $sep = -1; $j = $i; $l = 0; $nl++;
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
                        $i = $sep + 1;
                    $sep = -1; $j = $i; $l = 0; $nl++;
                }else
                    $i++;
            }
            return $nl;
        }

        #Funcón cabeza de la página
        function header(){
            #Redeclaración de varibles
            global $razonSocial;    #Nombre de compañia
            global $tipoIdent;      #Tipo de identificación
            global $numeroIdent;    #Nombre de comprobante
            global $ruta;           #Ruta de logo
            global $tipo_mov;       #Tipo de movimiento nombre
            global $numero_mov;     #Número de movimiento
            #Validación cuando la variable $ruta, la obtiene la ruta del logo no esta vacia
            if($ruta != '')  {
                $this->Image('../'.$ruta,10,10,18);
            }
            #Razón social
            $this->SetFont('Arial','B',12);
            $this->SetXY(40,15);
            $this->MultiCell(140,5,utf8_decode(strtoupper($razonSocial)),0,'C');
            #Tipo documento y número de documento
            $this->SetX(10);
            $this->Ln(1);
            $this->SetFont('Arial','B',9);
            $this->Cell(200,5,utf8_decode(strtoupper($tipoIdent).':'." ".$numeroIdent),0,0,'C');
            #Tipo de comprobante y número de comprobante
            $this->Ln(5);
            $this->SetFont('Arial','B',10);
            $this->Cell(200,5,utf8_decode(ucwords(strtoupper($tipo_mov." ".'Nª:')))." ".$numero_mov,0,0,'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(70,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
            $this->Cell(70,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
            $this->Cell(60,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }

    $pdf = new PDF_Mc_Table('P', 'mm', 'Letter');       #Creación del objeto pdf
    $nb=$pdf->AliasNbPages();       #Objeto de número de pagina
    $pdf->AddPage();                #Agregar página
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetWidths(array(30, 165));
    $pdf->SetAligns(array('L', 'L'));

    $pdf->Row_none(array('FECHA:', strtoupper($dia_letras.' '.$n_dia.' de '.$meses[$rowm[0][6]] .' de '.$anno)));
    $pdf->Ln(5);
    
    $pdf->SetWidths(array(30, 60, 30, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row_none(array(utf8_decode('DEPENDENCIA ORIGEN'), utf8_decode($rowm[0][8].' - '.$rowm[0][9]), utf8_decode('RESPONSABLE ORIGEN'), $rowm[0][11]));
    $pdf->Ln(5);
    $pdf->SetWidths(array(30, 60, 30, 60));
    $pdf->SetAligns(array('L', 'L', 'L', 'L'));
    $pdf->Row_none(array(utf8_decode('DEPENDENCIA DESTINO'), utf8_decode($rowm[0][13].' - '.$rowm[0][14]), utf8_decode('RESPONSABLE DESTINO'), $rowm[0][15]));
    $pdf->Ln(5);
    
    $pdf->SetWidths(array(30, 165));
    $pdf->SetAligns(array('L', 'L'));
    $pdf->Row_none(array(utf8_decode('DESCRIPCIÓN:'), utf8_decode(ucfirst(strtolower($rowm[0][16])))));
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(10, 5, '', 'LRT', 0,'C');
    $pdf->Cell(60, 5, utf8_decode('PLAN'), 'LRT', 0, 'C');
    $pdf->Cell(10, 5, 'CANT', 'LRT', 0, 'C');
    $pdf->Cell(40, 5, 'VALOR', 'LRT', 0, 'C');
    $pdf->Cell(40, 5, 'VALOR UNITARIO', 'LRT', 0, 'C');
    $pdf->Cell(40, 5, '', 'LRT', 0, 'C');
    $pdf->Ln(5);
    $pdf->Cell(10, 5, utf8_decode('Nª'), 'LRB', 0,'C');
    $pdf->Cell(60, 5, 'INVENTARIO', 'LRB', 0, 'C');
    $pdf->Cell(10, 5, 'IDAD', 'LRB', 0, 'C');
    $pdf->Cell(40, 5, 'UNITARIO', 'LRB', 0, 'C');
    $pdf->Cell(40, 5, 'IVA', 'LRB', 0, 'C');
    $pdf->Cell(40, 5, 'SUBTOTAL', 'LRB', 0, 'C');
    $devoltivos = array();

    $sqlP = "SELECT   dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva, pni.tipoinventario
            FROM      gf_detalle_movimiento dtm
            LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
            WHERE     dtm.movimiento = $id_mov";
    $resultP = $mysqli->query($sqlP);
    $a = 0; $valorTU = 0; $valorTI = 0; $valorTAA = 0;
    while ($rowP = mysqli_fetch_row($resultP)) {
        $a++;
        $valorTU  += ($rowP[3] * $rowP[2]); $valorTI += ($rowP[4] * $rowP[2]);
        $valorT   = ($rowP[3] + $rowP[4]) * $rowP[2];
        $valorTAA += $valorT;
        $valorTA  = number_format($valorT, 2, '.' , ',');
        $valorA   = number_format($rowP[3], 2, ',', '.');
        $valorI   = number_format($rowP[4], 2, ',', '.');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetWidths(array(10, 60, 10, 40, 40, 40));
        $pdf->SetAligns(array('C', 'L', 'C', 'R', 'R', 'R'));
        $pdf->Row(array($a,utf8_decode(mb_strtoupper($rowP[1])), number_format($rowP[2], 0), $valorA, $valorI, $valorTA));
        if($rowP[5] == 2){
            $devoltivos[] = $rowP[0];
        }
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(160, 5, 'SUBTOTAL', 'LRT', 0, 'C');
    $pdf->Cell(40, 5, number_format($valorTU, 2, ',', '.'), 'LTR', 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(160, 5, 'TOTAL IVA', 'LTR', 0, 'C');
    $pdf->Cell(40, 5, number_format($valorTI, 2, ',', '.'), 'LTR', 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(160, 5, 'TOTAL', 'LTRB', 0, 'C');
    $pdf->Cell(40, 5, number_format($valorTAA, 2, ',', '.'), 'LTRB', 0, 'R');
    $pdf->Ln(10);
    if(count($devoltivos) > 0){
        $xxx = 0;
        $yyy = 0;
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(200, 5,'LISTADO DE DEVOLUTIVOS', 1, 0, 'C');
        $pdf->Ln(5);
        $pdf->SetAligns(array('C', 'C', 'C', 'C'));
        $pdf->SetWidths(array(70, 20, 30, 80));
        $pdf->Row(array('ELEMENTO PLAN', 'PLACA', 'VALOR', utf8_decode('DESCRIPCIÓN')));
        $pdf->Ln(5);
        for ($i = 0; $i < count($devoltivos); $i++) {
            $rowDet = $movimiento->data_producto($devoltivos[$i]);
            foreach ($rowDet as $rowDD) {
                $xxx += $rowDD[3]; 
                #*** BUSCAR ESPECIFICACIONES DEL PRODUCTO;
                $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie 
                        FROM      gf_movimiento_producto mpr
                        LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                        LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                        LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                        LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                        WHERE  mpr.detallemovimiento = ".$devoltivos[$i]." AND pre.fichainventario != 6");
                $descripcion = "";
                for ($j = 0; $j < count($vp); $j++) {
                    $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                }
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetAligns(array('L', 'R', 'R', 'L'));
                $pdf->SetWidths(array(70, 20, 30, 80));
                $pdf->Row(array(utf8_decode($rowDD[1]), $rowDD[2], number_format($rowDD[3], 2, ',', '.'), utf8_decode($descripcion)));
                $pdf->Ln(5);
            }
        }
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetAligns(array('C', 'R', 'R'));
        $pdf->SetWidths(array(90, 30));
        $pdf->Row(array('TOTAL', number_format($xxx, 2, ',', '.')));
        $pdf->Ln(5);
    }

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetAligns(array('R', 'L'));
    $pdf->SetWidths(array(35, 165));
    $pdf->Row(array(utf8_decode('VALOR EN LETRAS:'), utf8_decode(numtoletras($valorTAA))));
    $pdf->Ln(30);
    $yy1 = $pdf->GetY();
    
    $data_firmas = $movimiento->data_firmas($id_tipo_mov);
    $xxx = 0;
    
    foreach($data_firmas as $row_firma){
        if($xxx == 1){
            $yyy = $yy1;
        }
        $xxx++;
        if($xxx % 2 == 0){
            $pdf->SetXY(140, $yyy);
            $pdf->Cell(60, 0, '', 'B');
            $pdf->Ln(3);
            $pdf->SetX(140);
            $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->SetX(140);
            $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
            $pdf->Ln(40);
        }else{
            $yyy = $pdf->GetY();
            $pdf->Cell(60, 0, '', 'B');
            $pdf->Ln(3);
            $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
        }
    }
    
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(200,5,"HE RECIBIDO A COMFORMIDAD LOS ELEMENTOS DESCRITOS ANTERIORMENTE",0,0,'C');
    #Final del documento
    
    while (ob_get_length()) {
        ob_end_clean();#Limpieza del buffer
    }
    #Salida del documento
    $nombre_doc = utf8_decode("informeEntradaAlmacenNª$numero_mov.pdf");
    $pdf->Output(0,$nombre_doc,0);
} else { 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Entrada_Almacen.xls");    
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Entrada Almacén</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="6" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonSocial ?>
            <br/><?php echo $tipoIdent.' : '.$numeroIdent ?>
            <br/>&nbsp;
            <br/><?php echo $tipo_mov." Nª:".$numero_mov ?>
            <br/>&nbsp;                 
            </strong> 
        </th>
        <tr></tr>    
        <?php 
        echo '<tr>';
        echo '<td colspan="6"><strong>'.$dia_letras.' '.$n_dia.' de '.$meses[$rowm[0][6]] .' de '.$anno.'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="3"><strong>DEPENDENCIA ORIGEN: '.$rowm[0][8].' - '.$rowm[0][9].'</strong></td>';
        echo '<td colspan="3"><strong>RESPONSABLE ORIGEN: '.$rowm[0][11].'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="3"><strong>DEPENDENCIA DESTINO: '.$rowm[0][13].' - '.$rowm[0][14].'</strong></td>';
        echo '<td colspan="3"><strong>RESPONSABLE DESTINO: '.$rowm[0][15].'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="6"><strong>DESCRIPCIÓN: '.$rowm[0][16].'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td><strong>Nª</strong></td>';
        echo '<td><strong>PLAN INVENTARIO</strong></td>';
        echo '<td><strong>CANTIDAD</strong></td>';
        echo '<td><strong>VALOR UNITARIO</strong></td>';
        echo '<td><strong>VALOR UNITARIO IVA</strong></td>';
        echo '<td><strong>SUBTOTAL</strong></td>';
        echo '</tr>';
        
        $devoltivos = array();
        $sqlP = "SELECT   dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva, pni.tipoinventario
            FROM      gf_detalle_movimiento dtm
            LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
            WHERE     dtm.movimiento = $id_mov";
        $resultP = $mysqli->query($sqlP);
        $a = 0; $valorTU = 0; $valorTI = 0; $valorTAA = 0;
        while ($rowP = mysqli_fetch_row($resultP)) {
            $a++;
            $valorTU  += ($rowP[3] * $rowP[2]); $valorTI += ($rowP[4] * $rowP[2]);
            $valorT   = ($rowP[3] + $rowP[4]) * $rowP[2];
            $valorTAA += $valorT;
            $valorTA  = number_format($valorT, 2, '.' , ',');
            $valorA   = number_format($rowP[3], 2, '.', ',');
            $valorI   = number_format($rowP[4], 2, '.', ',');
            if($rowP[5] == 2){
                $devoltivos[] = $rowP[0];
            }
            echo '<tr>';
            echo '<td>'.$a.'</td>';
            echo '<td>'.mb_strtoupper($rowP[1]).'</td>';
            echo '<td>'.number_format($rowP[2], 0).'</td>';
            echo '<td>'.$valorA.'</td>';
            echo '<td>'.$valorI.'</td>';
            echo '<td>'.$valorTA.'</td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td colspan="5"><strong>SUBTOTAL</strong></td>';
        echo '<td><strong>'.number_format($valorTU, 2, '.', ',').'</strong></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="5"><strong>TOTAL IVA</strong></td>';
        echo '<td><strong>'.number_format($valorTI, 2, '.', ',').'</strong></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="5"><strong>TOTAL</strong></td>';
        echo '<td><strong>'.number_format($valorTAA, 2, '.', ',').'</strong></td>';
        echo '</tr>';
        if(count($devoltivos) > 0){
            $xxx = 0;
            $yyy = 0;
            echo '<tr>';
            echo '<td colspan="6"><strong>LISTADO DE DEVOLUTIVOS</strong></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td colspan="2"><strong>ELEMENTO PLAN</strong></td>';
            echo '<td><strong>PLACA</strong></td>';
            echo '<td><strong>VALOR</strong></td>';
            echo '<td colspan="2"><strong>DESCRIPCIÓN</strong></td>';
            echo '</tr>';

            for ($i = 0; $i < count($devoltivos); $i++) {
                $rowDet = $movimiento->data_producto($devoltivos[$i]);
                foreach ($rowDet as $rowDD) {
                    $xxx += $rowDD[3]; 
                    #*** BUSCAR ESPECIFICACIONES DEL PRODUCTO;
                    $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie 
                            FROM      gf_movimiento_producto mpr
                            LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                            LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                            LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                            LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                            LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                            WHERE  mpr.detallemovimiento = ".$devoltivos[$i]." AND pre.fichainventario != 6");
                    $descripcion = "";
                    for ($j = 0; $j < count($vp); $j++) {
                        $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                    }
                    echo '<tr>';
                    echo '<td colspan="2">'.$rowDD[1].'</td>';
                    echo '<td>'.$rowDD[2].'</td>';
                    echo '<td>'.number_format($rowDD[3], 2, '.', ',').'</td>';
                    echo '<td colspan="2">'.$descripcion.'</td>';
                    echo '</tr>';
                }
            }
            echo '<tr>';
            echo '<td colspan="3"><strong>TOTAL</strong></td>';
            echo '<td><strong>'.number_format($xxx, 2, '.', ',').'</strong></td>';
            echo '</tr>';
        }

        ?>
    </table>
    </body>
    </html>

<?php             
} ?>