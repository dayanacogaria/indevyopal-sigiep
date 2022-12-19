
<!-- Librerias de carga para el datapicker -->

<?php 
 @session_start();
 $anno     = $_SESSION['anno'];
 $compania =$_SESSION['compania'];
?>

<link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <script>

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
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
               
        $("#sltFecha").datepicker({changeMonth: true,}).val();
       
        
        
});
</script>

<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<div class="modal fade recaDec" id="modalRecaudo" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content" style="width: 450px;">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Recaudo</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="row text-left">
                            <form class="form-horizontal" id="formu" name="formu" method="post" action="jsonComercio/registrarRecaudoJson.php">                                                        
                                <div class="form-group" >

                                    <?php
                                        require 'Conexion/conexion.php';
                                        $idDec = $_POST['id'];
                                        $Decla = "SELECT id_unico, cod_dec FROM  gc_declaracion WHERE id_unico ='$idDec'";
                                        $resDecla = $mysqli->query($Decla);
                                        $RD = mysqli_fetch_row($resDecla);
                                    ?>
                                    <input type="hidden" name="txtTper" value="">
                                    <input type="hidden" name="txtTdec1" value="">
                                    <input type="hidden" name="txtfecD" value="">
                                    <input type="hidden" name="txtidDec" value="<?php echo $RD[0] ?>">
                                    <input type="hidden" name="X" value="">
                                    <label for="banco" class="control-label col-sm-4 col-md-4 col-lg-4 text-right"><strong class="obligado">*</strong>Banco:</label>
                                        <?php 
                                          $per = "SELECT cb.id_unico, CONCAT(cb.numerocuenta,' - ',t.razonsocial)
                                                    FROM gf_cuenta_bancaria cb
                                                    lEFT JOIN  gf_cuenta_bancaria_tercero cbt ON cbt.cuentabancaria = cb.id_unico
                                                    lEFT JOIN  gf_tercero t ON cb.banco = t.id_unico
                                                    WHERE cbt.tercero = $compania" AND cb.parametrizacionanno = $anno ;
                                            $periodo = $mysqli->query($per);
                                        ?> 
                                    <div class="col-sm-6 col-md-6 col-lg-6">    
                                        <select  id="banco" name="banco" class="form-control select2" title="Seleccione el Banco" required>
                                            <option value="" >Banco</option>
                                            <?php 
                                                while($rowE = mysqli_fetch_row($periodo)){
                                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                                }
                                            ?>                     
                                        </select>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    
                                    <label for="sltFecha" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="sltFecha" id="sltFecha" title="Ingrese Fecha " type="date"  class="form-control "   placeholder="Ingrese la fecha">
                                    </div>    
                                </div>
                                <div class="form-group"  ">
                                
                                    <label for="txtconsg" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Consignación o Pago:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="txtconsg" id="txtconsg" title="Ingrese Número Consignación " type="text" class="form-control " placeholder="Ingrese Número Consignación o Pago ">
                                    </div>    
                                </div>
                                <div class="form-group"  ">
                                
                                    <label for="txtnum" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Declaración:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="txtnum" id="txtnum"  type="text" class="form-control " value="<?php echo $RD[1] ?>"  readonly>
                                    </div>    
                                </div>
                                <div class="form-group"  align="left">
                                    <label for="sltTipoR" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Tipo Recaudo:</label>
                                        <?php 
                                            $tiporec = "SELECT id_unico, nombre FROM gc_tipo_recaudo WHERE id_unico = 1 ";
                                            $treca = $mysqli->query($tiporec);
                                        ?> 
                                    <div class="col-sm-6 col-md-6 col-lg-6">    
                                        <select  id="sltTipoR" name="sltTipoR" class="form-control select2" title="Seleccione el Tipo de Recaudo" >
                                            <!--<option >Tipo Recaudo</option>-->
                                            <?php 
                                                while($rowTR = mysqli_fetch_row($treca)){
                                                    echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                                }
                                            ?>                     
                                        </select>
                                    </div>    
                                </div>
                            </form>
                        </div>    
                           
                        
                        
                                                 
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Guardar Recaudo"><li class="glyphicon glyphicon-floppy-disk" ></button>    
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                    </div>  
                </div>
            </div>

            <script>
                $('#guardar').click(function(e){
                    var cbanco  = $("#banco").val();
                    var fecha  = $("#sltFecha").val();
                    var pago   = $("#txtconsg").val();
                    var tipo   = $("#sltTipoR").val();
                    if(cbanco.length > 0 && fecha.length > 0 ){
                        $('#formu').submit();
                    }else{
                        if(cbanco == ""){
                            //$("#banco").parents(".col-sm-6").addClass('has-error');
                            $("#s2id_banco").addClass('has-error');
                            //$("label[for='banco']").addClass('has-error');
                        }

                        if(fecha == ""){
                            $("#sltFecha").parents(".col-sm-6").addClass('has-error');
                        }

                        if(pago == ""){
                            $("#txtconsg").parents(".col-sm-6").addClass('has-error');
                        }

                        if(tipo == ""){
                            $("#s2id_sltTipoR").parents(".col-sm-6").addClass('has-error');
                        }
                    }
                });


            </script>

            <script src="js/bootstrap.min.js"></script>
        
            <script type="text/javascript" src="js/select2.js"></script>
            <script type="text/javascript"> 
                $("#banco").select2();
            </script>
           
        </div>