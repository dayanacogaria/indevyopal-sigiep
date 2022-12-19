<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
require_once('../numeros_a_letras.php'); 
session_start();
ob_start();
ini_set('max_execution_time', 0);

$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
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
$valor_deuda=0;
$dia_v=0;
$mes_v=0;
$anno_v=0;
//tipo 2
$tipo_acu=$_POST['sltTipo'];
$nacuerdo=$_POST['txtNumeroA'];

if($tipo_acu==1){
       $selecT ="SELECT DISTINCT
         p.id_unico,
         p.nombres,
         p.numero,
         p.direccion as direccion,
         '' as tel,
         '' as digitoverficacion,
         a.valor,         
         DATE_FORMAT(fp.fechafactura, '%d/%m/%Y'),
         doc_ac.soportedeuda,
         DATE_FORMAT(a.fecha, '%d/%m/%Y') 
       FROM          ga_documento_acuerdo doc_ac
       LEFT JOIN gr_factura_predial fp on fp.numero=doc_ac.soportedeuda
       LEFT JOIN gp_tercero_predio tp on tp.predio=fp.predio and tp.orden='001'
       LEFT JOIN gr_propietarios p ON p.id_unico=tp.tercero
       left join ga_acuerdo a on a.id_unico=doc_ac.acuerdo
       WHERE doc_ac.acuerdo='$nacuerdo' and tp.orden='001'";   
       
    }else if ($tipo_acu==2)
    {
            $selecT ="SELECT DISTINCT
             tr.id_unico,IF(CONCAT_WS(' ',
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
                 tr.numeroidentificacion, 
                 dir.direccion, 
                 tel.valor, 
                 tr.digitoverficacion,
                 a.valor,
                 DATE_FORMAT(d.fecha,'%d/%m/%Y'),
                 doc_ac.soportedeuda,
                 DATE_FORMAT(a.fecha, '%d/%m/%Y')
           FROM ga_documento_acuerdo doc_ac
           LEFT JOIN gc_declaracion d on d.cod_dec=doc_ac.soportedeuda
           LEFT JOIN gc_contribuyente c on c.id_unico=d.contribuyente
           LEFT JOIN gf_tercero tr on tr.id_unico=c.tercero 
           LEFT JOIN gf_direccion dir ON dir.tercero = tr.id_unico 
           LEFT JOIN gf_telefono tel ON tel.tercero = tr.id_unico  
           left join ga_acuerdo a on a.id_unico=doc_ac.acuerdo
           WHERE doc_ac.acuerdo='$nacuerdo'";   
    }
    $dt =$mysqli->query($selecT);
    $dt = mysqli_fetch_row($dt);
    $nom_d=$dt[1];
    $nit_cc=number_format($dt[2],0,'.',',');
    $dirc=$dt[3];
    $tel=$dt[4];
    $fech_ac=$dt[9];
    $num_df=$dt[8];
    $fech=$dt[7];
    $f= explode("-", $fech);
    setlocale(LC_TIME, 'spanish');  
    $mes_v=strtoupper(strftime("%B",mktime(0, 0, 0, $f[1], 1, 2000))); 
    $valor_deuda=number_format($dt[6],2,'.',',');
    $dia_v=$f[2];
    $anno_v=$f[0];
    

if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$direccion = $comp[4];
$telefono = $comp[5];
$usuario = $_SESSION['usuario'];
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
#CREACION PDF, HEAD AND FOOTER
class PDF extends FPDF
{
function Header()
{ 
    
    global $fecha1;
    global $fecha2;
    global $cuentaI;
    global $cuentaF;
    global $nombreCompania;
    global $nom_d;
    global $nit_cc;
    global $dirc;
    global $tel;
    global $mes_v; 
    global $valor_deuda;
    global $dia_v;
    global $anno_v;
    global $fech_ac;
    global $num_df;
    global $nitcompania;
    global $numpaginas;
    global $ruta;
    global $direccion;
    global $telefono;
    global $fecha1;
    global $fecha2;
    
    $date1=$fecha1;    
    $date2=$fecha2;    
    $numpaginas=$this->PageNo();   
    
    $this->SetY(10);
   
    if($ruta != '')
    {
      $this->Image('../'.$ruta,20,8,20);
    }
    $this->SetY(10);
    $this->SetFont('Arial','B',12);
    $this->Cell(190,10,utf8_decode('CERTIFICADO DE ACUERDO DE PAGO '),0,0,'C');
    $this->ln(5);
    $this->ln(15);
    $this->SetFont('Arial','',10);
    $this->SetFont('Arial','B',10);
    $this->Cell(30,10,utf8_decode('Fecha:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(40,10,utf8_decode(ucwords(mb_strtolower($fech_ac))),0,0,'L');
    //$this->Cell(110,10,utf8_decode(ucwords(mb_strtolower($nombreCompania))),0,0,'L');
    $this->Cell(40,10,utf8_decode('Numero:'),0,0,'L');
    $this->Cell(40,10,utf8_decode(ucwords(mb_strtolower($num_df))),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->ln(10);
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode('1. IDENTIFICACIÓN DEL DEUDOR'),0,0,'L');
    $this->ln(9);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Nombres y Apellidos Completos:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(90,10,utf8_decode(ucwords(mb_strtolower($nom_d))),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('CC. ó Nit:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(90,10,utf8_decode(ucwords(mb_strtolower($nit_cc))),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Dirección Residencia:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(90,10,utf8_decode(ucwords(mb_strtolower($dirc))),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Número Teléfono:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(90,10,utf8_decode(ucwords(mb_strtolower($tel))),0,0,'L');
    $this->ln(10);
    $this->SetFont('Arial','',10);
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode('2. OBLIGACIÓN EN MORA:'),0,0,'L');
    $this->ln(10);
    $this->SetFont('Arial','',10);
    $this->MultiCell(190,5,utf8_decode("El deudor reconoce la existencia "
            . "de la siguiente obligación a favor del $nombreCompania correspondiente a la suma total "
            . "de ($ $valor_deuda). Se manifiesta expresamente que mediante el presente acuerdo materializa "
            . "su voluntad de pago para que sean recogidos los valores restantes."),0,'J');
    
    $this->SetFont('Arial','B',10);
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode('3. CONDICIONES GENERALES:'),0,0,'L');
    $this->ln(10);
    $this->SetFont('Arial','',10);
    $this->MultiCell(190,5,utf8_decode("- El DEUDOR ha decidido acordar con el ACREEDOR una "
            . "forma de pago de la obligación relacionada, dejando en claro y de manera "
            . "expresa que este arreglo o convenio de pago de ninguna manera constituye novación "
            . "o alguna otra figura que importe extinción de la obligación objeto del arreglo. "),0,'J');
    $this->ln(1);
    $this->MultiCell(190,5,utf8_decode("- Proceso ejecutivo. la obligación relacionada se encuentra vencida "
            . "desde el día $dia_v del mes $mes_v del año $anno_v.  "),0,'J');
    
    $this->SetFont('Arial','B',10);
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode('3. CONDICIONES ESPECIFICAS:'),0,0,'L');
    $this->ln(10);
    $this->SetFont('Arial','',10);
    $this->MultiCell(190,5,utf8_decode("El DEUDOR pagará a la orden del $nombreCompania la obligacion "
            . "señalada en el numeral 2 del presente acuerdo de la siguiente forma"),0,'J');
    $this->SetFont('Arial','B',10);
    
    //cuadro del acuerdo 
    }      
    
    function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    global $usuario;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(40,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(55,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(55,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(40,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('P','mm','Letter');      


$pdf->AliasNbPages();

##############BUSQUEDA DE TERCEROS COMPROBANTES DE RETENCION ENTRE FECHAS#########
if($tipo_acu==1){
       $selecT ="SELECT DISTINCT
         p.id_unico 
       FROM
         ga_documento_acuerdo doc_ac
       LEFT JOIN
          gr_factura_predial fp on fp.numero=doc_ac.soportedeuda
       LEFT JOIN
           gp_tercero_predio tp on tp.predio=fp.predio and tp.orden='001'
       LEFT JOIN
           gr_propietarios p ON p.id_unico=tp.tercero
       WHERE
         doc_ac.acuerdo='$nacuerdo' and tp.orden='001'";   
       
}else if ($tipo_acu==2)
{
        $selecT ="SELECT DISTINCT
         t.id_unico
       FROM
         ga_documento_acuerdo doc_ac
       LEFT JOIN
          gc_declaracion d on d.cod_dec=doc_ac.soportedeuda
       LEFT JOIN
           gc_contribuyente c on c.id_unico=d.contribuyente
       LEFT JOIN
           gf_tercero t on t.id_unico=c.tercero
       WHERE
         doc_ac.acuerdo='$nacuerdo'";   
}
 
$selecT =$mysqli->query($selecT);

while ($row = mysqli_fetch_row($selecT)) {
    $total =0;
        
    $idT=$row[0];
    if($tipo_acu==1){
         $dts="SELECT nombres 
             FROM gr_propietarios WHERE id_unico =$idT ";
    }else if ($tipo_acu==2)
    {
        $dts="SELECT 
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
             tr.numeroidentificacion, 
             tr.digitoverficacion,  
             tr.id_unico, 
             dir.direccion, 
             tel.valor, 
             c.nombre, 
             d.nombre 
         FROM  gf_tercero tr 
         LEFT JOIN 
                gf_direccion dir ON dir.tercero = tr.id_unico 
         LEFT JOIN 
                gf_telefono tel ON tel.tercero = tr.id_unico LEFT JOIN gf_ciudad c ON c.id_unico = tr.ciudadresidencia 
         LEFT JOIN 
                gf_departamento d ON c.departamento = d.id_unico 
         WHERE tr.id_unico =$idT";
    }
       
    #####BUSCAR detalle del acuerdo######
    
        $rt ="select DISTINCT da.nrocuota, DATE_FORMAT(da.fecha,'%d/%m/%Y')
            from ga_detalle_acuerdo da
            where da.acuerdo='$nacuerdo' order by da.nrocuota,da.fecha ";
    
    
    $rt =$mysqli->query($rt);
    $pdf->SetFont('Arial','',10);
    $pdf->AddPage();    
    $pdf->SetFont('Arial','B',6);
    $pdf->Ln(10);
    $cx = $pdf->GetX();
    $cy = $pdf->GetY();
    
    $pdf->Cell(8,5,utf8_decode('Cuota'),0,0,'C');
    $pdf->Cell(14,5,utf8_decode('Fecha Ven.'),0,0,'C');    
    $x =$pdf->GetX();
    $y =$pdf->GetY();    
    $h2 = 0; 
    //buscar los nombres de los conceptos
    if($tipo_acu==1){
        $conp="select DISTINCT c.nombre
              from ga_detalle_acuerdo da
              left join gr_concepto_predial cp on cp.id_unico=da.concepto_deuda
              left join gr_concepto c on c.id_unico=cp.id_concepto
              where da.acuerdo='$nacuerdo' AND da.valor != 0
              order by da.nrocuota,da.concepto_deuda";

        $numero_con = "select COUNT(DISTINCT c.nombre)
              from ga_detalle_acuerdo da
              left join gr_concepto_predial cp on cp.id_unico=da.concepto_deuda
              left join gr_concepto c on c.id_unico=cp.id_concepto
              where da.acuerdo='$nacuerdo' AND da.valor != 0 
              order by da.nrocuota,da.concepto_deuda  ";
    }else if ($tipo_acu==2)
    {
        $conp="select DISTINCT c.nom_inf
              from ga_detalle_acuerdo da
              left join gc_concepto_comercial c on c.id_unico=da.concepto_deuda
              where da.acuerdo='$nacuerdo' AND da.valor != 0
              order by da.nrocuota,da.concepto_deuda";

        $numero_con = "select COUNT(DISTINCT c.nom_inf)
              from ga_detalle_acuerdo da
              left join gc_concepto_comercial c on c.id_unico=da.concepto_deuda
              where da.acuerdo='$nacuerdo' AND da.valor != 0
              order by da.nrocuota,da.concepto_deuda  ";
    }
    $rt_conpt =$mysqli->query($conp);
    //$pdf->SetFont('Arial','',10);
    $n_con = $mysqli->query($numero_con);
    $concn = mysqli_fetch_row($n_con);

    $filas = 160 / $concn[0] ;
     while ($Tcon = mysqli_fetch_row($rt_conpt)) {
   
        $x =$pdf->GetX();
        $y =$pdf->GetY(); 
        $pdf->SetFont('Arial','B',6);
        $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[0]))),0,'C');
        $y2 = $pdf->GetY();
        $h = $y2 - $y;
        if($h > $h2){
            $alto = $h;
            $h2 = $h;
        }else{
            $h2 = $h;
        }
        $pdf->SetXY($x+$filas,$y);
    
    }
    $pdf->SetXY($cx,$cy);
    $pdf->Cell(8,$alto, utf8_decode(''),1,0,'C');
    $pdf->Cell(14,$alto, utf8_decode(''),1,0,'C');
    $con1 = $mysqli->query($conp);
    while ($Tcon = mysqli_fetch_row($con1)) {
   
        $x =$pdf->GetX();
        $y =$pdf->GetY(); 
        $pdf->SetFont('Arial','B',8);
        $pdf->MultiCell($filas,$alto, utf8_decode(),1,'C');
        $pdf->SetXY($x+$filas,$y);
    }
    $pdf->SetFont('Arial','B',6);   
    $pdf->Cell(16,$alto, utf8_decode('TOTAL'),1,0,'C');
    $pdf->Ln($alto);
     $pdf->SetFont('Arial','B',6); 
    $pdf->SetFont('Arial','',6);               
    
    
    while ($row1 = mysqli_fetch_row($rt)) {        
            
        
        $pdf->Cell(8,8,utf8_decode(ucwords(mb_strtolower($row1[0]))),0,'C');
        $pdf->Cell(14,8,utf8_decode($row1[1]),0,0,'R');
        $x =$pdf->GetX();  
        $y =$pdf->GetY();  
        
        if($tipo_acu==1){
                $conp="select   sum(da.valor),c.nombre
                        from ga_detalle_acuerdo da
                        left join gr_concepto_predial cp on cp.id_unico=da.concepto_deuda
                        left join gr_concepto c on c.id_unico=cp.id_concepto
                        where da.acuerdo='$nacuerdo' and da.nrocuota='$row1[0]'  
                            AND da.valor != 0 
                        GROUP by c.id_unico
                        order by da.nrocuota,da.concepto_deuda";
            }else if ($tipo_acu==2)
            {
                $conp="select  sum(da.valor),c.nombre
                        from ga_detalle_acuerdo da
                        left join gc_concepto_comercial c on c.id_unico=da.concepto_deuda
                        where da.acuerdo='$nacuerdo' and da.nrocuota='$row1[0]'  
                            AND da.valor != 0 GROUP by c.id_unico
                         order by da.nrocuota,da.concepto_deuda";
            }
            $rt_conpt_V =$mysqli->query($conp);
            //$pdf->SetFont('Arial','',10);
            $vlr_cta=0;
            while ($row_conpv = mysqli_fetch_row($rt_conpt_V)) {
                $vlr_cta=$vlr_cta+$row_conpv[0];
                $pdf->Cell($filas,8, number_format($row_conpv[0],2,'.',','),0,0,'R');
            }
        
        
        $pdf->Cell(16,8,number_format($vlr_cta,2,'.',','),0,0,'R');
        $pdf->Ln(5);
    }
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln(25);
    $pdf->SetFont('Arial','',10);
    //linea para firma
    $pdf->Cell(55,0,'',1,'R');
    $pdf->Ln(1);
    $pdf->Cell(13,5,'FIRMA DEUDOR',0,0,'L');
    $pdf->Ln(5);
    $pdf->Cell(13,5,'CC.',0,0,'L');
    
$pdf->SetX(-200);
$pdf->Ln(14);
}




while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'Certificado_Acuerdo_Pago ('.date('d/m/Y').').pdf',0);