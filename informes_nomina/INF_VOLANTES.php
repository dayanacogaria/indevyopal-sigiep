<?php 
require'../Conexion/conexion.php';
require_once("../Conexion/ConexionPDO.php");
ini_set('max_execution_time', 360);
session_start();
$con      = new ConexionPDO();
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$usuario  = $_SESSION['usuario'];
$hoy      = date('d/m/Y');
$empleado  = $_REQUEST['sltEmpleado'];  
$periodo   = $_REQUEST['sltPeriodo'];  
$tipo      = $_REQUEST['sltTipo'];  
$t         = 1;

#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN   
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN   
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
 $ruta_logo   = $rowC[0][6];




$consulta3 = "SELECT p.id_unico, p.codigointerno, DATE_FORMAT(p.fechainicio,'%d/%m/%Y'), DATE_FORMAT(p.fechafin,'%d/%m/%Y') , tpn.nombre
FROM gn_periodo p 
LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
WHERE (p.id_unico) =  '$periodo'";
$perio = $mysqli->query($consulta3);
$perN  = mysqli_fetch_row($perio);
$codigo = mb_strtoupper($perN[4].' - '.$perN[1]);
$codigo2 = mb_strtoupper($perN[1]);
$fechaI = $perN[2];
$fechaF = $perN[3];

