<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 25/07/2018
 * Time: 14:08
 */
require_once ('./Conexion/db.php');
class tercero{

    public $idUnico;
    public $nombreUno;
    public $nombreDos;
    public $apellidoUno;
    public $apellidoDos;
    public $razonSocial;
    public $numeroIdentificacion;
    public $digitoVerficacion;
    public $compania;
    public $tipoIdentificacion;
    public $ciudadResidencia;
    public $ciudadIdentificacion;
    public $telefono;
    public $tipoTelefono;
    public $perfil;
    public $representantelegal;
    public $fecha;
    public $direccion;
    public $email;
    private $mysqli;

    public function getIdUnico(){
        return $this->idUnico;
    }

    public function setIdUnico($idUnico){
        $this->idUnico = $idUnico;
    }

    public function getNombreUno(){
        return $this->nombreUno;
    }

    public function setNombreUno($nombreUno){
        $this->nombreUno = $nombreUno;
    }

    public function getNombreDos(){
        return $this->nombreDos;
    }

    public function setNombreDos($nombreDos){
        $this->nombreDos = $nombreDos;
    }

    public function getApellidoUno(){
        return $this->apellidoUno;
    }

    public function setApellidoUno($apellidoUno){
        $this->apellidoUno = $apellidoUno;
    }

    public function getApellidoDos(){
        return $this->apellidoDos;
    }

    public function setApellidoDos($apellidoDos){
        $this->apellidoDos = $apellidoDos;
    }

    public function getRazonSocial(){
        return $this->razonSocial;
    }

    public function setRazonSocial($razonSocial){
        $this->razonSocial = $razonSocial;
    }

    public function getNumeroIdentificacion(){
        return $this->numeroIdentificacion;
    }

    public function setNumeroIdentificacion($numeroIdentificacion){
        $this->numeroIdentificacion = $numeroIdentificacion;
    }

    public function getDigitoVerficacion(){
        return $this->digitoVerficacion;
    }

    public function setDigitoVerficacion($digitoVerficacion){
        $this->digitoVerficacion = $digitoVerficacion;
    }

    public function getCompania(){
        return $this->compania;
    }

    public function setCompania($compania){
        $this->compania = $compania;
    }

    public function getTipoIdentificacion(){
        return $this->tipoIdentificacion;
    }

    public function setTipoIdentificacion($tipoIdentificacion){
        $this->tipoIdentificacion = $tipoIdentificacion;
    }

    public function getCiudadResidencia(){
        return $this->ciudadResidencia;
    }

    public function setCiudadResidencia($ciudadResidencia){
        $this->ciudadResidencia = $ciudadResidencia;
    }

    public function getCiudadIdentificacion(){
        return $this->ciudadIdentificacion;
    }

