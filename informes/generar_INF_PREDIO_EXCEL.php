  <?php
     

           //*** ELABORACION DEL INFORME DETALLADO ***

            echo "El tipo de informe es detallado";
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Predio.xls");
            require_once("../Conexion/conexion.php");
            session_start();



         /*   $fechaInicial       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
            $fechaFinal         = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
            $conceptoInicialDetalle    = $mysqli->real_escape_string(''.$_POST["conceptoInicialDetalle"].'');
            $conceptoFinalDetalle      = $mysqli->real_escape_string(''.$_POST["conceptoFinalDetalle"].'');*/
            //$tipoInforme        = $mysqli->real_escape_string(''.$_POST["tipoInforme"].'');

            //convert date initial and final

           /* $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaInicial");
            $fechaI= $fechaI->format('Y/m/d');


            $fechaF = DateTime::createFromFormat('d/m/Y', "$fechaFinal");
            $fechaF= $fechaF->format('Y/m/d');*/




           //id
            $predioInicial=$mysqli->real_escape_string(''.$_POST["PredioInicial"].'');
            $predioFinal=$mysqli->real_escape_string(''.$_POST["PredioFinal"].'');

            //consultas pi
            $sqlPredioInicial= "SELECT p.id_unico,p.codigo_catastral,p.matricula_inmobiliaria,c.nombre FROM `gp_predio1` p
                                LEFT JOIN  gf_ciudad c ON c.id_unico=p.ciudad WHERE p.id_unico=$predioInicial";
            $sqlpi = $mysqli->query($sqlPredioInicial);

            $f=mysqli_fetch_array($sqlpi);

             $predioInicialOculto=ucwords(mb_strtolower($f[1]." - ".$f[2]." - ".$f[3]));


             //consultas pf
            $sqlPredioFinal= "SELECT p.id_unico,p.codigo_catastral,p.matricula_inmobiliaria,c.nombre FROM `gp_predio1` p
                                LEFT JOIN  gf_ciudad c ON c.id_unico=p.ciudad WHERE p.id_unico=$predioFinal";
            $sqlpf = $mysqli->query($sqlPredioFinal);
            $pf=mysqli_fetch_array($sqlpf);
            $predioFinalOculto=ucwords(mb_strtolower($pf[1]." - ".$pf[2]." - ".$pf[3]));



