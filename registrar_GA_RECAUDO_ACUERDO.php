<?php
#
@$anno = $_SESSION['anno'];
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
#session_start();
$anno = $_SESSION['anno'];
@$id = $_GET['idE'];
//$_GET['nacuerdo']
@$nacuerdo = 8;
@$idreca = 8;
@$nfac=$_GET['nfactura'];
@$tipo=$_GET['sltTiposelect'];
@$ntipo=$_GET['sltTiposelect'];
@$cont_sel=$_GET['sltContS'];
@$ncont_sel=$_GET['sltContS'];
@$fecha_ac=$_GET['fecha_acuerdo'];
@$disp=$_GET['dis'];
@$array = array (); 

if(empty($nfac)){     
     $a = "none";
} else {
    $a="inline-block";
}
if(empty($nacuerdo)){     
     $a2 = "none";
} else {
    $a2="inline-block";
}
$sql_an = "SELECT anno from gf_parametrizacion_anno where id_unico='$anno'";
    $resultado = $mysqli->query($sql_an);
    $ann = mysqli_fetch_row($resultado); 

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#enviar').click(function(){
          $('#enviar').attr("disabled", true);
        var fch=$('#sltFechaA').val();
        var tp=$('#sltTipo').val();
        var nfac=$('#sltFactura').val();
        var bn=$('#sltBanco').val();
        var vlr=$('#txtValorP').val();
        
        if(fch === '' || tp === '' || nfac === '' || vlr === '' || bn===''){
             $("#myModalcomp").modal('show');
        }else{
            
              window.location='json/registrarRecaudoAcuerdoJSON.php?sltFechaA='+fch+'&sltTipo='+tp+'&sltFactura='+nfac+'&sltBanco='+bn+'&txtValorP='+vlr;
            
        }
        
        return false;
    });         
});    
</script>


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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val(fecAct);
        $("#sltFecha").datepicker({changeMonth: true,}).val(fecAct);
        
        
});
</script>

