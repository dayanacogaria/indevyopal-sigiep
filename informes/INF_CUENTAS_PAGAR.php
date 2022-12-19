<?php
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();

#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
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

#*************** Recibir Variables ****************#
$fechaIn    = $_REQUEST['fechaini'];
$fechaFi    = $_REQUEST['fechafin'];
$fechaI     = fechaC($fechaIn);
$fechaF     = fechaC($fechaFi);
$terceroI   = $_REQUEST['sltTi'];
$terceroF   = $_REQUEST['sltTf'];

#******** Consulta De Comprobantes ********#
$rowt = $con->Listar("SELECT DISTINCT t.id_unico, 
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
    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
         t.numeroidentificacion, 
    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))   
FROM gf_comprobante_cnt cn 
LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
WHERE tc.clasecontable = 13 
AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF'
GROUP BY cn.id_unico  
ORDER BY cn.fecha, cn.numero");

if($_GET['t']=='1'){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    {
        function Header()
        { 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $nsucursal;
            global $ruta_logo;
            global $fechaIn;
            global $fechaFi;
            global $nomb_tipo;
            global $tipo;
            
            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,60,6,25);
            }
            $this->SetFont('Arial','B',12);
            $this->Cell(330,5,utf8_decode($razonsocial),0,0,'C');
            $this->Ln(5);
            $this->Cell(330, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
            $this->Ln(7);
            $this->SetFont('Arial','B',10);
            $this->Cell(330,5,utf8_decode('INFORME CUENTAS POR PAGAR'),0,0,'C');
            $this->Ln(5);
            $this->SetFont('Arial','B',9);
            $this->Cell(330,5,utf8_decode('Entre Fechas ' .$fechaIn.' A '.$fechaFi),0,0,'C');
            $this->Ln(8);
        }      

        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(15);
            $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
            $this->Cell(270);
            $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
        }
    }

    $pdf = new PDF('L','mm','Legal');
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','',9);
    $total = 0;
    $arraytercero = array();
    for ($t = 0; $t < count($rowt); $t++) {
        $tercero = $rowt[$t][0];
        #********* Buscar Cuentas Tercero Tercero **************#
        $row = $con->Listar("SELECT DISTINCT cn.id_unico,
            GROUP_CONCAT(dc.id_unico) 
            FROM gf_comprobante_cnt cn 
            LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
            WHERE tc.clasecontable = 13 
            AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND t.id_unico = $tercero 
            GROUP BY cn.id_unico  
            ORDER BY cn.fecha, cn.numero");
        for ($i = 0; $i < count($row); $i++) { 
            #*********** Buscar Afectaciones del Comprobante *********#
            $imprimir   = 0;
            $id_c       = $row[$i][0];
            $detalles   = $row[$i][1];        
            #*** Buscar Retenciones
            $rowr = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE comprobante = $id_c");
            if(count($rowr)>0){
                $retencion = $rowr[0][0];
            } else {
                $retencion = 0;
            }
            #*** Valor Comprobante 
            $rowv = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.comprobante = $id_c AND (c.clasecuenta = 4 OR c.clasecuenta =8)");
            $valor_b = $rowv[0][0];
            $rowa = $con->Listar("SELECT * FROM gf_detalle_comprobante WHERE detalleafectado IN (".$detalles.")");
            if(count($rowa)>0){   
                #****** Buscar Abonos *********#
                $rowab = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                    FROM gf_detalle_comprobante dc WHERE dc.detalleafectado IN (".$detalles.")");
                $abonos  = $rowab[0][0];
            } else {
                $abonos  = 0;
            }
            $saldo = $valor_b-$abonos;
            if($saldo>0){
                if(in_array($tercero, $arraytercero)) {

                } else {
                    array_push ( $arraytercero , $tercero );
                }
            }
        }
    }
    for ($t = 0; $t < count($rowt); $t++) {
        $tercero    = $rowt[$t][0];
        $tercero_n  = ucwords(mb_strtolower($rowt[$t][1])).' - '.$rowt[$t][2];
        if(in_array($tercero, $arraytercero)) {
            $pdf->SetFont('Arial','I',12);
            $pdf->SetX(25);
            $pdf->Cell(315,10, utf8_decode($tercero_n),1,0,'L');
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',9);
            $pdf->SetX(25);
            $pdf->Cell(25,10, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(30,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(90,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(30,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(30,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(30,10,utf8_decode(''),1,0,'C');
            $pdf->Cell(30,10,utf8_decode(''),1,0,'C');
            $pdf->Setx(25);
            $pdf->Cell(25,10, utf8_decode('FECHA'),0,0,'C');
            $pdf->Cell(50,5,utf8_decode('TIPO'),0,0,'C');
            $pdf->Cell(30,5,utf8_decode('NÚMERO'),0,0,'C');
            $pdf->Cell(90,10,utf8_decode('DESCRIPCIÓN'),0,0,'C');
            $pdf->Cell(30,5,utf8_decode('VALOR'),0,0,'C');
            $pdf->Cell(30,5,utf8_decode('VALOR'),0,0,'C');
            $pdf->Cell(30,10,utf8_decode('ABONOS'),0,0,'C');
            $pdf->Cell(30,10,utf8_decode('SALDO'),0,0,'C');
            $pdf->Ln(5);
            $pdf->SetX(25);
            $pdf->Cell(25,5, utf8_decode(''),0,0,'C');
            $pdf->Cell(50,5,utf8_decode('COMPROBANTE'),0,0,'C');
            $pdf->Cell(30,5,utf8_decode('COMPROBANTE'),0,0,'C');
            $pdf->Cell(90,5,utf8_decode(''),0,0,'C');
            $pdf->Cell(30,5,utf8_decode('BASE'),0,0,'C');
            $pdf->Cell(30,5,utf8_decode('RETENCIONES'),0,0,'C');
            $pdf->Cell(30,5,utf8_decode(''),0,0,'C');
            $pdf->Cell(30,5,utf8_decode(''),0,0,'C');
            $pdf->Ln(5);
            
            $row = $con->Listar("SELECT DISTINCT cn.id_unico, cn.fecha, 
                DATE_FORMAT(cn.fecha, '%d/%m/%Y'), 
                tc.sigla, tc.nombre, 
                cn.descripcion,  
                GROUP_CONCAT(dc.id_unico), cn.numero 
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
                LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
                LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                WHERE tc.clasecontable = 13 
                AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                AND t.id_unico = $tercero 
                GROUP BY cn.id_unico  
                ORDER BY cn.fecha, cn.numero");
            $totalt = 0;
            $pdf->SetFont('Arial','',9);
            for ($i = 0; $i < count($row); $i++) { 
                #*********** Buscar Afectaciones del Comprobante *********#
                $imprimir   = 0;
                $id_c       = $row[$i][0];
                $fecha      = $row[$i][2];
                $tipo       = mb_strtoupper($row[$i][3]).' - '.ucwords(mb_strtolower($row[$i][4]));
                $numero     = $row[$i][7]; 
                $descripcion= $row[$i][5];
                $detalles   = $row[$i][6];        
                #*** Buscar Retenciones
                $rowr = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE comprobante = $id_c");
                if(count($rowr)>0){
                    $retencion = $rowr[0][0];
                } else {
                    $retencion = 0;
                }
                #*** Valor Comprobante 
                $rowv = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                    WHERE dc.comprobante = $id_c AND (c.clasecuenta = 4 OR c.clasecuenta =8)");
                $valor_b = $rowv[0][0];
                $rowa = $con->Listar("SELECT * FROM gf_detalle_comprobante WHERE detalleafectado IN (".$detalles.")");
                if(count($rowa)>0){   
                    #****** Buscar Abonos *********#
                    $rowab = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                        FROM gf_detalle_comprobante dc WHERE dc.detalleafectado IN (".$detalles.")");
                    $abonos  = $rowab[0][0];
                } else {
                    $abonos  = 0;
                }
                $saldo = $valor_b-$abonos;
                if($saldo>0){
                    $pdf->SetX(25);
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->CellFitScale(25,5,utf8_decode($fecha),1,0,'L');
                    $pdf->CellFitScale(50,5,utf8_decode($tipo),1,0,'L');
                    $pdf->CellFitScale(30,5,utf8_decode($numero),1,0,'L');            
                    $pdf->CellFitScale(90,5,utf8_decode($descripcion),1,0,'L');
                    $pdf->CellFitScale(30,5,number_format($valor_b,2,'.',','),1,0,'R');
                    $pdf->CellFitScale(30,5,number_format($retencion,2,'.',','),1,0,'R');
                    $pdf->CellFitScale(30,5,number_format($abonos,2,'.',','),1,0,'R');
                    $pdf->CellFitScale(30,5,number_format($saldo,2,'.',','),1,0,'R');
                    $pdf->Ln(5);
                    $totalt +=$saldo;
                }
                $alt = $pdf->GetY();
                if($alt>180){
                    $pdf->AddPage();
                }
            }
            $pdf->SetFont('Arial','B',9);
            $pdf->SetX(25);
            $pdf->Cell(285,5,utf8_decode('Subtotal: '.$tercero_n),1,0,'L');
            $pdf->CellFitScale(30,5,number_format($totalt,2,'.',','),1,0,'R');
            $pdf->Ln(5);
            $total +=$totalt;
//            $pdf->Ln(0.5);
//            $pdf->SetX(25);
//            $pdf->Cell(315,0.1,'',1,0,'R');
//            $pdf->Ln(0.5);    
        }
        
    }   
    $pdf->SetFont('Arial','B',9);
    $pdf->SetX(25);
    $pdf->Cell(285,10,utf8_decode('TOTAL CUENTAS POR PAGAR'),1,0,'L');
    $pdf->CellFitScale(30,10,number_format($total,2,'.',','),1,0,'R');
    $pdf->Ln(10);
    
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0,utf8_decode('Informe_Cuentas_Pagar('.date('d-m-Y').').pdf'),0);
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Cuentas_Pagar.xls"); ?> 
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cuentas Por Pagar</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="8" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>INFORME CUENTAS POR PAGAR         
        <br/>ENTRE <?php echo $fechaIn.' - '.$fechaFi ?>
        <br/>&nbsp;                 
        </strong>
    </th>
        <tr></tr>    
    <?PHP 
    $total = 0;
    $arraytercero = array();
    for ($t = 0; $t < count($rowt); $t++) {
        $tercero = $rowt[$t][0];
        #********* Buscar Cuentas Tercero Tercero **************#
        $row = $con->Listar("SELECT DISTINCT cn.id_unico,
            GROUP_CONCAT(dc.id_unico) 
            FROM gf_comprobante_cnt cn 
            LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
            WHERE tc.clasecontable = 13 
            AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND t.id_unico = $tercero 
            GROUP BY cn.id_unico  
            ORDER BY cn.fecha, cn.numero");
        for ($i = 0; $i < count($row); $i++) { 
            #*********** Buscar Afectaciones del Comprobante *********#
            $imprimir   = 0;
            $id_c       = $row[$i][0];
            $detalles   = $row[$i][1];        
            #*** Buscar Retenciones
            $rowr = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE comprobante = $id_c");
            if(count($rowr)>0){
                $retencion = $rowr[0][0];
            } else {
                $retencion = 0;
            }
            #*** Valor Comprobante 
            $rowv = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.comprobante = $id_c AND (c.clasecuenta = 4 OR c.clasecuenta =8)");
            $valor_b = $rowv[0][0];
            $rowa = $con->Listar("SELECT * FROM gf_detalle_comprobante WHERE detalleafectado IN (".$detalles.")");
            if(count($rowa)>0){   
                #****** Buscar Abonos *********#
                $rowab = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                    FROM gf_detalle_comprobante dc WHERE dc.detalleafectado IN (".$detalles.")");
                $abonos  = $rowab[0][0];
            } else {
                $abonos  = 0;
            }
            $saldo = $valor_b-$abonos;
            if($saldo>0){
                if(in_array($tercero, $arraytercero)) {

                } else {
                    array_push ( $arraytercero , $tercero );
                }
            }
        }
    }
    for ($t = 0; $t < count($rowt); $t++) {
        $tercero    = $rowt[$t][0];
        $tercero_n  = ucwords(mb_strtolower($rowt[$t][1])).' - '.$rowt[$t][2];
        if(in_array($tercero, $arraytercero)) {
            echo '<th colspan="8" align="left"><strong><i>';
            echo '<br/>&nbsp;';
            echo '<br/>'.$tercero_n;
            echo '<br/>&nbsp;</i></strong>';
            echo '</th>';
            echo '<tr>';
            echo '<td rowspan="2"><center><strong>FECHA</strong></center></td>';
            echo '<td rowspan="2"><center><strong>TIPO COMPROBANTE</strong></center></td>';
            echo '<td rowspan="2"><center><strong>NÚMERO</strong></center></td>';
            echo '<td rowspan="2"><center><strong>DESCRIPCIÓN</strong></center></td>';
            echo '<td rowspan="2"><center><strong>VALOR BASE</strong></center></td>';
            echo '<td rowspan="2"><center><strong>VALOR RETENCIONES</strong></center></td>';
            echo '<td rowspan="2"><center><strong>ABONOS</strong></center></td>';
            echo '<td rowspan="2"><center><strong>SALDO</strong></center></td>';
            echo '</tr>';
            echo '<tr></tr>';
            $row = $con->Listar("SELECT DISTINCT cn.id_unico, cn.fecha, 
                DATE_FORMAT(cn.fecha, '%d/%m/%Y'), 
                tc.sigla, tc.nombre, 
                cn.descripcion,  
                GROUP_CONCAT(dc.id_unico), cn.numero 
                FROM gf_comprobante_cnt cn 
                LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
                LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
                LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                WHERE tc.clasecontable = 13 
                AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                AND t.id_unico = $tercero 
                GROUP BY cn.id_unico  
                ORDER BY cn.fecha, cn.numero");
            $totalt = 0;
            for ($i = 0; $i < count($row); $i++) { 
                #*********** Buscar Afectaciones del Comprobante *********#
                $imprimir   = 0;
                $id_c       = $row[$i][0];
                $fecha      = $row[$i][2];
                $tipo       = mb_strtoupper($row[$i][3]).' - '.ucwords(mb_strtolower($row[$i][4]));
                $numero     = $row[$i][7]; 
                $descripcion= $row[$i][5];
                $detalles   = $row[$i][6];        
                #*** Buscar Retenciones
                $rowr = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE comprobante = $id_c");
                if(count($rowr)>0){
                    $retencion = $rowr[0][0];
                } else {
                    $retencion = 0;
                }
                #*** Valor Comprobante 
                $rowv = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                    WHERE dc.comprobante = $id_c AND (c.clasecuenta = 4 OR c.clasecuenta =8)");
                $valor_b = $rowv[0][0];
                $rowa = $con->Listar("SELECT * FROM gf_detalle_comprobante WHERE detalleafectado IN (".$detalles.")");
                if(count($rowa)>0){   
                    #****** Buscar Abonos *********#
                    $rowab = $con->Listar("SELECT SUM(IF(dc.valor>0, dc.valor, dc.valor*-1)) 
                        FROM gf_detalle_comprobante dc WHERE dc.detalleafectado IN (".$detalles.")");
                    $abonos  = $rowab[0][0];
                } else {
                    $abonos  = 0;
                }
                $saldo = $valor_b-$abonos;
                if($saldo>0){
                    echo '<tr>';
                    echo '<td>'.$fecha.'</td>';
                    echo '<td>'.$tipo.'</td>';
                    echo '<td>'.$numero.'</td>';
                    echo '<td>'.$descripcion.'</td>';
                    echo '<td>'.number_format($valor_b,2,'.',',').'</td>';
                    echo '<td>'.number_format($retencion,2,'.',',').'</td>';
                    echo '<td>'.number_format($abonos,2,'.',',').'</td>';
                    echo '<td>'.number_format($saldo,2,'.',',').'</td>';
                    echo '</tr>';
                    $totalt +=$saldo;
                }
            }
            echo '<tr>';
            echo '<td colspan="7"><strong>Subtotal: '.$tercero_n.'</strong></td>';
            echo '<td><strong>'.number_format($totalt,2,'.',',').'</strong></td>';
            echo '</tr>'; 
            $total +=$totalt;
        }
    }
    echo '<tr>';
    echo '<td colspan="7"><strong>TOTAL CUENTAS POR PAGAR</strong></td>';
    echo '<td><strong>'.number_format($total,2,'.',',').'</strong></td>';
    echo '</tr>'; 
    
    ?>
<?php } ?>

