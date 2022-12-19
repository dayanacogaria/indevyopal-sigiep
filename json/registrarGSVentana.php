<?php

/**
 * Created by PhpStorm.
 * User: Alexander Numpaque
 * Date: 29/06/2017
 * Time: 3:09 PM
 */
class ventana {
    private $id_unico;
    private $nombre;
    private $menu_asociado;
    private $campos;
    private $botones;

    function __construct($nombre, $menu_asociado, $campos, $botones, $id_unico = NULL) {
        $this->nombre = $nombre;
        $this->menu_asociado = $menu_asociado;
        $this->campos = $campos;
        $this->botones = $botones;
        $this->id_unico = $id_unico;
    }

    /**
     * @return null
     */
    public function getIdUnico() {
        return $this->id_unico;
    }
    /**
     * @return mixed
     */
    public function getNombre() {
        return $this->nombre;
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
    public function getMenuAsociado() {
        return $this->menu_asociado;
    }

    /**
     * @param mixed $menu_asociado
     */
    public function setMenuAsociado($menu_asociado) {
        $this->menu_asociado = $menu_asociado;
    }

    /**
     * @return mixed
     */
    public function getBotones() {
        return $this->botones;
    }

    /**
     * @param mixed $botones
     */
    public function setBotones($botones) {
        $this->botones = $botones;
    }

    /**
     * @return mixed
     */
    public function getCampos() {
        return $this->campos;
    }

    /**
     * @param mixed $campos
     */
    public function setCampos($campos) {
        $this->campos = $campos;
    }

    /**
     * save_data
     *
     * Función para guardar datos en la tabla gs_ventana
     * @package Ventana
     * @author Alexander Numpaque
     * @param string $nombre Nombre de l ventana
     * @return bool $inserted Si es registrado el valor retornara verdadero
     */
    public static function save_data ($nombre) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_ventana(nombre) VALUES ($nombre)";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
        mysqli_close($mysqli);
    }

