<?php

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Resumen_Recaudo_Ica.xls");
    require_once("../Conexion/conexion.php");
    session_start();
    ini_set('max_execution_time', 0);
    #@$periodo       = $_GET['periodo'];
    $fecI     = $_POST['sltFechaI'];
    $fecF     = $_POST['sltFechaF'];
    $BancoI   = $_POST['sltBancoI'];
    $BancoF   = $_POST['sltBancoF'];
    #$val      = $_POST['sltVa'];
    $compania = $_SESSION['compania'];
    $usuario  = $_SESSION['usuario'];
    $id_anno    = $_SESSION['anno'];

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

    $cuentaBI = "SELECT descripcion FROM gf_cuenta_bancaria WHERE id_unico = '$BancoI'";
    $CBI = $mysqli->query($cuentaBI);
    $NCBI = mysqli_num_rows($CBI);
    if($NCBI > 0){
        $BII = mysqli_fetch_row($CBI);
        $BI = $BII[0];
    }else{
        $BI = "";
    }

    $cuentaBF = "SELECT descripcion FROM gf_cuenta_bancaria WHERE id_unico = '$BancoF'";
    $CBF = $mysqli->query($cuentaBF);
    $NCBF = mysqli_num_rows($CBF);
    if($NCBF > 0){
        $BFF = mysqli_fetch_row($CBF);
        $BF = $BFF[0];
    }else{
        $BF = "";
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
                    
                    <tr>
                        
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
                        <th>
                            Cuenta Inicial:
                        </th>
                        <th>
                            <?php echo $BI; ?>
                        </th>
                        <th>    
                            Cuenta Final:
                        </th>
                        <th>
                            <?php echo $BF; ?>
                        </th>
                    </tr>
                    <tr></tr>
                    
                    <?php 
                        #inicio de validacion de los campos que pueden ser vacios
                        if(empty($fecF) && empty($BancoF)){

                            $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico >= '$BancoI'  ORDER BY id_unico ASC";
                            $res = $mysqli->query($sql10);
    
                            while($CBan = mysqli_fetch_row($res)){

                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                                        LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE rc.fecha >='$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC ";
        
        
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";
        
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
                                                    cb.descripcion,
                                                    rc.num_pag,
                                                    c.codigo_mat,
                                                    d.id_unico,
                                                    rc.fecha,
                                                    d.cod_dec,
                                                    ac.vigencia,
                                                    ac.mes
                                        FROM gc_recaudo_comercial rc 
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                                        WHERE  rc.fecha >= '$fechaI'  AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

                                $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]'";
                                $RES1 = $mysqli->query($sql12);
                                $nre = mysqli_num_rows($RES1);

                                if($nre > 0){

                                    $cccc = "SELECT banco, numerocuenta FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                                    $xxx = $mysqli->query($cccc);
                                    $idba = mysqli_fetch_row($xxx);

                                    $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                                             tr.apellidodos)) AS NOMBRE
                                                FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                                    $re = $mysqli->query($Nba);
                                    $Nbc = mysqli_fetch_row($re);

                                    $valor = $mysqli->query($sql2);
                                    $cantidad = $mysqli->query($sql3);

                    ?>
                                    <tr>
                                        <th>Banco</th>
                                        <th><?php echo $Nbc[0]; ?></th>
                                        <th>Cuenta</th>
                                        <th><?php echo $idba[1]; ?></th>
                                    </tr>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Contribuyente</th>
                                        <th>Cod. Dec.</th>
                                        <th>Perioodo G.</th>
                                        <th>Mes</th>
                                        <th>Fecha R.</th>
                                    
                    <?php
                                        while($cat = mysqli_fetch_row($valor)){
                    ?>  
                                            <th><?php echo $cat[2] ?></th>    
                    <?php
                                        }  
                    ?>
                                    </tr>
                                    
                    <?php
                                    $CR = $mysqli->query($sql4);
                                    $con2 = $mysqli->query($sql2);
                                    while($CR1 = mysqli_fetch_row($CR)){

                                        if($CR1[9] == 0 ){
                                            $mes = "ANUAL";
                                        }elseif($CR1[9] == 1){
                                            $mes = "ENERO";
                                        }elseif($CR1[9] == 2){
                                            $mes = "FEBRERO";
                                        }elseif($CR1[9] == 3){
                                            $mes = "MARZO";
                                        }elseif($CR1[9] == 4){
                                            $mes = "ABRIL";
                                        }elseif($CR1[9] == 5){
                                            $mes = "MAYO";
                                        }elseif($CR1[9] == 6){
                                            $mes = "JUNIO";
                                        }elseif($CR1[9] == 7){
                                            $mes = "JULIO";
                                        }elseif($CR1[9] == 8){
                                            $mes = "AGOSTO";
                                        }elseif($CR1[9] == 9){
                                            $mes = "SEPTIEMBRE";
                                        }elseif($CR1[9] == 10){
                                            $mes = "OCTUBRE";
                                        }elseif($CR1[9] == 11){
                                            $mes = "NOVIEMBRE";
                                        }elseif($CR1[9] == 12){
                                            $mes = "DICIEMBRE";
                                        }

                                        $FReca = explode("-",$CR1[6]);
                                        $A = $FReca[0];
                                        $M = $FReca[1];
                                        $D = $FReca[2];

                                        $FR = $D.'/'.$M.'/'.$A;
                    ?>
                                        <tr>
                                            <td style='mso-number-format:\@' align="center"><?php echo $CR1[4] ?></td>
                                            <td><?php echo $CR1[1] ?></td>
                                            <td><?php echo $CR1[7] ?></td>
                                            <td><?php echo $CR1[8] ?></td>
                                            <td><?php echo $mes ?></td>
                                            <td><?php echo $FR ?></td>
                                       
                    <?php
                                            while($Tcon1 = mysqli_fetch_row($con2)){
                                                $sql5 = "SELECT dr.valor
                                                        FROM gc_detalle_recaudo dr
                                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                                        WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                                                
                                                $conc_comercio = $mysqli->query($sql5);
                                                $nconC = mysqli_num_rows($conc_comercio);

                                                if($nconC > 0){
                                                    $V = mysqli_fetch_row($conc_comercio);
                                                    
                    ?>
                                                    <td align="right"><?php echo number_format($V[0],0,'.',',') ?></td>
                    <?php

                                                }else{
                                                    $cero = 0;
                    ?>
                                                    <td><?php echo $cero ?></td>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>TOTAL:</th>
                    <?php
                                        $con3 = $mysqli->query($sql2);
                                        while($Tcon3 = mysqli_fetch_row($con3)){
                                            $sql5 = "SELECT SUM(dr.valor)
                                                FROM gc_detalle_recaudo dr
                                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND  rc.fecha >= '$fechaI' AND rc.cuenta_ban =  '$CBan[0]' AND rc.clase = 2";

                                            $res3 = $mysqli->query($sql5);
                                            $r = mysqli_fetch_row($res3);
                    ?>
                                            <th align="right"><?php echo number_format($r[0],0,'.',',') ?></th>
                    <?php
                                        }
                    ?>  
                                        
                                    </tr>
                                    <tr></tr>
                                    <tr></tr>
                    <?php
                                }
                    
                            }
                    

                        }elseif(!empty($fecF) && empty($BancoF)){

                            $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico >= '$BancoI'  ORDER BY id_unico ASC";
                            $res = $mysqli->query($sql10);
                        

                            while($CBan = mysqli_fetch_row($res)){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                                        LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC";
                                
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

                                $sql4 = "SELECT c.id_unico,
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
                                                cb.descripcion,
                                                rc.num_pag,
                                                c.codigo_mat,
                                                d.id_unico,
                                                rc.fecha,
                                                d.cod_dec,
                                                ac.vigencia,
                                                ac.mes
                                    FROM gc_recaudo_comercial rc 
                                    LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                    LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                                    WHERE  rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

                                $Existe = "SELECT * FROM gc_recaudo_comercial WHERE fecha BETWEEN '$fechaI' AND '$fechaF' AND cuenta_ban = '$CBan[0]' ANd clase = 2";
                                $Exis = $mysqli->query($Existe);
                                $nexi = mysqli_num_rows($Exis);

                                $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]'";
                                $RES1 = $mysqli->query($sql12);
                                #echo "<br/>";
                                $nres = mysqli_num_rows($RES1);
                                #echo "num: ".$nres." banco: ".$CBan[0];
                                #echo "<br/>";
                                if($nexi > 0){

                                    $cccc = "SELECT banco, numerocuenta FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                                    $xxx = $mysqli->query($cccc);
                                    $idba = mysqli_fetch_row($xxx);

                                    $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                                             tr.apellidodos)) AS NOMBRE
                                                FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                                    $re = $mysqli->query($Nba);
                                    $Nbc = mysqli_fetch_row($re);

                                    $valor = $mysqli->query($sql2);
                                    $cantidad = $mysqli->query($sql3);


                    ?>
                                    <tr>
                                        <th>Cuenta</th>
                                        <th><?php echo $Nbc[0] ?></th>
                                        <th>Cuenta</th>
                                        <th><?php echo $idba[1]; ?></th>
                                    </tr>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Contribuyente</th>
                                        <th>Cod. Dec.</th>
                                        <th>Perioodo G.</th>
                                        <th>Mes</th>
                                        <th>Fecha R.</th>
                                    
                    <?php
                                        while($cat = mysqli_fetch_row($valor)){
                    ?>  
                                            <th><?php echo $cat[2] ?></th>    
                    <?php
                                        }  
                    ?>
                                    </tr>
                                    
                    <?php
                                    $CR = $mysqli->query($sql4);
                                    $con2 = $mysqli->query($sql2);
                                    while($CR1 = mysqli_fetch_row($CR)){

                                        if($CR1[9] == 0 ){
                                            $mes = "ANUAL";
                                        }elseif($CR1[9] == 1){
                                            $mes = "ENERO";
                                        }elseif($CR1[9] == 2){
                                            $mes = "FEBRERO";
                                        }elseif($CR1[9] == 3){
                                            $mes = "MARZO";
                                        }elseif($CR1[9] == 4){
                                            $mes = "ABRIL";
                                        }elseif($CR1[9] == 5){
                                            $mes = "MAYO";
                                        }elseif($CR1[9] == 6){
                                            $mes = "JUNIO";
                                        }elseif($CR1[9] == 7){
                                            $mes = "JULIO";
                                        }elseif($CR1[9] == 8){
                                            $mes = "AGOSTO";
                                        }elseif($CR1[9] == 9){
                                            $mes = "SEPTIEMBRE";
                                        }elseif($CR1[9] == 10){
                                            $mes = "OCTUBRE";
                                        }elseif($CR1[9] == 11){
                                            $mes = "NOVIEMBRE";
                                        }elseif($CR1[9] == 12){
                                            $mes = "DICIEMBRE";
                                        }
                                        $FReca = explode("-",$CR1[6]);
                                        $A = $FReca[0];
                                        $M = $FReca[1];
                                        $D = $FReca[2];

                                        $FR = $D.'/'.$M.'/'.$A;
                    ?>
                                        <tr>
                                            <td style='mso-number-format:\@' align="center"><?php echo $CR1[4] ?></td>
                                            <td><?php echo $CR1[1] ?></td>
                                            <td><?php echo $CR1[7] ?></td>
                                            <td><?php echo $CR1[8] ?></td>
                                            <td><?php echo $mes ?></td>
                                            <td><?php echo $FR ?></td>
                                       
                    <?php
                                            while($Tcon1 = mysqli_fetch_row($con2)){
                                                $sql5 = "SELECT dr.valor
                                                        FROM gc_detalle_recaudo dr
                                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                                        WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                                                
                                                $conc_comercio = $mysqli->query($sql5);
                                                $nconC = mysqli_num_rows($conc_comercio);

                                                if($nconC > 0){
                                                    $V = mysqli_fetch_row($conc_comercio);
                                                    
                    ?>
                                                    <td align="right"><?php echo number_format($V[0],0,'.',',') ?></td>
                    <?php

                                                }else{
                                                    $cero = 0;
                    ?>
                                                    <td><?php echo $cero ?></td>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>TOTAL:</th>
                    <?php
                                        $con3 = $mysqli->query($sql2);
                                        while($Tcon3 = mysqli_fetch_row($con3)){
                                            $sql5 = "SELECT SUM(dr.valor)
                                                FROM gc_detalle_recaudo dr
                                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND  rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban =  '$CBan[0]' AND rc.clase = 2";

                                            $res3 = $mysqli->query($sql5);
                                            $r = mysqli_fetch_row($res3);
                    ?>
                                            <th align="right"><?php echo number_format($r[0],0,'.',',') ?></th>
                    <?php
                                        }
                    ?>  
                                        
                                    </tr>
                                    <tr></tr>
                                    <tr></tr>
                    <?php
                                }

                            }
                        }elseif(empty($fecF) && !empty($BancoF)){
                            $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico BETWEEN '$BancoI' AND '$BancoF' ORDER BY id_unico ASC";
                            $res = $mysqli->query($sql10);
                        

                            while($CBan = mysqli_fetch_row($res)){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                                        LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC";

                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE rc.fecha >= '$fechaI' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 "; 
                    
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
                                                    cb.descripcion,
                                                    rc.num_pag,
                                                    c.codigo_mat,
                                                    d.id_unico,
                                                    rc.fecha,
                                                    d.cod_dec,
                                                    ac.vigencia,
                                                    ac.mes
                                        FROM gc_recaudo_comercial rc 
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico 
                                        WHERE  rc.fecha >= '$fechaI'  AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";

                                $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]' ";
                                $RES1 = $mysqli->query($sql12);
                                
                                $nre = mysqli_num_rows($RES1);

                                if($nre > 0){
                                    $cccc = "SELECT banco FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                                    $xxx = $mysqli->query($cccc);
                                    $idba = mysqli_fetch_row($xxx);

                                    $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                                             tr.apellidodos)) AS NOMBRE
                                                FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                                    $re = $mysqli->query($Nba);
                                    $Nbc = mysqli_fetch_row($re);

                                    $valor = $mysqli->query($sql2);
                                    $cantidad = $mysqli->query($sql3);

                    ?>
                                    <tr>
                                        <th>Cuenta</th>
                                        <th><?php echo $Nbc[0] ?></th>
                                    </tr>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Contribuyente</th>
                                        <th>Cod. Dec.</th>
                                        <th>Perioodo G</th>
                                        <th>Mes</th>
                                        <th>Fecha R.</th>
                                    
                    <?php
                                        while($cat = mysqli_fetch_row($valor)){
                    ?>  
                                            <th><?php echo $cat[2] ?></th>    
                    <?php
                                        }  
                    ?>
                                    </tr>
                                    
                    <?php
                                    $CR = $mysqli->query($sql4);
                                    $con2 = $mysqli->query($sql2);
                                    while($CR1 = mysqli_fetch_row($CR)){
                                        if($CR1[9] == 0 ){
                                            $mes = "ANUAL";
                                        }elseif($CR1[9] == 1){
                                            $mes = "ENERO";
                                        }elseif($CR1[9] == 2){
                                            $mes = "FEBRERO";
                                        }elseif($CR1[9] == 3){
                                            $mes = "MARZO";
                                        }elseif($CR1[9] == 4){
                                            $mes = "ABRIL";
                                        }elseif($CR1[9] == 5){
                                            $mes = "MAYO";
                                        }elseif($CR1[9] == 6){
                                            $mes = "JUNIO";
                                        }elseif($CR1[9] == 7){
                                            $mes = "JULIO";
                                        }elseif($CR1[9] == 8){
                                            $mes = "AGOSTO";
                                        }elseif($CR1[9] == 9){
                                            $mes = "SEPTIEMBRE";
                                        }elseif($CR1[9] == 10){
                                            $mes = "OCTUBRE";
                                        }elseif($CR1[9] == 11){
                                            $mes = "NOVIEMBRE";
                                        }elseif($CR1[9] == 12){
                                            $mes = "DICIEMBRE";
                                        }
                                        $FReca = explode("-",$CR1[6]);
                                        $A = $FReca[0];
                                        $M = $FReca[1];
                                        $D = $FReca[2];

                                        $FR = $D.'/'.$M.'/'.$A;
                    ?>
                                        <tr>
                                            <td style='mso-number-format:\@' align="center"><?php echo $CR1[4] ?></td>
                                            <td><?php echo $CR1[1] ?></td>
                                            <td><?php echo $CR1[7] ?></td>
                                            <td><?php echo $CR1[8] ?></td>
                                            <td><?php echo $mes ?></td>
                                            <td><?php echo $FR ?></td>
                                       
                    <?php
                                            while($Tcon1 = mysqli_fetch_row($con2)){
                                                $sql5 = "SELECT dr.valor
                                                        FROM gc_detalle_recaudo dr
                                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                                        WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                                                
                                                $conc_comercio = $mysqli->query($sql5);
                                                $nconC = mysqli_num_rows($conc_comercio);

                                                if($nconC > 0){
                                                    $V = mysqli_fetch_row($conc_comercio);
                                                    
                    ?>
                                                    <td align="right"><?php echo number_format($V[0],0,'.',',') ?></td>
                    <?php

                                                }else{
                                                    $cero = 0;
                    ?>
                                                    <td><?php echo $cero ?></td>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>TOTAL:</th>
                    <?php
                                        $con3 = $mysqli->query($sql2);
                                        while($Tcon3 = mysqli_fetch_row($con3)){
                                            $sql5 = "SELECT SUM(dr.valor)
                                                FROM gc_detalle_recaudo dr
                                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND  rc.fecha AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$CBan[0]'  AND rc.clase = 2";

                                            $res3 = $mysqli->query($sql5);
                                            $r = mysqli_fetch_row($res3);
                    ?>
                                            <th align="right"><?php echo number_format($r[0],0,'.',',') ?></th>
                    <?php
                                        }
                    ?>  
                                        
                                    </tr>
                    <?php
                                }
                            }
                        }else{
                            $sql10 = "SELECT id_unico, numerocuenta, banco FROM  gf_cuenta_bancaria WHERE id_unico BETWEEN '$BancoI' AND '$BancoF' ORDER BY id_unico ASC";
                            $res = $mysqli->query($sql10);
                        

                            while($CBan = mysqli_fetch_row($res)){
                                $sql2 = "SELECT DISTINCT dd.concepto,  cc.descripcion, cc.nom_inf FROM gc_detalle_declaracion dd
                                        LEFT JOIN   gc_detalle_recaudo dr ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2 ORDER BY cc.codigo ASC";
                                    
                                $sql3 = "SELECT COUNT(DISTINCT dd.concepto) FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto =cc.id_unico
                                        WHERE rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban = '$CBan[0]' AND rc.clase = 2";      

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
                                                    cb.banco,
                                                    rc.num_pag,
                                                    c.codigo_mat,
                                                    d.id_unico,
                                                    rc.fecha,
                                                    d.cod_dec,
                                                    ac.vigencia,
                                                    ac.mes
                                        FROM gc_recaudo_comercial rc 
                                        LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
                                        LEFT JOIN gf_tercero  tr ON c.tercero = tr.id_unico 
                                        LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                        WHERE  rc.fecha BETWEEN '$fechaI' AND '$fechaF'  AND rc.cuenta_ban  = '$CBan[0]' AND rc.clase = 2";

                                

                                $sql12 = "SELECT * FROM gc_recaudo_comercial WHERE cuenta_ban = '$CBan[0]' AND clase = 2";
                                $RES1 = $mysqli->query($sql12);
                                
                                $nre = mysqli_num_rows($RES1);

                                if($nre > 0){
                                    $cccc = "SELECT banco FROM  gf_cuenta_bancaria WHERE id_unico = '$CBan[0]'";
                                    $xxx = $mysqli->query($cccc);
                                    $idba = mysqli_fetch_row($xxx);

                                    $Nba = "SELECT IF(CONCAT_WS(' ',
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
                                                             tr.apellidodos)) AS NOMBRE
                                                FROM gf_tercero tr WHERE id_unico = '$idba[0]'";

                                    $re = $mysqli->query($Nba);
                                    $Nbc = mysqli_fetch_row($re);

                                    $valor = $mysqli->query($sql2);
                                    $cantidad = $mysqli->query($sql3);

                    ?>
                                    <tr>
                                        <th>Cuenta</th>
                                        <th><?php echo $Nbc[0] ?></th>
                                    </tr>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Contribuyente</th>
                                        <th>Cod. Dec.</th>
                                        <th>Perioodo G.</th>
                                        <th>Mes</th>
                                        <th>Fecha R.</th>
                                    
                    <?php
                                        while($cat = mysqli_fetch_row($valor)){
                    ?>  
                                            <th><?php echo $cat[2] ?></th>    
                    <?php
                                        }  
                    ?>
                                    </tr>
                                    
                    <?php
                                    $CR = $mysqli->query($sql4);
                                    $con2 = $mysqli->query($sql2);
                                    while($CR1 = mysqli_fetch_row($CR)){

                                        if($CR1[9] == 0 ){
                                            $mes = "ANUAL";
                                        }elseif($CR1[9] == 1){
                                            $mes = "ENERO";
                                        }elseif($CR1[9] == 2){
                                            $mes = "FEBRERO";
                                        }elseif($CR1[9] == 3){
                                            $mes = "MARZO";
                                        }elseif($CR1[9] == 4){
                                            $mes = "ABRIL";
                                        }elseif($CR1[9] == 5){
                                            $mes = "MAYO";
                                        }elseif($CR1[9] == 6){
                                            $mes = "JUNIO";
                                        }elseif($CR1[9] == 7){
                                            $mes = "JULIO";
                                        }elseif($CR1[9] == 8){
                                            $mes = "AGOSTO";
                                        }elseif($CR1[9] == 9){
                                            $mes = "SEPTIEMBRE";
                                        }elseif($CR1[9] == 10){
                                            $mes = "OCTUBRE";
                                        }elseif($CR1[9] == 11){
                                            $mes = "NOVIEMBRE";
                                        }elseif($CR1[9] == 12){
                                            $mes = "DICIEMBRE";
                                        }

                                        $FReca = explode("-",$CR1[6]);
                                        $A = $FReca[0];
                                        $M = $FReca[1];
                                        $D = $FReca[2];

                                        $FR = $D.'/'.$M.'/'.$A;
                    ?>
                                        <tr>
                                            <td style='mso-number-format:\@' align="center"><?php echo $CR1[4] ?></td>
                                            <td><?php echo $CR1[1] ?></td>
                                            <td><?php echo $CR1[7] ?></td>
                                            <td><?php echo $CR1[8] ?></td>
                                            <td><?php echo $mes ?></td>
                                            <td><?php echo $FR ?></td>
                                       
                    <?php
                                            while($Tcon1 = mysqli_fetch_row($con2)){
                                                $sql5 = "SELECT dr.valor
                                                        FROM gc_detalle_recaudo dr
                                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                                        WHERE d.contribuyente = '$CR1[0]' AND cc.id_unico = '$Tcon1[0]' AND d.id_unico = '$CR1[5]'";
                                                
                                                $conc_comercio = $mysqli->query($sql5);
                                                $nconC = mysqli_num_rows($conc_comercio);

                                                if($nconC > 0){
                                                    $V = mysqli_fetch_row($conc_comercio);
                                                    
                    ?>
                                                    <td align="right"><?php echo number_format($V[0],0,'.',',') ?></td>
                    <?php

                                                }else{
                                                    $cero = 0;
                    ?>
                                                    <td><?php echo $cero ?></td>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>TOTAL:</th>
                    <?php
                                        $con3 = $mysqli->query($sql2);
                                        while($Tcon3 = mysqli_fetch_row($con3)){
                                            $sql5 = "SELECT SUM(dr.valor)
                                                FROM gc_detalle_recaudo dr
                                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo =  rc.id_unico
                                                WHERE dd.concepto = '$Tcon3[0]' AND  rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban =  '$CBan[0]' AND rc.clase = 2";

                                            $res3 = $mysqli->query($sql5);
                                            $r = mysqli_fetch_row($res3);
                    ?>
                                            <th align="right"><?php echo number_format($r[0],0,'.',',') ?></th>
                    <?php
                                        }
                    ?>  
                                        
                                    </tr>
                    <?php
                                }
                            }
                        }

                        #fin de validacion de los campos que pueden ser vacios

                        $hoy = date('d-m-Y');
                        $hoy = trim($hoy, '"');
                        $fecha_div = explode("-", $hoy);
                        $anioA = $fecha_div[2];
                        $mesA = $fecha_div[1];
                        $diaA = $fecha_div[0];

                        $vigeAct = $anioA;
                        #inicio de validacion de lso campos que pueden ser vacios para el calculo de la tabla de los valores  consolidados 
                        if(empty($fecF) && empty($BancoF)){

                            $VigenciaAC = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia = '$vigeAct' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban >= '$BancoI' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigActual = $mysqli->query($VigenciaAC);
                            $VAC = mysqli_fetch_row($VigActual);

                            $VigenciaAN = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia < '$vigeAct' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban >= '$BancoI' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigAnt = $mysqli->query($VigenciaAN);
                            $VAN = mysqli_fetch_row($VigAnt);
            
                            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$BancoI' AND rc.clase = 2
                                    order by cc.codigo ASC ";

                            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban = '$BancoI' AND rc.clase = 2
                                    order by cc.codigo ASC ";

                        }elseif(!empty($fecF) && empty($BancoF)){

                            $VigenciaAC = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia = '$vigeAct' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban >= '$BancoI' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigActual = $mysqli->query($VigenciaAC);
                            $VAC = mysqli_fetch_row($VigActual);

                            $VigenciaAN = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia < '$vigeAct' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban >= '$BancoI' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigAnt = $mysqli->query($VigenciaAN);
                            $VAN = mysqli_fetch_row($VigAnt);

                            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha BETWEEN'$fechaI' AND '$fechaF' AND  rc.cuenta_ban >= '$BancoI' AND rc.clase = 2
                                    order by cc.codigo ASC ";

                            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban = '$BancoI' AND rc.clase = 2
                                    order by cc.codigo ASC ";


                        }elseif(empty($fecF) && !empty($BancoF)){

                            $VigenciaAC = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia = '$vigeAct' AND rc.fecha >= '$fechaI' AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigActual = $mysqli->query($VigenciaAC);
                            $VAC = mysqli_fetch_row($VigActual);

                            $VigenciaAN = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia < '$vigeAct' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$fechaF' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigAnt = $mysqli->query($VigenciaAN);
                            $VAN = mysqli_fetch_row($VigAnt);

                            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2
                                    order by cc.codigo ASC ";

                            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha >= '$fechaI' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BacnoF' AND rc.clase = 2
                                    order by cc.codigo ASC ";
                        }else{

                            $VigenciaAC = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia = '$vigeAct' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigActual = $mysqli->query($VigenciaAC);
                            $VAC = mysqli_fetch_row($VigActual);

                            $VigenciaAN = "SELECT SUM(rc.valor) FROM gc_recaudo_comercial rc 
                                            LEFT JOIN gc_declaracion d ON rc.declaracion = d.id_unico 
                                            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
                                            LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                            WHERE ac.vigencia < '$vigeAct' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$fechaF' AND rc.clase = 2 AND cc.tipo_ope = 1 AND cc.tipo = 7 AND dd.tipo_det = 1";

                            $VigAnt = $mysqli->query($VigenciaAN);
                            $VAN = mysqli_fetch_row($VigAnt);

                            $ConceptosVAN = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                    WHERE ac.vigencia < '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.parametrizacionanno = '$id_anno' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2
                                    order by cc.codigo ASC ";
                            echo "<br/>";
                            $ConceptosVAC = "SELECT DISTINCT (dd.concepto), cc.* FROM gc_detalle_recaudo dr 
                                    LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                    LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                    LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                    LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                    LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico 
                                    WHERE ac.vigencia = '$vigeAct' AND dd.tipo_det = 1 AND cc.tipo != 1 AND cc.tipo != 4 AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.clase = 2
                                    order by cc.codigo ASC ";
                        }
                    ?>
                        <tr>
                            <th>TOTAL VIGENCIA ACTUAL:</th>
                            <th><?php echo number_format($VAC[0],0,'.',',') ?></th>
                        </tr>
                        <tr>
                    <?php
                        $ConRec = $mysqli->query($ConceptosVAC);
                        $NConR = mysqli_num_rows($ConRec);

                        if($NConR > 0){
                            while($rowCR = mysqli_fetch_row($ConRec)){
                    ?>
                                <th><?php echo $rowCR[11] ?></th>
                    <?php
                            }
                        }
                    ?>     
                        </tr>
                        <tr>
                    <?php
                            $conR2 = $mysqli->query($ConceptosVAC);
                            while($TconR2 = mysqli_fetch_row($conR2)){

                                
                                if(empty($fecF) && empty($BancoF)){
                                    $sql10 = "SELECT SUM(dr.valor)
                                        FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban >= '$BancoI' AND rc.fecha >= '$fechaI' AND rc.clase = 2";
                                }elseif(!empty($fecF) && empty($BancoF)){
                                    $sql10 = "SELECT SUM(dr.valor)
                                        FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban >= '$BancoI' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2";

                                }elseif(empty($fecF) && !empty($BancoF)){
                                    $sql10 = "SELECT SUM(dr.valor)
                                        FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.fecha >= '$fechaI' AND rc.clase = 2";
                                }else{
                                    $sql10 = "SELECT SUM(dr.valor)
                                        FROM gc_detalle_recaudo dr
                                        LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                        LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                        LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                        LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                        LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                        WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia = '$vigeAct' AND  rc.cuenta_ban BETWEEN '$BancoI' AND '$BancoF' AND rc.fecha BETWEEN '$fechaI' AND '$fechaF' AND rc.clase = 2";
                                }

                                $conc_comercio = $mysqli->query($sql10);
                                $nconC = mysqli_num_rows($conc_comercio);
                        
                                if($nconC > 0){
                                    $V = mysqli_fetch_row($conc_comercio);
                                }else{
                                    $V[0] = 0;
                                }
                    ?>
                                <th><?php echo number_format($V[0],0,'.',',') ?></th>
                    <?php

                            }
                    ?>
                        </tr> 
                        <tr></tr>
                        <tr>
                            <th>TOTAL VIGENCIA ANTERIOR:</th>
                            <th><?php echo number_format($VAN[0],0,'.',',') ?></th>
                        </tr>
                         
                    <?php
                        $ConRec = $mysqli->query($ConceptosVAN);
                        $NConR = mysqli_num_rows($ConRec);

                        if($NConR > 0){
                    ?>
                            <tr>
                    <?php
                                while($rowCR = mysqli_fetch_row($ConRec)){
                    ?>
                                    <th><?php echo $rowCR[11] ?></th>
                    <?php
                                }
                    ?>
                            </tr>
                            <tr>
                    <?php
                                $conR2 = $mysqli->query($ConceptosVAN);
                                while($TconR2 = mysqli_fetch_row($conR2)){

                                    $sql10 = "SELECT SUM(dr.valor)
                                                FROM gc_detalle_recaudo dr
                                                LEFT JOIN gc_detalle_declaracion dd ON dr.det_dec = dd.id_unico
                                                LEFT JOIN gc_declaracion d ON dd.declaracion = d.id_unico
                                                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico
                                                LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
                                                LEFT JOIN gc_recaudo_comercial rc ON dr.recaudo = rc.id_unico
                                                WHERE  cc.id_unico = '$TconR2[0]' AND ac.vigencia < '$vigeAct' AND rc.parametrizacionanno = '$id_anno' AND rc.clase = 2";
                
                                    $conc_comercio = $mysqli->query($sql10);
                                    $nconC = mysqli_num_rows($conc_comercio);
                        
                                    if($nconC > 0){
                                        $V = mysqli_fetch_row($conc_comercio);
                                    }else{
                                        $V[0] = 0;
                                    }
                    ?>
                                    <th><?php echo number_format($V[0],0,'.',',') ?></th>
                    <?php

                                }
                        
                    ?>
                            </tr>   
                    <?php
                        }
                    ?>            
                        
                        
    </tbody>  
           
</table>
</body>
</html>