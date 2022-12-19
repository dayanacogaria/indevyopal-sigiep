<?php
require_once './modelFactura/factura.php';
require_once './modelFactura/tipoFactura.php';
require_once './modelFactura/devolutivos.php';
require_once './modelFactura/movimiento.php';
require_once './modelFactura/pago.php';
require_once './modelFactura/detallefactura.php';
require_once './modelFactura/detallemovnto.php';
require_once './modelFactura/comprobanteContable.php';
require_once './modelFactura/detallecnt.php';
class devolutivosController{
    private $fat;
    private $tpf;
    private $dev;
    private $mov;
    private $pag;
    private $dft;
    private $dmv;
    private $cnt;
    private $dtc;

    public function __construct(){
        $this->fat = new factura();
        $this->tpf = new tipoFactura();
        $this->dev = new devolutivos();
        $this->mov = new movimiento();
        $this->pag = new pago();
        $this->dft = new detalleFactura();
        $this->dmv = new detallemovimiento();
        $this->cnt = new comprobanteContable();
        $this->dtc = new detalleCnt();
    }

    public function index(){
        list($aso, $xaso, $tipo, $nomTp, $numero, $fecha, $tercero, $nomT, $id, $desc) = array(0, "", 0, 0, "", "", 0, "", 0, "");
        if(!empty($_REQUEST['factura'])){
            $data = $this->dev->obtnerDataFactura($_REQUEST['factura']);
            list($aso, $xaso, $fecha, $tercero, $nomT,$desc)
                = array($data[0], "$data[20] $data[2]", $data[5], $data[3], "$data[14] $data[15]", $data[7]);
        }elseif(!empty($_REQUEST['dev'])){
            $data = $this->dev->obtnerDataFactura($_REQUEST['dev']);
            list($tipo, $nomTp, $numero, $fecha, $tercero, $nomT, $id, $desc)
                = array($data[1], $data[20], $data[2], $data[5], $data[3], "$data[14] $data[15]", $data[0], $data[7]);
        }
        $tipos = $this->tpf->listarTodo("1, 2, 3");
        $tipoD = $this->tpf->listarTodo("7");
        require_once './vistas/devolutivos/index.php';
    } 

    public function buscarFacturas(){
        $html = "";
        if(!empty($_REQUEST['tipo'])){
            $html .= "<option value=''>Factura</option>";
            $data  = $this->dev->buscarFacturasTipo($_REQUEST['tipo']);
            if(count($data)){
                foreach ($data as $row){
                    $html .= "<option value='$row[0]'>$row[1]</option>";
                }
            }else{
                $html .= "<option value=''>No hay facturas</option>";
            }
        }else{
            $html .= "<option value=''>No hay facturas</option>";
        }
        echo $html;
    }

    public function generarConsecutivo(){
        @session_start();
        echo $this->fat->validarNumeroFactura($_REQUEST['tipo'], $_SESSION['anno']);
    }

