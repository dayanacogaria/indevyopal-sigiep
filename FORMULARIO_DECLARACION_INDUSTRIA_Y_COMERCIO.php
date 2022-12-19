
<?php

require_once './Conexion/conexion.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
  <link rel="icon" href="img/AAA.ico" />
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-ui.js" type="text/javascript"></script>
  <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
 <link rel="stylesheet" href="css/normalize.css"/>
  <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
  <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
  
  <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
  <link rel="stylesheet" href="css/custom.css"/>
  <style>
        /*Estilos tabla*/
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        /*Campos dinamicos*/
        .campoD:focus {
            border-color: #66afe9;
            outline: 0;            
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
        }
        .campoD:hover{
            cursor: pointer;
        }
        /*Campos dinamicos label*/
        .valorLabel{
            font-size: 10px;
        }
        .valorLabel:hover{
            cursor: pointer;
            color:#1155CC;
        }
        /*td de la tabla*/
        .campos{
            padding: 0px;
            font-size: 10px
        }
        /*cuerpo*/
        body{
            font-size: 10px;
            font-family: Arial;
        }

        .client-form input[type="text"]{
            width: 100%;
        }
        .client-form select{
            width: 100%;
        }

        .client-form input[type="file"]{
            width: 100%;
        }

        </style>  

    <script src="js/md5.js"></script>



        <title>Modificar Contribuyente</title>


        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        
        <style>
        /*Estilos tabla*/
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        /*Campos dinamicos*/
        .campoD:focus {
            border-color: #66afe9;
            outline: 0;            
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
        }
        .campoD:hover{
            cursor: pointer;
        }
        /*Campos dinamicos label*/
        .valorLabel{
            font-size: 10px;
        }
        .valorLabel:hover{
            cursor: pointer;
            color:#1155CC;
        }
        /*td de la tabla*/
        .campos{
            padding: 0px;
            font-size: 10px
        }
        /*cuerpo*/
        body{
            font-size: 10px;
            font-family: Arial;
        }

        .client-form input[type="text"]{
            width: 100%;
        }
        .client-form select{
            width: 100%;
        }

        .client-form input[type="file"]{
            width: 100%;
        }

        </style>  

    </head>
    
<style>
  input[type="text"]{
    width: 100%;
  }
</style>
<script src="js/md5.js"></script>


<!--Scrip que envia los datos para el registro de la declaracion-->
<script type="text/javascript">
 
    function registrarDeclaracion(inputsValue){

        //VALORES DECLARACION

        var contribuyente=$("#contribuyente3").val();

        var pe=document.getElementById("periodo").selectedIndex;
        var periodo=document.getElementsByTagName("option")[pe].value;
        
        var vig=document.getElementById("vigenciaFiscal").selectedIndex;
        var vigenciaFiscal=document.getElementsByTagName("option")[vig].value;
        // var tipoPeriodicidad=$("#tipoPeriodicidad").val();
        //  var tipoDeclaracion=$("#tipoDeclaracion").val();

        //VALORES DECLARACION INGRESO

        //VALORES DETALLES DECLARACION
        //tomo el valor de inputs value, que nos da el valor de los inputs para iterar y recoger sus valores

        var inputsValue=inputsValue;  //valor numero de fila maxima
        var detallesDeclaracion=new Array();

        for($i = 1; $i <= inputsValue; $i++){

             var ncampo1 = "#idInputCC"+$i;
             var campo1  = $(ncampo1).val();

             /*var ncampo2 = "#idInputD"+$i;
             var campo2  = $(ncampo2).val();*/

             var ncampo3 = "#idInputV"+$i;
             var campo3  = $(ncampo3).val();

            var detalleDeclarcion = [campo1,campo3];

            detallesDeclaracion.push(detalleDeclarcion);
           

         }
            
         //REGISTRO DE LA DECLARACION CON INGRESOS Y DETALLES
         var form_data={                            
                  c:contribuyente,
                  p:periodo,
                  vf:vigenciaFiscal,
                  dsc:detallesDeclaracion
         };


         $.ajax({

                    type:"post",
                    url:"jsonComercio/registrarDeclaracionJson.php",
                    data:form_data,


                    success:function(data){

                        var  result = JSON.parse(data);
                          //  console.log(data);

                          if(result==true){
                             $("#rc").modal('show');
                          }else{
                               $("#nr").modal('show');
                          }


                    }


        });


    }
</script>

