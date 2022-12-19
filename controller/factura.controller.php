<?php
require_once './modelFactura/factura.php';
require_once './modelFactura/comprobanteContable.php';
require_once './modelFactura/comprobantePptal.php';
require_once './modelFactura/movimiento.php';
require_once './modelFactura/detallefactura.php';
require_once './modelFactura/detallemovnto.php';
require_once './modelFactura/concepto.php';
require_once './modelFactura/tipoFactura.php';
require_once './clases/almacen.php';
date_default_timezone_set('America/Bogota');
class facturaController{
    private $factura;
    private $cnt;
    private $pptal;
    private $mov;
    private $dtf;
    private $dtm;
    private $cpt;
    private $tpf;
    private $alm;

    public function __construct(){
        $this->factura = new factura();
        $this->cnt     = new comprobanteContable();
        $this->pptal   = new comprobantePptal();
        $this->mov     = new movimiento();
        $this->dtf     = new detalleFactura();
        $this->dtm     = new detallemovimiento();
        $this->cpt     = new concepto();
        $this->tpf     = new tipoFactura();
        $this->alm     = new almacen();
    }

    public function Index(){
        require_once 'registrar_GF_FACTURA.php';
    }

    public function registrar(){
        session_start();
        $numero             = $_POST['txtNumeroF'];
        $tipo               = $_POST['sltTipoFactura'];
        $tercero            = $_POST['sltTercero'];
        $fecha              = explode("/" ,$_POST['fecha']);
        $fecha              = "$fecha[2]-$fecha[1]-$fecha[0]";
        $fechav             = explode("/",$_POST['fechaV']);
        $fechavencimiento   = "$fechav[2]-$fechav[1]-$fechav[0]";
        $centrocosto        = $_POST['sltCentroCosto'];
        $descripcion        = $_POST['txtDescripcion'];
        $estado             = "4";
        $responsable        = $_SESSION['usuario_tercero'];
        $parametrizacionano = $_SESSION['anno'];
        $compania           = $_SESSION['compania'];
        $observaciones      = '';
        $descuento          = !empty($_REQUEST['txtDescuento'])?$_REQUEST['txtDescuento']:'NULL';

        if(!empty($_POST['sltVendedor'])){
            $vendedor = $_POST['sltVendedor'];
        }else{
            $vendedor = "NULL";
        }

        $ccnt = new comprobanteContable();
        $cppl = new comprobantePptal();
        $mov  = new movimiento();

        $result = $this->factura->InsertarData(
            $numero, $tipo, $tercero, $fecha, $fechavencimiento, $centrocosto, $descripcion, $estado,
            $responsable, $vendedor, $parametrizacionano, $descuento
        );

        if($result == true){
            $idF = $this->factura->obtnerUltimaFacturaTN($tipo, $numero);
            $tpC = $this->factura->obtnerTipoComprobanteCnt($tipo);
            $tpP = $this->cnt->obtnerTipoPptal($tpC);
            $tpM = $this->factura->obtenerTipoMovimiento($tipo);
            $idC = 0;
            $idP = 0;
            if(!empty($tpC)){
                $ccnt->numero              = $numero;
                $ccnt->fecha               = $fecha;
                $ccnt->descripcion         = $descripcion;
                $ccnt->tipocomprobante     = $tpC;
                $ccnt->parametrizacionanno = $parametrizacionano;
                $ccnt->compania            = $compania;
                $ccnt->tercero             = $tercero;
                $ccnt->estado              = 1;

                $this->cnt->registrar($ccnt);

                $idC = $this->cnt->obtnerUltimoRegistroTipo($tpC, $numero);
            }

            if(!empty($tpP)){
                $cppl->numero              = $numero;
                $cppl->fecha               = $fecha;
                $cppl->fechavencimiento    = "$fechav[2]-$fechav[1]-$fechav[0]";
                $cppl->descripcion         = $descripcion;
                $cppl->parametrizacionanno = $parametrizacionano;
                $cppl->tipocomprobante     = $tpP;
                $cppl->tercero             = $tercero;
                $cppl->estado              = 1;
                $cppl->responsable         = $responsable;

                $this->pptal->registrar($cppl);

                $idP = $this->pptal->obtnerUltimoRegistroTipoN($tpP, $numero);
            }

            if(!empty($tpM)){
                $num  = $this->factura->validarNumero($tpM, $parametrizacionano);
                $dep  = $this->mov->obnterDependencia($_SESSION['usuario_tercero']);
                $remo = $this->mov->guardar($tpM, $num, $fecha, 2, $descripcion, $observaciones, $_SESSION['anno'], $compania, $tercero, $_SESSION['usuario_tercero'], $centrocosto, 2147483647, $dep);
                $idM  = $this->mov->obtnerUltimoRegistro($tpM);
            }

            $url   = 'Location:json/registrarFacturaJson.php?action=registrado&factura='.md5($idF);
            $url  .= !empty($idC)?'&cnt='.md5($idC):'';
            $url  .= !empty($idP)?'&pptal='.md5($idP):'';
            $url  .= !empty($idM)?'&mov='.md5($idM):'';
            $url  .= !empty($_REQUEST['peso'])?'&peso='.$_REQUEST['peso']:'';
            header($url);
        }else{
            header('Location:json/registrarFacturaJson.php?action=noregistrado');
        }
    }

