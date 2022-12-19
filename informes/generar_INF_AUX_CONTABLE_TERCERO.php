<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#12/04/2018 |Erica G. | Parametrización
#25/08/2017 |Erica G. | Logo, firmas
#05/07/2017  | Erica G.| MOSTRAR LA CUENTA QUE TENGA SALDO INICIAL ASI NO TENGA MOVIMIENTO
# 21/04/2017 | Erica G.| CAMBIAR TERCERO POR TERCERO DEL DETALLE
# 06/04/2017 | Erica G | VERIFICACION CONSULTAS Y FILTROS
# 13/03/2017 | ERICA G | AÑADIR CAMPO NUMERO COMPROBANTE
# 06/03/2017 | ERICA G | ARREGLO CONSULTAS FECHA COMPROBANTE
# 03/03/2017 | Erica G //Arreglo de consultas
###############################################################
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
$paranno  = $_SESSION['anno'];
$compania = $_SESSION['compania'];

$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$cuentaini      = $mysqli->real_escape_string(''.$_POST["sltctai"].'');
$cuentafin      = $mysqli->real_escape_string(''.$_POST["sltctaf"].'');
$terceroI       = $mysqli->real_escape_string(''.$_POST["sltTeri"].'');
$terceroF       = $mysqli->real_escape_string(''.$_POST["sltTerf"].'');


#Conversión Fecha para Cabecera
$fecha1 = $fechaini;
$fecha1 = trim($fecha1, '"');
$fecha_div = explode("/", $fecha1);
$dia1 = $fecha_div[0];
$mes1 = $fecha_div[1];
$anio1 = $fecha_div[2];
$fecha1 = $dia1.'/'.$mes1.'/'.$anio1;

$fecha2 = $fechafin;
$fecha2 = trim($fecha2, '"');
$fecha_div = explode("/", $fecha2);
$dia2 = $fecha_div[0];
$mes2 = $fecha_div[1];
$anio2 = $fecha_div[2];
$fecha2 = $dia2.'/'.$mes2.'/'.$anio2;



