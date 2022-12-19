<?php

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Aportes_parafilacales_Det.xls");
    require_once("../Conexion/conexion.php");
    session_start();
    ini_set('max_execution_time', 0);
    #@$periodo       = $_GET['periodo'];
    $grupog = $_POST['sltGrupoG'];
    $periodo  = $_POST['sltPeriodo'];
    $tipof  = $_POST['sltTipoF'];
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];
   
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
                        <?php
                            }
                        ?>
                    </tr>
                        <?php
                            if(empty($grupog) || $grupog == ""){
                                $G[0] = "Todos";
                            }else{
                                $GR = "SELECT nombre FROM gn_grupo_gestion WHERE id_unico = '$grupog'";
                                $GRU = $mysqli->query($GR);
                                $G = mysqli_fetch_row($GRU);
                            }
                        ?>
                    <tr>
                        <th>
                            Grupo Gestión
                        </th>
                        <th>
                            <?php echo $G[0]; ?>
                        </th>
                    </tr>
                    <tr>

                        <?php
                         
                           if(empty($periodo) || $periodo == ""){
                               
                                $pern[1] = "Todos";
                                $pern[2] = "Todos";
                                $pern[3] = "Todos";
                                   
                            }else{
                                $sqlper = "SELECT   p.id_unico, 
                                                p.codigointerno, 
                                                p.fechainicio, 
                                                p.fechafin 
                                        FROM gn_periodo p 
                                        WHERE p.id_unico = $periodo";
                                $per = $mysqli->query($sqlper);
                                $pern = mysqli_fetch_row($per);
                            }    
                        ?>
                                    <th>
                                        Periodo:
                                    </th>
                                    <th>
                                        <?php echo $pern[1]; ?>
                                    </th>
                                    <th>
                                        Fecha Inicial:
                                    </th>
                                    <th>
                                        <?php echo $pern[2]; ?>
                                    </th>
                                <th>    
                                    Fecha Final:
                                </th>
                                <th>
                                    <?php echo $pern[3]; ?>
                                </th>
                        
                    </tr>
                    <tr>
                        <?php 
                            if(empty($grupog) && empty($periodo)  && empty($tipof)){
                                
                                $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero "
                                        . "t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                $valor = $mysqli->query($salud);

                                $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                $Fond = $mysqli->query($fondo);
                        ?>
                                <th>
                                    Tipo Fondo
                                </th>
                                <th>
                                    Salud
                                </th>
                        
                        <?php
                                while($SAL = mysqli_fetch_row($valor)){
                                    
                        ?>
                                    <tr>
                                        <th>
                                        <tr></tr>
                                        </th>
                                        <th>
                                            NIT
                                        </th>
                                        <th>
                                            <?php echo $SAL[0] ;?>
                                        </th>
                                        <th>
                                            Entidad
                                        </th>
                                        <th>
                                            <?php echo $SAL[1] ;?>
                                        </th>    
                                    </tr>
                                    <th>
                                        Código
                                    </th>
                                    <th>
                                        Número de Identificación
                                    </th>
                                    <th>
                                        Empleado
                                    </th>
                                    <th>
                                        Aporte Empleado
                                    </th>
                                    <th>
                                        Aporte Patrono
                                    </th>
                                    <th>
                                        Total
                                    </th>   
                        <?php
                                    
                                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                e.codigointerno,
                                                                e.tercero,
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
                                                                tr.apellidodos)) AS NOMBRE,
                                                                tr.numeroidentificacion

                                                      FROM gn_empleado e
                                                      LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                      LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                      LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                      LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                      LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                      WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]'";

                                    $resemp = $mysqli->query($sqlemp);
                                    
                                    while($ConE = mysqli_fetch_row($resemp)){
                       
                                        #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa 
                                        #por la entidad de salud
                                        $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'";

                                        $vaem = $mysqli->query($vemple);
                                        $valE = mysqli_fetch_row($vaem);
                                        

                                        

                                        #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa 
                                        #por la entidad de salud
                                        $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'";

                                        $vapa = $mysqli->query($vpat);
                                        $valP = mysqli_fetch_row($vapa);

                                        

                                        #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                                        $VTot = "SELECT SUM(n.valor)

                                            FROM gn_novedad n
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                            WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]'";

                                        $valT = $mysqli->query($VTot);
                                        $TOT = mysqli_fetch_row($valT);
                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $ConE[1];?>
                                            </td>

                                            <td>
                                                <?php echo $ConE[4];?>
                                            </td>
                                            <td>
                                                <?php echo $ConE[3];?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valE[0],2,'.',',');?>
                                            </td>

                                            <td align="right">
                                                <?php echo number_format($valP[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($TOT[0],2,'.',',');?>
                                            </td>
                                        </tr>      
                        <?php
                                        
                                    }
                                    
                                    $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                            . "WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'";
                                    $VToEM = $mysqli->query($ToEM);
                                    $VAEMP = mysqli_fetch_row($VToEM);
                                    
                                    $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                            . "WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'";

                                    $vapa1 = $mysqli->query($vpat1);
                                    $valP1 = mysqli_fetch_row($vapa1);
                                    
                                    $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                            WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]'";

                                    $valT1 = $mysqli->query($VTot1);
                                    $TOT1 = mysqli_fetch_row($valT1);
                        ?>
                                <tr>
                                    <td>
                                        
                                    </td>
                                    <td>
                                        
                                    </td>
                                    <td>
                                        TOTAL
                                    </td>
                                    <td align="right">
                                        <?php echo number_format($VAEMP[0],2,'.',',');?>
                                    </td>
                                    <td align="right">
                                        <?php echo number_format($valP1[0],2,'.',',');?>
                                    </td>
                                    <td align="right">
                                        <?php echo number_format($TOT1[0],2,'.',',');?>
                                    </td>
                                </tr>    
                        <?php            
                                } /// finaliza Salud
                                                    
                                $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                        . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                $valor1 = $mysqli->query($pension);
                        ?>
                                <th>
                                    Tipo Fondo
                                </th>
                                <th>
                                    Pensión
                                </th>
                        
                        <?php
                                while($PEN = mysqli_fetch_row($valor1)){
                        ?>
                                    <tr>
                                        <th>
                                        <tr></tr>
                                        </th>
                                        <th>
                                            NIT
                                        </th>
                                        <th>
                                            <?php echo $PEN[0] ;?>
                                        </th>
                                        <th>
                                            Entidad
                                        </th>
                                        <th>
                                            <?php echo $PEN[1] ;?>
                                        </th>    
                                    </tr>
                                    <th>
                                        Código
                                    </th>
                                    <th>
                                        Número de Identificación
                                    </th>
                                    <th>
                                        Empleado
                                    </th>
                                    <th>
                                        Aporte Empleado
                                    </th>
                                    <th>
                                        Aporte Patrono
                                    </th>
                                    <th>
                                        Fondo Solid
                                    </th>
                                    <th>
                                        Total
                                    </th>   
                        <?php  
                                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                e.codigointerno,
                                                                e.tercero,
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
                                                                tr.apellidodos)) AS NOMBRE,
                                                                tr.numeroidentificacion

                                                      FROM gn_empleado e
                                                      LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                      LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                      LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                      LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                      LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                      WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]'";

                                    $resemp = $mysqli->query($sqlemp);

                                    while($ConE = mysqli_fetch_row($resemp)){
                                        
                                        $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' ";

                                        $vaem = $mysqli->query($vemple);
                                        $valE = mysqli_fetch_row($vaem);
                                        
                                        $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'";

                                        $vapa = $mysqli->query($vpat);
                                        $valP = mysqli_fetch_row($vapa); 
                                        
                                        $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'";

                                        $foso = $mysqli->query($Fsol);
                                        $SolF = mysqli_fetch_row($foso);
                                        
                                        $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                            WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]'";

                                        $valT = $mysqli->query($VTot);
                                        $TOT = mysqli_fetch_row($valT);
                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $ConE[1];?>
                                            </td>

                                            <td>
                                                <?php echo $ConE[4];?>
                                            </td>
                                            <td>
                                                <?php echo $ConE[3];?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valE[0],2,'.',',');?>
                                            </td>

                                            <td align="right">
                                                <?php echo number_format($valP[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($SolF[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($TOT[0],2,'.',',');?>
                                            </td>
                                        </tr>      
                        <?php                
                                    }
                                    
                                    $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                            . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' ";

                                    $vaem1 = $mysqli->query($vemple1);
                                    $valE1 = mysqli_fetch_row($vaem1);
                                    
                                    $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                            . "WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'";

                                    $vapa1 = $mysqli->query($vpat1);
                                    $valP1 = mysqli_fetch_row($vapa1);
                                    
                                    $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                            . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' ";

                                    $foso1 = $mysqli->query($Fsol1);
                                    $SolF1 = mysqli_fetch_row($foso1);
                                    
                                    $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                            WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' ";

                                    $VAl12  = $mysqli->query($VTot12);
                                    $TOVAL12 = mysqli_fetch_row($VAl12);
                        ?>
                                    <tr>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            TOTAL
                                        </td>
                                        <td align="right">
                                            <?php echo number_format($valE1[0],2,'.',',');?>
                                        </td>
                                        <td align="right">
                                            <?php echo number_format($valP1[0],2,'.',',');?>
                                        </td>
                                        <td align="right">
                                            <?php echo number_format($SolF1[0],2,'.',',');?>
                                        </td>
                                        <td align="right">
                                            <?php echo number_format($TOVAL12[0],2,'.',',');?>
                                        </td>
                                    </tr>        
                        <?php                    
                                }/// finaliza el cargue de datos de pesión
                                
                                $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                        . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                $valor2 = $mysqli->query($arl);
                        
                        ?>
                                <th>
                                    Tipo Fondo
                                </th>
                                <th>
                                    ARL
                                </th>
                        
                        <?php
                                while($AR = mysqli_fetch_row($valor2)){
                        
                        ?>
                                    <tr>
                                        <th>
                                        <tr></tr>
                                        </th>
                                        <th>
                                            NIT
                                        </th>
                                        <th>
                                            <?php echo $AR[0] ;?>
                                        </th>
                                        <th>
                                            Entidad
                                        </th>
                                        <th>
                                            <?php echo $AR[1] ;?>
                                        </th>    
                                    </tr>
                                    <th>
                                        Código
                                    </th>
                                    <th>
                                        Número de Identificación
                                    </th>
                                    <th>
                                        Empleado
                                    </th>
                                    <th>
                                        Aporte Patrono
                                    </th>
                                     
                        <?php
                        
                                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                        e.codigointerno,
                                                        e.tercero,
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
                                                        tr.apellidodos)) AS NOMBRE,
                                                        tr.numeroidentificacion

                                              FROM gn_empleado e
                                              LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                              LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                              LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                              WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2";

                                    $resemp = $mysqli->query($sqlemp);
                                    
                                    while($ConE = mysqli_fetch_row($resemp)){
                                        
                                        $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]'";
                                        $vapa = $mysqli->query($vpat);
                                        $valP = mysqli_fetch_row($vapa);
                        
                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $ConE[1];?>
                                            </td>

                                            <td>
                                                <?php echo $ConE[4];?>
                                            </td>
                                            <td>
                                                <?php echo $ConE[3];?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP[0],2,'.',',');?>
                                            </td>
                                        </tr>      
                        <?php                
                                    }
                                    
                                    $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n WHERE n.concepto =363 ";
                                    $vapa1 = $mysqli->query($vpat1);
                                    $valP1 = mysqli_fetch_row($vapa1);          
                        ?>
                                    <tr>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            TOTAL
                                        </td>
                                        <td align="right">
                                            <?php echo number_format($valP1[0],2,'.',',');?>
                                        </td>
                                    </tr>        
                        <?php          
                                }/// finaliza el cargue de arl
                                
                                $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c "
                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipoentidadcredito is not NULL";
                                $valor2 = $mysqli->query($paraf);
                        ?>
                                <th>
                                    Tipo Fondo
                                </th>
                                <th>
                                    PARAFISCALES
                                </th>
                        
                        <?php        
                                while($PAR = mysqli_fetch_row($valor2)){
                                  
                        ?>
                                    <tr>
                                        <th>
                                        <tr></tr>
                                        </th>
                                        <th>
                                            NIT
                                        </th>
                                        <th>
                                            <?php echo $PAR[2] ;?>
                                        </th>
                                        <th>
                                            Entidad
                                        </th>
                                        <th>
                                            <?php echo $PAR[1] ;?>
                                        </th>    
                                    </tr>
                                    <th>
                                        Código
                                    </th>
                                    <th>
                                        Número de Identificación
                                    </th>
                                    <th>
                                        Empleado
                                    </th>
                                    <th>
                                        Aporte Patrono
                                    </th>
                                     
                        <?php

                                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                        e.codigointerno,
                                                        e.tercero,
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
                                                        tr.apellidodos)) AS NOMBRE,
                                                        tr.numeroidentificacion

                                              FROM gn_empleado e
                                              LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                              LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                              LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                              WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2";

                                    $resemp = $mysqli->query($sqlemp);

                                    while($ConE = mysqli_fetch_row($resemp)){
                                      
                                        $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' ";

                                        $vapa = $mysqli->query($vpat);
                                        $valP = mysqli_fetch_row($vapa);
                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $ConE[1];?>
                                            </td>

                                            <td>
                                                <?php echo $ConE[4];?>
                                            </td>
                                            <td>
                                                <?php echo $ConE[3];?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP[0],2,'.',',');?>
                                            </td>
                                        </tr>      
                        <?php
                                    }
                                    
                                    $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                            . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.id_unico = '$PAR[0]'";
                                    $vapa1 = $mysqli->query($vpat1);
                                    $valP2 = mysqli_fetch_row($vapa1);
                        ?>
                                    <tr>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            TOTAL
                                        </td>
                                        <td align="right">
                                            <?php echo number_format($valP2[0],2,'.',',');?>
                                        </td>
                                    </tr>        
                        <?php            
                                }//// finaliza el cargue de parafiscales
                                
                                //finaliza la condicion cuando todos los campos son vacios
                       
                            }elseif(!empty ($grupog)){
                                
                                if(empty($periodo) && empty($tipof)){
                                    
                                    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                    $valor = $mysqli->query($salud);

                                    $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                    $Fond = $mysqli->query($fondo);
                        
                        ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        SALUD
                                    </th>
                        
                        <?php
                                    while($SAL = mysqli_fetch_row($valor)){
                                    
                        ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $SAL[0] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $SAL[1] ;?>
                                            </th>    
                                        </tr>
                                        <th>
                                            Código
                                        </th>
                                        <th>
                                            Número de Identificación
                                        </th>
                                        <th>
                                            Empleado
                                        </th>
                                        <th>
                                            Aporte Empleado
                                        </th>
                                        <th>
                                            Aporte Patrono
                                        </th>
                                        <th>
                                            Total
                                        </th>   
                        <?php    
                        
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                        e.codigointerno,
                                                        e.tercero,
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
                                                        tr.apellidodos)) AS NOMBRE,
                                                        tr.numeroidentificacion

                                              FROM gn_empleado e
                                              LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                              LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                              LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                              WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog'";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){

                                            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                                            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'";

                                            $vaem = $mysqli->query($vemple);
                                            $valE = mysqli_fetch_row($vaem);

                                            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                                            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);

                                            #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                                            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' ";

                                            $valT = $mysqli->query($VTot);
                                            $TOT = mysqli_fetch_row($valT);
                                        
                                ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valE[0],2,'.',',');?>
                                                </td>

                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOT[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                                <?php
                                        }

                                        $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                            WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

                                        $VToEM = $mysqli->query($ToEM);
                                        $VAEMP = mysqli_fetch_row($VToEM);

                                        $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);

                                        $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                            WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

                                        $valT1 = $mysqli->query($VTot1);
                                        $TOT1 = mysqli_fetch_row($valT1);
                                
                                ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($VAEMP[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($TOT1[0],2,'.',',');?>
                                            </td>
                                        </tr>    
                                <?php
                                    }/// Finaliza el cargue de datos de Salud
                                    
                                    $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                    $valor1 = $mysqli->query($pension);
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        PENSIÓN
                                    </th>
                        
                                <?php    
                                    while($PEN = mysqli_fetch_row($valor1)){
                                    
                                ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $PEN[0] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $PEN[1] ;?>
                                            </th>    
                                        </tr>
                                        <th>
                                            Código
                                        </th>
                                        <th>
                                            Número de Identificación
                                        </th>
                                        <th>
                                            Empleado
                                        </th>
                                        <th>
                                            Aporte Empleado
                                        </th>
                                        <th>
                                            Aporte Patrono
                                        </th>
                                        <th>
                                            Fondo Solid
                                        </th>
                                        <th>
                                            Total
                                        </th>   
                                <?php        
                        
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                        e.codigointerno,
                                                        e.tercero,
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
                                                        tr.apellidodos)) AS NOMBRE,
                                                        tr.numeroidentificacion

                                                  FROM gn_empleado e
                                                  LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                  LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                  LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                  LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                  WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog'";

                                        $resemp = $mysqli->query($sqlemp);
                                        $nresemp = mysqli_num_rows($resemp); 
                                        
                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' ";

                                            $vaem = $mysqli->query($vemple);
                                            $valE = mysqli_fetch_row($vaem);
                                            
                                            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                                            
                                            $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'";

                                            $foso = $mysqli->query($Fsol);
                                            $SolF = mysqli_fetch_row($foso);
                                            
                                            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]'";

                                            $valT = $mysqli->query($VTot);
                                            $TOT = mysqli_fetch_row($valT);
                                ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valE[0],2,'.',',');?>
                                                </td>

                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($SolF[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOT[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                                <?php                    
                                        }
                                        
                                        $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' 
                                                        AND e.grupogestion = '$grupog'";

                                        $vaem1 = $mysqli->query($vemple1);
                                        $valE1 = mysqli_fetch_row($vaem1);
                                        
                                        $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);  

                                        
                                        $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
                                                    AND e.grupogestion = '$grupog'";

                                        $foso1 = $mysqli->query($Fsol1);
                                        $SolF1 = mysqli_fetch_row($foso1);
                                        
                                        $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";

                                        $VAl12  = $mysqli->query($VTot12);
                                        $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valE1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($SolF1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($TOVAL12[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                                <?php                
                                    }/// fINALIZA EL CARGUE DE DATOS DE PENSION
                                    
                                    $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                    $valor2 = $mysqli->query($arl);
                                    
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        ARL
                                    </th>

                            <?php
                                    while($AR = mysqli_fetch_row($valor2)){

                            ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $AR[0] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $AR[1] ;?>
                                            </th>    
                                        </tr>
                                        <th>
                                            Código
                                        </th>
                                        <th>
                                            Número de Identificación
                                        </th>
                                        <th>
                                            Empleado
                                        </th>
                                        <th>
                                            Aporte Patrono
                                        </th>
                        <?php
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                        e.codigointerno,
                                                        e.tercero,
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
                                                        tr.apellidodos)) AS NOMBRE,
                                                        tr.numeroidentificacion

                                                  FROM gn_empleado e
                                                  LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                  LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                  LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                  LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                  WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog'";

                                        $resemp = $mysqli->query($sqlemp);
                                        $nresemp = mysqli_num_rows($resemp);
                                        
                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]'";
                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                        <?php                
                                        }
                                    
                                        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "WHERE n.concepto =363 AND e.grupogestion = '$grupog'";
                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                        <?php                    
                                    }/// FINALIZA EL CARGUE DE DATOS DE ARL
                                    
                                    $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c "
                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipoentidadcredito is not NULL";
                                    $valor2 = $mysqli->query($paraf);
                        ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        PARAFISCALES
                                    </th>
                        
                        <?php        
                                    while($PAR = mysqli_fetch_row($valor2)){
                                  
                        ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $PAR[2] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $PAR[1] ;?>
                                            </th>    
                                        </tr>
                                        <th>
                                            Código
                                        </th>
                                        <th>
                                            Número de Identificación
                                        </th>
                                        <th>
                                            Empleado
                                        </th>
                                        <th>
                                            Aporte Patrono
                                        </th>
                                     
                        <?php

                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                            e.codigointerno,
                                                            e.tercero,
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
                                                            tr.apellidodos)) AS NOMBRE,
                                                            tr.numeroidentificacion

                                                  FROM gn_empleado e
                                                  LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                  LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                  LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                  LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                  WHERE c.tipofondo = 2 AND c.clase = 2 AND e.grupogestion = '$grupog' AND e.id_unico != 2";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                      
                                            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                    . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' ";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                        <?php
                                        }
                                    
                                        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog'";
                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP2 = mysqli_fetch_row($vapa1);
                        ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP2[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                        <?php            
                                    }//// finaliza el cargue de parafiscales
                                    
                                    ////FINALIZA LA CONDICIION CUANDO SOLO EL GRUPO DE GESTION NO ES VACIO
                                }elseif(!empty ($periodo) && empty ($tipof)){
                                    
                                    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                    $valor = $mysqli->query($salud);

                                    $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                    $Fond = $mysqli->query($fondo);
                        
                        ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        SALUD
                                    </th>
                        
                        <?php
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    while($SAL = mysqli_fetch_row($valor)){
                                    
                        
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                        e.codigointerno,
                                                        e.tercero,
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
                                                        tr.apellidodos)) AS NOMBRE,
                                                        tr.numeroidentificacion

                                        FROM gn_empleado e
                                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                        WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog' 
                                        AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                        OR c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog' 
                                        AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);
                                        $nresemp = mysqli_num_rows($resemp);

                                        if($nresemp > 0){
                            ?>
                                         
	                                        <tr>
	                                            <th>
	                                            <tr></tr>
	                                            </th>
	                                            <th>
	                                                NIT
	                                            </th>
	                                            <th>
	                                                <?php echo $SAL[0] ;?>
	                                            </th>
	                                            <th>
	                                                Entidad
	                                            </th>
	                                            <th>
	                                                <?php echo $SAL[1] ;?>
	                                            </th>    
	                                        </tr>
	                                        <th>
	                                            Código
	                                        </th>
	                                        <th>
	                                            Número de Identificación
	                                        </th>
	                                        <th>
	                                            Empleado
	                                        </th>
	                                        <th>
	                                            Aporte Empleado
	                                        </th>
	                                        <th>
	                                            Aporte Patrono
	                                        </th>
	                                        <th>
	                                            Total
	                                        </th>
                                <?php  
	                                        while($ConE = mysqli_fetch_row($resemp)){

	                                            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
	                                            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
	                                                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
	                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
	                                                    . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
	                                                    . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

	                                            $vaem = $mysqli->query($vemple);
	                                            $valE = mysqli_fetch_row($vaem);

	                                            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
	                                            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'";

	                                            $vapa = $mysqli->query($vpat);
	                                            $valP = mysqli_fetch_row($vapa);

	                                            #calcula el total de los dos aportes y los agrupa por cada entidad de salud
	                                            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6";

	                                            $valT = $mysqli->query($VTot);
	                                            $TOT = mysqli_fetch_row($valT);
                                        
                                ?>
	                                            <tr>
	                                                <td>
	                                                    <?php echo $ConE[1];?>
	                                                </td>

	                                                <td>
	                                                    <?php echo $ConE[4];?>
	                                                </td>
	                                                <td>
	                                                    <?php echo $ConE[3];?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valE[0],2,'.',',');?>
	                                                </td>

	                                                <td align="right">
	                                                    <?php echo number_format($valP[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($TOT[0],2,'.',',');?>
	                                                </td>
	                                            </tr>      
                                <?php
											}

	                                        $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                            WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' 
	                                            AND n.periodo = '$periodo'";

	                                        $VToEM = $mysqli->query($ToEM);
	                                        $VAEMP = mysqli_fetch_row($VToEM);

	                                        $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'
	                                                AND n.periodo = '$periodo'";

	                                        $vapa1 = $mysqli->query($vpat1);
	                                        $valP1 = mysqli_fetch_row($vapa1);

	                                        $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
	                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                            WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'
	                                            AND n.periodo = '$periodo' AND c.clase != 6";

	                                        $valT1 = $mysqli->query($VTot1);
	                                        $TOT1 = mysqli_fetch_row($valT1);
                                
                                ?>
	                                        <tr>
	                                            <td>

	                                            </td>
	                                            <td>

	                                            </td>
	                                            <td>
	                                                TOTAL
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($VAEMP[0],2,'.',',');?>
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($valP1[0],2,'.',',');?>
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($TOT1[0],2,'.',',');?>
	                                            </td>
	                                        </tr>    
                                <?php
                                		}
                                    }/// Finaliza el cargue de datos de Salud
                                    
                                    $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                    $valor1 = $mysqli->query($pension);
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        PENSIÓN
                                    </th>
                        
                                <?php  
                                
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    while($PEN = mysqli_fetch_row($valor1)){
                                    
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,  
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
                                        $resemp = $mysqli->query($sqlemp);
                                        $nresemp = mysqli_num_rows($resemp); 

                                        if($nresemp > 0){
                                    
                                    ?>
                                      
	                                        <tr>
	                                            <th>
	                                            <tr></tr>
	                                            </th>
	                                            <th>
	                                                NIT
	                                            </th>
	                                            <th>
	                                                <?php echo $PEN[0] ;?>
	                                            </th>
	                                            <th>
	                                                Entidad
	                                            </th>
	                                            <th>
	                                                <?php echo $PEN[1] ;?>
	                                            </th>    
	                                        </tr>
	                                        <th>
	                                            Código
	                                        </th>
	                                        <th>
	                                            Número de Identificación
	                                        </th>
	                                        <th>
	                                            Empleado
	                                        </th>
	                                        <th>
	                                            Aporte Empleado
	                                        </th>
	                                        <th>
	                                            Aporte Patrono
	                                        </th>
	                                        <th>
	                                            Fondo Solid
	                                        </th>
	                                        <th>
	                                            Total
	                                        </th>   
                                 <?php 
	                                        while($ConE = mysqli_fetch_row($resemp)){
	                                            
	                                            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' 
	                                                    AND n.periodo = '$periodo' ";

	                                            $vaem = $mysqli->query($vemple);
	                                            $valE = mysqli_fetch_row($vaem);
	                                            
	                                            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'";

	                                            $vapa = $mysqli->query($vpat);
	                                            $valP = mysqli_fetch_row($vapa);
	                                            
	                                            $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' 
	                                                        AND n.periodo = '$periodo'";

	                                            $foso = $mysqli->query($Fsol);
	                                            $SolF = mysqli_fetch_row($foso);
	                                            
	                                            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                    WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6";

	                                            $valT = $mysqli->query($VTot);
	                                            $TOT = mysqli_fetch_row($valT);
                                ?>
	                                            <tr>
	                                                <td>
	                                                    <?php echo $ConE[1];?>
	                                                </td>

	                                                <td>
	                                                    <?php echo $ConE[4];?>
	                                                </td>
	                                                <td>
	                                                    <?php echo $ConE[3];?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valE[0],2,'.',',');?>
	                                                </td>

	                                                <td align="right">
	                                                    <?php echo number_format($valP[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($SolF[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($TOT[0],2,'.',',');?>
	                                                </td>
	                                            </tr>      
                                <?php                    
                                        	}

                                        
	                                        $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' 
	                                                        AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";

	                                        $vaem1 = $mysqli->query($vemple1);
	                                        $valE1 = mysqli_fetch_row($vaem1);
	                                        
	                                        $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'
	                                                    AND n.periodo = '$periodo'";

	                                        $vapa1 = $mysqli->query($vpat1);
	                                        $valP1 = mysqli_fetch_row($vapa1);  

	                                        
	                                        $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
	                                                    AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'";

	                                        $foso1 = $mysqli->query($Fsol1);
	                                        $SolF1 = mysqli_fetch_row($foso1);
                                        
	                                        $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                    WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'
	                                                    AND n.periodo = '$periodo' AND c.clase != 6";

	                                        $VAl12  = $mysqli->query($VTot12);
	                                        $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
	                                        <tr>
	                                            <td>

	                                            </td>
	                                            <td>

	                                            </td>
	                                            <td>
	                                                TOTAL
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($valE1[0],2,'.',',');?>
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($valP1[0],2,'.',',');?>
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($SolF1[0],2,'.',',');?>
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($TOVAL12[0],2,'.',',');?>
	                                            </td>
	                                        </tr>        
                                <?php
                                        }        
                                    }/// fINALIZA EL CARGUE DE DATOS DE PENSION
                                    
                                    $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                    $valor2 = $mysqli->query($arl);
                                    
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        ARL
                                    </th>

                            	<?php
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    
                                    while($AR = mysqli_fetch_row($valor2)){

                            
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,  
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 4   AND t.numeroidentificacion =  '$AR[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 4  AND t.numeroidentificacion =  '$AR[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";

                                        $resemp = $mysqli->query($sqlemp);
                                        $nresemp = mysqli_num_rows($resemp);

                                        if($nresemp > 0){
                                    ?>
                                       
                        
                                         	<tr>
	                                            <th>
	                                            <tr></tr>
	                                            </th>
	                                            <th>
	                                                NIT
	                                            </th>
	                                            <th>
	                                                <?php echo $AR[0] ;?>
	                                            </th>
	                                            <th>
	                                                Entidad
	                                            </th>
	                                            <th>
	                                                <?php echo $AR[1] ;?>
	                                            </th>    
	                                        </tr>
	                                        <th>
	                                            Código
	                                        </th>
	                                        <th>
	                                            Número de Identificación
	                                        </th>
	                                        <th>
	                                            Empleado
	                                        </th>
	                                        <th>
	                                            Aporte Patrono
	                                        </th>
                                <?php
                                        	while($ConE = mysqli_fetch_row($resemp)){
                                            
	                                            $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]' AND periodo = '$periodo'";
	                                            $vapa = $mysqli->query($vpat);
	                                            $valP = mysqli_fetch_row($vapa);
                        		?>
	                                            <tr>
	                                                <td>
	                                                    <?php echo $ConE[1];?>
	                                                </td>

	                                                <td>
	                                                    <?php echo $ConE[4];?>
	                                                </td>
	                                                <td>
	                                                    <?php echo $ConE[3];?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP[0],2,'.',',');?>
	                                                </td>
	                                            </tr>      
                        		<?php                
                                        	}
                                    
	                                        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                . "WHERE n.concepto =363 AND e.grupogestion = '$grupog' AND periodo = '$periodo'";
	                                        $vapa1 = $mysqli->query($vpat1);
	                                        $valP1 = mysqli_fetch_row($vapa1);          
                            	?>
	                                        <tr>
	                                            <td>

	                                            </td>
	                                            <td>

	                                            </td>
	                                            <td>
	                                                TOTAL
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($valP1[0],2,'.',',');?>
	                                            </td>
	                                        </tr>        
                        		<?php   }

                                  	}/// FINALIZA EL CARGUE DE DATOS DE ARL

                                  	$Caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
                                    $valor2 = $mysqli->query($Caja);
                                    
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        CAJA DE COMPENSACIÓN FAMILIAR
                                    </th>

                            	<?php
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    
                                    while($CC = mysqli_fetch_row($valor2)){

                            
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,  
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 6   AND t.numeroidentificacion =  '$CC[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 6  AND t.numeroidentificacion =  '$CC[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";

                                        $resemp = $mysqli->query($sqlemp);
                                        $nresemp = mysqli_num_rows($resemp);

                                        if($nresemp > 0){
                                    ?>
                                       
                        
                                         	<tr>
	                                            <th>
	                                            <tr></tr>
	                                            </th>
	                                            <th>
	                                                NIT
	                                            </th>
	                                            <th>
	                                                <?php echo $CC[0] ;?>
	                                            </th>
	                                            <th>
	                                                Entidad
	                                            </th>
	                                            <th>
	                                                <?php echo $CC[1] ;?>
	                                            </th>    
	                                        </tr>
	                                        <th>
	                                            Código
	                                        </th>
	                                        <th>
	                                            Número de Identificación
	                                        </th>
	                                        <th>
	                                            Empleado
	                                        </th>
	                                        <th>
	                                            Aporte Patrono
	                                        </th>
                                <?php
                                        	while($ConE = mysqli_fetch_row($resemp)){
                                            
	                                            $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =256 AND empleado = '$ConE[0]' AND periodo = '$periodo'";
	                                            $vapa = $mysqli->query($vpat);
	                                            $valP = mysqli_fetch_row($vapa);
                        		?>
	                                            <tr>
	                                                <td>
	                                                    <?php echo $ConE[1];?>
	                                                </td>

	                                                <td>
	                                                    <?php echo $ConE[4];?>
	                                                </td>
	                                                <td>
	                                                    <?php echo $ConE[3];?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP[0],2,'.',',');?>
	                                                </td>
	                                            </tr>      
                        		<?php                
                                        	}
                                    
	                                        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                . "WHERE n.concepto =256 AND e.grupogestion = '$grupog' AND periodo = '$periodo'";
	                                        $vapa1 = $mysqli->query($vpat1);
	                                        $valP1 = mysqli_fetch_row($vapa1);          
                            	?>
	                                        <tr>
	                                            <td>

	                                            </td>
	                                            <td>

	                                            </td>
	                                            <td>
	                                                TOTAL
	                                            </td>
	                                            <td align="right">
	                                                <?php echo number_format($valP1[0],2,'.',',');?>
	                                            </td>
	                                        </tr>        
                        		<?php   }

                                  	}/// FINALIZA EL CARGUE DE DATOS DE CAJA DE COMPENSACION FAMILIAR

                                    
                                    $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c 
                                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
                                    $valor2 = $mysqli->query($paraf);
                        ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        PARAFISCALES
                                    </th>
                        
                        <?php        
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    
                                    while($PAR = mysqli_fetch_row($valor2)){
                                  
                        ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $PAR[2] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $PAR[1] ;?>
                                            </th>    
                                        </tr>
                                        <th>
                                            Código
                                        </th>
                                        <th>
                                            Número de Identificación
                                        </th>
                                        <th>
                                            Empleado
                                        </th>
                                        <th>
                                            Aporte Patrono
                                        </th>
                                     
                        <?php

                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1 
                                                    AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                                                    AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                      
                                            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                    . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                        <?php
                                        }
                                    
                                        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";
                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP2 = mysqli_fetch_row($vapa1);
                        ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP2[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                        <?php            
                                    }//// finaliza el cargue de parafiscales
                                }elseif(empty ($periodo) && !empty ($tipof)){
                                    
                                    if($tipof == 1){
                                        
                                        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                        $valor = $mysqli->query($salud);

                                        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                        $Fond = $mysqli->query($fondo);

                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            SALUD
                                        </th>
                        
                        <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($SAL = mysqli_fetch_row($valor)){
                                    
                          
                        
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                e.codigointerno,
                                                                e.tercero,
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
                                                                tr.apellidodos)) AS NOMBRE,
                                                                tr.numeroidentificacion

                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog'";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            if($nresemp > 0){

                                    ?>
                                            
	                                            <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $SAL[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $SAL[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
	                                            <th>
	                                                Total
	                                            </th>
                                    <?php   
                                            	while($ConE = mysqli_fetch_row($resemp)){

	                                                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
	                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
	                                                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
	                                                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
	                                                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
	                                                        . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  ";

	                                                $vaem = $mysqli->query($vemple);
	                                                $valE = mysqli_fetch_row($vaem);

	                                                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
	                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' ";

	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);

	                                                #calcula el total de los dos aportes y los agrupa por cada entidad de salud
	                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                    WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND c.clase != 6 ";

	                                                $valT = $mysqli->query($VTot);
	                                                $TOT = mysqli_fetch_row($valT);
                                        
                                ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valE[0],2,'.',',');?>
	                                                    </td>

	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($TOT[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                                <?php
                                            	}

	                                            $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

	                                            $VToEM = $mysqli->query($ToEM);
	                                            $VAEMP = mysqli_fetch_row($VToEM);

	                                            $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);

	                                            $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND c.clase != 6";
	                                               
	                                            $valT1 = $mysqli->query($VTot1);
	                                            $TOT1 = mysqli_fetch_row($valT1);
                                
                                ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($VAEMP[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($TOT1[0],2,'.',',');?>
	                                                </td>
	                                            </tr>    
                                <?php
                                			}
                                        }/// Finaliza el cargue de datos de Salud
                                    }elseif($tipof == 2){
                                        
                                        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                        $valor1 = $mysqli->query($pension);
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PENSIÓN
                                        </th>   
                        
                                <?php  
                                
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($PEN = mysqli_fetch_row($valor1)){
                                    
                               
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog'";
                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp); 

                                            if($nresemp > 0){

                                ?>
                                         
	                                            <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $PEN[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $PEN[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
	                                            <th>
	                                                Fondo Solid
	                                            </th>
	                                            <th>
	                                                Total
	                                            </th> 
                                   	<?php        
	                                            while($ConE = mysqli_fetch_row($resemp)){
	                                            
	                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'"; 
	                                                        

	                                                $vaem = $mysqli->query($vemple);
	                                                $valE = mysqli_fetch_row($vaem);

	                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' ";

	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);

	                                                $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'"; 
	                                                           

	                                                $foso = $mysqli->query($Fsol);
	                                                $SolF = mysqli_fetch_row($foso);
	                                            
	                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase != 6";

	                                                $valT = $mysqli->query($VTot);
	                                                $TOT = mysqli_fetch_row($valT);
                                ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valE[0],2,'.',',');?>
	                                                    </td>

	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($SolF[0],2,'.',',');?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($TOT[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                                <?php                    
                                            	}
                                        
	                                            $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' 
	                                                            AND e.grupogestion = '$grupog' ";

	                                            $vaem1 = $mysqli->query($vemple1);
	                                            $valE1 = mysqli_fetch_row($vaem1);

	                                            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";
	                                                        

	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);  


	                                            $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
	                                                        AND e.grupogestion = '$grupog'";

	                                            $foso1 = $mysqli->query($Fsol1);
	                                            $SolF1 = mysqli_fetch_row($foso1);

	                                            $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND c.clase != 6";
	                                                       

	                                            $VAl12  = $mysqli->query($VTot12);
	                                            $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valE1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($SolF1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($TOVAL12[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                                <?php   
                                			}             
                                        }/// fINALIZA EL CARGUE DE DATOS DE PENSION
                                    }elseif($tipof == 3){
                                       
                                        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                        $valor2 = $mysqli->query($arl);
                                    
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            ARL
                                        </th>

                            <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($AR = mysqli_fetch_row($valor2)){

                            
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,  
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 4   AND t.numeroidentificacion =  '$AR[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 4  AND t.numeroidentificacion =  '$AR[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            if($nresemp > 0){
						?>
                                           
                        
                                             	<tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $AR[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $AR[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
						<?php
	                                            while($ConE = mysqli_fetch_row($resemp)){

	                                                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]' ";
	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);
                        ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                        <?php                
                                            	}
                                    
	                                            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "WHERE n.concepto =363 AND e.grupogestion = '$grupog' ";
	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                        <?php              
                        					}      
                                        }/// FINALIZA EL CARGUE DE DATOS DE ARL
                                    }elseif($tipof == 4){
                                       $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c 
                                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
                                        $valor2 = $mysqli->query($paraf);
                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PARAFISCALES
                                        </th>
                        
                        <?php        
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($PAR = mysqli_fetch_row($valor2)){
                                  
                       

                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                        e.codigointerno,
                                                                        e.tercero,
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
                                                                        tr.apellidodos)) AS NOMBRE,
                                                                        tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog'";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);
                                            
                                            if($nresemp > 0){
                        ?>
                                                <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $PAR[2] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $PAR[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>

                        <?php
                                            	while($ConE = mysqli_fetch_row($resemp)){
                                      
	                                                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
	                                                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
	                                                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
	                                                        WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' ";

	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);
                        ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                        <?php
                                            	}
                                    
	                                            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
	                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
	                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
	                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' ";
	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP2 = mysqli_fetch_row($vapa1);  
                        ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP2[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                        <?php            
                        					}
                                        }//// finaliza el cargue de parafiscales 
                                    }else{
                                    	$Caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
                                        $valor2 = $mysqli->query($Caja);
                                    
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            CAJA DE COMPENSACIÓN FAMILIAR
                                        </th>

                            <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($CC = mysqli_fetch_row($valor2)){

                            
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,  
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 6    AND t.numeroidentificacion =  '$CC[0]' AND e.grupogestion = '$grupog' 
                                                AND vr.estado=1 ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            if($nresemp > 0){
						?>
                                           
                        
                                             	<tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $CC[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $CC[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
						<?php
	                                            while($ConE = mysqli_fetch_row($resemp)){

	                                                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =256 AND empleado = '$ConE[0]' ";
	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);
                        ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                        <?php                
                                            	}
                                    
	                                            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "WHERE n.concepto =256 AND e.grupogestion = '$grupog' ";
	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                        <?php              
                        					}      
                                        }/// FINALIZA EL CARGUE DE DATOS DE CAJA DE COMPENSACION
                                    }
                            
                                }else{
                                    
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    
                                    if($tipof == 1){
                                        
                                        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                        $valor = $mysqli->query($salud);

                                        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                        $Fond = $mysqli->query($fondo);

                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            SALUD
                                        </th>
                        
                        <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($SAL = mysqli_fetch_row($valor)){
                                    
                           
                        
                                             $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion

                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'  AND e.grupogestion = '$grupog' 
                                                    AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 1 AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]'  AND e.grupogestion = '$grupog' 
                                                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                           	if($nresemp > 0){
                                            
                        ?>
                                             
                       
                                             	<tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $SAL[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $SAL[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
	                                            <th>
	                                                Total
	                                            </th>
                        <?php
	                                            while($ConE = mysqli_fetch_row($resemp)){

	                                                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
	                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
	                                                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
	                                                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
	                                                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
	                                                        . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

	                                                $vaem = $mysqli->query($vemple);
	                                                $valE = mysqli_fetch_row($vaem);

	                                                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
	                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);

	                                                #calcula el total de los dos aportes y los agrupa por cada entidad de salud
	                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                    WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6 ";

	                                                $valT = $mysqli->query($VTot);
	                                                $TOT = mysqli_fetch_row($valT);
                                        
                                ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valE[0],2,'.',',');?>
	                                                    </td>

	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($TOT[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                                <?php
                                            	}

	                                            $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'
	                                                AND n.periodo = '$periodo'";

	                                            $VToEM = $mysqli->query($ToEM);
	                                            $VAEMP = mysqli_fetch_row($VToEM);

	                                            $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'
	                                                    AND n.periodo = '$periodo'";

	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);

	                                            $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6";
	                                               
	                                            $valT1 = $mysqli->query($VTot1);
	                                            $TOT1 = mysqli_fetch_row($valT1);
	                                
                                ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($VAEMP[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($TOT1[0],2,'.',',');?>
	                                                </td>
	                                            </tr>    
                                <?php
                                			}
                                        }/// Finaliza el cargue de datos de Salud
                                    }elseif($tipof == 2){
                                        
                                        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                        $valor1 = $mysqli->query($pension);
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PENSIÓN
                                        </th>   
                        
                                <?php  
                                
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($PEN = mysqli_fetch_row($valor1)){
                                    
                                      
                        
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion = '$PEN[0]'  AND e.grupogestion = '$grupog' 
                                                    AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 2 AND c.clase = 2 AND t.numeroidentificacion = '$PEN[0]'  AND e.grupogestion = '$grupog' 
                                                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp); 

                                            if($nresemp > 0){
                             	?>
                                             
                                
	                                            <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $PEN[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $PEN[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
	                                            <th>
	                                                Fondo Solid
	                                            </th>
	                                            <th>
	                                                Total
	                                            </th>  
                                <?php 
	                                            while($ConE = mysqli_fetch_row($resemp)){
	                                            
	                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' 
	                                                        AND n.periodo = '$periodo'"; 
	                                                        

	                                                $vaem = $mysqli->query($vemple);
	                                                $valE = mysqli_fetch_row($vaem);

	                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);

	                                                $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' 
	                                                            AND n.periodo = '$periodo'"; 
	                                                           

	                                                $foso = $mysqli->query($Fsol);
	                                                $SolF = mysqli_fetch_row($foso);
	                                            
	                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6";

	                                                $valT = $mysqli->query($VTot);
	                                                $TOT = mysqli_fetch_row($valT);
                                ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valE[0],2,'.',',');?>
	                                                    </td>

	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($SolF[0],2,'.',',');?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($TOT[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                                <?php                    
                                            	}
                                        
	                                            $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' 
	                                                            AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";

	                                            $vaem1 = $mysqli->query($vemple1);
	                                            $valE1 = mysqli_fetch_row($vaem1);

	                                            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
	                                                        AND n.periodo = '$periodo'";
	                                                        

	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);  


	                                            $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
	                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
	                                                        AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";

	                                            $foso1 = $mysqli->query($Fsol1);
	                                            $SolF1 = mysqli_fetch_row($foso1);

	                                            $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
	                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
	                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
	                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
	                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
	                                                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'
	                                                        AND n.periodo = '$periodo' AND c.clase != 6";
	                                                       

	                                            $VAl12  = $mysqli->query($VTot12);
	                                            $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valE1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($SolF1[0],2,'.',',');?>
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($TOVAL12[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                                <?php                
                                        	}
                                        }/// fINALIZA EL CARGUE DE DATOS DE PENSION
                                    }elseif($tipof == 3){
                                       
                                        $arl = "SELECT DISTINCT  t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                        $valor2 = $mysqli->query($arl);
                                    
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            ARL
                                        </th>

                            <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($AR = mysqli_fetch_row($valor2)){

                            
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                        e.codigointerno,
                                                                        e.tercero,
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
                                                                        tr.apellidodos)) AS NOMBRE,
                                                                        tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 4  AND t.numeroidentificacion = '$AR[0]' AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1 
                                                    AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 4 AND t.numeroidentificacion = '$AR[0]' AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                                                    AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            if($nresemp > 0){
                            ?>
                                           
	                                            <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $AR[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $AR[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
                            <?php
	                                            while($ConE = mysqli_fetch_row($resemp)){

	                                                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]' AND periodo = '$periodo'";
	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);
                        ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                        <?php                
                                            	}
                                    
	                                            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "WHERE n.concepto =363 AND e.grupogestion = '$grupog' AND periodo = '$periodo'";
	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                        <?php 
                        					}                   
                                        }/// FINALIZA EL CARGUE DE DATOS DE ARL
                                    }elseif($tipof == 4){
                                       $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c 
                                        		LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
                                        $valor2 = $mysqli->query($paraf);
                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PARAFISCALES
                                        </th>
                        
                        <?php        
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($PAR = mysqli_fetch_row($valor2)){
                                  
                                      		$sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                e.codigointerno,
                                                                e.tercero,
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
                                                                tr.apellidodos)) AS NOMBRE,
                                                                tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog' AND vr.estado=1 
                                                AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                                                AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            if($nresemp > 0){
                        ?>
                       
	                                            <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $PAR[2] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $PAR[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
                        <?php
	                                            while($ConE = mysqli_fetch_row($resemp)){
	                                      
	                                                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
	                                                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
	                                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
	                                                        . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";

	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);
                        ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                        <?php
                                            	}
                                    
	                                            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
	                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
	                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
	                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo' ";
	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP2 = mysqli_fetch_row($vapa1);  
                        ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP2[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                        <?php  
                        					}          
                                        }
                                    }else{
                                        $Caja = "SELECT DISTINCT  t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
                                        $valor2 = $mysqli->query($Caja);
                                    
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            CAJA DE COMPENSACIÓN FAMILIAR 
                                        </th>

                            <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($CC = mysqli_fetch_row($valor2)){

                            
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                        e.codigointerno,
                                                                        e.tercero,
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
                                                                        tr.apellidodos)) AS NOMBRE,
                                                                        tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 6  AND t.numeroidentificacion = '$CC[0]' AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1 
                                                    AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 6 AND t.numeroidentificacion = '$CC[0]' AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                                                    AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            if($nresemp > 0){
                            ?>
                                           
	                                            <tr>
	                                                <th>
	                                                <tr></tr>
	                                                </th>
	                                                <th>
	                                                    NIT
	                                                </th>
	                                                <th>
	                                                    <?php echo $CC[0] ;?>
	                                                </th>
	                                                <th>
	                                                    Entidad
	                                                </th>
	                                                <th>
	                                                    <?php echo $CC[1] ;?>
	                                                </th>    
	                                            </tr>
	                                            <th>
	                                                Código
	                                            </th>
	                                            <th>
	                                                Número de Identificación
	                                            </th>
	                                            <th>
	                                                Empleado
	                                            </th>
	                                            <th>
	                                                Aporte Patrono
	                                            </th>
                            <?php
	                                            while($ConE = mysqli_fetch_row($resemp)){

	                                                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =256 AND empleado = '$ConE[0]' AND periodo = '$periodo'";
	                                                $vapa = $mysqli->query($vpat);
	                                                $valP = mysqli_fetch_row($vapa);
                        ?>
	                                                <tr>
	                                                    <td>
	                                                        <?php echo $ConE[1];?>
	                                                    </td>

	                                                    <td>
	                                                        <?php echo $ConE[4];?>
	                                                    </td>
	                                                    <td>
	                                                        <?php echo $ConE[3];?>
	                                                    </td>
	                                                    <td align="right">
	                                                        <?php echo number_format($valP[0],2,'.',',');?>
	                                                    </td>
	                                                </tr>      
                        <?php                
                                            	}
                                    
	                                            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
	                                                    . "WHERE n.concepto =256 AND e.grupogestion = '$grupog' AND periodo = '$periodo'";
	                                            $vapa1 = $mysqli->query($vpat1);
	                                            $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
	                                            <tr>
	                                                <td>

	                                                </td>
	                                                <td>

	                                                </td>
	                                                <td>
	                                                    TOTAL
	                                                </td>
	                                                <td align="right">
	                                                    <?php echo number_format($valP1[0],2,'.',',');?>
	                                                </td>
	                                            </tr>        
                        <?php 
                        					}                   
                                        }/// FINALIZA EL CARGUE DE DATOS DE CAJA DE COMPENSACION	
                                    }
                                
                                }
                            }else{
                                
                                if(!empty($periodo) && empty($tipof)){
                                    
                                    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";

                                    $valor = $mysqli->query($salud);

                                    $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                    $Fond = $mysqli->query($fondo);
                                    
                                    #consulta la fecha inicial y la fecha final del periodo
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                    ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        SALUD
                                    </th>
                        
                        <?php                
                                    while($SAL = mysqli_fetch_row($valor)){
                        
                        ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $SAL[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $SAL[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                            <th>
                                                Total
                                            </th>   
                        <?php   
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado=1 
                                                AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo =1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado = 2 
                                                AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa 
                                            #por la entidad de salud
                                            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                    . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                    . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                                            $vaem = $mysqli->query($vemple);
                                            $valE = mysqli_fetch_row($vaem);
                                            
                                            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa 
                                            #por la entidad de salud
                                            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                    . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                    . "WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                                            
                                            #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                                            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' AND c.clase !=6";

                                            $valT = $mysqli->query($VTot);
                                            $TOT = mysqli_fetch_row($valT);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valE[0],2,'.',',');?>
                                                </td>

                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOT[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                                <?php
                                        }
                                        
                                        $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'";

                                        $VToEM = $mysqli->query($ToEM);
                                        $VAEMP = mysqli_fetch_row($VToEM);
                                        
                                        $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);
                                        
                                        $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo' AND c.clase !=6";

                                        $valT1 = $mysqli->query($VTot1);
                                        $TOT1 = mysqli_fetch_row($valT1);
                                        
                                ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($VAEMP[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($TOT1[0],2,'.',',');?>
                                            </td>
                                        </tr>    
                                <?php        
                                    }/// FINALIZA EL CARGUE DE LOS DATOS DE SALUD
                                    
                                    $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";

                                    $valor1 = $mysqli->query($pension);    
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        PENSIÓN
                                    </th>
                        
                        <?php    
                                    while($PEN = mysqli_fetch_row($valor1)){
                        
                         ?>
                                        <tr>
                                            <th>
                                                <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $PEN[0] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $PEN[1] ;?>
                                            </th>    
                                        </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                            <th>
                                                Fondo Solid
                                            </th>
                                            <th>
                                                Total
                                            </th>   
                                <?php        
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                            e.codigointerno,
                                                            e.tercero,
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
                                                            tr.apellidodos)) AS NOMBRE,
                                                            tr.numeroidentificacion
                                            FROM gn_empleado e
                                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                            WHERE c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                            OR c.tipofondo =2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 
                                            AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                    . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                    . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                                            $vaem = $mysqli->query($vemple);
                                            $valE = mysqli_fetch_row($vaem);
                                            
                                            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                    . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                    . "WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                                            
                                            $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                    . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                    . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                                            $foso = $mysqli->query($Fsol);
                                            $SolF = mysqli_fetch_row($foso);
                                            
                                            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' AND c.clase !=6";

                                            $valT = $mysqli->query($VTot);
                                            $TOT = mysqli_fetch_row($valT);
                                 ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valE[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($SolF[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOT[0],2,'.',',');?>
                                                </td>
                                            </tr>      
                                <?php                          
                                        }
                                        $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'";

                                        $vaem1 = $mysqli->query($vemple1);
                                        $valE1 = mysqli_fetch_row($vaem1);
                                        
                                        $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);
                                        
                                        $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]'   AND n.periodo = '$periodo'";

                                        $foso1 = $mysqli->query($Fsol1);
                                        $SolF1 = mysqli_fetch_row($foso1);
                                        
                                        $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo' AND c.clase !=6";

                                        $VAl12  = $mysqli->query($VTot12);
                                        $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valE1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($SolF1[0],2,'.',',');?>
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($TOVAL12[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                                <?php  
                                    }//// FINALIZA EL CARGUE DE LOS DATOS DE PENSIÓN
                                    
                                    $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";

                                    $valor2 = $mysqli->query($arl);
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        ARL
                                    </th>
                        
                        <?php        
                                    while($AR = mysqli_fetch_row($valor2)){
                        
                        ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $AR[0] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $AR[1] ;?>
                                            </th>    
                                        </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                     
                        <?php                
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "WHERE  n.concepto =363   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                            </tr>      
                        <?php                       
                                            

                                        }
                                        
                                        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico WHERE n.concepto =363 AND n.periodo = '$periodo'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);
                        ?>
                                        <tr>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                        <?php                       
                                    }/// FINALIZA EL CARGUE DE DATOS DE ARL
                                    
                                    $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                            LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";

                                    $valor2 = $mysqli->query($caja);
                                ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        CAJA DE COMPENSACION FAMILIAR
                                    </th>
                        
                        <?php        
                                    while($CA = mysqli_fetch_row($valor2)){
                        
                        ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $CA[0] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $CA[1] ;?>
                                            </th>    
                                        </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                     
                        <?php                
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "WHERE  n.concepto =256   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                            </tr>      
                        <?php                       
                                            

                                        }
                                        
                                        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico WHERE n.concepto =256 AND n.periodo = '$periodo'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);
                        ?>
                                        <tr>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                        <?php                       
                                    }/// FINALIZA EL CARGUE DE DATOS DE CAJA DE COMPENSACION

                                    $paraf = "SELECT t.id_unico, t.razonsocial,t.numeroidentificacion 
                                                FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                                                WHERE c.tipofondo = 5  AND c.tipoentidadcredito is not null";

                                    $valor2 = $mysqli->query($paraf);
                        ?>
                                    <th>
                                        Tipo Fondo
                                    </th>
                                    <th>
                                        PARAFISCALES
                                    </th>
                        
                        <?php              
                                    while($PAR = mysqli_fetch_row($valor2)){
                                      
                        ?>
                                        <tr>
                                            <th>
                                            <tr></tr>
                                            </th>
                                            <th>
                                                NIT
                                            </th>
                                            <th>
                                                <?php echo $PAR[2] ;?>
                                            </th>
                                            <th>
                                                Entidad
                                            </th>
                                            <th>
                                                <?php echo $PAR[1] ;?>
                                            </th>    
                                        </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                     
                        <?php                   
                                        $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                        $resemp = $mysqli->query($sqlemp);

                                        while($ConE = mysqli_fetch_row($resemp)){
                                            
                                            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                    . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                                            $vapa = $mysqli->query($vpat);
                                            $valP = mysqli_fetch_row($vapa);
                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $ConE[1];?>
                                                </td>

                                                <td>
                                                    <?php echo $ConE[4];?>
                                                </td>
                                                <td>
                                                    <?php echo $ConE[3];?>
                                                </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                            </tr>      
                        <?php                       
                                            
                                        }
                                        
                                        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo'";

                                        $vapa1 = $mysqli->query($vpat1);
                                        $valP1 = mysqli_fetch_row($vapa1);
                        ?>
                                        <tr>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                TOTAL
                                            </td>
                                            <td align="right">
                                                <?php echo number_format($valP1[0],2,'.',',');?>
                                            </td>
                                        </tr>        
                        <?php            
                                    }/// FINALIZA EL CARGUE DE DATOS DE PARAFISCALES
                                    
                                    /// FINALIZA LA CONDICION CUANDO SOLO EL PERIODO NO ES VACIO
                                }elseif(empty ($periodo) && !empty ($tipof)){
                                    
                                    if($tipof == 1){
                                        
                                        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                        $valor = $mysqli->query($salud);

                                        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                        $Fond = $mysqli->query($fondo);

                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            SALUD
                                        </th>
                        
                        <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($SAL = mysqli_fetch_row($valor)){
                                    
                        ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $SAL[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $SAL[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                            <th>
                                                Total
                                            </th>   
                        <?php    
                        
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                e.codigointerno,
                                                                e.tercero,
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
                                                                tr.apellidodos)) AS NOMBRE,
                                                                tr.numeroidentificacion

                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' ";

                                            $resemp = $mysqli->query($sqlemp);

                                            while($ConE = mysqli_fetch_row($resemp)){

                                                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                        . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  ";

                                                $vaem = $mysqli->query($vemple);
                                                $valE = mysqli_fetch_row($vaem);

                                                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' ";

                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);

                                                #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'";

                                                $valT = $mysqli->query($VTot);
                                                $TOT = mysqli_fetch_row($valT);
                                        
                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valE[0],2,'.',',');?>
                                                    </td>

                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($TOT[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                                <?php
                                            }

                                            $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' ";

                                            $VToEM = $mysqli->query($ToEM);
                                            $VAEMP = mysqli_fetch_row($VToEM);

                                            $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' ";

                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP1 = mysqli_fetch_row($vapa1);

                                            $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' ";
                                               
                                            $valT1 = $mysqli->query($VTot1);
                                            $TOT1 = mysqli_fetch_row($valT1);
                                
                                ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($VAEMP[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOT1[0],2,'.',',');?>
                                                </td>
                                            </tr>    
                                <?php
                                        }/// Finaliza el cargue de datos de Salud
                                    }elseif($tipof == 2){
                                        
                                        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                        $valor1 = $mysqli->query($pension);
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PENSIÓN
                                        </th>   
                        
                                <?php  
                                
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($PEN = mysqli_fetch_row($valor1)){
                                    
                                ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $PEN[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $PEN[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                            <th>
                                                Fondo Solid
                                            </th>
                                            <th>
                                                Total
                                            </th>   
                                <?php        
                        
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' ";
                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp); 

                                            while($ConE = mysqli_fetch_row($resemp)){
                                            
                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'"; 
                                                        

                                                $vaem = $mysqli->query($vemple);
                                                $valE = mysqli_fetch_row($vaem);

                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' ";

                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);

                                                $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'"; 
                                                           

                                                $foso = $mysqli->query($Fsol);
                                                $SolF = mysqli_fetch_row($foso);
                                            
                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' ";

                                                $valT = $mysqli->query($VTot);
                                                $TOT = mysqli_fetch_row($valT);
                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valE[0],2,'.',',');?>
                                                    </td>

                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($SolF[0],2,'.',',');?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($TOT[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                                <?php                    
                                            }
                                        
                                            $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' 
                                                             ";

                                            $vaem1 = $mysqli->query($vemple1);
                                            $valE1 = mysqli_fetch_row($vaem1);

                                            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' ";
                                                        

                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP1 = mysqli_fetch_row($vapa1);  


                                            $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
                                                        ";

                                            $foso1 = $mysqli->query($Fsol1);
                                            $SolF1 = mysqli_fetch_row($foso1);

                                            $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' ";
                                                       

                                            $VAl12  = $mysqli->query($VTot12);
                                            $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valE1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($SolF1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOVAL12[0],2,'.',',');?>
                                                </td>
                                            </tr>        
                                <?php                
                                        }/// fINALIZA EL CARGUE DE DATOS DE PENSION
                                    }elseif($tipof == 3){
                                       
                                        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                        $valor2 = $mysqli->query($arl);
                                    
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            ARL
                                        </th>

                            <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($AR = mysqli_fetch_row($valor2)){

                            ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $AR[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $AR[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                        <?php
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                        e.codigointerno,
                                                                        e.tercero,
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
                                                                        tr.apellidodos)) AS NOMBRE
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            while($ConE = mysqli_fetch_row($resemp)){

                                                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]' ";
                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);
                        ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                        <?php                
                                            }
                                    
                                            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE n.concepto =363  ";
                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP1[0],2,'.',',');?>
                                                </td>
                                            </tr>        
                        <?php                    
                                        }/// FINALIZA EL CARGUE DE DATOS DE ARL
                                    }else{
                                       $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c "
                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipoentidadcredito is not NULL";
                                        $valor2 = $mysqli->query($paraf);
                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PARAFISCALES
                                        </th>
                        
                        <?php        
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($PAR = mysqli_fetch_row($valor2)){
                                  
                        ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $PAR[2] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $PAR[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                     
                        <?php

                                            $$sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                        e.codigointerno,
                                                                        e.tercero,
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
                                                                        tr.apellidodos)) AS NOMBRE,
                                                                        tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 ";

                                            $resemp = $mysqli->query($sqlemp);

                                            while($ConE = mysqli_fetch_row($resemp)){
                                      
                                                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                        . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof'  ";

                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);
                        ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                        <?php
                                            }
                                    
                                            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE t.id_unico = '$PAR[0]'  AND c.tipofondo = '$tipof'  ";
                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP2 = mysqli_fetch_row($vapa1);  
                        ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP2[0],2,'.',',');?>
                                                </td>
                                            </tr>        
                        <?php            
                                        }//// finaliza el cargue de parafiscales 
                                    }
                                }else{
                                    $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                    $retEmp = $mysqli->query($retiro);
                                    $retE = mysqli_fetch_row($retEmp);
                                    
                                    if($tipof == 1){
                                        
                                        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
                                        $valor = $mysqli->query($salud);

                                        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
                                        $Fond = $mysqli->query($fondo);

                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            SALUD
                                        </th>
                        
                        <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($SAL = mysqli_fetch_row($valor)){
                                    
                        ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $SAL[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $SAL[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                            <th>
                                                Total
                                            </th>   
                        <?php    
                        
                                             $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion

                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'   
                                                    AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 1 AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]'  
                                                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);

                                            while($ConE = mysqli_fetch_row($resemp)){

                                                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                                                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                                                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                                                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                                                        . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

                                                $vaem = $mysqli->query($vemple);
                                                $valE = mysqli_fetch_row($vaem);

                                                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);

                                                #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'";

                                                $valT = $mysqli->query($VTot);
                                                $TOT = mysqli_fetch_row($valT);
                                        
                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valE[0],2,'.',',');?>
                                                    </td>

                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($TOT[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                                <?php
                                            }

                                            $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' 
                                                AND n.periodo = '$periodo'";

                                            $VToEM = $mysqli->query($ToEM);
                                            $VAEMP = mysqli_fetch_row($VToEM);

                                            $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' 
                                                    AND n.periodo = '$periodo'";

                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP1 = mysqli_fetch_row($vapa1);

                                            $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'";
                                               
                                            $valT1 = $mysqli->query($VTot1);
                                            $TOT1 = mysqli_fetch_row($valT1);
                                
                                ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($VAEMP[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOT1[0],2,'.',',');?>
                                                </td>
                                            </tr>    
                                <?php
                                        }/// Finaliza el cargue de datos de Salud
                                    }elseif($tipof == 2){
                                        
                                        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                                            . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
                                        $valor1 = $mysqli->query($pension);
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PENSIÓN
                                        </th>   
                        
                                <?php  
                                
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);
                                        while($PEN = mysqli_fetch_row($valor1)){
                                    
                                ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $PEN[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $PEN[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                            <th>
                                                Fondo Solid
                                            </th>
                                            <th>
                                                Total
                                            </th>   
                                <?php        
                        
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                    e.codigointerno,
                                                                    e.tercero,
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
                                                                    tr.apellidodos)) AS NOMBRE,
                                                                    tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion = '$PEN[0]'  
                                                    AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 2 AND c.clase = 2 AND t.numeroidentificacion = '$PEN[0]' 
                                                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp); 

                                            while($ConE = mysqli_fetch_row($resemp)){
                                            
                                                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' 
                                                        AND n.periodo = '$periodo'"; 
                                                        

                                                $vaem = $mysqli->query($vemple);
                                                $valE = mysqli_fetch_row($vaem);

                                                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);

                                                $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' 
                                                            AND n.periodo = '$periodo'"; 
                                                           

                                                $foso = $mysqli->query($Fsol);
                                                $SolF = mysqli_fetch_row($foso);
                                            
                                                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' ";

                                                $valT = $mysqli->query($VTot);
                                                $TOT = mysqli_fetch_row($valT);
                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valE[0],2,'.',',');?>
                                                    </td>

                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($SolF[0],2,'.',',');?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($TOT[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                                <?php                    
                                            }
                                        
                                            $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' 
                                                             AND n.periodo = '$periodo' ";

                                            $vaem1 = $mysqli->query($vemple1);
                                            $valE1 = mysqli_fetch_row($vaem1);

                                            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  
                                                        AND n.periodo = '$periodo'";
                                                        

                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP1 = mysqli_fetch_row($vapa1);  


                                            $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
                                                         AND n.periodo = '$periodo'";

                                            $foso1 = $mysqli->query($Fsol1);
                                            $SolF1 = mysqli_fetch_row($foso1);

                                            $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
                                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' 
                                                        AND n.periodo = '$periodo'";
                                                       

                                            $VAl12  = $mysqli->query($VTot12);
                                            $TOVAL12 = mysqli_fetch_row($VAl12);
                                        
                                ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valE1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($SolF1[0],2,'.',',');?>
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($TOVAL12[0],2,'.',',');?>
                                                </td>
                                            </tr>        
                                <?php                
                                        }/// fINALIZA EL CARGUE DE DATOS DE PENSION
                                    }elseif($tipof == 3){
                                       
                                        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                                           LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                                        $valor2 = $mysqli->query($arl);
                                    
                                ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            ARL
                                        </th>

                            <?php
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($AR = mysqli_fetch_row($valor2)){

                            ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $AR[0] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $AR[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                        <?php
                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                        e.codigointerno,
                                                                        e.tercero,
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
                                                                        tr.apellidodos)) AS NOMBRE,
                                                                        tr.numeroidentificacion
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2   AND vr.estado=1 
                                                    AND vr.vinculacionretiro IS NULL
                                                    OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2   AND vr.estado = 2 
                                                    AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);
                                            $nresemp = mysqli_num_rows($resemp);

                                            while($ConE = mysqli_fetch_row($resemp)){

                                                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]' AND periodo = '$periodo'";
                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);
                        ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                        <?php                
                                            }
                                    
                                            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE n.concepto =363  AND periodo = '$periodo'";
                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP1 = mysqli_fetch_row($vapa1);          
                            ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP1[0],2,'.',',');?>
                                                </td>
                                            </tr>        
                        <?php                    
                                        }/// FINALIZA EL CARGUE DE DATOS DE ARL
                                    }else{
                                       $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c "
                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipoentidadcredito is not NULL";
                                        $valor2 = $mysqli->query($paraf);
                        ?>
                                        <th>
                                            Tipo Fondo
                                        </th>
                                        <th>
                                            PARAFISCALES
                                        </th>
                        
                        <?php        
                                        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                                        $retEmp = $mysqli->query($retiro);
                                        $retE = mysqli_fetch_row($retEmp);

                                        while($PAR = mysqli_fetch_row($valor2)){
                                  
                        ?>
                                            <tr>
                                                <th>
                                                <tr></tr>
                                                </th>
                                                <th>
                                                    NIT
                                                </th>
                                                <th>
                                                    <?php echo $PAR[2] ;?>
                                                </th>
                                                <th>
                                                    Entidad
                                                </th>
                                                <th>
                                                    <?php echo $PAR[1] ;?>
                                                </th>    
                                            </tr>
                                            <th>
                                                Código
                                            </th>
                                            <th>
                                                Número de Identificación
                                            </th>
                                            <th>
                                                Empleado
                                            </th>
                                            <th>
                                                Aporte Patrono
                                            </th>
                                     
                        <?php

                                            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                                e.codigointerno,
                                                                e.tercero,
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
                                                                tr.apellidodos)) AS NOMBRE,
                                                                tr.numeroidentificacion
                                                FROM gn_empleado e
                                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2  AND vr.estado=1 
                                                AND vr.vinculacionretiro IS NULL
                                                OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2   AND vr.estado = 2 
                                                AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                                            $resemp = $mysqli->query($sqlemp);

                                            while($ConE = mysqli_fetch_row($resemp)){
                                      
                                                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                        . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' ";

                                                $vapa = $mysqli->query($vpat);
                                                $valP = mysqli_fetch_row($vapa);
                        ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $ConE[1];?>
                                                    </td>

                                                    <td>
                                                        <?php echo $ConE[4];?>
                                                    </td>
                                                    <td>
                                                        <?php echo $ConE[3];?>
                                                    </td>
                                                    <td align="right">
                                                        <?php echo number_format($valP[0],2,'.',',');?>
                                                    </td>
                                                </tr>      
                        <?php
                                            }
                                    
                                            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                                                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                                                    . "WHERE t.id_unico = '$PAR[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' ";
                                            $vapa1 = $mysqli->query($vpat1);
                                            $valP2 = mysqli_fetch_row($vapa1);  
                        ?>
                                            <tr>
                                                <td>

                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    TOTAL
                                                </td>
                                                <td align="right">
                                                    <?php echo number_format($valP2[0],2,'.',',');?>
                                                </td>
                                            </tr>        
                        <?php            
                                        }//// finaliza el cargue de parafiscales
                                    }
                                
                                
                                }
                            }    
                                
                        ?>
                    </tr>    
                        
                        
    </tbody>  
           
</table>
</body>
</html>