if($t==1){
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
        global $codigo;
        if($ruta_logo != '')
        {
          $this->Image('../'.$ruta_logo,20,8,20);
        } 
        $this->SetFont('Arial','B',10);
        $this->SetX(20);
        $this->Cell(170,5,utf8_decode(ucwords($razonsocial)),0,0,'C');
        $this->Ln(5);        
        $this->SetFont('Arial','',8);
        $this->SetX(20);
        $this->Cell(170, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); ;
        $this->Ln(5);
        $this->Cell(190,5,utf8_decode('VOLANTE DE '.$codigo),0,0,'C');
        $this->Ln(6);
        $this->SetFont('Arial','B',8);
        $this->SetX(0);
    }
    function Footer(){
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(130,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
        $this->Cell(40,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    }
  }

  $pdf = new PDF('P','mm','mcarta');   
  $nb=$pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->AliasNbPages();
  $pdf->SetFont('Arial','',8);

  #Consulta Empleados
  $sql = "SELECT distinct  e.id_unico, 
            e.tercero, 
            CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
            tc.categoria, 
            c.id_unico, 
            c.nombre, 
            c.salarioactual,
            gg.nombre,
            t.numeroidentificacion                            
     FROM gn_novedad n 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
    LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
    LEFT JOIN gn_concepto cn ON n.concepto =cn.id_unico 
    WHERE n.periodo = '$periodo' AND cn.clase = 1 AND cn.unidadmedida = 1  "; 
  $wo ='ORDER BY e.id_unico';
  $wh = '';
  if(empty($empleado) || $empleado == 2){
    $wh .= ' AND e.id_unico != 2';
  } else {
    $wh .= ' AND e.id_unico = '.$empleado;
  }
  $row = $con->Listar($sql.' '.$wh.' '.$wo);
  for ($i=0; $i <count($row) ; $i++) { 
    $idemp = $row[$i][0];
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(11);
    $pdf->Cell(25,5,utf8_decode('NÓMINA:'),0,0,'L');
    $pdf->Cell(24,5,utf8_decode($codigo2),0,0,'L');
    $pdf->Cell(25,5,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,5,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->Cell(15,5,utf8_decode($row[$i][2].' - '.$row[$i][8]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(25,5,utf8_decode('FECHA INCIAL:'),0,0,'L');
    $pdf->Cell(24,5,utf8_decode($fechaI),0,0,'L');
    $pdf->Cell(25,5,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,5,utf8_decode('CARGO:'),0,0,'L');
    $pdf->Cell(15,5,utf8_decode($row[$i][5]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(25,5,utf8_decode('FECHA FINAL:'),0,0,'L');
    $pdf->Cell(24,5,utf8_decode($fechaF),0,0,'L');
    $pdf->Cell(25.5,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,5,utf8_decode('GRUPO GESTIÓN:'),0,0,'L');
    $pdf->Cell(15,5,utf8_decode($row[$i][7]),0,0,'L');
    $pdf->Ln(5);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
    $pdf->Cell(75,5, utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(20,5, utf8_decode('Unidad'),1,0,'C');
    $pdf->Cell(15,5, utf8_decode('Cantidad'),1,0,'C');
    $pdf->Cell(30,5,utf8_decode('Devengos'),1,0,'C');
    $pdf->Cell(30,5,utf8_decode('Descuentos'),1,0,'C');
    $pdf->Ln(5);
    #Cuentas B
    $rowcb = $con->Listar("SELECT DISTINCT cb.numerocuenta, t.razonsocial FROM gn_empleado e 
    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico  
    LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.tercero = tr.id_unico 
    LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
    LEFT JOIN gf_tercero t ON cb.banco = t.id_unico 
    WHERE cb.parametrizacionanno = $anno AND e.id_unico =".$idemp);
    #Firmas
    $firma = "SELECT e.id_unico, IF(CONCAT_WS(' ',
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
     FROM gn_empleado e LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico  WHERE e.id_unico = '$idemp'";
    $fir = $mysqli->query($firma);
    $nfir = mysqli_fetch_row($fir);

    $firmas = "SELECT   c.nombre, 
        rd.orden,
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
        tr.apellidodos)) AS NOMBRE 
      FROM gf_responsable_documento rd 
      LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
      LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
      LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
      LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
      WHERE td.nombre = 'Volante Pago'
      ORDER BY rd.orden ASC";
    $fi = $mysqli->query($firmas);
    switch ($tipo) {
      #*Vacaciones
      case 7:        
          #Devengos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 1 AND n.valor !=0 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',8);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
              $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          #Descuentos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 2 AND n.valor !=0 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',8);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
              $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(''),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          $pdf->Ln(3);
          $pdf->cellfitscale(190,0.5,'',1,0,'C');
          $pdf->Ln(3);
          #FACTORES
          $x =$pdf->GetX();
          $y =$pdf->GetY();
          $rowf = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (8) and c.unidadmedida = 1 AND n.valor !=0 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',7);
          for ($f=0; $f <count($rowf) ; $f++) {             
            $pdf->cellfitscale(80,5,utf8_decode($rowf[$f][1].' - '.$rowf[$f][2]),0,0,'L');
            $pdf->cellfitscale(30,5,number_format($rowf[$f][3],2,'.',','),0,0,'R');
            $pdf->ln(3);
          }
          $y2 = $pdf->GetY();
          $pdf->SetXY($x, $y);
          $rowt = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (3,4,5) and c.unidadmedida = 1 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',10);
          for ($t=0; $t <count($rowt) ; $t++) {  
            $pdf->SetX(125);           
            $pdf->cellfitscale(45,5,utf8_decode($rowt[$t][2]),0,0,'L');
            $pdf->cellfitscale(30,5,number_format($rowt[$t][3],2,'.',','),0,0,'R');
            $pdf->ln(5);
          }
          $y3 = $pdf->GetY();
          $h = max($y2, $y3);
          $pdf->SetY($h);
          $pdf->Ln(5);
          $pdf->SetFont('Arial','BI',9);
          $pdf->cellfitscale(35,5,utf8_decode('CUENTA BANCARIA: '),0,0,'L');
          $pdf->SetFont('Arial','I',9);
          $pdf->Cell(150,5,utf8_decode($rowcb[0][0].' - '.ucwords(mb_strtolower($rowcb[0][1]))),0,0,'L');
          $pdf->Ln(15);

          #*******FIRMAS  
          $pdf->SetFont('Arial','B',8);
          $y =$pdf->GetY();
          $x = $pdf->GetX();
          while($F = mysqli_fetch_row($fi)){
            if($F[1] ==  1){
                 $pdf->Cell(60,0.1,'',1);  
                 $pdf->Ln(2); 
                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3);
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
            }else{
                $pdf->SetXY($x+70, $y); 

                $pdf->Cell(60,0.1,'',1); 
                $pdf->Ln(2); 
                $y1 =$pdf->GetY();
                $pdf->SetXY($x+70, $y1); 
                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3); 
                $y2 =$pdf->GetY();
                $pdf->SetXY($x+70, $y2); 
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
            }
          }    
          $pdf->SetXY($x+140, $y); 
          $pdf->Cell(60,0.1,'',1);
          $pdf->Ln(2); 
          $y1 =$pdf->GetY();
          $pdf->SetXY($x+140, $y1);
          $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
          $alto = $pdf->GetY();
              
          if($i==count($row)-1){}else{
            $pdf->AddPage();
          }
        
        
      break;
      #*Prima Servicios
      case 2:        
          #Devengos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 1 AND n.valor !=0 
            AND c.unidadmedida  = 1 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',8);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
              $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          #Descuentos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 2 AND n.valor !=0 
            AND c.unidadmedida  = 1 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',8);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
              $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(''),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          $pdf->Ln(3);
          $pdf->cellfitscale(190,0.5,'',1,0,'C');
          $pdf->Ln(3);
          #FACTORES
          $x =$pdf->GetX();
          $y =$pdf->GetY();
          $rowf = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (8) and c.unidadmedida = 1 AND n.valor !=0 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',7);
          for ($f=0; $f <count($rowf) ; $f++) {             
            $pdf->cellfitscale(80,5,utf8_decode($rowf[$f][1].' - '.$rowf[$f][2]),0,0,'L');
            $pdf->cellfitscale(30,5,number_format($rowf[$f][3],2,'.',','),0,0,'R');
            $pdf->ln(3);
          }
          $y2 = $pdf->GetY();
          $pdf->SetXY($x, $y);
          $rowt = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (3,4,5) and c.unidadmedida = 1 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',10);
          for ($t=0; $t <count($rowt) ; $t++) {  
            $pdf->SetX(125);           
            $pdf->cellfitscale(45,5,utf8_decode($rowt[$t][2]),0,0,'L');
            $pdf->cellfitscale(30,5,number_format($rowt[$t][3],2,'.',','),0,0,'R');
            $pdf->ln(5);
          }
          $y3 = $pdf->GetY();
          $h = max($y2, $y3);
          $pdf->SetY($h);
          $pdf->Ln(5);
          $pdf->SetFont('Arial','BI',9);
          $pdf->cellfitscale(35,5,utf8_decode('CUENTA BANCARIA: '),0,0,'L');
          $pdf->SetFont('Arial','I',9);
          $pdf->Cell(150,5,utf8_decode($rowcb[0][0].' - '.ucwords(mb_strtolower($rowcb[0][1]))),0,0,'L');
          $pdf->Ln(15);

          #*******FIRMAS  
          $pdf->SetFont('Arial','B',8);
          $y =$pdf->GetY();
          $x = $pdf->GetX();
          while($F = mysqli_fetch_row($fi)){
            if($F[1] ==  1){
                 $pdf->Cell(60,0.1,'',1);  
                 $pdf->Ln(2); 
                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3);
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
            }else{
                $pdf->SetXY($x+70, $y); 

                $pdf->Cell(60,0.1,'',1); 
                $pdf->Ln(2); 
                $y1 =$pdf->GetY();
                $pdf->SetXY($x+70, $y1); 
                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3); 
                $y2 =$pdf->GetY();
                $pdf->SetXY($x+70, $y2); 
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
            }
          }    
          $pdf->SetXY($x+140, $y); 
          $pdf->Cell(60,0.1,'',1);
          $pdf->Ln(2); 
          $y1 =$pdf->GetY();
          $pdf->SetXY($x+140, $y1);
          $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
          $alto = $pdf->GetY();
              
          if($i==count($row)-1){}else{
            $pdf->AddPage();
          }
        
        
      break;
      #*Prima Navidad
      case 8:        
          #Devengos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 1 AND n.valor !=0 
            AND c.unidadmedida  = 1 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',8);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
              $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          #Descuentos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 2 AND n.valor !=0 
            AND c.unidadmedida  = 1 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',8);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
              $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(''),0,0,'R');
              $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          $pdf->Ln(3);
          $pdf->cellfitscale(190,0.5,'',1,0,'C');
          $pdf->Ln(3);
          #FACTORES
          $x =$pdf->GetX();
          $y =$pdf->GetY();
          $rowf = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (8) and c.unidadmedida = 1 AND n.valor !=0 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',7);
          for ($f=0; $f <count($rowf) ; $f++) {             
            $pdf->cellfitscale(80,5,utf8_decode($rowf[$f][1].' - '.$rowf[$f][2]),0,0,'L');
            $pdf->cellfitscale(30,5,number_format($rowf[$f][3],2,'.',','),0,0,'R');
            $pdf->ln(3);
          }
          $y2 = $pdf->GetY();
          $pdf->SetXY($x, $y);
          $rowt = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (3,4,5) and c.unidadmedida = 1 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',10);
          for ($t=0; $t <count($rowt) ; $t++) {  
            $pdf->SetX(125);           
            $pdf->cellfitscale(45,5,utf8_decode($rowt[$t][2]),0,0,'L');
            $pdf->cellfitscale(30,5,number_format($rowt[$t][3],2,'.',','),0,0,'R');
            $pdf->ln(5);
          }
          $y3 = $pdf->GetY();
          $h = max($y2, $y3);
          $pdf->SetY($h);
          $pdf->Ln(5);
          $pdf->SetFont('Arial','BI',9);
          $pdf->cellfitscale(35,5,utf8_decode('CUENTA BANCARIA: '),0,0,'L');
          $pdf->SetFont('Arial','I',9);
          $pdf->Cell(150,5,utf8_decode($rowcb[0][0].' - '.ucwords(mb_strtolower($rowcb[0][1]))),0,0,'L');
          $pdf->Ln(15);

          #*******FIRMAS  
          $pdf->SetFont('Arial','B',8);
          $y =$pdf->GetY();
          $x = $pdf->GetX();
          while($F = mysqli_fetch_row($fi)){
            if($F[1] ==  1){
                 $pdf->Cell(60,0.1,'',1);  
                 $pdf->Ln(2); 
                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3);
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
            }else{
                $pdf->SetXY($x+70, $y); 

                $pdf->Cell(60,0.1,'',1); 
                $pdf->Ln(2); 
                $y1 =$pdf->GetY();
                $pdf->SetXY($x+70, $y1); 
                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3); 
                $y2 =$pdf->GetY();
                $pdf->SetXY($x+70, $y2); 
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
            }
          }    
          $pdf->SetXY($x+140, $y); 
          $pdf->Cell(60,0.1,'',1);
          $pdf->Ln(2); 
          $y1 =$pdf->GetY();
          $pdf->SetXY($x+140, $y1);
          $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
          $alto = $pdf->GetY();
              
          if($i==count($row)-1){}else{
            $pdf->AddPage();
          }
        
        
      break;
      #* CESANTIAS
      case 11:
        #Devengos
        $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
        FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
        LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
        LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
        WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
        AND c.clase= 1 AND n.valor !=0 
        AND c.unidadmedida  = 1 
        ORDER BY c.id_unico"; 
      $nom = $mysqli->query($consulta1);
      while($filaN = mysqli_fetch_row($nom)){
          $pdf->SetFont('Arial','',8);
          $valor  = $filaN[1];
          $codcon = $filaN[2];
          $descon = $filaN[3];              
          $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
          $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
          $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
          $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
          $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
          $pdf->Ln(3);
      }
      #Descuentos
      $consulta1 = "SELECT DISTINCT n.id_unico, 
         c.codigo, 
         c.descripcion,
         u.nombre, nr.valor, 
         n.valor 
        FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
        LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
        LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
        WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
        AND c.clase= 2 AND n.valor !=0 
        AND c.unidadmedida  = 1 
        ORDER BY c.id_unico"; 
      $nom = $mysqli->query($consulta1);
      while($filaN = mysqli_fetch_row($nom)){
          $pdf->SetFont('Arial','',8);
          $valor  = $filaN[1];
          $codcon = $filaN[2];
          $descon = $filaN[3];              
          $pdf->cellfitscale(20,5,utf8_decode($filaN[1]),0,0,'L');
          $pdf->cellfitscale(75,5,utf8_decode($filaN[2]),0,0,'L');        
          $pdf->cellfitscale(20,5,utf8_decode($filaN[3]),0,0,'L');
          $pdf->cellfitscale(15,5,utf8_decode($filaN[4]),0,0,'R');
          $pdf->cellfitscale(30,5,utf8_decode(''),0,0,'R');
          $pdf->cellfitscale(30,5,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
          $pdf->Ln(3);
      }
      $pdf->Ln(3);
      $pdf->cellfitscale(190,0.5,'',1,0,'C');
      $pdf->Ln(3);
      #FACTORES
      $x =$pdf->GetX();
      $y =$pdf->GetY();
      $rowf = $con->Listar("SELECT DISTINCT n.id_unico, 
         c.codigo, 
         c.descripcion,
         n.valor 
        FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.empleado =$idemp  AND n.periodo = $periodo  
        AND c.clase IN (8) and c.unidadmedida = 1 AND n.valor !=0 
        ORDER BY c.id_unico");
      $pdf->SetFont('Arial','B',7);
      for ($f=0; $f <count($rowf) ; $f++) {             
        $pdf->cellfitscale(80,5,utf8_decode($rowf[$f][1].' - '.$rowf[$f][2]),0,0,'L');
        $pdf->cellfitscale(30,5,number_format($rowf[$f][3],2,'.',','),0,0,'R');
        $pdf->ln(3);
      }
      $y2 = $pdf->GetY();
      $pdf->SetXY($x, $y);
      $rowt = $con->Listar("SELECT DISTINCT n.id_unico, 
         c.codigo, 
         c.descripcion,
         n.valor 
        FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.empleado =$idemp  AND n.periodo = $periodo  
        AND c.clase IN (3,4,5) and c.unidadmedida = 1 
        ORDER BY c.id_unico");
      $pdf->SetFont('Arial','B',10);
      for ($t=0; $t <count($rowt) ; $t++) {  
        $pdf->SetX(125);           
        $pdf->cellfitscale(45,5,utf8_decode($rowt[$t][2]),0,0,'L');
        $pdf->cellfitscale(30,5,number_format($rowt[$t][3],2,'.',','),0,0,'R');
        $pdf->ln(5);
      }
      $y3 = $pdf->GetY();
      $h = max($y2, $y3);
      $pdf->SetY($h);
      $pdf->Ln(5);
      $pdf->SetFont('Arial','BI',9);
      $pdf->cellfitscale(35,5,utf8_decode('CUENTA BANCARIA: '),0,0,'L');
      $pdf->SetFont('Arial','I',9);
      $pdf->Cell(150,5,utf8_decode($rowcb[0][0].' - '.ucwords(mb_strtolower($rowcb[0][1]))),0,0,'L');
      $pdf->Ln(15);

      #*******FIRMAS  
      $pdf->SetFont('Arial','B',8);
      $y =$pdf->GetY();
      $x = $pdf->GetX();
      while($F = mysqli_fetch_row($fi)){
        if($F[1] ==  1){
             $pdf->Cell(60,0.1,'',1);  
             $pdf->Ln(2); 
             $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3);
            $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
        }else{
            $pdf->SetXY($x+70, $y); 

            $pdf->Cell(60,0.1,'',1); 
            $pdf->Ln(2); 
            $y1 =$pdf->GetY();
            $pdf->SetXY($x+70, $y1); 
            $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3); 
            $y2 =$pdf->GetY();
            $pdf->SetXY($x+70, $y2); 
            $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
        }
      }    
      $pdf->SetXY($x+140, $y); 
      $pdf->Cell(60,0.1,'',1);
      $pdf->Ln(2); 
      $y1 =$pdf->GetY();
      $pdf->SetXY($x+140, $y1);
      $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
      $alto = $pdf->GetY();
          
      if($i==count($row)-1){}else{
        $pdf->AddPage();
      }
      break;
    
      #*RETROACTIVOS
      case 12:
            #Devengos
            $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 1 AND n.valor !=0 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',7);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];             
              $pdf->cellfitscale(20,3,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,3,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,3,utf8_decode(''),0,0,'L');
              $pdf->cellfitscale(15,3,utf8_decode(''),0,0,'R');
              $pdf->cellfitscale(30,3,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          #Descuentos
          $consulta1 = "SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             u.nombre, nr.valor, 
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            LEFT JOIN gn_unidad_medida_con u ON cr.unidadmedida = u.id_unico 
            LEFT JOIN gn_novedad nr ON nr.concepto = cr.id_unico and nr.periodo = $periodo and nr.empleado = $idemp 
            WHERE n.empleado = '$idemp' AND n.periodo = '$periodo' 
            AND c.clase= 2 AND n.valor !=0 
            ORDER BY c.id_unico"; 
          $nom = $mysqli->query($consulta1);
          while($filaN = mysqli_fetch_row($nom)){
              $pdf->SetFont('Arial','',7);
              $valor  = $filaN[1];
              $codcon = $filaN[2];
              $descon = $filaN[3];              
              $pdf->cellfitscale(20,3,utf8_decode($filaN[1]),0,0,'L');
              $pdf->cellfitscale(75,3,utf8_decode($filaN[2]),0,0,'L');        
              $pdf->cellfitscale(20,3,utf8_decode(''),0,0,'L');
              $pdf->cellfitscale(15,3,utf8_decode(''),0,0,'R');
              $pdf->cellfitscale(30,3,utf8_decode(''),0,0,'R');
              $pdf->cellfitscale(30,3,utf8_decode(number_format($filaN[5],2,'.',',')),0,0,'R');
              $pdf->Ln(3);
          }
          $pdf->cellfitscale(190,0.5,'',1,0,'C');
          $pdf->Ln(1);
          $x =$pdf->GetX();
          $y =$pdf->GetY();
          
          
          $rowt = $con->Listar("SELECT DISTINCT n.id_unico, 
             c.codigo, 
             c.descripcion,
             n.valor 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado =$idemp  AND n.periodo = $periodo  
            AND c.clase IN (3,4,5) and c.unidadmedida = 1 
            ORDER BY c.id_unico");
          $pdf->SetFont('Arial','B',7);
          for ($t=0; $t <count($rowt) ; $t++) {  
            $pdf->SetX(125);           
            $pdf->cellfitscale(45,3,utf8_decode($rowt[$t][2]),0,0,'L');
            $pdf->cellfitscale(30,3,number_format($rowt[$t][3],2,'.',','),0,0,'R');
            $pdf->ln(3);
          }
          $y2 = $pdf->GetY();
          $pdf->SetXY($x, $y);
          $pdf->SetFont('Arial','BI',9);
          $pdf->cellfitscale(35,3,utf8_decode('CUENTA BANCARIA: '),0,0,'L');
          $pdf->SetFont('Arial','I',9);
          $pdf->MultiCell(80,5,utf8_decode($rowcb[0][0].' - '.ucwords(mb_strtolower($rowcb[0][1]))),0,'L');
          $pdf->SetY($y2);


          if($pdf->GetY()>100){

            $pdf->AddPage();
            $pdf->Ln(20);                
          } else {
            $pdf->Ln(12);                
          }
          #*******FIRMAS  
          $pdf->SetFont('Arial','B',8);
          $y =$pdf->GetY();
          $x = $pdf->GetX();
          while($F = mysqli_fetch_row($fi)){
            if($F[1] ==  1){
                 $pdf->Cell(60,0.1,'',1);  
                 $pdf->Ln(2); 
                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3);
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
            }else{
                $pdf->SetXY($x+70, $y); 

                $pdf->Cell(60,0.1,'',1); 
                $pdf->Ln(2); 
                $y1 =$pdf->GetY();
                $pdf->SetXY($x+70, $y1); 
                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3); 
                $y2 =$pdf->GetY();
                $pdf->SetXY($x+70, $y2); 
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
            }
          }    
          $pdf->SetXY($x+140, $y); 
          $pdf->Cell(60,0.1,'',1);
          $pdf->Ln(2); 
          $y1 =$pdf->GetY();
          $pdf->SetXY($x+140, $y1);
          $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
          $alto = $pdf->GetY();
          
             
          if($i==count($row)-1){}else{
            $pdf->AddPage();
          }
      break;
    
    }
  }

 
  ob_end_clean();$pdf->Output(0,'Volante '.$codigo.' ('.date('d/m/Y').').pdf',0);  
} else { 
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=Volante_Horas_Extra.xls");
  ?>
   <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Volante De Pago HE</title>
  </head>
  <body>
  <?php 
  #Consulta Tercero 
  while($fila1 = mysqli_fetch_row($empl)){
    $idemp = $fila1[0];
  ?>  
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="4" align="center"><strong><center>
              <br/>&nbsp;
              <br/><?php echo $razonsocial ?>
              <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
              <br/>&nbsp;
              <br/>VOLANTE HORAS EXTRA   
              <br/>&nbsp;        </center>         
              </strong>
          </td>
        </tr>
        <tr align="center">
          <td><strong>NOMINA</strong></td>
          <td><strong><?=$codigo;?></strong></td>
          <td><strong>NOMBRE</strong></td>
          <td><strong><?=$fila1[2].' - '.$fila1[8];?></strong></td>
        </tr>
        <tr align="center">
          <td><strong>FECHA INICIAL</strong></td>
          <td><strong><?=$fechaI;?></strong></td>
          <td><strong>CARGO</strong></td>
          <td><strong><?=$fila1[5];?></strong></td>
        </tr>
        <tr align="center">
          <td><strong>FECHA FINAL</strong></td>
          <td><strong><?=$fechaF;?></strong></td>
          <td><strong>GRUPO GESTIÓN</strong></td>
          <td><strong><?=$fila1[7];?></strong></td>
        </tr>

        <tr>
            <td><center><strong>CÓDIGO</strong></center></td>
            <td><center><strong>CONCEPTO</strong></center></td>
            <td><center><strong>HORAS</strong></center></td>
            <td><center><strong>VALOR</strong></center></td>
        </tr>
        <?php 
        #Devengos
        $consulta1 = "SELECT n.id_unico, 
           n.valor, 
           c.codigo, 
           c.descripcion,
           ca.salarioactual, 
           c.conceptorel 
           FROM gn_novedad n 
           LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
           LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
           LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
           LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
           LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
           LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria
           LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
           WHERE n.empleado = '$idemp' AND (n.periodo) = '$periodo' 
           AND cr.clase= 9 
           ORDER BY c.id_unico"; 

        $nom = $mysqli->query($consulta1);
        $total_v = 0;
        $total_h = 0;
        while($filaN = mysqli_fetch_row($nom)){
            $valor  = $filaN[1];
            $codcon = $filaN[2];
            $descon = $filaN[3];
            $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND (periodo) = '$periodo' AND concepto = ".$filaN[5];
            $diast = $mysqli->query($consulta5);
            $dt = mysqli_fetch_row($diast);
            $total_h += $dt[1];
            $total_v += $valor;
            echo '<tr>
              <td>'.$codcon.'</td>
              <td>'.$descon.'</td>
              <td>'.$dt[1].'</td>
              <td>'.number_format($valor,2,'.',',').'</td>
            </tr>';
        }
        echo '<tr>
            <td colspan="2"><center><strong><i>TOTAL</i></strong></center></td>
            <td><center><strong><i>'.$total_h.'</i></strong></center></td>
            <td><center><strong><i>'.number_format($total_v,2,'.',',').'</i></strong></center></td>
        </tr>'?>
  <?php }?>  
  </table>
<?php } ?>
</body>
