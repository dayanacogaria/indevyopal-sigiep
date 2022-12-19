<?php
    session_start();
        
    require'../fpdf/fpdf.php';
    require'../Conexion/conexion.php';

    ini_set('max_execution_time', 360);
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];
    ob_start();
    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
      FROM gf_tercero ter 
      LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
      WHERE ter.id_unico = '.$compania;

    $rutaLogo = $mysqli->query($sqlRutaLogo);
    $rowLogo = mysqli_fetch_array($rutaLogo);
    $ruta = $rowLogo[0];
    $ciudadCompania = $rowLogo[1];

    //$grupog = $_POST['sltGrupoG'];
    //$periodo  = $_POST['sltPeriodo'];
    //$tipof  = $_POST['sltTipoF'];  

    /*$grupog = 7;
    $periodo = "";
    $tipof = "";
    */

/*
    if(empty($grupog) || $grupog == ""){

      $GRUP = "Todos";

    }else{

      $G = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico = $grupog";
      $GG = $mysqli->query($G);
      $GR = mysqli_fetch_row($GG);

      $GRUP = $GR[1];
    }

    if(empty($periodo) || $periodo == ""){

      $PER = "Todos";
      $FI = "";
      $FF = "";

    }else{

     $P = "SELECT id_unico, codigointerno , fechainicio, fechafin FROM gn_periodo WHERE id_unico = $periodo";
      $PP = $mysqli->query($P);
      $PERI = mysqli_fetch_row($PP);

      $PER = $PERI[1];
      

      $fecha_div = explode("-", $PERI[2]);
      $anion = $fecha_div[0];
      $mesn = $fecha_div[1];
      $dian = $fecha_div[2];
      $FI = $dian.'/'.$mesn.'/'.$anion;

      $fecha_div2 = explode("-", $PERI[3]);
      $anion1 = $fecha_div2[0];
      $mesn1 = $fecha_div2[1];
      $dian1 = $fecha_div2[2];
      $FF = $dian1.'/'.$mesn1.'/'.$anion1;

    }

    if(empty($tipof)|| $tipof ==""){

      $TF = "Todos";

    }else{

      $TIF = "SELECT id_unico , nombre FROM gn_tipo_fondo WHERE id_unico = $tipof";
      $FT = $mysqli->query($TIF);
      $TIPF = mysqli_fetch_row($FT);

      $TF = $TIPF[1];
    } 
*/
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

    $perD       = $_POST['PD']; 
    $TiDE       = $_POST['TD'];
    $c          = $_POST['sltNumI'];
    $p          = $_POST['sltctai'];
    $vf         = $_POST['sltVig'];
    $fecha_dec  = $_POST['Fec_D'];
    $pesasM     = $_POST['PeMe'];
    $numD       = $_POST['txtnum'];
    $Nest       = $_POST['txtNumE'];

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
    $sqlBG= "SELECT cc.id_unico,cc.descripcion,tc.nombre, cc.apli_descu, cc.apli_inte 
            FROM gc_concepto_comercial cc 
            LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
            WHERE tc.id_unico = 1"; 

    $qBG=$mysqli->query($sqlBG);

    $X = 8;
    $a = 1;
    while($BG = mysqli_fetch_row($qBG)){
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(10,5, utf8_decode($X),1,0,'R');
        $pdf->Cell(140,5, utf8_decode($BG[1]),1,0,'L');
        $nvalue    = 'iValue'.$a;
        $vvalue    = $_POST["$nvalue"];
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5, utf8_decode($vvalue),1,0,'R');
        $pdf->Ln(5);
        $a = $a+1;
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

    $actividad = "SELECT DISTINCT ac.id_unico, ac.codigo, ac.descripcion, ta.tarifa,aco.fechainicio,aco.fechacierre  
                FROM gc_actividad_comercial ac 
                LEFT JOIN gc_actividad_contribuyente aco ON aco.actividad = ac.id_unico
                LEFT JOIN gc_tarifa_actividad ta ON ta.act_comer = ac.id_unico
                WHERE aco.contribuyente = '$c' and ta.anno_grava = '$p'
                ORDER BY aco.fechainicio DESC, ac.codigo ASC";

    $activi =  $mysqli->query($actividad);

    $x =$pdf->GetX();
    $y =$pdf->GetY();   
    $cx = $pdf->GetX();
    $cy = $pdf->GetY(); 
    $h2 = 0; 
    $alto = 0;
    $ac = 1;
    while($ACT = mysqli_fetch_row($activi)){
      
     $nIdING = "idING".$ac;
     $vIdING = $_POST["$nIdING"];
     if($vIdING!= 0){
     
        $pdf->SetFont('Arial','',8);        
        $x =$pdf->GetX();
        $y =$pdf->GetY();
        $pdf->MultiCell(100,6, utf8_decode($ACT[2]),1,'L');
        
        $y2 = $pdf->GetY();
        $h = $y2 - $y;         
        $alto = $alto + $h;
        $h2 = $h;     

        $pdf->SetXY($x+100,$y);
        $x =$pdf->GetX();
        $pdf->SetFont('Arial','B',8);
        $pdf->MultiCell(15,$h, utf8_decode($ACT[1]),1,'C');
        $pdf->SetXY($x+15,$y);
        $x =$pdf->GetX();
        $pdf->SetFont('Arial','B',8);
        $pdf->MultiCell(35,$h, utf8_decode($vIdING),1,'R');
        $pdf->SetXY($x+35,$y);
        $x =$pdf->GetX();
        $pdf->SetFont('Arial','B',8);
        $pdf->MultiCell(15,$h, utf8_decode($ACT[3]."x 1000"),1,'R');
        $pdf->SetXY($x+15,$y);
        $x =$pdf->GetX();
        $nIdIMP = "idIMP".$ac;
        $vIdIMP = $_POST["$nIdIMP"];
        $pdf->SetFont('Arial','B',8);
        $pdf->MultiCell(35,$h, utf8_decode($vIdIMP),1,'R');        
       
       }
        $ac = $ac + 1;

    }
    
   
    $ccc = 1;
  
    $pdf->Cell(110,5, utf8_decode('TOTAL INGRESOS GRAVADOS EN EL MUNICIPIO O DISTRITO'),1,0,'C');
    $ITIG = "INGM".$ccc;
    $VTIG = $_POST["$ITIG"];
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(25,5, utf8_decode($VTIG),1,0,'R');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(35,5, utf8_decode('TOTAL IMPUESTO'),1,0,'C');
    $ITI = "TI".$ccc;
    $VTI = $_POST["$ITI"];
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5, utf8_decode($VTI),1,0,'R');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(200,5, utf8_decode('LIQUIDACION DEL IMPUESTO PARA LA GENERACION DE ENERGIA ELECTRICA LEY 56 DE 1981'),1,0,'C');
    $pdf->Ln(5);
    /*$y = $pdf->GetY();
    $pdf->MultiCell(50,6, utf8_decode('CAPACIDAD INSTALADA EN ESTE MUNICIPIO(En Kilovatios)'),0,'C');
    $y2 = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->SetY($y);
    $pdf->SetX($x);
    $pdf->Cell(30,6, utf8_decode(''),0,0,'C');
    $x = $pdf->GetX();
    $pdf->SetX($x);
    $pdf->MultiCell(70,6, utf8_decode('TOTAL IMPUESTO X ACTIVIDAD DE GENERACION DE ENERGÍA ELECTRICA'),0,'C');
    $y2 = $pdf->GetY();
    $pdf->SetY($y);
    $x = $pdf->GetX();
    $pdf->SetX($x);
    $pdf->Cell(50,6, utf8_decode(''),0,0,'C');*/
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('D. LIQUIDACION DEL IMPUESTO'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);

    $sqlLP="SELECT cc.id_unico,cc.descripcion,tc.nombre,cc.apli_descu, cc.apli_inte, cc.codigo, cc.tipo_ope  
            FROM gc_concepto_comercial cc 
            LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
            WHERE tc.id_unico = 2 ORDER BY cc.codigo ASC"; 

    $qLP=$mysqli->query($sqlLP);

    $X = 20;
    while($LI = mysqli_fetch_row($qLP)){
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(10,5, utf8_decode($X),1,0,'R');
        $pdf->Cell(140,5, utf8_decode($LI[1]),1,0,'L');
        $nvalue    = 'iValue'.$a;
        $vvalue    = $_POST["$nvalue"];
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5, utf8_decode($vvalue),1,0,'R');
        $pdf->Ln(5);
        $a = $a+1;
        $X = $X + 1;
    }
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(200,6, utf8_decode('E. PAGO'),1,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',8);

    $sqlGP="SELECT cc.id_unico,cc.descripcion,tc.nombre 
            FROM gc_concepto_comercial cc 
            LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
            WHERE tc.id_unico = 3"; 

    $qGP=$mysqli->query($sqlGP);


    while($PA = mysqli_fetch_row($qGP)){
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(10,5, utf8_decode($X),1,0,'R');
        $pdf->Cell(140,5, utf8_decode($PA[1]),1,0,'L');
        $nvalue    = 'iValue'.$a;
        $vvalue    = $_POST["$nvalue"];
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5, utf8_decode($vvalue),1,0,'R');
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