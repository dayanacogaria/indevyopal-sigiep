<?php
require_once ('../Conexion/conexion.php');
session_start();
$id = $_POST['id'];
if ($id == 0) {
    echo '0';
}else{
    $sql = "SELECT codi_cuenta FROM gf_cuenta
            WHERE id_unico = $id";
    $a = $mysqli->query($sql);        
    $b = mysqli_fetch_row($a);        
    $c = (String)$b[0];
    $d = strlen($c);
    
    if ($d == 1) {
        $query = "SELECT PADRE.activa FROM gf_cuenta PADRE 
                  LEFT JOIN gf_cuenta HIJO ON HIJO.predecesor = PADRE.id_unico
                  WHERE PADRE.id_unico = '$id'" ;
            
        $rs = $mysqli->query($query);            
        $res = mysqli_fetch_row($rs);            
        echo $res[0];
    }else{
        $query1 = "SELECT HIJO.activa FROM gf_cuenta PADRE 
                   LEFT JOIN gf_cuenta HIJO ON HIJO.predecesor = PADRE.id_unico
                   WHERE HIJO.id_unico = '$id'" ;
        $rs1 = $mysqli->query($query1);        
        $res1 = mysqli_fetch_row($rs1);            
        echo $res1[0];
    }
}
?>

