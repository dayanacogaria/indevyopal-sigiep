<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
@session_start();
?>
<title>Registrar Concepto</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #txtCodigo-error, #txtDescripcion-error, #sltUnidad-error, #sltClase-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
}
body{
    font-size: 12px;
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
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left" style="margin-top:-21px">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Concepto</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -15px" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" style="margin-top:-15px"action="json/registrarConceptoNominaJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                                            
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="txtCodigo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código:</label>
                                <input type="text" required="required" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="100" title="Ingrese el código del concepto" placeholder="Código Concepto">
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="txtDescripcion" class="col-sm-5 control-label"><strong class="obligado">*</strong>Descripción:</label>
                                <input type="text" required="required" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="100" title="Ingrese la descripción" placeholder="Descripción">
                            </div>
                            <?php 
                            $tf = "SELECT id_unico, nombre FROM gn_tipo_afiliacion";
                            $tfon = $mysqli->query($tf);
                            ?>
                            <div class="form-group" style="margin-top: -15px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Tipo Afiliación:</label>
                                <select name="sltTipoF" class="select2_single form-control" id="sltTipoF" title="Seleccione tipo de Afiliación" style="height: 30px">
                                <option  value="">Tipo Fondo</option>                                
                                    <?php while ($filaTF = mysqli_fetch_row($tfon)) { ?>                   
                                        <option value="<?php echo $filaTF[0];?>"><?php echo $filaTF[1];?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                            $um = "SELECT id_unico, LOWER(nombre) FROM gn_unidad_medida_con";
                            $umcon = $mysqli->query($um);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltUnidad" class="control-label col-sm-5"><strong class="obligado">*</strong>Unidad Medida CON:</label>
                                <select required="required" name="sltUnidad" class="select2_single form-control" id="sltUnidad" title="Seleccione unidad medida" style="height: 30px">
                                    <option  value="">Unidad Medida CON</option>
                                    <?php  while ($filaUM = mysqli_fetch_row($umcon)) { ?>                   
                                        <option value="<?php echo $filaUM[0];?>"><?php echo ucwords($filaUM[1]);?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                            $cl = "SELECT id_unico, LOWER(nombre) FROM gn_clase_concepto";
                            $cla = $mysqli->query($cl);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltClase" class="control-label col-sm-5"> <strong class="obligado">*</strong>Clase:</label>
                                <select required="required" name="sltClase" class="select2_single form-control" id="sltClase" title="Seleccione Clase Concepto" style="height: 30px">
                                <option  value="">Clase Concepto</option>                                
                                    <?php while ($filaCL = mysqli_fetch_row($cla)) { ?>                   
                                        <option value="<?php echo $filaCL[0];?>"><?php echo ucwords($filaCL[1]);?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                            $cg = "SELECT id_unico, nombre FROM gn_codigo_cgr";
                            $cgr = $mysqli->query($cg);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Código CGR:</label>
                                <select name="sltCCGR" class="select2_single form-control" id="sltCCGR" title="Seleccione código CGR" style="height: 30px">
                                <option value="">Código CGR</option>                               
                                    <?php  while ($filaCG = mysqli_fetch_row($cgr)) { ?>                   
                                        <option value="<?php echo $filaCG[0];?>"><?php echo $filaCG[1];?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php  $tere = "SELECT 						
                                            pt.perfil,
                                            pt.tercero,
                                            t.id_unico,
                                            LOWER(t.razonsocial)
                                FROM gf_perfil_tercero pt
                                LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                                WHERE pt.perfil = 12";
                            $terce = $mysqli->query($tere);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Entidad Crédito:</label>
                                <select name="sltEntidadC" class="select2_single form-control" id="sltEntidadC" title="Seleccione entidad crédito" style="height: 30px">
                                <option value="">Entidad Crédito</option>
                                    <?php while ($filaEC = mysqli_fetch_row($terce)) { ?>
                                        <option value="<?php echo $filaEC[2];?>"><?php echo ucwords(($filaEC[3])); ?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                            $di = "SELECT id_unico, nombre FROM gn_codigo_dian";
                            $dian = $mysqli->query($di);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Código DIAN:</label>
                                <select name="sltCCD" class="select2_single form-control" id="sltCCD" title="Seleccione código DIAN" style="height: 30px">
                                <option  value="">Código DIAN</option>
                                    <?php while ($filaCD = mysqli_fetch_row($dian)) { ?>                   
                                    <option value="<?php echo $filaCD[0];?>"><?php echo $filaCD[1];?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                            $cre = "SELECT id_unico, CONCAT(codigo,' - ',LOWER(descripcion)) FROM gn_concepto";
                            $crel = $mysqli->query($cre);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Concepto Relacionado:</label>
                                <select name="sltConcepto" class="select2_single form-control" id="sltConcepto" title="Seleccione Concepto Relativo" style="height: 30px">
                                    <option value="">Concepto Relacionado</option>
                                    <?php while ($filaCR = mysqli_fetch_row($crel)) { ?>                   
                                    <option value="<?php echo $filaCR[0];?>"><?php echo ucwords($filaCR[1]);?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Tipo Interfaz Financiera: </label>
                                <select name="interfaz" class="select2_single form-control" id="interfaz" title="Seleccione Tipo Interfaz Financiera" style="height: 30px">
                                    <option value="">Tipo Interfaz Financiera</option>
                                    <option value="1">Detallada</option>
                                    <option value="2">Acumulada</option>
                                </select>   
                            </div>   
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="nominaE" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente Nómina Electrónica:</label>
                                 <input type="text" name="nominaE" id="nominaE" class="form-control" maxlength="500" title="Ingrese Codigo Nómina Electrónica" placeholder="Codigo Nómina Electrónica">
                            </div>     
                            
                            <?php             
                        
                        $tn = "SELECT id_unico, nombre FROM gn_tipo_novedad_nomina";                 
                        $tipoN = $mysqli->query($tn);
                        ?>
                        <div class="form-group" style="margin-top: -15px">
                            <label class="control-label col-sm-5">Tipo Novedad Nómina Electrónica:</label>
                            <select name="tipoNE" class="select2_single form-control" id="tipoNE" title="Seleccione Tipo Novedad Nómina Electrónica" style="height: 30px">
                            <option value="">Seleccione Tipo Novedad Nómina</option>    
                                <?php 
                                while ($filaTN = mysqli_fetch_row($tipoN)) { ?>                   
                                    <option value="<?php echo $filaTN[0];?>"><?php echo $filaTN[1];?></option>
                                <?php } ?>
                            </select>   
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                                <label for="equivalenteSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente SUI:</label>
                                <select name="equivalenteSui" class="select2_single form-control" id="equivalenteSui" title="Seleccione Equivalente SUI" style="height: 30px">
                                    <option value="">Seleccione Equivalente SUI</option>   
                                    <option value="Sueldo">Sueldo</option>
                                    <option value="Otros Pagos Servicios Personales">Otros Pagos Servicios Personales</option>
                                    <option value="Prestaciones Legales">Prestaciones Legales</option>
                                    <option value="Prestaciones Extralegales">Prestaciones Extralegales</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="es_acumulable" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Es Acumulable IBC?:</label>
                                <input  type="radio" name="es_acumulable" id="es_acumulable"  value="1" >SI
                                <input  type="radio" name="es_acumulable" id="es_acumulable" value="2" checked>NO
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="acumulable_lf" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Aplica Liquidación Final?:</label>
                                <input  type="radio" name="acumulable_lf" id="acumulable_lf"  value="1" >SI
                                <input  type="radio" name="acumulable_lf" id="acumulable_lf" value="2" checked>NO
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="acumulable_ibr" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Es Acumulable IBR?:</label>
                                <input  type="radio" name="acumulable_ibr" id="acumulable_ibr"  value="1" >SI
                                <input  type="radio" name="acumulable_ibr" id="acumulable_ibr" value="2" checked>NO
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="liquida_retroactivo" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Liquida Retroactivo?:</label>
                                <input  type="radio" name="liquida_retroactivo" id="liquida_retroactivo"  value="1" >SI
                                <input  type="radio" name="liquida_retroactivo" id="liquida_retroactivo" value="2" checked>NO
                            </div>    
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>

                          </form>
                      </div>
                    </div>                  
                    <div class="col-sm-8 col-sm-1" style="margin-top:-23px">
                        <table class="tablaC table-condensed text-center" align="center">
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
                                </tr>
                                <tr>                                    
                                    <td>                                    
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_FONDO.php">TIPO FONDO</a>                                    
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>                                    
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_UNIDAD_MEDIDA_CON.php">UNIDAD MEDIDA CON</a>                                    
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>                                    
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_CLASE_CONCEPTO.php">CLASE</a>                                    
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>                                    
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_CODIGO_CGR.php">CODIGO CGR</a>                                    
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_TERCERO_ENTIDAD_FINANCIERA.php">ENTIDAD CREDITO</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>                                        
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_CODIGO_DIAN.php">CODIGO DIAN</a>
                                    </td>
                                </tr>                                                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        <?php require_once './footer.php'; ?>
        <script src="js/select/select2.full.js"></script>
        <script>
         $(document).ready(function() {
             $(".select2_single").select2({
            
            allowClear: true
          });
         
          
        });
        </script>
    </body>
</html>
    