#Fecha Previa - Comienzo
$fechaP     = $fechaini;
$fechaP     = trim($fechaP, '"');
$fecha_div  = explode("/", $fechaP);
$dia      = $fecha_div[0];
$mes        = $fecha_div[1];
$anio        = $fecha_div[2];
$diaA       = intval($dia);
$diaAnt     = $diaA-1;
#Rutina para obtener la fecha del día anterior
if ($diaAnt < 1)
 {
    switch ($mes)
    {
        case 1:
            $mes = 1;
            $diaAnt = 01;
         break;
        case 2:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 3:
            $mes = $mes - 1;
            $diaAnt = 29;
         break;
        case 4:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 5:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
        case 6:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 7:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
        case 8:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 9:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 10:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
        case 11:
            $mes = $mes - 1;
            $diaAnt = 31;
         break;
        case 12:
            $mes = $mes - 1;
            $diaAnt = 30;
         break;
    }
    $fechaP = $anio.'-'.$mes.'-'.$diaAnt;
}
#Fin Rutina para obtener la fecha previa*/
if($diaAnt<10){
    $diaAnt = '0'.$diaAnt;
}
 $fechaP = $anio.'-'.$mes.'-'.$diaAnt;

 
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
    global $comp1;
    global $comp2;        
    global $compMin;
    global $compMax;        
    global $fecha1;       
    global $fecha2;       
    global $cuenta1;      
    global $cuenta2;
    global $cuentaMin;      
    global $cuentaMax;
    global $centroIn;
    global $centroFi;
    global $numpaginas;
    global $ruta;
    $numpaginas=$numpaginas+1;
    
    $this->SetFont('Arial','B',10);
    //$this->Ln(1);
        // Título
    $this->SetY(10);
  if($ruta != '')
    {
      $this->Image('../'.$ruta,60,6,20);
    }
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
    $this->Cell(315,5,utf8_decode('AUXILIAR CONTABLE TERCERO'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(25);
    $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->SetX(25);
    $this->Cell(315,5,utf8_decode('Entre Terceros '.$centroIn.' y '.$centroFi),0,0,'C');
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->SetX(25);
    $this->Cell(315,5,utf8_decode('Comprobantes '.$comp1.' y '.$comp2),0,0,'C');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->SetX(25);
    $this->Cell(315,5,utf8_decode('entre Fechas '.$fecha1.' y '.$fecha2),0,0,'C');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->SetX(25);
    $this->Cell(315,5,utf8_decode('y Cuentas '.$cuentaMin.' a '.$cuentaMax),0,0,'C');
    
    $this->Ln(5 );
    
    $this->SetX(20);
    
    $this->Cell(15,9,utf8_decode(''),1,0,'C');
    $this->Cell(17,9,utf8_decode(''),1,0,'C');
    $this->Cell(30,9,utf8_decode(''),1,0,'C');
    $this->Cell(30,9,utf8_decode(''),1,0,'C');
    $this->Cell(55,9,utf8_decode('Nombre del Tercero'),1,0,'C');
    $this->Cell(60,9,utf8_decode('Descripción'),1,0,'C');
    $this->Cell(20,9,utf8_decode(''),1,0,'C');
    $this->Cell(32,9,utf8_decode(''),1,0,'C');
    $this->Cell(32,9,utf8_decode(''),1,0,'C');
    $this->Cell(32,9,utf8_decode(''),1,0,'C');
    
    $this->SetX(20);
    
    $this->Cell(15,9,utf8_decode('Fecha'),0,0,'C');
    $this->Cell(17,6,utf8_decode('Tipo'),0,0,'C');
    $this->Cell(30,6,utf8_decode('Número'),0,0,'C');
    $this->Cell(30,6,utf8_decode('Centro'),0,0,'C');
    $this->Cell(55,6,utf8_decode(''),0,0,'C');
    $this->Cell(60,9,utf8_decode(''),0,0,'C');
    $this->Cell(20,6,utf8_decode('N° De '),0,0,'C');
    $this->Cell(32,9,utf8_decode('Valor Débito'),0,0,'C');
    $this->Cell(32,9,utf8_decode('Valor Crédito'),0,0,'C');
    $this->Cell(32,9,utf8_decode('Saldo'),0,0,'C');
    
    $this->Ln(4);
    
    $this->SetX(20);
    
    $this->Cell(15,4,utf8_decode(''),0,0,'C');
    $this->Cell(17,4,utf8_decode('Comprobante'),0,0,'C');
    $this->Cell(30,4,utf8_decode('Comprobante'),0,0,'C');
    $this->Cell(30,4,utf8_decode('de Costo'),0,0,'C');
    $this->Cell(55,4,utf8_decode(''),0,0,'C');
    $this->Cell(60,4,utf8_decode(''),0,0,'C');
    $this->Cell(20,4,utf8_decode('Documento'),0,0,'C');
    $this->Cell(32,4,utf8_decode(''),0,0,'C');
    $this->Cell(32,4,utf8_decode(''),0,0,'C');
    $this->Cell(32,4,utf8_decode(''),0,0,'C');
    
    $this->Ln(6);
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


// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

    $compuno = $compini;
    $compdos = $compfin;        
    $fechauno = $fechaini;       
    $fechados = $fechafin;       
    $cuentauno = $cuentaini;      
    $cuentados = $cuentafin;
    
    
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];

$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum, t.ruta_logo as ruta 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Consulta Minimo Comprobante
#$compMin = 0;
$comp1 = "SELECT             
                    tc.id_unico,
                    tc.nombre
            FROM gf_tipo_comprobante tc 
            WHERE tc.sigla = '$compini'";
$mincomp = $mysqli->query($comp1);
while ($filamin = mysqli_fetch_array($mincomp)) 
{
 $compMin = $filamin['nombre'];   
}
#Fin Consulta Minimo Comprobante
$comp1 = $compMin;
#Consulta Maximo Comprobante
#$compmax = 0;
$comp2 = "SELECT                 
                    tc.id_unico,
                    tc.nombre
FROM  gf_tipo_comprobante tc 
WHERE tc.sigla = '$compfin'";
$maxcomp = $mysqli->query($comp2);
while ($filamax = mysqli_fetch_array($maxcomp)) 
{
 $compMax = $filamax['nombre'];   
}
#Fin Consulta Maximo Comprobante
$comp2=$compMax;

$mincta = "";
$maxcta = "";
#Consulta Mínima Cuenta
$cta1 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentaini";
$mincta = $mysqli->query($cta1);

while ($filac1 = mysqli_fetch_array($mincta)) 
{
 $cuentaMin = $filac1['codi_cuenta'];   
}
#Fin Consulta Mínima Cuenta
$cuenta1 = $cuentaMin;
#Inicio consulta Máxima Cuenta
$cta2 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentafin";
$maxcta = $mysqli->query($cta2);
while ($filac2 = mysqli_fetch_array($maxcta)) 
{
 $cuentaMax = $filac2['codi_cuenta'];   
}
#Fin Consulta Maxima Cuenta
$cuenta2 = $cuentaMax;

$nomcomp = "";
$tipodoc = "";
$numdoc = "";
    
while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = ($fila['traz']);       
        $tipodoc = ($fila['tnom']);       
        $numdoc = ($fila['tnum']);   
        $ruta = ($fila['ruta']);   
    }

