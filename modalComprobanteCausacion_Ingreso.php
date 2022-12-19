<div class="modal fade causa" id="modalCausacion" role="dialog" align="center" >
    <div class="modal-dialog" style="width:800px">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">          
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Comprobante de Causación</h4>
                <div class="col-sm-offset-12" style="margin-top:-30px;">
                    <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;margin-left: -25px;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    <!-- Cierre de contenedor de botón de cierre -->
                </div>
            </div>
            <div class="modal-body row" style="margin-top: 8px">
                <?php 
                require 'Conexion/conexion.php';
                $nomtipoc = "";
                $sigaltipoc = "";
                $numeroCrn = "";
                $fechaComprobante = "";
                $tercero = "";
                $tipoidentificacion = "";
                $estado = "";
                $descriptor = "";
                $clasecon = "";
                $numcont = "";
                if(!empty($_POST['com'])){
                    $com = $_POST['com']; 
                    $sql15 = "  SELECT      tpc.nombre,
                                            tpc.sigla,
                                            cnt.numero,
                                            date_format(cnt.fecha,'%d/%m/%Y'),
                                            IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
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
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',
                                            es.nombre,
                                            cnt.descripcion,
                                            clcon.nombre,
                                            cnt.numerocontrato
                                FROM        gf_comprobante_cnt cnt 
                                LEFT JOIN   gf_tipo_comprobante tpc ON cnt.tipocomprobante = tpc.id_unico
                                LEFT JOIN   gf_tercero ter ON ter.id_unico = cnt.tercero
                                LEFT JOIN   gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                LEFT JOIN   gf_estado_comprobante_cnt  es ON cnt.estado = es.id_unico
                                LEFT JOIN   gf_clase_contrato clcon ON clcon.id_unico = cnt.clasecontrato
                                WHERE       cnt.id_unico = $com";
                    $result15 = $mysqli->query($sql15);
                    $filaR = $result15->fetch_row();
                    $nomtipoc = $filaR[0];
                    $sigaltipoc = $filaR[1];
                    $numeroCrn = $filaR[2];
                    $fechaComprobante = $filaR[3];
                    $tercero = $filaR[4];
                    $tipoidentificacion = $filaR[6];
                    $estado = $filaR[7];
                    $descriptor = $filaR[8];
                    $clasecon = $filaR[9];
                    $numcont = $filaR[10];
                }
                 ?>
                 <!-- Inicio de contenedor de formulario en linea y grupo de formulario -->
                <div class="form-inline form-group">    
                    <!-- Inicio de etiqueta de fecha -->
                    <label class="control-label col-sm-2">
                        <strong class="obligado">*</strong>Fecha:                                    
                        <!-- Cierre de etiqueta de fecha -->
                    </label>                            
                    <!-- Campo de fecha -->
                    <input type="text" name="txtFechaC" id="txtFechaC" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $fechaComprobante; ?>" title="Fecha" placeholder="Fecha" readonly/>
                    <!-- Inicio de etiqueta de tipo comprobante -->
                    <label class="control-label col-sm-1">
                        <strong class="obligado">*</strong>Tipo:
                        <!-- Cierre de etiqueta de tipo comprobante -->
                    </label>
                    <!-- Campo o select de tipo comprobante -->
                    <select name="sltTipoComprobante" id="slTipoComprobante" style="width:200px;padding:-2px;font-size:10px;height:30px" class="col-sm-1 form-control input-sm" title="Seleccione el tipo de comprobante" readonly>                                
                        <?php       
                       echo "<option value=\"\">".ucwords(strtolower($nomtipoc)).PHP_EOL.$sigaltipoc."</option>";
                        ?>
                    </select>                                
                    <!-- Inicio etiqueta de número de comprobante -->
                    <label class="control-label col-sm-1">
                        <strong class="obligado">*</strong>Número:
                        <!-- Cierre etiqueta de número de comprobante -->
                    </label>
                    <!-- Campo de número de comprobante -->
                    <input type="text" name="txtNumeroC" id="txtNumeroC" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $numeroCrn; ?>" title="Número del comprobante" placeholder="Número" readonly/>                    
                    <!-- Cierre de contenedor de formulario en linea y grupo de formulario -->                                
                </div><br/>
                <!-- Inicio de contenedor de formulario en linea y deagrupamiento de formulario -->
                <div class="form-inline form-group" style="margin-top: 10px">
                    <!-- Inicio etiqueta de tercero -->
                    <label class="control-label col-sm-2">                                    
                        <strong class="obligado">*</strong>Tercero:                                    
                        <!-- Cierre etiqueta de tercero -->
                    </label>
                    <!-- Campo o select de tercero -->
                    <select name="sltTercero1" id="sltTercero1" style="width:418px;padding:-2px;font-size:10px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm text-left" title="Seleccione tercero" readonly>
                        <?php 
                        echo "<option value=\"\">".ucwords(mb_strtolower($tercero.PHP_EOL.$tipoidentificacion))."</option>";      
                        ?>
                    </select>                   
                    <!-- Inicio de etiqueta de estado -->
                    <label class="control-label col-sm-1">
                        <strong class="obligado">*</strong>Estado:
                        <!-- Cierre de etiqueta de estado -->
                    </label>
                    <!-- Campo de estado -->
                    <input type="text" name="txtEstadoC" id="txtEstadoC" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $estado; ?>" title="Estado del comprobante" placeholder="Estado" required readonly/>                                
                    <!-- Cierre de contenedor de formulario en linea y deagrupamiento de formulario -->
                </div>
                <br/>
                <div class="form-inline form-group" style="margin-top: 20px;margin-bottom:10px">
                    <!-- Inicio de etiqutea de descripción -->
                    <label class="control-label col-sm-2">
                        Descripción:
                        <!-- Cierre de etiqutea de descripción -->
                    </label>                                
                    <!-- Campo de descripción -->
                    <textarea name="txtDescriptor" id="txtDescriptor" class="form-control col-sm-1 area" maxlength="500" rows="4" cols="30" title="Descripción de comprobante" style="width:418px;max-height:40px;font-size: 10px" placeholder="Descripción" readonly><?php echo $descriptor; ?></textarea>
                    <label class="control-label col-sm-1">
                        Clase Contrato:
                        <!-- Cierre de etiqueta de estado -->
                    </label>
                    <select name="sltClaseContrato1" id="sltClaseContrato1" style="width:150px;padding:-2px;font-size:10px;height:30px" class="col-sm-1 form-control input-sm" title="Seleccione clasecontrato" readonly>
                        <?php 
                        echo "<option value=\"\">".ucwords(strtolower($clasecon))."</option>";      
                        ?>
                    </select>                   
                </div>
                <br/>
                <div class="form-inline form-group" style="margin-top: 35px">
                    <!-- Inicio de etiqutea de descripción -->
                    <label class="control-label col-sm-2">
                        Nro Contrato:
                        <!-- Cierre de etiqutea de descripción -->
                    </label>
                    <input type="text" name="txtNumCom" id="txtNumCom" style="width:150px;padding:-2px;height:30px;font-size: 10px" class="col-sm-1 form-control input-sm" value="<?php echo $numcont; ?>" title="Número del contrato" placeholder="Nro Contrato" required readonly/>                                
                </div>
                <div class="table-responsive col-sm-12" style="margin-top:10px"> 
                    
                    <!-- Inicio de la tabla -->
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
                           <?php if(!empty($_POST['com'])){
                            $com = $_POST['com'];
                            $sql = "SELECT  CT.codi_cuenta,
                                            CT.nombre,
                                            CT.naturaleza,                                            
                                            IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
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
                                            CC.nombre,                                            
                                            PR.nombre,
                                            DT.valor                                                        
                                    FROM       gf_detalle_comprobante DT
                                    LEFT JOIN  gf_cuenta CT ON DT.cuenta = CT.id_unico
                                    LEFT JOIN  gf_naturaleza N ON N.id_unico = DT.naturaleza
                                    LEFT JOIN  gf_tercero ter ON DT.tercero = ter.id_unico
                                    LEFT JOIN  gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                                    LEFT JOIN  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                                    LEFT JOIN  gf_proyecto PR ON DT.proyecto = PR.id_unico
                                    WHERE DT.comprobante = $com";
                                    $resultTemp = $mysqli->query($sql);
                                    while ($rowTemp = mysqli_fetch_row($resultTemp)) {
                                        echo "<tr>";
                                        echo "<td></td>";
                                        echo "<td>".$rowTemp[0].PHP_EOL.ucwords(mb_strtolower($rowTemp[1]))."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[3]))."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[4]))."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[5]))."</td>";                                        
                                        if($rowTemp[2] == 1){
                                            if($rowTemp[6] > 0){
                                                echo "<td>".number_format($rowTemp[6],2,',','.')."</td>";
                                                echo "<td>0.00</td>";
                                            }else{
                                                echo "<td>0.00</td>";
                                                echo "<td>".number_format($rowTemp[6]*-1,2,',','.')."</td>";
                                            }
                                        }elseif ($rowTemp[2] == 2) {
                                            if($rowTemp[6] > 0){
                                                echo "<td>0.00</td>";
                                                echo "<td>".number_format($rowTemp[6],2,',','.')."</td>";
                                            }else{
                                                echo "<td>".number_format($rowTemp[6]*-1,2,',','.')."</td>";
                                                echo "<td>0.00</td>";
                                            }
                                        }                                        
                                        echo "</tr>";
                                    }
                                }
                            ?>
                            <!-- Cierre del cuerpo de la tabla -->
                        </tbody>
                        <!-- Cierre de la tabla -->
                    </table>
                </div>                            
            </div>
            <div id="forma-modal" class="modal-footer">                
            </div>
        </div>
    </div>
</div>
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
<script type="text/javascript">
    $("#modalCausacion").on('shown.bs.modal',function(){
        var dataTable = $("#tablaDetalleC").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    });
</script>