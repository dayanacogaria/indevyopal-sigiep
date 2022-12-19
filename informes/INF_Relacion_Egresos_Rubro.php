<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#26/01/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
#   ************    Datos Recibe    ************    #
$fechaIn    = $_POST['fechaI'];
$fechaFi    = $_POST['fechaF'];
$fechaI     = fechaC($fechaIn);
$fechaF     = fechaC($fechaFi);
$bancoI     = $_POST['bancoI'];
$bancoF     = $_POST['bancoF'];
$rubroI     = $_POST['rubroI'];
$rubroF     = $_POST['rubroF'];   
$exportar   = $_REQUEST['exp'];        

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
#********************************************************#
# **** Consulta Bancos ***#
$banco = $con->Listar("SELECT DISTINCT  dc.cuenta, c.codi_cuenta, c.nombre 
    FROM 
        gf_detalle_comprobante dc 
    LEFT JOIN 
        gf_comprobante_cnt cnt ON dc.comprobante = cnt.id_unico 
    LEFT JOIN 
        gf_cuenta c ON dc.cuenta = c.id_unico 
    WHERE 
        cnt.tipocomprobante = 5 
        AND c.clasecuenta = 11 
        AND c.parametrizacionanno = $anno 
        AND c.codi_cuenta BETWEEN '$bancoI' AND '$bancoF' 
        AND cnt.fecha BETWEEN '$fechaI' AND '$fechaF' 
    ORDER BY c.codi_cuenta ASC");

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
                $numpaginas=$numpaginas+1;

                $this->SetFont('Arial','B',10);

                if($ruta_logo != '')
                {
                  $this->Image('../'.$ruta_logo,25,8,28);
                }
                $this->SetFont('Arial','B',10);	
                $this->MultiCell(330,5,utf8_decode($razonsocial),0,'C');		
                $this->SetX(10);
                $this->Ln(1);
                $this->Cell(330,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode('Tel: '.$telefonoTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode('INFORME RELACIÓN DE EGRESOS CON RUBRO'),0,0,'C');
                $this->Ln(5);
            }      

            function Footer(){
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }
        $pdf = new PDF('L','mm','Legal');   
        $nb=$pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->Ln(5);
        for ($i = 0; $i < count($banco); $i++) {
            $a = $pdf->GetY();
            if($a>170){
                $pdf->AddPage();
                $pdf->Ln(10);
            }
            $cuenta = $banco[$i][0];
            # ** Busco Movimientos Y Rubros Con Ese Banco ** #
            $com = $con->Listar("SELECT DISTINCT rb.id_unico, 
                        rb.codi_presupuesto, rb.nombre 
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                        LEFT JOIN gf_comprobante_pptal cp ON tc.comprobante_pptal = cp.tipocomprobante AND cn.numero = cp.numero 
                        LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                        WHERE dc.cuenta IN ($cuenta)
                        AND tc.clasecontable = 14 
                        AND rb.codi_presupuesto BETWEEN '$rubroI' AND '$rubroF' 
                        AND cn.parametrizacionanno = $anno 
                        AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                        ORDER BY rb.codi_presupuesto ASC;");
            if(count($com)>0){ 
                #Imprimir Banco
                $pdf->Setx(10);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(330,7, utf8_decode('BANCO: '.mb_strtoupper($banco[$i][1].' - '.$banco[$i][2])),1,0,'L');
                $pdf->Ln(7);
                $totalBanco =0;
                for ($j = 0; $j < count($com); $j++) {
                    $a = $pdf->GetY();
                    if($a>170){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $rubro = $com[$j][0];
                    #Imprimir Rubro
                    $pdf->Setx(10);
                    $pdf->SetFont('Arial','I',10);
                    $pdf->Cell(330,7, utf8_decode('Rubro: '.mb_strtoupper($com[$j][1].' - '.$com[$j][2])),1,0,'L');
                    $pdf->Ln(7);
                    $totalRubro =0;
                    # ** Buscar Egresos Que Usen El Rubro Fuente **#
                    $egr = $con->Listar("SELECT DISTINCT 
                        DATE_FORMAT(cp.fecha, '%d/%m/%Y'),
                        tcp.codigo, tcp.nombre, cp.numero, 
                        tca.codigo, tca.nombre, cpa.numero, 
                        if(dc.valor <0, dc.valor*-1, dc.valor) 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                    LEFT JOIN gf_comprobante_pptal cp ON tc.comprobante_pptal = cp.tipocomprobante AND cn.numero = cp.numero 
                    LEFT JOIN gf_tipo_comprobante_pptal tcp ON tcp.id_unico = cp.tipocomprobante 
                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
                    LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                    LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                    LEFT JOIN gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico 
                    LEFT JOIN gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico 
                    LEFT JOIN gf_tipo_comprobante_pptal tca ON cpa.tipocomprobante = tca.id_unico 
                    WHERE dc.cuenta IN ($cuenta)
                    AND tc.clasecontable = 14 
                    AND rb.id_unico =$rubro 
                    AND cn.parametrizacionanno = $anno 
                    AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                    ORDER BY cp.numero, cp.fecha  ASC");
                    $a = $pdf->GetY();
                    if($a>170){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Setx(10);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(35,7, utf8_decode(''),1,0,'C');
                    $pdf->Cell(85,7,utf8_decode(''),1,0,'C');
                    $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
                    $pdf->Cell(85,7,utf8_decode(''),1,0,'C');
                    $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
                    $pdf->Cell(45,7,utf8_decode(''),1,0,'C');
                    $pdf->Setx(10);
                    $pdf->Cell(35,7, utf8_decode('FECHA'),0,0,'C');
                    $pdf->Cell(85,7,utf8_decode('TIPO EGRESO'),0,0,'C');
                    $pdf->Cell(40,7,utf8_decode('NÚMERO'),0,0,'C');
                    $pdf->Cell(85,7,utf8_decode('TIPO OBLIGACIÓN'),0,0,'C');
                    $pdf->Cell(40,7,utf8_decode('NÚMERO'),0,0,'C');
                    $pdf->Cell(45,7,utf8_decode('VALOR GIRO'),0,0,'C');
                    $pdf->Ln(7);
                    for ($h = 0; $h < count($egr); $h++) {
                        $a = $pdf->GetY();
                        if($a>170){
                            $pdf->AddPage();
                            $pdf->Ln(10);
                            $pdf->Setx(10);
                            $pdf->SetFont('Arial','B',10);
                            $pdf->Cell(35,7, utf8_decode(''),1,0,'C');
                            $pdf->Cell(85,7,utf8_decode(''),1,0,'C');
                            $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
                            $pdf->Cell(85,7,utf8_decode(''),1,0,'C');
                            $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
                            $pdf->Cell(45,7,utf8_decode(''),1,0,'C');
                            $pdf->Setx(10);
                            $pdf->Cell(35,7, utf8_decode('FECHA'),0,0,'C');
                            $pdf->Cell(85,7,utf8_decode('TIPO EGRESO'),0,0,'C');
                            $pdf->Cell(40,7,utf8_decode('NÚMERO'),0,0,'C');
                            $pdf->Cell(85,7,utf8_decode('TIPO OBLIGACIÓN'),0,0,'C');
                            $pdf->Cell(40,7,utf8_decode('NÚMERO'),0,0,'C');
                            $pdf->Cell(45,7,utf8_decode('VALOR GIRO'),0,0,'C');
                            $pdf->Ln(7);
                        }
                        $pdf->SetFont('Arial','',10);
                        $pdf->Setx(10);
                        $pdf->Cell(35,5,utf8_decode($egr[$h][0]),1,0,'L');
                        $pdf->Cell(85,5,utf8_decode($egr[$h][1].' - '.$egr[$h][2]),1,0,'L');
                        $pdf->Cell(40,5,utf8_decode($egr[$h][3]),1,0,'L');
                        $pdf->Cell(85,5,utf8_decode($egr[$h][4].' - '.$egr[$h][5]),1,0,'L');
                        $pdf->Cell(40,5,utf8_decode($egr[$h][6]),1,0,'L');
                        $pdf->Cell(45,5, number_format($egr[$h][7],2,'.',','),1,0,'R');
                        $pdf->Ln(5);
                        $totalRubro +=$egr[$h][7];
                    }
                    $pdf->Setx(10);
                    $pdf->SetFont('Arial','I',10);
                    $pdf->Cell(285,7, utf8_decode('TOTAL RUBRO: '.mb_strtoupper($com[$j][1].' - '.$com[$j][2])),1,0,'R');
                    $pdf->Cell(45,7, number_format($totalRubro,2,'.',','),1,0,'R');
                    $pdf->Ln(7);
                    $totalBanco +=$totalRubro;
                }   
                $pdf->Setx(10);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(285,7, utf8_decode('TOTAL BANCO: '.mb_strtoupper($banco[$i][1].' - '.$banco[$i][2])),1,0,'R');
                $pdf->Cell(45,7, number_format($totalBanco,2,'.',','),1,0,'R');
                $pdf->Ln(7);
            }
        }
        ob_end_clean();		
        $pdf->Output(0,'Informe_Relacion_Egresos_Rubro.pdf',0);
    break;
    # *** Generar xls **#
    case 2:
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Informe_Relacion_Egresos_Rubro.xls"); ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Relación Egresos Rubro</title>
            </head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <th colspan="6" align="center"><strong>
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                        <br/>&nbsp;
                        <br/>RELACIÓN DE EGRESOS CON RUBRO 
                        <br/>&nbsp;</strong>
                    </th>
                    <tr>
                        <td><center><strong>FECHA</strong></center></td>
                        <td><center><strong>TIPO EGRESO</strong></center></td>
                        <td><center><strong>NÚMERO</strong></center></td>
                        <td><center><strong>TIPO OBLIGACIÓN</strong></center></td>
                        <td><center><strong>NÚMERO</strong></center></td>
                        <td><center><strong>VALOR GIRO</strong></center></td>

                    </tr>
                    <tbody>
                        <?php 
                        for ($i = 0; $i < count($banco); $i++) {
                            $cuenta = $banco[$i][0];
                            # ** Busco Movimientos Y Rubros Con Ese Banco ** #
                            $com = $con->Listar("SELECT DISTINCT rb.id_unico, 
                                        rb.codi_presupuesto, rb.nombre 
                                        FROM gf_detalle_comprobante dc 
                                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                        LEFT JOIN gf_comprobante_pptal cp ON tc.comprobante_pptal = cp.tipocomprobante AND cn.numero = cp.numero 
                                        LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
                                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                                        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                                        WHERE dc.cuenta IN ($cuenta)
                                        AND tc.clasecontable = 14 
                                        AND rb.codi_presupuesto BETWEEN '$rubroI' AND '$rubroF' 
                                        AND cn.parametrizacionanno = $anno 
                                        AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                        ORDER BY rb.codi_presupuesto ASC;");
                            if(count($com)>0){ 
                                #Imprimir Banco
                                echo '<tr>';
                                echo '<td colspan ="6"><strong>BANCO: '.mb_strtoupper($banco[$i][1].' - '.$banco[$i][2]).'</strong></td>';
                                echo '</tr>';
                                $totalBanco =0;
                                for ($j = 0; $j < count($com); $j++) {
                                    $rubro = $com[$j][0];
                                    #Imprimir Rubro
                                    echo '<tr>';
                                    echo '<td colspan ="6"><strong><i>Rubro: '.mb_strtoupper($com[$j][1].' - '.$com[$j][2]).'</i></strong></td>';
                                    echo '</tr>';
                                    $totalRubro =0;
                                    # ** Buscar Egresos Que Usen El Rubro Fuente **#
                                    $egr = $con->Listar("SELECT DISTINCT 
                                        DATE_FORMAT(cp.fecha, '%d/%m/%Y'),
                                        tcp.codigo, tcp.nombre, cp.numero, 
                                        tca.codigo, tca.nombre, cpa.numero, 
                                        if(dc.valor <0, dc.valor*-1, dc.valor) 
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                    LEFT JOIN gf_comprobante_pptal cp ON tc.comprobante_pptal = cp.tipocomprobante AND cn.numero = cp.numero 
                                    LEFT JOIN gf_tipo_comprobante_pptal tcp ON tcp.id_unico = cp.tipocomprobante 
                                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
                                    LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                                    LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                                    LEFT JOIN gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico 
                                    LEFT JOIN gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico 
                                    LEFT JOIN gf_tipo_comprobante_pptal tca ON cpa.tipocomprobante = tca.id_unico 
                                    WHERE dc.cuenta IN ($cuenta)
                                    AND tc.clasecontable = 14 
                                    AND rb.id_unico =$rubro 
                                    AND cn.parametrizacionanno = $anno 
                                    AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                    ORDER BY cp.numero, cp.fecha  ASC");
                                    
                                    echo '<tr>';
                                    echo '<td><strong>FECHA</strong></td>';
                                    echo '<td><strong>TIPO EGRESO</strong></td>';
                                    echo '<td><strong>NÚMERO</strong></td>';
                                    echo '<td><strong>TIPO OBLIGACIÓN</strong></td>';
                                    echo '<td><strong>NÚMERO</strong></td>';
                                    echo '<td><strong>VALOR GIRO</strong></td>';
                                    echo '</tr>';
                                    for ($h = 0; $h < count($egr); $h++) {
                                        echo '<tr>';
                                        echo '<td>'.$egr[$h][0].'</td>';
                                        echo '<td>'.$egr[$h][1].' - '.$egr[$h][2].'</td>';
                                        echo '<td>'.$egr[$h][3].'</td>';
                                        echo '<td>'.$egr[$h][4].' - '.$egr[$h][5].'</td>';
                                        echo '<td>'.$egr[$h][6].'</td>';
                                        echo '<td>'.number_format($egr[$h][7],2,'.',',').'</td>';
                                        echo '</tr>';
                                        $totalRubro +=$egr[$h][7];
                                    }
                                    echo '<tr>';
                                    echo '<td colspan ="5" align="right"><strong><i>TOTAL RUBRO: '.mb_strtoupper($com[$j][1].' - '.$com[$j][2]).'</i></strong></td>';
                                    echo '<td><strong><i>'.number_format($totalRubro,2,'.',',').'</i></strong></td>';
                                    echo '</tr>';
                                    $totalBanco +=$totalRubro;
                                }   
                                echo '<tr>';
                                echo '<td colspan ="5" align="right"><strong>TOTAL BANCO: '.mb_strtoupper($banco[$i][1].' - '.$banco[$i][2]).'</strong></td>';
                                echo '<td><strong>'.number_format($totalBanco,2,'.',',').'</strong></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </body>
        </html>
        <?php
    break;
}
        
        
  
    
?>
