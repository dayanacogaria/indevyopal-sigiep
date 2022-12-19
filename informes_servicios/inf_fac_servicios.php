<?php
#################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#06/09/2018 |Erica G. |Archivo Creado
################################################################################################################
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
$con = new ConexionPDO();
session_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$rowC     = $con->Listar("SELECT ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.nombre, 
                d.nombre 
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico 
LEFT JOIN   gf_ciudad c ON ter.ciudadidentificacion = c.id_unico 
LEFT JOIN   gf_departamento d ON c.departamento = d.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$rutalogoTer = $rowC[0][6];
$ciudad      = $rowC[0][7];
$departamento= $rowC[0][8];


#***********Consulta Detalles Factura************#

$fac= $con->Listar("SELECT f.id_unico, 
    f.numero_factura,  
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
    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
    f.fecha_factura, 
    DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
    p.direccion, s.codigo, s.nombre, 
    uv.codigo_ruta, 
    pr.nombre, pr.descripcion, 
    uv.codigo_interno, 
    m.referencia,  
    DATE_FORMAT(l.fecha,'%d/%m/%Y'), 
    u.nombre, 
    es.codigo, pr.id_unico, 
    uvms.id_unico, 
    l.valor , 
    DATE_FORMAT(pr.fecha_cierre, '%d/%m/%Y')
    FROM gp_factura f 
    LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
    LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico  
    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
    LEFT JOIN gp_predio1 p ON p.id_unico = uv.predio 
    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
    LEFT JOIN gp_periodo pr ON f.periodo = pr.id_unico 
    LEFT JOIN gp_lectura l ON l.periodo = pr.id_unico AND l.unidad_vivienda_medidor_servicio = uvms.id_unico 
    LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
    LEFT JOIN gp_uso u ON uv.uso = u.id_unico 
    LEFT JOIN gp_estrato es ON uv.estrato = es.id_unico 
    WHERE md5(f.id_unico) ='".$_GET['id']."'");
$id_factura     = $fac[0][0];
$numero         = $fac[0][1];
$tercero        = $fac[0][2].' - '.$fac[0][3];
$direccion_p    = $fac[0][6];
$sector         = $fac[0][7].' - '.$fac[0][8];
$codigo_ruta    = $fac[0][9];
$periodo        = ucwords(mb_strtolower($fac[0][11]));
$codigo_interno = $fac[0][12];
$medidor        = $fac[0][13];
$fecha_lectura  = $fac[0][14];
$uso            = $fac[0][15];
$estrato        = $fac[0][16];
$uvms           = $fac[0][18];
$lactual        = $fac[0][19];
$fecha_cierre   = $fac[0][20];
#********* Periodo Anterior ***********#
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$fac[0][17]."  
        ORDER BY pa.fecha_inicial DESC ");
$periodo_a = $rowp[0]['descripcion'];
$id_pa     = $rowp[0]['id_unico'];
#Deuda Anterior 
$da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) 
    FROM gp_detalle_factura df 
    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
    WHERE f.unidad_vivienda_servicio = $uvms AND f.periodo <= $id_pa");
$deuda_anterior =0;
if(count($da)>0){
    #*** Buscar Recaudo ***#
    $id_df      = $da[0][0];
    $valor_f    = $da[0][1];
    $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
        WHERE dp.detalle_factura IN ($id_df)");
    if(count(($rc))>0){
        $recaudo = 0;
    }elseif(empty ($rc[0][0])){
        $recaudo = 0;
    } else {
        $recaudo = $rc[0][0];
    }
    $deuda_anterior = $valor_f -$recaudo;
}
#******** Lectura Anterior ********#
$lab= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$la = $lab[0][0];
#******* Consumo ************#
$consumo = $lactual -$la;
#*********************** Consumos Anteriores ***************************#
#1
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$id_pa."  
        ORDER BY pa.fecha_inicial DESC ");
$id_pa     = $rowp[0]['id_unico'];
$laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$laan1 = $laa[0][0];
$valor1 = $la -$laan1;
#Valor
$vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
if(empty($vfac[0][0])) { $valorf1 =0;} else {$valorf1 = $vfac[0][0];}

#2
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$id_pa."  
        ORDER BY pa.fecha_inicial DESC ");
$id_pa     = $rowp[0]['id_unico'];
$laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$laan2 = $laa[0][0];
$valor2 = $laan1-$laan2;
#Valor
$vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
if(empty($vfac[0][0])) { $valorf2 =0;} else {$valorf2 = $vfac[0][0];}
#3
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$id_pa."  
        ORDER BY pa.fecha_inicial DESC ");
$id_pa     = $rowp[0]['id_unico'];
$laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$laan3 = $laa[0][0];
$valor3 = $laan2 - $laan3;
#Valor
$vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
if(empty($vfac[0][0])) { $valorf3 =0;} else {$valorf3 = $vfac[0][0];}
#4
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$id_pa."  
        ORDER BY pa.fecha_inicial DESC ");
$id_pa     = $rowp[0]['id_unico'];
$laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$laan4 = $laa[0][0];
$valor4 = $laan3 - $laan4;
#Valor
$vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
if(empty($vfac[0][0])) { $valorf4 =0;} else {$valorf4 = $vfac[0][0];}
#5
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$id_pa."  
        ORDER BY pa.fecha_inicial DESC ");
$id_pa     = $rowp[0]['id_unico'];
$laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$laan5 = $laa[0][0];
$valor5 = $laan4 - $laan4;
#Valor
$vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
if(empty($vfac[0][0])) { $valorf5 =0;} else {$valorf5 = $vfac[0][0];}
#6
$rowp = $con->Listar("SELECT DISTINCT pa.* 
        FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
        WHERE p.id_unico = ".$id_pa."  
        ORDER BY pa.fecha_inicial DESC ");
$id_pa     = $rowp[0]['id_unico'];
$laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
$laan6 = $laa[0][0];
$valor6 = $laan5 - $laan6;
#Valor
$vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
if(empty($vfac[0][0])) { $valorf6 =0;} else {$valorf6 = $vfac[0][0];}

#** Promedio **#
$promedio = (($valor1+$valor2+$valor3+$valor4+$valor5+$valor6+$consumo)/7);
$promedio = round($promedio,0);
#***********************************************************************#

#**********PDF*********#
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    ob_start();
    
    class PDF extends FPDF {
        function Header() {

            global $rutalogoTer;
            global $razonsocial;
            global $numeroIdent;
            global $ciudad;
            global $departamento;
            global $numero;
            if ($rutalogoTer != '') {
                $this->Image('../' . $rutalogoTer, 12, 6, 25);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(20);
            $this->MultiCell(150, 5, utf8_decode($razonsocial), 0, 'C');
            $this->SetXY(170,10);
            $this->Cell(30, 5,utf8_decode('FACTURA N°:'), 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(20);
            $this->Cell(150, 5,'NIT:'.$numeroIdent, 0, 0, 'C');
            $this->Cell(30, 5,$numero, 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(20);
            $this->MultiCell(150, 5,ucwords(mb_strtolower($ciudad.' - '.$departamento)),0, 'C');

            $this->Ln(8);
            
        }

        function Footer() {
        
        }

    }

    $pdf = new PDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFillColor(238,238,238); 
    #**¨Línea 1 **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(80,8,utf8_decode('Dirección'),1,0,'C', true);
    $pdf->Cell(55,8,utf8_decode('Barrio/Vereda'),1,0,'C',true);
    $pdf->Cell(55,8,utf8_decode('Código Ruta'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(80,5,utf8_decode($direccion_p),1,0,'L');
    $pdf->Cell(55,5,utf8_decode($sector),1,0,'L');
    $pdf->Cell(55,5,utf8_decode($codigo_ruta),1,0,'L');
    $pdf->Ln(5);
    #**¨Línea 2 **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(80,8,utf8_decode('Nombre'),1,0,'C',true);
    $pdf->Cell(40,8,utf8_decode('Atraso'),1,0,'C',true);
    $pdf->Cell(40,8,utf8_decode('Periodo Facturado'),1,0,'C',true);
    $pdf->Cell(30,8,utf8_decode('Código Interno'),1,0,'C',true);
    $pdf->Ln(8);    
    $pdf->SetFont('Arial','',10);
    $pdf->CellFitScale(80,5,utf8_decode($tercero),1,0,'L');
    $pdf->Cell(40,5,utf8_decode(''),1,0,'L');
    $pdf->Cell(40,5,utf8_decode($periodo),1,0,'L');
    $pdf->Cell(30,5,utf8_decode($codigo_interno),1,0,'L');
    $pdf->Ln(5);
    #**¨Línea 3 **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(80,8,utf8_decode('Problemas del Aforo'),1,0,'C',true);
    $pdf->Cell(40,8,utf8_decode('N° Medidor'),1,0,'C',true);
    $pdf->Cell(70,8,utf8_decode('Último Pago'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(80,5,utf8_decode(''),1,0,'L');
    $pdf->Cell(40,5,utf8_decode($medidor),1,0,'L');
    $pdf->Cell(70,5,utf8_decode(''),1,0,'L');
    $pdf->Ln(5);
    #**¨Línea 4 **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(80,8,utf8_decode('Dirección de entrega'),1,0,'C',true);
    $pdf->Cell(40,8,utf8_decode('Fecha Lectura'),1,0,'C',true);
    $pdf->Cell(70,8,utf8_decode('Otros Cobros'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(80,5,utf8_decode($direccion_p),1,0,'L');
    $pdf->Cell(40,5,utf8_decode($fecha_lectura),1,0,'L');
    #***************** Otros Cobros *************************#
    $yp = $pdf->GetY();
    $xp = $pdf->GetX();
    $pdf->Cell(50,5,utf8_decode('Deuda Anterior'),1,0,'L');
    $pdf->Cell(20,5,number_format($deuda_anterior, 2, '.', ','),1,0,'R');    
    $pdf->Ln(5);
    #$pdf->SetX($xp+120);
    #*** Buscar Otros Cobros ***#
    $otr = $con->Listar("SELECT df.id_unico, c.nombre,df.valor_total_ajustado FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico WHERE tc.id_unico IN (5,6) AND df.factura=$id_factura");
    $total_otros =0;
    for ($z=0; $z < count($otr); $z++) { 
        $pdf->SetX($xp);
        $pdf->Cell(50,5,utf8_decode($otr[$z][1]),1,0,'L');
        $pdf->Cell(20,5,number_format($otr[$z][2], 2, '.', ','),1,0,'R');   
        $pdf->Ln(5);
        $total_otros +=$otr[$z][2];
        
    }
    $yf1 = $pdf->GetY();
    #**¨Línea 5 **#
    $pdf->SetY($yp+5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(20,8,utf8_decode('Uso'),1,0,'C',true);
    $pdf->Cell(60,8,utf8_decode('Consumos anteriores'),1,0,'C',true);
    $pdf->Cell(20,8,utf8_decode('Lect. Ant'),1,0,'C',true);
    $pdf->Cell(20,8,utf8_decode('Lect. Act'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell(20,10,utf8_decode($uso),1,0,'C');
     #*********************** Consumos Anteriores ***************************#
    $pdf->SetFont('Arial','B',8);
   /* $pdf->Image('barras3.jpg', 40,$y+2, 35);
    $pdf->Cell(60,3,utf8_decode(''),0,0,'L');*/
    $pdf->Cell(10,3,utf8_decode(6),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(5),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(4),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(3),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(2),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(1),1,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,10,utf8_decode($la),1,0,'C');
    $pdf->Cell(20,10,utf8_decode($lactual),1,0,'C');
    $pdf->SetXY($x+20, $y+3);
    $pdf->Cell(60,7,utf8_decode(''),0,0,'C');
    /*$pdf->Cell(10,7,utf8_decode($valor6),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor5),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor4),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor3),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor2),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor1),1,0,'C');*/
    #***********************************************************************#
    $pdf->Ln(7);
    #**¨Línea 6**#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(20,8,utf8_decode('Estrato'),1,0,'C',true);
    $pdf->Cell(60,8,utf8_decode(''),0,0,'C');
    #$pdf->Cell(60,8,utf8_decode('Valor Facturas anteriores'),1,0,'C',true);
    $pdf->Cell(20,8,utf8_decode('Consumo'),1,0,'C',true);
    $pdf->Cell(20,8,utf8_decode('Promedio'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell(20,10,utf8_decode($estrato),1,0,'C');
    #*********************** Consumos Anteriores ***************************#
    

    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(60,3,utf8_decode(''),0,0,'L');
    /*$pdf->Cell(10,3,utf8_decode(6),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(5),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(4),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(3),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(2),1,0,'L');
    $pdf->Cell(10,3,utf8_decode(1),1,0,'L');*/
    $pdf->SetFont('Arial','',10);
    #***********************************************************************#
    $pdf->Cell(20,10,utf8_decode($consumo),1,0,'C');
    $pdf->Cell(20,10,utf8_decode($promedio),1,0,'C');
    $pdf->SetXY($x+20, $y+3);
    $pdf->CellFitScale(60,7,utf8_decode(''),0,0,'C');
    /*$pdf->CellFitScale(10,7,utf8_decode($valorf6),1,0,'C');
    $pdf->CellFitScale(10,7,utf8_decode($valorf5),1,0,'C');
    $pdf->CellFitScale(10,7,utf8_decode($valorf4),1,0,'C');
    $pdf->CellFitScale(10,7,utf8_decode($valorf3),1,0,'C');
    $pdf->CellFitScale(10,7,utf8_decode($valorf2),1,0,'C');
    $pdf->CellFitScale(10,7,utf8_decode($valorf1),1,0,'C');*/
    $pdf->Ln(7);
    #**¨Línea 7**#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(120,8,utf8_decode('Subsidios Y Contribuciones'),1,0,'C',true);
    $pdf->Ln(8);
    #********* Buscar Subsidios ************#
    $sb = $con->Listar("SELECT df.id_unico,IF(df.valor_total_ajustado<0,df.valor_total_ajustado*-1, df.valor_total_ajustado), c.nombre, tc.nombre, c.tipo_concepto, c.tipo_operacion FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico WHERE c.tipo_operacion = 3 AND tc.id_unico IN (2,3,4) AND df.factura =$id_factura");
    #** COncepto **#
    $pdf->Cell(20,5,utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(25,5,utf8_decode($sb[0][3]),1,0,'C');    
    $pdf->Cell(25,5,utf8_decode($sb[1][3]),1,0,'C');    
    $pdf->Cell(25,5,utf8_decode($sb[2][3]),1,0,'C');    
    $pdf->Cell(25,5,utf8_decode('Total'),1,0,'C');
    $pdf->Ln(5);
    #** Subsidios ***#
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(20,5,utf8_decode('Subsidios'),1,0,'C');
    $pdf->SetFont('Arial','',10);
    if(!empty($sb[0][0])) { $vlr1 =$sb[0][1]; } else { $vlr1 = 0; }
    if(!empty($sb[1][0])) { $vlr2 =$sb[1][1]; } else { $vlr2 = 0; }
    if(!empty($sb[2][0])) { $vlr3 =$sb[2][1]; } else { $vlr3 = 0; }
    $pdf->CellFitScale(25,5,number_format($vlr1, 2, ',', '.'),1,0,'R');
    $pdf->CellFitScale(25,5,number_format($vlr2, 2, ',', '.'),1,0,'R');
    $pdf->CellFitScale(25,5,number_format($vlr3, 2, ',', '.'),1,0,'R');
    $totalsubsidios = $vlr1 + $vlr2 + $vlr3;
    $pdf->Cell(25,5,number_format($totalsubsidios, 2, ',', '.'),1,0,'R');
    $pdf->Ln(5);
    
    #** Contribuciones **#
    $cb = $con->Listar("SELECT df.id_unico,IF(df.valor_total_ajustado<0,df.valor_total_ajustado*-1, df.valor_total_ajustado), c.nombre, tc.nombre, c.tipo_concepto, c.tipo_operacion FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico WHERE c.tipo_operacion = 2 
        AND tc.id_unico IN (2,3,4) AND df.factura =$id_factura");
    $pdf->SetFont('Arial','B',9);
    $pdf->CellFitScale(20,5,utf8_decode('Contribuciones'),1,0,'C');
    $pdf->SetFont('Arial','',10);
    if(!empty($cb[0][0])) { $vlr1 =$cb[0][1]; } else { $vlr1 = 0; }
    if(!empty($cb[1][0])) { $vlr2 =$cb[1][1]; } else { $vlr2 = 0; }
    if(!empty($cb[2][0])) { $vlr3 =$cb[2][1]; } else { $vlr3 = 0; }
    $pdf->CellFitScale(25,5,number_format($vlr1, 2, ',', '.'),1,0,'R');
    $pdf->CellFitScale(25,5,number_format($vlr2, 2, ',', '.'),1,0,'R');
    $pdf->CellFitScale(25,5,number_format($vlr3, 2, ',', '.'),1,0,'R');
    $totalcontribuciones = $vlr1 + $vlr2 + $vlr3;
    $pdf->Cell(25,5,number_format($totalcontribuciones, 2, ',', '.'),1,0,'R');
    $pdf->Ln(5);
    $yf2 = $pdf->GetY();
    $alt = $yf2-$yf1;
    $pdf->SetXY($xp,$yf1);
    $pdf->Cell(70,$alt,utf8_decode(''),1,0,'C');
    $pdf->Ln($alt);
    #** Línea 8 **#
    $pdf->SetFont('Arial','B',10);
    $pdf->CellFitScale(15,8,utf8_decode('Consumo'),1,0,'C',true);
    $pdf->CellFitScale(35,8,utf8_decode('Acueducto'),1,0,'C',true);
    $pdf->CellFitScale(35,8,utf8_decode('Aseo'),1,0,'C',true);
    $pdf->CellFitScale(35,8,utf8_decode('Alcantarillado'),1,0,'C',true);
    $pdf->CellFitScale(70,8,utf8_decode('Resumen Liquidación'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(15,25,utf8_decode(''),0,0,'C');
    #*************      Liquidación     ******************#
    $xlac = $pdf->GetX();
    $yprin = $pdf->GetY();
    $total_acueducto = 0;
    $total_aseo = 0;
    $total_alcantarillado= 0;
    #Acueducot 
    $pdf->SetFont('Arial','',8);
    $da = $con->Listar("SELECT df.id_unico, c.nombre,IF(df.valor_total_ajustado<0,df.valor_total_ajustado*-1, df.valor_total_ajustado)FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
        LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico 
        WHERE tc.id_unico IN (2) AND c.tipo_operacion = 1 AND df.factura=$id_factura");
    for ($d=0; $d <count($da) ; $d++) { 
        $pdf->SetX($xlac);
        $pdf->CellFitScale(20,5,utf8_decode($da[$d][1]),0,0,'L');
        $pdf->CellFitScale(15,5,number_format($da[$d][2],2,'.',','),0,0,'R');
        $pdf->ln(5);
        $total_acueducto += $da[$d][2];
    }
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX($xlac);
    $pdf->CellFitScale(20,5,utf8_decode('Total'),0,0,'L');
    $pdf->CellFitScale(15,5,number_format($total_acueducto,2,'.',','),0,0,'R');
    $pdf->ln(5);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetXY($xlac+35,$yprin);
    $xlas = $pdf->GetX();
    $pdf->SetFont('Arial','',8);
    $da = $con->Listar("SELECT df.id_unico, c.nombre,IF(df.valor_total_ajustado<0,df.valor_total_ajustado*-1, df.valor_total_ajustado)FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico WHERE tc.id_unico IN (3) AND c.tipo_operacion = 1 AND df.factura=$id_factura");
    for ($d=0; $d <count($da) ; $d++) { 
        $pdf->SetX($xlas);
        $pdf->CellFitScale(20,5,utf8_decode($da[$d][1]),0,0,'L');
        $pdf->CellFitScale(15,5,number_format($da[$d][2],2,'.',','),0,0,'R');
        $pdf->ln(5);
        $total_aseo +=$da[$d][2];
    }
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX($xlas);
    $pdf->CellFitScale(20,5,utf8_decode('Total'),0,0,'L');
    $pdf->CellFitScale(15,5,number_format($total_aseo,2,'.',','),0,0,'R');
    $pdf->ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($xlas+35, $yprin);
    $xlalc = $pdf->GetX();
    $da = $con->Listar("SELECT df.id_unico, c.nombre,IF(df.valor_total_ajustado<0,df.valor_total_ajustado*-1, df.valor_total_ajustado)FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico WHERE tc.id_unico IN (4) AND c.tipo_operacion = 1 AND df.factura=$id_factura");
    for ($d=0; $d <count($da) ; $d++) { 
        $pdf->SetX($xlalc);
        $pdf->CellFitScale(20,5,utf8_decode($da[$d][1]),0,0,'L');
        $pdf->CellFitScale(15,5,number_format($da[$d][2],2,'.',','),0,0,'R');
        $pdf->ln(5);
        $total_alcantarillado +=$da[$d][2];
    }
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX($xlalc);
    $pdf->CellFitScale(20,5,utf8_decode('Total'),0,0,'L');
    $pdf->CellFitScale(15,5,number_format($total_alcantarillado,2,'.',','),0,0,'R');
    $pdf->ln(5);
    $pdf->SetXY($xlalc+35, $yprin);
    #*************Resumen Liquidación *****************++#
    $xrl = $pdf->GetX();
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(40,5,utf8_decode('Acueducto'),0,0,'R');
    $pdf->Cell(30,5,number_format($total_acueducto,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $pdf->Cell(40,5,utf8_decode('Aseo'),0,0,'R');
    $pdf->Cell(30,5,number_format($total_aseo,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $pdf->Cell(40,5,utf8_decode('Alcantarillado'),0,0,'R');
    $pdf->Cell(30,5,number_format($total_alcantarillado,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $pdf->Cell(40,5,utf8_decode('Otros'),0,0,'R');
    $pdf->Cell(30,5,number_format($total_otros,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $pdf->Cell(40,5,utf8_decode('Subsidios(-)'),0,0,'R');
    $pdf->Cell(30,5,number_format($totalsubsidios,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $pdf->Cell(40,5,utf8_decode('Contribuciones(+)'),0,0,'R');
    $pdf->Cell(30,5,number_format($totalcontribuciones,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $pdf->Cell(40,5,utf8_decode('Deuda Anterior'),0,0,'R');
    $pdf->Cell(30,5,number_format($deuda_anterior,2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX($xrl);
    $total_pagar = $total_otros - $totalsubsidios + $totalcontribuciones + $deuda_anterior +$total_acueducto+$total_aseo+$total_alcantarillado;
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(40,5,utf8_decode('Total A Pagar'),0,0,'R');
    $pdf->Cell(30,5,number_format($total_pagar,2,'.',','),0,0,'R');
    $pdf->Ln(25);
    $yfin = $pdf->GetY();
    $altr = $yfin-$yprin;
    $pdf->SetXY($xlac-15,$yprin);
    $pdf->Cell(15,$altr,$consumo,1,0,'C');
    $pdf->Cell(35,$altr,'',1,0,'C');
    $pdf->Cell(35,$altr,'',1,0,'C');
    $pdf->Cell(35,$altr,'',1,0,'C');
    $pdf->Cell(70,$altr,'',1,0,'C');
    $pdf->Ln($altr);
    #**¨Línea 9**#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(75,8,utf8_decode('OBSERVACIÓN'),1,0,'C',true);
    $pdf->Cell(25,8,utf8_decode('HASTA EL'),1,0,'C',true);
    $pdf->Cell(55,8,utf8_decode('PAGUE EN'),1,0,'C',true);
    $pdf->Cell(35,8,utf8_decode('VALOR A PAGAR'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',8);
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->MultiCell(75,4,utf8_decode('Esta factura se asimila en todos sus efectos legales a una letra de cambio de acuerdo al Art. 774 C.C.Pague oportunamente y evite la SUSPENSION DEL SERVICIO'),1,'J');
    $pdf->SetXY($x+75,$y);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(25,12,utf8_decode($fecha_cierre),1,0,'C');
    $pdf->Cell(55,12,utf8_decode('OFICINAS EFECTY'),1,0,'C');
    $pdf->Cell(35,12,number_format($total_pagar,2,'.',','),1,0,'C');
    $pdf->Ln(11);
    $yi = $pdf->GetY();    
    if($deuda_anterior==0){
            $pdf->SetFont('Arial','I',12);
            $pdf->Image('icon.png', 12,$yi+5, 25);
            $pdf->SetXY(50, $yi+8);
            $pdf->Cell(100,12,utf8_decode('Felicitaciones, Usted se encuentra al día con su facturación'),0,0,'L');

    } else {
        $pdf->SetFont('Arial','I',12);
            $pdf->Image('icon2.png', 12,$yi+5, 25);
            $pdf->SetXY(50, $yi+8);
            $pdf->Cell(100,12,utf8_decode('Usted tiene facturas pendientes'),0,0,'L');
    }
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Factura(' . date('d/m/Y') . ').pdf', 0);