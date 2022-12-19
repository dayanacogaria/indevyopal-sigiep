<style>
    #btnCerrarModalMov:hover {border: 1px solid #020324;}
    #btnCerrarModalMov{box-shadow: 1px 1px 1px 1px #424852; margin-left: 50px;}
</style>
<?php require_once('Conexion/conexion.php'); ?>
<div class="modal fade mov" id="mdlFormulador" role="dialog" align="center" aria-labelledby="mdlFormulador" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:1000px">
        <div class="modal-content">            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Formulador</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;">
                    <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">                    
                    <div class= "contenedorForma">
                        <form name="form" id="form" accept-charset="" class="form-horizontal form-inline" method="POST"  enctype="multipart/form-data" action="#">
                            <div class="form-group text-left form-horizontal" style="margin-top: 30px; ">
                                <div class="form-group col-sm-12 text-left">                                    
                                    <div class="col-sm-2"></div>
                                    <p id="textoPantalla" class="form-control" name="textoPantalla" style="height:50px">0</p>
                                </div>
                                <div class="form-group text-left col-sm-12" style="margin-bottom: 8px">
                                    <div class="col-sm-2"></div>
                                    <input type="button" class="btn btn-primary largo shadow" value="7" onclick="numero('7')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="8" onclick="numero('8')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="9" onclick="numero('9')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="/" onclick="operacion('/')"  />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="<" onclick="operacion('<')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="!=" onclick="operacion('<>')" />
                                    <label style="font-size:14px; font-weight: bold;margin-left:15px;" class="control-label">VARIABLES:</label>
                                </div>
                                <div class="form-group text-left col-sm-12" style="margin-bottom: 8px">
                                    <div class="col-sm-2"></div>
                                    <input type="button" class="btn btn-primary largo shadow" value="4" onclick="numero('4')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="5" onclick="numero('5')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="6" onclick="numero('6')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="*" onclick="operacion('*')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value=">" onclick="operacion('>')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="==" onclick="operacion('==')" />
                                    <select class="seleccion form-control" id="variables" name="variables" style="padding: 2px; width: 200px; font-size:14px; font-weight: bold;margin-left:15px;" onchange="variabless('variables')">
                                   <option value="">Variables</option>                                   
                                    <?php  $_POST['consulta'];
                                    if(!empty($_POST['consulta'])) {
                                        $consulta=$_POST['consulta'];
                                        $rowc= $mysqli->query($consulta);
                                        if(mysqli_num_rows($rowc)>0){?>
                                            <?php while ($row = mysqli_fetch_row($rowc)) {?>
                                            <option value="<?php echo $row[0]?>"><?php echo strtolower($row[0])?></option>    
                                    <?php } }else{
                                        $sqlC = "SELECT DISTINCT cat.id_unico,cat.nombre FROM gn_categoria_formula cat 
                                                LEFT JOIN gn_variables vr ON vr.categoria = cat.id_unico
                                                WHERE vr.categoria IS NOT NULL";
                                        $resultC = $mysqli->query($sqlC);
                                        while($rowCC = mysqli_fetch_row($resultC)){
                                            echo "<optgroup label=\"".ucwords(strtolower($rowCC[1]))."\">";                                             
                                            $sqlDD = "SELECT id_unico,nombre FROM gn_variables WHERE categoria = $rowCC[0]";
                                            $resultDD = $mysqli->query($sqlDD);
                                            while($rowDD = mysqli_fetch_row($resultDD)){
                                                echo "<option value=\"$rowDD[1]\">".$rowDD[1]."</option>";
                                            }
                                            echo "</optgroup>";
                                        }
                                        
                                    } } ?>                                            
                                    </select>
                                </div>
                                <div class="form-group text-left col-sm-12" style="margin-bottom: 8px">
                                    <div class="col-sm-2"></div>
                                    <input type="button" class="btn btn-primary largo shadow" value="1" onclick="numero('1')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="2" onclick="numero('2')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="3" onclick="numero('3')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="-" onclick="operacion('-')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="<=" onclick="operacion('<=')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="(" onclick="caracter('(')" />
                                    <label style="font-size:14px; font-weight: bold;margin-left:15px;">FUNCIONES:</label>
                                </div>
                                <div class="form-group text-left col-sm-12" style="margin-bottom: 8px">
                                    <div class="col-sm-2"></div>
                                    <input type="button" class="btn btn-primary largo shadow" value="." onclick="comas('.')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="0" onclick="numero('0')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="C" onclick="retro()"   />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="+" onclick="operacion('+')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value=">=" onclick="operacion('>=')" >
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value=")" onclick="caracter(')')" />
                                    <select class="seleccion form-control" id="funciones" name="funciones" style="padding: 2px;width: 200px; font-size:14px; font-weight: bold; margin-left:15px;" onchange="funcioness('funciones')">
                                        <option value="">Funciones</option>
                                        <option value="Sin(">Sin</option>
                                        <option value="Cos(">Cos</option>
                                        <option value="Tan(">Tan</option>
                                    </select>                                        
                                </div>
                                <div class="form-group text-left col-sm-12" style="margin-bottom: 8px">
                                    <div class="col-sm-2"></div>
                                    <input type="button" class="btn btn-primary largo shadow" value="X" onclick="borrarTodo()" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="Cond" onclick="Condicion1('SI(')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="?" onclick="Condicion('?')" />
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;" value="=" onclick="operacion('=')" >
                                    <input type="button" class="btn btn-primary largo shadow" style="margin-left: 5px;width: 185px; height: 34px;" value="Ok" onclick="validar()" />
                                </div>
                            </div>
                        </form>
                    </div>                          
                </div>
            </div>
            <div id="forma-modal" class="modal-footer"></div>
        </div>
    </div>
</div>
<style>
    #textoPantalla { 
        width: 570px; 
        /**border: 2px black solid;**/
        text-align: right;
        padding: 5px 5px;
        background-color: white; font-family: "arial"; 
        overflow: hidden;      
        box-shadow:inset 2px 1px 2px gray;  
    }
    .largo{
        width: 88px;
        height: 34px;
        font-size: 14px;        
    }
