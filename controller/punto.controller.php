<?php
require_once './modelAlmacen/inventario.php';
require_once './modelFactura/factura.php';
require_once './modelFactura/detallefactura.php';
require_once './modelFactura/comprobanteContable.php';
require_once './modelFactura/detallecnt.php';
require_once './modelFactura/comprobantePptal.php';
require_once './modelFactura/detallePptal.php';
require_once './modelFactura/movimiento.php';
require_once './modelFactura/detallemovnto.php';
require_once './modelFactura/pago.php';
require_once './modelFactura/facturaCaja.php';
require_once './clases/tercero.php';
class puntocontroller{
    private $inv;
    private $fat;
    private $dtf;
    private $cnt;
    private $dtc;
    private $pto;
    private $dtp;
    private $mov;
    private $dtm;
    private $pag;
    private $mfc;
    private $ter;

    public function __construct(){
        $this->inv = new inventario();
        $this->fat = new factura();
        $this->dtf = new detalleFactura();
        $this->cnt = new comprobanteContable();
        $this->dtc = new detalleCnt();
        $this->pto = new comprobantePptal();
        $this->dtp = new detallePptal();
        $this->mov = new movimiento();
        $this->dtm = new detallemovimiento();
        $this->pag = new pago();
        $this->mfc = new facturacaja();
        $this->ter = new tercero();
    }

    public function index(){
        $cajas     = $this->mfc->obtenerListadoCaja();
        $tipoIdent = $this->ter->obtenerTipoIdentificacion();
        $departs   = $this->ter->obtenerDepartamentos();
        $terceros  = $this->ter->obtenerTerceros();
        require_once './vistas/punto/index.php';
    }

    public function buscarCodigos(){
        if(!empty($_REQUEST['codigo'])){
            $data     = $this->inv->buscarElementos($_REQUEST['codigo']);
            $cantidad = !empty($_REQUEST['peso'])?$_REQUEST['peso']:'';
            $html = "";
            foreach ($data as $row){
                $xunid = $this->inv->obtenerUnidadId($row[3]);
                $xuni  = $this->fat->obtenerTarifasElementosD($row[0]);
                $valor = $this->inv->obtenerValorProducto($row[0], $row[3]);
                $html .= "\n<tr class='filas'>";
                $html .= "\n\t<td style='font-size: 11px;'>$row[1]</td>";
                $html .= "\n\t<td style='font-size: 11px;'>$row[2]</td>";
                $html .= "\n\t<td style='width: 10%;'>";
                $html .= "\n\t\t<select class='select form-control' name='sltUnidad$row[0]' id='sltUnidad$row[0]' style='width: 100%;' title='Unidad de Medida'>";
                foreach ($xuni as $item) {
                    $html .= "\n\t\t\t<option value='$item[0]'>$item[5] $".number_format($item[1],2)."</option>";
                }
                $html .= "\n\t\t</select>";
                $html .= "\n\t\t<script>$('#sltUnidad$row[0]').select2();</script>";
                $html .= "\n\t</td>";
                $html .= "\n\t<td>";
                $html .= "\n\t\t<input type='text' value='$cantidad' id='txtCantidad$row[0]' name='txtCantidad$row[0]' style='width: 100%; font-size: 11px; padding: 2px !important;' class='form-control' title='Cantidad' />";
                $html .= "\n\t</td>";
                $html .= "\n\t<td>";
                $html .= "\n\t\t<input type='text' value='0' id='txtDescuento$row[0]' name='txtDescuento$row[0]' style='width: 100%; font-size: 11px; padding: 2px !important;' class='form-control' title='Descuento' />";
                $html .= "\n\t</td>";
                $html .= "\n\t<td style='width: 5%;;'>";
                $html .= "\n\t\t<a onclick='javascript:guardarDetalle($row[0])' class='guardar'><span class='glyphicon glyphicon-floppy-disk'></span></a>";
                $html .= "\n\t</td>";
                $html .= "\n</tr>";
            }
            echo $html;
        }
    }

    public function validarNaturalezaCredito($naturaleza, $valor){
        if($naturaleza == 1){
            $xvalor  = $valor * -1;
        }else{
            $xvalor  = $valor;
        }
        return $xvalor;
    }

    public function validarNaturalezaDebito($naturaleza, $valor){
        if($naturaleza == 1){
            $xvalor  = $valor;
        }else{
            $xvalor  = $valor * -1;
        }
        return $xvalor;
    }