    public function Procesar(){
        @session_start();
        $param = $_SESSION['anno'];
        $compn = $_SESSION['compania'];
        $usTer = $_SESSION['usuario_tercero'];
        $json  = json_decode($_REQUEST['Detalles']);
        if(count($json->datos) > 0){
            $xCentro = $this->mov->obtenerCentroCosto($param, 'VARIOS');
            $xProyto = $this->mov->obtenerProyecto('VARIOS');
            //$xOtrosD = $this->dev->datosAfectado('VARIOS');
            $data    = $this->dev->guardarFact($_REQUEST['numeroDt'], $_REQUEST['tipoDevt'], $_REQUEST['terceroD'],
                $_REQUEST['fechaDev'], 4, $usTer, $usTer, $param, $xCentro,$_REQUEST['desc']);
             
            if($data == true){
                $tipoM  = $this->dev->obtenerTipoMovimiento($_REQUEST['tipoDevt']);
                $xnumM  = $_REQUEST['numeroDt'];
                $xDepdc = $this->mov->obtenerDependenciaTercero($usTer);
                $xMov   = $this->mov->guardar($tipoM, $xnumM, $_REQUEST['fechaDev'], 2, '', '',
                    $param, $compn, $usTer, $usTer, $xCentro, $xProyto, $xDepdc);
                  
                if($xMov == true){
                    $idMov = $this->mov->obtenerUltimo($tipoM);
                }else{
                    $idMov = 'NULL';
                }
                $id_fat = $this->dev->obtenerUltimoIdTipo($_REQUEST['tipoDevt']);
                foreach ($json as $row){
                    foreach ($row as $item){
                        $id    = $item->id;
                        $cant  = $item->Cant;
                        $valor  = $item->Valor;
                        $valor = str_replace(".", '', $valor);
                        $valor = str_replace(",", ".", $valor);
                        $iva  = $item->Iva;
                        $xData = $this->dev->obtenerData($id);
                        $xDtm  = 'NULL';
                        if(!empty($idMov)){
                            $factor = $this->dev->obtenerUnidadFactor($xData[6], $xData[0]);
                            $xxx    = ($cant * $factor);
                            $xDmv = $this->dmv->guardar($xxx, $xData[5], 0, $idMov, $xData[9], $xData[6], $cant);
                            if($xDmv == true){
                                $xDtm = $this->dmv->obtenerUltimoRegistro($idMov);
                            }
                        }
                        $xValor = ($valor * -1);
                        $xIva   = ($iva * -1);
                        $xImp   = ($xData[4] * -1);
                        $xt1    = ($valor * $cant) +($iva * $cant) +($xData[4] * $cant) ;
                        $xTotal = ($xt1 * -1);
                        $valorO = ($valor + $iva + $xData[4]);
                        $xDft   = $this->dev->registrarData($id_fat, $xData[0],$xValor, $cant,$xIva, $xImp, 0,
                           $xTotal, 'NULL', $xDtm, $id, 0,$xData[11], $valorO, '"'.$xData[13].'"');

                        $data    = $this->dev->ActualizarFact($id_fat, $xData[14], $xData[15]);
                    }
                }
            }
            $url = "access.php?controller=Devolutivos&action=index";
            $url .= !empty($id_fat)?'&dev='.md5($id_fat):'';
            $url .= !empty($idMov)?'&mov='.md5($idMov[0]):'';
            echo $url;
        }
    }

    public function tipoMovimientoClase(){
        $data = $this->mov->obtenerTipoMovClase($_REQUEST['clase']);
        $html = "<option value=''>Tipo Movimiento</option>";
        foreach ($data as $row){
            $html .= "<option value='$row[0]'>$row[1]</option>";
        }
        echo $html;
    }

