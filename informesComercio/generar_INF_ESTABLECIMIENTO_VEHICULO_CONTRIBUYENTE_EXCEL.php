  <?php

          

            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Listado_Facturacion_General.xls");
            require_once("../Conexion/conexion.php");
            session_start();



                $idContribuyente=$_POST['contribuyente'];
 



            ?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Listado Establecimientos y Vehículos Contribuyente</title>
            </head>
            <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="9" bgcolor="skyblue"><CENTER><strong>Listado Establecimientos y Vehículos Contribuyente</strong></CENTER></td>
                </tr>
                <tr>
                    <!--- fecha tipo numero detalle tercero ,valor y valor ajustado.-->
                    <td><strong>ESTABLECIMIENTO</strong></td>
                    <td><strong>VEHICULO</strong></td>
                    <td><strong>NOMBRE</strong></td>
                    <td><strong>DIRECCIÓN</strong></td>
                    <td><strong>CIUDAD</strong></td>
                    <td><strong>TIPO VEHICULO</strong></td>
                    <td><strong>TIPO SERVICIO</strong></td>
                    <td><strong>PLACA</strong></td>
                    <td><center><strong>%</strong></center></td>


                </tr>


                <?php

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

                    ?>

                        <tr>
                            <td><?php   echo "X"  ?></td>
                            <td><?php  echo ""  ?></td>
                            <td><?php  echo ucwords(mb_strtolower($rowe[2])) ?></td>
                            <td><?php  echo ucwords(mb_strtolower($rowe[5])) ?></td>
                            <td><?php  echo $rowe[7] ?></td>
                            <td><?php  echo "" ?></td>
                            <td><?php echo  "" ?></td>
                            <td><?php echo  "" ?></td>
                            <td><?php echo  "" ?></td>
                        </tr>



                <?php
                 } ?>

                 <?php 

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

                  ?>

                        <tr>
                            <td><?php  echo ""  ?></td>
                            <td><?php  echo "X"  ?></td>
                            <td><?php  echo  "" ?></td>
                            <td><?php  echo  "" ?></td>
                            <td><?php  echo  "" ?></td>
                            <td><?php  echo ucwords(mb_strtolower($row[2])) ?></td>
                            <td><?php  echo ucwords(mb_strtolower($row['nombreServicio'])) ?></td>
                            <td><?php  echo $row['placa'] ?></td>
                            <td><?php  echo $row['porc_propiedad'] ?></td>

                        </tr>


                    <?php } ?>
            </table>
            </body>
            </html>

