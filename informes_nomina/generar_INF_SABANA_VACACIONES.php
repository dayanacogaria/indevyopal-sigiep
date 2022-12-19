<?php
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time',0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];

#************** Datos Compañia *********************#
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN       gf_ciudad c ON ter.ciudadresidencia = c.id_unico 
LEFT JOIN       gf_ciudad c2 ON ter.ciudadidentificacion = c2.id_unico 
LEFT JOIN       gf_departamento d1 ON c.departamento = d1.id_unico 
LEFT JOIN       gf_departamento d2 ON c2.departamento = d2.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 


#************** Datos Recibe *********************#
$periodo  = $_REQUEST['sltPeriodo'];

$np = $con->Listar("SELECT p.id_unico,p.codigointerno, tpn.nombre , fechafin , fechainicio 
    FROM gn_periodo p 
    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
    WHERE p.id_unico = $periodo");

$nperiodo = ucwords(mb_strtolower($np[0][1].' - '.$np[0][2]));
$fechafin = $np[0][3];
$fechaInicio = $np[0][4];

#********** Tipo PDF ***********#
if($_GET['t']==1){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    { 
        function Header()
        { 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $ruta_logo;
            global $nperiodo;
            if ($ruta_logo != '') {
                $this->Image('../' . $ruta_logo, 40,5,30);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', '', 8);
            $this->SetX(25);
            $this->Cell(320, 5, $nombreIdent.': '.$numeroIdent, 0, 0, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->Ln(4);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('SÁBANA DE VACACIONES'), 0, 0, 'C');
            $this->Ln(4); 
            $this->SetFont('Arial', 'B', 8);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('PERIODO:'.$nperiodo), 0, 0, 'C');
            $this->Ln(8);

        }
        function Footer()
        {
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(90,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
            $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
            $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
            $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }

    $pdf = new PDF('L','mm','Legal');        
    $pdf->AddPage();
    $pdf->AliasNbPages();
   

    if(empty($_REQUEST['sltUnidadE'])){        
        #**** Numero de conceptos ****#
        $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania  
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND e.codigointerno != '0000' 
            AND (c.clase IN(1,2,3,4,5,8))");

        $filas = 220 / $ncon[0][0] ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetX(35);
        $cx = $pdf->GetX();
        $cy = $pdf->GetY();
        $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
        $pdf->Cell(45,5, utf8_decode('Nombre'),0,0,'C');
        $pdf->Cell(15,5, utf8_decode('Básico'),0,0,'C');
        $h2 = 0;
        $h  = 0;
        $alto = 0;
        #**** Nombre de conceptos ****#
        $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                LOWER(c.descripcion),
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase IN(1,2,3,4,5,8)) 
            ORDER BY cast(c.orden as unsigned) ");
        #*** Titulos ***#
        $pdf->SetFont('Arial','B',7);
        for ($c = 0; $c < count($rowcn); $c++) {
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->MultiCell($filas,5, utf8_decode(ucwords($rowcn[$c][1])),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
            if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
            $pdf->SetXY($x+$filas,$y);
        }
        $pdf->SetXY($cx,$cy);
        $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(45,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
        for ($c = 0; $c < count($rowcn); $c++) {
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
        }
        $pdf->Ln($alto);
        #***************************************************************#
        #**** Buscar Terceros***#
        $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
            ca.salarioactual 
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
        AND  n.periodo = $periodo 
        AND (c.clase IN(1,2,3,4,5,8) AND n.valor>0) 
        ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");

        $pdf->SetFont('Arial','',7);
        
        $salarioa = 0;
        for ($e = 0; $e < count($rowe); $e++) { 
        	$pdf->SetX(35);
            $pdf->Cellfitscale(15,8, utf8_decode($rowe[$e][4]),1,0,'L');
            $pdf->Cellfitscale(45,8, ($rowe[$e][5]),1,0,'L');

            #*** Salario ****#
            $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = ".$rowe[$e][0]." 
                AND concepto = '78' AND periodo = '$periodo'");

            $pdf->Cellfitscale(15,8, utf8_decode(number_format($rowe[$e][6],0,'.',',')),1,0,'R');
            $salarioa += $rowe[$e][6];
            $x =$pdf->GetX();  
            $y =$pdf->GetY();
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
            }        
            $pdf->Ln(8);
        }
        $pdf->SetX(35);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,8, utf8_decode('Total:'),1,0,'C');
        $pdf->Cellfitscale(15,8, utf8_decode(number_format($salarioa,0,'.',',')),1,0,'R');

       for ($c = 0; $c < count($rowcn); $c++) {
            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = ".$rowcn[$c][2]." 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania    
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) AND n.valor>0)");
            if($num_con[0][1] > 0){
                $valor = $num_con[0][1];
            }else{
                $valor =0;
            }
            $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
        }
    } else {
        $unidad = $_REQUEST['sltUnidadE'];
        #**** Numero de conceptos ****#
        $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania  
            AND n.periodo = $periodo     
            AND e.unidadejecutora = $unidad  
            AND n.valor > 0 
            AND e.codigointerno != '0000' 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8)");

        $filas = 220 / $ncon[0][0] ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetX(35);
        $cx = $pdf->GetX();
        $cy = $pdf->GetY();
        $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
        $pdf->Cell(45,5, utf8_decode('Nombre'),0,0,'C');
        $pdf->Cell(15,5, utf8_decode('Básico'),0,0,'C');
        $h2 = 0;
        $h  = 0;
        $alto = 0;
        #**** Nombre de conceptos ****#
        $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo     
            AND e.unidadejecutora = $unidad 
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) 
            ORDER BY cast(c.orden as unsigned)");
        #*** Titulos ***#
        $pdf->SetFont('Arial','B',7);
        for ($c = 0; $c < count($rowcn); $c++) {
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
            if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
            $pdf->SetXY($x+$filas,$y);
        }
        $pdf->SetXY($cx,$cy);
        $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(45,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
        for ($c = 0; $c < count($rowcn); $c++) {
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
        }
        $pdf->Ln($alto);
        #***************************************************************#
        #**** Buscar Terceros***#
        $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
            ca.salarioactual 
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
        AND  n.periodo = $periodo 
        AND e.unidadejecutora = $unidad 
        AND (c.clase IN(1,2,3,4,5,8) AND n.valor>0) 
        ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");

        $pdf->SetFont('Arial','',7);
        $salarioa = 0;
        
        for ($e = 0; $e < count($rowe); $e++) { 
        	$pdf->SetX(35);
            $pdf->Cellfitscale(15,8, utf8_decode($rowe[$e][4]),1,0,'L');
            $pdf->Cellfitscale(45,8, ($rowe[$e][5]),1,0,'L');

            #*** Salario ****#
            $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = ".$rowe[$e][0]." 
                AND concepto = '78' AND periodo = '$periodo'");

            $pdf->Cellfitscale(15,8, utf8_decode(number_format($rowe[$e][6],0,'.',',')),1,0,'R');
            $salarioa += $rowe[$e][6];
            $x =$pdf->GetX();  
            $y =$pdf->GetY();
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
            }        
            $pdf->Ln(8);
        }
        $pdf->SetX(35);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,8, utf8_decode('Total:'),1,0,'C');
        $pdf->Cellfitscale(15,8, utf8_decode(number_format($salarioa,0,'.',',')),1,0,'R');

       for ($c = 0; $c < count($rowcn); $c++) {
            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = ".$rowcn[$c][2]." 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania  
                AND e.unidadejecutora = $unidad   
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) AND n.valor>0)");
            if($num_con[0][1] > 0){
                $valor = $num_con[0][1];
            }else{
                $valor =0;
            }
            $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
        }
    }
    $pdf->Ln(15);
      
    

    #**************** FIRMAS *****************#

    $pdf->Ln(25);
    $firmas = "SELECT   
            IF(CONCAT_WS(' ',
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
            tr.apellidodos)) AS NOMBRE , c.nombre, 
            rd.orden,rd.fecha_inicio, rd.fecha_fin

    FROM gf_responsable_documento rd 
    LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
    LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
    LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
    LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
    WHERE td.nombre = 'Sabana Vacaciones'
    ORDER BY rd.orden ASC";

    $fi = $mysqli->query($firmas);
    $altura = $pdf->GetY();
    if($altura > 180){
        $pdf->AddPage();
        $pdf->Ln(15);
    } 
    $pdf->SetFont('Arial','B',8);
    $xxx = 0;

    while($row_firma = mysqli_fetch_row($fi)){

        $imprimir = 0; 
        if (!empty($row_firma[4])) {
            if ($fechafin <= $row_firma[4]) {
                $imprimir = 1; 
            }
        } elseif (!empty($row_firma[3])) {
            if ($fechafin >= $row_firma[3]) {
                $imprimir = 1; 
            }
        }

        if( $imprimir==1){ 
            if($xxx == 0){
                $yyy = $yy1;
            }
            $xxx++;
            if($xxx % 2 == 0){
                $pdf->SetXY(150, $yyy);
                $pdf->Cell(60, 0, '', 'B');
                $pdf->Ln(3);
                $pdf->SetX(150);
                $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->SetX(150);
                $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
                $pdf->Ln(40);
            }else{
            	$pdf->SetX(35);
                $yyy = $pdf->GetY();
                $pdf->Cell(60, 0, '', 'B');
                $pdf->Ln(3);
                $pdf->SetX(35);
                $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->SetX(35);
                $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
            }
        }
    }    

    ob_end_clean();
    $pdf->Output(0,'Sabana_Vacaciones('.date('d/m/Y').').pdf',0);     
}
#******** Tipo Excel *************#
 else{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Sabana_Vacaciones.xls");  
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>Sabana Nomina</title>';
    echo '</head>';
    echo '<body>';
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
    
   if(empty($_REQUEST['sltUnidadE'])){
        #**** Numero de conceptos ****#
        $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8)");
        $nc =$ncon[0][0]+4;
        echo '<th colspan="'.$nc.'" align="center"><strong>';
        echo '<br/>&nbsp;';
        echo '<br/>'.$razonsocial;
        echo '<br/>'.$nombreIdent.': '.$numeroIdent;
        echo '<br/>&nbsp;';
        echo '<br/>SÁBANA DE VACACIONES';
        echo '<br/>&nbsp;';
        echo 'PERIODO:'.$nperiodo;
        echo '<br/>&nbsp;';
        echo '</strong>';
        echo '</th>';
        echo '<tr></tr>  ';
        
        #**** Numero de conceptos ****#
        $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8)");
        echo '<tr>'; 
        echo '<td><strong>Cod Int</strong></td>';
        echo '<td><strong>Nombre</strong></td>';
        echo '<td><strong>Básico</strong></td>';
        #**** Nombre de conceptos ****#
        $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                LOWER(c.descripcion),
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) 
            ORDER BY cast(c.orden as unsigned)");
        #*** Titulos ***#
        for ($c = 0; $c < count($rowcn); $c++) {
            echo '<td><strong>'.utf8_encode(ucwords($rowcn[$c][1])).'</strong></td>';
        }
        echo '<td><strong>Firma</strong></td>';
        echo '</tr>';
        #***************************************************************#
        #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
        $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
            ca.salarioactual 
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
        AND  n.periodo = $periodo 
        AND (c.clase IN(1,2,3,4,5,8) AND n.valor>0) 
        ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
        $salarioa = 0;
        for ($e = 0; $e < count($rowe); $e++) { 
            echo '<tr>';
            echo '<td align= "left">'.($rowe[$e][4]).'</td>';
            echo '<td>'.utf8_encode($rowe[$e][5]).'</td>';
            #*** Salario ****#
            $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = ".$rowe[$e][0]." 
                AND concepto = '78' AND periodo = '$periodo'");
            echo '<td>'.number_format($rowe[$e][6],0,'.',',').'</td>';
            $salarioa += $rowe[$e][6];
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td>'.number_format($valor,0,'.',',').'</td>';
            }        
            echo '<td></td>';
            echo '</tr>';
        }                
        echo '<tr>';
        echo '<td colspan="2"><strong>Total</strong></td>';
        echo '<td><strong>'.number_format($salarioa,0,'.',',').'</strong></td>';
        
        for ($c = 0; $c < count($rowcn); $c++) {
            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = ".$rowcn[$c][2]." 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) AND n.valor >0)");
            if($num_con[0][1] > 0){
                $valor = $num_con[0][1];
            }else{
                $valor =0;
            }
            echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
        }
        echo '</tr>';
    } else {
        #Unidad Ejecutora
        $unidad = $_REQUEST['sltUnidadE'];
        $nu = $con->Listar("SELECT id_unico, nombre FROM gn_unidad_ejecutora WHERE id_unico =".$unidad);
         #**** Numero de conceptos ****#
        $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo    
            AND e.unidadejecutora = $unidad   
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8)");
        $nc =$ncon[0][0]+4;
        echo '<th colspan="'.$nc.'" align="center"><strong>';
        echo '<br/>&nbsp;';
        echo '<br/>'.$razonsocial;
        echo '<br/>'.$nombreIdent.': '.$numeroIdent;
        echo '<br/>&nbsp;';
        echo '<br/>SÁBANA DE VACACIONES';
        echo '<br/>&nbsp;';
        echo 'NÓMINA:'.$nperiodo;
        echo '<br/>&nbsp;';
        echo '</strong>';
        echo '</th>';
        echo '<tr><td colspan="'.$nc.'"><strong>UNIDAD EJECUTORA: '.$nu[0][1].'</strong></td></tr>  ';
        
        #**** Numero de conceptos ****#
        $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo  
            AND e.unidadejecutora = $unidad       
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8)");
        echo '<tr>';
        echo '<td><strong>Cod Int</strong></td>';
        echo '<td><strong>Nombre</strong></td>';
        echo '<td><strong>Básico</strong></td>';
        #**** Nombre de conceptos ****#
        $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                LOWER(c.descripcion),
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND e.unidadejecutora = $unidad   
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) 
            ORDER BY cast(c.orden as unsigned)");
        #*** Titulos ***#
        for ($c = 0; $c < count($rowcn); $c++) {
            echo '<td><strong>'.utf8_encode(ucwords($rowcn[$c][1])).'</strong></td>';
        }
        echo '<td><strong>Firma</strong></td>';
        echo '</tr>';
        #***************************************************************#
        #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
        $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
            ca.salarioactual 
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
        AND  n.periodo = $periodo 
        AND e.unidadejecutora = $unidad   
        AND (c.clase IN(1,2,3,4,5,8) AND n.valor>0) 
        ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
        $salarioa = 0;
        for ($e = 0; $e < count($rowe); $e++) { 
            echo '<tr>';
            echo '<td align= "left">'.($rowe[$e][4]).'</td>';
            echo '<td>'.utf8_encode($rowe[$e][5]).'</td>';
            #*** Salario ****#
            $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = ".$rowe[$e][0]." 
                AND concepto = '78' AND periodo = '$periodo'");
            echo '<td>'.number_format($rowe[$e][6],0,'.',',').'</td>';
            $salarioa += $rowe[$e][6];
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td>'.number_format($valor,0,'.',',').'</td>';
            }        
            echo '<td></td>';
            echo '</tr>';
        }                
        echo '<tr>';
        echo '<td colspan="2"><strong>Total</strong></td>';
        echo '<td><strong>'.number_format($salarioa,0,'.',',').'</strong></td>';
        
        for ($c = 0; $c < count($rowcn); $c++) {
            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = ".$rowcn[$c][2]." 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad   
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 8) AND n.valor >0)");
            if($num_con[0][1] > 0){
                $valor = $num_con[0][1];
            }else{
                $valor =0;
            }
            echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
        }
        echo '</tr>';
    }

    echo '</table>';
    echo '</body>';
}
?>