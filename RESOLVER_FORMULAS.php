<?php 
require_once 'head.php';                                    //Archivo abjunto de la cabeza de la cabeza del formulario
require_once('Conexion/conexion.php');                      //Archivo abjunto con la conexión de base de datos
$sqlCatFor = "SELECT id_unico, nombre 
  FROM gn_categoria_formula   
  ORDER BY nombre ASC";
$cateForm = $mysqli->query($sqlCatFor);
?>
    <title>Formulador</title>
    <style>
        /**
         * @btnFX        
        */
        #btnFX { width: 30px; height: 30px; }

        /**
         * @btnFX:hover
        */
        #btnFX:hover {
            cursor: pointer;            
            border-radius: 5px;            
        }

        /**
         * @formulaF
        */
        #formulaF{cursor: not-allowed;text-align: left;} 

        /**
         * @resultado
        */
        #resultado {
            box-shadow: 1px 1px 1px 1px gray;
            border-color:#1075C1;
            width: 70%;
            height: 30px;
            border-radius: 5px;
        }

        /**
         * @shadow
        */
        .shadow {box-shadow: 1px 1px 1px 1px gray;border-color:#1075C1;}
        /**
         * @body
        */
        body{font-size: 12px}
    </style>
</head>
<body>
    <input type="hidden" id="id_cat_for">
    <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Fórmulas</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:setting_values()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php //echo $row[0]==''?0:$row[0] ?>">
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="formula" class="col-sm-2 control-label"><strong class="obligado">*</strong>Fórmula:</label>
                            <!-- Campo de formula -->
                            <textarea name="formulaF" id="formulaF" title="Fórmula" class="col-sm-1 form-control" onpaste="return false" oncut="return false" oncopy="return false" readonly="true" style="width: 70%;height: 100px; margin-top:0px;" placeholder="Fórmula"></textarea><!-- ./Campo de formula -->
                            <!-- Botón para abrir el formulador -->
                            <div class="col-sm-1">
                                <div id="btnFX">
                                    <a onclick="abrirCategoriaFormula()">                                
                                        <img src="images/formula.png" width="30px" height="30px" title="Fórmula">
                                    </a>                                                                    
                                </div>
                            </div><!-- ./Bóton para abrir el formulador -->
                        </div>                        
                        <div class="form-group" style="margin-top: -15px;">                            
                            <label for="resultadolabel" class="col-sm-2 control-label">Resultado:</label>
                            <!-- Label para imprimir el resultado de la formula -->
                            <label for="resultado" id="resultado" name="resultado" class="col-sm-1 control-label form-control" title="Resultado"></label><!-- ./Label para imprimir el resultado de la formula -->
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="no" class="col-sm-2 control-label"></label>
                            <!-- Botón para envio de formula -->
                            <button type="submit" class="btn btn-primary shadow">Calcular</button><!-- ./Botón para envio de formula -->
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php  require_once 'footer.php';?>
</body>
<script>    
    /**
    * Evalua que proceso seguir dependiendo que si en la formula existen variables
    *
    * @formula {String} cadena de texto, la cual es la formula a evaluar
    */
    function setting_values(){
        var formula = $("#formulaF").val();                 //Capturamos el valor del campo formulaF
        if(formula.length == 0) {
            $("#formulaF").css({'border-color': 'red'});            
        }else{        
            $("#formulaF").css({'border-color': '#D5D5D5'});    
            var x = 0;                                      //Contador en 0
            //Verificamos si hay variables escritas en el formulador
            for (var i = 0; i< formula.length; i++) {       //Ciclo en el que tomamos el tamaño de la formula enviada
                var caracter = formula.charAt(i);           //Convertimos a caracter la formula
                if( caracter == "&") {                      //Buscamos el caracter &
                    x++;                                    //Incremnetamos el contado x
                }
            }
            x = x/2;                                        //Divimos el contador en 2
            if(x>0){                                        //Si el valor del contador es mayor que 0 describe la formula, sino tan solo la resuelve
                describe_expression(formula);               //Llamamos a la función describe_expression
            }else{
                send_expression(formula);                   //Llamamos a la función send_expression            
            }        
        }
    }

    /**
    * Función para obtener los valores de las variables, envia un ajax el cual descubre los valores de la formula y los reemplaza
    *
    * @formula {String} Cadena de texto, la cual es la formula con las variables encerradas en &&
    */    
    function describe_expression(formula) {             //Enviamos un ajax para describir y descubir la formula
        //Variable para envio del ajax
        var form_data = {
            estruc:1,
            formula:formula            
        };
        //Envio ajax
        $.ajax({
            type:'POST',
            url:'consultasBasicas/estructura_formulador_valores.php',
            data:form_data,
            success: function(data,textStatus,jqXHR) {
                send_expression(data);                  //Llamamos a la función send_data() y enviamos la formula descrita, es decir con las variables reemplazadas con sus respectivos valores
            }
        }).error(function(data,textError,jqXHR) {
            alert('Error :'+textError);
        });
    }
    
    /**
    * Función para resolver la formula, envia un ajax a un archivo de excel la cual retorna el resultado de la formula
    *
    * @expression {String} Cadena de texo con las variables en la formula ya reemplazados
    * @return data {int} Resultado de la formula
    */        
    function send_expression(expression) {
        var expression = expression.replace(/[\?]+/g,",");                   //Buscamos los signos ? y los reemplazamos con ,
        var expression = expression.replace(/[\==]+/g,"=");                  //Buscamos los signos == y los reemplazamos con =
        //Variable de envio del ajax
        form_data={
            is_ajax:1,
            formula:expression,
        }
        //Envio ajax
        $.ajax({
            type: 'POST',
            url: "formulas/formulas.php",
            data:form_data,
            success: function (data) { 
                var result = JSON.parse(data);                              //Capturamos el valor retornado como json y lo decodificamos                
                $("#resultado").text(result);                               //Enviamos el valor resultamos al label con el id Resultado
            }
        }).error(function(data,textError,jqXHR) {
            alert('Error :'+textError);
        });        
    }
</script>
<!--LLAMADO AL FORMULADOR-->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" >
    function abrirCategoriaFormula() {$("#mdlCategoriaFor").modal('show');}                                                                                        
</script>           
<?php require_once './GP_FORMULADOR.php'; ?>
<div class="modal fade" id="mdlCategoriaFor" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">            
        <input type="hidden" name="idM" id="idM">
        <div class="modal-content client-form1">
            <div id="forma-modal" class="modal-header">       
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Categoría Fórmula</h4>
            </div>
            <div class="modal-body "  align="center">
                <div class="form-group" align="left">
                    <label  style="margin-left:150px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Categoría Fórmula:</label>
                    <select style="display:inline-block; width:250px; font-size: 0.9em; height: 30px; padding: 5px;" type="text" name="catFor" id="catFort" title="Ingrese la categoría" class="form-control input-sm"  required>
                        <option value="">Categoría Fórmula</option>
                        <?php 
                        while($row = mysqli_fetch_row($cateForm)) {
                            echo '<option value="'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[1]))).'</option>';
                        } ?>
                    </select>
                </div>
                <input type="hidden" id="id" name="id">  
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnCatFor" class="btn" style="color: #000; margin-top: 2px">Aceptar</button>
                <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">    
    $("#catFort").change(function() {
        var id_cat_for = $("#catFort").val();
        $("#id_cat_for").val(id_cat_for);
    });
    
    $("#btnCatFor").click(function() {
        if($("#id_cat_for").val() != "") {
            $("#mdlCategoriaFor").modal('hide');
            var id_cat_for = document.getElementById('id_cat_for').value;
            var formula = document.getElementById('formulaF').value;
            var consulta = "SELECT nombre FROM gn_variables WHERE categoria = " + id_cat_for + " ORDER BY nombre ASC";
            var form_data = {                           
                consulta : consulta, 
                formula : formula
            };
            $.ajax({
                type: 'POST',
                url: "GP_FORMULADOR.php#mdlFormulador",
                data:form_data,
                success: function (data) { 
                    $("#mdlFormulador").html(data);
                    $(".mov").modal('show');
                }
            });
        } else {
            $("#catFort").focus();
        }
     });    
</script>
<div class="modal fade" id="myModalFinalizacion" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Finalización Inválida</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verFinalizacion" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalFaltaC" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Hace falta cerrar expresión</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verFaltaC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalCondicion" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Condición mal estructurada</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verCondicion" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
</html>