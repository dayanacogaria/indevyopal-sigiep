<?php
require_once './modelFactura/recuadoFactura.php';

class RecuadoFacturaController{
    private $recuado;

    public function __construct(){
        $this->recuado = new recaudoFactura();
    }

    public function ReconstruirContable(){
        if(!empty($_REQUEST['idCnt'])){

            $id_pago = $_POST['idPago'];
            $id_cnt  = $_POST['idCnt'];
            $id_ptl  = $_POST['idPptal'];

            $des = $this->recuado->destruirRelacionPago($id_pago);
            $sin = $this->recuado->destruirRelacionAfectadoContable($id_cnt); #Destruimos la relacion del banco
            $del = $this->recuado->eliminarDetalleContabilidad($id_cnt); #eliminamos todos los detalless

            $cuenta_b = $this->recuado->obtnerBanco($id_pago);
            $banco    = $this->recuado->obtnerCuentaBanco($cuenta_b[0]);

            $datosPago = $this->recuado->obtenerDatosPago($id_pago);
            $tercero   = $this->recuado->obntnerTerceroPago($id_pago);
            $xxx = 0;
            foreach ($datosPago as $row_pago) {
                list($id_unico, $detallefactura, $valor, $iva, $impo) = array(
                    $row_pago[0], $row_pago[1], $row_pago[2], $row_pago[3], $row_pago[4]
                );
                $detalle_cnt = $this->recuado->obtnerDatosDetalleFactura($detallefactura);
                $cuenta_d    = $this->recuado->datosDetalleCnt($detalle_cnt[0]);
                $id_dtalle_p = $this->recuado->obnterIdPtal($valor, $id_ptl);
                $nat  = $this->recuado->obnterDatosCuenta($cuenta_d[0]);

                if(!empty($iva)){
                    $valor = $valor + $iva;
                }

                if(!empty($impo)){
                    $valor = $valor + $impo;
                }

                $xxx += $valor;

                $save = $this->recuado->guardarDatosDetalle($valor * -1, $id_cnt, $cuenta_d[0], $nat[0], $tercero[0]);
            }
            $nat_b  = $this->recuado->obnterDatosCuenta($banco[0]);
            $savebanco = $this->recuado->guardarDatosDetalle($xxx, $id_cnt, $banco[0], $nat_b[0], $tercero[0]);

            echo json_encode($savebanco);
        }
    }
}