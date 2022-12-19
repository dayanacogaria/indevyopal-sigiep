<?php
/**
* registrarGSDetalleArchivoJson.php
*
* @author Alexander Numpaque
* @package Detalle Archivo
* @version $Id: registrarGSDetalleArchivoJson.php 001 2017-05-18 Alexander Numpaque$
**/

/**
* detalle_archivo
*
* Esta clase contendra los procesos de registro eliminado, editado y registro de la tabla gs_detalle_archivo
*
* @author Alexander Numpaque
* @package Detalle Archivo
* @version $Id: registrarGSDetalleArchivoJson.php 001 2017-05-18 Alexander Numpaque$
*/
class detalle_archivo {

	private $id_unico;
	private $clase;
	private $conceptoRBCT;
	private $columna;

	function getId_unico(){
		return $this->id_unico;
	}

	function getClase(){
		return $this->clase;
	}

	function setClase($clase){
		$this->clase = $clase;
	}

	function getConceptoRubroCuenta(){
		return $this->conceptoRBCT;
	}

	function setConceptoRubroCuenta($conceptoRBCT){
		$this->conceptoRBCT = $conceptoRBCT;
	}

	function getColumna(){
		return $this->columna;
	}

	function setColumna($columna){
		$this->columna = $columna;
	}

	function __construct($clase, $conceptoRBCT, $columna, $id_unico = NULL){
		$this->clase = $clase;
		$this->conceptoRBCT = $conceptoRBCT;
		$this->columna = $columna;
		$this->id_unico = $id_unico;
	}

	/**
	* save_data
	*
	* Función para guardar el registro en la base de datos
	*
	* @author Alexander Numpaque
	* @package Detalle Archivo
	* @param int $clase Id de clase archivo
	* @param int $conceptoRBCT Id de concepto rubro cuenta
	* @param int|String $columna Columna del archivo que representa el concepto
	* @return bool $inserted Si el registro es realizado retorna true
	*/
	public static function save_data($clase, $conceptoRBCT, $columna, $param) {
		require ('../Conexion/conexion.php');
		$inserted = false;
		$sql = "INSERT INTO gs_detalle_archivo(clase, concepto_rubro_cuenta, columna, parametrizacionanno) VALUES($clase, $conceptoRBCT, $columna, $param)";
		$result = $mysqli->query($sql);
		if($result == true){
			$inserted = true;
		}
		return $inserted;
	}

	/**
	* save_data
	*
	* Función de modificar registro en la base de datos
	*
	* @author Alexander Numpaque
	* @package Detalle Archivo
	* @param int $clase Id de clase archivo
	* @param int $conceptoRBCT Id de concepto rubro cuenta
	* @param int|String $columna Columna del archivo que representa el concepto
	* @param int $id_unico Id del registro seleccionado para modificar
	* @return bool $edited Si el registro es modificado retorna true
	*/
	public static function modify_data($conceptoRBCT, $columna, $id_unico) {
		require ('../Conexion/conexion.php');
		$edited = false;
		$sql = "UPDATE gs_detalle_archivo SET concepto_rubro_cuenta = $conceptoRBCT, columna = $columna WHERE id_unico = $id_unico";
		$result = $mysqli->query($sql);
		if($result == true){
			$edited = true;
		}
		return $edited;
	}

	/**
	* delete_data
	*
	* Función eliminar el registro en la base de datos
	*
	* @author Alexander Numpaque
	* @package Detalle Archivo
	* @param int $id_unico Id del registro seleccionado para modificar
	* @return bool $deleted Si el registro es eliminado retorna true
	*/
	public static function delete_data($id_unico){
		require ('../Conexion/conexion.php');
		$deleted = false;
		$sql = "DELETE FROM gs_detalle_archivo WHERE id_unico = $id_unico";
		$result = $mysqli->query($sql);
		if($result == true){
			$deleted = true;
		}
		return $deleted;
	}
}
 ?>