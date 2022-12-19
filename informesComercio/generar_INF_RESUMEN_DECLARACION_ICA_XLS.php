<?php

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Resumen_Declaracion_Ica.xls");
    require_once("../Conexion/conexion.php");
    session_start();
    ini_set('max_execution_time', 0);
    #@$periodo       = $_GET['periodo'];
    $fecI     = $_POST['sltFechaA'];
    $fecF     = $_POST['sltFechaR'];
    $val      = $_POST['sltVa'];
    $compania = $_SESSION['compania'];
    $usuario  = $_SESSION['usuario'];

    if(!empty($fecI)){
        $hoy = trim($fecI, '"');
        $fecha_div = explode("/", $hoy);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fechaI = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
        
    }

    if(!empty($fecF)){
        $hoy1 = trim($fecF, '"');
        $fecha_div2 = explode("/", $hoy1);
        $anio1 = $fecha_div2[2];
        $mes1 = $fecha_div2[1];
        $dia1 = $fecha_div2[0];
        $fechaF = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
        
    }
   
?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Informe_Homologaciones</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0"> 
                <?php

                    $colO = "";                 //Nombre de columna origen
                    $colD = 0;                  //Contador de columnas destino
                    $columnasDestino = "";      //Nombres de columnas Destino
                    $tablaOrigen = "";          //Nombres de tabla Origen
                    $tablasDestino = "";        //Nombres de tablas Destino
                    $consultasTablaD = "";      //Consultas de tabla destino
                    $idTH = "";                 //Id de las tablas homologables
                    $consultaTablaO = "";       //Consulta de la tabla de origen
                ?>
                <thead>
                    <tr>
                        <?php

                            $consulta = "SELECT         t.razonsocial as traz,
                                                        t.tipoidentificacion as tide,
                                                        ti.id_unico as tid,
                                                        ti.nombre as tnom,
                                                        t.numeroidentificacion tnum
                                        FROM gf_tercero t
                                        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                                        WHERE t.id_unico = $compania";
                            $cmp = $mysqli->query($consulta);
                
                            while ($fila = mysqli_fetch_array($cmp)){
                
                                $nomcomp = mb_strtoupper($fila['traz']);       
                                $tipodoc = mb_strtoupper($fila['tnom']);       
                                $numdoc  = mb_strtoupper($fila['tnum']);   
                        ?>

                                <th>
                                    <?php echo $nomcomp ; ?>
                                </th>
                                <th>
                                    <?php echo $numdoc ; ?>
                                </th>
                        <?php
                            }
                        ?>
                    </tr>

                   
                        <?php
                            if($val == 1){
                                $TD = "Todas";
                            }elseif($val == 1){
                                $TD = "Pagadas";
                            }else{
                                $TD = "No Pagadas";
                            }
                        ?>
                    
                    <tr>
                        <th>
                            Listar Por
                        </th>
                        <th>
                            <?php echo $TD; ?>
                        </th>
                        <?php
                         
                           if(empty($fecF) || $fecF == ""){
                               
                                $fecF = "";
                                   
                            } 
                        ?>
                        <th>
                            Fecha Inicial:
                        </th>
                        <th>
                            <?php echo $fecI; ?>
                        </th>
                        <th>    
                            Fecha Final:
                        </th>
                        <th>
                            <?php echo $fecF; ?>
                        </th>
                    </tr>
                    
                    <?php 
                        if(empty($fecF) ){

                            if($val == 1){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                                    LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    WHERE d.fecha >='$fechaI' AND d.clase = 2 ORDER BY cc.codigo ASC";
                        
                        
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE d.fecha >= '$fechaI' AND d.clase = 2 ";
                                
                                $sql4 = "SELECT     c.id_unico,
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
                                                    tr.apellidodos)),
                                                    c.codigo_mat,
                                                    d.fecha,
                                                    d.id_unico
                                        FROM gc_declaracion d
                                    
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        WHERE  d.fecha >= '$fechaI' AND d.clase = 2 ORDER BY d.fecha ASC ";
                            }elseif($val == 2){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_recaudo_comercial rc
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE d.fecha >='$fechaI' AND d.clase = 2 ORDER BY cc.codigo ASC";
                                
                        
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_recaudo_comercial rc
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE d.fecha >= '$fechaI' AND d.clase = 2 ";
                                
                                $sql4 = "SELECT     c.id_unico,
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
                                                    tr.apellidodos)),
                                                    c.codigo_mat,
                                                    d.fecha,
                                                    d.id_unico
                                        FROM  gc_recaudo_comercial rc
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        WHERE  d.fecha >= '$fechaI' AND d.clase = 2 ORDER BY d.fecha ASC ";
                            }else{
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE d.fecha >='$fechaI' AND d.clase = 2 AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY cc.codigo ASC";
                        
                        
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE d.fecha >= '$fechaI' AND d.clase = 2 AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ";
                        
                                $sql4 = "SELECT     c.id_unico,
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
                                                    tr.apellidodos)),
                                                    c.codigo_mat,
                                                    d.fecha,
                                                    d.id_unico
                                        FROM gc_declaracion d
                                    
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        WHERE  d.fecha >= '$fechaI' AND d.clase = 2 AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY d.fecha ASC ";
                            }

                        }else{
                            if($val == 1){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                                        LEFT JOIN   gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 ORDER BY cc.codigo ASC";
                                
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 ";       
                                
                                $sql4 = "SELECT     c.id_unico,
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
                                                    tr.apellidodos)),
                                                    c.codigo_mat,
                                                    d.fecha,
                                                    d.id_unico
                                        FROM gc_declaracion d
                                    
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        
                                        WHERE  d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 ORDER BY d.fecha ASC ";
                            }elseif($val == 2){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_recaudo_comercial rc
                                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                                    LEFT JOIN   gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 ORDER BY cc.codigo ASC";

                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_recaudo_comercial rc
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 ";       
                        
                                $sql4 = "SELECT     c.id_unico,
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
                                                    tr.apellidodos)),
                                                    c.codigo_mat,
                                                    d.fecha,
                                                    d.id_unico
                                        FROM gc_recaudo_comercial rc
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        WHERE  d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 ORDER BY d.fecha ASC ";
                            }else{
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_declaracion d
                                        LEFT JOIN   gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY cc.codigo ASC";

                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_declaracion d
                                        LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ";       
                        
                                $sql4 = "SELECT     c.id_unico,
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
                                                    tr.apellidodos)),
                                                    c.codigo_mat,
                                                    d.fecha,
                                                    d.id_unico
                                        FROM gc_declaracion d
                                    
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        
                                        WHERE  d.fecha BETWEEN '$fechaI' AND '$fechaF' AND d.clase = 2 AND d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) ORDER BY d.fecha ASC"; 
                            }
        
                        }

                        $valor = $mysqli->query($sql2);
                    ?>
                    <tr>
                        <th>
                            Matrícula
                        </th>
                        <th>
                            Contribuyente
                        </th>
                        <th>
                            Fecha D.
                        </th>

                        <?php 
                            while ($Conc = mysqli_fetch_row($valor)) {
                        ?>
                                <th>
                                    <?php echo $Conc[2]; ?>
                                </th>
                        <?php
                            }
                        ?>
                    </tr> 

                    <?php
                        $Cont = $mysqli->query($sql4);
                        $con2 = $mysqli->query($sql2);
                        while ($CR = mysqli_fetch_row($Cont)) {
                    ?>
                            <tr>
                                <?php
                                        $fecD = trim($CR[3], '"');
                                        $fecha_div = explode("-", $fecD);
                                        $anio1 = $fecha_div[0];
                                        $mes1 = $fecha_div[1];
                                        $dia1 = $fecha_div[2];
                                        $fechaD = ''.$dia1.'/'.$mes1.'/'.$anio1.'';
                                ?>
                                    <td style='mso-number-format:\@' align="center">
                                        <?php echo $CR[2]; ?>
                                    </td>
                                    <td>
                                        <?php echo $CR[1]; ?>
                                    </td>
                                    <td>
                                        <?php echo $fechaD; ?>
                                    </td>
                                <?php
                                        while ($Tcon1 = mysqli_fetch_row($con2)) {
                                            $sql5 = "SELECT dd.valor
                                                    FROM gc_detalle_declaracion dd
                                                    
                                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                                    WHERE d.contribuyente = '$CR[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR[4]'";
                    
                                            $conc_comercio = $mysqli->query($sql5);
                                            $nconC = mysqli_num_rows($conc_comercio);
                                            if($nconC > 0){
                                                $V = mysqli_fetch_row($conc_comercio);
                                ?>
                                                <td>
                                                    <?php echo number_format($V[0],0,'.',',')  ?>
                                                </td>       
                                <?php                
                                            }else{
                                                $V[0] = 0;
                                ?>
                                                <td>
                                                    <?php echo number_format($V[0],0,'.',',')  ?>
                                                </td>       
                                <?php

                                            } 
                                        }
                                        $con2 = $mysqli->query($sql2);
                                ?>
                            </tr>
                    <?php    
                        }
                    ?>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>TOTAL</th>
                            <?php    
                                $con3 = $mysqli->query($sql2);
                                while($Tcon3 = mysqli_fetch_row($con3)){

                                    if(!empty($fecF)){
                                        
                                        if($val == 1){
                                            $sql5 = "SELECT SUM(dd.valor)
                                                FROM gc_detalle_declaracion dd
                                                LEFT JOIN  gc_declaracion d ON dd.declaracion = d.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND d.clase = 2 AND dd.tipo_det = 1 AND d.fecha BETWEEN '$fechaI' AND '$fechaF'";
                                        }elseif($val == 2){

                                            $sql5 = "SELECT SUM(dd.valor), dd.concepto FROM gc_detalle_declaracion dd 
                                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico 
                                                    LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico  
                                                    WHERE dd.concepto = '$Tcon3[0]' AND d.clase = 2 AND d.fecha BETWEEN '$fechaI' AND '$fechaF' ";
                                            
                                        }else{
                                            $sql5 = "SELECT SUM(dd.valor)
                                                FROM gc_detalle_declaracion dd
                                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico 
                                                LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND d.clase = 2 AND dd.tipo_det = 1 AND  d.fecha BETWEEN '$fechaI' AND '$fechaF' AND dd.declaracion NOT IN (SELECT rc.declaracion FROM gc_recaudo_comercial rc) ";  
                                                
                                        }
                                         
                                    }else{
                                        if($val == 1){
                                            $sql5 = "SELECT SUM(dd.valor)
                                                FROM gc_detalle_declaracion dd
                                                LEFT JOIN  gc_declaracion d ON dd.declaracion = d.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND d.clase = 2 AND dd.tipo_det = 1 AND d.fecha >= '$fechaI'";
                                        }elseif($val == 2){
                                            $sql5 = "SELECT SUM(dd.valor), dd.concepto FROM gc_detalle_declaracion dd 
                                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico 
                                                    LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico  
                                                    WHERE dd.concepto = '$Tcon3[0]' AND d.fecha >= '$fechaI' AND d.clase = 2 AND dd.tipo_det = 1";
                                            
                                        }else{


                                        $sql5 = "SELECT SUM(dd.valor)
                                                FROM gc_detalle_declaracion dd
                                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico 
                                                LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND d.clase = 1 AND dd.tipo_det = 2 AND  d.fecha >= '$fechaI' AND dd.declaracion NOT IN (SELECT rc.declaracion FROM gc_recaudo_comercial rc) ";


                                        }
                                    }

                                    $res3 = $mysqli->query($sql5);
                                    $r = mysqli_fetch_row($res3);
                            ?>
                                    
                                    <th align="right"><?php echo number_format($r[0],0,'.',',') ?></th>
                                    
                            <?php
                                }    
                            ?>
                        </tr>                       
                        
                        
    </tbody>  
           
</table>
</body>
</html>