$nb=$pdf->AliasNbPages();

#Consulta Cuentas
$sql3 = "SELECT DISTINCT COUNT(id_unico) as tctas, codi_cuenta "
        . "from gf_cuenta WHERE codi_cuenta "
        . "BETWEEN '$cuentaMin' AND '$cuentaMax' AND parametrizacionanno = $paranno  ORDER BY codi_cuenta ASC";
$ccuentas = $mysqli->query($sql3);

while ($filactas = mysqli_fetch_array($ccuentas)) 
{
    $numctas = $filactas['tctas'];   
}

##CONSULTA CENTRO DE COSTO#
$centroIn= "SELECT IF(CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) = '',
                  (ter.razonsocial),
                  CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos)) AS NOMBRE, ter.numeroidentificacion 
                    FROM gf_tercero ter WHERE ter.numeroidentificacion='$terceroI'";
$centroIn =$mysqli->query($centroIn);
$centroIn = mysqli_fetch_row($centroIn);
$centroIn = $centroIn[0];

$centroFi= "SELECT IF(CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) = '',
                  (ter.razonsocial),
                  CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos)) AS NOMBRE, ter.numeroidentificacion 
                    FROM gf_tercero ter WHERE ter.numeroidentificacion='$terceroF' ";
$centroFi =$mysqli->query($centroFi);
$centroFi = mysqli_fetch_row($centroFi);
$centroFi = $centroFi[0];


$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
$compania = $_SESSION['compania'];
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);


//$pdf->SetXY(164,10);
//$pdf->Cell(20, 5, $nomcomp,0,0,'C');
//$pdf->SetXY(164,14);
$pdf->SetFont('Arial','',8);
$pdf->SetX(25);    

$codd    = 0;
$totales = 0;
$valorA = 0;

#Variables de valor de naturaleza
$debito = "";
$credito = "";
$totaldeb = 0.00;
$totalcred = 0.00;

$saldoT = 0;
$saldoTT = 0;

