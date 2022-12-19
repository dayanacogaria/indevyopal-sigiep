<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../numeros_a_letras.php');
require'../fpdf/fpdf.php';
ini_set('max_execution_time', 0);
ob_start();
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$con2       = new ConexionPDO();
$anno       = $_SESSION['anno'];
$fechaini    = $_POST['fechaini']; 
$periodo  = $_POST['sltPeriodo'];
$CodigoRes = $_POST['codigoRes'];

$np = $con->Listar("SELECT p.id_unico,p.codigointerno, tpn.nombre , fechafin , fechainicio 
    FROM gn_periodo p 
    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
    WHERE p.id_unico = $periodo");

$nperiodo = ucwords(mb_strtolower($np[0][1].' - '.$np[0][2]));
$fechafin = $np[0][3];
$fechaInicio = $np[0][4];
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');


$comp = "SELECT
  t.razonsocial,
  t.numeroidentificacion,
  t.digitoverficacion,
  t.ruta_logo,
  d.direccion,
  tel.valor
FROM
  gf_tercero t
LEFT JOIN
  gf_direccion d ON d.tercero = t.id_unico
LEFT JOIN
  gf_telefono tel ON tel.tercero = t.id_unico
WHERE
  t.id_unico =$compania";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
#   ************   Datos Compañia   ************    #
$rowC = $con->Listar("SELECT 	ter.id_unico,
ter.razonsocial,
ter.nombre_comercial,         
UPPER(ti.nombre),
ter.numeroidentificacion,
ciudad.nombre,
dir.direccion,
tel.valor,
ter.ruta_logo,
ter.email
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN gf_ciudad ciudad ON ciudad.id_unico = ter.ciudadidentificacion
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreEm = $rowC[0][2];
$nombreIdent = $rowC[0][3];
$numeroIdent = $rowC[0][4];
$ciudad = $rowC[0][5];
$direccinTer = $rowC[0][6];
$telefonoTer = $rowC[0][7];
$ruta_logo   = $rowC[0][8];
$email   = $rowC[0][9];


class PDF extends FPDF
{
        function Header(){ 
          global $con;
          global $rowcn;
          global $con2;
          global $rowcn2;
          global $fechaini;
          global $CodigoRes;
          global $periodo;
          global $nombreCompania;
          global $mes_v; 
          global $ruta_logo;
          global $dia_v;
          global $anno_v;
          global $fech_ac;
          global $num_df;
          global $nitcompania;
          global $numpaginas;
          global $nombreEm;
          global $nombreIdent;
          global $telefonoTer;
          global $ciudad;
          global $direccinTer;
          global $dir1;
          global $dir2;
          global $dir3;
          global $dir4;
          global $dir5;
          global $dir6;
          global $numeroIdent;
          global $email;
          $numpaginas=$numpaginas+1;

          $Dire=explode(" ",$direccinTer);
          $dir1=$Dire[1];				
          $dir2 = $Dire[2];	
          $dir3= $Dire[3];	
          $dir4= $Dire[4];
          $dir5= $Dire[5];
          $dir6= $Dire[6];
            $this->SetFont('Arial','B',10);

            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,15,10,32);
            }
            $this->SetY(10);
            $this->SetFont('Arial','',10);
            $this->Cell(280,6,utf8_decode(ucwords(mb_strtolower($nombreEm))),0,0,'C');
           // $this->Cell(-160,6,utf8_decode(ucwords(mb_strtolower('Tel.'.$telefonoTer))),0,0,'C');
            $this->ln(5);
            $this->Cell(280,4,utf8_decode(ucwords(mb_strtolower($direccinTer))),0,0,'C');
            $this->ln(5);
            $this->Cell(280,2,utf8_decode(ucwords(mb_strtolower($email))),0,0,'C');
            $this->ln(5);
            $this->Cell(280,0,utf8_decode(ucwords(mb_strtolower('NIT '.$numeroIdent))),0,0,'C');
            $this->ln(5);
            $this->Cell(280,-2,utf8_decode(ucwords(mb_strtolower('TEL.'.$telefonoTer))),0,0,'C');
            $this->ln(5);
            $this->Cell(280,-4,utf8_decode(ucwords(mb_strtolower($ciudad))),0,0,'C');
            
        }      

        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }
    $pdf = new PDF('P','mm','Letter');  
    
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetLeftMargin(20);
    $pdf->AliasNbPages();
    $pdf->Ln(20);
    
    
    $pdf->SetFont('Arial','B',10);
    #** INDICE DE LIQUIDEZ **#
    $fechaD=explode("/",$fechaini);
    $diaC =$fechaD[0];				#Dia
    $mesC = (int) $fechaD[1];		#Mes
    $annoC= $fechaD[2];				#Año 
