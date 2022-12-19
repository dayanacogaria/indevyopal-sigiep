<?php
#Llamamos a la clase de conexión
session_start(); 
require_once ('./Conexion/conexion.php');
$user       = $mysqli->real_escape_string($_POST["txtUsuario"]);
$pass       = $mysqli->real_escape_string($_POST["txtPass"]);
$anno       = $mysqli->real_escape_string($_POST['sltAnno']);
$compania   = $mysqli->real_escape_string($_POST['sltTercero']);
$ter        = $mysqli->real_escape_string($_POST['txtIdentificacion']);
#Consulta para validar usuario
$sql="select u.usuario,u.contrasen,u.tercero, u.id_unico, tc.tipo_compania,
    u.estado , t.numeroidentificacion 
    from gs_usuario u 
    LEFT JOIN gf_tercero t ON u.tercero = t.id_unico 
    LEFT JOIN gf_tercero tc ON t.compania = tc.id_unico 
    where u.usuario='$user' and u.contrasen='$pass' 
    AND t.compania = $compania 
    AND CONCAT_WS(' - ',  
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
             t.apellidodos)), 
            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                 t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) )  
            LIKE '%".$ter."%'";
$result=$mysqli->query($sql);
$row= mysqli_fetch_row($result);
#Variables
if($row[5]==1){
    $usuario = $row[0];
    $contra = $row[1];
    if(($user == $usuario) && ($contra == $pass)){
        #Si usuario y contraseña son validos definimos sesion y guardamos los datos    
        $ingreado = "SI";    
        #Carmagmos la variable de parametrizacion año
        $_SESSION['anno'] = $anno;     
        #Cargamos de la variable compania
        $_SESSION['compania'] = $compania;    
        #Carmagos la variable nombre de usuario
        $_SESSION['usuario'] = $user;
        #Cargamos el id del tercero que se relaciona al logueado
        $_SESSION['usuario_tercero'] = $row[2];
        $_SESSION['id_usuario'] = $row[3];
        $_SESSION['tipo_compania'] = $row[4];
        $_SESSION['num_usuario'] = $row[6];
        echo 2;
    }else{    
        echo 1;
    }
} elseif($row[5]==3){
    echo 3;
} else {
    echo 1;
}
?>