    public function registrar(){
        session_start();
        $tipoF= $this->pag->obtenerTipoFactura($_REQUEST['factura']);
        $tipo = $this->pag->obtenerTipoRecaudo($tipoF);
        $num  = $this->pag->validarNumero($tipo, $_SESSION['anno']);
        $data = $this->pag->guardar($num, $tipo, $_SESSION['usuario_tercero'], date('Y-m-d'), $_REQUEST['banco'], 1,
            $_SESSION['anno'], $_SESSION['usuario_tercero']);
        $centrocosto = $this->mov->obtenerCentroCosto($_SESSION['anno'], 'VARIOS');
        $proyecto    = $this->mov->obtenerProyecto('VARIOS');
        if($data == true){
            $pag     = $this->pag->obtenerMaxId($tipo);
            $tipoCnt = $this->pag->obtenerTipoComprobante($tipo);
            list($dpto, $dcnt, $dch, $tipoP, $tipoH, $ncnt, $npto, $nch)
                = array(false, false, false, $this->pag->obtenerTipoComprobantePptal($tipoCnt),
                $this->cnt->obtenerTipoComprobanteCausacion($tipoCnt), 0 ,0 ,0);
            if(!empty($tipoCnt)){
                $ncnt = $this->cnt->validarNumero($tipoCnt, $_SESSION['anno']);
                $npto = $this->pto->validarNumero($tipoP, $_SESSION['anno']);
                $dpto = $this->pto->guardar($npto, date('Y-m-d'), date('Y-m-d'), "Pago $num",
                    $_SESSION['anno'], $tipoP, $_SESSION['usuario_tercero'], 3, $_SESSION['usuario_tercero']);
                $dcnt = $this->cnt->guardar($ncnt, date('Y-m-d'), "Pago $num", $tipoCnt, $_SESSION['anno'],
                    $_SESSION['compania'], $_SESSION['usuario_tercero'], 1);
            }
            $xxx = $this->fat->obtenerDetallesFactura($_REQUEST['factura']);
            if(count($xxx) > 0){
                $valorX     = 0;
                $valorY     = 0;
                $xctaB      = $this->dtc->obtenerCuentaBanco($_REQUEST['banco']);
                $idCnt      = $this->cnt->obtnerUltimoRegistroTipo($tipoCnt, $ncnt);
                $idPpto     = $this->pto->obtnerUltimoRegistroTipoN($tipoP, $npto);
                $xarCtC     = array();
                foreach ($xxx as $row){
                    $valorY += $row[8];
                    $config = $this->pag->obtenerConfiguracionConcepto($row[1], $_SESSION['anno']);
                    $xcont  = $this->pag->obtenerConfiguracionCuentasPorConceptoRubro($config['concepto_rubro']);
                    list($detallepto, $detallecnt) = array('NULL', 'NULL');
                    if($dpto == true){
                        $idpto = $this->pto->obtnerUltimoRegistroTipoN($tipoP, $npto);
                        $xdpto = $this->dtp->guardar('', $row[7], $idpto, $config['concepto_rubro'], $config['rubro_fuente'], $_SESSION['compania'],
                            $proyecto);
                        if($xdpto == true){
                            $detallepto = $this->dtp->obtenerUltimoDetalle($idpto);
                        }
                    }

                    if($dcnt == true){
                        $xnat  = $this->dtc->obtenerNaturaleza($xcont["cuenta_debito"]);
                        $xvalr = $this->validarNaturalezaCredito($xnat, $row[7] * $row[14]);
                        if(in_array($xcont["cuenta_debito"], $xarCtC)){
                            $xvalor = $this->fat->obtenerValorDetalleCnt($idCnt, $xcont["cuenta_debito"]);
                            if(($xvalor > 0 && $xvalr > 0) || ($xvalor < 0 && $xvalr < 0) ){
                                $xdcnt = $this->fat->actualizarDataComprobante($xvalr, $idCnt, $xcont["cuenta_debito"]);
                                if($xdcnt == true){$valorX     += ($row[7] * $row[14]);}
                            }else{
                                $xdcnt = $this->dtc->guardar(date('Y-m-d'), '', $xvalr, $xvalr, $idCnt, $xcont['cuenta_debito'],
                                $xnat, $_SESSION['usuario_tercero'], $xcont['proyecto'], $xcont['centrocosto'], $detallepto, 'NULL');
                                if($xdcnt == true){
                                    $detallecnt = $this->dtc->obtenerUltimoRegistro($idCnt);
                                    $valorX     += ($row[7] * $row[14]);
                                }
                            }
                        }else{
                            array_push($xarCtC, $xcont['cuenta_debito']);
                            $xdcnt = $this->dtc->guardar(date('Y-m-d'), '', $xvalr, $xvalr, $idCnt, $xcont['cuenta_debito'],
                            $xnat, $_SESSION['usuario_tercero'], $xcont['proyecto'], $xcont['centrocosto'], $detallepto, 'NULL');
                            if($xdcnt == true){
                                $detallecnt = $this->dtc->obtenerUltimoRegistro($idCnt);
                                $valorX     += ($row[7] * $row[14]);
                            }
                        }
                    }

                    $this->pag->guardar_detalle($row[0], ($row[7] * $row[14]), ($row[5] * $row[14]), ($row[6] * $row[14]), 0, 0, $pag, $detallecnt);

                    if(!empty($row[5]) && !empty($row[5])){/* Cuenta Iva */
                        $xnat = $this->dtc->obtenerNaturaleza($xcont['cuenta_debito']);
                        $xvli = $this->validarNaturalezaCredito($xnat, $row[5] * $row[14]);
                        if(in_array($xcont["cuenta_debito"], $xarCtC)){
                            $xvalor = $this->fat->obtenerValorDetalleCnt($idCnt, $xcont["cuenta_debito"]);
                            if(($xvalor > 0 && $xvli > 0) || ($xvalor < 0 && $xvli < 0) ){
                                $xdcnt = $this->fat->actualizarDataComprobante($xvli, $idCnt, $xcont["cuenta_debito"]);
                                if($xdcnt == true){$valorX     += ($row[5] * $row[14]);}
                            }else{
                                $xdci = $this->dtc->guardar(date('Y-m-d'), '', $xvli, $xvli, $idCnt, $xcont['cuenta_debito'], $xnat, $_SESSION['usuario_tercero'],
                                $_SESSION['usuario_tercero'], $xcont['proyecto'], $xcont['centrocosto'], 'NULL', 'NULL');
                                if($xdci == true){ $valorX += ($row[5] * $row[14]); }
                            }
                        }else{
                            array_push($xarCtC, $xcont['cuenta_debito']);
                            $xdci = $this->dtc->guardar(date('Y-m-d'), '', $xvli, $xvli, $idCnt, $xcont['cuenta_debito'], $xnat, $_SESSION['usuario_tercero'],
                            $_SESSION['usuario_tercero'], $xcont['proyecto'], $xcont['centrocosto'], 'NULL', 'NULL');
                            if($xdci == true){ $valorX += ($row[5] * $row[14]); }
                        }
                    }

                    if(!empty($row[6]) && !empty($row[6])){/* Cuenta Impoconsumo */
                        $xnat = $this->dtc->obtenerNaturaleza($xcont['cuenta_debito']);
                        $xvlm = $this->validarNaturalezaCredito($xnat, $row[6] * $row[14]);
                        if(in_array($xcont["cuenta_debito"], $xarCtC)){
                            $xvalor = $this->fat->obtenerValorDetalleCnt($idCnt, $xcont["cuenta_debito"]);
                            if(($xvalor > 0 && $xvlm > 0) || ($xvalor < 0 && $xvlm < 0) ){
                                $xdcnt = $this->fat->actualizarDataComprobante($xvlm, $idCnt, $xcont["cuenta_debito"]);
                                if($xdcnt == true){$valorX     += ($row[6] * $row[14]);}
                            }else{
                                $xdcm = $this->dtc->guardar(date('Y-m-d'), '', $xvlm, $xvlm, $idCnt, $xcont['cuenta_debito'], $xnat, $_SESSION['usuario_tercero'], $_SESSION['usuario_tercero'], $xcont['proyecto'], $xcont['centrocosto'], 'NULL', 'NULL');
                                if($xdcm == true){ $valorX += ($row[6] * $row[14]); }
                            }
                        }else{
                            array_push($xarCtC, $xcont['cuenta_debito']);
                            $xdcm = $this->dtc->guardar(date('Y-m-d'), '', $xvlm, $xvlm, $idCnt, $xcont['cuenta_debito'], $xnat, $_SESSION['usuario_tercero'], $_SESSION['usuario_tercero'], $xcont['proyecto'], $xcont['centrocosto'], 'NULL', 'NULL');
                            if($xdcm == true){ $valorX += ($row[6] * $row[14]); }
                        }
                    }
                }
                $xvalorB    = $this->dtc->validarNaturalezaDebito($xctaB['naturaleza'], $valorX);
                $this->dtc->guardar(date('Y-m-d'), '', $xvalorB, $xvalorB, $idCnt, $xctaB['cuenta'],$xctaB['naturaleza'],
                    $_SESSION['usuario_tercero'], $_SESSION['usuario_tercero'], $proyecto, $centrocosto, 'NULL',
                    'NULL');

                $xcnt = $this->cnt->ContarDetalles($idCnt);
                $xpto = $this->pto->ContarDetalles($idPpto);

                if(empty($xcnt) OR $xcnt == 0){
                    $this->cnt->eliminar($idCnt);
                }

                if(empty($xpto) OR $xpto == 0){
                    $this->pto->eliminar($idPpto);
                }

                if(!empty($_REQUEST['caja'])){
                    date_default_timezone_set('America/Bogota');
                    puntocontroller::registrarFacturaCaja($_REQUEST['factura'], $tipo, 1, $valorX, $_SESSION['usuario_tercero'],
                        $_REQUEST['caja'], date('Y-m-d'), date('H:i:s'));
                }
            }
        }
    }

