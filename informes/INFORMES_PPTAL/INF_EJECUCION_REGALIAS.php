<?php
require'../../Conexion/conexion.php';
require'../../Conexion/ConexionPDO.php';
require'../../jsonPptal/funcionesPptal.php';
@session_start();
$con = new ConexionPDO();

$usuario    =   $_SESSION['usuario'];
$compania   =   $_SESSION['compania'];
$calendario = CAL_GREGORIAN;
$parmanno   = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$anno       = anno($parmanno);
$mes        = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia        = cal_days_in_month($calendario, $mes, $anno); 
$fecha      = $anno.'-'.$mes.'-'.$dia;
$codigoI    = $mysqli->real_escape_string(''.$_POST['sltcni'].'');
$codigoF    = $mysqli->real_escape_string(''.$_POST['sltcnf'].'');
$fuente     = $mysqli->real_escape_string(''.$_POST['fuente'].'');

$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);


#************Datos Compañia************#
$sqlC = "SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$ruta_logo   = $rowC[6];
$meses  = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$month1 = $meses[(int)$mes];


switch($_REQUEST['t']){
    case 1:
        ini_set('max_execution_time', 0);
        require'../../fpdf/fpdf.php';
        ob_start();
        regalias_gastos($codigoI, $codigoF, $parmanno, $fuente, $fecha);
        class PDF extends FPDF { 
            function Header(){ 
                global $razonsocial;
                global $ruta_logo;
                global $nombreIdent;
                global $numeroIdent;
                global $month1;
                global $anno;
                global $codigoI;
                global $codigoF;

                $this->setX(0);
                $this->SetFont('Arial','B',10);
                $this->SetY(10);
                if($ruta_logo != '') {
                    $this->Image('../../'.$ruta_logo,60,6,20);
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
                $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE REGALIAS - GASTOS'),0,0,'C');
                $this->SetFont('Arial','B',8);
                $this->SetX(0);
                $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
                $this->Ln(3); 
                $this->SetFont('Arial','',6);
                $this->Cell(340,5,utf8_decode('RUBROS DEL '.$codigoI.' - '.$codigoF),0,0,'C');
                $this->Ln(3); 
                $this->Cell(340,5,utf8_decode('MES DE '.utf8_decode (ucwords(strtoupper($month1))).' '.$anno),0,0,'C');

                $this->Ln(5);
                $this->SetX(20);
                $this->Cell(15,9, utf8_decode(''),1,0,'C');#
                $this->Cell(40,9,utf8_decode(''),1,0,'C');#
                $this->Cell(25,9,utf8_decode(''),1,0,'C');################
                $this->Cell(16,9,utf8_decode(''),1,0,'C');#
                $this->Cell(64,9,utf8_decode(''),1,0,'C');# 
                $this->Cell(16,9,utf8_decode(''),1,0,'C');#
                $this->Cell(18,9,utf8_decode(''),1,0,'C');###################
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
                $this->CellFitScale(40,9,utf8_decode('DETALLE'),1,0,'C');#
                $this->CellFitScale(25,9,utf8_decode('FUENTE'),1,0,'C');################
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
                $this->CellFitScale(40,5,utf8_decode(' '),0,0,'C');#
                $this->CellFitScale(25,5,utf8_decode(' '),0,0,'C');###############################
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
                $this->Cell(326,5,'',0);
            } 
            function Footer() {
                global $usuario;
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->Cell(15);
                $this->Cell(25,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
                $this->Cell(70);
                $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
                $this->Cell(60);
                $this->Cell(30,10,utf8_decode('Usuario:'.strtoupper($usuario)),0); 
                $this->Cell(70);
                $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0);
            }
        }
        $pdf = new PDF('L','mm','Legal');        
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $sql2 = "SELECT DISTINCT 
            cod_rubro               as codrub, 
            nombre_rubro            as nomrub,
            cod_fuente              as codfte,
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
        from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
        $conejc  = $mysqli->query($sql2);
        $pdf->SetFont('Arial','',6); 
        while ($filactas = mysqli_fetch_array($conejc)) {
            $pdf->setX(20);    
            $p1  = (float) $filactas['ppti'];
            $p2  = (float) $filactas['adi'];
            $p3  = (float) $filactas['red'];
            $p4  = (float) $filactas['tcred'];
            $p5  = (float) $filactas['trcont'];
            $p6  = (float) $filactas['ppdf'];
            $p7  = (float) $filactas['disp'];
            $p8  = (float) $filactas['sald'];
            $p9  = (float) $filactas['reg'];
            $p10 = (float) $filactas['rega'];
            $p11 = (float) $filactas['tobl'];
            $p12 = (float) $filactas['tpag'];
            $p13 = (float) $filactas['reserv'];
            $p14 = (float) $filactas['cpag'];
            $p15 = (float) $filactas['disAb'];
            if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0){ 
            } else {
                $a = $pdf->GetY();
                if($a>160){
                    $pdf->AddPage();     
                    $pdf->setX(20); 
                }
                $pdf->Cell(15,4,'',0,0,'R');        
                $y = $pdf->GetY();
                $x = $pdf->GetX();        
                $pdf->MultiCell(40,3.5,utf8_decode($filactas['nomrub']),0,'L');        
                $y2 = $pdf->GetY();
                $h1 = $y2-$y;
                $pdf->Ln(-$h1);
                $pdf->SetX($x+40);
                $pdf->MultiCell(25,3.5,utf8_decode($filactas['codfte']),0,'L');        
                $y3 = $pdf->GetY();
                $h2 = $y3-$y;
                $h = max($h1, $h2);
                $px = $x + 60;
                $pdf->SetY($y);
                $pdf->SetX(20);
                if(!empty($filactas['codrub'])) {
                    $pdf->CellFitScale(15,$h,$filactas['codrub'],1,0,'L');
                } else {
                    $pdf->Cell(15,$h,'',1,0,'L');
                }
                $pdf->Cell(40,$h,'',1,0,'R'); 
                $pdf->Cell(25,$h,'',1,0,'R'); 
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
        $pdf->setX(20);
        $pdf->Cell(320,0.5,utf8_decode(''),1,0,'C');
        $pdf->Cell(31,4,utf8_decode($filactas['cnnom']),0,0,'C');

        $pdf->SetFont('Arial','B',9);
        $pdf->Ln(10);
        $compania = $_SESSION['compania'];
        $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
                LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
                LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
                WHERE LOWER(td.nombre) ='ejecucion presupuestal' AND td.compania = $compania  ORDER BY rd.orden ASC";
        $res= $mysqli->query($res);
        $i=0;
        $x=130;
        #ESTRUCTURA
        if(mysqli_num_rows($res)>0){
             $h=4;
             while ($row2 = mysqli_fetch_row($res)) {

                 $ter = "SELECT IF(CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) 
                            IS NULL OR CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) = '',
                            (tr.razonsocial),
                            CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos)) AS NOMBREC, "
                         . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                         . "FROM gf_tercero tr "
                         . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                         . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                         . "WHERE tr.id_unico ='$row2[0]'";

                 $ter = $mysqli->query($ter);
                 $ter = mysqli_fetch_row($ter);
                 if(!empty($ter[3])){
                         $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
                 } else {
                     $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
                 }

                 $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');

                 if($i==1){
                   $pdf->Ln(15);
                   $x=130;
                   $i=0;
                 } else {
                 $pdf->Ln(-25);
                 $pdf->SetX($x);
                 $x=$x+110;
                  $i=$i+1;
                 }

             }

         } 
        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0,utf8_decode('Informe_Ejecucion_Regalias_Gastos'.date('d-m-Y').').pdf'),0);
    break;
    case 2:
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Informe_Ejecucion_Regalias_Gastos.xls");
        regalias_gastos($codigoI, $codigoF, $parmanno, $fuente, $fecha);
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>EJECUCIÓN REGALIAS - GASTOS</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <tr>
                <th colspan="19" align="center"><strong>
                    <br/>&nbsp;
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                   <br/>&nbsp;
                   <br/>EJECUCIÓN PRESUPUESTAL REGALIAS - GASTOS
                   <br/>Rubros del <?php echo $codigoI.' al '.$codigoF ?>
                   <br/>Mes Acumulado <?php echo $month1.' - '.$anno ?><br/>&nbsp;</strong>
                </th>
          </tr>
          <tr>
                <td rowspan="2" align="center"><strong>RUBRO</strong></td>
                <td colspan="1" rowspan="2"align="center"><strong>DETALLE</strong></td>
                <td rowspan="2" align="center"><strong>FUENTE</strong></td> 
                <td colspan ="2" rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>
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
        <?php
        #Consulta Cuentas
        $sql2 = "SELECT DISTINCT 
            cod_rubro               as codrub, 
            nombre_rubro            as nomrub,
            cod_fuente              as codfte,
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
        from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
        $conejc  = $mysqli->query($sql2);
        while ($filactas = mysqli_fetch_array($conejc)) {     
            $p1  = (float) $filactas['ppti'];
            $p2  = (float) $filactas['adi'];
            $p3  = (float) $filactas['red'];
            $p4  = (float) $filactas['tcred'];
            $p5  = (float) $filactas['trcont'];
            $p6  = (float) $filactas['ppdf'];
            $p7  = (float) $filactas['disp'];
            $p8  = (float) $filactas['sald'];
            $p9  = (float) $filactas['reg'];
            $p10 = (float) $filactas['rega'];
            $p11 = (float) $filactas['tobl'];
            $p12 = (float) $filactas['tpag'];
            $p13 = (float) $filactas['reserv'];
            $p14 = (float) $filactas['cpag'];
            $p15 = (float) $filactas['disAb'];
               # $codd = $codd + 1;
            if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
            { } else {       
                echo '<tr>';
                echo '<td>'.$filactas['codrub'].'</td>';
                echo '<td>'.$filactas['nomrub'].'</td>';
                echo '<td align="center">'.$filactas['codfte'].'</td>';
                echo '<td align="right" colspan="2">'.number_format($p1 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p2 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p3 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p4 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p5 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p6 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p7 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p8 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p15 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p9 ,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p10,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p11,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p12,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p13,2,'.',',').'</td>';
                echo '<td align="right">'.number_format($p14,2,'.',',').'</td>';
                echo '</tr>';
            }
        }
        ?>
        </table>
        </body>
        </html>
        <?php     
    break;
    case 3:
        ini_set('max_execution_time', 0);
        require'../../fpdf/fpdf.php';
        ob_start();
        regalias_ingresos($codigoI, $codigoF, $parmanno, $fuente, $fecha);
        class PDF extends FPDF{
            function Header() { 
                global $razonsocial;
                global $ruta_logo;
                global $nombreIdent;
                global $numeroIdent;
                global $month1;
                global $anno;
                global $codigoI;
                global $codigoF;

                $this->SetFont('Arial','B',10);
                $this->SetY(10);
                if($ruta_logo != ''){
                    $this->Image('../../'.$ruta_logo,60,6,20);
                }
                $this->Cell(340,5,utf8_decode($razonsocial),0,0,'C');
                $this->setX(20);
                $this->SetFont('Arial','B',8);
                $this->Cell(320,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
                $this->Ln(5);
                $this->SetFont('Arial','',8);
                $this->Cell(340, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
                $this->SetFont('Arial','B',8);
                $this->SetX(20);
                $this->Cell(320,10,utf8_decode('VERSIÓN SGC'),0,0,'R');
                $this->Ln(5);
                $this->SetFont('Arial','',8);
                $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE REGALÍAS - INGRESOS'),0,0,'C');
                $this->SetFont('Arial','B',8);
                $this->SetX(20);
                $this->Cell(320,10,utf8_decode('FECHA SGC'),0,0,'R');
                $this->Ln(3); 
                $this->SetFont('Arial','',8);
                $this->Cell(340,5,utf8_decode('RUBROS DEL '.$codigoI.' - '.$codigoF),0,0,'C');
                $this->Ln(3);                
                $this->Cell(340,5,utf8_decode('MES DE '.utf8_decode (ucwords(strtoupper($month1))).' - '.$anno),0,0,'C');
                
                $this->Ln(5);
                $this->SetX(10);
                $this->Cell(23,9,utf8_decode(''),1,0,'C');#
                $this->Cell(70,9,utf8_decode(''),1,0,'C');#
                $this->Cell(24,9,utf8_decode(''),1,0,'C');#
                $this->Cell(35,9,utf8_decode(''),1,0,'C');#
                $this->Cell(72,9,utf8_decode(''),1,0,'C');#
                $this->Cell(35,9,utf8_decode(''),1,0,'C');#
                $this->Cell(35,9,utf8_decode(''),1,0,'C');
                $this->Cell(35,9,utf8_decode(''),1,0,'C');
                $this->SetX(10);
                $this->Cell(23,9,utf8_decode('RUBRO'),1,0,'C');#
                $this->Cell(70,9,utf8_decode('DETALLE'),1,0,'C');#
                $this->Cell(24,9,utf8_decode('FUENTE'),1,0,'C');#
                $this->Cell(35,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
                $this->Cell(72,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');#
                $this->Cell(35,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
                $this->Cell(35,9,utf8_decode('RECAUDO'),0,0,'C');#
                $this->Cell(35,7,utf8_decode('SALDOS POR'),0,0,'C');#
                $this->Ln(4);
                $this->SetX(10);
                $this->Cell(23,5,utf8_decode(''),0,0,'C');#
                $this->Cell(70,5,utf8_decode(''),0,0,'C');#
                $this->Cell(24,5,utf8_decode(''),0,0,'C');#
                $this->Cell(35,5,utf8_decode('INICIAL'),0,0,'C');#
                $this->Cell(36,5,utf8_decode('ADICIÓN'),1,0,'C');
                $this->Cell(36,5,utf8_decode('REDUCCIÓN'),1,0,'C');
                $this->Cell(35,5,utf8_decode('DEFINITIVO'),0,0,'C');
                $this->Cell(35,5,utf8_decode(''),0,0,'C');#
                $this->Cell(35,5,utf8_decode('RECAUDAR'),0,0,'C');#
                $this->Ln(5);
                $this->Cell(326,5,'',0);
            }      
            function Footer(){
                global $usuario;
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->Cell(15);
                $this->Cell(25,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
                $this->Cell(70);
                $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
                $this->Cell(60);
                $this->Cell(30,10,utf8_decode('Usuario: '.$usuario),0); 
                $this->Cell(70);
                $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
            }
        }
        $pdf = new PDF('L','mm','Legal');        
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial','',6);

        #Consulta Cuentas
        $sql2 = "SELECT DISTINCT 
                        cod_rubro           as codrub, 
                        nombre_rubro        as nomrub,
                        ptto_inicial        as ppti,
                        adicion             as adi,
                        reduccion           as red,
                        presupuesto_dfvo    as ppdf,
                        recaudos            as reca,
                        reservas            as reserv,
                        saldos_x_recaudar   as spag, 
                        cod_fuente          as fuente 
        from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
        $conejc  = $mysqli->query($sql2);
        $pdf->SetFont(Arial,'',7);
        $pdf->SetX(10);
        while ($filactas = mysqli_fetch_array($conejc)) {
            $p1  = (float) $filactas['ppti'];
            $p2  = (float) $filactas['adi'];
            $p3  = (float) $filactas['red'];
            $p4  = (float) $filactas['ppdf'];
            $p5  = (float) $filactas['reca'];
            $p6  = (float) $filactas['spag'];
            $a = $pdf->GetY();
            if($a > 190){
                $pdf->AddPage();
                $pdf->SetX(10);
            }
            if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0) { } 
            else {
                if($filactas['codrub']!="")
                    $pdf->cellfitscale(23,4,utf8_decode($filactas['codrub']),0,0,'L');
                else
                    $pdf->Cell(23,4,'',0,0,'L');
                $y = $pdf->GetY();
                $x = $pdf->GetX();        
                $pdf->MultiCell(70,4,utf8_decode($filactas['nomrub']),0,'L');
                $y2 = $pdf->GetY();
                $h = $y2-$y;
                $px = $x + 70;
                $pdf->Ln(-$h);
                $pdf->SetX($px);
                if(!empty($filactas['fuente']))
                    $pdf->cellfitscale(24,4,utf8_decode($filactas['fuente']),0,0,'L');
                else
                    $pdf->Cell(24,4,'',0,0,'L');        
                $pdf->cellfitscale(35,4,number_format($p1,2,'.',','),0,0,'R');
                $pdf->cellfitscale(36,4,number_format($p2,2,'.',','),0,0,'R');
                $pdf->cellfitscale(36,4,number_format($p3,2,'.',','),0,0,'R');
                $pdf->cellfitscale(35,4,number_format($p4,2,'.',','),0,0,'R');
                $pdf->cellfitscale(35,4,number_format($p5,2,'.',','),0,0,'R');
                $pdf->cellfitscale(35,4,number_format($p6,2,'.',','),0,0,'R');
                $pdf->Ln($h);
            }
        }

        $pdf->Cell(330,0.5,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,4,utf8_decode($filactas['cnnom']),0,0,'C');
        $pdf->SetFont('Arial','B',9);
        $pdf->Ln(10);
        $compania = $_SESSION['compania'];
        $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
               LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
               LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
               LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
               WHERE LOWER(td.nombre) ='ejecucion presupuestal' AND td.compania = $compania  ORDER BY rd.orden ASC";
        $res= $mysqli->query($res);
        $i=0;
        $x=130;
        #ESTRUCTURA
        if(mysqli_num_rows($res)>0){
            $h=4;
            while ($row2 = mysqli_fetch_row($res)) {
                $ter = "SELECT IF(CONCAT_WS(' ',
                           tr.nombreuno,
                           tr.nombredos,
                           tr.apellidouno,
                           tr.apellidodos) 
                           IS NULL OR CONCAT_WS(' ',
                           tr.nombreuno,
                           tr.nombredos,
                           tr.apellidouno,
                           tr.apellidodos) = '',
                           (tr.razonsocial),
                           CONCAT_WS(' ',
                           tr.nombreuno,
                           tr.nombredos,
                           tr.apellidouno,
                           tr.apellidodos)) AS NOMBREC, "
                        . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                        . "FROM gf_tercero tr "
                        . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                        . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                        . "WHERE tr.id_unico ='$row2[0]'";

                $ter = $mysqli->query($ter);
                $ter = mysqli_fetch_row($ter);
                $y = $pdf->GetY();
                if(!empty($ter[3])){
                        $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
                } else {
                    $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
                }

                $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');

                if($i==1){
                  $pdf->Ln(15);
                  $x=130;
                  $i=0;
                } else {
                   $pdf->SetXY($x, $y);
                   $x=$x+110;
                   $i=$i+1;
                }

            }

        } 
        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0,utf8_decode('Informe_Ejecucion_Regalias_Ingresos'.date('d-m-Y').').pdf'),0);
    break;
    case 4:
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Informe_Ejecucion_Regalias_Ingresos.xls");
        regalias_ingresos($codigoI, $codigoF, $parmanno, $fuente, $fecha);
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>EJECUCIÓN PRESUPUESTAL REGALÍAS - INGRESOS</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <tr>
                    <th colspan="9" align="center"><strong>
                        <br/>&nbsp;
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                        <br/>&nbsp;
                        <br/>EJECUCIÓN PRESUPUESTAL RENTAS E INGRESOS
                        <br/>Rubros del <?php echo $codigoI.' al '.$codigoF ?>
                        <br/>Mes Acumulado <?php echo $month1.' - '.$anno ?><br/>&nbsp;</strong>
                    </th>
                </tr>
                <tr>
                    <td rowspan="2" align="center"><strong>RUBRO</strong></td>
                    <td colspan="1" rowspan="2"align="center"><strong>DETALLE</strong></td>
                    <td colspan="1" rowspan="2"align="center"><strong>FUENTE</strong></td>
                    <td colspan ="1" rowspan="2" align="center"><strong>PRESUPUESTO INICIAL</strong></td>   
                    <td colspan ="2" align="center"><strong>MODIFICACIONES PRESUPUESTALES</strong></td>
                    <td rowspan="2" style="width:120px;" align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
                    <td rowspan="2" style="width:120px;" align="center"><strong>RECAUDO</strong></td>
                    <td rowspan="2" style="width:120px;" align="center"><strong>SALDOS POR RECAUDAR</strong></td>
                </tr>
                <tr>
                    <td  align="center"><strong>ADICION</strong></td>
                    <td  align="center"><strong>REDUCCION</strong></td>
                </tr>
                <?php
                #Consulta Cuentas
                $sql2 = "SELECT DISTINCT 
                    cod_rubro           as codrub, 
                    nombre_rubro        as nomrub,
                    ptto_inicial        as ppti,
                    adicion             as adi,
                    reduccion           as red,
                    presupuesto_dfvo    as ppdf,
                    recaudos            as reca,
                    reservas            as reserv,
                    saldos_x_recaudar   as spag,
                    cod_fuente          as fuente  
                from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
                $conejc  = $mysqli->query($sql2);
                while ($filactas = mysqli_fetch_array($conejc)) {
                    $p1  = (float) $filactas['ppti'];
                    $p2  = (float) $filactas['adi'];
                    $p3  = (float) $filactas['red'];
                    $p4  = (float) $filactas['ppdf'];
                    $p5  = (float) $filactas['reca'];
                    $p6  = (float) $filactas['spag'];
                    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0){ } 
                    else {          
                        echo '<tr>';
                        echo '<td>'.$filactas['codrub'].'</td>';
                        echo '<td>'.$filactas['nomrub'].'</td>';
                        echo '<td>'.$filactas['fuente'].'</td>';
                        echo '<td align="right">'.number_format($p1 ,2,'.',',').'</td>';
                        echo '<td align="right">'.number_format($p2 ,2,'.',',').'</td>';
                        echo '<td align="right">'.number_format($p3 ,2,'.',',').'</td>';
                        echo '<td align="right">'.number_format($p4 ,2,'.',',').'</td>';
                        echo '<td align="right">'.number_format($p5 ,2,'.',',').'</td>';
                        echo '<td align="right">'.number_format($p6 ,2,'.',',').'</td>';
                        echo '</tr>';
                    }
                } ?>
            </table>
        </body>
    </html>
    <?PHP 
    break;
}


