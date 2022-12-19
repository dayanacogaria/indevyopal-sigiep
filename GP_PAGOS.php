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
        <title>Pagos</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;">*</strong><strong class="obligado"></strong>Código:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="codigo" id="codigo" class="form-control select2" title="Seleccione Código" style="height: auto;" required>
                                        <option value="">Código</option>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="codigoI" class="col-sm-10 control-label"><strong style="color:#03C1FB;">*</strong><strong class="obligado"></strong>Código Interno:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="codigoI" id="codigoI" class="form-control select2" title="Seleccione Código Interno" style="height: auto;" required>
                                        <option>Código Intero</option>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top:10px; float: right">
                                    <button style="margin-left:0px;" type="button" class="btn sombra btn-primary" title="Buscar"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>
                                    <button onclick="" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group form-inline " style="margin-top: -5px;margin-left: -10px">
                                <div class="form-group form-inline  col-md-2 col-lg-2">
                                    <label for="nombre" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="nombre" id="nombre" class=" form-control select2" title="Seleccione Nombre" style="height: auto;" required>
                                        <option value="">Nombre</option>
                                    </select>
                                </div>
                            </div>
                            <br/>
                        </form>
                    </div>
                    <div class="table-responsive" style="margin-left: 50px; margin-right: 50px;margin-top:0px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>   
                                        <td class="cabeza"><strong>Año</strong></td>
                                        <td class="cabeza"><strong>Periodo</strong></td>                                        
                                        <td class="cabeza"><strong>Fecha Pago</strong></td>
                                        <td class="cabeza"><strong>Banco</strong></td>
                                        <td class="cabeza"><strong>Paquete</strong></td>
                                        <td class="cabeza"><strong>Valor Pagado</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Año</th>
                                        <th class="cabeza">Periodo</th>
                                        <th class="cabeza">Fecha Pago</th>  
                                        <th class="cabeza">Banco</th>  
                                        <th class="cabeza">Paquete</th>  
                                        <th class="cabeza">Valor Pagado</th>
                                    </tr>
                                </thead> 
                            </table>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#codigo").select2();
            $("#codigoI").select2();
            $("#nombre").select2();
            
        </script>
    </body>
</html>