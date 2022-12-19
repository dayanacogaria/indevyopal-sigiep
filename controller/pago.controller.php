<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 03/09/2018
 * Time: 14:20
 */
require_once './modelFactura/pago.php';
require_once './modelFactura/comprobanteContable.php';
require_once './modelFactura/comprobantePptal.php';
require_once './modelFactura/detallecnt.php';
require_once './modelFactura/detallePptal.php';
require_once './modelFactura/factura.php';
class pagoController{

    private $pag;
    private $cnt;
    private $pto;
    private $dtc;
    private $dpt;
    private $fat;

    public function __construct(){
        $this->pag = new pago();
        $this->cnt = new comprobanteContable();
        $this->pto = new comprobantePptal();
        $this->dtc = new detalleCnt();
        $this->dpt = new detallePptal();
        $this->fat = new factura();
    }

    public function vistaGenerarPagosFecha(){
        require_once './vistas/pagos/index.php';
    }

    public function formatearFecha($fecha, $separador){
        try {
            $xxx = explode($separador, $fecha);
            return "$xxx[2]-$xxx[1]-$xxx[0]";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function validarNaturalezaDebito($naturaleza, $valor){
        if($naturaleza == 1){
            $xvalor  = $valor;
        }else{
            $xvalor  = $valor * -1;
        }
        return $xvalor;
    }

    public function validarNaturalezaCredito($naturaleza, $valor){
        if($naturaleza == 1){
            $xvalor  = $valor * -1;
        }else{
            $xvalor  = $valor;
        }
        return $xvalor;
    }

    public function GenerarContabilidadFactura(){
        session_start();
        $fechaI   = $this->formatearFecha($_REQUEST['txtFechaI'], '/');
        $fechaF   = $this->formatearFecha($_REQUEST['txtFechaF'], '/');
        $data     = $this->fat->facturasFechaTipo($fechaI, $fechaF, $_REQUEST['sltTipoXF']);
        $tipoCnt  = $this->fat->obtnerTipoComprobanteCnt($_REQUEST['sltTipoXF']);
        $compania = $_SESSION['compania'];
        $param    = $_SESSION['anno'];
        $x        = 0;
        foreach ($data as $row) {
            $xcvnt  = $this->cnt->buscarComprobante($row[1], $tipoCnt);
            if(!empty($xcvnt)){
                $xtdet  = $this->cnt->obtenerDetallesComprobante($xcvnt);
                if(count($xtdet) == 0){
                    $this->cnt->eliminar($xcvnt);
                }
            }
            $xnumero = $row[1];
            $rescnt = $this->cnt->guardar($xnumero, $row[2], $row[4], $tipoCnt, $param, $compania, $row[6], 1);
            if($rescnt == true){
                $xarCtD = array(); $xarCtC = array();
                $xidCnt = $this->cnt->obtnerUltimoRegistroTipo($tipoCnt, $xnumero);
                $xdata  = $this->fat->obtenerDetallesFactura($row[0]);
                if(count($xdata) > 0){
                    foreach ($xdata as $xrow){
                        $xcong = $this->pag->obtenerConfiguracionConcepto($xrow[1], $param);
                        $xconr = $xcong["concepto_rubro"];
                        if(!empty($xconr)){
                            $xccn  = $this->pag->obtenerConfiguracionCuentasPorConceptoRubro($xconr);
                            if(count($xccn) > 0){
                                $cuentaDto = $xccn['cuenta_debito']; $cuentaCto = $xccn['cuenta_credito'];
                                $cuentaIva = $xccn['cuenta_iva'];    $cuentaImp = $xccn['cuenta_impoconsumo'];
                                $proyecto  = $xccn['proyecto'];      $centroCso = $xccn['centrocosto'];
                                list($xcuentDto, $xcuentIva, $xctaDtI, $xCtoIM,  $xcuentCto)
                                    = array('NULL', 'NULL', 'NULL', 'NULL', 'NULL');
                                if(!empty($cuentaDto)){
                                    if(!empty($xrow[7])){
                                        $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                        $xvlD = $this->validarNaturalezaDebito($xnat, $xrow[7] * $xrow[4]);
                                        if(in_array($cuentaDto, $xarCtD)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                            if( ($xvlD > 0 && $xValor > 0) || ($xvlD < 0 && $xValor < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlD, $xidCnt, $cuentaDto);
                                            }else{
                                                $xdtb = $this->dtc->guardar($row[2], '', $xvlD, $xvlD, $xidCnt,
                                                    $cuentaDto, $xnat, $row[6], $proyecto, $centroCso,
                                                    'NULL', 'NULL');
                                                if($xdtb == true){
                                                    $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                    $this->fat->actualizarDetalleCnt($xrow[0], $xcuentDto);
                                                    $x++;
                                                }
                                            }
                                        }else{
                                            array_push($xarCtD, $cuentaDto);
                                            $xdtb = $this->dtc->guardar($row[2], '', $xvlD, $xvlD, $xidCnt,
                                                $cuentaDto, $xnat, $row[6], $proyecto, $centroCso,
                                                'NULL', 'NULL');
                                            if($xdtb == true){
                                                $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                $this->fat->actualizarDetalleCnt($xrow[0], $xcuentDto);
                                                $x++;
                                            }
                                        }
                                    }
                                }

                                if(!empty($cuentaCto)){
                                    if(!empty($xrow[7])){
                                        $xnat = $this->dtc->obtenerNaturaleza($cuentaCto);
                                        $xvlC = $this->validarNaturalezaCredito($xnat, $xrow[7] * $xrow[4]);
                                        if(in_array($cuentaCto, $xarCtC)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaCto);
                                            if( ($xValor > 0 && $xvlC > 0) || ($xValor < 0 && $xvlC < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlC, $xidCnt, $cuentaCto);
                                            }else{
                                                $xdtc = $this->dtc->guardar($row[2], '', $xvlC, $xvlC, $xidCnt,
                                                    $cuentaCto, $xnat, $row[6], $proyecto, $centroCso,
                                                    'NULL', $xcuentDto);
                                                if($xdtc == true){ $xcuentCto = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                            }
                                        }else{
                                            array_push($xarCtC, $cuentaCto);
                                            $xdtc  = $this->dtc->guardar($row[2], '', $xvlC, $xvlC, $xidCnt,
                                                $cuentaCto, $xnat, $row[6], $proyecto, $centroCso,
                                                'NULL', $xcuentDto);
                                            if($xdtc == true){ $xcuentCto = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                        }
                                    }
                                }

                                if(!empty($cuentaIva)){
                                    if(!empty($xrow[5])){
                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaDto);
                                        $xvlDi = $this->validarNaturalezaDebito($xnat, $xrow[5] * $xrow[4]);
                                        if(in_array($cuentaDto, $xarCtD)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                            if( ($xValor > 0 && $xvlDi > 0) || ($xValor < 0 && $xvlDi < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDi, $xidCnt, $cuentaDto);
                                            }else{
                                                $xdtCi = $this->dtc->guardar($row[2], '', $xvlDi, $xvlDi, $xidCnt,
                                                    $cuentaDto, $xnat, $row[6], $proyecto, $centroCso,
                                                    'NULL', $xcuentCto);
                                                if($xdtCi == true){ $xctaDtI = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                            }
                                        }else{
                                            array_push($xarCtD, $cuentaDto);
                                            $xdtCi = $this->dtc->guardar($row[2], '', $xvlDi, $xvlDi, $xidCnt,
                                                $cuentaDto, $xnat, $row[6], $proyecto, $centroCso,
                                                'NULL', $xcuentCto);
                                            if($xdtCi == true){ $xctaDtI = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                        }

                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaIva);
                                        $xvlDi = $this->validarNaturalezaCredito($xnat, $xrow[5] * $xrow[4]);
                                        if(in_array($cuentaIva, $xarCtC)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaIva);
                                            if( ($xValor > 0 && $xvlDi > 0) || ($xValor < 0 & $xvlDi < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDi, $xidCnt, $cuentaIva);
                                            }else{
                                                $xdtI = $this->dtc->guardar($row[2], '', $xvlDi, $xvlDi, $xidCnt,
                                                    $cuentaIva, $xnat, $row[6], $proyecto, $centroCso,
                                                    'NULL', $xctaDtI);
                                                if($xdtI == true){ $xcuentIva = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                            }
                                        }else{
                                            array_push($xarCtC, $cuentaIva);
                                            $xdtI = $this->dtc->guardar($row[2], '', $xvlDi, $xvlDi, $xidCnt,
                                                $cuentaIva, $xnat, $row[6], $proyecto, $centroCso,
                                                'NULL', $xctaDtI);
                                            if($xdtI == true){ $xcuentIva = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                        }
                                    }
                                }

                                if(!empty($cuentaImp)){
                                    if(!empty($xrow[6])){
                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaDto);
                                        $xvlDX = $this->validarNaturalezaDebito($xnat, $xrow[6] * $xrow[4]);
                                        if(in_array($cuentaDto, $xarCtD)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                            if( ($xValor > 0 && $xvlDX > 0) || ($xValor < 0 && $xvlDX < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDX, $xidCnt, $cuentaDto);
                                            }else{
                                                $xdtDM = $this->dtc->guardar($row[2], '', $xvlDX, $xvlDX, $xidCnt,
                                                    $cuentaDto, $xnat, $row[6], $proyecto, $centroCso,
                                                    'NULL', $xcuentIva);
                                                if($xdtDM == true){ $xCtoIM = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                            }
                                        }else{
                                            array_push($xarCtD, $cuentaDto);
                                            $xdtDM = $this->dtc->guardar($row[2], '', $xvlDX, $xvlDX, $xidCnt,
                                                $cuentaDto, $xnat, $row[6], $proyecto, $centroCso,
                                                'NULL', $xcuentIva);
                                            if($xdtDM == true){ $xCtoIM = $this->dtc->obtenerUltimoRegistro($xidCnt); }
                                        }

                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaImp);
                                        $xvlDX = $this->validarNaturalezaCredito($xnat, $xrow[6] * $xrow[4]);
                                        if(in_array($cuentaImp, $xarCtC)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaImp);
                                            if( ($xValor > 0 && $xvlDX > 0) || ($xValor < 0 && $xvlDX < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDX, $xidCnt, $cuentaImp);
                                            }else{
                                                $this->dtc->guardar($row[2], '', $xvlDX, $xvlDX, $xidCnt,
                                                    $cuentaImp, $xnat, $row[6], $proyecto, $centroCso,
                                                    'NULL', $xCtoIM);
                                            }
                                        }else{
                                            array_push($xarCtC, $cuentaImp);
                                            $this->dtc->guardar($row[2], '', $xvlDX, $xvlDX, $xidCnt,
                                                $cuentaImp, $xnat, $row[6], $proyecto, $centroCso,
                                                'NULL', $xCtoIM);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $url = "access.php?controller=Factura&action=generarContabilidadBloque";
        if($x > 0){
            $data = true;
        }else{
            $data = false;
        }
        require_once './vistas/respuesta/index.php';
    }

    public function GenerarRecaudo(){
        session_start();
        $fechaI   = $this->formatearFecha($_REQUEST['txtFechaI'], '/');
        $fechaF   = $this->formatearFecha($_REQUEST['txtFechaF'], '/');
        $tipoR    = $this->pag->obtenerTipoRecaudo($_REQUEST['sltTipoXF']);
        $tipoCnt  = $this->pag->obtenerTipoComprobante($tipoR);
        $data     = $this->fat->facturasFechaTipo($fechaI, $fechaF, $_REQUEST['sltTipoXF']);
        $banco    = $_REQUEST['sltBanco'];
        $xcuentaB = $this->cnt->obtenerCuentaBanco($banco);
        $generar  = $_REQUEST['optGenerar'];
        $param    = $_SESSION['anno'];
        $compania = $_SESSION['compania'];
        $x        = 0;
        foreach ($data as $row){
            $xpag = $this->pag->buscarPagoFactura($row[0]);
            if(!empty($xpag)){
                $this->pag->eliminarDetallesPago($xpag);
                $this->pag->eliminarPago($xpag);
            }
            $numero = $this->pag->validarNumero($tipoR, $param);
            $xrspg = $this->pag->guardar($numero, $tipoR, $row[6], $row[2], $banco, 1, $param,
                $_SESSION['usuario_tercero']);
            if($xrspg == true){
                $xidPag = $this->pag->obtenerMaxId($tipoR);
                $xidCnt = 'NULL'; $xarCtC = array();
                if($generar == 1){
                    $nro = $this->cnt->validarNumero($tipoCnt, $param);
                    $xcnt = $this->cnt->guardar($nro, $row[2], '', $tipoCnt, $param, $compania, $row[6], 1);
                    if($xcnt == true){
                        $xidCnt = $this->cnt->obtnerUltimoRegistroTipo($tipoCnt, $nro);
                    }
                }
                $xdata = $this->fat->obtenerDetallesFactura($row[0]);
                if(count($xdata) > 0){
                    $xxx    = 0;
                    foreach ($xdata as $xrow){
                        $valorF = $xrow[7] * $xrow[4];
                        $valorI = $xrow[5] * $xrow[4];
                        $valorM = $xrow[6] * $xrow[4];
                        $xresdp = $this->pag->guardar_detalle($xrow[0], $valorF, $valorI, $valorM, 0, 0, $xidPag, 'NULL');
                        if($xresdp == true){
                            $idDpg = $this->pag->obtenerUltimoRegistroPago($xidPag);
                            $x++;
                            if($generar == 1){
                                $xcong = $this->pag->obtenerConfiguracionConcepto($xrow[1], $param);
                                if(count($xcong) > 0){
                                    $xconr = $xcong["concepto_rubro"];
                                    if(!empty($xconr)){
                                        $xccn  = $this->pag->obtenerConfiguracionCuentasPorConceptoRubro($xconr);
                                        if(count($xccn) > 0){
                                            $cuentaDto = $xccn['cuenta_debito'];      $cuentaIva = $xccn['cuenta_iva'];
                                            $cuentaImp = $xccn['cuenta_impoconsumo']; $proyecto  = $xccn['proyecto'];
                                            $centroCso = $xccn['centrocosto'];
                                            if(!empty($cuentaDto)){/* Registro Debito */
                                                if(!empty($valorF)){
                                                    $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                                    $xvlD = $this->validarNaturalezaCredito($xnat, $valorF);
                                                    if(in_array($cuentaDto, $xarCtC)){
                                                        $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                        if(($xValor > 0 && $xvlD > 0) || ($xValor < 0 && $xvlD < 0)){
                                                            $xrsdc = $this->fat->actualizarDataComprobante($xvlD, $xidCnt, $cuentaDto);
                                                            if($xrsdc == true){ $xxx += ($valorF); }
                                                        }else{
                                                            $xrsdc = $this->dtc->guardar($row[2], '', $xvlD,
                                                                $xvlD, $xidCnt, $cuentaDto, $xnat, $row[6], $proyecto,
                                                                $centroCso, 'NULL', 'NULL');
                                                            if($xrsdc == true){
                                                                $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                                $xxx       += ($valorF);
                                                                $this->pag->actualizarDetalleComprobanteDetallePago($idDpg, $xcuentDto);
                                                            }
                                                        }
                                                    }else{
                                                        array_push($xarCtC, $cuentaDto);
                                                        $xrsdc = $this->dtc->guardar($row[2], '', $xvlD,
                                                            $xvlD, $xidCnt, $cuentaDto, $xnat, $row[6], $proyecto,
                                                            $centroCso, 'NULL', 'NULL');
                                                        if($xrsdc == true){
                                                            $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                            $xxx       += ($valorF);
                                                            $this->pag->actualizarDetalleComprobanteDetallePago($idDpg, $xcuentDto);
                                                        }
                                                    }
                                                }
                                            }
                                            if(!empty($cuentaDto)){/* Registro de Iva */
                                                if(!empty($valorI)){
                                                    $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                                    $xvlI = $this->validarNaturalezaCredito($xnat, $valorI);
                                                    if(in_array($cuentaDto, $xarCtC)) {
                                                        $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                        if( ($xValor > 0 && $xvlI > 0) || ($xValor < 0 && $xvlI < 0) ){
                                                            $xrsdc = $this->fat->actualizarDataComprobante($xvlI, $xidCnt, $cuentaDto);
                                                            if($xrsdc == true){ $xxx += ($valorI); }
                                                        }else{
                                                            $xrsdc = $this->dtc->guardar($row[2], '', $xvlI,
                                                                $xvlI, $xidCnt, $cuentaDto, $xnat, $row[6], $proyecto,
                                                                $centroCso, 'NULL', 'NULL');
                                                            if($xrsdc == true){ $xxx += ($valorI); }
                                                        }
                                                    }else{
                                                        array_push($xarCtC, $cuentaDto);
                                                        $xrsdc = $this->dtc->guardar($row[2], '', $xvlI,
                                                            $xvlI, $xidCnt, $cuentaDto, $xnat, $row[6], $proyecto,
                                                            $centroCso, 'NULL', 'NULL');
                                                        if($xrsdc == true){ $xxx += ($valorI); }
                                                    }
                                                }
                                            }
                                            if(!empty($cuentaDto)){/* Registro de Impoconsumo */
                                                if(!empty($valorM)){
                                                    $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                                    $xvlM = $this->validarNaturalezaCredito($xnat, $valorM);
                                                    if(!in_array($cuentaDto, $xarCtC)){
                                                        $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                        if( ($xValor > 0 && $xvlM > 0) || ($xValor < 0 && $xvlM < 0) ){
                                                            $xrsdc = $this->fat->actualizarDataComprobante($xvlM, $xidCnt, $cuentaDto);
                                                            if($xrsdc == true){ $xxx += ($valorM); }
                                                        }else{
                                                            $xrsdc = $this->dtc->guardar($row[2], '', $xvlM,
                                                                $xvlM, $xidCnt, $cuentaDto, $xnat, $row[6], $proyecto,
                                                                $centroCso, 'NULL', 'NULL');
                                                            if($xrsdc == true){ $xxx += ($valorM); }
                                                        }
                                                    }else{
                                                        array_push($xarCtC, $cuentaDto);
                                                        $xrsdc = $this->dtc->guardar($row[2], '', $xvlM,
                                                            $xvlM, $xidCnt, $cuentaDto, $xnat, $row[6], $proyecto,
                                                            $centroCso, 'NULL', 'NULL');
                                                        if($xrsdc == true){ $xxx += ($valorM); }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($generar == 1){
                        $xnat  = $this->dtc->obtenerNaturaleza($xcuentaB);
                        $xvalr = $this->validarNaturalezaDebito($xnat, $xxx);
                        $centroCso = $this->cnt->obtenerCentroCosto($param, 'Varios');
                        $this->dtc->guardar($row[2], '', $xvalr, $xvalr, $xidCnt, $xcuentaB, $xnat, $row[6],
                            2147483647, $centroCso, 'NULL', 'NULL');
                    }
                }
            }
        }
        $url = "access.php?controller=Pago&action=vistaGenerarPagosFecha";
        if($x > 0){
            $data = true;
        }else{
            $data = false;
        }
        require_once './vistas/respuesta/index.php';
    }

    public function vistaRecaudoContabilidad(){
        require_once './vistas/pagos/recaudo.bloque.php';
    }

    public function generarContabilidadRecaudosBloque(){
        session_start();
        $fechaI   = $this->formatearFecha($_REQUEST['txtFechaI'], '/');
        $fechaF   = $this->formatearFecha($_REQUEST['txtFechaF'], '/');
        $tipoR    = $this->pag->obtenerTipoRecaudo($_REQUEST['sltTipoXF']);
        $tipoCnt  = $this->pag->obtenerTipoComprobante($tipoR);
        $data     = $this->pag->obtenerPagosFechaTipo($fechaI, $fechaF, $tipoR);
        $banco    = $_REQUEST['sltBanco'];
        $xcuentaB = $this->cnt->obtenerCuentaBanco($banco);
        $param    = $_SESSION['anno'];
        $compania = $_SESSION['compania'];
        $x        = 0;
        foreach ($data as $row){
            $xdc = $this->pag->buscarComprobanteRecaudo($row[0]);
            if(empty($xdc)){
                $xarCtC = array();
                $numero = $row[1];
                $xcnt = $this->cnt->guardar($numero, $row[2], '', $tipoCnt, $param, $compania, $row[3], 1);
                if($xcnt == true){
                    $xidCnt = $this->cnt->obtnerUltimoRegistroTipo($tipoCnt, $numero);
                    $xData  = $this->pag->buscarDetallesPago($row[0]);
                    if(count($xData) > 0){
                        $xxx    = 0;
                        foreach ($xData as $xrow){
                            $valorF = $xrow[2];
                            $valorI = $xrow[3];
                            $valorM = $xrow[4];
                            $xcong = $this->pag->obtenerConfiguracionConcepto($xrow[1], $param);
                            if(count($xcong) > 0){
                                $xconr = $xcong["concepto_rubro"];
                                if(!empty($xconr)){
                                    $xccn  = $this->pag->obtenerConfiguracionCuentasPorConceptoRubro($xconr);
                                    if(count($xccn) > 0) {
                                        $cuentaDto = $xccn['cuenta_debito']; $proyecto = $xccn['proyecto']; $centroCso = $xccn['centrocosto'];
                                        if(!empty($cuentaDto)){/* Registro Debito */
                                            if(!empty($valorF)){
                                                $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                                $xvlD = $this->validarNaturalezaCredito($xnat, $valorF);
                                                if(in_array($cuentaDto, $xarCtC)){
                                                    $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                    if(($xValor > 0 && $xvlD > 0) || ($xValor < 0 && $xvlD < 0)){
                                                        $xrsdc = $this->fat->actualizarDataComprobante($xvlD, $xidCnt, $cuentaDto);
                                                        if($xrsdc == true){ $xxx += ($valorF); }
                                                    }else{
                                                        $xrsdc = $this->dtc->guardar($row[2], '', $xvlD,
                                                            $xvlD, $xidCnt, $cuentaDto, $xnat, $row[3], $proyecto,
                                                            $centroCso, 'NULL', 'NULL');
                                                        if($xrsdc == true){
                                                            $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                            $xxx       += ($valorF);
                                                            $x++;
                                                            $this->pag->actualizarDetalleComprobanteDetallePago($xrow[0], $xcuentDto);
                                                        }
                                                    }
                                                }else{
                                                    array_push($xarCtC, $cuentaDto);
                                                    $xrsdc = $this->dtc->guardar($row[2], '', $xvlD,
                                                        $xvlD, $xidCnt, $cuentaDto, $xnat, $row[3], $proyecto,
                                                        $centroCso, 'NULL', 'NULL');
                                                    if($xrsdc == true){
                                                        $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                        $xxx       += ($valorF);
                                                        $x++;
                                                        $this->pag->actualizarDetalleComprobanteDetallePago($xrow[0], $xcuentDto);
                                                    }
                                                }
                                            }
                                        }
                                        if(!empty($cuentaDto)){/* Registro de Iva */
                                            if(!empty($valorI)){
                                                $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                                $xvlI = $this->validarNaturalezaCredito($xnat, $valorI);
                                                if(in_array($cuentaDto, $xarCtC)) {
                                                    $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                    if( ($xValor > 0 && $xvlI > 0) || ($xValor < 0 && $xvlI < 0) ){
                                                        $xrsdc = $this->fat->actualizarDataComprobante($xvlI, $xidCnt, $cuentaDto);
                                                        if($xrsdc == true){ $xxx += ($valorI); }
                                                    }else{
                                                        $xrsdc = $this->dtc->guardar($row[2], '', $xvlI,
                                                            $xvlI, $xidCnt, $cuentaDto, $xnat, $row[3], $proyecto,
                                                            $centroCso, 'NULL', 'NULL');
                                                        if($xrsdc == true){ $xxx += ($valorI); }
                                                    }
                                                }else{
                                                    array_push($xarCtC, $cuentaDto);
                                                    $xrsdc = $this->dtc->guardar($row[2], '', $xvlI,
                                                        $xvlI, $xidCnt, $cuentaDto, $xnat, $row[3], $proyecto,
                                                        $centroCso, 'NULL', 'NULL');
                                                    if($xrsdc == true){ $xxx += ($valorI); }
                                                }
                                            }
                                        }
                                        if(!empty($cuentaDto)){/* Registro de Impoconsumo */
                                            if(!empty($valorM)){
                                                $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                                $xvlM = $this->validarNaturalezaCredito($xnat, $valorM);
                                                if(!in_array($cuentaDto, $xarCtC)){
                                                    $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                    if( ($xValor > 0 && $xvlM > 0) || ($xValor < 0 && $xvlM < 0) ){
                                                        $xrsdc = $this->fat->actualizarDataComprobante($xvlM, $xidCnt, $cuentaDto);
                                                        if($xrsdc == true){ $xxx += ($valorM); }
                                                    }else{
                                                        $xrsdc = $this->dtc->guardar($row[2], '', $xvlM,
                                                            $xvlM, $xidCnt, $cuentaDto, $xnat, $row[3], $proyecto,
                                                            $centroCso, 'NULL', 'NULL');
                                                        if($xrsdc == true){ $xxx += ($valorM); }
                                                    }
                                                }else{
                                                    array_push($xarCtC, $cuentaDto);
                                                    $xrsdc = $this->dtc->guardar($row[2], '', $xvlM,
                                                        $xvlM, $xidCnt, $cuentaDto, $xnat, $row[3], $proyecto,
                                                        $centroCso, 'NULL', 'NULL');
                                                    if($xrsdc == true){ $xxx += ($valorM); }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if(!empty($xcuentaB)){
                            $xnat  = $this->dtc->obtenerNaturaleza($xcuentaB);
                            $xvalr = $this->validarNaturalezaDebito($xnat, $xxx);
                            $centroCso = $this->cnt->obtenerCentroCosto($param, 'Varios');
                            $this->dtc->guardar($row[2], '', $xvalr, $xvalr, $xidCnt, $xcuentaB, $xnat, $row[3],
                                2147483647, $centroCso, 'NULL', 'NULL');
                        }
                    }
                }
            }
        }
        $url = "access.php?controller=Pago&action=vistaRecaudoContabilidad";
        if($x > 0){
            $data = true;
        }else{
            $data = false;
        }
        require_once './vistas/respuesta/index.php';
    }

    public function listadoEntreFechasPdf(){
        ini_set('max_execution_time', 0);
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/recaudo/recaudo.fechas.php';
    }

    public function listadoEntreFechasExcel(){
        ini_set('max_execution_time', 0);
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ListadoEntreFechas.xls");
        require_once './vistas/recaudo/recaudo.fechasX.php';
    }
}