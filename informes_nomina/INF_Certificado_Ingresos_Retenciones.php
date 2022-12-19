<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
require_once('../numeros_a_letras.php'); 
require'../Conexion/ConexionPDO.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$nanno      = anno($_REQUEST['anno']);
$tercero    = $_REQUEST['tercero'];

#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                ter.digitoverficacion, 
                c.nombre, d.rss, c.rss 
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_ciudad c ON ter.ciudadidentificacion = c.id_unico 
LEFT JOIN 	gf_departamento d ON c.departamento = d.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$digito      = $rowC[0][4]; 
$ciudad      = $rowC[0][5]; 
$cod_dep     = $rowC[0][6]; 
$cod_mun     = $rowC[0][7]; 

# * Datos Tercero 
$rowt = $con->Listar("SELECT t.id_unico, t.nombreuno, t.nombredos, 
    t.apellidouno, t.apellidodos, 
    ti.sigla, 
    t.numeroidentificacion, t.digitoverficacion 
    FROM gf_tercero t 
    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
    WHERE t.id_unico = $tercero");

# Fecha Expedición 
$fechac =  explode("/",$_REQUEST['fecha']);
class PDF extends FPDF
{
    function Header(){    
        $this->SetY(10);
        $this->Image('../images/logo-DIAN.png',13,11,32);
        $this->Image('../images/muisca.png',147,13,32);
        $this->Image('../images/numero.png',182,11,27);
    }  
}

$pdf = new PDF('P','mm','legal');     
$pdf->AliasNbPages();
$pdf->AddPage();
$yp = $pdf->GetY();        
    $pdf->SetDrawColor(1,138,56);
    $pdf->SetFont('Arial','B',10);  
    $pdf->Cell(40,15,'',1,0,'C');
    $pdf->Cell(95,5,utf8_decode('Certificado de Ingresos y Retenciones'),'T',0,'C'); 
    $pdf->Ln(5);
    $pdf->Cell(40,5,'',0,0,'C');
    $pdf->Cell(95,5,utf8_decode('por Rentas de Trabajo y de Pensiones'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(40,5,'',0,0,'C');
    $pdf->Cell(95,5,utf8_decode('Año gravable '.$nanno),'B',0,'C');
    $pdf->SetXY(145,10);    
    $pdf->Cell(36,15,'',1,0,'C');
    $pdf->Cell(29,15,'',1,0,'C');
    $pdf->Ln(15);
    $pdf->SetFont('Arial','B',7);
    $pdf->SetTextColor(1,138,56);
    $pdf->Cell(120,8,utf8_decode('Antes de diligenciar este formulario lea cuidadosamente las instrucciones'),1,0,'C');
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(80,8,utf8_decode('4. Número de formulario'),1,0,'L');
    $pdf->Ln(8);
    $pdf->Cell(5,22,utf8_decode(''),1,0,'C');
    $pdf->Cell(55,5,utf8_decode('5. Número de Identificación Tributaria (NIT)'),'T',0,'L');
    $pdf->Cell(8,5,utf8_decode('6. DV.'),'TR',0,'C');
    $pdf->Cell(33,5,utf8_decode('7. Primer apellido'),'T',0,'L');
    $pdf->Cell(33,5,utf8_decode('8. Segundo apellido'),'T',0,'L');
    $pdf->Cell(33,5,utf8_decode('9. Primer nombre'),'T',0,'L'); 
    $pdf->Cell(33,5,utf8_decode('10. Otros nombres'),'TR',0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(55,7,utf8_decode($numeroIdent),'BR',0,'L');
    $pdf->Cell(8,7,utf8_decode($digito),'BR',0,'C');
    $pdf->Cell(33,7,utf8_decode(''),'BR',0,'L');
    $pdf->Cell(33,7,utf8_decode(''),'BR',0,'L');
    $pdf->Cell(33,7,utf8_decode(''),'BR',0,'L'); 
    $pdf->Cell(33,7,utf8_decode(''),'BR',0,'L');
    $pdf->Ln(7);
    $pdf->SetX(15);
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(195,5,utf8_decode('11. Razón social'),'TR',0,'L');
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(195,5,utf8_decode($razonsocial),'BR',0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',7);
    $pdf->TextWithDirection(13,($pdf->GetY()-4),utf8_decode('Retenedor'),'U');
    $pdf->Cell(5,16,utf8_decode(''),1,0,'C');
    $pdf->Cell(30,7,utf8_decode('24. Tipo de documento'),'T',0,'L');
    $pdf->Cell(37,7,utf8_decode('25. Número de Identificación'),'T',0,'L');
    $pdf->Cell(128,7,utf8_decode('Apellidos y nombres'),'LTR',0,'L');   
    $pdf->Ln(7);
    $pdf->SetX(15);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(30,9,utf8_decode($rowt[0][5]),'BR',0,'L');
    $pdf->Cell(37,9,utf8_decode($rowt[0][6]),'BR',0,'L');
    $pdf->Cell(32,9,utf8_decode($rowt[0][3]),'BR',0,'L');
    $pdf->Cell(32,9,utf8_decode($rowt[0][4]),'BR',0,'L');
    $pdf->Cell(32,9,utf8_decode($rowt[0][1]),'BR',0,'L'); 
    $pdf->Cell(32,9,utf8_decode($rowt[0][2]),'BR',0,'L');
    $pdf->Ln(9);
    $pdf->SetFont('Arial','B',7);
    $pdf->TextWithDirection(13,($pdf->GetY()-2),utf8_decode('Trabajador'),'U');
    $pdf->SetX(10);
    $pdf->Cell(75,5,utf8_decode('Período de la Certificación'),'LTR',0,'C');
    $pdf->Cell(32,5,utf8_decode('Fecha Expedición'),'LTR',0,'C');
    $pdf->Cell(51,5,utf8_decode('33.Lugar donde se practicó la retención'),'LTR',0,'L');
    $pdf->Cell(20,5,utf8_decode('34. Cód.Dpto.'),'LTR',0,'L');
    $pdf->Cell(22,3,utf8_decode('35. Cód. Ciudad/'),'LTR',0,'L');
    $pdf->Ln(3);
    $pdf->SetX(188);
    $pdf->Cell(22,3,utf8_decode('Municipio'),'LR',0,'L');
    $pdf->Ln(5);
    $pdf->SetXY(10,76);
    #Campos periodo certificación -fechas
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(10,7,utf8_decode('30. DE'),'BRL',0,'L');
    $pdf->Cell(10,7,utf8_decode($nanno),1,0,'L');
    $pdf->Cell(8,7,utf8_decode('01'),1,0,'L');
    $pdf->Cell(8,7,utf8_decode('01'),1,0,'L');
    $pdf->Cell(10,7,utf8_decode('31. DE'),'BRL',0,'L');
    $pdf->Cell(10,7,utf8_decode($nanno),1,0,'L');
    $pdf->Cell(8,7,utf8_decode('12'),1,0,'L');
    $pdf->Cell(8, 7,utf8_decode('31'),1,0,'L');
    $pdf->Cell(3,7,utf8_decode(''),'RB',0,'L'); //espacios
    #Campos fecha expedicion - fecha
    $pdf->Cell(3,7,utf8_decode(''),'LB',0,'L'); //espacios
    $pdf->Cell(10,7,utf8_decode($fechac[2]),1,0,'L');
    $pdf->Cell(8,7,utf8_decode($fechac[1]),1,0,'L');
    $pdf->Cell(8,7,utf8_decode($fechac[0]),1,0,'L');
    $pdf->Cell(3,7,utf8_decode(''),'RB',0,'L');//espacios
    #Campos Lugar retención y códigos
    $pdf->Cell(51,7,utf8_decode($ciudad),'LB',0,'L');  
    $pdf->Cell(20,7,utf8_decode($cod_dep),'BRL',0,'L');
    $pdf->Cell(22,7,utf8_decode($cod_mun),'BRL',0,'L');
    $pdf->Ln(7);
    $pdf->SetX(10);
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(158, 7,utf8_decode('36. Número de agencias, sucursales, filiales o subsidiarias de la empresa retenedora cuyos montos de retención se consolidan'),1,0,'L');
    $pdf->Cell(10, 7,utf8_decode(''),1,0,'L');
    $pdf->Cell(32, 7,utf8_decode(''),1,0,'L');
    $pdf->Ln(7);
    #Conceptos de los Ingresos
   
    $pdf->SetFont('Arial','B',8);  
    $pdf->SetFillColor(204,228,210);
    $pdf->Cell(150, 5,utf8_decode('Concepto de los Ingresos'),'TLR',0,'C', true);
    $pdf->Cell(50, 5,utf8_decode('Valor'),'TLR',0,'C', true);
    $pdf->Ln(5);

    #****** COnceptos de los Ingresos 
    $row = $con->Listar("SELECT DISTINCT id_unico, nombre, numero FROM gn_concepto_certificado 
    WHERE tipo = 1 AND compania = $compania 
    ORDER BY CAST(numero as UNSIGNED) ASC");
    $pdf->SetFont('Arial','',8);  
    $tr = 0;
    $ni = 0;
    $nf = 0;
    for ($i = 0; $i < count($row); $i++) {
        if($i==0){
            $ni = $row[$i][2];
        }
        if($i==count($row)-1){
            $nf = $row[$i][2];
        }
        $cc = $row[$i][0];
        $rowv = $con->Listar("SELECT SUM(n.valor)
            FROM gn_configuracion_certificado cn 
            LEFT JOIN gn_concepto c ON cn.concepto_nomina = c.id_unico 
            LEFT JOIN gn_novedad n ON n.concepto = c.id_unico 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            LEFT JOIN gn_empleado e ON  n.empleado = e.id_unico 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
            WHERE cn.concepto_certificado = $cc  
            AND cn.parametrizacionanno = ".$_REQUEST['anno']."
            AND t.id_unico = ".$_REQUEST['tercero']."
            AND p.parametrizacionanno = ".$_REQUEST['anno']);
        
        if ($i%2==1){ 
            $pdf->SetFillColor(242,248,243);
            $pdf->Cell(150, 5,utf8_decode($row[$i][1]),'LR',0,'L', true);
            $pdf->Cell(5, 5,utf8_decode($row[$i][2]),'LR',0,'L', true);
            $pdf->Cell(45, 5,number_format($rowv[0][0], 2,'.', ','),'LR',0,'R', true);
            $pdf->Ln(5);
        } else {            
            $pdf->Cell(150, 5,utf8_decode($row[$i][1]),'LR',0,'L');
            $pdf->Cell(5, 5,utf8_decode($row[$i][2]),'LR',0,'L');
            $pdf->Cell(45, 5,number_format($rowv[0][0], 2,'.', ','),'LR',0,'R');
            $pdf->Ln(5);
        }
        $tr += $rowv[0][0];
        
    }
    $pdf->SetFont('Arial','B',8);  
    $pdf->Cell(150, 5,utf8_decode('Total de ingresos brutos (Sume '.$ni.' a '.$nf.')'),'LR',0,'L', true);
    $pdf->Cell(5, 5,utf8_decode($nf+1),'LR',0,'L', true);
    $pdf->Cell(45, 5,number_format($tr, 2,'.', ','),'LR',0,'R', true);
    $pdf->Ln(5);

     #Conceptos de los Aportes 
    $pdf->SetFillColor(204,228,210);  
    $pdf->Cell(150, 5,utf8_decode('Concepto de los aportes'),'TLR',0,'C', true);
    $pdf->Cell(50, 5,utf8_decode('Valor'),'TLR',0,'C', true);
    $pdf->Ln(5);    
    
    $pdf->SetFont('Arial','',8);  
    #****** COnceptos de los Aportes 
    $row = $con->Listar("SELECT DISTINCT id_unico, nombre, numero FROM gn_concepto_certificado 
    WHERE tipo = 2 AND compania = $compania 
    ORDER BY CAST(numero as UNSIGNED) ASC");
    $pdf->SetFont('Arial','',8);  
    for ($i = 0; $i < count($row); $i++) {
        $cc = $row[$i][0];
        $rowv = $con->Listar("SELECT SUM(n.valor)
            FROM gn_configuracion_certificado cn 
            LEFT JOIN gn_concepto c ON cn.concepto_nomina = c.id_unico 
            LEFT JOIN gn_novedad n ON n.concepto = c.id_unico 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            LEFT JOIN gn_empleado e ON  n.empleado = e.id_unico 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
            WHERE cn.concepto_certificado = $cc  
            AND cn.parametrizacionanno = ".$_REQUEST['anno']."
            AND t.id_unico = ".$_REQUEST['tercero']."
            AND p.parametrizacionanno = ".$_REQUEST['anno']);
        if($i == count($row)-1){
            $pdf->SetFont('Arial','B',8);  
            $pdf->SetFillColor(1,138,56);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(150, 5,utf8_decode($row[$i][1]),1,0,'L', true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(5, 5,utf8_decode($row[$i][2]),1,0,'L');
            $pdf->Cell(45, 5,number_format($rowv[0][0], 2,'.', ','),1,0,'R');
            $pdf->Ln(5); 
        }elseif ($i%2==1){ 
            $pdf->SetFillColor(242,248,243);
            $pdf->Cell(150, 5,utf8_decode($row[$i][1]),'LR',0,'L', true);
            $pdf->Cell(5, 5,utf8_decode($row[$i][2]),'LR',0,'L', true);
            $pdf->Cell(45, 5, number_format($rowv[0][0], 2,'.', ','),'LR',0,'R', true);
            $pdf->Ln(5);
        } else {            
            $pdf->Cell(150, 5,utf8_decode($row[$i][1]),'LR',0,'L');
            $pdf->Cell(5, 5,utf8_decode($row[$i][2]),'LR',0,'L');
            $pdf->Cell(45, 5,number_format($rowv[0][0], 2,'.', ','),'LR',0,'R');
            $pdf->Ln(5);
        }
        
    }
    
    $pdf->SetFont('Arial','',8); 
    $pdf->SetX(10);
    $pdf->Cell(200, 5,utf8_decode('Nombre del pagador o agente retenedor'),'TLR',0,'L');
    $pdf->SetX(10);
    $pdf->Ln(5);
    $pdf->Cell(200, 7,utf8_decode($razonsocial),'BLR',0,'L');
    $pdf->Ln(7);

    #DATOS A CARGO DEL TRABAJADOR O PENSIONADO

    $pdf->SetFillColor(204,228,210);  
    $pdf->Cell(200, 5,utf8_decode('Datos a cargo del trabajador o pensionado'),'TLR',0,'C', true);
    $pdf->Ln(5);
    $pdf->SetFillColor(242,248,243);
    $pdf->Cell(100, 5,utf8_decode('Concepto de otros ingresos'),1,0,'C', true);
    $pdf->Cell(50, 5,utf8_decode('Valor Recibido'),1,0,'C', true);
    $pdf->Cell(50, 5,utf8_decode('Valor Retenido'),1,0,'C', true);
    $pdf->Ln(5);

    $pdf->SetFont('Arial','',8);  
    $pdf->Cell(100, 5,utf8_decode('Arrendamientos'),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('54'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('61'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);

    $pdf->SetFillColor(242,248,243);
    $pdf->Cell(100, 5,utf8_decode('Honorarios, comisiones y servicios'),'LR',0,'L', true);
    $pdf->Cell(5, 5,utf8_decode('55'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('62'),'LR',0,'L', true);
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);

   
    $pdf->Cell(100, 5,utf8_decode('Intereses y rendimientos financieros'),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('56'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('63'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');

    $pdf->Ln(5);    
    $pdf->Cell(100, 5,utf8_decode('Enajenación de activos fijos'),'LR',0,'L', true);
    $pdf->Cell(5, 5,utf8_decode('57'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('64'),'LR',0,'L', true);
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);


    $pdf->Cell(100, 5,utf8_decode('Loterías, rifas, apuestas y similares'),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('58'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('65'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);
     
    $pdf->Cell(100, 5,utf8_decode('Otros'),'LR',0,'L', true);
    $pdf->Cell(5, 5,utf8_decode('59'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('66'),'LR',0,'L', true);
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',8); 
    $pdf->Cell(100, 5,utf8_decode('Totales: (Valor recibido: Sume 54 a 59), (Valor retenido: Sume 61 a 66)'),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('60'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(5, 5,utf8_decode('67'),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);
    $pdf->Cell(150, 5,utf8_decode('Total retenciones año gravable '.$nanno.' (Sume 53 + 67)'),'LR',0,'L', true);
    $pdf->Cell(5, 5,utf8_decode('68'),'LR',0,'L', true);
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);

    #BIENES Y DERECHOS POSEÍDOS

    $pdf->SetFillColor(204,228,210);  
    $pdf->Cell(10, 5,utf8_decode('Item'),'TLR',0,'C', true);
    $pdf->Cell(145, 5,utf8_decode('69. Identificación de los bienes y derechos poseídos'),'TLR',0,'C', true);
    $pdf->Cell(45, 5,utf8_decode('70. Valor Patrimonial'),'TLR',0,'C', true);
    $pdf->Ln(5); 

    $pdf->SetFont('Arial','',8);
    $pdf->Cell(10, 5,utf8_decode('1'),'TLR',0,'C');  
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L');  
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);
    
    $pdf->SetFillColor(242,248,243);
    $pdf->Cell(10, 5,utf8_decode('2'),'LR',0,'C', true);  
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);

    $pdf->Cell(10, 5,utf8_decode('3'),'LR',0,'C');     
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);
    
    $pdf->Cell(10, 5,utf8_decode('4'),'LR',0,'C', true);  
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L', true);  
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);

    $pdf->SetX(10);
    $pdf->Cell(10, 5,utf8_decode('5'),'LR',0,'C');     
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L');  
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);
    
    $pdf->Cell(10, 5,utf8_decode('6'),'LR',0,'C', true);  
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L', true);   
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L', true);
    $pdf->Ln(5);
  
    $pdf->Cell(10, 5,utf8_decode('7'),'LR',0,'C');     
    $pdf->Cell(145, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(5);
    
    $pdf->SetFont('Arial','B',8);  
    $pdf->SetFillColor(1,138,56);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(150, 5,utf8_decode('Deudas vigentes a 31 de Diciembre de '.$nanno),1,0,'L', true);
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(5, 5,utf8_decode('71'),1,0,'L');
    $pdf->Cell(45, 5,utf8_decode(''),1,0,'L');
    $pdf->Ln(5);

     $pdf->SetFillColor(204,228,210);  
    $pdf->Cell(200, 5,utf8_decode('Identificación de la persona dependiente de acuerdo al parágrafo 2 del artículo 387 del Estatuto Tributario'),'TLR',0,'C', true);
    $pdf->Ln(5);

    $pdf->SetFont('Arial','',8); 
    $pdf->Cell(50, 5,utf8_decode('72. C.C. o NIT'),'TLR',0,'L');
    $pdf->Cell(100, 5,utf8_decode('73. Apellidos y Nombres'),'TLR',0,'L');
    $pdf->Cell(50, 5,utf8_decode('74. Parentesco'),'TLR',0,'L');

    $pdf->Ln(5);
    $pdf->Cell(50, 6,utf8_decode(''),'BLR',0,'L');
    $pdf->Cell(100, 6,utf8_decode(''),'BLR',0,'L');
     $pdf->Cell(50, 6,utf8_decode(''),'BLR',0,'L');
    $pdf->Ln(6);

    $pdf->SetFont('Arial','',6);         
    $pdf->Cell(150, 5,utf8_decode('Certifico que durante el año gravable de '.$nanno),'TLR',0,'L');
    $pdf->Cell(50, 5,utf8_decode(''),'TLR',0,'L');   
    $pdf->Ln(5);  
    $pdf->Cell(150, 3,utf8_decode('1. Mi patrimonio bruto era igual o inferior a 4.500 UVT ($143.366.000).'),'LR',0,'L');
    $pdf->Cell(50, 3,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(3);  
    $pdf->Cell(150, 3,utf8_decode('2. No fui responsable del impuesto sobre las ventas ni del impuesto nacional al consumo.'),'LR',0,'L');
    $pdf->Cell(50, 3,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(3);  
    $pdf->Cell(150, 3,utf8_decode('3. Mis ingresos brutos fueron inferiores a 1.400 UVT ($44.603.000).'),'LR',0,'L');
    $pdf->Cell(50, 3,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(3);  
    $pdf->Cell(150, 3,utf8_decode('4. Mis consumos mediante tarjeta de crédito no excedieron la suma de 1.400 UVT ($44.603.000).'),'LR',0,'L');
    $pdf->Cell(50, 5,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(3);  
    $pdf->Cell(150, 3,utf8_decode('5. Que el total de mis compras y consumos no superaron la suma de 1.400 UVT ($44.603.000)'),'LR',0,'L');
    $pdf->Cell(50, 3,utf8_decode(''),'LR',0,'L');
    $pdf->Ln(3);  
    $pdf->Cell(150, 3,utf8_decode('6. Que el valor total de mis consignaciones bancarias, depósitos o inversiones financieras no excedieron los 1.400 UVT ($44.603.000).'),'LR',0,'L');
    $pdf->Cell(50, 3,utf8_decode(''),'LR',0,'L');   
    $pdf->Ln(3);  
    $pdf->Cell(150, 4,utf8_decode(' Por lo tanto, manifiesto que no estoy obligado a presentar declaración de renta y complementario por el año gravable '.$nanno.'.'),'BLR',0,'L');
    $pdf->Cell(50, 4,utf8_decode(''),'BLR',0,'L');
  


while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'Certificado_Ingresos_Retenciones ('.date('d/m/Y').').pdf',0);
