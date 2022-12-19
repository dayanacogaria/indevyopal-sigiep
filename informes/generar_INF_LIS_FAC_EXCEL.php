  <?php
################ MODIFICACIONES ####################
#30/05/2017 | Anderson Alarcon | mejore filtros 
#30/05/2017 | Anderson Alarcon | diseño de todos los informes excel
############################################

//*** SE ELIGE EL TIPO DE INFORME EXCEL, GENERAL,DETALLADO O CONCEPTO***
$tipoInforme=$_POST['tipoInforme'];


 if($tipoInforme=="general"){

            //*** ELABORACION DEL INFORME GENERAL ***

            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_General.xls");
            require_once("../Conexion/conexion.php");
            session_start();



            $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
            $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
            $conceptoInicialFactura    = $mysqli->real_escape_string(''.$_POST["conceptoInicialFactura"].'');
            $conceptoFinalFactura      = $mysqli->real_escape_string(''.$_POST["conceptoFinalFactura"].'');
            $tipoInforme        = $mysqli->real_escape_string(''.$_POST["tipoInforme"].'');

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
                    <td colspan="6" bgcolor="skyblue"><CENTER><strong>Listado de Facturación</strong></CENTER></td>
                </tr>
                <tr>
                    <!--- fecha tipo numero detalle tercero ,valor y valor ajustado.-->
                    <td><strong>FECHA</strong></td>
                    <td><strong>TIPO</strong></td>
                    <td><strong>NÚMERO</strong></td>
                    <td><strong>DETALLE</strong></td>
                    <td><strong>TERCERO</strong></td>
                    <td><strong>VALOR</strong></td>
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
                              WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' AND f.tipofactura BETWEEN '$conceptoInicialFactura' and '$conceptoFinalFactura' ORDER BY  fecha_factura ASC";

                $lf=$mysqli->query($sqlF);


                while($f=mysqli_fetch_array($lf)){

                    $id_unico_factura=$f['id_unico'];

                    //CONSULTAR LOS DETALLES DE LA FACTURA $f Y SUMAR EL VALOR


                    $sqldf="SELECT   SUM(df.valor_total_ajustado) AS totalValor
                            FROM gp_detalle_factura df LEFT JOIN gp_factura f   ON df.factura=f.id_unico 
                            WHERE f.id_unico='$id_unico_factura'";

                  $ldf= $mysqli->query($sqldf);

                    $df=mysqli_fetch_array($ldf);

                   /* while($df=mysqli_fetch_array($ldf)){ */ ?>



                        <!--IMPRIMO RESULTADOS AL INFORME EXCEL -->


                        <tr>
                            <!--- fecha tipo numero detalle tercero ,valor y valor ajustado.-->
                            <td><?php   echo $f['fecha_factura']  ?></td>
                            <td><?php  echo $f['prefijo']  ?></td>
                            <td><?php  echo $f['numero_factura']  ?></td>
                            <td><?php  echo ucwords(mb_strtolower($f['descripcion'])) ?></td>
                            <td><?php  echo ucwords(mb_strtolower($f['NOMBRE']))?></td>
                            <td><?php  echo number_format($df['totalValor'],2,'.',',')?></td>
                        </tr>



                <?php

                  /*  }
            */
                 } ?>

            </table>
            </body>
            </html>

<?php


 }else{

        if($tipoInforme=="detallado"){

           //*** ELABORACION DEL INFORME DETALLADO ***

            echo "El tipo de informe es detallado";
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_Detallado.xls");
            require_once("../Conexion/conexion.php");
            session_start();



            $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
            $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
            $conceptoInicialDetalle    = $mysqli->real_escape_string(''.$_POST["conceptoInicialDetalle"].'');
            $conceptoFinalDetalle      = $mysqli->real_escape_string(''.$_POST["conceptoFinalDetalle"].'');
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
                    <td colspan="7" bgcolor="skyblue"><CENTER><strong>Listado de Facturación</strong></CENTER></td>
                </tr>
                
                        <tr>
                                    <td><strong>Concepto</strong></td>
                                    <td><strong>Cantidad</strong></td>
                                    <td><strong>Valor</strong></td>
                                    <td><strong>Iva</strong></td>
                                    <td><strong>Impoconsumo</strong></td>
                                    <td><strong>Ajuste del peso</strong></td>    
                                    <td><strong>Valor total del peso</strong></td>
                        </tr>


                <?php

                /*  ***

                   ' consultar las facturas con sus detalles, que esten entre fecha inicial y fecha final  y que esten   entre concepto inicial y concepto final  '

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
                 
               
                              WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' ORDER BY  fechaFacConvertida ASC";

                $lf=$mysqli->query($sqlF);


                while($f=mysqli_fetch_array($lf)){       ?>     

                 

                <?php

                        $factura_id_unico=$f['id_unico'];

                       $sqldf="SELECT  ct.*,
                                     df.* 
                                     FROM gp_detalle_factura df
                                     LEFT JOIN gp_concepto ct ON ct.id_unico=df.concepto_tarifa
                                     WHERE df.factura='$factura_id_unico' AND df.concepto_tarifa  BETWEEN '$conceptoInicialDetalle' AND '$conceptoFinalDetalle'";

                        $ldf=$mysqli->query($sqldf);


                        if($ldf->num_rows>0){       //validacion detalles factura  ?>

                               <!-- ***  FACTURA *** -->
                                <tr>
                                     <td colspan="7" >
                                        

                                            <strong><?php echo $f['numero_factura'] ?> </strong> 
                                            <strong><?php echo $f['prefijo'] ?> </strong>                            
                                            <strong> <?php  echo ucwords(mb_strtolower($f['NOMBRE']))?></strong>                 
                                            <strong><?php echo $f['fechaFacConvertida'] ?></strong>     
                                           
                                     
                                    </td>
                               </tr>

                                            

                         <?php while ($fdf=mysqli_fetch_array($ldf)) {   ?>
                            


 


                                  <!-- ***    DETALLES FACTURA *** -->


                                  <tr>
                                    <td><?php   echo $fdf['nombre']  ?></td>
                                    <td><?php  echo $fdf['cantidad']  ?></td>
                                    <td><?php  echo number_format($fdf['valor'],2,'.',',')?></td>
                                    <td><?php  echo $fdf['iva']  ?></td>
                                    <td><?php  echo $fdf['impoconsumo']  ?></td>
                                    <td><?php  echo $fdf['ajuste_peso']  ?></td>
                                    <td><?php  echo number_format($fdf['valor_total_ajustado'],2,'.',',')?></td>
                                  </tr>


                        <?php }
    
                        }
                          ?>       


                    



                <?php  }    ?>

            </table>
            </body>
            </html>







<?php

        }else{

                if($tipoInforme=="concepto"){

                        //*** ELABORACION DEL INFORME CONCEPTO***

                        header("Content-type: application/vnd.ms-excel");
                        header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_Concepto.xls");
                        require_once("../Conexion/conexion.php");
                        session_start();



                        $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
                        $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
                        $conceptoInicial    = $mysqli->real_escape_string(''.$_POST["conceptoInicial"].'');
                        $conceptoFinal      = $mysqli->real_escape_string(''.$_POST["conceptoFinal"].'');
                        $tipoInforme        = $mysqli->real_escape_string(''.$_POST["tipoInforme"].'');

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
                                <td colspan="6" bgcolor="skyblue"><CENTER><strong>Listado de Facturación</strong></CENTER></td>
                            </tr>
                            <tr>
                                <td><strong>NÚMERO</strong></td>
                                <td><strong>TIPO</strong></td>
                                <td><strong>FECHA</strong></td>
                                <td><strong>DESCRIPCIÓN</strong></td>
                                <td><strong>TERCERO</strong></td>
                                <td><strong>VALOR</strong></td>
                            </tr>



<?php

                                //
                                         $sqlf="SELECT f.*,df.concepto_tarifa,df.valor,tf.prefijo,ct.nombre AS nombreConceptoTarifa, 
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
                                        LEFT JOIN gp_detalle_factura df ON  df.factura=f.id_unico 
                                        LEFT JOIN gp_concepto ct ON df.concepto_tarifa=ct.id_unico 
                                        WHERE ct.id_unico BETWEEN '$conceptoInicial' AND '$conceptoFinal' AND fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                                        ORDER BY  ct.id_unico  ASC";

                            $lf=$mysqli->query($sqlf);
                            $conceptoTarifa="";
                            while($f=mysqli_fetch_array($lf)){


                                

                                    ?>

                                <?php if($conceptoTarifa!=$f['nombreConceptoTarifa']){ ?>

                                    <tr>
                                        <td colspan="6"><strong><?php   echo $f['nombreConceptoTarifa']  ?></strong></td>
 
                                    </tr>   
                                  
                                <?php
                                
                                    $conceptoTarifa=$f['nombreConceptoTarifa'];

                                    
                                } ?>

                          
                                    <tr>
                                        <td><?php  echo $f['numero_factura']  ?></td>
                                        <td><?php  echo $f['prefijo']  ?></td>
                                        <td><?php  echo $f['fechaFacConvertida']  ?></td>
                                        <td><?php  echo ucwords(mb_strtolower($f['descripcion']))  ?></td>
                                        <td><?php  echo ucwords(mb_strtolower($f['NOMBRE']))?></td>
                                        <td><?php  echo number_format($f['valor'],2,'.',',')?></td>
                                    </tr>



               <?php             }

                    
?>




                   
            </table>
            </body>
            </html>


<?php

                }else{
                           //ELABORACION TIPO TERCERO


                            header("Content-type: application/vnd.ms-excel");
                            header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_Tercero.xls");
                            require_once("../Conexion/conexion.php");
                            session_start();



                            $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
                            $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
                            $terceroInicial=$mysqli->real_escape_string(''.$_POST["terceroInicial"].'');
                            $terceroFinal=$mysqli->real_escape_string(''.$_POST["terceroFinal"].'');
                            $tipoInforme        = $mysqli->real_escape_string(''.$_POST["tipoInforme"].'');

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
                                        <td colspan="5" bgcolor="skyblue"><CENTER><strong>Listado de Facturación</strong></CENTER></td>
                                    </tr>
                                    <tr>
                                        <td><strong>NÚMERO</strong></td>
                                        <td><strong>TIPO</strong></td>
                                        <td><strong>FECHA</strong></td>
                                        <td><strong>DESCRIPCIÓN</strong></td>
                                        <td><strong>VALOR</strong></td><!--Valor total de los detalles de cada factura-->
                                    </tr>



                                    <?php
                                    $totalValorFacturas=0;

                                    $sqlTerceros="SELECT t.*, 
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
                                        
                                        FROM gf_tercero t WHERE t.id_unico BETWEEN '$terceroInicial' AND '$terceroFinal'";                                    
                              
                                    
                                    
                                    $lterceros=$mysqli->query($sqlTerceros);    
                                    while($tercero=mysqli_fetch_array($lterceros)){  
                                        
                                        $id_unicoTercero=$tercero['id_unico'];
                                        
                                    $sqlFacturas="SELECT f.*, tf.prefijo,
                                        DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida

 

                                      FROM gp_factura f
                                      LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
                                      LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
                                      WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' AND f.tercero='$id_unicoTercero'
                                      ORDER BY  fecha_factura ASC";
                                        
                                                      $lfacturas=$mysqli->query($sqlFacturas);
                                                      

                                                      
                                                      
                                                   ?>
                                                    
                                                    <?php if($lfacturas->num_rows>0){ 
                                                       ?>
                                    
                                                       <tr>
                                                        <td colspan="5"><strong><?php echo "Tercero:".$tercero['numeroidentificacion']." - ".ucwords(mb_strtolower($tercero['NOMBRE']))  ?></strong></td>   <!--Nombres y apellidos del Tercero-->
                                                       </tr>
                                    
                                                    <?php 

                                                
                                                         while($factura=mysqli_fetch_array($lfacturas)){
                                                    
                                                        $id_unico_factura=$factura['id_unico'];
                                                    
                                                        $sqldf="SELECT   SUM(df.valor_total_ajustado) AS totalValor
                                                        FROM gp_detalle_factura df LEFT JOIN gp_factura f   ON df.factura=f.id_unico 
                                                        WHERE f.id_unico='$id_unico_factura'";
                                                        
                                                        $ldf= $mysqli->query($sqldf);

                                                        $df=mysqli_fetch_array($ldf);
                                                        
                                                        $totalValorFacturas+=$df['totalValor'];   
                                                        
                                                        
                                                        ?>
                                                            
                                                                                                        <!--IMPRIMO RESULTADOS AL INFORME EXCEL -->


                                                        <tr>
                                                            <td><?php  echo $factura['numero_factura']  ?></td>
                                                            <td><?php  echo $factura['prefijo']  ?></td>
                                                            <td><?php   echo $factura['fecha_factura']  ?></td>
                                                            <td><?php  echo ucwords(mb_strtolower($factura['descripcion']))  ?></td>
                                                            <td><?php  echo number_format($df['totalValor'],2,'.',',')?></td>
                                                        </tr>
                                                            
                                                <?php
                                                        
                                                        
                                                    } ?>
                                    
                                                    
                                                    <?php } ?>
                                    
                                    
                                                                        

                                    
                                    

                                  <?php  }
                                    ?>
                                    
                                                                                                         
                                     <?php if($totalValorFacturas>0){ ?>

                                            <tr>
                                                <td style="border: none;"></td>
                                                <td style="border: none;"></td>
                                                <td style="border: none;"></td>
                                                <td style="border: none;"><strong>TOTALES:</strong> </td>
                                                <td style="border: none;"><strong><?php  echo number_format($totalValorFacturas,2,'.',',')?></strong></td>
                                            </tr>    
                                     <?php } ?>                                                                                                          
                                                      
                                    
                                 
                                </table>
                        
                            </body>
                            </html>









<?php


                 }

        }




      

 } ?>