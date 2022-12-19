<?php
require_once ('./Conexion/db.php');

class movimiento{

    public $id_unico;
    public $tipo;
    public $numero;
    public $fechaInicio;
    public $fechaFinal;
    public $tercero;
    public $numAdultos;
    public $numPeques;
    public $numHabitaciones;
    public $observaciones;
    public $fecha;
    public $fechaCancelacion;
    public $estado;
    public $formaPago;
    public $responsable;
    public $asociado;
    public $parametrizacionanno;
    public $fechaE;
    public $tipo_tarifa;
    public $descuento;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function getId_unico() {
        return $this->id_unico;
    }

    public function setId_unico($id_unico) {
        $this->id_unico = $id_unico;
        return $this;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }

    public function getNumero(){
        return $this->numero;
    }

    public function setNumero($numero){
        $this->numero = $numero;
    }

    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
        return $this;
    }

    public function getFechaFinal() {
        return $this->fechaFinal;
    }

    public function setFechaFinal($fechaFinal) {
        $this->fechaFinal = $fechaFinal;
        return $this;
    }

    public function getTercero() {
        return $this->tercero;
    }

    public function setTercero($tercero) {
        $this->tercero = $tercero;
        return $this;
    }

    public function getNumAdultos() {
        return $this->numAdultos;
    }

    public function setNumAdultos($numAdultos) {
        $this->numAdultos = $numAdultos;
        return $this;
    }

    public function getNumPeques() {
        return $this->numPeques;
    }

    public function setNumPeques($numPeques) {
        $this->numPeques = $numPeques;
        return $this;
    }

    public function getNumHabitaciones() {
        return $this->numHabitaciones;
    }

    public function setNumHabitaciones($numHabitaciones) {
        $this->numHabitaciones = $numHabitaciones;
        return $this;
    }

    public function getObservaciones() {
        return $this->observaciones;
    }

    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
        return $this;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
        return $this;
    }

    public function getFechaCancelacion() {
        return $this->fechaCancelacion;
    }

    public function setFechaCancelacion($fechaCancelacion) {
        $this->fechaCancelacion = $fechaCancelacion;
        return $this;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
        return $this;
    }

    public function getFormaPago() {
        return $this->formaPago;
    }

    public function setFormaPago($formaPago) {
        $this->formaPago = $formaPago;
        return $this;
    }

    public function getResponsable() {
        return $this->responsable;
    }

    public function setResponsable($responsable) {
        $this->responsable = $responsable;
        return $this;
    }

    public function getParametrizacionanno(){
        return $this->parametrizacionanno;
    }

    public function setParametrizacionanno($parametrizacionanno){
        $this->parametrizacionanno = $parametrizacionanno;
    }

    public function getAsociado() {
        return $this->asociado;
    }

    public function setAsociado($asociado) {
        $this->asociado = $asociado;
        return $this;
    }

    public function getFechaE(){
        return $this->fechaE;
    }

    public function setFechaE($fechaE){
        $this->fechaE = $fechaE;
    }

    public function getTipoTarifa(){
        return $this->tipo_tarifa;
    }

    public function setTipoTarifa($tipo_tarifa){
        $this->tipo_tarifa = $tipo_tarifa;
    }

    public function getDescuento(){
        return $this->descuento;
    }

    public function setDescuento($descuento){
        $this->descuento = $descuento;
    }

    public function  obtenerTipoIdentificacion(){
        try{
            $str = "SELECT id_unico, nombre FROM gf_tipo_identificacion ORDER BY nombre ASC";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerDepartamentos(){
        try{
            $str = "SELECT id_unico, nombre, rss FROM gf_departamento ORDER BY nombre ASC";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public  function obtenerTodo($clase){
        try {
            $str = "SELECT      mov.id_unico as id, CONCAT_WS(' ', tpm.nombre, mov.numero) as tipo,
                                (
                                    IF(
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                        ter.razonsocial,
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                    )
                                ) as tercero,
                                DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(fechaCancelacion, '%d/%m/%Y') as fechaCancelacion,
                                est.nombre as estado,
                                mov.descuento
                    FROM        gh_movimiento as mov
                    LEFT JOIN   gh_tipo_mov   as tpm ON mov.tipo        = tpm.id_unico
                    LEFT JOIN   gf_tercero    as ter ON mov.tercero     = ter.id_unico
                    LEFT JOIN   gh_estado_mov as est ON mov.estado      = est.id_unico
                    WHERE       tpm.clase = $clase";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallesMovDependencia($dep){
        try {
            $str = "SELECT    dtm.id_unico, pln.nombre, SUM(dtm.cantidad)
                    FROM      gf_movimiento         as mov
                    LEFT JOIN gf_detalle_movimiento as dtm ON dtm.movimiento      = mov.id_unico
                    LEFT JOIN gf_plan_inventario    as pln ON dtm.planmovimiento  = pln.id_unico
                    WHERE     mov.dependencia = $dep
                    AND       pln.xCantidad   = 1
                    GROUP BY  pln.id_unico";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerMovClaseVadFecha($clase){
        try {
            $str = "SELECT      mov.id_unico as id, tpm.nombre as tipo,
                                (
                                    IF(
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                        ter.razonsocial,
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                    )
                                ) as tercero,
                                DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(mov.fechaCancelacion, '%d/%m/%Y') as fechaCancelacion,
                                est.nombre as estado
                    FROM        gh_movimiento as mov
                    LEFT JOIN   gh_tipo_mov   as tpm ON mov.tipo        = tpm.id_unico
                    LEFT JOIN   gf_tercero    as ter ON mov.tercero     = ter.id_unico
                    LEFT JOIN   gh_estado_mov as est ON mov.estado      = est.id_unico
                    LEFT JOIN   gh_movimiento as aso ON aso.asociado    = mov.id_unico
                    WHERE       tpm.clase    = $clase
                    AND         aso.asociado IS NULL";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoMovClase($clase){
        try {
            $str = "SELECT id_unico, nombre FROM gh_tipo_mov WHERE clase = $clase";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposEspacio(){
        try {
            $str = "SELECT id_unico, nombre FROM gh_tipo_espacio";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerEspaciosH($tipo){
        try {
            $str = "SELECT id_unico, codigo, descripcion FROM gh_espacios_habitables WHERE tipo = $tipo ORDER BY codigo ASC";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoMovClDiff($clase, $id){
        try {
            $str = "SELECT id_unico, nombre FROM gh_tipo_mov WHERE clase = $clase AND id_unico != $id";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function buscarMovimientosEspacioFecha($id, $fecha, $tipo){
        try {
            $str = "SELECT    dtm.id_unico, dtm.espacio
                    FROM      gh_detalle_mov as dtm
                    LEFT JOIN gh_movimiento  as mov on dtm.movimiento = mov.id_unico
                    WHERE     mov.tipo        = $tipo
                    AND       dtm.espacio     = $id
                    AND       DATE_FORMAT(mov.fechaFinal, '%Y-%m-%d') >= '$fecha'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerEspaciosHijos($padre, $clase){
        try {
            $str = "SELECT    sph.id_unico, sph.codigo, sph.dependencia, tpe.nombre as tpNom, sph.ruta
                    FROM      gh_espacios_habitables as sph
                    LEFT JOIN gh_tipo_espacio        as tpe ON sph.tipo = tpe.id_unico
                    WHERE     md5(sph.asociado) = '$padre'
                    AND       tpe.clase IN ($clase)
                    ORDER BY  sph.codigo ASC";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerEspacios($clase){
        try {
            $str = "SELECT    sph.id_unico, sph.codigo, sph.dependencia, tpe.nombre as tpNom
                    FROM      gh_espacios_habitables as sph
                    LEFT JOIN gh_tipo_espacio        as tpe ON sph.tipo = tpe.id_unico
                    WHERE     tpe.clase IN ($clase)
                    ORDER BY  sph.codigo ASC";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerPisoCabana(){
        try {
            $str = "SELECT    sph.id_unico, CONCAT_WS(' ', sph.codigo, sph.descripcion)
                    FROM      gh_espacios_habitables as sph
                    LEFT JOIN gh_tipo_espacio        as tpe ON sph.tipo = tpe.id_unico
                    WHERE     tpe.clase IN (2, 10)
                    ORDER BY  sph.tipo ASC, sph.codigo ASC";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function obtenerClaseEspacioMov($espacio, $fecha){
        try {
            $str = "SELECT    MAX(mov.id_unico)
                    FROM      gh_detalle_mov as dtm
                    LEFT JOIN gh_movimiento  as mov on dtm.movimiento = mov.id_unico
                    LEFT JOIN gh_tipo_mov    as tpm on mov.tipo       = tpm.id_unico
                    WHERE     dtm.espacio  = $espacio
                    AND       DATE_FORMAT(mov.fechaInicio, '%Y-%m-%d') <= '$fecha'
                    AND       DATE_FORMAT(mov.fechaFinal, '%Y-%m-%d') >= '$fecha'
                    ";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovClaseFecha($clase, $fecha){
        try {
            $str = "SELECT    mov.id_unico
                    FROM      gh_detalle_mov as dtm
                    LEFT JOIN gh_movimiento  as mov on dtm.movimiento = mov.id_unico
                    LEFT JOIN gh_tipo_mov    as tpm on mov.tipo       = tpm.id_unico
                    WHERE     (tpm.clase    = $clase)
                    AND       (DATE_FORMAT(mov.fechaInicio, '%Y-%m-%d') <= '$fecha'
                    AND       DATE_FORMAT(mov.fechaFinal, '%Y-%m-%d') >= '$fecha')";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function terceros($perfil){
        try {
            $str = "SELECT      ter.id_unico as id,
                                (IF(
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))
                                ) as nomter,
                                CONCAT_WS(' ', tpi.nombre, ter.numeroidentificacion) as numdoc
                    FROM        gf_tercero             as ter
                    LEFT JOIN   gf_perfil_tercero      as pfl ON pfl.tercero            = ter.id_unico
                    LEFT JOIN   gf_tipo_identificacion as tpi ON ter.tipoidentificacion = tpi.id_unico
                    WHERE       pfl.perfil = $perfil";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function tercerosDiff($perfil, $id){
        try {
            $str = "SELECT      ter.id_unico as id,
                                (IF(
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos))
                                ) as nomter,
                                CONCAT_WS(' ', tpi.nombre, ter.numeroidentificacion) as numdoc
                    FROM        gf_tercero             as ter
                    LEFT JOIN   gf_perfil_tercero      as pfl ON pfl.tercero            = ter.id_unico
                    LEFT JOIN   gf_tipo_identificacion as tpi ON ter.tipoidentificacion = tpi.id_unico
                    WHERE       pfl.perfil    = $perfil
                    AND         ter.id_unico != $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerEstado($id){
        try{
            $str = "SELECT id_unico, nombre FROM gh_estado_mov WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerformaPago(){
        try{
            $str = "SELECT id_unico, nombre FROM gh_forma_pago ORDER BY nombre ASC";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function obtenerformaPagoDiff($id){
        try{
            $str = "SELECT id_unico, nombre FROM gh_forma_pago WHERE id_unico != $id ORDER BY nombre ASC";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public  function obtenerData($id){
        try {
            $str = "SELECT      mov.id_unico as id,
                                mov.tipo,
                                tpm.nombre as tipo,
                                tpm.clase,
                                DATE_FORMAT(fechaInicio, '%d/%m/%Y %H:%i') as fechaInicio,
                                DATE_FORMAT(fechaFinal, '%d/%m/%Y %H:%i')  as fechaFinal,
                                ter.id_unico,
                                (
                                    IF(
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                        ter.razonsocial,
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                    )
                                ) as tercero,
                                CONCAT_WS(' ', tip.nombre, ter.numeroidentificacion) as numdoc,
                                mov.numAdultos as adultos,
                                mov.numPeques as peques,
                                mov.numHabitaciones as habitaciones,
                                mov.observaciones as obs,
                                DATE_FORMAT(fecha, '%d/%m/%Y') as fecha,
                                DATE_FORMAT(fechaCancelacion, '%d/%m/%Y') as fechaCancelacion,
                                est.nombre as estado,
                                (
                                    IF(
                                        CONCAT_WS(' ', res.nombreuno, res.nombredos, res.apellidouno, res.apellidodos) = ' ',
                                        res.razonsocial,
                                        CONCAT_WS(' ', res.nombreuno, res.nombredos, res.apellidouno, res.apellidodos)
                                    )
                                ) as responsable,
                                mov.asociado,
                                mov.formaPago,
                                frm.nombre as nomm,
                                est.id_unico as idsta,
                                mov.tipo_tarifa,
                                tpt.nombre,
                                mov.descuento
                    FROM        gh_movimiento          AS mov
                    LEFT JOIN   gh_tipo_mov            AS tpm ON mov.tipo               = tpm.id_unico
                    LEFT JOIN   gf_tercero             AS ter ON mov.tercero            = ter.id_unico
                    LEFT JOIN   gf_tipo_identificacion AS tip ON ter.tipoidentificacion = tip.id_unico
                    LEFT JOIN   gh_estado_mov          AS est ON mov.estado             = est.id_unico
                    LEFT JOIN   gf_tercero             AS res ON mov.responsable        = mov.responsable
                    LEFT JOIN   gh_forma_pago          AS frm ON mov.formaPago          = frm.id_unico
                    LEFT JOIN   gp_tipo_tarifa         AS tpt ON mov.tipo_tarifa        = tpt.id_unico
                    WHERE       mov.id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtener($id){
        try {
            $str = "SELECT      mov.id_unico as id,
                                mov.tipo,
                                tpm.nombre as tipo,
                                DATE_FORMAT(fechaInicio, '%d/%m/%Y %H:%i') as fechaInicio,
                                DATE_FORMAT(fechaFinal, '%d/%m/%Y %H:%i')  as fechaFinal,
                                ter.id_unico,
                                (
                                    IF(
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                        ter.razonsocial,
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                    )
                                ) as tercero,
                                CONCAT_WS(' ', tip.nombre, ter.numeroidentificacion) as numdoc,
                                mov.numAdultos as adultos,
                                mov.numPeques as peques,
                                mov.numHabitaciones as habitaciones,
                                mov.observaciones as obs,
                                DATE_FORMAT(fecha, '%d/%m/%Y') as fecha,
                                DATE_FORMAT(fechaCancelacion, '%d/%m/%Y') as fechaCancelacion,
                                est.nombre as estado,
                                (
                                    IF(
                                        CONCAT_WS(' ', res.nombreuno, res.nombredos, res.apellidouno, res.apellidodos) = ' ',
                                        res.razonsocial,
                                        CONCAT_WS(' ', res.nombreuno, res.nombredos, res.apellidouno, res.apellidodos)
                                    )
                                ) as responsable,
                                mov.asociado,
                                mov.formaPago,
                                frm.nombre as nomm,
                                est.id_unico as idsta,
                                res.id_unico,
                                DATE_FORMAT(mov.fechaInicio, '%d/%m/%Y') as fechaI,
                                DATE_FORMAT(mov.fechaFinal, '%d/%m/%Y')  as fechaF,
                                mov.numero,
                                mov.tipo_tarifa,
                                tpt.nombre,
                                mov.descuento,
                                DATE_FORMAT(mov.fechaInicio, '%Y-%m-%d'),
                                DATE_FORMAT(mov.fechaFinal, '%Y-%m-%d')
                    FROM        gh_movimiento          as mov
                    LEFT JOIN   gh_tipo_mov            as tpm ON mov.tipo        = tpm.id_unico
                    LEFT JOIN   gf_tercero             as ter ON mov.tercero     = ter.id_unico
                    LEFT JOIN   gf_tipo_identificacion as tip ON ter.tipoidentificacion = tip.id_unico
                    LEFT JOIN   gh_estado_mov          as est ON mov.estado      = est.id_unico
                    LEFT JOIN   gf_tercero             as res ON mov.responsable = mov.responsable
                    LEFT JOIN   gh_forma_pago          as frm on mov.formaPago   = frm.id_unico
                    LEFT JOIN   gp_tipo_tarifa         AS tpt ON mov.tipo_tarifa        = tpt.id_unico
                    WHERE       md5(mov.id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function obtenerEspacio($id){
        try {
            $str = "SELECT    sph.id_unico, tpe.id_unico, tpe.nombre as nomtipo, sph.codigo, sph.descripcion, dpt.id_unico,
                              dpt.nombre, dpt.sigla, sph.asociado
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

    public  function registrar(movimiento $data){
        try {
            date_default_timezone_set('America/Bogota');
            $fecha = date('Y-m-d H:i');
            $str = "INSERT INTO gh_movimiento(
                                  tipo, numero, fechaInicio, fechaFinal, tercero, numAdultos, numPeques, numHabitaciones, observaciones,
                                  fecha, fechaCancelacion, estado, formaPago, responsable, asociado, parametrizacionanno, fechaE, tipo_tarifa, descuento
                    ) VALUES( $data->tipo, '$data->numero', '$data->fechaInicio', '$data->fechaFinal', $data->tercero, $data->numAdultos, $data->numPeques,
                              $data->numHabitaciones, '$data->observaciones', '$data->fecha', '$data->fechaCancelacion', $data->estado, $data->formaPago,
                              $data->responsable, $data->asociado, $data->parametrizacionanno, '$fecha', $data->tipo_tarifa, $data->descuento
                    )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimo($tipo){
        try {
            $str = "SELECT MAX(id_unico) FROM gh_movimiento WHERE tipo = $tipo";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public  function eliminar($id){
        try {
            $str = "DELETE FROM gh_movimiento WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizar($id, $tercero, $fechaC){
        try {
            $str = "UPDATE gh_movimiento SET tercero = $tercero, fechaCancelacion = $fechaC WHERE md5(id_unico) = '$id'";
            return $this->mysqli->query($str);
        } catch (Exception $e) {

        }
    }

    public function guardarDetalle($mov, $espacio, $asociado){
        try {
            $str = "INSERT INTO gh_detalle_mov(movimiento, espacio, asociado) VALUES ($mov, $espacio, $asociado)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoDetalles($mov){
        try {
            $str = "SELECT      dtm.id_unico, CONCAT_WS(' ', sph.codigo, sph.descripcion), dtm.asociado, sph.id_unico
                    FROM        gh_detalle_mov         as dtm
                    LEFT JOIN   gh_espacios_habitables as sph ON dtm.espacio = sph.id_unico
                    WHERE       dtm.movimiento = $mov";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoPersona(){
        try {
            $str = "SELECT id_unico, nombre FROM gh_tipo_persona ORDER BY nombre ASC";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalleMovimiento($detalle){
        try {
            $str = "SELECT    mov.id_unico
                    FROM      gh_detalle_mov  as dtm
                    LEFT JOIN gh_movimiento   as mov ON dtm.movimiento = mov.id_unico
                    WHERE     dtm.id_unico = $detalle";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardarRelacionDetalle($detalle, $tipo, $tercero){
        try {
            $str = "INSERT INTO gh_detalle_persona (detalle, tipo_persona, tercero) VALUES($detalle, $tipo, $tercero)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovEspacio($espacio, $clase){
        try {
            $str = "SELECT    mov.id_unico as movid,
                              tpm.nombre as tipNom,
                              DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha,
                              DATE_FORMAT(mov.fechaInicio, '%d/%m/%Y %H:%i') as fechaI,
                              DATE_FORMAT(mov.fechaFinal, '%d/%m/%Y %H:%i') as fechaF,
                              DATE_FORMAT(mov.fechaCancelacion, '%d/%m/%Y') as fechaC,
                              ter.id_unico,
                              UPPER(
                                IF(
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                  ter.razonsocial,
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                )
                              ) as tercero,
                              UPPER(CONCAT_WS(' ', tpi.nombre, ter.numeroidentificacion)) as numdoc,
                              mov.numAdultos, mov.numPeques, mov.numHabitaciones, mov.descuento
                    FROM      gh_detalle_mov         as dtm
                    LEFT JOIN gh_movimiento          as mov ON dtm.movimiento         = mov.id_unico
                    LEFT JOIN gh_tipo_mov            as tpm ON mov.tipo               = tpm.id_unico
                    LEFT JOIN gf_tercero             as ter ON mov.tercero            = ter.id_unico
                    LEFT JOIN gf_tipo_identificacion as tpi ON ter.tipoidentificacion = tpi.id_unico
                    WHERE     md5(dtm.espacio) = '$espacio'
                    AND       tpm.clase        = $clase
                    ORDER BY  mov.fecha DESC, mov.tipo ASC";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCompania($id){
        try {
            $str = "SELECT UPPER(t.razonsocial), t.numeroidentificacion, t.digitoverficacion, t.ruta_logo FROM gf_tercero t WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarDetalle($id){
        try {
            $str = "DELETE FROM gh_detalle_mov WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturaMovimiento($id){
        try {
            $str = "SELECT  MAX(md5(fat.id_unico)), mov.id_unico, dtc.comprobante, dtp.comprobantepptal
                    FROM      gp_factura                   as fat
                    LEFT JOIN gp_detalle_factura           as gdf on fat.id_unico                = gdf.factura
                    LEFT JOIN gf_detalle_comprobante       as dtc on gdf.detallecomprobante      = dtc.id_unico
                    LEFT JOIN gf_detalle_comprobante_pptal as dtp on dtc.detallecomprobantepptal = dtp.id_unico
                    LEFT JOIN gf_detalle_movimiento        as gdm on gdf.detallemovimiento       = gdm.id_unico
                    LEFT JOIN gf_movimiento                as mov on gdm.movimiento              = mov.id_unico
                    WHERE     md5(fat.mov_hotel) = '$id'
                    AND       fat.estado_factura = 4";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerNumero($tipo, $param){
        try {
            $xxx = 0;
            $str = "SELECT MAX(numero) FROM gh_movimiento WHERE tipo = $tipo AND parametrizacionanno = $param";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
               $row = $res->fetch_row();
               $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerAnnoParam($id){
        try {
            $xxx = 0;
            $str = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function validarConsecutivo($tipo){
        try {
            $param = $_SESSION['anno'];
            $xxx   = movimiento::obtenerNumero($tipo, $param);
            $dta   = movimiento::obtenerAnnoParam($param);
            if(empty($xxx)){
                $numero = $dta.'000001';
            }else{
                $numero = $xxx + 1;
            }
            return $numero;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerPrimerTipoClase($clase){
        try {
            $xxx = 0;
            $str = "SELECT MIN(id_unico) FROM gh_tipo_mov WHERE clase = $clase";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptosEspacio($id){
        try {
            $str = "SELECT    esp.id_concepto, con.nombre, con.id_unico
                    FROM      gph_espacio_habitable_concepto AS esp
                    LEFT JOIN gp_concepto                    AS con ON esp.id_concepto = con.id_unico
                    WHERE     md5(esp.id_espacio_habitable) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTarfiasConcepto($concepto){
        try {
            $str = "SELECT    tar.id_unico, tar.nombre, tar.valor, tpt.nombre
                    FROM      gp_concepto_tarifa AS cpt
                    LEFT JOIN gp_tarifa          AS tar ON cpt.tarifa = tar.id_unico
                    LEFT JOIN gp_tipo_tarifa     AS tpt ON tar.tipo_tarifa = tpt.id_unico
                    WHERE     cpt.concepto = $concepto";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTerceroVarios(){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_tercero WHERE nombreuno = 'Varios'";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ultimoDetalleRegistrado($mov){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gh_detalle_mov WHERE movimiento = $mov";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataDetallePersona($id){
        try {
            $str = "SELECT tipo_persona, tercero FROM gh_detalle_persona WHERE detalle = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCantidadTercerosHabitacion($id){
        try {
            $xxx = 0;
            $str = "SELECT    count(gdp.tercero)
                    FROM      gh_detalle_mov AS gdm
                    LEFT JOIN gh_detalle_persona AS gdp ON gdp.detalle = gdm.id_unico
                    WHERE     md5(gdm.id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerEspaciosMovimiento($mov){
        try {
            $str = "SELECT    gdm.espacio
                    FROM      gh_detalle_mov AS gdm
                    LEFT JOIN gh_movimiento AS gmv ON gdm.movimiento = gmv.id_unico
                    WHERE md5(gmv.id_unico) = '$mov'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTarifaConcepto($concepto, $tipo){
        try {
            $str = "SELECT    gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa = gtr.id_unico
                    WHERE     gct.concepto    = $concepto
                    AND       gtr.tipo_tarifa = $tipo";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerPersonasDetalle($detalle){
        try {
            $str = "SELECT tercero FROM gh_detalle_persona WHERE detalle = $detalle";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarConceptoFactura($mov, $concepto){
        try {
            $xxx = 0;
            $str = "SELECT    gdf.id_unico
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_factura g ON gdf.factura = g.id_unico
                    WHERE     md5(g.mov_hotel)    = '$mov'
                    AND       gdf.concepto_tarifa = $concepto
                    AND       g.estado_factura    IN (4, 5)";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function formatearFecha($fecha, $separador){
        try {
            $xxx = explode($separador, $fecha);
            return "$xxx[2]-$xxx[1]-$xxx[0]";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovimientosFecha($fechaI, $fechaF, $clase){
        try {
            $str = "SELECT    DISTINCT md5(mov.id_unico), CONCAT_WS(' ',gep.codigo, gep.descripcion), DATE_FORMAT(mov.fecha, '%d/%m/%Y'),
                              CONCAT_WS( ' - ', DATE_FORMAT(mov.fechaInicio, '%d/%m/%Y'), DATE_FORMAT(mov.fechaFinal, '%d/%m/%Y'))
                    FROM      gh_movimiento          AS mov
                    LEFT JOIN gh_tipo_mov            AS tpm ON mov.tipo = tpm.id_unico
                    LEFT JOIN gh_detalle_mov         AS gdm ON mov.id_unico = gdm.movimiento
                    LEFT JOIN gh_espacios_habitables AS gep ON gdm.espacio = gep.id_unico
                    WHERE     (mov.fechaInicio >= '$fechaI' AND mov.fechaFinal <= '$fechaF')
                    AND       tpm.clase = $clase";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovimientoAsociado($aso){
        try {
            $str = "SELECT md5(id_unico) FROM gh_movimiento WHERE md5(asociado) = '$aso'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovimientosFacturacion($id){
        try {
            $str = "SELECT     md5(gpf.id_unico), CONCAT_WS(' ', tpf.prefijo, gpf.numero_factura)
                    FROM       gp_factura AS gpf
                    LEFT JOIN  gp_tipo_factura AS tpf ON gpf.tipofactura = tpf.id_unico
                    WHERE      md5(gpf.mov_hotel) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturasMov($mov){
        try {
            $str = "SELECT    gdf.id_unico, gdf.valor_total_ajustado
                    FROM      gp_factura          AS gft
                    LEFT JOIN gp_detalle_factura  AS gdf ON gft.id_unico = gdf.factura
                    WHERE     gft.mov_hotel      = $mov
                    AND       gft.estado_factura IN (4, 5)";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarPagosFactura($dtf){
        try {
            $str = "SELECT valor FROM gp_detalle_pago WHERE detalle_factura = $dtf";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposTarifa(){
        try {
            $str = "SELECT    gtp.id_unico, CONCAT_WS(' ',gtp.nombre, FORMAT(SUM(tar.valor), 0))
                    FROM      gp_tipo_tarifa AS gtp
                    LEFT JOIN gp_tarifa AS tar ON gtp.id_unico = tar.tipo_tarifa
                    GROUP BY  gtp.id_unico
                    ORDER BY  gtp.nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorTarifaConcepto($concepto, $tipoT){
        try {
            $xxx = 0;
            $str = "SELECT    gtr.valor
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa = gtr.id_unico
                    WHERE     gct.concepto    = $concepto
                    AND       gtr.tipo_tarifa = $tipoT";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                foreach ($res->fetch_all(MYSQLI_NUM) as $item){
                    $xxx += $item[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarFecha($fecha, $mov){
        try {
            $str = "UPDATE gh_movimiento SET fechaFinal = 'NULL', fechaCancelacion = '$fecha' WHERE id_unico = $mov";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarDetallesPersonaMov($clase, $fechaI, $fechaF){
        try {
            $str = "SELECT    gmv.numero,
                              UPPER(IF(
                                CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                gtr.razonsocial,
                                CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                              )) AS TER,
                              DATE_FORMAT(gmv.fechaInicio, '%d/%m/%Y %H:%i') AS FECHALL,
                              DATE_FORMAT(gmv.fechaFinal, '%d/%m/%Y %H:%i') AS FECHAF,
                              gmv.fechaInicio, gmv.fechaFinal, gmv.asociado,
                              aso.numero, UPPER(tas.nombre), UPPER(gte.nombre), ges.codigo
                    FROM      gh_detalle_persona     AS gtp
                    LEFT JOIN gh_detalle_mov         AS gdm ON gtp.detalle     = gdm.id_unico
                    LEFT JOIN gh_movimiento          AS gmv ON gdm.movimiento  = gmv.id_unico
                    LEFT JOIN gh_tipo_mov            AS gtm ON gmv.tipo        = gtm.id_unico
                    LEFT JOIN gf_tercero             AS gtr ON gtp.tercero     = gtr.id_unico
                    LEFT JOIN gh_movimiento          AS aso ON gmv.asociado    = aso.id_unico
                    LEFT JOIN gh_tipo_mov            AS tas ON aso.tipo        = tas.id_unico
                    LEFT JOIN gh_espacios_habitables AS ges ON gdm.espacio     = ges.id_unico
                    LEFT JOIN gh_tipo_espacio        AS gte ON ges.tipo        = gte.id_unico
                    WHERE     gtm.clase = $clase
                    AND       gmv.fecha BETWEEN '$fechaI' AND '$fechaF' ";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovFecha($clase, $fechaI, $fechaF){
        try {
            $str = "SELECT  UPPER(gtm.nombre) AS NTIPO, gmv.numero AS NUM, DATE_FORMAT(gmv.fecha, '%d/%m/%Y') AS FECHA,
                                UPPER(
                                  IF(
                                    CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                    gtr.razonsocial,
                                    CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                  )
                                ) AS TER, gmv.id_unico
                        FROM      gh_movimiento AS gmv
                        LEFT JOIN gh_tipo_mov   AS gtm ON gmv.tipo     = gtm.id_unico
                        LEFT JOIN gf_tercero    AS gtr ON gmv.tercero  = gtr.id_unico
                        WHERE     gtm.clase = $clase
                        AND       gmv.fecha BETWEEN '$fechaI' AND '$fechaF' ";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturasMovimiento($mov){
        try {
            $str = "SELECT    fat.id_unico, tpf.prefijo, fat.numero_factura, DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y'),
                              UPPER(
                                  IF(
                                    CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                    gtr.razonsocial,
                                    CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                  )
                                ) AS TER
                    FROM      gp_factura         AS fat
                    LEFT JOIN gp_tipo_factura    AS tpf ON fat.tipofactura = tpf.id_unico
                    LEFT JOIN gf_tercero         AS gtr ON fat.responsable = gtr.id_unico
                    WHERE  mov_hotel = $mov";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallesFactura($factura){
        try {
            $str = "SELECT    dpt.id_unico, gct.nombre, dpt.valor, dpt.iva, dpt.impoconsumo
                    FROM      gp_detalle_factura AS dpt
                    LEFT JOIN gp_concepto AS gct ON dpt.concepto_tarifa = gct.id_unico
                    WHERE     dpt.factura = $factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}