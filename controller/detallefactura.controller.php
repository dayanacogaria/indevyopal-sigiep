<?php
require_once './modelAlmacen/inventario.php';
require_once './modelFactura/detallefactura.php';
require_once './modelFactura/detallecnt.php';
require_once './modelFactura/detallePptal.php';
require_once './modelFactura/rubroFuente.php';
require_once './modelFactura/concepto.php';
require_once './modelFactura/factura.php';
require_once './modelFactura/detallemovnto.php';
require_once './modelFactura/movimiento.php';
require_once './modelFactura/comprobanteContable.php';
class detalleFacturaController {

    private $detalleFactura;
    private $detalleCnt;
    private $detallePptal;
    private $rubroFuente;
    private $concepto;
    private $factura;
    private $dtm;
    private $cpt;
    private $mov;

    public function __construct() {
        $this->detalleFactura = new detallefactura();
        $this->detalleCnt     = new detalleCnt();
        $this->detallePptal   = new detallePptal();
        $this->rubroFuente    = new rubroFuente();
        $this->concepto       = new concepto();
        $this->factura        = new factura();
        $this->dtm            = new detallemovimiento();
        $this->cpt            = new concepto();
        $this->mov            = new movimiento();
    }

    public function registrar() {
        @session_start();
        $param = $_SESSION['anno'];
        $factura          = $_POST['txtIdFactura'];
        $concepto         = $_POST['sltConcepto'];
        $tercero          = $_POST['txtTercero'];
        $centrocosto      = $_POST['txtCentroCosto'];
        $descripcion      = $_POST['txtDescr'];
        $proyecto         = '2147483647';
        $fecha            = $_POST['txtFecha'];
        $valor            = $_POST['txtValorX'];
        $descuento        = $_POST['txtXDescuento'];
        if (empty($_POST['txtCantidad'])) {$cantidad = 1;} else {$cantidad = $_POST['txtCantidad'];}
        if (empty($_POST['txtIva'])) {$iva = 0;} else { $iva = $_POST['txtIva'];}
        if (empty($_POST['txtImpoconsumo'])) {$impoconsumo = 0;} else { $impoconsumo = $_POST['txtImpoconsumo'];}
        if (empty($_POST['txtAjustePeso'])) {$ajustePeso = 0;} else { $ajustePeso = $_POST['txtAjustePeso'];}
        if (empty($_POST['txtValorX'])) {
            if(empty($_POST['txtValor'])){
                $valor = 0;
            } else {
                $valor = $_POST['txtValor'];
            } 
        }else { $valor = $_POST['txtValorX'];}
        
        if(empty($_REQUEST['sltUnidad'])){
            $unidad     ='NULL';
        } else { 
            $unidad     = $_REQUEST['sltUnidad'];
        }
        $valorTotalAjuste = $_POST['txtValorA'];
        
        if(empty($_REQUEST['descripcion'])){
            $descripcion     = 'NULL';
        } else {
            $descripcion     = $_REQUEST['descripcion'];
        }

        $id_detalle_ptal = "NULL";
        $id_pptal = 0;
        $id_cnt   = 0;

        $detalleD  = "NULL";
        $detalleC  = "NULL";
        $detalle_i = "NULL";
        $detalle_x = "NULL";
        $detalle_y = "NULL";
        $conf = $this->concepto->obtnerConceptoFinanciero($concepto,$param);

        if (!empty($_POST['txtIdPptal'])) {
            $dtpp = new detallePptal();

            $id_pptal = $_POST['txtIdPptal'];
            $valordP  = ($valor * $cantidad) + $ajustePeso;

            $dtpp->descripcion      = $descripcion;
            $dtpp->valor            = $valordP;
            $dtpp->comprobantepptal = $id_pptal;
            $dtpp->rubrofuente      = $rubrof;
            $dtpp->conceptorubro    = $con_rub;
            $dtpp->tercero          = $tercero;
            $dtpp->proyecto         = $proyecto;
            $resp = $this->detallePptal->registrar($dtpp);
            if ($resp = true) {
                $id_detalle_ptal = $this->detallePptal->obtnerUltimoRegistroPptal($id_pptal);
            } else {
                $id_detalle_ptal = NULL;
            }
        }
        if (!empty($con_rub)) {
            if (!empty($_POST['txtIdCnt'])) {
                $id_cnt = $_POST['txtIdCnt'];
                $dt_cnt = new detalleCnt();
                $conf_c = $this->rubroFuente->obtenerConfiguracionCtas($con_rub);
                if (!empty($conf_c)) {
                    $cuenta_debito  = $conf_c[0];
                    $cuenta_credito = $conf_c[1];
                    $cuenta_iva     = $conf_c[2];
                    $cuenta_impo    = $conf_c[3];

                    $valorD = ($valor * $cantidad) + $ajustePeso;
                    if (!empty($cuenta_debito)) {
                        $dt_cnt->fecha                   = $fecha;
                        $dt_cnt->descripcion             = $descripcion;
                        $dt_cnt->valor                   = $valorD;
                        $dt_cnt->valorejecucion          = $valorD;
                        $dt_cnt->comprobante             = $id_cnt;
                        $dt_cnt->cuenta                  = $cuenta_debito;
                        $dt_cnt->naturaleza              = 1;
                        $dt_cnt->tercero                 = $tercero;
                        $dt_cnt->proyecto                = $proyecto;
                        $dt_cnt->centrocosto             = $centrocosto;
                        $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                        $dt_cnt->detalleafectado         = "NULL";
                        $resc = $this->detalleCnt->registrar($dt_cnt);
                        if ($resc == true) {
                            $detalleD = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);
                        }
                    }

                    if (!empty($cuenta_credito)) {
                        $dt_cnt->fecha                   = $fecha;
                        $dt_cnt->descripcion             = $descripcion;
                        $dt_cnt->valor                   = $valorD;
                        $dt_cnt->valorejecucion          = $valorD;
                        $dt_cnt->comprobante             = $id_cnt;
                        $dt_cnt->cuenta                  = $cuenta_credito;
                        $dt_cnt->naturaleza              = 2;
                        $dt_cnt->tercero                 = $tercero;
                        $dt_cnt->proyecto                = $proyecto;
                        $dt_cnt->centrocosto             = $centrocosto;
                        $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                        $dt_cnt->detalleafectado         = $detalleD;
                        $resc = $this->detalleCnt->registrar($dt_cnt);
                        if ($resc == true) {
                            $detalleC = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);
                        }
                    }

                    if (!empty($cuenta_iva)) {
                        if (!empty($iva) || $iva != '0' || $iva != '0.00') {
                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $iva;
                            $dt_cnt->valorejecucion          = $iva;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_iva;
                            $dt_cnt->naturaleza              = 2;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalleC;
                            $resd = $this->detalleCnt->registrar($dt_cnt);
                            $detalle_i = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);

                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $iva;
                            $dt_cnt->valorejecucion          = $iva;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_debito;
                            $dt_cnt->naturaleza              = 1;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalle_i;
                            $resc = $this->detalleCnt->registrar($dt_cnt);
                            $detalle_x = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);
                        }
                    }

