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
<title>Consolidado Almacén</title>
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
        label#periodoF-error, #Tipo_Informe-error {
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
                <h2 align="center" class="tituloform" style="margin-top:-3px">Informes Consolidado Almacén</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $anno ?>"/>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="periodoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo Final:</label>
                            <select required name="periodoF" id="periodoF" style="height: auto" class="select2_single form-control" title="Periodo Final" >
                                <option value="">Periodo Final</option>
                            </select>
                        </div>
                        <div class="form-group" class="cuentaF" style="margin-top:-5px;">
                            <label for="Tipo_Informe" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Informe:</label>
                            <select required name="Tipo_Informe" id="Tipo_Informe" style="height: auto" class="select2_single form-control" title="Periodo Final" >
                                <option value="">Tipo Informe</option>
                                <option value="1">Consolidado Cuenta Activo Acumulado </option>
                                <option value="2">Consolidado Cuenta Activo Detallado </option>
                                <option value="3">Consolidado Depreciación</option>
                                <option value="4">Consolidado Depreciación Detallado</option>
                            </select>
                        </div>
                        <div class="form-group" id="divcuentaI" style="margin-top:-5px; display: none">
                            <label for="cuentaI" class="col-sm-5 control-label"><strong class="obligado"></strong>Cuenta Inicial:</label>
                            <select  name="cuentaI" id="cuentaI" style="height: auto" class="select2_single form-control" title="Cuenta Inicial" >
                                <option value="">Cuenta Inicial</option>
                                <?php $rowci = $con->Listar("SELECT DISTINCT c.codi_cuenta, c.nombre 
                                FROM gf_producto_especificacion pe 
                                LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
                                LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
                                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                                LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                                LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
                                LEFT JOIN gf_cuenta c ON pe.valor = c.codi_cuenta 
                                LEFT JOIN gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico 
                                    
                                WHERE m.compania = $compania AND tm.clase = 3 AND fi.elementoficha = 10  
                                AND pe.valor !='' AND c.nombre !='' 
                                AND pa.compania = $compania AND pa.anno = '$nanno'
                                GROUP BY pe.valor 
                                ORDER BY pe.valor  ASC");
                                for ($ci = 0; $ci < count($rowci); $ci++) {
                                    echo '<option value="'.$rowci[$ci][0].'">'.$rowci[$ci][0].' - '.$rowci[$ci][1].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="divcuentaF" style="margin-top:-5px;display: none">
                            <label for="cuentaF" class="col-sm-5 control-label"><strong class="obligado"></strong>Cuenta Final:</label>
                            <select  name="cuentaF" id="cuentaF" style="height: auto" class="select2_single form-control" title="Cuenta Final" >
                                <option value="">Cuenta Final</option>
                                <?php $rowci = $con->Listar("SELECT DISTINCT c.codi_cuenta, c.nombre 
                                FROM gf_producto_especificacion pe 
                                LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
                                LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
                                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                                LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                                LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
                                LEFT JOIN gf_cuenta c ON pe.valor = c.codi_cuenta 
                                LEFT JOIN gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico 
                                
                                WHERE m.compania = $compania AND tm.clase = 3 AND fi.elementoficha = 10  
                                AND pe.valor !='' AND c.nombre !='' 
                                AND pa.compania = $compania AND pa.anno = '$nanno'
                                GROUP BY pe.valor 
                                ORDER BY pe.valor  DESC");
                                for ($ci = 0; $ci < count($rowci); $ci++) {
                                    echo '<option value="'.$rowci[$ci][0].'">'.$rowci[$ci][0].' - '.$rowci[$ci][1].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button onclick="reportePdf()"  class="btn sombra btn-primary" title="Generar reporte "><i class="fa fa-file-excel-o" aria-hidden="true"></i> Generar</button>              
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
        $('form').attr('action', 'informes_almacen/INF_CONSOLIDADO_ALMACEN.php');
    }
    </script>
    <script>    
        $(document).ready(function (){

           var form_data={action: 2, annio :$("#sltAnnio").val()};
           var optionMI ="<option value=''>Periodo Final</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  console.log($("#sltAnnio").val());
                  console.log(response);
                  optionMI =optionMI+response;
                  $("#periodoF").html(optionMI).focus();              
              }
           });
        });
    </script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true,
            });
        });
        $("#Tipo_Informe").change(function(){
            if($("#Tipo_Informe").val()==2){
                $("#divcuentaF").css('display', 'block');
                $("#divcuentaI").css('display', 'block');
            } else {
                $("#divcuentaF").css('display', 'none');
                $("#divcuentaI").css('display', 'none');
                $("#divcuentaF").val('');
                $("#divcuentaI").val('');
            }
        })
    </script>
    
</body>
</html>

    