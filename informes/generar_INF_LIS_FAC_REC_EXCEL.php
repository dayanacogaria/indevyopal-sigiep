<?php 
################ MODIFICACIONES ####################
#01/06/2017 | Anderson Alarcon | mejore filtros 
#01/06/2017 | Anderson Alarcon |diseño de todos los informes pdf 
############################################
$tipoInforme=$_POST['tipoInforme'];

if($tipoInforme=="general"){

            //*** ELABORACION DEL INFORME GENERAL ***

            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_Recaudo_General.xls");
            require_once("../Conexion/conexion.php");
            session_start();



            $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
            $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
        

            //convert date initial and final

            $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaInicial");
            $fechaI= $fechaI->format('Y/m/d');


            $fechaF = DateTime::createFromFormat('d/m/Y', "$fechaFinal");
            $fechaF= $fechaF->format('Y/m/d');



            ?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Listado de Facturación</title>
            </head>
            <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="7" bgcolor="skyblue"><CENTER><strong>Listado de Facturación</strong></CENTER></td>
                </tr>
                <tr>
                    <!--- fecha tipo numero detalle tercero ,valor y valor ajustado.-->
                    <td><strong>TIPO</strong></td>
                    <td><strong>NÚMERO</strong></td>
                    <td><strong>FECHA</strong></td>
                    <td><strong>DESCRIPCIÓN</strong></td>
                    <td><strong>TERCERO</strong></td>
                    <td><strong>VALOR FACTURA</strong></td>
                    <td><strong>VALOR RECAUDO</strong></td>
                    <td><strong>SALDO</strong></td>  
                </tr>


                <?php

                //CONSULTO LAS FACTURAS QUE ESTEN ENTRE LA FECHA INICIAL Y FINAL


                $sqlF="SELECT f.*, tf.prefijo,   
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
                                t.apellidodos)) AS NOMBRE 

                              FROM gp_factura f
                              LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
                              LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
                              WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' ORDER BY  fecha_factura ASC";

                $lf=$mysqli->query($sqlF);


                while($f=mysqli_fetch_array($lf)){

                    $id_unico_factura=$f['id_unico'];

                    //CONSULTAR LOS DETALLES DE LA FACTURA $f Y SUMAR EL VALOR


                    $sqldf="SELECT   SUM(df.valor_total_ajustado) AS tvDetallesFactura 
                            FROM gp_detalle_factura df
                            WHERE df.factura='$id_unico_factura'";

                  $ldf= $mysqli->query($sqldf);

                    $df=mysqli_fetch_array($ldf);


                    //CONSULTAR LA SUMATORIA DE LOS DETALLES DE PAGO

                 $sqldp="SELECT SUM(dp.valor) AS tvDetallePago 
                     FROM `gp_detalle_pago` dp
                     LEFT JOIN gp_detalle_factura df ON df.id_unico=dp.detalle_factura 
                     LEFT JOIN gp_factura f ON f.id_unico=df.factura
                     WHERE f.id_unico='$id_unico_factura'";

                  $ldp= $mysqli->query($sqldp);

                    $dp=mysqli_fetch_array($ldp);


                  ?>
                                <!--IMPRIMO RESULTADOS AL INFORME EXCEL -->


                                <tr>
                                    <td><?php  echo $f['prefijo']  ?></td>
                                    <td><?php  echo $f['numero_factura']  ?></td>
                                    <td><?php   echo $f['fecha_factura']  ?></td>
                                    <td><?php  echo ucwords(mb_strtolower($f['descripcion']))  ?></td>
                                    <td><?php  echo ucwords(mb_strtolower($f['NOMBRE']))?></td> 
                                    <td><?php  echo number_format($df['tvDetallesFactura'],2,'.',',')?></td><!--valor factura-->
                                    <td><?php  echo number_format($dp['tvDetallePago'],2,'.',',')?></td><!--valor recaudo-->

                                    <?php   $saldo=$df['tvDetallesFactura']-$dp['tvDetallePago']   ?>
                                    <td><?php  echo number_format($saldo,2,'.',',')?></td><!--saldo-->
                                </tr>



                <?php
                          
                 } ?>

            </table>
            </body>
            </html>

