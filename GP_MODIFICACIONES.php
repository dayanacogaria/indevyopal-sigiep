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
        <title>Modificaciones</title>
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Modificaciones</h2>
                    <div class="table-responsive" style="margin-left: 50px; margin-right: 50px;margin-top:0px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>   
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Hora</strong></td>
                                        <td class="cabeza"><strong>Item</strong></td>
                                        <td class="cabeza"><strong>Concepto</strong></td>
                                        <td class="cabeza"><strong>Valor Anterior</strong></td>
                                        <td class="cabeza"><strong>Valor Actual</strong></td>
                                        <td class="cabeza"><strong>Usuario</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Hora</th>
                                        <th class="cabeza">Item</th>
                                        <th class="cabeza">Concepto</th>
                                        <th class="cabeza">Valor Anterior</th>  
                                        <th class="cabeza">Valor Actual</th>  
                                        <th class="cabeza">Usuario</th>  
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