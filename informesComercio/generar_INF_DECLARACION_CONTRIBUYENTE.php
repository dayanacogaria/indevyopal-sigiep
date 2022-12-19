<?php
#######################################################################################################
#11/01/2019 |LORENA M. | Creación del informe de Declaración por contribuyente y ajustes a las celdas del PDF.
#######################################################################################################
    session_start();
    
    require'../fpdf/fpdf.php';
    require'../Conexion/conexion.php';

    ini_set('max_execution_time', 360);
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];
    $id = $_GET['id'];        
    ob_start();
    
    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
      FROM gf_tercero ter 
      LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
      WHERE ter.id_unico = '.$compania;

    $rutaLogo = $mysqli->query($sqlRutaLogo);
    $rowLogo = mysqli_fetch_array($rutaLogo);
    $ruta = $rowLogo[0];
    $ciudadCompania = $rowLogo[1];


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

        echo $departamento = "SELECT  d.nombre FROM gf_departamento d LEFT JOIN gf_ciudad c ON c.departamento = d.id_unico WHERE c.id_unico = '$idciudad' ";
        $departa = $mysqli->query($departamento);
        $dep = mysqli_fetch_row($departa);
    }
    

    $consulta1 = "SELECT d.id_unico,
                         d.cod_dec,
                         DATE_FORMAT(d.fecha, '%d/%m/%Y'),
                         d.contribuyente,
                         d.periodo,
                         d.vigencia,
                         d.tipo_per,
                         d.tipo_dec               
            FROM gc_declaracion d                
            WHERE md5(d.id_unico) = '$id' ";

    $resultado = $mysqli->query($consulta1);
    $row = mysqli_fetch_row($resultado);
    $iddec = $row[0];

    $consulta2 = "SELECT COUNT(id_unico)
                  FROM gc_establecimiento
                  WHERE contribuyente = '$row[3]'";

    $resultado2 = $mysqli->query($consulta2);
    $rowE = mysqli_fetch_row($resultado2);
                                              


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
        //$this->Cell(170,5,utf8_decode(ucwords($nomcomp)),0,0,'C');
        // Salto de línea
       $this->Ln(1);
        
        $this->SetFont('Arial','',8);
        $this->SetX(20);
        //$this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
        $this->Ln(1);
        $this->SetFont('Arial','B',10);
        $this->Cell(190,5,utf8_decode('FORMULARIO UNICO NACIONAL DE DECLARACION Y'),0,0,'C');
        $this->Ln(4);
        $this->Cell(190,5,utf8_decode('PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO'),0,0,'C');
        // Salto de línea
        $this->SetX(20);
        $this->Image('../logo/logo_colombia.png',180,8,15);
        $this->Ln(10);
        
        
        }
        // Pie de página
            function Footer()
                {
                // Posición: a 1,5 cm del final
                global $hoy;
                global $usuario;
                $this->SetY(-12);
                // Arial italic 8
                $this->SetFont('Arial','B',7);
                    $this->SetX(6);
                    $this->Cell(30,6,utf8_decode('Fecha: '.$hoy),0,0,'L');
                    $this->Cell(90,6,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                    $this->Cell(10,6,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                    $this->Cell(65,6,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
                }
            }


    // Creación del objeto de la clase heredada
    $pdf = new PDF('P','mm','legal');   



    $nb=$pdf->AliasNbPages();

    $pdf->AddPage();
    $pdf->AliasNbPages();

    $perD       = $row[6]; 
    $TiDE       = $row[7];
    $c          = $row[3];
    $p          = $row[4];
    $vf         = $row[5];
    $fecha_dec  = $row[2];
    $numD       = $id;
    $Nest       = $rowE[0];

    $pdf->SetFont('Arial','',8);

    $contribu = "SELECT IF(CONCAT_WS(' ',
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
                             tr.apellidodos)) AS NOMBRE,
                             tr.tipoidentificacion,
                             tr.numeroidentificacion,
                             c.codigo_mat,
                             tr.email,
                             c.telefono,
                             c.dir_correspondencia
                FROM gf_tercero tr
                
                LEFT JOIN gc_contribuyente c ON c.tercero = tr.id_unico 
                WHERE c.id_unico = '$c'";

    $contri = $mysqli->query($contribu);
    $Cont = mysqli_fetch_row($contri);
    
    if($TiDE == 1){
        $INI = "X";
        $SP  = "";
        $COR = "";
    }elseif($TiDE == 2){
        $INI = "";
        $SP  = "X";
        $COR = "";
    }else{
        $INI = "";
        $SP  = "";
        $COR = "X";
    }

    $periodoG = "SELECT vigencia FROM  gc_anno_comercial WHERE id_unico = '$p' ";
    $periodo = $mysqli->query($periodoG);
    $perG = mysqli_fetch_row($periodo);

    $pdf->Cell(40,5, utf8_decode('MUNICIPIO O DISTRITO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(35,5, utf8_decode($ciudad),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(40,5, utf8_decode('DEPARTAMENTO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(35,5, utf8_decode($dep[0]),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,5, utf8_decode('N° MATRICULA'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5, utf8_decode($Cont[3]),1,0,'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,8, utf8_decode('AÑO GRAVABLE'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(14,8, utf8_decode($perG[0]),1,0,'C');
    $pdf->SetFont('Arial','',7);
    $pdf->Cell(46,4, utf8_decode('SOLAMENTE PARA BOGOTÁ,'),'LTR',0,'C');
    $pdf->SetFont('Arial','',6);
    $pdf->Cell(15,4, utf8_decode('ENE - FEB'),'LT',0,'C');
    $pdf->Cell(15,4, utf8_decode('MAR - ABR'),'T',0,'C');
    $pdf->Cell(15,4, utf8_decode('MAY - JUN'),'T',0,'C');
    $pdf->Cell(15,4, utf8_decode('JUL - AGO'),'T',0,'C');
    $pdf->Cell(15,4, utf8_decode('SEP - OCT'),'T',0,'C');
    $pdf->Cell(15,4, utf8_decode('NOV - DIC'),'TR',0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(20,4, utf8_decode('ANUAL'),'TLR',0,'C');
    $pdf->Ln(4);
   
    $pdf->SetFont('Arial','',7);
    $pdf->Cell(44,4, utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(46,4, utf8_decode('marque el Bimestre o Periodo:'),'BR',0,'C');
    $pdf->SetFont('Arial','',6);
    $pdf->Cell(15,4, utf8_decode('[    ]'),'L',0,'C');
    $pdf->Cell(15,4, utf8_decode('[    ]'),'',0,'C');
    $pdf->Cell(15,4, utf8_decode('[    ]'),'',0,'C');
    $pdf->Cell(15,4, utf8_decode('[    ]'),'',0,'C');
    $pdf->Cell(15,4, utf8_decode('[    ]'),'',0,'C');
    $pdf->Cell(15,4, utf8_decode('[    ]'),'R',0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,4, utf8_decode('X'),'LRB',0,'C');
    $pdf->Ln(4);
    $pdf->SetFont('Arial','',8);

    $pdf->Cell(20,5, utf8_decode('DEC INICIAL'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(10,5, utf8_decode($INI),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(20,5, utf8_decode('SOLO PAGO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(10,5, utf8_decode($SP),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(20,5, utf8_decode('CORRECCION'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(10,5, utf8_decode($COR),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(33,5, utf8_decode('N° DEC QUE CORRIGE'),1,0,'C');
    $pdf->Cell(22,5, utf8_decode(''),1,0,'C');
    $pdf->Cell(35,5, utf8_decode('FECHA DECLARACION'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5, utf8_decode($fecha_dec),1,0,'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('A. INFORMACION DEL CONTRIBUYENTE'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);

    
    if($Cont[1] == 1){
        $CC     = "X";
        $NIT    = "";
        $OTRO   = "";
    }elseif($Cont[1] == 2){
        $CC     = "";
        $NIT    = "X";
        $OTRO   = "";
    }else{
        $CC     = "";
        $NIT    = "";
        $OTRO   = "X";
    }
    $pdf->Cell(60,5, utf8_decode('NOMBRES Y APELLIDOS O RAZON SOCIAL'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(75,5, utf8_decode($Cont[0]),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(7,5, utf8_decode('CC'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(5,5, utf8_decode($CC),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(7,5, utf8_decode('NIT'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(5,5, utf8_decode($NIT),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(9,5, utf8_decode('OTRO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(5,5, utf8_decode($OTRO),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(5,5, utf8_decode('N°'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(22,5, utf8_decode(number_format($Cont[2],0,',','.')),1,0,'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(60,5, utf8_decode('DIRECCION DE NOTIFICACION'),1,0,'C');  
    $pdf->Cell(140,5, utf8_decode($Cont[6]),1,0,'C'); 
    $pdf->Ln(5); 
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(60,5, utf8_decode('MUNICIPIO O DISTRITO DE NOTIFICACION'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,5, utf8_decode(''),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(40,5, utf8_decode('DEPARTAMENTO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,5, utf8_decode(''),1,0,'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,5, utf8_decode('TELEFONO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(40,5, utf8_decode($Cont[5]),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(40,5, utf8_decode('CORREO ELECTRONICO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(90,5, utf8_decode($Cont[4]),1,0,'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(40,5, utf8_decode('N° ESTABLECIMIENTOS'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5, utf8_decode($Nest),1,0,'C');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(60,5, utf8_decode('CLASIFICACION CONTRIBUYENTE'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(80,5, utf8_decode(''),1,0,'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('B. BASE GRAVABLE'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);
   
    $sqlBG1 = "SELECT cc.id_unico,cc.descripcion FROM gc_concepto_comercial cc LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo WHERE tc.id_unico = 1 ORDER BY cc.codigo ASC";
    $qBG1=$mysqli->query($sqlBG1);

    $X = 8;
    $totalVlrcon = 0;   

    while($BG1 = mysqli_fetch_row($qBG1)){
        
      $sqlBG = "SELECT cc.id_unico,
                     cc.descripcion,
                     tc.nombre,                
                     cc.apli_descu, 
                     cc.apli_inte,
                     dd.valor                    
               FROM `gc_detalle_declaracion` dd 
               LEFT JOIN `gc_concepto_comercial` cc ON dd.concepto = cc.id_unico
               LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
               WHERE md5(dd.declaracion) = '$id' and tc.id_unico = 1
               and cc.id_unico = '$BG1[0]'
               GROUP BY cc.id_unico "; 

         $qBG=$mysqli->query($sqlBG);
         $BG = mysqli_fetch_row($qBG);
         if(!empty($BG)){
           $vlrtot = $BG[5];
         }else{
            $vlrtot = 0;
         }        

         $pdf->SetFont('Arial','',8);
         $pdf->Cell(10,5, utf8_decode($X),1,0,'R');
         $pdf->Cell(140,5, utf8_decode($BG1[1]),1,0,'L');
         $pdf->SetFont('Arial','B',8);
         $pdf->Cell(50,5, utf8_decode(number_format($vlrtot,0,',','.')),1,0,'R');       
         $pdf->Ln(5);           
         $totalVlrcon = number_format($vlrtot,0,',','.');
         $X = $X + 1;

         }    
    
     
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('C. DISCRIMINACION DE INGRESOS GRAVADOS Y ACTIVIDADES DESARROLLADAS EN ESTE MUNICIPIO O DISTRITO'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','B',8);

    $pdf->SetFont('Arial','',8);
    $pdf->Cell(100,5, utf8_decode('ACTIVIDADES GRAVADAS'),1,0,'C');
    $pdf->Cell(15,5, utf8_decode('CODIGO'),1,0,'C');
    $pdf->Cell(35,5, utf8_decode('INGRESOS GRAVADOS'),1,0,'C');
    $pdf->Cell(15,5, utf8_decode('TARIFA'),1,0,'C');
    $pdf->Cell(35,5, utf8_decode('IMP. DE IND. Y COMER.'),1,0,'C');
    $pdf->Ln(5);
  
    $actividad1 = "SELECT DISTINCT ac.id_unico, ac.descripcion, ac.codigo
                FROM gc_actividad_contribuyente aco
                left join gc_actividad_comercial ac 
                on aco.actividad = ac.id_unico 
                where contribuyente = '$c'
                order by aco.fechainicio DESC, ac.codigo ASC";
     $activi1 =  $mysqli->query($actividad1);            

    $x =$pdf->GetX();
    $y =$pdf->GetY();   
    $cx = $pdf->GetX();
    $cy = $pdf->GetY(); 
    $h2 = 0; 
    $alto = 0 ;
    $ac = 1;
    $totImp = 0;
    $imp = 0;
    while($ACT1 = mysqli_fetch_row($activi1)){
        
    $actividad = "SELECT DISTINCT ac.id_unico, 
                 ac.codigo,
                 ac.descripcion, 
                 ta.tarifa, 
                 di.valor, 
                 ROUND(((di.valor*ta.tarifa)/1000)/1000)*1000
                FROM gc_actividad_comercial ac 
                LEFT JOIN gc_actividad_contribuyente aco
                ON aco.actividad = ac.id_unico 
                LEFT JOIN gc_declaracion d
                ON d.contribuyente = aco.contribuyente 
                LEFT JOIN gc_declaracion_ingreso di
                ON di.declaracion = d.id_unico and ac.id_unico = di.act_comer
                LEFT JOIN gc_tarifa_actividad ta
                ON ta.act_comer = ac.id_unico
                WHERE aco.contribuyente = '$c' and di.tipo_ingreso = 8 
                and d.id_unico = '$iddec' and ac.id_unico = '$ACT1[0]' 
                ORDER BY  ac.codigo ASC";

            $activi =  $mysqli->query($actividad);
           while($ACT = mysqli_fetch_row($activi)){
             if(!empty($BG)){
                   $vlrIng = $ACT[4];
                   $vlrImp  = $ACT[5];
                 }else{
                   $vlrIng = 0;
                   $vlrImp  = 0;
              }   

                $pdf->SetFont('Arial','',8);
                $x =$pdf->GetX();
                $y =$pdf->GetY();
                $pdf->MultiCell(100,6, utf8_decode($ACT1[1]),1,'L');
                                 
                $y2 = $pdf->GetY();
                $h = $y2 - $y;         
                $alto = $alto + $h;
                $h2 = $h;  

                $pdf->SetXY($x+100,$y);
                $x =$pdf->GetX();
                 $pdf->SetFont('Arial','B',8);
                $pdf->MultiCell(15,$h, utf8_decode($ACT1[2]),1,'C');
                $pdf->SetXY($x+15,$y);
                $x =$pdf->GetX();
                $pdf->SetFont('Arial','B',8);
                $pdf->MultiCell(35,$h, utf8_decode(number_format($vlrIng,0,',','.')),1,'R');
                $pdf->SetXY($x+35,$y);
                $x =$pdf->GetX();
                $pdf->SetFont('Arial','B',8);
                $pdf->MultiCell(15,$h, utf8_decode($ACT[3]."x 1000"),1,'R');
                $pdf->SetXY($x+15,$y);
                $x =$pdf->GetX();
                $pdf->SetFont('Arial','B',8);
                $pdf->MultiCell(35,$h, utf8_decode(number_format($vlrImp,0,',','.')),1,'R');        
                $totImp = $totImp + $vlrImp;

                $ac = $ac + 1;
        }       
    }
    
    $ccc = 1;
       
    $pdf->Cell(110,5, utf8_decode('TOTAL INGRESOS GRAVADOS EN EL MUNICIPIO O DISTRITO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(25,5, utf8_decode($totalVlrcon),1,0,'R');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(35,5, utf8_decode('TOTAL IMPUESTO'),1,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5, utf8_decode(number_format($totImp,0,',','.')),1,0,'R');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(200,5, utf8_decode('LIQUIDACION DEL IMPUESTO PARA LA GENERACION DE ENERGIA ELECTRICA LEY 56 DE 1981'),1,0,'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('D. LIQUIDACION DEL IMPUESTO'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);

     $sqlLP1 = "SELECT cc.id_unico,cc.descripcion FROM gc_concepto_comercial cc LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo WHERE tc.id_unico = 2 ORDER BY cc.codigo ASC";
     $qLP1 = $mysqli->query( $sqlLP1);
   

    $X = 20;
    while($LI1 = mysqli_fetch_row($qLP1)){


        $sqlLP="SELECT cc.id_unico,cc.descripcion,dd.valor 
                FROM gc_detalle_declaracion dd 
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                WHERE tc.id_unico = 2 and md5(dd.declaracion) = '$id'
                and cc.id_unico = '$LI1[0]'
                GROUP BY cc.id_unico
                ORDER BY cc.codigo ASC";      

        $qLP=$mysqli->query($sqlLP);
        $LI = mysqli_fetch_row($qLP);
         if(!empty($LI)){
           $vlrtotImp = $LI[2];
         }else{
           $vlrtotImp = 0;
         }    

        $pdf->SetFont('Arial','',8);
        $pdf->Cell(10,5, utf8_decode($X),1,0,'R');
        $pdf->Cell(140,5, utf8_decode($LI1[1]),1,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5, utf8_decode(number_format($vlrtotImp,0,',','.')),1,0,'R');
        $pdf->Ln(5);
        $a = $a+1;
        $X = $X + 1;
    }
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('E. PAGO'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);

   
    $sqlGP1="SELECT cc.id_unico,cc.descripcion
        FROM gc_concepto_comercial cc 
        LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
        WHERE tc.id_unico = 3";
    $qGP1=$mysqli->query($sqlGP1);

   
     while($PA1 = mysqli_fetch_row($qGP1)){
         $sqlGP ="SELECT cc.id_unico,
                         cc.descripcion,
                         dd.valor
            FROM gc_detalle_declaracion dd 
            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico  
            LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
            WHERE tc.id_unico = 3 and  md5(dd.declaracion) = '$id'
            and cc.id_unico = '$PA1[0]'
            GROUP BY cc.id_unico";

          $qGP=$mysqli->query($sqlGP);
          $PA = mysqli_fetch_row($qGP);
             if(!empty($PA)){
               $vlrtotPago = $PA[2];
             }else{
               $vlrtotPago = 0;
             }    

        $pdf->SetFont('Arial','',8);
        $pdf->Cell(10,5, utf8_decode($X),1,0,'R');
        $pdf->Cell(140,5, utf8_decode($PA1[1]),1,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5, utf8_decode(number_format($vlrtotPago,0,',','.')),1,0,'R');
        $pdf->Ln(5);
        $a = $a+1;
        $X = $X + 1;
    }
     $pdf->Cell(100,5, utf8_decode('FIRMA DEL DECLARANTE'),'LRT',0,'L'); 
      $pdf->Cell(100,5, utf8_decode('FIRMA DE CONTADOR [  ]   REVISOR FISCAL [  ]'),'RT',0,'L');
      $pdf->SetFont('Arial','',8);
      $pdf->Ln(5);
      $pdf->Cell(100,11, utf8_decode(''),'LRB',0,'L'); 
      $pdf->Cell(100,11, utf8_decode(''),'RB',0,'L');

      $pdf->Ln(11);  
      $pdf->Cell(100,11, utf8_decode('Nombre ______________________________________________________'),'L',0,'L'); 
      $pdf->Cell(100,11, utf8_decode('Nombre ______________________________________________________'),'RL',0,'C'); 

      $pdf->Ln(9);  
      $pdf->Cell(100,7, utf8_decode(' C.C.[  ]  C.E.[  ]  T.I.[  ]           _____________________________________'),'LB',0,'L'); 
      $pdf->Cell(100,7, utf8_decode(' C.C.[  ]  C.E.[  ]  T.I.[  ]        _______________________________________'),'RLB',0,'L'); 

    ob_end_clean();
    $pdf->Output(0,'Declaracion_Industria_y_Comercio ('.date('d/m/Y').').pdf',0);  
?>