    public function modificar(){
        $id_unico         = $_POST['id'];
        $valorF           = explode("/", $_POST['fecha']);
        $fecha            = "$valorF[2]-$valorF[1]-$valorF[0]";
        $descripcion      = $_POST['descripcion'];
        if(empty($descripcion) ){
            $descripcion ="NULL";
        } else {
            $descripcion ="$descripcion";
        }
        $valorV           = explode("/", $_POST['fechaVencimiento']);
        $fechavencimiento =  "$valorV[2]-$valorV[1]-$valorV[0]";
        $tercero          =$_REQUEST['tercero'];
        $vendedor         =$_REQUEST['vendedor'];
        $result           = $this->factura->modificar($id_unico, $fecha, $fechavencimiento, $descripcion, $tercero, $vendedor);

        if(!empty($_POST['id_cnt'])){
            $this->cnt->modificar($_POST['id_cnt'], $fecha, $descripcion, $tercero);
        }

        if(!empty($_POST['id_pptal'])){
            $this->pptal->modificar($_POST['id_pptal'], $fecha, $fechavencimiento, $descripcion, $tercero);
        }

        if(!empty($_POST['mov'])){
            $this->mov->modificar($fecha, $descripcion, $descripcion, $tercero, $_POST['mov']);
        }

        echo json_encode($result);
    }

    public function obtenerClaseFactura(){
        if(!empty($_REQUEST['id_tipo'])){
            echo $this->factura->obtnerClaseFactura($_REQUEST['id_tipo']);
        }
    }

    public function obtenerUrlFactura(){
        if(!empty($_REQUEST['factura'])){
            $factura = $_REQUEST['factura'];
            $dataFR  = $this->factura->obtenerRelacionFactura($factura);
            $idMov   = $this->factura->obtenerMovAlmacen($factura);
            if(empty($idMov)){
                $dataF = $this->factura->obtener($_REQUEST['factura']);
                $idMov = $this->mov->obtenerIdNumFactura($dataF['num']);
            }
            $url     = "?factura=".md5($factura);
            if(count($dataFR) > 0){
                $url .= !empty($dataFR[0])?'&cnt='.md5($dataFR[0]):'';
                $url .= !empty($dataFR[1])?'&pptal='.md5($dataFR[1]):'';
            }else{
                $dataC = $this->factura->buscarRelacionCnt($factura);
                $url  .= !empty($dataC)?'&cnt='.md5($dataC):'';
            }
            $url .= !empty($idMov)?'&mov='.md5($idMov):'';
            echo $url;
        }
    }