//$pdf->Cell(90,5,$dia_fat.', '.$diaC.' de '.$meses[$mesC].' de '.$annoC,0,0,'L');
    $valorletras = numtoletras($fechaD[0]);
    $valorleD=explode(" ",$valorletras);
    $valor =$valorleD[0];				#valor en letras del dia


    
    $pdf->ln(-10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,10,utf8_decode('RESOLUCION No.'),0,0,'C');
    $pdf->Cell(-148,10,utf8_decode(ucwords(mb_strtolower($CodigoRes))),0,0,'C');
    $pdf->ln(5);
   $pdf->Cell(190,8,utf8_decode(ucwords(mb_strtolower( $fechaD[0].' de '.$meses[$mesC].' de '.$annoC))),0,0,'C');
    
    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('Por medio de la cual se reconocen y cancelan unas horas extras dominicales y festivos.'),0,0,'C');
    $pdf->ln(9);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower('LA GERENTE GENERAL DE LA ' . $nombreCompania))),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    //$pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($nom_d))),0,0,'L');
    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('en  uso de sus atribuciones legales y,'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    //$pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($nit_cc))),0,0,'L');
    $pdf->ln(8);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,10,utf8_decode('CONSIDERANDO'),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    //$pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($nit_cc))),0,0,'L');
    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('Que por necesidad del servicio se laboraron horas extras en diferentes dependencias de la Empresa.'),0,0,'J');

    $pdf->ln(10);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('Que de acuerdo a lo establecido en el Artículo 168 del Código Sustantivo del Trabajo y en los Artículos 36'),0,0,'J');

    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('y 37 del Decreto Ley 1042 de 1978 y la Ley 1846 de 2017, por medio de  la cual se modifican los Artículos'),0,0,'J');

    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('160 y 161 del Código Sustantivo del Trabajo y se dictan otras disposiciones, se deben reconocer a los'),0,0,'J');

    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,10,utf8_decode('trabajadores cuando laboren tiempo extra así:'),0,0,'J');
    
    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- H.E.D.: Horas extras diurnas (6 A.M. a 9 P.M.): recargo del 25% (1.25)'),0,0,'L');
    
    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- H.E.N.: Horas extras nocturnas (9 P.M. a 6 A.M.): recargo del 75% (1.75)'),0,0,'L');
    
    $pdf->ln(5);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- R.N.O.: Recargo Nocturno (9 P.M. a 6 A.M.): recargo del 35% (0.35)'),0,0,'L');
    
    $pdf->ln(5);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- H.D.F.: Hora diurna (ordinaria) en festivo o dominical: recargo del 75% (1.75)'),0,0,'L');
    
    $pdf->ln(5);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- H.N.F.: Hora nocturna (ordinaria) en festivo o dominical: recargo del 110% (2.10)'),0,0,'L');
    
    $pdf->ln(5);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- H.E.F.D.: Hora extra diurna dominical o festiva: recargo del 100% (2.00)'),0,0,'L');
    
    $pdf->ln(5);
    $pdf->Cell(30,10,utf8_decode(''),0,0,'L');
    $pdf->Cell(160,10,utf8_decode('- H.E.F.N.: Hora extra nocturna dominical o festiva: recargo del 150% (2.50)'),0,0,'L');
    
    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(170,10,utf8_decode('Que está de acuerdo con lo estipulado en la Ley 789 del 31 de Diciembre de 2002, y el Artículo 179 del'),0,0,'L');
    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(120,10,utf8_decode('Régimen Laboral Colombiano y el Artículo 39 del Decreto Ley 1042/78.'),0,0,'L');
    

    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(170,10,utf8_decode('Que mediante Resolución No. 036 del 12 de Enero de 2000, se reglamentaron las horas extras para los'),0,0,'L');
    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(49,10,utf8_decode('fontaneros de conducción.'),0,0,'L');
    
    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(168,10,utf8_decode('Que se efectuó la liquidación correspondiente, por parte del área de Gestión Humana, de conformidad'),0,0,'L');
    $pdf->ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(144,10,utf8_decode('con los informes emitidos por los respectivos Jefes de Plantas, Redes y Permanencias'),0,0,'L');
    

    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(40,10,utf8_decode('Que por lo anterior,'),0,0,'L');
    
    $pdf->ln(8);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,10,utf8_decode('RESUELVE'),0,0,'C');
    
    $pdf->ln(8);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(182,10,utf8_decode('ARTICULO PRIMERO: Reconocer y cancelar a los funcionarios y trabajadores la suma correspondiente a las'),0,0,'L');
    $pdf->ln(8);

    $fechaD2=explode("-",$fechaInicio);
    $annoC2=$fechaD2[0];				#Dia
    $mesC2 = (int) $fechaD2[1];		#Mes
    $diaC2= $fechaD2[2];				#Año 
    
    $fechaD3=explode("-",$fechafin);
    $annoC3=$fechaD3[0];				#Dia
    $mesC3 = (int) $fechaD3[1];		#Mes
    $diaC3= $fechaD3[2];				#Año 


    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(180,8,utf8_decode(ucwords(mb_strtolower('horas extras diurnas, nocturnas, dominicales y festivas laboradas del ' . $diaC2 .' de ' .$meses[$mesC2] .' de ' .$annoC2 .' al '. $diaC3 .' de ' .$meses[$mesC3] .' de ' .$annoC3 ))),0,'L');
    $pdf->AddPage();
    $pdf->Ln(10);
     
    $rowcn = $con->Listar("SELECT DISTINCT e.id_unico, CONCAT_WS(' ', t.apellidouno, t.apellidodos, 
    t.nombreuno, t.nombredos ) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
     WHERE n.periodo = ".$periodo." AND c.clase = 9 AND e.id_unico IS NOT NULL
     order by CONCAT_WS(' ', t.apellidouno, t.apellidodos, t.nombreuno, t.nombredos )");

