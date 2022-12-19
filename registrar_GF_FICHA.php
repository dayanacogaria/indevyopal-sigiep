<?php 
	require_once('Conexion/conexion.php');

	//session_start();

	$claseC = "SELECT Id_Unico, Nombre FROM gf_clase_contable ORDER BY Nombre ASC";
	$clase = $mysqli->query($claseC);

?>
    <?php require_once 'head.php'; ?>
    <title>Registrar Ficha</title>
    </head>
    <body>    
        <div class="container-fluid text-center">
            <div class="row content">   
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px;margin-top: 0px;">Registrar Ficha</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFFicha.php?action=insert">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                    <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="100" title="Ingrese descripción" onkeypress="return txtValida(event,'car')" placeholder="Descripción" required>
                                </div>                    
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
                <div class="col-sm-8 col-sm-1">
                    <table class="tablaC table-condensed" style="margin-top: -22px;">
                        <thead>
                            <tr>
                                <tr>                                    
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>                                    
                                    <div class="btn btnConsultas disabled" style="margin-bottom: 1px;" id="div">
                                        <a href="javascript:void(0)"  id="linkMovE">
                                            FICHA<br/>INVENTARIO
                                        </a>                                        
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>       
    </body>
</html>