    public function obtenerUnidadesConcepto(){
        $data = $this->factura->obtenerUnidadesConcepto($_REQUEST['concepto']);
        $html = "";
        foreach ($data as $row){
            $html .= "<option value='$row[0]'>$row[1]</option>";
        }
        echo $html;
    }

    public function obtenerDependeciaResponsable(){
        try {
            session_start();
            $data = $this->factura->obtenerDependenciasResponsable($_SESSION['usuario_tercero']);
            $html = "";
            if(count($data) > 0){
                foreach ($data as $row){
                    $html .= "<option value='$row[0]'>".mb_strtoupper($row[1])."</option>";
                }
            }else {
                $data = $this->factura->obtenerDependencias();
                $html .= "<option value=''>Dependencia</option>";
                foreach ($data as $row){
                    $html .= "<option value='$row[0]'>".mb_strtoupper($row[1])."</option>";
                }
            }
            echo $html;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTercerosResponsable(){
        try {
            session_start();
            $data = $this->factura->obtenerDataTercero($_SESSION['usuario_tercero']);
            $html = "<option value='$data[0]'>$data[1]</option>";
            echo $html;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarRemision(){
        if(!empty($_REQUEST['factura'])){
            $factura = $_REQUEST['factura'];
            $dataFR  = $this->factura->obtenerRelacionFactura($factura);
            $idMov   = $this->factura->obtenerMovAlmacen($factura);
            if(empty($idMov)){
                $dataF = $this->factura->obtener($_REQUEST['factura']);
                $idMov = $this->mov->obtenerIdNumFactura($dataF['num']);
            }
            $url     = "registrar_GF_FACTURA_REMISION.php?factura=".md5($factura);
            if(count($dataFR) > 0){
                $url .= !empty($dataFR[0])?'&cnt='.md5($dataFR[0]):'';
                $url .= !empty($dataFR[1])?'&pptal='.md5($dataFR[1]):'';
            }else{
                $dataC = $this->factura->buscarRelacionCnt($factura);
                $url  .= !empty($dataC)?'&cnt='.md5($dataC):'';
            }
            $url .= !empty($idMov)?'&mov='.md5($idMov):'';
            echo $url;
        }
    }

    public function buscarTraslado(){
        if(!empty($_REQUEST['factura'])){
            $factura = $_REQUEST['factura'];
            $idMov   = $this->factura->obtenerMovAlmacen($factura);
            if(empty($idMov)){
                $dataF = $this->factura->obtener($_REQUEST['factura']);
                $idMov = $this->mov->obtenerIdNumFactura($dataF['num']);
            }
            $url     = "access.php?controller=Factura&action=VistaOrden&factura=".md5($factura);
            $url .= !empty($idMov)?'&mov='.md5($idMov):'';
            echo $url;
        }
    }

    public function registrarRemision(){
        session_start();
        $numero             = $_POST['txtNumeroF'];
        $tipo               = $_POST['sltTipoFactura'];
        $tercero            = $_POST['sltTercero'];
        $fecha              = explode("/" ,$_POST['fecha']);
        $fecha              = "$fecha[2]-$fecha[1]-$fecha[0]";
        $fechav             = explode("/",$_POST['fechaV']);
        $fechavencimiento   = "$fechav[2]-$fechav[1]-$fechav[0]";
        $centrocosto        = $_POST['sltCentroCosto'];
        $descripcion        = $_POST['txtDescripcion'];
        $estado             = "4";
        $responsable        = $tercero;
        $parametrizacionano = $_SESSION['anno'];
        $compania           = $_SESSION['compania'];
        $vendedor           = !empty($_POST['sltVendedor'])?$_POST['sltVendedor']:'NULL';
        $proyecto           = 2147483647;
        $descuento          = !empty($_REQUEST['txtDescuento'])?$_REQUEST['txtDescuento']:'NULL';

        $ccnt = new comprobanteContable();
        $cppl = new comprobantePptal();
        $mov  = new movimiento();

        $result = $this->factura->InsertarData(
            $numero, $tipo, $tercero, $fecha, $fechavencimiento, $centrocosto, $descripcion, $estado,
            $responsable, $vendedor, $parametrizacionano, $descuento
        );

        if($result == true){
            $idF = $this->factura->obtnerUltimaFacturaTN($tipo, $numero);
            $tpC = $this->factura->obtnerTipoComprobanteCnt($tipo);
            $tpP = $this->cnt->obtnerTipoPptal($tpC);
            $tpM = $this->factura->obtenerTipoMovimiento($tipo);
            list($idC, $idP, $idM) = array(0, 0, 0);
            if(!empty($tpC)){
                $ccnt->numero              = $numero;
                $ccnt->fecha               = $fecha;
                $ccnt->descripcion         = $descripcion;
                $ccnt->tipocomprobante     = $tpC;
                $ccnt->parametrizacionanno = $parametrizacionano;
                $ccnt->compania            = $compania;
                $ccnt->tercero             = $tercero;
                $ccnt->estado              = 1;

                $this->cnt->registrar($ccnt);

                $idC = $this->cnt->obtnerUltimoRegistroTipo($tpC, $numero);
            }

            if(!empty($tpP)){
                $cppl->numero              = $numero;
                $cppl->fecha               = $fecha;
                $cppl->fechavencimiento    = "$fechav[2]-$fechav[1]-$fechav[0]";
                $cppl->descripcion         = $descripcion;
                $cppl->parametrizacionanno = $parametrizacionano;
                $cppl->tipocomprobante     = $tpP;
                $cppl->tercero             = $tercero;
                $cppl->estado              = 1;
                $cppl->responsable         = $responsable;

                $this->pptal->registrar($cppl);

                $idP = $this->pptal->obtnerUltimoRegistroTipoN($tpP, $numero);
            }

            if(!empty($tpM)){
                $dep  = $this->mov->obnterDependencia($_SESSION['usuario_tercero']);
                $remo = $this->mov->guardar($tpM, $numero, $fecha, 2, $descripcion, '', $_SESSION['anno'], $compania, $tercero, $_SESSION['usuario_tercero'], $centrocosto, 2147483647, $dep);
                $idM  = $this->mov->obtnerUltimoRegistro($tpM);
            }
            $url   = 'Location:json/registrarFactRem.php?action=registrado&factura='.md5($idF);
            $url  .= !empty($idC)?'&cnt='.md5($idC):'';
            $url  .= !empty($idP)?'&pptal='.md5($idP):'';
            $url  .= !empty($idM)?'&mov='.md5($idM):'';
            $url  .= !empty($_REQUEST['peso'])?'&peso='.$_REQUEST['peso']:'';
            header($url);
        }else{
            header('Location:json/registrarFactRem.php?action=noregistrado');
        }
    }

    public function registrarCot(){
        session_start();
        $numero             = $_POST['txtNumeroF'];
        $tipo               = $_POST['sltTipoFactura'];
        $tercero            = $_POST['sltTercero'];
        $fecha              = explode("/" ,$_POST['fecha']);
        $fecha              = "$fecha[2]-$fecha[1]-$fecha[0]";
        $fechav             = explode("/",$_POST['fechaV']);
        $fechavencimiento   = "$fechav[2]-$fechav[1]-$fechav[0]";
        $centrocosto        = $_POST['sltCentroCosto'];
        $descripcion        = $_POST['txtDescripcion'];
        $estado             = "4";
        $responsable        = $_SESSION['usuario_tercero'];
        $parametrizacionano = $_SESSION['anno'];
        $compania           = $_SESSION['compania'];
        $observaciones      = '';
        $descuento          = !empty($_REQUEST['txtDescuento'])?$_REQUEST['txtDescuento']:'NULL';
        if(!empty($_POST['sltVendedor'])){
            $vendedor = $_POST['sltVendedor'];
        }else{
            $vendedor = "NULL";
        }
        $data = $this->factura->InsertarData(
            $numero, $tipo, $tercero, $fecha, $fechavencimiento, $centrocosto, $descripcion, $estado,
            $responsable, $vendedor, $parametrizacionano, $descuento
        );

        if($data == true){
            $idF = $this->factura->obtnerUltimaFacturaTN($tipo, $numero);
            $tpM = $this->factura->obtenerTipoMovimiento($tipo);
            list($idM) = array(0, 0, 0);

            if(!empty($tpM)){
                $dep  = $this->mov->obnterDependencia($_SESSION['usuario_tercero']);
                $remo = $this->mov->guardar($tpM, $numero, $fecha, 2, $descripcion, '', $_SESSION['anno'], $compania, $tercero, $_SESSION['usuario_tercero'], $centrocosto, 2147483647, $dep);
                $idM  = $this->mov->obtnerUltimoRegistro($tpM);
            }
            $url   = 'registrar_GF_FACTURA_COTIZACION.php?factura='.md5($idF);
            $url  .= !empty($idM)?'&mov='.md5($idM):'';
        }

        require_once './vistas/respuesta/index.php';
    }

    public function registrarPedido(){
        session_start();
        $numero             = $_POST['txtNumeroF'];
        $tipo               = $_POST['sltTipoFactura'];
        $tercero            = $_POST['sltTercero'];
        $fecha              = explode("/" ,$_POST['fecha']);
        $fecha              = "$fecha[2]-$fecha[1]-$fecha[0]";
        $fechav             = explode("/",$_POST['fechaV']);
        $fechavencimiento   = "$fechav[2]-$fechav[1]-$fechav[0]";
        $centrocosto        = $_POST['sltCentroCosto'];
        $descripcion        = $_POST['txtDescripcion'];
        $estado             = "4";
        $responsable        = $_SESSION['usuario_tercero'];
        $parametrizacionano = $_SESSION['anno'];
        $compania           = $_SESSION['compania'];
        $observaciones      = '';
        $descuento          = !empty($_REQUEST['txtDescuento'])?$_REQUEST['txtDescuento']:'0';
        if(!empty($_POST['sltVendedor'])){
            $vendedor = $_POST['sltVendedor'];
        }else{
            $vendedor = "NULL";
        }
        $data = $this->factura->InsertarData(
            $numero, $tipo, $tercero, $fecha, $fechavencimiento, $centrocosto, $descripcion, $estado,
            $responsable, $vendedor, $parametrizacionano, $descuento
        );

        if($data == true){
            $idF = $this->factura->obtnerUltimaFacturaTN($tipo, $numero);
            $tpM = $this->factura->obtenerTipoMovimiento($tipo);
            list($idM) = array(0, 0, 0);

            if(!empty($tpM)){
                $dep  = $this->mov->obnterDependencia($_SESSION['usuario_tercero']);
                $remo = $this->mov->guardar($tpM, $numero, $fecha, 2, $descripcion, '', $_SESSION['anno'], $compania, $tercero, $_SESSION['usuario_tercero'], $centrocosto, 2147483647, $dep);
                $idM  = $this->mov->obtnerUltimoRegistro($tpM);
            }

            if(!empty($_REQUEST['asociado'])){
                $dataX = $this->factura->obtenerDetalles($_REQUEST['asociado']);
                foreach ($dataX as $row){
                    $dtm = 'NULL';
                    if(!empty($idM)){
                        $elemento = $this->cpt->obtnerConceptoPlanI($row[1]);
                        $unidad   = $this->factura->obtenerUnidadElemento($elemento);
                        $factor   = $this->factura->obtenerUnidadFactor($unidad, $row[1]);
                        $xxx      = $row[3] * $factor;
                        $xsaldoV  = $this->dtm->obtenerSaldoPlan($elemento);
                        $xsaldoC  = $this->dtm->obtnerCantidadPlan($elemento);

                        if(empty($xsaldoC)){
                            $xvalor = 0;
                        }
                        if(!empty($xsaldoV) || !empty($xsaldoC)){
                            $xvalor = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                        }

                        if($xsaldoV < 0){
                            $xvalor = 0;
                        }

                        $xdm = $this->dtm->guardar($xxx, $xvalor, 0, $idM, $elemento, $unidad, $row[3]);
                        if($xdm == true){
                            $dtm = $this->dtm->obtenerUltimoRegistro($idM);
                        }
                    }
                    $this->dtf->registrarData($idF, $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], 'NULL', $dtm, $row[0], 'NULL');
                }
            }

            $url   = 'registrar_GF_FACTURA_PEDIDO.php?factura='.md5($idF);
            $url  .= !empty($idM)?'&mov='.md5($idM):'';
        }

        require_once './vistas/respuesta/index.php';
    }

    public function buscarCot(){
        if(!empty($_REQUEST['factura'])){
            $factura  = $_REQUEST['factura'];
            $url  = "registrar_GF_FACTURA_COTIZACION.php";
            $url .= "?factura=".md5($factura);
            $dataMov = $this->factura->buscarMovFactura($factura);
            if(count($dataMov) > 0){
                $url  .= !empty($dataMov[0])?'&mov='.md5($dataMov[0]):'';
            }
            echo $url;
        }
    }

    public function buscarPedido(){
        if(!empty($_REQUEST['factura'])){
            $factura  = $_REQUEST['factura'];
            $url  = "registrar_GF_FACTURA_PEDIDO.php";
            $url .= "?factura=".md5($factura);
            $dataMov = $this->factura->buscarMovFactura($factura);
            if(count($dataMov) > 0){
                $url  .= !empty($dataMov[0])?'&mov='.md5($dataMov[0]):'';
            }
            echo $url;
        }
    }

    public function informesFactura(){
        $tipoI  = $this->factura->obtenerTiposClase($_REQUEST['clase'], 'ASC');
        $tipoF  = $this->factura->obtenerTiposClase($_REQUEST['clase'], 'DESC');
        require_once './vistas/factura/factura.informes.php';
    }

    public function InformeGeneralPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/factura/factura.general.php';
    }

    public function InformeGeneralExcel(){
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
        header("Content-Disposition: attachment; filename=InformeGeneral.xls");
        require_once './vistas/factura/factura.excel.php';
    }

    public function InformeDetalladoPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/factura/factura.detallado.php';
    }

    public function InformeDetalladoExcel(){
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
        header("Content-Disposition: attachment; filename=InformeDetallado.xls");
        require_once './vistas/factura/detallado.factura.php';
    }

    public function InformeConceptoPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/factura/concepto.factura.php';
    }

