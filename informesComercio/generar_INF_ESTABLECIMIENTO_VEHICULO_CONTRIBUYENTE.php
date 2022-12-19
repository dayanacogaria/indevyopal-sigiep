<?php

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
?>



<?php

                $idContribuyente=$_POST['contribuyente'];

                //Fin Conversión Fecha / Hora
                $hoy = date('d-m-Y');
                $hoy = trim($hoy, '"');
                $fecha_div = explode("-", $hoy);
                $anioh = $fecha_div[2];
                $mesh = $fecha_div[1];
                $diah = $fecha_div[0];
                $hoy = $diah.'/'.$mesh.'/'.$anioh;

                class PDF extends FPDF
                {  
                // Cabecera de página
                    function Header()
                    {

                        // Logo
                        //$this->Image('logo_pb.png',10,8,33);
                        //Arial bold 15
                        global $nomcomp;
                        global $tipodoc;
                        global $numdoc;
    
                        global $fecha1;
                        global $fecha2;
  



                        global $numpaginas;
                        $numpaginas=$numpaginas+1;

                        $this->SetFont('Arial','B',10);
                        //$this->Ln(1);
                        // Título
                        $this->SetY(10);
                        //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');
                        //$pdf->SetFillColor(232,232,232);

                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode($nomcomp),0,0,'C');
                        // Salto de línea
                        $this->setX(25);
                        $this->SetFont('Arial','B',8);
                        $this->Cell(315,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

                        $this->Ln(5);

                        $this->SetFont('Arial','',8);
                        $this->SetX(25);
                        $this->Cell(315, 5,$tipodoc.': '.$numdoc,0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

                        $this->Ln(5);

                        $this->SetFont('Arial','',8);
                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode('LISTADO ESTABLECIMIENTOS Y VEHICULOS CONTRIBUYENTE'),0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');

                        $this->Ln(17);

                 
                        $this->SetFont('Arial','B',8);
                        $this->SetX(5);

                        $this->Cell(25,9,utf8_decode(''),1,0,'C');
                        $this->Cell(25,9,utf8_decode(''),1,0,'C');
                        $this->Cell(50,9,utf8_decode(''),1,0,'C');

                        $this->Cell(50,9,utf8_decode('Dirección'),1,0,'C');
                        $this->Cell(35,9,utf8_decode('Ciudad'),1,0,'C');
                        $this->Cell(50,9,utf8_decode('Tipo Vehiculo'),1,0,'C');
                        $this->Cell(50,9,utf8_decode('Tipo Servicio'),1,0,'C');
                        $this->Cell(25,9,utf8_decode('Placa'),1,0,'C');
                        $this->Cell(35,9,utf8_decode('%'),1,0,'C');




                        $this->SetX(5);
                        $this->Cell(25,9,utf8_decode('Establecimiento'),0,0,'C');
                        $this->Cell(25,9,utf8_decode('Vehiculo'),0,0,'C');

                        $this->Cell(50,9,utf8_decode('Nombre'),0,0,'C');

                       

                        $this->Ln(9);
                        
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
                        $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                        $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                        $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
                    }
                }

                $pdf = new PDF('L','mm','Legal');

                    $fechauno = $fechaini;       
                    $fechados = $fechafin;       
                    $compania = $_SESSION['compania'];
                    $usuario = $_SESSION['usuario'];

                    $consulta = "SELECT     t.razonsocial as traz,
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
                    $numdoc = "";
                    
                    while ($fila = mysqli_fetch_array($cmp))
                        {
                            $nomcomp = utf8_decode($fila['traz']);       
                            $tipodoc = utf8_decode($fila['tnom']);       
                            $numdoc = utf8_decode($fila['tnum']);   
                        }


                    $pdf->AddPage();
                    $pdf->AliasNbPages();
                    $pdf->SetFont('Arial','B',10);

                    $pdf->SetFont('Arial','',8);
                    $pdf->SetX(50);
                    $yp = $pdf->GetY();


                    $codd    = 0;
                    $totales = 0;
                    $valorA = 0;

                    //LOGO
                    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
                      FROM gf_tercero ter 
                      LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
                      WHERE ter.id_unico = '.$compania;
                    $rutaLogo = $mysqli->query($sqlRutaLogo);
                    $rowLogo = mysqli_fetch_array($rutaLogo);
                    $ruta = $rowLogo[0];
                    if($ruta != '')
                        {
                          $pdf->Image('../'.$ruta,30,8,20);
                        }
                    $sqle = "SELECT e.id_unico,
                            IF(CONCAT_WS(' ',
                            t.nombreuno,
                            t.nombredos,
                            t.apellidouno,
                            t.apellidodos) 
                            IS NULL OR CONCAT_WS(' ',
                            t.nombreuno,
                            t.nombredos,
                            t.apellidouno,
                            t.apellidodos) = '',
                            (t.razonsocial),
                            CONCAT_WS(' ',
                            t.nombreuno,
                            t.nombredos,
                            t.apellidouno,
                            t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                            e.nombre,
                            DATE_FORMAT(e.fechainicioAct,'%d-%m-%Y') AS fechaFacConvertida,
                            est.nombre,
                            e.direccion,
                            e.cod_catastral,
                            ciu.nombre,
                            b.nombre,
                            l.nombre,
                            te.nombre,
                            tame.nombre
                             
                    FROM gc_establecimiento e
                    LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
                    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                    LEFT JOIN gp_estrato est ON est.id_unico=e.estrato
                    LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
                    LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
                    LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
                    LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
                    LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad
                    WHERE e.contribuyente=$idContribuyente";

                    //CONSULTA ESTABLECIMIENTOS
                    $resultadoe=$mysqli->query($sqle);

                    while($rowe=mysqli_fetch_array($resultadoe)){
                       
                        //llenar datos
                        $pdf->SetX(5);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Cell(25,4,utf8_decode(X),0,0,'C');
                        $pdf->Cell(25,4,utf8_decode(),0,0,'C');



                       $y1 = $pdf->GetY();
                       $x1 = $pdf->GetX();
                       $pdf->MultiCell(50,4,utf8_decode(ucwords(mb_strtolower($rowe[2]))),0,'L');
                       $y2 = $pdf->GetY();
                       $h = $y2-$y1;
                       $px = $x1+50;

                       if($numpaginas>$paginactual){
                           $pdf->SetXY($px,$yp);
                           $h=$y2-$yp;
                       } else {
                           $pdf->SetXY($px,$y1);
                       }
                           
                       $y6 = $pdf->GetY();
                       $x6 = $pdf->GetX();
                       $pdf->MultiCell(50,4,utf8_decode(ucwords(mb_strtolower($rowe[5]))),0,'L');
                       $y7 = $pdf->GetY();
                       $h7 = $y7-$y6;
                       $px7 = $x6+50;

                       if($numpaginas>$paginactual){
                           $pdf->SetXY($px7,$yp);
                           $h7=$y7-$yp;
                       } else {
                           $pdf->SetXY($px7,$y6);
                       }


                        $pdf->Cell(35,4,utf8_decode($rowe[7]),0,0,'C');

                    


                        //salto
                        $alto = max($h,$h7);
                        $pdf->Ln($alto);
                        $paginactual=$numpaginas;
                    }


                    //CONSULTA VEHICULOS 
                                     
                    $sql = "
                    SELECT v.id_unico,

                    IF(CONCAT_WS(' ',
                    t.nombreuno,
                    t.nombredos,
                    t.apellidouno,
                    t.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    t.nombreuno,
                    t.nombredos,
                    t.apellidouno,
                    t.apellidodos) = '',
                    (t.razonsocial),
                    CONCAT_WS(' ',
                    t.nombreuno,
                    t.nombredos,
                    t.apellidouno,
                    t.apellidodos)) AS NOMBRECONTRIBUYENTE ,  

                    tv.nombre,

                    IF(CONCAT_WS(' ',
                    terv.nombreuno,
                    terv.nombredos,
                    terv.apellidouno,
                    terv.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    terv.nombreuno,
                    terv.nombredos,
                    terv.apellidouno,
                    terv.apellidodos) = '',
                    (terv.razonsocial),
                    CONCAT_WS(' ',
                    terv.nombreuno,
                    terv.nombredos,
                    terv.apellidouno,
                    terv.apellidodos)) AS NOMBRETERCERO,  


                    v.cod_inter,
                    tser.nombre AS nombreServicio,
                    v.placa,
                    v.porc_propiedad

                    FROM gc_vehiculo v

                    LEFT JOIN gc_contribuyente c ON c.id_unico=v.contribuyente
                    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                    LEFT JOIN gc_tipo_vehiculo tv ON tv.id_unico=v.tipo_vehiculo
                    LEFT JOIN gf_tercero terv ON terv.id_unico=v.tercero
                    LEFT JOIN gc_tipo_servicio tser ON tser.id_unico=v.tipo_serv
                    WHERE v.contribuyente=$idContribuyente";

                    $resultado=$mysqli->query($sql);

                    while($row=mysqli_fetch_array($resultado)){
                       


                        //llenar datos
                        $pdf->SetX(5);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Cell(25,4,utf8_decode(),0,0,'C');
                        $pdf->Cell(25,4,utf8_decode(X),0,0,'C');



                        $pdf->Cell(50,4,utf8_decode(),0,0,'C');
                        $pdf->Cell(50,4,utf8_decode(),0,0,'C');
                        $pdf->Cell(35,4,utf8_decode(),0,0,'C');

                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(50,4,utf8_decode(ucwords(mb_strtolower($row[2]))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+50;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }

                        $y20 = $pdf->GetY();
                        $x20 = $pdf->GetX();
                        $pdf->MultiCell(50,4,utf8_decode(ucwords(mb_strtolower($row['nombreServicio']))),0,'L');
                        $y222 = $pdf->GetY();
                        $h10 = $y222-$y20;
                        $px2 = $x20+50;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h10=$y222-$yp;
                        } else {
                            $pdf->SetXY($px2,$y20);
                        }

                        //$pdf->Cell(48,4,utf8_decode($row['nombreServicio']),1,0,'L');
                        $pdf->Cell(25,4,utf8_decode($row['placa']),0,0,'C');
                        $pdf->Cell(35,4,utf8_decode($row['porc_propiedad']),0,0,'C');


                        //salto
                        $alto = max($h1,$h10);
                        $pdf->Ln($alto);
                        $paginactual=$numpaginas;
                    }


                    while (ob_get_length()) {
                        ob_end_clean();
                    }
         //nombre del archivo de  descarga           
        $pdf->Output(0,'Listado Establecimientos y Vehiculos Contribuyente ('.date('d/m/Y').').pdf',0);


?>
