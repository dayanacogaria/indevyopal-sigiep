<?php 
require'../Conexion/conexion.php';
require_once("../Conexion/ConexionPDO.php");
ini_set('max_execution_time', 360);
session_start();
$con      = new ConexionPDO();
$compania = $_SESSION['compania'];
$usuario  = $_SESSION['usuario'];
$hoy      = date('d/m/Y');
$empleado  = $_REQUEST['sltEmpleado'];  
$periodo   = $_REQUEST['sltPeriodo'];  

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

#** Consulta Tercero 
if(empty($empleado) || $empleado == 2){
    
    $consulta2 = "SELECT DISTINCT  e.id_unico, 
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
    WHERE e.id_unico != 2 AND cn.clase = 9 
    AND (n.periodo) = '$periodo'";
}else{
    $consulta2 = "SELECT distinct  e.id_unico, 
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
      WHERE (n.periodo) = '$periodo' AND (e.id_unico) = '$empleado' 
      AND cn.clase = 9 
      ORDER BY e.id_unico";    
}     
//echo $consulta2;                   
$empl   = $mysqli->query($consulta2);
$numemp = mysqli_num_rows($empl);


$consulta3 = "SELECT id_unico, codigointerno, DATE_FORMAT(fechainicio,'%d/%m/%Y'), DATE_FORMAT(fechafin,'%d/%m/%Y') FROM gn_periodo WHERE (id_unico) = '$periodo'";
$perio = $mysqli->query($consulta3);
$perN  = mysqli_fetch_row($perio);
$codigo = $perN[1];
$fechaI = $perN[2];
$fechaF = $perN[3];

if($_REQUEST['t']==1){
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
        if($ruta != '')
        {
          $this->Image('../'.$ruta_logo,20,8,15);
        } 
        $this->SetFont('Arial','B',10);
        $this->SetX(20);
        $this->Cell(170,5,utf8_decode(ucwords($razonsocial)),0,0,'C');
        $this->Ln(5);        
        $this->SetFont('Arial','',8);
        $this->SetX(20);
        $this->Cell(170, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); ;
        $this->Ln(5);
        $this->Cell(190,5,utf8_decode('VOLANTE DE PAGO HORAS EXTRAS'),0,0,'C');
        $this->Ln(3);
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

  #Consulta Tercero 

  while($fila1 = mysqli_fetch_row($empl)){
    $idemp = $fila1[0];
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(11);
    $pdf->Cell(37,18,utf8_decode('NÓMINA:'),0,0,'L');
    $pdf->Cell(12,18,utf8_decode($codigo),0,0,'L');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,18,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->Cell(15,18,utf8_decode($fila1[2].' - '.$fila1[8]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(37,18,utf8_decode('FECHA INCIAL:'),0,0,'L');
    $pdf->Cell(12,18,utf8_decode($fechaI),0,0,'L');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,18,utf8_decode('CARGO:'),0,0,'L');
    $pdf->Cell(15,18,utf8_decode($fila1[5]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(37,19,utf8_decode('FECHA FINAL:'),0,0,'L');
    $pdf->Cell(12,18,utf8_decode($fechaF),0,0,'L');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(25,18,utf8_decode('GRUPO GESTION:'),0,0,'L');
    $pdf->Cell(15,18,utf8_decode($fila1[7]),0,0,'L');
    $pdf->Ln(15);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5, utf8_decode('Código'),1,0,'C');
    $pdf->Cell(90,5, utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Horas'),1,0,'C');
    $pdf->Cell(40,5,utf8_decode('Valor'),1,0,'C');
    $pdf->Ln(5);
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
        $pdf->SetFont('Arial','',7);
        $valor  = $filaN[1];
        $codcon = $filaN[2];
        $descon = $filaN[3];

        
        $pdf->cellfitscale(30,5,utf8_decode($codcon),0,0,'C');
        $pdf->cellfitscale(90,5,utf8_decode($descon),0,0);        
        $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND (periodo) = '$periodo' AND concepto = ".$filaN[5];
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);
        $pdf->cellfitscale(30,5,utf8_decode($dt[1]),0,0,'R');
        $pdf->cellfitscale(40,5,utf8_decode(number_format($valor,2,'.',',')),0,0,'R');
        $pdf->Ln(3);
        $total_h += $dt[1];
        $total_v += $valor;
    }
    $pdf->Ln(3);
    $pdf->cellfitscale(190,0.5,'',1,0,'C');
    $pdf->Ln(3);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale(120,5,utf8_decode('TOTAL'),0,0,'C');
    $pdf->cellfitscale(30,5,utf8_decode($total_h),0,0,'R');
    $pdf->cellfitscale(40,5,utf8_decode(number_format($total_v,2,'.',',')),0,0,'R');
    $pdf->AddPage();
  }
   

  ob_end_clean();$pdf->Output(0,'Volante_Horas_Extra ('.date('d/m/Y').').pdf',0);  
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
