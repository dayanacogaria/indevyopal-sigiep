<?php
require './Conexion/conexion.php';
require './head.php';
date_default_timezone_set('America/Bogota');
$param  = $_SESSION['anno'];
$tipo   = $this->fat->obtenerMovimientoFactura(3);
$numero = $this->fat->validarNumeroFactura($tipo, $param);
$nomPer = $this->pag->obtenerTercero($_SESSION['usuario_tercero']);
$cuenta = $this->pag->obtenerCuentasB($_SESSION['anno'], $_SESSION['compania']);
$tercer = $this->pag->obtenerDataTerceroVarios($_SESSION['compania']);
$tipoM  = $this->fat->obtenerTipoMovimiento($tipo);
$fecha  = date('d/m/Y');
list($fat, $mov, $caja, $nomCaja, $tercero, $nomTer, $estado, $nestado, $idVenddr) = array(0, 0, "", "", 0, "", 0, "", 0);
if(!empty($_SESSION['caja'])){
    $caja    = $_SESSION['caja'];
    $nomCaja = $this->mfc->obtenerdataCaja($caja);
    $tercero = 2;
    $nomTer  = $this->pag->obtenerTercero($tercero);
    if(empty($_REQUEST['fat']) && empty($_GET['mov'])){
        $centrocosto = $this->mov->obtenerCentroCosto($param, 'VARIOS');
        $data    = $this->fat->guardarFact($numero, $tipo, $tercer, date('Y-m-d'), 4, $_SESSION['usuario_tercero'],
            $_SESSION['usuario_tercero'], $_SESSION['anno'], $centrocosto);
        if($data == true){
            $fat = $this->fat->obtnerUltimaFacturaTN($tipo, $numero);
        }
        $proyecto    = $this->mov->obtenerProyecto('VARIOS');
        $dependencia = $this->mov->obtenerDependenciaTercero($_SESSION['usuario_tercero']);
        $res         = $this->mov->guardar($tipoM, $this->fat->validarNumero($tipoM, $param), date('Y-m-d'), 2, "$numero", '',
            $param, $_SESSION['compania'], $_SESSION['usuario_tercero'], $_SESSION['usuario_tercero'], $centrocosto, $proyecto,
            $dependencia);
        if($res == true){
            $mov  = $this->mov->obtnerUltimoRegistro($tipoM);
        }
        $terceros = $this->fat->obtenerTercerosDiff($tercero, $_SESSION['compania']);
        $estado   = 4;
        $nestado  = "GENERADA";
        $idVenddr = $_SESSION['usuario_tercero'];
        $venddrs  = $this->ter->obtenerDiffTerPerfil("1, 2", $_SESSION['usuario_tercero']);
    }else{
        $data     = $this->fat->obtnerFactura($_REQUEST['fat']);
        $numero   = $data[2];
        $fecha    = $data[5];
        $fat      = $data[0];
        $tercero  = $data[3];
        $estado   = $data[8];
        $nestado  = $data[11];
        $nomTer   = $this->pag->obtenerTercero($data[9]);
        $dmov     = $this->mov->obtenerId($_REQUEST['mov']);
        $mov      = $dmov[0];
        //$terceros = $this->ter->obtenerDiffTerPerfil("3, 4, 1, 5, 6", $tercero);
        $terceros = $this->fat->obtenerTercerosDiff($tercero, $_SESSION['compania']);
        $nomPer   = $data[18];
        $idVenddr = $data[19];
        $venddrs  = $this->ter->obtenerDiffTerPerfil("1, 2", $idVenddr);
    }
}
?>
    <title>Punto de Venta</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <style>

        .borde-sombra{
            box-shadow: 0px 2px 5px 1px grey;
        }

        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}

        .cabeza{
            font-weight: 800;
        }

        body, html, .campos, #txtNumero, #txtRecibido, #lblNumeroFactura, #lblCambio{
            font-size: 12px;
        }

        #txtNumero, .botones{
            margin-top: 5px;
        }

        .botones{
            margin-bottom: 5px;
        }

        .contenedorT>a{
            font-size: 20px;
        }

        .contenedorT{
            margin-right: 15px;
            margin-left: 15px;
        }

        #tblProductos>thead>tr>th, #tblFactura>thead>tr>th{
            width: 100%;
            font-size: 10px;
        }

        #tblFactura>tbody>tr>td, #tblFactura>thead>tr>th, #tblProductos>thead>tr>td{
            font-size: 10px !important;
        }

        #tblProductos{
            margin-top: 5px;
            height: 22vh;
            overflow-y: auto;
            overflow-x: auto;
            display: block;
        }

        #tblFactura{
            height: 63.5vh;
            overflow-y: auto;
            overflow-x: auto;
            display: block;
        }

        .filas{
            cursor: pointer;
        }

        #lblSubTotal, #lblIva, #lblImpo, #lblTotal, #lblCambio{
            margin-right: 10px;
        }

        .VRecibido, .Btns{
            padding-right: 15px;
            padding-left: 10px;
        }

        #lblTotal, #lblVendendor, #lblNumeroFactura, #lblCaja, #lblEstado, #lblSubTotal, #lblIva, #lblImpo{
            color: rgba(57,143,192,0.94);
        }

        #lblCambio{
            color: rgba(15,181,60,0.94);
            font-weight: 600;
        }

        .borde-inferior{
            border-bottom: #0000FF solid 1px;
        }

        #txtRecibido, #lblCambio{
            width: 100%;
            margin-right: 5px;
            margin-left: 5px;
            height: 29px;
            border: 0;
            box-shadow: none !important;
            text-align: right;
        }

        .margin-superior{
            margin-top: 5px !important;
        }

        .margen-superior{
            margin-top: 10px !important;
        }

        .margen-spr{
            margin-top: 15px !important;
        }

        .margen-inf{
            padding-bottom: 15px !important;
        }

        label{
            margin-bottom: 0 !important;
        }

        input:-moz-read-only { /* For Firefox */
            background-color: transparent !important;
        }

        input:read-only {
            background-color: transparent !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9 col-md-9 col-lg-9">
                <div class="col-sm-6 col-md-6 col-lg-6 margin-superior">
                    <div class="table-responsive">
                        <input type="hidden" name="txtFactura" id="txtFactura" value="<?php echo $fat; ?>">
                        <input type="hidden" name="txtMov" id="txtMov" value="<?php echo $mov; ?>">
                        <input type="hidden" name="txtCaja" id="txtCaja" value="<?php echo $caja ?>">
                        <table id="tblFactura" class="table table-bordered clearfix" width="100%">
                            <thead>
                                <tr>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza" style="width: 10%;">Unidad</th>
                                    <th class="cabeza" title="Cantidad">Cantidad</th>
                                    <th class="cabeza">Iva</th>
                                    <th class="cabeza">Impo</th>
                                    <th class="cabeza">Valor</th>
                                    <th class="cabeza">Total</th>
                                    <th class="cabeza" style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                list($subtotal, $iva, $impo, $total) = array(0, 0, 0, 0);
                                if(!empty($fat)){
                                    $html = "";
                                    $data = $this->fat->obtenerDetallesFactura($fat);
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
                                        $iva      += ($row[5] * $row[14]);
                                        $impo     += ($row[6] * $row[14]);
                                        $subtotal += ($row[7] * $row[14]);
                                        $total    += $row[8];
                                    }
                                    echo $html;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-sm-6 col-md-6 col-lg-6 borde-sombra margin-superior margen-inf">
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                            <input type="text" name="txtNumero" id="txtNumero" class="form-control" placeholder="Codigo de Barras" style="width: 100%;" autocomplete="off" autofocus>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <table id="tblProductos" class="table-bordered clearfix" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="cabeza" style="width: 25%;">Producto</th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza" style="width: 8%">Unidad</th>
                                        <th class="cabeza">Cantidad</th>
                                        <th class="cabeza" style="width: 15%;">Descuento</th>
                                        <th class="cabeza" style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="7" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">7</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="8" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">8</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="9" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">9</a>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="4" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">4</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="5" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">5</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="6" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">6</a>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="1" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">1</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="2" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">2</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="3" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">3</a>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="0" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">0</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="C" class="btn btn-primary borde-sombra col-sm-6 col-md-6 col-lg-7 botones"><span class="fa fa-long-arrow-left"></span></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <table id="tblTotal" class="display" width="100%">
                        <thead>
                        <tr>
                            <th>SUBTOTAL</th>
                            <td class="text-right"><label id="lblSubTotal">$ <?php echo number_format($subtotal,0, ',', '.') ?></label></td>
                            <th>TOTAL</th>
                            <th class="text-right borde-inferior"><label id="lblTotal"><?php echo $total ?></label></th>
                        </tr>
                        <tr>
                            <th>IVA</th>
                            <td class="text-right"><label id="lblIva">$ <?php echo number_format($iva, 0, ',', '.') ?></label></td>
                            <th>RECIBIDO</th>
                            <td class="VRecibido borde-inferior">
                                <input type="text" id="txtRecibido" name="txtRecibido" class="form-control" placeholder="Valor Recibido" value="" title="Ingrese el valor recibido">
                            </td>
                        </tr>
                        <tr>
                            <th>IMPOCONSUMO</th>
                            <td class="text-right"><label id="lblImpo">$ <?php echo number_format($impo, 0, ',', '.') ?></label></td>
                            <th>CAMBIO</th>
                            <td class="text-right borde-inferior"><input type="text" id="lblCambio" class="form-control" placeholder="Cambio" title="Valor a devolver" value="" readonly></td>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra margin-superior margen-inf">
                    <h4 class="text-center">INFORMACIÓN</h4>
                    <div class="form-group">
                        <label for="lblNumero" class="control-label col-sm-7 col-md-7 col-lg-7">N° Factura:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5 text-right">
                            <label id="lblNumeroFactura"><?php echo $numero ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lblCaja" class="control-label col-sm-7 col-md-7 col-lg-7">Caja:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5 text-right">
                            <label id="lblCaja"><?php echo $nomCaja ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lblEstado" class="control-label col-sm-7 col-md-7 col-lg-7">Estado:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5 text-right">
                            <label id="lblEstado"><?php echo $nestado ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lblVendendor" class="control-label col-sm-7 col-md-7 col-lg-7">Vendedor:</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <select name="sltVendedor" id="sltVendedor" class="select form-control" title="Seleccione vendedor">
                                <?php
                                $html = "<option value='$idVenddr'>$nomPer</option>";
                                foreach ($venddrs as $row){
                                    $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lblVendendor" class="control-label col-sm-7 col-md-7 col-lg-7">Tercero:</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <select name="sltTercero" id="sltTercero" class="select form-control">
                                <?php
                                $html = "<option value='$tercero'>$nomTer</option>";
                                foreach ($terceros as $row){
                                    $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtFecha" class="control-label col-sm-7 col-md-7 col-lg-7">Fecha:</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <input type="text" id="txtFecha" name="txtFecha" value="<?php echo  $fecha ?>" class="form-control fecha" style='width: 100%;' readonly >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sltFormaPago" class="control-label col-sm-12 col-md-12 col-lg-12">Forma de Pago:</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <select name="sltFormaPago" id="sltFormaPago" class="select form-control" placeholder="Forma de Pago">
                                <?php
                                $html = "<option value=''></option>";
                                if(count($cuenta) > 0){
                                    foreach ($cuenta  as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra margen-superior form-group margen-inf">
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a class="btn btn-primary borde-sombra" title="Imprimir a pos" id="btnImprimir">
                            <span class="glyphicon glyphicon-print"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a href="javascript:new_()" class="btn btn-primary borde-sombra" title="Nueva Factura" id="btnNuevo">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a href="" class="btn btn-primary borde-sombra" title="Buscar Facturas" data-toggle="modal" data-target="#mdlFacturas">
                            <span class="glyphicon glyphicon-search"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a class="btn btn-primary borde-sombra" data-toggle="modal" data-target="#mdlInformes" title="Informes de Caja">
                            <span class="glyphicon glyphicon-list"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a href="" class="btn btn-primary borde-sombra" title="Cierre de Caja">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a class="btn btn-primary borde-sombra" title="Registrar Tercero" data-toggle="modal" data-target="#mdlTercero">
                            <span class="glyphicon glyphicon-user"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a class="btn btn-primary borde-sombra" title="Imprimir a pdf" id="btnImprimirPdf">
                            <span class="fa fa-file-pdf-o"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require './footer.php'; ?>
    <?php require './vistas/punto/informes.modal.php'; ?>
    <?php require './vistas/punto/caja.modal.php'; ?>
    <?php require './vistas/punto/facturas.modal.php'; ?>
    <?php require './modales.php'; ?>
    <?php require './vistas/punto/tercero.modal.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/md5.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select").select2();
        $.datetimepicker.setLocale('es');
        $(".fecha").datetimepicker({
            timepicker:false,
            format:'d/m/Y',
            formatDate:'d/m/Y'
        });

        $(function () {
            var i = 1;
            $("#tabla thead th").each(function () {
                if(i != 0){
                    var title = $(this).text();
                    switch (i){
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="'+title+'" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="'+title+'" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="'+title+'" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="'+title+'" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="'+title+'" class="campos"/>' );
                            break;
                        case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="'+title+'" class="campos"/>' );
                            break;
                    }
                    i = i + 1;
                }else{
                    i = i + 1;
                }
            });

            var table = $("#tabla").DataTable({
                "autoFill": true,
                "scrollX": "100%",
                "scrollY": "350px",
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
            table.columns().every(function () {
                var that = this;
                if(i != 0){
                    $( 'input', this.header() ).on('keyup change', function () {
                        if( that.search() != this.value ){
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i + 1;
                }else{
                    i = i + 1;
                }
            });

        });
        $(".botones").click(function (e) {
            e.preventDefault();
            let btn   = $(this);
            let value = $(btn).attr('href');
            let data  = $("#txtNumero").val();
            if(value == "C"){
                if(data != ""){
                    x = data.substr(0, data.length - 1);
                }
            }else{
                x = data + value;
            }
            $("#txtNumero").val(x);
            $.get("access.php?controller=Punto&action=buscarCodigos", { codigo: x, peso : <?php echo !empty($_REQUEST['peso'])?$_REQUEST['peso']:'0'; ?> }, function (data) {
                $("#tblProductos>tbody").html(data);
            });
        });

        $("#txtNumero").keyup(function (e) {
            e.preventDefault();
            var x = e.target.value;
            $.get("access.php?controller=Punto&action=buscarCodigos", { codigo: x, peso : <?php echo !empty($_REQUEST['peso'])?$_REQUEST['peso']:'0'; ?> }, function (data) {
                $("#tblProductos>tbody").html(data);
            });
        });

        $("#txtRecibido").keyup(function (e) {
            let x     = $(this).val();
            let total = $("#lblTotal").text();
            let data  = parseFloat(x) - parseFloat(total);
            $("#lblCambio").val(data.toFixed(0));
        });

        function guardarDetalle(cod) {
            var fat       = $("#txtFactura").val();
            var mov       = $("#txtMov").val();
            let cantidad  = $("#txtCantidad"+cod).val();
            let tarifa    = $("#sltUnidad"+cod).val();
            let estado    = <?php echo $estado; ?>;
            let descuento = $("#txtDescuento"+cod).val();
            if(estado == 4){
                $.get("access.php?controller=Punto&action=registrarDetalles",
                { factura : fat, mov : mov, codigo:cod, cantidad : cantidad, tarifa : tarifa, descuento : descuento },
                function (data) {
                    window.location = data;
                });
            }else{
                $("#mdlEstado").modal("show");
            }
        }

        function actualizarDetalle($x){
            let cantidad = $("#txtCantidad"+$x).val();
            let tarifa   = $("#sltUnidad"+$x).val();
            let detalle  = $x;
            let fat   = $("#txtFactura").val();
            $.get("access.php?controller=Punto&action=ActualizarDataDetalle",
                {  detalle : detalle, tarifa : tarifa, cantidad : cantidad },
                function(data){
                    $("#tblFactura>tbody").html(data);
                }
            );
        }

        function eliminarDetalle(dtf, dtm, fat) {
            $.post("access.php?controller=Punto&action=eliminarDetalles", { factura:fat, dtf:dtf, dtm:dtm }, function (data) {
                $("#tblFactura>tbody").html(data);

                $.get("access.php?controller=Punto&action=obtenerValorTotales", {factura: fat}, function (data) {
                    var res = JSON.parse(data);
                    $("#lblSubTotal").text(res.subtotal.toFixed(0));
                    $("#lblIva").text(res.iva.toFixed(0));
                    $("#lblImpo").text(res.impo.toFixed(0));
                    $("#lblTotal").text(res.total.toFixed(0));
                });
            });
        }

        function new_(){
            window.location = 'access.php?controller=Punto';
        }

        $("#btnImprimir").click(function (e) {
            let fat = $("#txtFactura").val();
            let frp = $("#sltFormaPago").val();
            let cja = $("#txtCaja").val();
            let est = <?php echo $estado; ?>;
            let rec = $("#txtRecibido").val();
            let cam = $("#lblCambio").val();
            if(est == 4){
                if(frp != ""){
                    $.get("access.php?controller=Punto&action=Registrar", { factura : fat, banco : frp, caja : cja }, function (data) {});

                    $.get("access.php?controller=Punto&action=CambiarEstadoFactura", { fat : fat }, function (data) {});

                    window.open("access.php?controller=Punto&action=Imprimir&factura="+fat+"&Recibido="+rec+"&Cambio="+cam);
                }else{
                    $("#mdlFormaPago").modal("show");
                    //alert("Seleccione forma de pago");
                }
            }else{
                window.open("access.php?controller=Punto&action=Imprimir&factura="+fat+"&Recibido="+rec+"&Cambio="+cam);
            }
        });

        $("#btnImprimirPdf").click(function (e) {
            let fat = $("#txtFactura").val();
            let frp = $("#sltFormaPago").val();
            let cja = $("#txtCaja").val();
            let est = <?php echo $estado; ?>;
            let rec = $("#txtRecibido").val();
            let cam = $("#lblCambio").val();
            if(est == 4){
                if(frp != ""){
                    $.get("access.php?controller=Punto&action=Registrar", { factura : fat, banco : frp, caja : cja }, function (data) {});

                    $.get("access.php?controller=Punto&action=CambiarEstadoFactura", { fat : fat }, function (data) {});

                    window.open("access.php?controller=Punto&action=imprimirPDF&factura="+md5(fat)+"&Recibido="+rec+"&Cambio="+cam);
                }else{
                    $("#mdlFormaPago").modal("show");
                    //alert("Seleccione forma de pago");
                }
            }else{
                window.open("access.php?controller=Punto&action=imprimirPDF&factura="+md5(fat)+"&Recibido="+rec+"&Cambio="+cam);
            }
        });

        $("#sltInforme").change(function (e) {
            let xxx = e.target.value;
            let url = "";
            switch (xxx){
                case '1':
                    url = 'access.php?controller=Punto&action=InformeConsolidado';
                    break;
                case '2':
                    url = 'access.php?controller=Punto&action=InformeDetallado';
                    break;
                case '3':
                    url = 'access.php?controller=Punto&action=InformeCaja';
                    break;
            }
            $("#frmInformes").attr('action', url);
        });

        $("#sltCaja").change(function (e) {
           $.get("access.php?controller=Punto&action=cargarCaja", { caja: e.target.value }, function (data) {
               window.location.reload();
           });
        });

        $("#btnGenerarI").click(function(){
            $("#frmInformes").submit();
        });

        <?php
        if(empty($_SESSION['caja'])){
            echo "$(\"#mdlCaja\").modal(\"show\");";
        }
        ?>

        $("#txtFecha").change(function(e){
            var fecha = e.target.value;
            var fat   = $("#txtFactura").val();
            var mov   = $("#txtMov").val();
            let estdo = <?php echo $estado; ?>;
            if(estdo == 4){
                $.get("access.php?controller=Punto&action=CambiarFecha", { factura: fat, mov:mov, fecha:fecha }, function(data){});
            }else{
                $("#mdlBloqueo").modal("show");
            }
        });

        $("#txtFechaB").blur(function (e) {
            var fecha = e.target.value;
            $.get("access.php?controller=Punto&action=BuscarFacturas", { fecha: fecha, clase: 3 }, function (data) {
                $(".modal-body #sltFacturas").html(data);
                $("#sltFacturas").trigger("change");
            });
        });

        $("#sltFacturas").change(function(e){
            var fac = e.target.value;
            $.get("access.php?controller=Punto&action=obtenerMovAl", { fat: fac }, function (data) {
                let res = JSON.parse(data);
                window.location = "access.php?controller=Punto&action=Index&fat="+res.fat+"&mov="+res.mov;
            });
        });

        $("#sltTercero").change(function (e) {
            let ter = e.target.value;
            let fat = $("#txtFactura").val();
            let mov = $("#txtMov").val();

            $.get("access.php?controller=Punto&action=CambiarTercero", { fat : fat, mov : mov, ter : ter }, function (data) {});
        });

        let estado = <?php echo $estado; ?>;
        if(estado == 5){
            $(".eliminar, .guardar, .bloquear").css('display', 'none');
        }

        $("#txtRazonSocial").keyup(function (e) {
            let razon = e.target.value;
            if(razon.length > 1){
                $("#txtPrimerNombre, #txtSegundoNombre, #txtPrimerApellido, #txtSegundoApellido").removeAttr("required").css("display", "none");
                $("label[for='txtPrimerNombre'], label[for='txtSegundoNombre'], label[for='txtPrimerApellido'], label[for='txtSegundoApellido']").css("display", "none");
                $("label[for='txtPrimerNombre'], label[for='txtSegundoNombre'], label[for='txtPrimerApellido'], label[for='txtSegundoApellido']").parent().css("display", "none");
            }else{
                $("#txtPrimerNombre, #txtSegundoNombre, #txtPrimerApellido, #txtSegundoApellido").attr("required", true);
                $("#txtPrimerNombre, #txtSegundoNombre, #txtPrimerApellido, #txtSegundoApellido").css("display", "block");
                $("label[for='txtPrimerNombre'], label[for='txtSegundoNombre'], label[for='txtPrimerApellido'], label[for='txtSegundoApellido']").css("display", "block");
                $("label[for='txtPrimerNombre'], label[for='txtSegundoNombre'], label[for='txtPrimerApellido'], label[for='txtSegundoApellido']").parent().css("display", "block");
            }
        });

        $("#sltDepto").change(function (e) {
            var depto = e.target.value;
            if(!isNaN(depto)){
                $.get("access.php?controller=Punto&action=obtenerCiudad", { depto: depto }, function (data) {
                    $("#sltCiudad").html(data);
                    $("#sltCiudad").trigger("change");
                });
            }
        });

        $("#txtNumeroI").blur(function (e) {
            let num = e.target.value;
            $.get("access.php?controller=Punto&action=calcularDigito", { numero : num }, function (data) {
                $("#txtDigito").val(data);
            });
        });

        $("#btnModalGuardarT").click(function (e) {
            $("#formTercero").submit();
        });

        $("#sltVendedor").change(function (e) {
            let vendendor = e.target.value;
            let fat       = $("#txtFactura").val();
            if(!isNaN(vendendor)){
                $.get("access.php?controller=Punto&action=ActualizarVendedor", { vendedor : vendendor, fat : fat }, function (data) {})
            }
        });
    </script>
</body>
</html>