function regalias_gastos($codigoI, $codigoF, $parmanno, $fuente, $fechaF){
    require'../../Conexion/conexion.php';
    $ctas = "SELECT DISTINCT
                rpp.nombre,
                rpp.codi_presupuesto,
                f.nombre,
                rpp2.codi_presupuesto, 
                rf.id_unico 
              FROM
                gf_rubro_pptal rpp
              LEFT JOIN
                gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
              LEFT JOIN
                gf_fuente f ON rf.fuente = f.id_unico
              LEFT JOIN
                gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
             WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
                 AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 10) 
                 AND rpp.parametrizacionanno = $parmanno 
            ORDER BY rpp.codi_presupuesto ASC";
    $ctass= $GLOBALS['mysqli']->query($ctas);
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
    while ($row1 = mysqli_fetch_row($ctass)) {
        $insert= "INSERT INTO temporal_consulta_pptal_gastos "
                . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
                . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
        $GLOBALS['mysqli']->query($insert);
    }
     $select ="SELECT DISTINCT
        rpp.nombre,
        rpp.codi_presupuesto,
        f.id_unico, 
        rpp2.codi_presupuesto, 
        dcp.rubrofuente 
      FROM
        gf_detalle_comprobante_pptal dcp
      LEFT JOIN
        gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
      LEFT JOIN
        gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
      LEFT JOIN
        gf_fuente f ON rf.fuente = f.id_unico
      LEFT JOIN
        gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
      LEFT JOIN 
        gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
      WHERE f.tipofuente ='$fuente' 
        AND rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
        ORDER BY rpp.codi_presupuesto ASC";
    $select1 = $GLOBALS['mysqli']->query($select);
    while($row = mysqli_fetch_row($select1)){
        #PRESUPUESTO INICIAL
        $pptoInicial= presupuestos_r($row[4], 1, $fechaF);
        #ADICION
        $adicion    = presupuestos_r($row[4], 2, $fechaF);
        #REDUCCION
        $reduccion  = presupuestos_r($row[4], 3, $fechaF);
        #TRAS.CRED Y CONT.
        $tras       = presupuestos_r($row[4], 4, $fechaF);
            if($tras>0){
                $trasCredito = $tras;
                $trasCont = 0;
            }else {
                $trasCredito = 0;
                $trasCont = $tras;
            }
            
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo  = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
        #DISPONIBILIDAD
        $disponibilidad         = disponibilidades_r($row[4], 14, $fechaF);
        #SALDO DISPONIBLE
        $saldoDisponible        = $presupuestoDefinitivo-$disponibilidad;
        #REGISTROS
        $registros              = disponibilidades_r($row[4], 15, $fechaF);
        #REGISTROS ABIERTOS
        $disponibilidadesAbiertas = $disponibilidad-$registros;
        #TOTAL OBLIGACIONES
        $totalObligaciones      = disponibilidades_r($row[4], 16, $fechaF);
        #REGISTROS ABIERTOS
        $registrosAbiertos      = $registros-$totalObligaciones;
        #TOTAL PAGOS
        $totalPagos             = disponibilidades_r($row[4], 17, $fechaF);
        #RESERVAS
        $reservas               = $registros-$totalObligaciones;
        #CUENTAS POR PAGAR
        $cuentasxpagar          = $totalObligaciones-$totalPagos;
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $update="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicial', "
                . "adicion = '$adicion', "
                . "reduccion = '$reduccion', "
                . "tras_credito = '$trasCredito', "
                . "tras_cont = '$trasCont', "
                . "presupuesto_dfvo = '$presupuestoDefinitivo', "
                . "disponibilidades = '$disponibilidad', "
                . "saldo_disponible = '$saldoDisponible', "
                . "disponibilidad_abierta = '$disponibilidadesAbiertas', "
                . "registros = '$registros', "
                . "registros_abiertos = '$registrosAbiertos', "
                . "total_obligaciones = '$totalObligaciones', "
                . "total_pagos = '$totalPagos', "
                . "reservas = '$reservas', "
                . "cuentas_x_pagar = '$cuentasxpagar' "
                . "WHERE rubro_fuente = '$row[4]'";
        $update = $GLOBALS['mysqli']->query($update);
    }    
    #CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
    $acum = "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, tras_credito, tras_cont, "
            . "presupuesto_dfvo, disponibilidades, "
            . "saldo_disponible,registros, "
            . "registros_abiertos,total_obligaciones, "
            . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
            . "FROM temporal_consulta_pptal_gastos "
            . "ORDER BY cod_rubro DESC ";
    $acum = $GLOBALS['mysqli']->query($acum);
    while ($rowa1= mysqli_fetch_row($acum)){
        $acumd = "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, tras_credito, tras_cont, "
            . "presupuesto_dfvo, disponibilidades, "
            . "saldo_disponible,registros, "
            . "registros_abiertos,total_obligaciones, "
            . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
            . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
            . "ORDER BY cod_rubro DESC ";
        $acumd = $GLOBALS['mysqli']->query($acumd);
        while ($rowa= mysqli_fetch_row($acumd)){
            if(!empty($rowa[2])){
                $va11= "SELECT id_unico, "
                . "cod_rubro,"
                . "cod_predecesor, "
                . "ptto_inicial, adicion, tras_credito, tras_cont, "
                . "presupuesto_dfvo, disponibilidades, "
                . "saldo_disponible,registros, "
                . "registros_abiertos,total_obligaciones, "
                . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
                . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
                $va1 = $GLOBALS['mysqli']->query($va11);
                $va= mysqli_fetch_row($va1);
                $pptoInicialM = $rowa[3]+$va[3];
                $adicionM = $rowa[4]+$va[4];
                $trasCreditoM = $rowa[5]+$va[5];
                $trasContM = $rowa[6]+$va[6];
                $presupuestoDefinitivoM = $rowa[7]+$va[7];
                $disponibilidadM = $rowa[8]+$va[8];
                $saldoDisponibleM = $rowa[9]+$va[9];
                $registrosM = $rowa[10]+$va[10];
                $registrosAbiertosM = $rowa[11]+$va[11];
                $totalObligacionesM = $rowa[12]+$va[12];
                $totalPagosM = $rowa[13]+$va[13];
                $reservasM = $rowa[14]+$va[14];
                $cuentasxpagarM = $rowa[15]+$va[15];
                $reduccionM = $rowa[16]+$va[16];
                $disponibilidadAbiertaM = $rowa[17]+$va[17];
                #ACTUALIZAR TABLA CON DATOS HALLADOS
                $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                    . "ptto_inicial ='$pptoInicialM', "
                    . "adicion = '$adicionM', "
                    . "reduccion = '$reduccionM', "
                    . "tras_credito = '$trasCreditoM', "
                    . "tras_cont = '$trasContM', "
                    . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                    . "disponibilidades = '$disponibilidadM', "
                    . "saldo_disponible = '$saldoDisponibleM', "
                    . "disponibilidad_abierta = '$disponibilidadAbiertaM', "
                    . "registros = '$registrosM', "
                    . "registros_abiertos = '$registrosAbiertosM', "
                    . "total_obligaciones = '$totalObligacionesM', "
                    . "total_pagos = '$totalPagosM', "
                    . "reservas = '$reservasM', "
                    . "cuentas_x_pagar = '$cuentasxpagarM' "
                    . "WHERE cod_rubro = '$rowa[2]'";
                $updateA = $GLOBALS['mysqli']->query($updateA);
            }
        }
    }

}
function regalias_ingresos($codigoI, $codigoF, $parmanno, $fuente, $fechaF){
    require'../../Conexion/conexion.php';
    $ctas = "SELECT DISTINCT
        rpp.nombre,
        rpp.codi_presupuesto,
        f.nombre,
        rpp2.codi_presupuesto, 
        rf.id_unico 
        FROM
        gf_rubro_pptal rpp
        LEFT JOIN
        gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
        LEFT JOIN
        gf_fuente f ON rf.fuente = f.id_unico
        LEFT JOIN
        gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
        WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
         AND rpp.tipoclase = 6 AND rpp.parametrizacionanno = '$parmanno' 
        ORDER BY rpp.codi_presupuesto ASC";
    $ctass= $GLOBALS['mysqli']->query($ctas);
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
    while ($row1 = mysqli_fetch_row($ctass)) {
        $insert= "INSERT INTO temporal_consulta_pptal_gastos "
                . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
                . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
        $GLOBALS['mysqli']->query($insert);

    }
    $select ="SELECT DISTINCT
        rpp.nombre,
        rpp.codi_presupuesto,
        f.id_unico, 
        rpp2.codi_presupuesto, 
        dcp.rubrofuente 
      FROM
        gf_detalle_comprobante_pptal dcp
      LEFT JOIN
        gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
      LEFT JOIN
        gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
      LEFT JOIN
        gf_fuente f ON rf.fuente = f.id_unico
      LEFT JOIN
        gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
      WHERE f.tipofuente ='$fuente' AND rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
            AND rpp.parametrizacionanno = $parmanno"; 
    $select1 = $GLOBALS['mysqli']->query($select);
    while($row = mysqli_fetch_row($select1)){

        #PRESUPUESTO INICIAL
        $pptoInicial= presupuestos_r($row[4], 1, $fechaF);
        #ADICION
        $adicion    = presupuestos_r($row[4], 2, $fechaF);
        #REDUCCION
        $reduccion  = presupuestos_r($row[4], 3, $fechaF);
        #PRESUPUESTO DEFINITIVO
        $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion;
        #RECAUDOS
        $recaudos = disponibilidades_r($row[4], 18,$fechaF);
        #SALDOS POR RECAUDAR
        $saldos = $presupuestoDefinitivo-$recaudos;
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $update="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicial', "
                . "adicion = '$adicion', "
                . "reduccion = '$reduccion', "
                . "presupuesto_dfvo = '$presupuestoDefinitivo', "
                . "recaudos = '$recaudos', "
                . "saldos_x_recaudar = '$saldos' "
                . "WHERE rubro_fuente = '$row[4]'";
        $update = $GLOBALS['mysqli']->query($update);

    }   
    $acum = "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, reduccion, "
            . "presupuesto_dfvo, recaudos, "
            . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos "
            . "ORDER BY cod_rubro DESC ";
    $acum = $GLOBALS['mysqli']->query($acum);
    while ($rowa1= mysqli_fetch_row($acum)){
        $acumd = "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, reduccion, "
            . "presupuesto_dfvo, recaudos, "
            . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
            . "ORDER BY cod_rubro DESC ";
        $acumd = $GLOBALS['mysqli']->query($acumd);
        while ($rowa= mysqli_fetch_row($acumd)){
            if(!empty($rowa[2])){
                $va11= "SELECT id_unico, "
                . "cod_rubro,"
                . "cod_predecesor, "
                . "ptto_inicial, adicion, reduccion, "
                . "presupuesto_dfvo, recaudos, "
                . "saldos_x_recaudar "
                . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
                $va1 = $GLOBALS['mysqli']->query($va11);
                $va= mysqli_fetch_row($va1);
                $pptoInicialM = $rowa[3]+$va[3];
                $adicionM = $rowa[4]+$va[4];
                $reduccionM = $rowa[5]+$va[5];
                $presupuestoDefinitivoM = $rowa[6]+$va[6];
                $recaudosM = $rowa[7]+$va[7];
                $saldosM = $rowa[8]+$va[8];
                #ACTUALIZAR TABLA CON DATOS HALLADOS
                $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                        . "ptto_inicial ='$pptoInicialM', "
                        . "adicion = '$adicionM', "
                        . "reduccion = '$reduccionM', "
                        . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                        . "recaudos = '$recaudosM', "
                        . "saldos_x_recaudar = '$saldosM' "
                        . "WHERE cod_rubro = '$rowa[2]'";
                $updateA = $GLOBALS['mysqli']->query($updateA);
            }
        }
    }
}
function presupuestos_r($id_rubF, $tipoO, $fechaF){
    require'../../Conexion/conexion.php';
    $presu = 0;
    $query = "SELECT valor as value 
        FROM
          gf_detalle_comprobante_pptal dc
        LEFT JOIN
          gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
        LEFT JOIN
          gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
        WHERE
          dc.rubrofuente = '$id_rubF' 
          AND tcp.tipooperacion = '$tipoO' 
          AND cp.fecha <='$fechaF' 
          AND (tcp.clasepptal = '13')";
    $ap = $GLOBALS['mysqli']->query($query);
    if(mysqli_num_rows($ap)>0){
        $sum=0;
        while ($sum1= mysqli_fetch_array($ap)) {
            $sum = $sum1['value']+$sum;
        }
    } else {
       $sum=0; 
    }
    $presu = $sum;
    return $presu;
}
function disponibilidades_r($id_rubFue, $clase, $fechaF){
    require'../../Conexion/conexion.php';	
    $apropiacion_def = 0;
    $queryApro = "SELECT   detComP.valor, 
    tipComP.tipooperacion, 
    tipComP.nombre, rubFue.id_unico, 
    rubFue.rubro, rubP.id_unico,  
    rubP.nombre  
    from gf_detalle_comprobante_pptal detComP 
    left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal 
    left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante 
    left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente 
    left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro 
    where tipComP.clasepptal = '$clase' 
    and rubFue.id_unico =  $id_rubFue AND comP.fecha <= '$fechaF'";
    $apropia = $GLOBALS['mysqli']->query($queryApro);
    while($row = mysqli_fetch_row($apropia)){
        if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
        { $apropiacion_def += $row[0]; }
        elseif($row[1] == 3)
        { $apropiacion_def -= $row[0];}
    }
    return $apropiacion_def;
}
?>