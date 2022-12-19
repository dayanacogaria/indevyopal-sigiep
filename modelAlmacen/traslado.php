<?php
require_once ('./Conexion/db.php');
class traslado{
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    /**
     * get_detp
     *
     * Función para obtener los valores de la dependencia
     *
     * @author Alexander Numpaque
     * @package Translados Devolutivos
     * @param String $dept hash md5 con el id de la dependencia
     */
    public function get_dept ($dept) {
        try {
            $sql = "SELECT dtp.id_unico, CONCAT(dtp.sigla,' ' ,dtp.nombre) FROM gf_dependencia dtp WHERE md5(dtp.id_unico) = '$dept'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * get_allD
     *
     * Función para consultar las dependencias Direfentes a bodega
     *
     * @author Alexander Numpaque
     * @package Translados Devolutivos
     * @param int $compania Id de la compañia vigente
     * @param String|null $orden Ordenamiento de consulta ASC ó DESC
     */
    public function get_allD ($compania, $orden = NULL){
        try {
            $sql = "SELECT dtp.id_unico, CONCAT(dtp.sigla,' ' ,dtp.nombre) FROM gf_dependencia dtp WHERE dtp.compania = $compania AND dtp.tipodependencia != 1 ORDER BY dtp.nombre $orden";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * get_dift_dept
     *
     * Función para obtener las dependencias diferentes a la dependencia selecccionada
     *
     * @author Alexander Numpaque
     * @package Translados Devolutivos
     * @param String $dept Hash md5 con el id de la dependencia
     * @param int $compania Id de la compañia
     * @param int $type Id de tipo dependencia que no se desea obtener
     */
    public function get_dift_dept ($dept, $compania, $type) {
        try {
            $sql = "SELECT    dtp.id_unico, CONCAT(dtp.sigla,' ' ,dtp.nombre) FROM gf_dependencia dtp
                      WHERE     dtp.compania = $compania
                      AND       dtp.tipodependencia != $type
                      AND       md5(dtp.id_unico) != '$dept'
                      ORDER BY  dtp.nombre ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * get_resDep
     *
     * Función para obtener el tercero respecto aun id y compañia
     *
     * @author Alexander Numpaque
     * @package Translados Devolutivos
     * @param String $id_ter hash md5 del tercero
     * @param int $compania Id de la compañia
     */
    public function get_resDep($id_ter, $compania) {
        try {
            $sql = "SELECT DISTINCT ter.id_unico, IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '' OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL, ter.razonsocial, CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS 'NOMBRE_T',
                                    CONCAT_WS(' ',tpi.nombre, ' ', IF(ter.digitoverficacion = '' OR ter.digitoverficacion IS NULL, ter.numeroidentificacion, CONCAT_WS(' ', ter.numeroidentificacion, ' - ',ter.digitoverficacion))) AS 'NUM_IDENT'
                    FROM            gf_dependencia_responsable dpr
                    LEFT JOIN       gf_dependencia dpt            ON dpt.id_unico = dpr.dependencia
                    LEFT JOIN       gf_tercero ter                ON ter.id_unico = dpr.responsable
                    LEFT JOIN       gf_tipo_identificacion tpi    ON tpi.id_unico = ter.tipoidentificacion
                    WHERE           md5(ter.id_unico) = '$id_ter' AND dpt.compania = $compania";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * get_resDep_Diff
     *
     * Función para obtener los terceros respecto a una dependencia y una compañia siendo el tercero diferente al enviado
     *
     * @author Alexander Numpaque
     * @package Translados Devolutivos
     * @param $id_ter
     * @param $id_dept
     * @param $compania
     */
    public function get_resDep_Diff ($id_ter, $id_dept, $compania) {
        try {
            $sql = "SELECT DISTINCT ter.id_unico, IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '' OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL, ter.razonsocial, CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) AS 'NOMBRE_T',
                                    CONCAT_WS(' ',tpi.nombre, ' ', IF(ter.digitoverficacion = '' OR ter.digitoverficacion IS NULL, ter.numeroidentificacion, CONCAT_WS(' ', ter.numeroidentificacion, ' - ',ter.digitoverficacion))) AS 'NUM_IDENT'
                    FROM            gf_dependencia_responsable dpr
                    LEFT JOIN       gf_dependencia dpt          ON dpt.id_unico = dpr.dependencia
                    LEFT JOIN       gf_tercero ter              ON ter.id_unico = dpr.responsable
                    LEFT JOIN       gf_tipo_identificacion tpi  ON tpi.id_unico = ter.tipoidentificacion
                    WHERE           md5(dpr.dependencia) = '$id_dept'
                    AND             dpt.compania = $compania
                    AND             md5(dpr.responsable) != '$id_ter'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function tipoTraslado($compania){
        try {
            $sql = "SELECT tpm.id_unico, UPPER(tpm.sigla), tpm.nombre
                     FROM   gf_tipo_movimiento tpm
                     WHERE  tpm.clase = 5 AND tpm.compania = $compania";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function generarConsecutivo($tipo, $param, $compania){
        try {
            $xxx = 0;
            $sql = "SELECT MAX(numero) FROM gf_movimiento WHERE tipomovimiento = $tipo AND parametrizacionanno = $param AND compania = $compania ORDER BY id_unico DESC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            if(empty($row[0])){
                $xxx = date('Y').'000001';
            }else{
                $xxx = $row[0] + 1;
            }

            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterMovsB($compania, $param){
        try {
            $sql = "SELECT      mov.id_unico,CONCAT(tpm.sigla,' ',mov.numero,' ',DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                    CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
                    FROM        gf_movimiento mov
                    LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                    WHERE       tpm.clase IN (5)
                    AND         mov.compania = $compania
                    AND         mov.parametrizacionanno = $param
                    ORDER BY    mov.tipomovimiento, mov.numero DESC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function valorAcumMov($mov){
        try {
            $xxx = 0;
            $sql = "SELECT (dtm.valor) FROM gf_detalle_movimiento dtm WHERE dtm.movimiento = $mov";
            $res = $this->mysqli->query($sql);
            while ($row_r = $res->fetch_row()) {
                $xxx += $row_r[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerProductos(){
        try {
            $sql = "SELECT DISTINCT producto FROM gf_movimiento_producto";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterMaxPro($pro){
        try {
            $sql = "SELECT MAX(detallemovimiento) FROM gf_movimiento_producto WHERE producto = $pro";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerMovProF($detalle, $producto, $deptOrg, $resOrg){
        try {
            $sql ="SELECT    dtm.id_unico  'ID',       pes.valor   'PLACA',  dtm.valor     'VALOR',    pro.descripcion 'DESCRIPCION',
                              tpm.sigla     'TIPO_MOV', mov.numero  'N_MOV',  pro.id_unico  'PRODUCTO', fch.descripcion 'NFICHA',
                              plan.nombre nom_pro, plan.codi as plnc
                    FROM      gf_detalle_movimiento dtm
                    LEFT JOIN gf_movimiento_producto mop      ON dtm.id_unico         = mop.detallemovimiento
                    LEFT JOIN gf_movimiento mov               ON mov.id_unico         = dtm.movimiento
                    LEFT JOIN gf_producto pro                 ON pro.id_unico         = mop.producto
                    LEFT JOIN gf_producto_especificacion pes  ON pro.id_unico         = pes.producto
                    LEFT JOIN gf_ficha_inventario fin         ON pes.fichainventario  = fin.id_unico
                    LEFT JOIN gf_elemento_ficha elm           ON fin.elementoficha    = elm.id_unico
                    LEFT JOIN gf_tipo_movimiento tpm          ON mov.tipomovimiento   = tpm.id_unico
                    LEFT JOIN gf_ficha fch                    ON fin.ficha            = fch.id_unico
                    LEFT JOIN gf_plan_inventario plan         ON dtm.planmovimiento   = plan.id_unico
                    WHERE     dtm.id_unico          = $detalle
                    AND       pro.id_unico          = $producto
                    AND       fin.elementoficha     = 6
                    AND       md5(mov.dependencia)  = '$deptOrg'
                    AND       md5(mov.tercero)      = '$resOrg'
                    AND       (pro.baja IS NULL OR pro.baja = 0)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTraslado($id_unico){
        try {
            $sql = "SELECT mov.id_unico,
                           date_format(mov.fecha, '%d/%m/%Y') as fecha,
                           mov.numero as  numero,
                           mov.dependencia,
                           tpm.sigla,
                           tpm.nombre as tpnom,
                           dep.sigla as cdep,
                           dep.nombre as depnom,
                           IF(
                              concat_ws(' ', ter.nombreuno, ter.nombredos, apellidouno, apellidodos) = ' ',
                              ter.razonsocial,
                              concat_ws(' ', ter.nombreuno, ter.nombredos, apellidouno, apellidodos)
                           ) as ternom,
                           IF(
                              ter.digitoverficacion = ' ',
                              ter.numeroidentificacion,
                              concat_ws(' - ', ter.numeroidentificacion, ter.digitoverficacion)
                           ) as doc
                    FROM gf_movimiento as mov
                    LEFT JOIN gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_dependencia     dep ON mov.dependencia    = dep.id_unico
                    LEFT JOIN gf_tercero         ter ON mov.tercero        = ter.id_unico
                    WHERE md5(mov.id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetallesTraslado($mov){
        try {
            $sql = "SELECT    dtm.id_unico, dtm.valor, concat_ws(' ',pln.codi, pln.nombre) as producto, pro.descripcion, pEs.valor, pro.id_unico
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_plan_inventario pln         ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr     ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro     ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_producto_especificacion pEs ON pEs.producto      = pro.id_unico
                    WHERE     md5(dtm.movimiento) = '$mov' AND pEs.fichainventario = 6";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarRelMovP($detalle, $producto){
        try {
            $sql = "DELETE FROM gf_movimiento_producto WHERE detallemovimiento = $detalle AND producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}