<script>
  $(document).ready(function(){

        //evento enter de numero identificacion
            $('#numero').on('keypress', function(e) {
                var code = e.keyCode || e.which;
                if(code==13){


                    var identificacion= $('input[name=identificacion]:checked', '#form').val();
                    var numeroidentificacion=$("#numero").val();

                    if(numeroidentificacion!=""){

                         //validacion contribuyente

                            var form_data={                            
                              numero:numeroidentificacion,
                              identific:identificacion 
                            };
                         $.ajax({

                                    type:"post",
                                    url:"jsonComercio/formularioDeclaracionIndustriaYComercioJson.php",
                                   // data:"numero="+numeroidentificacion, 
                                    data:form_data,
                                    dataType:"json",

                                    success:function(data){

                                          result = JSON.parse(data);
                                          if(result==true){

                                             document.location='FORMULARIO_DECLARACION_INDUSTRIA_Y_COMERCIO.php?i='+md5(identificacion)+'&n='+md5(numeroidentificacion);


                                          }else{
                                              $("#NoModal").modal('show');
                                              //document.location='FORMULARIO_DECLARACION_INDUSTRIA_Y_COMERCIO.php';

                                          }


                                    }


                                });


                    }



                }


            });



  });   

</script>
<script type="text/javascript">


  var cr1=2;
  var ci=0;
  function addRow(){

                    var table = document.getElementById("tablab");
                    var row = table.insertRow(cr1);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    var cell6 = row.insertCell(5);
                    var cell7 = row.insertCell(6);
                    var cell8 = row.insertCell(7);

                    cell2.setAttribute("onclick","addInput(this,ci)");
                    cell3.setAttribute("onclick","addInput(this,ci)");
                    cell4.setAttribute("onclick","addInput(this,ci)");
                    cell5.setAttribute("onclick","addInput(this,ci)");
                    cell6.setAttribute("onclick","addInput(this,ci)");
                    cell7.setAttribute("onclick","addInput(this,ci)");
                    cell8.setAttribute("onclick","addInput(this,ci)");

                    cell2.setAttribute("class","tLine");
                    cell3.setAttribute("class","tLine");
                    cell4.setAttribute("class","tLine");
                    cell5.setAttribute("class","tLine");
                    cell6.setAttribute("class","tLine");
                    cell7.setAttribute("class","tLine");
                    cell8.setAttribute("class","tLine");

                   


                    cr1++;
                   // document.getElementById("p1").innerHTML = "New text!";

  }

   
function closeInput(elm) {
    var td = elm.parentNode;
    var value = elm.value;
    td.removeChild(elm);
    td.innerHTML = value;
}

/*function addInput(elm,i) {
    if (elm.getElementsByTagName('input').length > 0) return;

    var value = elm.innerHTML;
    elm.innerHTML = '';

    var input = document.createElement('input');
    input.setAttribute('type', 'text');
    input.setAttribute('value', value);


    input.setAttribute('onBlur', 'closeInput(this)');
    input.setAttribute('name', i);

    elm.appendChild(input);
    input.focus();
    //alert(value);
}*/




</script>

<body >   

<div class="container-fluid text-left">
   <div class="row content">


                    
        <div class="col-sm-12" style="margin-top:0%;    margin-bottom: 1.2%;">

