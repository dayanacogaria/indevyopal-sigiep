<?php
/**
 * registrarGSTipoArchivoJson.php
 * 
 * Archivo para registro,eliminado y modificado de tipo dependencia
 * 
 * @author Alexander Numpaque
 * @package Tipo Dependencia
 * @version $Id: registrarTipo_dependencia.php 001 2017-05-25 Alexander Numpaque$
 * */
    
/**
* Tipo Dependencia
* 
* Clase para guardado, modificado y eliminado de gf_tipo_dependencia
* 
* @author Alexander Numpaque
* @package Tipo Archivo
* @version $Id: registrarTipo_dependencia.php 001 2017-05-25 Alexander Numpaque$
* */

class tipoDependencia {

    private $id_unico;
    private $nombre;
    
    private function getId() {
        return $this->id_unico;
    }

    private function getNombre() {
        return $this->nombre;
    }

    private function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function __construct($nombre, $id_unico = NULL) {
        $this->nombre = $nombre;
        $this->id_unico = $id_unico;         
    }

    /**
     * save_date
     * 
     * Función para registrar valores en la tabla gf_tipo_dependencia
     * 
     * @author Alexander Numpaque
     * @package Tipo Dependencia
     * @param String $nombre Valor o nombre del tipo
     * @return bool $inserted Si el valor es insertado retornara true
     **/
    public function save_data($nombre) {
        require ('../Conexion/conexion.php');
        $inserted = false;        
        $sql = "INSERT INTO gf_tipo_dependencia (nombre) VALUES($nombre)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * modify_data
     * 
     * Función para modificar valores en la tabla gf_tipo_dependencia
     * 
     * @author Alexander Numpaque
     * @package Tipo  Dependencia
     * @param String $nombre Valor a cambiar (Nombre del tipo)
     * @param int $id_unico Id del registro a modificar
     * @return bool Si es modificado retornara true
     **/
    public static function modify_data($nombre, $id_unico) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gf_tipo_dependencia SET nombre = $nombre WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    /**
     * delete_data
     * 
     * Función para eliminar valores en la tabla gf_tipo_Dependencia
     * 
     * @author Alexander Numpaque
     * @package Tipo Dependencia
     * @param int $id_unico Id del  registro a eliminar
     * @return bool $deleted retornara true si el valor es eliminado de la base de datos
     */
    public static function delete_data($id_unico) {
        require ('../Conexion/conexion.php');
        $delete = false;
        $sql = "DELETE FROM gf_tipo_dependencia WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $delete = true;
        }
        return $delete;
    }
}

    
?>
