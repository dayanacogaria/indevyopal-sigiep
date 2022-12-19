<?php 
#######################################################################################
#                           ARCHIVO CONTROLADOR SALDOS INICIALES
#29/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once('../Conexion/conexion.php');
session_start();
$action     = $_POST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
switch ($action){
    ############ELIMINAR DETALLE SALDOS INICIALES#############
    case (1):
        $id         =$_POST['id'];
        $del ="DELETE FROM gf_detalle_comprobante WHERE id_unico = $id";
        $del = $mysqli->query($del);
        if($del==true){
            $result=1;
        } else {
            $result=2;
        }
        echo json_decode($result);
    break;
    ############CARGAR CUENTA MODIFICAR DETALLE#############
    case (2):
        $id         =$_POST['id'];
         $del ="SELECT dc.cuenta, c.codi_cuenta, c.nombre  "
                . "FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                . "WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        echo '<option value ='.$cta[0].'>'.$cta[1].' - '.ucwords(mb_strtolower($cta[2])).'</option>' ;
        $c ="SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta WHERE id_unico !=$cta[0] AND parametrizacionanno = $anno ORDER BY codi_cuenta ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            echo '<option value ='.$row[0].'>'.$row[1].' - '.ucwords(mb_strtolower($row[2])).'</option>' ;
        }
    break;
    ############CARGAR TERCERO MODIFICAR DETALLE#############
    case (3):
        $id         =$_POST['id'];
         $del ="SELECT dc.tercero, IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_tercero tr ON dc.tercero = tr.id_unico 
                  WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        if(empty($cta[3])){ 
            $ni = $cta[2];
        } else {
            $ni = $cta[2].'-'.$cta[3];
        }
        echo '<option value ='.$cta[0].'>'.ucwords(mb_strtolower($cta[1])).' - '.$ni.'</option>' ;
        $c ="SELECT tr.id_unico, IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
                FROM gf_tercero tr WHERE tr.id_unico !=$cta[0] AND tr.compania = $compania 
                ORDER BY NOMBRE ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            if(empty($row[3])){ 
                $ni = $row[2];
            } else {
                $ni = $row[2].'-'.$row[3];
            }
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).' - '.$ni.'</option>' ;
        }
    break;
    ############CARGAR CENTRO COSTO MODIFICAR DETALLE#############
    case (4):
        $id         =$_POST['id'];
         $del ="SELECT dc.centrocosto, c.nombre  "
                . "FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_centro_costo c ON dc.centrocosto = c.id_unico "
                . "WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        echo '<option value ='.$cta[0].'>'.ucwords(mb_strtolower($cta[1])).'</option>' ;
        $c ="SELECT id_unico, nombre FROM gf_centro_costo WHERE id_unico !=$cta[0] AND parametrizacionanno = $anno ORDER BY nombre ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).'</option>' ;
        }
    break;
    ############CARGAR PROYECTO MODIFICAR DETALLE#############
    case (5):
        $id         =$_POST['id'];
         $del ="SELECT dc.proyecto, c.nombre  "
                . "FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_proyecto c ON dc.proyecto = c.id_unico "
                . "WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        echo '<option value ='.$cta[0].'>'.ucwords(mb_strtolower($cta[1])).'</option>' ;
        $c ="SELECT id_unico, nombre FROM gf_proyecto WHERE id_unico !=$cta[0] ORDER BY nombre ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).'</option>' ;
        }
    break;
    ############CARGAR VALOR MODIFICAR DETALLE#############
    case (6):
        $id         =$_POST['id'];
         $del ="SELECT dc.valor, c.naturaleza FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        $valor = $cta[0];
        if($cta[1]==1){
            if($valor>0){
               $res = $valor;
            } else {
                $res = $valor;
            }
        }else {
            if($valor>0){
               $res = $valor*-1;
            } else {
                $res = $valor*-1;
            }
        }
        echo $res;
    break;
    ############AUXILIAR DE TERCERO MODIFICAR DETALLE#############
    case (7):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliartercero 
                from gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############CENTRO COSTO MODIFICAR DETALLE#############
    case (8):
        $id         =$_POST['id'];
        $sqli = "select distinct c.centrocosto 
                from gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############PROYECTO MODIFICAR DETALLE#############
    case (9):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliarproyecto 
                from gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############AUXILIAR DE TERCERO MODIFICAR DETALLE#############
    case (10):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliartercero 
                from  gf_cuenta c 
                WHERE c.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############CENTRO COSTO MODIFICAR DETALLE#############
    case (11):
        $id         =$_POST['id'];
        $sqli = "select distinct c.centrocosto 
                from  gf_cuenta c 
                WHERE c.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############PROYECTO MODIFICAR DETALLE#############
    case (12):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliarproyecto 
                from gf_cuenta c 
                WHERE c.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ###########################MODIFICAR DETALLE############
    case (13):
        $id=$_POST['id'];
        $cuenta=$_POST['cuenta'];
        $tercero=$_POST['tercero'];
        $centroC=$_POST['centrocosto'];
        $protec=$_POST['proyecto'];
        $debito=$_POST['debito'];
        $credito=$_POST['credito'];
        $valor = 0;
        $sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
        $rs = $mysqli->query($sql);
        $nat = mysqli_fetch_row($rs);
        $natural = $nat[0];
        #naturaleza 1 Debito, 2 credito
        if (empty($_POST['debito']) || $_POST['debito']=='0') {
            if ($_POST['credito'] != '""' || $_POST['credito'] != '0') {
                if ($natural == 1) {
                    $valor =$mysqli->real_escape_string($_POST['credito']*-1); 

                }else{

                    $valor =$mysqli->real_escape_string($_POST['credito']); 

                }

            }
        }
        if (empty($_POST['credito']) || $_POST['credito']=='0') {
            if($_POST['debito'] != '""' || $_POST['credito'] != '0'){
                if ($natural==2) {
                    $valor =  $mysqli->real_escape_string($_POST['debito']*-1);           
                }else{
                   $valor = $mysqli->real_escape_string($_POST['debito']);
                }        
            }
        }
        $sql = "UPDATE gf_detalle_comprobante SET valor=$valor,cuenta=$cuenta,tercero=$tercero,proyecto=$protec,"
                 . "centrocosto=$centroC WHERE id_unico=$id";
        $rs = $mysqli->query($sql);
        echo json_encode($rs); 
    break;

}

