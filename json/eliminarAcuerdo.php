<?php
session_start();
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
$id     = $_GET['id'];
$con    = new ConexionPDO();
$result = false;

switch($_REQUEST['action']){
    #* Eliminar acuerdo
    case (1):
        #Datos Acuerdo 
        $da = $con->Listar("SELECT a.tipo, da.soportedeuda, p.id_unico, c.id_unico 
            FROM ga_acuerdo a 
            LEFT JOIN ga_documento_acuerdo da ON a.id_unico = da.acuerdo 
            LEFT JOIN gr_factura_predial fp ON fp.numero = da.soportedeuda 
            LEFT JOIN gp_predio1 p ON fp.predio = p.id_unico 
            LEFT JOIN gc_declaracion d ON d.cod_dec = da.soportedeuda 
            LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
            WHERE a.id_unico = $id");
        if($da[0][0]==1){
            $sql_cons ="UPDATE `gp_predio1` 
                    SET `estado`=:estado 
                    WHERE `id_unico`=:id_unico";
            $sql_dato = array(
                array(":estado",2),
                array(":id_unico",$da[0][2]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);         
        } else {
            $sql_cons ="UPDATE `gc_contribuyente` 
                    SET `estado`=:estado 
                    WHERE `id_unico`=:id_unico";
            $sql_dato = array(
                array(":estado",1),
                array(":id_unico",$da[0][3]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        if(empty($resp)){
            $sql = "DELETE FROM ga_detalle_acuerdo WHERE acuerdo = $id";
            $result = $mysqli->query($sql);
            $sql = "DELETE FROM ga_documento_acuerdo WHERE acuerdo = $id";
            $result = $mysqli->query($sql);
            $sql = "DELETE FROM ga_acuerdo WHERE id_unico = $id";
            $result = $mysqli->query($sql);
        }
        echo json_encode($result);
    break;
    #* Eliminar Recaudo
    case (2):
        $rta  = false;
        $tipo = $_REQUEST['tipo'];
        #*Predial
        if($tipo==1){
            $pago = $_REQUEST['id'];
            #* Buscar CNT 
            $cnt = $con->Listar("SELECT DISTINCT dc.comprobante 
            FROM gr_detalle_pago_predial dpp 
            LEFT JOIN gf_detalle_comprobante dc ON dpp.detallecomprobante = dc.id_unico 
            WHERE dpp.pago = $pago ");
            $id_cnt = $cnt[0][0];
            #Eliminar Detalle Comprobante 
            $d = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gf_detalle_comprobante WHERE comprobante =$id_cnt");
            $det = $d[0][0]; 
            #** Buscar Comprobantes Homologados
            #*Causacion 
            $cs =$con->Listar("SELECT DISTINCT 
                    dca.comprobante FROM gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_detalle_comprobante dca ON dc.id_unico = dca.detalleafectado 
                WHERE 
                    dc.comprobante = $id_cnt AND dca.detalleafectado IS NOT NULL");
            if(count($cs)>0){
                $id_c = $cs[0][0];
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_c");
                if(empty($dl)){
                    $e +=1;
                }
            }
            #*pptal
            $cp = $con->Listar("SELECT DISTINCT 
                        dp.comprobantepptal FROM gf_detalle_comprobante dc 
                LEFT JOIN 
                        gf_detalle_comprobante_pptal dp ON dp.id_unico = dc.detallecomprobantepptal
                WHERE 
                        dc.comprobante = $id_cnt AND dc.detallecomprobantepptal IS NOT NULL;");
            if($cp>0){
                $id_p = $cp[0][0];
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                if(empty($dl)){
                    $e +=1;
                }
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_p");
                if(empty($dl)){
                    $e +=1;
                }
            } else {
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                if(empty($dl)){
                    $e +=1;
                }
            }
            #* Actualizar Detales factura 
            $sqle = "UPDATE gr_detalle_pago_predial dpp 
                LEFT JOIN ga_detalle_factura df ON dpp.id_unico = df.iddetallerecaudo 
                SET df.iddetallerecaudo = NULL 
                WHERE dpp.pago = $pago";
            $upd = $mysqli->query($sqle);
            
            
            #Eliminar Detalle Pago 
            $sql = "DELETE FROM gr_detalle_pago_predial WHERE pago = $pago";
            $result = $mysqli->query($sql);
            $sql = "DELETE FROM gr_pago_predial WHERE id_unico  = $pago";
            $result = $mysqli->query($sql);
            if($result==true){
                $rta = true;
            }
        }
        echo $rta;
    break;
    #* Eliminar Factura Acuerdo
    case (3):
        $rta    = false;
        $id     = $_REQUEST['id'];
        $sql    = "DELETE FROM ga_detalle_factura WHERE factura = $id";
        $result = $mysqli->query($sql);
        $sql    = "DELETE FROM ga_factura_acuerdo WHERE id_unico  = $id";
        $result = $mysqli->query($sql);
        if($result==true){
            $rta = true;
        }
        echo $rta;
    break;
}