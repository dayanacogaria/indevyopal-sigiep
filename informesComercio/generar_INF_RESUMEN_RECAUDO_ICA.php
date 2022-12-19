<?php

    session_start();
        
    require'../fpdf/fpdf.php';
    require'../Conexion/conexion.php';
    ini_set('max_execution_time', 360);
    ob_start();
    $compania   = $_SESSION['compania'];
    $usuario    = $_SESSION['usuario'];
    $id_anno    = $_SESSION['anno'];
    $consulta = "SELECT         lower(t.razonsocial) as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum
                FROM gf_tercero t
                LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico                WHERE t.id_unico = $compania";

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
    echo "Banco Inicial: ".$BancoI;
    echo "<br/>";
    echo "Banco Final: ".$BancoF;
    echo "<br/>";
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

    $cuentaBI = "SELECT descripcion FROM gf_cuenta_bancaria WHERE id_unico = '$BancoI'";
    $CBI = $mysqli->query($cuentaBI);
    $NCBI = mysqli_num_rows($CBI);
    if($NCBI > 0){
        $BII = mysqli_fetch_row($CBI);
        $BI = $BII[0];
    }else{
        $BI = "";
    }

    $cuentaBF = "SELECT descripcion FROM gf_cuenta_bancaria WHERE id_unico = '$BancoF'";
    $CBF = $mysqli->query($cuentaBF);
    $NCBF = mysqli_num_rows($CBF);
    if($NCBF > 0){
        $BFF = mysqli_fetch_row($CBF);
        $BF = $BFF[0];
    }else{
        $BF = "";
    }

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
            global $BI;
            global $BF;
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
            $this->Cell(330,10,utf8_decode('RESUMEN DE RECAUDOS DE RETEICA'),0,0,'C');
            $this->Ln(10);
            $this->SetFont('Arial','B',10);
            $this->Cell(25,10,utf8_decode('Fecha Inicial:'),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(20,10,utf8_decode($FI),0,0,'C');
            $this->SetFont('Arial','B',10);
            $this->Cell(30,10,utf8_decode('Fecha Final:'),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(20,10,utf8_decode($FF),0,0,'C');
            $this->Ln(5);
            
            $this->SetFont('Arial','B',10);
            $this->Cell(30,10,utf8_decode('Cuenta Inicial:'),0,0,'C');
            $this->Cell(15,10,utf8_decode(''),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(30,10,utf8_decode($BI),0,0,'C');
            $this->Cell(15,10,utf8_decode(''),0,0,'C');
            $this->SetFont('Arial','B',10);
            $this->Cell(40,10,utf8_decode('Cuenta Final:'),0,0,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(40,10,utf8_decode($BF),0,0,'C');
            $this->SetFont('Arial','',8);
            
            $this->SetFont('Arial','',8);
            $this->SetX(0);
            $this->Ln(20);
            

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

        $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico >= '$BancoI'  ORDER BY id_unico ASC";
        $res = $mysqli->query($sql10);
    

        while($CBan = mysqli_fetch_row($res)){

        $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                WHERE rc.fecha >='$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC ";
        
        
        $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";
        
        $sql4 = "SELECT     c.id_unico,
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
                            rc.num_pag,
                            c.codigo_mat,
                            d.id_unico,
                            rc.fecha,
                            d.cod_dec
                FROM gc_recaudo_comercial rc 
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                WHERE  rc.fecha >= '$fechaI'  AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

        $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]'";
            $RES1 = $mysqli->query($sql12);
            
            $nre = mysqli_num_rows($RES1);

            if($nre > 0){
                $valor = $mysqli->query($sql2);
                $cantidad = $mysqli->query($sql3);
                $ncon = mysqli_fetch_row($cantidad);
                $nb=$pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->AliasNbPages();
                $filas = 245 / $ncon[0] ;
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5, utf8_decode('Cuenta: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($CBan[1]),0,0,'C'); 

                $cccc = "SELECT banco FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                $xxx = $mysqli->query($cccc);
                $idba = mysqli_fetch_row($xxx);

                $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                         tr.apellidodos)) AS NOMBRE
                            FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                $re = $mysqli->query($Nba);
                $Nbc = mysqli_fetch_row($re);

                $pdf->Cell(15,5, utf8_decode('Banco: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($Nbc[0]),0,0,'C'); 
                
                $pdf->Ln(10);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();

                
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(15,5, utf8_decode('Cod Mat.'),0,0,'C');
                $pdf->Cell(35,5, utf8_decode('Contribuyente'),0,0,'C');
                $pdf->Cell(10,5, utf8_decode('Decla'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Periodo G'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Fecha R'),0,0,'C');

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
                        }
                        $pdf->SetXY($x+$filas,$y);
                        

                    

                
                }

                $pdf->SetXY($cx,$cy);
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(35,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');


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

                    $FReca = explode("-",$CR1[6]);
                    $A = $FReca[0];
                    $M = $FReca[1];
                    $D = $FReca[2];

                    $FR = $D.'/'.$M.'/'.$A;
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[4]),1,0,'C');  
                    $pdf->Cellfitscale(35,8, utf8_decode($CR1[1]),1,0,'R');  
                    $pdf->Cellfitscale(10,8, utf8_decode($CR1[7]),1,0,'C'); 
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[4]),1,0,'C');

                    
                    $pdf->Cellfitscale(15,8, utf8_decode($FR),1,0,'C'); 
                    

                    while($Tcon1 = mysqli_fetch_row($con2)){
                        $sql5 = "SELECT dr.valor
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                        
                        $conc_comercio = $mysqli->query($sql5);
                        $nconC = mysqli_num_rows($conc_comercio);
                        
                        if($nconC > 0){
                            $V = mysqli_fetch_row($conc_comercio);
                            $pdf->Cellfitscale($filas,8, utf8_decode(number_format($V[0],0,'.',',')),1,0,'R');
                        }else{
                            $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
                        }
                    }
                    $pdf->Ln(8);
                    $con2 = $mysqli->query($sql2);
                } 
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(90,8, utf8_decode('Total:'),1,0,'C');
                
                $con3 = $mysqli->query($sql2);
                while($Tcon3 = mysqli_fetch_row($con3)){
                    if(empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND  rc.fecha >= '$fechaI' AND rc.cuenta_ban =  '$CBan[0]' AND rc.clase = 2";
                    }elseif(!empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";
                    }elseif(empty($fecF) && !empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ";
                    }else{
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ";
                            
                    }
                    
                    $res3 = $mysqli->query($sql5);
                    $r = mysqli_fetch_row($res3);
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($r[0],0,'.',',')),1,0,'R');
                }
            }

        }

    }elseif(!empty($fecF) && empty($BancoF)){

        $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico >= '$BancoI'  ORDER BY id_unico ASC";
        $res = $mysqli->query($sql10);
    

        while($CBan = mysqli_fetch_row($res)){

            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                    LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC";
            
            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

            $sql4 = "SELECT     c.id_unico,
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
                            rc.num_pag,
                            c.codigo_mat,
                            d.id_unico,
                            rc.fecha,
                            d.cod_dec
                FROM gc_recaudo_comercial rc 
                LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                WHERE  rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

            $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]'";
            $RES1 = $mysqli->query($sql12);
            
            $nre = mysqli_num_rows($RES1);

            if($nre > 0){
                $valor = $mysqli->query($sql2);
                $cantidad = $mysqli->query($sql3);
                $ncon = mysqli_fetch_row($cantidad);
                $nb=$pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->AliasNbPages();
                $filas = 245 / $ncon[0] ;
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5, utf8_decode('Cuenta: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($CBan[1]),0,0,'C'); 

                $cccc = "SELECT banco FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                $xxx = $mysqli->query($cccc);
                $idba = mysqli_fetch_row($xxx);

                $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                         tr.apellidodos)) AS NOMBRE
                            FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                $re = $mysqli->query($Nba);
                $Nbc = mysqli_fetch_row($re);

                $pdf->Cell(15,5, utf8_decode('Banco: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($Nbc[0]),0,0,'C'); 
                
                $pdf->Ln(10);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();

                
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(15,5, utf8_decode('Cod Mat.'),0,0,'C');
                $pdf->Cell(35,5, utf8_decode('Contribuyente'),0,0,'C');
                $pdf->Cell(10,5, utf8_decode('Decla'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Periodo G'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Fecha R'),0,0,'C');

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
                        }
                        $pdf->SetXY($x+$filas,$y);
                        

                    

                
                }

                $pdf->SetXY($cx,$cy);
                
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(35,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');


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

                    $FReca = explode("-",$CR1[6]);
                    $A = $FReca[0];
                    $M = $FReca[1];
                    $D = $FReca[2];

                    $FR = $D.'/'.$M.'/'.$A;
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[4]),1,0,'C');  
                    $pdf->Cellfitscale(35,8, utf8_decode($CR1[1]),1,0,'R');  
                    $pdf->Cellfitscale(10,8, utf8_decode($CR1[7]),1,0,'C'); 
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[7]),1,0,'C'); 

                    
                    $pdf->Cellfitscale(15,8, utf8_decode($FR),1,0,'C'); 
                    

                    while($Tcon1 = mysqli_fetch_row($con2)){
                        $sql5 = "SELECT dr.valor
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                        
                        $conc_comercio = $mysqli->query($sql5);
                        $nconC = mysqli_num_rows($conc_comercio);
                        
                        if($nconC > 0){
                            $V = mysqli_fetch_row($conc_comercio);
                            $pdf->Cellfitscale($filas,8, utf8_decode(number_format($V[0],0,'.',',')),1,0,'R');
                        }else{
                            $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
                        }
                    }
                    $pdf->Ln(8);
                    $con2 = $mysqli->query($sql2);
                } 
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(90,8, utf8_decode('Total:'),1,0,'C');
                
                $con3 = $mysqli->query($sql2);
                while($Tcon3 = mysqli_fetch_row($con3)){
                    if(empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' rc.fecha >= '$fechaI' rc.cuenta_ban = '$BancoI' AND rc.clase = 2";
                    }elseif(!empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";
                    }elseif(empty($fecF) && !empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";
                    }else{
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ";
                           
                    }
                    
                    $res3 = $mysqli->query($sql5);
                    $r = mysqli_fetch_row($res3);
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($r[0],0,'.',',')),1,0,'R');
                }
            }

        }


    }elseif(empty($fecF) && !empty($BancoF)){

        $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico BETWEEN '$BancoI' AND '$BancoF' ORDER BY id_unico ASC";
        $res = $mysqli->query($sql10);
    

        while($CBan = mysqli_fetch_row($res)){
            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                    LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC";

            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 "; 
                    
            $sql4 = "SELECT     c.id_unico,
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
                                rc.num_pag,
                                c.codigo_mat,
                                d.id_unico,
                                rc.fecha,
                                d.cod_dec,
                                ac.vigencia
                    FROM gc_recaudo_comercial rc 
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico 
                    WHERE  rc.fecha >= '$fechaI'  AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

            $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]' ";
            $RES1 = $mysqli->query($sql12);
            
            $nre = mysqli_num_rows($RES1);

            if($nre > 0){
                $valor = $mysqli->query($sql2);
                $cantidad = $mysqli->query($sql3);
                $ncon = mysqli_fetch_row($cantidad);
                $nb=$pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->AliasNbPages();
                $filas = 245 / $ncon[0] ;
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5, utf8_decode('Cuenta: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($CBan[1]),0,0,'C'); 

                $cccc = "SELECT banco FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                $xxx = $mysqli->query($cccc);
                $idba = mysqli_fetch_row($xxx);

                $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                         tr.apellidodos)) AS NOMBRE
                            FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                $re = $mysqli->query($Nba);
                $Nbc = mysqli_fetch_row($re);

                $pdf->Cell(15,5, utf8_decode('Banco: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($Nbc[0]),0,0,'C'); 
                
                $pdf->Ln(10);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();

                
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(15,5, utf8_decode('Cod Mat.'),0,0,'C');
                $pdf->Cell(35,5, utf8_decode('Contribuyente'),0,0,'C');
                $pdf->Cell(10,5, utf8_decode('Decla'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Periodo G'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Fecha R'),0,0,'C');

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
                        }
                        $pdf->SetXY($x+$filas,$y);
                        

                    

                
                }

                $pdf->SetXY($cx,$cy);
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(35,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');


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

                    $FReca = explode("-",$CR1[6]);
                    $A = $FReca[0];
                    $M = $FReca[1];
                    $D = $FReca[2];

                    $FR = $D.'/'.$M.'/'.$A;
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[4]),1,0,'C');  
                    $pdf->Cellfitscale(35,8, utf8_decode($CR1[1]),1,0,'R');  
                    $pdf->Cellfitscale(10,8, utf8_decode($CR1[8]),1,0,'C'); 
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[7]),1,0,'C'); 

                    
                    $pdf->Cellfitscale(15,8, utf8_decode($FR),1,0,'C'); 
                    

                    while($Tcon1 = mysqli_fetch_row($con2)){
                        $sql5 = "SELECT dr.valor
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                        
                        $conc_comercio = $mysqli->query($sql5);
                        $nconC = mysqli_num_rows($conc_comercio);
                        
                        if($nconC > 0){
                            $V = mysqli_fetch_row($conc_comercio);
                            $pdf->Cellfitscale($filas,8, utf8_decode(number_format($V[0],0,'.',',')),1,0,'R');
                        }else{
                            $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
                        }
                    }
                    $pdf->Ln(8);
                    $con2 = $mysqli->query($sql2);
                } 
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(90,8, utf8_decode('Total:'),1,0,'C');
                
                $con3 = $mysqli->query($sql2);
                while($Tcon3 = mysqli_fetch_row($con3)){
                    if(empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' rc.fecha >= '$fechaI' rc.cuenta_ban = '$BancoI' AND rc.clase = 2";
                    }elseif(!empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2";
                    }elseif(empty($fecF) && !empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";
                    }else{
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ";
                       
                    }
                    
                    $res3 = $mysqli->query($sql5);
                    $r = mysqli_fetch_row($res3);
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($r[0],0,'.',',')),1,0,'R');
                }
            }

        }

    }else{

        $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico BETWEEN '$BancoI' AND '$BancoF' ORDER BY id_unico ASC";
        $res = $mysqli->query($sql10);
    

        while($CBan = mysqli_fetch_row($res)){

            $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                    LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC";
                
            $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                    WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";       
            
            $sql4 = "SELECT     c.id_unico,
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
                                cb.banco,
                                rc.num_pag,
                                c.codigo_mat,
                                d.id_unico,
                                rc.fecha,
                                d.cod_dec,
                                ac.vigencia
                    FROM gc_recaudo_comercial rc 
                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                    LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    WHERE  rc.fecha BETWEEN '$fechaI' AND '$fechaF'  AND rc.cuenta_ban  = '$CBan[0]' AND rc.clase = 2";

            

            $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]' AND clase = 2";
            $RES1 = $mysqli->query($sql12);
            
            $nre = mysqli_num_rows($RES1);

            if($nre > 0){
                $valor = $mysqli->query($sql2);
                $cantidad = $mysqli->query($sql3);
                $ncon = mysqli_fetch_row($cantidad);
                $nb=$pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->AliasNbPages();
                $filas = 245 / $ncon[0] ;
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15,5, utf8_decode('Cuenta: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($CBan[1]),0,0,'C'); 

                $cccc = "SELECT banco FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                $xxx = $mysqli->query($cccc);
                $idba = mysqli_fetch_row($xxx);

                $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                         tr.apellidodos)) AS NOMBRE
                            FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                $re = $mysqli->query($Nba);
                $Nbc = mysqli_fetch_row($re);

                $pdf->Cell(15,5, utf8_decode('Banco: '),0,0,'C');
                $pdf->Cellfitscale(25,5, utf8_decode($Nbc[0]),0,0,'C'); 
                
                $pdf->Ln(10);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();

                
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(15,5, utf8_decode('Cod Mat.'),0,0,'C');
                $pdf->Cell(35,5, utf8_decode('Contribuyente'),0,0,'C');
                $pdf->Cell(10,5, utf8_decode('Decla'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Periodo G'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Fecha R'),0,0,'C');

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
                        }
                        $pdf->SetXY($x+$filas,$y);
                        

                    

                
                }

                $pdf->SetXY($cx,$cy);
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(35,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');


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

                    $FReca = explode("-",$CR1[6]);
                    $A = $FReca[0];
                    $M = $FReca[1];
                    $D = $FReca[2];

                    $FR = $D.'/'.$M.'/'.$A;
                    $pdf->SetFont('Arial','',7);
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[4]),1,0,'C');  
                    $pdf->Cellfitscale(35,8, utf8_decode($CR1[1]),1,0,'L');  
                    $pdf->Cellfitscale(10,8, utf8_decode($CR1[7]),1,0,'C'); 
                    $pdf->Cellfitscale(15,8, utf8_decode($CR1[8]),1,0,'C'); 

                    
                    $pdf->Cellfitscale(15,8, utf8_decode($FR),1,0,'C'); 
                    

                    while($Tcon1 = mysqli_fetch_row($con2)){
                        $sql5 = "SELECT dr.valor
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                        
                        $conc_comercio = $mysqli->query($sql5);
                        $nconC = mysqli_num_rows($conc_comercio);
                        
                        if($nconC > 0){
                            $V = mysqli_fetch_row($conc_comercio);
                            $pdf->Cellfitscale($filas,8, utf8_decode(number_format($V[0],0,'.',',')),1,0,'R');
                        }else{
                            $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
                        }
                    }
                    $pdf->Ln(8);
                    $con2 = $mysqli->query($sql2);
                    
                    $Altura = $pdf->GetY();

                    if($Altura >= 180){
                        //$pdf->Ln(50);
                        $pdf->AddPage();
                        $filas = 245 / $ncon[0] ;

                        $cx1 = $pdf->GetX();
                        $cy1 = $pdf->GetY();

                        
                        $pdf->SetFont('Arial','B',8);
                        $pdf->Cell(15,5, utf8_decode('Cod Mat.'),0,0,'C');
                        $pdf->Cell(35,5, utf8_decode('Contribuyente'),0,0,'C');
                        $pdf->Cell(10,5, utf8_decode('Decla'),0,0,'C');
                        $pdf->Cell(15,5, utf8_decode('Periodo G'),0,0,'C');
                        $pdf->Cell(15,5, utf8_decode('Fecha R'),0,0,'C');

                        $h4 = 0;
                        $h3 = 0;
                        $alto1 = 0;
                        $valor1 = $mysqli->query($sql2);
                        while($cat1 = mysqli_fetch_row($valor1)){

                    
                            $x1 =$pdf->GetX();
                            $y1 =$pdf->GetY(); 
                            $pdf->SetFont('Arial','B',8);
                            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($cat1[2]))),0,'C');
                            $y3 = $pdf->GetY();
                            $h3 = $y3 - $y1;
                        
                            if($h3 > $h4){
                        
                                $alto1 = $h3;
                                $h4 = $h3;
                            }
                            $pdf->SetXY($x1+$filas,$y1);
                        
                        }

                        $pdf->SetXY($cx1,$cy1);
                        $pdf->Cell(15,$alto1, utf8_decode(''),1,0,'C');
                        $pdf->Cell(35,$alto1, utf8_decode(''),1,0,'C');
                        $pdf->Cell(10,$alto1, utf8_decode(''),1,0,'C');
                        $pdf->Cell(15,$alto1, utf8_decode(''),1,0,'C');
                        $pdf->Cell(15,$alto1, utf8_decode(''),1,0,'C');


                        $con5 = $mysqli->query($sql2);

                        while ($Tcon6 = mysqli_fetch_row($con5)) {
                        
                            $x4 =$pdf->GetX();
                            $y4 =$pdf->GetY(); 
                            $pdf->SetFont('Arial','',8);
                            $pdf->MultiCell($filas,$alto1, utf8_decode(''),1,'C');
                            $pdf->SetXY($x4+$filas,$y4);
                        }
                        $pdf->Ln($alto1);

                    }
                } 
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(90,8, utf8_decode('Total:'),1,0,'C');
                
                $con3 = $mysqli->query($sql2);
                while($Tcon3 = mysqli_fetch_row($con3)){
                    if(empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' rc.fecha >= '$fechaI' rc.cuenta_ban = '$BancoI' AND rc.clase = 2 ";
                    }elseif(!empty($fecF) && empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                                FROM gc_detalle_recaudo dr
                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2";
                    }elseif(empty($fecF) && !empty($BancoF)){
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND  rc.fecha >= '$fechaI' rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2";
                    }else{
                        $sql5 = "SELECT SUM(dr.valor)
                            FROM gc_detalle_recaudo dr
                            LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                            LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                            WHERE dd.concepto = '$Tcon3[0]' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ";
                        
                    }
                    
                    $res3 = $mysqli->query($sql5);
                    $r = mysqli_fetch_row($res3);
                    $pdf->Cellfitscale($filas,8, utf8_decode(number_format($r[0],0,'.',',')),1,0,'R');
                }
            }

        }
        
        $pdf->Ln(20);

        $hoy = date('d-m-Y');
        $hoy = trim($hoy, '"');
        $fecha_div = explode("-", $hoy);
        $anioA = $fecha_div[2];
        $mesA = $fecha_div[1];
        $diaA = $fecha_div[0];

        $vigeAct = $anioA;
        $pdf->SetFont('Arial','B',10);
        echo $VigenciaAC = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                        WHERE ac.vigencia = '$vigeAct' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

        $VigActual = $mysqli->query($VigenciaAC);
        $VAC = mysqli_fetch_row($VigActual);

        if(empty($fecF) && empty($BancoF)){
            
            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = $BancoI' AND rc.clase = 2
                    order by cc.codigo ASC ";

            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$BancoI' AND rc.clase = 2
                    order by cc.codigo ASC ";

        }elseif(!empty($fecF) && empty($BancoF)){

            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha BETWEEN'$fechaI' AND '$fechaF' AND  rc.cuenta_ban = '$BancoI' AND rc.clase = 2
                    order by cc.codigo ASC ";

            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban = '$BancoI' AND rc.clase = 2
                    order by cc.codigo ASC ";


        }elseif(empty($fecF) && !empty($BancoF)){
            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2
                    order by cc.codigo ASC ";

            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BacnoF' AND rc.clase = 2
                    order by cc.codigo ASC ";
        }else{
            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2
                    order by cc.codigo ASC ";
            echo "<br/>";
            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2
                    order by cc.codigo ASC ";
        }
        $PosY = $pdf->GetY();
        if($PosY >= 180){
            $pdf->Ln(20);
        }
        $pdf->Cell(50,5, utf8_decode('TOTAL VIGENCIA ACTUAL:'),0,0,'L');
        $pdf->Cellfitscale(50,5, utf8_decode(number_format($VAC[0],0,'.',',')),0,0,'R');
        $pdf->Ln(5);

        

        $ConRec = $mysqli->query($ConceptosVAC);
        $NConR = mysqli_num_rows($ConRec);
        
        $filas1 = 335 / $NConR ;
        $cx = $pdf->GetX();
        $cy = $pdf->GetY();
        
        $A2 = 0;
        $A = 0;
        $altura = 0;
        while($rowCR = mysqli_fetch_row($ConRec)){

            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas1,5, utf8_decode(ucwords(mb_strtolower($rowCR[11]))),0,'C');
            $y2 = $pdf->GetY();
            $A = $y2 - $y;
              
            if($A > $A2){
                   
                $altura = $A;
                $A2 = $A;
            }
            $pdf->SetXY($x+$filas1,$y);
        }

        $conR1 = $mysqli->query($ConceptosVAC);
        $pdf->SetXY($cx,$cy);
        while ($TconR = mysqli_fetch_row($conR1)) {
                
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','',8);
            $pdf->MultiCell($filas1,$altura, utf8_decode(''),1,'C');
            $pdf->SetXY($x+$filas1,$y);
        }
        $pdf->Ln($altura);

        $conR2 = $mysqli->query($ConceptosVAC);
        while($TconR2 = mysqli_fetch_row($conR2)){

            
            if(empty($fecF) && empty($BancoF)){
                $sql10 = "SELECT SUM(dr.valor)
                    FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban = '$BancoI' AND rc.fecha >= '$fechaI' AND rc.clase = 2";
            }elseif(!empty($fecF) && empty($BancoF)){
                $sql10 = "SELECT SUM(dr.valor)
                    FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban = '$BancoI' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2";

            }elseif(empty($fecF) && !empty($BancoF)){
                $sql10 = "SELECT SUM(dr.valor)
                    FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.fecha >= '$fechaI' AND rc.clase = 2";
            }else{
                $sql10 = "SELECT SUM(dr.valor)
                    FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2";
            }
            
                        
            $conc_comercio = $mysqli->query($sql10);
            $nconC = mysqli_num_rows($conc_comercio);
                        
            if($nconC > 0){
                $V = mysqli_fetch_row($conc_comercio);
                $pdf->Cellfitscale($filas1,8, utf8_decode(number_format($V[0],0,'.',',')),1,0,'R');
            }else{
                $pdf->Cellfitscale($filas1,8, utf8_decode('0'),1,0,'R');  
            }
        }
        $pdf->Ln(10);

       

        $VigenciaAN = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                        WHERE ac.vigencia < '$vigeAct' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

        $VigAnterior = $mysqli->query($VigenciaAN);
        $VAN = mysqli_fetch_row($VigAnterior);

        $PosY2 = $pdf->GetY();
        if($PosY2 >= 160){
            $pdf->Ln(20);
        }
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(50,5, utf8_decode('TOTAL VIGENCIAS ANTERIORES: '),0,0,'L');
        $pdf->Cellfitscale(50,5, utf8_decode(number_format($VAN[0],0,'.',',')),0,0,'R');
        $pdf->Ln(5);

        
        
        $ConRec = $mysqli->query($ConceptosVAN);
        $NConR = mysqli_num_rows($ConRec);
        
        $filas1 = 335 / $NConR ;
        $cx = $pdf->GetX();
        $cy = $pdf->GetY();
        
        $A2 = 0;
        $A = 0;
        $altura = 0;
        while($rowCR = mysqli_fetch_row($ConRec)){

            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas1,5, utf8_decode(ucwords(mb_strtolower($rowCR[11]))),0,'C');
            $y2 = $pdf->GetY();
            $A = $y2 - $y;
              
            if($A > $A2){
                   
                $altura = $A;
                $A2 = $A;
            }
            $pdf->SetXY($x+$filas1,$y);
        }

        $conR1 = $mysqli->query($ConceptosVAN);
        $pdf->SetXY($cx,$cy);
        while ($TconR = mysqli_fetch_row($conR1)) {
                
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','',8);
            $pdf->MultiCell($filas1,$altura, utf8_decode(''),1,'C');
            $pdf->SetXY($x+$filas1,$y);
        }
        $pdf->Ln($altura);

        $conR2 = $mysqli->query($ConceptosVAN);
        while($TconR2 = mysqli_fetch_row($conR2)){

            echo $sql10 = "SELECT SUM(dr.valor)
                    FROM gc_detalle_recaudo dr
                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                    WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia < '$vigeAct' AND rc.parametrizacionanno = '$id_anno' AND rc.clase = 2";
             echo "<br/>";           
            $conc_comercio = $mysqli->query($sql10);
            $nconC = mysqli_num_rows($conc_comercio);
                        
            if($nconC > 0){
                $V = mysqli_fetch_row($conc_comercio);
                $pdf->Cellfitscale($filas1,8, utf8_decode(number_format($V[0],0,'.',',')),1,0,'R');
            }else{
                $pdf->Cellfitscale($filas1,8, utf8_decode('0'),1,0,'R');  
            }
        }

        
    }
    


    ob_end_clean();
    $pdf->Output(0,'Resumen_Recaduo ('.date('d/m/Y').').pdf',0);     
    
?>