    public function ActualizarDataDetalle(){
        $dtm = $this->fat->obtenerDetalleMov($_REQUEST['detalle']);
        $dta = $this->fat->obtenerInfoConceptoTarifa($_REQUEST['tarifa']);
        $fat = $this->fat->obtenerFacturaDetalle($_REQUEST['detalle']);

        $cantidad = (float) $_REQUEST['cantidad'];
        $factor   = (float) $dta['factor'];
        $xxx      = $cantidad * $factor;

        list($xiva, $ximpo) = array(0, 0);
        if(!empty($dta['iva'])){
            $iva   = (float) $dta['iva'];
            $valor = (float) $dta['valor'] / ( 1 + $iva / 100 );
            $xiva  = ( $valor * $iva ) / 100;
        }

        if(!empty($dta['impo'])){
            $impo  = (float) $dta['impo'];
            $valor = (float) $dta['valor'] / ( 1 + $impo / 100 );
            $ximpo = ( $valor * $impo ) / 100;
        }

        $total = ($valor + $xiva + $ximpo) * $cantidad;

        $res = $this->fat->actualizarDetalleFac($xxx, $xiva, $ximpo, $valor, $total, $_REQUEST['detalle']);
        if($res == true){

            $xsaldoV = $this->dtm->obtenerSaldoPlan($dta['concepto']) + $dtm['valor'];
            $xsaldoC = $this->dtm->obtnerCantidadPlan($dta['concepto']) + $dtm['cantidad'];

            if(empty($xsaldoC)){
                $xvalor  = $valor;
            }

            if(!empty($xsaldoV) || !empty($xsaldoC)){
                $xvalor  = ((( $xsaldoV / $xsaldoC ) * $xxx ) / $xxx );
            }

            $this->dtm->actualizarDataDetalle($_REQUEST['cantidad'], $dta['unidad'], $xxx, $xvalor, $dtm['id']);
        }

        echo puntocontroller::recargarTabla($fat);
    }

