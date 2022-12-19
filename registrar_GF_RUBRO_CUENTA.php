<?php 
require_once('Conexion/conexion.php');
session_start();
require_once ('./head_listar.php');
$ctR = "SELECT CRB.id_unico,CT.nombre FROM gf_concepto_rubro CRB
        LEFT JOIN gf_concepto CT ON CT.id_unico = CRB.concepto
        LEFT JOIN gf_rubro_pptal RBP ON RBP.id_unico = CRB.rubro";

$Crb = $mysqli->query($ctR);


?>
        <title>Registrar rubro cuenta</title>
    </head>
    <body>
        <?php
        // put your code here
        ?>
    </body>
</html>