?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Listado de Facturación</title>
            </head>
            <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
              
                <?php 

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
                ?>
                <tr>
                    <td colspan="17" bgcolor="skyblue"><CENTER><strong><?php echo $nomcomp ?></strong></CENTER></td>
                </tr>
                 <tr>
                    <td colspan="17" bgcolor="skyblue"><CENTER><strong><?php echo $tipodoc.": ".$numdoc  ?></strong></CENTER></td>      
                </tr>
                 <tr>
                    <td colspan="17" bgcolor="skyblue"><CENTER><strong>LISTADO PREDIAL</strong></CENTER></td>
                </tr>
                <tr>
                    <td colspan="17" bgcolor="skyblue"><CENTER><strong>entre <?php echo $predioInicialOculto." y ".$predioFinalOculto ?></strong></CENTER></td>
                </tr>
                
                        <tr>
                                    <td><strong>Código Catastral</strong></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Matricula Inmobiliaria</strong></td>
                                    <td><strong>Año Creación</strong></td>
                                    <td><strong>Código SIG</strong></td>
                                    <td><strong>Código IGAC</strong></td>    
                                    <td><strong>Participación</strong></td>
                                    <td><strong>Principal</strong></td>
                                    <td><strong>Dirección</strong></td>
                                    <td><strong>Ciudad</strong></td>
                                    <td><strong>Barrio</strong></td>
                                    <td><strong>Estrato</strong></td>   
                                    <td><strong>Estado</strong></td>
                                    <td><strong>Ruta</strong></td>
                                    <td><strong>Tipo Predio</strong></td>
                                    <td><strong>Predio Asociado</strong></td>
                                    <td><strong>Terceros Asociados</strong></td><!--tercero -->
                                
                        </tr>


                        <?php 

                        $sql="SELECT DISTINCT p.id_unico,  
                                p.codigo_catastral, 
                                p.nombre,
                                p.matricula_inmobiliaria, 
                                p.aniocreacion,
                                p.codigo_sig,
                                p.codigoigac,
                                p.participacion,
                                p.principal,
                                p.direccion, 
                                p.ciudad, 
                                c.nombre,
                                c.departamento,
                                d.nombre,
                                p.barrio, 
                                b.nombre,
                                p.estrato,
                                e.nombre,
                                p.estado, 
                                ep.nombre,
                                p.ruta, 
                                r.nombre, 
                                p.tipo_predio,      
                                tp.nombre,
                                p.predioaso,
                                pa.codigo_catastral, 
                                pa.nombre 
                            FROM gp_predio1 p  
                            LEFT JOIN gf_ciudad c           ON p.ciudad = c.id_unico 
                            LEFT JOIN gp_barrio b           ON p.barrio = b.id_unico 
                            LEFT JOIN gp_ruta   r           ON p.ruta = r.id_unico 
                            LEFT JOIN gp_tipo_predio tp     ON p.tipo_predio = tp.id_unico
                            LEFT JOIN gf_departamento d     ON c.departamento = d.id_unico 
                            LEFT JOIN gp_estrato e          ON p.estrato = e.id_unico 
                            LEFT JOIN gr_estado_predio ep   ON p.estado = ep.id_unico
                            LEFT JOIN gp_predio1 pa         ON p.predioaso = pa.id_unico 
                            WHERE p.id_unico 
                            BETWEEN '$predioInicial' AND '$predioFinal'";


                            $resultado=$mysqli->query($sql);

                        ?>

                        <?php while($row=mysqli_fetch_array($resultado)){ ?>
                      
                            
                                    <?php 
                                    //id unico predio
                                    $id_unico_predio=$row[0];

                                    $sqlTerceros="SELECT p.id_unico,tp.propietario,tp.porcentaje,p.nombre,t.numeroidentificacion,
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
                                                    t.apellidodos)) AS nombreTercero
                                  
                                                    FROM gp_tercero_predio tp 
                                                    INNER JOIN gf_tercero t ON t.id_unico=tp.tercero
                                                    INNER JOIN gp_predio1 p ON p.id_unico=tp.predio 
                                                    WHERE p.id_unico=$id_unico_predio";

                                    $resultadoTercero=$mysqli->query($sqlTerceros);


                                                     ?>

                                <tr>


                                        <td><?php echo mb_strtoupper($row[1]);?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[2]));?></td>
                                        <td><?php echo mb_strtoupper($row[3]);?></td>
                                        <td><?php echo $row[4];?></td>
                                        <td><?php echo mb_strtoupper($row[5]);?></td>
                                        <td><?php echo mb_strtoupper($row[6]);?></td>
                                        <td><?php echo $row[7];?></td>
                                        <td><?php if($row[8]=='1') { echo 'Si';} else { echo 'No';};?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[9]));?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[11].' - '.$row[13]));?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[15]));?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[17]));?></td>
                                        <td><?php echo ucwords(mb_strtolower($row[19]));?></td>
                                        <td><?php echo ucwords(strtolower($row[21]));?></td>
                                        <td><?php echo ucwords(strtolower($row[23]));?></td>
                                        <td><?php echo ucwords(strtolower($row[25]. ' - '.$row[26]));?></td>   



                                        <!--terceros-->
                                        <td>
                                           <?php
                                         
                                            while($rowTercero=mysqli_fetch_array($resultadoTercero)){ 

                                            echo "* ".ucwords(mb_strtolower($rowTercero['nombreTercero']))." - ".$rowTercero['numeroidentificacion']." - ".$rowTercero['propietario']." - ".$rowTercero['porcentaje']."%<br>";

                                            } ?>
                                        </td>
                              
                                
                                </tr>
                        <?php } ?>
   
            </table>
            </body>
            </html>








     