    public function registrarDetalles(){
        $elemento  = $_REQUEST['codigo'];
        $concepto  = $this->inv->obtenerIdConcepto($elemento);
        $dta       = $this->fat->obtenerInfoConceptoTarifa($_REQUEST['tarifa']);

        $cantidad  = (float) $_REQUEST['cantidad'];
        $factor    = (float) $dta['factor'];
        $xxx       = $cantidad * $factor;
        $descuento = $_REQUEST['descuento'];

        if($descuento == 0){
            $valor = (float) $dta['valor'];
        }else{
            $valor = (float) $dta['valor'] - (( (float) $dta['valor'] * $descuento ) / 100);
        }

        list($xiva, $ximpo) = array(0, 0);
        if(!empty($dta['iva'])){
            $iva   = (float) $dta['iva'];
            $valor = $valor / ( 1 + $iva / 100 );
            $xiva  = ( $valor * $iva ) / 100;
        }

        if(!empty($dta['impo'])){
            $impo  = (float) $dta['impo'];
            $valor = $valor / ( 1 + $impo / 100 );
            $ximpo = ( $valor * $impo ) / 100;
        }

        $total  = ($valor + $xiva + $ximpo) * $cantidad;
        $unidad = NULL;
        if(!empty($concepto)){
            $xCantE  = $this->dtm->obtenerSaldoEntradaPlan($elemento);
            $xsaldoV = $this->dtm->obtenerSaldoPlan($elemento);
            $xsaldoC = $this->dtm->obtnerCantidadPlan($elemento);
            $xvalor  = 0;
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
            $unidad = $dta['unidad'];
            $xdata  = $this->dtm->guardar($xxx, $xvalor, 0, $_REQUEST['mov'], $elemento, $dta['unidad'], $cantidad, $descuento);
            if($xdata == true){
                $dtm = $this->dtm->obtenerUltimoRegistro($_REQUEST['mov']);
                $this->buscarHijosPadre($elemento, $dta['unidad'], $_REQUEST['mov'], $dtm);
            }
        }
        //var_dump($_REQUEST['factura'], $concepto, $valor, $cantidad, $xiva, $ximpo, 0, $total, 'NULL', $dtm, 'NULL', $descuento, $unidad);
        $data   = $this->dtf->registrarData($_REQUEST['factura'], $concepto, $valor, $cantidad, $xiva, $ximpo, 0, $total, 'NULL', $dtm, 'NULL', $descuento, $unidad, '');
        echo "access.php?controller=Punto&action=Index&fat=".md5($_REQUEST['factura'])."&mov=".md5($_REQUEST['mov']);
    }

    public function recargarTabla($factura){
        $html = "";
        $data = $this->fat->obtenerDetallesFactura($factura);
        foreach ($data as $row){
            $uni   = $this->inv->obtnerUnidadFactorId($row[12], $row[13]);
            $html .= "<tr>";
            $html .= "<td class='text-left'>$row[3]</td>";
            $html .= "<td class='text-left'>$uni[5]</td>";
            $html .= "<td class='text-center'>$row[14]</td>";
            $html .= "<td class='text-right'>".number_format($row[5] * $row[14], 0)."</td>";
            $html .= "<td class='text-right'>".number_format($row[6] * $row[14], 0)."</td>";
            $html .= "<td class='text-right'>".number_format($row[7] * $row[14], 0)."</td>";
            $html .= "<td class='text-right'>".number_format($row[8], 0)."</td>";
            $html .= "<td class='text-center'><a href='javascript:eliminarDetalle($row[0], $row[10], $row[11])' class='eliminar'><span class='glyphicon glyphicon-remove'></span></a></td>";
            $html .= "</tr>";
        }
        echo $html;
    }

    public function obtenerValorTotales(){
        if(!empty($_REQUEST['factura'])){
            list($xsub, $xiva, $ximpo, $xtotal) = array(0, 0, 0, 0);
            $data = $this->fat->obtenerDetallesFactura($_REQUEST['factura']);
            foreach ($data as $row){
                $xsub   += ($row[7] * $row[14]);
                $ximpo  += ($row[6] * $row[14]);
                $xiva   += ($row[5] * $row[14]);
                $xtotal += $row[8];
            }
            $datax['subtotal'] = $xsub;
            $datax['impo']     = $ximpo;
            $datax['iva']      = $xiva;
            $datax['total']    = $xtotal;
            echo json_encode($datax);
        }
    }

