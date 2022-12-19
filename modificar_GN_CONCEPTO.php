<?php 
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
  $id = (($_GET["id"]));

  $sql = "SELECT        c.id_unico,
                        c.codigo,
                        c.descripcion,
                        c.tipofondo,
                        tf.id_unico,
                        tf.nombre,
                        c.compania,
                        t.id_unico,
                        t.razonsocial,
                        c.unidadmedida,
                        um.id_unico,
                        um.nombre,
                        c.clase,
                        cl.id_unico,
                        cl.nombre,
                        c.codigocgr,
                        cc.id_unico,
                        cc.nombre,
                        c.tipoentidadcredito,
                        ter.id_unico,
                        ter.razonsocial,
                        c.conceptorel,
                        cr.id_unico,
                        cr.codigo,
                        c.codigodian,
                        cd.id_unico,
                        cd.nombre,
                        CONCAT(cr.codigo,' - ',LOWER(cr.descripcion)), 
                        c.tipo_interfaz,
                        c.acum_ibc, 
                        c.aplica_liquidacion_final,
                        c.ibr, 
                        c.liquida_retroactivo,
                        c.equivalente_NE,
                        tnn.nombre,
                        c.tipo_novedad_nomina,
                        c.equivalante_sui,
                        c.equivalente_personal_cos 
                FROM gn_concepto c	 
                LEFT JOIN 	gn_tipo_afiliacion tf            ON c.tipofondo          = tf.id_unico
                LEFT JOIN 	gn_unidad_medida_con um     ON c.unidadmedida       = um.id_unico
                LEFT JOIN 	gn_clase_concepto cl        ON c.clase              = cl.id_unico
                LEFT JOIN  	gn_codigo_cgr cc            ON c.codigocgr          = cc.id_unico
                LEFT JOIN   gf_tercero ter              ON c.tipoentidadcredito = ter.id_unico
                LEFT JOIN   gf_tercero t                ON c.compania            = t.id_unico
                LEFT JOIN 	gn_concepto cr              ON c.conceptorel        = cr.id_unico
                LEFT JOIN  	gn_codigo_dian cd           ON c.codigodian         = cd.id_unico
                LEFT JOIN gn_tipo_novedad_nomina tnn  ON tnn.id_unico=c.tipo_novedad_nomina
                where md5(c.id_unico) = '$id'";
  $resultado = $mysqli->query($sql);
  $row = mysqli_fetch_row($resultado);    
    
        $cid      = $row[0];
        $ccod     = $row[1];
        $cdesc    = $row[2];
        $ctip     = $row[3];
        $tfid     = $row[4];
        $tfnom    = $row[5];
        $cter     = $row[6];
        $tid1     = $row[7];
        $ter1     = $row[8];
        $cum      = $row[9];
        $umid     = $row[10];
        $umnom    = $row[11];
        $ccla     = $row[12];
        $clid     = $row[13];
        $clnom    = $row[14];
        $ccodc    = $row[15];
        $codcid   = $row[16];
        $codcnom  = $row[17];
        $cte      = $row[18];
        $tid2     = $row[19];        
        $ter2     = $row[20];
        $ccr      = $row[21];
        $crid     = $row[22];
        $crcod    = $row[23];
        $ccodd    = $row[24];
        $coddid   = $row[25];
        $coddnom  = $row[26];
        $pred     = $row[27]; 
        $tipoI    = $row[28];
        $acumI    = $row[29];
        $nominaE  = $row[33];
        $tipoNE   = $row[34];
        $tipoNEid   = $row[35];
        $equivalenteSUI = $row[36];
        $equivalentePer = $row[37];
