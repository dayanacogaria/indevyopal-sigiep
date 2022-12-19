<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 02/06/2017
 * Time: 10:24 AM
 *
 * 09/06/2017
 * Se agrego filtro por compañia
 */
require ('head.php');
require ('Conexion/conexion.php');
$compania = $_SESSION['compania'];
$param    = $_SESSION['anno'];
?>
    <title>Cargue Archivo de Viasoft</title>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/jquery-ui.js"></script>
    <style type="text/css" media="screen">
        label #sltTipoC-error, #flViasoft-error, #txtFecha-error, #sltBanco-error, #sltTercero-error {
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;
        }
    </style>
    <script>
        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function(error, element) {
                    $( element )
                        .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });

        $(function(){
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if(dia < 10){
                dia = "0" + dia;
            }
            if(mes < 10){
                mes = "0" + mes;
            }
            //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#txtFecha").datepicker({changeMonth: true}).val();
        });
    </script>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php include ('menu.php'); ?>
            <div class="col-sm-10 form-horizontal">
                <h2 id="forma-titulo3" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px" align="center">Cargue Archivo de Viasoft</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarRecaudoViasoftJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom:10px">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group">
                            <label for="sltTipoC" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Comprobante:</label>
                            <select name="sltTipoC" id="sltTipoC" class="form-control col-sm-1 select2" style="width: 35%" title="Seleccione tipo comprobante de recaudo" required>
                                <?php
                                echo "<option value=\"\">Tipo Comprobante</option>";
                                $sqlTC = "SELECT tpc.id_unico,CONCAT(tpc.sigla,' ',tpc.nombre) FROM gf_tipo_comprobante tpc  LEFT JOIN gf_clase_contable cls ON cls.id_unico = tpc.clasecontable WHERE tpc.comprobante_pptal IS NOT NULL AND tpc.clasecontable = 9 AND tpc.compania = $compania";
                                $resultC = $mysqli->query($sqlTC);
                                while($rowTC = mysqli_fetch_row($resultC)){
                                    echo "<option value=\"".$rowTC[0]."\">".$rowTC[1]."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="txtFecha" class="control-label col-sm-5"><strong class="obligado">*</strong>Fecha:</label>
                            <input type="text" name="txtFecha" id="txtFecha" class="form-control col-sm-1" style="width: 35%" title="Ingrese fecha" value="<?php echo date('d/m/Y') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="sltBanco" class="control-label col-sm-5"><strong class="obligado">*</strong>Banco:</label>
                            <select name="sltBanco" id="sltBanco" class="form-control col-sm-1 select2" style="width: 35%" title="Seleccione banco" required>
                                <?php
                                echo "<option value=\"\">Banco</option>";
                                $sqlB = "SELECT ctb.id_unico, CONCAT_WS(' ',ctb.numerocuenta ,ctb.descripcion) FROM gf_cuenta_bancaria ctb
                                        LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                        WHERE ctbt.tercero = $compania AND ctb.parametrizacionanno = $param";
                                $resultB = $mysqli->query($sqlB);
                                while ($rowB = mysqli_fetch_row($resultB)) {
                                    echo "<option value=\"".$rowB[0]."\">".ucwords(mb_strtolower($rowB[1]))."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sltTercero" class="control-label col-sm-5"><strong class="obligado">*</strong>Tercero a registrar :</label>
                            <select name="sltTercero" id="sltTercero" class="form-control col-sm-1 select2" style="width: 35%" title="Seleccione tercero" required>
                                <?php
                                echo "<option value=\"\">Tercero</option>";
                                $sqlT = "SELECT   ter.id_unico,
                                                  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                  (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                                  CONCAT_WS(' ',ti.nombre,' - ',ter.numeroidentificacion,' ',ter.digitoverficacion) AS 'TipoD'
                                        FROM      gf_tercero ter
                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
                                        WHERE ter.compania = $compania 
                                        ORDER BY  NOMBRE ASC";
                                $resultT = $mysqli->query($sqlT);
                                while($rowT = mysqli_fetch_row($resultT)) {
                                    echo "<option value=".$rowT[0].">".ucwords(mb_strtolower($rowT[1]))." (".$rowT[2].")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="flViasoft" class="control-label col-sm-5"><strong class="obligado">*</strong>Subir Archivo Viasoft:</label>
                            <input type="file" name="flViasoft" id="flViasoft" class="form-control" title="Seleccione un archivo de excel para leer" accept=".xls,.xlsx" style="width: 35%" required>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-5"></div>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-left: 0px;"><li class="glyphicon glyphicon-cloud-upload"></li></button>
                            <input type="hidden" name="MM_insert" >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2({allowClear:true});
    </script>
</body>
<?php require ('footer.php'); ?>
</html>