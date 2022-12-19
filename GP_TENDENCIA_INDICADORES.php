<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Tendencia Indicadores</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
             .borde-sombra{
                box-shadow: 0px 2px 5px 1px grey;
            }
        </style>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Consulta Pagos</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -5px; margin-left: -10px">
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;">*</strong><strong class="obligado"></strong>Año:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="codigo" id="codigo" class="form-control select2" title="Seleccione Año" style="height: auto;" required>
                                        <option value="">Año</option>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <label for="codigoI" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Valor Recaudado</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top:10px; float: right">
                                    <button style="margin-left:0px;" type="button" class="btn sombra btn-primary" title="Buscar"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>
                                    <button onclick="" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group form-inline " style="margin-top: -5px;margin-left: -10px">
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="nombre" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Indicador:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="nombre" id="nombre" class=" form-control select2" title="Seleccione Nombre" style="height: auto;" required>
                                        <option value="">Tipo Indicador</option>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <label for="codigoI" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Valor Facturado</label>
                                </div>
                            </div>
                            <br/>
                        </form>
                    </div>
                    <h2 align="center" class="tituloform" style="margin-top:0px">Indicadores de Gestión</h2>
                    <div class="form-group form-inline  col-md-1 col-lg-1"></div>
                    <div class="form-group form-inline  col-md-4 col-lg-4 borde-sombra" style="margin-top:15px;">
                        <div class="card mb-3">
                            <div class="card-body">
                              <canvas id="myAreaChart" width="80%" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-inline  col-md-2 col-lg-2"></div>
                    <div class="form-group form-inline  col-md-4 col-lg-4 borde-sombra" style="margin-top:15px">
                        <div class="card mb-3">
                            <div class="card-body">
                                <canvas id="myBarChart" width="80%" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
        <script src="vendor/chart.js/Chart.min.js"></script>
        <script src="js/sb-admin-charts.min.js"></script>
        
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#codigo").select2();
            $("#codigoI").select2();
            $("#nombre").select2();
            
        </script>
    </body>
</html>