<?php
#################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#14/02/2019 |Erica G. |Cambio formato
#18/01/2019 |Erica G. |Añadir Fecha
#03/10/2018 |Erica G. |Archivo Creado
################################################################################################################
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require '../code128.php';
//require'../barcode.php';
$con = new ConexionPDO();
session_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];

#************Datos Compañia************#
$rowCod = $con->Listar("SELECT codigobarras FROM gs_codigo_barras WHERE codigobarras ='7709998589964'");

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
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico 
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

$periodo = $_REQUEST['periodo'];


$ddatosP = $con->Listar("SELECT MONTH(fecha_cierre), imagen from gp_periodo WHERE id_unico = $periodo");
$mesp = $ddatosP[0][0];

#**********PDF*********#
    header("Content-Type: text/html;charset=utf-8");
   // require'../fpdf/fpdf.php';
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
                $this->Image('../' . $rutalogoTer, 10, -7, 2);
            }
            
        }

        function Footer() {
            global $mesp;
            $this->SetFont('Arial','B',8);
            if($mesp=='12' || $mesp=='01'){
                $this->SetY(-20);
                $this->Image('ar.jpg', 180, 235, 23);
                $this->SetX(50);
                $this->SetFont('Arial','I',8);
                $this->SetTextColor(174,33,2);
                $this->MultiCell(130,5,utf8_decode('LA JUNTA ADMINISTRADORA LOCAL LES DESEA A TODOS SUS USUARIOS UNA FELIZ NAVIDAD Y UN PROSPERO AÑO 2019!!!'),0,'R');
            }
         //   $this->SetY(-10);
         //   $this->SetFont('Arial','B',8);
         //   $this->SetTextColor(0,0,0);
          //  $this->Cell(190,5,utf8_decode('NOTA: El costo de la reconexión por suspensión es de $25.000'),0,0,'R');
        }

    }

   	$pdf=new PDF_Code128('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->AliasNbPages();
#***********Consulta Detalles Factura************#

if(!empty($_GET['factura'])){
    $id_factura = $_REQUEST['factura'];    
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
        pr.nombre, pr.nombre, 
        uv.codigo_interno, 
        m.referencia,  
        DATE_FORMAT(l.fecha,'%d/%m/%Y'), 
        u.nombre, 
        es.codigo, pr.id_unico, 
        uvms.id_unico, 
        l.valor , 
        DATE_FORMAT(pr.fecha_cierre, '%d/%m/%Y'), 
        m.estado_medidor, 
        uvs.id_unico , 
        DATE_FORMAT(pr.primera_fecha,'%d/%m/%Y'), 
        DATE_FORMAT(pr.segunda_fecha,'%d/%m/%Y'), 
        es.id_unico, u.id_unico, 
        uv.id_unico 
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
        WHERE f.id_unico = $id_factura  
        ORDER BY cast(s.codigo as unsigned), cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");
} else {
    $sectorI = $_REQUEST['sector1'];
    $sectorF = $_REQUEST['sector2'];    
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
        pr.nombre, pr.nombre, 
        uv.codigo_interno, 
        m.referencia,  
        DATE_FORMAT(l.fecha,'%d/%m/%Y'), 
        u.nombre, 
        es.codigo, pr.id_unico, 
        uvms.id_unico, 
        l.valor , 
        DATE_FORMAT(pr.fecha_cierre, '%d/%m/%Y'), 
        m.estado_medidor, 
        uvs.id_unico, 
        DATE_FORMAT(pr.primera_fecha,'%d/%m/%Y'), 
        DATE_FORMAT(pr.segunda_fecha,'%d/%m/%Y'), 
        es.id_unico, u.id_unico , 
        uv.id_unico 
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
        WHERE f.periodo = $periodo AND s.id_unico BETWEEN '$sectorI' AND '$sectorF' 
        ORDER BY cast(s.codigo as unsigned),cast((replace(uv.codigo_ruta, '.',''))  as unsigned) ASC");
}

for ($f = 0; $f < count($fac); $f++) {
    $id_factura     = $fac[$f][0];
    $numero         = $fac[$f][1];
    $tercero        = $fac[$f][2];
    $identificacion = $fac[$f][3];
    $direccion_p    = $fac[$f][6];
    $sector         = $fac[$f][7].' - '.$fac[$f][8];
    $codigo_ruta    = $fac[$f][9];
    $periodo_n      = ucwords(mb_strtolower($fac[$f][11]));
    $codigo_interno = $fac[$f][12];
    $medidor        = $fac[$f][13];
    $fecha_lectura  = $fac[$f][14];
    $uso            = $fac[$f][15];
    $estrato        = $fac[$f][16];
    $uvms           = $fac[$f][18];
    $lactual        = $fac[$f][19];
    $fecha_cierre   = $fac[$f][20];
    $fecha_factura  = $fac[$f][4];
    $estado_medidor = $fac[$f][21];
    $uvs            = $fac[$f][22];
    $fecha_p        = $fac[$f][23];
    $fecha_s        = $fac[$f][24];
    $id_estrato     = $fac[$f][25];
    $id_uso         = $fac[$f][26];
    $id_uv          = $fac[$f][27];

    #********* Periodo Anterior ***********#
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$fac[$f][17]."  
            ORDER BY pa.fecha_inicial DESC ");
    $periodo_a = $rowp[0]['descripcion'];
    $id_pa     = $rowp[0]['id_unico'];
    #********* Buscar Unidad¿_v con otros medidores ********#
    $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
            WHERE unidad_vivienda_servicio = $uvs");
    $ids_uv = $ids_uv[0][0];
    #Deuda Anterior     
    $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico) 
        FROM gp_detalle_factura df 
        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
        WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $id_pa");
    //var_dump($id_pa);
    $deuda_anterior =0;
    if(count($da)>0){
        #*** Buscar Recaudo ***#
        $id_df      = $da[0][0];
        $dav = $con->Listar("SELECT SUM(df.valor_total_ajustado) 
        FROM gp_detalle_factura df 
        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
        WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $id_pa");
        $valor_f    = $dav[0][0];
        $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
            WHERE p.fecha_pago <='$fecha_factura' AND dp.detalle_factura IN ($id_df)");
        if(count(($rc))>0 && !empty($rc[0][0])){
            $recaudo = $rc[0][0];
        }else {
            $recaudo = 0;
        }
        $deuda_anterior = $valor_f -$recaudo;
    }
    #** Atraso **#  
    $datr = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), df.factura 
        FROM gp_detalle_factura df 
        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
        WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $id_pa 
        GROUP BY df.factura ");
    $atraso = 0; 
    if(count($datr)>0){
        for ($at= 0; $at < count($datr); $at++) {
            #*** Buscar Recaudo ***#
            $id_df      = $datr[$at][0];
            $dav = $con->Listar("SELECT SUM(df.valor_total_ajustado) 
            FROM gp_detalle_factura df 
            LEFT JOIN gp_factura f ON f.id_unico = df.factura 
            WHERE df.id_unico IN ($id_df)");
            $valor_f    = $dav[0][0];
            $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                WHERE p.fecha_pago <'$fecha_factura' AND dp.detalle_factura IN ($id_df)");
            if(count(($rc))>0 && !empty($rc[0][0])){
                $recaudo = $rc[0][0];
            }else {
                $recaudo = 0;
            }
            if(($valor_f -$recaudo)>0){
                $atraso +=1;
            }
        }
        
    }
    #******** Lectura Anterior ********#
    $lab = $con->Listar("SELECT valor, cantidad_facturada FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    if(count($lab)>0){
       $la = $lab[0][0];
    } else {
       $la = 0;
    }
    #******* Consumo ************#
    $labc = $con->Listar("SELECT valor, cantidad_facturada FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodo");
    $lactual = $labc[0][0];
    $consumo = $labc[0][1];
    #*********************** Consumos Anteriores ***************************#
    #1
    $ba = $con->Listar("SELECT cantidad_facturada FROM gp_lectura 
        WHERE unidad_vivienda_medidor_servicio = $uvms 
            AND periodo < $periodo  ORDER BY periodo DESC");
    $valor6 = $ba[0][0];
    $valor5 = $ba[1][0];
    $valor4 = $ba[2][0];
    $valor3 = $ba[3][0];
    $valor2 = $ba[4][0];
    $valor1 = $ba[5][0];

    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(empty($vfac[0][0])) { $valorf6 =0;} else {$valorf6 = $vfac[0][0];}

    #** Promedio **#
    $promedio   = (($valor1+$valor2+$valor3+$valor4+$valor5+$valor6)/6);
    $promedio   = round($promedio,0);
    $lam        = 0;
    $lactualm   = 0;
    if(strlen($la)>3){  $lam = substr($la,-3);} else { $lam = $la;}
    if(strlen($lactual)>3){ $lactualm = substr($lactual,-3); } else { $lactualm = $lactual; }
    #***********************************************************************#
    $pdf->Image('../' . $rutalogoTer, 12, -3, 30);
    $pdf->SetXY(2,23);
    $pdf->setTextColor(0, 165, 236);
    $pdf->SetFont('Arial', 'B',9);
    $pdf->Cell(50, 5,'NIT:'.$numeroIdent, 0, 0, 'C');
    $pdf->setTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetY(10);
    $pdf->SetX(35);
    $pdf->MultiCell(150, 5, utf8_decode($razonsocial), 0, 'C');
    $pdf->SetXY(170,10);
    $pdf->setTextColor(0, 165, 236);
    $pdf->SetFont('Arial', 'B',14);
    $pdf->MultiCell(50, 5,ucwords(mb_strtolower($ciudad)),0, 'C');
    $pdf->SetFont('Arial', 'B',11);
    $pdf->SetXY(180,16);
    $pdf->MultiCell(30, 5,ucwords(mb_strtolower($departamento)),0, 'C');
   // $pdf->Cell(30, 5,utf8_decode('FACTURA N°:'), 0, 0, 'C');
    $pdf->setTextColor(0, 0, 0);
    $pdf->Ln(5);
    $pdf->SetY(16);
    $pdf->SetX(40);
   // $pdf->Cell(150, 5,'NIT:'.$numeroIdent, 0, 0, 'C');
    //$pdf->Cell(30, 5,$numero, 0, 0, 'C');
    //$pdf->Ln(5);
    //$pdf->SetX(40);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->MultiCell(150, 5,'SERVICIO DE ACUEDUCTO Y ALCANTARILLADO',0, 'C');
     $pdf->SetX(35);
    $pdf->MultiCell(150, 5,'FACTURA DE VENTA POR SERVICIOS DE ACUEDUCTO, ALCANTARILLADO Y ASEO',0, 'C');
    $pdf->SetXY(140,20);
   // $pdf->Cell(30, 5,utf8_decode('Fecha Expedición:'), 0, 0, 'L');
   // $pdf->Cell(30, 5,$fac[$f][5], 0, 0, 'C');
    $pdf->Ln(8);
    #***************************************************************************#
    $pdf->SetFillColor(238,238,238); 
    #**¨Línea 1 **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30, 8,utf8_decode('Factura N°'), 1, 0, 'C',true);
    $pdf->Cell(70,8,utf8_decode('Nombre'),1,0,'C',true);
    $pdf->Cell(50,8,utf8_decode('Barrio/Vereda'),1,0,'C',true);
    $pdf->Cell(50,8,utf8_decode('Periodo Facturado'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30, 5,$numero, 1, 0, 'C');
    $pdf->SetFont('Arial','',8);
    $pdf->CellFitScale(70,5,utf8_decode($tercero),1,0,'L');
    $pdf->Cell(50,5,utf8_decode($sector),1,0,'L');
    $pdf->CellFitScale(50,5,utf8_decode($periodo_n.' - '.$fecha_p.' - '.$fecha_s),1,0,'L');
    $pdf->Ln(5);
    #**¨Línea 2 **#
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(30, 8,utf8_decode('Fecha Expedición'), 1, 0, 'L', true);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,8,utf8_decode('Dirección'),1,0,'C', true);
    $pdf->Cell(20,8,utf8_decode('Atraso'),1,0,'C',true);
    $pdf->Cell(40,8,utf8_decode('Código Ruta'),1,0,'C',true);
    $pdf->Cell(40,8,utf8_decode('Código Interno'),1,0,'C',true);
    $pdf->Ln(8);    
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30, 5,$fac[$f][5], 1, 0, 'C');
    $pdf->Cell(70,5,utf8_decode($direccion_p),1,0,'L');
    $pdf->Cell(20,5,utf8_decode($atraso),1,0,'L');
    $pdf->Cell(40,5,utf8_decode($codigo_ruta),1,0,'L');
    $pdf->Cell(40,5,utf8_decode($codigo_interno),1,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(12,8,utf8_decode('Estrato'),1,0,'C',true);
    $pdf->Cell(18,8,utf8_decode('Uso'),1,0,'C',true);    
    $pdf->Cell(60,8,utf8_decode('Consumos anteriores'),1,0,'C',true);
    $pdf->Cell(13,8,utf8_decode('Lect. Ant'),1,0,'C',true);
    $pdf->Cell(13,8,utf8_decode('Lect. Act'),1,0,'C',true);
    $pdf->Cell(14,8,utf8_decode('Consumo'),1,0,'C',true);
    $pdf->Cell(15,8,utf8_decode('Promedio'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','',8);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell(12,10,utf8_decode($estrato),1,0,'C');
    $pdf->Cell(18,10,utf8_decode($uso),1,0,'C');
    
     #*********************** Consumos Anteriores ***************************#
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(10,3,utf8_decode('Mes 6'),1,0,'L');
    $pdf->Cell(10,3,utf8_decode('Mes 5'),1,0,'L');
    $pdf->Cell(10,3,utf8_decode('Mes 4'),1,0,'L');
    $pdf->Cell(10,3,utf8_decode('Mes 3'),1,0,'L');
    $pdf->Cell(10,3,utf8_decode('Mes 2'),1,0,'L');
    $pdf->Cell(10,3,utf8_decode('Mes 1'),1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(13,10,utf8_decode($lam),1,0,'C');
    $pdf->Cell(13,10,utf8_decode($lactualm),1,0,'C');
    $pdf->Cell(14,10,utf8_decode($consumo),1,0,'C');
    $pdf->Cell(15,10,utf8_decode($promedio),1,0,'C');
    $pdf->SetXY($x+30, $y+3);
    $pdf->Cell(10,7,utf8_decode($valor6),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor5),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor4),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor3),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor2),1,0,'C');
    $pdf->Cell(10,7,utf8_decode($valor1),1,0,'C');
    $pdf->Ln(7);
    #***********************************************************************#
    # Servicos 
    #***********************************************************************#
    $pdf->Cell(144,0.5,utf8_decode(''),1,1,'C');
    $pdf->Ln(0.5);
    $pdf->SetFont('Arial','B',10);   
    $pdf->Cell(48,8,utf8_decode('Acueducto'),1,0,'C',true);
    $pdf->Cell(0.5,8,utf8_decode(''),1,0,'C',true);
    $pdf->Cell(48,8,utf8_decode('Alcantarillado'),1,0,'C',true);    
    $pdf->Cell(0.5,8,utf8_decode(''),1,0,'C',true);
    $pdf->Cell(48,8,utf8_decode('Aseo'),1,0,'C',true);
    $pdf->Ln(8);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetFont('Arial','',6);  
    $bac = $con->Listar("SELECT c.id_unico, c.nombre_m,t.valor 
        FROM gp_concepto_tarifa ct LEFT JOIN gp_concepto c ON ct.concepto = c.id_unico 
        LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
        WHERE c.nombre like '%acueducto%' AND ct.parametrizacionanno = $para AND t.valor != 0
        AND c.nombre_m !='Subsidio Asuaped COVID-19' ");
    $x1 = $pdf->GetX();
    $y1 = $pdf->GetY();
    for ($ac = 0; $ac < count($bac); $ac++) {
        $pdf->SetX($x1);
        $pdf->Cell(33,4,utf8_decode($bac[$ac][1]),0,0,'L');
        $pdf->Cell(15,4,'$'.number_format($bac[$ac][2], 2, '.',','),0,0,'R');
        $pdf->Ln(4);
    }
    $pdf->SetXY($x1+48.5, $y1);
    $x2 = $pdf->GetX();
    $y2 = $pdf->GetY();
    #**¨Verficar si la unidad de vivienda tiene servicio activo 
    $serva = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio 
        WHERE unidad_vivienda=$id_uv AND tipo_servicio =2 AND estado_servicio = 1");
    if(count($serva)>0){
        $bal = $con->Listar("SELECT c.id_unico, c.nombre_m,t.valor 
            FROM gp_concepto_tarifa ct LEFT JOIN gp_concepto c ON ct.concepto = c.id_unico 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%alcantarillado%' AND ct.parametrizacionanno = $para AND t.valor != 0 
            AND c.nombre_m !='Subsidio Asuaped COVID-19' ");
        for ($al = 0; $al < count($bal); $al++) {
            $pdf->SetX($x2);
            $pdf->Cell(33,4,utf8_decode($bal[$al][1]),0,0,'L');
            $pdf->Cell(15,4,'$'.number_format($bal[$al][2], 2, '.',','),0,0,'R');
            $pdf->Ln(4);
        }
    }
    $pdf->SetXY($x2+48.5, $y2);
    $x3 = $pdf->GetX();
    $serva = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio 
        WHERE unidad_vivienda=$id_uv AND tipo_servicio =3 AND estado_servicio = 1");
    if(count($serva)>0){
        $bas = $con->Listar("SELECT c.id_unico, c.nombre_m,t.valor 
            FROM gp_concepto_tarifa ct LEFT JOIN gp_concepto c ON ct.concepto = c.id_unico 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%aseo%' AND ct.parametrizacionanno = $para AND t.valor != 0");
        for ($as = 0; $as < count($bas); $as++) {
            $pdf->SetX($x3);
            $pdf->Cell(33,4,utf8_decode($bas[$as][1]),0,0,'L');
            $pdf->Cell(15,4,'$'.number_format($bas[$as][2], 2, '.',','),0,0,'R');
            $pdf->Ln(4);
        }
    }
    
    $pdf->SetXY($x, $y);
    $pdf->Cell(48,20,utf8_decode(''),1,0,'C');
    $pdf->Cell(0.5,20,utf8_decode(''),1,0,'C');
    $pdf->Cell(48,20,utf8_decode(''),1,0,'C');    
    $pdf->Cell(0.5,20,utf8_decode(''),1,0,'C');
    $pdf->Cell(48,20,utf8_decode(''),1,0,'C');
    $pdf->Ln(20);
    $pdf->Cell(144.5,utf8_decode(''),1,1,'C');
    $pdf->Ln(0.5);
    #******************************************************************************************#
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $bac = $con->Listar("SELECT c.id_unico, c.nombre_m,df.valor_total_ajustado 
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
        LEFT JOIN gp_concepto c oN df.concepto_tarifa = c.id_unico 
        WHERE c.nombre like '%acueducto%'  AND f.id_unico =$id_factura");
    $x1 = $pdf->GetX();
    $y1 = $pdf->GetY();
    $totalac = 0;
    $totals  = 0;
    $v1      = 0;
    for ($ac = 0; $ac < count($bac); $ac++) { 
        if($bac[$ac][1]=='Cargo Fijo'){
            $totals  += $bac[$ac][2];
        }
        $id_concepto = $bac[$ac][0];
        $vl = valores($id_concepto, $id_uso, $id_estrato, $para);   
        if($consumo>30){
            if($bac[$ac][1]=='Consumo'){
                $valt = $bac[$ac][2];
                $vmc  = $bac[$ac][2]/$consumo;
                $v1   = round($vmc * 16);
                $v11  = round($vmc * 14);
                $v2   = $valt - ($v1+$v11);
                $totals  +=($v1+$v11);

                #** Buscar concepto en tabla parametros
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Consumo Básico (0-16)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v1, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->Cell(33,4,utf8_decode('Consumo con subsidio(17-30)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v11, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Consumo (>30)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v2, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            } elseif($bac[$ac][1]=='Subsidio') {
                $porc = Round(($bac[$ac][2]*-1)*100)/$totals;
                $v4   = Round($v1 *$porc)/100;
                $v44  = Round($v11 *$porc)/100;
                $v5   = ($bac[$ac][2]*-1) - ($v4+$v44);
                $pm   = round($porc,0);
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Subsido Cargo Fijo '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v5, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x1);
                $pdf->CellFitScale(33,4,utf8_decode('Subsido Consumo Básico (0-16) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v4, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Subsido (17-30) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v44, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            } else {
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode($bac[$ac][1].' '.$vl),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($bac[$ac][2], 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            }
        } elseif($consumo>=16){ 
            if($bac[$ac][1]=='Consumo'){
                $valt = $bac[$ac][2];
                $vmc  = $bac[$ac][2]/$consumo;
                $vrx  = $consumo-16;
                $v1   = round($vmc * 16);
                if($vrx>0){
                    $v11  = round($vmc * $vrx);
                } else {
                    $v11  = 0;
                }
                //echo $v11;
                $v2   = $valt - ($v1+$v11);
                $totals  +=($v1+$v11);
                #** Buscar concepto en tabla parametros
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Consumo Básico (0-16)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v1, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->Cell(33,4,utf8_decode('Consumo con subsidio(17-30)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v11, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            } elseif($bac[$ac][1]=='Subsidio') {
                $porc = Round(($bac[$ac][2]*-1)*100)/$totals;
                $v4   = Round($v1 *$porc)/100;
                $v44  = Round($v11 *$porc)/100;
                $v5   = ($bac[$ac][2]*-1) - ($v4+$v44);
                $pm   = round($porc,0);
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Subsido Cargo Fijo '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v5, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x1);
                $pdf->CellFitScale(33,4,utf8_decode('Subsido Consumo Básico (0-16) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v4, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Subsido (17-30) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v44, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            } else {
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode($bac[$ac][1].' '.$vl),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($bac[$ac][2], 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            }
        } else {
            if($bac[$ac][1]=='Consumo'){
                $totals  += $bac[$ac][2];
            }
            if($bac[$ac][1]=='Consumo'){
                $v1 = $bac[$ac][2];
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Consumo Básico (0-16)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v1, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            } elseif($bac[$ac][1]=='Subsidio') {
                $porc = Round(($bac[$ac][2]*-1)*100)/$totals;
                $v4   = Round($v1 *$porc)/100;
                $v5   = ($bac[$ac][2]*-1) - ($v4);
                $pm   = round($porc,0);
                $pm   = round($porc,0);
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode('Subsido Cargo Fijo '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v5, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x1);
                $pdf->CellFitScale(33,4,utf8_decode('Subsido Consumo Básico (0-16) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v4, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            } else {
                $pdf->SetX($x1);
                $pdf->Cell(33,4,utf8_decode($bac[$ac][1]),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($bac[$ac][2], 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            }            
        }
        $totalac += $bac[$ac][2];
    }
    $pdf->SetXY($x1+48.5, $y1);
    $x2 = $pdf->GetX();
    $y2 = $pdf->GetY();
    $bal = $con->Listar("SELECT c.id_unico, c.nombre_m,df.valor_total_ajustado 
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
        LEFT JOIN gp_concepto c oN df.concepto_tarifa = c.id_unico 
        WHERE c.nombre like '%alcantarillado%' AND f.id_unico =$id_factura");
    $totalal = 0;
    $totals  = 0;
    $totalsubsidio =0;
    $totalsinsubsidio = 0;
    for ($al = 0; $al < count($bal); $al++) {
        $id_concepto = $bal[$al][0];
        $vl = valores($id_concepto, $id_uso, $id_estrato, $para);
        if($bal[$al][1]=='Cargo Fijo'){
            $totals  += $bal[$al][2];
        }
        if($consumo>30){
            if($bal[$al][1]=='Consumo'){
                $valt = $bal[$al][2];
                $vmc  = $bal[$al][2]/$consumo;
                $v1   = round($vmc * 16);
                $v11  = round($vmc * 14);
                $v2   = $valt - ($v1+$v11);
                $totals  +=($v1+$v11);
                #** Buscar concepto en tabla parametros
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Consumo Básico (0-16)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v1, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Consumo con subsidio (17-30)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v11, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Consumo (>30)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v2, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $totalsinsubsidio += $v1+$v11+$v2;
            } elseif($bal[$al][1]=='Subsidio') {
                $porc = Round(($bal[$al][2]*-1)*100)/$totals;
                $v4   = Round($v1 * $porc)/100;
                $v44  = Round($v11 * $porc)/100;
                $v5   = ($bal[$al][2]*-1) - ($v4+$v44);
                $pm   = round($porc,0);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Subsido Cargo Fijo '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v5, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->CellFitScale(33,4,utf8_decode('Subsido Consumo Básico (0-16) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v4, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Subsido (17-30) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v44, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $totalsubsidio +=$v5+$v4+$v44;
            }  else {
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode($bal[$al][1].' '.$vl),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($bal[$al][2], 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            }
        }elseif($consumo>=16){ 
            if($bal[$al][1]=='Consumo'){
                $valt = $bal[$al][2];
                $vmc  = $bal[$al][2]/$consumo;
                $vrx  = $consumo-16;
                $v1   = round($vmc * 16);
                $v11  = round($vmc * $vrx);
                $v2   = $valt - ($v1+$v11);
                $totals  +=($v1+$v11);
                #** Buscar concepto en tabla parametros
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Consumo Básico (0-16)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v1, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Consumo con subsidio (17-30)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v11, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $totalsinsubsidio += $v1+$v11;
            } elseif($bal[$al][1]=='Subsidio') {
                $porc = Round(($bal[$al][2]*-1)*100)/$totals;
                $v4   = Round($v1 * $porc)/100;
                $v44  = Round($v11 * $porc)/100;
                $v5   = ($bal[$al][2]*-1) - ($v4+$v44);
                $pm   = round($porc,0);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Subsido Cargo Fijo '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v5, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->CellFitScale(33,4,utf8_decode('Subsido Consumo Básico (0-16) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v4, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Subsido (17-30) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v44, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $totalsubsidio +=$v5+$v4+$v44;
            }  else {
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode($bal[$al][1].' '.$vl),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($bal[$al][2], 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            }
        } else {
            if($bal[$al][1]=='Consumo'){
                $totals  += $bal[$al][2];
            }
            if($bal[$al][1]=='Consumo'){
                $v1 = $bal[$al][2];
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Consumo Básico (0-16)'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($v1, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $totalsinsubsidio += $v1;
                
            }elseif($bal[$al][1]=='Subsidio') {
                $porc = Round(($bal[$al][2]*-1)*100)/$totals;
                $v4   = Round($v1 * $porc)/100;
                $v5   = ($bal[$al][2]*-1) - ($v4);
                $pm   = round($porc,0);
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode('Subsido Cargo Fijo '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v5, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $pdf->SetX($x2);
                $pdf->CellFitScale(33,4,utf8_decode('Subsido Consumo Básico (0-16) - '.$pm.'%'),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format('-'.$v4, 2, '.',','),0,0,'R');
                $pdf->Ln(4);
                $totalsubsidio +=$v5+$v4;
            } else {
                $pdf->SetX($x2);
                $pdf->Cell(33,4,utf8_decode($bal[$al][1].' '.$vl),0,0,'L');
                $pdf->Cell(15,4,'$'.number_format($bal[$al][2], 2, '.',','),0,0,'R');
                $pdf->Ln(4);
            }
        }
        $totalal += $bal[$al][2];
    }

    $pdf->SetXY($x2+48.5, $y2);
    $x3 = $pdf->GetX();
    $bas = $con->Listar("SELECT c.id_unico, c.nombre_m,df.valor_total_ajustado 
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
        LEFT JOIN gp_concepto c oN df.concepto_tarifa = c.id_unico 
        WHERE c.nombre like '%aseo%' AND f.id_unico =$id_factura");
    $totalas = 0;
    for ($as = 0; $as < count($bas); $as++) {
        $id_concepto = $bas[$as][0];
        $vl = valores($id_concepto, $id_uso, $id_estrato, $para);
        #** Buscar 
        $pdf->SetX($x3);
        $pdf->Cell(33,4,utf8_decode($bas[$as][1].' '.$vl),0,0,'L');
        $pdf->Cell(15,4,'$'.number_format($bas[$as][2], 2, '.',','),0,0,'R');
        $pdf->Ln(4);
        $totalas += $bas[$as][2];
    }
    $pdf->SetXY($x, $y);
    $pdf->Cell(48,40,utf8_decode(''),1,0,'C');
    $pdf->Cell(0.5,40,utf8_decode(''),1,0,'C');
    $pdf->Cell(48,40,utf8_decode(''),1,0,'C');    
    $pdf->Cell(0.5,40,utf8_decode(''),1,0,'C');
    $pdf->Cell(48,40,utf8_decode(''),1,0,'C');
    $pdf->Ln(40);
    $pdf->SetFont('Arial','B',8.5);  
    $pdf->Cell(144,0.5,utf8_decode(''),1,1,'C');
    $pdf->Ln(0.5);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell(33,10,utf8_decode('Total Acueducto'),0,0,'L',true);
    $pdf->Cell(15,10,'$'.number_format($totalac, 2, '.',','),0,0,'R',true);
    $pdf->Cell(0.5,10,utf8_decode(''),0,0,'C',true);
    $pdf->Cell(33,10,utf8_decode('Total Alcantarillado'),0,0,'L',true);
    $pdf->Cell(15,10,'$'.number_format($totalal, 2, '.',','),0,0,'R',true);
    $pdf->Cell(0.5,10,utf8_decode(''),0,0,'C',true);
    $pdf->Cell(33,10,utf8_decode('Total Aseo'),0,0,'L',true);
    $pdf->Cell(15,10,'$'.number_format($totalas, 2, '.',','),0,0,'R',true);
    $pdf->SetXY($x,$y);
    $pdf->Cell(48,10,'',1,0,'L');
    $pdf->Cell(0.5,10,'',1,0,'C');
    $pdf->Cell(48,10,'',1,0,'L');
    $pdf->Cell(0.5,10,'',1,0,'C');
    $pdf->Cell(48,10,'',1,0,'L');
    $pdf->Ln(10);
    $pdf->Cell(144,0.5,utf8_decode(''),1,1,'C');
    $pdf->Ln(0.5);
    $pdf->SetFont('Arial','B',9); 
    $pdf->Cell(96.5,10,utf8_decode('OTROS CONCEPTOS'),1,0,'C',true);
    $pdf->Cell(0.5,10,'',1,0,'C',true);
    $pdf->SetXY(155,54);
    $pdf->setFillColor(0, 165, 236);
    $pdf->setTextColor(255, 255, 255);
    $pdf->SetFont('Arial','B',10); 
    $pdf->Cell(55,8,utf8_decode('TOTALES'),1,0,'C',true);
    $pdf->SetXY(155,62);
    $pdf->Cell(0.5,40,'',1,0,'C');
    $pdf->Cell(54,40,'',1,0,'C');
    $pdf->Cell(0.5,40,'',1,0,'C');
    $pdf->Ln(40);
    $pdf->SetX(155);
    $pdf->Cell(54,0.5,'',1,0,'C');

    $pdf->Ln(10);
    $pdf->SetXY(137,162);
    $pdf->Ln(0.5);
    #* Otros conceptos *#
    $pdf->SetFont('Arial','B',8); 
    $pdf->setTextColor(0, 0, 0);
    $pdf->setFillColor(255, 255, 255);
    $xp = $pdf->GetX();
    $yp = $pdf->GetY();
    $pdf->Cell(34.5,8,utf8_decode('Concepto'),1,0,'C',true);
    $pdf->Cell(20,8,utf8_decode('Saldo'),1,0,'C',true);
    $pdf->Cell(20,8,utf8_decode('Valor C.'),1,0,'C',true);
    $pdf->Cell(11,8,utf8_decode('Total C.'),1,0,'C',true);
    $pdf->Cell(11,8,utf8_decode('Pend.'),1,0,'C',true);
    $pdf->Cell(0.5,8,'',1,0,'C',true);
    $pdf->Ln(8);
    #*** Buscar Otros Conceptos ***#
    $otc = $con->Listar("SELECT c.nombre, o.valor_total, o.valor_cuota, 
            o.cuotas_pagas, o.cuotas_pendientes, c.id_unico, o.id_unico, 
            o.total_cuotas  
                FROM gf_otros_conceptos o 
                LEFT JOIN gp_concepto c ON o.concepto = c.id_unico 
                WHERE o.unidad_vivienda_ms IN ($ids_uv) AND c.id_unico != 23 ");
    $pdf->SetFont('Arial','',8);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    for ($ot = 0; $ot < count($otc); $ot++) { 
        #* Buscar si concepto esta relacionado a la factura 
        $crf = $con->Listar("SELECT * FROM gp_detalle_factura 
            WHERE factura = $id_factura AND otros_conceptos = ".$otc[$ot][6]);
        if(count($crf)>0) { 
            #** Buscar Facturas siguientes con ese concepto ****#
            $df = $con->Listar("SELECT DISTINCT df.* 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                AND f.periodo >$periodo 
                AND df.otros_conceptos =".$otc[$ot][6]." 
                AND df.concepto_tarifa =".$otc[$ot][5]);
            $dr  =0;
            if(count($df)>0){ $dr = count($df);} else {$dr = 0;}
            $vr = $otc[$ot][2]*$dr;
            $saldo = $otc[$ot][2]*($otc[$ot][4]+$dr);
            $pdf->SetFont('Arial','',7.5); 
            $pdf->Cell(34.5,5,utf8_decode($otc[$ot][0]),0,0,'L'); 
            $pdf->Cell(20,5,'$'.number_format($saldo,2,'.',','),0,0,'C');
            $pdf->Cell(20,5,'$'.number_format($otc[$ot][2],2,'.',','),0,0,'C');
            $pdf->Cell(11,5,utf8_decode($otc[$ot][7]),0,0,'C');
            $pdf->Cell(11,5,utf8_decode($otc[$ot][4]+$dr),0,0,'C');
            $pdf->Ln(5);
        }
    }
    $tss = $con->Listar("SELECT SUM(valor_total_ajustado)    FROM gp_detalle_factura WHERE factura = $id_factura AND valor_total_ajustado>0 ");
    $tis = $con->Listar("SELECT SUM(valor_total_ajustado*-1) FROM gp_detalle_factura WHERE factura = $id_factura AND valor_total_ajustado<0 ");
    $total_sin = $tss[0][0] + $deuda_anterior; //SIN SUBSIDIO PAGARÍA
    $total_sub = $tis[0][0]; // CON EL SUBSIDIO AHORRO
    
    $pdf->SetXY(10,197.5);
    $pdf->setFillColor(0, 165, 236);
    $pdf->setTextColor(255, 255, 255);
    $pdf->SetFont('Arial','B',9); 
    $pdf->Cell(48.5,8,utf8_decode('SIN SUBSIDIO PAGARÍA'),1,0,'C',true);
    $pdf->Cell(48.5,8,utf8_decode('CON EL SUBSIDIO AHORRO'),1,0,'C',true);
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',10); 
    $pdf->setFillColor(255, 255, 255);
    $pdf->setTextColor(0, 0, 0);
    $pdf->Cell(48.5,8,'$'.number_format($total_sin,2,'.',','),1,0,'C');
    $pdf->Cell(48.5,8,'$'.number_format($total_sub,2,'.',','),1,0,'C');

    $pdf->SetFont('Arial','B',8);
    $pdf->SetXY(155, 153);
    $pdf->MultiCell(55,4,utf8_decode('NOTA: El costo de la reconexión por suspensión es de $25.000'),1,'J');
     $pdf->SetFont('Arial','B',9);
    $pdf->SetXY(107,153);    
    $pdf->setFillColor(255, 255, 255);
    $pdf->setTextColor(0, 0, 0);
    $pdf->Cell(48,8,utf8_decode('OBSERVACIÓN'),1,0,'C',true);     
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',9);
    $pdf->SetX(107);
    $pdf->Cell(103,6,utf8_decode('Vigilado por la Superintendencia de Servicios Públicos'),'TR', 0,'C');  
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);
    $pdf->SetX(107);
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->MultiCell(103,4,utf8_decode('La presente se asimila por todos los efectos legales a un Título Valor o de conformidad con lo establecido en el artículo 774 del Código de Comercio, la ley 142 en su artículo 532, y lo estipulado por la ley 1231 de 2006. Autorización Numeración de Facturación Nº 18762003300632 Fecha:2017/05/17 Numeración Autorizada del Nº 20001 al 31225'),'R','J');
    $pdf->SetX(107);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(103,5,utf8_decode('PAGUE OPORTUNAMENTE Y EVITE LA SUSPENSIÓN DEL SERVICIO'),'BR', 0,'C');  
    $pdf->Ln(5);
    


    $pdf->SetXY(155,64);
    $pdf->SetFont('Arial','',9);  
    $pdf->Cell(30,5,utf8_decode('Total Acueducto'),0,0,'L');
    $pdf->Cell(23,5,'$'.number_format($totalac, 2, '.',','),0,0,'R');
    $pdf->SetXY(155,69);
    $pdf->Cell(30,5,utf8_decode('Total Alcantarillado'),0,0,'L');
    $pdf->Cell(23,5,'$'.number_format($totalal, 2, '.',','),0,0,'R');
    $pdf->SetXY(155,74);
    $pdf->Cell(30,5,utf8_decode('Total Aseo'),0,0,'L');
    $pdf->Cell(23,5,'$'.number_format($totalas, 2, '.',','),0,0,'R');
    $pdf->SetXY(155,79);
    $xp1 = $pdf->GetX();
    $xp2 = $pdf->GetY();
    #*** Buscar Otros Cobros ***#
    $otr = $con->Listar("SELECT df.id_unico, c.nombre,df.valor_total_ajustado 
        FROM gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
        LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico 
        WHERE tc.id_unico IN (5) AND df.factura=$id_factura");
    $total_otros =0;
    for ($z=0; $z < count($otr); $z++) { 
        $pdf->SetX($xp1);
        $pdf->Cell(30,5,utf8_decode($otr[$z][1]),0,0,'L');
        $pdf->Cell(23,5,'$'.number_format($otr[$z][2], 2, '.', ','),0,0,'R');   
        $pdf->Ln(5);
        $total_otros +=$otr[$z][2];
        
    }
    if($deuda_anterior>0){
        $pdf->SetX($xp1);
        $pdf->Cell(30,5,utf8_decode('Deuda Anterior'),0,0,'L');
        $pdf->Cell(23,5,'$'.number_format($deuda_anterior, 2, '.', ','),0,0,'R');   
        $pdf->Ln(5);
    }

   

    // espacio de otros conceptos
    $pdf->SetXY($xp,$yp);
    $pdf->Cell(96.5,35,'',1,0,'C');
    $pdf->Cell(0.5,35,'',1,0,'C');
    $pdf->Ln(35);
    
    //echo 'OC'.$total_otros.' -DA '.$deuda_anterior.' - TAC'.$totalac.' - TASE'.$totalas.' - TAL'.$totalal;
    $total_pagar = $total_otros + $deuda_anterior +$totalac+$totalas+$totalal;
    $pdf->SetFont('Arial','B',10);
    $pdf->SetXY(155,103);
    $pdf->setFillColor(0, 165, 236);
    $pdf->setTextColor(255, 255, 255);
    $pdf->Cell(55,8,utf8_decode('VALOR A PAGAR'),1,0,'C',true);

    $pdf->SetXY(155,111);
    $pdf->setFillColor(255, 255, 255);
    $pdf->setTextColor(0, 0, 0);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0.5,8,'',1,0,'C');
    $pdf->Cell(54,8,'$'.number_format($total_pagar,2,'.',','),1,0,'C');
    $pdf->Cell(0.5,8,'',1,0,'C');
    $pdf->Ln(8);
    $pdf->SetX(155);
    $pdf->Cell(54,0.5,'',1,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->SetXY(155,120);
    $pdf->setFillColor(238, 238, 238);
    $pdf->Cell(55,6,utf8_decode('HASTA EL'),1,1,'C',true);
    $pdf->SetXY(155,126);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(55,11,utf8_decode($fecha_cierre),1,0,'C');   
    $pdf->SetXY(155,136);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(55,6,utf8_decode('PAGUE EN'),1,1,'C',true);
    $pdf->SetXY(155,142);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(55,11,utf8_decode('OFICINAS EFECTY'),1,0,'C');


    #* Firmas 
    $rowfirmas = $con->Listar("SELECT IF(CONCAT_WS(' ', 
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos) 
    IS NULL OR CONCAT_WS(' ',
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos) = '',
    UPPER(t.razonsocial),
    CONCAT_WS(' ',
    UPPER(t.nombreuno),
    UPPER(t.nombredos),
    UPPER(t.apellidouno),
    UPPER(t.apellidodos))) AS NOMBRE, ti.nombre, t.numeroidentificacion, UPPER(car.nombre) , 
    t.tarjeta_profesional, t.firma 
 FROM gp_factura f 
 LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
 LEFT JOIN gf_tipo_comprobante tcp ON tf.tipo_comprobante = tcp.id_unico 
 LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
 LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
 LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico
 LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
 LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
 LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
 LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
 WHERE f.id_unico = $id_factura AND t.id_unico IS NOT NULL 
 AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= f.fecha_factura) 
 AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >=  f.fecha_factura) 
 ORDER BY rd.ORDEN ASC");
$nombre = $rowfirmas[0][0];
$cargo  = $rowfirmas[0][3];
$firma  = $rowfirmas[0][5];


      $pdf->Sety(213);
      $pdf->Ln(3);
      $pdf->SetFont('Arial','B',7); 
      $pdf->Cell(80,6, strtoupper(''),'',0,'C'); 
      $pdf->Cell(50,6, utf8_decode('PAGUE HASTA:'.' '.utf8_decode($fecha_cierre)),'',0,'R'); 
      $pdf->Cell(20,6, utf8_decode(''),'',0,'L');
      $pdf->Cell(25,6, utf8_decode('VALOR:'.' '.'$'.number_format($total_pagar,2,'.',',')),'',0,'R');
      $pdf->Cell(20,6, number_format('', 0, ',','.'),'',0,'R');
      $pdf->Image('../' .$firma, 13,219, 60);
      $pdf->Sety(232);
      $pdf->SetFont('Arial','B',8); 
      $pdf->Ln(2);  
      $pdf->SetX(10); 
      $pdf->MultiCell(60, 4, utf8_decode($nombre), 0, 'C');
      $pdf->SetX(10); 
      $pdf->Cell(60,4, utf8_decode($cargo),'',0,'C');  
      $pdf->Ln(4);   
      $pdf->SetX(15); 
      $pdf->setFillColor(255, 255, 255);      	
      $pdf->SetFont('Arial','B',6); 
      $pdf->Cell(50,4, 'PAGO SERVICIO DE ACUEDUCTO Y ALCANTARILLADO','',0,'C');     
      $pdf->Ln(4); 
      $pdf->SetX(20); 
      $pdf->Cell(40,4, 'PERIODO FACTURADO:','',0,'C'); 
      $pdf->Ln(4);
      $pdf->SetX(20); 
      $pdf->Cell(40,4, utf8_decode($periodo_n.' - '.$fecha_p.' - '.$fecha_s),'',0,'C');    
      $pdf->Cell(95,4, strtoupper(''),'',0,'C'); 

      //información código de barras
        $xt = str_replace(',','',$total_pagar);
        $ct = strlen($xt);
        $hoy = date('Y-m-d');
        $hoy = str_replace('-','',$hoy);
        if($ct < 14){
            $xt = str_pad($xt, 14, "0", STR_PAD_LEFT);
        }                    
        $fechart = explode('/',$fecha_cierre);
        $dia=  $fechart[0];
        $mes=  $fechart[1];
        $anio= $fechart[2];
        $fechart = $anio.$mes.$dia;
        $fechafact = str_replace('-','',$fecha_factura);
        $ref = $numero.$identificacion;
        $cr = strlen($ref);
        if($cr < 24){
            $ref = str_pad($ref, 24, "0", STR_PAD_LEFT);
        }
      //Código de barras
        $codigoEAN = $rowCod[0][0];
        $format_barcode = "415".$codigoEAN."8020".$ref."3900".$xt."96".$fechart;
        $barcode = "(415)$codigoEAN(8020)$ref(3900)$xt(96)$fechart"; 
        $pdf->setFillColor(0, 0, 0);          	
		$pdf->Code128(80,225,$format_barcode,130,25);		
        $pdf->SetXY(95, 249);
        $pdf->SetFont('Arial','',7); 
        $pdf->Cell(100,9, utf8_decode($barcode),'',0,'C');

      //fin código de barras  

         $pdf->SetY(173);        
         $pdf->SetFont('Arial','',9); 
         $pdf->Ln(11);
        $yi = $pdf->GetY();   
    if($deuda_anterior==0){
            $pdf->SetFont('Arial','I',10);
            $pdf->Image('icon.png', 112,$yi+12, 15);
             $pdf->SetXY(130, $yi+12);
            $pdf->MultiCell(87,5,utf8_decode('¡Felicitaciones! Usted se encuentra al día con su facturación.'),0,'L');
           
    } else {
        $pdf->SetFont('Arial','I',10);
            $pdf->Image('icon2.png', 112,$yi+12, 15);
            $pdf->SetXY(130, $yi+12);
            $pdf->MultiCell(87,5,utf8_decode('Usted tiene facturas pendientes con la entidad. Cancele a tiempo para evitar la suspensión del servicio.'),0,'L');
    }

    $pdf->SetXY(107, 192);
    $pdf->Cell(103,21.6,'',1,0,'C'); 
    $pdf->Ln(3);
    $pdf->AddPage();
     
    $sqlRutaImg =  "SELECT imagen from gp_periodo WHERE id_unico = $periodo";
    $rutaImg = $mysqli->query($sqlRutaImg);
    $rowImg = mysqli_fetch_array($rutaImg);
    $ruta = $rowImg[0]; 
    if($ruta != ''){
      $pdf->Image('../'.$ruta, 5, 40, 100);
    }   
   
    $pdf->Image('../' . $rutalogoTer, 11, -3, 30);
    $pdf->Image('../img/mensaje2.png',5,170, 100);
    $pdf->Image('../img/informacion.png', 110,38, 100);
    $pdf->Image('../img/mensaje1.png', 110,170, 100);

   
    
    if($f == count($fac)-1) {
    } else {
    $pdf->AddPage();
    }
}



#*****************************************************************************************
    


#*****************************************************************************************
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Factura(' . date('d/m/Y') . ').pdf', 0);

function valores($id_concepto, $id_uso, $id_estrato, $para){
    global $con;
    $vl ='';
    #* Buscar por uso y estrato
    $conp = $con->Listar("SELECT id_unico, consumo, valor 
            FROM gp_parametros_facturacion 
            WHERE concepto = $id_concepto AND estrato = $id_estrato 
            AND uso = $id_uso AND parametrizacionanno = $para");
    if(count($conp)>0){
        $vl = $conp[0][1].' - '.$conp[0][2].'%';
    } else {
        #* Buscar por uso
        $conp = $con->Listar("SELECT id_unico, consumo, valor 
            FROM gp_parametros_facturacion 
            WHERE concepto = $id_concepto 
            AND uso = $id_uso AND parametrizacionanno = $para");
        if(count($conp)>0){
            $vl = $conp[0][1].' - '.$conp[0][2].'%';
        } else {
            #* Buscar por estrato
            $conp = $con->Listar("SELECT id_unico, consumo, valor 
                FROM gp_parametros_facturacion 
                WHERE concepto = $id_concepto AND estrato = $id_estrato 
                AND parametrizacionanno = $para");
            if(count($conp)>0){
                $vl = $conp[0][1].' - '.$conp[0][2].'%';
            } else {
                $conp = $con->Listar("SELECT id_unico, consumo, valor 
                FROM gp_parametros_facturacion 
                WHERE concepto = $id_concepto 
                   AND parametrizacionanno = $para");
                if(count($conp)>0){
                    $vl = $conp[0][1].' - '.$conp[0][2].'%';
                }
            }
        }
    }
    return $vl;
}    