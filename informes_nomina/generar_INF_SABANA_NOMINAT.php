    <?php
    require'../Conexion/conexion.php';
    require'../Conexion/ConexionPDO.php';
    ini_set('max_execution_time',0);
    session_start();
    $con        = new ConexionPDO();
    $compania   = $_SESSION['compania'];
    $usuario    = $_SESSION['usuario'];

    #************** Datos Compañia *********************#
    $rowC = $con->Listar("SELECT    ter.id_unico,
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
                    $this->Image('../' . $ruta_logo, 20,8,20);
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
                $this->Cell(320, 5, utf8_decode('SÁBANA DE NÓMINA'), 0, 0, 'C');
                $this->Ln(4); 
                $this->SetFont('Arial', 'B', 8);
                $this->SetX(25);
                $this->Cell(320, 5, utf8_decode('NÓMINA:'.$nperiodo), 0, 0, 'C');
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
       

        if(empty($_REQUEST['sltUnidadE']) && empty($_REQUEST['sltTipoE'])){        
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania  
                AND n.periodo = $periodo      
                AND e.codigointerno != '0000' 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

            $filas = 250 / $ncon[0][0] ;
            $pdf->SetFont('Arial','B',7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();

            $pdf->Cell(15,5, utf8_decode('N°'),0,0,'C');
            $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
            $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
           
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
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 6) 
                AND c.orden IS NOT NULL
                ORDER BY CAST(c.orden as UNSIGNED)");

             
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
            $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
            $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
           
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
            AND (c.clase IN(1,2,3,4,5) ) 
            ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");

            $pdf->SetFont('Arial','',7);
            $salarioa = 0;

            for ($e = 0; $e < count($rowe); $e++) { 
                $pdf->Cellfitscale(15,8, utf8_decode($e+1),1,0,'L');
                $pdf->Cellfitscale(15,8, utf8_decode($rowe[$e][4]),1,0,'L');
                $pdf->Cellfitscale(50,8, ($rowe[$e][5]),1,0,'L');

                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = ".$rowe[$e][0]." 
                    AND concepto = '78' AND periodo = '$periodo'");

               
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
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                }        
                $pdf->Ln(8);
            }
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(80,8, utf8_decode('Total:'),1,0,'C');
           

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
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) )");
                if(!empty($num_con[0][1])) {
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
            }
        } elseif(!empty($_REQUEST['sltTipoE'])) {

            #Tipo 
            $tipo = $_REQUEST['sltTipoE'];
            $nu = $con->Listar("SELECT id_unico, nombre FROM gn_tipo_empleado WHERE id_unico =".$tipo);
            $gg = $con->Listar("SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_empleado_tipo et ON e.id_unico = et.empleado 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE et.tipo = $tipo 
                AND n.periodo = $periodo      ");
             #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo    
                AND et.tipo  = $tipo   
                AND c.clase IN(1,2,3,4,5,6)");
            $filas = 200 / $ncon[0][0] ;
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(331,5, utf8_decode('TIPO EMPLEADO: '.$nu[0][1]),1,0,'L');
            $pdf->Ln(5);
            #*GG
            $totalsalarioa = 0;
            for ($g=0; $g < count($gg); $g++) { 
                $id_gg = $gg[$g][0];
                $pdf->SetFont('Arial','BI',8);
                $pdf->Cell(331,5, utf8_decode('GRUPO GESTIÓN: '.$gg[$g][1]),1,0,'L');
                $pdf->Ln(5);
                
                $filas = 200 / $ncon[0][0] ;
                $pdf->SetFont('Arial','B',7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(15,5, utf8_decode('N°'),0,0,'C');
                $pdf->Cell(15,5, utf8_decode('Cédula'),0,0,'C');
                $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
              
                $h2 = 0;
                $h  = 0;
                $alto = 0;
                $rowcn = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        LOWER(c.descripcion),
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                    WHERE t.compania = $compania 
                    AND n.periodo = $periodo     
                    AND et.tipo  = $tipo     
                    AND c.clase IN(1,2,3,4,5,6) 
                    AND c.orden IS NOT NULL
                    ORDER BY CAST(c.orden as UNSIGNED)");

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
                $pdf->Cell(15,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
               
                for ($c = 0; $c < count($rowcn); $c++) {
                    $x =$pdf->GetX();
                    $y =$pdf->GetY(); 
                    $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
                    $pdf->SetXY($x+$filas,$y);
                }
                $pdf->Ln($alto);
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
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                WHERE e.id_unico !=2 AND t.compania = $compania 
                AND  n.periodo = $periodo 
                AND et.tipo  = $tipo     
                AND e.grupogestion = $id_gg  
                AND (c.clase IN(1,2,3,4,5)  ) 
                ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
                $pdf->SetFont('Arial','',7);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) { 
                    $pdf->Cellfitscale(15,8, utf8_decode($e+1),1,0,'L');
                    $pdf->Cellfitscale(15,8, utf8_decode($rowe[$e][4]),1,0,'L');
                    $pdf->Cellfitscale(50,8, ($rowe[$e][5]),1,0,'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                        valor FROM gn_novedad 
                        WHERE empleado = ".$rowe[$e][0]." 
                        AND concepto = '78' AND periodo = '$periodo'");

                   
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
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                    }        
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(80,8, utf8_decode('Total:'),1,0,'C');
          
                  
                
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                        WHERE c.id_unico = ".$rowcn[$c][2]." 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND et.tipo  = $tipo   
                        AND e.grupogestion = $id_gg  
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5))");
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                }
                
                $totalsalarioa +=$salarioa;
                if($g == count($gg)-1){$pdf->Ln(8);}else {$pdf->AddPage();}
            }
            $pdf->SetFont('Arial','BI',8);
            $pdf->Cell(80,8, utf8_decode('TOTAL '.$nu[0][1]),1,0,'C');
           
              
            
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND et.tipo  = $tipo   
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) )");
                if(!empty($num_con[0][1])) {
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
            }
            $pdf->Ln(8);
        }elseif(!empty($_REQUEST['sltUnidadE'])) {
            $unidad     = $_REQUEST['sltUnidadE'];
            $acumulado  = $_REQUEST['chkGG'];
            $nu = $con->Listar("SELECT id_unico, nombre FROM gn_unidad_ejecutora WHERE id_unico =".$unidad);
            
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo    
                AND e.unidadejecutora = $unidad  AND (c.clase IN(1,2,3,4,5))");
            $nconhe = $con->Listar("SELECT COUNT(DISTINCT n.concepto), GROUP_CONCAT(DISTINCT n.concepto)
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico
                WHERE t.compania = $compania 
                AND n.periodo = $periodo    
                AND e.unidadejecutora = $unidad  
                AND (cr.clase IN(9))");
            

            if(empty($nconhe[0][0])){
                $nche = 0;
                $conceptohe = 0;
            } else {
                $nche = $nconhe[0][0];
                $conceptohe = $nconhe[0][1];
            }
            $nc =($ncon[0][0]-$nche)+5;
            $filas = 320 / $nc+1;
            
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(320,5, utf8_decode('UNIDAD EJECUTORA: '.$nu[0][1]),1,0,'L');
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',7);
            if($acumulado ==1 ){
                $gg = $con->Listar("SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE e.unidadejecutora = $unidad
                AND n.periodo = $periodo    ");
                #* Grupos de gestión 
                for ($g=0; $g <count($gg) ; $g++) { 
                    $id_gg = $gg[$g][0];
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(320,5, utf8_decode('GRUPO GESTIÓN: '.$gg[$g][1]),1,0,'L');
                    $pdf->Ln(5);
                    $cx = $pdf->GetX();
                    $cy = $pdf->GetY();
                    $pdf->Cell(8,5, utf8_decode('N°'),0,0,'C');
                    $pdf->Cell(12,5, utf8_decode('Cédula'),0,0,'C');
                    $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
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
                        AND e.unidadejecutora = $unidad   
                        AND c.clase IN (1,2,3,4,5,6) 
                        AND c.id_unico NOT IN (".$conceptohe.")
                        AND c.orden IS NOT NULL
                        ORDER BY CAST(c.orden as UNSIGNED)");

                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn); $c++) {
                        
                        $x =$pdf->GetX();
                        $y =$pdf->GetY(); 
                        $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))),0,'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
                        $pdf->SetXY($x+$filas,$y);
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            $x =$pdf->GetX();
                            $y =$pdf->GetY(); 
                            $pdf->MultiCell($filas,5, utf8_decode('Total Horas Extra'),0,'C');
                            $y2 = $pdf->GetY();
                            $h = $y2 - $y;
                            if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
                            $pdf->SetXY($x+$filas,$y);;
                        }

                    }
                    $pdf->SetXY($cx,$cy);
                    $pdf->Cell(8,$alto, utf8_decode(''),1,0,'C');
                    $pdf->Cell(12,$alto, utf8_decode(''),1,0,'C');
                    $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
                    for ($c = 0; $c < count($rowcn); $c++) {
                        
                        $x =$pdf->GetX();
                        $y =$pdf->GetY(); 
                        $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
                        $pdf->SetXY($x+$filas,$y);
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            $x =$pdf->GetX();
                            $y =$pdf->GetY(); 
                            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
                            $pdf->SetXY($x+$filas,$y);
                        }
                    }
                    
                    $pdf->Ln($alto);

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
                    AND e.grupogestion = $id_gg 
                    AND (c.clase IN(1,2,3,4,5) ) 
                    ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
                    $salarioa = 0;
                    $pdf->SetFont('Arial','',7);
                    for ($e = 0; $e < count($rowe); $e++) { 
                        $pdf->Cellfitscale(8,8,($e+1),1,0,'L');
                        $pdf->Cellfitscale(12,8, utf8_decode($rowe[$e][4]),1,0,'L');
                        $pdf->Cellfitscale(50,8, ($rowe[$e][5]),1,0,'L');

                        $x =$pdf->GetX();  
                        $y =$pdf->GetY();
                        for ($c = 0; $c < count($rowcn); $c++) {
                            
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                                AND n.periodo = $periodo ");
                            if(!empty($num_con[0][1])) {
                                $valor = $num_con[0][1];
                            }else{
                                $valor =0;
                            }
                            $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                            if(ucwords($rowcn[$c][1])=='Sueldo'){
                                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                    FROM gn_novedad n 
                                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    WHERE c.id_unico IN (".$conceptohe.") AND e.id_unico = ".$rowe[$e][0]." 
                                    AND n.periodo = $periodo ");
                                if(!empty($num_con[0][1])) {
                                    $valor = $num_con[0][1];
                                }else{
                                    $valor =0;
                                }
                                $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                            }
                        }        
                        $pdf->Ln(8);
                    }
                    $pdf->SetFont('Arial','BI',8);
                    $pdf->Cell(70,8, utf8_decode('TOTAL GRUPO GESTIÓN:'),1,0,'C');

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
                            AND e.grupogestion = $id_gg ");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                                WHERE c.id_unico IN (".$conceptohe.")  
                                AND n.periodo = $periodo 
                                AND e.id_unico !=2 
                                AND t.compania = $compania 
                                AND e.unidadejecutora = $unidad   
                                AND e.grupogestion = $id_gg ");
                            if(!empty($num_con[0][1])) {
                                $valor = $num_con[0][1];
                            }else{
                                $valor =0;
                            }
                            $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                        }
                    }
                    $pdf->Ln(8);
                    if($g ==count($gg)-1){} else {$pdf->AddPage();}
                }
                $pdf->SetFont('Arial','BI',8);
                $pdf->Cell(70,8, utf8_decode('TOTAL UNIDAD EJECUTORA'),1,0,'C');

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
                        AND e.unidadejecutora = $unidad  ");
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico IN (".$conceptohe.")  
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad  ");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                    }
                }
                
            } else {
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(8,5, utf8_decode('N°'),0,0,'C');
                $pdf->Cell(12,5, utf8_decode('Cédula'),0,0,'C');
                $pdf->Cell(50,5, utf8_decode('Nombre'),0,0,'C');
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
                    AND e.unidadejecutora = $unidad   
                    AND c.clase IN (1,2,3,4,5,6) 
                    AND c.id_unico NOT IN (".$conceptohe.")
                    AND c.orden IS NOT NULL
                    ORDER BY CAST(c.orden as UNSIGNED)");
                    
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    
                    $x =$pdf->GetX();
                    $y =$pdf->GetY(); 
                    $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))),0,'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
                    $pdf->SetXY($x+$filas,$y);
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        $x =$pdf->GetX();
                        $y =$pdf->GetY(); 
                        $pdf->MultiCell($filas,5, utf8_decode('Total Horas Extra'),0,'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
                        $pdf->SetXY($x+$filas,$y);;
                    }

                }
                $pdf->SetXY($cx,$cy);
                $pdf->Cell(8,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(12,$alto, utf8_decode(''),1,0,'C');
                $pdf->Cell(50,$alto, utf8_decode(''),1,0,'C');
                for ($c = 0; $c < count($rowcn); $c++) {
                    
                    $x =$pdf->GetX();
                    $y =$pdf->GetY(); 
                    $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
                    $pdf->SetXY($x+$filas,$y);
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        $x =$pdf->GetX();
                        $y =$pdf->GetY(); 
                        $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
                        $pdf->SetXY($x+$filas,$y);
                    }
                }
                
                $pdf->Ln($alto);

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
                AND (c.clase IN(1,2,3,4,5) ) 
                ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
                $salarioa = 0;
                $pdf->SetFont('Arial','',7);
                for ($e = 0; $e < count($rowe); $e++) { 
                    $pdf->Cellfitscale(8,8,($e+1),1,0,'L');
                    $pdf->Cellfitscale(12,8, utf8_decode($rowe[$e][4]),1,0,'L');
                    $pdf->Cellfitscale(50,8, ($rowe[$e][5]),1,0,'L');

                    $x =$pdf->GetX();  
                    $y =$pdf->GetY();
                    for ($c = 0; $c < count($rowcn); $c++) {
                        
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                            AND n.periodo = $periodo ");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico IN (".$conceptohe.") AND e.id_unico = ".$rowe[$e][0]." 
                                AND n.periodo = $periodo ");
                            if(!empty($num_con[0][1])) {
                                $valor = $num_con[0][1];
                            }else{
                                $valor =0;
                            }
                            $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                        }
                    }        
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial','BI',8);
                $pdf->Cell(70,8, utf8_decode('TOTAL UNIDAD EJECUTORA:'),1,0,'C');

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
                        AND e.unidadejecutora = $unidad ");
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico IN (".$conceptohe.")  
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        $pdf->Cellfitscale($filas,8, number_format($valor, 0, '.',','),1,0,'R');  
                    }
                }
                $pdf->Ln(8);
                if($g ==count($gg)-1){} else {$pdf->AddPage();}
            }
            
        }
        $pdf->Ln(10);
          
        

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
        WHERE td.nombre = 'Sabana Nomina'
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
                    $pdf->SetXY(140, $yyy);
                    $pdf->Cell(60, 0, '', 'B');
                    $pdf->Ln(3);
                    $pdf->SetX(140);
                    $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                    $pdf->Ln(5);
                    $pdf->SetX(140);
                    $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
                    $pdf->Ln(30);
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
        $pdf->Output(0,'Sabana_Nomina('.date('d/m/Y').').pdf',0);     
    }
    #******** Tipo Excel *************#
     else{
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Sabana_Nomina.xls");  
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<title>Sabana Nomina</title>';
        echo '</head>';
        echo '<body>';
        echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
        
        if(empty($_REQUEST['sltUnidadE']) && empty($_REQUEST['sltTipoE'])){
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo     
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc =$ncon[0][0]+4;
            echo '<th colspan="'.$nc.'" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>'.$razonsocial;
            echo '<br/>'.$nombreIdent.': '.$numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:'.$nperiodo;
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
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            echo '<tr>';
            echo '<td><strong>Cod Int</strong></td>';
            echo '<td><strong>Nombre</strong></td>';
         
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
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5 OR c.clase = 6) 
                AND c.orden IS NOT NULL
                ORDER BY CAST(c.orden as UNSIGNED)");
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
            AND (c.clase IN(1,2,3,4,5) ) 
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
           
                $salarioa += $rowe[$e][6];
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                        AND n.periodo = $periodo ");
                    if(!empty($num_con[0][1])) {
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
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) )");
                if(!empty($num_con[0][1])) {
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
            }
            echo '</tr>';
        } elseif(!empty($_REQUEST['sltTipoE'])) {
            #Tipo 
            $tipo = $_REQUEST['sltTipoE'];
            $nu = $con->Listar("SELECT id_unico, nombre FROM gn_tipo_empleado WHERE id_unico =".$tipo);
            $gg = $con->Listar("SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_empleado_tipo et ON e.id_unico = et.empleado 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE et.tipo = $tipo 
                AND n.periodo = $periodo     ");
             #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo    
                AND et.tipo  = $tipo
                AND c.clase IN(1,2,3,4,5,6)");
            $nc =$ncon[0][0]+4;
            echo '<th colspan="'.$nc.'" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>'.$razonsocial;
            echo '<br/>'.$nombreIdent.': '.$numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:'.$nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr><td colspan="'.$nc.'"><strong>TIPO EMPLEADO: '.$nu[0][1].'</strong></td></tr>  ';
            #*GG
            $totalsalarioa=0;
            for ($g=0; $g < count($gg); $g++) { 
                $id_gg = $gg[$g][0];
                echo '<tr><td colspan="'.$nc.'"><strong>GRUPO GESTIÓN: '.$gg[$g][1].'</strong></td></tr>  ';
                
                echo '<tr>';
                echo '<td><strong>Cod Int</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
             
                #**** Nombre de conceptos ****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        LOWER(c.descripcion),
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                    WHERE t.compania = $compania 
                    AND n.periodo = $periodo    
                    AND et.tipo  = $tipo     
                    AND c.clase IN(1,2,3,4,5,6)   
                    AND c.orden IS NOT NULL
                    ORDER BY CAST(c.orden as UNSIGNED)");
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
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                WHERE e.id_unico !=2 AND t.compania = $compania 
                AND  n.periodo = $periodo 
                AND et.tipo  = $tipo     
                AND e.grupogestion = $id_gg  
                AND (c.clase IN(1,2,3,4,5)  ) 
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
                   
                    $salarioa += $rowe[$e][6];
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                            AND n.periodo = $periodo ");
                        if(!empty($num_con[0][1])) {
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
               
                
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                        WHERE c.id_unico = ".$rowcn[$c][2]." 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND et.tipo  = $tipo   
                        AND e.grupogestion = $id_gg  
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) )");
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
                }
                $totalsalarioa +=$salarioa;
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td colspan="2"><strong><i>TOTAL TIPO EMPLEADO: '.$nu[0][1].'</i></strong></td>';

            
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND et.tipo  = $tipo   
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) )");
                if(!empty($num_con[0][1])) {
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
            }
            echo '</tr>';
        } elseif(!empty($_REQUEST['sltUnidadE'])) {
            #Unidad Ejecutora
            $unidad     = $_REQUEST['sltUnidadE'];
            $acumulado  = $_REQUEST['chkGG'];
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
                AND (c.clase IN(1,2,3,4,5))");
            $nconhe = $con->Listar("SELECT COUNT(DISTINCT n.concepto), GROUP_CONCAT(DISTINCT n.concepto)
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico
                WHERE t.compania = $compania 
                AND n.periodo = $periodo    
                AND e.unidadejecutora = $unidad  
                AND (cr.clase IN(9))");
            if(empty($nconhe[0][0])){
                $nche = 0;
                $conceptohe = 0;
            } else {
                $nche = $nconhe[0][0];
                $conceptohe = $nconhe[0][1];
            }

            $nc =($ncon[0][0]-$nche)+5;
            echo '<th colspan="'.$nc.'" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>'.$razonsocial;
            echo '<br/>'.$nombreIdent.': '.$numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:'.$nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr><td colspan="'.$nc.'"><strong>UNIDAD EJECUTORA: '.$nu[0][1].'</strong></td></tr>  ';
            
            #***************************************************************#
            $totalsa = 0;
            if($acumulado ==1 ){
                $gg = $con->Listar("SELECT DISTINCT gg.id_unico, gg.nombre FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE e.unidadejecutora = $unidad
                AND n.periodo = $periodo ");
                #* Grupos de gestión 
                for ($g=0; $g <count($gg) ; $g++) { 
                    $id_gg = $gg[$g][0];
                    echo '<tr><td colspan="'.$nc.'"><strong>GRUPO GESTIÓN: '.$gg[$g][1].'</strong></td></tr>  ';
                   
                    #**** Numero de conceptos ****#
                    echo '<tr>';
                    echo '<td><strong>Consecutivo</strong></td>';
                    echo '<td><strong>N° Ident.</strong></td>';
                    echo '<td><strong>Nombre</strong></td>';
                   
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
                        AND e.unidadejecutora = $unidad   
                        AND c.clase IN (1,2,3,4,5,6) 
                        AND c.id_unico NOT IN (".$conceptohe.")
                        AND c.orden IS NOT NULL
                        ORDER BY CAST(c.orden as UNSIGNED)");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn); $c++) {
                        
                        echo '<td><strong>'.utf8_encode(ucwords($rowcn[$c][1])).'</strong></td>';
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            echo '<td><strong>Total Horas Extra</strong></td>';
                        }

                    }
                    echo '</tr>';
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
                    AND e.grupogestion = $id_gg 
                    AND (c.clase IN(1,2,3,4,5) ) 
                    ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) { 
                        echo '<tr>';
                        echo '<td>'.($e+1).'</td>';
                        echo '<td align= "left">'.($rowe[$e][4]).'</td>';
                        echo '<td>'.utf8_encode($rowe[$e][5]).'</td>';
                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                            valor FROM gn_novedad 
                            WHERE empleado = ".$rowe[$e][0]." 
                            AND concepto = '78' AND periodo = '$periodo'");
                       
                        $salarioa += $rowe[$e][6];
                        for ($c = 0; $c < count($rowcn); $c++) {
                            
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                                AND n.periodo = $periodo ");
                            if(!empty($num_con[0][1])) {
                                $valor = $num_con[0][1];
                            }else{
                                $valor =0;
                            }
                            echo '<td>'.number_format($valor,0,'.',',').'</td>';
                            if(ucwords($rowcn[$c][1])=='Sueldo'){
                                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                    FROM gn_novedad n 
                                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    WHERE c.id_unico IN (".$conceptohe.") AND e.id_unico = ".$rowe[$e][0]." 
                                    AND n.periodo = $periodo ");
                                if(!empty($num_con[0][1])) {
                                    $valor = $num_con[0][1];
                                }else{
                                    $valor =0;
                                }
                                echo '<td>'.number_format($valor,0,'.',',').'</td>';
                            }
                        }        
                        echo '</tr>';
                    }                
                    echo '<tr>';
                    echo '<td colspan="3"><strong>Total</strong></td>';
                 
                    $totalsa += $salarioa;
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
                            AND e.grupogestion = $id_gg ");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                                WHERE c.id_unico IN (".$conceptohe.")  
                                AND n.periodo = $periodo 
                                AND e.id_unico !=2 
                                AND t.compania = $compania 
                                AND e.unidadejecutora = $unidad   
                                AND e.grupogestion = $id_gg ");
                            if(!empty($num_con[0][1])) {
                                $valor = $num_con[0][1];
                            }else{
                                $valor =0;
                            }
                            echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="3"><strong><i>TOTAL UNIDAD EJECUTORA</i></strong></td>';
               
                
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
                        AND e.unidadejecutora = $unidad ");
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    echo '<td><strong><i>'.number_format($valor,0,'.',',').'</i></strong></td>'; 
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico IN (".$conceptohe.")  
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        echo '<td><strong><i>'.number_format($valor,0,'.',',').'</i></strong></td>';
                    }
                }
                echo '</tr>';


            } else {
                #**** Numero de conceptos ****#
                echo '<tr>';
                echo '<td><strong>Consecutivo</strong></td>';
                echo '<td><strong>N° Ident.</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
             
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
                AND e.unidadejecutora = $unidad   
                AND c.clase IN (1,2,3,4,5,6) 
                AND c.id_unico NOT IN (".$conceptohe.")
                AND c.orden IS NOT NULL
                 ORDER BY CAST(c.orden as UNSIGNED)");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    
                    echo '<td><strong>'.utf8_encode(ucwords($rowcn[$c][1])).'</strong></td>';
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        echo '<td><strong>Total Horas Extra</strong></td>';
                    }
                }
                echo '</tr>';
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
                AND (c.clase IN(1,2,3,4,5) ) 
                ORDER BY CONCAT_WS(' ',t.apellidouno,t.apellidodos,t.nombreuno,t.nombredos) ASC ");
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) { 
                    echo '<tr>';
                    echo '<td>'.($e+1).'</td>';
                    echo '<td align= "left">'.($rowe[$e][4]).'</td>';
                    echo '<td>'.utf8_encode($rowe[$e][5]).'</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                        valor FROM gn_novedad 
                        WHERE empleado = ".$rowe[$e][0]." 
                        AND concepto = '78' AND periodo = '$periodo'");
                  
                    $salarioa += $rowe[$e][6];
                    for ($c = 0; $c < count($rowcn); $c++) {
                        
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                            AND n.periodo = $periodo ");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        echo '<td>'.number_format($valor,0,'.',',').'</td>';
                        if(ucwords($rowcn[$c][1])=='Sueldo'){
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico IN (".$conceptohe.") AND e.id_unico = ".$rowe[$e][0]." 
                                AND n.periodo = $periodo ");
                            if(!empty($num_con[0][1])) {
                                $valor = $num_con[0][1];
                            }else{
                                $valor =0;
                            }
                            echo '<td>'.number_format($valor,0,'.',',').'</td>';
                        }
                    }        
                    echo '<td></td>';
                    echo '</tr>';
                }                
                echo '<tr>';
                echo '<td colspan="3"><strong>Total</strong></td>';
               
                
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
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) )");
                    if(!empty($num_con[0][1])) {
                        $valor = $num_con[0][1];
                    }else{
                        $valor =0;
                    }
                    echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>'; 
                    if(ucwords($rowcn[$c][1])=='Sueldo'){
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico IN (".$conceptohe.")  
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad   ");
                        if(!empty($num_con[0][1])) {
                            $valor = $num_con[0][1];
                        }else{
                            $valor =0;
                        }
                        echo '<td><strong>'.number_format($valor,0,'.',',').'</strong></td>';
                    }
                }
                echo '</tr>';
            }
        }


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
        WHERE td.nombre = 'Sabana Nomina'
        ORDER BY rd.orden ASC";

        $fi = $mysqli->query($firmas);
        $xxx = 0;
        echo '<tr>';
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
                echo '<td colspan="3"><strong>';
                echo '<br/>&nbsp;';
                echo '<br/>&nbsp;';
                echo '<br/>&nbsp;';
                echo '<br/>'.$row_firma[0];
                echo '<br/>'.$row_firma[1];
                echo '<strong></td>';

            }
        }    
        echo '</tr>';
        echo '</table>';
        echo '</body>';
    }
    ?>