<!--Formulario de inicio-->
<center><h1>FORMULARIO DECLARACIÓN DE INDUSTRIA Y COMERCIO Y AVISOS Y TABLEROS MUNICIPIO DE DUITAMA (NIT 891855138-1)</h1></center><br>

       <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarContribuyenteJson.php" >
            

                                          
                    <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                          <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">

                                <div class="col-sm-1 col-md-1 col-lg-1">
                                </div>
    
                                <label for="sltctai" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong style="color:#03C1FB;">*</strong>Periodo</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="periodo" id="periodo" required  class="form-control select2" title="Seleccione Tercero">


                                     <?php 

                                        $consulta=" SELECT annoc.id_unico,annoc.vigencia
                                                        FROM gc_anno_comercial annoc
                                                        ORDER BY vigencia ASC";
                                       
                                                    $rr=$mysqli->query($consulta); 
                                                    
                                                    ?>

                                        <?php while($fa=mysqli_fetch_row($rr)){ ?>
                                                    <option value="<?php echo $fa[0]?>"><?php echo $fa[1] ?></option>
                                         <?php } ?>                          

                                  </select>
                                </div>


                                <label for="sltctai" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia Fiscal:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="vigenciaFiscal" id="vigenciaFiscal" required  class="form-control select2" title="Seleccione Tercero">


                                     <?php 

                                        $consulta=" SELECT annoc.id_unico,annoc.vigencia
                                                        FROM gc_anno_comercial annoc
                                                        ORDER BY vigencia DESC";
                                       
                                                    $rr=$mysqli->query($consulta); 
                                                    
                                                    ?>

                                        <?php while($fa=mysqli_fetch_row($rr)){ ?>
                                                    <option value="<?php echo $fa[0]?>"><?php echo $fa[1] ?></option>
                                         <?php } ?>                          

                                  </select>
                                </div>


                    

                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                       Régimen:
                                </label>

                                <div class="col-sm-1 col-md-1 col-lg-1">
                                   <input   type="radio" name="regimen" value="comun">Común <br>  
                                <input    type="radio" name="regimen" value="simplificado"> Simplificado 
                                </div>
                                     
                     </div>
 
              </form>
   <a href="listar_GC_CONTRIBUYENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


          <h5 id="forma-titulo3a" align="center" style="width:97%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: #0e315a; border-radius: 5px;">.</h5>
          <!---->


             <div class="client-form contenedorForma" style="margin-top:-0.3%;">
 <?php if( isset($_GET['i']) && isset($_GET['n']) ){ ?>


<?php
                        //consulta contribuyente
                        $i=$_GET['i'];
                        $n=$_GET['n'];

                        $sql="SELECT c.id_unico,
                        IF(CONCAT_WS(' ',
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
                        t.apellidodos)) AS nt, 
                        c.tercero,
                        t.numeroidentificacion,
                        ti.nombre
                        FROM gc_contribuyente c 
                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico=t.tipoidentificacion
                        WHERE md5(t.numeroidentificacion)='$n' AND md5(ti.nombre)='$i'";


                        //contribuyente
                        $resultado=$mysqli->query($sql);
                        $rowC=mysqli_fetch_row($resultado);

                        if($resultado->num_rows > 0){

                            $idTercero=$rowC[2];
                            //direcciones
                            $sqld="SELECT direccion FROM gf_direccion WHERE tercero=$idTercero LIMIT 1";
                            $resultadod=$mysqli->query($sqld);
                            $rowd=mysqli_fetch_row($resultadod);    

                            //telefonos
                            $sqlt="SELECT valor FROM gf_telefono WHERE tercero=$idTercero LIMIT 1";
                            $resultadot=$mysqli->query($sqlt);
                            $rowt=mysqli_fetch_row($resultadot);  ?> 
<!--formulario de consulta 2-->
       <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarContribuyenteJson.php" >
            

                                          
                     <div class="form-group" style="margin-top: 1.2%; margin-left: 1%;">

                          <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">


                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                        1. Identificación:
                                </label>

                                  <div class="col-sm-1 col-md-1 col-lg-1">
                                      <?php if ($rowC[4]=="Nit") {  ?>

                                         <input   required="" type="radio" name="identificacion" value="Nit" checked>Nit <br> 
                                         <input    required="" type="radio" name="identificacion" value="Cédula Ciudadanía"> cc <br>

                                      <?php }else{ ?>

                                         <input   required="" type="radio" name="identificacion" value="Nit">Nit <br> 
                                         <input    required="" type="radio" name="identificacion" value="Cédula Ciudadanía" checked> cc <br>

                                      <?php } ?>

                                  </div>


                                <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" >
                                        Número:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <!--<input type="search" id="myInput" onsearch="searchContribuyente()">-->
                                    <input  type="text" name="numero"  id="numero"  class="form-control" title="Número" onkeypress="return txtValida(event,'num')"  maxlength="15" value="<?php echo $rowC[3] ?>">
                                </div>

                                <label for="cante" class="control-label col-sm-1 col-md-1 col-lg-1">
                                        DV:
                                </label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <input   type="text" name="dv" id="cante" class="form-control" title="DV" onkeypress="return txtValida(event,'num_car')" placeholder="DV"  maxlength="15"  readonly>
                                </div>

                                <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" >
                                        Telefono:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input  type="text" name="telefono" required="" id="t" class="form-control" title="Telefono" placeholder="Telefono" value="<?php echo $rowt[0] ?>" maxlength="15" readonly>
                                </div>



                     </div>


                    <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">
                                        Código Postal:
                                </label>
                                <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->
                                <input type="hidden" name="contribuyente" id="contribuyente3" value="<?php echo $rowC[0] ?>">
                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     3. Contribuyente:
                                </label>
                                <div  class="col-sm-9 col-md-9 col-lg-9">
                                    <input  type="text" name="contribuyente"  id="contribuyente1" class="form-control" title="Contribuyente"  placeholder="Contribuyente" maxlength="15" readonly value="<?php echo $rowC[1] ?>">
                                </div>

                          </div>
      <!--              <div class="form-group" style="margin-top: 5px; margin-left: 5px;">


                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     4. Razón Social (Nombre de Establecimiento):
                                </label>
                                <div class="col-sm-9 col-md-9 col-lg-9">
                                    <input  type="text" name="razón Social"  id="cact" class="form-control" title="Razón Social" onkeypress="return txtValida(event,'num_car')" placeholder="Razón Social" value="<?php echo $rowC[4] ?>"  maxlength="15" readonly>
                                </div>

                          </div>-->

                    <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">
                                        Código Postal:
                                </label>
                                <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->

                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     5. Dirección para notificar:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input  type="text" name="direcciónNotificar"  id="dn" class="form-control" title="Dirección para notificar" placeholder="Dirección" value="<?php echo $rowd[0] ?>"  maxlength="15" readonly>
                                </div>

                                <!--<label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     6. Dirección Establecimiento:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input  type="text" name="direcciónEstablecimiento"  id="cact" class="form-control" title="Dirección Establecimiento" onkeypress="return txtValida(event,'num_car')" placeholder="Dirección Establecimiento" value="<?php echo $rowC[4] ?>"  maxlength="15" readonly>
                                </div>


                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     7. No. Establecimientos:
                                </label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <input  type="text" name="noEstablecimientos"  id="cact" class="form-control" title="No. Establecimientos" onkeypress="return txtValida(event,'num')" placeholder="No. Establecimientos" value="<?php echo $rowC[4] ?>"  maxlength="15" readonly>
                                </div> -->



                          </div>




 
              </form>
       </div>
  
      </div>

 


    <body>


 <?php  

                     //establecimientos
                       $sqlee="
                            SELECT e.id_unico,
                            e.nombre,
                            DATE_FORMAT(e.fechainicioAct,'%d-%m-%Y') AS fechaFacConvertida,
                            est.nombre,
                            e.direccion,
                            e.cod_catastral,
                            ciu.nombre,
                            b.nombre,
                            l.nombre,
                            te.nombre,
                            tame.nombre
                            FROM gc_establecimiento e
                            LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
                            LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                            LEFT JOIN gp_estrato est ON est.id_unico=e.estrato
                            LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
                            LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
                            LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
                            LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
                            LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad
                            WHERE md5(t.numeroidentificacion)='$n'";
                        $resultadoee=$mysqli->query($sqlee);
                          


 ?>

        <div class="container-fluid text-center">
            <div class="row content">


                <div class="col-sm-12 col-md-12 col-lg-12 text-left">
     
                    <div class="table-responsive" style="margin-top:-10px;">
                        <div class="table-responsive" >
                            <table id="tabla" class="table table-bordered"  cellspacing="0" width="100%">
                                <caption><strong>Establecimientos</strong></caption>
                                <thead>
                                    <tr>  
                               
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicial</strong></td>
                                        <td class="cabeza"><strong>Estrato</strong></td>
                                        <td class="cabeza"><strong>Dirección</strong></td>
                                        <td class="cabeza"><strong>Código Catastral</strong></td>
                                        <td class="cabeza"><strong>Ciudad</strong></td>
                                        <td class="cabeza"><strong>Barrio</strong></td>
                                        <td class="cabeza"><strong>Localización</strong></td>
                                        <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                        <td class="cabeza"><strong>Tamaño Entidad</strong></td>
                                   </tr>
                                </thead>
                                <tbody>
                     <?php while($rowee=mysqli_fetch_row($resultadoee)){ ?>
                                     <tr>

                                        <td  id="" class="campos"><?php echo $rowee[1] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[2] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[3] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[4] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[5] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[6] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[7] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[8] ?></td>
                                        <td  id="" class="campos"><?php echo $rowee[9] ?></td>                
                                        <td  id="" class="campos"><?php echo $rowee[10] ?></td>                

                                                   
                       <?php } ?>             
                                
                                    </tr>
                                  
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>


<?php




//vehiculos
$sqlV = "SELECT                         v.id_unico,
                                        tv.nombre,
                                        v.cod_inter,
                                        v.placa,
                                        v.porc_propiedad

                                        FROM gc_vehiculo v

                                        LEFT JOIN gc_contribuyente c ON c.id_unico=v.contribuyente
                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                        LEFT JOIN gc_tipo_vehiculo tv ON tv.id_unico=v.tipo_vehiculo
                                        WHERE md5(t.numeroidentificacion)='$n'";

$resultadoV=$mysqli->query($sqlV);


$sqlti="SELECT id_unico,nombre FROM gc_tipo_ingreso WHERE nombre='Ingresos Totales Año' or nombre='Ingresos Vehiculo segun%'";
    
$rti=$mysqli->query($sqlti);  
       
$filas=0;

 ?>
                <!--2da table Trasnportadores publicos urbanos-->
                <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-top:-10px;">
                        <div class="table-responsive" >
                            <table id="tablab" class="table table-bordered"  cellspacing="0" width="100%">
                               <caption><strong>B. VEHICULOS</strong></caption>
                                <thead>
                                    <tr>    
                                        <td><strong>8. Tipo Vehiculo</strong></td>
                                        <td><strong>9. Empresa</strong></td>
                                        <td><strong>10. Código Interno</strong></td>
                                        <td><strong>11. Placas</strong></td>
                                        <td><strong>12. % Propiedad</strong></td>
                                      <?php 
                                      $cn1=13;
                                      while($rowti=mysqli_fetch_row($rti)){ ?>

                                        <td><strong><?php echo $cn1.'.'.$rowti[1] ?></strong></td>
                                
                                        <?php
                                        $cn1++;
                                         } ?>
                                       <!-- <td><strong>14. <?php echo $rowti[1] ?></strong></td>-->
                                  
                                   </tr>
                                </thead>
                                <tbody>

                     <?php while($rowvv=mysqli_fetch_row($resultadoV)){ 
                                         $filas++;
                        ?>

                                     <tr>
                                         <td><?php echo $rowvv[1] ?></td>
                                         <td></td>
                                         <td><?php echo $rowvv[2] ?></td>
                                         <td><?php echo $rowvv[3] ?></td>
                                         <td><?php echo $rowvv[4] ?></td>
                                         <?php
                                            $idInputIT="idIngresosTotales".$filas;
                                             $filas++;

                                            $idInputIV="idIngresosVehiculo".$filas;

                                          ?>
                                         <td><input id="<?php echo $idInputIT ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idInputIV ?>"  value="" style="width: 100%" type="number"></td>
                                    </tr>
        
                        <?php 

                        } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


<!--
                <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12  text-right">
                                <button onclick="addRow()" type="submit" id="btnGuardarDetalle"  class="btn btn-primary shadow"><li class="glyphicon glyphicon-plus"></li></button>                              
                            </div>
                </div>-->
<?php $sqlti2="SELECT id_unico,nombre FROM gc_tipo_ingreso WHERE nombre='Ingresos Brutos por Actividad' or nombre='Deducciones' or nombre='Ingresos Excluidos' or nombre='Ingresos Externos' or nombre='Ingresos Fuera de Duitama' or nombre='Ingresos Netos Gravables' or nombre='Tarifa' or nombre='Impuesto'";
      $rti2=$mysqli->query($sqlti2);  

 ?>
               <!--3ra table Ingresos por Actividad -->
               <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-top:-10px;">
                        <div class="table-responsive" >
                            <table id="tablac" class="table table-bordered"  cellspacing="0" width="100%">
                               <caption><strong>C. INGRESOS POR ACTIVIDAD</strong></caption>
                                <thead>
                                    <tr>  
                                        <td><strong>15. Código RIT</strong></td>
                                        <td><strong>16. Código CIIU</strong></td>
                                       <?php
                                        $cn=17;
                                        while($rowt2=mysqli_fetch_row($rti2)){ ?>
                                        <td><strong><?php echo $cn++.'.'.$rowt2[1] ?></strong></td>
                                        <?php } ?>
                                   </tr>
                                </thead>
                                <tbody>
                                <?php /*for($i=1;$i<=$filas;$i++){*/ ?>
                                     <tr>
                                         <?php
                            /* $filas++;

                                            $idICRIT="idCodigoRit".$filas;
                             $filas++;

                                            $idCCIUU="idCCIUU".$filas;*/
                             $filas++;

                                            $idIBA="idIBA".$filas;
                             $filas++;

                                            $idD="idD".$filas;
                             $filas++;

                                            $idIE="idIE".$filas;
                             $filas++;

                                            $idIEXT="idIEXT".$filas;
                             $filas++;

                                            $idIFD="idIFD".$filas;
                             $filas++;

                                            $idING="idING".$filas;
                             $filas++;

                                            $idTarifa="idTarifa".$filas;
                             $filas++;

                                            $idIMP="idIMP".$filas;
                                          ?>
                                         <td><!--<input id="<?php echo $idICRIT ?>"  value="" style="width: 100%" type="number">--></td>
                                         <td><!--<input id="<?php echo $idCCIUU ?>"  value="" style="width: 100%" type="number">--></td>
                                         <td><input id="<?php echo $idIBA ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idD ?>"  value="" style="width: 100%" type="number"></td>
                                         <td dir="ltr"><input id="<?php echo $idIE ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idIEXT ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idIFD ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idING ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idTarifa ?>"  value="" style="width: 100%" type="number"></td>
                                         <td><input id="<?php echo $idIMP ?>"  value="" style="width: 100%" type="number"></td>
                                    </tr>
                                    <?php/* } */?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               <!-- <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12  text-right">
                                <button onclick="addRow2()" type="submit" id="btnGuardarDetalle"  class="btn btn-primary shadow"><li class="glyphicon glyphicon-plus"></li></button>                              
                            </div>
                </div>-->

                <?php $sqlBG="SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                              LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                              WHERE tc.nombre='D. BASE GRAVABLE'"; 

                      $qBG=$mysqli->query($sqlBG);


                      $inputsValue=0;  //contador inputs value


                              ?>
               <!--4to Base Gravable -->
               <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-top:-10px;">
                        <div class="table-responsive">
                            <table id="tablac" class="table table-bordered"  cellspacing="0" width="100%">
                               <caption><strong>D. BASE GRAVABLE</strong></caption>
                                <tbody>
                    
                                <?php 
                           
                                while($rowBG=mysqli_fetch_row($qBG)){ ?>
                                     <tr>
                                         <!--id_concepto Comercial-->
                                         <?php  $inputsValue++;
                                            $idInputCC="idInputCC".$inputsValue;
                                           $nameIdConceptoComercial="iidConceptoComercial".$inputsValue ?>
                                         <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowBG[0]; ?>" >

                                         <!--descripcion concepto comercial-->
                                         <?php 
                                            $idInputD="idInputD".$inputsValue;
                                           $nameDescripcion="iDescripcion".$inputsValue ?>
                                        <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowBG[1]; ?>" >
                                         <td style="width: 80%"><?php echo ucwords(mb_strtolower($rowBG[1])) ?></td>

                                         <!--value detalle declaracion-->
                                         <?php
                                            $idInputV="idInputV".$inputsValue;
                                            $nameValue="iValue".$inputsValue;  //
                                          ?>
                                         <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="number"></td>
                                         <!--<td dir="ltr" id="test1" class="tLine" nowrap onclick="addInput(this)"></td>-->
                                    </tr>
                        
                                    <?php
                                     } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php $sqlLP="SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                              LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                              WHERE tc.nombre='E. LIQUIDACION PRIVADA'"; 

                      $qLP=$mysqli->query($sqlLP);
                              ?>

               <!--5to Liquidación privada -->
               <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-top:-10px;">
                          <div class="table-responsive">
                            <table id="tablac" class="table table-bordered"  cellspacing="0" width="100%">
                               <caption><strong>E. LIQUIDACIÓN PRIVADA</strong></caption>
                                <tbody>

                                <?php while($rowLP=mysqli_fetch_row($qLP)){ ?>

                                     <tr>

                                         <!--id_concepto Comercial-->
                                         <?php  $inputsValue++;
                                            $idInputCC="idInputCC".$inputsValue;
                                           $nameIdConceptoComercial="iidConceptoComercial".$inputsValue ?>
                                         <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowLP[0] ?>" >

                                         <!--descripcion concepto comercial-->
                                         <?php 
                                            $idInputD="idInputD".$inputsValue;
                                           $nameDescripcion="iDescripcion".$inputsValue ?>
                                        <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowLP[1] ?>" >
                                         <td style="width: 80%"><?php echo ucwords(mb_strtolower($rowLP[1])) ?></td>

                                         <!--value detalle declaracion-->
                                         <?php
                                            $idInputV="idInputV".$inputsValue;
                                            $nameValue="iValue".$inputsValue;  //
                                          ?>
                                         <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="number"></td>

                                       <!--  <td style="width: 50%"><?php echo ucwords(mb_strtolower($rowLP[1])) ?></td>
                                         <td dir="ltr" id="test1" class="tLine" nowrap onclick="addInput(this)"></td>-->
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php $sqlFC="SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                              LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                              WHERE tc.nombre='F. CORRECCION'"; 

                      $qFC=$mysqli->query($sqlFC);
                              ?>

               <!--6to F. Correcion -->
               <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-top:-10px;">
                      <div class="table-responsive">
                            <table id="tablac" class="table table-bordered"  cellspacing="0" width="100%">
                               <caption><strong>F. CORRECIÓN(Si no es correcto pase al Numeral 44)</strong></caption>
                                <tbody>

                                <?php while($rowFC=mysqli_fetch_row($qFC)){ ?>

                                     <tr>


                                         <!--id_concepto Comercial-->
                                         <?php  $inputsValue++;
                                            $idInputCC="idInputCC".$inputsValue;
                                           $nameIdConceptoComercial="iidConceptoComercial".$inputsValue ?>
                                         <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowFC[0] ?>" >

                                         <!--descripcion concepto comercial-->
                                         <?php 
                                            $idInputD="idInputD".$inputsValue;
                                           $nameDescripcion="iDescripcion".$inputsValue ?>
                                        <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowFC[1] ?>" >
                                         <td style="width: 80%"><?php echo ucwords(mb_strtolower($rowFC[1])) ?></td>

                                         <!--value detalle declaracion-->
                                         <?php
                                            $idInputV="idInputV".$inputsValue;
                                            $nameValue="iValue".$inputsValue;  //
                                          ?>
                                         <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="number"></td>

                                         <!--<td style="width: 50%"><?php echo ucwords(mb_strtolower($rowFC[1])) ?></td>
                                         <td dir="ltr" id="test1" class="tLine" nowrap onclick="addInput(this)"></td>-->
                                    </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               <!--7tmo G.pago -->
                <?php $sqlGP="SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                              LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                              WHERE tc.nombre='G. PAGO'"; 

                      $qGP=$mysqli->query($sqlGP);
                              ?>

               <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-top:-10px;">
                            <table id="tablac" class="table table-bordered"  cellspacing="0" width="100%">
                               <caption><strong>G. PAGO</strong></caption>
                                <tbody>
                                <?php while($rowGP=mysqli_fetch_row($qGP)){ ?>

                                     <tr>

                                         <!--id_concepto Comercial-->
                                         <?php  $inputsValue++;
                                            $idInputCC="idInputCC".$inputsValue;
                                           $nameIdConceptoComercial="iidConceptoComercial".$inputsValue ?>
                                         <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowGP[0] ?>" >

                                         <!--descripcion concepto comercial-->
                                         <?php 
                                            $idInputD="idInputD".$inputsValue;
                                           $nameDescripcion="iDescripcion".$inputsValue ?>
                                        <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowGP[1] ?>" >
                                         <td style="width: 80%"><?php echo ucwords(mb_strtolower($rowGP[1])) ?></td>

                                         <!--value detalle declaracion-->
                                         <?php
                                            $idInputV="idInputV".$inputsValue;
                                            $nameValue="iValue".$inputsValue;  //
                                          ?>
                                         <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="number"></td>
                                         <!--<td style="width: 50%"><?php echo ucwords(mb_strtolower($rowGP[1])) ?></td>
                                         <td dir="ltr" id="test1" class="tLine" nowrap onclick="addInput(this)"></td>-->
                                    </tr>


                                <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                                    <div class="col-sm-12 col-md-12 col-lg-12  text-right">
                                        <button onclick="registrarDeclaracion(<?php echo $inputsValue ?>)" type="submit" id="btnGuardarDetalle"  class="btn btn-primary shadow"><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                                    </div>
                        </div><br><br>
                    </div>


                </div> 

                            <?php

                        }

                        ?>

 

      





   
<?php

    }else{ ?> 

<!--formulario sin consulta  1 -->
<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarContribuyenteJson.php" >
            

                                          
                     <div class="form-group" style="margin-top: 1.2%; margin-left: 1%;">

                          <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">


                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                        1. Identificación:
                                </label>

                                  <div class="col-sm-1 col-md-1 col-lg-1">
                                     <input   required="" type="radio" name="identificacion" value="Nit" checked="">Nit <br> 
                                     <input    required="" type="radio" name="identificacion" value="Cédula Ciudadanía"> cc <br>
                                  </div>


                                <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" >
                                        Número:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <!--<input type="search" id="myInput" onsearch="searchContribuyente()">-->
                                    <input  type="text" name="numero"  id="numero"  class="form-control" title="Número" onkeypress="return txtValida(event,'num')" placeholder="Número" maxlength="15">
                                </div>

                                <label for="cante" class="control-label col-sm-1 col-md-1 col-lg-1">
                                        DV:
                                </label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <input   type="text" name="dv" id="cante" class="form-control" title="DV" onkeypress="return txtValida(event,'num_car')" placeholder="DV"  maxlength="15"  readonly>
                                </div>

                                <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" >
                                        Telefono:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input  type="text" name="telefono" required="" id="t" class="form-control" title="Telefono" placeholder="Telefono"  maxlength="15" readonly>
                                </div>



                     </div>


                    <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">
                                        Código Postal:
                                </label>
                                <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->

                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     3. Contribuyente:
                                </label>
                                <div  class="col-sm-9 col-md-9 col-lg-9">
                                    <input  type="text" name="contribuyente"  id="contribuyente2" class="form-control" title="Contribuyente"  placeholder="Contribuyente" maxlength="15" readonly 
                                </div>

                          </div>
      <!--              <div class="form-group" style="margin-top: 5px; margin-left: 5px;">


                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     4. Razón Social (Nombre de Establecimiento):
                                </label>
                                <div class="col-sm-9 col-md-9 col-lg-9">
                                    <input  type="text" name="razón Social"  id="cact" class="form-control" title="Razón Social" onkeypress="return txtValida(event,'num_car')" placeholder="Razón Social" value="<?php echo $rowC[4] ?>"  maxlength="15" readonly>
                                </div>

                          </div>-->

                    <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">
                                        Código Postal:
                                </label>
                                <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->

                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     5. Dirección para notificar:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input  type="text" name="direcciónNotificar"  id="dn" class="form-control" title="Dirección para notificar" placeholder="Dirección"   maxlength="15" readonly>
                                </div>

                                <!--<label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     6. Dirección Establecimiento:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input  type="text" name="direcciónEstablecimiento"  id="cact" class="form-control" title="Dirección Establecimiento" onkeypress="return txtValida(event,'num_car')" placeholder="Dirección Establecimiento" value="<?php echo $rowC[4] ?>"  maxlength="15" readonly>
                                </div>


                                <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >
                                     7. No. Establecimientos:
                                </label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <input  type="text" name="noEstablecimientos"  id="cact" class="form-control" title="No. Establecimientos" onkeypress="return txtValida(event,'num')" placeholder="No. Establecimientos" value="<?php echo $rowC[4] ?>"  maxlength="15" readonly>
                                </div> -->



                          </div>




 
     </form>



<?php } ?>               

<div class="modal fade" id="rc" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
   <div class="modal fade" id="nr" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>  




            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Establecimiento?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
  <div class="modal fade" id="NoModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No hay contribuyentes, por favor verifique la identificación y el número</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!--Script que dan estilo al formulario-->

  <script src="js/bootstrap.min.js"></script>
  <script src="js/txtValida.js"></script>

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
                  url:"json/eliminarTarifaPJson.php?id="+id,
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
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GR_TARIFA.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GR_TARIFA.php';
      });    
  </script>
    </body>
</html>
   

<?php if (isset($_GET['e'])){ ?>
<script>
 $().ready(function() {
            var validator = $("#form3").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione Actividad Comercial",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".form-group").addClass("has-error").removeClass('has-success');          

                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".form-group").addClass('has-success').removeClass('has-error');   

                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        }); 

</script>   
                     <!--2do formulario Establecimiento -->

<div class="col-sm-8 col-md-8 col-lg-8 client-form" style="margin-top:-11.3%;margin-right: 10px;margin-left: 1%;padding: 5px 5px 5px 5px;">   
                
                      

                    </div>


                      <!--listado establecimiento contribuyente-->
                     <div class="col-sm-8" style="margin-top:-0.5%">
                          


           <!--modales de eliminacion-->
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>¿Desea eliminar el registro seleccionado de Establecimiento?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                      <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                  </div>
                </div>
                </div>
                <div class="modal fade" id="myModal1" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                
                <?php require 'GC_ESTABLECIMIENTO_MODAL.php' ; ?>
                <!--Script que envia los datos para la eliminación-->
                <script type="text/javascript">
                      function eliminare(id)
                      {
                         var result = '';
                         $("#myModal").modal('show');
                         $("#ver").click(function(){
                              $("#mymodal").modal('hide');
                              $.ajax({
                                  type:"GET",
                                  url:"jsonComercio/eliminarEstablecimientosJson.php?id="+id,
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

                       function open_modal_e(id){  
                              var form_data={                            
                                id:id 
                              };
                              $.ajax({
                                  type:"POST",
                                  url: "GC_ESTABLECIMIENTO_MODAL.php#mdlModificar",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlModificar").html(data);
                                    $(".modale").modal('show');
                                 }
                             })  
                        }
                  </script>

                  <script type="text/javascript">
                      function modal()
                      {
                         $("#myModal").modal('show');
                      }
                  </script>
                    <!--Actualiza la página-->
                  <script type="text/javascript">
                    
                      $('#ver1').click(function(){
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });
                    
                  </script>

                  <script type="text/javascript">    
                      $('#ver2').click(function(){
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });    
                  </script>

                          <script type="text/javascript" >
                            function abrirdetalleMov(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }
                            function abrirdetalleMov1(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }                                                                                          
                          </script>

                          <style>
                              .valores:hover{
                                  cursor: pointer;
                                  color:#1155CC;
                              }
                          </style>
                          <div class="container">

                          </div>

                          <script>
                            $("#btnGuardarM").click(function(){
                                if($("#sltBanco").val()==""){
                                    $("#mdlBanco").modal('show');
                                }else{
                                    var form_data = {
                                        banco:$("#sltBanco").val(),
                                        fecha:$("#fecha").val(),
                                        descripcion:$("#txtDescripcion").val(),
                                        valor:<?php echo $w; ?>,
                                        valorEjecucion:'0',
                                        comprobante:$("#id").val(),
                                        tercero:$("#slttercero").val(),
                                        proyecto:$("#sltproyecto").val(),
                                        centro:$("#sltcentroc").val()
                                    };
                                    var result = '';
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                                        data:form_data,
                                        success: function (data) {
                                            result = JSON.parse(data);
                                            console.log(data);
                                            if(result==true){
                                                $("#guardado").modal('show');
                                            }else{
                                                $("#noguardado").modal('show');
                                            }
                                        }
                                    });
                                }                                                                                                
                            });                        
                          </script>
                      </div>                



              <?php } ?>
  



  </div>
</div>
        <!-- Modal de carga de datos -->
        <div class="modal fade" id="mdlBanco" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No hay un banco seleccionado</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un tipo de comprobante.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modales de guardado -->
        <div class="modal fade" id="guardado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información guardada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnG" class="btn" onclick="reloda()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noguardado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido guardar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Moidales de eliminado -->
        <!--<div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Detalle Ingreso?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Modales de modificado -->
        <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'modalComprobanteCausacion_Ingreso.php'; ?>
        <!-- Librerias -->
        <script src="js/bootstrap.min.js"></script>
        
        <script type="text/javascript" src="js/select2.js"></script>

        <script>
          $(".select2").select2();
          $("#slttercero").select2();                           
          $("#sltBanco").select2();                             
          $("#sltContribuyente").select2();                          
          $('#btnModifico').click(function(){
              window.location.reload();
          });        
          $('#btnNoModifico').click(function(){
              window.location.reload();
          });        
          $('#ver1').click(function(){
              window.location.reload();
          });        
          $('#ver2').click(function(){  
            window.location.reload();
          });        
          $('#btnG').click(function(){
              window.location.reload();
          });        
          $('#btnG2').click(function(){  
              window.location.reload();
          });            
          $("#btnMdlEliminadoP").click(function(){  
              window.location.reload();
          });
          function reload()  {
            window.location.reload();
          }
        </script>
        <?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php'; ?>        
        <script type="text/javascript">
        $("#modalCausacion").on('shown.bs.modal',function(){
            var dataTable = $("#tablaDetalleC").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        });
      </script>
    </body>
</html>