//$pdf->setY(37);
#$pdf->SetY(39);
$cnt = 0;
//for ($cnt=$cuentaini; $cnt<=$cuentafin;$cnt++)
    //$cuentas = "SELECT id_unico FROM gf_cuenta";
    $cuentas = "SELECT DISTINCT cuenta FROM gf_detalle_comprobante dc "
            . "LEFT JOIN gf_cuenta c ON dc.cuenta= c.id_unico "
            . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
            . "WHERE dc.valor IS NOT NULL AND cn.parametrizacionanno = $paranno   AND c.codi_cuenta BETWEEN '$cuentaMin' AND '$cuentaMax' 
 ORDER BY c.codi_cuenta ASC";
    $cuenta = $mysqli->query($cuentas);
    
    $yp = $pdf->GetY();
    
    $totaldebT=0;
    $totalcredT=0; 
    while ($filacuenta = mysqli_fetch_array($cuenta)) 
    {
        $cuent = $filacuenta['cuenta'];
        $cnt = $cuent;   
    $idcuen = "SELECT codi_cuenta, nombre FROM gf_cuenta WHERE id_unico = '$cnt'";
    $codcuen = $mysqli->query($idcuen);
    while ($filacuen = mysqli_fetch_array($codcuen)) 
    {
        $codicuenta = $filacuen['codi_cuenta'].' - '.ucwords(mb_strtolower($filacuen['nombre']));   
    }

########################Consulta Principal################################################# 
 $fecha11 = trim($fechaini, '"');
$fecha_div1 = explode("/", $fecha11);
$dia11 = $fecha_div1[0];
$mes11 = $fecha_div1[1];
$anio11 = $fecha_div1[2];
$fechaini1 =$anio11.'/'.$mes11.'/'.$dia11;
$fecha12 = trim($fechafin, '"');
$fecha_div2 = explode("/", $fecha12);
$dia12 = $fecha_div2[0];
$mes12 = $fecha_div2[1];
$anio12 = $fecha_div2[2];
$fechafin1 =$anio12.'/'.$mes12.'/'.$dia12;     
    
$sql = "SELECT DISTINCT
                                                cn.id_unico             as cnid,
                                                cn.tipocomprobante      as cntcom,
                                                cn.numero               as cnnum,
                                                cn.tercero              as cnter, 
                                                tr.id_unico             as trid,
                                                tr.nombreuno            as trnom1,
                                                tr.nombredos            as trnom2,
                                                tr.apellidouno          as trape1,
                                                tr.apellidodos          as trape2,
                                                tr.razonsocial          as trsoc,
                                                ti.nombre               as tinom,
                                                tr.numeroidentificacion as trnum,
                                                ct.id_unico             as ctid,
                                                ct.sigla               as ctnom,
                                                cc.id_unico             as ccid,
                                                cc.nombre               as ccnom,
                                                cn.numerocontrato       as cnnumcont,
                                                ec.nombre               as ecnom,
                                                cn.descripcion          as cndesc,
                                                dc.comprobante          as dccomp,
                                                dc.centrocosto          as dccos,
                                                cn.fecha                as dcfec,
                                                cen.id_unico            as cencid,
                                                cen.nombre              as cennom,
                                                cta.codi_cuenta         as nomcta,
                                                cta.naturaleza          as natcta,
                                                dc.valor                as dcvalor,
                                                dc.cuenta               as dcuenta,
                                                cn.fecha                as cnfec, 
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
                                                tr.apellidodos)) AS NOMBRE, 
                                                dc.id_unico 
                                        FROM gf_comprobante_cnt cn
                                        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
                                        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
                                        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
                                        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
                                        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
                                        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
                                        WHERE dc.valor IS NOT NULL AND dc.cuenta = '$cnt'
                                        AND cn.fecha BETWEEN '$fechaini1' AND '$fechafin1'
                                        AND ct.sigla BETWEEN '$compini' AND '$compfin'  AND ct.clasecontable != 5 
                                        AND tr.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF'  
                                        AND cn.parametrizacionanno = $paranno      
                                        ORDER BY cn.fecha ASC";
     
