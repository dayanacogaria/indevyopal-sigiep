
window.onload = function(){ 
pantalla=document.getElementById("textoPantalla"); //elemento pantalla de salida
//document.onkeydown = teclado; //función teclado disponible
}
x="0"; //número en pantalla
xi=1; //iniciar número en pantalla: 1=si; 0=no;
coma=0; //estado coma decimal 0=no, 1=si;
ni=0; //estado variable o funcion;1=si; 0=no;
//mostrar número en pantalla según se va escribiendo:
function numero(xx) {  //recoge el número pulsado en el argumento.
         if (x=="0" || xi==1  ) {  // inicializar un número, 
            pantalla.innerHTML=xx; //mostrar en pantalla
            x=xx; //guardar número;
            }
         else { //continuar un número
            pantalla.innerHTML+=xx; //añadimos y mostramos en pantalla.
            x+=xx; //añadimos y guardamos
            }
         xi=0 //el número está iniciado y podemos ampliarlo.
         }	  
function retro(){ //Borrar sólo el último número escrito.
         cifras=x.length; //hayar número de caracteres en pantalla
         br=x.substr(cifras-1,cifras); //describir último caracter
         if (br==".") {coma=0;} //Si el caracter quitado es la coma, se permite escribirla de nuevo.
         console.log(br.search(/^[a-zA-Z]+/)==0);
         if (br.search(/^[a-zA-Z]+/)==0) {
             l=1;
             while (l==1){
                 console.log(br);
                 if (br!=''){
                     if(br.search(/^[a-zA-Z]+/)==0){
                        l=1;
                        cifras=x.length;
                        br=x.substr(cifras-1,cifras);
                        if(br.search(/^[a-zA-Z]+/)==0){
                            x=x.substr(0,cifras-1);
                        }
                    } else {
                        l=0;
                        ni=0;
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
             if (br!=''){
             x=x.substr(0,cifras-1);
            } else {
                x="0";
                xi=1;
                coma=0;
                ni=0;
            }
         }
         
         pantalla.innerHTML=x; //mostrar resultado en pantalla	 
         }

function select(id){
    if(ni==1){
        
    } else {
    var xx = document.getElementById(id).value;
    if (x=="0" || xi==1  ) {  // inicializar un número, 
            pantalla.innerHTML=xx; //mostrar en pantalla
            x=xx; //guardar número;
            }
         else { //continuar un número
            pantalla.innerHTML+=xx; //añadimos y mostramos en pantalla.
            x+=xx; //añadimos y guardamos
            }
         xi=0 //el número está iniciado y podemos ampliarlo.
         ni=1;
    }
}
function comas(xx){
    if(x==0 || xi==1 || coma==1){
        
    } else {

    if (xx=="." && coma==0) { //si escribimos una coma decimal pòr primera vez
           pantalla.innerHTML+=xx;
           x+=xx;
           coma=1; //cambiar el estado de la coma  
       } else {
           pantalla.innerHTML+=xx;
           x+=xx
       }
    }
}


