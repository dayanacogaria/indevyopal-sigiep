<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../Conexion/conexionsql.php');
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Causación Intereses.xls");

ini_set('max_execution_time', 0);
session_start();

$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$parmanno   = $_SESSION['anno'];
$anno       = anno($parmanno);

$fechaini     = $_REQUEST['fechaI'];
$fechafin     = $_REQUEST['fechaF'];
$tipoinforme = $_REQUEST['tipoInforme'];



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
$ruta_logo = $rowC[0][6];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>INFORME CAUSACIÓN DE INTERESES</title>
    </head>
    <body>
        
            <?php
            if ($tipoinforme == 1) {
            ?>    
               <table width="100%" border="1" cellspacing="0" cellpadding="0">
                 <th colspan="6" align="center">
                  <strong><br/><?= $razonsocial ?>
                          <br/>&nbsp;INFORME DETALLADO DE CAUSACIÓN          
                    <br/>&nbsp;
                    </strong>
                </th>
                <?php

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

                    $ts_interes1       = 0;                               
                    $ts_seguros1       = 0;
                    $ts_admon1         = 0;                            
                             
               while( $rows = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) { 

                echo '<tr>';
                echo '<td colspan="6" align="center">';
                echo '<strong>' . 'TIPO CRÉDITO: ' . ucwords($rows['Nombre_Tipo_Credito']) . '</strong></td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td><strong>NÚMERO CRÉDITO</strong></td>';
                echo '<td><strong>CÉDULA DEUDOR</strong></td>';
                echo '<td><strong>NOMBRE DEUDOR</strong></td>';
                echo '<td><strong>VALOR INTERESES</strong></td>';
                echo '<td><strong>VALOR SEGURO</strong></td>';
                echo '<td><strong>VALOR ADMINISTRACIÓN</strong></td>';                      
                echo '</tr>';


                 $identificador    = $rows['Identificador'];                              
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

                          $ts_interes       = 0;                               
                          $ts_seguros       = 0;
                          $ts_admon         = 0;  

                         while( $row = sqlsrv_fetch_array( $stmtpm, SQLSRV_FETCH_ASSOC)) {     

                            $numcredito     = $row["credito"];
                            $cedula         = $row["NUM_DOC_DEUDOR"];
                            $nombre         = $row["NOM_DEUDOR"];
                            $interes        = $row["Valor_Concepto"];
                            $seguros        = $row["Valor_Concepto_Seguro"];
                            $admon          = $row["Valor_Concepto_Admin"];                                                 
                           

                            $ts_interes       += $interes;
                            $ts_seguros       += $seguros;                                                
                            $ts_admon         += $admon;                                                 
                           

                              
                                echo '<tr>';
                                echo '<td>' . utf8_decode($numcredito).'</td>';
                                echo '<td>' . utf8_decode($cedula).'</td>';
                                echo '<td>' . utf8_decode(trim($nombre)).'</td>';
                                echo '<td>' . number_format($interes, 2, '.', ',').'</td>';
                                echo '<td>' . number_format($seguros, 2, '.', ',').'</td>';
                                echo '<td>' . number_format($admon, 2, '.', ',').'</td>';                                       
                                echo '</tr>'; 


                         }  //END WHILE

                        $ts_interes1       += $ts_interes;
                        $ts_seguros1       += $ts_seguros;                                                
                        $ts_admon1         += $ts_admon;                                                 
                       

                        echo '<tr>';
                        echo '<td></td>';
                        echo '<td></td>';
                        echo '<td>TOTAL: </td>';
                        echo '<td>'.number_format($ts_interes, 2, '.', ',').'</td>';
                        echo '<td>'.number_format($ts_seguros, 2, '.', ',').'</td>';
                        echo '<td>'.number_format($ts_admon, 2, '.', ',').'</td>';                                       
                        echo '</tr>';  

             
                 }  //end while  


                    echo '<tr>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td><strong>TOTAL INTERES: </strong></td>';
                    echo '<td><strong>TOTAL SEGURO: </strong></td>';
                    echo '<td><strong>TOTAL GASTOS ADMINISTRATIVOS: </strong></td>';                                                     
                    echo '</tr>'; 

                    echo '<tr>'; 
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';                      
                    echo '<td>'.number_format($ts_interes1, 2, '.', ',').'</td>';
                    echo '<td>'.number_format($ts_seguros1, 2, '.', ',').'</td>';
                    echo '<td>'.number_format($ts_admon1, 2, '.', ',').'</td>';                                       
                    echo '</tr>';         


        
            } else {  //else tipoinforme
           ?>     
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                 <th colspan="4" align="center">
                  <strong><br/><?= $razonsocial ?>
                          <br/>&nbsp;CONSOLIDADO PROYECCIÓN MENSUAL
                    <br/>&nbsp;
                    </strong>
                    </th>
                    <?php


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

                   
                    echo '<tr>';
                    echo '<td><strong>TIPO CRÉDITO</strong></td>';
                    echo '<td><strong>Total Interés + Interés Acuerdo</strong></td>';
                    echo '<td><strong>Total Seguro</strong></td>';
                    echo '<td><strong>Total Gastos Administración</strong></td>';                                       
                    echo '</tr>';

                    $ts_interes      = 0;
                    $ts_seguros      = 0;                                                
                    $ts_admon        = 0; 

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
                                      and s.Id_Tipo_Credito=@Tipo_Credito  and dc.Id_Concepto='22'  and dc.Numero_Cuota!='0'  ";
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
              

                               $tipocredito = $rows["Nombre_Tipo_Credito"];
                               $interes     = $rows1["Valor_Concepto"];
                               $seguro      = $rows2["Valor_Concepto"];
                               $admon       = $rows3["Valor_Concepto"];                                           
                               

                                $ts_interes       += $interes;
                                $ts_seguros       += $seguro;                                                
                                $ts_admon         += $admon;  


                              echo '<tr>';                                                         
                              echo '<td>'.utf8_decode($tipocredito).'</td>';
                              echo '<td>'.number_format($interes, 2, '.', ',').'</td>';
                              echo '<td>'.number_format($seguro, 2, '.', ',').'</td>'; 
                              echo '<td>'.number_format($admon, 2, '.', ',').'</td>';                                      
                              echo '</tr>'; 
                   
                  }//end while


                   echo '<tr>';
                   echo '<td><strong>TOTALES: </strong></td>';                                                    
                   echo '<td><strong>'.number_format($ts_interes, 2, '.', ',').'</strong></td>';
                   echo '<td><strong>'.number_format($ts_seguros, 2, '.', ',').'</strong></td>'; 
                   echo '<td><strong>'.number_format($ts_admon, 2, '.', ',').'</strong></td>';                                      
                   echo '</tr>'; 
                   
            } //end if tipo informe

           ?>   
        </table>
    </body>
</html>   