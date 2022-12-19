<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#15/07/2019 |Erica G. | ARCHIVO CREADO
#######################################################################################################
require_once ('Conexion/conexion.php');
require'Conexion/ConexionPDO.php';
require_once('jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$nanno    = anno($anno);
$con      = new ConexionPDO();

?>
<title>Generar Códigos De Productos</title>
</head>
<body> 
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <script>
        $().ready(function () {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function (error, element) {

                    $(element)
                            .closest("form")
                            .find("label[for='" + element.attr("id") + "']")
                            .append(error);
                },
            });

            $(".cancel").click(function () {
                validator.resetForm();
            });
        });
    </script>
    <style>
        body{
            font-size: 12px;
        }       
        label#productoI-error, #productoF-error {
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }
    </style>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Generar Códigos De Productos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" class="cuentaF" style="margin-top:-5px;">
                            <label for="productoI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Producto Inicial</label>
                            <select required name="productoI" id="productoI" style="height: auto" class="select2_single form-control" title="Producto Inicial" >
                                <?php $rowI = $con->Listar("SELECT DISTINCT pe.valor, p.descripcion
                                FROM gf_producto_especificacion pe 
                                LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
                                LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
                                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                                LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
                                WHERE m.compania = $compania AND fi.elementoficha = 6  
                                AND pe.valor !='' 
                                GROUP BY pe.valor 
                                ORDER BY CAST(pe.valor AS UNSIGNED)  ASC");
                                for ($i = 0; $i < count($rowI); $i++) {
                                    echo '<option value="'.$rowI[$i][0].'">'.$rowI[$i][0].' - '.$rowI[$i][1].'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group" class="cuentaF" style="margin-top:-5px;">
                            <label for="productoF" class="col-sm-5 control-label"><strong class="obligado">*</strong>Producto Final</label>
                            <select required name="productoF" id="productoF" style="height: auto" class="select2_single form-control" title="Producto Final" >
                                <?php $rowI = $con->Listar("SELECT DISTINCT pe.valor, p.descripcion
                                FROM gf_producto_especificacion pe 
                                LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
                                LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
                                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                                LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
                                WHERE m.compania = $compania AND fi.elementoficha = 6  
                                AND pe.valor !='' 
                                GROUP BY pe.valor 
                                ORDER BY CAST(pe.valor AS UNSIGNED)  DESC");
                                for ($i = 0; $i < count($rowI); $i++) {
                                    echo '<option value="'.$rowI[$i][0].'">'.$rowI[$i][0].' - '.$rowI[$i][1].'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button onclick="reportePdf()"  class="btn sombra btn-primary" title="Generar reporte "><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generar</button>              
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
    function reportePdf(){
        $('form').attr('action', 'informes_almacen/INF_CODIGO_PRODUCTOS.php');
    }
    </script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true,
            });
        });
    </script>
    
</body>
</html>