for ($c = 0; $c < count($rowcn); $c++) {
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(60,10,(($rowcn[$c][1])),0,0,'L');
  $pdf->ln(4);

  $rowcn2 = $con2->Listar("SELECT DISTINCT cv.descripcion as concepto , SUM(n.valor) as horas, 
  SUM(nv.valor) as valor FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
   LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
   LEFT JOIN gn_concepto cv ON c.id_unico = cv.conceptorel
    LEFT JOIN gn_novedad nv ON cv.id_unico = nv.concepto 
    and nv.empleado = e.id_unico and nv.periodo = n.periodo 
    WHERE e.id_unico = ".$rowcn[$c][0]." AND n.periodo = ".$periodo." AND c.clase = 9 
    GROUP by e.id_unico, c.id_unico, cv.id_unico");
    for ($c2 = 0; $c2 < count($rowcn2); $c2++) {
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(120,10,utf8_decode(ucwords(mb_strtolower($rowcn2[$c2][0]))),0,0,'L');
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(20,10,utf8_decode(ucwords(mb_strtolower($rowcn2[$c2][1]))),0,0,'R');
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(26,10,utf8_decode(ucwords(mb_strtolower(number_format($rowcn2[$c2][2],0, ',', ',')))),0,0,'R');
      $pdf->ln(4);
    }
    $rowcn4 = $con2->Listar("SELECT DISTINCT SUM(nv.valor) as valor FROM gn_novedad n 
        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gn_concepto cv ON c.id_unico = cv.conceptorel
        LEFT JOIN gn_novedad nv ON cv.id_unico = nv.concepto 
        and nv.empleado = e.id_unico and nv.periodo = n.periodo 
        WHERE e.id_unico = ".$rowcn[$c][0]." AND n.periodo = ".$periodo." AND c.clase = 9");
        $pdf->Cell(318,10,utf8_decode('======='),0,0,'C');
        $pdf->ln(4);
        $pdf->Cell(166,10,utf8_decode(ucwords(mb_strtolower(number_format($rowcn4[0][0],0, '.', '.')))),0,0,'R');
       
        $pdf->ln(4);
        
    $pdf->ln(4);  
    if($pdf->GetY()>220){
      $pdf->AddPage();
      $pdf->ln(10);  
    }
}  



 

$rowcn3 = $con->Listar("SELECT DISTINCT SUM(nv.valor) as valor FROM gn_novedad n 
    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
    LEFT JOIN gn_concepto cv ON c.id_unico = cv.conceptorel
    LEFT JOIN gn_novedad nv ON cv.id_unico = nv.concepto 
    and nv.empleado = e.id_unico and nv.periodo = n.periodo 
    WHERE n.periodo = ".$periodo." AND c.clase = 9");
 $pdf->SetFont('Arial','',10);
       
$pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower('TOTAL EXTRAS MES DE ' .$meses[$mesC2].' de '.$annoC.'................... '.number_format($rowcn3[0][0],2, '.', '.')))),0,0,'L');
$pdf->ln(2); 
$pdf->Cell(0,20,utf8_decode('ARTICULO SEGUNDO: La presente Resolución rige a partir de la fecha de su expedición.'),0,0,'L');
$pdf->ln(15); 
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,20,utf8_decode('COMUNÍQUESE Y CÚMPLASE'),0,0,'C');
$pdf->ln(15); 
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower(' Dada en Duitama a los '.$valor.' ('.$fechaD[0].') dias del mes '.$meses[$mesC].' de '.$annoC))),0,0,'L');

$rowcn5 = $con->Listar("SELECT IF(tr.razonsocial IS NULL OR tr.razonsocial ='', 
CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos), tr.razonsocial) AS NOMBRE ,
c.nombre, rd.orden,rd.fecha_inicio, rd.fecha_fin, trs.nombre FROM gf_responsable_documento rd 
LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico 
LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico 
LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico 
LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico 
LEFT JOIN gf_tipo_responsable trs ON trs.id_unico = rd.tiporesponsable 
WHERE td.nombre = 'Resolucion Horas Extras' ORDER BY rd.orden ASC");

$pdf->ln(20); 
$pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower($rowcn5[0][0]))),0,0,'L');

$pdf->ln(4); 
$pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower($rowcn5[0][1]))),0,0,'L');


$pdf->ln(6); 
$pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower('Elaboró: '.$rowcn5[1][0].', '.$rowcn5[1][1]))),0,0,'L');


$pdf->ln(4); 
$pdf->Cell(0,10,utf8_decode(ucwords(mb_strtolower('Revisó: '.$rowcn5[2][0].', '.$rowcn5[2][1]))),0,0,'L');



ob_end_clean();		
    $pdf->Output(0,'Resolucion_Horas_Extras.pdf',0);
?>