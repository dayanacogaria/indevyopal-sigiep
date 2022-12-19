<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conexion = "localhost";
$database_conexion = "vivienda_yopal";
$username_conexion = "sigiep";
$password_conexion = "grupo3a@2020";
$mysqli = new mysqli($hostname_conexion,$username_conexion,$password_conexion,$database_conexion);
mysqli_set_charset($mysqli,'utf8');
/* comprobar la conexión */
if (mysqli_connect_errno()) {
    printf("Falló la conexión: %s\n", mysqli_connect_error());
    exit();
} 
$baseDatos = $database_conexion;
?> 