<?php
require_once('Conexion/conexion.php');
$query = "SELECT g.id_unico, g.nombre, m.nombre, g.video 
    FROM gs_guias g LEFT JOIN gs_modulos m ON g.modulo = m.id_unico 
    WHERE g.video IS NOT NULL"; 
$resultado = $mysqli->query($query);
require_once('head_listar.php');
?>
<title>Listar Videos</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Videos</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Módulo</strong></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Ver</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Módulo</th>
                                    <th>Nombre</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                        
                                    </td>
                                    <td><?php echo $row[2];?></td>
                                    <td><?php echo $row[1];?></td>
                                    <td><a href="<?php echo $row[3]?>" target="_blank"><i class="glyphicon glyphicon-send"></a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    
</body>
</html>


