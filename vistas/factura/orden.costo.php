<?php
require './Conexion/conexion.php';
require './head.php';
$url        = "";
$urlD       = "";
$centros    = $this->factura->obtenerListadoCentrocosto($_SESSION['anno'], $_SESSION['compania']);
$terceros   = $this->factura->obtenerListadoTerceros('ASC', $_SESSION['compania']);
$vendedores = $this->factura->obtenerListadoTerceros('ASC', $_SESSION['compania']);
$conceptos  = $this->factura->listadoConceptos($_SESSION['anno']);
$bancos     = $this->factura->listadoBancos($_SESSION['compania']);
if(!empty($_REQUEST['factura'])){
    $factura = $_REQUEST['factura'];
    $data    = $this->factura->obtnerDataFactura($_REQUEST['factura']);
    list( $idFactura, $tipofactura, $numero, $tercero, $centroCosto, $fecha, $fechaVencimiento, $descripcion, $estado, $vendedor, $nomTercero, $nomVendedor, $nomTipoF, $nomCentroCosto)
        = array($data[0] ,$data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[19], $data[14], $data[18], $data[20], $data[21]);
    if(!empty($_REQUEST['cnt'])){ $idCnt = $cnt->obtner($_REQUEST['cnt']); }
    if(!empty($_REQUEST['pptal'])){ $idPptal = $ptl->obtner($_REQUEST['pptal']);}
}
if(!empty($_REQUEST['mov'])){ $salida = 'registrar_GR_SALIDA_ALMACEN.php?movimiento='.$_REQUEST['mov'];}

$url   = "access.php?controller=Factura&action=GuardarordenTraslado";
$url  .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';

