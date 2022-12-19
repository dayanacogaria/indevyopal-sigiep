<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 01/06/2017
 * Time: 5:17 PM
 */

/**
 * Class configuracion_viasoft
 */
class configuracion_viasoft {
    private $id_unico;
    private $concepto;
    private $codigo_viasoft;
    private $porcentaje;

    /**
     * @return mixed
     */
    public function getIdUnico() {
        return $this->id_unico;
    }

    /**
     * @return mixed
     */
    public function getConcepto() {
        return $this->concepto;
    }

    /**
     * @param mixed $concepto
     */
    public function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    /**
     * @return mixed
     */
    public function getCodigoViasoft() {
        return $this->codigo_viasoft;
    }

    /**
     * @param mixed $codigo_viasoft
     */
    public function setCodigoViasoft($codigo_viasoft) {
        $this->codigo_viasoft = $codigo_viasoft;
    }

    /**
     * @return mixed
     */
    public function getPorcentaje() {
        return $this->porcentaje;
    }

    /**
     * @param mixed $porcentaje
     */
    public function setPorcentaje($porcentaje) {
        $this->porcentaje = $porcentaje;
    }

    /**
     * configuracion_viasoft constructor.
     * @param int $concepto Id del concepto
     * @param int $codigo_viasoft Codigo concepto viasoft
     * @param int $porcentaje Porcentaje a tomar del valor
     * @param null|int $id_unico Id del registro puede ser nulo cuando se crea
     */
    function __construct($concepto, $codigo_viasoft, $porcentaje, $id_unico = NULL) {
        $this->concepto = $concepto;
        $this->codigo_viasoft = $codigo_viasoft;
        $this->porcentaje = $porcentaje;
        $this->$id_unico = $id_unico;
    }

    /**
     * save_data
     *
     * Función para registrar en la base de datos
     *
     * @param int $concepto Id de concepto
     * @param int $codigoV Código de concepto siasoft
     * @param int $porcentaje Porcentaje de valor
     * @return bool $inserted Cuando se registra retornara verdadero
     */
    public static function save_data($concepto, $codigoV, $porcentaje, $param) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gs_configuracion_viasoft (id_concepto, codigo_viasoft, porcentaje, parametrizacionanno) VALUES ($concepto, $codigoV, $porcentaje, $param)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * modifiy_data
     *
     * Función para modificar los valores en la base de datos
     *
     * @param int $concepto Id de concepto
     * @param int $codigoV Código de viasoft
     * @param int $porcentaje Porcentaje
     * @param int $id_unico Id del registro seleccionado para modificar
     * @return bool $modify_data Cuando se modifica se retornara verdadero
     */
    public static function modify_data($codigoV, $porcentaje, $id_unico) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gs_configuracion_viasoft SET codigo_viasoft = $codigoV, porcentaje = $porcentaje WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    /**
     * delete_data
     *
     * Función para eliminar los valores en la base de datos
     *
     * @param int $id_unico Id del registro seleccionado
     * @return bool $deleted Cuando se elimine retornara true
     */
    public static function delete_data ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gs_configuracion_viasoft WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }
}