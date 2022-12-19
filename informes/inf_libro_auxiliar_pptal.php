<?php
################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#27/08/2018 |Erica G. |Fuente
#20/10/2017 |Erica G. |Archivo Creado
################################################################################################################
require'../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $para";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno = $an[0];

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
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
$rutalogoTer = $rowC[6];

#*************Datos Formulario**************#
$rubroI = $_POST['sltctai'];
$rubroF = $_POST['sltctaf'];
$fecha1 ='01/01/'.$anno;;
$fecha_div = explode("/", $fecha1);
$dia1 = $fecha_div[0];
$mes1 = $fecha_div[1];
$anio1 = $fecha_div[2];
$fechaI = $anio1 . '-' . $mes1 . '-' . $dia1;
$fecha2 = $_POST['fechafin'];
$fecha_div2 = explode("/", $fecha2);
$dia2 = $fecha_div2[0];
$mes2 = $fecha_div2[1];
$anio2 = $fecha_div2[2];
$fechaF = $anio2 . '-' . $mes2 . '-' . $dia2;

#***********Consulta datos recibidos************#
if(empty($_REQUEST['fuente'])){
    $cng = "SELECT DISTINCT r.id_unico, r.codi_presupuesto , LOWER(r.nombre)   
    FROM gf_detalle_comprobante_pptal dc 
    LEFT JOIN gf_comprobante_pptal c ON dc.comprobantepptal = c.id_unico 
    LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico
    LEFT JOIN gf_rubro_pptal r ON rf.rubro = r.id_unico 
    WHERE r.codi_presupuesto BETWEEN '".$rubroI."' AND '".$rubroF."' 
    AND c.fecha BETWEEN '".$fechaI."' AND '".$fechaF."' 
    AND r.tipoclase = 7 AND r.parametrizacionanno = $para";
} else {
    $cng = "SELECT DISTINCT r.id_unico, r.codi_presupuesto,  CONCAT_WS(' - ',LOWER(r.nombre), LOWER(f.nombre)) 
    FROM gf_detalle_comprobante_pptal dc 
    LEFT JOIN gf_comprobante_pptal c ON dc.comprobantepptal = c.id_unico 
    LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico
    LEFT JOIN gf_rubro_pptal r ON rf.rubro = r.id_unico 
    LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
    WHERE r.codi_presupuesto BETWEEN '".$rubroI."' AND '".$rubroF."' 
    AND c.fecha BETWEEN '".$fechaI."' AND '".$fechaF."' 
    AND f.id_unico = ".$_REQUEST['fuente']." 
    AND r.tipoclase = 7 AND r.parametrizacionanno = $para";
}
$cng = $mysqli->query($cng);

