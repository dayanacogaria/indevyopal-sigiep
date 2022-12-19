<?php

    session_start();
        
    require'../fpdf/fpdf.php';
    require'../Conexion/conexion.php';
    ini_set('max_execution_time', 360);
    ob_start();
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];

    $consulta = "SELECT         lower(t.razonsocial) as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum
                FROM gf_tercero t
                LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                WHERE t.id_unico = $compania";

    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
    FROM gf_tercero ter 
    LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
    WHERE ter.id_unico = '.$compania;

    $rutaLogo = $mysqli->query($sqlRutaLogo);
    $rowLogo = mysqli_fetch_array($rutaLogo);
    $ruta = $rowLogo[0];
    $ciudadCompania = $rowLogo[1];

    $fecI = $_POST['sltFechaI'];
    $fecF = $_POST['sltFechaF'];

    if(!empty($fecI)){
        $hoy = trim($fecI, '"');
        $fecha_div = explode("/", $hoy);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fechaI = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
        
    }

    if(!empty($fecF)){
        $hoy1 = trim($fecF, '"');
        $fecha_div2 = explode("/", $hoy1);
        $anio1 = $fecha_div2[2];
        $mes1 = $fecha_div2[1];
        $dia1 = $fecha_div2[0];
        $fechaF = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
        
    }

    $BancoI = $_POST['sltBancoI'];
    $BancoF = $_POST['sltBancoF'];



    $cmp = $mysqli->query($consulta);

        $nomcomp = "";
        $tipodoc = "";
        $numdoc = 0;
        
        while ($fila = mysqli_fetch_array($cmp))
        {
            $nomcomp = $fila['traz'];       
            $tipodoc = utf8_decode($fila['tnom']);       
            $numdoc  = utf8_decode($fila['tnum']);   
        }
    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode("-", $hoy);
    $anioh = $fecha_div[2];
    $mesh = $fecha_div[1];
    $diah = $fecha_div[0];
    $hoy = $diah.'/'.$mesh.'/'.$anioh;

    $sql1 = "SELECT   e.codigointerno,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    t.numeroidentificacion, 
                    CONCAT(t.nombreuno,'  ',t.nombredos,'  ',t.apellidouno,'  ',t.apellidodos)
            
            FROM gn_empleado e
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            WHERE e.id_unico !=2";

    $cp      = $mysqli->query($sql1);


    class PDF extends FPDF
    {
        // Cabecera de página  
        function Header()
        { 
            global $nomcomp;
            global $tipodoc;
            global $numdoc;
            global $ruta;
            global $periodo;
            global $pe;
            global $bo;
            global $cp;
            global $grg;
            global $unie;

            // Logo
            if($ruta != '')
            {
            $this->Image('../'.$ruta,20,8,15);
            } 
            $this->SetFont('Arial','B',14);
        
            // Título
            $this->Cell(330,10,utf8_decode(ucwords($nomcomp)),0,0,'C');
            // Salto de línea
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
        
            $this->Ln(4);

            $this->SetFont('Arial','',10);
            $this->Cell(330,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
            $this->SetFont('Arial','B',8);
            $this->SetX(0);
        

            $this->Ln(4);

            $this->SetFont('Arial','',8);
            $this->Cell(330,10,utf8_decode('SÁBANA DE PRIMA DE NAVIDAD'),0,0,'C');
        
            $this->SetFont('Arial','',8);
            $this->SetX(0);
            $this->Cell(350,18,utf8_decode('NÓMINA: '. $pe[1]),0,0,'C');
            $this->SetX(0);
            $this->Cell(350,26,utf8_decode('UNIDAD EJECUTORA: '. $unie[1]),0,0,'C');
            $this->SetX(0);
            $this->Ln(30);
            

        }
        // Pie de página
        function Footer()
        {
            // Posición: a 1,5 cm del final
            global $hoy;
            global $usuario;
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
            $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
            $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
            $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }


    // Creación del objeto de la clase heredada
    $pdf = new PDF('L','mm','Legal');        
    if(empty($fecF) && empty($BancoF)){

        $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                LEFT JOIN 	gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                WHERE rc.fecha >='$fechaI' AND rc.cuenta_ban >= '$BancoI' ORDER BY cc.id_unico ASC";
        
        
        $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban >='$BancoI'";
        
        $sql4 = "SELECT 	c.id_unico,
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
                            tr.apellidodos)),
                            cb.descripcion,
                            rc.num_pag
                FROM gc_recaudo_comercial rc 
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                WHERE  rc.fecha >= '$fechaI'  AND rc.cuenta_ban >='$BancoI'";

    }elseif(!empty($fecF) && empty($BancoF)){
        $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                LEFT JOIN 	gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban >='$BancoI' ORDER BY cc.id_unico ASC";
        
        $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban >='$BancoI'";

        $sql4 = "SELECT 	c.id_unico,
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
                            tr.apellidodos)),
                            cb.descripcion,
                            rc.num_pag
                FROM gc_recaudo_comercial rc 
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                WHERE  rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban >='$BancoI'";

    }elseif(empty($fecF) && !empty($BancoF)){
        $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                LEFT JOIN 	gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' ORDER BY cc.id_unico ASC";

        $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF'"; 
                
        $sql4 = "SELECT 	c.id_unico,
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
                            tr.apellidodos)),
                            cb.descripcion,
                            rc.num_pag
                FROM gc_recaudo_comercial rc 
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                WHERE  rc.fecha >= '$fechaI'  AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF'";

    }else{
        $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                LEFT JOIN 	gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' ORDER BY cc.id_unico ASC";

        $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF'";       
        
        $sql4 = "SELECT 	c.id_unico,
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
                            tr.apellidodos)),
                            cb.descripcion,
                            rc.num_pag
                FROM gc_recaudo_comercial rc 
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                WHERE  rc.fecha BETWEEN '$fechaI' AND '$fechaF'  AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF'";
    }

    $valor = $mysqli->query($sql2);
    $cantidad = $mysqli->query($sql3);
    $ncon = mysqli_fetch_row($cantidad);
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $filas = 215 / $ncon[0] ;
    $pdf->SetFont('Arial','B',8);
    $cx = $pdf->GetX();
    $cy = $pdf->GetY();

    $pdf->Cell(50,5, utf8_decode('Contribuyente'),0,0,'C');
    $pdf->Cell(35,5, utf8_decode('# Pago'),0,0,'C');
    $pdf->Cell(40,5, utf8_decode('Cuenta Bancaria'),0,0,'C');

    $h2 = 0;
    $h = 0;
    $alto = 0;



    while($cat = mysqli_fetch_row($valor)){

        
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($cat[2]))),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
        
            if($h > $h2){
        
                $alto = $h;
                $h2 = $h;
            }else{
            
                $h2 = $h;
            }
            $pdf->SetXY($x+$filas,$y);
        
        

    
    }

    $pdf->SetXY($cx,$cy);
    $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
    $pdf->Cell(35,$alto, utf8_decode(''),1,0,'C');
    $pdf->Cell(40,$alto, utf8_decode(''),1,0,'C');


    $con1 = $mysqli->query($sql2);

    while ($Tcon = mysqli_fetch_row($con1)) {
    
        $x =$pdf->GetX();
        $y =$pdf->GetY(); 
        $pdf->SetFont('Arial','',8);
        $pdf->MultiCell($filas,$alto, utf8_decode(''),1,'C');
        $pdf->SetXY($x+$filas,$y);
    }
    $pdf->Ln($alto);


    $CR = $mysqli->query($sql4);
    $con2 = $mysqli->query($sql2);
    while($CR1 = mysqli_fetch_row($CR)){
        
        $pdf->Cellfitscale(50,8, utf8_decode($CR1[1]),1,0,'C');  
        $pdf->Cellfitscale(35,8, utf8_decode($CR1[3]),1,0,'C'); 
        $pdf->Cellfitscale(40,8, utf8_decode($CR1[2]),1,0,'C'); 
        

        while($Tcon1 = mysqli_fetch_row($con2)){
            $sql5 = "SELECT dr.valor
                    FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]'";
            
            $conc_comercio = $mysqli->query($sql5);
            $nconC = mysqli_num_rows($conc_comercio);
            
            if($nconC > 0){
                $V = mysqli_fetch_row($conc_comercio);
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($V[0],2,'.',',')),1,0,'R');
            }else{
                $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
            }
        }
        $pdf->Ln(8);
        $con2 = $mysqli->query($sql2);
    }    


    ob_end_clean();
    $pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);     
    
?>