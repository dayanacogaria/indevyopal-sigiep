<?php
/**
 * registrar_GF_DEPENDENCIAjson.php
 * 
 * Archivo para registro,eliminado y modificado de dependencia
 * 
 * @author Alexander Numpaque
 * @package Dependencia
 * @version $Id: registrar_GF_DEPENDENCIAJson.php 001 2017-05-25 Alexander Numpaque$
 * */

/**
* gf_dependencia
* 
* Clase para guardado, modificado y eliminado de gf_dependencia
* 
* @author Alexander Numpaque
* @package Dependencia
* @version $Id: registrar_GF_DEPENDENCIAjson.php 001 2017-05-25 Alexander Numpaque$
* */

class gf_dependencia {
    
    private $id_unico;
    private $nombre;
    private $sigla;
    private $movimiento;
    private $activa;
    private $predecesor;
    private $centroCosto;
    private $tipoDependencia;
    private $compania;

    function __construct($nombre, $sigla, $movimiento, $activa, $predecesor, $centroCosto, $tipoDependencia, $compania, $id_unico = NULL) {
        $this->id_unico = $id_unico;
        $this->nombre = $nombre;
        $this->sigla = $sigla;
        $this->movimiento = $movimiento;
        $this->activa = $activa;
        $this->predecesor = $predecesor;
        $this->centroCosto = $centroC;
        $this->tipoDependencia = $tipoDependencia;
        $this->compania = $compania;
    }

    public function getIdUnico () {
        return $this->id_unico;
    }

    public function getNombre () {
        return $this->nombre;
    }

    public function setNombre ($nombre) {
        $this->nombre = $nombre;
    }

    public function getSigla () {
        return $this->sigla;
    }

    public function setSigla ($sigla) {
        $this->sigla = $sigla;
    }

    public function getMovimiento() {
        return $this->movimiento;
    }
    
    public function setMovimiento($movimiento) {
        $this->movimiento = $movimiento;
        return $this;
    }

    public function getActiva() {
        return $this->activa;
    }
    
    public function setActiva($activa) {
        $this->activa = $activa;
        return $this;
    }

    public function getPredecesor() {
        return $this->predecesor;
    }
    
    public function setPredecesor($predecesor) {
        $this->predecesor = $predecesor;
        return $this;
    }

    public function getCentrocosto() {
        return $this->centroCosto;
    }
    
    public function setCentrocosto($centroCosto) {
        $this->centroCosto = $centroCosto;
        return $this;
    }

    public function getTipoDependencia() {
        return $this->tipoDependencia;
    }
    
    public function setTipoDependencia($tipoDependencia) {
        $this->tipoDependencia = $tipoDependencia;
        return $this;
    }

    public function getCompania() {
        return $this->compania;
    }
    
    public function setCompania($compania) {
        $this->compania = $compania;
        return $this;
    }

    /**
     * Función para registrar valores en la base de datos
     * 
     * save_data 
     * 
     * @author Alexander Numpaque
     * @package Dependencia
     * @param String $nombre Nombtre de la dependencia
     * @param String $sigla Sigla de  la dependencia
     * @param int $movimiento 1|2 Tiene o no tiene movimiento
     * @param int $activa 1|2 Activa o inactiva
     * @param int $predecesor Id de predecesor
     * @param int $centroCosto Id de centro de costo
     * @param int $tipoDependencia Id de tipo dependencia
     * @param int $compania Id de compañia
     * @return bool $inserted retorna verdadero cuando se inserta el valor en la base de datos
     */
    public static function save_data ($nombre, $sigla, $movimiento, $activa, $predecesor, $centroCosto, $tipoDependencia, $compania) {
        require ('../Conexion/conexion.php');

        $inserted = false;
        $sql = "INSERT INTO gf_dependencia (nombre, sigla, movimiento, activa, predecesor, centrocosto, tipodependencia, compania) 
                VALUES ($nombre, $sigla, $movimiento, $activa, $predecesor, $centroCosto, $tipoDependencia, $compania)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * modify_data
     * 
     * Modificamos los valores en la base de datos
     * 
     * @author Alexander Numpaque
     * @package Dependencia
     * @param String $nombre Nombtre de la dependencia
     * @param String $sigla Sigla de  la dependencia
     * @param int $movimiento 1|2 Tiene o no tiene movimiento
     * @param int $activa 1|2 Activa o inactiva
     * @param int $predecesor Id de predecesor
     * @param int $centroCosto Id de centro de costo
     * @param int $tipoDependencia Id de tipo dependencia
     * @param int $compania Id de compañia
     * @return bool $inserted retorna verdadero cuando se inserta el valor en la base de datos
     * @param int $id_unico Id del registro a modificar
     * @return bool $edited retorna verdadero cuando el registro es modificado
     */
    public static function modify_data ($nombre, $sigla, $movimiento, $activa, $predecesor, $centroCosto, $tipoDependencia, $id_unico) {
        require ('../Conexion/conexion.php');
        $compania = $_SESSION['compania'];

        $edited = false;
        $sql = "UPDATE gf_dependencia 
                SET nombre=$nombre, sigla=$sigla,movimiento=$movimiento, activa=$activa, predecesor=$predecesor, centrocosto=$centroCosto, tipodependencia=$tipoDependencia 
                WHERE id_unico = $id_unico
                AND compania = $compania";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    /**
     * delete_data
     * 
     * Función para eliminar valores de la base de datos
     * 
     * @author Alexander Numpaque
     * @package Dependencia
     * @param int $id_unico Id del registro a eliminar
     * @return bool $deleted Retorna verdadero si el registro es eliminado
     */
    public static function delete_data ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gf_dependencia 
                WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }
} 
?>