<script>
function estado(value){

     if(value=="1" ){

            document.getElementById("sltTipo").disabled=false;
            document.getElementById("sltContribuyente").disabled=true;
            

    }else{
            document.getElementById("sltContribuyente").disabled=false;
            document.getElementById("sltTipo").disabled=true;
}
}
</script>
   <title>Registrar Recaudo Acuerdo de Pago</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Recaudo Acuerdo Pago</h2>
                      
                      <div class="client-form contenedorForma" style="margin-top: -7px;">
                          <form id="formid" name="formid" class="form-horizontal" method="POST"  enctype="multipart/form-data" >
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
<!-------------------------------------------------------------------------------------------------------------------- -->
                                <div class="form-group form-inline" style="margin-top:-25px">
                                    <?php 
                                    $vlr_fac="";
                                    if($nfac==""){
                                        $vlfac[0]=0;
                                    }else{
                                    if($ntipo==1){
                                        $vlr_fac="SELECT DISTINCT 
                                                    ((sum(df.valor))-((case when (sum(dpp.valor)is null) then (0) else (sum(dpp.valor)) end)+
                                                    (case when (sum(dppa.valor)is null) then (0) else (sum(dppa.valor)) end))) as saldo_fac 
                                                    FROM ga_factura_acuerdo f
                                                    left join ga_detalle_factura df on f.id_unico = df.factura
                                                    left join gr_detalle_pago_predial dpp on df.iddetallerecaudo=dpp.id_unico
                                                    left join gr_detalle_pago_predial dppa on dppa.id_unico=dpp.iddetalleanulacion
                                                    where f.id_unico='$nfac'";
                                        
                                    }else if($ntipo==2){
                                        $vlr_fac="SELECT DISTINCT 
                                                    ((sum(df.valor))-((case when (sum(dr.valor)is null) then (0) else (sum(dr.valor)) end)+
                                                    (case when (sum(dra.valor)is null) then (0) else (sum(dra.valor)) end))) as saldo_fac 
                                                    FROM ga_factura_acuerdo f
                                                    left join ga_detalle_factura df on f.id_unico = df.factura
                                                    left join gc_detalle_recaudo dr on df.iddetallerecaudo=dr.id_unico
                                                    left join gc_detalle_recaudo dra on dra.id_unico=dr.iddetalleanulacion
                                                    where f.id_unico='$nfac'";
                                    }
                                         $vlf = $mysqli->query($vlr_fac);
                                         $vlfac = mysqli_fetch_row($vlf);
                                    }
                                    ?>
                                    <!--Fecha-->

                                      <!----------Script para invocar Date Picker-->
                                     <script type="text/javascript">
                                      $(document).ready(function() {
                                         $("#datepicker").datepicker();
                                      });
                                      </script>

                                      <label for="sltFechaA" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Fecha:
                                      </label>
                                      <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Acuerdo" 
                                      type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  
                                      placeholder="Ingrese la fecha" 
                                      >  

                                    <!--tipo-->
                                    <?php  
                                    if(empty($ntipo)){
                                        $tip = "SELECT id_unico, nombre FROM ga_tipo_acuerdo";
                                        $t[0]="";
                                        $t[1]="Tipo Acuerdo";
                                    }else{
                                        $tip = "SELECT id_unico, nombre FROM ga_tipo_acuerdo where id_unico!= '$ntipo'";
                                        $tx="SELECT id_unico, nombre FROM ga_tipo_acuerdo where id_unico= '$ntipo'";
                                        $tipoa = $mysqli->query($tx);
                                        $t = mysqli_fetch_row($tipoa);
                                    }                                
                                          $tipon = $mysqli->query($tip);

                                      ?> 
                                      <label for="sltTipo" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Tipo Acuerdo:
                                      </label>
                                      <select   name="sltTipo" id="sltTipo" title="Seleccione Tipo Acuerdo" 
                                                style="width: 140px;height: 30px" class="form-control col-sm-1">
                                          <option value="<?php echo $t[0]; ?>"><?php echo $t[1]; ?></option>

                                         <?php 
                                              while($rowEV = mysqli_fetch_row($tipon))
                                              {
                                                  echo "<option  value=".$rowEV[0].">".$rowEV[1]."</option >";
                                              }

                                          ?>                                                       
                                      </select>
                                    <script>
                                           $("#sltTipo").click(function(){
                                             var tp=$('#sltTipo').val();
                                             var fch=$('#sltFechaA').val();
                                             window.location='registrar_GA_RECAUDO_ACUERDO.php?sltTiposelect='+tp+'&fecha_acuerdo='+fch;
                                           });


                                    </script>
                                    <!--numero de factura-->
                                    <?php  
                                    if(empty($ntipo)){
                                        
                                        $t[0]="";
                                        $t[1]="Factura";
                                        $vls="";
                                    }else{
                                        $tip="";
                                        if($ntipo==1){
                                            
                                            $tip = "SELECT DISTINCT f.id_unico,
                                                f.numero,
                                                ((sum(df.valor))-((case when (sum(dpp.valor)is null) then (0) else (sum(dpp.valor)) end)+
                                                    (case when (sum(dppa.valor)is null) then (0) else (sum(dppa.valor)) end))) as saldo_fac,
                                                    f.fecha_ven
                                                FROM ga_factura_acuerdo f
                                                left join ga_detalle_factura df on f.id_unico = df.factura
                                                left join gr_detalle_pago_predial dpp on df.iddetallerecaudo=dpp.id_unico
                                                left join gr_detalle_pago_predial dppa on dppa.id_unico=dpp.iddetalleanulacion
                                                left join ga_detalle_acuerdo da on df.detalleacuerdo = da.id_unico
                                                left join ga_acuerdo a on da.acuerdo = a.id_unico
                                                where a.tipo='$ntipo'  and f.id_unico='$nfac'
                                                and (SELECT DISTINCT  
                                                     ((sum(df2.valor))-((case when (sum(dpp2.valor)is null) then (0) else (sum(dpp2.valor)) end)+
                                                     (case when (sum(dppa2.valor)is null) then (0) else (sum(dppa2.valor)) end))) as saldo_fac
                                                     FROM  ga_detalle_factura df2
                                                     left join gr_detalle_pago_predial dpp2 on df2.iddetallerecaudo=dpp2.id_unico
                                                     left join gr_detalle_pago_predial dppa2 on dppa2.id_unico=dpp2.iddetalleanulacion
                                                     where df2.id_unico=df.id_unico) !=0 group by f.numero";
                                            
                                            $tip2 = "SELECT DISTINCT f.id_unico,
                                                f.numero,
                                                ((sum(df.valor))-((case when (sum(dpp.valor)is null) then (0) else (sum(dpp.valor)) end)+
                                                    (case when (sum(dppa.valor)is null) then (0) else (sum(dppa.valor)) end))) as saldo_fac,
                                                    f.fecha_ven
                                                FROM ga_factura_acuerdo f
                                                left join ga_detalle_factura df on f.id_unico = df.factura
                                                left join gr_detalle_pago_predial dpp on df.iddetallerecaudo=dpp.id_unico
                                                left join gr_detalle_pago_predial dppa on dppa.id_unico=dpp.iddetalleanulacion
                                                left join ga_detalle_acuerdo da on df.detalleacuerdo = da.id_unico
                                                left join ga_acuerdo a on da.acuerdo = a.id_unico
                                                where a.tipo='$ntipo' and f.id_unico!='$nfac'
                                                and (SELECT DISTINCT  
                                                     ((sum(df2.valor))-((case when (sum(dpp2.valor)is null) then (0) else (sum(dpp2.valor)) end)+
                                                     (case when (sum(dppa2.valor)is null) then (0) else (sum(dppa2.valor)) end))) as saldo_fac
                                                     FROM  ga_detalle_factura df2
                                                     left join gr_detalle_pago_predial dpp2 on df2.iddetallerecaudo=dpp2.id_unico
                                                     left join gr_detalle_pago_predial dppa2 on dppa2.id_unico=dpp2.iddetalleanulacion
                                                     where df2.id_unico=df.id_unico) !=0 group by f.numero";
                                            
                                        }else if($ntipo==2){
                                            /*
                                            SELECT DISTINCT 
                                                    ((sum(df.valor))-((case when (sum(dr.valor)is null) then (0) else (sum(dr.valor)) end)-
                                                    (case when (sum(dra.valor)is null) then (0) else (sum(dra.valor)) end))) as saldo_fac 
                                                    FROM ga_factura_acuerdo f
                                                    left join ga_detalle_factura df on f.id_unico = df.factura
                                                    left join gc_detalle_recaudo dr on df.iddetallerecaudo=dr.id_unico
                                                    left join gc_detalle_recaudo dra on dra.id_unico=dr.iddetalleanulacion
                                                        where f.id_unico='$nfac'
                                             *             
                                             *                                  */
                                            
                                            $tip="SELECT DISTINCT f.id_unico,f.numero,
                                                    ((sum(df.valor))-((case when (sum(dr.valor)is null) then (0) else (sum(dr.valor)) end)+
                                                    (case when (sum(dra.valor)is null) then (0) else (sum(dra.valor)) end))) as saldo_fac,
                                                    f.fecha_ven

                                                    FROM ga_factura_acuerdo f
                                                     left join ga_detalle_factura df on f.id_unico = df.factura
                                                     left join gc_detalle_recaudo dr on df.iddetallerecaudo=dr.id_unico
                                                     left join gc_detalle_recaudo dra on dra.id_unico=dr.iddetalleanulacion
                                                     left join ga_detalle_acuerdo da on df.detalleacuerdo = da.id_unico
                                                     left join ga_acuerdo a on da.acuerdo = a.id_unico
                                                     where a.tipo='$ntipo' and f.id_unico='$nfac'

                                                     and (SELECT DISTINCT  
                                                    ((sum(df2.valor))-((case when (sum(dr2.valor)is null) then (0) else (sum(dr2.valor)) end)+
                                                    (case when (sum(dra2.valor)is null) then (0) else (sum(dra2.valor)) end))) as saldo_fac

                                                    FROM  ga_detalle_factura df2
                                                     left join gc_detalle_recaudo dr2 on df2.iddetallerecaudo=dr2.id_unico
                                                     left join gc_detalle_recaudo dra2 on dra2.id_unico=dr2.iddetalleanulacion

                                                     where df2.id_unico=df.id_unico) !=0 group by f.numero";                                            
                                            
                                            $tip2 ="SELECT DISTINCT f.id_unico,f.numero,
                                                    ((sum(df.valor))-((case when (sum(dr.valor)is null) then (0) else (sum(dr.valor)) end)+
                                                    (case when (sum(dra.valor)is null) then (0) else (sum(dra.valor)) end))) as saldo_fac,
                                                    f.fecha_ven

                                                    FROM ga_factura_acuerdo f
                                                     left join ga_detalle_factura df on f.id_unico = df.factura
                                                     left join gc_detalle_recaudo dr on df.iddetallerecaudo=dr.id_unico
                                                     left join gc_detalle_recaudo dra on dra.id_unico=dr.iddetalleanulacion
                                                     left join ga_detalle_acuerdo da on df.detalleacuerdo = da.id_unico
                                                     left join ga_acuerdo a on da.acuerdo = a.id_unico
                                                     where a.tipo='$ntipo' and f.id_unico!='$nfac' 

                                                     and (SELECT DISTINCT  
                                                    ((sum(df2.valor))-((case when (sum(dr2.valor)is null) then (0) else (sum(dr2.valor)) end)+
                                                    (case when (sum(dra2.valor)is null) then (0) else (sum(dra2.valor)) end))) as saldo_fac

                                                    FROM  ga_detalle_factura df2
                                                     left join gc_detalle_recaudo dr2 on df2.iddetallerecaudo=dr2.id_unico
                                                     left join gc_detalle_recaudo dra2 on dra2.id_unico=dr2.iddetalleanulacion

                                                     where df2.id_unico=df.id_unico) !=0 group by f.numero";
                                        }
                                        $tipoa = $mysqli->query($tip);
                                        $t = mysqli_fetch_row($tipoa);
                                        $vlsal=number_format($t[2], 2, '.', ',');
                                        $vls=' -Saldo Factura: '.$vlsal.' -Fecha factura: '.$t[3];
                                        if(empty($nfac)){
                                            $t[0]="";
                                            $t[1]="Factura";
                                            $vls="";
                                        }else{
                                           
                                        }
                                         $tipon = $mysqli->query($tip2);
                                        
                                    }    
                                      ?> 
                                      <label for="sltFactura" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Factura Acuerdo:
                                      </label>
                                      <select   name="sltFactura" id="sltFactura" title="Seleccione Factura Acuerdo" 
                                                style="width: 140px;height: 30px" class="form-control col-sm-1">
                                          <option value="<?php echo $t[0]; ?>"><?php echo $t[1].$vls; ?></option>

                                         <?php 
                                              while($rowEV = mysqli_fetch_row($tipon))
                                              {
                                                  $vlsal=number_format($rowEV[2], 2, '.', ',');
                                                  $vls=' -Saldo Factura: '.$vlsal.' -Fecha factura: '.$rowEV[3];
                                                  echo "<option  value=".$rowEV[0].">".$rowEV[1].$vls."</option >";
                                              }

                                          ?>                                                       
                                      </select>
                                    <script>
                                           $("#sltFactura").click(function(){
                                             var tp=$('#sltTipo').val();
                                             var fch=$('#sltFechaA').val();
                                             var fac=$('#sltFactura').val();
                                             
                                             window.location='registrar_GA_RECAUDO_ACUERDO.php?sltTiposelect='+tp+'&fecha_acuerdo='+fch+'&nfactura='+fac;
                                           });


                                    </script>
                                   
                                </div>
                      <div class="form-group form-inline" >
                          
                          <!--Banco-->
                                    <?php  
                                    
                                        $tip = "SELECT DISTINCT 
                                                cb.id_unico,concat(cb.numerocuenta,' - ',t.razonsocial)as b 
                                                FROM gf_cuenta_bancaria cb
                                                left join gf_tercero t on t.id_unico=cb.banco 
                                                where cb.parametrizacionanno!='$ann[0]'";
                                        $tx="SELECT DISTINCT 
                                                cb.id_unico,concat(cb.numerocuenta,' - ',t.razonsocial)as b 
                                                FROM gf_cuenta_bancaria cb
                                                left join gf_tercero t on t.id_unico=cb.banco 
                                                where cb.parametrizacionanno='$anno'";
                                        $tipoa = $mysqli->query($tx);
                                        $t = mysqli_fetch_row($tipoa);
                                    
                                        
                                        $tipon = $mysqli->query($tx);

                                      ?> 
                                      <label for="sltBanco" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Banco:
                                      </label>
                                      <select   name="sltBanco" id="sltBanco" title="Seleccione Banco" 
                                                style="width: 430px;height: 30px" class="form-control col-sm-1">
                                          <option value="<?php echo $t[0]; ?>"><?php echo $t[1]; ?></option>

                                         <?php 
                                              while($rowEV = mysqli_fetch_row($tipon))
                                              {
                                                  echo "<option  value=".$rowEV[0].">".$rowEV[1]."</option >";
                                              }

                                          ?>                                                       
                                      </select>
                          
                          <!--Valor Pago-->
                          
                          <label for="txtValorP" class="col-sm-2 control-label">
                            	<strong class="obligado">*</strong>Valor Pago:
                          </label>
                          
                          <input  name="txtValorP2" id="txtValorP2" title="Ingrese Valor Pago" type="hidden" 
                                 value="<?php echo $vlfac[0] ?>">
                          
                          <input  name="txtValorP" id="txtValorP" title="Ingrese Valor Pago" type="text" 
                                  style="width: 140px;height: 30px" class="form-control col-sm-1" 
                                  placeholder="Valor Pago" value="<?php echo $vlfac[0] ?>">
                          
                         
                          </div> 
                          <div class="form-group form-inline" style="margin-top:-5px">                            
                          
                           <!-- <label for="No" class="col-sm-2 control-label"></label>-->
                            <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" 
                              style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 800px ; ">
                                <li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                        </div>
                          <!-- ----------------------------------------------------------------------  -->
                         
