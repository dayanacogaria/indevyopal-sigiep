var graph = function(canvasId, w, h, values){
    this.canvas        = document.getElementById(canvasId);
    this.canvas.width  = w;
    this.canvas.heigth = h;
    this.radio         = Math.min(this.canvas.width / 2, this.canvas.height / 2);
    this.context       = this.canvas.getContext("2d");
    this.values        = values;
    this.tamanoDonut   = 0;

    this.paint = function () {
        this.total = this.getTotal();
        var inicioAngulo = 0;
        var value;
        var angulo;
        var color;
        for (var i in this.values) {
            value  = values[i]["value"];
            color  = values[i]["color"];
            angulo = 2 * Math.PI * value / this.total;
            this.context.fillStyle = color;
            this.context.beginPath();
            this.context.moveTo(this.canvas.width / 2, this.canvas.height / 2);
            this.context.arc(this.canvas.width / 2, this.canvas.height / 2, this.radio, inicioAngulo, (inicioAngulo + angulo));
            this.context.closePath();
            this.context.fill();

            inicioAngulo += angulo;
        }
    }

    this.paintDonut = function (tamano, color) {
        this.tamanoDonut = tamano;
        this.paint();

        this.context.fillStyle = color;
        this.context.beginPath();
        this.context.moveTo(this.canvas.width / 2, this.canvas.height / 2);
        this.context.arc(this.canvas.width / 2, this.canvas.height / 2, this.radio * tamano, 0, 2 * Math.PI);
        this.context.closePath();
        this.context.fill();
    }

    this.setPorcent = function (color) {
        var value;
        var etiquetaX;
        var etiquetaY;
        var inicioAngulo = 0;
        var angulo;
        var texto;
        var incrementar  = 0;

        if(this.tamanoDonut)
            incrementar = (this.radio * this.tamanoDonut) / 2;

        this.context.font      = "bold 7.8pt Sans-serif";
        this.context.fillStyle = color;
        
        for (var i in this.values){
            value     = values[i]["value"];
            angulo    = 2 * Math.PI * value / this.total;

            etiquetaX = this.canvas.width  / 2 + (incrementar + this.radio / 2) * Math.cos(inicioAngulo + angulo / 2);
            etiquetaY = this.canvas.height / 2 + (incrementar + this.radio / 2) * Math.sin(inicioAngulo + angulo / 2);

            texto     = Math.round(100 * value / this.total);

            if(etiquetaX < this.canvas.width / 2)
                etiquetaX -= 10;

            this.context.beginPath();
            if(texto > 0){
                this.context.fillText(texto + "%", etiquetaX, etiquetaY);
            }
            this.context.stroke();

            inicioAngulo += angulo;
        }
    }

    this.getTotal = function () {
        var total = 0;
        for (var i in this.values){
            total += values[i]["value"];
        }
        return total;
    }

    this.setLeyend = function (leyendId) {
        var html = "<ul class='leyenda'>";

        for (var i in this.values){
            html += "<li><span style='background-color:"+values[i]["color"]+"'></span>"+i+"</li>";
        }

        html += "</ul>";

        document.getElementById(leyendId).innerHTML = html;
    }
}