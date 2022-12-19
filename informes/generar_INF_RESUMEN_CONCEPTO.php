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

$grupog = $_POST['sltGrupoG'];
$periodo  = $_POST['sltPeriodo'];
$clase  = $_POST['sltClase'];  
$concepto  = $_POST['sltConcepto'];



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

if(empty($clase) || $clase ==""){

  $CL = "Todas";

}else{

  $C = "SELECT id_unico, nombre FROM gn_clase_concepto WHERE id_unico = $clase";
  $CC = $mysqli->query($C);
  $CLS = mysqli_fetch_row($CC);

  $CL = $CLS[1];
}

if(empty($concepto)|| $concepto ==""){

  $CO = "Todos";

}else{

  $CON = "SELECT id_unico , descripcion FROM gn_concepto WHERE id_unico = $concepto";
  $CN = $mysqli->query($CON);
  $CONCE = mysqli_fetch_row($CN);

  $CO = $CONCE[1];
}

$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

 $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;




    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = utf8_decode($fila['traz']);       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc = utf8_decode($fila['tnum']);   
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
    global $CL;
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
    $this->Cell(170,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
   $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->SetX(20);
    $this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->Ln(5);
     $this->SetFont('Arial','B',12);
    $this->Cell(190,5,utf8_decode('RESUMEN POR CONCEPTO'),0,0,'C');
    // Salto de línea
    $this->Ln(3);
    $this->SetFont('Arial','B',8);
    $this->SetX(0);

    $this->SetFont('Arial','B',8);
    $this->Cell(37,18,utf8_decode('PERIODO:'),0,0,'C');
    $this->Cell(12,18,utf8_decode($PER),0,0,'C');
    $this->Cell(25.5,18,utf8_decode(''),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->Cell(35,18,utf8_decode('GRUPO GESTION:'),0,0,'L');
    $this->SetFont('Arial','B',8);
    $this->Cell(10,18,utf8_decode(''),0,0,'L');
    $this->Cell(15,18,utf8_decode($GRUP),0,0,'C');
    $this->Ln(4);
    $this->SetX(11);
    $this->Cell(18,18,utf8_decode('Fecha Inicial:'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->Cell(32,18,utf8_decode($FI),0,0,'C');
    $this->Cell(39,18,utf8_decode('CLASE:'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->Cell(34,18,utf8_decode($CL),0,0,'C');
    $this->Ln(4);
    $this->SetX(13);
    $this->Cell(13,19,utf8_decode('Fecha Final:'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->Cell(37.5,18,utf8_decode($FF),0,0,'C');
    $this->Cell(40,18,utf8_decode('CONCEPTO:'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->Cell(27,18,utf8_decode($CO),0,0,'C');
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

$pdf->SetFont('Arial','',8);


//consulta sql

#$cp      = $mysqli->query($sql);
#$codd    = 0;
#Asignación de anchos de columna

if(empty($grupog) && empty($periodo) && empty($clase) && empty($concepto)){

    $clase1 = "SELECT DISTINCT cc.id_unico, cc.nombre FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "LEFT JOIN gn_clase_concepto cc ON c.clase = cc.id_unico ";
    $Class = $mysqli->query($clase1);
       
    while($CLA = mysqli_fetch_row($Class)){

         $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
               . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
               . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 GROUP BY c.id_unico";
       
        $conc = $mysqli->query($concepto1);
        $ncon = mysqli_num_rows($conc);
        
        if($ncon > 0 ){
            
            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('CLASE CONCEPTO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode($CLA[1]),0,0,'C');
            $pdf->Ln(12);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(20,6, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(120,6, utf8_decode('Concepto'),1,0,'C');
            $pdf->Cell(40,6, utf8_decode('Valor'),1,0,'C');
            $pdf->Ln(6);
             
            $mc0=20;$mc1=120;$mc2=40;
            while($CO = mysqli_fetch_row($conc)){
            
                
                
                $pdf->SetFont('Arial','',9);
                $pdf->cellfitscale($mc0,6,utf8_decode($CO[1]),0,0,'C');
                $pdf->cellfitscale($mc1,6,utf8_decode($CO[2]),0,0,'L');
                $pdf->cellfitscale($mc2,6,utf8_decode(number_format($CO[3],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }  

            $pdf->Ln(3);
            $pdf->Cell(180,0.5,'',1);
            $pdf->Ln(3);
            
            $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                    . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 ";
            
            $ClaT = $mysqli->query($Tclas);
            $CT = mysqli_fetch_row($ClaT);
            
            $pdf->SetX(0);
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(100,6, utf8_decode(''),0,0,'C');
            $pdf->Cell(50,6, utf8_decode('TOTAL:'),0,0,'C');
            $pdf->cellfitscale(40,6,utf8_decode(number_format($CT[0],2,'.',',')),0,0,'R');
            $y = $pdf->GetY();
            if($y >= 210  ){
                  $pdf->Ln(40);
            }else{
                $pdf->Ln(4);
            }    
        }
    }
}else{


    if(empty($clase) && empty($concepto  )){

        $clase1 = "SELECT DISTINCT cc.id_unico, cc.nombre FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "LEFT JOIN gn_clase_concepto cc ON c.clase = cc.id_unico ";
        
        $clas = $mysqli->query($clase1);
       
        

            if(empty($concepto)){
                
                while($CLA = mysqli_fetch_row($clas)){ 
                    if(empty($grupog)  && empty($periodo)){ 

                       echo $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 GROUP BY c.id_unico";
                       echo "<br>";
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
        
                        $pdf->SetFont('Arial','B',8);
                        $pdf->SetX(6);

                       echo $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                        . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1  ";
                       echo "<br>";
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);
                    
                    }elseif(!empty($grupog) && empty($periodo)){
                        echo "R";
                       echo $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND e.grupogestion = '$grupog' GROUP BY c.id_unico";
                       echo "<br>";
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND e.grupogestion = '$grupog' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);
                    
                        
                    }elseif(empty($grupog) && !empty($periodo)){

                        $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND n.periodo = '$periodo' GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                        . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.periodo = '$periodo'  ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);
                    
                    }else{
                        
                        $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND n.periodo = '$periodo' AND e.grupogestion = '$grupog' GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
        
                        echo $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);    
                        
                    }
                    if($ncon > 0 ){
            
                        $pdf->SetFont('Arial','B',8);
                        $pdf->SetX(6);

                        $pdf->Cell(37,18,utf8_decode('CLASE CONCEPTO:'),0,0,'C');
                        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
                        $pdf->Cell(12,18,utf8_decode($CLA[1]),0,0,'C');
                        $pdf->Ln(12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Cell(20,6, utf8_decode('Código'),1,0,'C');
                        $pdf->Cell(120,6, utf8_decode('Concepto'),1,0,'C');
                        $pdf->Cell(40,6, utf8_decode('Valor'),1,0,'C');
                        $pdf->Ln(6);
             
                        $mc0=20;$mc1=120;$mc2=40;
                        while($CO = mysqli_fetch_row($conc)){
            
                            $pdf->SetFont('Arial','',9);
                            $pdf->cellfitscale($mc0,6,utf8_decode($CO[1]),0,0,'C');
                            $pdf->cellfitscale($mc1,6,utf8_decode($CO[2]),0,0,'L');
                            $pdf->cellfitscale($mc2,6,utf8_decode(number_format($CO[3],2,'.',',')),0,0,'R');
                            $pdf->Ln(3);
                        }  

                        $pdf->Ln(3);
                        $pdf->Cell(180,0.5,'',1);
                        $pdf->Ln(3);
               
                        $pdf->SetX(0);
                        $pdf->SetFont('Arial','B',9);
                        $pdf->Cell(100,6, utf8_decode(''),0,0,'C');
                        $pdf->Cell(50,6, utf8_decode('TOTAL:'),0,0,'C');
                        $pdf->cellfitscale(40,6,utf8_decode(number_format($CT[0],2,'.',',')),0,0,'R');
                        $y = $pdf->GetY();
                        if($y >= 210  ){
                            $pdf->Ln(40);
                        }else{
                            $pdf->Ln(4);
                        }    
                    }
                }    
                    
                }else{
                    echo "hola p";
                    if(empty($grupog) && empty($periodo)){

                     echo "hola1".$concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' GROUP BY c.id_unico";
                     echo "<br>";
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                       echo"hola1".$Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' ";
                       echo "<br>";
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT); 

                    }elseif(!empty($grupog) && empty($periodo)){
                        echo "E";
                        $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' AND e.grupogestion = '$grupog' "
                        . "GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND e.grupogestion = '$grupog' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);

                    }elseif(empty($grupog) && !empty($periodo) || $periodo !== ""){
                        
                        $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' AND n.periodo = '$periodo' "
                        . "GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND n.periodo = '$periodo' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);
                        
                    }else{

                        $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.di_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' "
                        . "AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);
                    }
                   
            
            
            if($ncon > 0 ){
            
                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);

                $pdf->Cell(37,18,utf8_decode('CLASE CONCEPTO:'),0,0,'C');
                $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
                $pdf->Cell(12,18,utf8_decode($CLA[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(20,6, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(120,6, utf8_decode('Concepto'),1,0,'C');
                $pdf->Cell(40,6, utf8_decode('Valor'),1,0,'C');
                $pdf->Ln(6);
             
                $mc0=20;$mc1=120;$mc2=40;
                while($CO = mysqli_fetch_row($conc)){
            
                    $pdf->SetFont('Arial','',9);
                    $pdf->cellfitscale($mc0,6,utf8_decode($CO[1]),0,0,'C');
                    $pdf->cellfitscale($mc1,6,utf8_decode($CO[2]),0,0,'L');
                    $pdf->cellfitscale($mc2,6,utf8_decode(number_format($CO[3],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }  

                $pdf->Ln(3);
                $pdf->Cell(180,0.5,'',1);
                $pdf->Ln(3);
            
               
                $pdf->SetX(0);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(100,6, utf8_decode(''),0,0,'C');
                $pdf->Cell(50,6, utf8_decode('TOTAL:'),0,0,'C');
                $pdf->cellfitscale(40,6,utf8_decode(number_format($CT[0],2,'.',',')),0,0,'R');
                $y = $pdf->GetY();
                if($y >= 210  ){
                    $pdf->Ln(40);
                }else{
                    $pdf->Ln(4);
                }    
            }    
      
        } 
    
    }elseif(!empty($clase) && empty($concepto)){
        echo "hola C";
        echo$clase1 = "SELECT DISTINCT cc.id_unico, cc.nombre FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "LEFT JOIN gn_clase_concepto cc ON c.clase = cc.id_unico WHERE cc.id_unico = '$clase' ";
        echo "<br>";
        $clas = $mysqli->query($clase1);
                     
        $mc0=20;$mc1=120;$mc2=40;
    
        while($CLA = mysqli_fetch_row($clas)){ 

            if(empty($concepto)){

                if(empty($grupog)  && empty($periodo)){ 
                    echo "hola1";
                    $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0  "
                        . "GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1  ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);

                }elseif(!empty($grupog) && empty($periodo)){
                                        echo "hola2";

                   echo $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND e.grupogestion = '$grupog' "
                        . "GROUP BY c.id_unico";
                   echo "<br>";
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                    echo    $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND e.grupogestion = '$grupog' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);

                }elseif(empty($grupog) && !empty($periodo)){
                    echo "hola3";

                    $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND n.periodo = '$periodo' "
                        . "GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1  AND n.periodo = '$periodo' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);

                }else{
                                        echo "hola4";

                    $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' "
                        . "GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT);
                }
            }else{
                echo "z";
                if(empty($grupog) && empty($periodo)){

                    $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto'  "
                        . "GROUP BY c.id_unico";
       
                    $conc = $mysqli->query($concepto1);
                    $ncon = mysqli_num_rows($conc);
                        
                    $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                            . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto'  ";
            
                    $ClaT = $mysqli->query($Tclas);
                    $CT = mysqli_fetch_row($ClaT);

                }elseif(!empty($grupog) && empty($periodo)){

                    $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' AND e.grupogestion = '$grupog' "
                        . "GROUP BY c.id_unico";
       
                    $conc = $mysqli->query($concepto1);
                    $ncon = mysqli_num_rows($conc);
                        
                    $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unhico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND egrupogestion = '$grupog' ";
            
                    $ClaT = $mysqli->query($Tclas);
                    $CT = mysqli_fetch_row($ClaT);

                }elseif(empty($grupog) && !empty($periodo) || $periodo !== ""){

                   $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' AND n.periodo = '$periodo' "
                        . "GROUP BY c.id_unico";
       
                    $conc = $mysqli->query($concepto1);
                    $ncon = mysqli_num_rows($conc);
                        
                    $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND n.periodo = '$periodo' ";
            
                    $ClaT = $mysqli->query($Tclas);
                    $CT = mysqli_fetch_row($ClaT); 

                }else{

                    $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                        . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' "
                        . "AND n.periodo = '$periodo' AND e.grupogestion = '$grupog'"
                        . "GROUP BY c.id_unico";
       
                        $conc = $mysqli->query($concepto1);
                        $ncon = mysqli_num_rows($conc);
                        
                        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND n.periodo = '$periodo' AND e.grupogestion = '$grupog' ";
            
                        $ClaT = $mysqli->query($Tclas);
                        $CT = mysqli_fetch_row($ClaT); 
                }
            }
            if($ncon > 0 ){
            
                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);

                $pdf->Cell(37,18,utf8_decode('CLASE CONCEPTO:'),0,0,'C');
                $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
                $pdf->Cell(12,18,utf8_decode($CLA[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(20,6, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(120,6, utf8_decode('Concepto'),1,0,'C');
                $pdf->Cell(40,6, utf8_decode('Valor'),1,0,'C');
                $pdf->Ln(6);
             
                $mc0=20;$mc1=120;$mc2=40;
                while($CO = mysqli_fetch_row($conc)){
            
                    $pdf->SetFont('Arial','',9);
                    $pdf->cellfitscale($mc0,6,utf8_decode($CO[1]),0,0,'C');
                    $pdf->cellfitscale($mc1,6,utf8_decode($CO[2]),0,0,'L');
                    $pdf->cellfitscale($mc2,6,utf8_decode(number_format($CO[3],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }  

                $pdf->Ln(3);
                $pdf->Cell(180,0.5,'',1);
                $pdf->Ln(3);
            
               
                $pdf->SetX(0);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(100,6, utf8_decode(''),0,0,'C');
                $pdf->Cell(50,6, utf8_decode('TOTAL:'),0,0,'C');
                $pdf->cellfitscale(40,6,utf8_decode(number_format($CT[0],2,'.',',')),0,0,'R');
                $y = $pdf->GetY();
                if($y >= 210  ){
                    $pdf->Ln(40);
                }else{
                    $pdf->Ln(4);
                }    
            }
        
        }
    }
}
     

if(!empty($concepto)){
    
    echo "hola V";

   $clase1 = "SELECT DISTINCT cc.id_unico, cc.nombre FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "LEFT JOIN gn_clase_concepto cc ON c.clase = cc.id_unico WHERE c.id_unico = '$concepto' ";
    $claco = $mysqli->query($clase1);
    
    while($CLA = mysqli_fetch_row($claco)){
    
        if(empty($grupog)  && empty($periodo)){ 
         echo "hola 1";
        echo$concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                    . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "where  c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' "
                    . "GROUP BY c.id_unico";
       echo "<br>";
        $conc = $mysqli->query($concepto1);
        $ncon = mysqli_num_rows($conc);
                        
        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                . "WHERE  c.unidadmedida = 1 AND c.id_unico = '$concepto'  ";
            
        $ClaT = $mysqli->query($Tclas);
        $CT = mysqli_fetch_row($ClaT); 
              

        }elseif(!empty($grupog) && empty($periodo)){
        echo "hola2";
        echo $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                    . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' "
                    . " AND e.grupogestion = '$grupog'"
                    . "GROUP BY c.id_unico";
       echo "<br>";
        $conc = $mysqli->query($concepto1);
        $ncon = mysqli_num_rows($conc);
                        
        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto'  AND e.grupogestion = '$grupog' ";
            
        $ClaT = $mysqli->query($Tclas);
        $CT = mysqli_fetch_row($ClaT); 

        }elseif(empty($grupog) && !empty($periodo)){
echo "hola3";
     echo   $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                    . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' "
                    . "AND n.periodo = '$periodo' "
                    . "GROUP BY c.id_unico";
       echo "<br>";
       echo "clase".$CLA[0];
        $conc = $mysqli->query($concepto1);
        $ncon = mysqli_num_rows($conc);
                        
       echo $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND n.periodo = '$periodo'  ";
            
        $ClaT = $mysqli->query($Tclas);
        $CT = mysqli_fetch_row($ClaT); 
        }else{
echo "hola4";
        $concepto1 = "SELECT DISTINCT c.id_unico, c.codigo, c.descripcion, SUM(n.valor)  "
                . "FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                . "where c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND n.valor > 0 AND c.id_unico = '$concepto' "
                . "AND n.periodo = '$periodo' AND e.grupogestion = '$grupog'"
                . "GROUP BY c.id_unico";
       
        $conc = $mysqli->query($concepto1);
        $ncon = mysqli_num_rows($conc);
                        
        $Tclas = "SELECT SUM(n.valor) FROM gn_novedad n "
                . "LEFT JOIN gn_concepto c ON  n.concepto = c.id_unico "
                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                . "WHERE c.clase = '$CLA[0]' AND c.unidadmedida = 1 AND c.id_unico = '$concepto' AND n.periodo = '$periodo' AND e.grupogestion = '$grupog' ";
            
        $ClaT = $mysqli->query($Tclas);
        $CT = mysqli_fetch_row($ClaT); 
        }
    
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);
    $pdf->Cell(37,18,utf8_decode('CLASE CONCEPTO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode($CLA[1]),0,0,'C');
    $pdf->Ln(12);
         
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(20,6, utf8_decode('Código'),1,0,'C');
    $pdf->Cell(120,6, utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(40,6, utf8_decode('Valor'),1,0,'C');
    $pdf->Ln(8);
    $mc0=20;$mc1=120;$mc2=40;
    while($CO = mysqli_fetch_row($conc)){

        
        $pdf->SetFont('Arial','',9);
        $pdf->cellfitscale($mc0,6,utf8_decode($CO[1]),0,0,'C');
        $pdf->cellfitscale($mc1,6,utf8_decode($CO[2]),0,0,'L');
        $pdf->cellfitscale($mc2,6,utf8_decode(number_format($CO[3],2,'.',',')),0,0,'R');
        $pdf->Ln(5);
    }

    $pdf->Ln(3);
    $pdf->Cell(180,0.5,'',1);
    $pdf->Ln(3);
        
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(140,5, utf8_decode('TOTAL:'),0,0,'C');
    $pdf->cellfitscale($mc2,6,utf8_decode(number_format($CT[0],2,'.',',')),0,0,'R');
    $pdf->Ln(4); 
    }
    
}        

ob_end_clean();
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);  
?>
