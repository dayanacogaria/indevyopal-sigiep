<?php
class conectar{
    public static function conexion(){
        $hostname_conexion = "localhost";
        $database_conexion = "vivienda_yopal";
        $username_conexion = "sigiep";
        $password_conexion = "grupo3a@2020";
        $mysqli = new mysqli($hostname_conexion,$username_conexion,$password_conexion,$database_conexion);
        mysqli_set_charset($mysqli,'utf8');
        return $mysqli;
    }
}