<?php


 }else{

        if($tipoInforme=="detallado"){

          //*** ELABORACION DEL INFORME DETALLADO ***

           
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_Recaudo_Detallado.xls");
            require_once("../Conexion/conexion.php");
            session_start();

                $facturaInicial=$_POST['facturaInicial'];
                $facturaFinal=$_POST['facturaFinal'];

            $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
            $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
            $conceptoInicial    = $mysqli->real_escape_string(''.$_POST["conceptoInicial"].'');
            $conceptoFinal      = $mysqli->real_escape_string(''.$_POST["conceptoFinal"].'');
            //$tipoInforme        = $mysqli->real_escape_string(''.$_POST["tipoInforme"].'');

            //convert date initial and final

            $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaInicial");
            $fechaI= $fechaI->format('Y/m/d');


            $fechaF = DateTime::createFromFormat('d/m/Y', "$fechaFinal");
            $fechaF= $fechaF->format('Y/m/d');



?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Listado de Facturación</title>
            </head>
            <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="8" bgcolor="skyblue"><CENTER><strong>Listado de Facturación</strong></CENTER></td>
                </tr>
                
                        <tr>
                                    <td><strong>NÚMERO PAGO</strong></td>
                                    <td><strong>TIPO</strong></td>
                                    <td><strong>FECHA </strong></td>
                                    <td><strong>VALOR</strong></td>
                                    <td><strong>IVA</strong></td>
                                    <td><strong>IMPOCONSUMO</strong></td>
                                    <td><strong>AJUSTE DEL PESO</strong></td> 
                                    <td><strong>SALDO</strong></td>  

                                    
                        </tr>


                <?php

                /*  ***

                   ' consultar las facturas con sus detalles, que esten entre fecha inicial y fecha final 

                *** */


                $sqlF="SELECT f.*, tf.prefijo,   
                                DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida,

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
                                t.apellidodos)) AS NOMBRE 

                              FROM gp_factura f
                              LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
                              LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
                 
               
                              WHERE f.id_unico BETWEEN '$facturaInicial' AND '$facturaFinal'";

                $lf=$mysqli->query($sqlF);

                //FACTURAS
                while($f=mysqli_fetch_array($lf)){       ?>     


                <?php

                        $factura_id_unico=$f['id_unico'];


                        //Suma de los detalles de la factura 

                        $sqlsumdf="SELECT SUM(detfac.valor_total_ajustado) AS valordf FROM gp_detalle_factura detfac
                                    WHERE detfac.factura='$factura_id_unico'";

                        $lsum=$mysqli->query($sqlsumdf);
                        $s=mysqli_fetch_array($lsum);

                        //DETALLES DE PAGO


                        $sqlDP="SELECT p.*,tp.nombre,dp.* 
                                FROM gp_detalle_pago dp 
                                LEFT JOIN gp_pago p ON p.id_unico=dp.pago 
                                LEFT JOIN gp_tipo_pago tp ON tp.id_unico=p.tipo_pago 
                                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura=df.id_unico 
                                LEFT JOIN gp_factura f ON df.factura=f.id_unico
                                WHERE f.id_unico='$factura_id_unico' AND p.fecha_pago BETWEEN '$fechaI' AND '$fechaF'";  

                        $ldp=$mysqli->query($sqlDP);

                       


                        if($ldp->num_rows>0){






                        ?>



                         <!-- ***  FACTURA *** -->
                                <tr>
                                     <td colspan="4" >
                                        

                                            <strong><?php echo $f['numero_factura'] ?> </strong> 
                                            <strong><?php echo $f['prefijo'] ?> </strong>                            
                                            <strong> <?php  echo ucwords(mb_strtolower($f['NOMBRE']))?></strong>                 
                                            <strong><?php echo $f['fechaFacConvertida'] ?></strong>    
                                            <strong><?php echo "$".number_format($s['valordf'],2,'.',',') ?></strong>     

                                           
                                     
                                    </td>
                               </tr>

                        <?php    
                        
                            $vDetallePago=0; 
                        
                            while( $dp=mysqli_fetch_array($ldp)){
                               $vDetallePago=$vDetallePago+($dp['valor']+$dp['iva']+$dp['impoconsumo']+$dp['ajuste_peso']); 
                               
                                
                               $saldo=$s['valordf']-$vDetallePago; 

                                ?>

                                  <tr>
                                      
                                    <td><?php echo $dp['numero_pago']  ?></td>
                                    <td><?php echo ucwords(mb_strtolower($dp['nombre']))  ?></td><!--tipo pago-->
                                    <td><?php echo $dp['fecha_pago']  ?></td>
                                    <td><?php   echo number_format($dp['valor'],2,'.',',')  ?></td>
                                    <td><?php   echo number_format($dp['iva'],2,'.',',')  ?></td>
                                    <td><?php   echo number_format($dp['impoconsumo'],2,'.',',')  ?></td>
                                    <td><?php   echo number_format($dp['ajuste_peso'],2,'.',',')  ?></td>
                                    <td><?php   echo number_format($saldo,2,'.',',')  ?></td>

                                  </tr>

                            

                       <?php }

                       }  ?>


             



                      

                <?php  }    ?>

            </table>
            </body>
            </html>







       


  <?php  
        }
  }  ?>