    public function buscarDevs(){
        if(!empty($_REQUEST['factura'])){
            $factura = trim($_REQUEST['factura']);
            $dataFR  = $this->fat->obtenerRelacionFactura($factura);
            $idMov   = $this->fat->obtenerMovAlmacen($factura);
            if(empty($idMov)){
                $dataF = $this->fat->obtener($_REQUEST['factura']);
                $idMov = $this->mov->obtenerIdNumFactura($dataF['num']);
            }
            $url     = "access.php?controller=Devolutivos&action=index&dev=".md5($factura);
            if(count($dataFR) > 0){
                $url .= !empty($dataFR[0])?'&cnt='.md5($dataFR[0]):'';
                $url .= !empty($dataFR[1])?'&pptal='.md5($dataFR[1]):'';
            }else{
                $dataC = $this->fat->buscarRelacionCnt($factura);
                $url  .= !empty($dataC)?'&cnt='.md5($dataC):'';
            }
            $url .= !empty($idMov)?'&mov='.md5($idMov):'';
            echo $url;
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

    public function procesarContabilidad(){
        @session_start();
        $param  = $_SESSION['anno'];
        $compn  = $_SESSION['compania'];
        $data   = $this->fat->obtnerDataFactura($_REQUEST['dev']);
        $xRCnt  = $this->fat->buscarRelacionCnt($data[0]);
        if(empty($xRCnt) OR $xRCnt === 0 OR $xRCnt == ' '){
            $xTipoC = $this->fat->obtnerTipoComprobanteCnt($data[1]);
            if(!empty($xTipoC)){
                $xnum = $this->cnt->validarNumero($xTipoC, $param);
                $xCnt = $this->cnt->guardar($xnum, $data[22],'', $xTipoC, $param, $compn, $data[3], 1);
                if($xCnt == true){
                    $xCentro = $this->mov->obtenerCentroCosto($param, 'VARIOS');
                    $xProyto = $this->mov->obtenerProyecto('VARIOS');
                    $xIdCnt  = $this->cnt->obtnerUltimoRegistroTipo($xTipoC, $xnum);
                    $xData   = $this->fat->obtenerDetallesFactura($data[0]);
                    $xarCtD  = array(); $xarCtC = array();
                    foreach ($xData as $row){
                        $xcong = $this->pag->obtenerConfiguracionConcepto($row[1], $param);
                        $xconr = $xcong["concepto_rubro"];
                        if(!empty($xconr)){
                            $xccn  = $this->pag->obtenerConfiguracionCuentasPorConceptoRubro($xconr);
                            if(count($xccn) > 0){
                                $cuentaDto = $xccn['cuenta_debito']; $cuentaCto = $xccn['cuenta_credito'];
                                $cuentaIva = $xccn['cuenta_iva'];    $cuentaImp = $xccn['cuenta_impoconsumo'];
                                list($xcuentDto, $xcuentIva, $xctaDtI, $xCtaDm,  $xcuentCto, $xCtaI)
                                    = array('NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL');
                                if($cuentaDto != $cuentaCto){
                                    if(!empty($cuentaDto)){
                                        if(!empty($row[7])){
                                            $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                            $xVlD = $this->validarNaturalezaDebito($xnat, $row[7] * $row[4]);
                                            if(in_array($cuentaDto, $xarCtD)){
                                                $xValor = $this->fat->obtenerValorDetalleCnt($xIdCnt, $cuentaDto);
                                                if( ($xVlD > 0 && $xValor > 0) || ($xVlD < 0 && $xValor < 0) ){
                                                    $this->fat->actualizarDataComprobante($xVlD, $xIdCnt, $cuentaDto);
                                                }else{
                                                    $xDtCdto = $this->dtc->guardar($data[22], '', $xVlD, $xVlD, $xIdCnt,
                                                        $cuentaDto, $xnat, $data[3], $xProyto, $xCentro, 'NULL', 'NULL');
                                                    if($xDtCdto == true){
                                                        $xcuentDto = $this->dtc->obtenerUltimoRegistro($xIdCnt);
                                                        $this->fat->actualizarDetalleCnt($row[0], $xcuentDto);
                                                    }
                                                }
                                            }else{
                                                array_push($xarCtD, $cuentaDto);
                                                $xDtCdto = $this->dtc->guardar($data[22], '', $xVlD, $xVlD, $xIdCnt,
                                                    $cuentaDto, $xnat, $data[3], $xProyto, $xCentro, 'NULL', 'NULL');
                                                if($xDtCdto == true){
                                                    $xcuentDto = $this->dtc->obtenerUltimoRegistro($xIdCnt);
                                                    $this->fat->actualizarDetalleCnt($row[0], $xcuentDto);
                                                }
                                            }
                                        }
                                    }

                                    if(!empty($cuentaCto)){
                                        if(!empty($row[7])){
                                            $xnat = $this->dtc->obtenerNaturaleza($cuentaCto);
                                            $xvlC = $this->validarNaturalezaCredito($xnat, $row[7] * $row[4]);
                                            if(in_array($cuentaCto, $xarCtC)){
                                                $xValor = $this->fat->obtenerValorDetalleCnt($xIdCnt, $cuentaCto);
                                                if( ($xValor > 0 && $xvlC > 0) || ($xValor < 0 && $xvlC < 0) ){
                                                    $this->fat->actualizarDataComprobante($xvlC, $xIdCnt, $cuentaCto);
                                                }else{
                                                    $xCtCto = $this->dtc->guardar($data[22], '', $xvlC, $xvlC, $xIdCnt,
                                                        $cuentaCto, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xcuentDto);
                                                    if($xCtCto == true){ $xcuentCto = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                                }
                                            }else{
                                                array_push($xarCtC, $cuentaCto);
                                                $xCtCto = $this->dtc->guardar($data[22], '', $xvlC, $xvlC, $xIdCnt,
                                                    $cuentaCto, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xcuentDto);
                                                if($xCtCto == true){ $xcuentCto = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                            }
                                        }
                                    }
                                }

                                if(!empty($cuentaImp)){
                                    if(!empty($row[5])){
                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaDto);
                                        $xvlDi = $this->validarNaturalezaDebito($xnat, $row[5] * $row[4]);
                                        if(in_array($cuentaDto, $xarCtD)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xIdCnt, $cuentaDto);
                                            if( ($xValor > 0 && $xvlDi > 0) || ($xValor < 0 && $xvlDi < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDi, $xIdCnt, $cuentaDto);
                                            }else{
                                                $xDti = $this->dtc->guardar($data[22], '', $xvlDi, $xvlDi, $xIdCnt,
                                                    $cuentaDto, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xcuentCto);
                                                if($xDti == true){ $xctaDtI = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                            }
                                        }else{
                                            array_push($xarCtD, $cuentaDto);
                                            $xDti = $this->dtc->guardar($data[22], '', $xvlDi, $xvlDi, $xIdCnt,
                                                $cuentaDto, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xcuentCto);
                                            if($xDti == true){ $xctaDtI = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                        }
                                    }

                                    $xnat  = $this->dtc->obtenerNaturaleza($cuentaIva);
                                    $xvlDi = $this->validarNaturalezaCredito($xnat, $row[5] * $row[4]);
                                    if(in_array($cuentaIva, $xarCtC)){
                                        $xValor = $this->fat->obtenerValorDetalleCnt($xIdCnt, $cuentaIva);
                                        if( ($xValor > 0 && $xvlDi > 0) || ($xValor < 0 & $xvlDi < 0) ){
                                            $this->fat->actualizarDataComprobante($xvlDi, $xIdCnt, $cuentaIva);
                                        }else{
                                            $xCti = $this->dtc->guardar($data[22], '', $xvlDi, $xvlDi, $xIdCnt,
                                                $cuentaImp, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xctaDtI);
                                            if($xCti == true){ $xCtaI = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                        }
                                    }else{
                                        array_push($xarCtC, $cuentaIva);
                                        $xCti = $this->dtc->guardar($data[22], '', $xvlDi, $xvlDi, $xIdCnt,
                                            $cuentaImp, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xctaDtI);
                                        if($xCti == true){ $xCtaI = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                    }
                                }

                                if(!empty($cuentaImp)){
                                    if(!empty($row[6])){
                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaDto);
                                        $xvlDX = $this->validarNaturalezaDebito($xnat, $row[6] * $row[4]);
                                        if(in_array($cuentaDto, $xarCtD)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xIdCnt, $cuentaDto);
                                            if( ($xValor > 0 && $xvlDX > 0) || ($xValor < 0 && $xvlDX < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDX, $xIdCnt, $cuentaDto);
                                            }else{
                                                $xDtM = $this->dtc->guardar($data[22], '', $xvlDX, $xvlDX, $xIdCnt,
                                                    $cuentaImp, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xCtaI);
                                                if($xDtM == true){ $xCtaDm = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                            }
                                        }else{
                                            array_push($xarCtD, $cuentaDto);
                                            $xDtM = $this->dtc->guardar($data[22], '', $xvlDX, $xvlDX, $xIdCnt,
                                                $cuentaImp, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xCtaI);
                                            if($xDtM == true){ $xCtaDm = $this->dtc->obtenerUltimoRegistro($xIdCnt); }
                                        }

                                        $xnat  = $this->dtc->obtenerNaturaleza($cuentaImp);
                                        $xvlDX = $this->validarNaturalezaCredito($xnat, $row[6] * $row[4]);
                                        if(in_array($cuentaImp, $xarCtC)){
                                            $xValor = $this->fat->obtenerValorDetalleCnt($xIdCnt, $cuentaImp);
                                            if( ($xValor > 0 && $xvlDX > 0) || ($xValor < 0 && $xvlDX < 0) ){
                                                $this->fat->actualizarDataComprobante($xvlDX, $xIdCnt, $cuentaImp);
                                            }else{
                                                $this->dtc->guardar($data[22], '', $xvlDX, $xvlDX, $xIdCnt,
                                                    $cuentaImp, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xCtaDm);
                                            }
                                        }else{
                                            array_push($xarCtC, $cuentaImp);
                                            $this->dtc->guardar($data[22], '', $xvlDX, $xvlDX, $xIdCnt,
                                                $cuentaImp, $xnat, $data[3], $xProyto, $xCentro, 'NULL', $xCtaDm);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function procesarRecaudo(){
        @session_start();
        $param    = $_SESSION['anno'];
        $compn    = $_SESSION['compania'];
        $dataF    = $this->fat->obtnerDataFactura($_REQUEST['fat']);
        $xDataA   = $this->fat->obtenerFacturaAso($dataF[0]);
        $xPagoF   = $this->pag->buscarPagoFactura($dataF[0]);
        if(empty($xPagoF)){
            $pago     = $this->fat->obtenerRecaudoFactura($xDataA);
            $xDtPg    = $this->pag->obtenerDataPago($pago);
            $tipoR    = $this->pag->obtenerTipoRecaudo($dataF[1]);
            $tipoCnt  = $this->pag->obtenerTipoComprobante($tipoR);
            $xnumro   = $this->pag->validarNumero($tipoR, $param);
            $xcuentaB = $this->cnt->obtenerCuentaBanco($xDtPg[1]);
            $xPago    = $this->pag->guardar($xnumro, $tipoR, $_SESSION['usuario_tercero'], $dataF[22], $xDtPg[1], 1,
                $param, $_SESSION['usuario_tercero']);
            if($xPago == true){
                $xidPag = $this->pag->obtenerMaxId($tipoR);
                $xidCnt = 'NULL'; $xarCtC = array();
                $nro    = $this->cnt->validarNumero($tipoCnt, $param);
                $xcnt   = $this->cnt->guardar($nro, $dataF[22], '', $tipoCnt, $param, $compn, $dataF[3], 1);
                if($xcnt == true){
                    $xidCnt = $this->cnt->obtnerUltimoRegistroTipo($tipoCnt, $nro);
                }
                $xdata = $this->fat->obtenerDetallesFactura($dataF[0]);
                $xxx    = 0;
                foreach ($xdata as $xrow){
                    $valorF = $xrow[7] * $xrow[4];
                    $valorI = $xrow[5] * $xrow[4];
                    $valorM = $xrow[6] * $xrow[4];
                    $xresdp = $this->pag->guardar_detalle($xrow[0], $valorF, $valorI, $valorM, 0, 0, $xidPag, 'NULL');
                    if($xresdp == true){
                        $idDpg = $this->pag->obtenerUltimoRegistroPago($xidPag);
                        $xcong = $this->pag->obtenerConfiguracionConcepto($xrow[1], $param);
                        if(count($xcong) > 0){
                            $xconr = $xcong["concepto_rubro"];
                            if(!empty($xconr)){
                                $xccn  = $this->pag->obtenerConfiguracionCuentasPorConceptoRubro($xconr);
                                if(count($xccn) > 0){
                                    $cuentaDto = $xccn['cuenta_debito'];$proyecto  = $xccn['proyecto'];
                                    $centroCso = $xccn['centrocosto'];
                                    if(!empty($cuentaDto)){/* Registro Debito */
                                        if(!empty($valorF)){
                                            $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                            $xvlD = $this->validarNaturalezaCredito($xnat, $valorF);
                                            if(in_array($cuentaDto, $xarCtC)){
                                                $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                if(($xValor > 0 && $xvlD > 0) || ($xValor < 0 && $xvlD < 0)){
                                                    $xrsdc = $this->fat->actualizarDataComprobante($xvlD, $xidCnt, $cuentaDto);
                                                    if($xrsdc == true){ $xxx += ($valorF * -1); }
                                                }else{
                                                    $xrsdc = $this->dtc->guardar($dataF[22], '', $xvlD,
                                                        $xvlD, $xidCnt, $cuentaDto, $xnat, $dataF[3], $proyecto,
                                                        $centroCso, 'NULL', 'NULL');
                                                    if($xrsdc == true){
                                                        $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                        $xxx       += ($valorF * -1);
                                                        $this->pag->actualizarDetalleComprobanteDetallePago($idDpg, $xcuentDto);
                                                    }
                                                }
                                            }else{
                                                array_push($xarCtC, $cuentaDto);
                                                $xrsdc = $this->dtc->guardar($dataF[22], '', $xvlD,
                                                    $xvlD, $xidCnt, $cuentaDto, $xnat, $dataF[3], $proyecto,
                                                    $centroCso, 'NULL', 'NULL');
                                                if($xrsdc == true){
                                                    $xcuentDto = $this->dtc->obtenerUltimoRegistro($xidCnt);
                                                    $xxx       += ($valorF * -1);
                                                    $this->pag->actualizarDetalleComprobanteDetallePago($idDpg, $xcuentDto);
                                                }
                                            }
                                        }
                                    }
                                    if(!empty($cuentaDto)) {/* Registro de Iva */
                                        if(!empty($valorI)){
                                            $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                            $xvlI = $this->validarNaturalezaCredito($xnat, $valorI);
                                            if(in_array($cuentaDto, $xarCtC)) {
                                                $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                if( ($xValor > 0 && $xvlI > 0) || ($xValor < 0 && $xvlI < 0) ){
                                                    $xrsdc = $this->fat->actualizarDataComprobante($xvlI, $xidCnt, $cuentaDto);
                                                    if($xrsdc == true){ $xxx += ($valorI * -1); }
                                                }else{
                                                    $xrsdc = $this->dtc->guardar($dataF[22], '', $xvlI,
                                                        $xvlI, $xidCnt, $cuentaDto, $xnat, $dataF[3], $proyecto,
                                                        $centroCso, 'NULL', 'NULL');
                                                    if($xrsdc == true){ $xxx += ($valorI * -1); }
                                                }
                                            }else{
                                                array_push($xarCtC, $cuentaDto);
                                                $xrsdc = $this->dtc->guardar($dataF[22], '', $xvlI,
                                                    $xvlI, $xidCnt, $cuentaDto, $xnat, $dataF[3], $proyecto,
                                                    $centroCso, 'NULL', 'NULL');
                                                if($xrsdc == true){ $xxx += ($valorI * -1); }
                                            }
                                        }
                                    }
                                    if(!empty($cuentaDto)) {/* Registro de Impoconsumo */
                                        if(!empty($valorM)){
                                            $xnat = $this->dtc->obtenerNaturaleza($cuentaDto);
                                            $xvlM = $this->validarNaturalezaCredito($xnat, $valorM);
                                            if(!in_array($cuentaDto, $xarCtC)){
                                                $xValor = $this->fat->obtenerValorDetalleCnt($xidCnt, $cuentaDto);
                                                if( ($xValor > 0 && $xvlM > 0) || ($xValor < 0 && $xvlM < 0) ){
                                                    $xrsdc = $this->fat->actualizarDataComprobante($xvlM, $xidCnt, $cuentaDto);
                                                    if($xrsdc == true){ $xxx += ($valorM * -1); }
                                                }else{
                                                    $xrsdc = $this->dtc->guardar($dataF[22], '', $xvlM,
                                                        $xvlM, $xidCnt, $cuentaDto, $xnat, $dataF[3], $proyecto,
                                                        $centroCso, 'NULL', 'NULL');
                                                    if($xrsdc == true){ $xxx += ($valorM * -1); }
                                                }
                                            }else{
                                                array_push($xarCtC, $cuentaDto);
                                                $xrsdc = $this->dtc->guardar($dataF[22], '', $xvlM,
                                                    $xvlM, $xidCnt, $cuentaDto, $xnat, $dataF[3], $proyecto,
                                                    $centroCso, 'NULL', 'NULL');
                                                if($xrsdc == true){ $xxx += ($valorM * -1); }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $xnat      = $this->dtc->obtenerNaturaleza($xcuentaB);
                $xvalr     = $this->validarNaturalezaDebito($xnat, ($xxx * -1));
                $centroCso = $this->cnt->obtenerCentroCosto($param, 'Varios');
                $this->dtc->guardar($dataF[22], '', $xvalr, $xvalr, $xidCnt, $xcuentaB, $xnat, $dataF[3],
                    2147483647, $centroCso, 'NULL', 'NULL');
            }
        }

        $url = "access.php?controller=Devolutivos&action=index&dev=".$_REQUEST['fat']."&mov=".$_REQUEST['mov'];
        $dataFR  = $this->fat->obtenerRelacionFactura($dataF[0]);
        if(count($dataFR) > 0){
            $url .= !empty($dataFR[0])?'&cnt='.md5($dataFR[0]):'';
            $url .= !empty($dataFR[1])?'&pptal='.md5($dataFR[1]):'';
        }else{
            $dataC = $this->fat->buscarRelacionCnt($dataF[0]);
            $url  .= !empty($dataC)?'&cnt='.md5($dataC):'';
        }
        echo $url;
    }
}