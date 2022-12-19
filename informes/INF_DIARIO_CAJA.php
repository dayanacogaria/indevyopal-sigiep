<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#01/03/2018 | Erica G. | Archivo Creado
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
$fechaI    = $_POST['fechaI'];
$fechaF    = $_POST['fechaF'];
$fechaI     = fechaC($fechaI);
$fechaF     = fechaC($fechaF);
$tipocI     = $_POST['tipoI'];
$tipocF     = $_POST['tipoF'];
$cuenta     = $_POST['cuentaI'];
$exportar   = $_REQUEST['tipo'];        

#   ************ Buscar Datos Cuenta ************    #
$dc = $con->Listar("SELECT codi_cuenta, UPPER(nombre) 
        FROM gf_cuenta WHERE id_unico = $cuenta");
$nombreCuenta = $dc[0][0].' - '.$dc[0][1];

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
#Consulta Saldo Inicial  y Saldo Final
#SI FECHA INICIAL =01 DE ENERO
    $fechaPrimera = $nanno . '-01-01';
    if ($fechaI == $fechaPrimera) {
        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $fechaMax = $nanno . '-12-31';
        $com = "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE
                      cp.fecha BETWEEN '$fechaI' AND '$fechaMax' 
                      AND cc.id_unico = '5' 
                      AND dc.cuenta = '$cuenta' AND cp.parametrizacionanno =$anno";
        $com = $mysqli->query($com);
        if (mysqli_num_rows($com) > 0) {
            $saldo = mysqli_fetch_row($com);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
        }
    #SI FECHA INICIAL !=01 DE ENERO
    } else {
        #TRAE EL SALDO INICIAL
        $sInicial = "SELECT SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                WHERE dc.cuenta='$cuenta' 
                AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaI' 
                AND cn.parametrizacionanno =$anno AND cc.id_unico !='20'";
        $sald = $mysqli->query($sInicial);
        if (mysqli_num_rows($sald) > 0) {
            $saldo = mysqli_fetch_row($sald);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
        }
    }
    #** Sumatoria Cuenta Principal
    $sumc = $con->Listar("SELECT c.codi_cuenta, LOWER(c.nombre), GROUP_CONCAT(cn.id_unico), 
        IF(c.naturaleza=1 && dc.valor>0,SUM(dc.valor),0) as d1,
        IF(c.naturaleza=2 && dc.valor<0,SUM(dc.valor*-1),0) as d2,
        IF(c.naturaleza=1 && dc.valor<0,SUM(dc.valor*-1),0) as c2,
        IF(c.naturaleza=2 && dc.valor>0,SUM(dc.valor),0) as c1 
        FROM gf_detalle_comprobante dc 
        LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico =dc.comprobante 
        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
        WHERE cn.fecha >='$fechaI' AND cn.fecha <='$fechaF'  
        AND dc.cuenta =  $cuenta 
        AND cn.parametrizacionanno = $anno ");
    $debito  = $sumc[0][3]+$sumc[0][4];
    $credito = $sumc[0][5]+$sumc[0][6];
    $saldoNuevo = $saldo + $debito - $credito;
    
#*******************************************************************************#    

switch ($exportar){
    # *** Generar Pdf **#
    case 1:
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
                global $tipo;
                global $nombreCuenta;
                $numpaginas=$numpaginas+1;

                $this->SetFont('Arial','B',10);

                if($ruta_logo != '')
                {
                  $this->Image('../'.$ruta_logo,10,5,28);
                }
                $this->SetFont('Arial','B',10);	
                $this->MultiCell(195,5,utf8_decode($razonsocial),0,'C');		
                $this->SetX(10);
                $this->Ln(1);
                $this->Cell(195,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(195,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(195,5,utf8_decode('Tel: '.$telefonoTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(195,5,utf8_decode('BOLETÍN DIARIO DE CAJA '),0,0,'C');
                $this->Ln(5);
                $this->SetX(10);
                $this->Cell(195,5,utf8_decode('CUENTA:'.$nombreCuenta),0,0,'C');
                $this->Ln(5);
            }      

            function Footer(){
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }
        $pdf = new PDF('P','mm','Letter');   
        $nb=$pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->Ln(5);
        $pdf->SetFont('Arial','I',12);
        $pdf->Cell(195,5,utf8_decode('Saldo Inicial $'.number_format($saldo,2,'.',',')),0,0,'L');
        $pdf->Ln(5);
        $totalFinalDebito  = 0;
        $totalFinalCredito = 0;
        #  ************** Consultar Las Fechas **************  #
        $rowf =$con->Listar("SELECT DISTINCT cn.fecha,    
            CASE DAYOFWEEK(cn.fecha)
                   WHEN 1 THEN 'Domingo'
                   WHEN 2 THEN 'Lunes'
                   WHEN 3 THEN 'Martes'
                   WHEN 4 THEN 'Miércoles'
                   WHEN 5 THEN 'Jueves'
                   WHEN 6 THEN 'Viernes'
                   WHEN 7 THEN 'Sábado'
                   END nombre_dia,
           DAY(cn.fecha), 
                CASE MONTH(cn.fecha) 
                WHEN  1 THEN 'Enero'
                WHEN  2 THEN 'Febrero'
                WHEN  3 THEN 'Marzo'
                WHEN  4 THEN 'Abril'
                WHEN  5 THEN 'Mayo'
                WHEN  6 THEN 'Junio'
                WHEN  7 THEN 'Julio'
                WHEN  8 THEN 'Agosto'
                WHEN  9 THEN 'Septiembre'
                WHEN  10 THEN 'Octubre'
                WHEN  11 THEN 'Noviembre'
                WHEN  12 THEN 'Diciembre' 
                END as 'Mes', 
                YEAR(cn.fecha) 
            FROM gf_comprobante_cnt cn 
            LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
            WHERE tc.id_unico BETWEEN '$tipocI' AND '$tipocF' 
            AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND dc.cuenta = $cuenta 
            AND cn.parametrizacionanno = $anno 
            ORDER BY cn.fecha ASC");
        if(count($rowf)>0) { 
            for ($f = 0; $f < count($rowf); $f++) {
                $alt = $pdf->GetY();
                if($alt>240){
                    $pdf->AddPage();
                    $pdf->Ln(5);
                }
                $fechaP = $rowf[$f][0];
                $totalFechaD = 0;
                $totalFechaC = 0;
                $nfecha = $rowf[$f][1].', '.$rowf[$f][2].' De '.$rowf[$f][3].' De '.$rowf[$f][4];
                $pdf->SetFont('Arial','B',12);
                $pdf->Ln(5);
                $pdf->Cell(195,8,utf8_decode($nfecha),0,0,'C');
                $pdf->Ln(5);
                # ** Consulta de Tipos De Comprobante Según Variables Recibidas 
                $rowtc = $con->Listar("SELECT DISTINCT  tc.id_unico, UPPER(sigla), LOWER(nombre) FROM gf_tipo_comprobante tc 
                    LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico 
                    LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                    WHERE tc.id_unico BETWEEN '$tipocI' AND '$tipocF' 
                    AND cn.fecha =  '$fechaP' 
                    AND dc.cuenta = $cuenta 
                    AND cn.parametrizacionanno = $anno 
                    ORDER BY tc.id_unico ASC");
                for ($i = 0; $i < count($rowtc); $i++) {
                    $alt = $pdf->GetY();
                    if($alt>240){
                        $pdf->AddPage();
                        $pdf->Ln(5);
                    }
                    $ncomprobante = $rowtc[$i][1].' - '.ucwords($rowtc[$i][2]);
                    $totalComprobanteD = 0;
                    $totalComprobanteC = 0;
                    $tipo = $rowtc[$i][0];
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Ln(5);
                    $pdf->Cell(195,8,utf8_decode('Tipo De Movimiento '.$rowtc[$i][1].' - '.ucwords($rowtc[$i][2])),0,0,'L');
                    $pdf->ln(10);
                    #** Titulos
                    $alt = $pdf->GetY();
                    if($alt>240){
                        $pdf->AddPage();
                        $pdf->Ln(5);
                    }
                    $pdf->Cell(40,8,utf8_decode('CUENTA'),1,0,'C');
                    $pdf->Cell(85,8,utf8_decode('NOMBRE'),1,0,'C');
                    $pdf->Cell(35,8,utf8_decode('VALOR DÉBITO'),1,0,'C');
                    $pdf->Cell(35,8,utf8_decode('VALOR CRÉDITO'),1,0,'C');
                    $pdf->Ln(8);
                    
                    #** Sumatoria Cuenta Principal
                    $sumc = $con->Listar("SELECT c.codi_cuenta, LOWER(c.nombre), GROUP_CONCAT(cn.id_unico), 
                        IF(c.naturaleza=1 && dc.valor>0,SUM(dc.valor),0) as d1,
                        IF(c.naturaleza=2 && dc.valor<0,SUM(dc.valor*-1),0) as d2,
                        IF(c.naturaleza=1 && dc.valor<0,SUM(dc.valor*-1),0) as c2,
                        IF(c.naturaleza=2 && dc.valor>0,SUM(dc.valor),0) as c1 
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico =dc.comprobante 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        WHERE cn.fecha =  '$fechaP'  AND dc.cuenta =  $cuenta 
                        AND cn.parametrizacionanno = $anno AND cn.tipocomprobante = $tipo 
                        ORDER BY c.codi_cuenta ASC ");

                    $debito  = $sumc[0][3]+$sumc[0][4];
                    $credito = $sumc[0][5]+$sumc[0][6];
                    $totalComprobanteD +=$debito;
                    $totalComprobanteC +=$credito;
                    $alt = $pdf->GetY();
                    if($alt>240){
                        $pdf->AddPage();
                        $pdf->Ln(5);
                        $pdf->Cell(40,8,utf8_decode('CUENTA'),1,0,'C');
                        $pdf->Cell(85,8,utf8_decode('NOMBRE'),1,0,'C');
                        $pdf->Cell(35,8,utf8_decode('VALOR DÉBITO'),1,0,'C');
                        $pdf->Cell(35,8,utf8_decode('VALOR CRÉDITO'),1,0,'C');
                        $pdf->Ln(8);
                    }
                    $pdf->SetFont('Arial','',10);
                    $pdf->Cell(40,5,utf8_decode($sumc[0][0]),0,0,'L');
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell(85,5,utf8_decode(ucwords($sumc[0][1])),0,'L');
                    $y2 = $pdf->GetY();
                    $h = $y2-$y;
                    $pdf->SetXY($x+85, $y);
                    $pdf->Cell(35,5,utf8_decode(number_format($debito,2,'.',',')),0,0,'R');
                    $pdf->Cell(35,5,utf8_decode(number_format($credito,2,'.',',')),0,0,'R');
                    $pdf->Ln($h);
                    #** Sumatoria Cuentas Contrapartida
                    $cuentasc = $sumc[0][2];
                    $ctasc = $con->Listar("SELECT c.codi_cuenta, LOWER(c.nombre), 
                        IF(c.naturaleza=1 && dc.valor>0,SUM(dc.valor),0) as d1,
                        IF(c.naturaleza=2 && dc.valor<0,SUM(dc.valor*-1),0) as d2,
                        IF(c.naturaleza=1 && dc.valor<0,SUM(dc.valor*-1),0) as c2,
                        IF(c.naturaleza=2 && dc.valor>0,SUM(dc.valor),0) as c1 
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico =dc.comprobante 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        WHERE cn.id_unico IN($cuentasc) 
                        AND dc.cuenta !=  $cuenta 
                        GROUP BY c.id_unico 
                        ORDER BY c.codi_cuenta ASC");
                    for ($z = 0; $z < count($ctasc); $z++) {
                        $alt = $pdf->GetY();
                        if($alt>240){
                            $pdf->AddPage();
                            $pdf->Ln(5);
                            $pdf->Cell(40,8,utf8_decode('CUENTA'),1,0,'C');
                            $pdf->Cell(85,8,utf8_decode('NOMBRE'),1,0,'C');
                            $pdf->Cell(35,8,utf8_decode('VALOR DÉBITO'),1,0,'C');
                            $pdf->Cell(35,8,utf8_decode('VALOR CRÉDITO'),1,0,'C');
                            $pdf->Ln(8);
                        }
                        $debitoc  = $ctasc[$z][2]+$ctasc[$z][3];
                        $creditoc = $ctasc[$z][4]+$ctasc[$z][5];
                        $pdf->Cell(40,5,utf8_decode($ctasc[$z][0]),0,0,'L');
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell(85,5,utf8_decode(ucwords($ctasc[$z][1])),0,'L');
                        $y2 = $pdf->GetY();
                        $h = $y2-$y;
                        $pdf->SetXY($x+85, $y);
                        $pdf->Cell(35,5,utf8_decode(number_format($debitoc,2,'.',',')),0,0,'R');
                        $pdf->Cell(35,5,utf8_decode(number_format($creditoc,2,'.',',')),0,0,'R');
                        $pdf->Ln($h);
                        $totalComprobanteD +=$debitoc;
                        $totalComprobanteC +=$creditoc;
                    }
                    
                    $pdf->Cell(125,0.5,'',0,0,'L');
                    $pdf->Cell(70,0.5,'',1,0,'L');
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',10);                    
                    $pdf->Cell(125,5,utf8_decode('Subtotal Tipo Movimiento '.$ncomprobante),0,0,'R');
                    $pdf->Cell(35,5,utf8_decode(number_format($totalComprobanteD,2,'.',',')),0,0,'R');
                    $pdf->Cell(35,5,utf8_decode(number_format($totalComprobanteC,2,'.',',')),0,0,'R');
                    $pdf->Ln(5);
                    $totalFechaD += $totalComprobanteD;
                    $totalFechaC += $totalComprobanteC;
                }
                $pdf->Cell(125,0.5,'',0,0,'L');
                $pdf->Cell(70,0.5,'',1,0,'L');
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(125,5,utf8_decode('Subtotal  '.$nfecha),0,0,'R');
                $pdf->Cell(35,5,utf8_decode(number_format($totalFechaD,2,'.',',')),0,0,'R');
                $pdf->Cell(35,5,utf8_decode(number_format($totalFechaC,2,'.',',')),0,0,'R');
                $pdf->Ln(5);
                $totalFechaD += $totalComprobanteD;
                $totalFechaC += $totalComprobanteC;
                $totalFinalDebito  += $totalFechaD;
                $totalFinalCredito += $totalFechaC;
            }
        }
        $pdf->Cell(125,0.5,'',0,0,'L');
        $pdf->Cell(70,0.5,'',1,0,'L');
        $pdf->Ln(2);
        $pdf->SetFont('Arial','B',10);
        $alt = $pdf->GetY();
        if($alt>240){
            $pdf->AddPage();
        }
        $pdf->Cell(125,5,utf8_decode('Totales  '),0,0,'R');
        $pdf->Cell(35,5,utf8_decode(number_format($totalFinalDebito,2,'.',',')),0,0,'R');
        $pdf->Cell(35,5,utf8_decode(number_format($totalFinalCredito,2,'.',',')),0,0,'R');
        $pdf->Ln(10);
       
        $pdf->SetFont('Arial','I',12);
        $pdf->Cell(195,5,utf8_decode('Saldo A Fecha Final $'.number_format($saldoNuevo,2,'.',',')),0,0,'L');
        $pdf->Ln(10);
        
        $pdf->SetFont('Arial','I',12);
        if($saldoNuevo <0){
            $num = $saldoNuevo*-1;
            $saldoLetras = ' - '.numtoletras($num);
        } else {
            $saldoLetras = numtoletras($saldoNuevo);
        }
        $pdf->MultiCell(195,5,utf8_decode('Saldo A Fecha Final En Letras '.ucwords(mb_strtolower($saldoLetras))),0,'L');
        $pdf->Ln(5);
        
        ob_end_clean();		
        $pdf->Output(0,'Informe_Boletin_Diario_Caja.pdf',0);
    break;
    # *** Generar xls **#
    case 2:
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Informe_Boletin_Diario_Caja.xls"); ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Boletín Diario De Caja</title>
            </head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <th colspan="4" align="center"><strong>
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                        <br/>&nbsp;
                        <br/>BOLETÍN DIARIO DE CAJA 
                        <br/>CUENTA: <?PHP echo $nombreCuenta; ?> 
                        <br/>&nbsp;</strong>
                    </th>
                    <tbody>
                        <tr><td colspan="4" ><strong><br/><i>Saldo Inicial : $<?php echo number_format($saldo,2,'.',',');?></i><br/>&nbsp;</strong></td></tr>
                        <?php 
                        $totalFinalDebito  = 0;
                        $totalFinalCredito = 0;
                        #  ************** Consultar Las Fechas **************  #
                        $rowf =$con->Listar("SELECT DISTINCT cn.fecha,    
                            CASE DAYOFWEEK(cn.fecha)
                                   WHEN 1 THEN 'Domingo'
                                   WHEN 2 THEN 'Lunes'
                                   WHEN 3 THEN 'Martes'
                                   WHEN 4 THEN 'Miércoles'
                                   WHEN 5 THEN 'Jueves'
                                   WHEN 6 THEN 'Viernes'
                                   WHEN 7 THEN 'Sábado'
                                   END nombre_dia,
                           DAY(cn.fecha), 
                                CASE MONTH(cn.fecha) 
                                WHEN  1 THEN 'Enero'
                                WHEN  2 THEN 'Febrero'
                                WHEN  3 THEN 'Marzo'
                                WHEN  4 THEN 'Abril'
                                WHEN  5 THEN 'Mayo'
                                WHEN  6 THEN 'Junio'
                                WHEN  7 THEN 'Julio'
                                WHEN  8 THEN 'Agosto'
                                WHEN  9 THEN 'Septiembre'
                                WHEN  10 THEN 'Octubre'
                                WHEN  11 THEN 'Noviembre'
                                WHEN  12 THEN 'Diciembre' 
                                END as 'Mes', 
                                YEAR(cn.fecha) 
                            FROM gf_comprobante_cnt cn 
                            LEFT JOIN gf_tipo_comprobante tc  ON cn.tipocomprobante = tc.id_unico 
                            LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                            WHERE tc.id_unico BETWEEN '$tipocI' AND '$tipocF' 
                            AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                            AND dc.cuenta = $cuenta 
                            AND cn.parametrizacionanno = $anno 
                            ORDER BY cn.fecha ASC");
                        for ($f = 0; $f < count($rowf); $f++) {
                            $fechaP = $rowf[$f][0];
                            $totalFechaD = 0;
                            $totalFechaC = 0;
                            $nfecha = $rowf[$f][1].', '.$rowf[$f][2].' De '.$rowf[$f][3].' De '.$rowf[$f][4];
                            echo '<tr><td colspan="4"><center>
                                            <strong><br/>'.$nfecha.'<br/>&nbsp;</strong>
                                  </center></td></tr>';
                            # ** Consulta de Tipos De Comprobante Según Variables Recibidas 
                            $rowtc = $con->Listar("SELECT DISTINCT  tc.id_unico, UPPER(sigla), LOWER(nombre) FROM gf_tipo_comprobante tc 
                                LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico 
                                LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                                WHERE tc.id_unico BETWEEN '$tipocI' AND '$tipocF' 
                                AND cn.fecha =  '$fechaP' 
                                AND dc.cuenta = $cuenta 
                                AND cn.parametrizacionanno = $anno 
                                ORDER BY tc.id_unico ASC");
                            for ($i = 0; $i < count($rowtc); $i++) {
                                $ncomprobante = $rowtc[$i][1].' - '.ucwords($rowtc[$i][2]);
                                $totalComprobanteD = 0;
                                $totalComprobanteC = 0;
                                $tipo = $rowtc[$i][0];
                                echo '<tr>
                                        <td colspan="4">
                                            <strong><br/>Tipo De Movimiento '.$rowtc[$i][1].' - '.ucwords($rowtc[$i][2]).'<br/>&nbsp;</strong>
                                        </td>
                                    </tr>';
                                #** Titulos
                                echo '<tr>';
                                echo '<td><center><strong>Código Cuenta</strong></center></td>';
                                echo '<td><center><strong>Nombre</strong></center></td>';
                                echo '<td><center><strong>Valor Débito</strong></center></td>';
                                echo '<td><center><strong>Valor Crédito</strong></center></td>';
                                echo '</tr>';
                                #** Sumatoria Cuenta Principal
                                $sumc = $con->Listar("SELECT c.codi_cuenta, LOWER(c.nombre), GROUP_CONCAT(cn.id_unico), 
                                    IF(c.naturaleza=1 && dc.valor>0,SUM(dc.valor),0) as d1,
                                    IF(c.naturaleza=2 && dc.valor<0,SUM(dc.valor*-1),0) as d2,
                                    IF(c.naturaleza=1 && dc.valor<0,SUM(dc.valor*-1),0) as c2,
                                    IF(c.naturaleza=2 && dc.valor>0,SUM(dc.valor),0) as c1 
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico =dc.comprobante 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    WHERE cn.fecha =  '$fechaP'  AND dc.cuenta =  $cuenta 
                                    AND cn.parametrizacionanno = $anno AND cn.tipocomprobante = $tipo 
                                    ORDER BY c.codi_cuenta ASC ");
                                
                                $debito  = $sumc[0][3]+$sumc[0][4];
                                $credito = $sumc[0][5]+$sumc[0][6];
                                $totalComprobanteD +=$debito;
                                $totalComprobanteC +=$credito;
                                echo '<tr>';
                                echo '<td>'.$sumc[0][0].'</td>';
                                echo '<td>'.ucwords($sumc[0][1]).'</td>';
                                echo '<td>'.number_format($debito,2,'.',',').'</td>';
                                echo '<td>'.number_format($credito,2,'.',',').'</td>';
                                echo '</tr>';
                                #** Sumatoria Cuentas Contrapartida
                                $cuentasc = $sumc[0][2];
                                $ctasc = $con->Listar("SELECT c.codi_cuenta, LOWER(c.nombre), 
                                    IF(c.naturaleza=1 && dc.valor>0,SUM(dc.valor),0) as d1,
                                    IF(c.naturaleza=2 && dc.valor<0,SUM(dc.valor*-1),0) as d2,
                                    IF(c.naturaleza=1 && dc.valor<0,SUM(dc.valor*-1),0) as c2,
                                    IF(c.naturaleza=2 && dc.valor>0,SUM(dc.valor),0) as c1 
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico =dc.comprobante 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    WHERE cn.id_unico IN($cuentasc) 
                                    AND dc.cuenta !=  $cuenta 
                                    GROUP BY c.id_unico 
                                    ORDER BY c.codi_cuenta ASC");
                                for ($z = 0; $z < count($ctasc); $z++) {
                                    $debitoc  = $ctasc[$z][2]+$ctasc[$z][3];
                                    $creditoc = $ctasc[$z][4]+$ctasc[$z][5];
                                    echo '<tr>';
                                    echo '<td>'.$ctasc[$z][0].'</td>';
                                    echo '<td>'.ucwords($ctasc[$z][1]).'</td>';
                                    echo '<td>'.number_format($debitoc,2,'.',',').'</td>';
                                    echo '<td>'.number_format($creditoc,2,'.',',').'</td>';
                                    echo '</tr>';
                                    $totalComprobanteD +=$debitoc;
                                    $totalComprobanteC +=$creditoc;
                                }
                                echo '<tr>
                                    <td colspan="2" align="center"><strong><br/>Subtotal Tipo Movimiento '.$ncomprobante.' <br/>&nbsp;</strong></td>
                                    <td><strong><br/>'.number_format($totalComprobanteD,2,'.',',').'<br/>&nbsp; </strong></td>
                                    <td><strong><br/>'.number_format($totalComprobanteC,2,'.',',').'<br/>&nbsp; </strong></td>
                                 </tr>';
                                $totalFechaD += $totalComprobanteD;
                                $totalFechaC += $totalComprobanteC;
                            }
                            echo '<tr>
                                    <td colspan="2" align="center"><br/><strong>Subtotal '.$nfecha.'  </strong><br/>&nbsp;</td>
                                    <td><strong><br/>'.number_format($totalFechaD,2,'.',',').' <br/>&nbsp;</strong></td>
                                    <td><strong><br/>'.number_format($totalFechaC,2,'.',',').' <br/>&nbsp;</strong></td>
                                 </tr>';
                            $totalFinalDebito  += $totalFechaD;
                            $totalFinalCredito += $totalFechaC;
                        }
                        echo '<tr>
                                <td colspan="2" align="center"><strong><br/>Totales<br/>&nbsp;</strong></td>
                                <td><strong><br/>'.number_format($totalFinalDebito,2,'.',',').' <br/>&nbsp;</strong></td>
                                <td><strong><br/>'.number_format($totalFinalCredito,2,'.',',').' <br/>&nbsp;</strong></td>
                            </tr>';
                        
                        echo '<tr>
                                <td colspan="4"><strong><i><br/>Saldo A Fecha Final $'.number_format($saldoNuevo,2,'.',',').'<br/>&nbsp;</i></strong></td>
                            </tr>';
                        if($saldoNuevo <0){
                            $num = $saldoNuevo*-1;
                            $saldoLetras = ' - '.numtoletras($num);
                        } else {
                            $saldoLetras = numtoletras($saldoNuevo);
                        }
                       echo '<tr>
                                <td colspan="4"><strong><i><br/>Saldo A Fecha Final En Letras '.ucwords(mb_strtolower($saldoLetras)).'<br/>&nbsp;</i></strong></td>
                            </tr>';
                        ?>
                    </tbody>
                </table>
            </body>
        </html>
        <?php
    break;
}