$urlD  = "access.php?controller=DetalleFactura&action=GuardarordenTraslado";
$urlD .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
?>
    <title>Traslado</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <style>
        .client-form input[type='text'], input[type='number']{
            width:  100%;
            font-size: 11px !important;
        }

        #form>.form-group{
            margin-bottom: 0 !important;
        }

        .client-form textarea{
            width: 100%;
            height: 34px;
        }

        .margen-spr{
            margin-top: 5px !important;
        }

        form, input{
            font-family: Arial;
            font-size: 11px !important;
        }

        .borde-int{
            box-shadow: inset 0px 2px 5px 1px grey;
            border-radius: 5px;
        }

        input:read-only {
            background-color: #fff !important;
        }

        table.dataTable thead th,table.dataTable thead td{padding:1px 18px; font-size:10px;}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px;}
        .dataTables_wrapper .ui-toolbar{padding:2px;}
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require './menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" style="margin-top:-2px;" class="tituloform">Traslado</h2>
                <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-7px; border:4px solid #020324;border-radius: 10px;">
                    <div class="client-form">
                        <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo $url; ?>">
                            <p align="center" class="parrafoO" style="margin-bottom: -0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="sltTipoFactura" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Tipo Factura:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltTipoFactura" id="sltTipoFactura" class="form-control select"  title="Seleccione el tipo de factura" required="">
                                        <?php
                                        $html = "";
                                        if(empty($tipofactura)){
                                            $html .= "<option value=''>Tipo Factura</option>";
                                            foreach ($tipos as $row){
                                                $html .= "<option value='$row[0]'>$row[1]</option>";
                                            }
                                        }else{
                                            $html .= "<option value='$tipofactura'>$nomTipoF</option>";
                                        }
                                        echo $html;
                                        ?>
                                    </select>
                                </div>
                                <label for="txtNumero" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Nro:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtNumero" id="txtNumero" class="form-control" title="Número de factura" placeholder="Nro de Factura" value="<?php echo $numero; ?>" required="" readonly/>
                                </div>
                                <label for="fecha" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Fecha:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control fecha" value="<?php echo $fecha ?>" type="text" name="txtFecha" id="txtFecha" title="Ingrese la fecha" placeholder="Fecha" onchange="validarFecha();change_date()" readonly required>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Fecha Vto:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control fecha" value="<?php echo $fechaVencimiento ?>" type="text" name="txtFechaV" id="txtFechaV" title="Ingrese la fecha" placeholder="Fecha Vencimiento" onchange="diferents_date()" readonly required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Centro Costo:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltCentroCosto" id="sltCentroCosto" class="form-control select" title="Seleccione centro de costo" required>
                                        <?php
                                        $html = "";
                                        if(!empty($centroCosto)){
                                            $html .= "<option value='$centroCosto'>$nomCentroCosto</option>";
                                        }else{
                                            foreach ($centros as $row){
                                                $html .= "<option value='$row[0]'>$row[1]</option>";
                                            }
                                        }
                                        echo $html;
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-sm-1 col-lg-1"><strong class="obligado">*</strong>Tercero:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select" name="sltTercero" id="sltTercero" title="Seleccione un tercero para consultar" required>
                                        <?php
                                        $html = "";
                                        if(!empty($tercero)){
                                            $html .= "<option value='$tercero'>$nomTercero</option>";
                                            foreach ($terceros as $row){
                                                $html .= "<option value='$row[0]'>$row[1]</option>";
                                            }
                                        }else{
                                            $html .= "<option value=''>Tercero</option>";
                                            foreach ($terceros as $row){
                                                $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                            }
                                        }
                                        echo $html;
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1" for="sltVendedor">Vendedor:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select" name="sltVendedor" id="sltVendedor" title="Seleccione un tercero para consultar" required>
                                        <?php
                                        $html = "";
                                        if(!empty($vendedor)){
                                            $html .= "<option value='$vendedor'>$nomVendedor</option>";
                                            foreach ($vendedores as $row){
                                                $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                            }
                                        }else{
                                            $html .= "<option value=''>Vendedor</option>";
                                            foreach ($vendedores as $row){
                                                $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                            }
                                        }
                                        echo $html;
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1" for="txtDescripcion">Descripción:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <textarea class="form-control" style="margin-top:0px;" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ><?php echo $descripcion ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 col-md-1 col-lg-1 text-rigth control-label">% Descuento:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtDescuento" id="txtDescuento" style="width: 100%;" class="form-control" placeholder="% Descuento" disabled value="<?php echo $descuento; ?>">
                                </div>
                                <label class="cambio control-label col-sm-1 col-md-1 col-lg-1" for="sltBuscar">Buscar Factura:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2" >
                                    <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Comprobante" class="select form-control" >
                                        <?php
                                        $html = "<option value=''>Tipo Factura</option>";
                                        foreach ($tps as $row){
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                        echo $html;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltBuscar" id="sltBuscar" title="Buscar comprobante" class="select form-control" onchange="consultarF()">
                                        <option value="">Buscar Comprobante</option>
                                    </select>
                                </div>
                                <input type="hidden" name="id" id="id" value="<?php echo $idFactura; ?>" />
                                <div class="col-sm-3 col-md-3 col-lg-3 text-right">
                                    <a id="btnNuevo" onclick="javascript:nuevo()" class="btn btn-primary borde-sombra btn-group" title="Ingresar nueva factura"><li class="glyphicon glyphicon-plus"></li></a>
                                    <button type="submit" id="btnGuardar" class="btn btn-primary borde-sombra btn-group" title="Guardar factura"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnImprimir" onclick="informe()" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnModificar" onclick="modificarPago(<?php echo empty($idCnt)?0:$idCnt ?>,<?php echo empty($idPptal)?0:$idPptal ?>, '<?php echo empty($_GET['mov'])?0:$_GET['mov'] ?>')" title="Editar"><li class="glyphicon glyphicon glyphicon-edit"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnEliminar" onclick="eliminarDatos(<?php echo $idFactura ?>,<?php echo empty($idCnt)?0:$idCnt ?>,<?php echo empty($idPptal)?0:$idPptal ?>,'<?php echo empty($_GET['mov'])?0:$_GET['mov'] ?>')" title="Eliminar"><li class="glyphicon glyphicon-remove"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnRebuilt" onclick="reconstruirComprobantes(<?php echo $idFactura ?>,<?php echo empty($idCnt)?0:$idCnt ?>,<?php echo empty($idPptal)?0:$idPptal ?>)" title="Reconstruir comprobantes cnt y pptal"><i class="glyphicon glyphicon-retweet"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8 margen-spr">
                <div class="col-sm-12 col-md-12 col-lg-12 borde-int">
                    <form name="form" id="form-detalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="<?php echo $urlD ?>">
                        <input type="hidden" name="txtIdFactura" id="txtIdFactura" class="hidden" value="<?php echo $idFactura; ?>"/>
                        <div class="form-group text-left">
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="sltConcepto" class="control-label"><strong class="obligado">*</strong>Concepto:</label>
                                <select name="sltConcepto" id="sltConcepto" class="form-control select" required>
                                    <?php
                                    $html = "<option value=''>Concepto</option>";
                                    foreach ($conceptos as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="sltConcepto" class="control-label"><strong class="obligado">*</strong>Unidad:</label>
                                <select name="sltUnidad" id="sltUnidad" class="form-control select" required>
                                    <option value="">Unidad</option>
                                </select>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <label for="txtCantidad" class="control-label"><strong class="obligado">*</strong>Cantidad:</label>
                                <input type="text" name="txtCantidad" id="txtCantidad" class="form-control" value="0" title="Cantidad" autocomplete="off" required>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <label for="txtXDescuento" class="control-label"><strong class="obligado"></strong>Descuento:</label>
                                <input type="text" name="txtXDescuento" id="txtXDescuento" class="form-control" value="0" title="Descuento" placeholder="%" autocomplete="off">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="txtValor" class="control-label"><strong class="obligado">*</strong>Valor Unitario:</label>
                                <input type="text" name="txtValor" id="txtValor" class="form-control" value="0" title="Valor Unitario" placeholder="Valor Unitario" autocomplete="off" readonly required>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="txtValorT" class="control-label"><strong class="obligado">*</strong>Valor Total:</label>
                                <input type="text" name="txtValorT" id="txtValorT" class="form-control" value="0" title="Valor Total" placeholder="Valor Total" autocomplete="off" readonly required>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <button type="submit" id="btnGuardarDetalle" class="btn btn-primary borde-sombra" style="margin-top: 20px;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8">
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                <div class="table-responsive margen-spr" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <td class="oculto">Identificador</td>
                            <td width="7%" class="cabeza"></td>
                            <td class="cabeza"><strong>Concepto</strong></td>
                            <td class="cabeza"><strong>Cantidad</strong></td>
                            <td class="cabeza"><strong>Valor Unitario</strong></td>
                            <td class="cabeza"><strong>Ajuste del peso</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                        </tr>
                        <tr>
                            <th class="oculto">Identificador</th>
                            <th width="7%" class="cabeza"></th>
                            <th class="cabeza">Concepto</th>
                            <th class="cabeza">Cantidad</th>
                            <th class="cabeza">Valor</th>
                            <th class="cabeza">Ajuste del peso</th>
                            <th class="cabeza">Valor Total Ajustado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sumaCantidad   = 0;
                        $sumaValor      = 0;
                        $sumaIva        = 0;
                        $sumaImpo       = 0;
                        $sumaAjuste     = 0;
                        $sumaValortotal = 0;
                        if(!empty($_GET['factura'])){
                            $result = $this->dtf->obtnerListados($idFactura);
                            while($row=mysqli_fetch_row($result)){ ?>
                                <tr>
                                    <td class="oculto"></td>
                                    <td class="campos" onloadstart="javascript:inhabilitar(<?php echo $row[0] ?>)">
                                        <?php
                                        $id_dd = "$row[0],";
                                        $id_dd .= !empty($row[10])?$row[10]:0;
                                        ?>
                                        <a href="#<?php echo md5($row[0]);?>" onclick="javascript:eliminar(<?php echo $id_dd; ?>)" id="btnDel<?php echo $row[0]; ?>" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                        <a href="#<?php echo md5($row[0]);?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarValor(<?php echo $row[0]; ?>);javascript:cambioValor(<?php echo $row[0]; ?>);javascript:calcularValores(<?php echo $row[0]; ?>);javascript:calcularValoresEscrito(<?php echo $row[0]; ?>)">
                                            <li class="glyphicon glyphicon-edit"></li>
                                        </a>
                                    </td>
                                    <td class="campos">
                                        <?php echo '<label class="valorLabel" style="font-weight: normal;" id="concepto'.$row[0].'">'.ucwords(strtolower($row[2])).'</label>'; ?>
                                        <select class="col-sm-12 campoD form-control" name="sltconcepto<?php echo $row[0] ?>" id="sltconcepto<?php echo $row[0] ?>" title="Seleccione concepto" style="display: none;">
                                            <option value="<?php echo $row[1]; ?>"><?php echo $row[2]; ?></option>
                                            <?php
                                            $sqlCn = "SELECT cnp.id_unico,cnp.nombre FROM gf_concepto con
                                                        LEFT JOIN gp_concepto cnp ON cnp.concepto_financiero = con.id_unico
                                                        WHERE cnp.id_unico != $row[1]
                                                        ORDER BY cnp.nombre DESC";
                                            $resc = $mysqli->query($sqlCn);
                                            while($row2 = mysqli_fetch_row($resc)){
                                                echo '<option value="'.$row2[0].'">'.$row2[1].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row[0].'">'.$row[3].'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;" class="col-sm-12 campoD text-left form-control"  type="text" name="txtcantidad'.$row[0].'" id="txtcantidad'.$row[0].'" value="'.$row[3].'" />';
                                        $sumaCantidad += $row[3];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValor'.$row[0].'">'.number_format($row[4] + $row[5]+ $row[6], 2, '.', ',').'</label>';
                                        //echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtValor'.$row[0].'" id="txtValor'.$row[0].'" value="'.$row[4].'" />';
                                        $sumaValor += $row[4];
                                        ?>
                                        <select class="col-sm-12 campoD form-control" name="txtValor<?php echo $row[0] ?>" id="txtValor<?php echo $row[0] ?>" title="Seleccione valor" style="display: none;">
                                        </select>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblAjustepeso'.$row[0].'">'.number_format($row[7], 2, '.', ',').'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;" class="col-sm-12 campoD text-left form-control" type="text" name="txtAjustepeso'.$row[0].'" id="txtAjustepeso'.$row[0].'" value="'.$row[7].'" />';
                                        $sumaAjuste += $row[7];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValorAjuste'.$row[0].'">'.number_format($row[9], 0, '.', ',').'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none; width:100.5px;" class="col-sm-9 campoD text-left form-control"  type="text" name="txtValorAjuste'.$row[0].'" id="txtValorAjuste'.$row[0].'" value="'.$row[9].'" readonly ="true"/>';
                                        $sumaValortotal += ($row[9]);
                                        ?>
                                        <div >
                                            <table id="tab<?php echo $row[0] ?>" style="padding: 0px; background : transparent;" class="col-sm-1">
                                                <tbody>
                                                <tr style="background-color: transparent;">
                                                    <td style="background-color: transparent;">
                                                        <a  href="#<?php echo md5($row[0]);?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $id_dd; ?>)">
                                                            <li class="glyphicon glyphicon-floppy-disk"></li>
                                                        </a>
                                                    </td>
                                                    <td style="background-color: transparent;">
                                                        <a href="#<?php echo md5($row[0]);?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none;" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                            <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-8 col-sm-2 col-md-8 col-md-2 col-lg-8 col-lg-2">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <h2 class="titulo" align="center" style="font-size:17px; margin-top: -70px;">Información<br/>adicional</h2>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12" id="btnCnt">
                    <?php if(!empty($_GET['cnt'])){ ?>
                        <a class="btn btn-primary btnInfo" href="#" onclick="return cargarComprobante(<?php echo $idCnt; ?>)">COMPROBANTE<br/>CONTABLE</a>
                        <?php
                    }else{ ?>
                        <a class="btn btn-primary btnInfo disabled" href="#" readonly>COMPROBANTE<br/>CONTABLE</a>
                        <?php
                    } ?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12" id="btnPto">
                    <?php if(!empty($_GET['pptal'])){?>
                        <a class="btn btn-primary btnInfo" href="#" onclick="return cargarPresupuestal(<?php echo $idPptal; ?>)">COMPROBANTE<br/>PRESUPUESTAL</a>
                        <?php
                    }else{ ?>
                        <a class="btn btn-primary btnInfo disabled" href="#" readonly>COMPROBANTE<br/>PRESUPUESTAL</a>
                        <?php
                    } ?>
                </div>
                <div id="recaudo" style="display:none;" class="col-sm-12 col-md-12 col-lg-12">
                    <a class="btn btn-primary btnInfo" onclick="modalRecaudo()">REGISTRAR<br/>RECAUDO</a>
                </div>
                <?php if(!empty($_GET['factura'])){
                    $rc = "SELECT DISTINCT dp.pago FROM gp_detalle_pago dp
                                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
                                WHERE md5(df.factura)='".$_GET['factura']."'";
                    $rc = $mysqli->query($rc);
                    if(mysqli_num_rows($rc)>0){
                        $n = mysqli_num_rows($rc);
                        $pg = mysqli_fetch_row($rc);?>
                        <tr>
                            <td>
                                <input type="hidden" name="numR" id="numR" value="<?php echo $n?>">
                                <a class="btn btn-primary btnInfo" onclick="abrirRecaudos(<?php echo $pg[0]?>)">VER<br/>RECAUDO</a>
                            </td>
                        </tr>
                        <script>
                            function abrirRecaudos(pg){
                                if($("#numR").val()>1){
                                    $("#mdlRecaudos").modal('show');
                                } else {
                                    cargarR(pg);
                                }
                            }
                        </script>
                        <div class="modal fade" id="mdlRecaudos" role="dialog" align="center" >
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div id="forma-modal" class="modal-header">
                                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                                    </div>
                                    <div class="modal-body" style="margin-top: 8px">
                                        <?php
                                        $rc = "SELECT DISTINCT dp.pago, pg.numero_pago FROM gp_detalle_pago dp
                                                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
                                                LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                                                WHERE md5(df.factura)='".$_GET['factura']."'";
                                        $rc = $mysqli->query($rc);
                                        while ($row1 = mysqli_fetch_row($rc)) {
                                            echo '<button onclick="cargarR('.$row1[0].')" class="btn btn-primary btnInfo">'.$row1[1].'</button><br/>';
                                        }?>

                                    </div>
                                    <div id="forma-modal" class="modal-footer">
                                        <button type="button" id="btnCerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            function cargarR(pg){
                                var form_data = {
                                    action:9,
                                    pago:pg
                                };
                                //Envio ajax
                                $.ajax({
                                    type:'POST',
                                    url:'jsonPptal/gf_facturaJson.php',
                                    data:form_data,
                                    success: function(data,textStatus,jqXHR){
                                        console.log(data);
                                        window.open(data);
                                    },error : function(data,textStatus,jqXHR){
                                        alert('data : '+data+' , textStatus: '+textStatus+', jqXHR : '+jqXHR);
                                    }
                                });
                            }
                        </script>
                    <?php } } ?>
                <div class="col-sm-12 col-md-12 col-lg-12" id="btnSalida">
                    <a href="<?php echo $salida ?>" class="btn btn-primary btnInfo" target="_blank">SALIDA<br/>ALMACÉN</a>
                </div>
            </div>
            <?php require './footer.php'; ?>
        </div>
    </div>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            var i= 1;
            $('#tabla thead th').each( function () {
                if(i != 1){
                    var title = $(this).text();
                    switch (i){
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 8:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 9:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                }else{
                    i = i+1;
                }
            });
            // DataTable
            var table = $('#tabla').DataTable({
                "autoFill": true,
                "scrollX": true,
                "pageLength": 5,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0){
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    } );
                    i = i+1;
                }else{
                    i = i+1;
                }
            } );
        } );

        $("#sltTipoBuscar").change(function(e){
            var tipo = e.target.value;
            var form_data = { estruc:26, tipo: tipo }
            var option = '<option value="">Buscar Comprobante</option>';
            $.ajax({
                type:'POST',
                url:'jsonPptal/consultas.php',
                data:form_data,
                success: function(data){
                    var option = option + data;
                    $("#sltBuscar").html(option);
                }
            });
        })

        function consultarF(){
            //Captura de variables
            var factura = $("#sltBuscar").val();
            //Array de envio
            var form_data = { factura : factura };
            //Envio ajax
            $.ajax({
                type:'POST',
                url:'access.php?controller=Factura&action=buscarTraslado',
                data:form_data,
                success: function(data){
                    window.location = data;
                }
            });
        }

        if($factura){
            $("#btnGuardarDetalle").prop('disabled',false);
        }else{
            $("#btnGuardarDetalle").prop('disabled',true);
        }

        function inhabilitar(id){
            $.post("access.php?controller=DetalleFactura&action=inhabilitar", { cnt: <?php echo !empty($idCnt)?$idCnt:0 ?>, ptal: <?php echo !empty($idPptal)?$idPptal:0 ?> }, function(data){
                if(data > 0){
                    $("#btnDel"+id).prop('disabled', true);
                }else{
                    $("#btnDel"+id).prop('disabled',false);
                }
            });
        }

        function eliminar(id, mov){
            var result = '';
            var form_data = { action : 6, id_unico : id };
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"POST",
                    data:form_data,
                    url: "jsonPptal/gf_facturaJson.php",
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result == 1) {
                            var form_data = { action : 'Eliminar', id_unico : id, mov : mov };
                            $.ajax({
                                type:"POST",
                                data:form_data,
                                url: "access.php?controller=DetalleFactura&action=Eliminar",
                                success: function (data) {
                                    result = JSON.parse(data);
                                    if(result==true)
                                        $("#mdlEliminado").modal('show');
                                    else
                                        $("#mdlNoeliminado").modal('show');
                                }
                            });
                        } else {
                            $("#mdlNoeliminado").modal('show');
                        }
                    }
                });

            });
        }

        function guardarCambios(id, mov){
            var sltConcepto    = 'sltconcepto'+id;
            var txtCantidad    = 'txtcantidad'+id;
            var txtValor       = 'txtValor'+id;
            var txtIva         = 'txtIva'+id;
            var txtImpoconsumo = 'txtImpoconsumo'+id;
            var txtAjustepeso  = 'txtAjustepeso'+id;
            var txtValorAjuste = 'txtValorAjuste'+id
            var form_data = {
                id       : id,
                concepto : $("#"+sltConcepto).val(),
                cantidad : $("#"+txtCantidad).val(),
                valor    : $("#"+txtValor).val(),
                iva      : $("#"+txtIva).val(),
                impoconsumo : $("#"+txtImpoconsumo).val(),
                ajustepeso  : $("#"+txtAjustepeso).val(),
                valorAjuste : $("#"+txtValorAjuste).val(),
                mov         : mov
            };
            var result = '';
            $.ajax({
                type: 'POST',
                url: "access.php?controller=DetalleFactura&action=Modificar",
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result == true){
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    };
                }
            });
        }

        function modificar(id){
            //En el que valida si el campos idPrevio tiene un valor
            //en el que asignamos los nombres de los labels y campos
            //y el asignamos la idPrevio y a su vez solo mostramos los labels
            if( ( $("#idPrevio").val() !== 0 ) || ( $("#idPrevio").val() !== "" ) ){
                var lblConceptoC    = 'concepto'+$("#idPrevio").val();
                var sltConceptoC    = 'sltconcepto'+$("#idPrevio").val();
                var lblCantidadC    = 'lblCantidad'+$("#idPrevio").val();
                var txtCantidadC    = 'txtcantidad'+$("#idPrevio").val();
                var lblValorC       = 'lblValor'+$("#idPrevio").val();
                var txtValorC       = 'txtValor'+$("#idPrevio").val();
                var lblIvaC         = 'lblIva'+$("#idPrevio").val();
                var txtIvaC         = 'txtIva'+$("#idPrevio").val();
                var lblImpoconsumoC = 'lblImpoconsumo'+$("#idPrevio").val();
                var txtImpoconsumoC = 'txtImpoconsumo'+$("#idPrevio").val();
                var lblAjustepesoC  = 'lblAjustepeso'+$("#idPrevio").val();
                var txtAjustepesoC  = 'txtAjustepeso'+$("#idPrevio").val();
                var guardarC        = 'guardar'+$("#idPrevio").val();
                var cancelarC       = 'cancelar'+$("#idPrevio").val();
                var tablaC          = 'tab'+$("#idPrevio").val();
                var lblValorAjusteC = 'lblValorAjuste'+$("#idPrevio").val();
                var txtValorAjusteC = 'txtValorAjuste'+$("#idPrevio").val();

                $("#"+lblConceptoC).css('display','block');
                $("#"+sltConceptoC).css('display','none');
                $("#"+lblCantidadC).css('display','block');
                $("#"+txtCantidadC).css('display','none');
                $("#"+lblValorC).css('display','block');
                $("#"+txtValorC).css('display','none');
                $("#"+lblIvaC).css('display','block');
                $("#"+txtIvaC).css('display','none');
                $("#"+lblImpoconsumoC).css('display','block');
                $("#"+txtImpoconsumoC).css('display','none');
                $("#"+lblAjustepesoC).css('display','block');
                $("#"+txtAjustepesoC).css('display','none');
                $("#"+guardarC).css('display','none');
                $("#"+cancelarC).css('display','none');
                $("#"+tablaC).css('display','none');
                $("#"+lblValorAjusteC).css('display','block');
                $("#"+txtValorAjusteC).css('display','none');
            }
            //Aqui creamos las variables similares a las anteriores en la que asignamos el nombre y el id
            var lblConcepto    = 'concepto'+id;
            var sltConcepto    = 'sltconcepto'+id;
            var lblCantidad    = 'lblCantidad'+id;
            var txtCantidad    = 'txtcantidad'+id;
            var lblValor       = 'lblValor'+id;
            var txtValor       = 'txtValor'+id;
            var lblIva         = 'lblIva'+id;
            var txtIva         = 'txtIva'+id;
            var lblImpoconsumo = 'lblImpoconsumo'+id;
            var txtImpoconsumo = 'txtImpoconsumo'+id;
            var lblAjustepeso  = 'lblAjustepeso'+id;
            var txtAjustepeso  = 'txtAjustepeso'+id;
            var lblValorAjuste = 'lblValorAjuste'+id;
            var txtValorAjuste = 'txtValorAjuste'+id;
            var guardar        = 'guardar'+id;
            var cancelar       = 'cancelar'+id;
            var tabla          = 'tab'+id;
            //ocultamos los labels y mostramos los campos ocultos
            $("#"+sltConcepto).css('display','block');
            $("#"+lblConcepto).css('display','none');
            $("#"+txtCantidad).css('display','block');
            $("#"+lblCantidad).css('display','none');
            $("#"+txtValor).css('display','block');
            $("#"+lblValor).css('display','none');
            $("#"+txtIva).css('display','block');
            $("#"+lblIva).css('display','none');
            $("#"+txtImpoconsumo).css('display','block');
            $("#"+lblImpoconsumo).css('display','none');
            $("#"+lblAjustepeso).css('display','none');
            $("#"+txtAjustepeso).css('display','block');
            $("#"+lblValorAjuste).css('display','none');
            $("#"+txtValorAjuste).css('display','block')
            $("#"+guardar).css('display','block');
            $("#"+cancelar).css('display','block');
            $("#"+tabla).css('display','block');
            //Asignamos el valor de la id al campo id actual
            $("#idActual").val(id);
            //Y preguntamos si el valor del idPrevio es diferente a la id
            //y se la asignamos
            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);
            }
        }

        function cambioValor(id){
            $("#sltconcepto"+id).change(function() {
                var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 1 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data,
                    success: function (data) {
                        if(data !== ""){
                            $("#txtValor"+id).html(data).fadeIn();
                        }
                    }
                });
            });
        }

        function calcularValores(id) {
            var ajuste    = <?php echo empty($ajuste)?0:$ajuste; ?>;
            var Impo      = 0.00;
            var iva       = 0.00;
            var valor     = 0;
            var totalIva  = 0;
            var totalImpo = 0;

            $("#txtValor"+id).change(function(){
                //Validación para el campo de valor no tome valores cero
                if($("#txtValor"+id).val() !== '0'){
                    var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 2 };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            var cantidad = $("#txtcantidad"+id).val();
                            if(cantidad==0 || cantidad==''){ cantidad = 1; }else{ cantidad = $("#txtcantidad"+id).val(); }
                            iva      = data;
                            valor    = $("#txtValor"+id).val() * cantidad;
                            totalIva = (iva*valor)/100;
                            if (isNaN(totalIva)) { totalIva = 0; }
                            $("#txtIva"+id).val(totalIva);
                        }
                    });
                }else{
                    var can = 0;
                    //Validación para campo cantidad
                    if(isNaN($("#txtcantidad"+id).val())){ can = 1; }else{ can = ($("#txtcantidad"+id).val()); }
                    //Vaciamos los campos
                    $("#txtIva"+id).val('0');
                    $("#txtImpoconsumo"+id).val('0');
                    $("#txtAjustepeso"+id).val('0');
                    $("#txtValorAjuste"+id).val('0');
                    //Cambio de campo
                    $("#txtValor"+id).replaceWith('<input type="text" id="txtValor'+id+'" name="txtValor'+id+'" class="form-control campoD" placeholder="Valor" title="Ingrese el valor" onkeypress="return justNumbers(event)"/>');
                    $("#txtValor"+id).focus();
                    //Función de cambio por campo valor
                    $("#txtValor"+id).blur(function(){
                        //Validación de valor de campo valor
                        if($("#txtValor"+id).val() !== 0 ){
                            //Operaciones de valor
                            var valor = $("#txtValor"+id).val() * can;
                            if (isNaN(valor)) { valor = 0; }
                            //Asiganción de valores para el campo de total
                            $("#txtValorAjuste"+id).val(valor);
                        }
                    });
                    //Función de cambio para campo iva
                    $("#txtIva"+id).blur(function(){
                        //Validación de campo iva
                        if($("#txtIva"+id).val() !== 0){
                            //Captura de valores
                            var valor  = $("#txtValor"+id).val()*can;
                            var iva    = $("#txtIva"+id).val();
                            //Operación de iva
                            var totalI = (valor * iva) /100;
                            //Asiganción de valor de iva
                            if (isNaN(totalI)) { totalI = 0; }
                            $("#txtIva"+id).val(totalI);
                            //Asiganción de valores para el campo de total
                            $("#txtValorAjuste"+id).val(valor+totalI);
                        }
                    });
                    //Función de campo para campo impoconsumo
                    $("#txtImpoconsumo"+id).blur(function(){
                        //Validación de campo impoconsumo
                        if($("#txtImpoconsumo"+id).val() !== 0){
                            //Captura de valores
                            var valor  = $("#txtValor"+id).val()*can;
                            var impo   = $("#txtImpoconsumo"+id).val();
                            var iva    = $("#txtIva"+id).val();
                            //Operación de impoconsumo
                            var totalM = (valor*impo) /100;
                            if (isNaN(totalM)) { totalM = 0; }
                            var t      = parseFloat(valor) + parseFloat(iva) + parseFloat(totalM);
                            //ASiganción de valor de impo
                            $("#txtImpoconsumo"+id).val(totalM);
                            //Asiganción de valores para el campo de total
                            $("#txtValorAjuste"+id).val(t);
                        }
                    });
                    //Función para ajuste al peso
                    $("#txtAjustepeso"+id).blur(function(){
                        //Valiación para ajuste al peso
                        if($("#txtAjustepeso"+id).val() !== 0){
                            //Captura de valores
                            var valor  = $("#txtValor"+id).val()*can;
                            var impo   = $("#txtImpoconsumo"+id).val();
                            var iva    = $("#txtIva"+id).val();
                            var ajuste = $("#txtAjustepeso"+id).val();
                            //operaciones
                            var suma     = parseFloat(valor) + parseFloat(impo) + parseFloat(iva);
                            var redondeo = redondeaAlAlza(suma,ajuste);
                            var aj       = redondeoTotal(suma,ajuste);
                            //Asiganción de valores
                            if (isNaN(redondeo)) { redondeo = 0; }
                            $("#txtAjustepeso"+id).val(redondeo);
                            if (isNaN(aj)) { aj = 0; }
                            $("#txtValorAjuste"+id).val(aj);

                        }
                    });
                }
            });

            $("#txtValor"+id).change(function(){
                //Validación para el campo de valor no tome valores cero
                if($("#txtValor"+id).val() !== '0'){
                    var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 3 };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            Impo  = data;
                            valor = $("#txtValor"+id).val();
                            var cantidad = $("#txtcantidad"+id).val();
                            if( cantidad == 0 || cantidad == '' ){
                                cantidad = 1;
                            }else{
                                cantidad = $("#txtcantidad"+id).val();
                            }
                            var oper  = (valor * cantidad);
                            totalImpo = (Impo*oper)/100;
                            var suma    = parseFloat(oper) + parseFloat(totalIva) + parseFloat(totalImpo);
                            var redondo = redondeaAlAlza(suma,ajuste) ;
                            var ajusteT = redondeoTotal(suma,ajuste);
                            if (isNaN(totalImpo)) { totalImpo = 0; }
                            if (isNaN(redondo)) { redondo = 0; }
                            if (isNaN(ajusteT)) { ajusteT = 0; }
                            $("#txtImpoconsumo"+id).val(totalImpo);
                            $("#txtAjustepeso"+id).val(redondo);
                            $("#txtValorAjuste"+id).val(ajusteT);
                        }
                    });
                }

            });
        }

        function calcularValoresEscrito(id) {
            var ajuste    = <?php echo empty($ajuste)?0:$ajuste; ?>;
            var Impo      = 0.00;
            var iva       = 0.00;
            var valor     = 0;
            var totalIva  = 0;
            var totalImpo = 0;
            $("#txtcantidad"+id).keyup(function(){
                var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 2 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data,
                    success: function (data) {
                        iva      = data;
                        valor    = $("#txtValor"+id).val();
                        totalIva = (iva*valor)/100;
                        if (isNaN(totalIva)){ totalIva = 0; }
                        $("#txtIva"+id).val(totalIva);
                    }
                });

                var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 3 };
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValor.php",
                    data:form_data,
                    success: function (data) {
                        Impo      = data;
                        valor     = $("#txtValor"+id).val();
                        totalImpo = (Impo*valor)/100;
                        if (isNaN(totalImpo)){ totalImpo  = 0; }
                        $("#txtImpoconsumo"+id).val(totalImpo);
                        var cantidad = $("#txtcantidad"+id).val();
                        if( cantidad == 0 || cantidad == '' ){
                            cantidad = 1;
                        }else{
                            cantidad = $("#txtcantidad"+id).val();
                        }

                        var oper    = (valor * cantidad);
                        var suma    = oper + totalIva + totalImpo;
                        var redondo = redondeaAlAlza(suma,ajuste);
                        var ajusteT = redondeoTotal(suma,ajuste);
                        $("#txtAjustepeso"+id).val(redondo);
                        $("#txtValorAjuste"+id).val(ajusteT);
                    }
                });
            });
        }

        function cancelar(id){
            //Creamos las variables en la que cargamos los nombres de los campos y label y le concatenamos la id
            var lblConcepto = 'concepto'+id;
            var sltConcepto = 'sltconcepto'+id;
            var lblCantidad = 'lblCantidad'+id;
            var txtCantidad = 'txtcantidad'+id;
            var lblValor    = 'lblValor'+id;
            var txtValor    = 'txtValor'+id;
            var lblIva      = 'lblIva'+id;
            var txtIva         = 'txtIva'+id;
            var lblImpoconsumo = 'lblImpoconsumo'+id;
            var txtImpoconsumo = 'txtImpoconsumo'+id;
            var lblAjustepeso  = 'lblAjustepeso'+id;
            var txtAjustepeso  = 'txtAjustepeso'+id;
            var lblValorAjuste = 'lblValorAjuste'+id;
            var txtValorAjuste = 'txtValorAjuste'+id;
            var guardar        = 'guardar'+id;
            var cancelar       = 'cancelar'+id;
            var tabla          = 'tab'+id;
            //ocultamos los campos y mostramos los labels
            $("#"+lblConcepto).css('display','block');
            $("#"+sltConcepto).css('display','none');
            $("#"+lblCantidad).css('display','block');
            $("#"+txtCantidad).css('display','none');
            $("#"+lblValor).css('display','block');
            $("#"+txtValor).css('display','none');
            $("#"+lblIva).css('display','block');
            $("#"+txtIva).css('display','none');
            $("#"+lblImpoconsumo).css('display','block');
            $("#"+txtImpoconsumo).css('display','none');
            $("#"+lblAjustepeso).css('display','block');
            $("#"+txtAjustepeso).css('display','none');
            $("#"+lblValorAjuste).css('display','block');
            $("#"+txtValorAjuste).css('display','none');
            $("#"+guardar).css('display','none');
            $("#"+cancelar).css('display','none');
            $("#"+tabla).css('display','none');
        }

        function modificarPago(id_cnt, id_pptal, mov){
            var id          = $("#id").val();
            var fecha       = $("#txtFecha").val();
            var tercero     = $("#sltTercero").val();
            var centroCosto = $("#sltCentroCosto").val();
            var fechavence  = $("#txtFechaV").val();
            var descripcion = $("#txtDescripcion").val();

            var form_data = {
                id               : id,
                fecha            : fecha,
                tercero          : tercero,
                centrocosto      : centroCosto,
                fechaVencimiento : fechavence,
                descripcion      : descripcion,
                id_cnt           : id_cnt,
                id_pptal         : id_pptal,
                mov              : mov
            };

            var result='';
            $.ajax({
                type: 'POST',
                url: "access.php?controller=Factura&action=Modificar",
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if (result==true) {
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    }
                }
            });
        }

        function cargarValor(id){
            $("#sltconcepto"+id).append(function(){

                var form_data = { is_ajax:1, data:+id };

                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValorT.php",
                    data:form_data,
                    success: function (data) {
                        $("#txtValor"+id).html(data).fadeIn();
                    }
                });
            });
        }

        function eliminarDatos(factura, cnt,pptal, mov){
            if(factura !== 0){
                //Validamos que la factura cnt y pptal no esten vacias
                $("#modalEliminarFactura").modal('show');
                $("#btnEC").click(function(){
                    if( cnt !== 0 && pptal !== 0 ) {
                        //Variable de envio ajax
                        var form_data = { existente : 50, factura : factura, pptal : pptal, cnt : cnt, mov : mov };
                        var result    = '';
                        //Envio ajax
                        $.ajax({
                            type:'POST',
                            url: 'access.php?controller=DetalleFactura&action=EliminarTodos',
                            data: form_data,
                            success : function(data) {
                                result = JSON.parse(data);
                                if(result == true) {
                                    $("#mdlEliminado").modal('show');
                                    $("#ver1").click(function(){
                                        window.location.reload();
                                    });
                                } else{
                                    $("#mdlNoeliminado").modal('show');
                                }
                            }
                        }).error(function(data,textError) {
                            console.log('Data :'+data+', Error:'+textError);
                        });
                    }
                });
            }
        }

        sumaFecha = function(d, fecha){
            var Fecha  = new Date();
            var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
            var sep    = sFecha.indexOf('/') != -1 ? '/' : '-';
            var aFecha = sFecha.split(sep);
            var fecha  = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
            fecha      = new Date(fecha);
            fecha.setDate(fecha.getDate()+parseInt(d));
            var anno   = fecha.getFullYear();
            var mes    = fecha.getMonth()+1;
            var dia    = fecha.getDate();
            mes        = (mes < 10) ? ("0" + mes) : mes;
            dia        = (dia < 10) ? ("0" + dia) : dia;
            var fechaFinal = dia+sep+mes+sep+anno;
            return (fechaFinal);
        }

        function sum_v(cantidad, valor, iva, x){
            var cantidad = parseFloat(cantidad);
            var valor    = parseFloat(valor);
            var iva      = parseFloat(iva);
            var oper     = (cantidad * valor);
            $("#txtValorAjuste"+x).val(oper+iva);
        }

        $("#sltUnidad").change(function (e) {
            let unidad   = e.target.value;
            let concepto = $("#sltConcepto").val();

            if(!isNaN(concepto)){
                $.get("access.php?controller=Punto&action=ObtenerValorTarifaUnidad", { unidad: unidad, concepto: concepto }, function(data){
                    $("#sltValor").html(data).fadeIn();
                });
            }
        });

        $("#sltTipoFactura").change(function(e){
            let tipo = e.target.value;
            if(!isNaN(tipo)){
                $.get("access.php?controller=Punto&action=BuscarIndicador", { tipo : tipo}, function (data) {
                    if(data == 1){
                        $("#txtDescuento").attr("disabled", false);
                    }else{
                        $("#txtDescuento").attr("disabled", true);
                    }
                });
            }
        })

        $("#sltTipoFactura").change(function(e){
            let tipo = e.target.value;
            var form_data = { tipo : $("#sltTipoFactura").val(), action : 1 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php",
                data: form_data,
                success: function (data) {
                    $("#txtNumero").val(data);
                }
            });
        });

        function cargarComprobante(idCnt){
            //Vector de envio con mi variable
            var form_data = { idC : idCnt };
            $.ajax({
                type: 'POST',
                url: "modalConsultaComprobanteC.php",
                data: form_data,
                success: function (data) {
                    $("#modalComprobanteC").html(data);
                    $(".comprobantec").modal('show');
                }
            });
        }

        function cargarPresupuestal(idPptal){
            //Vector de envio con mi variable
            var form_data={
                idP:idPptal
            };
            $.ajax({
                type: 'POST',
                url: "modalConsultaComprobanteP.php",
                data: form_data,
                success: function (data) {
                    $("#modalComprobanteP").html(data);
                    $(".comprobantep").modal('show');
                }
            });
        }

        function justNumbers(e){
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                return true;
            return /\d/.test(String.fromCharCode(keynum));
        }

        function informe(){
            window.open('informes/inf_com_rem.php?factura=<?php if(!empty($factura)){echo $factura;}else{echo " ";} ?>');
        }

        function validarFecha(){
            //Capturamos la variable del tipo factura
            var tipoF = parseInt($("#sltTipoFactura").val());
            //Validamos que no este vacia
            if(!isNaN(tipoF) || tipoF.length > 0){
                //Preparamos la variable de envio con los valores
                var form_data = { x : 2, fecha : $("#txtFecha").val(), tipo : tipoF,  id_factura:<?php echo empty($idFactura)? 0 : $idFactura; ?> };
                //Variable de conversion
                var result = '';
                //Envio de ajax
                $.ajax({
                    type:'POST',
                    url:'consultasBasicas/consultas_factura.php',
                    data:form_data,
                    success: function(data){
                        //Capturamos el data y lo convertimos a json
                        result = data;
                        //Validamos si el valor es true
                        if(result == true) {
                            $("#mensaje_fecha").html("<p>La fecha es mayor a la anterior factura</p>");
                            $("#mdlfecha").modal('show');   //Muestra modal
                            $("#txtFecha").val('');            //Campo fecha es vacia
                            $("#txtFechaV").val('');           //Campo fecha es vacia
                        }else if(result == 5){
                            $("#mensaje_fecha").html("<p>La fecha es menor a la ultima factura</p>");
                            $("#mdlfecha").modal('show');   //Muestra modal
                            $("#txtFecha").val('');            //Campo fecha es vacia
                            $("#txtFechaV").val('');           //Campo fecha es vacia
                        }
                    }
                });
            }
        }

        function change_date(){
            var fecha = $("#txtFecha").val();
            var fechaV = sumaFecha(30,fecha);
            $("#txtFechaV").val(fechaV);
        }

        function diferents_date(){
            var fecha1 = $("#txtFecha").val();         //Fecha
            var fecha2 = $("#txtFechaV").val();        //Fecha de vencimiento
            //Dividimos las fechas
            var inicial = fecha1.split("/");        //Fecha
            var final   =  fecha2.split("/");       //Fecha de vencimiento
            //creamos variables de fecha y la formateamos para año-mes-dia
            var dateStart = new Date(inicial[2],inicial[1],inicial[0]); //Fecha
            var dateEnd   = new Date(final[2],final[1],final[0]);       //Fecha de vencimiento
            //Validamos que la fecha de vencimiento no sea mayor que la del campo fecha
            if(dateEnd < dateStart){
                $("#mensaje_fecha").html("<p id=\"mensaje_fecha\">La fecha es menor</p>");
                $("#mdlfecha").modal('show');
                var fv = sumaFecha(30,fecha1);
                $("#txtFechaV").val(fv);
            }
        }

        $("#sltTercero").change(function() {
            var form_data  = { tercero : $("#sltTercero").val() };
            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarFechav.php",
                data:form_data,
                success: function (data) {
                    if( data !== 0 ){
                        var fechaV = data;
                        var fecha  = sumaFecha(fechaV,$("#txtFechaV").val());
                        $("#txtFechaV").val(fecha);
                    }
                }
            });
        });

        $("#modalComprobanteC").on('shown.bs.modal',function(){
            try{
                var dataTable = $("#tablaDetalleC").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            }catch(err){}
        });

        $("#modalComprobanteP").on('shown.bs.modal',function(){
            try{
                var dataTable = $("#tablaDetalleP").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            }catch(err){}
        });

        $().ready(function() {
            var validator = $("#form-detalle").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione tipo de predio",
                },
                errorElement:"em",
                errorPlacement: function(error){
                    error.addClass('help-block');
                },
                highlight: function(element){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });

            var validator = $("#form").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione tipo de predio",
                },
                errorElement:"em",
                errorPlacement: function(error){
                    error.addClass('help-block');
                },
                highlight: function(element){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                        $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });

        function nuevo() {
            window.location='access.php?controller=Factura&action=VistaOrden';
        }

        function reconstruirComprobantes(id_factura, id_cnt, id_pptal){
            if(!isNaN(id_factura) && !isNaN(id_cnt) && !isNaN(id_pptal)){
                var form_data = { id_factura : id_factura, id_cnt : id_cnt, id_pptal : id_pptal };

                $.ajax({
                    type:"POST",
                    url:"access.php?controller=DetalleFactura&action=ReconstruirComprobantes",
                    data:form_data,
                    success: function(data){
                        if(data.length > 0){
                            $("#mensaje_c").html("<p id=\"mensaje_c\">Información Reconstruida Correctamente</p>");
                            $("#mdlConstruir").modal("show");
                        }else{
                            $("#mensaje_c").html("<p id=\"mensaje_c\">La información no se reconstruyo correctamente</p>");
                            $("#mdlConstruir").modal("show");
                        }
                    }
                });
            }
        }

        function reload(){
            window.location.reload();
        }

        $("#sltConcepto").change(function(e){
            var concepto = e.target.value;
            if(!isNaN(concepto)){
                $.post("access.php?controller=detallefactura&action=obtenerPlanIdConcepto", {concepto: concepto}, function (data) {
                    if(data != 0){
                        $.post("access.php?controller=salida&action=obtnerCantidadPlan", {sltElemento: data}, function (data) {
                            if(data > 0){
                                $("#txtCantidadE").val(data);
                            }else{
                                $("#mdlCantidad").modal("show");
                                $("#btnGuardarDetalle").attr('disabled', true);

                                $("#btnCant").click(function(){
                                    $("#txtCantidad").val(' ');
                                });

                                $("#btnCanApt").click(function(){
                                    $("#btnGuardarDetalle").attr('disabled', false);
                                });
                            }
                        });
                    }
                });
            }
        });

        $("#txtCantidad").blur(function (e) {
            var xCan = parseFloat($("#txtCantidadE").val());
            var xCon = parseFloat($("#sltConcepto").val());
            var xCtd = parseFloat(e.target.value);
            if(!isNaN(xCon)){
                $.post("access.php?controller=detallefactura&action=obtenerPlanIdConcepto", { concepto: xCon}, function(data){
                    if(data != 0){
                        if(xCtd > xCan){
                            $("#mdlCantidad").modal("show");
                            $("#btnGuardarDetalle").attr('disabled', true);

                            $("#btnCant").click(function(){
                                $("#txtCantidad").val(' ');
                            });

                            $("#btnCanApt").click(function(){
                                $("#btnGuardarDetalle").attr('disabled', false);
                            });
                        }
                    }
                });
            }
        });

        var form_data = {
            action      :3,
            id_factura  : $("#id").val(),
        };
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_facturaJson.php",
            data:form_data,
            success: function(data){
                if(data!=0){
                    $("#recaudo").css("display", "block");
                    $("#tiporecaudo").val(data);
                }
            }
        });

        $("#txtCantidad").blur(function(e){
            let concepto = $("#sltConcepto").val();
            let unidad   = $("#sltUnidad").val();
            let cantidad = e.target.value;

            $.get("access.php?controller=almacen&action=ObtenerValorCoste", { concepto:concepto, unidad:unidad }, function (data) {
                $("#txtValor").val(parseFloat(data));
                $("#txtValor").val(parseFloat(data) * cantidad);
            });
        });

        $("#txtXDescuento").blur(function(e){
            let descuento = e.target.value;
            let valor     = $("#txtValor").val();

            if(descuento > 0){
                let x = parseFloat(valor) - ( (parseFloat(valor) * parseFloat(descuento) ) / 100);
                $("#txtValor").val(x.toFixed(0));
            }
        });

        $("#forma-modal>.btn").click(function () {
            window.location.reload();
        });
    </script>
    <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModifico" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn btn-default" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdltipofactura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body">
                    <p>Seleccione un tipo de factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlfecha" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p id="mensaje_fecha"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn btn-default" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>¿Desea eliminar el registro seleccionado de Factura?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalValFechaV" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>La fecha debe no puede ser menor a la fecha de la factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnTipoF" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEliminarFactura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>¿Desea eliminar la Factura seleccionada?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnEC" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlConstruir" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p id="mensaje_c"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCons" class="btn btn-default" data-dismiss="modal" onclick="reload()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlCantidad" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>La cantidad es mayor a la existente¿Esta seguro que desea realizar la factura?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCanApt" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCant" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="tiporecaudo" name="tiporecaudo">
    <div class="modal fade" id="mdlRecaudo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Banco</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <div class="form-group form-inline" style="margin-left: 100px;">
                        <label for="sltBanco" class="control-label col-sm-2">
                            <strong class="obligado">*</strong>Banco:
                        </label>
                        <select name="sltBanco" id="sltBanco" class="select col-sm-2 form-control input-sm" style="width : 300px; cursor : pointer; height : 30px;" title="Seleccione banco" required>
                            <?php
                            $html = '<option value="">Banco</option>';
                            foreach ($bancos as $row){
                                $html .= "<option value='$row[0]'>$row[1]</option>";
                            }
                            echo $html;
                            ?>
                        </select>
                        <br/>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="registrarRecaudo" class="btn btn-default" data-dismiss="modal" >Registrar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function modalRecaudo(){
            $("#mdlRecaudo").modal("show");
        }

        $("#registrarRecaudo").click(function(){
            if($("#sltBanco").val() !="") {
                var form_data ={action:5, factura:$("#id").val() };
                var resultado = "";
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_facturaJson.php",
                    data:form_data,
                    success: function(data){
                        console.log(data+'Val');
                        resultado = JSON.parse(data);
                        var msj = resultado["msj"];
                        var rta = resultado["rta"];
                        if(rta==0){
                            var form_data={action:4, recaudo:$("#tiporecaudo").val(), banco:$("#sltBanco").val(),id_factura  : $("#id").val()};
                            $.ajax({
                                type:"POST",
                                url:"jsonPptal/gf_facturaJson.php",
                                data:form_data,
                                success: function(data){
                                    if(data ==0){
                                        $("#mensaje").html("Recudo Registrado Correctamente");
                                        $("#myModalError").modal("show");
                                        $("#btnErrorModal").click(function(){
                                            document.location.reload();
                                        })
                                    } else {
                                        $("#mensaje").html("Error Al Registrar Recaudo");
                                        $("#myModalError").modal("show");
                                        $("#btnErrorModal").click(function(){
                                            document.location.reload();
                                        })
                                    }
                                }
                            });
                        } else {
                            $("#mensaje").html(msj);
                            $("#myModalError").modal("show");
                            $("#btnErrorModal").click(function(){
                                $("#myModalError").modal("hide");
                            })
                        }
                    }
                });
            }
        });

        $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
    </script>
    <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <labe id="mensaje" name="mensaje" style="font-weight: 700;"></labe>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorModal" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './modalConsultaComprobanteP.php'; ?>
    <?php require_once './modalConsultaComprobanteC.php'; ?>
</body>
</html>