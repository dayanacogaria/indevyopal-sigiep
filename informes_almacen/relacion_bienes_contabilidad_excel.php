<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=InformeInventarioDevolutivos.xls");
ini_set('max_execution_time', 0);
session_start();
ob_start();

require ('../Conexion/conexion.php');
require ('../Conexion/ConexionPDO.php');
require_once ('../modelAlmacen/producto.php');
require_once ('../modelAlmacen/depreciacion.php');
$con = new ConexionPDO();
$dep = new depreciacion();
$pro = new producto();

$ano = $_SESSION['anno'];
$per = $_REQUEST['txtPeriodoX'];

$compania = $_SESSION['compania'];
$usuario  = $_SESSION['usuario'];
$datosC   = $dep->tercero_informe($compania);

$nombreCompania = $datosC[0];
$nitCompania    = $datosC[1]." - ".$datosC[3];
$ruta           = $datosC[2];

$ff    = explode("/", $_REQUEST['txtPeriodoX']);
$dia   = date("d", (mktime(0, 0, 0, $ff[1] + 1, 1, $ff[0]) - 1));
$fecha = "$ff[0]-$ff[1]-$dia";


#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Informe Devolutivos</title>
</head>
<body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th colspan="8" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/>INFORME DEVOLUTIVOS
                    <br/><?PHP echo "HASTA EL PERIODO $per";?> 
                    <br/>&nbsp;</strong>
                </th>
            </tr>
            <tr>
                <th><strong>CODIGO <br/> CUENTA</strong></th>
                <th><strong>NOMBRE <br/> CUENTA</strong></th>
                <th><strong>DESCRIPCION <br/> PRODUCTO</strong></th>
                <th><strong>PLACA <br/> PRODUCTO</strong></th>
                <th><strong>FECHA <br/> ADQUISICI&Oacute;N</strong></th>
                <th><strong>VALOR <br/> UNITARIO</strong></th>
                <th><strong>DETERIORO <br/> ACUMULADO</strong></th>
                <th><strong>VALOR <br> RESIDUAL</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php
            /*$str = "SELECT plan_inventario, tipo_movimiento, cuenta_debito FROM gf_configuracion_almacen WHERE parametrizacion_anno = $ano";
            $res = $mysqli->query($str);
            while ($value = mysqli_fetch_row($res)) {
                $sql = "SELECT id_unico, nombre FROM gf_plan_inventario WHERE predecesor = $value[0]";
                $rst = $mysqli->query($sql);
                if(mysqli_num_rows($rst) > 0){
                    while($item = mysqli_fetch_row($rst)){
                        $stl = "SELECT    mpr.producto FROM gf_movimiento_producto AS mpr
                                LEFT JOIN gf_detalle_movimiento    AS dtm ON mpr.detallemovimiento = dtm.id_unico
                                LEFT JOIN gf_movimiento            AS mov ON dtm.movimiento        = mov.id_unico
                                LEFT JOIN gf_plan_inventario       AS pln ON dtm.planmovimiento    = pln.id_unico
                                WHERE     dtm.planmovimiento = $item[0]
                                AND       mov.tipomovimiento = $value[1]
                                AND       pln.tipoinventario IN (2, 3, 4)";
                        $rt1 = $mysqli->query($stl);
                        if(mysqli_num_rows($rt1) > 0){
                            while($row = mysqli_fetch_row($rt1)){
                                $strC = "SELECT codi_cuenta, nombre FROM gf_cuenta WHERE id_unico = $value[2]";
                                $resC = $mysqli->query($strC);
                                $rowC = mysqli_fetch_row($resC);
                                $strP = "SELECT descripcion, valor, DATE_FORMAT(fecha_adquisicion, '%d/%m/%Y') FROM gf_producto WHERE id_unico = $row[0]";
                                $resP = $mysqli->query($strP);
                                $rowP = mysqli_fetch_row($resP);
                                $strE = "SELECT valor FROM gf_producto_especificacion WHERE producto = $row[0] AND fichainventario = 6";
                                $resE = $mysqli->query($strE);
                                $rowE = mysqli_fetch_row($resE);
                                $xxx  = 0;
                                $strX = "SELECT valor FROM ga_depreciacion WHERE producto = $row[0] AND fecha_dep >= '$fecha'";
                                $resX = $mysqli->query($strX);
                                if(mysqli_num_rows($resX) > 0){
                                    while($rowX = mysqli_fetch_row($resX)){
                                        $xxx += $rowX[0];
                                    }
                                }
                                $valorR = $rowP[1] - $xxx;
                                ?>
                                <tr>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo $rowC[0]; ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo $rowC[1]; ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo $item[1]; ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo $rowE[0]; ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo $rowP[2]; ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo 1; ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo number_format($rowP[1], 2); ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo number_format($xxx, 2); ?></td>
                                    <td style="border: solid 1px #000; font-size: 10px;"><?php echo number_format($valorR, 2); ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
            }*/
            #*** Cnsulta de los elementos plan inventario de tipo devolutivos e inmuebles ***#
            $rowpi = $con->Listar("SELECT DISTINCT 
                    if(ct.codi_cuenta=NULL,'',ctp.codi_cuenta) AS codigoc, 
                if(ct.nombre=NULL,'',ctp.nombre) as nombrec, 
                CONCAT_WS(' - ',pi.codi, pi.nombre) as descripcion, 
                MAX(pe.valor) as placa, 
                ((SELECT MAX(date_format(mbf.fecha, '%d/%m/%Y'))
                    from gf_detalle_movimiento dmbf 
                    LEFT JOIN gf_movimiento mbf ON dmbf.movimiento = mbf.id_unico 
                    LEFT JOIN gf_tipo_movimiento tmbf ON mbf.tipomovimiento= tmbf.id_unico 
                    WHERE tmbf.clase = 2 
                            AND dmbf.planmovimiento = pi.id_unico)) as fechaAdqusicion, 
                    MAX(dm.valor), 
                    MAX(dp.valor) as dep , 
               ((SELECT DISTINCT count(mb.id_unico)
                from gf_detalle_movimiento dmb 
                LEFT JOIN gf_movimiento mb ON dmb.movimiento = mb.id_unico 
                LEFT JOIN gf_tipo_movimiento tmb ON mb.tipomovimiento= tmb.id_unico where tmb.clase = 7 
               AND dmb.planmovimiento = pi.id_unico)) as mb
            FROM 
            gf_detalle_movimiento dm 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            LEFT JOIN gf_movimiento_producto mp ON mp.detallemovimiento = dm.id_unico 
            LEFT JOIN gf_producto pr ON mp.producto =pr.id_unico 
            LEFT JOIN gf_producto_especificacion pe ON pe.producto = pr.id_unico AND pe.fichainventario = 6 
            LEFT JOIN gf_configuracion_almacen ca ON ca.plan_inventario = pi.id_unico 
                    AND ca.parametrizacion_anno = $ano  
                AND ca.tipo_movimiento = tm.id_unico 
            LEFT JOIN gf_cuenta ct ON ca.cuenta_debito = ct.id_unico     
            LEFT JOIN gf_configuracion_almacen cap ON cap.plan_inventario = pi.predecesor
                    AND cap.parametrizacion_anno = $ano 
                AND cap.tipo_movimiento = tm.id_unico 
            LEFT JOIN gf_cuenta ctp ON cap.cuenta_debito = ctp.id_unico     
            LEFT JOIN ga_depreciacion dp ON dp.producto = pr.id_unico AND dp.fecha_dep >= '$fecha' 
            WHERE pi.tipoinventario IN (2,4) and pi.compania = $compania   
            GROUP BY pr.id_unico  
            ORDER BY cast(pe.valor as unsigned) ASC");
            
            for ($i = 0; $i < count($rowpi); $i++) {
                if($rowpi[$i][6]==0){ 
                    echo '<tr>';
                    echo '<td>'.$rowpi[$i][0].'</td>';
                    echo '<td>'.utf8_decode($rowpi[$i][1]).'</td>';
                    echo '<td>'.utf8_decode($rowpi[$i][2]).'</td>';
                    echo '<td>'.$rowpi[$i][3].'</td>';
                    echo '<td>'.$rowpi[$i][4].'</td>';
                    echo '<td>'.number_format($rowpi[$i][5], 2, '.', ',').'</td>';
                    echo '<td>'.number_format($rowpi[$i][6], 2, '.', ',').'</td>';
                    $t = $rowpi[$i][5]-$rowpi[$i][6];
                    echo '<td>'.number_format($t, 2, '.', ',').'</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</body>
</html>