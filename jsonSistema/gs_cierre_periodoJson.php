<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
session_start();
$anno = $_SESSION['anno'];
$case = $_REQUEST['action'];
$result=0;
$num=0;
$id_usuario = $_SESSION['id_usuario'];
switch ($case) {
    case 1:
        $annio  = $_POST['param'];
        $mes    = $_POST['mes'];
        $estado = $_POST['estado'];
        $fecha = date('Y/m/d');
        $u=$_SESSION['usuario'];
        $us = "SELECT id_unico FROM gs_usuario WHERE usuario = '$u'";
        $us=$mysqli->query($us);
        $us = mysqli_fetch_row($us);
        $usuario =$us[0];
        //BUSCAR CIERRE
        $bc = "SELECT * FROM gs_cierre_periodo WHERE anno = $annio AND mes =$mes";
        $bc = $mysqli->query($bc);
        if(mysqli_num_rows($bc)>0){
            $result=2;
            $num=1;
        } else {
            $insert = "INSERT INTO gs_cierre_periodo (anno, mes, estado, fecha_cierre, usuario, parametrizacionanno) "
                    . "VALUES($annio, $mes, $estado, '$fecha',$usuario, $anno )";
            $insert = $mysqli->query($insert);
            if($insert==true || $insert==1){
                $result=1;
            }else {
                $result=2;
            }
        }
        
    break;
    #MODIFICAR
    case 2:
        $id     = $_POST['id'];
        $annio  = $_POST['param'];
        $mes    = $_POST['mes'];
        $estado = $_POST['estado'];
        $fecha  = date('Y/m/d');
        $u=$_SESSION['usuario'];
        $us = "SELECT id_unico FROM gs_usuario WHERE usuario = '$u'";
        $us=$mysqli->query($us);
        $us = mysqli_fetch_row($us);
        $usuario =$us[0];
        //BUSCAR CIERRE
        $bc = "SELECT * FROM gs_cierre_periodo WHERE anno = $annio AND mes =$mes AND id_unico !='$id'";
        $bc = $mysqli->query($bc);
        if(mysqli_num_rows($bc)>0){
            $result=2;
            $num=1;
        } else {
             $insert = "UPDATE gs_cierre_periodo SET anno=$annio, mes=$mes, "
                    . "estado=$estado, fecha_cierre='$fecha', usuario=$usuario  "
                    . "WHERE id_unico = $id";
            $insert = $mysqli->query($insert);
            if($insert==true || $insert==1){
                $result=1;
            }else {
                $result=2;
            }
        }
        
    break;
    #* Cierre Consolidado
    case 20:
        $anno     = anno($_SESSION['anno']);
        $mesI     = $_REQUEST['mesI'];
        $mesF     = $_REQUEST['mesF'];
        $terceroI = $_REQUEST['terceroI'];
        $terceroF = $_REQUEST['terceroF'];
        $rm       = $con->Listar("SELECT DISTINCT numero FROM gf_mes WHERE numero BETWEEN '$mesI' and '$mesF'");
        $cn       = $con->Listar("SELECT DISTINCT c.compania, t.numeroidentificacion 
            FROM gf_consolidacion c 
            LEFT JOIN gf_tercero t ON c.compania = t.id_unico  
            WHERE c.consolidado = 1 
            AND c.compania BETWEEN $terceroI AND $terceroF");
        $g =0;
        for ($i=0; $i <count($cn) ; $i++) { 
            $id_com = $cn[$i][1];
            #* Buscar parametrizacion 
            $pc = $con->Listar("SELECT pa.id_unico 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE t.numeroidentificacion = $id_com AND pa.anno = '$anno'");
            if(!empty($pc[0][0])>0){
                for ($m=0; $m <count($rm) ; $m++) { 
                    $nm     = $rm[$m][0];
                    $id_p   = $pc[0][0];
                    #** Buscar Id Mes 
                    $idm = $con->Listar("SELECT id_unico FROM gf_mes 
                        WHERE numero ='$nm' AND parametrizacionanno =$id_p ");
                    $id_mes = $idm[0][0];
                    #** Buscr si tieen cierre
                    $bc = $con->Listar("SELECT * FROM gs_cierre_periodo WHERE mes = $id_mes AND anno = $id_p");
                    if(!empty($bc[0][0])){
                        $sql_cons ="UPDATE  `gs_cierre_periodo` 
                        SET `estado` =:estado
                        WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                            array(":estado",2),
                            array(":id_unico",$bc[0][0]),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    } else {
                        $sql_cons ="INSERT INTO `gs_cierre_periodo` 
                            ( `anno`, `mes`, 
                            `estado`, `fecha_cierre`, 
                            `usuario`, `parametrizacionanno`) 
                        VALUES (:anno, :mes, 
                            :estado, :fecha_cierre, 
                            :usuario, :parametrizacionanno)";
                        $sql_dato = array(
                            array(":anno",$id_p),
                            array(":mes",$id_mes),
                            array(":estado",2),
                            array(":fecha_cierre",date('Y-m-d')),
                            array(":usuario",$id_usuario),
                            array(":parametrizacionanno",$id_p),
                            
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        //var_dump($resp);
                    }
                    if(empty($resp)){
                        $g +=1;
                    }
                }
            }
        }
        echo $g;
    break;
    #* Apertura
    case 21:
        $mesI     = $_REQUEST['mesI'];
        $mesF     = $_REQUEST['mesF'];
        $terceroI = $_REQUEST['terceroI'];
        $terceroF = $_REQUEST['terceroF'];
        $anno     = anno($_SESSION['anno']);
        $rm       = $con->Listar("SELECT DISTINCT numero FROM gf_mes WHERE numero BETWEEN '$mesI' and '$mesF'");
        $cn       = $con->Listar("SELECT DISTINCT c.compania, t.numeroidentificacion 
            FROM gf_consolidacion c 
            LEFT JOIN gf_tercero t ON c.compania = t.id_unico  
            WHERE c.consolidado = 1 
            AND c.compania BETWEEN $terceroI AND $terceroF");
        $g =0;
        for ($i=0; $i <count($cn) ; $i++) { 
            $id_com = $cn[$i][1];
            #* Buscar parametrizacion 
            $pc = $con->Listar("SELECT pa.id_unico 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE t.numeroidentificacion = $id_com AND pa.anno = '$anno'");
            if(!empty($pc[0][0])>0){
                for ($m=0; $m <count($rm) ; $m++) { 
                    $nm     = $rm[$m][0];
                    $id_p   = $pc[0][0];
                    #** Buscar Id Mes 
                    $idm = $con->Listar("SELECT id_unico FROM gf_mes 
                        WHERE numero ='$nm' AND parametrizacionanno =$id_p ");
                    $id_mes = $idm[0][0];
                    #** Buscr si tieen cierre
                    $bc = $con->Listar("SELECT * FROM gs_cierre_periodo WHERE mes = $id_mes AND anno = $id_p");
                    if(!empty($bc[0][0])){

                        $sql_cons ="UPDATE  `gs_cierre_periodo` 
                        SET `estado` =:estado
                        WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                            array(":estado",1),
                            array(":id_unico",$bc[0][0]),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    } else {
                        $sql_cons ="INSERT INTO `gs_cierre_periodo` 
                            ( `anno`, `mes`, 
                            `estado`, `fecha_cierre`, 
                            `usuario`, `parametrizacionanno`) 
                        VALUES (:anno, :mes, 
                            :estado, :fecha_cierre, 
                            :usuario, :parametrizacionanno)";
                        $sql_dato = array(
                            array(":anno",$id_p),
                            array(":mes",$id_mes),
                            array(":estado",1),
                            array(":fecha_cierre",date('Y-m-d')),
                            array(":usuario",$id_usuario),
                            array(":parametrizacionanno",$id_p),
                            
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                     //   var_dump($resp);
                    }
                    if(empty($resp)){
                        $g +=1;
                    }
                }
            }
        }
        echo $g;
    break;
    
 } 
?>