$cp      = $mysqli->query($sql);
###########################################Fin Consulta Principal################################# 
#Consulta Secundaria
$a=$_SESSION['anno'];
$anno="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $a";
$anno = $mysqli->query($anno);
if(mysqli_num_rows($anno)>0){
    $anno = mysqli_fetch_row($anno);
    $anno = $anno[0];
} else {
    $anno = date('Y');
}

        if ($fechaini1 != $anno.'/01/01') 
        { 
            
$sql2  = "SELECT DISTINCT
                                                cn.id_unico             as cnid,                                                
                                                cn.tipocomprobante      as cntcom,
                                                cn.numero               as cnnum,
                                                cn.tercero              as cnter, 
                                                tr.id_unico             as trid,
                                                tr.nombreuno            as trnom1,
                                                tr.nombredos            as trnom2,
                                                tr.apellidouno          as trape1,
                                                tr.apellidodos          as trape2,
                                                tr.razonsocial          as trsoc,
                                                ti.nombre               as tinom,
                                                tr.numeroidentificacion as trnum,
                                                ct.id_unico             as ctid,
                                                ct.sigla                as ctnom,
                                                cc.id_unico             as ccid,
                                                cc.nombre               as ccnom,
                                                cn.numerocontrato       as cnnumcont,
                                                ec.nombre               as ecnom,
                                                cn.descripcion          as cndesc,
                                                dc.comprobante          as dccomp,
                                                dc.centrocosto          as dccos,
                                                dc.fecha                as dcfec,
                                                cen.id_unico            as cencid,
                                                cen.nombre              as cennom,
                                                cta.codi_cuenta         as nomcta,
                                                cta.naturaleza          as natcta,
                                                dc.valor                as dcvalor,
                                                dc.cuenta               as dcuenta,
                                                dc.fecha                as cnfec, 
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
                                                tr.apellidodos)) AS NOMBRE, dc.id_unico  
                                        FROM gf_comprobante_cnt cn
                                        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
                                        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante 
                                        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico 
                                        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
                                        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
                                        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
                                        WHERE dc.cuenta = '$cnt'
                                        AND cn.fecha BETWEEN '$anno-01-01' AND '$fechaP'
                                        AND ct.sigla BETWEEN '$compini' AND '$compfin' 
                                        AND tr.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF'  
                                        AND cn.parametrizacionanno = $paranno  
                                        ORDER BY dc.fecha ASC";
        } #WHERE dc.cuenta BETWEEN '$cuentaini' AND '$cuentafin'
        elseif($fechaini1 == $anno.'/01/01') { //            
            $sql2  = "SELECT DISTINCT
                                                cn.id_unico             as cnid,                                                
                                                cn.tipocomprobante      as cntcom,
                                                cn.numero               as cnnum,
                                                cn.tercero              as cnter, 
                                                tr.id_unico             as trid,
                                                tr.nombreuno            as trnom1,
                                                tr.nombredos            as trnom2,
                                                tr.apellidouno          as trape1,
                                                tr.apellidodos          as trape2,
                                                tr.razonsocial          as trsoc,
                                                ti.nombre               as tinom,
                                                tr.numeroidentificacion as trnum,
                                                ct.id_unico             as ctid,
                                                ct.sigla                as ctnom,
                                                cc.id_unico             as ccid,
                                                cc.nombre               as ccnom,
                                                cn.numerocontrato       as cnnumcont,
                                                ec.nombre               as ecnom,
                                                cn.descripcion          as cndesc,
                                                dc.comprobante          as dccomp,
                                                dc.centrocosto          as dccos,
                                                dc.fecha                as dcfec,
                                                cen.id_unico            as cencid,
                                                cen.nombre              as cennom,
                                                cta.codi_cuenta         as nomcta,
                                                cta.naturaleza          as natcta,
                                                dc.valor                as dcvalor,
                                                dc.cuenta               as dcuenta,
                                                dc.fecha                as cnfec, 
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
                                                tr.apellidodos)) AS NOMBRE , dc.id_unico 
                                        FROM gf_comprobante_cnt cn
                                        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
                                        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
                                        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
                                        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante 
                                        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
                                        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
                                        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
                                        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
                                        WHERE dc.cuenta = '$cnt'
                                        AND cn.fecha = '$anno-01-01' 
                                        AND ct.clasecontable = 5 
                                        AND tr.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF'  
                                        AND cn.parametrizacionanno = $paranno  
                                        ORDER BY dc.fecha ASC";//Empty Query
        }
        $csaldo = $mysqli->query($sql2);
echo $sql2;
echo '<br/>';
$saldoTA = 0.00;

#########################################################################################
###################Consulta para obtener sumatoria de Saldos#############################
#########################################################################################  

    
while ($filasal =   mysqli_fetch_array($csaldo)) 
    {
     #if ($filasal['cnfec']<='2016-01-01')
     #{     
     echo $filasal['natcta'].'<br/>'  ;
         #Naturaleza Débito
         if ($filasal['natcta']==1) {
             if ($filasal['dcvalor']>=0) {
                 $debA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $debA;
                 $debitoA = number_format($filasal['dcvalor'],2,'.',',');
             }else{
                 $debitoA = "0.00";
             }
             //$saldoT = $saldoT - $deb;
         }  elseif ($filasal['natcta']==2) {
             if($filasal['dcvalor']<=0){
                 $debA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $debA;
                 $xA = (float) substr($filasal['dcvalor'],'1');
                 $debitoA = number_format($xA,2,'.',',');
             }else{
                 $debitoA = "0.00";
             }
             
            }
        #Fin Naturaleza Débito
        # 
        #Naturaleza Crédito
            if($filasal['natcta']==2){
             if ($filasal['dcvalor']>=0) {
                 $crA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $crA;
                 $creditoA = number_format($filasal['dcvalor'],2,'.',',');
             }else{
                 $creditoA = "0.00";
             }
             //$saldoT = $saldoT - $cr;
            }elseif($filasal['natcta']==1){
                 if($filasal['dcvalor']<=0){
                 $crA = $filasal['dcvalor'];
                 $saldoTA = $saldoTA + $crA;
                 $yA = (float) substr($filasal['dcvalor'], '1');
                 $creditoA = number_format($yA,2,'.',',');
                 }  else {
                 $creditoA = "0.00";
                }
         }
     
     #    }
     }

      
        
       