require_once './head.php';
?>
<title>Modificar Concepto</title>
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
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Concepto</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -10px" class="client-form">
                        <form name="form" id="form" class="form-horizontal" style="margin-top:-15px; "method="POST"  enctype="multipart/form-data" action="json/modificarConceptoNominaJson.php">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p><input type="hidden" class="hidden" name="idcom" id="idcom" value="<?php echo  $cter; ?>">
                            <div class="form-group" style="margin-top: -15px;">
                                 <label for="txtCodigo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código:</label>
                                 <input type="text" required="required" name="txtCodigo" id="txtCodigo" class="form-control" value="<?php echo $ccod?>" maxlength="100" title="Ingrese el código del concepto" placeholder="Código Concepto">
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                 <label for="txtDescripcion" class="col-sm-5 control-label"><strong class="obligado">*</strong>Descripción:</label>
                                <input type="text" required="required"name="txtDescripcion" id="txtDescripcion" value="<?php echo $cdesc?>" class="form-control" maxlength="100" title="Ingrese la descripción" placeholder="Descripción">
                            </div>
                            <?php             
                            if(empty($ctip))
                                $tf = "SELECT id_unico, LOWER(nombre) FROM gn_tipo_afiliacion";
                            else
                                $tf = "SELECT id_unico, LOWER(nombre) FROM gn_tipo_afiliacion WHERE id_unico != $ctip";
                                                       
                            $tfon = $mysqli->query($tf);
                            ?>
                            <div class="form-group" style="margin-top: -15px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Tipo Afiliación:</label>
                                <select name="sltTipoF" class="select2_single form-control" id="sltTipoF" title="Seleccione tipo de afiliación" style="height: 30px">
                                    <?php if(!empty($tfid)){ ?>
                                        <option value="<?php echo $tfid?>"><?php echo ucwords($tfnom)?></option>
                                        <option value="">-</option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php }
                                    while ($filaTF = mysqli_fetch_row($tfon)) { ?>                   
                                        <option value="<?php echo $filaTF[0];?>"><?php echo ucwords($filaTF[1]);?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                                              
                            if(empty($cum))
                                $um = "SELECT id_unico, LOWER(nombre) FROM gn_unidad_medida_con";
                            else
                                $um = "SELECT id_unico, LOWER(nombre) FROM gn_unidad_medida_con WHERE id_unico != $cum";
                                              
                            $umcon = $mysqli->query($um);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltUnidad" class="control-label col-sm-5"><strong class="obligado">*</strong>Unidad Medida CON:</label>
                                <select name="sltUnidad" required="required" class="select2_single form-control" id="sltUnidad" title="Seleccione unidad medida " style="height: 30px">
                                    <?php if(!empty($umid)){ ?>
                                    <option value="<?php echo $umid?>"><?php echo ucwords($umnom)?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php } 
                                    while ($filaUM = mysqli_fetch_row($umcon)) { ?>                   
                                        <option value="<?php echo $filaUM[0];?>"><?php echo ucwords($filaUM[1]);?></option>
                                    <?php } ?>                                    
                                </select>   
                            </div>
                            <?php                                               
                            if(empty($ccla))
                                $cl = "SELECT id_unico, LOWER(nombre) FROM gn_clase_concepto";
                            else
                            $cl = "SELECT id_unico, LOWER( nombre) FROM gn_clase_concepto WHERE id_unico != $ccla";
                                              
                            $cla = $mysqli->query($cl);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltClase" class="control-label col-sm-5"><strong class="obligado">*</strong>Clase:</label>
                                <select name="sltClase" required="required" class="select2_single form-control" id="sltClase" title="Seleccione Clase Concepto" style="height: 30px">
                                    <?php if(!empty($clid)){ ?>
                                    <option value="<?php echo $clid?>"><?php echo ucwords($clnom)?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php } 
                                    while ($filaCL = mysqli_fetch_row($cla)) { ?>                   
                                    <option value="<?php echo $filaCL[0];?>"><?php echo ucwords($filaCL[1]);?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php                              
                            if(empty($ccodc))
                                $cg = "SELECT id_unico, nombre FROM gn_codigo_cgr";
                            else
                                $cg = "SELECT id_unico, nombre FROM gn_codigo_cgr WHERE id_unico != $ccodc";
                            $cgr = $mysqli->query($cg);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Código CGR:</label>
                                <select name="sltCCGR" class="select2_single form-control" id="sltCCGR" title="Seleccione código CGR" style="height: 30px">
                                    <?php if(!empty($codcid)){ ?>
                                        <option value="<?php echo $codcid?>"><?php echo $codcnom?></option>
                                        <option value="">-</option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php    } 
                                    while ($filaCG = mysqli_fetch_row($cgr)) { ?>                   
                                    <option value="<?php echo $filaCG[0];?>"><?php echo $filaCG[1];?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php                               
                            if(empty($cte))
                                $tere = "SELECT 						
                                            pt.perfil,
                                            pt.tercero,
                                            t.id_unico,
                                            t.razonsocial
                                FROM gf_perfil_tercero pt
                                LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                                WHERE pt.perfil = 12";
                            else
                                $tere = "SELECT 						
                                            pt.perfil,
                                            pt.tercero,
                                            t.id_unico,
                                            t.razonsocial
                                FROM gf_perfil_tercero pt
                                LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                                WHERE pt.perfil = 12 AND t.id_unico != $cte";
                            
                            $terce = $mysqli->query($tere);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Entidad Crédito:</label>
                                <select name="sltEntidadC" class="select2_single form-control" id="sltEntidadC" title="Seleccione entidad crédito" style="height: 30px">
                                    <?php if(!empty($cte)){ ?>
                                        <option value="<?php echo $cte?>"><?php echo ucwords(mb_strtolower($ter2))?></option>
                                        <option value="">-</option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php     } 
                                    while ($filaEC = mysqli_fetch_row($terce)) { ?>
                                        <option value="<?php echo $filaEC[2];?>"><?php echo ucwords(mb_strtolower($filaEC[3])); ?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php                               
                            if(empty($ccodd))
                                $di = "SELECT id_unico, nombre FROM gn_codigo_dian";
                            else
                                $di = "SELECT id_unico, nombre FROM gn_codigo_dian WHERE codigo != $ccodd";
                                              
                            $dian = $mysqli->query($di);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Código DIAN:</label>
                                <select name="sltCCD" class="select2_single form-control" id="sltCCD" title="Seleccione código DIAN" style="height: 30px">                                
                                    <?php if(!empty($coddid)){ ?>
                                    <option value="<?php echo $coddid?>"><?php echo $coddnom?></option>
                                    <option value="">-</option>
                                    <?php }else{ ?>
                                    <option value="">-</option>
                                    <?php    } 
                                    while ($filaCD = mysqli_fetch_row($dian)) { ?>                   
                                    <option value="<?php echo $filaCD[0];?>"><?php echo $filaCD[1];?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <?php 
                            if(empty($crcod))
                            $cre = "SELECT id_unico, CONCAT(codigo,' - ',LOWER(descripcion)) FROM gn_concepto WHERE id_unico != $row[0] AND compania = 1 ORDER BY id_unico ASC ";
                                else
                            $cre = "SELECT id_unico, CONCAT(codigo,' - ',LOWER(descripcion)) FROM gn_concepto WHERE id_unico != $row[0] AND compania = 1 AND codigo != $crcod ORDER BY id_unico ASC";        
                            $crel = $mysqli->query($cre);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado     "></strong>Concepto Relacionado:</label>
                                <select name="sltConcepto" class="select2_single form-control" id="sltConcepto" title="Seleccione Concepto Relativo" style="height: 30px">
                                    <?php  if(!empty($crid)){ ?>
                                    <option value="<?php echo $crid?>"><?php echo ucwords($pred)?></option>
                                    <option value="">-</option>
                                    <?php } else { ?>
                                    <option value="">-</option>
                                    <?php    } 
                                    while ($filaCR = mysqli_fetch_row($crel)) { ?>                   
                                    <option value="<?php echo $filaCR[0]?>"><?php echo ucwords($filaCR[1]);?></option>
                                    <?php } ?>                                     
                                </select>   
                            </div>
                            
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Tipo Interfaz Financiera:</label>
                                <select name="interfaz" class="select2_single form-control" id="interfaz" title="Seleccione Tipo Interfaz Financiera" style="height: 30px">
                                    <?php if(empty($tipoI)) {?>
                                    <option value="">-</option>
                                    <option value="1">Detallada</option>
                                    <option value="2">Acumulada</option>
                                    <?php }  else {
                                        if($tipoI==1){ ?>
                                            <option value="1">Detallada</option>
                                            <option value="">-</option>
                                            <option value="2">Acumulada</option>
                                        <?php }elseif ($tipoI==2) { ?>
                                            <option value="2">Acumulada</option>
                                            <option value="">-</option>
                                            <option value="1">Detallada</option>
                                            
                                        <?php } else { ?>
                                            <option value="">-</option>
                                            <option value="1">Detallada</option>
                                            <option value="2">Acumulada</option>
                                        <?php }
                                       
                                    }?>
                                </select>   
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nominaE" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente Nómina Electrónica:</label>
                                <input type="text" name="nominaE" id="nominaE" class="form-control" maxlength="500" title="Ingrese Codigo Nómina Electrónica" placeholder="Codigo Nómina Electrónica" value="<?php echo $nominaE?>">
                            </div>
                            <?php             
                        
                            $tn = "SELECT id_unico, nombre FROM gn_tipo_novedad_nomina";                 
                            $tipoN = $mysqli->query($tn);
                            ?>
                            <div class="form-group" style="margin-top: -15px">
                                <label class="control-label col-sm-5">Tipo Novedad Nómina Electrónica:</label>
                                <select name="tipoNE" class="select2_single form-control" id="tipoNE" title="Seleccione Tipo Novedad Nómina Electrónica" style="height: 30px">
                                    <?php if(!empty($tipoNEid)){ ?>
                                        <option value="<?php echo $tipoNEid?>"><?php echo $tipoNE;?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php }
                                    while ($filaTN = mysqli_fetch_row($tipoN)) { ?>                   
                                        <option value="<?php echo $filaTN[0];?>"><?php echo $filaTN[1];?></option>
                                    <?php } ?>
                                </select>   
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="equivalenteSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente SUI:</label>
                                <select name="equivalenteSui" class="select2_single form-control" id="equivalenteSui" title="Seleccione Equivalente SUI" style="height: 30px">
                                    <?php if(!empty($equivalenteSUI)){ ?>
                                        <option value="<?php echo $equivalenteSUI?>"><?php echo $equivalenteSUI;?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php }?>
                                    <option value="Sueldo">Sueldo</option>
                                    <option value="Otros Pagos Servicios Personales">Otros Pagos Servicios Personales</option>
                                    <option value="Prestaciones Legales">Prestaciones Legales</option>
                                    <option value="Prestaciones Extralegales">Prestaciones Extralegales</option>
                                    <option value="">-</option>
                                </select>
                            </div>
                             <div class="form-group" style="margin-top: -10px;">
                                <label for="equivalentePer" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente Personal Costos:</label>
                                <select name="equivalentePer" class="select2_single form-control" id="equivalentePer" title="Seleccione Equivalente Personal Costos" style="height: 30px">
                                    <?php if(!empty($equivalentePer)){ ?>
                                        <option value="<?php echo $equivalentePer?>"><?php echo $equivalentePer;?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php }?>
                                    <option value="Asig_Basica">Asig_Basica</option>
                                    <option value="Gastos_Representacion">Gastos_Representacion</option>
                                    <option value="PrimaTS">PrimaTS</option>
                                    <option value="Prima_Gestion">Prima_Gestion</option>
                                    <option value="Prima_Localizacion">Prima_Localizacion</option>
                                    <option value="Prima_Coordinacion">Prima_Coordinacion</option>
                                    <option value="Prima_Riesgo">Prima_Riesgo</option>
                                    <option value="Prima_Extraordinaria">Prima_Extraordinaria</option>
                                    <option value="Prima_Altomando">Prima_Altomando</option>
                                    <option value="Prima_Sub_Alimentacion">Prima_Sub_Alimentacion</option>
                                    <option value="Auxilio_Transporte">Auxilio_Transporte</option>
                                    <option value="Prima_Antiguedad">Prima_Antiguedad</option>
                                    <option value="Prima_Servicios">Prima_Servicios</option>
                                    <option value="Prima_Navidad">Prima_Navidad</option>
                                    <option value="Bon_Servicios">Bon_Servicios</option>
                                    <option value="Bon_Recreacion">Bon_Recreacion</option>
                                    <option value="Prima_vacaciones">Prima_vacaciones</option>
                                    <option value="Otras_Primas">Otras_Primas</option>
                                    <option value="Cesantias">Cesantias</option>
                                    <option value="Intereses_Cesantias">Intereses_Cesantias</option>

                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="es_acumulable" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Es acumulable IBC?:</label>
                                <?php   if ($acumI==1) { ?>
                                            <input  type="radio" name="es_acumulable" id="es_acumulable"  value="1" checked="checked">SI
                                            <input  type="radio" name="es_acumulable" id="es_acumulable" value="2">NO
                                <?php   } else { ?>
                                            <input  type="radio" name="es_acumulable" id="es_acumulable"  value="1">SI
                                            <input  type="radio" name="es_acumulable" id="es_acumulable" value="2" checked="checked">NO
                                <?php   } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="acumulable_lf" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Aplica Liquidación Final?:</label>
                                <?php   if ($row[30]==1) { ?>
                                            <input  type="radio" name="acumulable_lf" id="acumulable_lf"  value="1" checked="checked">SI
                                            <input  type="radio" name="acumulable_lf" id="acumulable_lf" value="2">NO
                                <?php   } else { ?>
                                            <input  type="radio" name="acumulable_lf" id="acumulable_lf"  value="1">SI
                                            <input  type="radio" name="acumulable_lf" id="acumulable_lf" value="2" checked="checked">NO
                                <?php   } ?>
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="acumulable_ibr" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Es acumulable IBR?:</label>
                                <?php   if ($row[31]==1) { ?>
                                            <input  type="radio" name="acumulable_ibr" id="acumulable_ibr"  value="1" checked="checked">SI
                                            <input  type="radio" name="acumulable_ibr" id="acumulable_ibr" value="2">NO
                                <?php   } else { ?>
                                            <input  type="radio" name="acumulable_ibr" id="acumulable_ibr"  value="1">SI
                                            <input  type="radio" name="acumulable_ibr" id="acumulable_ibr" value="2" checked="checked">NO
                                <?php   } ?>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="liquida_retroactivo" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Liquida Retroactivo?:</label>
                                <?php   if ($row[32]==1) { ?>
                                    <input  type="radio" name="liquida_retroactivo" id="liquida_retroactivo"  value="1" checked="checked">SI
                                    <input  type="radio" name="liquida_retroactivo" id="liquida_retroactivo" value="2" >NO
                                <?php   } else { ?>
                                    <input  type="radio" name="liquida_retroactivo" id="liquida_retroactivo"  value="1" >SI
                                    <input  type="radio" name="liquida_retroactivo" id="liquida_retroactivo" value="2" checked="checked">NO
                                <?php   } ?>
                            </div>                                                          
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
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
