<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Modificaciones
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Modificado por :  Alexander Numpaque
// Fecha          :  25/04/2017
// Descripción    :  Se agrego validación para campos vacios, y se verifico tildes
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
#Llamado a la clase de conexión
require_once './Conexion/conexion.php';
require_once './funciones/funciones_consulta.php';
?>
<!-- Script para diseño de tabla con la libreria Datatable -->
<script type="text/javascript">
    $(document).ready(function() {
        var i= 1;
        $('#tablaDetalleC thead th').each( function () {
            if(i != 1){
                var title = $(this).text();
                switch (i){
                case 2:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 3:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 4:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 5:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 6:
                  $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 7:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 8:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 9:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
            }
            i = i+1;
        }else{
            i = i+1;
        }
    });
    // DataTable
    var table = $('#tablaDetalleC').DataTable({
        "autoFill": true,
        "scrollX": true,
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
    table.columns().every( function () {
        var that = this;
        if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
            i = i+1;
        }else{
            i = i+1;
        }
    } );
} );
</script>
<!-- Contenedor de modal -->
<div class="modal fade comprobantec" id="modalComprobanteC" role="dialog">
    <!-- Inicio de cuadro de dialogo -->
    <div class="modal-dialog" style="width:800px">
        <!-- Inicio de contenedor de modal -->
        <div class="modal-content">
            <!-- Inicio de cabeza de modal -->
            <div class="modal-header" id="forma-modal">
                <!-- Titulo del modal -->
                <h4 class="modal-title text-center" style="font-size: 24; padding: 3px;">Comprobante Contable</h4>
                <!-- Inicio de contenedor de botón de cierre -->
                <div class="col-sm-offset-12" style="margin-top:-30px;">
                    <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;margin-left: -25px;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    <!-- Cierre de contenedor de botón de cierre -->
                </div>
                <!-- Cierre de cabeza de modal -->
            </div>
            <!-- Inicio de cuerpo del modal -->
            <div class="modal-body" style="margin-top: 8px">
                <?php
                #Variables autoincrementables
                $sumar = 0;
                $sumaT = 0;
                #Creados variables relacionadas con sus posibles datos inicializadas en 0 o en vacio
                $numeroComprobante=0;
                $fechaComprobante="";
                $descriptor="";
                $tipoComprobante=0;
                $tercer=0;
                $estdo=0;
                /* Variable que se envia por ajax
                 * @idC $_POST int
                 */
                $idComprobante=$_POST['idC'];
                #Consulta para mostrar los datos insertados
                $sqlC="select   numero,
                                date_format(fecha,'%d/%m/%Y'),
                                descripcion,
                                tipocomprobante,
                                tercero,
                                estado
                    from gf_comprobante_cnt
                    where id_unico=$idComprobante";
                $resultC=$mysqli->query($sqlC);
                #Variable vectorizada debido a la asignación de la función mysqli_fetch_row() se convierte en un array númerico
                $valoresComprobante= mysqli_fetch_row($resultC);
                #Variable de conteo de existencia de registro
                $conteo = mysqli_num_rows($resultC);
                #Si la variable de conteo es mayor que 0 que cargue las variables con sus respectivos valores
                if($conteo>0){
                    #Cargamos las variables con sus posibles valores
                    $numeroComprobante=$valoresComprobante[0];
                    $fechaComprobante=$valoresComprobante[1];
                    $descriptor=$valoresComprobante[2];
                    $tipoComprobante=$valoresComprobante[3];
                    $tercer1=$valoresComprobante[4];
                    $estdo=$valoresComprobante[5];
                }
                #Consulta para obtener el nombre del estado
                if(empty($estdo)){
                    $estdo =1;
                }
                $sql="select nombre from gf_estado_comprobante_cnt where id_unico=$estdo";
                #Cargamos una variable con el valor retornado por la función obtener valor la cual retornara un string con el nombre
                $nombreE=obtener_valor($sql);
                
                #Creamos el formulario que presentara el resultado de los datos anteriores
                ?>
                <!-- Inicio de contenedor con la grib de bootstrap -->
                <div class="row">
                    <!-- Inicio de contenedor con el cliente de formulario -->
                    <div class="client-form col-sm-12" style="margin-top:-20px">
                        <!-- Inicio del formulario -->
                        <form name="form" id="frmComprobante" class="form-horizontal col-sm-12" method="POST"  enctype="multipart/form-data" action="#">
                            <!-- Inicio de parrafo de texto de campos obligatorios -->
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                <!-- Parrafo de campos obligatorios -->
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                                <!-- Cierre de parrafo de texto de campos obligatorios -->
                            </p>
                            <!-- Inicio de contenedor de formulario en linea y grupo de formulario -->
                            <div class="form-inline form-group">
                                <!-- Inicio de etiqueta de fecha -->
                                <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>Fecha:
                                    <!-- Cierre de etiqueta de fecha -->
                                </label>
                                <!-- Campo de fecha -->
                                <input type="text" name="txtFechaC" id="txtFechaC" style="width:150px;padding:-2px;height:26px" class="col-sm-1 form-control input-sm" value="<?php echo $fechaComprobante; ?>" title="Fecha" placeholder="Fecha" readonly/>
                                <!-- Inicio de etiqueta de tipo comprobante -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Tipo:
                                    <!-- Cierre de etiqueta de tipo comprobante -->
                                </label>
                                <!-- Campo o select de tipo comprobante -->
                                <select name="sltTipoComprobante" id="slTipoComprobante" style="width:150px;padding:-2px;font-size:10px;height:26px" class="col-sm-1 form-control input-sm" title="Seleccione el tipo de comprobante" readonly>
                                    <?php
                                    #Consulta para cargar el tipo de comprobante dependiendo de un id_unico
                                    $sql1="select id_unico,nombre from gf_tipo_comprobante where id_unico=$tipoComprobante";
                                    #Función de carga de combos
                                    cargar_combos($sql1);
                                    #Consulta para cargar el tipo de comprobante dependiendo que sean diferentes a un id_unico
                                    $sql2="select id_unico,nombre from gf_tipo_comprobante where id_unico!=$tipoComprobante";
                                    #Función de carga de combos
                                    cargar_combos($sql2);
                                    ?>
                                </select>
                                <!-- Inicio etiqueta de número de comprobante -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Número:
                                    <!-- Cierre etiqueta de número de comprobante -->
                                </label>
                                <!-- Campo de número de comprobante -->
                                <input type="text" name="txtNumeroC" id="txtNumeroC" style="width:150px;padding:-2px;height:26px" class="col-sm-1 form-control input-sm" value="<?php echo $numeroComprobante; ?>" title="Número del comprobante" placeholder="Número" readonly/>
                                <!-- Script para cambiar el tipo de comprobante -->
                                <script type="text/javascript">
                                    /*
                                     *Toma el valor del campo sltTipoComprobante y va y pregunta a una función
                                     *la cual consulta que el último número registrado donde el tipo de
                                     *comprobante sea el enviado ó corresponda al valor del campo. Y si existe
                                     *un comprobante que se relacione a el tipo de comprobnate enviado y sea
                                     *el ultimo este suma uno y devuelve el valor y lo ubica en el campo de
                                     *text #NumeroC, peró si no existe ningún comprobante registrado que se
                                     *relacione a el tipo de comprobante este inicializara la cuenta retornando
                                     *como valor para el campo #NumeroC el año actual con 00001
                                     */
                                     $("#slTipoComprobante").change(function(){
                                        /*
                                         *Vector enviado con el valor del campo de tipo comprobante, y la posición
                                         *En la que se debe realzar la consulta
                                         */
                                        var form_data = {
                                            tipo:$("#slTipoComprobante").val(),
                                            nuevos:5
                                        };
                                        //Envio ajax
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/generarNuevos.php",
                                            data: form_data,
                                            success: function (data) {
                                                $("#txtNumeroC").val(data);
                                            }
                                        });
                                    });
                                </script>
                                <!-- Cierre de contenedor de formulario en linea y grupo de formulario -->
                            </div>
                            <!-- Inicio de contenedor de formulario en linea y deagrupamiento de formulario -->
                            <div class="form-inline form-group" style="margin-top:-15px">
                                <!-- Inicio etiqueta de tercero -->
                                <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>Tercero:
                                    <!-- Cierre etiqueta de tercero -->
                                </label>
                                <!-- Campo o select de tercero -->
                                <select name="sltTercero1" id="sltTercero1" style="width:366px;padding:-2px;font-size:10px;height:26px" class="col-sm-1 form-control input-sm" title="Seleccione tercero" readonly>
                                    <?php
                                    #Consulta para traer el tercero dependiendo del id de tercero que tiene el comprobante
                                    $sqltercero="SELECT  IF(    CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                                IF(ter.apellidouno IS NULL,'',
                                                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))=''
                                                    OR  CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                                IF(ter.apellidouno IS NULL,'',
                                                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL
                                                    ,(ter.razonsocial),
                                                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                                IF(ter.apellidouno IS NULL,'',
                                                                IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE ter.id_unico = $tercer1
                                                ORDER BY NOMBRE ASC";
                                    $ter = $mysqli->query($sqltercero);
                                    #Impresión de los valores consultados
                                    $per = mysqli_fetch_row($ter);
                                    echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].'    '.$per[2])).'</option>';
                                    #Consulta para traer los valores diferentes  del id del tercero que se relaciona con el tercero
                                    $tersql="SELECT  IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE ter.id_unico != $tercer1
                                                ";
                                    $tercer = $mysqli->query($tersql);
                                    #Impresión de los valores consultados
                                    while($per1 = mysqli_fetch_row($tercer)){
                                        echo '<option value="'.$per1[1].'">'.ucwords(mb_strtolower($per1[0].'    '.$per1[2])).'</option>';
                                    }
                                    ?>
                                </select>
                                <!-- Inclusión de la libreria select2 en el input -->
                                <script type="text/javascript">
                                    $("#sltTercero1").select2();
                                    $("#sltTercero1").attr("disabled", true);
                                    $("#slTipoComprobante").attr("disabled",true);
                                </script>
                                <!-- Inicio de etiqueta de estado -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Estado:
                                    <!-- Cierre de etiqueta de estado -->
                                </label>
                                <!-- Campo de estado -->
                                <input type="text" name="txtEstadoC" id="txtEstadoC" style="width:150px;padding:-2px;height:26px" class="col-sm-1 form-control input-sm" value="<?php echo $nombreE; ?>" title="Estado del comprobante" placeholder="Estado" required readonly/>
                                <!-- Cierre de contenedor de formulario en linea y deagrupamiento de formulario -->
                            </div>
                            <!-- Inicio de contenedor de formulario en linea y deagrupamiento de formulario -->
                            <div class="form-inline form-group" style="margin-top:-15px">
                                <!-- Inicio de etiqutea de descripción -->
                                <label class="control-label col-sm-2">
                                    Descripción:
                                    <!-- Cierre de etiqutea de descripción -->
                                </label>
                                <!-- Campo de descripción -->
                                <textarea name="txtDescriptor" id="txtDescriptor" class="form-control col-sm-1 area" maxlength="500" rows="4" cols="30" title="Descripción de comprobante" style="width:366px;margin-top:-1px;max-height:40px;" placeholder="Descripción" readonly><?php echo $descriptor; ?></textarea>
                                <!-- Boton -->
                                <!--<div class="col-sm-1" style="margin-top:15px;margin-left:105px">
                                    <a onclick="javascript:modificarComprobante()" id="btnModificar" class="btn sombra btn-primary" title="Modificar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                                </div>-->
                                <!-- Cierre de contenedor de formulario en linea y deagrupamiento de formulario -->
                            </div>
                            <!-- Cierre del formulario -->
                        </form>
                        <!-- Cierre de contenedor con el cliente del formulario -->
                    </div>
                    <!-- Inicio de contenedor de la tabla -->
                    <div class="col-sm-12" style="margin-top:10px">
                        <!-- Contenedor responsivo de la tabla -->
                        <div class="table-responsive">
                            <!-- Inicio de la tabla -->
                            <?php $totalD = 0; $totalC = 0; ?>
                            <table id="tablaDetalleC" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <!-- Inicio de la cabeza de la tabla -->
                                <thead>
                                    <!-- Campos para titulos de los campos -->
                                    <tr>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Cuenta</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Centro Costo</strong></td>
                                        <td class="cabeza"><strong>Proyecto</strong></td>
                                        <td class="cabeza"><strong>Débito</strong></td>
                                        <td class="cabeza"><strong>Crédito</strong></td>
                                    </tr>
                                    <!-- Campos para filtros -->
                                    <tr>
                                        <th width="7%"></th>
                                        <th class="cabeza">Cuenta Contable</th>
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Centro Costo</th>
                                        <th class="cabeza">Proyecto</th>
                                        <th class="cabeza">Débito</th>
                                        <th class="cabeza">Crédito</th>
                                    </tr>
                                    <!-- Cierre de la cabeza de la tabla -->
                                </thead>
                                <!-- Inicio del cuerpo de la tabla -->
                                <tbody>
                                    <?php
                                    $sql="SELECT   DT.id_unico,
                                                        CT.id_unico as cuenta,
                                                        CT.nombre,
                                                        CT.codi_cuenta,
                                                        CT.naturaleza,
                                                        N.id_unico,
                                                        N.nombre,
                                                        (IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                                     (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) )AS 'NOMBRE',
                                                                     ter.id_unico,
                                                                     CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',
                                                        CC.id_unico,
                                                        CC.nombre,
                                                        PR.id_unico,
                                                        PR.nombre,
                                                        DT.valor
                                    FROM       gf_detalle_comprobante DT
                                    LEFT JOIN  gf_cuenta CT ON DT.cuenta = CT.id_unico
                                    LEFT JOIN  gf_naturaleza N ON N.id_unico = DT.naturaleza
                                    LEFT JOIN  gf_tercero ter ON DT.tercero = ter.id_unico
                                    LEFT JOIN  gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                                    LEFT JOIN  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                                    LEFT JOIN  gf_proyecto PR ON DT.proyecto = PR.id_unico
                                    WHERE (DT.comprobante) = $idComprobante";
                                    $rs = $mysqli->query($sql);
                                    if(mysqli_num_rows($rs)>0){
                                    while ($row = mysqli_fetch_row($rs)) { ?>
                                    <tr>
                                        <td class="campos">
                                            <!--<a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                                <li class="glyphicon glyphicon-trash"></li>
                                            </a>
                                            <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarT(<?php echo $row[0]; ?>);javascript:cargarT2(<?php echo $row[0]; ?>);javascript:cargarCentro(<?php echo $row[0]; ?>);javascript:cargarCentro2(<?php echo $row[0]; ?>);javascript:cargarProyecto(<?php echo $row[0]; ?>);javascript:cargarProyecto2(<?php echo $row[0]; ?>)">
                                                <li class="glyphicon glyphicon-edit"></li>
                                            </a>-->
                                        </td>
                                        <!-- Código de cuenta y nombre de la cuenta -->
                                        <td class="campos text-left" >
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'. (ucwords(mb_strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                                            <select style="display: none;padding:2px" class="col-sm-12 campoD" id="sltC<?php echo $row[0]; ?>">
                                                <option value="<?php echo $row[1];?>"><?php echo $row[3].'-'.$row[2]; ?></option>
                                                    <?php
                                                    $sqlCTN = "SELECT DISTINCT id_unico,codi_cuenta,nombre FROM gf_cuenta WHERE (codi_cuenta != $row[3]) AND movimiento = 1
                                                OR      centrocosto = 1
                                                OR      auxiliartercero = 1
                                                OR      auxiliarproyecto = 1";
                                                    $result = $mysqli->query($sqlCTN);
                                                    while ($s = mysqli_fetch_row($result)){
                                                        echo '<option value="'.$s[0].'">'.$s[1].' - '.$s[2].'</option>';
                                                    }
                                                    ?>
                                            </select>
                                        </td>
                                        <!-- Datos de tercero -->
                                        <td class="campos text-left">
                                            <?php echo '<label class="valorLabel" title="'.$row[9].'" style="font-weight:normal" id="tercero'.$row[0].'">'. (ucwords(mb_strtolower($row[7]))).'</label>'; ?>
                                            <select id="sltTercero<?php echo $row[0]; ?>" style="display: none;padding: 2px;height:18" class="col-sm-12 campoD">
                                                <option value="<?php echo $row[8] ?>"><?php echo  utf8_encode(ucwords(mb_strtolower($row[7]))) ?></option>
                                                <?php
                                                $sqlTR = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                        (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE',
                                                        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                        WHERE  ter.id_unico != $row[8]";
                                                $resulta = $mysqli->query($sqlTR);
                                                while($e = mysqli_fetch_row($resulta)){
                                                    echo '<option value="'.$e[1].'">'.ucwords(mb_strtolower($e[0].' - '.$e[2])).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="campos text-left">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC'.$row[0].'">'. (ucwords(mb_strtolower($row[11]))).'</label>'; ?>
                                            <select id="sltcentroC<?php echo $row[0]; ?>" style="display: none;padding:2px;height:19px" class="col-sm-12 campoD">
                                                <option value="<?php echo $row[10]; ?>"><?php echo $row[11]; ?></option>
                                                <?php
                                                $sqlCCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != '$row[10]'";
                                                $g = $mysqli->query($sqlCCT);
                                                while($f = mysqli_fetch_row($g)){
                                                    echo '<option value="'.$f[0].'">'.$f[1].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="campos text-left">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto'.$row[0].'">'. (ucwords(mb_strtolower($row[13]))).'</label>'; ?>
                                            <select style="display: none;padding:2px;height:19px" class="col-sm-12 campoD" id="sltProyecto<?php echo $row[0]; ?>">
                                                <option value="<?php echo $row[12]; ?>"><?php echo $row[13]; ?></option>
                                                <?php
                                                $sqlCP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $row[17]";
                                                $result = $mysqli->query($sqlCP);
                                                while ($y = mysqli_fetch_row($result)){
                                                    echo '<option value="'.$y[0].'">'.$y[1].'</option>';
                                                }
                                                ?>
                                                <!-- Validación de campos en la tabla -->
                                            </select>
                                        </td>
                                        <!-- Campo de valor debito y credito. Validación para imprimir valor -->
                                        <td class="campos text-right" align="center">

                                            <?php

                                            if ($row[4] == 1) {
                                                if($row[14] >= 0){
                                                    $totalD += $row[14];
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                                    
                                                }else{
                                                    echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                                }
                                            }else {
                                                if($row[14] <= 0){
                                                    $x = (float) substr($row[14],'1');
                                                    $totalD += $x;
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                                                }else{
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                                }
                                            }

                                           ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php
                                            if ($row[4] == 2) {
                                                if($row[14] >= 0){
                                                    $totalC += $row[14];
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                                }else{
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                                }
                                            }else{
                                               if($row[14] <= 0){
                                                    $x = (float) substr($row[14],'1');
                                                    $totalC += $x;
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                                                }else{
                                                    echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                                }
                                            }?>
                                        </td>
                                    </tr>
                                    <?php }
                                    }
                                    ?>
                                </tbody>
                                <!-- Cierre de la tabla -->
                            </table>
                            <!-- Cierre de contenedor responsivo -->
                        </div>
                        <!-- Cierre de contenedor de la tabla -->
                    </div>
                    <!-- Cierre de contenedor con la grib de bootstrap -->
                    <div class="col-sm-12 col-md-12 col-lg-12 text-right">
                        <label for="">Total Débito:  <?php echo "$".number_format($totalD,2,'.',','); ?></label>
                        <label for="">Total Crédito: <?php echo "$".number_format($totalC,2,'.',','); ?></label>
                    </div>
                </div>
                <!-- Cierre del cuerpo del modal -->
            </div>
            <!-- Inicio de footer del modal -->
            <div id="forma-modal" class="modal-footer"></div>
            <!-- Fin de footer del modal -->
            <!-- Cierre de contenedor de modal -->
        </div>
        <!-- Cierre de cuadro de dialogo -->
    </div>
    <!-- Cierr de contenedor de modal -->
</div>