################################### 
# Fin Consulta para llenar Saldos #
###################################

if(mysqli_num_rows($cp)>0) {
$pdf->SetFont('Arial','B',7);
   $pdf->SetX(20);
   $pdf->Cell(165,4,utf8_decode('Código Cuenta: '.$codicuenta),0);
   $pdf->Cell(20,4,utf8_decode('Saldo Inicial: '),0);
   $pdf->Cell(40,4,utf8_decode('$'.number_format($saldoTA,2,'.',',')),0);
   $pdf->Cell(205,4,'',0);
   $pdf->Ln(4);    
$pdf->SetFont('Arial','',7);

//$saldoTT = $ttdeb+$ttcred;

/*$pdf->SetXY(277,37);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(32,5,'Saldo Inicial: ',1,0,'R');
$pdf->Cell(32,5,number_format($saldoT,2,'.',','),0,0,'R');*/

$tmp = 0;

$cuenta1 = $cuentaini;
$cuenta2 = 0;

$saldoT = $saldoTA;
#for($i = 0; $i<=12; $i++)
#{
#######################################################
############CONSULTA PARA LLENAR LA TABLA##############
#######################################################
$totaldeb=0;
$totalcred=0;

while ($fila = mysqli_fetch_array($cp)) 
        {  
    if($fila['ctnom']=='SLI'){
        
    } else {
       $paginactual = $numpaginas;
    $deb=0;
        if ($fila['natcta']==1) {
            if ($fila['dcvalor']>=0) {
                $deb = $fila['dcvalor'];            
                $tmp = $deb;
                $saldoT = $saldoT + $deb;
                $debito = number_format($fila['dcvalor'],2,'.',',');
                $totaldeb = $totaldeb+$fila['dcvalor'];
            }else{
                $debito = "0.00";
            }
            //$saldoT = $saldoT - $deb;
        }  elseif ($fila['natcta']==2) {
            if($fila['dcvalor']<=0){
                $deb = $fila['dcvalor'];
                $tmp = $deb;
                $saldoT = $saldoT + $deb;
                $x = (float) substr($fila['dcvalor'],'1');
                $debito = number_format($x,2,'.',',');
                $totaldeb = $totaldeb+$x;
            }else{
                $debito = "0.00";
            }
            
        }
        #Fin Naturaleza Débito
     $cr = 0;   
        #Naturaleza Crédito
        if($fila['natcta']==2){
            if ($fila['dcvalor']>=0) {
                $cr = $fila['dcvalor'];
                $saldoT = $saldoT + $cr;
                $credito = number_format($fila['dcvalor'],2,'.',',');
                $totalcred=$totalcred+$fila['dcvalor'];

            }else{
                $credito = "0.00";
            }
            //$saldoT = $saldoT - $cr;
        }elseif($fila['natcta']==1){
            if($fila['dcvalor']<=0){
                $cr = $fila['dcvalor'];
                $saldoT = $saldoT + $cr;
                $y = (float) substr($fila['dcvalor'], '1');
                $credito = number_format($y,2,'.',',');
                $totalcred=$totalcred+$y;
                }  else {
                $credito = "0.00";
            }
            //$saldoT = $saldoT - $cr;
        }
        #Fin Naturaleza Crédito
        $codd = $codd + 1;
        $fechaCC = $fila['cnfec'];
        $fechaCC = trim($fila['cnfec'], '"');
        $fecha_div = explode("-", $fechaCC);
        $anio = $fecha_div[0];
        $mes = $fecha_div[1];
        $dia = $fecha_div[2];
        $fechaCC = $dia.'/'.$mes.'/'.$anio;
        #Fecha - Fin
    
        $pdf->SetX(20);    
    
        $pdf->Cell(15,4,utf8_decode($fechaCC),0,0,'C');
        $pdf->Cell(17,4,utf8_decode($fila['ctnom']),0,0,'C');
         $pdf->Cell(30,4,utf8_decode($fila['cnnum']),0,0,'R');
        $pdf->Cell(30,4,utf8_decode($fila['cennom']),0,0,'L');
        
       $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();
        $pdf->MultiCell(55,4,utf8_decode(ucwords(mb_strtolower($fila['NOMBRE'] )).' - '.$fila['trnum']),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y1;
        $px = $x1+55;
        if($numpaginas>$paginactual){
           $pdf->SetXY($px,$yp);
           $h=$y2-$yp;
        } else {
            $pdf->SetXY($px,$y1);
        }
        
        $y2 = $pdf->GetY();
        $x2 = $pdf->GetX();
        $pdf->MultiCell(60,4,utf8_decode($fila['cndesc']),0,'J');
        $y22 = $pdf->GetY();
        $h1 = $y22-$y2;
        $px2 = $x2+60;
        if($numpaginas>$paginactual){
           $pdf->SetXY($px2,$yp);
           $h1=$y22-$yp;
        } else {
            $pdf->SetXY($px2,$y2);
        }
        
        $pdf->CellFitScale(20,4,utf8_decode($fila['cnnumcont']),0,0,'R');
        $pdf->Cell(32,4,utf8_decode($debito),0,0,'R');
        
        $pdf->Cell(32,4,utf8_decode($credito),0,0,'R');
        $pdf->Cell(32,4,utf8_decode(number_format($saldoT,2,'.',',')),0,0,'R');
        $alto = max($h,$h1);
        $pdf->Ln($alto);
        $paginactual=$numpaginas;
    }
        }
    $pdf->Ln(3);
    $pdf->SetX(20);
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(227,4,'TOTALES',0,0,'R');
    $pdf->Cell(32,4,utf8_decode(number_format($totaldeb,2,'.',',')),0,0,'R');
    $pdf->Cell(32,4,utf8_decode(number_format($totalcred,2,'.',',')),0,0,'R');
    $pdf->Ln(3);
    $totaldebT=$totaldebT+$totaldeb;
    $totalcredT=$totalcredT+$totalcred;  
  }
}
    $pdf->Ln(3);
    $pdf->SetX(220);
    $pdf->Cell(123,0.5,'',1);
    $pdf->SetFont('Arial','B',7);
    $pdf->Ln(3);
    $pdf->SetX(20);
    $pdf->Cell(227,4,'TOTALES',0,0,'R');
    $pdf->Cell(32,4,utf8_decode(number_format($totaldebT,2,'.',',')),0,0,'R');
    $pdf->Cell(32,4,utf8_decode(number_format($totalcredT,2,'.',',')),0,0,'R');
    $pdf->Ln(5);
    $pdf->SetX(20);
    $pdf->Cell(323,0.5,'',1);
    
     ################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(20);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='auxiliar contable' AND td.compania = $compania  ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
 if(mysqli_num_rows($res)>0){
     $h=4;
     while ($row2 = mysqli_fetch_row($res)) {
         
         $ter = "SELECT IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBREC, "
                 . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                 . "FROM gf_tercero tr "
                 . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                 . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                 . "WHERE tr.id_unico ='$row2[0]'";
         
         $ter = $mysqli->query($ter);
         $ter = mysqli_fetch_row($ter);
         if(!empty($ter[3])){
                 $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
         } else {
             $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
         }
         
         $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');
        
         if($i==1){
           $pdf->Ln(15);
           $x=130;
           $i=0;
         } else {
         $pdf->Ln(-20);
         $pdf->SetX($x);
         $x=$x+110;
          $i=$i+1;
         }
        
     }
     
 }      
    
while (ob_get_length()) {
  ob_end_clean();
}
$pdf->Output(0,'Informe_Auxiliares_Contables_Tercero ('.date('d/m/Y').').pdf',0);
?>