    /**
     * modify_data
     *
     * Función para modificar datos guardados en la tabla gs_ventana
     * @package Ventana
     * @author Alexander Numpaque
     * @param int $id_unico Id del registro a modificar
     * @param String $nombre Variable a cambiar
     * @return bool
     */
    public static function modify_data ($id_unico, $nombre) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gs_ventana SET nombre = '$nombre' WHERE id_unico = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
        mysqli_close($mysqli);
    }

    /**
     * delete_data
     * Función para eliminar valores en la tabla gs_ventana
     * @package Ventana
     * @author Alexander Numpaque
     * @param $id_unico
     * @return bool Si es eliminado retornara verdadero
     */
    public static function delete_data ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_ventana WHERE id_unico = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true){
            $deleted = true;
        }
        return $deleted;
        mysqli_close($mysqli);
    }

    /**
     * save_inputs
     * Función para registrar en la tabla gs_ventana_campo
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $window Id de menu ventana
     * @param int $input Id de campo registrado
     * @return bool Si es registrado retornara verdadero
     */
    public static function save_inputs ($window, $input) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_ventana_campo(menuventana, campo) VALUES($window, $input)";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
        mysqli_close($mysqli);
    }

    /**
     * delete_inputs
     * Función para eliminar valores en la tabla gs_ventana_Campo
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $id_unico Id unico del registro a eliminar
     * @return bool Si es eliminado retornara verdadero
     */
    public static function delete_inputs ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_ventana_campo WHERE menuventana = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
        mysqli_close($mysqli);
    }

    /**
     * Función para registrar los botones a menu ventana
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $window Id de menu ventana
     * @param int $button
     * @return bool $inserted Retornara verdadero si el registro es guardado en la base de datos
     */
    public static function save_buttons ($window, $button) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_ventana_boton(menuventana, boton) VALUES($window, $button)";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
        mysqli_close($mysqli);
    }

    /**
     * delete_buttons
     * Función para eliminar los botones relacionados a menu ventana
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $id_unico Id del registro a eliminar
     * @return bool $deleted Si el registro es eliminado retornara verdadero
     */
    public static function delete_buttons ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_ventana_boton WHERE menuventana = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
        mysqli_close($mysqli);
    }

    /**
     * save_input
     * Función para registrar el botón en la base de datos
     * @author Alexander Numpaque
     * @package Ventana
     * @param String $nombre Valor a registrar en la base de datos
     * @return bool $inserted
     */
    public static function save_input ($nombre) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_campo(nombre) VALUES ($nombre)";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
        mysqli_close($mysqli);
    }

    /**
     * modify_input
     * Función para modificar el botón
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $id_unico Id del registro a modificar
     * @param string $nombre Valor a cambiar en la base de datos
     * @return bool $edited
     */
    public static function modify_input ($id_unico, $nombre) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gs_campo SET nombre = '$nombre' WHERE id_unico = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
        mysqli_close($mysqli);
    }

    /**
     * delete_input
     * Función para eliminar el botón
     * @author Alexander Numpaque
     * @package Ventana
     * @param $id_unico Id unico del registro a eliminar
     * @return bool $deleted Si el registro es eliminado retornara verdadero
     */
    public static function delete_input ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_campo WHERE id_unico = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true){
            $deleted = true;
        }
        return $deleted;
        mysqli_close($mysqli);
    }

    /**
     * save_button
     * Función para registrar el botón en la base de datos
     * @author Alexander Numpaque
     * @package Ventana
     * @param String $nombre Valor a registrar en la base de datos
     * @return bool $inserted
     */
    public static function save_button ($nombre) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_boton(nombre) VALUES ($nombre)";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
        mysqli_close($mysqli);
    }

    /**
     * modify_button
     * Función para modificar el botón
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $id_unico Id del registro a modificar
     * @param string $nombre Valor a cambiar en la base de datos
     * @return bool $edited
     */
    public static function modify_button ($id_unico, $nombre) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gs_boton SET nombre = '$nombre' WHERE id_unico = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
        mysqli_close($mysqli);
    }

    /**
     * delete_button
     * Función para eliminar el botón
     * @author Alexander Numpaque
     * @package Ventana
     * @param $id_unico Id unico del registro a eliminar
     * @return bool $deleted Si el registro es eliminado retornara verdadero
     */
    public static function delete_button ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_boton WHERE id_unico = $id_unico";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if($result == true){
            $deleted = true;
        }
        return $deleted;
        mysqli_close($mysqli);
    }

    /**
     * get_menu_ventana
     * Función para obtener el id de menu ventana
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $window Id de ventana
     * @return int $id_m Id de menu ventana
     */
    public static function get_menu_ventana ($window) {
        require ('../Conexion/conexion.php');
        $id_m = 0;
        $sql = "SELECT id_unico FROM gs_menu_ventana WHERE ventana = $window";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $id_m = $row[0];
        }
        return $id_m;
        mysqli_close($mysqli);
    }

    /**
     * get_ventana_boton
     * Función para obtener el id de ventana boton
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $mnu_v Id de menu ventana
     * @param int $boton Id del botón
     * @return int $id_v Id del registro obtenido por la consulta
     */
    public static function get_ventana_boton ($mnu_v, $boton) {
        require ('../Conexion/conexion.php');
        $id_v = 0;
        $sql = "SELECT id_unico FROM gs_ventana_boton WHERE menuventana = $mnu_v AND  boton = $boton";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $id_v = $row[0];
        }
        return $id_v;
        mysqli_close($mysqli);
    }

    /**
     * get_ventana_campo
     * Función para obtener el id de ventana campo
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $mnu_v Id de menu ventana
     * @param int $campo Id del campo
     * @return int $id_v Id del registro obtenido por la consulta
     */
    public static function get_ventana_campo ($mnu_v, $campo) {
        require ('../Conexion/conexion.php');
        $id_v = 0;
        $sql = "SELECT id_unico FROM gs_ventana_campo WHERE menuventana = $mnu_v AND  campo = $campo";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $id_v = $row[0];
        }
        return $id_v;
        mysqli_close($mysqli);
    }

    /**
     * get_last_menu_ventana
     * Función para obtener el ultimo id registrado en la tabla menu ventana
     * @author Alexander Numpaque
     * @package Ventana
     * @return int $id Id del último registro de la tabla gs_menu_ventana
     */
    public static function get_last_menu_ventana () {
        require ('../Conexion/conexion.php');
        $id = 0;
        $sql = "SELECT MAX(id_unico) FROM gs_menu_ventana";
        /** @var Connection $mysqli */
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $id = $row[0];
        }
        return $id;
        mysqli_close($mysqli);
    }

    /**
     * save_main_window
     * Función para guardar los valores en gs_menu_ventana
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $window Id de ventana
     * @param int $main Id de la opcion de menu asociado
     * @return bool $inserted Retorna verdadero cuando el valor es guardado en la base de datos
     */
    public static function save_main_window ($window, $main) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_menu_ventana(menuaso, ventana) VALUES ($main, $window)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
        mysqli_close($mysqli);
    }

    /**
     * delete_main_window
     * Función para eliminar los registro en menu ventana
     * @param int $id_unico Id del registro a eliminar
     * @return bool $deleted Retornara verdadero si el registro es eliminado
     */
    public static function delete_main_window ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_menu_ventana WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
        mysqli_close($mysqli);
    }

    /**
     * get_gs_menu_aso
     * Función para obtener el id de menu asociado
     * @author Alexander Numpaque
     * @package Ventana
     * @param int $padre Id del menu padre
     * @param int $hijo Id del menu hijo
     * @return int Retornara el id del menu asociado es decir de la familia
     */
    public static function get_menu_aso ($padre, $hijo) {
        require ('../Conexion/conexion.php');
        $id = 0;
        $sql = "SELECT id_unico FROM gs_menu_aso WHERE menupadre = $padre AND menuhijo = $hijo";
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
           $row = mysqli_fetch_row($result);
           $id = $row[0];
        }
        return $id;
        mysqli_close($mysqli);
    }

    /**
     * get_last_window
     * Función para obtener el ultimo id registrado en ventana
     * @return int $id Id del ultimo registro en la tabla ventana
     */
    public static function get_last_window() {
        require ('../Conexion/conexion.php');
        $id = 0;
        $sql = "SELECT MAX(id_unico) FROM gs_ventana";
        $result = $mysqli->query($sql);
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            $id = $row[0];
        }
        return $id;
        mysqli_close($mysqli);
    }
}