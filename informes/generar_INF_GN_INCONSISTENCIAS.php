<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Inconsistencia_Homologaciones.xls");
require_once("../Conexion/conexion.php");
session_start();
$anno = $_SESSION['anno'];
ini_set('max_execution_time', 0);
$tipoI        = $mysqli->real_escape_string(''.$_POST["tipoInf"].'');
$informe      = $mysqli->real_escape_string(''.$_POST["nombre"].'');

#Encabezado nombre Informe##;
$ni = "SELECT UPPER(nombre) FROM gn_informe WHERE id = $informe";
$ni = $mysqli->query($ni);
$ni = mysqli_fetch_row($ni);
$nomI = $ni[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Inconsistencias</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr><td colspan="3"><strong><center><?php echo ($nomI);?></center></strong></td></tr>
    
<?php 


#********************CODIGO CGR***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'codigo_cgr'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong>CÓDIGO CGR</strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, f.id_unico, f.nombre), "
                                        . "r.parametrizacionanno   FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1] ";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************EJECUCION INGRESOS***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'ejecucion_ingresos'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong>EJECUCION INGRESOS</strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}


#********************EJECUCION PRESUPUESTAL***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'ejecucion_presupuestal'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> EJECUCIÓN PRESUPUESTAL </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno   FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}


#********************GASTOS FUNCIONAMIENTO***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'gastos_funcionamiento'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){ 
    ?>
    <tr><td colspan="3"><center><strong> GASTOS FUNCIONAMIENTO </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';                            
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}



#********************GASTOS INVERSION***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'gastos_inversion'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> GASTOS INVERSION </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}


#********************PROGRAMACION GASTOS***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'programacion_gastos'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> PROGRAMACIÓN GASTOS </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND  parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), "
                                        . "r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]== $anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************PROGRAMACION INGRESOS***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'programacion_ingresos'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> PROGRAMACION INGRESOS </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************REPORTE CUENTAS POR PAGAR***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'reporte_cuentas_por_pagar'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> REPORTE CUENTAS POR PAGAR </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND  parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************REPORTE INFORMACION***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'reporte_informacion'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> REPORTE INFORMACIÓN </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE  parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}


#********************REPORTE RESERVAS PPTAL***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'reporte_reservas_pptal'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> REPORTE RESERVAS PPTAL </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************REPORTE TESORERIA***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'reporte_tesoreria'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> REPORTE TESORERIA </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************SALDOS DISPONIBLES***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'saldos_disponibles'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> SALDOS DISPONIBLES </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}

#********************SERVICIO DEUDA***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'servicio_deuda'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> SERVICIO DEUDA </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, f.id_unico, "
                                        . "f.nombre), r.parametrizacionanno  FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}


#********************NIIF***********************************#
$tablacgr = "SELECT id, tabla_origen FROM gn_tabla_homologable WHERE informe =$informe AND tabla_destino = 'niif'";
$tablacgr = $mysqli->query($tablacgr);
if(mysqli_num_rows($tablacgr)>0){
    ?>
    <tr><td colspan="3"><center><strong> NIIF </strong></center></td></tr>
    <tr>
        <td><strong>CÓDIGO ORIGEN</strong></td>
        <td><strong>CÓDIGO HOMOLOGADO</strong></td>
        <td><strong>CÓDIGO A HOMOLOGAR</strong></td>
    </tr>
    <?php 
    while ($rowtabla = mysqli_fetch_row($tablacgr)) {
        $th = $rowtabla[0];
        #******Buscar códigos CGR que tienen hijos*****#
        $cgrh = "SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
        $cuentas = $mysqli->query($cgrh);
        if(mysqli_num_rows($cuentas)>0){
            while ($row = mysqli_fetch_row($cuentas)) {
                #ASIGNAR EL ID A UNA VARIABLE
                $codigo = str_replace(' ', '', $row[0]);
                #Agregar un punto y buscar si el código tiene hijos
                $codigob = $codigo.'.';
                $hcod = "SELECT * FROM codigo_cgr WHERE id_unico LIKE '$codigob%' AND parametrizacionanno = $anno ORDER BY id_unico DESC";
                $hcod = $mysqli->query($hcod);
                if(mysqli_num_rows($hcod)>0){
                    $idqi = mysqli_fetch_row($hcod);
                    #***Buscar si esta configurado el código**#
                    $sh ="SELECT * FROM gn_homologaciones WHERE id_destino ='$codigo' AND origen = '$th'";
                    $sh = $mysqli->query($sh);
                    if(mysqli_num_rows($sh)>0){
                        while ($row1 = mysqli_fetch_row($sh)) {
                            #***Buscar Código Origen***#
                            if($rowtabla[1]=='gf_rubro_pptal'){
                                $sl = "SELECT CONCAT_WS(' ',r.codi_presupuesto, r.nombre, "
                                        . "f.id_unico, f.nombre), r.parametrizacionanno FROM "
                                        . "gf_rubro_fuente rf "
                                        . "LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "WHERE rf.id_unico = $row1[1]";
                            } elseif($rowtabla[1]=='gf_cuenta'){
                                $sl = "SELECT CONCAT_WS(' ',codi_cuenta, nombre), parametrizacionanno FROM gf_cuenta "
                                        . "WHERE id_unico = $row1[1]";
                            }
                            $sl = $mysqli->query($sl);
                            $sl = mysqli_fetch_row($sl);
                            if($sl[1]==$anno){
                                echo '<tr>';
                                echo '<td>'.$sl[0].'</td>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$idqi[0].'</td>';
                                echo '</tr>';
                            }
                        }
                        
                    }
                }
            }
        } 
    }
}


?>
</table>
</body>
</html>
    