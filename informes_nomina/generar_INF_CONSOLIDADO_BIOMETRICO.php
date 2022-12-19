

<?php
#************ 22/10/2021- Elkin O- Se crea informe donde se muestran los conceptos de horas extras y se compara con
# con la info del archivo biometrico que se ingresa, dando asi la diferencia dependiendo el empleado***********#
setlocale(LC_TIME, 'es_ES');
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
ini_set('max_execution_time', 0); 
session_start();
$con  = new ConexionPDO();
$compania = $_SESSION['compania'];
$periodo=$_POST['sltPeriodo'];
$periodoM="SELECT codigointerno FROM gn_periodo
WHERE  id_unico='$periodo'";
$pe=$mysqli->query($periodoM);
$rowP=mysqli_fetch_row($pe);
$empleadoI=$_POST['sltEmpleadoI'];
$empleadoF=$_POST['sltEmpleadoF']; 
$docBioI="SELECT numeroidentificacion
 FROM gf_tercero t 
 LEFT JOIN gn_empleado e ON e.tercero=t.id_unico
 WHERE e.id_unico=$empleadoI";
 $eI=$mysqli->query($docBioI);
 $docI=mysqli_fetch_row($eI);

 $docBioF="SELECT numeroidentificacion
 FROM gf_tercero t 
 LEFT JOIN gn_empleado e ON e.tercero=t.id_unico
 WHERE e.id_unico=$empleadoI";
 $eF=$mysqli->query($docBioF);
 $docF=mysqli_fetch_row($eF);


#   ************   Datos Compañia   ************    #
$rowC = $con->Listar("SELECT
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
$razonsocial = $rowC[0][0];
$nombreEm = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$ciudad = $rowC[0][4];
$direccinTer = $rowC[0][5];
$telefonoTer = $rowC[0][6];
$ruta_logo   = $rowC[0][7];
$email   = $rowC[0][8];
$t     = ''; 

 #   ************   Armado de documento   ************    #

    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    {
        function Header(){ 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            global $emple;
            global $ciudad;
            global $email;
            global $afiliacion;
            global $afilia;
            $numpaginas=$numpaginas+1;
            $this->SetFont('Arial','B',10);
            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,10,5,28);
            }
            $this->SetY(10);
            $this->SetFont('Arial','',10);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($razonsocial))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($direccinTer))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($email))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower('NIT '.$numeroIdent))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower('TEL.'.$telefonoTer))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($ciudad))),0,0,'R');
            $this->ln(10);
        }      
        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(260,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
             
    }
    $pdf = new PDF('P','mm','Letter');   
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetY(50);
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,utf8_decode("CONSOLIDADO BIOMÉTRICO"),0,0,"C");
    $pdf->Ln(8);
    $pdf->Cell(200,5,('Periodo: '.$rowP[0]),0,0,"C");
    $pdf->Ln(15);
    $nombreEmp="SELECT CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos),numeroidentificacion,e.id_unico
    FROM gf_tercero t 
    LEFT JOIN gn_empleado e ON e.tercero=t.id_unico
    WHERE e.id_unico BETWEEN '$empleadoI' and '$empleadoF'";
    $nomE=$mysqli->query($nombreEmp);
    while($rowNom = mysqli_fetch_row($nomE)){

        $sqlConfig="SELECT concepto, nombre_campo 
        FROM gn_homologacion_biometrico";
        $config=$mysqli->query($sqlConfig);

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,5,('NOMBRE EMPLEADO:'),0,0,"L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(160,5,utf8_decode($rowNom[0]),0,0,"L");
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(60,5,utf8_decode('CONCEPTO NÓMINA'),1,0,"L");
        $pdf->Cell(60,5,utf8_decode('CONCEPTO BIOMÉTRICO '),1,0,"L");
        $pdf->Cell(30,5,('VALOR NO'),1,0,"L");
        $pdf->Cell(25,5,('VALOR BIO'),1,0,"L");
        $pdf->Cell(25,5,('DIFERENCIA'),1,0,"L");
 
        $pdf->Ln(5);

    while($rowA = mysqli_fetch_row($config)){
    
       $sqlBio="SELECT 
                SUM(REPLACE($rowA[1], ',', '.')) AS extras
                from gn_empleado_asistencia
                where periodo=$periodo
                and numerodocumento='$rowNom[1]'";
        $bio=$mysqli->query($sqlBio);
        $rowBi = mysqli_fetch_row($bio);
        
       $sqlNo="SELECT n.valor,c.descripcion,c.codigo
                from gn_novedad n 
                 LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                 where periodo=$periodo
                 and n.empleado= '$rowNom[2]'
                 and n.concepto=$rowA[0]";
         $nov=$mysqli->query($sqlNo);
         $rowNo = mysqli_fetch_row($nov);         

         $pdf->SetFont('Arial','',10);
       if($rowNo[1]==null){
            $sqlcf="SELECT c.descripcion,c.codigo
            from gn_concepto c 
            WHERE c.id_unico=$rowA[0]";
            $nomcc=$mysqli->query($sqlcf);
            $rowNocc = mysqli_fetch_row($nomcc);
            
            $pdf->SetFont('Arial','',10);
            $pdf->Cellfitscale(60,5,($rowNocc[1].'-'.$rowNocc[0]),1);
        }else{
            $pdf->Cellfitscale(60,5,($rowNo[2].'-'.$rowNo[1]),1);
        }
        $pdf->SetFont('Arial','',10);
        $pdf->Cellfitscale(60,5,($rowA[1]),1);
        $pdf->SetFont('Arial','',10);


       
         if($rowNo[0]==null){
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,5,('0'),1,0,"R");

        }else{
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,5,($rowNo[0]),1,0,"R");

        }

       

         if($rowBi[0]==null){
        $pdf->Cell(25,5,('0'),1,0,"R");
         }else{
            $pdf->Cell(25,5,($rowBi[0]),1,0,"R");
         }


         $total=$rowNo[0]-$rowBi[0];
         $pdf->SetFont('Arial','B',10);
         $pdf->Cell(25,5,($total),1,0,"R");
         $pdf->Ln();    
    }
$pdf->Ln(10);
    }
   ob_end_clean();		
   $pdf->Output(0,'Consolidado_Biométrico_Horas_Extras.pdf',0);
    ?>
   
        
<?php ?>