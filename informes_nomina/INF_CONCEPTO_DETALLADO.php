<?php
#MODIFICACIONES
#04/10/2021-Elkin O-Se corrigio el reemplazo de codigo de concepto a id_unico concepto, ya que el valor que toma del select es id_unico
#05/10/2021-Elkin O- Se cambio el codigo, por el id_unico para el remplazo de concepto.
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time',0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$anno       = $_SESSION['anno'];

#************** Datos Compañia *********************#
$rowC = $con->Listar("SELECT  ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
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
$acumulado = $_REQUEST['acumulado'];
$periodo   = $_REQUEST['periodoI'];

$np = $con->Listar("SELECT p.id_unico,p.codigointerno, tpn.nombre , fechafin , fechainicio 
    FROM gn_periodo p 
    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
    WHERE p.id_unico = $periodo");

$nperiodo = ucwords(mb_strtolower($np[0][1].' - '.$np[0][2]));
$fechafin = $np[0][3];
$fechaInicio = $np[0][4];


$nconcepto = '';
if($_REQUEST['retencion']==1){
    $ncon = $con->Listar("SELECT codigo, descripcion, id_unico FROM gn_concepto WHERE codigo in ('110','125')");
    for ($c=0; $c <count($ncon) ; $c++) { 
        $nconcepto .= $ncon[$c][0].' - '.$ncon[$c][1].'  ';
    }
    //Se cambio el codigo, por el id_unico para el remplazo de concepto.
    $concepto = "'90'";
    $acumulado = 2;
} else {
    $concepto = $_REQUEST['conceptoI'];
    $ncon = $con->Listar("SELECT codigo, descripcion, id_unico FROM gn_concepto WHERE id_unico = $concepto");
    $nconcepto = $ncon[0][0].' - '.$ncon[0][1];
}
    
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
            global $nconcepto;
            if ($ruta_logo != '') {
                $this->Image('../' . $ruta_logo, 20,8,20);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(15);
            $this->Cell(200, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', '', 8);
            $this->SetX(15);
            $this->Cell(200, 5, $nombreIdent.': '.$numeroIdent, 0, 0, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->Ln(4);
            $this->SetX(15);
            $this->Cell(200, 5, utf8_decode('INFORME POR CONCEPTO DETALLADO'), 0, 0, 'C');
            $this->Ln(4); 
            $this->SetFont('Arial', 'B', 8);
            $this->SetX(15);
            $this->Cell(200, 5, utf8_decode('PERIODO:'.$nperiodo), 0, 0, 'C');
            $this->Ln(4); 
            $this->SetFont('Arial', 'B', 8);
            $this->SetX(15);
            $this->Cell(200, 5, ('CONCEPTO:'.$nconcepto), 0, 0, 'C');
            $this->Ln(8);

        }
        function Footer()
        {
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(90,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
            $this->Cell(90,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }

    $pdf = new PDF('P','mm','letter');        
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $rowba = "SELECT DISTINCT tb.id_unico, tb.razonsocial, tb.numeroidentificacion  
    FROM gn_novedad n 
    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
    LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
    LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
    LEFT JOIN gf_tercero tb ON cb.banco = tb.id_unico 
    WHERE e.id_unico !=2 
    AND t.compania = $compania 
    AND cb.parametrizacionanno = $anno 
    AND  n.periodo = $periodo 
    AND c.codigo = $concepto 
    AND tb.id_unico IS NOT NULL 
    ORDER BY tb.razonsocial ASC"; 


$pdf->Ln(2);
$pdf->SetFont('Arial','BI',9);
$pdf->MultiCell(200,5,utf8_decode($rowes) ,0,'L');
    if($acumulado==1){        

        #* Buscar bancos 
        $rowb = $con->Listar("SELECT DISTINCT tb.id_unico, tb.razonsocial, tb.numeroidentificacion  
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico  
        LEFT JOIN gf_tercero tb ON cb.banco = tb.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
        AND  n.periodo = $periodo 
        AND c.id_unico in($concepto)
        AND tb.id_unico IS NOT NULL 
        ORDER BY tb.razonsocial ASC"); 

        for ($b=0; $b <count($rowb); $b++) { 
            $id_banco = $rowb[$b][0];
            
            $pdf->Ln(2);
            $pdf->SetFont('Arial','BI',9);
            $pdf->Cell(190,8, utf8_decode($rowb[$b][1].' - '.$rowb[$b][2]),1,0,'L');
            $pdf->Ln(8);


            $filas = 40;
            $pdf->SetFont('Arial','B',8);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
           
            $pdf->Cell(20,5, utf8_decode('CÉDULA'),0,0,'C');
            $pdf->Cell(90,5, utf8_decode('NOMBRE'),0,0,'C');
            $h2 = 0;
            $h  = 0;
            $alto = 0;
            #*** Titulos ***#
            $pdf->SetFont('Arial','B',7);
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->MultiCell($filas,5, utf8_decode($nconcepto),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
            if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
            $pdf->SetXY($x+$filas,$y);
            $pdf->Cell(40,5, utf8_decode('CUENTAs'),0,0,'C');

            $pdf->SetXY($cx,$cy);
            $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(90,$alto, utf8_decode(''),1,0,'C');
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
            $pdf->Cell(40,$alto, utf8_decode(''),1,0,'C');
            $pdf->Ln($alto);

            #***************************************************************#
            $rowe = $con->Listar("SELECT DISTINCT  e.id_unico, 
                e.codigointerno, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
                ca.salarioactual , cb.numerocuenta 
            FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
            LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
            WHERE e.id_unico !=2 
            AND t.compania = $compania 
            AND  n.periodo = $periodo 
            AND c.id_unico = $concepto 
            AND cb.banco   = $id_banco 
            ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC");
            
            $pdf->SetFont('Arial','',7);
            $valort = 0;
            for ($e = 0; $e < count($rowe); $e++) { 
                $pdf->Cellfitscale(20,8, utf8_decode($rowe[$e][4]),1,0,'L');
                $pdf->Cellfitscale(90,8, ($rowe[$e][5]),1,0,'L');

                $x =$pdf->GetX();  
                $y =$pdf->GetY();
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = $concepto AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                $pdf->Cellfitscale(40,8, utf8_decode($rowe[$e][7]),1,0,'R'); 
                $pdf->Ln(8);
                $valort += $valor;
            }
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(110,8, utf8_decode('Total:'),1,0,'C');
            $pdf->Cellfitscale($filas,8, number_format($valort, 0, '.',','),1,0,'R');  

        }
    } else {


  

        $pdf->SetFont('Arial','B',8);
        $cx = $pdf->GetX();
        $cy = $pdf->GetY();

          
        $pdf->Ln();
        $pdf->Cell(20,5, utf8_decode('CÉDULA'),0,0,'C');
        $pdf->Cell(90,5, utf8_decode('NOMBRE'),0,0,'C');
        $xp = $pdf->GetX();
        $h  = 0;
        $alto = 0;
        if($_REQUEST['retencion']==1){
            for ($c=0; $c <count($ncon) ; $c++) { 
                $nconceptoi = $ncon[$c][0].' - '.$ncon[$c][1].'  ';
                $pdf->MultiCell(40,5, ($nconceptoi),0,'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $cy;
                $pdf->SetXY($xp + 40, $cy);
                $xp = $xp + 40;
                
            }
        } else { 
            $pdf->MultiCell(40,5, utf8_decode($nconcepto),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $cy;
            $pdf->SetXY($cx+150,$cy);
            $pdf->Cell(40,5, utf8_decode('CUENTA1'),0,0,'C');
        }
        
        $alto = $h;
        $pdf->SetXY($cx,$cy);
        $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(90,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(40,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(40,$alto, utf8_decode(''),1,0,'C');
        $pdf->Ln($alto);
    
        #***************************************************************#
        #**** Buscar Terceros***#
    

        $rowe = $con->Listar("SELECT DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
            ca.salarioactual , cb.numerocuenta 
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
        WHERE e.id_unico !=2 
        AND  n.periodo = $periodo 
        AND t.compania = $compania 
        AND c.id_unico  IN ($concepto)
        ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC");
      //$pdf->Cellfitscale(20,8,$SQL,1,0,'L');
        $pdf->SetFont('Arial','',7);
        $valort = 0;
        $valort2 = 0;
        for ($e = 0; $e < count($rowe); $e++) { 
            $pdf->Cellfitscale(20,8, utf8_decode($rowe[$e][4]),1,0,'L');
            $pdf->Cellfitscale(90,8, ($rowe[$e][5]),1,0,'L');
            $x =$pdf->GetX();  
            $y =$pdf->GetY();

            if($_REQUEST['retencion']==1){
                for ($c=0; $c <count($ncon) ; $c++) { 
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico =".$ncon[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                        AND n.periodo = $periodo ");
                    if($num_con[0][1] > 0){
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    $pdf->Cellfitscale(40,8, number_format($valor, 0, '.',','),1,0,'R');  
                    IF($c==0){
                        $valort +=$valor;
                    } else {
                        $valort2 +=$valor;
                    }
                    
                }
                $pdf->Ln(8);
            } else { 
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = $concepto AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                $pdf->Cellfitscale(40,8, number_format($valor, 0, '.',','),1,0,'R');  
                $pdf->Cellfitscale(40,8, utf8_decode($rowe[$e][7]),1,0,'R'); 
                $pdf->Ln(8);
                $valort += $valor;
            }

            
        }
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(110,8, utf8_decode('Total:'),1,0,'C');
        $pdf->Cellfitscale(40,8, number_format($valort, 0, '.',','),1,0,'R');  
        if($_REQUEST['retencion']==1){
            $pdf->Cellfitscale(40,8, number_format($valort2, 0, '.',','),1,0,'R');  
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
    WHERE td.nombre = 'Informe Concepto Detallado'
    ORDER BY rd.orden ASC";

    $fi = $mysqli->query($firmas);
    $altura = $pdf->GetY();
    if($altura > 220){
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
                $pdf->SetXY(100, $yyy);
                $pdf->Cell(60, 0, '', 'B');
                $pdf->Ln(3);
                $pdf->SetX(100);
                $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->SetX(100);
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
    }    

    ob_end_clean();
    $pdf->Output(0,'Inf_Concepto_Detallado('.date('d/m/Y').').pdf',0);      
    
}
#******** Tipo Excel *************#
 else{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Concepto_Detallado.xls");  
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>Informe Concepto Detallado</title>';
    echo '</head>';
    echo '<body>';
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
    echo '<th colspan="4" align="center"><strong>';
    echo '<br/>&nbsp;';
    echo '<br/>'.$razonsocial;
    echo '<br/>'.$nombreIdent.': '.$numeroIdent;
    echo '<br/>&nbsp;';
    echo '<br/>INFORME POR CONCEPTO DETALLADO';
    echo '<br/>&nbsp;';
    echo 'PERIODO:'.$nperiodo;
    echo '<br/>CONCEPTO:'.$nconcepto;
    echo '<br/>&nbsp;';
    echo '</strong>';
    echo '</th>';
    if($acumulado==1){        

        #* Buscar bancos 
        $rowb = $con->Listar("SELECT DISTINCT tb.id_unico, tb.razonsocial, tb.numeroidentificacion  
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico  
        LEFT JOIN gf_tercero tb ON cb.banco = tb.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
        AND  n.periodo = $periodo 
        AND c.id_unico in($concepto)
        AND tb.id_unico IS NOT NULL 
        ORDER BY tb.razonsocial ASC");
       
        for ($b=0; $b <count($rowb); $b++) { 
            $id_banco = $rowb[$b][0];
            echo '<tr><td colspan="4"><strong><i><br/>&nbsp;';
            echo '<br/>'.$rowb[$b][1].' - '.$rowb[$b][2];
            echo '<br/>&nbsp</i></strong></td></tr>  ';
            echo '<tr>';
            echo '<td><strong>CEDULA</strong></td>';
            echo '<td><strong>NOMBRE</strong></td>';
            echo '<td><strong>'.utf8_encode($nconcepto).'</strong></td>';
            echo '<td><strong>CUENTA</strong></td>';
            echo '</tr>';
            #***************************************************************#

            $rowe = $con->Listar("SELECT DISTINCT  e.id_unico, 
                e.codigointerno, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
                ca.salarioactual , cb.numerocuenta 
            FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
            LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
            WHERE e.id_unico !=2 
            AND t.compania = $compania 
            AND  n.periodo = $periodo 
            AND c.id_unico = $concepto 
            AND cb.banco   = $id_banco 
            ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC");
            
            $valort   = 0;
            for ($e = 0; $e < count($rowe); $e++) { 
                echo '<tr>';
                echo '<td align= "left">'.($rowe[$e][4]).'</td>';
                echo '<td>'.utf8_encode($rowe[$e][5]).'</td>';

                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = $concepto AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td>'.number_format($valor,0,'.',',').'</td>';  
                echo '<td>'.utf8_encode($rowe[$e][7]).'</td>'; 
                echo '</tr>';
                $valort  += $valor;
            }                
            echo '<tr>';
            echo '<td colspan="2"><strong>Total</strong></td>';
            echo '<td><strong>'.number_format($valort,0,'.',',').'</strong></td>'; 
            
            
            echo '</tr>';
        }
    } else {
    
    
        echo '<tr>';
        echo '<td><strong>CEDULA</strong></td>';
        echo '<td><strong>NOMBRE</strong></td>';
        if($_REQUEST['retencion']==1){
            for ($c=0; $c <count($ncon) ; $c++) { 
                $nconceptoi = $ncon[$c][0].' - '.$ncon[$c][1].'  ';
                echo '<td><strong>'.utf8_encode($nconceptoi).'</strong></td>';
            }
        } else { 
            echo '<td><strong>'.utf8_encode($nconcepto).'</strong></td>';
            echo '<td><strong>CUENTA BANCARIA</strong></td>';
        }
        echo '</tr>';
        #***************************************************************#
        #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
        $rowe = $con->Listar("SELECT DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos),
            ca.salarioactual , cb.numerocuenta 
        FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = t.id_unico 
        LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
        WHERE e.id_unico !=2 AND t.compania = $compania 
      
        AND  n.periodo = $periodo 
        AND c.id_unico  IN ($concepto)
        ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC");
        
        $valort = 0;
        $valort2 = 0;
        for ($e = 0; $e < count($rowe); $e++) { 
            echo '<tr>';
            echo '<td align= "left">'.($rowe[$e][4]).'</td>';
            echo '<td>'.utf8_encode($rowe[$e][5]).'</td>';
            if($_REQUEST['retencion']==1){
                for ($c=0; $c <count($ncon) ; $c++) { 
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico =".$ncon[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                        AND n.periodo = $periodo ");
                    if($num_con[0][1] > 0){
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    echo '<td>'.number_format($valor,0,'.',',').'</td>';
                    IF($c==0){
                        $valort +=$valor;
                    } else {
                        $valort2 +=$valor;
                    }
                    
                }
                
            } else { 
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico =$concepto AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td>'.number_format($valor,0,'.',',').'</td>';
                echo '<td>'.($rowe[$e][7]).'</td>';
                
                $valort +=$valor;
             
            
            }
            echo '</tr>';

            
        }                
        echo '<tr>';
        echo '<td colspan="2"><strong>Total</strong></td>';
        echo '<td><strong>'.number_format($valort,0,'.',',').'</strong></td>'; 
        if($_REQUEST['retencion']==1){
            echo '<td><strong>'.number_format($valort2,0,'.',',').'</strong></td>'; 
        }
        echo '</tr>';    
    }
    echo '</table>';
    echo '</body>';
}
?>