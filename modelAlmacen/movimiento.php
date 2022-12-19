<?php
require_once ('../Conexion/db.php');
class mov{
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function data_compania($compania){
        try {
            $sql = "SELECT ter.razonsocial, tp.nombre, CONCAT(ter.numeroidentificacion,' - ',ter.digitoverficacion), ter.ruta_logo
                     FROM gf_tercero ter
                     LEFT JOIN gf_tipo_identificacion tp ON tp.id_unico = ter.tipoidentificacion
                     WHERE ter.id_unico = $compania";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function data_movimiento($mov){
        try {
            $sql = "SELECT      mov.id_unico, UPPER(tpm.nombre), mov.numero,
                                CONCAT(
                                    ELT(
                                        WEEKDAY(mov.fecha) + 1,
                                        'Lunes',
                                        'Martes',
                                        'Miercoles',
                                        'Jueves',
                                        'Viernes',
                                        'Sabado',
                                        'Domingo'
                                    )
                                ) AS DIA_SEMANA,
                                DATE_FORMAT(mov.fecha, '%d') as dia,
                                DATE_FORMAT(mov.fecha, '%m') as mes,
                                DATE_FORMAT(mov.fecha, '%Y') as anno,
                                cid.nombre, mov.tipo_doc_sop, mov.numero_doc_sop,
                                IF( CONCAT_WS( ' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS( ' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos )
                                ) AS 'NOMBRE',
                                dpt.nombre, mov.descripcion, mov.observaciones, mov.tipomovimiento,
                                IF( CONCAT_WS( ' ', res.nombreuno, res.nombredos, res.apellidouno, res.apellidodos) = ' ',
                                    res.razonsocial,
                                    CONCAT_WS( ' ', res.nombreuno, res.nombredos, res.apellidouno, res.apellidodos)
                                ) as tercero,
                                tercero2,
                                ctr.nombre
                    FROM      gf_movimiento mov
                    LEFT JOIN gf_tipo_movimiento tpm ON tpm.id_unico = mov.tipomovimiento
                    LEFT JOIN gf_ciudad          cid ON cid.id_unico = mov.lugarentrega
                    LEFT JOIN gf_tercero         ter ON ter.id_unico = mov.tercero
                    LEFT JOIN gf_tercero         res ON res.id_unico = mov.tercero2
                    LEFT JOIN gf_dependencia     dpt ON dpt.id_unico = mov.dependencia
                    LEFT JOIN gf_centro_costo ctr    ON ctr.id_unico = mov.centrocosto
                    WHERE     MD5(mov.id_unico) = '$mov'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function data_detalles($mov){
        try {
            $sql = "SELECT    dtm.id_unico, CONCAT_WS(' ',pni.codi, ' - ', pni.nombre), dtm.cantidad, dtm.valor, dtm.iva
                    FROM      gf_detalle_movimiento dtm
                    LEFT JOIN gf_plan_inventario pni ON pni.id_unico = dtm.planmovimiento
                    WHERE     dtm.movimiento = $mov";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function data_firmas($id){
        try {
            $sql = "SELECT    IF(CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                ter.razonsocial,
                                CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) as nombre,
                                car.nombre,
                                ti.nombre, CONCAT(ter.numeroidentificacion, ' ',ter.digitoverficacion),
                                UPPER(tpr.nombre)
                    FROM      gf_tipo_movimiento tpc
                    LEFT JOIN gf_tipo_documento tpd         ON tpd.id_unico       = tpc.tipo_documento
                    LEFT JOIN gf_responsable_documento doc  ON doc.tipodocumento  = tpc.tipo_documento
                    LEFT JOIN gf_tipo_responsable tpr       ON tpr.id_unico       = doc.tiporesponsable
                    LEFT JOIN gg_tipo_relacion tprl         ON doc.tipo_relacion  = tprl.id_unico
                    LEFT JOIN gf_tercero ter                ON doc.tercero        = ter.id_unico
                    LEFT JOIN gf_cargo_tercero cter         ON cter.tercero       = ter.id_unico
                    LEFT JOIN gf_cargo car                  ON cter.cargo         = car.id_unico
                    LEFT JOIN gf_tipo_identificacion ti     ON ti.id_unico        = ter.tipoidentificacion
                    WHERE     tpc.id_unico = $id
                    ORDER BY  doc.tipodocumento ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function data_asociado($id_mov){
        try {
            $sql = "SELECT DISTINCT mov_a.id_unico,
                                    CONCAT_WS(' ',UPPER(tpm.sigla), 'Nº', mov_a.numero),
                                    CONCAT(
                                        ELT(
                                            WEEKDAY(mov_a.fecha) + 1,
                                            'Lunes',
                                            'Martes',
                                            'Miercoles',
                                            'Jueves',
                                            'Viernes',
                                            'Sabado',
                                            'Domingo'
                                        )
                                    ) AS DIA_SEMANA,
                                    DATE_FORMAT(mov_a.fecha, '%d'),
                                    DATE_FORMAT(mov_a.fecha, '%m'),
                                    DATE_FORMAT(mov_a.fecha, '%Y')
                    FROM            gf_detalle_movimiento dtm
                    LEFT JOIN       gf_detalle_movimiento dta   ON dta.id_unico         = dtm.detalleasociado
                    LEFT JOIN       gf_movimiento         mov_a ON mov_a.id_unico       = dta.movimiento
                    LEFT JOIN       gf_tipo_movimiento    tpm   ON mov_a.tipomovimiento = tpm.id_unico
                    WHERE           dtm.movimiento = $id_mov";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function data_tercero($id_unico){
        try {
            $sql = "SELECT    UPPER(tip.nombre),
                              CONCAT_WS(' ', ter.numeroidentificacion, ter.digitoverficacion) as doc,
                              direccion,
                              valor
                    FROM      gf_tercero ter
                    LEFT JOIN gf_tipo_identificacion tip ON ter.tipoidentificacion = tip.id_unico
                    LEFT JOIN gf_direccion           dir ON dir.tercero            = ter.id_unico
                    LEFT JOIN gf_telefono            tel ON tel.tercero            = ter.id_unico
                    WHERE     ter.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
           die($e->getMessage());
        }
    }

    public function data_producto($detalle){
        try {
            $sql = "SELECT DISTINCT mpr.producto, CONCAT_WS(' - ', pln.codi, UPPER(pln.nombre)) as plan, pre.valor as serie, dtm.valor, dtm.iva
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                    WHERE  mpr.detallemovimiento = $detalle AND pre.fichainventario = 6";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}