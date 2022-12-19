<?php
require_once './head.php';
require_once './Conexion/conexion.php';
require_once './funciones/funciones_consulta.php';
$compania = $_SESSION['compania'];
?>
<link rel="stylesheet" href="css/select/select2.min.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<title>Modificar Usuario</title>
<style type="text/css">
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        max-height:30px
    }
</style>
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -22px;">
                <h2 class="tituloform" align="center">Modificar Usuario</h2>
                <div class="contenedorForma client-form" style="margin-top: -5px">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarUsuarioJson.php">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <?php
                        $idU = $_GET['id'];
                        $id = 0;
                        $usuario = "";
                        $contrasena = "";
                        $observacio = "";
                        $rol = 0;
                        $tercero = 0;
                        $estado = 0;
                        $sql = "select  usr.id_unico,
                                            usr.usuario,
                                            usr.contrasen,                                            
                                            usr.observaciones,
                                            usr.rol,
                                            usr.tercero,
                                            usr.estado
                                    from gs_usuario usr
                                    where md5(usr.id_unico)='$idU'";
                        $result = $mysqli->query($sql);
                        $fila = mysqli_num_rows($result);
                        $row = mysqli_fetch_row($result);
                        if ($fila > 0) {
                            $id = $row[0];
                            $usuario = $row[1];
                            $contrasena = $row[2];
                            $observacio = $row[3];
                            $rol = $row[4];
                            $tercero = $row[5];
                            $estado = $row[6];
                        }?>
                        <input type="hidden" class="hidden" name="txtId" id="txtId" value="<?php echo $id; ?>"/>
                        <div class="form-group" style="margin-top:-10px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Usuario:
                            </label>
                            <input type="text" name="txtUsuario" id="txtUsuario" placeholder="Usuario" maxlength="80" class="form-control input-sm" onkeypress="return txtValida(event, 'sin_espcio')" title="Ingrese el nombre del usuario" style="font-size:10px" value="<?php echo $usuario; ?>" required/>
                        </div>
                        <div class="form-group" style="margin-top:-15px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Contraseña:
                            </label>
                            <input type="password" name="txtPass" placeholder="Contraseña" id="txtPass" maxlength="80" class="form-control input-sm col-sm-1" onkeypress="return txtValida(event, 'todas')" title="Ingrese contraseña" required size="20" value="<?php echo $contrasena; ?>"/>
                        </div>
                        <div class="form-group" style="margin-top:-15px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Rol:
                            </label>
                            <select name="sltRol" id="sltRol" class="form-control input-sm" title="Seleccione el rol de usuario" required>
                                <?php
                                $sql1 = "select id_unico,nombre from gs_rol where id_unico=$rol";
                                cargar_combos($sql1);
                                if($_SESSION['num_usuario']=='900849655'){        
                                    $sql2 = "SELECT r.Id_Unico, r.Nombre rol 
                                    FROM gs_rol r  WHERE compania = $compania AND id_unico!=$rol"; 
                                } else {
                                    $sql2 = "SELECT r.Id_Unico, r.Nombre rol 
                                    FROM gs_rol r  WHERE compania = $compania  
                                    AND nombre != 'Administrador Grupo AAA Asesores SAS' AND id_unico!=$rol"; 
                                } 
                                cargar_combos($sql2);
                                ?>
                            </select>
                        </div><br/>
                        <div class="form-group" style="margin-top:-17px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Tercero:
                            </label>
                            <select name="sltTercero" id="sltTercero" class="form-control input-sm" title="Seleccione tercero" required style="height:30px">
                                <?php
                                $sql3 = "SELECT  IF(CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) 
                                            IS NULL OR CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) = '',
                                            (t.razonsocial),
                                            CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos)) AS NOMBRE, 
                                            t.id_unico, 
                                       IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                            t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                         FROM gf_tercero t
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
                                            LEFT JOIN gf_perfil_tercero prt ON t.id_unico = prt.tercero 
                                            WHERE t.compania = $compania  AND t.id_unico = $tercero 
                                            ";
                                $result3 = $mysqli->query($sql3);
                                $row3 = mysqli_fetch_row($result3);
                                echo '<option value="' . $row3[1] . '">' . ucwords(mb_strtolower($row3[0] . PHP_EOL . $row3[2])) . '</option>';
                                $sql4 = "SELECT  IF(CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) 
                                            IS NULL OR CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) = '',
                                            (t.razonsocial),
                                            CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos)) AS NOMBRE, 
                                            t.id_unico, 
                                       IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                            t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                         FROM gf_tercero t
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
                                            LEFT JOIN gf_perfil_tercero prt ON t.id_unico = prt.tercero 
                                            WHERE t.compania = $compania  AND t.id_unico != $tercero 
                                            ";
                                $rs1 = $mysqli->query($sql4);
                                while ($row1 = mysqli_fetch_row($rs1)) {
                                    echo '<option value="' . $row1[1] . '">' . ucwords(mb_strtolower($row1[0] . PHP_EOL . $row1[2])) . '</option>';
                                }
                                ?>
                            </select>
                        </div><br/>
                        <div class="form-group" style="margin-top:-17px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Estado:
                            </label>
                            <select name="sltEstado" id="sltEstado" class="form-control input-sm" title="Seleccion un estado para el usuario" required style="height:30px">
                                <?php
                                $sql5 = "select id_unico,nombre from gs_estado_usuario where id_unico=$estado";
                                cargar_combos($sql5);
                                $sql6 = "select id_unico,nombre from gs_estado_usuario where id_unico!=$estado";
                                cargar_combos($sql6);
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top:-15px">
                            <label class="control-label col-sm-5">
                                Observaciones:
                            </label>
                            <textarea class="area form-control" name="txtObservaciones" rows="4" cols="20" placeholder="Observaciones"><?php echo $observacio; ?></textarea>
                        </div>
                        <div class="form-group" style="margin-top:-5px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -12px; margin-left: 0px;">Guardar</button>
                        </div> 
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select/select2.full.js"></script>
    <script type="text/javascript">
        $("#sltRol").select2({
            allowClear: true
        });
        $("#sltTercero").select2({
            allowClear: true
        });
        $("#sltEstado").select2({
            allowClear: true
        });
    </script>
    <script type="text/javascript">
        function showPassword() {
            if ($("#txtPass").val() != 0) {
                if ($('#txtPass').is(':text')) {
                    $("#txtPass").attr('type', 'password');
                } else {
                    $("#txtPass").attr('type', 'text');
                }
            }
        }
    </script>
    <link rel="stylesheet" href="css/bootstrap-theme.css"/>
    <script src="js/bootstrap.min.js"></script>
    <div>
        <?php require_once './footer.php'; ?>
    </div>        
</body>    
</html>