</style>
<script>
    window.onload = function(){ 
        pantalla=document.getElementById("textoPantalla"); //elemento pantalla de salida
        //document.onkeydown = teclado; //función teclado disponible
    }
    <?php 
    if(!empty($_POST['formula'])) { ?>
        var formula = <?php echo "'".$_POST['formula']."'"?>;
        if(formula=='' || formula=="" || formula.length<=1) { 
            x="0";
            xi=1; //iniciar número en pantalla: 1=si; 0=no;
            coma=0; //estado coma decimal 0=no, 1=si;
            ni=0; //estado variable o funcion;1=si; 0=no;
        } else { 
            x=formula;
            document.getElementById("textoPantalla").innerHTML=formula;
            xi=0;
            coma=0; //estado coma decimal 0=no, 1=si;
            ni=0; //estado variable o funcion;1=si; 0=no;
        }
    <?php } else { ?>
        x="0"; //número en pantalla
        xi=1; //iniciar número en pantalla: 1=si; 0=no;
        coma=0; //estado coma decimal 0=no, 1=si;
        ni=0; //estado variable o funcion;1=si; 0=no;
    <?php } ?>    
    //mostrar número en pantalla según se va escribiendo:
    function numero(xx) {  //recoge el número pulsado en el argumento.    
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter
        if (br.search(/^[\)\a-zA-Z\&]+/)==0) {
        } else {        
            if (xi==1 && coma ==0 || x=='0') {  // inicializar un número,             
                document.getElementById('textoPantalla').innerHTML=xx;
                x=xx; //guardar número;
            } else { //continuar un número
                document.getElementById('textoPantalla').innerHTML+=xx;
                x+=xx; //añadimos y guardamos
            }
            xi=0 //el número está iniciado y podemos ampliarlo.
        }
        if (br.search(/^[\*\\/\\+\-\(\<\>\=\(\)]+/)==0) {
            coma=0;
        }       
    }
    function retro(){ //Borrar sólo el último número escrito.
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter
         
        if (br==".") {coma=0;} //Si el caracter quitado es la coma, se permite escribirla de nuevo.
            if(x=="0"){
                x="0";             
            }else {
                if (br.search(/^[\a-zA-Z\&]+/)==0) {
                    l=1;
                    while (l==1){
                        if (br!=''){
                            if(br.search(/^[\a-zA-Z\&]+/)==0){
                                l=1;
                                cifras=x.length;
                                br=x.substr(cifras-1,cifras);
                                if(br.search(/^[\a-zA-Z\&]+/)==0){
                                    x=x.substr(0,cifras-1);
                                }
                            } else {
                                if(br.search(/^[\s]+/)==0){
                                    l=1;
                                    cifras=x.length;
                                    br=x.substr(cifras-1,cifras);
                                    if(br.search(/^[\s]+/)==0){
                                        x=x.substr(0,cifras-1);
                                    } 
                                }else {
                                    l=0;
                                    ni=0;
                                }
                            }                    
                        }else {
                            l=0;
                            x="0";
                            xi=1;
                            coma=0;
                            ni=0;
                        }
                    }
                } else {             
                if(br=='('){
                    zz= x.substr(cifras-2,cifras); //describir último caracter   
                    if(zz.search(/^[a-zA-Z]+/)==0){
                        x=x.substr(0,cifras-1);
                        cifras=x.length; //hayar número de caracteres en pantalla
                        br=x.substr(cifras-1,cifras); //describir último caracter
                        l=1;
                        while (l==1){
                            if (br!=''){
                                if(br.search(/^[\a-zA-Z\&]+/)==0){
                                    l=1;
                                    cifras=x.length;
                                    br=x.substr(cifras-1,cifras);
                                    if(br.search(/^[\a-zA-Z\&]+/)==0){
                                       x=x.substr(0,cifras-1);
                                    }
                                } else {
                                    if(br.search(/^[\s]+/)==0){
                                        l=1;
                                        cifras=x.length;
                                        br=x.substr(cifras-1,cifras);
                                        if(br.search(/^[\s]+/)==0){
                                           x=x.substr(0,cifras-1);
                                        } 
                                    }else {
                                        l=0;
                                        ni=0;
                                    }
                               }
                            }else {
                                l=0;
                                x="0";
                                xi=1;
                                coma=0;
                                ni=0;
                            }
                        }                      
                    } else {
                        x=x.substr(0,cifras-1);
                    }
                } else {
                    if(br=='='){
                        zz=x.substr(cifras-2,cifras);                        
                        if(zz=='==' || zz=='!=' || zz=='<=' || zz=='>='){
                            x=x.substr(0,cifras-2);  
                        } else {
                            x=x.substr(0,cifras-1);  
                        }
                    } else {
                        if (br!=''){
                            x=x.substr(0,cifras-1);  
                        } else {
                            x="0";
                            xi=1;
                            coma=0;
                            ni=0;
                        } 
                    }
                }
            }         
            document.getElementById('textoPantalla').innerHTML=x;
        }
    }
    function funcioness(id){
        if(ni==1){        
        } else {
            var xx = document.getElementById(id).value;    
            if (x=="0" || xi==1  ) {  // inicializar un número,     
                cifras=x.length;
                br=x.substr(cifras-1,cifras);
                if (br.search(/^[\0-9\.\a-zA-Z\)\&]+/)!=0) {            
                } else  {
                    document.getElementById('textoPantalla').innerHTML=xx;
                    x=xx; //añadimos y guardamos
                    xi=0; //el número está iniciado y podemos ampliarlo.            
                }           
            } else { //continuar un número            
                cifras=x.length;
                br=x.substr(cifras-1,cifras);            
                if (br.search(/^[0-9\a-zA-Z\.\)\&]+/)==0) {            
                } else  {
                    document.getElementById('textoPantalla').innerHTML+=xx;
                    x+=xx; //añadimos y guardamos
                    xi=0; //el número está iniciado y podemos ampliarlo.            
                }         
            }
        }
    }

    function variabless(id){
        cifras=x.length;
        br=x.substr(cifras-1,cifras);
        if(ni==1){        
        } else {
            var xx = document.getElementById(id).value;
            var xx = xx.toLowerCase();
            if(xx==''){} else{ 
                if (x=="0" || xi==1  || x=='') {
                    xx='&'+xx+'&';
                    document.getElementById('textoPantalla').innerHTML=xx;
                    x=xx; //guardar número;
                    xi=0; //el número está iniciado y podemos ampliarlo.
                    ni=1;        
                } else { //continuar un número            
                    cifras=x.length;
                    br=x.substr(cifras-1,cifras);            
                    if (br.search(/^[0-9\a-zA-Z\.\)\&]+/)==0) {            
                    } else  {
                        xx='&'+xx+'&';
                        document.getElementById('textoPantalla').innerHTML+=xx;        
                        x+=xx; //añadimos y guardamos
                        xi=0; //el número está iniciado y podemos ampliarlo.
                        ni=1;            
                    }
                }
            }
        }
    }

    function comas(xx){
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter
        if (br.search(/^[\)\a-zA-Z\(\&]+/)==0) {
        } else {
            if (coma==0) { //si escribimos una coma decimal pòr primera vez         
                if (br.search(/^[\*\\/\\+\-\(\<\>\=\.\(\)]+/)==0) {                
                } else {
                    document.getElementById('textoPantalla').innerHTML+=xx; 
                    x+=xx;
                    coma=1; //cambiar el estado de la coma  
                }           
            } else {}
        }    
    }

    function operacion(xx){
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter
        if (br.search(/^[\.\*\\/\\+\-\(\<\>\=\?\a-zA-Z]+/)==0) {
        }else {
            if (xi==1) {  // inicializar un número,             
            }  else { //continuar un número
                document.getElementById('textoPantalla').innerHTML+=xx;  
                x+=xx; //añadimos y guardamos
                xi=0; //el número está iniciado y podemos ampliarlo.
                ni=0;
            }                       
        }
    }

    function caracter(xx){    
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter        
        if (x=="0" || xi==1  ) {  // inicializar un número, 
            if(xx==')'){}else{
                document.getElementById('textoPantalla').innerHTML=xx;    
                x=xx; //guardar número;
                xi=0; //el número está iniciado y podemos ampliarlo.
                ni=0;
            }
        } else { //continuar un número
            //Si caracter es )
            if(xx==')'){
                prim=0;
                //Verifica que el ultimo elemento no sea +/*-><=(
                if (br.search(/^[\=\.\>\<\+\-\/\*\?\(]+/)==0) { } else {
                    for (var i = 0; i< x.length; i++) {
                        var caracter = x.charAt(i);
                        if( caracter == "(") {
                            prim=prim+1;
                        }  
                    }
                    if(prim>0){
                        document.getElementById('textoPantalla').innerHTML+=xx;
                        x+=xx; //añadimos y guardamos
                        xi=0; //el número está iniciado y podemos ampliarlo.
                        ni=0;
                    } else {}
                }
            } else {
                //Si el caracter es (
                if(xx=='('){                   
                    //verifica que el ultimo caracter no sea . 0-9 &
                    if (br.search(/^[0-9]+/)==0) { } else {
                        if (br.search(/^[\.\&\?\)]+/)==0) { } else { 
                            document.getElementById('textoPantalla').innerHTML+=xx;
                            x+=xx; //añadimos y guardamos
                            xi=0; //el número está iniciado y podemos ampliarlo.
                            ni=0;  
                        }
                    } 
                } else { }
            } 
        }   
    }

    function validar(){
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter
        prim=0;
        prim2=0;
        cond=0;
        if (br.search(/^[\=\.\>\<\+\-\/\*\?\(]+/)==0 || br.search(/^[a-zA-Z]+/)==0) { 
            $('#myModalFinalizacion').modal('show');
            $('#verFinalizacion').click(function(){
                $('#myModalFinalizacion').modal('hide');
            });
        } else {
            for (var i = 0; i< x.length; i++) {
                var caracter = x.charAt(i);
                if( caracter == "(") {
                    prim=prim+1;
                }  else {
                    if(caracter==")"){
                        prim2=prim2+1;
                    } else {
                        if(caracter=="?"){
                            cond=cond+1;
                        }   
                    }
                }
            }
        
            if(prim!=prim2){
                $('#myModalFaltaC').modal('show');
                $('#verFaltaC').click(function(){
                    $('#myModalFaltaC').modal('hide');
                });
            } else {
                var Cadena =x;
                var Search = "SI("
                var i = 0;
                var iff = 0;
                while (i != -1) {
                    var i = Cadena.indexOf(Search,i);
                    if (i != -1) {
                        i++;
                        iff++;
                    }
                }
                var ifff= iff*2;
                if(ifff!=cond){
                    $('#myModalCondicion').modal('show');
                    $('#verCondicion').click(function(){
                        $('#myModalCondicion').modal('hide');
                    });
                } else {
                    document.getElementById('formulaF').value=x;
                    $(".mdlFormulador").modal('hide');
                    $(".mov").modal('hide');
                } 
            }
        }
    }

    function borrarTodo(){
        document.getElementById('textoPantalla').innerHTML=0;
        x="0"; //reiniciar número en pantalla
        coma=0; //reiniciar estado coma decimal 
        ni=0 //indicador de número oculto a 0;
        xi=1;
    }

    function Condicion1(xx){
        cifras=x.length; //hayar número de caracteres en pantalla
        br=x.substr(cifras-1,cifras); //describir último caracter        
        if ( x=='0' || x=='' ) {  // inicializar un número, 
           x=xx;        
           document.getElementById('textoPantalla').innerHTML=xx;    
           xi=0;
        }else {             
            if (br.search(/^[a-zA-Z]+/)==0 || br.search(/^[0-9]+/)==0 || br.search(/^[\&\)]+/)==0) {    
                x+=xx;        
                document.getElementById('textoPantalla').innerHTML+=xx;             
            } else {
                document.getElementById('textoPantalla').innerHTML+=xx;  
                x+=xx; //añadimos y guardamos
                xi=0; //el número está iniciado y podemos ampliarlo.
                ni=0;
            }
        } 
    }
    
    function Condicion(xx){
        if (x=='0' || x=='') {  // inicializar un número,            
        } else { 
            cifras=x.length; //hayar número de caracteres en pantalla
            br=x.substr(cifras-1,cifras); //describir último caracter
            var iff=0;
            //Contar cuantos if hay
            var Cadena =x;
            var Search = "SI("
            var i = 0;
            var iff = 0;
            while (i != -1) {
                var i = Cadena.indexOf(Search,i);
                if (i != -1) {
                    i++;
                    iff++;
                }
            }
            //contar cuantos condicionales hay
            var cond=0;
            for (var i = 0; i< x.length; i++) {
                var caracter = x.charAt(i);
                if( caracter == "?") {
                    cond=cond+1;
                } 
            }
            //comparar 
            var ifff= iff*2;
            if(ifff!=cond){
                if (br.search(/^[\=\.\>\<\+\-\/\*\(\?\)]+/)==0 ) {  
                    x+=xx;        
                    document.getElementById('textoPantalla').innerHTML+=xx; 
                    ni=0;
                } else {
                    x+=xx;        
                    document.getElementById('textoPantalla').innerHTML+=xx; 
                    ni=0;
                }
            }
        }
    }      
</script>