$c = $_GET['c'];
#**********PDF*********#
if($c==1){
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    ob_start();
    
    class PDF extends FPDF {
        function Header() {

            global $rutalogoTer;
            global $razonsocial;
            global $numeroIdent;
            global $rubroI;
            global $rubroF;
            global $fecha1;
            global $fecha2;
            global $numpaginas;
            $numpaginas = $this->PageNo();
            if ($rutalogoTer != '') {
                $this->Image('../' . $rutalogoTer, 12, 6, 25);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', '', 8);
            $this->SetX(25);
            $this->Cell(320, 5, $numeroIdent, 0, 0, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->Ln(4);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('LIBRO AUXILIAR PRESUPUESTAL'), 0, 0, 'C');
            $this->Ln(4);

            $this->SetFont('Arial', '', 7);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('Rubros ' . $rubroI . ' y ' . $rubroF), 0, 0, 'C');

            $this->Ln(3);

            $this->SetFont('Arial', '', 7);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('entre Fechas ' . $fecha1 . ' y ' . $fecha2), 0, 0, 'C');

            $this->Ln(10);

            $this->SetX(10);
            $this->SetFont('Arial', 'B', 8);

            $this->Cell(20, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(58, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(58, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(25, 9, utf8_decode(''), 1, 0, 'C');

            $this->SetX(10);

            $this->Cell(20, 9, utf8_decode('Tipo'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Número'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Fecha'), 0, 0, 'C');
            $this->Cell(58, 9, utf8_decode('Nombre Tercero'), 0, 0, 'C');
            $this->Cell(58, 9, utf8_decode('Descipción'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Afecta'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Apropiación'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('C.D.P'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Registro'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Obligación'), 0, 0, 'C');
            $this->Cell(25, 9, utf8_decode('Giro'), 0, 0, 'C');

            $this->Ln(9);
        }

        function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(10);
        $this->Cell(160, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
        $this->Cell(170, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

    }

    $pdf = new PDF('L', 'mm', 'Legal');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $yp = $pdf->GetY();
    
    while ($row = mysqli_fetch_row($cng)) {
        $alt = $pdf->GetY();
        if($alt>180){
            $pdf->AddPage();
        }
        if(empty($_REQUEST['fuente'])) { 
        $dr = "SELECT cp.id_unico, 
                tc.codigo,
                cp.numero, 
                DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                IF(CONCAT_WS(' ',
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
                t.apellidodos)) AS NOMBRE,
                LOWER(cp.descripcion), 
                UPPER(tca.codigo), cpa.numero , tc.clasepptal, tc.tipooperacion , 
                dc.valor 
            FROM gf_detalle_comprobante_pptal dc 
            LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.comprobanteafectado = dca.id_unico 
            LEFT JOIN gf_comprobante_pptal cpa ON dca.comprobantepptal = cpa.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tca ON cpa.tipocomprobante = tca.id_unico 
            WHERE rf.rubro = '".$row[0]."' AND cp.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'  
            AND tc.clasepptal IN(13,14,15,16,17)
            ORDER BY cp.fecha , tc.clasepptal  ";
        } else {
            $dr = "SELECT cp.id_unico, 
                tc.codigo,
                cp.numero, 
                DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                IF(CONCAT_WS(' ',
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
                t.apellidodos)) AS NOMBRE,
                LOWER(cp.descripcion), 
                UPPER(tca.codigo), cpa.numero , tc.clasepptal, tc.tipooperacion , 
                dc.valor 
            FROM gf_detalle_comprobante_pptal dc 
            LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.comprobanteafectado = dca.id_unico 
            LEFT JOIN gf_comprobante_pptal cpa ON dca.comprobantepptal = cpa.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tca ON cpa.tipocomprobante = tca.id_unico 
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            WHERE rf.rubro = '".$row[0]."' AND cp.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'  
            AND f.id_unico = ".$_REQUEST['fuente']." 
            AND tc.clasepptal IN(13,14,15,16,17)
            ORDER BY cp.fecha , tc.clasepptal  ";
        }
        $dr = $mysqli->query($dr);
        if(mysqli_num_rows($dr)>0) { 
            $vaprop =0;
            $vdis =0;
            $vreg =0;
            $vobl =0;
            $vpag =0;
            $pdf->SetFont('Arial', 'B', 10);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Cell(50, 5, utf8_decode($row[1]), 0, 0, 'L');
            $pdf->Cell(20, 5, utf8_decode(''), 0,0, 'L');
            $pdf->Cell(266, 5, utf8_decode(ucwords($row[2])), 0,0, 'L');
            $pdf->SetXY($x, $y);
            $pdf->Cell(336, 5, utf8_decode(''), 1, 0, 'L');
            $pdf->Ln(5);
            #***********Detalles del rubro*************#
            while ($row1 = mysqli_fetch_row($dr)) {
                $alt =$pdf->GetY();
                if($alt>180){
                    $pdf->AddPage();
                }
                $paginactual = $numpaginas;
                $pdf->SetFont('Arial', '', 8);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Cell(20, 5, utf8_decode(''), 0, 0, 'C');
                $pdf->Cell(25, 5, utf8_decode(''), 0, 0, 'C');
                $pdf->Cell(25, 5, utf8_decode(''), 0, 0, 'C');
                
                
                $pdf->MultiCell(58, 5, utf8_decode(ucwords(mb_strtolower($row1[4]))), 0, 'J');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                $px = $x + 128;
                if ($numpaginas > $paginactual) {
                    $pdf->SetXY($px, $yp);
                    $h = $y2 - $yp;
                } else {
                    $pdf->SetXY($px, $y);
                }

                $x2 = $pdf->GetX();
                $y2 = $pdf->GetY();
                $pdf->MultiCell(58, 5, utf8_decode(ucwords($row1[5])), 0, 'J');
                $y22 = $pdf->GetY();
                $h2 = $y22 - $y2;
                $px2 = $x2 + 58;
                if ($numpaginas > $paginactual) {
                    $pdf->SetXY($x, $yp);
                    $h2 = $y22 - $yp;
                } else {
                    $pdf->SetXY($x, $y);
                }
                $alto = max($h, $h2);
                
                $pdf->Cell(20, $alto, utf8_decode(mb_strtoupper($row1[1])), 1, 0, 'C');
                $pdf->Cell(25, $alto, utf8_decode($row1[2]), 1, 0, 'C');
                $pdf->Cell(25, $alto, utf8_decode($row1[3]), 1, 0, 'C');
                $pdf->Cell(58, $alto, utf8_decode(''), 1, 0, 'R');
                $pdf->Cell(58, $alto, utf8_decode(''), 1, 0, 'R');
                $pdf->Cell(25, $alto, utf8_decode($row1[6].' - '.$row1[7]), 1, 0, 'C');
                if($row1[8]==13 ){
                    $aprop =number_format($row1[10], 2, '.', ',');
                    if($row1[9]==3){
                        $vaprop -=$row1[10];
                    } else {
                        $vaprop +=$row1[10];
                    }
                } else {
                    $aprop ="";
                }
                $pdf->Cell(25, $alto, $aprop, 1, 0, 'R');
                if($row1[8]==14){
                    $dis =number_format($row1[10], 2, '.', ',');
                    if($row1[9]==3){
                        $vdis -=$row1[10];
                    } else {
                        $vdis +=$row1[10];
                    }
                    
                } else {
                    $dis ="";
                }
                $pdf->Cell(25, $alto, $dis, 1, 0, 'R');
                if($row1[8]==15){
                    $rep=number_format($row1[10], 2, '.', ',');
                    if($row1[9]==3){
                        $vreg -=$row1[10];
                    } else {
                        $vreg +=$row1[10];
                    }
                    
                } else {
                    $rep ="";
                }
                $pdf->Cell(25, $alto, $rep, 1, 0, 'R');
                if($row1[8]==16){
                    $obl=number_format($row1[10], 2, '.', ',');
                    if($row1[9]==3){
                        $vobl -=$row1[10];
                    } else {
                        $vobl +=$row1[10];
                    }
                    
                } else {
                    $obl ="";
                }
                $pdf->Cell(25, $alto,$obl, 1, 0, 'R');
                if($row1[8]==17){
                    $pag = number_format($row1[10], 2, '.', ',');
                    if($row1[9]==3){
                        $vpag -=$row1[10];
                    } else {
                        $vpag +=$row1[10];
                    }
                    
                } else {
                    $pag ="";
                }
                $pdf->Cell(25, $alto,$pag, 1, 0, 'R'); 
                $pdf->Ln($alto);
            }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(211, 5, utf8_decode('Totales'), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vaprop, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vdis, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vreg, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vobl, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vpag, 2, '.', ','), 1, 0, 'R');
            $pdf->Ln(5);
            $pdf->Cell(211, 5, utf8_decode('Saldo'), 1, 0, 'R');
            $pdf->Cell(25, 5, '', 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vaprop-$vdis, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vdis-$vreg, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vreg-$vobl, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 5, number_format($vobl-$vpag, 2, '.', ','), 1, 0, 'R');
            $pdf->Ln(5);
            
        }
        
        
        
        
        
    }
    
    
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Libro_Auxiliar_Pptal(' . date('d/m/Y') . ').pdf', 0);


}
#**********XLS*********#
elseif($c==2){
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Libro_Auxiliar_Pptal.xls");
    require_once("../Conexion/conexion.php");
    ini_set('max_execution_time', 0);
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Libro Auxiliar Presupuestal</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="11" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
               <br/>&nbsp;
               <br/>LIBRO AUXILIAR PRESUPUESTAL
               <br/><?php echo 'Rubros ' . $rubroI . ' y ' . $rubroF?>
               <br/>Fecha <?php echo $fecha1.' a '.$fecha2?></strong>
                <br/>&nbsp;
            </th>
      </tr>
      <tr>
        <td><strong>TIPO</strong></td>
        <td><strong>NÚMERO</strong></td>
        <td><strong>FECHA</strong></td>
        <td><strong>NOMBRE TERCERO</strong></td>
        <td><strong>DESCRIPCION</strong></td>
        <td><strong>AFECTA</strong></td>
        <td><strong>APROPIACIÓN</strong></td>
        <td><strong>C.D.P</strong></td>
        <td><strong>REGISTRO</strong></td>
        <td><strong>OBLIGACIÓN</strong></td>
        <td><strong>GIRO</strong></td>        
      </tr>
    
    <?php
    while ($row = mysqli_fetch_row($cng)) {
    
         if(empty($_REQUEST['fuente'])) { 
        $dr = "SELECT cp.id_unico, 
                tc.codigo,
                cp.numero, 
                DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                IF(CONCAT_WS(' ',
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
                t.apellidodos)) AS NOMBRE,
                LOWER(cp.descripcion), 
                UPPER(tca.codigo), cpa.numero , tc.clasepptal, tc.tipooperacion , 
                dc.valor 
            FROM gf_detalle_comprobante_pptal dc 
            LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.comprobanteafectado = dca.id_unico 
            LEFT JOIN gf_comprobante_pptal cpa ON dca.comprobantepptal = cpa.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tca ON cpa.tipocomprobante = tca.id_unico 
            WHERE rf.rubro = '".$row[0]."' AND cp.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'  
            AND tc.clasepptal IN(13,14,15,16,17)
            ORDER BY cp.fecha , tc.clasepptal  ";
        } else {
            $dr = "SELECT cp.id_unico, 
                tc.codigo,
                cp.numero, 
                DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                IF(CONCAT_WS(' ',
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
                t.apellidodos)) AS NOMBRE,
                LOWER(cp.descripcion), 
                UPPER(tca.codigo), cpa.numero , tc.clasepptal, tc.tipooperacion , 
                dc.valor 
            FROM gf_detalle_comprobante_pptal dc 
            LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
            LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
            LEFT JOIN gf_detalle_comprobante_pptal dca ON dc.comprobanteafectado = dca.id_unico 
            LEFT JOIN gf_comprobante_pptal cpa ON dca.comprobantepptal = cpa.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tca ON cpa.tipocomprobante = tca.id_unico 
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
            WHERE rf.rubro = '".$row[0]."' AND cp.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'  
            AND f.id_unico = ".$_REQUEST['fuente']." 
            AND tc.clasepptal IN(13,14,15,16,17)
            ORDER BY cp.fecha , tc.clasepptal  ";
        }
        $dr = $mysqli->query($dr);
        if(mysqli_num_rows($dr)>0) { 
            $vaprop =0;
            $vdis =0;
            $vreg =0;
            $vobl =0;
            $vpag =0;
            ?>
            <tr>
                <td colspan="11"><strong><?php echo  $row[1].'   '.ucwords($row[2]);?></strong></td>
            </tr>
        
            <?php 
            #***********Detalles del rubro*************#
            while ($row1 = mysqli_fetch_row($dr)) {
               ?>
                <tr>
                    <td><?php echo  mb_strtoupper($row1[1])?></td>
                    <td><?php echo  $row1[2]?></td>
                    <td><?php echo  $row1[3]?></td>
                    <td><?php echo  ucwords(mb_strtolower($row1[4]))?></td>
                    <td><?php echo  ucwords($row1[5])?></td>
                    <td><?php echo  $row1[6].' - '.$row1[7]?></td>
                    <?php 
                    if($row1[8]==13 ){
                        $aprop =number_format($row1[10], 2, '.', ',');
                        if($row1[9]==3){
                            $vaprop -=$row1[10];
                        } else {
                            $vaprop +=$row1[10];
                        }
                        
                    } else {
                        $aprop ="";
                    }
                    if($row1[8]==14){
                        $dis =number_format($row1[10], 2, '.', ',');
                        if($row1[9]==3){
                            $vdis -=$row1[10];
                        } else {
                            $vdis +=$row1[10];
                        }

                    } else {
                        $dis ="";
                    }
                    if($row1[8]==15){
                        $rep=number_format($row1[10], 2,  '.', ',');
                        if($row1[9]==3){
                            $vreg -=$row1[10];
                        } else {
                            $vreg +=$row1[10];
                        }
                        
                    } else {
                        $rep ="";
                    }
                    if($row1[8]==16){
                        $obl=number_format($row1[10], 2,  '.', ',');
                        if($row1[9]==3){
                            $vobl -=$row1[10];
                        } else {
                            $vobl +=$row1[10];
                        }
                        
                    } else {
                        $obl ="";
                    }
                    if($row1[8]==17){
                        $pag = number_format($row1[10], 2,  '.', ',');
                        if($row1[9]==3){
                            $vpag -=$row1[10];
                        } else {
                            $vpag +=$row1[10];
                        }
                        
                    } else {
                        $pag ="";
                    }
                    ?>
                    <td><?php echo  $aprop?></td>
                    <td><?php echo  $dis?></td>
                    <td><?php echo  $rep?></td>
                    <td><?php echo  $obl?></td>
                    <td><?php echo  $pag?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="6"><strong>TOTALES</strong></td>
                <td><strong><?php echo number_format($vaprop, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vdis, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vreg, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vobl, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vpag, 2,  '.', ',');?></strong></td>
            </tr>
            <tr>
                <td colspan="6"><strong>SALDO</strong></td>
                <td><strong></strong></td>
                <td><strong><?php echo number_format($vaprop-$vdis, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vdis-$vreg, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vreg-$vobl, 2,  '.', ',');?></strong></td>
                <td><strong><?php echo number_format($vobl-$vpag, 2,  '.', ',');?></strong></td>
            </tr>
            <?php 
            
        }
        
        
        
        
        
    }
    
}
?>