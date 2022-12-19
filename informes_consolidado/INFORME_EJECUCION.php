<?php
@session_start();
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
$usuario    = $_SESSION['usuario'];
$fechaActual= date('d/m/Y');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$nanno      = anno($anno);
#***********************Datos Compañia***********************#
$compania   = $_SESSION['compania'];
$rowC       = $con->Listar("SELECT 
        ter.id_unico,
        ter.razonsocial,
        UPPER(ti.nombre),
        IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
            ter.numeroidentificacion, 
            CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
        dir.direccion,
        tel.valor,
        ter.ruta_logo 
    FROM            
        gf_tercero ter
    LEFT JOIN 	
        gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
    LEFT JOIN       
        gf_direccion dir ON dir.tercero = ter.id_unico
    LEFT JOIN 	
        gf_telefono  tel ON tel.tercero = ter.id_unico
    WHERE 
        ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$m2      = $_REQUEST['sltmesf'];
$codigoI = $_REQUEST['sltcodi'];
$codigoF = $_REQUEST['sltcodf'];
$month2  = $meses[(int)$m2];

#Informe de Gastos 
if($_REQUEST['c']==1){
    $row = $con->Listar("SELECT DISTINCT 
            cod_rubro               as codrub, 
            nombre_rubro            as nomrub,
            cod_rubro              as codfte,
            ptto_inicial            as ppti,
            adicion                 as adi,
            reduccion               as red,
            tras_credito            as tcred,
            tras_cont               as trcont,
            presupuesto_dfvo        as ppdf,
            disponibilidades        as disp,
            saldo_disponible        as sald,
            registros               as reg,
            registros_abiertos      as rega,
            total_obligaciones      as tobl,
            total_pagos             as tpag,
            reservas                as reserv,
            cuentas_x_pagar         as cpag,
            disponibilidad_abierta  as disAb 
    FROM temporal_pptal_consolidada ORDER BY cod_rubro ASC");
    if($_REQUEST['t']==1){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF{
        function Header(){             
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $month2;
            global $nanno;
            global $ruta_logo;
            global $codigoI;
            global $codigoF;
            $this->setX(0);
            $this->SetFont('Arial','B',10);
            $this->SetY(10);
            if($ruta_logo != ''){
                $this->Image('../'.$ruta_logo,60,6,20);
            }
            $this->Cell(340,5,utf8_decode($razonsocial),0,0,'C');
            $this->setX(0);
            $this->SetFont('Arial','B',8);
            $this->Cell(340,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
            $this->Ln(5);

            $this->SetFont('Arial','',8);
            $this->Cell(340, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
            $this->Cell(340,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

            $this->Ln(5);

            $this->SetFont('Arial','',8);
            $this->Cell(340,5,utf8_decode('EJECUCIÓN DEL PRESUPUESTO DE GASTOS CONSOLIDADO'),0,0,'C');
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
            $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
            $this->Ln(3); 

            $this->SetFont('Arial','',6);
            $this->Cell(340,5,utf8_decode($month2.' DE '.$nanno),0,0,'C');
            $this->Ln(5);
            $this->Cell(340,5,utf8_decode('RUBROS: '.$codigoI.' AL '.$codigoF),0,0,'C');
            $this->Ln(5);

            $this->SetX(20);
            $this->Cell(15,9, utf8_decode(''),1,0,'C');#
            $this->Cell(65,9,utf8_decode(''),1,0,'C');#
            $this->Cell(16,9,utf8_decode(''),1,0,'C');#
            $this->Cell(64,9,utf8_decode(''),1,0,'C');# 
            $this->Cell(16,9,utf8_decode(''),1,0,'C');#
            $this->Cell(18,9,utf8_decode(''),1,0,'C');#
            $this->Cell(16,9,utf8_decode(''),1,0,'C');#
            $this->Cell(18,9,utf8_decode(''),1,0,'C');#
            $this->Cell(16,9,utf8_decode(''),1,0,'C');#
            $this->Cell(16,9,utf8_decode(''),1,0,'C');#
            $this->Cell(16,9,utf8_decode(''),1,0,'C');
            $this->Cell(16,9,utf8_decode(''),1,0,'C');
            $this->Cell(16,9,utf8_decode(''),1,0,'C');
            $this->Cell(16,9,utf8_decode(''),1,0,'C');
            $this->SetX(20);

            $this->CellFitScale(15,9, utf8_decode('RUBRO'),1,0,'C');#
            $this->CellFitScale(65,9,utf8_decode('DETALLE'),1,0,'C');#
            $this->CellFitScale(16,8,utf8_decode('PRESUPUESTO'),0,0,'C');#
            $this->CellFitScale(64,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');
            $this->CellFitScale(16,8,utf8_decode('PRESUPUESTO'),0,0,'C');#
            $this->CellFitScale(18,9,utf8_decode('DISPONIBILIDADES'),1,0,'C');################
            $this->CellFitScale(16,8,utf8_decode('SALDO'),0,0,'C');#
            $this->CellFitScale(18,9,utf8_decode('DISPONIBILIDADES'),1,0,'C');################
            $this->CellFitScale(16,9,utf8_decode('REGISTROS'),1,0,'C');#
            $this->CellFitScale(16,8,utf8_decode('REGISTROS'),0,0,'C');#
            $this->CellFitScale(16,8,utf8_decode('TOTAL'),0,0,'C');#
            $this->CellFitScale(16,8,utf8_decode('TOTAL'),0,0,'C');#
            $this->CellFitScale(16,9,utf8_decode('RESERVAS'),1,0,'C');
            $this->CellFitScale(16,8,utf8_decode('CUENTAS'),0,0,'C');
            $this->Ln(4);
            $this->SetX(20);

            $this->CellFitScale(15,5,utf8_decode(' '),0,0,'C');#
            $this->CellFitScale(65,5,utf8_decode(' '),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode('INICIAL'),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode('ADICIÓN'),1,0,'C');
            $this->CellFitScale(16,5,utf8_decode('REDUCCIÓN'),1,0,'C');
            $this->CellFitScale(16,5,utf8_decode('TRAS.CREDITO'),1,0,'C');
            $this->CellFitScale(16,5,utf8_decode('TRAS.CONT'),1,0,'C');
            $this->CellFitScale(16,5,utf8_decode('DEFINITIVO'),0,0,'C');
            $this->CellFitScale(18,5,utf8_decode(' '),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode('DISPONIBLE'),0,0,'C');#
            $this->CellFitScale(18,5,utf8_decode('ABIERTAS'),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode(' '),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode('ABIERTOS'),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode('OBLIGACIONES'),0,0,'C');#
            $this->CellFitScale(16,5,utf8_decode('PAGOS'),0,0,'C');
            $this->CellFitScale(16,5,utf8_decode(' '),0,0,'C');
            $this->CellFitScale(16,5,utf8_decode('POR PAGAR'),0,0,'C');
            $this->Ln(5);
        }      

        function Footer()
        {
            global $usuario;
            global $fechaActual;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(15);
            $this->Cell(25,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
            $this->Cell(70);
            $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
            $this->Cell(60);
            $this->Cell(30,10,utf8_decode('Usuario:'.strtoupper($usuario)),0); 
            $this->Cell(70);
            $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0);
        }
    }
    $pdf = new PDF('L','mm','Legal');  
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    for ($i = 0; $i < count($row); $i++) {
        $p1  = (float) $row[$i]['ppti'];
        $p2  = (float) $row[$i]['adi'];
        $p3  = (float) $row[$i]['red'];
        $p4  = (float) $row[$i]['tcred'];
        $p5  = (float) $row[$i]['trcont'];
        $p6  = (float) $row[$i]['ppdf'];
        $p7  = (float) $row[$i]['disp'];
        $p8  = (float) $row[$i]['sald'];
        $p9  = (float) $row[$i]['reg'];
        $p10 = (float) $row[$i]['rega'];
        $p11 = (float) $row[$i]['tobl'];
        $p12 = (float) $row[$i]['tpag'];
        $p13 = (float) $row[$i]['reserv'];
        $p14 = (float) $row[$i]['cpag'];
        $p15 = (float) $row[$i]['disAb'];
        if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
        { } else {
            if($pdf->GetY()>170){
                $pdf->AddPage();     
            }
            $pdf->SetX(20);
            $x  = $pdf->GetX();        
            $y  = $pdf->GetY();
            $pdf->SetX($x+15);
            $pdf->MultiCell(65,3.5,utf8_decode($row[$i]['nomrub']),0,'L');        
            $y2 = $pdf->GetY();
            $h = $y2 - $y;  
            $pdf->SetXY($x, $y);
            $pdf->CellFitScale(15,$h,$row[$i]['codrub'],1,0,'L');
            $pdf->Cell(65,$h,'',1,0,'R'); 
            if(empty($p1)) {
                $pdf->Cell(16,$h,number_format($p1,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p1,2,'.',','),1,0,'R');
            }
            if(empty($p2)) {
                $pdf->Cell(16,$h,number_format($p2,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p2,2,'.',','),1,0,'R');
            }        
            if(empty($p3)) {
               $pdf->Cell(16,$h,number_format($p3,2,'.',','),1,0,'R');
            } else {
               $pdf->Cell(16,$h,number_format($p3,2,'.',','),1,0,'R');
            }
            if(empty($p4)) {
                $pdf->Cell(16,$h,number_format($p4,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p4,2,'.',','),1,0,'R');
            }

             if(empty($p5)) {
                $pdf->Cell(16,$h,number_format($p5,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p5,2,'.',','),1,0,'R');
            }

             if(empty($p6)) {
                $pdf->Cell(16,$h,number_format($p6,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p6,2,'.',','),1,0,'R');
            }

             if(empty($p7)) {
                $pdf->Cell(18,$h,number_format($p7,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(18,$h,number_format($p7,2,'.',','),1,0,'R');
            }


             if(empty($p8)) {
                $pdf->Cell(16,$h,number_format($p8,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p8,2,'.',','),1,0,'R');
            }

             if(empty($p15)) {
                $pdf->Cell(18,$h,number_format($p15,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(18,$h,number_format($p15,2,'.',','),1,0,'R');
            }

             if(empty($p9)) {
                $pdf->Cell(16,$h,number_format($p9,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p9,2,'.',','),1,0,'R');
            }


             if(empty($p10)) {
                $pdf->Cell(16,$h,number_format($p10,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p10,2,'.',','),1,0,'R');
            }


             if(empty($p11)) {
                $pdf->Cell(16,$h,number_format($p11,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p11,2,'.',','),1,0,'R');
            }

             if(empty($p12)) {
                $pdf->Cell(16,$h,number_format($p12,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p12,2,'.',','),1,0,'R');
            }

             if(empty($p13)) {
                $pdf->Cell(16,$h,number_format($p13,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p13,2,'.',','),1,0,'R');
            }

             if(empty($p14)) {
                $pdf->Cell(16,$h,number_format($p14,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(16,$h,number_format($p14,2,'.',','),1,0,'R');
            }
            $pdf->Ln($h);
        }
    }
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0,utf8_decode('Informe_Ejecucion_Pptal_Gastos_Consolidado('.date('d-m-Y').').pdf'),0);

} elseif($_REQUEST['t']==2){
    header("Content-Disposition: attachment; filename=Informe_Ejecucion_Pptal_Gastos_Consolidado.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Presupuestal de Gastos</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="17" align="center"><strong>
            <?php echo $razonsocial.'<br/>&nbsp;'.$nombreIdent.': '.$numeroIdent.'<br/>&nbsp;'.
                    'EJECUCIÓN DEL PRESUPUESTO DE GASTOS CONSOLIDADO'.'<br/>&nbsp;'.
                    $month2.' DE '.$nanno.'<br/>&nbsp;'.
                    'RUBROS: '.$codigoI.' AL '.$codigoF;
            ?>
            <br/>&nbsp;
            </strong>
        </th>
        <tr>
            <td rowspan="2" align="center"><strong>RUBRO</strong></td>
            <td rowspan="2" align="center"><strong>DETALLE</strong></td>
            <td rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
            <td colspan ="4" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
            <td rowspan="2" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
            <td rowspan="2" align="center"><strong>DISPONIBILIDADES</strong></td>
            <td rowspan="2" align="center"><strong>SALDO DISPONIBLE</strong></td>
            <td rowspan="2" align="center"><strong>DISPONIBILIDADES ABIERTAS</strong></td>
            <td rowspan="2" align="center"><strong>REGISTROS</strong></td>
            <td rowspan="2" align="center"><strong>REGISTROS ABIERTOS</strong></td>
            <td rowspan="2" align="center"><strong>TOTAL OBLIGACIONES</strong></td>
            <td rowspan="2" align="center"><strong>TOTAL PAGOS</strong></td>
            <td rowspan="2" align="center"><strong>RESERVAS</strong></td>
            <td rowspan="2" align="center"><strong>CUENTAS POR PAGAR</strong></td>
        </tr>
        <tr>
            <td  align="center"><strong>ADICION</strong></td>
            <td  align="center"><strong>REDUCCION</strong></td>
            <td  align="center"><strong>TRAS.CREDITO</strong></td> 
            <td  align="center"><strong>TRAS.CONT</strong></td>
        </tr>
        <?php for ($i = 0; $i < count($row); $i++) {
            $p1  = (float) $row[$i]['ppti'];
            $p2  = (float) $row[$i]['adi'];
            $p3  = (float) $row[$i]['red'];
            $p4  = (float) $row[$i]['tcred'];
            $p5  = (float) $row[$i]['trcont'];
            $p6  = (float) $row[$i]['ppdf'];
            $p7  = (float) $row[$i]['disp'];
            $p8  = (float) $row[$i]['sald'];
            $p9  = (float) $row[$i]['reg'];
            $p10 = (float) $row[$i]['rega'];
            $p11 = (float) $row[$i]['tobl'];
            $p12 = (float) $row[$i]['tpag'];
            $p13 = (float) $row[$i]['reserv'];
            $p14 = (float) $row[$i]['cpag'];
            $p15 = (float) $row[$i]['disAb'];
            if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
            { } else {
                echo '<tr>';
                echo '<td>'.$row[$i]['codrub'].'</td>';
                echo '<td>'.$row[$i]['nomrub'].'</td>';
                echo '<td>'.number_format($p1,2,'.',',').'</td>';
                echo '<td>'.number_format($p2,2,'.',',').'</td>';
                echo '<td>'.number_format($p3,2,'.',',').'</td>';
                echo '<td>'.number_format($p4,2,'.',',').'</td>';
                echo '<td>'.number_format($p5,2,'.',',').'</td>';
                echo '<td>'.number_format($p6,2,'.',',').'</td>';
                echo '<td>'.number_format($p7,2,'.',',').'</td>';
                echo '<td>'.number_format($p8,2,'.',',').'</td>';
                echo '<td>'.number_format($p15,2,'.',',').'</td>';
                echo '<td>'.number_format($p9,2,'.',',').'</td>';
                echo '<td>'.number_format($p10,2,'.',',').'</td>';
                echo '<td>'.number_format($p11,2,'.',',').'</td>';
                echo '<td>'.number_format($p12,2,'.',',').'</td>';
                echo '<td>'.number_format($p13,2,'.',',').'</td>';
                echo '<td>'.number_format($p14,2,'.',',').'</td>';
                echo '</tr>';
            }
        }?>
    </table>
</body>
</html>
<?php } 
#Informe Ingresos 
} elseif($_REQUEST['c']==2){
    $row = $con->Listar("SELECT DISTINCT 
            cod_rubro               as codrub, 
            nombre_rubro            as nomrub,
            cod_rubro               as codfte,
            ptto_inicial            as ppti,
            presupuesto_dfvo        as ppdf,
            adicion                 as adi,
            reduccion               as red,
            recaudos                as reca,
            saldos_x_recaudar       as spag 
    FROM temporal_pptal_consolidada ORDER BY cod_rubro ASC");
    if($_REQUEST['t']==1){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF{
        function Header(){             
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $month2;
            global $nanno;
            global $ruta_logo;
            global $codigoI;
            global $codigoF;
            $this->setX(0);
            $this->SetFont('Arial','B',10);
            $this->SetY(10);
            if($ruta_logo != ''){
                $this->Image('../'.$ruta_logo,60,6,20);
            }
            $this->Cell(340,5,utf8_decode($razonsocial),0,0,'C');
            $this->setX(0);
            $this->SetFont('Arial','B',8);
            $this->Cell(340,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
            $this->Ln(5);

            $this->SetFont('Arial','',8);
            $this->Cell(340, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
            $this->Cell(340,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

            $this->Ln(5);

            $this->SetFont('Arial','',8);
            $this->Cell(340,5,utf8_decode('EJECUCIÓN DEL PRESUPUESTO DE INGRESOS CONSOLIDADO'),0,0,'C');
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
            $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
            $this->Ln(3); 

            $this->SetFont('Arial','',6);
            $this->Cell(340,5,utf8_decode($month2.' DE '.$nanno),0,0,'C');
            $this->Ln(5);
            $this->Cell(340,5,utf8_decode('RUBROS: '.$codigoI.' AL '.$codigoF),0,0,'C');
            $this->Ln(5);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(23,9,utf8_decode(''),1,0,'C');#
            $this->Cell(94,9,utf8_decode(''),1,0,'C');#
            $this->Cell(35,9,utf8_decode(''),1,0,'C');#
            $this->Cell(72,9,utf8_decode(''),1,0,'C');#
            $this->Cell(35,9,utf8_decode(''),1,0,'C');#
            $this->Cell(35,9,utf8_decode(''),1,0,'C');
            $this->Cell(35,9,utf8_decode(''),1,0,'C');

            $this->SetX(10);
            $this->Cell(23,9,utf8_decode('RUBRO'),1,0,'C');#
            $this->Cell(94,9,utf8_decode('DETALLE'),1,0,'C');#
            $this->Cell(35,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
            $this->Cell(72,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');#
            $this->Cell(35,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
            $this->Cell(35,9,utf8_decode('RECAUDO'),0,0,'C');#
            $this->Cell(35,7,utf8_decode('SALDOS POR'),0,0,'C');#
            $this->Ln(4);
            
            $this->SetX(10);
            $this->Cell(23,5,utf8_decode(''),0,0,'C');#
            $this->Cell(94,5,utf8_decode(''),0,0,'C');#
            $this->Cell(35,5,utf8_decode('INICIAL'),0,0,'C');#
            $this->Cell(36,5,utf8_decode('ADICIÓN'),1,0,'C');
            $this->Cell(36,5,utf8_decode('REDUCCIÓN'),1,0,'C');
            $this->Cell(35,5,utf8_decode('DEFINITIVO'),0,0,'C');
            $this->Cell(35,5,utf8_decode(''),0,0,'C');#
            $this->Cell(35,5,utf8_decode('RECAUDAR'),0,0,'C');#
            $this->Ln(5);
        }      

        function Footer()
        {
            global $usuario;
            global $fechaActual;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(15);
            $this->Cell(25,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
            $this->Cell(70);
            $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
            $this->Cell(60);
            $this->Cell(30,10,utf8_decode('Usuario:'.strtoupper($usuario)),0); 
            $this->Cell(70);
            $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0);
        }
    }
    $pdf = new PDF('L','mm','Legal');  
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',7);
    for ($i = 0; $i < count($row); $i++) {
        $p1  = (float) $row[$i]['ppti'];
        $p2  = (float) $row[$i]['adi'];
        $p3  = (float) $row[$i]['red'];
        $p4  = (float) $row[$i]['ppdf'];
        $p5  = (float) $row[$i]['reca'];
        $p6  = (float) $row[$i]['spag'];
        if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0)
        { } else {
            if($pdf->GetY()>170){
                $pdf->AddPage();     
            }
            
            $pdf->SetX(10);
            $x  = $pdf->GetX();        
            $y  = $pdf->GetY();
            $pdf->SetX($x+23);
            $pdf->MultiCell(94,3.5,utf8_decode($row[$i]['nomrub']),0,'L');        
            $y2 = $pdf->GetY();
            $h = $y2 - $y;  
            $pdf->SetXY($x, $y);
            $pdf->CellFitScale(23,$h,$row[$i]['codrub'],1,0,'L');
            $pdf->Cell(94,$h,'',1,0,'R'); 
            if(empty($p1)) {
                $pdf->Cell(35,$h,number_format($p1,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(35,$h,number_format($p1,2,'.',','),1,0,'R');
            }
            if(empty($p2)) {
                $pdf->Cell(36,$h,number_format($p2,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(36,$h,number_format($p2,2,'.',','),1,0,'R');
            }        
            if(empty($p3)) {
               $pdf->Cell(36,$h,number_format($p3,2,'.',','),1,0,'R');
            } else {
               $pdf->Cell(36,$h,number_format($p3,2,'.',','),1,0,'R');
            }
            
            if(empty($p4)) {
                $pdf->Cell(35,$h,number_format($p4,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(35,$h,number_format($p4,2,'.',','),1,0,'R');
            }
             if(empty($p5)) {
                $pdf->Cell(35,$h,number_format($p5,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(35,$h,number_format($p5,2,'.',','),1,0,'R');
            }
            if(empty($p6)) {
                $pdf->Cell(35,$h,number_format($p6,2,'.',','),1,0,'R');
            } else {
                $pdf->CellFitScale(35,$h,number_format($p6,2,'.',','),1,0,'R');
            }
            $pdf->Ln($h);
        }
    }
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0,utf8_decode('Informe_Ejecucion_Pptal_Ingresos_Consolidado('.date('d-m-Y').').pdf'),0);

} elseif($_REQUEST['t']==2){
    header("Content-Disposition: attachment; filename=Informe_Ejecucion_Pptal_Ingresos_Consolidado.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Presupuestal de Ingresos</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="8" align="center"><strong>
            <?php echo $razonsocial.'<br/>&nbsp;'.$nombreIdent.': '.$numeroIdent.'<br/>&nbsp;'.
                    'EJECUCIÓN DEL PRESUPUESTO DE INGRESOS CONSOLIDADO'.'<br/>&nbsp;'.
                    $month2.' DE '.$nanno.'<br/>&nbsp;'.
                    'RUBROS: '.$codigoI.' AL '.$codigoF;
            ?>
            <br/>&nbsp;
            </strong>
        </th>
        <tr>
            <td rowspan="2" align="center"><strong>RUBRO</strong></td>
            <td rowspan="2" align="center"><strong>DETALLE</strong></td>
            <td rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
            <td colspan ="2" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
            <td rowspan="2" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
            <td rowspan="2" align="center"><strong>RECAUDOS</strong></td>
            <td rowspan="2" align="center"><strong>SALDOS POR RECAUDAR</strong></td>
        </tr>
        <tr>
            <td  align="center"><strong>ADICION</strong></td>
            <td  align="center"><strong>REDUCCION</strong></td>
        </tr>
        <?php for ($i = 0; $i < count($row); $i++) {
            $p1  = (float) $row[$i]['ppti'];
            $p2  = (float) $row[$i]['adi'];
            $p3  = (float) $row[$i]['red'];
            $p4  = (float) $row[$i]['ppdf'];
            $p5  = (float) $row[$i]['reca'];
            $p6  = (float) $row[$i]['spag'];
            if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0)
            { } else {
                echo '<tr>';
                echo '<td>'.$row[$i]['codrub'].'</td>';
                echo '<td>'.$row[$i]['nomrub'].'</td>';
                echo '<td>'.number_format($p1,2,'.',',').'</td>';
                echo '<td>'.number_format($p2,2,'.',',').'</td>';
                echo '<td>'.number_format($p3,2,'.',',').'</td>';
                echo '<td>'.number_format($p4,2,'.',',').'</td>';
                echo '<td>'.number_format($p5,2,'.',',').'</td>';
                echo '<td>'.number_format($p6,2,'.',',').'</td>';
                echo '</tr>';
            }
        }?>
    </table>
</body>
</html>
<?php }    
#Informe Gerencial Gastos
}