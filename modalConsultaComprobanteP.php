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
        $('#tablaDetalleP thead th').each( function () {
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
    var table = $('#tablaDetalleP').DataTable({
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
<div class="modal fade comprobantep" id="modalComprobanteP" role="dialog">
    <!-- Inicio de cuadro de dialogo -->
    <div class="modal-dialog" style="width:800px">
        <!-- Inicio contenido del modal -->
         <div class="modal-content">
            <!-- Inicio de cabeza de modal -->
            <div class="modal-header" id="forma-modal">
                <!-- Titulo del modal -->
                <h4 class="modal-title text-center" style="font-size: 24; padding: 3px;">Comprobante Presupuestal</h4>
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
                #Creamos las variables inicializadas en 0 ó en vacio para cargar los valores
                $numeroP=0;
                $fechaP="";
                $fechaVP="";
                $descripcionP="";
                $tipoComprobanteP=0;
                $terceroP=0;
                $estadoP=0;
                $idPresupuestal = "";
                #Captura de la variable enviada por post
                if(!empty($_POST['idP'])){
                    $idPresupuestal=$_POST['idP'];
                    #Consulta para obtener el valores de las variables
                    $sqlComprobanteP="  select  numero,
                                                date_format(fecha,'%d/%m/%Y'),
                                                date_format(fechavencimiento,'%d/%m/%Y'),
                                                descripcion,
                                                tipocomprobante,
                                                tercero,
                                                estado
                                        from gf_comprobante_pptal
                                        where id_unico=$idPresupuestal";
                    $resultComprobanteP=$mysqli->query($sqlComprobanteP);
                    #Variable vectorizada debido a la asignación de la función mysqli_fetch_row() se convierte en un array númerico
                    $valorComprobanteP= mysqli_fetch_row($resultComprobanteP);
                    #Variable de conteo de existencia de registro
                    $conteoP= mysqli_num_rows($resultComprobanteP);
                    #Si la variable de conteoP es mayor que 0 que cargue las variables con sus respectivos valores
                    if($conteoP>0){
                        #Cargamos las variables con sus posibles valores
                        $numeroP=$valorComprobanteP[0];
                        $fechaP=$valorComprobanteP[1];
                        $fechaVP=$valorComprobanteP[2];
                        $descripcionP=$valorComprobanteP[3];
                        $tipoComprobanteP=$valorComprobanteP[4];
                        $terceroP=$valorComprobanteP[5];
                        $estadoP=$valorComprobanteP[6];
                    }
                    #Consulta para obtener el nombre del estado
                    $sql="select nombre from gf_estado_comprobante_pptal where id_unico=$estadoP";
                    #Cargamos una variable con el valor retornado por la función obtener valor la cual retornara un string con el nombre
                    $nombreEP=obtener_valor($sql);
                    #Creamos el formulario que presentara el resultado de los datos anteriores

                }
                ?>
                <!-- Inicio de contenedor con la grib de bootstrap -->
                <div class="row">
                    <!-- Inicio de contenedor con el cliente de formulario -->
                    <div class="client-form col-sm-12" style="margin-top:-20px">
                        <!-- Inicio del formulario -->
                        <form name="form" id="frmComprobanteP" class="form-horizontal col-sm-12" method="POST"  enctype="multipart/form-data" action="#">
                            <!-- Inicio de parrafo de texto de campos obligatorios -->
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                <!-- Parrafo de campos obligatorios -->
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                                <!-- Cierre de parrafo de texto de campos obligatorios -->
                            </p>
                            <div class="form-inline form-group">
                                <!-- Inicio de etiqueta de fecha -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Fecha:
                                    <!-- Cierre de etiqueta de fecha -->
                                </label>
                                <!-- Campo de fecha -->
                                <input type="text" name="txtFechaP" id="txtFechaP" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $fechaP; ?>" title="Fecha" placeholder="Fecha" required readonly/>
                                <!-- Inicio de etiqueta de tipo comprobante -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Tipo:
                                    <!-- Cierre de etiqueta de tipo comprobante -->
                                </label>
                                <!-- Campo o select de tipo comprobante -->
                                <select name="sltTipoComprobanteP" id="slTipoComprobanteP" style="width:250px;padding:-2px;font-size:10px;height:30px" class="col-sm-1 form-control input-sm" title="Seleccione el tipo de comprobante presupuestal" required readonly>
                                    <?php
                                    #Consulta para cargar el tipo de comprobante dependiendo de un id_unico
                                    $sql1="select id_unico,nombre from gf_tipo_comprobante_pptal where id_unico=$tipoComprobanteP";
                                    #Función de carga de combos
                                    cargar_combos($sql1);
                                    #Consulta para cargar el tipo de comprobante dependiendo que sean diferentes a un id_unico
                                    $sql2="select id_unico,nombre from gf_tipo_comprobante_pptal where id_unico!=$tipoComprobanteP";
                                    #Función de carga de combos
                                    cargar_combos($sql2);
                                    ?>
                                </select>
                                <!-- Incluimos la libreria select2 -->
                                <script type="text/javascript">
                                    $("#slTipoComprobanteP").select2();
                                </script>
                                <!-- Inicio etiqueta de número de comprobante -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Número:
                                    <!-- Cierre etiqueta de número de comprobante -->
                                </label>
                                <!-- Campo de número de comprobante -->
                                <input type="text" name="txtNumeroP" id="txtNumeroP" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $numeroP; ?>" title="Número del comprobante presupuestal" placeholder="Número" required readonly/>
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
                                     $("#slTipoComprobanteP").change(function(){
                                        /*
                                         *Vector enviado con el valor del campo de tipo comprobante, y la posición
                                         *En la que se debe realzar la consulta
                                         */
                                        var form_data = {
                                            tipo:$("#slTipoComprobanteP").val(),
                                            nuevos:5
                                        };
                                        //Envio ajax
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/generarNuevos.php",
                                            data: form_data,
                                            success: function (data) {
                                                $("#txtNumeroP").val(data);
                                            }
                                        });
                                    });
                                </script>
                                <!-- Cierre de contenedor de formulario en linea y grupo de formulario -->
                            </div>
                            <!-- Inicio de contenedor de formulario en linea y deagrupamiento de formulario -->
                                <div class="form-inline form-group" style="margin-top:-15px">
                                    <!-- Inicio de etiqueta de fecha vencimiento-->
                                    <label class="control-label col-sm-1">
                                        <strong class="obligado">*</strong>Fecha V:
                                        <!-- Cierre de etiqueta de fecha vencimiento-->
                                    </label>
                                    <!-- Campo de fecha vencimiento-->
                                    <input type="text" name="txtFechaVP" id="txtFechaVP" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $fechaP; ?>" title="Fecha vencimiento" placeholder="Fecha" required readonly/>
                                    <!-- Inicio etiqueta de tercero -->
                                    <label class="control-label col-sm-1">
                                        <strong class="obligado">*</strong>Tercero:
                                        <!-- Cierre etiqueta de tercero -->
                                    </label>
                                    <!-- Campo o select de tercero -->
                                    <select name="sltTerceroP" id="sltTerceroP" style="width:465px;padding:-2px;font-size:10px;height:30px" class="col-sm-1 form-control input-sm" title="Seleccione tercero" required readonly>
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
                                                    WHERE ter.id_unico = $terceroP
                                                    ORDER BY NOMBRE ASC";
                                        $ter = $mysqli->query($sqltercero);
                                        #Impresión de los valores consultados
                                        $per = mysqli_fetch_row($ter);
                                        echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].'    '.$per[2])).'</option>';
                                        #Consulta para traer los valores diferentes  del id del tercero que se relaciona con el tercero
                                        $tersql="SELECT  IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                    WHERE ter.id_unico != $terceroP
                                                    ";
                                        $tercer = $mysqli->query($tersql);
                                        #Impresión de los valores consultados
                                        while($per1 = mysqli_fetch_row($tercer)){
                                            echo '<option value="'.$per1[1].'">'.ucwords(strtolower($per1[0].'    '.$per1[2])).'</option>';
                                        }
                                        ?>
                                    </select>
                                    <!-- Inclusión de la libreria select2 en el input -->
                                    <script type="text/javascript">
                                        $("#sltTerceroP").select2();
                                        $("#sltTerceroP").attr("disabled", true);
                                        $("#slTipoComprobanteP").attr('disabled',true);
                                    </script>
                                    <!-- Cierrede contenedor de formulario en linea y deagrupamiento de formulario -->
                            </div>
                            <!-- Inicio de contenedor de formulario en linea y deagrupamiento de formulario -->
                            <div class="form-inline form-group" style="margin-top:-15px">
                                <!-- Inicio de etiqueta de estado -->
                                <label class="control-label col-sm-1">
                                    <strong class="obligado">*</strong>Estado:
                                    <!-- Cierre de etiqueta de estado -->
                                </label>
                                <!-- Campo de estado -->
                                <input type="text" name="txtEstadoP" id="txtEstadoP" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $nombreEP; ?>" title="Estado del comprobante presupuestal" placeholder="Estado" required readonly/>
                                <!-- Inicio de etiqutea de descripción -->
                                <label class="control-label col-sm-1">
                                    Descrip<br/>ción:
                                    <!-- Cierre de etiqutea de descripción -->
                                </label>
                                <!-- Campo de descripción -->
                                <textarea name="txtDescriptor" id="txtDescriptor" class="form-control col-sm-1 area" maxlength="500" rows="4" cols="30" title="Descripción del comprobante presupuestal" style="width:465px;margin-top:-1px;max-height:40px;font-size: 10px" placeholder="Descripción" readonly><?php echo $descripcionP; ?></textarea>
                                <!-- Cierre de contenedor de formulario en linea y deagrupamiento de formulario -->
                            </div>
                        </form>
                        <!-- Cierre de contenedor con el cliente de formulario -->
                    </div>
                    <!-- Inicio de contenedor de la tabla -->
                    <div class="col-sm-12" style="margin-top:10px">
                        <!-- Contenedor responsivo de la tabla -->
                        <?php $totalD = 0; ?>
                        <div class="table-responsive">
                            <!-- Inicio de la tabla -->
                            <table id="tablaDetalleP" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <!-- Cierre de contenedor responsivo-->
                                <thead>
                                    <tr>
                                      <td width="7%"></td>
                                      <td class="cabeza"><strong>Concepto</strong></td>
                                      <td class="cabeza"><strong>Rubro</strong></td>
                                      <td class="cabeza"><strong>Valor</strong></td>
                                    </tr>
                                    <tr>
                                      <th width="7%"></th>
                                      <th>Nombre</th>
                                      <th>Rubro</th>
                                      <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $queryGen = "SELECT DISTINCT detComP.id_unico, detComP.conceptoRubro, detComP.rubroFuente, detComP.valor
                                    FROM gf_detalle_comprobante_pptal detComP
                                    WHERE detComP.comprobantepptal = $idPresupuestal";
                                    $resultP=$mysqli->query($queryGen);
                                    while ($row = mysqli_fetch_row($resultP)) { ?>
                                        <tr>
                                            <td></td>
                                            <td class="campos" align="left">
                                                <?php
                                                $sqlC = "SELECT     cn.nombre
                                                        FROM        gf_concepto_rubro cntr
                                                        LEFT JOIN   gf_concepto cn ON cntr.concepto = cn.id_unico
                                                        WHERE       cntr.id_unico = $row[1]";
                                                $resultC = $mysqli->query($sqlC);
                                                $rC = mysqli_fetch_row($resultC);
                                                echo ucwords(mb_strtolower($rC[0]));
                                                ?>
                                            </td>
                                            <td class="campos" align="left">
                                                <?php
                                                $sqlR = "SELECT     rb.nombre
                                                        FROM        gf_rubro_fuente rbf
                                                        LEFT JOIN   gf_rubro_pptal rb ON rbf.rubro = rb.id_unico
                                                        WHERE       rbf.id_unico = $row[2]";
                                                $resultR = $mysqli->query($sqlR);
                                                $rR = mysqli_fetch_row($resultR);
                                                echo ucwords(mb_strtolower($rR[0]));
                                                ?>
                                            </td>
                                            <td class="campos text-right">
                                                <?php
                                                $totalD += $row[3];
                                                echo number_format($row[3], 2, '.', ',');
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <!-- Cierre de contenedor responsivo de la tabla -->
                        </div>
                        <!-- Cierre de contenedor de la tabla -->
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 text-right">
                        <label>Total: <?php echo number_format($totalD,2,',','.') ?></label>
                    </div>
                    <!-- Cierre de contenedor con la grib de bootstrap -->
                </div>
                <!-- Cierre del cuerpo del modal -->
            </div>
            <!-- Inicio de footer del modal -->
            <div id="forma-modal" class="modal-footer"></div>
            <!-- Fin de footer del modal -->
            <!-- Cierre de contenedor del modal -->
         </div>
        <!-- Cierre de cuadro de dialogo -->
    </div>
</div>