                    if (!empty($cuenta_impo)) {
                        if (!empty($impoconsumo) || $impoconsumo != '0' || $impoconsumo != '0.00') {
                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $impoconsumo;
                            $dt_cnt->valorejecucion          = $impoconsumo;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_impo;
                            $dt_cnt->naturaleza              = 2;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalle_x;
                            $this->detalleCnt->registrar($dt_cnt);
                            $detalle_y = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);

                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $impoconsumo;
                            $dt_cnt->valorejecucion          = $impoconsumo;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_debito;
                            $dt_cnt->naturaleza              = 1;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalle_y;
                            $this->detalleCnt->registrar($dt_cnt);
                        }
                    }
                }
            }
        }

        $detalleMov = 'NULL';

        
        if(!empty($_GET['mov'])){
            $elemento = $this->cpt->obtnerConceptoPlanI($concepto);
            if(!empty($elemento)){
                $dtaMovA                   = $this->mov->obtenerId($_GET['mov']);
                $factor                    = $this->factura->obtenerUnidadFactor($unidad, $concepto);
                $xxx                       = $cantidad * $factor;
                $xsaldoV                   = $this->dtm->obtenerSaldoPlan($elemento);
                $xsaldoC                   = $this->dtm->obtnerCantidadPlan($elemento);
                $xvalor                    = 0;

                if(!empty($xsaldoV) || !empty($xsaldoC)){
                    $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                }

                if($xsaldoV < 0){
                    $xvalor = $this->mov->buscarValorMaximoElemento($elemento);
                }

                if($xsaldoC < 0 || empty($xsaldoC)){
                    if(empty($xCantE)){
                        $xvalor = 0;
                    }else{
                        $xvalor = $this->mov->buscarValorMaximoElemento($elemento);
                    }
                }

                if($factor == 0){
                    $xvalor = 0;
                }
                $xdm = $this->dtm->guardar($xxx, $xvalor, 0, $dtaMovA[0], $elemento, $unidad, $cantidad);
                if($xdm == true){
                    $detalleMov = $this->dtm->obtenerUltimoRegistro($dtaMovA[0]);
                    $this->buscarHijosPadre($elemento, $unidad, $dtaMovA[0], $detalleMov);
                }
            }
        }

        $total = ($valor + $iva + $impoconsumo) * $cantidad;
        $resdf = $this->detalleFactura->registrarData($factura, $concepto, $valor, $cantidad, $iva, $impoconsumo, $ajustePeso, $total, $detalleD, $detalleMov, 'NULL', $descuento, $unidad, $descripcion);

        if ($resdf == true) {
            $url  = 'Location:json/registrarFacturaJson.php?action=registrado&factura='. md5($factura);
            $url .= !empty($id_cnt)?'&cnt='.md5($id_cnt):'';
            $url .= !empty($id_pptal)?'&pptal='.md5($id_pptal):'';
            $url .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
            header($url);
        } else {
            header('Location:json/registrarFacturaJson.php?action=noregistrado');
        }
    }

    public function modificar() {
        $id_unico = $_POST['id'];
        $concepto = $_POST['concepto'];
        $cantidad = $_POST['cantidad'];
        $valor    = $_POST['valor'];
        $unidad   = $_POST['unidad'];

        if ($_POST['iva'] == "NaN") {
            $iva = 0;
        } else {
            $iva = $_POST['iva'];
        }

        if ($_POST['impoconsumo'] == "NaN") {
            $impoconsumo = 0;
        } else {
            $impoconsumo = $_POST['impoconsumo'];
        }

        if ($_POST['ajustepeso'] == "NaN") {
            $ajustepeso = 0;
        } else {
            $ajustepeso = $_POST['ajustepeso'];
        }

        $valorajuste = $_POST['valorAjuste'];
        $valorOPe    = ($valor * $cantidad) + $ajustepeso;

        $data = $this->detalleFactura->desplazmientoDetallesCompleto($id_unico);

        $detalleP = $data[0];
        $detalleD = $data[1];
        $detalleC = $data[2];
        $detalleI = $data[3];
        $detalleM = $data[4];
        $detalleO = $data[5];
        $detalleN = $data[6];

        if (!empty($detalleP)) {
            $this->detallePptal->modificar($detalleP, $valorOPe);
        }

        if (!empty($detalleD)) {
            $this->detalleCnt->modificar($detalleD, $valorOPe);
        }

        if (!empty($detalleC)) {
            $this->detalleCnt->modificar($detalleC, $valorOPe);
        }

        if (!empty($iva) || $iva != '0' || $iva != '0.00') {
            if (!empty($detalleI)) {
                $this->detalleCnt->modificar($detalleI, $iva);
            }

            if (!empty($detalleM)) {
                $this->detalleCnt->modificar($detalleM, $iva);
            }
        }

        if (!empty($impoconsumo) || $impoconsumo != '0' || $impoconsumo != '0.00') {
            if (!empty($detalleO)) {
                $this->detalleCnt->modificar($detalleO, $impoconsumo);
            }

            if (!empty($detalleN)) {
                $this->detalleCnt->modificar($detalleN, $impoconsumo);
            }
        }

        if(!empty($_REQUEST['mov'])){
            $this->dtm->actualizar($_REQUEST['mov'], $cantidad);
        }
        
        $dtf = new detalleFactura();
        $dtf->concepto_tarifa      = $concepto;
        $dtf->unidad_origen        = $unidad;
        $dtf->cantidad             = $cantidad;
        $dtf->valor                = $valor;
        $dtf->iva                  = $iva;
        $dtf->impoconsumo          = $impoconsumo;
        $dtf->ajuste_peso          = $ajustepeso;
        $dtf->valor_total_ajustado = $valorajuste;
        $dtf->id_unico             = $id_unico;
        $res = $this->detalleFactura->modificar($dtf);
        echo json_encode($res);
    }

    public function eliminarTodos() {
        $factura = $_POST['factura'];
        $cnt     = $_POST['cnt'];
        $pptal   = $_POST['pptal'];
        $res     = false;
        if (!empty($factura)) {
            $res=$this->detalleFactura->eliminarDetallesFactura($factura);
        }

        if (!empty($cnt) && $cnt!=0) {
            $this->detalleCnt->destruirRelacionArbolDetallesCnt($cnt);
            $res=$this->detalleCnt->eliminarDetallesCnt($cnt);
        }

        if (!empty($pptal)&& $pptal!=0) {
            $res = $this->detallePptal->eliminarDetalleComprobante($pptal);
        }

        if(!empty($_REQUEST['mov'])){
            $this->dtm->eliminarDetalles($_REQUEST['mov']);
        }

        echo json_encode($res);
    }

    public function eliminar() {
        $id_unico = $_REQUEST['id_unico'];
        $data     = $this->detalleFactura->desplazmientoDetallesCompleto($id_unico);
        $detalleN = $data[6];
        $detalleO = $data[5];
        $detalleM = $data[4];
        $detalleI = $data[3];
        $detalleC = $data[2];
        $detalleD = $data[1];
        $detalleP = $data[0];

        $res = $this->detalleFactura->eliminar($id_unico);

        if (!empty($detalleN)) {
            $this->detalleCnt->eliminar($detalleN);
        }

        if (!empty($detalleO)) {
            $this->detalleCnt->eliminar($detalleO);
        }

        if (!empty($detalleM)) {
            $this->detalleCnt->eliminar($detalleM);
        }

        if (!empty($detalleI)) {
            $this->detalleCnt->eliminar($detalleI);
        }

        if (!empty($detalleC)) {
            $this->detalleCnt->eliminar($detalleC);
        }

        if (!empty($detalleD)) {
            $this->detalleCnt->eliminar($detalleD);
        }

        if (!empty($detalleP)) {
            $this->detallePptal->eliminar($detalleP);
        }

        if(!empty($_REQUEST['mov'])){
            $this->dtm->eliminar($_REQUEST['mov']);
        }

        echo json_encode($res);
    }

    public function reconstruirComprobantes() {
        @session_start();
        if (!empty($_REQUEST['id_factura'])
         || !empty($_REQUEST['id_cnt']) || !empty($_REQUEST['id_pptal'])) {
            $fat = $_REQUEST['id_factura'];
            $cnt = $_REQUEST['id_cnt'];
            $pta = $_REQUEST['id_pptal'];
            $param    = $_SESSION['anno'];
            $dtp = new detallePptal();
            $dtc = new detalleCnt();
            $this->cnt = new comprobanteContable();
            $tercero = $this->detalleCnt->obtnerTercero($cnt);
            $fecha   = $this->detalleCnt->obtnerFecha($cnt);
            $descripcion = $this->detalleCnt->obtnerDescripcion($cnt);
            $proyecto = "2147483647";
            $centrocosto = $this->cnt->obtenerCentroCosto($param, 'Varios');
            $this->detalleFactura->destruirRelacionDetalles($fat);
            $this->detalleCnt->destruirRelacionArbolDetallesCnt($cnt);
            $this->detalleCnt->eliminarDetallesCnt($cnt);

            $this->detallePptal->eliminarDetalleComprobante($pta);

            $data = $this->detalleFactura->obtnerDetallesFactura($fat);
            $x = 0;
            foreach ($data as $row) {
                $id_unico = $row[0]; $con_fat = $row[1]; $cantidad = $row[2];
                $valor    = $row[3]; $iva     = $row[4]; $impo     = $row[5];
                $ajuste   = $row[6]; $factura = $row[7]; $valor_a  = $row[8];
                $data_cr  = $this->concepto->obtnerConceptoFinanciero($con_fat, $param);
                #$data_cr  = $this->detalleFactura->obtenerConceptoRubro($con_fat);
                $con_rbr  = $data_cr;
                #$rubroFte = $this->detalleFactura->obtnerRubroFuente($rubro);
                $config_d = $this->detalleFactura->obtnerConfigRubroCuenta($con_rbr);
                $valordP  = ($valor * $cantidad) + $ajuste;
                $dtp->descripcion      = $descripcion;
                $dtp->valor            = $valordP;
                $dtp->comprobantepptal = $pta;
                $dtp->rubrofuente      = $rubroFte;
                $dtp->conceptorubro    = $con_rbr;
                $dtp->tercero          = $tercero;
                $dtp->proyecto         = $proyecto;

                $resp = $this->detallePptal->registrar($dtp);
                
                $id_detalle_ptal = "NULL";
                if ($resp = true) {
                    $x++;
                    $id_detalle_ptal = $this->detallePptal->obtnerUltimoRegistroPptal($pta);
                }
                if(empty($id_detalle_ptal)){
                    $id_detalle_ptal = "NULL";
                }
                if(!empty($config_d)){
                    $cta_dbto = $config_d[0];
                    $cta_crto = $config_d[1];
                    $cta_iva  = $config_d[2];
                    $cta_impo = $config_d[3];

                    $valorD = ($valor * $cantidad) + $ajuste;
                    $valortc = $valorD+$iva+$impo;
                    $mul =0;
                    if($valortc!=$valor_a){
                        $mul =1;
                    }
                    if (!empty($cta_dbto)) {
                        $dtc->fecha                   = $fecha;
                        $dtc->descripcion             = $descripcion;
                        $dtc->valor                   = $valorD;
                        $dtc->valorejecucion          = $valorD;
                        $dtc->comprobante             = $cnt;
                        $dtc->cuenta                  = $cta_dbto;
                        $dtc->naturaleza              = 1;
                        $dtc->tercero                 = $tercero;
                        $dtc->proyecto                = $proyecto;
                        $dtc->centrocosto             = $centrocosto;
                        $dtc->detallecomprobantepptal = $id_detalle_ptal;
                        $dtc->detalleafectado         = "NULL";
                        $resc = $this->detalleCnt->registrar($dtc);
                        if ($resc == true) {
                            $x++;
                            $detalleD = $this->detalleCnt->obtenerUltimoRegistro($cnt);
                            $this->detalleFactura->relacionarDetalleComprobante($id_unico, $detalleD);
                        }
                    }

                    if (!empty($cta_crto)) {
                        $dtc->fecha                   = $fecha;
                        $dtc->descripcion             = $descripcion;
                        $dtc->valor                   = $valorD;
                        $dtc->valorejecucion          = $valorD;
                        $dtc->comprobante             = $cnt;
                        $dtc->cuenta                  = $cta_crto;
                        $dtc->naturaleza              = 2;
                        $dtc->tercero                 = $tercero;
                        $dtc->proyecto                = $proyecto;
                        $dtc->centrocosto             = $centrocosto;
                        $dtc->detallecomprobantepptal = $id_detalle_ptal;
                        $dtc->detalleafectado         = "NULL";
                        $resc = $this->detalleCnt->registrar($dtc);
                        #var_dump($resc);
                        if ($resc == true) {
                            $x++;
                            $detalleC = $this->detalleCnt->obtenerUltimoRegistro($cnt);
                        }
                    }

                    if (!empty($cta_iva)) {
                        if (!empty($iva) || $iva != '0' || $iva != '0.00') {
                            if($mul ==1){
                                $valor_iva = ($iva * $cantidad);
                            } else {
                                $valor_iva = ($iva);
                            }
                            $dtc->fecha                   = $fecha;
                            $dtc->descripcion             = $descripcion;
                            $dtc->valor                   = $valor_iva;
                            $dtc->valorejecucion          = $valor_iva;
                            $dtc->comprobante             = $cnt;
                            $dtc->cuenta                  = $cta_iva;
                            $dtc->naturaleza              = 2;
                            $dtc->tercero                 = $tercero;
                            $dtc->proyecto                = $proyecto;
                            $dtc->centrocosto             = $centrocosto;
                            $dtc->detallecomprobantepptal = $id_detalle_ptal;
                            $dtc->detalleafectado         = "NULL";
                            $resd = $this->detalleCnt->registrar($dtc);
                            $detalle_i = $this->detalleCnt->obtenerUltimoRegistro($cnt);

                            $dtc->fecha                   = $fecha;
                            $dtc->descripcion             = $descripcion;
                            $dtc->valor                   = $valor_iva;
                            $dtc->valorejecucion          = $valor_iva;
                            $dtc->comprobante             = $cnt;
                            $dtc->cuenta                  = $cta_dbto;
                            $dtc->naturaleza              = 1;
                            $dtc->tercero                 = $tercero;
                            $dtc->proyecto                = $proyecto;
                            $dtc->centrocosto             = $centrocosto;
                            $dtc->detallecomprobantepptal = $id_detalle_ptal;
                            $dtc->detalleafectado         = "NULL";
                            $resc = $this->detalleCnt->registrar($dtc);
                            $detalle_x = $this->detalleCnt->obtenerUltimoRegistro($cnt);
                        }
                    }

                    if (!empty($cta_impo)) {
                        if (!empty($impo) || $impo != '0' || $impo != '0.00') {
                            if($mul ==1){
                                $valor_impo = ($impo * $cantidad);
                            } else {
                                $valor_impo = ($impo);
                            }
                            $dtc->fecha                   = $fecha;
                            $dtc->descripcion             = $descripcion;
                            $dtc->valor                   = $valor_impo;
                            $dtc->valorejecucion          = $valor_impo;
                            $dtc->comprobante             = $cnt;
                            $dtc->cuenta                  = $cta_impo;
                            $dtc->naturaleza              = 2;
                            $dtc->tercero                 = $tercero;
                            $dtc->proyecto                = $proyecto;
                            $dtc->centrocosto             = $centrocosto;
                            $dtc->detallecomprobantepptal = $id_detalle_ptal;
                            $dtc->detalleafectado         = "NULL";
                            $this->detalleCnt->registrar($dtc);
                            $detalle_y = $this->detalleCnt->obtenerUltimoRegistro($cnt);

                            $dtc->fecha                   = $fecha;
                            $dtc->descripcion             = $descripcion;
                            $dtc->valor                   = $valor_impo;
                            $dtc->valorejecucion          = $valor_impo;
                            $dtc->comprobante             = $cnt;
                            $dtc->cuenta                  = $cta_dbto;
                            $dtc->naturaleza              = 1;
                            $dtc->tercero                 = $tercero;
                            $dtc->proyecto                = $proyecto;
                            $dtc->centrocosto             = $centrocosto;
                            $dtc->detallecomprobantepptal = $id_detalle_ptal;
                            $dtc->detalleafectado         = "NULL";
                            $this->detalleCnt->registrar($dtc);
                        }
                    }
                }
            }

            // if(count($data) == $x){
            //     $w = true;
            // }else{
            //     $w = false;
            // }

            echo (($x));
        }
    }

    public function registrarRemision() {
        $factura          = $_POST['txtIdFactura'];
        $concepto         = $_POST['sltConcepto'];
        $iva              = $_POST['txtIva'];
        $impoconsumo      = $_POST['txtImpoconsumo'];
        $ajustePeso       = $_POST['txtAjustePeso'];
        $valorTotalAjuste = $_POST['txtValorA'];
        $tercero          = $_POST['txtTercero'];
        $centrocosto      = $_POST['txtCentroCosto'];
        $descripcion      = $_POST['txtDescr'];
        $proyecto         = '2147483647';
        $fecha            = $_POST['txtFecha'];
        $valor            = $_POST['txtValorX'];
        $descuento        = $_POST['txtXDescuento'];

        if (empty($_POST['txtCantidad'])) {
            $cantidad = 1;
        } else {
            $cantidad = $_POST['txtCantidad'];
        }
        @session_start();
        $param = $_SESSION['anno'];
        $conf = $this->concepto->obtnerConceptoFinanciero($concepto, $param);

        $id_detalle_ptal = "NULL";
        $id_pptal = 0;
        $id_cnt   = 0;

        $detalleD  = "NULL";
        $detalleC  = "NULL";
        $detalle_i = "NULL";
        $detalle_x = "NULL";
        $detalle_y = "NULL";

        if (empty($_POST['sltRubroFuente'])) {
            $rbf = new rubroFuente();
            $fuente      = $_POST['sltFuentes'];
            $rubro       = $_POST['sltRubros'];
            $rbf->rubro  = $rubro;
            $rbf->fuente = $fuente;

            $this->rubroFuente->registrar($rbf);
            $con_rub = $this->rubroFuente->obtnerConceptoR($rubro, $conf);
            $rubrof  = $this->rubroFuente->obtenerIdRubroFuente($rubro, $fuente);
        } else {
            $rubrof  = $_POST['sltRubroFuente'];
            $con_rub = $_POST['txtConceptoRubro'];
        }

        if (!empty($_POST['txtIdPptal'])) {
            $dtpp = new detallePptal();

            $id_pptal = $_POST['txtIdPptal'];
            $valordP  = ($valor * $cantidad) + $ajustePeso;

            $dtpp->descripcion      = $descripcion;
            $dtpp->valor            = $valordP;
            $dtpp->comprobantepptal = $id_pptal;
            $dtpp->rubrofuente      = $rubrof;
            $dtpp->conceptorubro    = $con_rub;
            $dtpp->tercero          = $tercero;
            $dtpp->proyecto         = $proyecto;
            $resp = $this->detallePptal->registrar($dtpp);
            if ($resp = true) {
                $id_detalle_ptal = $this->detallePptal->obtnerUltimoRegistroPptal($id_pptal);
            }
        }

        if (!empty($conf)) {
            if (!empty($_POST['txtIdCnt'])) {
                $id_cnt = $_POST['txtIdCnt'];
                $dt_cnt = new detalleCnt();
                $conf_c = $this->rubroFuente->obtenerConfiguracionCtas($con_rub);
                if (!empty($conf_c)) {
                    $cuenta_debito  = $conf_c[0];
                    $cuenta_credito = $conf_c[1];
                    $cuenta_iva     = $conf_c[2];
                    $cuenta_impo    = $conf_c[3];

                    $valorD = ($valor * $cantidad) + $ajustePeso;
                    if (!empty($cuenta_debito)) {
                        $dt_cnt->fecha                   = $fecha;
                        $dt_cnt->descripcion             = $descripcion;
                        $dt_cnt->valor                   = $valorD;
                        $dt_cnt->valorejecucion          = $valorD;
                        $dt_cnt->comprobante             = $id_cnt;
                        $dt_cnt->cuenta                  = $cuenta_debito;
                        $dt_cnt->naturaleza              = 1;
                        $dt_cnt->tercero                 = $tercero;
                        $dt_cnt->proyecto                = $proyecto;
                        $dt_cnt->centrocosto             = $centrocosto;
                        $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                        $dt_cnt->detalleafectado         = "NULL";
                        $resc = $this->detalleCnt->registrar($dt_cnt);
                        if ($resc == true) {
                            $detalleD = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);
                        }
                    }

                    if (!empty($cuenta_credito)) {
                        $dt_cnt->fecha                   = $fecha;
                        $dt_cnt->descripcion             = $descripcion;
                        $dt_cnt->valor                   = $valorD;
                        $dt_cnt->valorejecucion          = $valorD;
                        $dt_cnt->comprobante             = $id_cnt;
                        $dt_cnt->cuenta                  = $cuenta_credito;
                        $dt_cnt->naturaleza              = 2;
                        $dt_cnt->tercero                 = $tercero;
                        $dt_cnt->proyecto                = $proyecto;
                        $dt_cnt->centrocosto             = $centrocosto;
                        $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                        $dt_cnt->detalleafectado         = $detalleD;
                        $resc = $this->detalleCnt->registrar($dt_cnt);
                        var_dump($resc);
                        if ($resc == true) {
                            $detalleC = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);
                        }
                    }

                    if (!empty($cuenta_iva)) {
                        if (!empty($iva) || $iva != '0' || $iva != '0.00') {
                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $iva;
                            $dt_cnt->valorejecucion          = $iva;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_iva;
                            $dt_cnt->naturaleza              = 2;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalleC;
                            $resd = $this->detalleCnt->registrar($dt_cnt);
                            $detalle_i = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);

                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $iva;
                            $dt_cnt->valorejecucion          = $iva;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_debito;
                            $dt_cnt->naturaleza              = 1;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalle_i;
                            $resc = $this->detalleCnt->registrar($dt_cnt);
                            $detalle_x = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);
                        }
                    }

                    if (!empty($cuenta_impo)) {
                        if (!empty($impoconsumo) || $impoconsumo != '0' || $impoconsumo != '0.00') {
                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $impoconsumo;
                            $dt_cnt->valorejecucion          = $impoconsumo;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_impo;
                            $dt_cnt->naturaleza              = 2;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalle_x;
                            $this->detalleCnt->registrar($dt_cnt);
                            $detalle_y = $this->detalleCnt->obtenerUltimoRegistro($id_cnt);

                            $dt_cnt->fecha                   = $fecha;
                            $dt_cnt->descripcion             = $descripcion;
                            $dt_cnt->valor                   = $impoconsumo;
                            $dt_cnt->valorejecucion          = $impoconsumo;
                            $dt_cnt->comprobante             = $id_cnt;
                            $dt_cnt->cuenta                  = $cuenta_debito;
                            $dt_cnt->naturaleza              = 1;
                            $dt_cnt->tercero                 = $tercero;
                            $dt_cnt->proyecto                = $proyecto;
                            $dt_cnt->centrocosto             = $centrocosto;
                            $dt_cnt->detallecomprobantepptal = $id_detalle_ptal;
                            $dt_cnt->detalleafectado         = $detalle_y;
                            $this->detalleCnt->registrar($dt_cnt);
                        }
                    }
                }
            }
        }

        $detalleMov  = 'NULL';
        if(empty($_REQUEST['sltUnidad'])){
            $unidad     = 'NULL';
        } else {
            $unidad     = $_REQUEST['sltUnidad'];
        }
        if(empty($_REQUEST['descripcion'])){
            $descripcion     = NULL;
        } else {
            $descripcion     = $_REQUEST['descripcion'];
        }
        
        if(!empty($_GET['mov'])){
            $elemento = $this->cpt->obtnerConceptoPlanI($concepto);
            if(!empty($elemento)){
                $dtaMovA                   = $this->mov->obtenerId($_GET['mov']);
                $factor                    = $this->factura->obtenerUnidadFactor($unidad, $concepto);
                $xxx                       = $cantidad * $factor;
                $xsaldoV                   = $this->dtm->obtenerSaldoPlan($elemento);
                $xsaldoC                   = $this->dtm->obtnerCantidadPlan($elemento);
                $xCantE                    = $this->dtm->obtenerSaldoEntradaPlan($elemento);
                $xvalor                    = 0;

                if(!empty($xsaldoV) || !empty($xsaldoC)){
                    $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                }

                if($xsaldoV < 0){
                    $xvalor = $this->mov->buscarValorMaximoElemento($elemento);
                }

                if($xsaldoC < 0 || empty($xsaldoC)){
                    if(empty($xCantE)){
                        $xvalor = 0;
                    }else{
                        $xvalor = $this->mov->buscarValorMaximoElemento($elemento);
                    }
                }

                if($factor == 0){
                    $xvalor = 0;
                }
                $xdm = $this->dtm->guardar($xxx, $xvalor, 0, $dtaMovA[0], $elemento, $unidad, $cantidad);
                if($xdm == true){
                    $detalleMov = $this->dtm->obtenerUltimoRegistro($dtaMovA[0]);
                    $this->buscarHijosPadre($elemento, $unidad, $dtaMovA[0], $detalleMov);
                }
            }
        }

        $total = ($valor + $iva + $impoconsumo) * $cantidad;
        $resdf = $this->detalleFactura->registrarData($factura, $concepto, $valor, $cantidad, $iva, $impoconsumo, $ajustePeso, $total, $detalleD, $detalleMov, 'NULL', $descuento, $unidad, $descripcion);

        if ($resdf == true) {
            $url  = 'Location:json/registrarFactRem.php?action=registrado&factura='.md5($factura);
            $url .= !empty($id_cnt)?'&cnt='.md5($id_cnt):'';
            $url .= !empty($id_pptal)?'&pptal='.md5($id_pptal):'';
            $url .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
            header($url);
        } else {
            header('Location:json/registrarFactRem.php?action=noregistrado');
        }
    }

    public function obtenerPlanIdConcepto(){
        if(!empty($_REQUEST['concepto'])){
            $this->cpt->id_unico = $_REQUEST['concepto'];
            $dataCpt = $this->cpt->obtnerConceptoPl();
            if(count($dataCpt) > 0){
                echo $dataCpt[0];
            }else{
                echo 0;
            }
        }
    }

    public function registrarCot() {
        $factura          = $_POST['txtIdFactura'];
        $concepto         = $_POST['sltConcepto'];
        $iva              = $_POST['txtIva'];
        $impoconsumo      = $_POST['txtImpoconsumo'];
        $ajustePeso       = $_POST['txtAjustePeso'];
        $valorTotalAjuste = $_POST['txtValorA'];
        $tercero          = $_POST['txtTercero'];
        $centrocosto      = $_POST['txtCentroCosto'];
        $descripcion      = $_POST['txtDescr'];
        $proyecto         = '2147483647';
        $fecha            = $_POST['txtFecha'];
        $valor            = $_POST['txtValorX'];
        $descuento        = !empty($_POST['txtXDescuento'])?$_REQUEST['txtXDescuento']:'NULL';

        if (empty($_POST['txtCantidad'])) {
            $cantidad = 1;
        } else {
            $cantidad = $_POST['txtCantidad'];
        }
        if ($descripcion=='NULL') {
            $descripcion="";
        }else{
            $descripcion=$descripcion;
        }

        $detalleMov  = 'NULL';
        $unidad      = $_REQUEST['sltUnidad'];
        if(!empty($_GET['mov'])){
            $elemento = $this->cpt->obtnerConceptoPlanI($concepto);
            if(!empty($elemento)){
                $dtaMovA                   = $this->mov->obtenerId($_GET['mov']);
                $factor                    = $this->factura->obtenerUnidadFactor($unidad, $concepto);
                $xxx                       = $cantidad * $factor;
                $xsaldoV                   = $this->dtm->obtenerSaldoPlan($elemento);
                $xsaldoC                   = $this->dtm->obtnerCantidadPlan($elemento);

                if(empty($xsaldoC)){
                    $xvalor                = 0;
                }
                if(!empty($xsaldoV) || !empty($xsaldoC)){
                    $xvalor                = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                }

                if($xsaldoV < 0){
                    $xvalor = 0;
                }

                $xdm = $this->dtm->guardar($xxx, $xvalor, 0, $dtaMovA[0], $elemento, $unidad, $cantidad);
                if($xdm == true){
                    $detalleMov = $this->dtm->obtenerUltimoRegistro($dtaMovA[0]);
                }
            }
        }
        //$url  = 'registrar_GF_FACTURA_COTIZACION.php?factura='.md5($factura);
        $total = ($valor + $iva + $impoconsumo) * $cantidad;
        //var_dump($factura, $concepto, $valor, $cantidad, $iva, $impoconsumo, $ajustePeso, $total, 'NULL', $detalleMov, 'NULL', $descuento, $unidad);
        
        $data = $this->detalleFactura->registrarData($factura, $concepto, $valor, $cantidad, $iva, $impoconsumo, $ajustePeso, $total, 'NULL', $detalleMov, 'NULL', $descuento, $unidad, "");
        
        $url  = 'registrar_GF_FACTURA_COTIZACION.php?factura='.md5($factura);
        $url .= !empty($id_cnt)?'&cnt='.md5($id_cnt):'';
        $url .= !empty($id_pptal)?'&pptal='.md5($id_pptal):'';
        $url .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
        require_once './vistas/respuesta/index.php';
    }

    public function registrarPedido() {
        $factura          = $_POST['txtIdFactura'];
        $concepto         = $_POST['sltConcepto'];
        $iva              = $_POST['txtIva'];
        $impoconsumo      = $_POST['txtImpoconsumo'];
        $ajustePeso       = $_POST['txtAjustePeso'];
        $valorTotalAjuste = $_POST['txtValorA'];
        $tercero          = $_POST['txtTercero'];
        $centrocosto      = $_POST['txtCentroCosto'];
        $descripcion      = $_POST['txtDescr'];
        $proyecto         = '2147483647';
        $fecha            = $_POST['txtFecha'];
        $valor            = $_POST['txtValorX'];
        $descuento        = !empty($_POST['txtXDescuento'])?$_REQUEST['txtXDescuento']:'NULL';

        if (empty($_POST['txtCantidad'])) {
            $cantidad = 1;
        } else {
            $cantidad = $_POST['txtCantidad'];
        }

        $detalleMov  = 'NULL';
        $unidad      = $_REQUEST['sltUnidad'];
        if(!empty($_GET['mov'])){
            $elemento = $this->cpt->obtnerConceptoPlanI($concepto);
            if(!empty($elemento)){
                $dtaMovA = $this->mov->obtenerId($_GET['mov']);
                $factor  = $this->factura->obtenerUnidadFactor($unidad, $concepto);
                $xxx     = $cantidad * $factor;
                $xsaldoV = $this->dtm->obtenerSaldoPlan($elemento);
                $xsaldoC = $this->dtm->obtnerCantidadPlan($elemento);

                if(empty($xsaldoC)){
                    $xvalor = 0;
                }

                if(!empty($xsaldoV) || !empty($xsaldoC)){
                    $xvalor = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                }

                if($xsaldoV < 0){
                    $xvalor = 0;
                }

                $xdm = $this->dtm->guardar($xxx, $xvalor, 0, $dtaMovA[0], $elemento, $unidad, $cantidad);
                if($xdm == true){
                    $detalleMov = $this->dtm->obtenerUltimoRegistro($dtaMovA[0]);
                }
            }
        }

        $total = ($valor + $iva + $impoconsumo) * $cantidad;
        $data = $this->detalleFactura->registrarData($factura, $concepto, $valor, $cantidad, $iva, $impoconsumo, $ajustePeso, $total, 'NULL', $detalleMov, 'NULL', $descuento, $unidad);
        $url  = 'registrar_GF_FACTURA_PEDIDO.php?factura='.md5($factura);
        $url .= !empty($id_cnt)?'&cnt='.md5($id_cnt):'';
        $url .= !empty($id_pptal)?'&pptal='.md5($id_pptal):'';
        $url .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
        require_once './vistas/respuesta/index.php';
    }

    public function GuardarordenTraslado() {
        $factura          = $_POST['txtIdFactura'];
        $concepto         = $_POST['sltConcepto'];
        $iva              = 0;
        $impoconsumo      = 0;
        $ajustePeso       = 0;
        $valor            = $_POST['txtValor'];
        $descuento        = $_POST['txtXDescuento'];

        if (empty($_POST['txtCantidad'])) {
            $cantidad = 1;
        } else {
            $cantidad = $_POST['txtCantidad'];
        }

        $detalleMov  = 'NULL';
        $unidad      = $_REQUEST['sltUnidad'];
        if(!empty($_GET['mov'])){
            $elemento = $this->cpt->obtnerConceptoPlanI($concepto);
            if(!empty($elemento)){
                $dtaMovA = $this->mov->obtenerId($_GET['mov']);
                $factor  = $this->factura->obtenerUnidadFactor($unidad, $concepto);
                $xxx     = $cantidad * $factor;
                $xsaldoV = $this->dtm->obtenerSaldoPlan($elemento);
                $xsaldoC = $this->dtm->obtnerCantidadPlan($elemento);

                if(empty($xsaldoC)){
                    $xvalor = $this->mov->buscarValorMaximoElemento($elemento);
                }else{
                    $xvalor = $valor;
                }

                if($xsaldoV < 0){
                    $xvalor = 0;
                }

                $xdm = $this->dtm->guardar($xxx, $xvalor, 0, $dtaMovA[0], $elemento, $unidad, $cantidad);
                if($xdm == true){
                    $detalleMov = $this->dtm->obtenerUltimoRegistro($dtaMovA[0]);
                }
            }
        }

        $total = ($valor + $iva + $impoconsumo) * $cantidad;
        $data  = $this->detalleFactura->registrarData($factura, $concepto, $valor, $cantidad, $iva, $impoconsumo, $ajustePeso, $total, 'NULL', $detalleMov, 'NULL', $descuento, $unidad);
        $url   = "";
        if ($data == true) {
            $url  = 'access.php?controller=Factura&action=VistaOrden&factura='.md5($factura);
            $url .= !empty($id_cnt)?'&cnt='.md5($id_cnt):'';
            $url .= !empty($id_pptal)?'&pptal='.md5($id_pptal):'';
            $url .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
        } else {
            $url .= "access.php?controller=Factura&action=VistaOrden";
        }
        require_once './vistas/respuesta/index.php';
    }

    public function buscarHijosPadre($elemento, $unidad, $mov, $dtm){
        $data = $this->mov->obtenerRelacionHijosUnidad($elemento, $unidad);
        if(count($data) > 0){
            foreach ($data as $row){
                $xCantE  = $this->dtm->obtenerSaldoEntradaPlan($row[0]);
                $xsaldoV = $this->dtm->obtenerSaldoPlan($row[0]);
                $xsaldoC = $this->dtm->obtnerCantidadPlan($row[0]);
                $xvalor  = 0;
                if(!empty($xsaldoV) || !empty($xsaldoC)){
                    $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                }

                if($xsaldoV < 0){
                    $xvalor = $this->mov->buscarValorMaximoElemento($row[0]);
                }

                if($xsaldoC < 0 || empty($xsaldoC)){
                    if(empty($xCantE)){
                        $xvalor  = 0;
                    }else{
                        $xvalor = $this->mov->buscarValorMaximoElemento($row[0]);
                    }
                }

                $xuni = $this->factura->obtenerUnidadMinimaPlan($row[0]);
                $this->dtm->guardarA($row[1], $xvalor, 0, $mov, $row[0], $xuni, $row[1], 0, $dtm);
            }
        }
    }
}
