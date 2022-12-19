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

    $fecI = $_POST['sltFechaA'];
    $fecF = $_POST['sltFechaR'];
    $val = $_POST['sltVa'];

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

    if($val == 1){
        $v = "Todas";
    }elseif($val == 2){
        $v = "Pagadas";
    }else{
        $v = "No Pagadas";
    }

    $FI = $fecI;
    $FF = $fecF;

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
            global $FI;
            global $FF;
            global $v;
            
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

            $this->SetFont('Arial','',10);
            $this->Cell(330,10,utf8_decode('RESUMEN DE DECLARACION'),0,0,'C');
            $this->Ln(10);
            $this->SetFont('Arial','B',10);
            $this->Cell(25,10,utf8_decode('Fecha Inicial:'),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(20,10,utf8_decode($FI),0,0,'C');
            $this->SetFont('Arial','B',10);
            $this->Cell(30,10,utf8_decode('Fecha Final:'),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(20,10,utf8_decode($FF),0,0,'C');
            $this->SetFont('Arial','B',10);
            $this->Cell(30,10,utf8_decode('Listar Por:'),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(20,10,utf8_decode($v),0,0,'C');
            $this->Ln(5);
            
            $this->SetFont('Arial','',8);
            $this->SetX(0);
            $this->Ln(10);
            

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
    if(empty($fecF) ){

        if($val == 1){
            ECHO $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
            WHERE d.fecha >='$fechaI'  ORDER BY cc.codigo ASC";
    
    
            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE d.fecha >= '$fechaI' ";
            
            $sql4 = "SELECT DISTINCT	c.id_unico,
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
                                c.codigo_mat,
                                d.fecha,
                                d.id_unico
                    FROM gc_declaracion d
                    INNER JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    WHERE  d.fecha >= '$fechaI' ORDER BY d.fecha ASC ";
        }elseif($val == 2){
            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_recaudo_comercial rc
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE d.fecha >='$fechaI'  ORDER BY cc.id_unico ASC";
            
    
            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_recaudo_comercial rc
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE d.fecha >= '$fechaI' ";
            
            $sql4 = "SELECT DISTINCT c.id_unico,
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
                                c.codigo_mat,
                                d.fecha,
                                d.id_unico
                    FROM  gc_recaudo_comercial rc
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                    INNER JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    WHERE  d.fecha >= '$fechaI' ORDER BY d.fecha ASC ";
        }else{
            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE d.fecha >='$fechaI' AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY cc.codigo ASC";
    
    
            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE d.fecha >= '$fechaI' AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ";
    
            $sql4 = "SELECT DISTINCT c.id_unico,
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
                                c.codigo_mat,
                                d.fecha,
                                d.id_unico
                    FROM gc_declaracion d
                    INNER JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    WHERE  d.fecha >= '$fechaI' AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY d.fecha ASC ";
        }

    }else{
        if($val == 1){
            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                    LEFT JOIN 	gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY cc.codigo ASC";
            
            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' ";       
            
            $sql4 = "SELECT DISTINCT c.id_unico,
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
                                c.codigo_mat,
                                d.fecha,
                                d.id_unico
                    FROM gc_declaracion d
                    INNER JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    
                    WHERE  d.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY d.fecha ASC ";
        }elseif($val == 2){
            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_recaudo_comercial rc
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                LEFT JOIN 	gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF'  ORDER BY cc.codigo ASC";

            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_recaudo_comercial rc
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' ";       
    
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
                                c.codigo_mat,
                                d.fecha,
                                d.id_unico
                    FROM gc_recaudo_comercial rc
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    WHERE  d.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY d.fecha ASC ";
        }else{
            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                    LEFT JOIN 	gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY cc.codigo ASC";

            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ";       
    
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
                                c.codigo_mat,
                                d.fecha,
                                d.id_unico
                    FROM gc_declaracion d
                
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    
                    WHERE  d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY d.fecha ASC"; 
        }
        
    }

    $valor = $mysqli->query($sql2);
    $cantidad = $mysqli->query($sql3);
    $ncon = mysqli_fetch_row($cantidad);
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $filas = 220 / $ncon[0] ;
    $pdf->SetFont('Arial','B',8);
    $cx = $pdf->GetX();
    $cy = $pdf->GetY();

    $pdf->Cell(20,5, utf8_decode('Cod Matricula'),0,0,'C');
    $pdf->Cell(50,5, utf8_decode('Contribuyente'),0,0,'C');
    $pdf->Cell(20,5, utf8_decode('Fecha Dec'),0,0,'C');
    
    

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
            
            }
            $pdf->SetXY($x+$filas,$y);
        
        

    
    }

    $pdf->SetXY($cx,$cy);
    $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
    $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
    $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');


    $con1 = $mysqli->query($sql2);

    while ($Tcon = mysqli_fetch_row($con1)) {
    
        $x =$pdf->GetX();
        $y =$pdf->GetY(); 
        $pdf->SetFont('Arial','',8);
        $pdf->MultiCell($filas,$alto, utf8_decode(''),1,'C');
        $pdf->SetXY($x+$filas,$y);
    }

    $cx = $pdf->GetX();
    $cy = $pdf->GetY();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(10,5, utf8_decode('Pago'),0,0,'C');
    $pdf->Cell(20,5, utf8_decode('Fecha Pago'),0,0,'C');
    $pdf->SetXY($cx,$cy);
    $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
    $pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');

    $pdf->Ln($alto);

    $pdf->SetFont('Arial','',9);
    $CR = $mysqli->query($sql4);
    $con2 = $mysqli->query($sql2);
    while($CR1 = mysqli_fetch_row($CR)){
        $pdf->Cellfitscale(20,8, utf8_decode($CR1[2]),1,0,'C');  
        $pdf->Cellfitscale(50,8, utf8_decode($CR1[1]),1,0,'C');  

        $fecD = trim($CR1[3], '"');
        $fecha_div = explode("-", $fecD);
        $anio1 = $fecha_div[0];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[2];
        $fechaD = ''.$dia1.'/'.$mes1.'/'.$anio1.'';
        $pdf->Cellfitscale(20,8, utf8_decode($fechaD),1,0,'C');  
        

        while($Tcon1 = mysqli_fetch_row($con2)){
            $sql5 = "SELECT dd.valor
                    FROM gc_detalle_declaracion dd
                    
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[4]'";
            
            $conc_comercio = $mysqli->query($sql5);
            $nconC = mysqli_num_rows($conc_comercio);
            
            if($nconC > 0){
                $V = mysqli_fetch_row($conc_comercio);
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($V[0],2,'.',',')),1,0,'R');
            }else{
                $pdf->Cellfitscale($filas,8, utf8_decode('0.00'),1,0,'R');  
            }
        }

        $rec = "SELECT rc.fecha FROM gc_recaudo_comercial rc LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                WHERE d.contribuyente = '$CR1[0]' AND rc.declaracion = '$CR1[4]'";
        
        $recaudo = $mysqli->query($rec);
        $nrec = mysqli_num_rows($recaudo);
        if($nrec > 0){
            $reca = mysqli_fetch_row($recaudo);
            $fec = trim($reca[0], '"');
            $fecha_div = explode("-", $fec);
            $anio1 = $fecha_div[0];
            $mes1 = $fecha_div[1];
            $dia1 = $fecha_div[2];
            $fechaP = ''.$dia1.'/'.$mes1.'/'.$anio1.'';
            $pago = "SI";
        }else{
            $pago = "NO";
            $fechaP = "";
        }

        $pdf->Cell(10,8, utf8_decode($pago),1,0,'C');
        $pdf->Cell(20,8, utf8_decode($fechaP),1,0,'C');        
        $pdf->Ln(8);
        $con2 = $mysqli->query($sql2);

        
    } 
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(90,8, utf8_decode('Total:'),1,0,'C');
    
    $con3 = $mysqli->query($sql2);
    while($Tcon3 = mysqli_fetch_row($con3)){

        if(!empty($fecF)){
            
            if($val == 1){
                $sql5 = "SELECT SUM(dd.valor)
                    FROM gc_detalle_declaracion dd
                    
                    WHERE dd.concepto = '$Tcon3[0]'";
            }elseif($val == 2){

                $sql5 = "SELECT SUM(dd.valor), dd.concepto FROM gc_detalle_declaracion dd LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico  WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF'";

            }else{
                $sql5 = "SELECT SUM(dd.valor)
                    FROM gc_detalle_declaracion dd
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                    WHERE dd.concepto = '$Tcon3[0]' AND  d.fecha BETWEEN '$fechaI' AND '$fechaF' AND dd.declaracion NOT IN (SELECT rc.declaracion FROM gc_recaudo_comercial rc) ";  
                    
            }
             
        }else{
            if($val == 1){
                $sql5 = "SELECT SUM(dd.valor)
                    FROM gc_detalle_declaracion dd
                    
                    WHERE dd.concepto = '$Tcon3[0]'";
            }elseif($val == 2){
                echo $sql5 = "SELECT SUM(dd.valor), dd.concepto FROM gc_detalle_declaracion dd LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico  WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha >= '$fechaI' ";
                echo "<br/>";
            }else{


            $sql5 = "SELECT SUM(dd.valor)
                    FROM gc_detalle_declaracion dd
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                    WHERE dd.concepto = '$Tcon3[0]' AND  d.fecha >= '$fechaI' AND dd.declaracion NOT IN (SELECT rc.declaracion FROM gc_recaudo_comercial rc) ";


            }
        }
        

        $res3 = $mysqli->query($sql5);
        $r = mysqli_fetch_row($res3);
        $pdf->Cellfitscale($filas,8, utf8_decode(number_format($r[0],2,'.',',')),1,0,'R');
    }


    ob_end_clean();
    $pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);     
    
?>