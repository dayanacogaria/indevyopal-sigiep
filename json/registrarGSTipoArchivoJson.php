<?php 
/**
 * registrarGSTipoArchivoJson.php
 * 
 * Archivo para registro,eliminado y modificado de tipo archivo
 * 
 * @author Alexander Numpaque
 * @package Tipo Archivo
 * @version $Id: listar_GS_TIPO_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
 * */

/**
* tipoArchivo
* 
* Clase para guardado, modificado y eliminado de gs_tipo_archivo
* 
* @author Alexander Numpaque
* @package Tipo Archivo
* @version $Id: listar_GS_TIPO_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
* */
class tipoArchivo {

	private $id_unico;
	private $nombre;
	
	public function getId(){
		return $this->id_unico;
	}

	public function getNombre(){
		return $this->nombre;
	}

	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	function __construct($nombre,$id_unico = null) {
		$this->nombre = $nombre;
		$this->id_unico = $id_unico;
	}
	/**
	 * save_type
	 * 
	 * Función para guardar los tipo de archivo
	 * 
	 * @author Alexander Numpaque
	 * @package Tipo Archivo
	 * @param String $nombre Variable con el nombre de tipo de archivo
	 * @return bool $inserted indicador que el registro a sido guardado en la base de datos
	 * */
	public function save_type($nombre){
		$inserted = false;
		require ('../Conexion/conexion.php');
		$sql = "INSERT INTO gs_tipo_archivo(nombre) VALUES($nombre)";
		$result = $mysqli->query($sql);
		if($result == true){
			$inserted = true;
		}
		return $inserted;
	}

	/**
	 * modify_type
	 * 
	 * Función para modificar el nombre del tipo de archivo
	 * 
	 * @author Alexander Numpaque
	 * @package Tipo Archivo
	 * @param String $nombre Variable con nuevo valor para actualizar el campo
	 * @param int $id_unico id del tipo de archivo
	 * @return bool $edited Variable para indicar que se modifico el valor en la base de datoss
	 * */
	public function modify_type($nombre,$id_unico){
		require ('../Conexion/conexion.php');	
		$edited = false;	
		$sql = "UPDATE gs_tipo_archivo SET nombre = $nombre WHERE id_unico = $id_unico";
		$result = $mysqli->query($sql);		
		if($result == true){
			$edited = true;
		}
		return $result;
	}

	/**
	 * delete_type
	 * 
	 * Función para eliminar el registro en la base de datos
	 * 
	 * @author Alexander Numpaque
	 * @package Tipo Archivo
	 * @param int $id_unico Id del valor seleccionado para ser eliminado de la base de datos
	 * @return bool $deleted Variable para indicar si se elimino el registro en la base de datos
	 * */
	public function delete_type($id_unico){
		$deleted = false;
		require ('../Conexion/conexion.php');
		$sql = "DELETE FROM gs_tipo_archivo WHERE id_unico = $id_unico";
		$result = $mysqli->query($sql);
		if($result == true) {
			$deleted = true;
		}
		return $deleted;
	}
}

 ?>
