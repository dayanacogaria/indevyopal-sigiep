<?php 
/**
* registrarGSClaseArchivo.php
* 
* @author Alexander Numpaque
* @package Clase Archivo
* @version $Id: listar_GS_CLASE_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
**/

/**
* claseArchivo
* 
* @author Alexander Numpaque
* @package Clase Archivo
* @version $Id: listar_GS_CLASE_ARCHIVO.php 001 2017-05-17 Alexander Numpaque$
*/
class claseArchivo {

	private $id_unico;
	private $nombre;
	private $id_tipo;
	private $linea_inicial;

	public function getId_unico(){
		return $this->id_unico;
	}

	public function getNombre(){
		return $this->nombre;
	}

	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	public function getIdTipoA(){
		return $this->id_tipo;
	}
	
	public function setIdTipoA($id_tipo){
		return $this->id_tipo = $id_tipo;
	}

	public function getLineaInicial(){
		return $this->linea_inicial;
	}

	public function setLineaInicial($linea_inicial){
		return $this->linea_inicial = $linea_inicial;
	}

	function __construct($nombre, $id_tipo, $linea_inicial, $id_unico = NULL){
		$this->nombre = $nombre;
		$this->id_tipo = $id_tipo;
		$this->linea_inicial = $linea_inicial;
		$this->id_unico = $id_unico;
	}

	/**
	* save_class
	* 
	* Función para guardado de clase archivo
	* 
	* @author Alexander Numpaque
	* @package Clase Archivo
	* @param String $nombre Nombre de la clase de archivo
	* @param int $id_tipo id de tipo de archivo
	* @param int $lineaI Linea o fila en donde se inicia la lectura del archivo
	* @return bool $inserted Si es insertado retornara true
	**/
	public function save_class($nombre, $id_tipo, $lineaI){
		require ('../Conexion/conexion.php');
		$inserted = false;
		$sql = "INSERT INTO gs_clase_archivo (nombre, id_tipo_archivo, linea_inicial) VALUES($nombre ,$id_tipo ,$lineaI)";
		$result = $mysqli->query($sql);
		if($result == true){
			$inserted = true;
		}
		return $inserted;
	}

	/**
	* modify_class
	* 
	* Función para modificar el registro seleccionado de clase archivo
	* 
	* @author Alexander Numpaque
	* @package Clase Archivo
	* @param String $nombre Nombre de la clase de archivo
	* @param int $id_tipo id de tipo de archivo
	* @param int $lineaI Linea o fila en donde se inicia la lectura del archivo
	* @param int $id_unico Id del registro que se va a modificar
	* @return bool $edited Si es editado retornara true
	**/
	function modify_class($nombre, $id_tipo, $lineaI, $id_unico){
		require ('../Conexion/conexion.php');
		$edited = false;
		$sql = "UPDATE gs_clase_archivo SET nombre = $nombre, id_tipo_archivo = $id_tipo, linea_inicial = $lineaI WHERE id_unico = $id_unico";
		$result = $mysqli->query($sql);
		if($result == true){
			$edited = true;
		}
		return $edited;
	}
	/**
	* delete_class
	* 
	* Función para eliminar el registro seleccionado de clase archivo
	* 
	* @author Alexander Numpaque
	* @package Clase Archivo
	* @param int $id_unico id del registro seleccionado
	* @return bool $deleted Si es eliminado elregistro retornara true
	**/
	function delete_class($id_unico) {
		require ('../Conexion/conexion.php');
		$deleted = false;
		$sql = "DELETE FROM gs_clase_archivo WHERE id_unico = $id_unico";
		$result = $mysqli->query($sql);
		if($result == true){
			$deleted = true;
		}
		return $deleted;
	}
}
 ?>