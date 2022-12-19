<?php
require_once ('./Conexion/db.php');

class espacioHabitables{

    public $id_unico;
    public $tipo;
    public $codigo;
    public $descripcion;
    public $dependencia;
    public $asociado;
    public $ruta;
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public  function obtenerListado(){
        try {
            $str = "SELECT    sph.id_unico, tpe.nombre as nomtipo, sph.codigo, sph.descripcion, dpt.sigla, dpt.nombre, spe.codigo, sph.ruta, sph.estado 
                    FROM      gh_espacios_habitables as sph
                    LEFT JOIN gh_espacios_habitables as spe ON sph.asociado         = spe.id_unico
                    LEFT JOIN gh_tipo_espacio        as tpe ON sph.tipo             = tpe.id_unico
                    LEFT JOIN gf_dependencia         as dpt ON sph.dependencia      = dpt.id_unico
                    ORDER BY  sph.codigo ASC";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDiferentesCodigo($id){
        try {
            $str = "SELECT sph.id_unico, sph.codigo, sph.descripcion FROM gh_espacios_habitables as sph WHERE md5(sph.id_unico) != '$id' ORDER BY  sph.codigo ASC";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerAsociados(){
        try {
            $str = "SELECT sph.id_unico,  sph.codigo, sph.descripcion FROM gh_espacios_habitables as sph ORDER BY  sph.codigo ASC";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtener($id){
        try {
            $str = "SELECT    sph.id_unico, tpe.id_unico, tpe.nombre as nomtipo, sph.codigo, sph.descripcion, dpt.id_unico,
                              dpt.nombre, dpt.sigla, sph.asociado, sph.ruta, sph.estado 
                    FROM      gh_espacios_habitables as sph
                    LEFT JOIN gh_tipo_espacio        as tpe ON sph.tipo             = tpe.id_unico
                    LEFT JOIN gf_dependencia         as dpt ON sph.dependencia      = dpt.id_unico
                    WHERE     md5(sph.id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerTarifaTipoE($espacio){
        try {
            $str = "SELECT tar.id_unico, tar.valor, tar.nombre, tar.valor_rango_inferior, tar.valor_rango_superior
                    FROM gh_espacios_habitables sph
                    LEFT JOIN gh_tipo_espacio tpe ON sph.tipo = tpe.id_unico
                    LEFT JOIN gh_espacio_tarifa spt ON tpe.id_unico = spt.tipo_espacio
                    LEFT JOIN gp_tarifa tar ON spt.tarifa = tar.id_unico
                    WHERE md5(sph.id_unico) = '$espacio'";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposDiferentes($id){
        try {
            $str = "SELECT    tpe.id_unico, tpe.nombre, cle.nombre as clase
                    FROM      gh_tipo_espacio  as tpe
                    LEFT JOIN gh_clase_espacio as cle ON tpe.clase = cle.id_unico
                    WHERE     tpe.id_unico != $id";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerTipos(){
        try {
            $str = "SELECT    tpe.id_unico, tpe.nombre, cle.nombre as clase
                    FROM      gh_tipo_espacio  as tpe
                    LEFT JOIN gh_clase_espacio as cle ON tpe.clase = cle.id_unico";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerDependencias($compania){
        try {
            $str = "SELECT id_unico, CONCAT_WS(' ', sigla, nombre) FROM gf_dependencia WHERE compania = $compania";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerDependenciasDiferentes($id, $compania){
        try {
            $str = "SELECT id_unico, CONCAT_WS(' ', sigla, nombre) FROM gf_dependencia WHERE (id_unico != $id) AND (compania = $compania)";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function registrar(espacioHabitables $data){
        try {
            $str = "INSERT INTO gh_espacios_habitables (tipo, codigo, descripcion, dependencia, asociado, ruta, estado) VALUES($data->tipo, '$data->codigo', '$data->descripcion',  $data->dependencia, $data->asociado,'$data->ruta',$data->estado)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerNovedad(){
        try {
            $str = "SELECT *
                    FROM gh_novedad";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function registrarNovedad($nombre, $estado){
        try {
            $str = "INSERT INTO gh_novedad (nombre, inactivo) VALUES('$nombre', '$estado')";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function actualizarNovedad($id, $nombre, $estado){
        try {
            $str = "UPDATE gh_novedad
                    SET nombre = '$nombre',
                        inactivo = '$estado'
                    WHERE id_unico = $id
                    ";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function eliminarNovedad($id){
        try {
            $str = "DELETE
                    FROM gh_novedad
                    WHERE id_unico = $id
                    ";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerNovedadEspacio($id){
        try {
            $str = "
                SELECT nvsph.id_unico, nov.nombre, nvsph.fecha_inicial, nvsph.fecha_final, nov.inactivo, nov.id_unico
                FROM gh_novedad nov
                    LEFT JOIN gh_novedad_espacio_habitable nvsph
                      ON nov.id_unico = nvsph.novedad
                        LEFT JOIN gh_espacios_habitables sph
                           ON nvsph.espacio = sph.id_unico
                WHERE md5(sph.id_unico) = '$id'
                ORDER BY nvsph.fecha_inicial";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerEspacio($id){
        try {
            $str = "
                SELECT id_unico, codigo
                FROM gh_espacios_habitables
                WHERE md5(id_unico) = '$id'
                    ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function validarNovedad($id){
        try {
            $str = "
                SELECT MAX(id_unico)
                FROM gh_novedad_espacio_habitable
                WHERE espacio = $id
                ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerFechaNovedad($id){
        try {
            $str = "
                SELECT fecha_final
                FROM gh_novedad_espacio_habitable
                WHERE id_unico = $id
                ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function registrarNovedadEspacio($novedad, $espacio, $fechaI, $fechaF){
        try {
            $str = "INSERT INTO gh_novedad_espacio_habitable (novedad, espacio, fecha_inicial, fecha_final)
                    VALUES($novedad, $espacio, '$fechaI', '$fechaF')";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function actualizarNovedadEspacio($id, $novedad, $fechaI, $fechaF){
        try {
            $str = "UPDATE gh_novedad_espacio_habitable
                    SET novedad = $novedad,
                        fecha_inicial = '$fechaI',
                        fecha_final = '$fechaF'
                    WHERE id_unico = $id
                    ";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerminFecha($id){
        try {
            $str = "SELECT fecha_final
                    FROM gh_novedad_espacio_habitable
                    WHERE id_unico < $id
                    ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function eliminarNovedadEspacio($id){
        try {
            $str = "DELETE
                    FROM gh_novedad_espacio_habitable
                    WHERE id_unico = $id
                    ";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public  function validacionBloqueo($id, $fechaI, $anno){
        try {
            $str = "SELECT mov.numero
                    FROM gh_movimiento mov
                        LEFT JOIN gh_detalle_mov gdm ON mov.id_unico = gdm.movimiento
                    WHERE mov.tipo = 4 AND mov.parametrizacionanno = $anno
                    AND mov.fechaFinal >= '$fechaI'
                    AND gdm.espacio = $id
                    ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public  function validacionReserva($id, $fechaI, $anno){
        try {
            $str = "SELECT mov.numero
                    FROM gh_movimiento mov
                        LEFT JOIN gh_detalle_mov gdm ON mov.id_unico = gdm.movimiento
                        LEFT JOIN gh_movimiento aso ON mov.id_unico = aso.asociado
                    WHERE mov.tipo = 1 AND mov.parametrizacionanno = $anno
                    AND mov.fechaFinal >= '$fechaI'
                    AND gdm.espacio = $id
                    AND aso.id_unico IS NULL
                    ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public  function validacionIngreso($id, $fechaI, $anno){
        try {
            $str = "SELECT mov.numero
                    FROM gh_movimiento mov
                        LEFT JOIN gh_detalle_mov gdm ON mov.id_unico = gdm.movimiento
                        LEFT JOIN gh_movimiento aso ON mov.id_unico = aso.asociado
                    WHERE mov.tipo = 3 AND mov.parametrizacionanno = $anno
                    AND mov.fechaFinal >= '$fechaI'
                    AND gdm.espacio = $id
                    AND aso.id_unico IS NULL
                    ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public  function ObtenerRuta($id){
        try {
            $str = "Select ruta
                    FROM gh_espacios_habitables
                    WHERE id_unico = $id";
            $result = $this->mysqli->query($str);
            return $row = $result->fetch_array(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function actualizar(espacioHabitables $data){
        try {
            $str = "UPDATE gh_espacios_habitables SET tipo = $data->tipo, codigo = '$data->codigo', descripcion = '$data->descripcion', dependencia = $data->dependencia, asociado = $data->asociado, ruta = '$data->ruta',estado = $data->estado WHERE md5(id_unico) = '$data->id_unico'";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function eliminar($id){
        try {
            $str = "DELETE FROM gh_espacios_habitables WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}