<!--------------------------------------------------------------------------------------------------- -->                              
                      
                        
                    </form>
                           
                          <div class="col-sm-6 text-right" align="center" style="display:<?php echo $a?>">
                              <label class="control-label valores text-center" ><strong>DETALLE SALDO FACTURA</strong></label>
                          </div>
                    <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a?>">
                            <?php require_once './menu.php'; 
                            $tab_fd= '';
                            
                            
                            ?>
                    <!-- <div class="col-sm-12 text-left" style="display:<?php echo $a?>">

                    class="col-sm-8 col-md-8 col-lg-8text-left"-->
                                <div class=" col-sm-5 col-md-5 col-lg-5 table-responsive" style="margin-left: 50px; margin-right: 50px;margin-top:0px;width: 1000px;">
                                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                        <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="60%">
                                            <thead>
                                                <tr>
                                                    <td style="display: none;">Identificador</td>
                                                    <td width="7%" class="cabeza"></td>                                        
                                                    <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                                    <td class="cabeza"><strong>Empleado</strong></td>
                                                    -->
                                                    <td class="cabeza"><strong>Cuota</strong></td>
                                                    <?php 
                                                    if($tipo==1)
                                                    {//$nacuerdo
                                                        $sql = "SELECT DISTINCT 
                                                                c.nombre, da.concepto_deuda,cp.anno 
                                                                FROM ga_detalle_factura df 
                                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo  
                                                                LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
                                                                LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
                                                                WHERE df.factura = '$nfac' ORDER BY da.concepto_deuda ";

                                                    }else if($tipo==2){
                                                       $sql = "SELECT DISTINCT 
                                                                cc.nom_inf,da.concepto_deuda,cc.id_unico  
                                                                FROM ga_detalle_factura df 
                                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                                                                LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                                WHERE df.factura = '$nfac' ORDER BY da.concepto_deuda";
                                                    }else{
                                                        $sql ="";
                                                    }
                                                     $resultado = $mysqli->query($sql);
                                                     while ($row = mysqli_fetch_row($resultado)) {    
                                                          echo "<td class='cabeza'><strong>$row[0] - $row[2]</strong></td>";
                                                     }
                                                    ?>
                                                    
                                                    <td class="cabeza"><strong>Total Cuota</strong></td>
                                                    
                                                </tr>
                                                <tr>
                                                    <th class="cabeza" style="display: none;">Identificador</th>
                                                    <th class="cabeza" width="7%"></th>
                                                    <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                                    <th class="cabeza">Empleado</th>
                                                    -->
                                                    <th class="cabeza">Cuota</th>
                                                    
                                                    <?php 
                                                    if($tipo==1)
                                                    {//$nacuerdo
                                                        $sql = "SELECT DISTINCT 
                                                                c.nombre, cp.id_unico,cp.anno 
                                                                FROM ga_detalle_factura df 
                                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                                                                LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
                                                                LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
                                                                WHERE df.factura = '$nfac' ORDER BY da.concepto_deuda ";

                                                    }else if($tipo==2){
                                                       $sql = "SELECT DISTINCT 
                                                                cc.nom_inf,cc.id_unico,cc.id_unico  
                                                                FROM ga_detalle_factura df 
                                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo 
                                                                LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                                WHERE df.factura = '$nfac' ORDER BY da.concepto_deuda";
                                                    }else{
                                                        $sql ="";
                                                    }
                                                     $resultado = $mysqli->query($sql);
                                                     while ($row = mysqli_fetch_row($resultado)) {    
                                                          echo "<th class='cabeza'>$row[0] - $row[2]</th>";
                                                     }
                                                    ?>
                                                    
                                                    <th class="cabeza">Total Cuota</th>
                                                    
                                                    
                                                </tr>
                                            </thead>    
                                            <tbody>
                                                <?php 
                                                if($tipo==1)
                                                {
                                                 $sql1="SELECT DISTINCT 
                                                                da.nrocuota,da.fecha
                                                                FROM ga_detalle_factura df 
                                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                                                                LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
                                                                LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
                                                                WHERE df.factura = '$nfac' ORDER BY da.concepto_deuda";
                                                } else if($tipo==2){
                                                  $sql1 = "SELECT DISTINCT 
                                                                da.nrocuota,da.fecha
                                                                FROM ga_detalle_factura df 
                                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                                                                LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda  
                                                                WHERE df.factura = '$nfac' ORDER BY da.concepto_deuda";
                                                }else{
                                                  $sql1 ="";
                                                }
                                                  $re = $mysqli->query($sql1);
                                                while ($rowC = mysqli_fetch_row($re)) {  
                                                    $tota=0;
                                                        $ncta   = $rowC[0];
                                                        
                                                        ?>
                                                 <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        
                                                    </td>                                        
                                                    <!-- Actualización 24 / 02 16:47 No es necesario mostrar el nombre del empleado
                                                    <td class="campos"><?php #echo $ternom?></td>                
                                                    -->                             
                                                 
                                                    <td class="campos text-center"><?php echo $ncta;?></td>                   
                                                      
                                                <?php
                                                        if($tipo==1)
                                                        {//$nacuerdo
                                                            $sql = "SELECT DISTINCT
                                                                    c.nombre,
                                                                    (df.valor -
                                                                    ((CASE WHEN dr.valor IS NULL THEN(0) ELSE dr.valor END) +
                                                                     (CASE WHEN dra.valor IS NULL THEN(0) ELSE dra.valor END))) as saldo_fac,
                                                                      cp.id_unico,
                                                                    da.concepto_deuda

                                                                    FROM ga_detalle_factura df 
                                                                    LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                                                                    left join gr_detalle_pago_predial dr on df.iddetallerecaudo=dr.id_unico
                                                                    left join gr_detalle_pago_predial dra on dra.id_unico=dr.iddetalleanulacion
                                                                    LEFT JOIN  gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda
                                                                    LEFT JOIN  gr_concepto c ON c.id_unico = cp.id_concepto
                                                                    LEFT JOIN  gr_detalle_pago_predial dpp on dpp.pago=df.iddetallerecaudo
                                                                    WHERE  df.factura = '$nfac' and da.nrocuota='$ncta'
                                                                      group by cp.id_unico
                                                                    order by da.nrocuota,da.concepto_deuda asc";

                                                        }else if($tipo==2){
                                                           $sql = "SELECT DISTINCT
                                                                    cc.nom_inf,
                                                                    (df.valor -
                                                                    ((CASE WHEN dr2.valor IS NULL THEN(0) ELSE dr2.valor END) +
                                                                     (CASE WHEN dra.valor IS NULL THEN(0) ELSE dra.valor END))) as saldo_fac,
                                                                      cc.id_unico,
                                                                    da.concepto_deuda

                                                                    FROM ga_detalle_factura df 
                                                                    LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
                                                                    left join gc_detalle_recaudo dr2 on df.iddetallerecaudo=dr2.id_unico
                                                                    left join gc_detalle_recaudo dra on dra.id_unico=dr2.iddetalleanulacion
                                                                    LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                                    LEFT JOIN  gc_detalle_recaudo dr on dr.recaudo=df.iddetallerecaudo
                                                                    WHERE  df.factura = '$nfac' and da.nrocuota='$ncta'
                                                                      group by cc.id_unico
                                                                    order by da.nrocuota,da.concepto_deuda asc";
                                                        }else{
                                                            $sql ="";
                                                        }
                                                         $result = $mysqli->query($sql);
                                                       
                                                        while ($rowV = mysqli_fetch_row($result)) {  
                                                            $vcuota=$rowV[1];
                                                            $tota=$tota+$vcuota;
                                                            ?>
                                                            <td class="campos text-right"><?php echo number_format($vcuota, 2, '.', ',');?></td>  
                                                            <?php
                                                            
                                                            
                                                        }
                                                        ?>
                                                        
                                                    <td class="campos text-right"><?php echo number_format($tota, 2, '.', ',');?></td>  
                                                    
                                                 
                                                </tr> 
                                                <?php }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                        <div class="col-sm-offset-7  col-sm-7 text-left" style= " margin-top:5px;margin-bottom:-10px;display:<?php echo $a?> " >
                            <div class="col-sm-3">
                                <div class="form-group"  align="left">                                    
                                    <label class="control-label">
                                        <strong>Total Factura:</strong>
                                    </label>                                
                                </div>
                            </div>                        
                            <div class="col-sm-2 text-right" align="left">
                                <label id="txtValor" class="control-label valores" title="Suma Deuda"><?php echo number_format($vlfac[0], 2, '.', ',') ?></label>                                                   
                            </div>                        
                            
                    </div>
                                </div>  
                          
                  </div>

                   
<!---------------------------------------------------------------------------------------------------->                        
    
        <!-- </div> -->   
                
            </div>
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Vinculación Retiro?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModalcomp" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Asegurese que los campos obligatorios esten diligenciados.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>     
       
       
       
       
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarVinculacionRetiroJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
  </script>

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
<script type="text/javascript">
      function recargar()
      {
        window.location.reload();     
      }
  </script>     
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){ 
         reload();
        //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
        //window.location='../listar_GN_ACCIDENTE.php';
        window.history.go(-1);        
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        window.history.go(-1);
      });    
  </script>
</div>
<script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaA').value;
        var fechafi= document.getElementById('sltFecha').value;
          var fi = document.getElementById("sltFecha");
        fi.disabled=false;
      
       
            $( "#sltFecha" ).datepicker( "destroy" );
            $( "#sltFecha" ).datepicker({ changeMonth: true, minDate: fechain});
     
}
</script>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltVinculacion").select2();
</script>

<script type="text/javascript" src="js/select2.js"> </script>
        <script type="text/javascript"> 
         $("#sltCausa").select2();
</script>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltTipo").select2();
         $("#sltContribuyente").select2();
         $("#sltFactura").select2();
         $("#sltBanco").select2();
</script>
</body>
</html>