    public function setCiudadIdentificacion($ciudadIdentificacion){
        $this->ciudadIdentificacion = $ciudadIdentificacion;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function getTipoTelefono(){
        return $this->tipoTelefono;
    }

    public function setTipoTelefono($tipoTelefono){
        $this->tipoTelefono = $tipoTelefono;
    }

    public function getPerfil(){
        return $this->perfil;
    }

    public function setPerfil($perfil){
        $this->perfil = $perfil;
    }

    public function getRepresentantelegal(){
        return $this->representantelegal;
    }

    public function setRepresentantelegal($representantelegal){
        $this->representantelegal = $representantelegal;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function setFecha($fecha){
        $this->fecha = $fecha;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function setDireccion($direccion){
        $this->direccion = $direccion;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtenerTipoIdentificacion(){
        try{
            $str = "SELECT id_unico, nombre FROM gf_tipo_identificacion ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerDepartamentos(){
        try{
            $str = "SELECT id_unico, nombre, rss FROM gf_departamento ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerDepartamentosDiff($id){
        try{
            $str = "SELECT id_unico, nombre, rss FROM gf_departamento WHERE id_unico != $id ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }
    public function obtenerCiudades($depto){
        try {
            $str = "SELECT id_unico, UPPER(nombre), rss FROM gf_ciudad WHERE departamento = $depto ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCiudad($id){
        try {
            $str = "SELECT    ciu.id_unico, ciu.nombre as nomciu, dep.id_unico, dep.nombre as nomdep
                    FROM      gf_ciudad       as ciu
                    LEFT JOIN gf_departamento as dep on ciu.departamento = dep.id_unico
                    WHERE     ciu.id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCiudadesDiff($depto, $id){
        try {
            $str = "SELECT id_unico, nombre, rss FROM gf_ciudad WHERE departamento = $depto AND id_unico != $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoIdentificacionId($id){
        try{
            $str = "SELECT id_unico, nombre FROM gf_tipo_identificacion WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            return $this->dao->buscar_fila($res);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerTipoIdentificacionDiffId($id){
        try{
            $str = "SELECT id_unico, nombre FROM gf_tipo_identificacion WHERE id_unico != $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerListado($perfil, $compania){
        try{
            $str = "SELECT    ter.id_unico as id, CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) as nomter,
                              ter.tipoidentificacion as idTipoIdent, tip.nombre as tipnombre, ter.numeroidentificacion as numident, ter.digitoverficacion  as digito, ter.ciudadresidencia as IdCiudadRes,
                              cid.nombre as ciuResd, cid.departamento as IdDepto, dep.nombre as depto, tpt.nombre as tipoTel,
                              tel.valor as telefono, ter.ciudadidentificacion as idIdent, cdi.nombre as ciudadIdent, der.id_unico as deptr, der.nombre as nomdepRe
                    FROM      gf_tercero             as ter
                    LEFT JOIN gf_tipo_identificacion as tip on ter.tipoidentificacion   = tip.id_unico
                    LEFT JOIN gf_ciudad              as cid on ter.ciudadresidencia     = cid.id_unico
                    LEFT JOIN gf_departamento        as dep on cid.departamento         = dep.id_unico
                    LEFT JOIN gf_telefono            as tel on ter.id_unico             = tel.tercero
                    LEFT JOIN gf_tipo_telefono       as tpt on tel.tipo_telefono        = tpt.id_unico
                    LEFT JOIN gf_perfil_tercero      as prt on ter.id_unico             = prt.tercero
                    LEFT JOIN gf_ciudad              as cdi on ter.ciudadidentificacion = cdi.id_unico
                    LEFT JOIN gf_departamento        as der on cdi.departamento         = der.id_unico
                    WHERE     tpt.id_unico = 2
                    AND       prt.perfil   = $perfil
                    AND       ter.compania = $compania";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtener($perfil, $id){
        try{
            $str = "SELECT    ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.tipoidentificacion as idTipoIdent,
                              tip.nombre as tipnombre, ter.digitoverficacion  as digito, ter.ciudadresidencia as IdCiudadRes,
                              cid.nombre as ciuResd, cid.departamento as IdDepto, dep.nombre as depto, tel.valor as telefono,
                              ter.ciudadidentificacion as idIdent, cdi.nombre as ciudadIdent, der.id_unico as deptr, der.nombre as nomdepRe, ter.numeroidentificacion,
                              DATE_FORMAT(ter.fecha_nacimiento, '%d/%m/%Y') as fecha, dir.direccion, ter.representantelegal, ter.email
                    FROM      gf_tercero             as ter
                    LEFT JOIN gf_tipo_identificacion as tip on ter.tipoidentificacion   = tip.id_unico
                    LEFT JOIN gf_ciudad              as cid on ter.ciudadresidencia     = cid.id_unico
                    LEFT JOIN gf_departamento        as dep on cid.departamento         = dep.id_unico
                    LEFT JOIN gf_telefono            as tel on ter.id_unico             = tel.tercero
                    LEFT JOIN gf_tipo_telefono       as tpt on tel.tipo_telefono        = tpt.id_unico
                    LEFT JOIN gf_perfil_tercero      as prt on ter.id_unico             = prt.tercero
                    LEFT JOIN gf_ciudad              as cdi on ter.ciudadidentificacion = cdi.id_unico
                    LEFT JOIN gf_departamento        as der on cdi.departamento         = der.id_unico
                    LEFT JOIN gf_direccion           as dir on ter.id_unico             = dir.tercero
                    WHERE     tpt.id_unico      = 2
                    AND       prt.perfil        = $perfil
                    AND       md5(ter.id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerDataTerceroIdent($num){
        try{
            $str = "SELECT    ter.id_unico, CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) as nomter, ter.razonsocial,
                              ter.tipoidentificacion as idTipoIdent, tip.nombre as tipnombre, ter.digitoverficacion  as digito, ter.ciudadresidencia as IdCiudadRes,
                              cid.nombre as ciuResd, cid.departamento as IdDepto, dep.nombre as depto, tpt.nombre as tipoTel, tel.valor as telefono,
                              ter.ciudadidentificacion as idIdent, cdi.nombre as ciudadIdent, der.id_unico as deptr, der.nombre as nomdepRe, ter.representantelegal
                    FROM      gf_tercero             as ter
                    LEFT JOIN gf_tipo_identificacion as tip on ter.tipoidentificacion   = tip.id_unico
                    LEFT JOIN gf_ciudad              as cid on ter.ciudadresidencia     = cid.id_unico
                    LEFT JOIN gf_departamento        as dep on cid.departamento         = dep.id_unico
                    LEFT JOIN gf_telefono            as tel on ter.id_unico             = tel.tercero
                    LEFT JOIN gf_tipo_telefono       as tpt on tel.tipo_telefono        = tpt.id_unico
                    LEFT JOIN gf_perfil_tercero      as prt on ter.id_unico             = prt.tercero
                    LEFT JOIN gf_ciudad              as cdi on ter.ciudadidentificacion = cdi.id_unico
                    LEFT JOIN gf_departamento        as der on cdi.departamento         = der.id_unico
                    WHERE     ter.numeroidentificacion = '$num'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Metodo para guardar la información de tercero
     *
     * guardar
     *
     * @param string $nombreuno
     * @param string $nombredos
     * @param string $apellidouno
     * @param string $apellidodos
     * @param string $razonsocial
     * @param int $numero
     * @param int $digito
     * @param int $compania
     * @param int $tipo
     * @param int $representante
     * @param int $ciudad
     * @return bool|mysqli_result|string
     */
    public function guardar($nombreuno, $nombredos, $apellidouno, $apellidodos, $razonsocial, $numero, $digito, $compania, $tipo, $representante, $ciudad, $nomComercial, $email){
        try{
           $str = "INSERT INTO gf_tercero(
                                  nombreuno, nombredos, apellidouno, apellidodos, razonsocial, numeroidentificacion,
                                  digitoverficacion, compania, tipoidentificacion, representantelegal, ciudadresidencia,
                                  ciudadidentificacion, nombre_comercial, email
                                ) VALUES(
                                  '$nombreuno', '$nombredos', '$apellidouno', '$apellidodos', '$razonsocial', $numero, $digito,
                                  $compania, $tipo, $representante, $ciudad, $ciudad, '$nomComercial', '$email'
                                )";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function guardarTelefono($tipo, $valor, $tercero){
        try{
            $str = "INSERT INTO gf_telefono(tipo_telefono, valor, tercero) VALUES($tipo, '$valor', $tercero) ";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function guardarPerfil($perfil, $tercero){
        try{
            $str = "INSERT INTO gf_perfil_tercero(perfil, tercero) VALUES ($perfil, $tercero)";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function eliminar($id){
        try{
            $str = "DELETE FROM gf_tercero WHERE id_unico = $id";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function eliminarPerfil($tercero, $perfil){
        try{
            $str = "DELETE FROM gf_perfil_tercero WHERE tercero = $tercero AND perfil = $perfil";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function eliminarTelefono($tercero, $tipo){
        try{
            $str = "DELETE FROM gf_telefono WHERE tercero = $tercero AND tipo_telefono = $tipo";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerTerceros(){
        try {
            $str = "SELECT    ter.id_unico,
                              ( IF(
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                  ter.razonsocial,
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                )
                              ) AS Nom,
                              CONCAT_WS(' ', tip.nombre, ter.numeroidentificacion, ter.digitoverficacion) AS num
                    FROM      gf_tercero AS ter
                    LEFT JOIN gf_tipo_identificacion AS tip ON ter.tipoidentificacion = tip.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardarDireccion($direccion, $tipo, $ciudad, $tercero){
        try {
            $str = "INSERT INTO gf_direccion (direccion, tipo_direccion, ciudad_direccion, tercero) VALUES('$direccion', $tipo, $ciudad, $tercero)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarDireccion($id){
        try {
            $str = "DELETE FROM gf_direccion WHERE tercero = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoRazones($perfil, $compania){
        try {
            $str = "SELECT    ter.id_unico, ter.razonsocial, tpi.nombre, ter.numeroidentificacion, ter.digitoverficacion, cdr.nombre, cdi.nombre, tel.valor
                    FROM      gf_tercero AS ter
                    LEFT JOIN gf_tipo_identificacion AS tpi ON ter.tipoidentificacion   = tpi.id_unico
                    LEFT JOIN gf_perfil_tercero      AS prt ON prt.tercero              = ter.id_unico
                    LEFT JOIN gf_ciudad              AS cdr ON ter.ciudadresidencia     = cdr.id_unico
                    LEFT JOIN gf_ciudad              AS cdi ON ter.ciudadidentificacion = cdi.id_unico
                    LEFT JOIN gf_telefono            AS tel ON ter.id_unico             = tel.tercero
                    WHERE     prt.perfil   = $perfil
                    AND       ter.compania = $compania";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function carcularDigito($numero){
        list($x, $y) = array(0, 0);
        $z           = strlen($numero);
        $arreglo     = array();
        $arreglo[1]  = 3;
        $arreglo[2]  = 7;
        $arreglo[3]  = 13;
        $arreglo[4]  = 17;
        $arreglo[5]  = 19;
        $arreglo[6]  = 23;
        $arreglo[7]  = 29;
        $arreglo[8]  = 37;
        $arreglo[9]  = 41;
        $arreglo[10] = 43;
        $arreglo[11] = 47;
        $arreglo[12] = 53;
        $arreglo[13] = 59;
        $arreglo[14] = 67;
        $arreglo[15] = 71;

        for ($i = 0; $i < $z; $i++){
            $y  = substr($numero, $i, 1);
            $x += ($y * $arreglo[$z - $i]);
        }

        $y = $x % 11;

        if($y > 1){
            $dig = 11 - $y;
        }else{
            $dig = $y;
        }

        return $dig;
    }

    public function obtenerDiffTerPerfil($perfil, $id){
        try{
            $str = "SELECT    ter.id_unico,
                              (
                                IF(
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                  ter.razonsocial,
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                )
                              )
                              , CONCAT_WS( ' ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion)
                    FROM      gf_tercero             AS ter
                    LEFT JOIN gf_tipo_identificacion AS tip on ter.tipoidentificacion   = tip.id_unico
                    LEFT JOIN gf_perfil_tercero      AS prt on ter.id_unico             = prt.tercero
                    WHERE     prt.perfil       IN ($perfil)
                    AND       ter.id_unico     != $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }
}