    public function InformeConceptoExcel(){
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
        header("Content-Disposition: attachment; filename=InformeConcepto.xls");
        require_once './vistas/factura/concepto.excel.php';
    }

    public function InformeTerceroPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/factura/factura.tercero.php';
    }

    public function InformeTerceroExcel(){
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
        header("Content-Disposition: attachment; filename=InformeTercero.xls");
        require_once './vistas/factura/tercero.excel.php';
    }

    public function informesRecaudo(){
        $tipoI  = $this->factura->obtenerTiposClase($_REQUEST['clase'], 'ASC');
        $tipoF  = $this->factura->obtenerTiposClase($_REQUEST['clase'], 'DESC');
        require_once './vistas/recaudo/recaudo.informes.php';
    }

    public function InformeRecaudoGeneralPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/recaudo/recuado.general.php';
    }

    public function InformeRecaudoGeneralExcel(){
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
        header("Content-Disposition: attachment; filename=InformeRecaudoGeneral.xls");
        require_once './vistas/recaudo/general.excel.php';
    }

    public function InformeRecaudoDetalladoPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/recaudo/recaudo.detallado.php';
    }

    public function InformeRecaudoDetalladoExcel(){
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
        header("Content-Disposition: attachment; filename=InformeRecaudoDetallado.xls");
        require_once './vistas/recaudo/detallado.excel.php';
    }

    public function VistaOrden(){
        $tipos   = $this->tpf->listarTodo(6);
        $tps     = $this->tpf->listarTodo(6);
        $ajuste  = $this->alm->obtenerParametroBasico(4);
        list($tipofactura, $numero, $fecha, $fechaVencimiento, $descripcion, $descuento, $nomtipoF, $idFactura)
            = array(0, "", "", "", "", 0, "", 0);
        require_once './vistas/factura/orden.costo.php';
    }

    public function GuardarordenTraslado(){
        @session_start();
        $numero             = $_REQUEST['txtNumero'];
        $tipo               = $_REQUEST['sltTipoFactura'];
        $fecha              = explode("/" ,$_POST['txtFecha']);
        $fecha              = "$fecha[2]-$fecha[1]-$fecha[0]";
        $fechav             = explode("/",$_POST['txtFechaV']);
        $fechavencimiento   = "$fechav[2]-$fechav[1]-$fechav[0]";
        $tercero            = $_REQUEST['sltTercero'];
        $centrocosto        = $_REQUEST['sltCentroCosto'];
        $descripcion        = $_REQUEST['txtDescripcion'];
        $estado             = 4;
        $responsable        = $_SESSION['usuario_tercero'];
        $vendedor           = $_REQUEST['sltVendedor'];
        $descuento          = !empty($_REQUEST['txtDescuento'])?$_REQUEST['txtDescuento']:'0';
        $compania           = $_SESSION['compania'];
        $parametrizacionano = $_SESSION['anno'];
        $data = $this->factura->InsertarData(
            $numero, $tipo, $tercero, $fecha, $fechavencimiento, $centrocosto, $descripcion, $estado,
            $responsable, $vendedor, $parametrizacionano, $descuento
        );

        $url = "";
        if($data == true){
            $tpM = $this->factura->obtenerTipoMovimiento($tipo);
            $idF = $this->factura->obtnerUltimaFacturaTN($tipo, $numero);
            $idM = "";
            if(!empty($tpM)){
                $dep  = $this->mov->obnterDependencia($_SESSION['usuario_tercero']);
                $remo = $this->mov->guardar($tpM, $numero, $fecha, 2, $descripcion, '', $_SESSION['anno'], $compania, $tercero, $_SESSION['usuario_tercero'], $centrocosto, 2147483647, $dep);
                if($remo == true){
                    $idM  = $this->mov->obtnerUltimoRegistro($tpM);
                }
            }
            $url  .= 'access.php?controller=Factura&action=VistaOrden&factura='.md5($idF);
            $url  .= !empty($idM)?'&mov='.md5($idM):'';
            $url  .= !empty($_REQUEST['peso'])?'&peso='.$_REQUEST['peso']:'';
        }else{
            $url = 'access.php?controller=Factura&action=VistaOrden';
        }

        require_once './vistas/respuesta/index.php';
    }

    function CambioEstadoPrecio(){
        $data = $this->factura->CambiarEstadoPrecio($_REQUEST['id'], $_REQUEST['estado']);
        echo json_encode(["res" => $data]);
    }

    public function actualizarDatosPrecio(){
        $data = $this->factura->modificarPrecioEstado($_REQUEST['id_precio'], $_REQUEST['precio'], $_REQUEST['estado']);
        if($data == true){
            $this->factura->modificarTarifa($_REQUEST['id_tarifa'], $_REQUEST['precio']);
        }
        echo json_encode(["res" => $data]);
    }

    public function generarContabilidadBloque(){
        require_once './vistas/factura/factura.bloque.php';
    }

    public function cuentasXCobrar(){
        require_once './vistas/factura/cuentas.cobrar.php';
    }

    public function ListadoCuentaXCobrar(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/factura/cuentasXC.informe.php';
    }

    public function ListadoCuentaXCobrarExcel(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ListadoCuentasXCobrar.xls");
        require_once './vistas/factura/cuentasXC.excel.php';
    }
}