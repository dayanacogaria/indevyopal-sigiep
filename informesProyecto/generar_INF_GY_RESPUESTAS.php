<?php
    session_start();
        
    require'../fpdf/fpdf.php';
    require'../Conexion/conexion.php';

    ini_set('max_execution_time', 360);
    $compania   = $_SESSION['compania'];
    $usuario    = $_SESSION['usuario'];
    
    $id_clase   = $_REQUEST['idCP'];
    $id_pr      = $_REQUEST['idPR'];
    
    ob_start();
    
    #TRAE LA RUTA DEL LOGO DE LA COMPAÑIA
    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
      FROM gf_tercero ter 
      LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
      WHERE ter.id_unico = '.$compania;

    $rutaLogo = $mysqli->query($sqlRutaLogo);
    $rowLogo = mysqli_fetch_array($rutaLogo);
    $ruta = $rowLogo[0];
    $ciudadCompania = $rowLogo[1];

    
    #TRAE LOS DATOS DE LA COMPAÑIA
    $consulta = "SELECT         lower(t.razonsocial) as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum,
                                t.ciudadidentifiCacion ciu,
                                c.nombre nomc
                FROM gf_tercero t
                LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                LEFT JOIN gf_ciudad c ON t.ciudadidentificacion = c.id_unico
                WHERE t.id_unico = $compania";

    $cmp = $mysqli->query($consulta);

    $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    $ciudad = "";
    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp    = $fila['traz'];       
        $tipodoc    = utf8_decode($fila['tnom']);       
        $numdoc     = utf8_decode($fila['tnum']); 
        $ciudad     = $fila['nomc'];
        $idciudad   = $fila['ciu'];

      
    }

   
    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode("-", $hoy);
    $anioh = $fecha_div[2];
    $mesh = $fecha_div[1];
    $diah = $fecha_div[0];
    $hoy = $diah.'/'.$mesh.'/'.$anioh;


    $per = "";
    $emp = "";
    $codi = "";


    class PDF extends FPDF
    {
    // Cabecera de página  
    function Header()
    { 
        
        
        global $nomcomp;
        global $tipodoc;
        global $numdoc;
        global $per;
        global $emp;
        global $codi;
        global $ruta;
        global $valor;
        global $codcon;
        global $descon;
        global $numeroP; 
        global $CO;
        global $TF;
        global $PER;
        global $FI;
        global $FF;
        global $GRUP;
      
        

        $numeroP = $this->PageNo();

        if($ruta != '')
        {
          $this->Image('../'.$ruta,20,8,15);
        } 
        // Logo
        //$this->Image('logo_pb.png',10,8,33);
        //Arial bold 10
        $this->SetFont('Arial','B',12);
        
            // Título
        
        $this->SetX(20);
        $this->Cell(170,5,utf8_decode(ucwords($nomcomp)),0,0,'C');
        // Salto de línea
        $this->Ln(5);
        
        $this->SetFont('Arial','',9);
        $this->SetX(20);
        $this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
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
                    $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                    $this->Cell(10,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                    $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
                }
            }


    // Creación del objeto de la clase heredada
    $pdf = new PDF('P','mm','letter');   



    $nb=$pdf->AliasNbPages();

    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    #TRAE EL NOMBRE DE LA CLASE DE PREGUNTA DE LA COMPAÑIA
    $ClaseP = "SELECT id_unico , nombre FROM gy_clase_pregunta WHERE md5(id_unico)= '$id_clase'";
    $ClasPr = $mysqli->query($ClaseP);
    $CP     = mysqli_fetch_row($ClasPr); 
    
    #COSULTA EL NOMBRE DE LOS TIPO DE PREGUNTAS DE LA CLASE
    $informacion = "SELECT nombre,
                                id_unico
                    FROM gy_tipo_pregunta 
                    WHERE compania = '$compania'  AND id_clase_pregunta = '$CP[0]'";
    
    $inform = $mysqli->query($informacion);
    


    $pdf->SetFont('Arial','B',10);
    $pdf->SetX(13);
    $pdf->Cell(190,6, utf8_decode(strtoupper($CP[1])),1,0,'C');
    $pdf->Ln(6);
    
    $X = 1;
    while($inf = mysqli_fetch_row($inform)){
        
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(13);
        $pdf->Cell(190,6, utf8_decode(strtoupper($X.'. '.$inf[0])),1,0,'C');
        $pdf->Ln(6);
        $Y = 1;
        
        #COSULTA TODA LA INFORMACION DE LAS RESPUESTAS DE LA COMPAÑIA Y DE LA CLASE
        $respuestas = "SELECT p.nombre, rp.respuesta FROM gy_respuesta_pregunta rp
                        LEFT JOIN gy_pregunta p ON rp.id_pregunta = p.id_unico
                        LEFT JOIN gy_tipo_pregunta tp ON p.id_tipo_pregunta = tp.id_unico
                        WHERE rp.compania = '$compania' AND p.id_tipo_pregunta = '$inf[1]' AND md5(rp.id_proyecto) = '$id_pr '";
        
        $respues = $mysqli->query($respuestas);
        
        $h2 = 0; 
        $pdf->SetX(13);
        while($res = mysqli_fetch_row($respues)){
            $pdf->SetFont('Arial','',9);
            $pdf->SetX(13);
            $x =$pdf->GetX();
            $y1 =$pdf->GetY(); 
            $pdf->MultiCell(75,6, utf8_decode(strtoupper($X.'. '.$Y.'. '.$res[0])),0,'L');
            $y3 =$pdf->GetY(); 
            $pdf->SetXY(88,$y1);
            $pdf->MultiCell(115,6, utf8_decode(strtoupper($res[1])),0,'L');
            $y2 =$pdf->GetY(); 
            
            if($y3 > $y2){
                $yalto = $y3;
            }else{
                $yalto = $y2;
            }
            $h = $yalto - $y1;
            
            if($h > $h2){
                $alto = $h;
                $h2 = $h;
            }
            echo "y1: ".$y1;
            echo "<br/>";
            echo "y2: ".$y2;
            echo "<br/>";
            echo "alto: ".$alto;
            echo "<br/>";
            $pdf->SetXY(13,$y1);
            $pdf->Cell(75,$alto, utf8_decode(''),1,'C');
            $pdf->Cell(115,$alto, utf8_decode(''),1,'C');
            
            $pdf->Ln($alto);  
            $Y++;
        }
        $X++;
    }
    

    ob_end_clean();
    $pdf->Output(0,'Declaracion_Industria_y_Comercio ('.date('d/m/Y').').pdf',0);  
?>