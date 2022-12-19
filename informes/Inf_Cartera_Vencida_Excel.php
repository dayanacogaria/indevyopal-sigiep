<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../Conexion/conexionsql.php');
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe Cartera Vencida.xls");

ini_set('max_execution_time', 0);
session_start();

$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$parmanno   = $_SESSION['anno'];
$anno       = anno($parmanno);

$fechacorte     = fechaC($_REQUEST['fechaC']);


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
        <title>CARTERA VENCIDA</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
         <th colspan="11" align="center">
                <strong><br/><?= $razonsocial ?>
            <?php
             echo ' <br/>&nbsp;CARTERA VENCIDA'                      
            ?>         
            <br/>FECHA A CORTE: <?= $fechacorte ?>
           </strong>
         </th>

                <?php
                echo '<tr>';
                echo '<td><strong>LÍNEA CRÉDITO</strong></td>';
                echo '<td><strong>NUM. CREDITOS</strong></td>';
                echo '<td><strong>SALDO CAPITAL</strong></td>';
                echo '<td><strong>SALDO INTERESES</strong></td>';
                echo '<td><strong>SALDO SEGUROS</strong></td>';
                echo '<td><strong>SALDO HONORARIOS</strong></td>';
                echo '<td><strong>SALDO RECARGOS</strong></td>';
                echo '<td><strong>SALDO ADMINISTRACIÓN</strong></td>';
                echo '<td><strong>SALDO OTROS</strong></td>';          
                echo '<td><strong>HONORARIOS</strong></td>';
                echo '<td><strong>TOTAL CARTERA</strong></td>';               
                echo '</tr>';
             
                    $num_creditos         = 0;
                    $saldo_capital        = 0;
                    $saldo_interes1       = 0;
                    $saldo_seguro         = 0;
                    $saldo_honorario      = 0;
                    $saldo_recargo        = 0;
                    $saldo_administracion = 0;
                    $saldo_otros          = 0;
                    $honorarios           = 0;                            
                    $total_cartera        = 0;                
            

                    $ts_num_creditos         = 0;
                    $ts_saldo_capital        = 0;
                    $ts_saldo_interes        = 0;
                    $ts_saldo_seguro         = 0;
                    $ts_saldo_honorario      = 0;
                    $ts_saldo_recargo        = 0;
                    $ts_saldo_administracion = 0;
                    $ts_saldo_otros          = 0;
                    $ts_honorarios           = 0;                            
                    $ts_total_cartera        = 0;


                        //Número de Créditos
                        $sqlNC = "declare @Fecha_C date= '" . $fechacorte . "';
                                    SELECT Linea_credito,COUNT( DISTINCT(Numero_Credito)) num_creditos, SUM(Saldo_Capital) AS saldo_capital, 
                                    SUM(Saldo_Interes) AS saldo_interes,
                                    SUM(Saldo_Seguro) AS saldo_seguro, SUM(Saldo_Honorario) AS saldo_honorario, 
                                    SUM(Saldo_Recargo) AS saldo_recargo, SUM(Saldo_Administracion) AS saldo_administracion ,
                                    SUM(Saldo_Otros) AS saldo_otros,SUM(Saldo_Interes_Acuerdo) AS saldo_int_acuerdo,
                                    SUM(Saldo_Costos_Judiciales) AS saldo_otros,SUM(Honorarios) AS honorarios
                                    FROM CARTERA_VENCIDA
                                    WHERE Fecha_Corte= @Fecha_C AND Indicativo = 'SI' 
                                    group by Linea_credito";


                        $stmtNC = sqlsrv_query($conn, $sqlNC);                         
                         


                        while ($rows = sqlsrv_fetch_array($stmtNC, SQLSRV_FETCH_ASSOC)) {


                            $linea_credito        = $rows["Linea_credito"];
                            $num_creditos         = $rows["num_creditos"];
                            $saldo_capital        = $rows["saldo_capital"];
                            $saldo_interes        = $rows["saldo_interes"];
                            $saldo_seguro         = $rows["saldo_seguro"];
                            $saldo_honorario      = $rows["saldo_honorario"];
                            $saldo_recargo        = $rows["saldo_recargo"];
                            $saldo_administracion = $rows["saldo_administracion"];
                            $saldo_otros          = $rows["saldo_otros"];
                            $honorarios           = $rows["honorarios"];                            
                            $total_cartera        = ($saldo_capital + $saldo_interes + $saldo_seguro + $saldo_honorario + $saldo_recargo + $saldo_administracion + $saldo_otros + $honorarios);

                           

                            echo '<tr>';
                            echo '<td>' . utf8_encode($linea_credito) . '</td>';                    
                            echo '<td>' . number_format($num_creditos, 0, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_capital, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_interes, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_seguro, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_honorario, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_recargo, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_administracion, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($saldo_otros, 2, '.', ',') . '</td>';                     
                            echo '<td>' . number_format($honorarios, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($total_cartera, 2, '.', ',') . '</td>';                          
                            echo '</tr>';


                            $ts_num_creditos          += $num_creditos;
                            $ts_saldo_capital         += $saldo_capital;
                            $ts_saldo_interes         += $saldo_interes;
                            $ts_saldo_seguro          += $saldo_seguro;
                            $ts_saldo_honorario       += $saldo_honorario;
                            $ts_saldo_recargo         += $saldo_recargo;
                            $ts_saldo_administracion  += $saldo_administracion;
                            $ts_saldo_otros           += $saldo_otros;
                            $ts_honorarios            += $honorarios;
                            $ts_total_cartera         += $total_cartera;                            
                           
                            
                        }                       
                  
                    echo '<td><strong>TOTALES</td>';                   
                    echo '<td><strong>' . number_format($ts_num_creditos, 0, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_capital, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_interes, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_seguro, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_honorario, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_recargo, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_administracion, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_saldo_otros, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_honorarios, 2, '.', ',') . '</strong></td>';
                    echo '<td><strong>' . number_format($ts_total_cartera, 2, '.', ',') . '</strong></td>'; 


                echo '<tr></tr>';
                echo '<th colspan="3">ETAPAS PROCESALES</th>';
                echo '<tr>';
                echo '<td><strong>LÍNEA CRÉDITO</strong></td>';
                echo '<td><strong>ETAPA PROCESAL</strong></td>';
                echo '<td><strong>CANTIDAD</strong></td>';                            
                echo '</tr>';                

                  
                 $cantidad_estado = 0;


                 $sqlEP = "declare @Fecha_C date= '" . $fechacorte . "';
                              select  Linea_credito,ep.Descripcion, COUNT(distinct Numero_Credito)  as cantidad from CARTERA_VENCIDA cv
                              left join ETAPA_PROCESAL ep on ep.Id_Etapa = cv.Id_Etapa                             
                              WHERE Fecha_Corte = @Fecha_C and Indicativo = 'SI'
                              group by cv.Id_Etapa, ep.Descripcion, Linea_credito
                              ORDER BY Linea_credito";
                 $stmtEP = sqlsrv_query($conn, $sqlEP); 

                        $total_cantidad = 0;

                        while ($rows = sqlsrv_fetch_array($stmtEP, SQLSRV_FETCH_ASSOC)) {


                            $linea_credito       = $rows["Linea_credito"];
                            $etapa               = $rows["Descripcion"] == "" ? "No tiene" : $rows["Descripcion"];
                            $cantidad            = $rows["cantidad"];

                            echo '<tr>';
                            echo '<td>' . utf8_encode($linea_credito) . '</td>';                    
                            echo '<td>' . utf8_encode(strtoupper($etapa)) . '</td>';
                            echo '<td>' . number_format($cantidad , 0, '.', ',') . '</td>';                                                    
                            echo '</tr>';


                            $total_cantidad +=   $cantidad;
                            

                       }    
                            echo '<tr>';                                              
                            echo '<td></td>';
                            echo '<td><strong>TOTAL: </strong></td>';
                            echo '<td><strong>' . number_format($total_cantidad , 0, '.', ',') . '</strong></td>';
                            echo '</tr>';                                                 
                           



                        echo '<tr></tr>';
                        echo '<th colspan="3">EDADES CARTERA</th>';
                        echo '<tr>';
                        echo '<td><strong>LÍNEA CRÉDITO</strong></td>';
                        echo '<td><strong>CALIFICACIÓN</strong></td>';
                        echo '<td><strong>CANTIDAD</strong></td>';                            
                        echo '</tr>';     


                      $cantidad_calificacion       = 0;


                       $sqlCE = "declare @Fecha_C date= '" . $fechacorte . "';
                                 SELECT DISTINCT Linea_Credito, Calificacion, COUNT(distinct Numero_Credito)  as cantidad
                                    FROM CARTERA_VENCIDA 
                                    WHERE Fecha_Corte = @Fecha_C and Indicativo = 'SI'
                                    GROUP BY Calificacion,Linea_Credito
                                    ORDER BY Linea_Credito"; 
                       $stmtCE = sqlsrv_query($conn, $sqlCE);  
                       
                        $total_cantidad = 0;
                        while ($rows = sqlsrv_fetch_array($stmtCE, SQLSRV_FETCH_ASSOC)) {


                            $linea_credito              = $rows["Linea_Credito"];
                            $calificacion               = $rows["Calificacion"];
                            $cantidad_calificacion      = $rows["cantidad"];

                            echo '<tr>';
                            echo '<td>' . utf8_encode($linea_credito) . '</td>';                    
                            echo '<td>' . utf8_encode($calificacion) . '</td>';
                            echo '<td>' . number_format($cantidad_calificacion , 0, '.', ',') . '</td>';                                                    
                            echo '</tr>';


                            $total_cantidad +=   $cantidad_calificacion;
                            

                       }   

                            echo '<tr>';                                              
                            echo '<td></td>';
                            echo '<td><strong>TOTAL: </strong></td>';
                            echo '<td><strong>' . number_format($total_cantidad , 0, '.', ',') . '</strong></td>';
                            echo '</tr>';                   
           

            ?>   
        </table>
    </body>
</html>   