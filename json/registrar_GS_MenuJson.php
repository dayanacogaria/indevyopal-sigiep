<?php

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 22/06/2017
 * Time: 11:59 AM
 */
class menu {
    private $id_unico;
    private $nombre;
    private $ruta;
    private $father;
    private $son;

    function __construct($nombre, $ruta, $compania, $id_unico = NULL) {
        $this->id_unico = $id_unico;
        $this->nombre = $nombre;
        $this->ruta = $ruta;
    }

    /**
     * @param mixed $id_unico
     */
    public function setIdUnico($id_unico) {
        $this->id_unico = $id_unico;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * @param mixed $ruta
     */
    public function setRuta($ruta) {
        $this->ruta = $ruta;
    }

    /**
     * @return mixed
     */
    public function getRuta() {
        return $this->ruta;
    }

    /**
     * @param mixed $father
     */
    public function setFather($father) {
        $this->father = $father;
    }

    /**
     * @return mixed
     */
    public function getFather() {
        return $this->father;
    }

    /**
     * @param mixed $son
     */
    public function setSon($son) {
        $this->son = $son;
    }

    /**
     * @return mixed
     */
    public function getSon() {
        return $this->son;
    }


    /**
     * save_data_main
     *
     * Función para registrar las opciones de menu
     *
     * @author Alexander Numpaque
     * @package Menu
     * @param String $nombre Nombre de la opción de menú
     * @param String $ruta Ruta o localización del archivo de la opción de menú
     * @return bool $inserted Retorna verdadero cuando es guardado en la base de datos
     */
    public static function save_data_main($nombre, $ruta,  $orden, $estado, $padre) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_menu (nombre, ruta,  orden, estado, predecesor) 
            VALUES (\"$nombre\",$ruta,  $orden, $estado, $padre)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }
    public static function get_max_id_main() {
        require ('../Conexion/conexion.php');
        $id = 0;
        $sql = "SELECT MAX(id_unico) FROM gs_menu";
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $id = $row[0];
        }
        return $id;
    }

    public static function save_data_son_father($father, $son) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_menu_aso(menupadre, menuhijo) VALUES ($father, $son)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    public static function exist_family ($father, $son) {
        require ('../Conexion/conexion.php');
        $exist = 0;
        $sql = "SELECT id_unico FROM gs_menu_aso WHERE menupadre = $father && menuhijo = $son";
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $exist = $row[0];
        }
        return $exist;
    }
    public static function  exist_option ($nombre) {
        require ('../Conexion/conexion.php');
        $exist = 0;
        $sql = "SELECT id_unico FROM gs_menu WHERE nombre = '$nombre'";
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $exist = $row[0];
        }
        return $exist;
    }

    public static function delete_option ($id) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_menu WHERE id_unico = $id";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }
    public static function delete_family ($father, $son) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_menu_aso WHERE menupadre = $father && menuhijo = $son";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }

    public static function modify_option($id_unico, $nombre, $ruta, $padre, $orden, $estado) {
        
        require ('../Conexion/conexion.php'); 
        $edited = false;
        $sql = "UPDATE gs_menu 
            SET nombre = \"$nombre\", ruta =$ruta, 
                predecesor = $padre, orden = $orden, 
            estado = $estado WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    public static function modify_family ($id_unico , $father, $son) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gs_menu_aso SET menupadre = $father, menuhijo = $son WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }
}