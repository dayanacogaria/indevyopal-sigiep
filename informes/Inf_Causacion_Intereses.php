<?php 
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../Conexion/conexionsql.php');
require '../code128.php';
header("Content-Type: text/html;charset=utf-8");

ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$parmanno   = $_SESSION['anno'];
$anno       = anno($parmanno); 

$fechaini     = $_REQUEST['fechaI'];
$fechafin     = $_REQUEST['fechaF'];
$tipoinforme= $_REQUEST['tipoInforme'];



#   ************   Datos Compañia   ************    #

$rowC = $con->Listar("SELECT  ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];    

$ac = 260;

ob_start();
class PDF extends FPDF
{
    function Header(){ 
        
    }      

    function Footer(){
    }
}
//$pdf = new PDF('P','mm',array(140,210));   
$pdf = new PDF_Code128('P','mm','letter'); 
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',10);
if($ruta_logo != ''){
  $pdf->Image('../'.$ruta_logo,18,15,40);
}
$pdf->SetX(10);
$pdf->Cell(57,30, '',1, 0, 'C');
$pdf->SetX(67);
$pdf->SetFont('Arial','B',11.5);
$pdf->MultiCell($ac-118,8, utf8_decode($razonsocial),1,  'C');
$pdf->SetX(67);



if($tipoinforme ==  1){

$pdf->SetFont('Arial','B',10);
$pdf->Cell($ac-118,7,utf8_decode('INFORME DETALLADO DE CAUSACIÓN'),'LTR',0,'C');
$pdf->Ln(7);
$pdf->SetX(67);
$pdf->SetFont('Arial','B',8);
$pdf->Cell($ac-118,7, utf8_decode('ENTRE: '.$fechaini.' Y '.$fechafin ),'LBR',0,'C'); 
$pdf->Ln(7);

      
             $sqltc = "declare @Fecha_I date='".$fechaini."';
                                        declare @Fecha_F date='".$fechafin."';
                                        select tc.Identificador,                                     
                                        tc.Nombre_Tipo_Credito                                       
                                        from CREDITO as c

                                        left join DETALLE_CREDITO as dc on dc.Numero_Credito=c.Numero_Credito
                                        left join SOLICITUD_CREDITOS as s on s.Identificador=c.Id_Solicitud_Credito
                                        left join TIPO_CREDITO as tc on tc.Identificador=s.Id_Tipo_Credito 
                                        where  dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                        and 

                                        (CASE WHEN (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='5'and dci.Numero_Cuota!='0' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='22' ) is null THEN (0) ELSE 
                                        
                                        (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='5'and dci.Numero_Cuota!='0' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='22' ) end)
                                        +

                                        (CASE WHEN (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='18'and dci.Numero_Cuota!='0' and c.Indicador_Gastos_Anticipado='False') is null THEN (0) ELSE 
                                        
                                        (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='18'and dci.Numero_Cuota!='0' and c.Indicador_Gastos_Anticipado='False') end)
                                        +(
                                        CASE WHEN (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='3' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='21') is null THEN (0) ELSE
                                        (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='3' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='21') end)>0 
                                        group by tc.Nombre_Tipo_Credito, tc.Identificador
                                        order by tc.Nombre_Tipo_Credito asc ";
                     $stmt  = sqlsrv_query( $conn, $sqltc); 
                          
                     

                     while( $rows = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) { 

                      $pdf->Ln(8);                       
                      $pdf->SetFont('Arial','B',9); 
                      $pdf->Cell(199,6, utf8_decode('TIPO CRÉDITO: '. $rows['Nombre_Tipo_Credito']),'LTR',0,'C');                     
                      $pdf->Ln(6);

                             
                            $pdf->SetFont('Arial','B',7); 
                            $pdf->Cell(30,5, utf8_decode('NÚMERO DE CRÉDITO'),'LTR',0,'C');
                            $pdf->Cell(24,5, utf8_decode('CÉDULA DEUDOR'),'LTR',0,'C');
                            $pdf->Cell(63,5, utf8_decode('NOMBRE DEUDOR'),'LTR',0,'C');
                            $pdf->Cell(25,5,utf8_decode('VALOR INTERESES'),'LTR',0,'C');
                            $pdf->Cell(25,5,utf8_decode('VALOR SEGURO'),'LTR',0,'C');
                            $pdf->Cell(32,5,utf8_decode('VALOR ADMINISTRACIÓN'),'LTR',0,'C');                         
                            $pdf->Ln(5);

                            $identificador = $rows['Identificador'];
                              
                             $ts_interes       = 0;                               
                             $ts_seguros       = 0;
                             $ts_admon         = 0;                               
                             $ts_total         = 0;

                    
                             

                               $sql = " declare @Fecha_I date='".$fechaini."';
                                        declare @Fecha_F date='".$fechafin."';
                                        declare @Tipo_Credito varchar(20)='".$rows['Identificador']."';

                                        select 
                                        c.Numero_Credito as credito,
                                        tc.Nombre_Tipo_Credito,
                                        (SELECT TOP (1) p.Numero_Documento  FROM PERSONA as p 
                                        LEFT OUTER JOIN PERSONA_SOLICITUD as ps ON ps.Id_persona = p.Numero_Documento 
                                        LEFT OUTER JOIN SOLICITUD_CREDITOS as s ON ps.Id_Solicitud = s.Identificador 
                                        LEFT OUTER JOIN CREDITO as cr ON cr.Id_Solicitud_Credito = s.Identificador
                                        WHERE (cr.Numero_Credito = c.Numero_Credito) AND (ps.Id_Tipo_Relacion_Solicitud = '1'
                                        and ps.Principal='True') order by p.Nombre_Completo) AS NUM_DOC_DEUDOR,

                                        (SELECT TOP (1) p.Nombre_Completo  FROM PERSONA as p 
                                        LEFT OUTER JOIN PERSONA_SOLICITUD as ps ON ps.Id_persona = p.Numero_Documento 
                                        LEFT OUTER JOIN SOLICITUD_CREDITOS as s ON ps.Id_Solicitud = s.Identificador 
                                        LEFT OUTER JOIN CREDITO as cr ON cr.Id_Solicitud_Credito = s.Identificador
                                        WHERE (cr.Numero_Credito = c.Numero_Credito) AND (ps.Id_Tipo_Relacion_Solicitud = '1'
                                        and ps.Principal='True') order by p.Nombre_Completo) AS NOM_DEUDOR,

                                        (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto IN('3', '21') ) as Valor_Concepto,

                                        (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto IN ('5','22') and dci.Numero_Cuota!='0' ) as Valor_Concepto_Seguro,

                                        (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='18' and c.Indicador_Gastos_Anticipado='False') as Valor_Concepto_Admin  

                                        from CREDITO as c

                                        left join DETALLE_CREDITO as dc on dc.Numero_Credito=c.Numero_Credito
                                        left join SOLICITUD_CREDITOS as s on s.Identificador=c.Id_Solicitud_Credito
                                        left join TIPO_CREDITO as tc on tc.Identificador=s.Id_Tipo_Credito 
                                        where  dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                        and s.Id_Tipo_Credito=@Tipo_Credito  and 

                                        (CASE WHEN (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto in ('18') and dci.Numero_Cuota!='0' and c.Indicador_Gastos_Anticipado='False') is null THEN (0) ELSE 
                                        (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto in ('18') and dci.Numero_Cuota!='0' and c.Indicador_Gastos_Anticipado='False') end)
                                        + (CASE WHEN (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto in ('5', '22') and dci.Numero_Cuota!='0' ) is null THEN (0) ELSE 
                                        (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto in ('5', '22')and dci.Numero_Cuota!='0' ) end)
                                        +(
                                        CASE WHEN (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto in ('3', '21') ) is null THEN (0) ELSE
                                        (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto in ('3', '21') ) end)>0 

                                        group by c.Numero_Credito,tc.Nombre_Tipo_Credito,c.Fecha_Desembolso,c.Indicador_Gastos_Anticipado order by c.Numero_Credito asc";

                                 $stmtpm  = sqlsrv_query( $conn, $sql ); 

                                 while( $row = sqlsrv_fetch_array( $stmtpm, SQLSRV_FETCH_ASSOC)) { 

                                                        
                                    $numcredito     = $row["credito"] ;
                                    $cedula         = $row["NUM_DOC_DEUDOR"];
                                    $nombre         = $row["NOM_DEUDOR"];
                                    $interes        = $row["Valor_Concepto"];
                                    $seguros        = $row["Valor_Concepto_Seguro"];
                                    $admon          = $row["Valor_Concepto_Admin"];                                                 
                                    $total          =  ($intereses + $interes + $seguros + $administracion);

                                    $ts_interes       += $interes;
                                    $ts_seguros       += $seguros;                                                
                                    $ts_admon         += $admon;                                                 
                                    $ts_total         += $total;


                                                

                                    $pdf->SetFont('Arial','',7);
                                    $pdf->Cell(30,8, utf8_decode($numcredito),1,0,'C');
                                    $pdf->Cell(24,8, utf8_decode($cedula),1,0,'C');
                                    $pdf->Cell(63,8, utf8_decode(trim($nombre)),1,0,'L');
                                    $pdf->Cell(25,8,number_format($interes ,2, '.', ','),1,0,'R');
                                    $pdf->Cell(25,8,number_format($seguros ,2, '.', ','),1,0,'R');
                                    $pdf->Cell(32,8,number_format($admon ,2, '.', ','),1,0,'R');      
                                    $pdf->Ln(8);  
                                

                               }


                        $ts_interes1       += $ts_interes;
                        $ts_seguros1       += $ts_seguros;                                                
                        $ts_admon1         += $ts_admon;                                                 
                        $ts_total1         += $ts_total;

                        $pdf->SetFont('Arial','B',8);
                        $pdf->Cell(117,8, 'TOTAL: ',1,0,'R');                                                
                        $pdf->Cell(25,8, number_format($ts_interes,2, '.', ','),1,0,'R');
                        $pdf->Cell(25,8, number_format($ts_seguros,2, '.', ','),1,0,'R');
                        $pdf->Cell(32,8, number_format($ts_admon ,2, '.', ','),1,0,'R');
                        $pdf->Ln(6);
                         
                          


                }//end while tipos crédito

                 $pdf->Ln(10); 
                 $pdf->Cell(94,5, '','',0,'R');
                 $pdf->Cell(35,5, utf8_decode('TOTAL INTERÉS'),'LTR',0,'R');
                 $pdf->Cell(35,5, 'TOTAL SEGURO','LTR',0,'R');
                 $pdf->Cell(35,5, 'TOTAL GASTOS','LTR',0,'R');
                 $pdf->Ln(5);     
                 $pdf->SetFont('Arial','B',9); 
                 $pdf->Cell(94,5, '','',0,'R');
                 $pdf->Cell(35,5, utf8_decode(''),'LBR',0,'R');
                 $pdf->Cell(35,5, '','LBR',0,'R');
                 $pdf->Cell(35,5, 'ADMINISTRATIVOS','LBR',0,'R');
                  $pdf->Ln(5); 
                 $pdf->Cell(94,8, '','',0,'R');                                                                               
                 $pdf->Cell(35,8, number_format($ts_interes1,2, '.', ','),1,0,'R');
                 $pdf->Cell(35,8, number_format($ts_seguros1,2, '.', ','),1,0,'R');
                 $pdf->Cell(35,8, number_format($ts_admon1 ,2, '.', ','),1,0,'R');
                       
                
                          


} else {  //else tipoinforme

$pdf->SetFont('Arial','B',10);
$pdf->Cell($ac-118,7,utf8_decode('INFORME CONSOLIDADO DE CAUSACIÓN'),'LTR',0,'C');
$pdf->Ln(7);
$pdf->SetX(67);
$pdf->SetFont('Arial','B',8);
$pdf->Cell($ac-118,7, utf8_decode('ENTRE: '.$fechaini.' Y '.$fechafin ),'LBR',0,'C'); 
$pdf->Ln(15);

               $sqltc = "declare @Fecha_I date='".$fechaini."';
                                        declare @Fecha_F date='".$fechafin."';
                                        select tc.Identificador,                                     
                                        tc.Nombre_Tipo_Credito                                       
                                        from CREDITO as c

                                        left join DETALLE_CREDITO as dc on dc.Numero_Credito=c.Numero_Credito
                                        left join SOLICITUD_CREDITOS as s on s.Identificador=c.Id_Solicitud_Credito
                                        left join TIPO_CREDITO as tc on tc.Identificador=s.Id_Tipo_Credito 
                                        where  dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                        and 

                                        (CASE WHEN (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='18'and dci.Numero_Cuota!='0' and c.Indicador_Gastos_Anticipado='False') is null THEN (0) ELSE 
                                        
                                        (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='18'and dci.Numero_Cuota!='0'  and c.Indicador_Gastos_Anticipado='False') end)
                                        +

                                        (CASE WHEN (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='5'and dci.Numero_Cuota!='0' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='22' ) is null THEN (0) ELSE 
                                        
                                        (select sum(dci.Valor_Concepto) from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='5'and dci.Numero_Cuota!='0' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='22' ) end)
                                        +(
                                        CASE WHEN (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='3' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='21') is null THEN (0) ELSE
                                        (select sum(dci.Valor_Concepto) 
                                        from DETALLE_CREDITO as dci 
                                        where dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='3' or
                                        dci.Numero_Credito=c.Numero_Credito and dci.Fecha_Posible_pago>=@Fecha_I 
                                        and dci.Fecha_Posible_pago<=@Fecha_F and dci.Id_Concepto='21') end)>0 
                                        group by tc.Nombre_Tipo_Credito, tc.Identificador
                                        order by tc.Nombre_Tipo_Credito asc  ";
                     $stmt  = sqlsrv_query( $conn, $sqltc); 
                          
                     $pdf->SetX(28);
                     $pdf->Cell(60,5, utf8_decode('TIPO CRÉDITO'),'LTR',0,'C');
                     $pdf->Cell(37,5, utf8_decode('Total'),'LTR',0,'C');
                     $pdf->Cell(37,5, utf8_decode('Total'),'LTR',0,'C');
                     $pdf->Cell(37,5,utf8_decode('Total Gastos'),'LTR',0,'C');
                     $pdf->Ln(5);
                     $pdf->SetX(28);
                     $pdf->Cell(60,5, utf8_decode(''),'LBR',0,'C');
                     $pdf->Cell(37,5, utf8_decode('Interés + Interés Acuerdo'),'LBR',0,'C');
                     $pdf->Cell(37,5, utf8_decode('Seguro'),'LBR',0,'C');
                     $pdf->Cell(37,5,utf8_decode('Administración'),'LBR',0,'C');
                     $pdf->Ln(5);
                              
                    

                     while( $rows = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) { 

                      
                          
                               $sqlI = "declare @Fecha_I date='".$fechaini."';
                                        declare @Fecha_F date='".$fechafin."';
                                        declare @Tipo_Credito varchar(20)='".$rows['Identificador']."';

                                      select distinct sum(dc.Valor_Concepto) as Valor_Concepto

                                      from CREDITO as c

                                      left join DETALLE_CREDITO as dc on dc.Numero_Credito=c.Numero_Credito
                                      left join SOLICITUD_CREDITOS as s on s.Identificador=c.Id_Solicitud_Credito
                                      left join TIPO_CREDITO as tc on tc.Identificador=s.Id_Tipo_Credito 
                                      where  dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                      and s.Id_Tipo_Credito=@Tipo_Credito  and dc.Id_Concepto='3' or
                                      dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                      and s.Id_Tipo_Credito=@Tipo_Credito and dc.Id_Concepto='21'";
                                $stmtI  = sqlsrv_query( $conn, $sqlI );  
                                $rows1   = sqlsrv_fetch_array( $stmtI, SQLSRV_FETCH_ASSOC);      


                                     
                               $sqlS = "declare @Fecha_I date='".$fechaini."';
                                        declare @Fecha_F date='".$fechafin."';
                                        declare @Tipo_Credito varchar(20)='".$rows['Identificador']."';

                                      select distinct sum(dc.Valor_Concepto) as Valor_Concepto

                                      from CREDITO as c

                                      left join DETALLE_CREDITO as dc on dc.Numero_Credito=c.Numero_Credito
                                      left join SOLICITUD_CREDITOS as s on s.Identificador=c.Id_Solicitud_Credito
                                      left join TIPO_CREDITO as tc on tc.Identificador=s.Id_Tipo_Credito 
                                      where  dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                      and s.Id_Tipo_Credito=@Tipo_Credito  and dc.Id_Concepto='5'  and dc.Numero_Cuota!='0' or
                                      dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                      and s.Id_Tipo_Credito=@Tipo_Credito  and dc.Id_Concepto='22'  and dc.Numero_Cuota!='0' ";
                                $stmtS  = sqlsrv_query( $conn, $sqlS );  
                                $rows2   = sqlsrv_fetch_array( $stmtS, SQLSRV_FETCH_ASSOC);      
        


                                     

                                       
                               $sqlA = "declare @Fecha_I date='".$fechaini."';
                                        declare @Fecha_F date='".$fechafin."';
                                        declare @Tipo_Credito varchar(20)='".$rows['Identificador']."';

                                      select distinct sum(dc.Valor_Concepto) as Valor_Concepto

                                      from CREDITO as c

                                      left join DETALLE_CREDITO as dc on dc.Numero_Credito=c.Numero_Credito
                                      left join SOLICITUD_CREDITOS as s on s.Identificador=c.Id_Solicitud_Credito
                                      left join TIPO_CREDITO as tc on tc.Identificador=s.Id_Tipo_Credito 
                                      where  dc.Fecha_Posible_pago>=@Fecha_I and dc.Fecha_Posible_pago<=@Fecha_F 
                                      and s.Id_Tipo_Credito=@Tipo_Credito   and dc.Id_Concepto='18'  and dc.Numero_Cuota!='0' 
                                      AND c.Indicador_Gastos_Anticipado='False' ";

                               $stmtA  = sqlsrv_query( $conn, $sqlA );  
                               $rows3   = sqlsrv_fetch_array( $stmtA, SQLSRV_FETCH_ASSOC);      

                                     $pdf->SetX(28);
                                                                 
                                    $tipocredito = $rows["Nombre_Tipo_Credito"];
                                    $interes     = $rows1["Valor_Concepto"];
                                    $seguro      = $rows2["Valor_Concepto"];
                                    $admon       = $rows3["Valor_Concepto"];                                              
                                    $total       =  ($ts_interes + $ts_seguro + $ts_admo);


                                    $ts_interes       += $interes;
                                    $ts_seguros       += $seguro;                                                
                                    $ts_admon         += $admon;  

                                   
                                    $pdf->SetFont('Arial','B',7);                                  
                                    $pdf->Cell(60,8, utf8_decode($tipocredito),1,0,'L');
                                    $pdf->SetFont('Arial','',7);  
                                    $pdf->Cell(37,8, number_format($interes,2, '.', ','),1,0,'R');
                                    $pdf->Cell(37,8, number_format($seguro,2, '.', ','),1,0,'R');
                                    $pdf->Cell(37,8, number_format($admon,2, '.', ','),1,0,'R');                                   
                                    $pdf->Ln(8);  
                        
                }   //END WHILE
             $pdf->SetX(28);  
             $pdf->SetFont('Arial','B',8); 
             $pdf->Cell(60,8, 'TOTALES:   ',1,0,'R');
             $pdf->Cell(37,8, number_format($ts_interes,2, '.', ','),1,0,'R');
             $pdf->Cell(37,8, number_format($ts_seguros,2, '.', ','),1,0,'R');
             $pdf->Cell(37,8, number_format($ts_admon,2, '.', ','),1,0,'R');    
 
}//END IF TIPO INFORME


$yc = $pdf->GetY(); 

ob_end_clean();   
$pdf->Output(0,'Informe_Causación.pdf',0);

?>