    public function registrarFacturaCaja($factura, $tipo, $tipo_mov, $valor, $tercero, $caja, $fecha, $hora){
        try {
            return $this->mfc->registrar($factura, $tipo, $tipo_mov, $valor, $tercero, $caja, $fecha, $hora);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarDetalles(){
        if(!empty($_REQUEST['dtf'])){
            $data = $this->dtf->eliminar($_REQUEST['dtf']);
            if($data == true){
                if(!empty($_REQUEST['dtm'])){
                    $this->dtm->eliminar($_REQUEST['dtm']);
                }
            }
        }
        echo puntocontroller::recargarTabla($_REQUEST['factura']);
    }

    public function InformeConsolidado(){
        session_start();
        $ffI     = explode("/", $_REQUEST['txtFechaI']);
        $fechaI  = "$ffI[2]-$ffI[1]-$ffI[0]";
        $ffS     = explode("/", $_REQUEST['txtFechaF']);
        $fechaS  = "$ffS[2]-$ffS[1]-$ffS[0]";
        $data    = $this->fat->obtenerFacturasFechaa($fechaI, $fechaS);
        $dataC   = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $html  = "";
        $html .= "<!DOCTYPE html>
        <html>
        <head>
            <meta charset=\"utf-8\">
            <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
            <title>Informe Consolidado</title>
            <script type=\"text/javascript\">
                function imprimir() {
                    if (window.print) {
                        window.print();
                    } else {
                        alert(\"La función de impresion no esta soportada por su navegador.\");
                    }
                }
            </script>
        </head>
        <body onload=\"imprimir();\">";
        $html .= "<div style='width: 150px;margin: 0; left: 0; top: 0;padding: 0; font-family: sans-serif, Arial, Helvetica;'>";
        $html .= "<h4 style='font-size:10px; text-align:center;'>".$dataC['razon']." <br/> NIT ".$dataC['nit']." <br/> DIRECCION ".$dataC['dir']." <br/> TELEFONO : ".$dataC['tel']." <br/> ".$dataC['ciu']." ".$dataC['dep']." <br/> INFORME CONSOLIDADO <br/> ENTRE ".$_REQUEST['txtFechaI']." Y ".$_REQUEST['txtFechaF']." </h4>";
        $html .= "<table  style='width:100%; font-size: 9px;'>
            <thead>
                <tr>
                    <th>FACTURA</th>
                    <th>FECHA</th>
                    <th>VALOR</th>
                </tr>
            </thead>
            <tbody>";
        $total = 0;
        foreach ($data as $row) {
            $xxx  = $this->fat->obtenerValorFactura($row[0]);
            if(!empty($xxx)){
                $html .= "<tr>";
                $html .= "<td>$row[1]</td>";
                $html .= "<td>$row[2]</td>";
                $html .= "<td style='text-align:right'>".number_format($xxx, 0)."</td>";
                $html .= "</tr>";
                $total += $xxx;
            }
        }
        $html .= "</tbody>
        <tfoot>
            <tr>
                <td colspan='2' >TOTAL</td>
                <td style='font-weight:700; text-align:right'>".number_format($total, 0)."</td>
            </tr>
            <tr>
                <td colspan='3' style='font-weight:600';>DESARROLLADO POR GRUPO AAA ASESORES</td>
            </tr>
            <tr>
                <td colspan='3' style='font-weight:600'>SIGIEP.CO</td>
            </tr>
        </tfoot>
        </table>";
        $html .= "</div>";
        $html .= "</body>
        </html>";
        echo $html;
    }

    public function cargarCaja(){
        @session_start();
        if(!empty($_REQUEST['caja'])){
            $_SESSION['caja'] = $_REQUEST['caja'];
        }
    }

    public function imprimir(){
        session_start();
        $dataC  = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $dataF  = $this->fat->obtener($_GET['factura']);
        if($dataF['estd'] == 4){
            if(($handle = fopen("\\\\192.168.1.103\\POS-80", "w")) === FALSE){
                die('No se puedo Imprimir, Verifique su conexion con el Terminal');
            }

            fwrite($handle,chr(27). chr(64));//reinicio
            fwrite($handle, chr(27). chr(112). chr(48));//ABRIR EL CAJON
            fclose($handle); // cierra el fichero PRN
            $salida = shell_exec('lpr COM1');
        }
        $html = "";
        $html .= "<!DOCTYPE html>
        <html>
        <head>
            <meta charset=\"UTF-8\">
            <title>Imprimir Factura</title>
            <script type=\"text/javascript\">
                function imprimir() {
                    if (window.print) {
                        window.print();
                    } else {
                        alert(\"La función de impresion no esta soportada por su navegador.\");
                    }
                }
            </script>
        </head>
        <body onload=\"imprimir();\">";
        $html .= "<div style='width: 150px; margin: 0; left: 0; top: 0;padding: 0; font-family: sans-serif, Arial, Helvetica;'>";
        $html .= "<h4 style='font-size: 11px; text-align:center;'>".$dataC['razon']." <br/> NIT ".$dataC['nit']." <br/> DIRECCIÓN ".$dataC['dir']." <br/> TELÉFONO : ".$dataC['tel']." <br/> ".$dataC['ciu']." ".$dataC['dep']."</h4>";
        $html .= "<h4 style='font-size: 11px; text-align:center;'> ".$dataF['tipo']." ".$dataF['num']." <br/> ".$dataF['res']." </h4>";
        $html .= "<table style='width:100%;'>";
        $html .= "<thead>";
        $html .= "<tr style='font-size: 10px; text-align: left;'>";
        $html .= "<th>FECHA:</th>";
        $html .= "<th colspan='3'>".$dataF['fecha']."</th>";
        $html .= "</tr>";
        $html .= "<tr style='font-size: 10px; text-align: left;'>";
        $html .= "<th>CLIENTE:</th>";
        $html .= "<th colspan='3'>".$dataF['cliente']."</th>";
        $html .= "</tr>";
        $html .= "<tr style='font-size: 10px; text-align: left;'>";
        $html .= "<th>CC / NIT:</th>";
        $html .= "<th colspan='3'>".$dataF['doc']."</th>";
        $html .= "</tr>";
        $html .= "<tr style='font-size: 10px; text-align: left;'>";
        $html .= "<th>DIRECCIÓN:</th>";
        $html .= "<th colspan='3'>".$dataF['dir']."</th>";
        $html .= "</tr>";
        $html .= "<tr style='font-size: 10px; text-align: left;'>";
        $html .= "<th>TELÉFONO:</th>";
        $html .= "<th colspan='3'>".$dataF['tel']."</th>";
        $html .= "</tr>";      
        $html .= "<tr style='font-size: 10px; text-align: left;'>";
        $html .= "<th colspan='4'></th>";
        $html .= "</tr>";      
        $html .= "<tr style='font-size: 10px;'>";
        $html .= "<th>PRODUCTO</th>";
        $html .= "<th>CANT.</th>";
        $html .= "<th>V/U</th>";
        $html .= "<th>TOTAL</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        list($iva, $impo, $total, $xxx, $valr) = array(0, 0, 0, 0, 0);
        $data = $this->fat->obtenerDetallesFactura($_GET['factura']);
        foreach($data as $row){
            $iva  += ($row[5] * $row[14]);
            $impo += ($row[6] * $row[14]);
            $valr += ($row[7] * $row[14]);
            $xxx  += $row[8];
            $html .= "<tr style='font-size: 10px;'>";
            $html .= "<td style='text-align: left;'>$row[3]</td>";
            $html .= "<td style='text-align: center;'>".number_format($row[4],0)."</td>";
            $html .= "<td style='text-align: right;'>".number_format($row[7],0)."</td>";
            $html .= "<td style='text-align: right;'>".number_format($row[7]*$row[4],0)."</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody>";
        $html .= "<tfoot style='font-size: 10px; font-weight:700'>";
        $html .= "<tr>";
        $html .= "<td>SUBTOTAL</td>";
        $html .= "<td colspan='3' style='text-align: right;'>".number_format($valr, 0)."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td>IVA</td>";
        $html .= "<td colspan='3' style='text-align: right;'>".number_format($iva, 0)."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td>IMPO</td>";
        $html .= "<td colspan='3' style='text-align: right;'>".number_format($impo, 0)."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td>TOTAL</td>";
        $html .= "<td colspan='3' style='text-align: right;'>".number_format($xxx, 0)."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td>RECIBIDO</td>";
        $html .= "<td colspan='3' style='text-align: right;'>".number_format(!empty($_GET['Recibido'])?$_GET['Recibido']:0, 0)."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td>CAMBIO</td>";
        $html .= "<td colspan='3' style='text-align: right;'>".number_format(!empty($_GET['Cambio'])?$_GET['Cambio']:0, 0)."</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan='4' style='font-weight:600;text-align: center;'>Desarrollado por</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan='4' style='font-weight:600;text-align: center;'>Grupo AAA Asesores S.A.S</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td colspan='4' style='font-weight:600; text-align: center;'>SIGIEP.CO</td>";
        $html .= "</tr>";
        $html .= "</tfoot>";
        $html .= "</table>";
        $html .= "</div>"; 
        $html .= "</body>
        </html>";
        echo $html;
    }

    public function CambiarFecha(){
        if(!empty($_REQUEST['fecha'])){
            $ff    = explode("/", $_REQUEST['fecha']);
            $fecha = "$ff[2]-$ff[1]-$ff[0]";
            $res   = $this->fat->cambiarFechaFactura($_REQUEST['factura'], $fecha);
            if($res == true){
                $this->mov->cambiarFechaMov($_REQUEST['mov'], $fecha);
            }
            return $res;
        }
    }

    public function BuscarFacturas(){
        $ff    = explode("/", $_REQUEST['fecha']);
        $fecha = "$ff[2]-$ff[1]-$ff[0]";
        $html  = "<option value=''>Facturas</option>";
        $data  = $this->fat->buscarFacturasFecha($fecha, $_REQUEST['clase']);
        foreach ($data as $row){
            $html .= "<option value='$row[0]'>$row[1]</option>";
        }
        echo $html;
    }

    public function obtenerMovAl(){
        if(!empty($_REQUEST['fat'])){
            $dtm = $this->fat->obtenerMovAlmacen($_REQUEST['fat']);
            if(empty($dtm)){
                $fat = $this->fat->obtnerFactura(md5($_REQUEST['fat']));
                $dtm = $this->mov->obtenerIdNumFactura($fat[2]);
            }
            echo json_encode(array("mov" => md5($dtm), "fat" => md5($_REQUEST['fat'])));
        }
    }

    public function recargarData(){
        $data = puntocontroller::recargarTabla($_REQUEST['fat']);
        if(!empty($data)){
            echo $data;
        }
    }

    public function CambiarTercero(){
        if(!empty($_REQUEST['fat'])){
            $res = $this->fat->actualizarTercero($_REQUEST['fat'], $_REQUEST['ter']);
            if($res == true){
                $this->mov->actualizarTercero($_REQUEST['mov'], $_REQUEST['ter']);
            }
        }
    }

    public function CambiarEstadoFactura(){
        if(!empty($_REQUEST['fat'])){
            $this->fat->cambiarEstadoFactura($_REQUEST['fat'], 5);
        }
    }

    public function ConvertirRemision(){
        @session_start();
        $clase = $this->fat->obtenerClaseFactura($_REQUEST['fat']);
        if($clase == 2){
            echo 3;
        }else{
            $tipo = $this->fat->obtenerMovimientoFactura(2);
            $tpm  = $this->fat->obtenerTipoMovimiento($tipo);
            $numF = $this->fat->validarNumeroFactura($tipo, $_SESSION['anno']);
            $numM = $this->fat->validarNumero($tpm, $_SESSION['anno']);

            $res = $this->fat->CambiarRemision($_REQUEST['fat'], $numF, $tipo);
            if($res == true){
                $this->mov->cambiarRemision($_REQUEST['mov'], $numM, $tpm);
                echo 1;
            }
        }
    }

    public function ConvertirPos(){
        try {
            @session_start();
            $tipo  = $this->fat->obtenerMovimientoFactura(3);
            $tipoM = $this->fat->obtenerTipoMovimiento($tipo);
            $tipoR = $this->fat->obtenerTipoRecaudo($tipo);
            $dataF = $this->fat->obtnerFactura($_REQUEST['fat']);
            $dataM = $this->mov->obtenerId($_REQUEST['mov']);
            $dataP = $this->fat->obtenerRecaudoFactura($dataF[0]);/* id de pago */
            $dataC = $this->fat->obtenerRelacioncontableCnt($dataP);
            $tipoC = $this->fat->obtenerTiposComprobantes($tipo);

            $numF  = $this->fat->validarNumeroFactura($tipo, $_SESSION['anno']);
            $numM  = $this->fat->validarNumero($tipoM, $_SESSION['anno']);
            $numR  = $this->pag->validarNumero($tipoR, $_SESSION['anno']);

            $res   = $this->fat->CambiarRemision($dataF[0], $numF, $tipo);
            if($res == true){
                $this->mov->cambiarRemision($dataM[0], $numM, $tipoM);

                if(!empty($dataP)){
                    $this->pag->CambiarRemision($dataP, $numR ,$tipoR);
                }

                if(!empty($dataC['cnt'])){
                    $numC = $this->cnt->validarNumero($tipoC['tipo_cnt'], $_SESSION['anno']);
                    $this->cnt->CambiarRemision($dataC['cnt'], $numC, $tipoC['cnt']);
                }

                if(!empty($dataC['pto'])){
                    $numP = $this->pto->validarNumero($tipoC['tipo_pto'], $_SESSION['anno']);
                    $this->pto->CambiarRemision($dataC['pto'], $numP, $tipoC['tipo_pto']);
                }

                $xxx = $this->pag->obtenerTotalPago($dataP);
                $iP  = $this->pag->obtenerData($dataP);
                date_default_timezone_set('America/Bogota');
                $this->mfc->registrar($dataF[0], $iP[0], 1, $xxx, $_SESSION['usuario_tercero'], 1, date("Y-m-d"), date('H:i:s'));
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function BuscarIndicador(){
        echo $this->fat->buscarIndicador($_REQUEST['tipo']);
    }

    public function ObtenerValorTarifaUnidad(){
        $data = $this->fat->obtenerUnidadConceptoTarifa($_REQUEST['concepto'], $_REQUEST['unidad']);
        $html = "";
        $html .= "";
        if(count($data) > 0){
            foreach ($data as $row){
                $html .= "<option value='$row[0]/$row[1]'>$row[0]</option>";
            }
        }
        echo $html;
    }

    public function Punto(){
        $url = "";
        switch ($_REQUEST['proceso']){
            case 'create':
                $url = 'access.php?controller=Punto&action=Index&peso='.$_REQUEST['peso'];
                break;
            case 'last':
                $cajas  = $this->mfc->obtenerListadoCaja();
                $tipo   = $this->fat->obtenerMovimientoFactura(3);
                $tipoM  = $this->fat->obtenerTipoMovimiento($tipo);
                $fat    = $this->fat->obtenerUltimoIdTipo($tipo);
                $mov    = $this->mov->obtnerUltimoRegistro($tipoM);
                $url    = "access.php?controller=Punto&action=Index&fat=".md5($fat)."&mov=".md5($mov)."&peso=".$_REQUEST['peso'];
                break;
        }
        header("Location:$url");
    }

    public function Remision(){
        $url = "";
        switch ($_REQUEST['proceso']){
            case 'create';
                $url = "registrar_GF_FACTURA_REMISION.php?peso=".$_REQUEST['peso'];
                break;
            case 'last':
                $tipo   = $this->fat->obtenerMovimientoFactura(2);
                $tipoM  = $this->fat->obtenerTipoMovimiento($tipo);
                $fat    = $this->fat->obtenerUltimoIdTipo($tipo);
                $mov    = $this->mov->obtnerUltimoRegistro($tipoM);
                $url = "registrar_GF_FACTURA_REMISION.php?factura=".md5($fat)."&mov=".md5($mov)."&peso=".$_REQUEST['peso'];
                break;
        }
        header("Location:$url");
    }

    public function RecostruirSalida(){
        $data = $this->fat->obtenerDetallesFactura($_REQUEST['factura']);
        $mov  = $this->mov->obtenerId($_REQUEST['salida']);
        foreach ($data as $row){
            $unidad   = $row[12];
            $concepto = $row[1];
            $id       = $row[0];
            $id_dtm   = $row[10];
            $elemento = $row[13];
            $factor   = $this->fat->obtenerUnidadFactor($unidad, $concepto);

            $xsaldoV = $this->dtm->obtenerSaldoPlan($elemento) + $row[15];
            $xsaldoC = $this->dtm->obtnerCantidadPlan($elemento) + $row[14];

            if(empty($xsaldoC)){
                $xvalor  = 0;
            }

            if(!empty($xsaldoV) || !empty($xsaldoC)){
                $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
            }

            if($xsaldoV < 0){
                $xvalor = 0;
            }

            $xxx = $row[14] * $factor;

            $dtm = $this->dtm->guardar($xxx, $xvalor, 0, $mov[0], $elemento, $unidad, $row[14]);
            if($dtm == true){
                $id_lm = $this->dtm->obtenerUltimoRegistro($mov[0]);
                $dtf = $this->dtf->actualizarIdDetalleMov($id, $id_lm);
                if($dtf == true){
                    $this->dtm->eliminar($id_dtm);
                }
            }
        }
    }

    public function obtenerCantidadElementos(){
        $html = "<option value=''>Unidad Empaque</option>";
        if(!empty($_REQUEST['elemento'])){
            $data = $this->inv->obtenerUnidadF($_REQUEST['elemento']);
            foreach ($data as $row) {
                $html .= "<option value='$row[0]'>$row[1]</option>";
            }
        }
        echo $html;
    }

    public function imprimirPDF(){
        @session_start();
        ob_start();
        $dataC  = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $dataF  = $this->fat->obtnerFactura($_GET['factura']);
        require      './fpdf/fpdf.php';
        require_once './informes_punto/factura.informe.php';
    }

    public function obtenerCiudad(){
        try {
            $html = "<option value=''>Ciudad</option>";
            $data = $this->ter->obtenerCiudades($_REQUEST['depto']);
            foreach ($data as $row){
                $html .= "<option value='$row[0]'>$row[1]</option>";
            }
            echo $html;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function calcularDigito(){
        echo $this->ter->carcularDigito($_REQUEST['numero']);
    }

    public function registrarTercero(){
        @session_start();
        $razon     = !empty($_REQUEST['txtRazonSocial'])?$_REQUEST['txtRazonSocial']:NULL;
        $primernom = !empty($_REQUEST['txtPrimerNombre'])?$_REQUEST['txtPrimerNombre']:NULL;
        $segndonom = !empty($_REQUEST['txtSegundoNombre'])?$_REQUEST['txtSegundoNombre']:NULL;
        $primerape = !empty($_REQUEST['txtPrimerApellido'])?$_REQUEST['txtPrimerApellido']:NULL;
        $segndoape = !empty($_REQUEST['txtSegundoApellido'])?$_REQUEST['txtSegundoApellido']:NULL;
        $nombreCom = !empty($_REQUEST['txtNombreComercial'])?$_REQUEST['txtNombreComercial']:NULL;
        $tipoIdent = $_REQUEST['sltTipoIdent'];
        $numeroI   = $_REQUEST['txtNumeroI'];
        $digito    = $_REQUEST['txtDigito'];
        $direccion = $_REQUEST['txtDireccion'];
        $ciudad    = $_REQUEST['sltCiudad'];
        $numeroTel = $_REQUEST['txtNumeroC'];
        $represnte = !empty($_REQUEST['sltRepresentante'])?$_REQUEST['sltRepresentante']:'NULL';
        $email     = !empty($_REQUEST['txtEmail'])?$_REQUEST['txtEmail']:'NULL';
        if(empty($razon)){
            $perfil = 3;
        }else{
            $perfil = 4;
        }

        $data = $this->ter->guardar($primernom, $segndonom, $primerape, $segndoape, $razon, $numeroI, $digito, $_SESSION['compania'], $tipoIdent, $represnte, $ciudad, $nombreCom, $email);
        if($data == true){
            $dataT = $this->ter->obtenerDataTerceroIdent($numeroI);
            $this->ter->guardarDireccion($direccion, 6, $ciudad, $dataT[0]);
            $this->ter->guardarTelefono(2, $numeroTel, $dataT[0]);
            $this->ter->guardarPerfil($perfil, $dataT[0]);
        }
        $url  = $_REQUEST['txtUrl'];
        require_once './vistas/respuesta/index.php';
    }

    public function ActualizarVendedor(){
        if(!empty($_REQUEST['fat'])){
            $this->fat->actualizarVendedor($_REQUEST['fat'], $_REQUEST['vendedor']);
        }
    }

    public function informesPunto(){
        require_once './vistas/punto/informes/punto.informes.php';
    }

    public function ResumenVentasPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $tipo           = $this->fat->obtenerMovimientoFactura(3);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/punto/informes/resumen.punto.php';
    }

    public function ResumenVentasExcel(){
        ini_set('max_execution_time', 0);
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $tipo           = $this->fat->obtenerMovimientoFactura(3);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ResumenVenta.xls");
        require_once './vistas/punto/informes/resumen.excel.php';
    }

    public function PlanillaVentasPdf(){
        ini_set('max_execution_time', 0);
        ob_start();
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $tipo           = $this->fat->obtenerMovimientoFactura(3);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        require './fpdf/fpdf.php';
        require_once './vistas/punto/informes/planilla.punto.php';
    }

    public function PlanillaVentasExcel(){
        ini_set('max_execution_time', 0);
        session_start();
        $dataF          = $this->cnt->obtenerDataCompania($_SESSION['compania']);
        $tipo           = $this->fat->obtenerMovimientoFactura(3);
        $razonsocial    = $dataF['razon'];
        $nombreTipoIden = 'NIT';
        $ruta           = $dataF['log'];
        $numeroIdent    = $dataF['nit'];
        $direccion      = $dataF['dir'];
        $telefono       = $dataF['tel'];
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=PlanillaVenta.xls");
        require_once './vistas/punto/informes/planilla.excel.php';
    }

    public function PlanillaVentasCostoPdf(){
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
        require_once './vistas/punto/informes/venta.punto.php';
    }

    public function PlanillaVentasCostoExcel(){
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
        header("Content-Disposition: attachment; filename=PlanillaVentaCosto.xls");
        require_once './vistas/punto/informes/venta.excel.php';
    }

    public function obtenerValorUnidad(){
        echo $this->fat->obtenerUnidadConceptoTarifaPrimero($_REQUEST['concepto'], $_REQUEST['unidad']);
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

                $xuni = $this->fat->obtenerUnidadMinimaPlan($row[0]);
                $this->dtm->guardarA($row[1], $xvalor, 0, $mov, $row[0], $xuni, $row[1], 0, $dtm);
            }
        }
    }

    public function obtenerTerceros(){
        $results = array();
        $data    = $this->fat->obtenerTercero($_REQUEST['term']);
        foreach ($data as $row){
            $results[] = ['id' => $row[0], 'value' => $row[1] ];
        }
        echo json_encode($results);
    }

    public function obtenerListadoFacturasTerceroClase(){
        $data = $this->fat->buscarFacturasTercero($_REQUEST['tercero'], $_REQUEST['clase']);
        $html  = "<option value=''>Facturas</option>";
        foreach ($data as $row){
            $html .= "<option value='$row[0]'>$row[1]</option>";
        }
        echo $html;
    }

    public function ListadoSinCostePDF(){
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
        require_once './vistas/punto/informes/sincoste.pdf.php';
    }

    public function ListadoSinCosteXLS(){
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
        header("Content-Disposition: attachment; filename=ListadoProductosSinCoste.xls");
        require_once './vistas/punto/informes/sincoste.xls.php';
    }

    public function ListadoProductosSinVentasPDF(){
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
        $ffI     = explode("/", $_REQUEST['txtFechaI']);
        $fechaI  = "$ffI[2]-$ffI[1]-$ffI[0]";
        $ffS     = explode("/", $_REQUEST['txtFechaF']);
        $fechaF  = "$ffS[2]-$ffS[1]-$ffS[0]";
        require './fpdf/fpdf.php';
        require_once './vistas/punto/informes/sinventa.pdf.php';
    }

    public function ListadoProductosSinVentasXLS(){
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
        $ffI     = explode("/", $_REQUEST['txtFechaI']);
        $fechaI  = "$ffI[2]-$ffI[1]-$ffI[0]";
        $ffS     = explode("/", $_REQUEST['txtFechaF']);
        $fechaF  = "$ffS[2]-$ffS[1]-$ffS[0]";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ListadoProductosSinVenta.xls");
        require_once './vistas/punto/informes/sinventa.xls.php';
    }
}